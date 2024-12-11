<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\QueryBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Book',
    description: 'API endpoints for managing books',
)]
class BookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }

    #[
        OA\Get(
            path: '/books',
            tags: ['Book'],
            summary: 'Get all books',
            description: 'Get all books with optional query parameters to filter, sort, and search books.',
            operationId: 'book.index',
            parameters: [
                new OA\Parameter(
                    name: 'search',
                    in: 'query',
                    description: 'Search books by title and description',
                    required: false,
                    schema: new OA\Schema(type: 'string'),
                ),
                new OA\Parameter(
                    name: 'perPage',
                    in: 'query',
                    description: 'Number of books per page',
                    required: false,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Successful operation'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function index(QueryBookRequest $request)
    {
        $books = $this->bookRepositoryInterface->index($request->validated());

        return ApiResponse::sendResponse(new BookCollection($books), '', 200);
    }

    #[
        OA\Post(
            path: '/books',
            tags: ['Book'],
            summary: 'Create a new book',
            description: 'Create a new book with the provided data.',
            operationId: 'book.store',
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        required: ['title', 'publish_date', 'author_id'],
                        properties: [
                            new OA\Property(property: 'title', type: 'string'),
                            new OA\Property(property: 'description', type: 'string'),
                            new OA\Property(property: 'publish_date', type: 'string', format: 'date'),
                            new OA\Property(property: 'author_id', type: 'integer'),
                        ],
                        example: [
                            'title' => 'PHP for beginners',
                            'description' => 'A beginner\'s guide to PHP',
                            'publish_date' => '2021-10-01',
                            'author_id' => 1,
                        ],
                    )
                )
            ),
            responses: [
                new OA\Response(response: Response::HTTP_CREATED, description: 'Book created successfully'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function store(StoreBookRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();

        try {
            $book = $this->bookRepositoryInterface->store($request);

            DB::commit();

            return ApiResponse::sendResponse(new BookResource($book), 'Book created successfully', 201);
        } catch (\Exception $exception) {
            return ApiResponse::rollback($exception);
        }
    }

    #[
        OA\Get(
            path: '/books/{id}',
            tags: ['Book'],
            summary: 'Get book by ID',
            description: 'Get book by ID.',
            operationId: 'book.show',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Book ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Successful operation'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Book not found'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function show($id)
    {
        try {
            $book = $this->bookRepositoryInterface->show($id);

            return ApiResponse::sendResponse(new BookResource($book), '', 200);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Book not found');
        } catch (\Exception $exception) {
            return ApiResponse::throw($exception);
        }
    }

    #[
        OA\Put(
            path: '/books/{id}',
            tags: ['Book'],
            summary: 'Update book by ID',
            description: 'Update book by ID with the provided data.',
            operationId: 'book.update',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Book ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        required: ['title', 'publish_date', 'author_id'],
                        properties: [
                            new OA\Property(property: 'title', type: 'string'),
                            new OA\Property(property: 'description', type: 'string'),
                            new OA\Property(property: 'publish_date', type: 'string', format: 'date'),
                            new OA\Property(property: 'author_id', type: 'integer'),
                        ],
                        example: [
                            'title' => 'PHP for beginners',
                            'description' => 'A beginner\'s guide to PHP',
                            'publish_date' => '2021-10-01',
                            'author_id' => 1,
                        ],
                    )
                )
            ),
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Book updated successfully'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Book not found'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function update(UpdateBookRequest $request, $id)
    {
        $request = $request->validated();

        DB::beginTransaction();

        try {
            $book = $this->bookRepositoryInterface->update($request, $id);

            DB::commit();

            return ApiResponse::sendResponse(new BookResource($book), 'Book updated successfully', 200);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Book not found');
        } catch (\Exception $exception) {
            return ApiResponse::rollback($exception);
        }
    }

    #[
        OA\Delete(
            path: '/books/{id}',
            tags: ['Book'],
            summary: 'Delete book by ID',
            description: 'Delete book by ID.',
            operationId: 'book.destroy',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Book ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_NO_CONTENT, description: 'Book deleted successfully'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Book not found'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function destroy($id)
    {
        try {
            $this->bookRepositoryInterface->delete($id);

            return ApiResponse::sendResponse([], 'Book deleted successfully', 204);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Book not found');
        } catch (\Exception $exception) {
            return ApiResponse::throw($exception);
        }
    }
}

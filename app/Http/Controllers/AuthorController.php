<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\QueryAuthorRequest;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use App\Interfaces\AuthorRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Author',
    description: 'API endpoints for managing authors',
)]
class AuthorController extends Controller
{
    private AuthorRepositoryInterface $authorRepositoryInterface;

    public function __construct(AuthorRepositoryInterface $authorRepositoryInterface)
    {
        $this->authorRepositoryInterface = $authorRepositoryInterface;
    }

    #[
        OA\Get(
            path: '/authors',
            tags: ['Author'],
            summary: 'Get all authors',
            description: 'Get all authors with optional query parameters to filter, sort, and search authors.',
            operationId: 'index',
            parameters: [
                new OA\Parameter(
                    name: 'search',
                    in: 'query',
                    description: 'Search authors by name',
                    required: false,
                    schema: new OA\Schema(type: 'string'),
                ),
                new OA\Parameter(
                    name: 'perPage',
                    in: 'query',
                    description: 'Number of authors per page',
                    required: false,
                    schema: new OA\Schema(type: 'integer'),
                ),
                new OA\Parameter(
                    name: 'page',
                    in: 'query',
                    description: 'Page number',
                    required: false,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Successful operation'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        ),
    ]
    public function index(QueryAuthorRequest $request)
    {
        $authors = $this->authorRepositoryInterface->index($request->validated());

        return ApiResponse::sendResponse(new AuthorCollection($authors), '', 200);
    }

    #[
        OA\Post(
            path: '/authors',
            tags: ['Author'],
            summary: 'Create a new author',
            description: 'Create a new author with the provided data.',
            operationId: 'store',
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        required: ['name'],
                        properties: [
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'bio', type: 'string'),
                            new OA\Property(property: 'birth_date', type: 'string', format: 'date'),
                        ],
                        example: [
                            'name' => 'John Doe',
                            'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            'birth_date' => '1990-01-01',
                        ],
                    )
                )
            ),
            responses: [
                new OA\Response(response: Response::HTTP_CREATED, description: 'Author created successfully'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function store(StoreAuthorRequest $request)
    {
        $request = $request->validated();

        DB::beginTransaction();

        try {
            $author = $this->authorRepositoryInterface->store($request);

            DB::commit();

            return ApiResponse::sendResponse(new AuthorResource($author), 'Author created successfully', 201);
        } catch (\Exception $exception) {
            return ApiResponse::rollback($exception);
        }
    }

    #[
        OA\Get(
            path: '/authors/{id}',
            tags: ['Author'],
            summary: 'Get author by ID',
            description: 'Get author by ID.',
            operationId: 'show',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Author ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Successful operation'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Author not found'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function show($id)
    {
        try {
            $author = $this->authorRepositoryInterface->show($id);

            return ApiResponse::sendResponse(new AuthorResource($author), '', 200);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Author not found');
        } catch (\Exception $exception) {
            return ApiResponse::throw($exception);
        }
    }

    #[
        OA\Put(
            path: '/authors/{id}',
            tags: ['Author'],
            summary: 'Update author by ID',
            description: 'Update author by ID with the provided data.',
            operationId: 'update',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Author ID',
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
                        properties: [
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'bio', type: 'string'),
                            new OA\Property(property: 'birth_date', type: 'string', format: 'date'),
                        ],
                        example: [
                            'name' => 'John Doe',
                            'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                            'birth_date' => '1990-01-01',
                        ],
                    )
                )
            ),
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Author updated successfully'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Author not found'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function update(UpdateAuthorRequest $request, $id)
    {
        $request = $request->validated();

        DB::beginTransaction();

        try {
            $author = $this->authorRepositoryInterface->update($request, $id);

            DB::commit();

            return ApiResponse::sendResponse(new AuthorResource($author), 'Author updated successfully', 200);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Author not found');
        } catch (\Exception $exception) {
            return ApiResponse::rollback($exception);
        }
    }

    #[
        OA\Delete(
            path: '/authors/{id}',
            tags: ['Author'],
            summary: 'Delete author by ID',
            description: 'Delete author by ID.',
            operationId: 'destroy',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Author ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_NO_CONTENT, description: 'Author deleted successfully'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Author not found'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function destroy($id)
    {
        try {
            $this->authorRepositoryInterface->delete($id);

            return ApiResponse::sendResponse([], 'Author deleted successfully', 204);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Author not found');
        } catch (\Exception $exception) {
            return ApiResponse::throw($exception);
        }
    }
}

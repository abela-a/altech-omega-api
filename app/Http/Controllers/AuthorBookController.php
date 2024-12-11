<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\QueryAuthorBookRequest;
use App\Http\Resources\AuthorBookCollection;
use App\Interfaces\BookRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class AuthorBookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }

    #[
        OA\Get(
            path: '/authors/{id}/books',
            tags: ['Author'],
            summary: 'Get all books by author',
            description: 'Get all books by author with optional query parameters to filter, sort, and search books.',
            operationId: 'author.books',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Author ID',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                ),
                new OA\Parameter(
                    name: 'perPage',
                    in: 'query',
                    description: 'Number of books per page',
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
                new OA\Parameter(
                    name: 'total',
                    in: 'query',
                    description: 'Total number of books',
                    required: false,
                    schema: new OA\Schema(type: 'integer'),
                ),
            ],
            responses: [
                new OA\Response(response: Response::HTTP_OK, description: 'Successful operation'),
                new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Author not found'),
                new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Invalid data'),
                new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal server error'),
            ],
        )
    ]
    public function index(QueryAuthorBookRequest $request, $authorId)
    {
        $books = $this->bookRepositoryInterface->authorBooks($authorId, $request->validated());

        return ApiResponse::sendResponse(new AuthorBookCollection($books), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

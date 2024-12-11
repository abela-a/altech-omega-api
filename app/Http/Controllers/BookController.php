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
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    private BookRepositoryInterface $bookRepositoryInterface;

    public function __construct(BookRepositoryInterface $bookRepositoryInterface)
    {
        $this->bookRepositoryInterface = $bookRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(QueryBookRequest $request)
    {
        $books = $this->bookRepositoryInterface->index($request->validated());

        return ApiResponse::sendResponse(new BookCollection($books), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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

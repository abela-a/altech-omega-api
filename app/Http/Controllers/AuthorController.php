<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Interfaces\AuthorRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    private AuthorRepositoryInterface $authorRepositoryInterface;

    public function __construct(AuthorRepositoryInterface $authorRepositoryInterface)
    {
        $this->authorRepositoryInterface = $authorRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->authorRepositoryInterface->index();

        return ApiResponse::sendResponse(AuthorResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, $id)
    {
        $request = $request->validated();

        DB::beginTransaction();

        try {
            $author = $this->authorRepositoryInterface->update($request, $id);

            DB::commit();

            return ApiResponse::sendResponse($author, 'Author updated successfully', 201);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::notFound('Author not found');
        } catch (\Exception $exception) {
            return ApiResponse::rollback($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorRepositoryInterface->delete($id);

        return ApiResponse::sendResponse([], 'Author deleted successfully', 204);
    }
}

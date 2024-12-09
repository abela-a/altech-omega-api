<?php

use App\Http\Controllers\AuthorBookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('authors.books', AuthorBookController::class, ['only' => ['index']]);
    Route::apiResource('books', BookController::class);
});

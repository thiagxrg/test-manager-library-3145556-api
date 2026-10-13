<?php

use Illuminate\Support\Facades\Route;
use Library\Adapters\Inbound\Http\AuthController;
use Library\Adapters\Inbound\Http\BookController;
use Library\Adapters\Inbound\Http\CategoryController;
use Library\Adapters\Inbound\Http\LoanController;
use Library\Adapters\Inbound\Http\MemberController;
use Library\Adapters\Inbound\Http\ReportController;
use Library\Adapters\Inbound\Http\UserController;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'ok']));
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);

        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{id}', [BookController::class, 'show']);
        Route::get('/books/{id}/cover', [BookController::class, 'showCover']);

        Route::middleware('role:admin')->group(function () {
            Route::post('/users', [UserController::class, 'store']);
            Route::post('/books', [BookController::class, 'store']);
            Route::put('/books/{id}', [BookController::class, 'update']);
            Route::delete('/books/{id}', [BookController::class, 'destroy']);
            Route::put('/books/{id}/cover', [BookController::class, 'uploadCover']);
            Route::delete('/books/{id}/cover', [BookController::class, 'deleteCover']);
            Route::get('/reports/most-borrowed', [ReportController::class, 'mostBorrowed']);
        });

        Route::middleware('role:admin,librarian')->group(function () {
            Route::get('/members', [MemberController::class, 'index']);
            Route::get('/members/{id}', [MemberController::class, 'show']);
            Route::post('/members', [MemberController::class, 'store']);

            Route::post('/loans', [LoanController::class, 'store']);
            Route::get('/loans', [LoanController::class, 'index']);
            Route::get('/loans/overdue', [LoanController::class, 'overdueList']);
            Route::get('/loans/{id}', [LoanController::class, 'show']);
            Route::post('/loans/{loanId}/items/{itemId}/return', [LoanController::class, 'returnItem']);
        });
    });
});

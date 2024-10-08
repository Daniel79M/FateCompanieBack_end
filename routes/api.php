<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('FATE.v1.0.0')->group(function () {
    
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('otp_code', [AuthController::class, 'checkOtpCode']);

    
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('store/{user_id}', [GroupeController::class, 'store'])->name('store');
        Route::get('groupe/index', [GroupeController::class, 'index'])->name('index');
        Route::get('users', [UserController::class, 'index']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('groupe/{groupeId}/addMember', [GroupeController::class, 'addMember'])->name('addMember');
        Route::get('groupe/ShowGroupsForUser/{user_id}', [GroupeController::class, 'ShowGroupsForUser'])->name('ShowGroupsForUser');
        Route::post('upload-file/{id}', [FileController::class, 'file'])->name('file');
        Route::get('groupe/{groupeId}/files', [FileController::class, 'getGroupFiles']);

    });
}); 

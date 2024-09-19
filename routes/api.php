<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/', function () {
    return "API";
});

Route::post('/login',[AdminController::class, 'login']);
Route::post('/register',[AdminController::class, 'register']);
Route::post('/logout', [AdminController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/display',[AdminController::class, 'display']);
Route::get('/findstaff/{id}',[AdminController::class, 'findstaff']);
Route::post('/addstaff',[AdminController::class, 'addstaff']);
Route::put('/updatestaff/{id}',[AdminController::class, 'updatestaff']);
Route::delete('/deletestaff/{id}',[AdminController::class, 'deletestaff']);


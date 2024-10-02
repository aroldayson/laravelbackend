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

// staff
Route::get('/display',[AdminController::class, 'display']);
Route::get('/findstaff/{id}',[AdminController::class, 'findstaff']);
Route::post('/addstaff',[AdminController::class, 'addstaff']);
Route::put('/updatestaff/{id}',[AdminController::class, 'updatestaff']);
Route::delete('/deletestaff/{id}',[AdminController::class, 'deletestaff']);
Route::get('/getUser',[AdminController::class, 'getUser']);
// Route::put('/uploadss', [AdminController::class, 'uploadss']);

Route::put('/upload/{id}', [AdminController::class, 'upload']);
Route::get('/upload/{id}', [AdminController::class, 'upload']);

Route::post('/update-profile-image/{id}', [AdminController::class, 'updateProfileImage']);
// pricemanagement
Route::get('/pricedisplay',[AdminController::class, 'pricedisplay']);
Route::post('/addprice',[AdminController::class, 'addprice']);
Route::delete('/deletecateg/{id}',[AdminController::class, 'deletecateg']);
Route::get('/findprice/{id}',[AdminController::class, 'findprice']);
Route::put('/updateprice/{id}',[AdminController::class, 'updateprice']);

// dashboard
Route::get('/dashdisplays',[AdminController::class, 'dashdisplays']);
Route::get('/expensendisplays',[AdminController::class, 'expensendisplays']);

// customer
Route::get('/customerdisplay',[AdminController::class, 'customerdisplay']);
Route::get('/findcustomer/{id}',[AdminController::class, 'findcustomer']);

// transactions
Route::get('/Transadisplay',[AdminController::class, 'Transadisplay']);
Route::get('/findtrans/{id}',[AdminController::class, 'findtrans']);
Route::get('/printtrans/{id}',[AdminController::class, 'printtrans']);
Route::get('/calculateBalance/{id}',[AdminController::class, 'calculateBalance']);

// expenses
Route::get('/displayexpenses',[AdminController::class, 'displayexpenses']);




// sampleoutput
Route::get('/sampledis/{customerId}',[AdminController::class, 'sampledis']);
Route::get('/sampledis',[AdminController::class, 'sampledis']);
Route::get('/CountDisplay',[AdminController::class, 'CountDisplay']);


<?php

use App\Controllers\ProductController;
use Support\Route;
use Support\View;
use Support\AuthMiddleware; //<-- Penambahan Middleware atau session login

// handleMiddleware();

Route::get('/home', [ProductController::class, 'index']);
Route::get('/product', function(){
    View::render('products/products',[],'navbar/navbar');
});
Route::get('/products',[ProductController::class, 'products']);

Route::get('/',function(){
    View::render('welcome/welcome');
});
<?php

use App\Controllers\ProductController;
use Support\Route;
use Support\View;
use Support\AuthMiddleware; //<-- Penambahan Middleware atau session login

// handleMiddleware();

Route::get('/home', function(){
    return View::render('home/home',['data'=>'ini adalah home'],'navbar/navbar');
});
Route::get('/product', function(){
    View::render('products/products',[],'navbar/navbar');
});
Route::get('/products',[ProductController::class, 'products']);

Route::get('/',function(){
    View::render('welcome/welcome');
});
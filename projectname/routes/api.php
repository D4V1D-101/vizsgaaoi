<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| RESTful API endpoints a User modellhez
| Prefix: /api/users
|
*/

// GET /api/users - Összes user lekérése
Route::get('/users', [UserController::class, 'index']);

// GET /api/users/{id} - Egy konkrét user lekérése
Route::get('/users/{id}', [UserController::class, 'show']);


// POST /api/users - Új user létrehozása
Route::post('/users', [UserController::class, 'store']);

// PUT /api/users/{id} - User frissítése
Route::put('/users/{id}', [UserController::class, 'update']);

// DELETE /api/users/{id} - User törlése
Route::delete('/users/{id}', [UserController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Post API Routes
|--------------------------------------------------------------------------
|
| RESTful API endpoints a Post modellhez
| Prefix: /api/posts
|
*/

// GET /api/posts - Összes post lekérése
Route::get('/posts', [PostController::class, 'index']);


// GET /api/posts/{id} - Egy konkrét post lekérése
Route::get('/posts/{id}', [PostController::class, 'show']);


// POST /api/posts - Új post létrehozása
Route::post('/posts', [PostController::class, 'store']);


// PUT /api/posts/{id} - Post frissítése
Route::put('/posts/{id}', [PostController::class, 'update']);

// DELETE /api/posts/{id} - Post törlése
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Alternatív Resource Routes (opcionális)
|--------------------------------------------------------------------------
|
| Ha szeretnéd, használhatod a Laravel resource route-okat is:
|
| Route::apiResource('users', UserController::class);
| Route::apiResource('posts', PostController::class);
|
| Ez automatikusan létrehozza az összes RESTful route-ot.
|
*/

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CarritoController;
use App\Http\Controllers\Api\CheckoutController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui puedes registrar las rutas de la API para tu aplicacion.
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoints CRUD para Categorías y Productos (Requisito 2)
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('productos', ProductoController::class);

Route::get('/carrito', [CarritoController::class, 'index']);
Route::post('/carrito', [CarritoController::class, 'store']);
Route::put('/carrito/{id}', [CarritoController::class, 'update']);
Route::delete('/carrito/{id}', [CarritoController::class, 'destroy']);
Route::delete('/carrito', [CarritoController::class, 'vaciar']);

Route::get('/checkout/resumen', [CheckoutController::class, 'resumen']);
Route::post('/checkout/confirmar', [CheckoutController::class, 'confirmar']);
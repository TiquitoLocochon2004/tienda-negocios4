<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CarritoItemController;
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

Route::get('/carrito', [CarritoItemController::class, 'index']);
Route::post('/carrito', [CarritoItemController::class, 'store']);
Route::put('/carrito/{id}', [CarritoItemController::class, 'update']);
Route::delete('/carrito/{id}', [CarritoItemController::class, 'destroy']);
Route::delete('/carrito/vaciar', [CarritoItemController::class, 'vaciar']);

Route::get('/checkout/resumen', [CheckoutController::class, 'resumen']);
Route::post('/checkout/confirmar', [CheckoutController::class, 'confirmar']);

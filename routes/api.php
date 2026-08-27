<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\CarritoItemController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui puedes registrar las rutas de la API para tu aplicacion.
*/

// --- RUTAS PÚBLICAS ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Endpoints Públicos de Catálogo (Categorías y Productos)
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('productos', ProductoController::class);

// --- RUTAS PROTEGIDAS (Requieren Token JWT) ---
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Carrito de Compras (Protegido por usuario)
    Route::get('/carrito', [CarritoItemController::class, 'index']);
    Route::post('/carrito', [CarritoItemController::class, 'store']);
    Route::put('/carrito/{id}', [CarritoItemController::class, 'update']);
    Route::delete('/carrito/vaciar', [CarritoItemController::class, 'vaciar']);
    Route::delete('/carrito/{id}', [CarritoItemController::class, 'destroy']);

    // Checkout y Procesamiento de Orden
    Route::get('/checkout/resumen', [CheckoutController::class, 'resumen']);
    Route::post('/checkout/confirmar', [CheckoutController::class, 'confirmar']);
});
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\User; 
use App\Models\CarritoItem;
use Illuminate\Http\Request;

class CarritoItemController extends Controller
{
    // Función auxiliar para obtener dinámicamente un usuario existente
    private function getUserId()
    {
        $user = User::first();
        return $user ? $user->id : 1;
    }

    // Ver el contenido actual del carrito
    public function index()
    {
        $userId = $this->getUserId();

        // Usamos Eloquent con un join para traer los datos del producto
        $items = CarritoItem::join('productos', 'carrito_items.producto_id', '=', 'productos.id')
            ->where('carrito_items.user_id', $userId)
            ->select('carrito_items.id as item_id', 'productos.id as producto_id', 'productos.nombre', 'productos.precio', 'carrito_items.cantidad')
            ->get();

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item->precio * $item->cantidad;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                // number_format devuelve un string con formato estricto de 2 decimales
                'subtotal' => number_format($subtotal, 2, '.', '') 
            ]
        ], 200);
    }

    // Agregar un producto al carrito
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $userId = $this->getUserId();
        $producto = Producto::findOrFail($request->producto_id);
        
        // Buscar si el producto ya está en el carrito de este usuario usando Eloquent
        $itemExistente = CarritoItem::where('user_id', $userId)
            ->where('producto_id', $producto->id)
            ->first();

        $cantidadActual = $itemExistente ? $itemExistente->cantidad : 0;
        $totalSolicitado = $cantidadActual + $request->cantidad;

        // Validar stock
        if ($producto->stock < $totalSolicitado) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Stock disponible: ' . $producto->stock
            ], 422);
        }

        // Si ya existe, actualizamos usando el modelo
        if ($itemExistente) {
            $itemExistente->cantidad = $totalSolicitado;
            $itemExistente->save(); // Eloquent maneja el updated_at automáticamente
        } else {
            // Si no existe, creamos una nueva instancia del modelo
            $nuevoItem = new CarritoItem();
            $nuevoItem->user_id = $userId;
            $nuevoItem->producto_id = $producto->id;
            $nuevoItem->cantidad = $request->cantidad;
            $nuevoItem->save(); // Eloquent maneja created_at y updated_at
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito'
        ], 201);
    }

    // Actualizar la cantidad de un producto
    public function update(Request $request, int $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $userId = $this->getUserId();

        $itemExistente = CarritoItem::where('user_id', $userId)
            ->where('producto_id', $id)
            ->first();

        if (!$itemExistente) {
            return response()->json([
                'success' => false,
                'message' => 'El producto no está en el carrito'
            ], 404);
        }

        $producto = Producto::findOrFail($id);

        if ($producto->stock < $request->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Stock disponible: ' . $producto->stock
            ], 422);
        }

        // Actualizamos usando el modelo directamente
        $itemExistente->cantidad = $request->cantidad;
        $itemExistente->save();

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada con éxito'
        ], 200);
    }

    // Eliminar un producto del carrito
    public function destroy(int $id)
    {
        $userId = $this->getUserId();

        $eliminado = CarritoItem::where('user_id', $userId)
            ->where('producto_id', $id)
            ->delete();

        if (!$eliminado) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Producto eliminado del carrito'], 200);
    }

    // Vaciar el carrito por completo
    public function vaciar()
    {
        $userId = $this->getUserId();
        
        CarritoItem::where('user_id', $userId)->delete();

        return response()->json(['success' => true, 'message' => 'El carrito ha sido vaciado'], 200);
    }
}
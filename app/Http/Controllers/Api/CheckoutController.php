<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarritoItem;
use App\Models\Producto;
use App\Models\User;
use App\DTOs\CheckoutDataDTO;
use App\DTOs\OrdenConfirmadaDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Función auxiliar para obtener dinámicamente un usuario existente
    private function getUserId()
    {
        $user = User::first();
        return $user ? $user->id : 1;
    }

    // Resumen de Compra
    public function resumen()
    {
        $userId = $this->getUserId(); 
        
        $itemsCarrito = CarritoItem::where('user_id', $userId)->get();

        if ($itemsCarrito->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'El carrito está vacío'
            ], 400);
        }

        $subtotal = 0;
        $itemsDetalle = [];

        foreach ($itemsCarrito as $item) {
            $producto = Producto::find($item->producto_id);
            
            if (!$producto) {
                continue;
            }

            $subtotal += $producto->precio * $item->cantidad;
            
            $itemsDetalle[] = [
                'producto_id' => $producto->id,
                'producto' => $producto->nombre,
                'cantidad' => $item->cantidad,
                'precio_unitario' => number_format($producto->precio, 2, '.', ''),
                'subtotal_item' => number_format($producto->precio * $item->cantidad, 2, '.', '')
            ];
        }

        $impuestos = $subtotal * 0.21; // 21% IVA de ejemplo
        $costoEnvio = 50.00; // Costo fijo de envío

        // Instanciamos el DTO
        $checkoutDTO = new CheckoutDataDTO($subtotal, $impuestos, $costoEnvio, $itemsDetalle);

        return response()->json([
            'success' => true,
            'data' => $checkoutDTO->toArray()
        ], 200);
    }

    // Confirmar la compra (Checkout final)
    public function confirmar(Request $request)
    {
        // Validamos los datos de envío y pago que viajan en el request
        $request->validate([
            'direccion_envio' => 'required|string|max:255',
            'metodo_pago' => 'required|string|in:tarjeta,efectivo,transferencia'
        ]);

        $userId = $this->getUserId();
        
        // Obtener los ítems del carrito
        $itemsCarrito = DB::table('carrito_items')
            ->where('user_id', $userId)
            ->get();

        if ($itemsCarrito->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'No puedes confirmar una compra con el carrito vacío'
            ], 400);
        }

        $subtotal = 0;

        // Doble validación de stock antes de descontar (Requisito 9)
        foreach ($itemsCarrito as $item) {
            $producto = Producto::find($item->producto_id);
            
            if (!$producto || $producto->stock < $item->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para el producto: " . ($producto ? $producto->nombre : 'Desconocido')
                ], 422);
            }
            $subtotal += $producto->precio * $item->cantidad;
        }

        $impuestos = $subtotal * 0.21;
        $costoEnvio = 50.00;
        $totalGeneral = $subtotal + $impuestos + $costoEnvio;

        // ID simulado de la orden guardada y descuento de inventario
        $ordenId = rand(1000, 9999);

        foreach ($itemsCarrito as $item) {
            $producto = Producto::find($item->producto_id);
            $producto->stock -= $item->cantidad;
            $producto->save();
        }

        // Vaciar el carrito del usuario tras la compra exitosa
        DB::table('carrito_items')->where('user_id', $userId)->delete();

        // Instanciar DTO de confirmación (Requisito 6)
        $ordenDTO = new OrdenConfirmadaDTO(
            $ordenId, 
            $request->metodo_pago, 
            $request->direccion_envio, 
            $totalGeneral
        );

        return response()->json([
            'success' => true,
            'data' => $ordenDTO->toArray()
        ], 200);
    }
}
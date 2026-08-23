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
    private function getUserId()
    {
        $user = User::first();
        return $user ? $user->id : 1;
    }

    public function resumen()
    {
        $userId = $this->getUserId(); 
        
        $itemsCarrito = CarritoItem::where('user_id', $userId)->get();

        // Código 400 (Bad Request): Se devuelve si el usuario intenta ver el resumen 
        // pero su carrito no contiene ningún producto.
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

        $impuestos = $subtotal * 0.21;
        $costoEnvio = 50.00;

        $checkoutDTO = new CheckoutDataDTO($subtotal, $impuestos, $costoEnvio, $itemsDetalle);

        // Código 200 (OK): Indica que la solicitud se procesó correctamente. 
        // En este contexto, significa que el resumen de compra se calculó y generó 
        // con éxito, devolviendo los datos detallados (subtotal, impuestos, envío e ítems).
        return response()->json([
            'success' => true,
            'data' => $checkoutDTO->toArray()
        ], 200);
    }

    public function confirmar(Request $request)
    {
        $request->validate([
            'direccion_envio' => 'required|string|max:255',
            'metodo_pago' => 'required|string|in:tarjeta,efectivo,transferencia'
        ]);

        $userId = $this->getUserId();
        
        $itemsCarrito = DB::table('carrito_items')
            ->where('user_id', $userId)
            ->get();

        // Código 400 (Bad Request): Se devuelve específicamente en el proceso de confirmación 
        // si el usuario intenta pagar o avanzar con el checkout pero su carrito está vacío.
        if ($itemsCarrito->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'No puedes confirmar una compra con el carrito vacío'
            ], 400);
        }

        return DB::transaction(function () use ($userId, $request, $itemsCarrito) {
            $subtotal = 0;

            foreach ($itemsCarrito as $item) {
                $producto = Producto::find($item->producto_id);
                
                // Código 422 (Unprocessable Entity): Se devuelve si un producto que estaba 
                // en el carrito fue eliminado de la base de datos de la tienda antes de que 
                // el usuario pudiera confirmar su compra.
                if (!$producto) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Uno de los productos en tu carrito ya no se encuentra disponible.'
                    ], 422);
                }

                // Código 422 (Unprocessable Entity): Se devuelve si el producto existe, 
                // pero la cantidad que el usuario desea comprar supera el stock disponible actual 
                // en el inventario.
                if ($producto->stock < $item->cantidad) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto: " . $producto->nombre . ". Stock disponible: " . $producto->stock
                    ], 422);
                }

                $subtotal += $producto->precio * $item->cantidad;
            }

            $impuestos = $subtotal * 0.21;
            $costoEnvio = 50.00;
            $totalGeneral = $subtotal + $impuestos + $costoEnvio;

            $ordenId = rand(1000, 9999);

            foreach ($itemsCarrito as $item) {
                $producto = Producto::find($item->producto_id);
                $producto->stock -= $item->cantidad;
                $producto->save();
            }

            DB::table('carrito_items')->where('user_id', $userId)->delete();

            $ordenDTO = new OrdenConfirmadaDTO(
                $ordenId, 
                $request->metodo_pago, 
                $request->direccion_envio, 
                $totalGeneral
            );

            // Código 200 (OK): Indica que la operación de compra se completó exitosamente. 
            // Aquí significa que el inventario se descontó, el carrito se vació y la orden 
            // se generó correctamente, devolviendo los datos de confirmación al usuario.
            return response()->json([
                'success' => true,
                'data' => $ordenDTO->toArray()
            ], 200);
        });
    }
}
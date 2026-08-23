<?php

namespace App\Http\Controllers;

use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoItemController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar los datos básicos de entrada
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $cantidadSolicitada = (int) $request->cantidad;
        
        // Si usas el sistema de autenticación por defecto de Laravel:
        $userId = Auth::id(); 

        // 2. Buscar si el usuario ya tiene este producto en su carrito
        $itemCarrito = CarritoItem::where('user_id', $userId)
                              ->where('producto_id', $producto->id)
                              ->first();

        // Calcular cuánto tendría en total si sumamos lo nuevo
        $cantidadActualEnCarrito = $itemCarrito ? $itemCarrito->cantidad : 0;
        $cantidadTotalDeseada = $cantidadActualEnCarrito + $cantidadSolicitada;

        // 3. Validar que la cantidad total no supere el stock disponible
        if ($cantidadTotalDeseada > $producto->stock) {
            return back()->withErrors([
                'cantidad' => "No puedes agregar esa cantidad. Stock disponible: {$producto->stock} (Ya tienes {$cantidadActualEnCarrito} en el carrito)."
            ]);
        }

        // 4. Si ya existe, sumamos; si no, creamos el registro nuevo
        if ($itemCarrito) {
            $itemCarrito->cantidad = $cantidadTotalDeseada;
            $itemCarrito->save();
        } else {
            CarritoItem::create([
                'user_id'     => $userId,
                'producto_id' => $producto->id,
                'cantidad'    => $cantidadSolicitada,
            ]);
        }

        return redirect()->back()->with('success', '¡Producto agregado al carrito con éxito!');
    }
}
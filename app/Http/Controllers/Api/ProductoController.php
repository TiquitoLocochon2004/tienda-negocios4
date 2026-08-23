<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        // Traemos los productos junto a su categoría asociada
        return response()->json([
            'success' => true,
            'data' => Producto::with('categoria')->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $producto = Producto::create([
            'nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre)),
            'precio' => $request->precio,
            'stock' => $request->stock,
            'categoria_id' => $request->categoria_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado con éxito',
            'data' => $producto->load('categoria')
        ], 201);
    }

    public function show(Producto $producto)
    {
        return response()->json([
            'success' => true,
            'data' => $producto->load('categoria')
        ], 200);
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $producto->update([
            'nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre)),
            'precio' => $request->precio,
            'stock' => $request->stock,
            'categoria_id' => $request->categoria_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado con éxito',
            'data' => $producto->load('categoria')
        ], 200);
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado con éxito'
        ], 200);
    }
}
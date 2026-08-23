<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Categoria::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre'
        ]);

        $categoria = Categoria::create([
            'nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada con éxito',
            'data' => $categoria
        ], 201);
    }

    public function show(Categoria $categoria)
    {
        return response()->json([
            'success' => true,
            'data' => $categoria
        ], 200);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id
        ]);

        $categoria->update([
            'nombre' => trim(preg_replace('/\s+/', ' ', $request->nombre))
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada con éxito',
            'data' => $categoria
        ], 200);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada con éxito'
        ], 200);
    }
}

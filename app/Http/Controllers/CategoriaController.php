<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // Mostrar listado de categorías
    public function index()
    {
        $categorias = Categoria::all();
        return view('categorias.index', compact('categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('categorias.create');
    }

    // Guardar nueva categoría en la BD usando el Form Request
    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')
                         ->with('success', 'Categoría creada con éxito.');
    }

    // Mostrar formulario para editar
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    // Actualizar la categoría en la BD
    public function update(StoreCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()->route('categorias.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

    // Eliminar la categoría
    public function destroy(Categoria $categoria)
    {
        // Si borras la categoría, sus productos también se eliminarán.
        $categoria->delete();

        return redirect()->route('categorias.index')
                         ->with('success', 'Categoría eliminada.');
    }
}
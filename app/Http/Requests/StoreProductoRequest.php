<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge([
                // trim() quita espacios al inicio y final
                // preg_replace compacta espacios múltiples en el medio a uno solo
                // Respetamos las mayúsculas originales (ej: "iPhone 15")
                'nombre' => trim(preg_replace('/\s+/', ' ', $this->nombre))
            ]);
        }
    }

    public function rules(): array
    {
        // Si estamos actualizando, ignoramos el ID actual para la regla unique
        $productoId = $this->route('producto') ? $this->route('producto')->id : null;

        return [
            'nombre'       => 'required|string|max:100|unique:productos,nombre,' . $productoId,
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'     => 'El nombre del producto es obligatorio.',
            'nombre.unique'       => 'Ya existe un producto registrado con este nombre.',
            'precio.required'     => 'El precio es obligatorio.',
            'precio.numeric'      => 'El precio debe ser un número válido.',
            'stock.required'      => 'El stock es obligatorio.',
            'stock.integer'       => 'El stock debe ser un número entero.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }
}
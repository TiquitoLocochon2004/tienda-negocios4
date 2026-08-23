<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str; // Importante para manipular strings

class StoreCategoriaRequest extends FormRequest
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
                // preg_replace quita espacios dobles en el medio
                // ucwords(strtolower(...)) estandariza la capitalización (Ej: "electrónica" -> "Electrónica")
                'nombre' => ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $this->nombre))))
            ]);
        }
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria') ? $this->route('categoria')->id : null;

        return [
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $categoriaId,
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
            'nombre.max' => 'El nombre no puede superar los 50 caracteres.'
        ];
    }
}
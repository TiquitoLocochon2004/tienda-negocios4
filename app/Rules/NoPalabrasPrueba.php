<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoPalabrasPrueba implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Convertimos a minúsculas para evaluar sin importar mayúsculas
        $valorMinuscula = strtolower(trim($value));

        // Verificamos si empieza con "test" o "prueba"
        if (str_starts_with($valorMinuscula, 'test') || str_starts_with($valorMinuscula, 'prueba')) {
            $fail('El :attribute no puede comenzar con la palabra "Test" o "Prueba".');
        }
    }
}
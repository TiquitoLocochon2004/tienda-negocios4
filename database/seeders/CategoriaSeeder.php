<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::create(['nombre' => 'Periféricos']);
        Categoria::create(['nombre' => 'Pantallas']);
        Categoria::create(['nombre' => 'Componentes']);
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'id' => 1,
            'name' => 'Usuario Carrito',
            'email' => 'carrito@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->call([
            CategoriaSeeder::class,
        ]);
    }
}
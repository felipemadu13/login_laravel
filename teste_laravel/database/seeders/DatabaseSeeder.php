<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(4)->create();

        \App\Models\User::factory()->create([
            'name' => 'Felipe',
            'lastName' => 'Teste',
            'email' => 'test@example.com',
            'cpf' => '000.111.222-33',
            'phone' => '(84) 3222-2222',
            'status' => 'active',
            'role' => 'admin',
            'password' => '123456'

        ]);

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Usuario Teste',
            'email' => 'teste@email.com',
            'password' => Hash::make('password'), 
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('P4ssw0rd')
        ]);

        Client::create([
            'name' => 'Juan Pérez',
            'identity_type' => 'CC',
            'identity_number' => '12345678',
            'phone_number' => '3001234567',
            'email' => 'juan.perez@email.com'
        ]);

        Client::create([
            'name' => 'María González',
            'identity_type' => 'CE',
            'identity_number' => '87654321',
            'phone_number' => '3009876543',
            'email' => 'maria.gonzalez@email.com'
        ]);

        Client::create([
            'name' => 'Carlos Rodríguez',
            'identity_type' => 'NIT',
            'identity_number' => '900123456',
            'phone_number' => '3005555555',
            'email' => 'carlos.rodriguez@empresa.com'
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Board;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin/moderator
        User::create([
            'name'           => 'Admin',
            'email'          => 'admin@example.com',
            'password'       => Hash::make('admin12345'),
            'role'           => 'admin',
            'remember_token' => Str::random(10),
        ]);

        // Boards awal
        Board::insert([
            ['slug' => 'general',      'name' => 'General',      'description' => 'Tempat diskusi umum',    'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'programming',  'name' => 'Programming',  'description' => 'Diskusi tentang coding', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'random',       'name' => 'Random',       'description' => 'Obrolan bebas',          'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

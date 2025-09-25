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
            ['slug' => 'general',      'name' => 'General',        'description' => 'Tempat diskusi umum',                           'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'programming',  'name' => 'Programming',    'description' => 'Diskusi tentang coding dan pemrograman',         'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'random',       'name' => 'Random',         'description' => 'obrolan bebas tentang apa saja',       'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'politic',       'name' => 'Politic',         'description' => 'politic only board',                 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'gaming',       'name' => 'Gaming',         'description' => 'Diskusi seputar game PC, mobile, dan console',    'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'anime',        'name' => 'Anime & Manga',  'description' => 'Diskusi anime, manga, dan budaya Jepang',         'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'music',        'name' => 'Music',          'description' => 'Ngobrol soal lagu, musisi, dan rekomendasi musik', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'movies',       'name' => 'Movies & TV',    'description' => 'Review film, serial TV, dan rekomendasi tontonan', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'books',        'name' => 'Books',          'description' => 'Diskusi buku, novel, dan literatur',              'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'sports',       'name' => 'Sports',         'description' => 'Bahas olahraga, tim favorit, dan event olahraga dunia', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'finance',       'name' => 'Finance',        'description' => 'diskusi tentang keuangan', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'creative',       'name' => 'Creative',         'description' => 'diskusi seputar bidang kreatif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->call(AdminUserSeeder::class);
    }
}

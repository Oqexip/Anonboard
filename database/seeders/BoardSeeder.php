<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Board::create([
            'slug' => 'general',
            'name' => 'General',
            'description' => 'Tempat diskusi umum',
        ]);

        Board::create([
            'slug' => 'programming',
            'name' => 'Programming',
            'description' => 'Diskusi tentang coding',
        ]);

        Board::create([
            'slug' => 'random',
            'name' => 'Random',
            'description' => 'Obrolan bebas',
        ]);
    }
}
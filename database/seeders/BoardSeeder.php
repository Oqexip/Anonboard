<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
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
            'description' => 'Diskusi tentang coding dan pemrograman',
        ]);

        Board::create([
            'slug' => 'random',
            'name' => 'Random',
            'description' => 'Obrolan bebas tentang apa saja',
        ]);

        Board::create([
            'slug' => 'politic',
            'name' => 'Politic',
            'description' => 'politic only board',
        ]);

        Board::create([
            'slug' => 'gaming',
            'name' => 'Gaming',
            'description' => 'Diskusi seputar game PC, mobile, dan console',
        ]);

        Board::create([
            'slug' => 'anime',
            'name' => 'Anime & Manga',
            'description' => 'Diskusi anime, manga, dan budaya Jepang',
        ]);

        Board::create([
            'slug' => 'music',
            'name' => 'Music',
            'description' => 'Ngobrol soal lagu, musisi, dan rekomendasi musik',
        ]);

        Board::create([
            'slug' => 'movies',
            'name' => 'Movies & TV',
            'description' => 'Review film, serial TV, dan rekomendasi tontonan',
        ]);

        Board::create([
            'slug' => 'books',
            'name' => 'Books',
            'description' => 'Diskusi buku, novel, dan literatur',
        ]);

        Board::create([
            'slug' => 'sports',
            'name' => 'Sports',
            'description' => 'Bahas olahraga, tim favorit, dan event olahraga dunia',
        ]);
        Board::create([
            'slug' => 'finance',
            'name' => 'Finance',
            'description' => 'Diskusi tentang keuangan',
        ]);
        Board::create([
            'slug' => 'creative',
            'name' => 'Creative',
            'description' => 'diskusi seputar bidang kreatif',
        ]);
    }
}

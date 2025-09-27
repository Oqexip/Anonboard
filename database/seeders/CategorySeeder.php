<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $boards = Board::all();

        foreach ($boards as $board) {
            $categories = ['Review', 'Recommendation', 'Discussion'];

            foreach ($categories as $cat) {
                Category::firstOrCreate(
                    [
                        'board_id' => $board->id,
                        'slug' => Str::slug($board->slug . '-' . $cat),
                    ],
                    [
                        'name' => $cat
                    ]
                );
            }
        }
    }
}

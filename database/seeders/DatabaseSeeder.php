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
        $this->call(
            [
                AdminUserSeeder::class,
                BoardSeeder::class,
                CategorySeeder::class,
                ]
            );
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cuisine;
use Illuminate\Database\Seeder;

class CuisineSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $cuisines = [
            ['name' => 'Japanese'],
            ['name' => 'Asian'],
            ['name' => 'Vietnamese'],
        ];

        Cuisine::upsert(
            $cuisines,
            ['name'],
            ['name']
        );
    }
}

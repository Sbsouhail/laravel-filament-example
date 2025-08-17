<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $cities = [
            ['name' => 'New York City'],
            ['name' => 'Los Angeles'],
            ['name' => 'Chicago'],
        ];

        City::upsert(
            $cities,
            ['name'],
            ['name']
        );
    }
}

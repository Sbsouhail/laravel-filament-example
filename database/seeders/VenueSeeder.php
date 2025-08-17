<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        $venues = [
            ['name' => 'Rooftop'],
            ['name' => 'Restaurant'],
            ['name' => 'Bar'],
            ['name' => 'Seaside Restaurant'],
        ];

        Venue::upsert(
            $venues,
            ['name'],
            ['name']
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/cities.json'); // JSON file path
        if (!File::exists($filePath)) {
            $this->command->error('cities.json file not found in database/data!');
            return;
        }

        $cities = json_decode(File::get($filePath), true);

        foreach ($cities as $city) {
            DB::table('cities')->updateOrInsert(
                ['id' => $city['id']],
                [
                    'state_id' => $city['state_id'] ?? null,
                    'name' => $city['name'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Cities seeded successfully.');
    }
}

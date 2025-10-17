<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StatesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/states.json'); // JSON file path
        if (!File::exists($filePath)) {
            $this->command->error('states.json file not found in database/data!');
            return;
        }

        $states = json_decode(File::get($filePath), true);

        foreach ($states as $state) {
            DB::table('states')->updateOrInsert(
                ['id' => $state['id']],
                [
                    'country_id' => $state['country_id'] ?? null,
                    'name' => $state['name'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('States seeded successfully.');
    }
}

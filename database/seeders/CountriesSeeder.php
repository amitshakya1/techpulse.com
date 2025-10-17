<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/countries.json'); // JSON file path
        if (!File::exists($filePath)) {
            $this->command->error('countries.json file not found in database/data!');
            return;
        }

        $countries = json_decode(File::get($filePath), true);

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['id' => $country['id']],
                [
                    'name' => $country['name'] ?? null,
                    'iso2' => $country['iso2'] ?? null,
                    'iso3' => $country['iso3'] ?? null,
                    'capital' => $country['capital'] ?? null,
                    'currency' => $country['currency'] ?? null,
                    'currency_name' => $country['currency_name'] ?? null,
                    'currency_symbol' => $country['currency_symbol'] ?? null,
                    'region' => $country['region'] ?? null,
                    'latitude' => $country['latitude'] ?? null,
                    'longitude' => $country['longitude'] ?? null,
                    'phonecode' => $country['phonecode'] ?? null,
                    'nationality' => $country['nationality'] ?? null,
                    'timezones' => isset($country['timezones']) ? json_encode($country['timezones']) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Countries seeded successfully.');
    }
}

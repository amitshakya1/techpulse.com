<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use App\Models\Currency;
class CurrencyJob extends BaseJob
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiUrl = config('services.exchangerates.api_url');
        $apiKey = config('services.exchangerates.api_key');
        $response = Http::withoutVerifying()->get("{$apiUrl}/{$apiKey}/latest/USD");

        if ($response->failed()) {
            throw new \Exception('Currency API request failed: ' . $response->status());
        }

        $data = $response->json();
        if ($data['result'] === "success") {
            $baseCurrency = $data['base_code'] ?? 'USD'; // usually USD
            foreach ($data['conversion_rates'] as $code => $rate) {
                // Update or create each currency
                Currency::updateOrCreate(
                    [
                        'base_code' => $baseCurrency,
                        'code' => $code
                    ],
                    [
                        'exchange_rate' => $rate,
                        'status' => 'active'
                    ]
                );
            }
        }
    }
}

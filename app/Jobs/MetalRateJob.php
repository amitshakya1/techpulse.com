<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use App\Models\MetalRate;
class MetalRateJob extends BaseJob
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
        $apiUrl = config('services.metalrates.api_url');
        $apiKey = config('services.metalrates.api_key');
        $metals = ['XAU' => 'Gold', 'XAG' => 'Silver', 'XPT' => 'Platinum', 'XPD' => 'Palladium'];

        foreach ($metals as $code => $name) {
            $response = Http::withHeaders([
                'x-access-token' => $apiKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->get("{$apiUrl}/{$code}/USD");

            if ($response->failed()) {
                throw new \Exception('Metal API request failed for ' . $code . ': ' . $response->status());
            }

            $data = $response->json();

            if (!empty($data)) {
                MetalRate::updateOrCreate(
                    [
                        'metal' => $data['metal'],
                        'currency' => $data['currency'], // make sure currency is part of unique key
                    ],
                    [
                        'name' => $name ?? null,
                        'exchange' => $data['exchange'] ?? null,
                        'symbol' => $data['symbol'] ?? null,
                        'prev_close_price' => $data['prev_close_price'] ?? null,
                        'open_price' => $data['open_price'] ?? null,
                        'low_price' => $data['low_price'] ?? null,
                        'high_price' => $data['high_price'] ?? null,
                        'open_time' => $data['open_time'] ?? null,
                        'price' => $data['price'] ?? null,
                        'ch' => $data['ch'] ?? null,
                        'chp' => $data['chp'] ?? null,
                        'ask' => $data['ask'] ?? null,
                        'bid' => $data['bid'] ?? null,
                        'price_gram_24k' => $data['price_gram_24k'] ?? null,
                        'price_gram_22k' => $data['price_gram_22k'] ?? null,
                        'price_gram_21k' => $data['price_gram_21k'] ?? null,
                        'price_gram_20k' => $data['price_gram_20k'] ?? null,
                        'price_gram_18k' => $data['price_gram_18k'] ?? null,
                        'price_gram_16k' => $data['price_gram_16k'] ?? null,
                        'price_gram_14k' => $data['price_gram_14k'] ?? null,
                        'price_gram_10k' => $data['price_gram_10k'] ?? null,
                    ]
                );
            }
        }
    }

}

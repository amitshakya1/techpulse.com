<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
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
        $response = Http::get('https://v6.exchangerate-api.com/v6/INVALID_KEY/latest/INR');

        if ($response->failed()) {
            throw new \Exception('Currency API request failed: ' . $response->status());
        }
    }
}

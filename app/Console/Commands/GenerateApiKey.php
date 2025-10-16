<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key 
                            {store_id? : The ID of the store}
                            {--status=active : The status of the API key (active, draft, archived)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API key for a store';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get or ask for store_id
        $storeId = $this->argument('store_id');

        if (!$storeId) {
            // Show available stores
            $stores = Store::select('id', 'name', 'domain')->get();

            if ($stores->isEmpty()) {
                $this->error('No stores found. Please create a store first.');
                return 1;
            }

            $this->info('Available Stores:');
            foreach ($stores as $store) {
                $this->line("  [{$store->id}] {$store->name} ({$store->domain})");
            }

            $storeId = $this->ask('Enter the Store ID');
        }

        // Validate store exists
        $store = Store::find($storeId);
        if (!$store) {
            $this->error("Store with ID {$storeId} not found.");
            return 1;
        }

        // Get status
        $status = $this->option('status');
        if (!in_array($status, ['active', 'draft', 'archived'])) {
            $this->error('Invalid status. Must be: active, draft, or archived');
            return 1;
        }

        // Generate API key
        $apiKeyValue = 'sk_' . Str::random(48);

        $apiKey = ApiKey::create([
            'store_id' => $storeId,
            'api_key' => $apiKeyValue,
            'status' => $status,
        ]);

        // Display success message
        $this->newLine();
        $this->info('✅ API Key Generated Successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Store ID', $apiKey->store_id],
                ['Store Name', $store->name],
                ['API Key', $apiKey->api_key],
                ['Status', $apiKey->status],
                ['Created At', $apiKey->created_at],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  Store this API key securely. It won\'t be shown again!');
        $this->newLine();

        // Show usage example
        $this->comment('Usage Example:');
        $this->line('curl -X GET http://api.techpulse.test:8000/v1/secure/test \\');
        $this->line('  -H "X-API-KEY: ' . $apiKey->api_key . '"');

        return 0;
    }
}

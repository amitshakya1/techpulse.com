<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait HandlesDatabaseTransactionsTrait
{
    /**
     * Run a database operation safely within a transaction
     *
     * @param  callable  $callback
     * @param  string|null  $errorMessage
     * @param  array  $context
     * @return mixed
     */
    protected function runSafely(callable $callback, ?string $errorMessage = null, array $context = [])
    {
        try {
            DB::beginTransaction();

            $result = $callback();

            DB::commit();

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error($errorMessage ?? 'Database operation failed', array_merge($context, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));

            // If your controller has errorResponse() from ApiResponseTrait
            return $this->errorResponse(
                $errorMessage ?? 'Something went wrong. Please try again later.',
                500
            );
        }
    }
}

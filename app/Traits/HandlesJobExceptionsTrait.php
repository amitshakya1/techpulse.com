<?php

namespace App\Traits;

use Throwable;
use Illuminate\Support\Facades\Log;

trait HandlesJobExceptionsTrait
{
    /**
     * Called automatically by Laravel when the job fails.
     */
    public function failed(Throwable $exception)
    {
        Log::error("Job failed: " . $exception->getMessage(), [
            'job' => static::class,
            'trace' => $exception->getTraceAsString(),
        ]);

        // Optional: Send alert/notification
        // Notification::route('mail', 'admin@example.com')
        //     ->notify(new JobFailedNotification($this, $exception));
    }
}

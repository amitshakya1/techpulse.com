<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Throwable;

class JobFailedNotification extends BaseNotification
{

    public string $jobClass;
    public Throwable $exception;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $jobClass, Throwable $exception)
    {
        $this->jobClass = $jobClass;
        $this->exception = $exception;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = new MailMessage;
        $message->subject('🚨 Job Failed: ' . class_basename($this->jobClass))
            ->greeting('Hello Admin,')
            ->line('A queued job has failed in your application.')
            ->line('**Job:** ' . $this->jobClass)
            ->line('**Error Message:** ' . $this->exception->getMessage())
            ->line('**File:** ' . $this->exception->getFile() . ':' . $this->exception->getLine())
            ->line('**Time:** ' . now()->toDateTimeString())
            ->line('---')
            ->line('**Stack Trace:**')
            ->line('```' . substr($this->exception->getTraceAsString(), 0, 2000) . '```') // avoid too long trace
            ->line('Please check the logs for more details.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job' => $this->jobClass,
            'message' => $this->exception->getMessage(),
            'file' => $this->exception->getFile(),
            'line' => $this->exception->getLine(),
            'failed_at' => now()->toDateTimeString(),
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends BaseNotification
{
    public $token;
    public $subdomain;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $subdomain)
    {
        $this->token = $token;
        $this->subdomain = $subdomain;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = url(
            route(
                "{$this->subdomain}.reset-password",
                ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()],
            )
        );

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }


}

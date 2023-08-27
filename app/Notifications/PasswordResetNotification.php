<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $password_reset_code;
    public $user;
    public function __construct($password_reset_code, $user)
    {
        $this->password_reset_code = $password_reset_code;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset Request - Confirmation Code Required')
            ->greeting('Dear ' . $this->user->name)
            ->line("We have received a request to reset the password for your account at " . config('app.name') . "To ensure the security of your account, please enter the confirmation code below.")
            ->line('Confirmation Code: ' . $this->password_reset_code->confirmation_code)
            ->line('Please note that this code will expire after 30 minutes for security reasons. If you do not use it within this timeframe, you will need to initiate the password reset process again.')
            ->line('Thank you for your attention to this matter.
            ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

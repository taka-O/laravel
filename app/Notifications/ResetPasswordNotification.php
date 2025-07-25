<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $reset_url;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $reset_url)
    {
       $this->token = $token;
       $this->token = $reset_url;
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
        $url = $reset_url . '?' . $this->token;

        return (new MailMessage)
            ->subject('パスワードリセット通知')
            ->line('パスワードリセットのリクエストを受け付けました。')
            ->action('パスワードをリセット', $url)
            ->line('このメールに心当たりがない場合は、何もする必要はありません。');
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

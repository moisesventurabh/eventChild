<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Configure FRONTEND_URL no .env, ex: FRONTEND_URL=http://localhost:5173
        $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', config('app.url'))), '/');
        $url = "{$frontendUrl}/?token={$this->token}&email=" . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage())
            ->subject('Redefinição de senha - ' . config('app.name'))
            ->line('Você solicitou a redefinição da sua senha.')
            ->action('Redefinir senha', $url)
            ->line('Este link expira em 60 minutos.')
            ->line('Se você não solicitou a redefinição, nenhuma ação é necessária.');
    }
}
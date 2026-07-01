<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Permintaan Perubahan Kata Sandi')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Permintaan pengaturan ulang kata sandi telah diterima.')
            ->line('Jika Anda tidak melakukan permintaan ini, abaikan email ini.');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda - '.config('app.name'))
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Terima kasih telah mendaftar di '.config('app.name').'.')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun Anda.')
            ->action('Verifikasi Alamat Email', $verificationUrl)
            ->line('Jika Anda tidak melakukan pendaftaran ini, silakan abaikan email ini.')
            ->salutation('Salam hangat,'."\n".'Tim '.config('app.name'));
    }
}

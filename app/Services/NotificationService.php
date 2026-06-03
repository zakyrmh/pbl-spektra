<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim push notification ke user.
     *
     * @param  int|string  $userId
     */
    public function sendPush($userId, string $message): bool
    {
        Log::info("Simulasi Push Notification ke User ID [{$userId}]: {$message}");

        return true;
    }

    /**
     * Kirim pesan WhatsApp ke nomor telepon.
     */
    public function sendWhatsApp(string $phone, string $message): bool
    {
        Log::info("Simulasi WhatsApp ke [{$phone}]: {$message}");

        return true;
    }

    /**
     * Pemicu panggilan suara nomor antrean di loket.
     */
    public function triggerVoiceCall(string $queueNumber, string $counter): bool
    {
        Log::info("Simulasi Panggilan Suara: Nomor Antrean {$queueNumber} silakan menuju {$counter}");

        return true;
    }
}

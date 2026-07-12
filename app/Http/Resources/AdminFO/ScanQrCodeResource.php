<?php

declare(strict_types=1);

namespace App\Http\Resources\AdminFO;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanQrCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'queue_number' => $this->queue_number,
            'status' => $this->status->value ?? $this->status,
            'booking_code' => $this->booking_code,
            'purpose' => $this->purpose,
            'user_name' => $this->user?->name ?? 'Walk-In Citizen',
            'department_name' => $this->department?->name ?? '-',
        ];
    }
}

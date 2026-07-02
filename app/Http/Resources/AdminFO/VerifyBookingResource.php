<?php

declare(strict_types=1);

namespace App\Http\Resources\AdminFO;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerifyBookingResource extends JsonResource
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
            'booking_code' => $this->booking_code,
            'user' => [
                'name' => $this->user->name,
                'nik' => $this->user->nik,
            ],
            'department' => [
                'name' => $this->department?->name ?? '-',
            ],
            'service' => [
                'name' => $this->purpose ?? '-',
            ],
        ];
    }
}

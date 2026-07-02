<?php

declare(strict_types=1);

namespace App\Http\Resources\AdminGerai;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalledQueueResource extends JsonResource
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
            'called_at' => $this->called_at?->toIso8601String(),
            'user' => [
                'name' => $this->user?->name ?? 'Warga',
                'nik' => $this->user?->nik ?? '-',
            ],
            'purpose' => $this->purpose ?? 'Layanan Umum',
        ];
    }
}

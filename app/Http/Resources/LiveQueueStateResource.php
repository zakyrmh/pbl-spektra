<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveQueueStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [
                'id' => null,
                'booking_code' => null,
                'status' => null,
                'queue_number' => null,
            ];
        }

        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'called_at' => $this->called_at ? $this->called_at->toIso8601String() : null,
            'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
            'visitor_name' => $this->user ? $this->user->name : 'Warga',
        ];
    }
}

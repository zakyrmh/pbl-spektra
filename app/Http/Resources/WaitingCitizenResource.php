<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaitingCitizenResource extends JsonResource
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
            ];
        }

        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'status' => $this->status,
            'purpose' => $this->purpose,
            'session_name' => $this->session_name,
            'checked_in_at' => $this->checked_in_at ? $this->checked_in_at->toIso8601String() : null,
            'visitor_name' => $this->user ? $this->user->name : 'Warga',
        ];
    }
}

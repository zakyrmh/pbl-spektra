<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Queue $resource
 */
class PrintedTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'queue_number' => $this->resource->queue_number,
            'booking_code' => $this->resource->booking_code,
            'purpose' => $this->resource->purpose,
            'status' => $this->resource->status,
            'booking_date' => $this->resource->booking_date->toDateString(),
            'visitor' => [
                'name' => $this->resource->user->name,
                'nik' => $this->resource->user->nik,
                'phone' => $this->resource->user->no_telp,
            ],
            'department' => [
                'id' => $this->resource->department->id,
                'name' => $this->resource->department->name,
                'inisial' => $this->resource->department->inisial,
                'nomor_loket' => $this->resource->department->nomor_loket,
            ],
            'printed_at' => $this->resource->created_at->toIso8601String(),
        ];
    }
}

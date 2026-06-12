<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Department $resource
 */
class ActiveQueueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get the active queue (e.g. status is 'Serving' today)
        $activeQueue = $this->resource->queues->first();

        return [
            'counter_id' => $this->resource->id,
            'counter_name' => $this->resource->name,
            'department_name' => $this->resource->name,
            'status' => $this->resource->status->value,
            'active_number' => $activeQueue ? $activeQueue->queue_number : '-',
            'active_status' => $activeQueue ? $activeQueue->status : 'Idle',
            'called_at' => $activeQueue && $activeQueue->called_at ? $activeQueue->called_at->toIso8601String() : null,
        ];
    }
}

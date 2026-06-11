<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ActivityLog
 */
class LiveActivityResource extends JsonResource
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
            'causer' => $this->causer ? [
                'id' => $this->causer->id,
                'name' => $this->causer->name,
                'email' => $this->causer->email,
            ] : null,
            'event' => $this->event,
            'action' => $this->action, // backward compatibility helper
            'description' => $this->description,
            'properties' => $this->properties,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at->toDateTimeString(),
            'time_formatted' => $this->created_at->format('H:i'),
        ];
    }
}

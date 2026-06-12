<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorLookupResource extends JsonResource
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
                'name' => null,
                'no_telp' => null,
                'is_found' => false,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'no_telp' => $this->no_telp,
            'is_found' => true,
        ];
    }
}

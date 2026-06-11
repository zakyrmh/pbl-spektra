<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Ubah resource menjadi array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'nik' => $this->nik,
            'no_telp' => $this->no_telp,
            'role' => $this->role?->value,
            'role_label' => $this->role_label,
            'department' => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'nomor_loket' => $this->department->nomor_loket,
            ] : null,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

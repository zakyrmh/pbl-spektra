<?php

declare(strict_types=1);

namespace App\Data\AdminFO;

use App\Http\Requests\AdminFO\StoreWalkInTicketRequest;

class WalkInTicketData
{
    public function __construct(
        public int $departmentId,
        public string $name,
        public ?string $nik,
        public string $phone,
        public string $purpose,
        public bool $isPriority = false
    ) {}

    /**
     * Map request payload to DTO.
     */
    public static function fromRequest(StoreWalkInTicketRequest $request): self
    {
        return new self(
            departmentId: (int) $request->input('department_id'),
            name: trim($request->string('name')->toString()),
            nik: $request->filled('nik') ? trim($request->string('nik')->toString()) : null,
            phone: trim($request->string('phone')->toString()),
            purpose: trim($request->string('purpose')->toString()),
            isPriority: (bool) $request->input('is_priority', false)
        );
    }
}

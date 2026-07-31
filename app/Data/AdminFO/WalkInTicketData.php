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
        public bool $isPriority = false,
        public ?array $nextDepartmentIds = null
    ) {}

    /**
     * Map request payload to DTO.
     */
    public static function fromRequest(StoreWalkInTicketRequest $request): self
    {
        $nextDeptIds = $request->input('next_department_ids');
        if (is_array($nextDeptIds)) {
            $nextDeptIds = array_values(array_unique(array_filter(array_map('intval', $nextDeptIds), fn ($id) => $id > 0 && $id !== (int) $request->input('department_id'))));
        } else {
            $nextDeptIds = null;
        }

        return new self(
            departmentId: (int) $request->input('department_id'),
            name: trim($request->string('name')->toString()),
            nik: $request->filled('nik') ? trim($request->string('nik')->toString()) : null,
            phone: trim($request->string('phone')->toString()),
            purpose: trim($request->string('purpose')->toString()),
            isPriority: (bool) $request->input('is_priority', false),
            nextDepartmentIds: $nextDeptIds
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Data\SuperAdmin;

use Carbon\Carbon;

final class UserSessionData
{
    /**
     * @param  array{browser: string, os: string, device: string}  $browser_info
     */
    public function __construct(
        public string $id,
        public ?string $ip_address,
        public Carbon $last_activity_at,
        public bool $is_recent,
        public array $browser_info
    ) {}

    /**
     * Pabrikasi DTO dari session row database.
     *
     * @param  array{browser: string, os: string, device: string}  $browserInfo
     */
    public static function fromRow(
        object $row,
        array $browserInfo,
        Carbon $lastActivityAt,
        bool $isRecent
    ): self {
        return new self(
            id: (string) $row->id,
            ip_address: $row->ip_address,
            last_activity_at: $lastActivityAt,
            is_recent: $isRecent,
            browser_info: $browserInfo
        );
    }
}

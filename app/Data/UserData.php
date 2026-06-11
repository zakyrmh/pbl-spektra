<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UserData
{
    public function __construct(
        public string $name,
        public ?string $nik,
        public string $email,
        public ?string $no_telp,
        public UserRole $role,
        public ?int $department_id,
        public ?string $nomor_loket,
        public ?string $password = null
    ) {}

    /**
     * Pabrikasi DTO dari objek Form Request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        $role = $request->input('role');
        $roleEnum = $role instanceof UserRole ? $role : UserRole::from((string) $role);

        return new self(
            name: (string) $request->input('name'),
            nik: $request->input('nik') ? (string) $request->input('nik') : null,
            email: (string) $request->input('email'),
            no_telp: $request->input('no_telp') ? (string) $request->input('no_telp') : null,
            role: $roleEnum,
            department_id: $request->input('departments_id') ? (int) $request->input('departments_id') : null,
            nomor_loket: $request->input('nomor_loket') ? (string) $request->input('nomor_loket') : null,
            password: $request->input('password') ? (string) $request->input('password') : null
        );
    }

    /**
     * Konversi properti DTO menjadi array asosiatif (tanpa password).
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'nik' => $this->nik,
            'email' => $this->email,
            'no_telp' => $this->no_telp,
            'role' => $this->role->value,
            'department_id' => $this->department_id,
            'nomor_loket' => $this->nomor_loket,
        ];
    }
}

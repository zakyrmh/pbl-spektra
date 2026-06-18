<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk memperbarui user ini.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'role' => ['required', Rule::in(UserRole::values())],
            'departments_id' => ['nullable', 'integer', 'exists:departments,id', Rule::requiredIf($this->input('role') === UserRole::AdminGerai->value)],
            'nomor_loket' => ['nullable', 'string', 'max:10'],
        ];
    }
}

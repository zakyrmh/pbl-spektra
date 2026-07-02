<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat user.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:users,nik'],
            'email' => ['required', 'email', 'unique:users,email'],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'role' => ['required', Rule::in(UserRole::values())],
            'departments_id' => ['nullable', 'integer', 'exists:departments,id', Rule::requiredIf($this->input('role') === UserRole::AdminGerai->value)],
            'nomor_loket' => ['nullable', 'string', 'max:10'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ];
    }
}

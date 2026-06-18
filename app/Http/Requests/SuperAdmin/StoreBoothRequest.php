<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBoothRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat booth.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('viewAny', User::class);
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
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial'],
            'nomor_loket' => ['required', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }
}

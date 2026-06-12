<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PapanPanggilActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user && ($user->role === UserRole::AdminGerai || $user->role === UserRole::SuperAdmin);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post') && str_contains((string) $this->route()?->getName(), 'skip')) {
            return [
                'cancel_reason' => ['required', 'string', 'min:5', 'max:255'],
            ];
        }

        return [];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cancel_reason.required' => 'Alasan pembatalan/melewati antrean harus diisi.',
            'cancel_reason.min' => 'Alasan pembatalan harus minimal 5 karakter.',
        ];
    }
}

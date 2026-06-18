<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminGerai;

use App\Models\Queue;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WaitingListActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role->value ?? $user->role, ['admin_gerai', 'super_admin'])) {
            return false;
        }

        $booking = $this->route('booking');
        if ($booking instanceof Queue) {
            return $booking->department_id === $user->departments_id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->routeIs('admin.daftar-tunggu.cancel')) {
            return [
                'reason' => ['required', 'string', 'min:5'],
            ];
        }

        return [];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan pembatalan wajib diisi.',
            'reason.min' => 'Alasan pembatalan minimal harus 5 karakter.',
        ];
    }
}

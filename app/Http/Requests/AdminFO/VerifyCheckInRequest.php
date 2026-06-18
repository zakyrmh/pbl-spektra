<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminFO;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyCheckInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_code' => ['required', 'string', 'max:36'],
            'nik_input' => ['nullable', 'string', 'digits:16'],
        ];
    }
}

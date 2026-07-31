<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminFO;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApiStoreWalkInTicketRequest extends FormRequest
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
            'nik' => ['required', 'string', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'next_department_ids' => ['nullable', 'array'],
            'next_department_ids.*' => ['integer', 'exists:departments,id', 'different:department_id'],
            'phone' => ['required', 'string', 'regex:/^(08[0-9]{8,13}|\+628[0-9]{8,11})$/'],
            'is_priority' => ['nullable', 'boolean'],
        ];
    }
}

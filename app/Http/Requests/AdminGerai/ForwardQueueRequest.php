<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminGerai;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForwardQueueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $role = $this->user()?->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : (string) $role;

        return in_array($roleValue, [
            UserRole::AdminGerai->value,
            UserRole::SuperAdmin->value,
        ], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    /**
     * Human-readable attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'target_department_id' => 'instansi tujuan',
        ];
    }
}

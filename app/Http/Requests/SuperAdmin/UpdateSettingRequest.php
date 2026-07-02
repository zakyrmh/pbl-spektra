<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
            'app_name' => ['required', 'string', 'max:255'],
            'app_logo' => ['required', 'string', 'max:255'],
            'maintenance_mode' => ['required', 'in:0,1'],
            'marquee_text' => ['required', 'string', 'max:500'],
            'marquee_active' => ['required', 'in:0,1'],
            'reverb_host' => ['required', 'string', 'max:255'],
            'reverb_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'reverb_scheme' => ['required', 'in:http,https'],
            'websocket_enabled' => ['required', 'in:0,1'],
        ];
    }
}

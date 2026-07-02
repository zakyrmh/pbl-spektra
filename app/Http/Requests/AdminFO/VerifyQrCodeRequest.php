<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminFO;

use Illuminate\Foundation\Http\FormRequest;

class VerifyQrCodeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Checked-In,Serving'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Data QR code/booking code wajib dikirimkan.',
            'code.string' => 'Data QR code/booking code harus berupa string.',
            'status.in' => 'Status target tidak valid. Pilih antara Checked-In atau Serving.',
        ];
    }
}

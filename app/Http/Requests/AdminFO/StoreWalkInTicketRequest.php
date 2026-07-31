<?php

declare(strict_types=1);

namespace App\Http\Requests\AdminFO;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWalkInTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->value === 'admin_fo';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'next_department_ids' => ['nullable', 'array'],
            'next_department_ids.*' => ['integer', 'exists:departments,id', 'different:department_id'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'nik' => ['nullable', 'string', 'digits:16'],
            'phone' => ['required', 'string', 'min:8', 'max:15'],
            'purpose' => ['required', 'string', 'min:5', 'max:500'],
            'is_priority' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom validation messages in Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.required' => 'Silakan pilih instansi tujuan.',
            'department_id.exists' => 'Instansi yang dipilih tidak valid.',
            'name.required' => 'Nama lengkap warga wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.min' => 'Nomor telepon minimal 8 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 karakter.',
            'purpose.required' => 'Keperluan kunjungan wajib diisi.',
            'purpose.min' => 'Keperluan minimal 5 karakter.',
        ];
    }

    /**
     * Get custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'department_id' => 'instansi',
            'name' => 'nama lengkap',
            'nik' => 'NIK',
            'phone' => 'nomor telepon',
            'purpose' => 'keperluan kunjungan',
        ];
    }
}

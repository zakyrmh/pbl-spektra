<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBookingRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'keperluan' => ['required', 'string', 'min:5', 'max:255'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.required' => 'Silakan pilih instansi / lembaga.',
            'department_id.exists' => 'Instansi terpilih tidak valid.',
            'keperluan.required' => 'Silakan ketik keperluan Anda.',
            'keperluan.min' => 'Keperluan harus minimal 5 karakter.',
            'keperluan.max' => 'Keperluan tidak boleh lebih dari 255 karakter.',
            'booking_date.required' => 'Silakan pilih tanggal booking.',
            'booking_date.date' => 'Format tanggal booking tidak valid.',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh hari kemarin.',
        ];
    }
}

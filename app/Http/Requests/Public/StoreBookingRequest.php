<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Carbon\Carbon;
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
            'next_department_ids' => ['nullable', 'array'],
            'next_department_ids.*' => ['integer', 'exists:departments,id', 'different:department_id'],
            'keperluan' => ['required', 'string', 'min:5', 'max:255'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'session_name' => [
                'required',
                'string',
                'in:Sesi 1,Sesi 2',
                function ($attribute, $value, $fail) {
                    $date = $this->input('booking_date');
                    if ($date) {
                        try {
                            $parsedDate = Carbon::parse($date);
                            if ($parsedDate->isToday()) {
                                $now = Carbon::now();
                                if ($value === 'Sesi 1' && $now->hour >= 12) {
                                    $fail('Sesi 1 tidak dapat dipilih karena waktu pelayanan sesi pagi (sebelum jam 12:00) untuk hari ini telah berakhir.');
                                } elseif ($value === 'Sesi 2' && $now->hour >= 15) {
                                    $fail('Sesi 2 tidak dapat dipilih karena batas waktu pelayanan hari ini (15:00) telah berakhir.');
                                }
                            }
                        } catch (\Exception $e) {
                            // Let general date validation handle invalid dates
                        }
                    }
                },
            ],
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
            'session_name.required' => 'Silakan pilih sesi booking.',
            'session_name.in' => 'Sesi booking terpilih tidak valid.',
        ];
    }
}

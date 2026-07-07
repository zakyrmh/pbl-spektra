<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'ktp_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'is_priority' => ['nullable', 'boolean'],
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
            'phone_number.regex' => 'Format nomor HP tidak valid (harus diawali 08 dan berisi 10-15 angka).',
            'avatar.max' => 'Ukuran foto profil tidak boleh melebihi 2MB.',
            'avatar.mimes' => 'Format foto profil harus JPG atau PNG.',
            'ktp_photo.max' => 'Ukuran foto KTP tidak boleh melebihi 2MB.',
            'ktp_photo.mimes' => 'Format foto KTP harus JPG atau PNG.',
        ];
    }
}

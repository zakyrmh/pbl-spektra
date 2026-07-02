<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Models\Department;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBoothRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk memperbarui booth.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('viewAny', User::class);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department');
        // Handle if it's passed as a model instance or ID string
        $id = is_object($departmentId) ? $departmentId->id : $departmentId;

        return [
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial,'.$id],
            'nomor_loket' => ['required', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Daftarkan callback setelah validasi dasar selesai.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $name = $this->input('name');
            if ($name) {
                $lowerName = strtolower(trim($name));
                $department = $this->route('department');
                $id = is_object($department) ? $department->id : $department;

                $exists = Department::query()
                    ->whereRaw('LOWER(name) = ?', [$lowerName])
                    ->where('id', '!=', $id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('name', 'nama loket sudah digunakan');
                }
            }
        });
    }

    /**
     * Dapatkan pesan kesalahan kustom untuk aturan validasi.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama instansi wajib diisi.',
            'inisial.required' => 'Kode prefix antrean wajib diisi.',
            'inisial.unique' => 'Kode prefix antrean sudah digunakan.',
            'nomor_loket.required' => 'Nomor loket wajib diisi.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.max' => 'Ukuran logo tidak boleh lebih dari 2MB.',
        ];
    }
}

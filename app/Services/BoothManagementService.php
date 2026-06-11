<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\BoothData;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class BoothManagementService
{
    /**
     * Dapatkan data index untuk manajemen gerai/booth.
     */
    public function getBoothConfigIndexData(): array
    {
        $totalDepartments = Department::query()->count('*');
        $totalStaff = User::query()->where('role', UserRole::AdminGerai->value)->count('*');

        // Mengambil departemen tanpa eager loading counters/services yang sudah dihapus
        $departments = Department::query()->latest()->get();

        // Petugas loket untuk form penugasan
        $officers = User::query()->where('role', UserRole::AdminGerai->value)->get();

        return compact('totalDepartments', 'totalStaff', 'departments', 'officers');
    }

    /**
     * Buat booth/gerai (Department) baru.
     */
    public function createBooth(BoothData $data): Department
    {
        $attributes = $data->toArray();

        if ($data->logo instanceof UploadedFile) {
            $attributes['logo'] = $this->processLogoUpload($data->logo);
        } else {
            $attributes['logo'] = null;
        }

        $department = Department::create($attributes);

        // Jika DTO status di-set, set status-nya (menggunakan accessor/mutator)
        if ($data->status) {
            $department->status = $data->status;
            $department->save();
        }

        AuditLogger::log(
            event: 'department_created',
            description: "Gerai baru '{$department->name}' (Inisial: {$department->inisial}) berhasil dibuat.",
            subject: $department,
            properties: ['after' => $department->toArray()]
        );

        return $department;
    }

    /**
     * Perbarui data booth/gerai (Department).
     */
    public function updateBooth(Department $department, BoothData $data): Department
    {
        $attributes = $data->toArray();
        $before = $department->toArray();

        if ($data->logo instanceof UploadedFile) {
            // Hapus logo lama jika ada
            if ($department->logo) {
                Storage::disk('public')->delete($department->logo);
            }
            $attributes['logo'] = $this->processLogoUpload($data->logo);
        } else {
            // Tetap gunakan logo yang lama jika tidak diupload yang baru
            unset($attributes['logo']);
        }

        $department->fill($attributes);

        if ($data->status) {
            $department->status = $data->status;
        }

        $department->save();

        AuditLogger::log(
            event: 'department_updated',
            description: "Data Gerai '{$department->name}' berhasil diperbarui.",
            subject: $department,
            properties: [
                'before' => $before,
                'after' => Department::find($department->id)->toArray(),
            ]
        );

        return $department;
    }

    /**
     * Hapus booth/gerai (Department).
     */
    public function deleteBooth(Department $department): void
    {
        $name = $department->name;

        if ($department->logo) {
            Storage::disk('public')->delete($department->logo);
        }

        AuditLogger::log(
            event: 'department_deleted',
            description: "Gerai '{$name}' (Inisial: {$department->inisial}) dihapus dari sistem.",
            subject: $department,
            properties: ['snapshot' => $department->toArray()]
        );

        $department->delete();
    }

    /**
     * Proses upload logo dan konversi ke WebP.
     */
    private function processLogoUpload(UploadedFile $file): string
    {
        $filename = 'logos/'.bin2hex(random_bytes(20)).'.webp';
        $manager = new ImageManager(new Driver);
        $encoded = $manager->decode($file->getContent())->encode(new WebpEncoder(quality: 80));

        Storage::disk('public')->put($filename, $encoded->toString());

        return $filename;
    }
}

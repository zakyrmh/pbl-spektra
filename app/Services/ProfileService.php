<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update the user profile data and log the event.
     */
    public function updateProfile(User $user, array $data): User
    {
        $before = [
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'avatar_path' => $user->avatar_path,
            'ktp_photo_path' => $user->ktp_photo_path,
        ];

        // Update fields if provided
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['phone_number'])) {
            $user->phone_number = $data['phone_number'];
        }

        // Handle Avatar upload
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            // Delete old avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            // Store new avatar
            $user->avatar_path = $data['avatar']->store('avatars', 'public');
        }

        // Handle KTP photo upload
        if (isset($data['ktp_photo']) && $data['ktp_photo'] instanceof UploadedFile) {
            // Delete old KTP photo if exists
            if ($user->ktp_photo_path) {
                Storage::disk('public')->delete($user->ktp_photo_path);
            }
            // Store new KTP photo
            $user->ktp_photo_path = $data['ktp_photo']->store('ktp_photos', 'public');
        }

        $user->save();

        $after = [
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'avatar_path' => $user->avatar_path,
            'ktp_photo_path' => $user->ktp_photo_path,
        ];

        // Audit Trail logging
        AuditLogger::userUpdated($user, $before, $after);

        return $user;
    }
}

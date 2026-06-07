<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /**
     * Tampilkan form edit profil.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Proses update profil pengunjung.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'avatar' => [
                'nullable',
                File::image()
                    ->max(2048)
                    ->types(['jpeg', 'jpg', 'png']),
            ],
            'ktp_photo' => [
                'nullable',
                File::image()
                    ->max(2048)
                    ->types(['jpeg', 'jpg', 'png']),
            ],
        ], [
            'phone_number.regex' => 'Format nomor HP tidak valid (harus diawali 08 dan berisi 10-15 angka).',
            'avatar.max' => 'Ukuran foto profil tidak boleh melebihi 2MB.',
            'avatar.mimes' => 'Format foto profil harus JPG atau PNG.',
            'ktp_photo.max' => 'Ukuran foto KTP tidak boleh melebihi 2MB.',
            'ktp_photo.mimes' => 'Format foto KTP harus JPG atau PNG.',
        ]);

        $this->profileService->updateProfile($user, $validated);

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Data profil berhasil diperbarui.');
    }
}

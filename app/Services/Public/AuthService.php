<?php

declare(strict_types=1);

namespace App\Services\Public;

use App\Exceptions\Public\UnverifiedEmailException;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AuthService
{
    /**
     * Attempt to log in a user.
     *
     * @throws ValidationException
     * @throws UnverifiedEmailException
     */
    public function login(string $email, string $password, bool $remember): User
    {
        $field = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        if (Auth::attempt([
            $field => $email,
            'password' => $password,
        ], $remember)) {
            /** @var User $user */
            $user = Auth::user();

            if (! $user->hasVerifiedEmail()) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                session(['unverified_email' => $user->email]);

                throw new UnverifiedEmailException($user->email);
            }

            session()->regenerate();
            $user->update(['last_login_at' => now()]);
            AuditLogger::userLoggedIn($user);

            return $user;
        }

        throw ValidationException::withMessages([
            'email' => 'NIK/Email atau password salah.',
        ]);
    }

    /**
     * Register a new visitor user.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'nik' => $data['nik'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password']),
            'role' => 'pengunjung',
        ]);

        event(new Registered($user));
        session(['unverified_email' => $user->email]);

        return $user;
    }

    /**
     * Send password reset link.
     *
     * @throws ValidationException
     */
    public function sendResetLink(string $email): string
    {
        $status = PasswordFacade::sendResetLink(['email' => $email]);

        if ($status === PasswordFacade::RESET_LINK_SENT) {
            return $status;
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    /**
     * Reset password.
     *
     * @throws ValidationException
     */
    public function resetPassword(array $credentials): string
    {
        $status = PasswordFacade::reset(
            $credentials,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordFacade::PASSWORD_RESET) {
            return $status;
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            AuditLogger::userLoggedOut($user);
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Verify user email.
     */
    public function verify(User $user, string $hash): void
    {
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(401, 'Tautan verifikasi tidak valid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Auth::login($user);
        session()->regenerate();
        $user->update(['last_login_at' => now()]);
        AuditLogger::userLoggedIn($user);
    }

    /**
     * Resend verification notification.
     *
     * @throws ValidationException
     */
    public function resendNotification(?string $email): void
    {
        if (! $email) {
            throw ValidationException::withMessages([
                'email' => 'Silakan masuk terlebih dahulu atau registrasi ulang.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw new \RuntimeException('Email Anda sudah terverifikasi.');
        }

        $user->sendEmailVerificationNotification();
    }
}

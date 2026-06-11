<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam '.$seconds.' detik.',
            ])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            /** @var User $user */
            $user = Auth::user();

            // Cek apakah email sudah terverifikasi
            if (! $user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Simpan email di session untuk kirim ulang verification link
                session(['unverified_email' => $user->email]);

                return redirect()->route('verification.notice')->withErrors([
                    'email' => 'Email Anda belum terverifikasi. Silakan cek email Anda untuk melakukan verifikasi.',
                ]);
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Catat waktu login terakhir untuk tracking "staf aktif online"
            $user->update(['last_login_at' => now()]);

            // Audit trail: catat event login
            AuditLogger::userLoggedIn($user);

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($throttleKey, 60); // Blokir selama 60 detik jika mencapai limit

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Tampilkan halaman registrasi
    public function register()
    {
        return view('auth.register');
    }

    // Proses registrasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'digits:16', 'unique:users,nik'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'regex:/^(08[0-9]{8,13}|\+628[0-9]{8,11})$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Alamat email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'phone_number.required' => 'Nomor HP wajib diisi.',
            'phone_number.regex' => 'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 karakter).',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.letters' => 'Password harus mengandung minimal satu huruf.',
            'password.numbers' => 'Password harus mengandung minimal satu angka.',
            'password.symbols' => 'Password harus mengandung minimal satu simbol.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => 'pengunjung',
        ]);

        // Memicu event Registered agar notifikasi verifikasi email dikirim
        event(new Registered($user));

        // Simpan email di session untuk konfirmasi
        session(['unverified_email' => $user->email]);

        return redirect()->route('verification.notice')->with('success', 'Registrasi berhasil. Silakan cek email Anda untuk melakukan verifikasi.');
    }

    // Tampilkan halaman lupa password
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Proses reset password
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = PasswordFacade::sendResetLink(
            $request->only('email')
        );

        if ($status === PasswordFacade::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    // Tampilkan form reset password
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // Proses update password baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = PasswordFacade::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == PasswordFacade::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Audit trail: catat event logout sebelum sesi dihapus
        if ($user) {
            AuditLogger::userLoggedOut($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Tampilkan halaman konfirmasi verifikasi email
    public function notice(Request $request)
    {
        $email = session('unverified_email');

        if (! $email && Auth::check()) {
            $user = Auth::user();
            if ($user->hasVerifiedEmail()) {
                return redirect('/dashboard');
            }
            $email = $user->email;
            session(['unverified_email' => $email]);
        }

        if (! $email) {
            return redirect()->route('login');
        }

        return view('auth.verify-email', ['email' => $email]);
    }

    // Proses link verifikasi di email (Auto-Login & Redirect Dashboard)
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan verifikasi tidak valid atau telah kedaluwarsa.');
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(401, 'Tautan verifikasi tidak valid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Auth::login($user);

        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);
        AuditLogger::userLoggedIn($user);

        return redirect('/dashboard')->with('success', 'Email Anda berhasil diverifikasi. Selamat datang di dashboard!');
    }

    // Kirim ulang email verifikasi
    public function resend(Request $request)
    {
        $email = session('unverified_email');

        if (! $email && Auth::check()) {
            $email = Auth::user()->email;
        }

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'email' => 'Silakan masuk terlebih dahulu atau registrasi ulang.',
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard')->with('success', 'Email Anda sudah terverifikasi.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

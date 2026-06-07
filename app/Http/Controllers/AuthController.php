<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
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
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Catat waktu login terakhir untuk tracking "staf aktif online"
            Auth::user()->update(['last_login_at' => now()]);

            // Audit trail: catat event login
            AuditLogger::userLoggedIn(Auth::user());

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
            'phone_number' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'phone_number.regex' => 'Format nomor HP tidak valid (harus diawali 08 dan berisi 10-15 angka).',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role' => 'pengunjung',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
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
}

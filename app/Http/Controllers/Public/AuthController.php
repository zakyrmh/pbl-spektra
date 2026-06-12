<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Exceptions\Public\UnverifiedEmailException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ForgotPasswordRequest;
use App\Http\Requests\Public\LoginRequest;
use App\Http\Requests\Public\RegisterRequest;
use App\Http\Requests\Public\ResetPasswordRequest;
use App\Models\User;
use App\Services\Public\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Tampilkan halaman login
     */
    public function index(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi/login
     */
    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        try {
            $this->authService->login(
                email: $request->input('email'),
                password: $request->input('password'),
                remember: $request->boolean('remember')
            );

            $request->clear();
        } catch (UnverifiedEmailException $e) {
            $request->clear();

            return redirect()->route('verification.notice')->withErrors([
                'email' => $e->getMessage(),
            ]);
        } catch (ValidationException $e) {
            $request->hit();

            return back()->withErrors($e->errors())->onlyInput('email');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Tampilkan halaman registrasi
     */
    public function register(): View
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        return redirect()->route('verification.notice')->with('success', 'Registrasi berhasil. Silakan cek email Anda untuk melakukan verifikasi.');
    }

    /**
     * Tampilkan halaman lupa password
     */
    public function forgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengiriman link reset password
     */
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        try {
            $status = $this->authService->sendResetLink($request->input('email'));

            return back()->with('status', __($status));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses update password baru
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        try {
            $status = $this->authService->resetPassword(
                $request->only('email', 'password', 'password_confirmation', 'token')
            );

            return redirect()->route('login')->with('status', __($status));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Proses logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect('/login');
    }

    /**
     * Tampilkan halaman konfirmasi verifikasi email
     */
    public function notice(Request $request): RedirectResponse|View
    {
        $email = session('unverified_email');

        if (! $email && Auth::check()) {
            /** @var User $user */
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

    /**
     * Proses link verifikasi di email (Auto-Login & Redirect Dashboard)
     */
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan verifikasi tidak valid atau telah kedaluwarsa.');
        }

        /** @var User $user */
        $user = User::findOrFail($id);

        $this->authService->verify($user, $hash);

        return redirect('/dashboard')->with('success', 'Email Anda berhasil diverifikasi. Selamat datang di dashboard!');
    }

    /**
     * Kirim ulang email verifikasi
     */
    public function resend(Request $request): RedirectResponse
    {
        $email = session('unverified_email') ?: (Auth::check() ? Auth::user()->email : null);

        try {
            $this->authService->resendNotification($email);
        } catch (ValidationException $e) {
            return redirect()->route('login')->withErrors($e->errors());
        } catch (\RuntimeException $e) {
            return redirect('/dashboard')->with('success', $e->getMessage());
        }

        return back()->with('status', 'verification-link-sent');
    }
}

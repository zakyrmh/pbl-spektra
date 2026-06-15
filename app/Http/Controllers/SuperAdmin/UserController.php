<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Data\SuperAdmin\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\User;
use App\Services\SuperAdmin\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function __construct(
        protected UserManagementService $userService
    ) {}

    /**
     * Tampilkan halaman Manajemen Pengguna dengan metrics & filter.
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $data = $this->userService->getUsersIndexData($request);

        if ($request->ajax() || $request->has('ajax') || $request->expectsJson()) {
            return response()->json([
                'html' => view('super_admin.users.table', ['users' => $data['users']])->render(),
                'info' => $data['users']->total() > 0
                    ? 'Menampilkan <strong class="text-gray-700 dark:text-gray-300">'.$data['users']->firstItem().'–'.$data['users']->lastItem().'</strong> dari <strong class="text-gray-700 dark:text-gray-300">'.$data['users']->total().'</strong> pengguna'
                    : '',
            ]);
        }

        return view('super_admin.users.index', $data);
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $dto = UserData::fromRequest($request);
        $user = $this->userService->createUser($dto);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $dto = UserData::fromRequest($request);
        $this->userService->updateUser($user, $dto);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif pengguna.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorize('toggleStatus', $user);

        $statusLabel = $this->userService->toggleStatus($user);

        return back()->with('success', "Akun {$user->name} berhasil {$statusLabel}.");
    }

    /**
     * Reset password pengguna dengan password sementara yang aman.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $this->authorize('resetPassword', $user);

        $result = $this->userService->resetPassword($user);

        return back()->with('temp_password', $result);
    }

    /**
     * Hapus pengguna dari sistem.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $result = $this->userService->deleteUser($user);

        if (! $result['success']) {
            return redirect()->route('users.index')->with('error', $result['message']);
        }

        return redirect()->route('users.index')->with('success', $result['message']);
    }

    /**
     * Tampilkan log aktivitas untuk seorang pengguna.
     */
    public function activityLog(User $user): View
    {
        $this->authorize('viewActivityLog', $user);

        $logs = $this->userService->getUserActivityLogs($user);

        return view('super_admin.users.activity_log', compact('user', 'logs'));
    }
}

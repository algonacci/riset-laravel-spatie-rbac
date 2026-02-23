<?php

use App\Http\Controllers\RbacAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('rbac.home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password tidak valid.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('rbac.me'));
    })->name('login.attempt');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/me', function () {
        $user = Auth::user();

        return view('rbac.me', [
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    })->name('rbac.me');

    Route::get('/dashboard-admin', function () {
        return view('rbac.page', [
            'title' => 'Dashboard Admin',
            'description' => 'Halaman ini hanya boleh diakses role admin.',
        ]);
    })->middleware('role:admin')->name('rbac.dashboard-admin');

    Route::get('/tulis-artikel', function () {
        return view('rbac.page', [
            'title' => 'Tulis Artikel',
            'description' => 'Halaman ini butuh permission "tambah artikel".',
        ]);
    })->middleware('permission:tambah artikel')->name('rbac.tulis-artikel');

    Route::prefix('rbac-admin')
        ->name('rbac.admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/', [RbacAdminController::class, 'index'])->name('index');

            Route::post('/roles', [RbacAdminController::class, 'storeRole'])->name('roles.store');
            Route::put('/roles/{role}', [RbacAdminController::class, 'updateRole'])->name('roles.update');
            Route::delete('/roles/{role}', [RbacAdminController::class, 'destroyRole'])->name('roles.destroy');

            Route::post('/permissions', [RbacAdminController::class, 'storePermission'])->name('permissions.store');
            Route::put('/permissions/{permission}', [RbacAdminController::class, 'updatePermission'])->name('permissions.update');
            Route::delete('/permissions/{permission}', [RbacAdminController::class, 'destroyPermission'])->name('permissions.destroy');

            Route::put('/users/{user}/access', [RbacAdminController::class, 'updateUserAccess'])->name('users.access.update');
        });
});

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacAdminController extends Controller
{
    public function index(): View
    {
        return view('rbac.admin', [
            'roles' => Role::query()
                ->where('guard_name', 'web')
                ->with('permissions')
                ->orderBy('name')
                ->get(),
            'permissions' => Permission::query()
                ->where('guard_name', 'web')
                ->orderBy('name')
                ->get(),
            'users' => User::query()
                ->with(['roles', 'permissions'])
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where(fn ($query) => $query->where('guard_name', 'web'))],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Role '{$role->name}' berhasil dibuat.");
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web'))
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')->where(fn ($query) => $query->where('guard_name', 'web'))],
        ]);

        $role->update([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($validated['permissions'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Role '{$role->name}' berhasil diperbarui.");
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return back()->withErrors(['role' => "Role 'admin' dikunci agar halaman manajemen RBAC tidak terkunci."]);
        }

        $roleName = $role->name;
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Role '{$roleName}' berhasil dihapus.");
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions', 'name')->where(fn ($query) => $query->where('guard_name', 'web')),
            ],
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Permission '{$permission->name}' berhasil dibuat.");
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions', 'name')
                    ->where(fn ($query) => $query->where('guard_name', 'web'))
                    ->ignore($permission->id),
            ],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Permission '{$permission->name}' berhasil diperbarui.");
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permissionName = $permission->name;
        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Permission '{$permissionName}' berhasil dihapus.");
    }

    public function updateUserAccess(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('guard_name', 'web'))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')->where(fn ($query) => $query->where('guard_name', 'web'))],
        ]);

        $roleNames = Role::query()
            ->whereIn('id', $validated['roles'] ?? [])
            ->pluck('name')
            ->all();

        $permissionNames = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->pluck('name')
            ->all();

        if ($request->user()?->id === $user->id && ! in_array('admin', $roleNames, true)) {
            return back()->withErrors(['roles' => "Role 'admin' untuk akun yang sedang login tidak boleh dilepas."]);
        }

        $user->syncRoles($roleNames);
        $user->syncPermissions($permissionNames);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('status', "Akses user '{$user->email}' berhasil diperbarui.");
    }
}

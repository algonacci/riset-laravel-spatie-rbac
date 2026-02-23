<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RBAC Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        .grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); }
        .row { border-top: 1px dashed #ddd; margin-top: 0.8rem; padding-top: 0.8rem; }
        .muted { color: #666; font-size: 0.9rem; }
        .ok { background: #f0fff0; border: 1px solid #c8ebc8; padding: 0.6rem; border-radius: 6px; }
        .err { background: #fff2f2; border: 1px solid #e6b9b9; padding: 0.6rem; border-radius: 6px; }
        label { display: block; margin: 0.35rem 0; }
        input[type="text"] { width: 100%; padding: 0.45rem; margin-bottom: 0.4rem; }
        button { padding: 0.45rem 0.7rem; cursor: pointer; }
        .danger { background: #9c1c1c; color: #fff; border: 0; }
        .chips { display: flex; flex-wrap: wrap; gap: 0.35rem; margin: 0.35rem 0 0.6rem; }
        .chip { background: #f4f4f4; border: 1px solid #ddd; border-radius: 999px; padding: 0.12rem 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <h1>RBAC Admin Panel</h1>
    <p class="muted">Kelola role, permission, dan akses user dari satu halaman.</p>
    <p><a href="{{ route('home') }}">Kembali ke Home</a></p>

    @if (session('status'))
        <p class="ok">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid">
        <section class="card">
            <h2>Tambah Role</h2>
            <form method="POST" action="{{ route('rbac.admin.roles.store') }}">
                @csrf
                <label for="new_role_name">Nama role</label>
                <input id="new_role_name" type="text" name="name" placeholder="contoh: editor_artikel" required>

                <p class="muted">Permission default (opsional):</p>
                @foreach ($permissions as $permission)
                    <label>
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                        {{ $permission->name }}
                    </label>
                @endforeach

                <p><button type="submit">Buat Role</button></p>
            </form>
        </section>

        <section class="card">
            <h2>Tambah Permission</h2>
            <form method="POST" action="{{ route('rbac.admin.permissions.store') }}">
                @csrf
                <label for="new_permission_name">Nama permission</label>
                <input id="new_permission_name" type="text" name="name" placeholder="contoh: content.article.publish" required>
                <p><button type="submit">Buat Permission</button></p>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Kelola Role</h2>
        @forelse ($roles as $role)
            <div class="row">
                <form method="POST" action="{{ route('rbac.admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    <label>Nama role</label>
                    <input type="text" name="name" value="{{ $role->name }}" required>

                    <p class="muted">Permissions:</p>
                    @foreach ($permissions as $permission)
                        <label>
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                            >
                            {{ $permission->name }}
                        </label>
                    @endforeach

                    <p>
                        <button type="submit">Update Role</button>
                    </p>
                </form>

                <form method="POST" action="{{ route('rbac.admin.roles.destroy', $role) }}">
                    @csrf
                    @method('DELETE')
                    <button class="danger" type="submit">Hapus Role</button>
                </form>
            </div>
        @empty
            <p class="muted">Belum ada role.</p>
        @endforelse
    </section>

    <section class="card">
        <h2>Kelola Permission</h2>
        @forelse ($permissions as $permission)
            <div class="row">
                <form method="POST" action="{{ route('rbac.admin.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')
                    <label>Nama permission</label>
                    <input type="text" name="name" value="{{ $permission->name }}" required>
                    <p><button type="submit">Update Permission</button></p>
                </form>

                <form method="POST" action="{{ route('rbac.admin.permissions.destroy', $permission) }}">
                    @csrf
                    @method('DELETE')
                    <button class="danger" type="submit">Hapus Permission</button>
                </form>
            </div>
        @empty
            <p class="muted">Belum ada permission.</p>
        @endforelse
    </section>

    <section class="card">
        <h2>Kelola Akses User</h2>
        <p class="muted">Role dan direct-permission bisa diatur per user.</p>

        @foreach ($users as $user)
            <div class="row">
                <h3>{{ $user->email }}</h3>

                <div class="chips">
                    @foreach ($user->roles as $role)
                        <span class="chip">role: {{ $role->name }}</span>
                    @endforeach
                    @foreach ($user->permissions as $permission)
                        <span class="chip">direct: {{ $permission->name }}</span>
                    @endforeach
                    @if ($user->roles->isEmpty() && $user->permissions->isEmpty())
                        <span class="chip">belum ada akses</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('rbac.admin.users.access.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <p class="muted">Roles:</p>
                    @foreach ($roles as $role)
                        <label>
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}
                            >
                            {{ $role->name }}
                        </label>
                    @endforeach

                    <p class="muted">Direct permissions:</p>
                    @foreach ($permissions as $permission)
                        <label>
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                {{ $user->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                            >
                            {{ $permission->name }}
                        </label>
                    @endforeach

                    <p><button type="submit">Update Akses User</button></p>
                </form>
            </div>
        @endforeach
    </section>
</body>
</html>

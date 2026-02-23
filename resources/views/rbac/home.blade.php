<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spatie RBAC Research</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        code { background: #f6f6f6; padding: 0.1rem 0.3rem; }
    </style>
</head>
<body>
    <h1>Spatie RBAC Research</h1>

    <div class="card">
        @auth
            <p>Login sebagai: <strong>{{ auth()->user()->email }}</strong></p>

            <ul>
                <li><a href="{{ route('rbac.me') }}">Cek role & permission saya</a></li>
                <li><a href="{{ route('rbac.dashboard-admin') }}">Route role:admin</a></li>
                <li><a href="{{ route('rbac.tulis-artikel') }}">Route permission:tambah artikel</a></li>
                @role('admin')
                    <li><a href="{{ route('rbac.admin.index') }}">Panel Manajemen RBAC (Admin)</a></li>
                @endrole
            </ul>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            <p>Belum login. Masuk dulu untuk test middleware RBAC.</p>
            <p><a href="{{ route('login') }}">Login sekarang</a></p>
        @endauth
    </div>

    @auth
        <div class="card">
            <h2>Demo Directive Spatie</h2>

            @role('admin')
                <p><strong>[ROLE]</strong> Kamu adalah <code>admin</code>.</p>
            @elserole('penulis')
                <p><strong>[ROLE]</strong> Kamu adalah <code>penulis</code>.</p>
            @else
                <p><strong>[ROLE]</strong> Kamu belum punya role yang dicek di demo ini.</p>
            @endrole

            @hasanyrole('admin|penulis')
                <p><strong>[HAS ANY ROLE]</strong> Kamu punya salah satu role: <code>admin</code> atau <code>penulis</code>.</p>
            @endhasanyrole

            @can('tambah artikel')
                <p><strong>[PERMISSION]</strong> Kamu boleh <code>tambah artikel</code>.</p>
            @else
                <p><strong>[PERMISSION]</strong> Kamu TIDAK punya permission <code>tambah artikel</code>.</p>
            @endcan

            @can('hapus artikel')
                <p><strong>[PERMISSION]</strong> Kamu boleh <code>hapus artikel</code>.</p>
            @else
                <p><strong>[PERMISSION]</strong> Kamu TIDAK punya permission <code>hapus artikel</code>.</p>
            @endcan
        </div>
    @endauth

    <div class="card">
        <p><strong>Contoh middleware aktif:</strong></p>
        <p><code>->middleware('role:admin')</code></p>
        <p><code>->middleware('permission:tambah artikel')</code></p>
    </div>
</body>
</html>

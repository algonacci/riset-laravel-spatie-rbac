<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My RBAC Profile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        li { margin-bottom: 0.3rem; }
    </style>
</head>
<body>
    <h1>My RBAC Profile</h1>
    <p>User: <strong>{{ $user->email }}</strong></p>

    <div class="card">
        <h2>Roles</h2>
        @if ($roles->isEmpty())
            <p>User ini belum punya role.</p>
        @else
            <ul>
                @foreach ($roles as $role)
                    <li>{{ $role }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card">
        <h2>Permissions (effective)</h2>
        @if ($permissions->isEmpty())
            <p>User ini belum punya permission.</p>
        @else
            <ul>
                @foreach ($permissions as $permission)
                    <li>{{ $permission }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <p><a href="{{ route('home') }}">Kembali ke Home</a></p>
</body>
</html>

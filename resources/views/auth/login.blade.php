<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login RBAC Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 760px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.4rem; font-weight: 600; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.55rem; margin-bottom: 0.8rem; }
        button { padding: 0.6rem 0.9rem; cursor: pointer; }
        .error { color: #b00020; margin-bottom: 0.8rem; }
    </style>
</head>
<body>
    <h1>Login RBAC Demo</h1>

    <div class="card">
        <p><strong>Akun seeder untuk test:</strong></p>
        <p>admin: <code>admin@gmail.com</code> / <code>password</code></p>
        <p>penulis: <code>penulis@gmail.com</code> / <code>password</code></p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label>
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>

            <p>
                <button type="submit">Login</button>
            </p>
        </form>
    </div>

    <p><a href="{{ route('home') }}">Kembali ke Home</a></p>
</body>
</html>

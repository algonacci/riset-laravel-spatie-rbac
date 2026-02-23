<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="card">
        <p>{{ $description }}</p>
        <p>Login user: <strong>{{ auth()->user()->email }}</strong></p>
    </div>

    <p><a href="{{ route('home') }}">Kembali ke Home</a></p>
</body>
</html>

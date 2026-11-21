<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container" style="max-width: 420px;">
    <div class="card shadow-sm mt-5">
        <div class="card-body p-4">

            <h4 class="mb-4 text-center">Shop Management</h4>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" required
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-success w-100">Login</button>
            </form>

            @if ($canRegister)
                <hr>
                <p class="text-center small mb-0">
                    First time here?
                    <a href="{{ route('register') }}">Create Admin Account</a>
                </p>
            @endif

        </div>
    </div>
</div>

</body>
</html>
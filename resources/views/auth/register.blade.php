<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>অ্যাডমিন অ্যাকাউন্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 460px;">
    <div class="card shadow-sm mt-5">
        <div class="card-body p-4">
            <h4 class="mb-1 text-center">অ্যাডমিন অ্যাকাউন্ট তৈরি</h4>
            <p class="text-center text-muted small mb-4">এই ফর্ম শুধু প্রথম ব্যবহারকারীর জন্য।</p>

            <form method="POST" action="{{ route('register.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">নাম</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">ইমেইল</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">পাসওয়ার্ড (কমপক্ষে ৮ অক্ষর)</label>
                    <input type="password" name="password" required
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">পাসওয়ার্ড নিশ্চিত করুন</label>
                    <input type="password" name="password_confirmation" required class="form-control">
                </div>
                <button class="btn btn-success w-100">অ্যাকাউন্ট তৈরি করুন</button>
            </form>

            <p class="text-center small mt-3 mb-0"><a href="{{ route('login') }}">লগইনে ফিরে যান</a></p>
        </div>
    </div>
</div>
</body>
</html>

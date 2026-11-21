<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>{{ $shop->shop_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Bengali', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-purple-800">{{ $shop->shop_name }}</h1>
                @if ($shop->tagline)
                    <p class="text-sm text-gray-500">{{ $shop->tagline }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('shops.edit', $shop->shop_id) }}"
                   class="px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded hover:bg-purple-200">সম্পাদনা</a>
                <a href="{{ route('shops.index') }}"
                   class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">তালিকায় ফিরুন</a>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="font-semibold text-gray-600">মালিকের নাম</dt>
                <dd>{{ $shop->owner_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">ফোন</dt>
                <dd>
                    {{ $shop->phone_1 ?? '-' }}
                    @if ($shop->phone_2) / {{ $shop->phone_2 }} @endif
                </dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold text-gray-600">ঠিকানা</dt>
                <dd>{{ $shop->address ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-600">যোগ করা হয়েছে</dt>
                <dd>{{ $shop->created_at?->format('d-m-Y') ?? '-' }}</dd>
            </div>
        </dl>
    </div>
</body>
</html>

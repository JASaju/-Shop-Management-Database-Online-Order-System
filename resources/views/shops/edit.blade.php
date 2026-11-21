<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>দোকানের তথ্য সম্পাদনা</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Bengali', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-purple-800 mb-6">দোকানের তথ্য সম্পাদনা</h1>
                @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('shops.update', $shop->shop_id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">দোকানের নাম <span class="text-red-600">*</span></label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $shop->shop_name) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">ট্যাগলাইন</label>
                <input type="text" name="tagline" value="{{ old('tagline', $shop->tagline) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">মালিকের নাম</label>
                <input type="text" name="owner_name" value="{{ old('owner_name', $shop->owner_name) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">ঠিকানা</label>
                <input type="text" name="address" value="{{ old('address', $shop->address) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-1">ফোন ১</label>
                    <input type="text" name="phone_1" value="{{ old('phone_1', $shop->phone_1) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">ফোন ২</label>
                    <input type="text" name="phone_2" value="{{ old('phone_2', $shop->phone_2) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-purple-800 text-white rounded hover:bg-purple-900">
                    হালনাগাদ করুন
                </button>
                <a href="{{ route('shops.show', $shop->shop_id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>দোকান তালিকা</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Bengali', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('memos.create') }}"
               class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">ফিরুন</a>
            <a href="{{ route('shops.create') }}"
               class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-900 text-sm">
                + নতুন দোকান
            </a>
        </div>
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border text-sm">
            <thead class="bg-purple-800 text-white">
                <tr>
                    <th class="p-2 text-left">দোকানের নাম</th>
                    <th class="p-2 text-left">মালিক</th>
                    <th class="p-2 text-left">ফোন</th>
                    <th class="p-2 text-left">ঠিকানা</th>
                    <th class="p-2 w-32 text-center">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shops as $shop)
                    <tr class="border-t">
                        <td class="p-2">
                            <a href="{{ route('shops.show', $shop->shop_id) }}" class="text-purple-800 hover:underline font-semibold">
                                {{ $shop->shop_name }}
                            </a>
                            @if ($shop->tagline)
                                <div class="text-xs text-gray-500">{{ $shop->tagline }}</div>
                            @endif
                        </td>
                        <td class="p-2">{{ $shop->owner_name ?? '-' }}</td>
                        <td class="p-2">
                            {{ $shop->phone_1 ?? '-' }}
                            @if ($shop->phone_2)
                                <br>{{ $shop->phone_2 }}
                            @endif
                        </td>
                        <td class="p-2">{{ $shop->address ?? '-' }}</td>
                        <td class="p-2 text-center">
                            <a href="{{ route('shops.edit', $shop->shop_id) }}"
                               class="text-blue-600 hover:underline mr-2">সম্পাদনা</a>
                            <form action="{{ route('shops.destroy', $shop->shop_id) }}" method="POST"
                                  class="inline" onsubmit="return confirm('আপনি কি নিশ্চিত মুছে ফেলতে চান?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">মুছুন</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">কোনো দোকান পাওয়া যায়নি</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $shops->links() }}
        </div>
    </div>
</body>
</html>

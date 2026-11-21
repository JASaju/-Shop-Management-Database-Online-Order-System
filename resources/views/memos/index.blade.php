<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Memo List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-purple-800">Memo List</h1>
        <a href="{{ route('memos.create') }}" class="px-4 py-2 bg-purple-800 text-white rounded text-sm">+ New Memo</a>
    </div>

    @if (session('success') || session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
            {{ session('success') ?? session('status') }}
        </div>
    @endif

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
        <tr>
            <th class="p-2 text-left">Memo No</th>
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-right">Amount</th>
            <th class="p-2 text-right">Due</th>
            <th class="p-2"></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($memos as $memo)
            <tr class="border-t">
                <td class="p-2">{{ $memo->memo_no }}</td>
                <td class="p-2">{{ \Carbon\Carbon::parse($memo->memo_date)->format('d-m-Y') }}</td>
                <td class="p-2">{{ $memo->customer_name }}</td>
                <td class="p-2 text-right">{{ number_format($memo->total_amount - ($memo->discount_amount ?? 0), 2) }}</td>
                <td class="p-2 text-right {{ ($memo->due_amount ?? 0) > 0 ? 'text-red-600 font-semibold' : '' }}">{{ number_format($memo->due_amount ?? 0, 2) }}</td>
                <td class="p-2 text-right whitespace-nowrap">
                    <a href="{{ route('memos.show', $memo->memo_id) }}" class="text-purple-700 underline">View</a>
                    <form action="{{ route('memos.destroy', $memo->memo_id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this memo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 underline ml-2">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $memos->links() }}</div>
</div>
</body>
</html>
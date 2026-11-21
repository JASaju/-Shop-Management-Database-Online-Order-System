<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Requests</title>
    <style>
        body{margin:0;font-family:system-ui,sans-serif;background:#f4f6f8;color:#1f2937}
        .wrap{max-width:1100px;margin:0 auto;padding:16px}
        h1{font-size:20px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
        a{color:#0b7a44}
        .tabs a{display:inline-block;padding:6px 12px;border:1px solid #e5e7eb;border-radius:999px;background:#fff;text-decoration:none;font-size:13px;margin-right:4px;color:#1f2937}
        .tabs a.on{background:#0f9d58;color:#fff;border-color:#0f9d58}
        table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;margin-top:12px}
        th,td{padding:10px 12px;border-bottom:1px solid #eee;text-align:left;font-size:14px}
        th{background:#f9fafb}
        .st{padding:2px 9px;border-radius:999px;font-size:12px;background:#eee}
        .st.pending{background:#fef3c7;color:#92400e}.st.called{background:#dbeafe;color:#1e40af}
        .st.confirmed{background:#dcfce7;color:#166534}.st.cancelled{background:#fee2e2;color:#991b1b}
        .ok{background:#dcfce7;padding:10px;border-radius:8px;margin:8px 0}
        .call{font-weight:700;text-decoration:none}
    </style>
</head>
<body>
<div class="wrap">
    <h1>
        <span>Customer Order Requests @if($pending)<small>({{ $pending }} pending)</small>@endif</span>
        <a href="{{ route('home') }}">&larr; Admin home</a>
    </h1>

    @if(session('success'))<div class="ok">{{ session('success') }}</div>@endif

    <div class="tabs">
        <a href="{{ route('order-requests.index') }}" class="{{ !$status ? 'on' : '' }}">All</a>
        @foreach(['pending','called','confirmed','cancelled'] as $s)
            <a href="{{ route('order-requests.index', ['status' => $s]) }}" class="{{ $status === $s ? 'on' : '' }}">{{ ucfirst($s) }}</a>
        @endforeach
    </div>

    <table>
        <thead><tr><th>#</th><th>Time</th><th>Name</th><th>Mobile</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($orders as $o)
            <tr>
                <td>{{ $o->order_request_id }}</td>
                <td>{{ $o->created_at?->format('d M, h:i A') }}</td>
                <td>{{ $o->customer_name ?: '—' }}</td>
                <td><a class="call" href="tel:{{ $o->phone }}">{{ $o->phone }}</a></td>
                <td>{{ count($o->items ?? []) }}</td>
                <td>৳{{ number_format($o->total_amount, 2) }}</td>
                <td><span class="st {{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
                <td><a href="{{ route('order-requests.show', $o) }}">View</a></td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;padding:30px">Kono order request nai.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px">{{ $orders->links() }}</div>
</div>
</body>
</html>

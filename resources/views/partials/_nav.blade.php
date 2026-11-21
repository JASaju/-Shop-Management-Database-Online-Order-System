<nav class="max-w-4xl mx-auto mb-4 flex flex-wrap items-center gap-2 text-sm">
    <a href="{{ route('home') }}"
       class="px-3 py-1.5 rounded {{ request()->routeIs('home', 'products.*') ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
        দোকান ব্যবস্থাপনা
    </a>
    <a href="{{ route('memos.create') }}"
       class="px-3 py-1.5 rounded {{ request()->routeIs('memos.create') ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
        নতুন মেমো
    </a>
    <a href="{{ route('memos.index') }}"
       class="px-3 py-1.5 rounded {{ request()->routeIs('memos.index') ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
        মেমো তালিকা
    </a>
    <a href="{{ route('shops.index') }}"
       class="px-3 py-1.5 rounded {{ request()->routeIs('shops.*') ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
        দোকান তালিকা
    </a>
    <a href="{{ route('shops.create') }}"
       class="px-3 py-1.5 rounded {{ request()->routeIs('shops.create') ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
        + নতুন দোকান
    </a>
    <a href="{{ route('dues.index') }}" class="px-3 py-1.5 rounded bg-purple-100 text-purple-800 hover:bg-purple-200">গ্রাহকের বাকি</a>
    <a href="{{ route('purchases.index') }}" class="px-3 py-1.5 rounded bg-purple-100 text-purple-800 hover:bg-purple-200">ক্রয়</a>
    <a href="{{ route('expiry.index') }}" class="px-3 py-1.5 rounded bg-purple-100 text-purple-800 hover:bg-purple-200">মেয়াদ</a>
    <a href="{{ route('order-requests.index') }}">অর্ডার লিস্ট
        @php($pendingOrders = \App\Models\OrderRequest::where('status','pending')->count())
        @if($pendingOrders)<span style="background:#dc2626;color:#fff;border-radius:999px;padding:1px 7px;font-size:12px">{{ $pendingOrders }}</span>@endif
    </a>

</nav>

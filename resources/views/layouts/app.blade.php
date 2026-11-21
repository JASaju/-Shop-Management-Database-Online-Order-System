<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Digital AI Solutions')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; }
        .table thead th a { color: inherit; text-decoration: none; white-space: nowrap; }
        .table thead th a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Shop Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home', 'products.*') ? 'active' : '' }}" href="{{ route('home') }}">Product</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('memos.*') ? 'active' : '' }}" href="{{ route('memos.index') }}">Memo</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('memos.create') }}">New Memo</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('shops.*') ? 'active' : '' }}" href="{{ route('shops.index') }}">Shop</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}" href="{{ route('companies.index') }}">Company</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">Purchase</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dues.*') ? 'active' : '' }}" href="{{ route('dues.index') }}">Due</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('expiry.*') ? 'active' : '' }}" href="{{ route('expiry.index') }}">Expiry</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">More</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('hub.index') }}">All Reports (PDF)</a></li>
                        <li><a class="dropdown-item" href="{{ route('ledgers.index') }}">Ledger Report</a></li>
                        <li><a class="dropdown-item" href="{{ route('staff.index') }}">Employees</a></li>
                        <li><a class="dropdown-item" href="{{ route('instruments.index') }}">Equipment</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a></li>
            </ul>
            @auth
                <span class="navbar-text text-white me-3">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $shopName = config('app.name', 'Your Shop Name');

    $category = $category ?? $categories->firstWhere('id', $categoryId);

    $seoTitle = $category
        ? $category->name . ' Products Online | ' . $shopName
        : 'Digital AI Solutions | ' . $shopName;

    $seoDescription = $category
        ? 'Buy ' . $category->name . ' products online from ' . $shopName . '. Only in-stock items shown with updated prices. Submit your order list and we will call you.'
        : 'Browse in-stock products, medical equipment, surgical items, baby care and healthcare products at ' . $shopName . '. Submit your order list online and we will call you.';

    if ($search !== '') {
        $seoTitle = 'Search: ' . $search . ' | ' . $shopName;
        $seoDescription = 'Search results for ' . $search . ' at ' . $shopName . '.';
    }

    // Canonical: search parameter bad (duplicate URL ekhate)
    $canonicalUrl = $categoryId
        ? route('welcome', ['category_id' => $categoryId])
        : route('welcome');

    // Search result page index hobe na
    $robots = $search !== ''
        ? 'noindex, follow'
        : 'index, follow, max-image-preview:large, max-snippet:-1';

    // Social share image: public/og-image.jpg (1200x630) rakhun
    $ogImage = asset('og-image.jpg');

    $keywords = collect([
        'online pharmacy', 'medicine', 'medical equipment', 'surgical items',
        'healthcare products', 'Bangladesh', $category->name ?? null,
    ])->filter()->implode(', ');

    $priceValidUntil = now()->addDays(30)->toDateString();

    // Shudhu stock-e thaka product ashe, tai availability InStock
    $schemaItems = $products->take(50)->values()->map(function ($p, $index) use ($canonicalUrl, $priceValidUntil) {
        $item = [
            '@type' => 'Product',
            'name' => $p->name,
            'sku' => (string) $p->medicine_id,
            'category' => $p->category_name ?? 'Products',
            'url' => $canonicalUrl,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'BDT',
                'price' => round(
                    (float) $p->default_price *
                    (1 - (float) ($p->default_discount_percent ?? 0) / 100),
                    2
                ),
                'priceValidUntil' => $priceValidUntil,
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => ((float) ($p->stock ?? 0)) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
        if (!empty($p->company_name)) {
            $item['brand'] = ['@type' => 'Brand', 'name' => $p->company_name];
        }
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => $item,
        ];
    })->all();

    $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
@endphp

    <!-- SEO TITLE -->
    <title>{{ $seoTitle }}</title>

    <!-- SEO META -->
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="author" content="{{ $shopName }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="googlebot" content="{{ $robots }}">
    <meta name="format-detection" content="telephone=yes">

    <!-- CANONICAL -->
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" hreflang="en-bd" href="{{ $canonicalUrl }}">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $shopName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_BD">

    <!-- TWITTER -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- THEME -->
    <meta name="theme-color" content="#0f9d58">

    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <!-- STRUCTURED DATA: ORGANIZATION -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Pharmacy',
            'name' => $shopName,
            'url' => url('/'),
            'image' => $ogImage,
            'areaServed' => 'BD',
        ], $jsonFlags) !!}
    </script>

    <!-- STRUCTURED DATA: WEBSITE + SEARCH -->
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $shopName,
            'url' => url('/'),
            'description' => $seoDescription,
            'inLanguage' => 'en',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/') . '?q={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ], $jsonFlags) !!}
    </script>

    <!-- STRUCTURED DATA: BREADCRUMB (category page-e) -->
    @if($category)
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('welcome')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $category->name, 'item' => $canonicalUrl],
                ]
            ], $jsonFlags) !!}
        </script>
    @endif

    <!-- STRUCTURED DATA: PRODUCT LIST -->
    @if($products->isNotEmpty())
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => $seoTitle,
                'numberOfItems' => count($schemaItems),
                'itemListElement' => $schemaItems
            ], $jsonFlags) !!}
        </script>
    @endif
    <style>
        :root {
            --bg: #f4f6f8;
            --card: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --brand: #0f9d58;
            --brand-dark: #0b7a44;
            --danger: #dc2626;
            --line: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        a {
            text-decoration: none;
        }

        /* HEADER */

        header {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 12px 16px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 20px;
            margin: 0;
            flex: 1;
        }

        header form {
            flex: 1 1 260px;
            display: flex;
            gap: 6px;
        }

        header input[type=search] {
            flex: 1;
            min-width: 0;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 14px;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: var(--brand);
            color: #fff;
        }

        .btn:hover {
            background: var(--brand-dark);
        }

        .btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn.ghost {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
        }

        .adminlink {
            font-size: 13px;
            color: var(--muted);
        }

        .cartbtn {
            position: relative;
        }

        .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            min-width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            padding: 0 5px;
        }

        /* CATEGORY MENU */

        .categories {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 13px 16px;
            background: #fff;
            border-bottom: 1px solid var(--line);
            scrollbar-width: thin;
        }

        .categories a {
            white-space: nowrap;
            padding: 9px 16px;
            border-radius: 20px;
            color: #374151;
            background: #f3f4f6;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
        }

        .categories a:hover,
        .categories a.active {
            background: var(--brand);
            color: #fff;
        }

        /* MAIN */

        main {
            max-width: 1250px;
            margin: 0 auto;
            padding: 20px 16px;
            min-height: 65vh;
        }

        .section-title {
            font-size: 21px;
            font-weight: 700;
            margin: 5px 0 18px;
        }

        .grid {
            display: grid;
            gap: 15px;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        }

        /* PRODUCT CARD */

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
            transition: .2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,.07);
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
            line-height: 1.4;
        }

        .meta {
            font-size: 12.5px;
            color: var(--muted);
        }

        .category-tag {
            display: inline-block;
            width: fit-content;
            padding: 4px 9px;
            border-radius: 5px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 11px;
            font-weight: 600;
        }

        .price {
            font-size: 21px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .price s {
            font-size: 13px;
            color: var(--muted);
            font-weight: 400;
            margin-left: 5px;
        }

        .tag {
            display: inline-block;
            width: fit-content;
            font-size: 11px;
            padding: 4px 9px;
            border-radius: 20px;
            background: #ecfdf5;
            color: var(--brand-dark);
        }

        .tag.out {
            background: #fef2f2;
            color: var(--danger);
        }

        .row {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-top: auto;
            padding-top: 8px;
        }

        .qty {
            display: flex;
            align-items: center;
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }

        .qty button {
            border: 0;
            background: #f9fafb;
            width: 30px;
            height: 35px;
            font-size: 18px;
            cursor: pointer;
        }

        .qty input {
            width: 42px;
            text-align: center;
            border: 0;
            height: 35px;
            font-size: 14px;
            -moz-appearance: textfield;
        }

        .qty input::-webkit-inner-spin-button,
        .qty input::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        .row .btn {
            flex: 1;
        }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 60px 0;
            grid-column: 1 / -1;
        }

        /* FOOTER */

        footer {
            text-align: center;
            padding: 20px 15px;
            background: #fff;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 13px;
        }

        /* CART */

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            display: none;
            z-index: 30;
        }

        .overlay.open {
            display: block;
        }

        .drawer {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: min(420px, 100%);
            background: #fff;
            z-index: 40;
            transform: translateX(100%);
            transition: transform .25s;
            display: flex;
            flex-direction: column;
        }

        .drawer.open {
            transform: none;
        }

        .drawer h2 {
            margin: 0;
            padding: 15px 16px;
            border-bottom: 1px solid var(--line);
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer .close {
            background: none;
            border: 0;
            font-size: 25px;
            cursor: pointer;
        }

        .items {
            flex: 1;
            overflow: auto;
            padding: 8px 16px;
        }

        .item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 5px 10px;
            padding: 12px 0;
            border-bottom: 1px dashed var(--line);
        }

        .item .n {
            font-weight: 600;
            font-size: 14px;
        }

        .item .s {
            font-size: 12px;
            color: var(--muted);
        }

        .item .t {
            font-weight: 700;
            text-align: right;
        }

        .item .ctl {
            display: flex;
            align-items: center;
            gap: 8px;
            grid-column: 1/-1;
        }

        .item .rm {
            margin-left: auto;
            background: none;
            border: 0;
            color: var(--danger);
            cursor: pointer;
            font-size: 13px;
        }

        .foot {
            border-top: 1px solid var(--line);
            padding: 14px 16px;
            display: grid;
            gap: 9px;
            background: #fafafa;
        }

        .total {
            display: flex;
            justify-content: space-between;
            font-size: 19px;
            font-weight: 700;
        }

        .foot input {
            padding: 11px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
        }

        .foot input.bad {
            border-color: var(--danger);
        }

        .err {
            color: var(--danger);
            font-size: 13px;
            min-height: 16px;
        }

        .toast {
            position: fixed;
            left: 50%;
            bottom: 24px;
            transform: translateX(-50%);
            background: #111827;
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            z-index: 60;
            display: none;
            max-width: 90%;
            text-align: center;
        }

        .toast.show {
            display: block;
        }

        @media(max-width: 600px) {
            header h1 {
                flex-basis: 100%;
            }

            header form {
                flex: 1 1 100%;
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px;
            }

            .card {
                padding: 10px;
            }

            .card h3 {
                font-size: 14px;
            }

            .price {
                font-size: 17px;
            }

            .row {
                flex-wrap: wrap;
            }
        }

        /* Keep the page clear of the fixed left branding strip */
        body {
            padding-left: 65px;
        }

        @media (max-width: 600px) {
            .left-marquee {
                width: 42px;
            }

            .left-marquee .brand {
                font-size: 8px;
                padding: 10px 0;
            }

            .left-marquee .marquee-track span {
                font-size: 11px;
                padding: 9px 0;
            }

            .left-marquee .contact {
                font-size: 7px;
                max-height: 120px;
            }

            body {
                padding-left: 42px;
            }
        }

    </style>
</head>

<body>

<header>

    <h1>{{ config('app.name', 'Our Products') }}</h1>

    <!-- SEARCH -->
    <form method="GET" action="{{ route('welcome') }}">

        <input type="hidden"
               name="category_id"
               value="{{ $categoryId }}">

        <input type="search"
               name="q"
               value="{{ $search }}"
               placeholder="Search products..."
               aria-label="Search products">

        <button class="btn" type="submit">
            Search
        </button>

    </form>

    <!-- CART -->
    <button class="btn cartbtn" id="openCart" type="button">
        My List
        <span class="badge" id="cartCount">0</span>
    </button>

    @auth
    <a class="adminlink" href="{{ route('home') }}">
        Admin
    </a>
    @else
        <a class="adminlink" href="{{ route('login') }}">
            Admin Login
        </a>
        @endauth

</header>


<!-- CATEGORY MENU -->
<nav class="categories" aria-label="Product Categories">

    <a href="{{ route('welcome', $search ? ['q' => $search] : []) }}"
       class="{{ empty($categoryId) ? 'active' : '' }}">
        All Products
    </a>

    @foreach($categories as $cat)

        <a href="{{ route('welcome', [
            'category_id' => $cat->id,
            'q' => $search
        ]) }}"
           class="{{ (string)$categoryId === (string)$cat->id ? 'active' : '' }}">

            {{ $cat->name }}

        </a>

    @endforeach

</nav>

<marquee direction="left" scrollamount="5">
    Demo Shop, will start in January-2027, Insha'Allah. 01799549404, esaju.cse@gmail.com
</marquee>
<!-- PRODUCTS -->
<main>

    <h2 class="section-title">

        @if($search !== '')
            Search Results: {{ $search }}
        @elseif($category)
            {{ $category->name }}
        @else
            All Products
        @endif

    </h2>

    @if($products->isEmpty())

        <div class="empty">
            <h3>Product Not Found!</h3>
            <p>Please select another category or search again.</p>

            <a class="btn" href="{{ route('welcome') }}">
                View All Products
            </a>
        </div>

    @else

        <div class="grid">

            @foreach($products as $p)

                @php
                    $price = (float) $p->default_price;

                    $disc = (float) (
                        $p->default_discount_percent ?? 0
                    );

                    $final = round(
                        $price - ($price * $disc / 100),
                        2
                    );

                    $stock = (float) ($p->stock ?? 0);

                    $expired = (float) (
                        $p->expired_stock ?? 0
                    );
                @endphp

                <article class="card"
                         data-id="{{ $p->medicine_id }}"
                         data-name="{{ $p->name }}"
                         data-unit="{{ $p->unit }}"
                         data-price="{{ $final }}">

                    <h3>{{ $p->name }}</h3>

                    @if($p->category_name)

                        <span class="category-tag">
                            {{ $p->category_name }}
                        </span>

                    @endif

                    @if($p->company_name)

                        <div class="meta">
                            {{ $p->company_name }}
                        </div>

                    @endif

                    <div class="price">

                        ৳{{ number_format($final, 2) }}

                        @if($disc > 0)

                            <s>
                                ৳{{ number_format($price, 2) }}
                            </s>

                        @endif

                    </div>

                    <div class="meta">

                        Per {{ $p->unit ?: 'unit' }}

                        @if($disc > 0)
                            · {{ rtrim(rtrim(number_format($disc, 2), '0'), '.') }}% off
                        @endif

                    </div>

                    @if($stock > 0)

                        <span class="tag">
                            In Stock
                            ({{ rtrim(rtrim(number_format($stock, 2), '0'), '.') }})
                        </span>

                    @elseif($expired > 0)

                        <span class="tag out">
                            Expired Stock
                        </span>

                    @else

                        <span class="tag out">
                            Out of Stock
                        </span>

                    @endif

                    <div class="row">

                        <div class="qty">

                            <button type="button" class="dec"
                                    aria-label="Decrease quantity">
                                −
                            </button>

                            <input type="number"
                                   class="q"
                                   value="1"
                                   min="1"
                                   max="10000">

                            <button type="button" class="inc"
                                    aria-label="Increase quantity">
                                +
                            </button>

                        </div>

                        <button type="button"
                                class="btn add"
                                @if($stock <= 0) disabled @endif>
                            Add
                        </button>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</main>


<!-- FOOTER -->
<footer>
    &copy; {{ date('Y') }}
    {{ config('app.name', 'Your Shop Name') }}.
    All Rights Reserved.
</footer>


<!-- CART OVERLAY -->
<div class="overlay" id="overlay"></div>


<!-- CART DRAWER -->
<aside class="drawer" id="drawer" aria-label="My Product List">

    <h2>
        My Product List

        <button class="close" id="closeCart" type="button">
            &times;
        </button>
    </h2>

    <div class="items" id="items"></div>

    <div class="foot">

        <div class="total">
            <span>Total</span>
            <span id="grand">৳0.00</span>
        </div>

        <input type="text"
               id="cname"
               placeholder="Customer Name (optional)"
               maxlength="120">

        <input type="tel"
               id="phone"
               placeholder="Mobile Number * (01XXXXXXXXX)"
               inputmode="numeric"
               maxlength="14"
               required>

        <div class="err" id="err"></div>

        <button class="btn" id="send" type="button">
            Submit Order
        </button>

        <button class="btn ghost" id="clear" type="button">
            Clear List
        </button>

    </div>

</aside>


<!-- TOAST -->
<div class="toast" id="toast"></div>


<!-- JAVASCRIPT -->
<script>
    (function () {

        'use strict';

        const KEY = 'my_product_list_v1';

        let cart = {};

        try {
            cart = JSON.parse(localStorage.getItem(KEY) || '{}');

            if (!cart || typeof cart !== 'object' || Array.isArray(cart)) {
                cart = {};
            }

        } catch (e) {
            cart = {};
        }

        function $(selector, root = document) {
            return root.querySelector(selector);
        }

        function fmt(n) {
            return '৳' + Number(n).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
        }

        function save() {
            try {
                localStorage.setItem(KEY, JSON.stringify(cart));
            } catch (e) {
                toast('Unable to save your list.');
            }
        }

        function toast(message) {
            const t = $('#toast');

            t.textContent = message;
            t.classList.add('show');

            setTimeout(function () {
                t.classList.remove('show');
            }, 3000);
        }

        function openCart(isOpen) {
            $('#drawer').classList.toggle('open', isOpen);
            $('#overlay').classList.toggle('open', isOpen);
        }

        function render() {

            const ids = Object.keys(cart);

            let sum = 0;
            let count = 0;

            const box = $('#items');

            box.innerHTML = '';

            if (ids.length === 0) {

                const empty = document.createElement('div');

                empty.className = 'empty';
                empty.textContent = 'Empty List.';

                box.appendChild(empty);
            }

            ids.forEach(function (id) {

                const it = cart[id];

                if (!it || !Number.isFinite(Number(it.price)) ||
                    !Number.isFinite(Number(it.qty))) {
                    delete cart[id];
                    return;
                }

                const line = Number(it.price) * Number(it.qty);

                sum += line;
                count += Number(it.qty);

                const el = document.createElement('div');

                el.className = 'item';

                const info = document.createElement('div');

                const name = document.createElement('div');
                name.className = 'n';
                name.textContent = it.name;

                const detail = document.createElement('div');
                detail.className = 's';
                detail.textContent =
                    fmt(it.price) + ' × ' + it.qty +
                    (it.unit ? ' ' + it.unit : '');

                info.appendChild(name);
                info.appendChild(detail);

                const total = document.createElement('div');
                total.className = 't';
                total.textContent = fmt(line);

                const controls = document.createElement('div');
                controls.className = 'ctl';

                const qtyBox = document.createElement('div');
                qtyBox.className = 'qty';

                const dec = document.createElement('button');
                dec.type = 'button';
                dec.textContent = '−';
                dec.dataset.a = 'dec';

                const qtyInput = document.createElement('input');
                qtyInput.type = 'number';
                qtyInput.min = '1';
                qtyInput.max = '10000';
                qtyInput.value = it.qty;

                const inc = document.createElement('button');
                inc.type = 'button';
                inc.textContent = '+';
                inc.dataset.a = 'inc';

                qtyBox.appendChild(dec);
                qtyBox.appendChild(qtyInput);
                qtyBox.appendChild(inc);

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'rm';
                remove.dataset.a = 'rm';
                remove.textContent = 'Remove';

                controls.appendChild(qtyBox);
                controls.appendChild(remove);

                el.appendChild(info);
                el.appendChild(total);
                el.appendChild(controls);

                el.addEventListener('click', function (e) {

                    const action = e.target.dataset.a;

                    if (!action) return;

                    if (action === 'inc') {
                        cart[id].qty = Math.min(
                            10000,
                            Number(cart[id].qty) + 1
                        );
                    }

                    if (action === 'dec') {
                        cart[id].qty = Math.max(
                            1,
                            Number(cart[id].qty) - 1
                        );
                    }

                    if (action === 'rm') {
                        delete cart[id];
                    }

                    save();
                    render();
                });

                qtyInput.addEventListener('change', function () {

                    cart[id].qty = Math.min(
                        10000,
                        Math.max(1, parseInt(qtyInput.value, 10) || 1)
                    );

                    save();
                    render();
                });

                box.appendChild(el);

            });

            $('#grand').textContent = fmt(sum);
            $('#cartCount').textContent = count;

            save();
        }


        /* PRODUCT CARDS */

        document.querySelectorAll('.card').forEach(function (card) {

            const q = $('.q', card);

            $('.inc', card).onclick = function () {

                q.value = Math.min(
                    10000,
                    (parseInt(q.value, 10) || 1) + 1
                );

            };

            $('.dec', card).onclick = function () {

                q.value = Math.max(
                    1,
                    (parseInt(q.value, 10) || 1) - 1
                );

            };

            $('.add', card).onclick = function () {

                const id = card.dataset.id;

                const qty = Math.min(
                    10000,
                    Math.max(1, parseInt(q.value, 10) || 1)
                );

                if (cart[id]) {

                    cart[id].qty = Math.min(
                        10000,
                        Number(cart[id].qty) + qty
                    );

                } else {

                    cart[id] = {
                        id: parseInt(id, 10),
                        name: card.dataset.name,
                        unit: card.dataset.unit,
                        price: parseFloat(card.dataset.price),
                        qty: qty
                    };

                }

                q.value = 1;

                save();
                render();

                toast(card.dataset.name + ' added to list.');

            };

        });


        /* CART BUTTONS */

        $('#openCart').onclick = function () {
            openCart(true);
        };

        $('#closeCart').onclick = function () {
            openCart(false);
        };

        $('#overlay').onclick = function () {
            openCart(false);
        };

        $('#clear').onclick = function () {

            cart = {};

            save();
            render();

            $('#err').textContent = '';

        };


        /* ORDER SUBMISSION */

        $('#send').onclick = async function () {

            const phoneEl = $('#phone');
            const err = $('#err');
            const btn = $('#send');

            err.textContent = '';
            phoneEl.classList.remove('bad');

            if (Object.keys(cart).length === 0) {

                err.textContent = 'Empty List. Please add products.';
                return;

            }

            const phone = phoneEl.value.replace(/[\s-]/g, '');

            if (!/^(?:\+?88)?01[3-9]\d{8}$/.test(phone)) {

                phoneEl.classList.add('bad');

                err.textContent =
                    'Please enter a valid Bangladeshi mobile number.';

                phoneEl.focus();

                return;

            }

            const items = Object.keys(cart).map(function (id) {

                return {
                    id: parseInt(id, 10),
                    qty: cart[id].qty
                };

            });

            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {

                const res = await fetch(
                        @json(route('order.request')),
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        },

                        body: JSON.stringify({

                            phone: phone,

                            customer_name:
                            $('#cname').value.trim() || null,

                            items: items

                        })
                    }
                );

                const data = await res.json().catch(function () {
                    return {};
                });

                if (!res.ok) {

                    let message = data.message || 'Something went wrong.';

                    if (data.errors) {

                        const firstError =
                            Object.values(data.errors)[0];

                        if (Array.isArray(firstError)) {
                            message = firstError[0];
                        }

                    }

                    err.textContent = message;

                    return;
                }

                if (!data.ok) {

                    err.textContent =
                        data.message || 'Order could not be submitted.';

                    return;
                }

                cart = {};

                save();
                render();

                openCart(false);

                phoneEl.value = '';
                $('#cname').value = '';

                toast(
                    data.message ||
                    'Your order was submitted successfully.'
                );

            } catch (e) {

                err.textContent =
                    'Network problem. Please try again.';

            } finally {

                btn.disabled = false;
                btn.textContent = 'Submit Order';

            }

        };


        /* INITIALIZE */

        render();

    })();
</script>

</body>
</html>
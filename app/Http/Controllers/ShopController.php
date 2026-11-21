<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::orderByDesc('shop_id')->paginate(20);

        return view('shops.index', compact('shops'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name'  => ['required', 'string', 'max:150'],
            'tagline'    => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:150'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone_1'    => ['nullable', 'string', 'max:20'],
            'phone_2'    => ['nullable', 'string', 'max:20'],
        ]);

        $shop = Shop::create($validated);

        return redirect()->route('shops.show', $shop->shop_id)
            ->with('success', 'দোকান সফলভাবে যোগ করা হয়েছে');
    }

    public function show(Shop $shop)
    {
        return view('shops.show', compact('shop'));
    }

    public function edit(Shop $shop)
    {
        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'shop_name'  => ['required', 'string', 'max:150'],
            'tagline'    => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:150'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone_1'    => ['nullable', 'string', 'max:20'],
            'phone_2'    => ['nullable', 'string', 'max:20'],
        ]);

        $shop->update($validated);

        return redirect()->route('shops.show', $shop->shop_id)
            ->with('success', 'দোকানের তথ্য আপডেট হয়েছে');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return redirect()->route('shops.index')
            ->with('success', 'দোকান মুছে ফেলা হয়েছে');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicShopController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim((string) $request->input('q', ''));
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $today      = now()->toDateString();

        // Shudhu oi category gulo dekhabe jader moddhe stock-e (meyad ache) product ache
        $categories = DB::table('product_categories as cat')
            ->where('cat.status', 1)
            ->whereExists(function ($q) use ($today) {
                $q->select(DB::raw(1))
                    ->from('medicines as m')
                    ->join('purchase_items as pi', 'pi.medicine_id', '=', 'm.medicine_id')
                    ->whereColumn('m.category_id', 'cat.id')
                    ->where('pi.quantity_remaining', '>', 0)
                    ->where('pi.expiry_date', '>=', $today);
            })
            ->orderBy('cat.name')
            ->select('cat.*')
            ->get();

        // Shudhu non-expired batch-er stock jog hobe; stock 0 hole product list-e ashbe na
        $query = DB::table('medicines as m')
            ->leftJoin('companies as c', 'c.company_id', '=', 'm.company_id')
            ->leftJoin('product_categories as cat', 'cat.id', '=', 'm.category_id')
            ->join('purchase_items as pi', function ($j) use ($today) {
                $j->on('pi.medicine_id', '=', 'm.medicine_id')
                  ->where('pi.quantity_remaining', '>', 0)
                  ->where('pi.expiry_date', '>=', $today);
            })
            ->select(
                'm.medicine_id',
                'm.name',
                'm.unit',
                'm.default_price',
                'm.default_discount_percent',
                'c.company_name',
                'cat.name as category_name',
                DB::raw('SUM(pi.quantity_remaining) as stock')
            )
            ->groupBy(
                'm.medicine_id',
                'm.name',
                'm.unit',
                'm.default_price',
                'm.default_discount_percent',
                'c.company_name',
                'cat.name'
            )
            ->havingRaw('SUM(pi.quantity_remaining) > 0');

        if ($search !== '') {
            $query->where('m.name', 'like', '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%');
        }

        if ($categoryId !== null) {
            $query->where('m.category_id', $categoryId);
        }

        $products = $query->orderBy('m.name')->get();

        $category = $categories->firstWhere('id', $categoryId);

        return view('welcome', compact(
            'products',
            'search',
            'categories',
            'categoryId',
            'category'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'phone'          => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'customer_name'  => ['nullable', 'string', 'max:120'],
            'items'          => ['required', 'array', 'min:1', 'max:100'],
            'items.*.id'     => ['required', 'integer', 'exists:medicines,medicine_id'],
            'items.*.qty'    => ['required', 'numeric', 'min:1', 'max:10000'],
        ], [
            'phone.required' => 'Mobile number *.',
            'phone.regex'    => 'Bangladeshi Mobile No. Please',
            'items.required' => 'Empty List',
        ]);

        $ids = collect($data['items'])->pluck('id')->unique();

        // Price client theke nibo na — server e DB theke nibo (tamper rokhte).
        $medicines = Medicine::whereIn('medicine_id', $ids)->get()->keyBy('medicine_id');

        // Server-side stock check (meyad-uttirno batch bad)
        $stocks = DB::table('purchase_items')
            ->whereIn('medicine_id', $ids)
            ->where('quantity_remaining', '>', 0)
            ->where('expiry_date', '>=', now()->toDateString())
            ->groupBy('medicine_id')
            ->selectRaw('medicine_id, SUM(quantity_remaining) as stock')
            ->pluck('stock', 'medicine_id');

        $lines = [];
        $total = 0;
        foreach ($data['items'] as $row) {
            $m = $medicines[$row['id']] ?? null;
            if (! $m) {
                continue;
            }

            $avail = (float) ($stocks[$m->medicine_id] ?? 0);
            if ($avail <= 0) {
                continue; // stock nai -> order-e nibo na
            }

            $qty   = min((float) $row['qty'], $avail);
            $price = (float) $m->default_price;
            $disc  = (float) ($m->default_discount_percent ?? 0);
            $unit  = round($price - ($price * $disc / 100), 2);
            $line  = round($unit * $qty, 2);
            $total += $line;

            $lines[] = [
                'medicine_id' => $m->medicine_id,
                'name'        => $m->name,
                'unit'        => $m->unit,
                'price'       => $unit,
                'qty'         => $qty,
                'line_total'  => $line,
            ];
        }

        if (empty($lines)) {
            return response()->json([
                'ok'      => false,
                'message' => 'Selected products are currently out of stock.',
            ], 422);
        }

        $order = OrderRequest::create([
            'customer_name' => $data['customer_name'] ?? null,
            'phone'         => preg_replace('/^\+?88/', '', $data['phone']),
            'items'         => $lines,
            'total_amount'  => round($total, 2),
            'status'        => 'pending',
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'The list was sent successfully. As soon as possible call your Mobile.',
            'order'   => $order->order_request_id,
            'total'   => $order->total_amount,
        ]);
    }
}
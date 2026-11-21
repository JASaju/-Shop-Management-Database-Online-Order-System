<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MemoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    /** Fields the user can search by on the home page. */
    private const SEARCH_FIELDS = ['all', 'name', 'unit', 'price', 'id'];

    /** Columns the list can be sorted by (query-string key => DB column). */
    private const SORTABLE = [
        'id'    => 'medicine_id',
        'name'  => 'name',
        'unit'  => 'unit',
        'price' => 'default_price',
        'date'  => 'created_at',
    ];

    public function index(Request $request)
    {
        $field = $request->input('field', 'all');
        if (! in_array($field, self::SEARCH_FIELDS, true)) {
            $field = 'all';
        }

        $q    = trim((string) $request->input('q', ''));
        $unit = trim((string) $request->input('unit', ''));

        $sort = $request->input('sort', 'id');
        if (! array_key_exists($sort, self::SORTABLE)) {
            $sort = 'id';
        }
        $dir = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Medicine::query();

        if ($q !== '') {
            $like = '%' . $this->escapeLike($q) . '%';

            $query->where(function ($w) use ($field, $q, $like) {
                $byId = ctype_digit($q);

                switch ($field) {
                    case 'name':
                        $w->where('name', 'like', $like);
                        break;
                    case 'unit':
                        $w->where('unit', 'like', $like);
                        break;
                    case 'price':
                        $w->whereRaw('CAST(default_price AS CHAR) like ?', [$like]);
                        break;
                    case 'id':
                        $byId ? $w->where('medicine_id', (int) $q) : $w->whereRaw('1 = 0');
                        break;
                    default: // all fields
                        $w->where('name', 'like', $like)
                          ->orWhere('unit', 'like', $like)
                          ->orWhereRaw('CAST(default_price AS CHAR) like ?', [$like]);
                        if ($byId) {
                            $w->orWhere('medicine_id', (int) $q);
                        }
                }
            });
        }

        if ($unit !== '') {
            $query->where('unit', $unit);
        }

        $products = $query
            ->orderBy(self::SORTABLE[$sort], $dir)
            ->orderBy('medicine_id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $units = Medicine::query()->distinct()->orderBy('unit')->pluck('unit');

        return view('products.index', compact('products', 'units', 'field', 'q', 'unit', 'sort', 'dir'));
    }

    public function create()
    {
        return view('products.create', ['units' => $this->unitOptions()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $product = Medicine::create($data);

        return redirect()->route('home')
            ->with('success', "পণ্য \"{$product->name}\" যোগ করা হয়েছে।");
    }

    public function edit(Medicine $product)
    {
        return view('products.edit', [
            'product' => $product,
            'units'   => $this->unitOptions($product->unit),
        ]);
    }

    public function update(Request $request, Medicine $product)
    {
        $data = $request->validate($this->rules($product));

        $product->update($data);

        return redirect()->route('home')
            ->with('success', "পণ্য \"{$product->name}\" আপডেট হয়েছে।");
    }

    public function destroy(Medicine $product)
    {
        $name = $product->name;

        DB::transaction(function () use ($product) {
            // memo_items.medicine_id has a foreign key to medicines. Old memos keep
            // their own copy of the name/price (medicine_name, unit_price), so we
            // detach them instead of blocking the delete or losing sales history.
            MemoItem::where('medicine_id', $product->medicine_id)->update(['medicine_id' => null]);

            $product->delete();
        });

        return redirect()->route('home')
            ->with('success', "পণ্য \"{$name}\" মুছে ফেলা হয়েছে।");
    }

    private function rules(?Medicine $product = null): array
    {
        return [
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('medicines', 'name'),
            ],
            'unit'          => ['required', 'string', 'max:30'],
            'default_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    private function unitOptions(?string $current = null)
    {
        $units = Medicine::query()->distinct()->pluck('unit')
            ->merge(['pcs', 'bottle', 'tube', 'vial', 'bag', 'sachet', 'box', 'strip']);

        if ($current) {
            $units->push($current);
        }

        return $units->filter()->unique()->sort()->values();
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WMS_Global;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Ambil daftar produk (API) dengan filter, search, dan pagination.
     */
    public function index(Request $r)
    {
        $perPage = min(max((int)$r->integer('per_page', 10), 5), 100);
        $search  = trim((string)$r->query('q', ''));
        $status  = $r->query('status');

        $query = WMS_Global::product()->newQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode_product', 'like', "%{$search}%");
            });
        }

        if ($status === 'ready') {
            $query->whereColumn('stock_quantity', '>', 'stok_minimum');
        } elseif ($status === 'low') {
            $query->whereColumn('stock_quantity', '<=', 'stok_minimum')
                  ->where('stock_quantity', '>', 0);
        } elseif ($status === 'empty') {
            $query->where('stock_quantity', '<=', 0);
        }

        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $products->map(function ($p) {
                return [
                    'id'             => $p->id,
                    'kode_product'   => $p->kode_product,
                    'name'           => $p->name,
                    'description'    => $p->description,
                    'harga_beli'     => $p->harga_beli,
                    'harga_jual'     => $p->harga_jual,
                    'stock_quantity' => $p->stock_quantity,
                    'stok_minimum'   => $p->stok_minimum,
                    'status'         => ($p->stock_quantity <= 0) ? 'empty': (($p->stock_quantity <= $p->stok_minimum) ? 'low' : 'ready'),
                    'created_at'     => date('Y-m-d H:i', strtotime($p->created_at)),
                ];
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    public function formOptions()
    {
        return response()->json([
            'categories' => WMS_Global::category()->select('id', 'name')->orderBy('name')->get(),
            'uoms'       => WMS_Global::uom()->select('id', 'name')->orderBy('name')->get(),
            'suppliers'  => WMS_Global::supplier()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $validated = $r->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'harga_beli'     => 'nullable|numeric|min:0',
            'harga_jual'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'stok_minimum'   => 'required|integer|min:0',
            'category_id'    => 'nullable|exists:categories,id',
            'uom_id'         => 'nullable|exists:uoms,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['kode_product'] = 'PRD-' . strtoupper(Str::random(6));
        $validated['created_by']   = auth()->user()->name ?? 'system';

        $product = WMS_Global::product()->create($validated);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'product' => $product,
        ]);
    }

    /**
     * Tampilkan detail produk
     */
    public function show($id)
    {
        $product = WMS_Global::product()
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('uoms', 'uoms.id', '=', 'products.uom_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->select(
                'products.*',
                'categories.name as category_name',
                'uoms.name as uom_name',
                'suppliers.name as supplier_name'
            )
            ->findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $r, $id)
    {
        $product = WMS_Global::product()->findOrFail($id);

        $validated = $r->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'harga_beli'     => 'nullable|numeric|min:0',
            'harga_jual'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'stok_minimum'   => 'required|integer|min:0',
            'category_id'    => 'nullable|exists:categories,id',
            'uom_id'         => 'nullable|exists:uoms,id',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['updated_by'] = auth()->user()->name ?? 'system';

        $product->update($validated);

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'product' => $product,
        ]);
    }


    public function destroy($id)
    {
        $product = WMS_Global::product()->findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}

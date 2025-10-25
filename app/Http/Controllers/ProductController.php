<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
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

        $query = Product::query();

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode_product', 'like', "%{$search}%");
            });
        }

        // Filter status stok
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
                    'status'         => $p->stock_status, // ready / low / empty
                    'created_at'     => $p->created_at->format('Y-m-d H:i'),
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

    /**
     * Simpan produk baru
     */
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
        ]);

        $validated['kode_product'] = 'PRD-' . strtoupper(Str::random(6));
        $validated['created_by']   = auth()->user()->name ?? 'system';

        $product = Product::create($validated);

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
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update produk
     */
    public function update(Request $r, $id)
    {
        $product = Product::findOrFail($id);

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
        ]);

        $product->update($validated);

        return response()->json(['message' => 'Produk berhasil diperbarui.']);
    }

    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }
}

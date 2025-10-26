<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WMS_Global extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && $model->isFillable('created_by')) {
                $model->created_by = Auth::user()->name ?? Auth::user()->email;
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->updated_by = Auth::user()->name ?? Auth::user()->email;
            }
        });
    }

    // ===== Mapping kolom dinamis per tabel =====
    protected function setFillableFor($table)
    {
        $map = [
            'products' => [
                'kode_product', 'name', 'description',
                'harga_beli', 'harga_jual', 'stock_quantity',
                'stok_minimum', 'category_id', 'uom_id',
                'supplier_id', 'is_active', 'created_by', 'updated_by'
            ],
            'categories' => ['name', 'description', 'created_by', 'updated_by'],
            'suppliers'  => ['name', 'address', 'phone', 'created_by', 'updated_by'],
            'uoms'       => ['name', 'symbol', 'created_by', 'updated_by'],
        ];

        $this->fillable = $map[$table] ?? [];
        return $this;
    }

    // ===== Factory Instance per tabel =====
    protected static function instanceFor($table)
    {
        $instance = new static;
        $instance->setTable($table);
        $instance->setFillableFor($table);
        return $instance;
    }

    public static function product()
    {
        return static::instanceFor('products');
    }

    public static function category()
    {
        return static::instanceFor('categories');
    }

    public static function supplier()
    {
        return static::instanceFor('suppliers');
    }

    public static function uom()
    {
        return static::instanceFor('uoms');
    }

    // ===== Accessor tambahan =====
    public function getStockStatusAttribute()
    {
        if (!isset($this->stock_quantity) || !isset($this->stok_minimum)) {
            return null;
        }

        if ($this->stock_quantity <= 0) return 'empty';
        if ($this->stock_quantity <= $this->stok_minimum) return 'low';
        return 'ready';
    }
}

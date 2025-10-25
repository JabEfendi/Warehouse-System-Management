<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WMS_Global extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = null; // akan diatur dinamis
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        // Otomatis isi created_by & updated_by bila kolomnya ada
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

    /*
    |--------------------------------------------------------------------------
    | SECTION: PRODUCT MODEL LOGIC
    |--------------------------------------------------------------------------
    */
    public static function product()
    {
        $instance = new static;
        $instance->table = 'products';
        $instance->fillable = [
            'kode_product',
            'name',
            'description',
            'harga_beli',
            'harga_jual',
            'stock_quantity',
            'stok_minimum',
            'category_id',
            'uom_id',
            'supplier_id',
            'is_active',
            'created_by',
        ];
        return $instance;
    }

    // Fungsi relasi manual (dipanggil setelah product() digunakan)
    public function category()
    {
        return $this->belongsTo(WMS_Global::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WMS_Global::class, 'supplier_id');
    }

    public function uom()
    {
        return $this->belongsTo(WMS_Global::class, 'uom_id');
    }

    // Status stok otomatis
    public function getStockStatusAttribute()
    {
        if (!isset($this->stock_quantity) || !isset($this->stok_minimum)) {
            return null;
        }

        if ($this->stock_quantity <= 0) {
            return 'empty';
        } elseif ($this->stock_quantity <= $this->stok_minimum) {
            return 'low';
        } else {
            return 'ready';
        }
    }
}

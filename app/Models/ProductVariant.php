<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'variant_name',
        'edition',
        'box_condition',
        'item_condition',
        'has_manual',
        'has_decals',
        'purchase_price',
        'sale_price',
        'min_stock',
        'status',
    ];

    protected $casts = [
        'has_manual' => 'boolean',
        'has_decals' => 'boolean',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_stock' => 'decimal:3',
        'deleted_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

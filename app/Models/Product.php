<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'base_unit_id',
        'manufacturer_id',
        'series_id',
        'kit_code',
        'name',
        'slug',
        'grade',
        'scale',
        'material',
        'runner_count',
        'release_date',
        'box_art_url',
        'description',
        'status',
    ];

    protected $casts = [
        'runner_count' => 'integer',
        'release_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(ModelKitManufacturer::class, 'manufacturer_id');
    }

    public function series()
    {
        return $this->belongsTo(ModelKitSeries::class, 'series_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelKitSeries extends Model
{
    use SoftDeletes;

    protected $table = 'model_kit_series';

    protected $fillable = [
        'manufacturer_id',
        'name',
        'slug',
        'universe',
        'description',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(ModelKitManufacturer::class, 'manufacturer_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'series_id');
    }
}

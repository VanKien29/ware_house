<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelKitManufacturer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'country',
        'website_url',
        'description',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

}

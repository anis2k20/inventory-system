<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock_quantity',
        'description',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'image' => 'array',
    ];

    protected $appends = ['image_url', 'thumbnail_url'];

    public function getImageUrlAttribute()
    {
        return $this->image && isset($this->image['original']) ? asset('storage/' . $this->image['original']) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->image && isset($this->image['thumbnail']) ? asset('storage/' . $this->image['thumbnail']) : null;
    }
}

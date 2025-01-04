<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['brand_id', 'category_id', 'name', 'part_number', 'description', 'mrp', 'selling_price', 'buying_price', 'product_images'];
    protected $casts = ["product_images" => "array"];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

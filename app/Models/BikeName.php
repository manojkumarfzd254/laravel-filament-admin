<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeName extends Model
{
    protected $fillable = ['name', 'image', 'brand_id', 'status'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}

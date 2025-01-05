<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = ['product_id', 'quantity', 'operation', 'description'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

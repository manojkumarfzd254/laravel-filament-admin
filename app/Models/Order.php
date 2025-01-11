<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total_amount', 'status', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity', 'amount', 'mrp', 'product_id')
                    ->withTimestamps();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

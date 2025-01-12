<?php

namespace App\Models;

use App\Jobs\GenerateInvoicePdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total_amount', 'status', 'description', 'invoice_path'];


    protected static function booted(): void
    {
        static::created(function(Order $order) {
          
            GenerateInvoicePdf::dispatch($order);
        });
    }
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Stock;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $items = OrderProduct::where('order_id', $order->id)->get();
        foreach ($items as $item) {
            Stock::create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'operation' => 'remove',
                'description' => $order->description
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}

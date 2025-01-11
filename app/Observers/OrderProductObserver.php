<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Stock;

class OrderProductObserver
{
    /**
     * Handle the OrderProduct "created" event.
     */
    public function created(OrderProduct $orderProduct): void
    {
        $order = Order::find($orderProduct->order_id);
        Stock::create([
            'product_id' => $orderProduct->product_id,
            'quantity' => $orderProduct->quantity,
            'operation' => 'remove',
            'description' => $order->description
        ]);
    }

    /**
     * Handle the OrderProduct "updated" event.
     */
    public function updated(OrderProduct $orderProduct): void
    {
        //
    }

    /**
     * Handle the OrderProduct "deleted" event.
     */
    public function deleted(OrderProduct $orderProduct): void
    {
        //
    }

    /**
     * Handle the OrderProduct "restored" event.
     */
    public function restored(OrderProduct $orderProduct): void
    {
        //
    }

    /**
     * Handle the OrderProduct "force deleted" event.
     */
    public function forceDeleted(OrderProduct $orderProduct): void
    {
        //
    }
}

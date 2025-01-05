<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Stock;

class StockObserver
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {
        $productStocks = Stock::where('product_id', $stock->product_id)->where('operation','add')->get();
        $productStockRemove = Stock::where('product_id', $stock->product_id)->where('operation','remove')->get();
        $product = Product::find($stock->product_id);
        $product->quantity = $productStocks->sum('quantity') - $productStockRemove->sum('quantity');
        $product->save();
    }

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        $productStocks = Stock::where('product_id', $stock->product_id)->where('operation','add')->get();
        $productStockRemove = Stock::where('product_id', $stock->product_id)->where('operation','remove')->get();
        $product = Product::find($stock->product_id);
        $product->quantity = $productStocks->sum('quantity') - $productStockRemove->sum('quantity');
        $product->save();
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        $productStocks = Stock::where('product_id', $stock->product_id)->where('operation','add')->get();
        $productStockRemove = Stock::where('product_id', $stock->product_id)->where('operation','remove')->get();
        $product = Product::find($stock->product_id);
        $product->quantity = $productStocks->sum('quantity') - $productStockRemove->sum('quantity');
        $product->save();
    }

    /**
     * Handle the Stock "restored" event.
     */
    public function restored(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "force deleted" event.
     */
    public function forceDeleted(Stock $stock): void
    {
        //
    }
}

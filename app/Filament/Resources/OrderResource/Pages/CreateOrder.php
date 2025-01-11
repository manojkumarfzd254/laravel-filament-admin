<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\OrderProduct;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    public function handleRecordCreation(array $data): Model
    {
        $items = $data['products'];
        unset($data['products']);
        $order = static::getModel()::create($data);
        foreach ($items as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'customer_id' => $data['customer_id'],
                'quantity' => $item['quantity'],
                'amount' => $item['amount'],
                'mrp' => $item['mrp'],
                'discount' => $item['discount'] ?? 0,
            ]);
        }
        Notification::make()
        ->title('Order created successfully')
        ->success()
        ->send();
        return $order;
    }
}
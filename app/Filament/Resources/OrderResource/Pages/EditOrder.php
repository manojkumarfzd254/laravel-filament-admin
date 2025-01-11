<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use App\Models\OrderProduct;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $items = $data['products'];
        return $record;
    }

    public function handleRecordEditing(array $data): Model
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

<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // dd($data);
        if (isset($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);

            if ($customer) {
                $data['email'] = $customer->email;
                $data['phone_number'] = $customer->phone_number;
                $data['address'] = $customer->address;
                $data['state'] = $customer->state;
                $data['city'] = $customer->city;
            }
        }

        return $data;
    }
}

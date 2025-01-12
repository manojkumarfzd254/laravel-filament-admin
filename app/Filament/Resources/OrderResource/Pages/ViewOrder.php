<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
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

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make("Customer Details")
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('customer.name')->label('Customer Name'),
                        Infolists\Components\TextEntry::make('customer.email')->label('Email'),
                        Infolists\Components\TextEntry::make('customer.phone_number')->label('Phone Number'),
                        Infolists\Components\TextEntry::make('customer.state')->label('State'),
                        Infolists\Components\TextEntry::make('customer.city')->label('City'),
                        Infolists\Components\TextEntry::make('customer.address')->label('Address')->columnSpanFull(),
                    ]),
                Section::make("Order Details")
                    ->columns(3)
                    ->schema([
                        // Using RepeatableEntry in a proper way by looping through the products relationship
                        RepeatableEntry::make('products')
                            ->schema([
                                Grid::make(5) // Using a grid to simulate a table structure
                                ->schema([
                                    Infolists\Components\TextEntry::make('product.name')->label('Product Name')->columnSpan(1),
                                    Infolists\Components\ImageEntry::make('product.product_images')->size(40)->label('Product Images')->columnSpan(1),
                                    Infolists\Components\TextEntry::make('quantity')->label('Quantity')->columnSpan(1),
                                    Infolists\Components\TextEntry::make('mrp')->money('inr')->label('MRP')->columnSpan(1),
                                    Infolists\Components\TextEntry::make('amount')->money('inr')->label('Amount')->columnSpan(1),
                                ])
                            ])
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('total_amount')->money('inr')->label('Total Amount'),
                        Infolists\Components\TextEntry::make('status')->label('Status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'success' => 'primary',
                            'canceled' => 'danger',
                        }),
                        Infolists\Components\TextEntry::make('created_at')->label('Created At')->date('F j, Y, g:i A'),
                        Infolists\Components\TextEntry::make('description')->label('Description')->html()->columnSpanFull(),
                        
                    ])
            ]);
    }
}

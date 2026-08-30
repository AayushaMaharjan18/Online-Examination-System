<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('tax')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->required()
                    ->default('pending')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                TextInput::make('payment_method')
                    ->required()
                    ->default('cod'),
                TextInput::make('payment_status')
                    ->required()
                    ->default('unpaid'),
                TextInput::make('shipping_name')
                    ->required(),
                TextInput::make('shipping_phone')
                    ->tel()
                    ->required(),
                TextInput::make('shipping_district')
                    ->required(),
                TextInput::make('shipping_municipality')
                    ->required(),
                TextInput::make('shipping_ward')
                    ->required(),
                TextInput::make('shipping_street')
                    ->required(),
                DatePicker::make('estimated_delivery_date'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('label')
                    ->required()
                    ->default('Home'),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('district')
                    ->required(),
                TextInput::make('municipality')
                    ->required(),
                TextInput::make('ward')
                    ->required(),
                TextInput::make('street')
                    ->required(),
                Toggle::make('is_default')
                    ->required(),
            ]);
    }
}

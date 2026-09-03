<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Review;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product.name')->label('Product')->disabled(),
                TextInput::make('user.name')->label('Customer')->disabled(),
                TextInput::make('user.email')->label('Email')->disabled(),
                TextInput::make('order.order_number')->label('Order #')->disabled(),
                Select::make('rating')->options([
                    1 => '1 - Poor',
                    2 => '2 - Fair',
                    3 => '3 - Good',
                    4 => '4 - Very Good',
                    5 => '5 - Excellent',
                ])->required(),
                TextInput::make('title')->maxLength(255),
                Textarea::make('comment')->required()->rows(4),
                Select::make('status')
                    ->options([
                        Review::STATUS_PENDING => 'Pending',
                        Review::STATUS_APPROVED => 'Approved',
                        Review::STATUS_REJECTED => 'Rejected',
                    ])
                    ->default(Review::STATUS_PENDING)
                    ->required(),
                Toggle::make('is_visible_on_homepage')->label('Show on homepage'),
                Toggle::make('is_verified_purchase')
                    ->label('Verified purchase')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}


<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('short_description')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('रू'),
                TextInput::make('compare_price')
                    ->numeric()
                    ->prefix('रू'),
                TextInput::make('final_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('रू'),
                TextInput::make('discount_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('in_stock')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_new')
                    ->required(),
                Toggle::make('is_best_seller')
                    ->required(),
                Toggle::make('is_limited_edition')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('gender'),
                TextInput::make('movement'),
                TextInput::make('strap'),
                TextInput::make('case_material'),
                TextInput::make('case_diameter'),
                TextInput::make('case_thickness'),
                TextInput::make('water_resistance'),
                TextInput::make('dial_color'),
                TextInput::make('glass_type'),
                TextInput::make('warranty_period'),
                TextInput::make('average_rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('reviews_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('brand_id')
                    ->numeric(),
                FileUpload::make('images')
                    ->label('Product images (upload from laptop)')
                    ->image()
                    ->imageEditor()
                    ->multiple()
                    ->directory('products')
                    ->disk('public')
                    ->columnSpanFull(),
                FileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->image()
                    ->imageEditor()
                    ->directory('products/thumbnails')
                    ->disk('public'),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
            ]);
    }
}

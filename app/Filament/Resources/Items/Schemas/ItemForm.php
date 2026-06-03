<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('category_id')
                    ->numeric()
                    ->default(null),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('short_description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('sales_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('company')
                    ->default(null),
                Select::make('accepted')
                    ->options(['accepted' => 'Accepted', 'rejected' => 'Rejected', 'pending' => 'Pending'])
                    ->default('pending')
                    ->required(),
                TextInput::make('priceAfterDiscount')
                    ->numeric()
                    ->default(null),
                TextInput::make('DiscountPercentage')
                    ->numeric()
                    ->default(null),
                Toggle::make('availability')
                    ->required(),
                FileUpload::make('item_image')
                    ->image()
                    ->required(),
                Textarea::make('details_image')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // تأكد من أن أسماء الأعمدة هنا تطابق أسماء الحقول في قاعدة بياناتك
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('product name')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('price')
                    ->label('price')
                    ->money('SAR'),


                \Filament\Tables\Columns\TextColumn::make('category.name') // الوصول للعلاقة
                ->label('Category')
                    ->sortable(),


//                \Filament\Tables\Columns\TextColumn::make('availability')
//                    ->label('availability')
//                    ->sortable(),


                \Filament\Tables\Columns\IconColumn::make('availability')
                ->label('availability')
                ->boolean()
                ->sortable(),




            ])

            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),


            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);


    }
}

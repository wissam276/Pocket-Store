<?php

namespace App\Filament\Resources\Items\Tables;

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

                \Filament\Tables\Columns\TextColumn::make('accepted')
                    ->label('Status')
                    ->sortable()
                // أضف أي حقول أخرى تحتاجها
            ])
            ->filters([
                Filter::make('Pending Acceptance')
                    ->query(fn (Builder $query): Builder => $query->where('accepted', 'pending')),

                Filter::make('Accepted')
                    ->query(fn (Builder $query): Builder => $query->where('accepted', 'accepted')),

                Filter::make('Rejected')
                    ->query(fn (Builder $query): Builder => $query->where('accepted', 'rejected')),
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

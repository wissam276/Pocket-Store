<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Last Order')
                    ->sortable()
                    ->searchable(),


                \Filament\Tables\Columns\TextColumn::make('customer_name')
                ->label('Customer Name')
                    ->getStateUsing(function ($record) {
                        return $record->user ? $record->user->first_name . ' ' . $record->user->second_name : 'N/A';
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->whereHas('user', function ($q) use ($direction) {
                            $q->orderBy('first_name', $direction);
                        });
                    })
                    ->searchable(['first_name', 'second_name']),



                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('status')
                    ->sortable()
                    ->searchable(),
            ])


            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

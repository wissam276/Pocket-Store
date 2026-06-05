<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('new Item')
                ->icon('heroicon-o-plus')
                ->color('primary')

        ];
    }
}

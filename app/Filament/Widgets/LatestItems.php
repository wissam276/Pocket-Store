<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ItemResource;
use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestItems extends BaseWidget
{
    protected static ?int $sort = 2;
    // تحديد عدد المنتجات التي تظهر
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'أحدث المنتجات المضافة';
    public function table(Table $table): Table
    {
        return $table
            ->query(Item::query()->latest())
            ->defaultPaginationPageOption(5) // يبدأ بعرض 5 ويسمح للمستخدم بزيادة العدد ->paginated(false) // تعطيل التنقل بين الصفحات
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المنتج'),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime(),
            ]);
    }
}

<?php


namespace App\Filament\Widgets;


use App\Models\User;
use App\Models\Item; // أو اسم الموديل لديك إذا كان Item
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;



class ShopOverview extends BaseWidget
{
    protected static ?int $sort = 1;



    protected function getStats(): array
    {

        return[

            Stat::make('Customer Count', User::where('role', 'customer')->count())
                ->description("Total number of customers")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),



            Stat::make('Products Count',Item::count())
            ->description("Total number of products")
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('warning'),


        ];

    }
    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }



}

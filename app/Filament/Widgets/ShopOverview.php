<?php


namespace App\Filament\Widgets;


use App\Models\User;
use App\Models\Item; // أو اسم الموديل لديك إذا كان Item
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class ShopOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return[
            Stat::make('Customer Count', User::where('role', 'customer')->count())
                ->description("Total number of customers")
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),


            Stat::make('Seller Count', User::where('role', 'seller')->count())
            ->description("Total number of sellers")
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('info'),


            Stat::make('Products Count',Item::count())
            ->description("Total number of products")
            ->descriptionIcon('heroicon-m-shopping-cart')
            ->color('warning'),


            Stat::make('Accepted Items', Item::where('accepted', 'accepted')->count())
                ->description('Total number of accepted products')
                ->color('success'),

            Stat::make('Rejected Items', Item::where('accepted', 'rejected')->count())
                ->description('Total number of rejected products')
                ->color('danger'),

            Stat::make('Pending Items', Item::where('accepted', 'pending')->count())
                ->description('Total number of pending products')
                ->color('info'),
        ];





    }
    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}

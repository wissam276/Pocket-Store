<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use App\Models\Order;
use Filament\Actions\Action; // تأكد من هذا الاستدعاء
use Filament\Resources\Pages\EditRecord;

class EditOrders extends EditRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // استخدم Action مباشرة لأنك قمت بـ use في الأعلى
            Action::make('markAsShipped')
                ->label('شحن الطلب')
                ->icon('heroicon-o-truck')
                ->color('primary')
                ->action(function (Order $record) {
                    $record->update(['status' => 'shipped']);
                    // اختياري: إرسال تنبيه للمدير
                    \Filament\Notifications\Notification::make()
                        ->title('تم شحن الطلب بنجاح')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->visible(fn (Order $record) => $record->status === 'pending'),
        ];
    }
}

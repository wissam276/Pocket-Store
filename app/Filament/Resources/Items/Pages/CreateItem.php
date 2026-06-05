<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    // هذا هو المكان الصحيح لإضافة الـ user_id تلقائياً
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = auth()->id(); // يحفظ الـ ID للمستخدم الحالي في قاعدة البيانات

        return $data;
    }
}

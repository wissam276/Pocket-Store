<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms;
//use Filament\Schemas\Form;


class OrdersForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات الطلب')
                    ->schema([
                        // اختيار العميل
                        Select::make('user_id')
                            ->relationship('user', 'first_name') // افترضنا وجود العلاقة
                            ->label('العميل')
                            ->required()
                            ->searchable(),



                        Select::make('user_id')
                            ->relationship('user', 'first_name')
                            ->label('العميل')
                            ->required()
                            ->searchable()
                            ->live() // هذا يجعل الـ Select يرسل طلباً للخادم عند تغير القيمة
                            ->afterStateUpdated(fn ($state, $set) => $set('phone_number', \App\Models\User::find($state)?->phone_number)),

                        TextInput::make('phone_number') // غير الاسم هنا ليكون اسماً مستقلاً
                        ->label('رقم هاتف العميل')
                            ->disabled()
                            ->dehydrated(false) // يمنع الحفظ
                            ->afterStateHydrated(function ($component, $state, $record) {
                                // يتم تنفيذ هذا عند فتح الصفحة (لتحميل الرقم من الريكورد الحالي)
                                if ($record && $record->user) {
                                    $component->state($record->user->phone_number);
                                }
                            }),



                        // حالة الطلب
                        Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Confirmed' => 'Confirmed',
                                'Processing' => 'Processing',
                                'Shipped' => 'Shipped',
                                'Delivered' => 'Delivered',
                                'Canceled' => 'Canceled',
                            ])
                             ->required(),

                        // السعر الإجمالي
                        TextInput::make('total_price')
                            ->label('الإجمالي')
                            ->numeric()
                            ->prefix('SAR')
                            ->required(),
                    ])->columns(2),





                Section::make('المنتجات في الطلب')
                    ->schema([
                        Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('Item', 'name')
                                    ->required(),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ])
            ]);
    }
}

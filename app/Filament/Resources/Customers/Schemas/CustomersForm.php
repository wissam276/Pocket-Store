<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class CustomersForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('معلومات العميل الشخصية')
                ->schema([
                    TextInput::make('first_name')
                        ->label('الاسم الأول')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('second_name')
                        ->label('اسم العائلة')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('phone_number')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(20),
                ])->columns(2),

            Section::make('الأمان')
                ->schema([
                    TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // مطلوبة عند الإنشاء فقط
                        ->revealable(),
                ]),
        ]);
    }
}

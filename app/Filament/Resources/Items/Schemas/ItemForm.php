<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
//use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات المنتج')
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم المنتج')
                            ->required()
                            ->live(onBlur: true) // هذا السطر هو الأهم: يجعل الحقل يراقب التغيرات
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                // بمجرد تغيير الاسم، يتم تعبئة حقل الـ slug تلقائياً
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('الرابط (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true), // لضمان عدم تكرار الرابط في قاعدة البيانات

                        // الحقل المطلوب: category_id كـ Select مرتبط بجدول التصنيفات
                        Select::make('category_id')
                            ->label('التصنيف')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('SAR'),

                        TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                    ])->columns(2),

                Section::make('التفاصيل والوصف')
                    ->schema([
                        Textarea::make('short_description'),
                        RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('الأسعار والخيارات')
                    ->schema([
                        TextInput::make('sales_count')
                            ->numeric()
                            ->default(0),
                        TextInput::make('company'),
                        TextInput::make('priceAfterDiscount')
                            ->numeric(),
                        TextInput::make('DiscountPercentage')
                            ->numeric(),
                        Toggle::make('availability')
                            ->default(true),
                    ])->columns(3),

                Section::make('الوسائط')
                    ->schema([
                        FileUpload::make('item_image')
                            ->image()
                            ->required(),
                        FileUpload::make('details_image')
                            ->label('صور إضافية')
                            ->multiple() // يتعامل مع JSON بشكل ممتاز
                            ->image(),
                    ]),

            ]);
    }
}

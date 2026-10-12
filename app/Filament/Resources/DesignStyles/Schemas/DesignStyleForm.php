<?php

namespace App\Filament\Resources\DesignStyles\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DesignStyleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                TextInput::make('name')->label('اسم النمط')->maxLength(255)->required(),
                Textarea::make('description')->label('الوصف')->rows(3)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
            Section::make('العرض والترتيب')->schema([
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')->label('ترتيب الظهور')->numeric()->integer()->minValue(0)->default(0)->required(),
            ])->columns(2)->columnSpanFull(),
            Section::make('الصور والمرفقات')->schema([
                SpatieMediaLibraryFileUpload::make('cover')->label('صورة الغلاف')->collection('cover')->disk('public')->maxSize(10240)->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('gallery')->label('معرض الصور')->collection('gallery')->disk('public')->maxSize(10240)->multiple()->reorderable()->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}

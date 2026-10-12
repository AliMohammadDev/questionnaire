<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->description('ترفع الصور والمرفقات من صفحة السجل المرتبط بها. هنا يمكنك مراجعتها وتعديل أسمائها.')->schema([
                TextInput::make('name')->label('اسم الملف')->maxLength(255)->required(),
                TextInput::make('file_name')->label('اسم الملف المخزن')->disabled()->dehydrated(false),
                TextInput::make('collection_name')->label('مجموعة الملفات')->disabled()->dehydrated(false),
                TextInput::make('mime_type')->label('نوع الملف')->disabled()->dehydrated(false),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}

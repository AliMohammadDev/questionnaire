<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->schema([
                TextInput::make('name')->label('اسم العميل')->maxLength(255)->required(),
                TextInput::make('phone')->label('رقم الهاتف')->maxLength(100)->tel()->required(),
                TextInput::make('email')->label('البريد الإلكتروني')->maxLength(255)->email(),
                Textarea::make('address')->label('العنوان')->rows(3)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}

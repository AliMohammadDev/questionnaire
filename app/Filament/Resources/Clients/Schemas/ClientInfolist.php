<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('name')->label('اسم العميل')->placeholder('—'),
                TextEntry::make('phone')->label('رقم الهاتف')->placeholder('—'),
                TextEntry::make('email')->label('البريد الإلكتروني')->placeholder('—'),
                TextEntry::make('address')->label('العنوان')->placeholder('—')->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}

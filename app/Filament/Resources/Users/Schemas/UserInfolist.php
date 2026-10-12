<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('name')->label('الاسم')->placeholder('—'),
                TextEntry::make('email')->label('البريد الإلكتروني')->placeholder('—')->copyable()->icon(Heroicon::OutlinedEnvelope),
                TextEntry::make('roles.name')->label('الأدوار')->badge()->placeholder('دون دور'),
                TextEntry::make('permissions.name')->label('صلاحيات إضافية')->badge()->placeholder('لا توجد'),
                TextEntry::make('email_verified_at')->label('تاريخ تأكيد البريد')->dateTime('Y/m/d H:i')->placeholder('غير مؤكد'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}

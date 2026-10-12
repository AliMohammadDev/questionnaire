<?php

namespace App\Filament\Resources\AnswerOptions\Schemas;

use App\Filament\Support\QuestionnaireEntries;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AnswerOptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('answer.project.project_code')->label('المشروع')->placeholder('—'),
                TextEntry::make('answer.question.title')->label('السؤال')->placeholder('—')->columnSpanFull(),
                TextEntry::make('answer.id')->label('الإجابة')->placeholder('—'),
                TextEntry::make('questionOption.label')->label('الخيار المختار')->placeholder('—'),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps(),
        ]);
    }
}

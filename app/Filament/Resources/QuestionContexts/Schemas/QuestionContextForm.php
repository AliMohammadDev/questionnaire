<?php

namespace App\Filament\Resources\QuestionContexts\Schemas;

use App\Filament\Support\QuestionnaireFields;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionContextForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->description('اترك أي مستوى فارغاً ليشمل جميع قيمه. يجب أن تتوافق الاختيارات مع هيكل المشروع.')->schema([
                QuestionnaireFields::relationship('question_id', 'question', 'title', 'السؤال'),
                QuestionnaireFields::department(false),
                QuestionnaireFields::category(false),
                QuestionnaireFields::projectType(false),
                QuestionnaireFields::relationship('design_style_id', 'designStyle', 'name', 'النمط التصميمي')->required(false),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}

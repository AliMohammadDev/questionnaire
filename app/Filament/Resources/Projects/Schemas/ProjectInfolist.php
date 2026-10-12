<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\ProjectAnswerEntries;
use App\Filament\Support\QuestionnaireEntries;
use App\Models\Answer;
use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('البيانات الأساسية')->icon(Heroicon::OutlinedInformationCircle)->schema([
                TextEntry::make('client.name')->label('العميل')->placeholder('—'),
                TextEntry::make('project_code')->label('كود المشروع')->placeholder('—')->copyable(),
                TextEntry::make('project_name')->label('اسم المشروع')->placeholder('—'),
                TextEntry::make('department.name')->label('القسم الرئيسي')->placeholder('—'),
                TextEntry::make('projectCategory.name')->label('تصنيف المشروع')->placeholder('—'),
                TextEntry::make('projectType.name')->label('نوع المشروع')->placeholder('—'),
                TextEntry::make('designStyle.name')->label('النمط التصميمي')->placeholder('—'),
                TextEntry::make('submitted_at')->label('تاريخ الإرسال')->placeholder('—')->dateTime('Y/m/d H:i'),
                TextEntry::make('completed_at')->label('تاريخ الإكمال')->placeholder('—')->dateTime('Y/m/d H:i'),
                TextEntry::make('notes')->label('الملاحظات')->placeholder('—')->columnSpanFull(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            Section::make('أسئلة المشروع وإجاباتها')
                ->description('الأسئلة مرتبة حسب أقسام الاستبيان، وتظهر الإجابة أسفل كل سؤال.')
                ->schema(fn (Project $record): array => ProjectAnswerEntries::sections($record))
                ->visible(fn (): bool => Gate::allows('viewAny', Answer::class) && Gate::allows('view', new Answer))
                ->columnSpanFull(),
            Section::make('الصور والمرفقات')->icon(Heroicon::OutlinedPhoto)->schema([
                QuestionnaireEntries::attachments(),
            ])->columns(['default' => 1, 'md' => 2])->columnSpanFull(),
            QuestionnaireEntries::timestamps()->extraAttributes(['class' => 'project-record-metadata']),
        ]);
    }
}

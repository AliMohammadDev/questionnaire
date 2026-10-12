<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Answers\AnswerResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Answer;
use App\Models\Client;
use App\Models\Project;
use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuestionnaireStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('المشاريع', Project::count())->description('جميع مشاريع العملاء')->descriptionIcon('heroicon-o-briefcase')->color('info')->extraAttributes(['class' => 'questionnaire-stat questionnaire-stat-blue'])->url(ProjectResource::getUrl()),
            Stat::make('الإجابات', Answer::count())->description('إجابات الاستبيانات المسجلة')->descriptionIcon('heroicon-o-chat-bubble-left-right')->color('warning')->extraAttributes(['class' => 'questionnaire-stat questionnaire-stat-yellow'])->url(AnswerResource::getUrl()),
            Stat::make('العملاء', Client::count())->description('العملاء المسجلون')->descriptionIcon('heroicon-o-user-group')->color('danger')->extraAttributes(['class' => 'questionnaire-stat questionnaire-stat-red'])->url(ClientResource::getUrl()),
            Stat::make('الأسئلة الفعالة', Question::where('is_active', true)->count())->description('أسئلة الاستبيان المتاحة')->descriptionIcon('heroicon-o-question-mark-circle')->color('success')->extraAttributes(['class' => 'questionnaire-stat questionnaire-stat-green'])->url(QuestionResource::getUrl()),
        ];
    }
}

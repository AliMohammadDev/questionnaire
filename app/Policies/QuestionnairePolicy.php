<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\AnswerType;
use App\Models\Client;
use App\Models\Department;
use App\Models\DesignStyle;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectType;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePolicy
{
    public function delete(User $user, Model $record): Response
    {
        if ($record instanceof User && $record->is($user)) {
            return Response::deny('لا يمكنك حذف حسابك أثناء استخدام لوحة الإدارة.');
        }
        $relationships = match ($record::class) {
            Department::class => ['projectCategories', 'questionContexts', 'projects'],
            ProjectCategory::class => ['projectTypes', 'questionContexts', 'projects'],
            ProjectType::class, DesignStyle::class => ['questionContexts', 'projects'],
            QuestionSection::class, AnswerType::class => ['questions'],
            Question::class => ['options', 'contexts', 'conditions', 'dependentConditions', 'answers'],
            QuestionOption::class => ['questionConditions', 'answerOptions'],
            Client::class => ['projects'],
            Project::class => ['answers'],
            Answer::class => ['answerOptions'],
            default => [],
        };
        foreach ($relationships as $relationship) {
            if ($record->{$relationship}()->exists()) {
                return Response::deny('هذا السجل مستخدم. عدّل السجلات المرتبطة به قبل حذفه.');
            }
        }

        return Response::allow();
    }
}

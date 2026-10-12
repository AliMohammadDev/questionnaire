<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AnswerOption;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AnswerOptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AnswerOption');
    }

    public function view(AuthUser $authUser, AnswerOption $answerOption): bool
    {
        return $authUser->can('View:AnswerOption');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AnswerOption');
    }

    public function update(AuthUser $authUser, AnswerOption $answerOption): bool
    {
        return $authUser->can('Update:AnswerOption');
    }

    public function delete(AuthUser $authUser, AnswerOption $answerOption): Response
    {
        return $authUser->can('Delete:AnswerOption')
            ? (new QuestionnairePolicy)->delete($authUser, $answerOption)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AnswerOption');
    }

    public function restore(AuthUser $authUser, AnswerOption $answerOption): bool
    {
        return $authUser->can('Restore:AnswerOption');
    }

    public function forceDelete(AuthUser $authUser, AnswerOption $answerOption): bool
    {
        return $authUser->can('ForceDelete:AnswerOption');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AnswerOption');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AnswerOption');
    }

    public function replicate(AuthUser $authUser, AnswerOption $answerOption): bool
    {
        return $authUser->can('Replicate:AnswerOption');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AnswerOption');
    }
}

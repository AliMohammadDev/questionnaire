<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AnswerType;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AnswerTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AnswerType');
    }

    public function view(AuthUser $authUser, AnswerType $answerType): bool
    {
        return $authUser->can('View:AnswerType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AnswerType');
    }

    public function update(AuthUser $authUser, AnswerType $answerType): bool
    {
        return $authUser->can('Update:AnswerType');
    }

    public function delete(AuthUser $authUser, AnswerType $answerType): Response
    {
        return $authUser->can('Delete:AnswerType')
            ? (new QuestionnairePolicy)->delete($authUser, $answerType)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AnswerType');
    }

    public function restore(AuthUser $authUser, AnswerType $answerType): bool
    {
        return $authUser->can('Restore:AnswerType');
    }

    public function forceDelete(AuthUser $authUser, AnswerType $answerType): bool
    {
        return $authUser->can('ForceDelete:AnswerType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AnswerType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AnswerType');
    }

    public function replicate(AuthUser $authUser, AnswerType $answerType): bool
    {
        return $authUser->can('Replicate:AnswerType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AnswerType');
    }
}

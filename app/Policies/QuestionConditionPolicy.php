<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QuestionCondition;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class QuestionConditionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuestionCondition');
    }

    public function view(AuthUser $authUser, QuestionCondition $questionCondition): bool
    {
        return $authUser->can('View:QuestionCondition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuestionCondition');
    }

    public function update(AuthUser $authUser, QuestionCondition $questionCondition): bool
    {
        return $authUser->can('Update:QuestionCondition');
    }

    public function delete(AuthUser $authUser, QuestionCondition $questionCondition): Response
    {
        return $authUser->can('Delete:QuestionCondition')
            ? (new QuestionnairePolicy)->delete($authUser, $questionCondition)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QuestionCondition');
    }

    public function restore(AuthUser $authUser, QuestionCondition $questionCondition): bool
    {
        return $authUser->can('Restore:QuestionCondition');
    }

    public function forceDelete(AuthUser $authUser, QuestionCondition $questionCondition): bool
    {
        return $authUser->can('ForceDelete:QuestionCondition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuestionCondition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuestionCondition');
    }

    public function replicate(AuthUser $authUser, QuestionCondition $questionCondition): bool
    {
        return $authUser->can('Replicate:QuestionCondition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuestionCondition');
    }
}

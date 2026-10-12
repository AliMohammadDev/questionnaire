<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QuestionSection;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class QuestionSectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuestionSection');
    }

    public function view(AuthUser $authUser, QuestionSection $questionSection): bool
    {
        return $authUser->can('View:QuestionSection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuestionSection');
    }

    public function update(AuthUser $authUser, QuestionSection $questionSection): bool
    {
        return $authUser->can('Update:QuestionSection');
    }

    public function delete(AuthUser $authUser, QuestionSection $questionSection): Response
    {
        return $authUser->can('Delete:QuestionSection')
            ? (new QuestionnairePolicy)->delete($authUser, $questionSection)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QuestionSection');
    }

    public function restore(AuthUser $authUser, QuestionSection $questionSection): bool
    {
        return $authUser->can('Restore:QuestionSection');
    }

    public function forceDelete(AuthUser $authUser, QuestionSection $questionSection): bool
    {
        return $authUser->can('ForceDelete:QuestionSection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuestionSection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuestionSection');
    }

    public function replicate(AuthUser $authUser, QuestionSection $questionSection): bool
    {
        return $authUser->can('Replicate:QuestionSection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuestionSection');
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\QuestionContext;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class QuestionContextPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuestionContext');
    }

    public function view(AuthUser $authUser, QuestionContext $questionContext): bool
    {
        return $authUser->can('View:QuestionContext');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuestionContext');
    }

    public function update(AuthUser $authUser, QuestionContext $questionContext): bool
    {
        return $authUser->can('Update:QuestionContext');
    }

    public function delete(AuthUser $authUser, QuestionContext $questionContext): Response
    {
        return $authUser->can('Delete:QuestionContext')
            ? (new QuestionnairePolicy)->delete($authUser, $questionContext)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:QuestionContext');
    }

    public function restore(AuthUser $authUser, QuestionContext $questionContext): bool
    {
        return $authUser->can('Restore:QuestionContext');
    }

    public function forceDelete(AuthUser $authUser, QuestionContext $questionContext): bool
    {
        return $authUser->can('ForceDelete:QuestionContext');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuestionContext');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuestionContext');
    }

    public function replicate(AuthUser $authUser, QuestionContext $questionContext): bool
    {
        return $authUser->can('Replicate:QuestionContext');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuestionContext');
    }
}

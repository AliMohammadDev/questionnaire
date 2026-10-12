<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProjectType;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProjectTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectType');
    }

    public function view(AuthUser $authUser, ProjectType $projectType): bool
    {
        return $authUser->can('View:ProjectType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectType');
    }

    public function update(AuthUser $authUser, ProjectType $projectType): bool
    {
        return $authUser->can('Update:ProjectType');
    }

    public function delete(AuthUser $authUser, ProjectType $projectType): Response
    {
        return $authUser->can('Delete:ProjectType')
            ? (new QuestionnairePolicy)->delete($authUser, $projectType)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProjectType');
    }

    public function restore(AuthUser $authUser, ProjectType $projectType): bool
    {
        return $authUser->can('Restore:ProjectType');
    }

    public function forceDelete(AuthUser $authUser, ProjectType $projectType): bool
    {
        return $authUser->can('ForceDelete:ProjectType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectType');
    }

    public function replicate(AuthUser $authUser, ProjectType $projectType): bool
    {
        return $authUser->can('Replicate:ProjectType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectType');
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProjectCategory;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProjectCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectCategory');
    }

    public function view(AuthUser $authUser, ProjectCategory $projectCategory): bool
    {
        return $authUser->can('View:ProjectCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectCategory');
    }

    public function update(AuthUser $authUser, ProjectCategory $projectCategory): bool
    {
        return $authUser->can('Update:ProjectCategory');
    }

    public function delete(AuthUser $authUser, ProjectCategory $projectCategory): Response
    {
        return $authUser->can('Delete:ProjectCategory')
            ? (new QuestionnairePolicy)->delete($authUser, $projectCategory)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProjectCategory');
    }

    public function restore(AuthUser $authUser, ProjectCategory $projectCategory): bool
    {
        return $authUser->can('Restore:ProjectCategory');
    }

    public function forceDelete(AuthUser $authUser, ProjectCategory $projectCategory): bool
    {
        return $authUser->can('ForceDelete:ProjectCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectCategory');
    }

    public function replicate(AuthUser $authUser, ProjectCategory $projectCategory): bool
    {
        return $authUser->can('Replicate:ProjectCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectCategory');
    }
}

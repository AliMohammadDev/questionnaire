<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DesignStyle;
use App\Models\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DesignStylePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DesignStyle');
    }

    public function view(AuthUser $authUser, DesignStyle $designStyle): bool
    {
        return $authUser->can('View:DesignStyle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DesignStyle');
    }

    public function update(AuthUser $authUser, DesignStyle $designStyle): bool
    {
        return $authUser->can('Update:DesignStyle');
    }

    public function delete(AuthUser $authUser, DesignStyle $designStyle): Response
    {
        return $authUser->can('Delete:DesignStyle')
            ? (new QuestionnairePolicy)->delete($authUser, $designStyle)
            : Response::deny();
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DesignStyle');
    }

    public function restore(AuthUser $authUser, DesignStyle $designStyle): bool
    {
        return $authUser->can('Restore:DesignStyle');
    }

    public function forceDelete(AuthUser $authUser, DesignStyle $designStyle): bool
    {
        return $authUser->can('ForceDelete:DesignStyle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DesignStyle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DesignStyle');
    }

    public function replicate(AuthUser $authUser, DesignStyle $designStyle): bool
    {
        return $authUser->can('Replicate:DesignStyle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DesignStyle');
    }
}

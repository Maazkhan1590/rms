<?php

namespace App\Policies;

use App\Models\Grant;
use App\Models\User;

class GrantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('grant_read');
    }

    public function view(User $user, Grant $grant): bool
    {
        return $user->can('grant_read');
    }

    public function create(User $user): bool
    {
        return $user->can('grant_create');
    }

    public function update(User $user, Grant $grant): bool
    {
        return $user->can('grant_update');
    }

    public function delete(User $user, Grant $grant): bool
    {
        return $user->can('grant_delete');
    }

    public function approve(User $user, Grant $grant): bool
    {
        return $user->can('grant_approve');
    }
}

<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('publication_read');
    }

    public function view(User $user, Publication $publication): bool
    {
        return $user->can('publication_read');
    }

    public function create(User $user): bool
    {
        return $user->can('publication_create');
    }

    public function update(User $user, Publication $publication): bool
    {
        return $user->can('publication_update');
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->can('publication_delete');
    }

    public function approve(User $user, Publication $publication): bool
    {
        return $user->can('publication_approve');
    }
}

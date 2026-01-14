<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('report_read');
    }

    public function view(User $user, Report $report): bool
    {
        return $user->can('report_read');
    }

    public function create(User $user): bool
    {
        return $user->can('report_create');
    }

    public function update(User $user, Report $report): bool
    {
        return $user->can('report_update');
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->can('report_delete');
    }

    public function approve(User $user, Report $report): bool
    {
        return $user->can('report_approve');
    }
}

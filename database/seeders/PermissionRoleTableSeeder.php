<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = Permission::all()->keyBy('title')->map->id;

        $admin = Role::where('title', 'Admin')->first();
        if ($admin) {
            $admin->permissions()->sync($permissions->values());
        }

        $selfService = ['profile_password_edit'];

        $managementReadOnly = [
            'user_access',
            'user_show',
            'role_access',
            'role_show',
            'permission_access',
            'permission_show',
        ];

        $publicationCrud = [
            'publication_access',
            'publication_create',
            'publication_read',
            'publication_update',
            'publication_delete',
            'publication_approve',
        ];

        $grantCrud = [
            'grant_access',
            'grant_create',
            'grant_read',
            'grant_update',
            'grant_delete',
            'grant_approve',
        ];

        $rtnCrud = [
            'rtn_access',
            'rtn_create',
            'rtn_read',
            'rtn_update',
            'rtn_delete',
        ];

        $bonusCrud = [
            'bonus_access',
            'bonus_create',
            'bonus_read',
            'bonus_update',
            'bonus_delete',
        ];

        $consultancyCrud = [
            'consultancy_access',
            'consultancy_create',
            'consultancy_read',
            'consultancy_update',
            'consultancy_delete',
        ];

        $commercializationCrud = [
            'commercialization_access',
            'commercialization_create',
            'commercialization_read',
            'commercialization_update',
            'commercialization_delete',
        ];

        $partnershipCrud = [
            'partnership_access',
            'partnership_create',
            'partnership_read',
            'partnership_update',
            'partnership_delete',
        ];

        $conferenceCrud = [
            'conference_access',
            'conference_create',
            'conference_read',
            'conference_update',
            'conference_delete',
        ];

        $investmentCrud = [
            'investment_access',
            'investment_create',
            'investment_read',
            'investment_update',
            'investment_delete',
        ];

        $supervisionCrud = [
            'supervision_access',
            'supervision_create',
            'supervision_read',
            'supervision_update',
            'supervision_delete',
        ];

        $editorialCrud = [
            'editorial_access',
            'editorial_create',
            'editorial_read',
            'editorial_update',
            'editorial_delete',
        ];

        $studentCrud = [
            'student_access',
            'student_create',
            'student_read',
            'student_update',
            'student_delete',
        ];

        $internalFundingCrud = [
            'internal_funding_access',
            'internal_funding_create',
            'internal_funding_read',
            'internal_funding_update',
            'internal_funding_delete',
        ];

        $blockFundingCrud = [
            'block_funding_access',
            'block_funding_create',
            'block_funding_read',
            'block_funding_update',
            'block_funding_delete',
        ];

        $sdgCrud = [
            'sdg_access',
            'sdg_create',
            'sdg_read',
            'sdg_update',
            'sdg_delete',
        ];

        $workflowPermissions = [
            'workflow_access',
            'workflow_read',
            'workflow_approve',
            'workflow_reject',
        ];

        $policyPermissions = [
            'policy_access',
            'policy_read',
        ];

        $reportCrud = [
            'report_access',
            'report_create',
            'report_read',
            'report_update',
            'report_delete',
            'report_approve',
        ];

        $collegePermissions = [
            'college_access',
            'college_read',
        ];

        $auditPermissions = [
            'audit_access',
            'audit_read',
        ];

        // Dean gets full access to most modules
        $deanPermissions = array_merge(
            $managementReadOnly,
            $publicationCrud,
            $grantCrud,
            $rtnCrud,
            $bonusCrud,
            $consultancyCrud,
            $commercializationCrud,
            $partnershipCrud,
            $conferenceCrud,
            $investmentCrud,
            $supervisionCrud,
            $editorialCrud,
            $studentCrud,
            $internalFundingCrud,
            $blockFundingCrud,
            $sdgCrud,
            $workflowPermissions,
            $policyPermissions,
            $reportCrud,
            $collegePermissions,
            $auditPermissions,
            $selfService
        );

        // Coordinator gets read/write access to most modules
        $coordinatorPermissions = array_merge(
            $managementReadOnly,
            [
                'publication_access',
                'publication_create',
                'publication_read',
                'publication_update',
                'publication_delete',
                'grant_access',
                'grant_create',
                'grant_read',
                'grant_update',
                'rtn_access',
                'rtn_read',
                'rtn_update',
                'bonus_access',
                'bonus_read',
                'consultancy_access',
                'consultancy_read',
                'consultancy_update',
                'commercialization_access',
                'commercialization_read',
                'conference_access',
                'conference_read',
                'investment_access',
                'investment_read',
                'supervision_access',
                'supervision_read',
                'editorial_access',
                'editorial_read',
                'student_access',
                'student_read',
                'internal_funding_access',
                'internal_funding_read',
                'block_funding_access',
                'block_funding_read',
                'sdg_access',
                'sdg_read',
                'workflow_access',
                'workflow_read',
                'workflow_approve',
                'workflow_reject',
                'policy_access',
                'policy_read',
                'report_access',
                'report_create',
                'report_read',
                'report_update',
                'college_access',
                'college_read',
            ],
            $selfService
        );

        // Faculty gets create/read access to their own submissions
        $facultyPermissions = array_merge(
            [
                'publication_access',
                'publication_create',
                'publication_read',
                'publication_update',
                'grant_access',
                'grant_create',
                'grant_read',
                'rtn_access',
                'rtn_create',
                'rtn_read',
                'rtn_update',
                'bonus_access',
                'bonus_create',
                'bonus_read',
                'consultancy_access',
                'consultancy_create',
                'consultancy_read',
                'consultancy_update',
                'commercialization_access',
                'commercialization_create',
                'commercialization_read',
                'commercialization_update',
                'conference_access',
                'conference_create',
                'conference_read',
                'conference_update',
                'investment_access',
                'investment_read',
                'supervision_access',
                'supervision_read',
                'editorial_access',
                'editorial_read',
                'student_access',
                'student_read',
                'internal_funding_access',
                'internal_funding_read',
                'block_funding_access',
                'block_funding_read',
                'sdg_access',
                'sdg_read',
                'workflow_access',
                'workflow_read',
                'report_access',
                'report_read',
            ],
            $selfService
        );

        // Student gets limited access - can view and submit publications only
        $studentPermissions = [
            'publication_access',
            'publication_create',
            'publication_read',
        ];

        $this->syncRolePermissions('Dean', $permissions, $deanPermissions);
        $this->syncRolePermissions('Coordinator', $permissions, $coordinatorPermissions);
        $this->syncRolePermissions('Faculty', $permissions, $facultyPermissions);
        $this->syncRolePermissions('Student', $permissions, $studentPermissions);
    }

    protected function syncRolePermissions(string $roleTitle, $permissions, array $permissionTitles): void
    {
        $role = Role::where('title', $roleTitle)->first();

        if (! $role) {
            return;
        }

        $role->permissions()->sync($permissions->only($permissionTitles)->values());
    }
}

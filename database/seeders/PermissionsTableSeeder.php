<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'watch_create',
            ],
            [
                'id'    => 18,
                'title' => 'watch_edit',
            ],
            [
                'id'    => 19,
                'title' => 'watch_show',
            ],
            [
                'id'    => 20,
                'title' => 'watch_delete',
            ],
            [
                'id'    => 21,
                'title' => 'watch_access',
            ],
            [
                'id'    => 22,
                'title' => 'category_create',
            ],
            [
                'id'    => 23,
                'title' => 'category_edit',
            ],
            [
                'id'    => 24,
                'title' => 'category_show',
            ],
            [
                'id'    => 25,
                'title' => 'category_delete',
            ],
            [
                'id'    => 26,
                'title' => 'category_access',
            ],
            [
                'id'    => 27,
                'title' => 'profile_password_edit',
            ],
            [
                'id'    => 28,
                'title' => 'publication_create',
            ],
            [
                'id'    => 29,
                'title' => 'publication_read',
            ],
            [
                'id'    => 30,
                'title' => 'publication_update',
            ],
            [
                'id'    => 31,
                'title' => 'publication_delete',
            ],
            [
                'id'    => 32,
                'title' => 'publication_approve',
            ],
            [
                'id'    => 33,
                'title' => 'grant_create',
            ],
            [
                'id'    => 34,
                'title' => 'grant_read',
            ],
            [
                'id'    => 35,
                'title' => 'grant_update',
            ],
            [
                'id'    => 36,
                'title' => 'grant_delete',
            ],
            [
                'id'    => 37,
                'title' => 'grant_approve',
            ],
            [
                'id'    => 38,
                'title' => 'report_create',
            ],
            [
                'id'    => 39,
                'title' => 'report_read',
            ],
            [
                'id'    => 40,
                'title' => 'report_update',
            ],
            [
                'id'    => 41,
                'title' => 'report_delete',
            ],
            [
                'id'    => 42,
                'title' => 'report_approve',
            ],
            // Publication Access
            [
                'id'    => 43,
                'title' => 'publication_access',
            ],
            // Grant Access
            [
                'id'    => 44,
                'title' => 'grant_access',
            ],
            // RTN Submissions
            [
                'id'    => 45,
                'title' => 'rtn_access',
            ],
            [
                'id'    => 46,
                'title' => 'rtn_create',
            ],
            [
                'id'    => 47,
                'title' => 'rtn_read',
            ],
            [
                'id'    => 48,
                'title' => 'rtn_update',
            ],
            [
                'id'    => 49,
                'title' => 'rtn_delete',
            ],
            // Bonus Recognition
            [
                'id'    => 50,
                'title' => 'bonus_access',
            ],
            [
                'id'    => 51,
                'title' => 'bonus_create',
            ],
            [
                'id'    => 52,
                'title' => 'bonus_read',
            ],
            [
                'id'    => 53,
                'title' => 'bonus_update',
            ],
            [
                'id'    => 54,
                'title' => 'bonus_delete',
            ],
            // Consultancies & KT
            [
                'id'    => 55,
                'title' => 'consultancy_access',
            ],
            [
                'id'    => 56,
                'title' => 'consultancy_create',
            ],
            [
                'id'    => 57,
                'title' => 'consultancy_read',
            ],
            [
                'id'    => 58,
                'title' => 'consultancy_update',
            ],
            [
                'id'    => 59,
                'title' => 'consultancy_delete',
            ],
            // Commercializations
            [
                'id'    => 60,
                'title' => 'commercialization_access',
            ],
            [
                'id'    => 61,
                'title' => 'commercialization_create',
            ],
            [
                'id'    => 62,
                'title' => 'commercialization_read',
            ],
            [
                'id'    => 63,
                'title' => 'commercialization_update',
            ],
            [
                'id'    => 64,
                'title' => 'commercialization_delete',
            ],
            // Partnerships & MOUs
            [
                'id'    => 65,
                'title' => 'partnership_access',
            ],
            [
                'id'    => 66,
                'title' => 'partnership_create',
            ],
            [
                'id'    => 67,
                'title' => 'partnership_read',
            ],
            [
                'id'    => 68,
                'title' => 'partnership_update',
            ],
            [
                'id'    => 69,
                'title' => 'partnership_delete',
            ],
            // Conference Activities
            [
                'id'    => 70,
                'title' => 'conference_access',
            ],
            [
                'id'    => 71,
                'title' => 'conference_create',
            ],
            [
                'id'    => 72,
                'title' => 'conference_read',
            ],
            [
                'id'    => 73,
                'title' => 'conference_update',
            ],
            [
                'id'    => 74,
                'title' => 'conference_delete',
            ],
            // Research Investments
            [
                'id'    => 75,
                'title' => 'investment_access',
            ],
            [
                'id'    => 76,
                'title' => 'investment_create',
            ],
            [
                'id'    => 77,
                'title' => 'investment_read',
            ],
            [
                'id'    => 78,
                'title' => 'investment_update',
            ],
            [
                'id'    => 79,
                'title' => 'investment_delete',
            ],
            // Supervision & Exams
            [
                'id'    => 80,
                'title' => 'supervision_access',
            ],
            [
                'id'    => 81,
                'title' => 'supervision_create',
            ],
            [
                'id'    => 82,
                'title' => 'supervision_read',
            ],
            [
                'id'    => 83,
                'title' => 'supervision_update',
            ],
            [
                'id'    => 84,
                'title' => 'supervision_delete',
            ],
            // Editorial Appointments
            [
                'id'    => 85,
                'title' => 'editorial_access',
            ],
            [
                'id'    => 86,
                'title' => 'editorial_create',
            ],
            [
                'id'    => 87,
                'title' => 'editorial_read',
            ],
            [
                'id'    => 88,
                'title' => 'editorial_update',
            ],
            [
                'id'    => 89,
                'title' => 'editorial_delete',
            ],
            // Student Involvements
            [
                'id'    => 90,
                'title' => 'student_access',
            ],
            [
                'id'    => 91,
                'title' => 'student_create',
            ],
            [
                'id'    => 92,
                'title' => 'student_read',
            ],
            [
                'id'    => 93,
                'title' => 'student_update',
            ],
            [
                'id'    => 94,
                'title' => 'student_delete',
            ],
            // Internal Fundings
            [
                'id'    => 95,
                'title' => 'internal_funding_access',
            ],
            [
                'id'    => 96,
                'title' => 'internal_funding_create',
            ],
            [
                'id'    => 97,
                'title' => 'internal_funding_read',
            ],
            [
                'id'    => 98,
                'title' => 'internal_funding_update',
            ],
            [
                'id'    => 99,
                'title' => 'internal_funding_delete',
            ],
            // Block Fundings
            [
                'id'    => 100,
                'title' => 'block_funding_access',
            ],
            [
                'id'    => 101,
                'title' => 'block_funding_create',
            ],
            [
                'id'    => 102,
                'title' => 'block_funding_read',
            ],
            [
                'id'    => 103,
                'title' => 'block_funding_update',
            ],
            [
                'id'    => 104,
                'title' => 'block_funding_delete',
            ],
            // SDG Contributions
            [
                'id'    => 105,
                'title' => 'sdg_access',
            ],
            [
                'id'    => 106,
                'title' => 'sdg_create',
            ],
            [
                'id'    => 107,
                'title' => 'sdg_read',
            ],
            [
                'id'    => 108,
                'title' => 'sdg_update',
            ],
            [
                'id'    => 109,
                'title' => 'sdg_delete',
            ],
            // Workflows
            [
                'id'    => 110,
                'title' => 'workflow_access',
            ],
            [
                'id'    => 111,
                'title' => 'workflow_create',
            ],
            [
                'id'    => 112,
                'title' => 'workflow_read',
            ],
            [
                'id'    => 113,
                'title' => 'workflow_update',
            ],
            [
                'id'    => 114,
                'title' => 'workflow_approve',
            ],
            [
                'id'    => 115,
                'title' => 'workflow_reject',
            ],
            // Policies
            [
                'id'    => 116,
                'title' => 'policy_access',
            ],
            [
                'id'    => 117,
                'title' => 'policy_create',
            ],
            [
                'id'    => 118,
                'title' => 'policy_read',
            ],
            [
                'id'    => 119,
                'title' => 'policy_update',
            ],
            [
                'id'    => 120,
                'title' => 'policy_delete',
            ],
            // Reports
            [
                'id'    => 121,
                'title' => 'report_access',
            ],
            // Colleges & Departments
            [
                'id'    => 122,
                'title' => 'college_access',
            ],
            [
                'id'    => 123,
                'title' => 'college_create',
            ],
            [
                'id'    => 124,
                'title' => 'college_read',
            ],
            [
                'id'    => 125,
                'title' => 'college_update',
            ],
            [
                'id'    => 126,
                'title' => 'college_delete',
            ],
            // Audit Logs
            [
                'id'    => 127,
                'title' => 'audit_access',
            ],
            [
                'id'    => 128,
                'title' => 'audit_read',
            ],
            // My Research Access (for faculty dashboard)
            [
                'id'    => 129,
                'title' => 'research_access',
            ],
        ];

        // Use updateOrCreate to avoid duplicate key errors
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['id' => $permission['id']],
                ['title' => $permission['title']]
            );
        }
    }
}

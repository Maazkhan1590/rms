<?php

namespace Database\Seeders;

use App\Models\PolicyVersion;
use App\Models\ScoringPolicy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScoringPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        $adminUser = User::where('email', 'admin@admin.com')->first() ?? User::first();

        // Create or get the default policy version
        $policyVersion = PolicyVersion::firstOrCreate(
            [
                'version_number' => $currentYear . '.1',
            ],
            [
                'year' => $currentYear,
                'is_active' => true,
                'description' => 'Default scoring policy version with standard scoring values for all categories.',
                'created_by' => $adminUser?->id ?? 1,
            ]
        );

        // If this version is being created, deactivate other versions
        if ($policyVersion->wasRecentlyCreated) {
            PolicyVersion::where('id', '!=', $policyVersion->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $effectiveFrom = now()->startOfYear();
        $effectiveTo = null; // Ongoing

        // Define all policies
        $policies = [
            // Publications
            [
                'name' => 'Journal Paper (Indexed)',
                'type' => 'publication',
                'category' => 'scopus',
                'subcategory' => null, // Applies to all quartiles
                'points' => 60.0,
                'cap' => 120.0,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Paper (International Refereed)',
                'type' => 'publication',
                'category' => 'international_refereed',
                'subcategory' => null,
                'points' => 60.0,
                'cap' => 120.0,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Conference Paper',
                'type' => 'publication',
                'category' => null,
                'subcategory' => null,
                'points' => 15.0,
                'cap' => 15.0,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Book',
                'type' => 'publication',
                'category' => null,
                'subcategory' => null,
                'points' => 10.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Book Chapter',
                'type' => 'publication',
                'category' => null,
                'subcategory' => null,
                'points' => 10.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Non-indexed Journal',
                'type' => 'publication',
                'category' => 'non_indexed',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],

            // Grants
            [
                'name' => 'External Grant (Principal Investigator)',
                'type' => 'grant',
                'category' => 'external_grant',
                'subcategory' => 'PI',
                'points' => 90.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Matching Grant (Principal Investigator)',
                'type' => 'grant',
                'category' => 'matching_grant',
                'subcategory' => 'PI',
                'points' => 15.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'GRG/URG (Advisor)',
                'type' => 'grant',
                'category' => 'grg_urg',
                'subcategory' => 'Advisor',
                'points' => 10.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Patent (SU-registered)',
                'type' => 'grant',
                'category' => 'patent_copyright',
                'subcategory' => null,
                'points' => 10.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Grant Application',
                'type' => 'grant',
                'category' => 'grant_application',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Co-Principal Investigator',
                'type' => 'grant',
                'category' => null,
                'subcategory' => 'Co-PI',
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Co-Investigator',
                'type' => 'grant',
                'category' => null,
                'subcategory' => 'Co-I',
                'points' => 6.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],

            // RTN
            [
                'name' => 'RTN-3 (Student Co-author)',
                'type' => 'rtn',
                'category' => 'RTN_3',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'RTN-4 (Research in Teaching)',
                'type' => 'rtn',
                'category' => 'RTN_4',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],

            // Bonus Recognitions
            [
                'name' => 'Editorial Board',
                'type' => 'bonus',
                'category' => 'editorial_board',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'External Examiner',
                'type' => 'bonus',
                'category' => 'external_examiner',
                'subcategory' => null,
                'points' => 6.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Regulatory/Professional Body',
                'type' => 'bonus',
                'category' => 'regulatory_body',
                'subcategory' => null,
                'points' => 7.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Workshop/Seminar',
                'type' => 'bonus',
                'category' => 'workshop_seminar',
                'subcategory' => null,
                'points' => 8.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Keynote/Plenary',
                'type' => 'bonus',
                'category' => 'keynote_plenary',
                'subcategory' => null,
                'points' => 9.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Reviewer',
                'type' => 'bonus',
                'category' => 'journal_reviewer',
                'subcategory' => null,
                'points' => 5.0,
                'cap' => null,
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
        ];

        // Insert policies (using updateOrCreate to avoid duplicates)
        foreach ($policies as $policyData) {
            ScoringPolicy::updateOrCreate(
                [
                    'name' => $policyData['name'],
                    'type' => $policyData['type'],
                    'policy_version_id' => $policyVersion->id,
                ],
                $policyData
            );
        }

        $this->command->info('Scoring policy version and policies created successfully!');
        $this->command->info("Policy Version: {$policyVersion->version_number} ({$policyVersion->year})");
        $this->command->info('Total policies created: ' . count($policies));
    }
}

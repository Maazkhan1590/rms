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
            [
                'name' => 'External Grant (Co-Principal Investigator)',
                'type' => 'grant',
                'category' => 'external_grant',
                'subcategory' => 'Co-PI',
                'points' => 45.0,
                'cap' => 90.0, // Cap at 90 points total for external grants
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'External Grant (Co-Investigator)',
                'type' => 'grant',
                'category' => 'external_grant',
                'subcategory' => 'Co-I',
                'points' => 30.0,
                'cap' => 60.0, // Cap at 60 points total for external grants as Co-I
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Matching Grant (Co-Principal Investigator)',
                'type' => 'grant',
                'category' => 'matching_grant',
                'subcategory' => 'Co-PI',
                'points' => 7.5,
                'cap' => 15.0, // Cap at 15 points total for matching grants
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Matching Grant (Co-Investigator)',
                'type' => 'grant',
                'category' => 'matching_grant',
                'subcategory' => 'Co-I',
                'points' => 5.0,
                'cap' => 10.0, // Cap at 10 points total for matching grants as Co-I
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'GRG/URG (Principal Investigator)',
                'type' => 'grant',
                'category' => 'grg_urg',
                'subcategory' => 'PI',
                'points' => 15.0,
                'cap' => 30.0, // Cap at 30 points total for GRG/URG as PI
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'GRG/URG (Co-Principal Investigator)',
                'type' => 'grant',
                'category' => 'grg_urg',
                'subcategory' => 'Co-PI',
                'points' => 7.5,
                'cap' => 15.0, // Cap at 15 points total for GRG/URG as Co-PI
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Paper (Q1 Quartile)',
                'type' => 'publication',
                'category' => 'scopus',
                'subcategory' => 'Q1',
                'points' => 60.0,
                'cap' => 120.0, // Cap at 120 points for Q1 journals
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Paper (Q2 Quartile)',
                'type' => 'publication',
                'category' => 'scopus',
                'subcategory' => 'Q2',
                'points' => 50.0,
                'cap' => 100.0, // Cap at 100 points for Q2 journals
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Paper (Q3 Quartile)',
                'type' => 'publication',
                'category' => 'scopus',
                'subcategory' => 'Q3',
                'points' => 40.0,
                'cap' => 80.0, // Cap at 80 points for Q3 journals
                'is_active' => true,
                'effective_from' => $effectiveFrom,
                'effective_to' => $effectiveTo,
                'version' => '1.0',
                'policy_version_id' => $policyVersion->id,
                'created_by' => $adminUser?->id ?? 1,
            ],
            [
                'name' => 'Journal Paper (Q4 Quartile)',
                'type' => 'publication',
                'category' => 'scopus',
                'subcategory' => 'Q4',
                'points' => 30.0,
                'cap' => 60.0, // Cap at 60 points for Q4 journals
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

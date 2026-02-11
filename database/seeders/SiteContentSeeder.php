<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            // Footer Section
            [
                'key' => 'footer_description',
                'type' => 'text',
                'value' => 'A premier platform for academic research submission, peer review, and open-access publication. Advancing knowledge through collaboration and innovation.',
                'label' => 'Footer Description',
                'section' => 'footer',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'footer_address',
                'type' => 'text',
                'value' => "123 Research Avenue\nAcademic City, Country",
                'label' => 'Footer Address',
                'section' => 'footer',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'footer_phone',
                'type' => 'phone',
                'value' => '+1 (555) 123-4567',
                'label' => 'Footer Phone',
                'section' => 'footer',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'footer_email',
                'type' => 'email',
                'value' => 'info@researchportal.edu',
                'label' => 'Footer Email',
                'section' => 'footer',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'footer_hours',
                'type' => 'text',
                'value' => 'Mon-Fri: 9:00 AM - 5:00 PM',
                'label' => 'Footer Hours',
                'section' => 'footer',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'footer_copyright',
                'type' => 'text',
                'value' => 'Academic Research Portal. All rights reserved.',
                'label' => 'Footer Copyright',
                'section' => 'footer',
                'order' => 6,
                'is_active' => true,
            ],
            // Social Media Links
            [
                'key' => 'social_twitter',
                'type' => 'url',
                'value' => 'https://twitter.com/researchportal',
                'label' => 'Twitter URL',
                'section' => 'footer',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'key' => 'social_linkedin',
                'type' => 'url',
                'value' => 'https://linkedin.com/company/researchportal',
                'label' => 'LinkedIn URL',
                'section' => 'footer',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'key' => 'social_youtube',
                'type' => 'url',
                'value' => 'https://youtube.com/@researchportal',
                'label' => 'YouTube URL',
                'section' => 'footer',
                'order' => 12,
                'is_active' => true,
            ],
            [
                'key' => 'social_github',
                'type' => 'url',
                'value' => 'https://github.com/researchportal',
                'label' => 'GitHub URL',
                'section' => 'footer',
                'order' => 13,
                'is_active' => true,
            ],
            [
                'key' => 'social_orcid',
                'type' => 'url',
                'value' => 'https://orcid.org/0000-0000-0000-0000',
                'label' => 'ORCID URL',
                'section' => 'footer',
                'order' => 14,
                'is_active' => true,
            ],
            // Legal Links
            [
                'key' => 'footer_privacy_link',
                'type' => 'url',
                'value' => '/privacy-policy',
                'label' => 'Privacy Policy Link',
                'section' => 'footer',
                'order' => 20,
                'is_active' => true,
            ],
            [
                'key' => 'footer_terms_link',
                'type' => 'url',
                'value' => '/terms-of-service',
                'label' => 'Terms of Service Link',
                'section' => 'footer',
                'order' => 21,
                'is_active' => true,
            ],
            [
                'key' => 'footer_cookie_link',
                'type' => 'url',
                'value' => '/cookie-policy',
                'label' => 'Cookie Policy Link',
                'section' => 'footer',
                'order' => 22,
                'is_active' => true,
            ],
            // General/Header Content (if needed)
            [
                'key' => 'site_name',
                'type' => 'text',
                'value' => 'Academic Research Portal',
                'label' => 'Site Name',
                'section' => 'general',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'site_tagline',
                'type' => 'text',
                'value' => 'Advancing Knowledge Through Innovation',
                'label' => 'Site Tagline',
                'section' => 'general',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }

        $this->command->info('Site content seeded successfully!');
    }
}

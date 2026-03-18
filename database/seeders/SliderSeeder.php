<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Advancing Knowledge Through <span class="highlight">Innovative</span> Research',
                'description' => 'Submit, discover, and collaborate on cutting-edge academic research across all disciplines with our global community of scholars.',
                'tag' => 'New Research',
                'image_url' => 'assets/images/sliders/slide-1.jpg',
                'button_text' => '<i class="fas fa-rocket"></i> Start Your Research',
                'button_link' => '/register',
                'button_text_secondary' => '<i class="fas fa-book-reader"></i> Explore Publications',
                'button_link_secondary' => '/publications',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Global Knowledge <span class="highlight">Without Barriers</span>',
                'description' => 'Discover thousands of peer-reviewed papers available to researchers worldwide. Access cutting-edge research across all academic fields.',
                'tag' => 'Open Access',
                'image_url' => 'assets/images/sliders/slide-2.jpg',
                'button_text' => '<i class="fas fa-search"></i> Browse Publications',
                'button_link' => '/publications',
                'button_text_secondary' => '<i class="fas fa-download"></i> Download Resources',
                'button_link_secondary' => '#',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Join a Network of <span class="highlight">Leading Experts</span>',
                'description' => 'Connect with researchers from top institutions around the world. Collaborate on groundbreaking projects and share knowledge across disciplines.',
                'tag' => 'Collaborate',
                'image_url' => 'assets/images/sliders/slide-3.jpg',
                'button_text' => '<i class="fas fa-users"></i> Join Our Community',
                'button_link' => '/register',
                'button_text_secondary' => '<i class="fas fa-calendar-alt"></i> View Events',
                'button_link_secondary' => '#',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::updateOrCreate(
                ['order' => $slider['order']],
                $slider
            );
        }

        $this->command->info('Sliders seeded successfully!');
    }
}

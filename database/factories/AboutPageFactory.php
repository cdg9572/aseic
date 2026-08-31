<?php

namespace Database\Factories;

use App\Models\AboutPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AboutPage>
 */
class AboutPageFactory extends Factory
{
    protected $model = AboutPage::class;

    public function definition(): array
    {
        return [
            'type' => AboutPage::TYPE_FORUM,
            'page_title' => fake()->sentence(4),
            'folder_name' => fake()->unique()->words(2, true),
            'subtitle' => fake()->sentence(),
            'is_main_page_visible' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\HomepagePartner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomepagePartner>
 */
class HomepagePartnerFactory extends Factory
{
    protected $model = HomepagePartner::class;

    public function definition(): array
    {
        return [
            'type' => HomepagePartner::TYPE_ORGANIZED,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'position' => fake()->jobTitle(),
            'affiliation' => fake()->company(),
            'linkedin_url' => fake()->url(),
            'is_active' => true,
            'is_image_visible' => false,
            'content' => fake()->paragraph(),
        ];
    }
}

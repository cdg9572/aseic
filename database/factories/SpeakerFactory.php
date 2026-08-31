<?php

namespace Database\Factories;

use App\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Speaker>
 */
class SpeakerFactory extends Factory
{
    protected $model = Speaker::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'position' => fake()->jobTitle(),
            'affiliation' => fake()->company(),
            'presentation_subject' => fake()->sentence(),
            'role' => Speaker::ROLE_SPEAKER,
            'is_active' => true,
            'is_image_visible' => false,
            'content' => fake()->paragraph(),
        ];
    }
}

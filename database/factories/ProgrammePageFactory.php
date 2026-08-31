<?php

namespace Database\Factories;

use App\Models\ProgrammePage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammePageFactory extends Factory
{
    protected $model = ProgrammePage::class;

    public function definition(): array
    {
        return [
            'type' => ProgrammePage::TYPE_THEME,
            'page_title' => $this->faker->sentence(4),
            'subtitle' => '<p>'.$this->faker->sentence().'</p>',
            'title' => '<p>'.$this->faker->sentence().'</p>',
            'location' => $this->faker->city(),
            'event_date' => 'September 1-2, 2026',
            'content' => '<p>'.$this->faker->paragraph().'</p>',
        ];
    }
}

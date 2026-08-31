<?php

namespace Database\Factories;

use App\Models\MainPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MainPage>
 */
class MainPageFactory extends Factory
{
    protected $model = MainPage::class;

    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2026, 2099);

        return [
            'is_visible' => false,
            'folder_name' => $year.'-forum',
            'event_name' => $year.' Forum',
            'event_start_date' => $year.'-01-01',
            'event_end_date' => $year.'-01-02',
            'use_custom_event_date' => false,
            'programme_items' => array_fill(0, 4, [
                'time' => null,
                'subject' => null,
                'content' => null,
            ]),
        ];
    }
}

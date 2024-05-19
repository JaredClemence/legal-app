<?php

namespace Database\Factories\KCBA;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KCBA\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title=$this->faker->sentence;
        $presenter=$this->faker->name;
        $startDate= $this->faker->dateTimeBetween('now','+30 years');
        return [
            'title'=>$title,
            'presenter_desc'=>$presenter,
            'start'=>$startDate,
            'duration'=>60,
        ];
    }
}

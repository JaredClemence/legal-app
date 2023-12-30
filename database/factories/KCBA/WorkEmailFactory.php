<?php

namespace Database\Factories\KCBA;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KCBA\WorkEmail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KCBA\WorkEmail>
 */
class WorkEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = WorkEmail::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email'    => fake()->email(),
            'firm_name'=> fake()->company()
        ];
    }
}

<?php

namespace Database\Factories\KCBA;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KCBA\Firm;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FirmFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Firm::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firm_name'=>fake()->company
        ];
    }
}

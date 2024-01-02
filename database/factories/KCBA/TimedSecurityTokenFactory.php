<?php

namespace Database\Factories\KCBA;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\KCBA\TimedSecurityToken;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimedSecurityToken>
 */
class TimedSecurityTokenFactory extends Factory
{
    protected $model = TimedSecurityToken::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
                'hash' => md5( Carbon::now() ),
                'minutes_to_expire' => fake()->numberBetween(60, 7*24*60)
        ];
    }
    
    public function three_days_to_expire() : Factory
    {
        return $this->state( function( array $attributes ){
            $minutes_to_expire = 3 /* days */ * 24 /* hours per day */ * 60 /* minutes per hour */;
            return compact('minutes_to_expire');
        });
    }
}

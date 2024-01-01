<?php

namespace Database\Factories\KCBA;

use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\KCBA\WorkEmailFactory;
use App\Models\KCBA\WorkEmail;
use Database\Factories\UserFactory;
use App\Models\KCBA\Member;
use App\Models\User;
use App\Models\KCBA\Firm;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KCBA\Member>
 */
class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Member::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $barnum = '';
        for($i=0;$i<7;$i++){
            $barnum .= fake()->randomDigit();
        }
        $status = ['PENDING','SUSPENDED','EXPIRED', 
            'ACTIVE', 'ACTIVE', 'ACTIVE', 'ACTIVE', 'ACTIVE','ACTIVE',
            'ACTIVE'];
        return [
            'user_id' => User::factory(),
            'firm_id' => Firm::factory(),
            'work_email'=> fake()->companyEmail,
            'barnum'  => random_int(0, 1)==0?null:$barnum,
            'status'  => fake()->randomElement($status)
        ];
    }
    
    public function suspended(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'SUSPENDED',
            ];
        });
    }
    
    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ACTIVE',
            ];
        });
    }
    
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'EXPIRED',
            ];
        });
    }
    
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'PENDING',
            ];
        });
    }
}

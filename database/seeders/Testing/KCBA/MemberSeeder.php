<?php

namespace Database\Seeders\Testing\KCBA;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KCBA\Member;
use App\Models\KCBA\Firm;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factories = Firm::factory()->count(10)->create();
        $members = Member::factory()
                ->recycle($factories)
                ->count(30)->create(/*['firm_id'=>$factories->random()->id]*/);
    }
}

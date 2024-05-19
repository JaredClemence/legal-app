<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KCBA\Member;
use App\Models\User;
use App\Models\KCBA\Firm;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try{
            Member::factory()->create(
                    [
                        'id'=>User::where("email","=","jaredclemence@gmail.com")->get()->first()?->id,
                        'work_email'=>"jclemence@ch-law.com",
                        'firm_id'=>Firm::where("firm_name","=","Coleman & Horowitt")->get()->first()?->id,
                        "barnum"=>"343496",
                        "role"=>"ADMIN"
                    ]);
            Member::factory()->create(
                    [
                        'id'=>User::where("email","=","jared.clemence@gmail.com")->get()->first()?->id,
                        'work_email'=>"jclemence@ch-law.com",
                        'firm_id'=>Firm::where("firm_name","=","Coleman & Horowitt")->get()->first()?->id,
                        "barnum"=>"343496",
                        "role"=>"USER"
                    ]);
        }catch(\Exception $e){
            
        }
    }
}

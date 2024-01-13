<?php

namespace Database\Seeders\Testing\KCBA;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KCBA\Member;
use App\Models\KCBA\Firm;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Firm::count() < 10){
            $this->seedFirms();
        }
        $firms = Firm::all();
        
        $users = [];
        if(\App\Models\User::count() < 50){
            $this->seedUsers();
        }
        $users = \App\Models\User::all();
        
        if( Member::count() < 50 ){
            $members = [];
            $users->each(function($user) use ($firms, &$members) {
                $userid = $user->id;
                $firmid = $firms->random()->id;
                $barnum = "";
                while(strlen($barnum) < 6){
                    $barnum .= floor(rand() % 10);
                }
                $members[] = [
                    'work_email'=>$user->email,
                    'user_id' => $userid,
                    'firm_id' => $firmid,
                    'barnum'  => $barnum,
                    'status'  => "ACTIVE"
                ];
            });
            DB::table('members')->insertOrIgnore($members);
        }
        
        assert(Member::count()>=50);
    }

    private function seedFirms() {
        $firm_names = [];
        for($i=0; $i<10; $i++){
            $firm_names[] = ["firm_name"=>fake()->company];
        }
        DB::table('firms')->insertOrIgnore($firm_names);
    }

    private function seedUsers() {
        for( $i=0; $i<50; $i++){
            $users[] = [
                'name'=>fake()->name,
                'email'=>fake()->email,
                'password'=> \Illuminate\Support\Facades\Hash::make("password")
            ];
        }
        DB::table('users')->insertOrIgnore($users);
    }

}

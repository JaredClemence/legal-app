<?php

namespace Database\Seeders\Testing\KCBA;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KCBA\Member;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Member::factory()->count(100)->create();
        $collection = Member::select(['id','email_id'])->get();
        $firmMembers = $collection->random(90);
        $firmMembers = $firmMembers->shuffle();
        $firmMembers = $firmMembers->shuffle();
        while( $firmMembers->count() > 0 ){
            $group = $firmMembers->pop( rand(5,20) )->sort();
            dd($group);
            $newFirm = fake()->company;
            $email_ids = $group->map(function($member){
                return $member->email_id;
            });
            dd($email_ids);
            $idList = $email_ids->join(", ");
            DB::table('work_emails')->whereRaw("id IN ($idList)" )
                    ->update(['firm_name'=>$newFirm]);
        }
    }
}

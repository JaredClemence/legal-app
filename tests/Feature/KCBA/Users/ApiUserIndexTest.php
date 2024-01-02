<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\Testing\KCBA\MemberSeeder;
use App\Models\KCBA\Member;
use Illuminate\Support\Facades\DB;
use App\Models\KCBA\WorkEmail;

class ApiUserIndexTest extends TestCase
{
    /**
     * @group GateTest
     */
    public function test_route_returns_200():void {
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)->get('/kcba/users');
        $response->assertSuccessful();
        $response->assertStatus(200);
    }
    
    public function test_shows_all_users_for_admin():void{}
    
    public function test_shows_firm_users_for_general_users():void{
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)->get('/kcba/users');
        //$response->assertSee("SUCCESS");
        $firm_id = $member->firm_id;
        $expectations = Member::with(['user'])->where('firm_id','=',$firm_id)->get();
        
        $expectations->each( function($expectedDisplay) use ($response) {
            $response->assertSee($expectedDisplay->user->name);
        } );
    }
    
    public function test_does_not_show_other_firm_users_for_general_users():void{
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)->get('/kcba/users');
        $firm_id = $member->firm_id;
        
        $firm_id = $member->firm_id;
        $expectations = Member::with(['user'])->where('firm_id','<>',$firm_id)->get();
        
        $expectations->each( function($expectedNotDisplayed) use ($response){
            $response->assertDontSee($expectedNotDisplayed->user->name);
        } );
    }
    
    /**
     * 
     * @group GateTest
     */
    public function test_index_fails_without_authentication():void{
        $response = $this->get('/kcba/users');
        $response->assertRedirect();
    }
    
    protected function get_random_active_member():Member {
        $this->seed(MemberSeeder::class);
        $member = Member::where('status','=','ACTIVE')->with(['user'])->inRandomOrder()->first();
        return $member;
    }
}

<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\Testing\KCBA\MemberSeeder;
use App\Models\KCBA\Member;
use Illuminate\Support\Facades\DB;

class ApiUserCreateTest extends TestCase
{
    /**
     * 
     * @group UserCreate
     */
    public function test_route_returns_200():void {
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7)        
        ];
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)
                ->post('/kcba/users', $postedData);
        $response->assertSuccessful();
    }
    
    public function test_call_creates_database_entry():void {
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7)        
        ];
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)
                ->post('/kcba/users', $postedData);
        $members = Member::with(['user','firm'])->where('work_email','=',$postedData['work_email'])->get();
        $this->assertEquals( 1, $members->count(), "One and only one member has the new email address." );
        $member = $members->pop();
        $this->assertEquals( $postedData['name'], $member->user->name, "Database contained new member name.");
        $this->assertEquals( $postedData['email'], $member->user->email, "Database contained new member personal email.");
        $this->assertEquals( $postedData['work_email'], $member->work_email, "Database contained new work email.");
        $this->assertEquals( $postedData['firm_name'], $member->firm->firm_name, "Database contained new firm name.");
        $this->assertEquals( $postedData['barnum'], $member->barnum, "Database contained new member bar number.");
    }
    
    /**
     * @group UserCreate
     */
    public function test_new_members_have_pending_status():void {
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7)        
        ];
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this->actingAs($user)
                ->post('/kcba/users', $postedData);
        $members = Member::with(['user','firm'])->where('work_email','=',$postedData['work_email'])->get();
        $this->assertEquals( 1, $members->count(), "One and only one member has the new email address." );
        $member = $members->pop();
        $this->assertEquals("PENDING", $member->status, "New members have PENDING status." );
    }
    
    /**
     * 
     * @group UserCreate
     */
    public function test_route_fails_without_security_token():void {
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7)        
        ];
        $member = $this->get_random_active_member();
        $user = $member->user;
        $response = $this
                ->post('/kcba/users', $postedData);
        $response->assertRedirect();
    }
    
    /**
     * 
     * @group UserCreate
     */
    public function test_route_fails_with_expired_security_token():void {
        $this->markTestIncomplete("Not designed.");
        
    }
    
    /**
     * 
     * @group UserCreate
     */
    public function test_new_user_created_on_post():void {
        $this->markTestIncomplete("Not designed.");
        
    }
    
    /**
     * 
     * @group UserCreate
     */
    public function test_new_user_failed_without_security_token():void {
        $this->markTestIncomplete("Not designed.");
        
    }
    
    /**
     * 
     * @group UserCreate
     */
    public function test_new_user_field_battery_demonstrates_flexible_use_of_optional_and_required_fields():void {
        $this->markTestIncomplete("Not designed.");
        
    }
    
    
    
    protected function get_random_active_member():Member {
        $this->seed(MemberSeeder::class);
        $member = Member::where('status','=','ACTIVE')->with(['user'])->inRandomOrder()->first();
        return $member;
    }
}

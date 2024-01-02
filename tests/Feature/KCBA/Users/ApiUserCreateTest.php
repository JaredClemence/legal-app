<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\Testing\KCBA\MemberSeeder;
use App\Models\KCBA\Member;
use Illuminate\Support\Facades\DB;
use App\Models\KCBA\TimedSecurityToken;
use Illuminate\Support\Carbon;

class ApiUserCreateTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
        
        $iterationCount = 0;
        while( DB::table('members')->count() < 100 && $iterationCount++ < 5 ){
            try{
                $this->seed(MemberSeeder::class);
            }catch(Exception $e ){
                //do nothing...try again
            }
        }
        if( $iterationCount == 5 ){
            throw new Exception("Unable to seed database with MemberSeeder.");
        }
    }
    
    public function tearDown(): void {
        TimedSecurityToken::all()->each( function($token){
            $token->delete();
        } );
        parent::tearDown();
    }
    /**
     * 
     * @group UserCreate
     * @group TokenImmediate
     */
    public function test_route_returns_200_with_token():void {
        $token = TimedSecurityToken::factory()->create();
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7),
            'token'=>$token->hash,
        ];
        $response = $this->post("/kcba/users", $postedData);
        $token->delete();
        $response->assertSuccessful();
    }
    /**
     * 
     * @group UserCreate
     */
    public function test_route_returns_200_for_admin():void {
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7),  
        ];
        $member = $this->get_random_active_member();
        $member->role = "ADMIN";
        $member->save();
        $member->refresh();
        $user = $member->user;
        $response = $this->actingAs($user)
                ->post("/kcba/users", $postedData);
        $response->assertSuccessful();
    }
    
    public function test_call_creates_database_entry():void {
        $token = TimedSecurityToken::factory()->create();
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7),
            'token'=>$token->hash,
        ];
        $response = $this->post('/kcba/users', $postedData);
        $token->delete();
        
        $members = Member::with(['user','firm'])->where('work_email','=',$postedData['work_email'])->get();
        $this->assertEquals( 1, $members?->count(), "One and only one member has the new email address." );
        $member = $members->pop();
        $this->assertEquals( $postedData['name'], $member?->user?->name, "Database contained new member name.");
        $this->assertEquals( $postedData['email'], $member?->user?->email, "Database contained new member personal email.");
        $this->assertEquals( $postedData['work_email'], $member?->work_email, "Database contained new work email.");
        $this->assertEquals( $postedData['firm_name'], $member?->firm?->firm_name, "Database contained new firm name.");
        $this->assertEquals( $postedData['barnum'], $member?->barnum, "Database contained new member bar number.");
    }
    
    /**
     * @group UserCreate
     */
    public function test_new_members_have_pending_status():void {
        $token = TimedSecurityToken::factory()->create();
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7),
            'token'=>$token->hash,
        ];
        $response = $this->post('/kcba/users', $postedData);
        $members = Member::with(['user','firm'])->where('work_email','=',$postedData['work_email'])->get();
        $this->assertEquals( 1, $members?->count(), "One and only one member has the new email address." );
        $member = $members->pop();
        $this->assertEquals("PENDING", $member?->status, "New members have PENDING status." );
        $token->delete();
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
            'barnum' => fake()->text(7),
        ];
        $response = $this->post('/kcba/users', $postedData);
        $response->assertStatus(401);
    }
    
    /**
     * 
     * @group UserCreate
     * @group TimeTravel
     */
    public function test_route_fails_with_expired_security_token():void {
        $token = TimedSecurityToken::factory()->three_days_to_expire()->create();
        $postedData = [
            'name' => fake()->name,
            'email' => fake()->email,
            'work_email' => fake()->companyEmail,
            'firm_name' => fake()->company,
            'barnum' => fake()->text(7),
            'token'=>$token->hash,
        ];
        $this->travel(4)->days(function() use ($postedData) {
            $response = $this->post('/kcba/users', $postedData);
        $response->assertStatus(403);
        });
        $token->delete();
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

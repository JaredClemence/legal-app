<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Database\Seeders\Testing\KCBA\MemberSeeder;
use App\Models\KCBA\Member;
use App\Models\KCBA\TimedSecurityToken;

class ApiUserEditAdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
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
    
    public function test_route_returns_200_for_admin():void {
        $token = TimedSecurityToken::factory()->create();
        $postedData = $this->makeUniquePostedData($token);
        $response = $this->post("/kcba/users", $postedData);
        $token->delete();
        $response->assertSuccessful();
    }
    
    public function test_admin_can_change_user_id():void {}
    public function test_admin_can_change_status():void {}
    public function test_admin_can_change_work_email():void {}
    public function test_admin_can_change_role():void {}
    public function test_admin_can_change_email():void{}
    public function test_admin_can_change_firm_name():void{}
    public function test_admin_can_change_name():void{}
    public function test_admin_can_change_barnum():void {}
    public function test_admin_can_change_password_password():void{}
    
    public function test_admin_can_set_firm_name():void {}
    public function test_admin_can_set_barnum():void {}
    public function test_admin_can_set_name():void {}
    public function test_admin_can_set_password():void {}
    
    public function test_admin_cannot_change_email_to_shared_email():void {
        
    }
    public function test_attempt_to_change_shared_email_by_admin_does_not_trigger_event():void {
        //concept -> event will likely cause a logged entry and will 
        //cause a special password reset email to be sent to the owning user
        //reminding that user of their correct account. (Solution assumes that 
        //email holder does not realize they are using a different email to manage
        //their account.
        
        
    }
    
    private function makeNonAdminUser() {
        $member = $this->get_random_active_member();
        $member->role = "USER";
        $member->save();
        $member->refresh();
        $user = $member->user;
        return $user;
    }
    
    protected function get_random_active_member():Member {
        $this->seed(MemberSeeder::class);
        $member = Member::where('status','=','ACTIVE')->with(['user'])->inRandomOrder()->first();
        return $member;
    }
    
    private function makeUniquePostedData($token=null) {
        $iteration = 0;
        do{
            $postedData = [
                'name' => fake()->name,
                'email' => fake()->email,
                'work_email' => fake()->companyEmail,
                'firm_name' => fake()->company,
                'barnum' => fake()->text(7),
            ];
        }while( (
                User::where('email','=',$postedData['email'])->get()->first() === null ||
                Member::where('work_email','=', $postedData['email'])->get()->first() === null ||
                Member::where('work_email','=', $postedData['work_email'])->get()->first() === null
                ) && 
                $iteration++ < 5 );
        if($token!==null){
            $postedData['token']=$token->hash;
        }
        return $postedData;
    }
}

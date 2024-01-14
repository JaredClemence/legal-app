<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\KCBA\TimedSecurityToken;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Database\Seeders\Testing\KCBA\MemberSeeder;
use App\Models\KCBA\Member;
use Tests\Feature\KCBA\Users;
use Illuminate\Http\Response;

class ApiUserEditUserTest extends TestCase
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
    
    /**
     * 
     * @return void
     * @group UserEdits
     */
    public function test_route_returns_200_for_user():void {
        //$token = TimedSecurityToken::factory()->create();
        $token = null;
        $member = $this->makeNonAdminMember();
        $user = $member->user;
                
        $postedData = $this->makeCleanPostedData($member, 'name', $token);
        
        $url = route('kcba.member.edit',compact('member'));
        $response = $this->actingAs($user)
                ->post($url, $postedData);
        if($token) $token->delete();
        $response->assertSuccessful();
    }
    
    /**
     * 
     * @param type $expectedChangeAbility
     * @param type $fieldName
     * @return void
     * @dataProvider fieldTestData
     * @group UserEdits
     */
    public function test_user_field_change_situations($expectedChangeAbility, $fieldName):void {
        //$token = TimedSecurityToken::factory()->create();
        $token = null;
        $member = $this->makeNonAdminMember();
        $user = $member->user;
                
        $changedDataField = $fieldName;
        $originalValue = $this->getCurrentValue($member, $fieldName);
        $postedData = $this->makeCleanPostedData($member, $changedDataField, $token);
        
        $response = $this->actingAs($user)
                ->post(route('kcba.member.edit',compact('member')), $postedData);
        if($token) $token->delete();
        $response->assertSuccessful();
        
        $changedValue = $this->getCurrentValue($member, $fieldName);
        
        if($expectedChangeAbility == "can"){
            $this->assertNotEquals($originalValue, $changedValue, "User was not able to change the value for $fieldName.");
        }else{
            $this->assertEquals($originalValue, $changedValue, "User was able to change the value for $fieldName.");
        }
    }
    
    static public function fieldTestData(){
        return [
            ['can','barnum'],
            ['can','name'],
            ['can', 'password']
        ];
    }
    
    public function test_user_cannot_change_user_id():void {
        //$token = TimedSecurityToken::factory()->create();
        $token = null;
        $user = $this->makeNonAdminMember();
        
        $changedDataField = $fieldName;
        $originalValue = $this->getCurrentValue($user, $fieldName);
        $postedData = $this->makeCleanPostedData($user, $changedDataField, $token);
        
        $response = $this->actingAs($user)
                ->post("/kcba/users", $postedData);
        if($token) $token->delete();
        $response->assertSuccessful();
        
        $changedValue = $this->getCurrentValue($user, $fieldName);
        
        if($result == "can"){
            $this->assertNotEquals($originalValue, $changedValue, "User was not able to change the value for $fieldName.");
        }else{
            $this->assertEquals($originalValue, $changedValue, "User was able to change the value for $fieldName.");
        }
    }
    public function test_user_cannot_change_status():void {}
    public function test_user_cannot_change_work_email():void {}
    public function test_user_cannot_change_role():void {}
    public function test_user_cannot_change_email():void{}
    public function test_user_cannot_change_firm_name():void{}
    public function test_user_can_change_name():void{}
    public function test_user_can_change_barnum():void {}
    public function test_user_can_change_password_password():void{}
    
    public function test_user_cannot_set_email_verified_at():void{}
    public function test_user_can_set_firm_name():void {}
    public function test_user_can_set_barnum():void {}
    public function test_user_can_set_name():void {}
    public function test_user_can_set_password():void {}
    
    public function test_user_cannot_change_email_to_shared_email():void {
        
    }
    public function test_attempt_to_change_shared_email_by_user_triggers_event():void {
        //concept -> event will likely cause a logged entry and will 
        //cause a special password reset email to be sent to the owning user
        //reminding that user of their correct account. (Solution assumes that 
        //email holder does not realize they are using a different email to manage
        //their account.
        
        
    }
    
    private function makeCleanPostedData(Member $member, string $field, $token=null) {
        $dataArray = $member->getFormData();
        switch($field){
            case 'name':
                $newValue = fake()->name;
                break;
            case 'email':
            case 'work_email':
                $newValue = $this->selectCleanEmail($dataArray['id'], $dataArray['user_id']);
                break;
            case 'firm_name':
                $newValue = fake()->company;
                break;
            case 'password':
                $newValue = fake()->password;
                break;
            case 'barnum':
                $newValue = "";
                while(strlen($newValue)<6){
                    $newValue .= rand()%10;
                }
                break;
        }
        $dataArray[$field] = $newValue;
        if($token!==null){
            $dataArray['token']=$token->hash;
        }
        return $dataArray;
    }
    
    private function makeNonAdminMember() {
        $member = $this->get_random_active_member();
        $member->role = "USER";
        $member->save();
        $member->refresh();
        return $member;
    }
    
    protected function get_random_active_member():Member {
        $this->seed(MemberSeeder::class);
        $member = Member::where('status','=','ACTIVE')->with(['user','firm'])->inRandomOrder()->first();
        return $member;
    }

    public function selectCleanEmail($memberid, $userid) {
        $failsafeCounter = 0;
        $limit = 10;
        do{
            $newEmail = fake()->email;
            $member = Member::where('id','<>',$memberid)->where('work_email','<>',$newEmail)->first();
            $user = User::where('id','<>',$userid)->where('email','<>',$newEmail)->first();
            if($user == null && $member == null ) return $newEmail;
        }while( $failsafeCounter++ < $limit );
        throw new \Exception("Attempted to find an unused email. Tried $limit times. All emails were previously used by another user.");
    }

    public function getCurrentValue(Member $member, string $fieldName) {
        $array = $member->getFormData();
        if( isset( $array[$fieldName] ) ){
            return $array[$fieldName];
        }
        switch($fieldName){
            default:
                $this->markTestIncomplete("Current Value is not yet defined for $fieldName.");
        }
    }

}

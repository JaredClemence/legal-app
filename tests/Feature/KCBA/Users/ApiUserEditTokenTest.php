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

/**
 * @group TokenEditTest
 */
class ApiUserEditTokenTest extends TestCase
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
     * @group UserEdits
     */
    public function test_route_returns_200_for_user():void {
        $token = TimedSecurityToken::factory()->create();
        $member = $this->makeNonAdminMember();
        $user = $member->user;
                
        $postedData = $this->makeCleanPostedData($member, 'name', $token);
        
        $url = route('kcba.member.edit',compact('member'));
        $response = $this->post($url, $postedData);
        if($token) $token->delete();
        $response->assertSuccessful();
    }
    
    /**
     * @dataProvider fieldTestChangeData
     */
    public function test_user_changes_to_existing_values($expectedChangeAbility, $fieldName):void {
        //$token = TimedSecurityToken::factory()->create();
        $token = null;
        $member = $this->makeNonAdminMember();
        $user = $member->user;
                
        $changedDataField = $fieldName;
        $originalValue = $this->getCurrentValue($member, $fieldName);
        $postedData = $this->makeCleanPostedData($member, $changedDataField, $token);
        
        $this->assertNotEquals($originalValue, $postedData[$fieldName], 
                "The changed field test failed to establish propper initial "
                . "conditions, because $fieldName equals $originalValue in the "
                . "database and is {$postedData[$fieldName]} in the POST data.");
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
    
    static public function fieldTestChangeData(){
        return [
            'barnum'=>['can','barnum'],
            'name'=>['can','name'],
            'password'=>['can', 'password'],
            'firm_name'=>['can', 'firm_name'],
            'work_email'=>['cannot', 'work_email'],
            'role'=>['cannot','role'],
            'email'=>['cannot','email'],
            'status'=>['cannot','status'],
            'user_id'=>['cannot','user_id'],
            'firm_id'=>['cannot','firm_id']
        ];
    }
    
    private function makeCleanPostedData(Member $member, string $field, $token=null) {
        $dataArray = $member->getFormData();
        $newValue = null;
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
            case 'status':
                $newValue = "PENDING";
                break;
            case 'role':
                $newValue = $dataArray[$field]=="USER"?"ADMIN":"USER";
                break;
            case 'user_id':
            case 'firm_id':
                do{
                    $newValue = rand() % 25;
                }while( $dataArray[$field] == $newValue );
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
            $member = Member::where('id','<>',$memberid)->where('work_email','=',$newEmail)->first();
            $user = User::where('id','<>',$userid)->where('email','=',$newEmail)->first();
            if($user == null && $member == null ) return $newEmail;
        }while( $failsafeCounter++ < $limit );
        throw new \Exception("Attempted to find an unused email. Tried $limit times. All emails were previously used by another user.");
    }

    public function getCurrentValue(Member $member, string $fieldName) {
        $member->refresh();
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

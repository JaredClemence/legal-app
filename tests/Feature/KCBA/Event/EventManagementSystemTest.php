<?php

namespace Tests\Feature\KCBA\Event;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\KCBA\TimedSecurityToken;
use Database\Factories\KCBA\TimedSecurityTokenFactory;
use Database\Factories\KCBA\MemberFactory;
use App\Models\KCBA\Member;
use App\Models\User;
use App\Models\KCBA\Event;

/**
 * @group EventManagement
 */
class EventManagementSystemTest extends TestCase
{
    use RefreshDatabase;
    
    static $ADMIN = "admin";
    static $USER = "user";
    static $TOKEN = "token";
    static $CREATE_ENDPOINT = "kcba.events.create";
    static $EDIT_ENDPOINT = "kcba.events.edit";
    static $VIEW_ENDPOINT = "kcba.events.show";
    private $user;
    private $token;
    private $response;

    /**
     * A basic feature test example.
     */
    public function testAdminDisplaysCreateForm(){
        $this->initializeUser(self::$ADMIN);
        $this->displayEndpoint(self::$CREATE_ENDPOINT);
        $this->assertSuccessfulDisplay(true);
        $this->reset();
    }
    /**
     * @depends testAdminDisplaysCreateForm
     */
    public function testAdminCreatesEvent(){
        $this->markTestIncomplete();
    }
    public function testAdminDisplaysEditForm(){
        $this->initializeUser(self::$ADMIN);
        $this->displayEndpoint(self::$EDIT_ENDPOINT);
        $this->assertSuccessfulDisplay(true);
        $this->reset();
    }
    /**
     * @depends testAdminEditsEvent
     */
    public function testAdminEditsEvent(){
        $this->markTestIncomplete();
    }
    
    public function testUserCannotDisplayCreateForm(){
        $this->initializeUser(self::$USER);
        $this->displayEndpoint(self::$CREATE_ENDPOINT);
        $this->assertSuccessfulDisplay(false);
        $this->reset();
    }
    public function testUserCannotCreateEvent(){
        $this->markTestIncomplete();
    }
    
    public function testUserCannotDisplayEditForm(){
        $this->initializeUser(self::$USER);
        $this->displayEndpoint(self::$EDIT_ENDPOINT);
        $this->assertSuccessfulDisplay(false);
        $this->reset();
    }
    public function testUserCannotEditEvent(){
        $this->markTestIncomplete();
    }
    
    public function testTokenCannotDisplayCreateForm(){
        $this->initializeUser(self::$TOKEN);
        $this->displayEndpoint(self::$CREATE_ENDPOINT);
        $this->assertSuccessfulDisplay(false);
        $this->reset();
    }
    public function testTokenCannotCreateEvent(){
        $this->markTestIncomplete();
    }
    public function testTokenCannotDisplayEditForm(){
        $this->initializeUser(self::$TOKEN);
        $this->displayEndpoint(self::$EDIT_ENDPOINT);
        $this->assertSuccessfulDisplay(false);
        $this->reset();
    }
    public function testTokenCannotEditEvent(){
        $this->markTestIncomplete();
    }
    public function testUserCanDisplayEvent(){
        $this->initializeUser(self::$USER);
        $this->displayEndpoint(self::$VIEW_ENDPOINT);
        $this->assertSuccessfulDisplay(true);
        $this->reset();
    }
    public function testAdminCanDisplayEvent(){
        $this->initializeUser(self::$ADMIN);
        $this->displayEndpoint(self::$VIEW_ENDPOINT);
        $this->assertSuccessfulDisplay(true);
        $this->reset();
    }

    private function initializeUser($type) {
        $user = $this->makeUser();
        switch($type){
            case self::$ADMIN:
                $this->upgradeUserToAdmin($user);
                break;
            case self::$TOKEN:
                $this->createToken();
                $user = null;
                break;
        }
        $this->user = $user;
    }

    private function displayEndpoint($name) {
        $url = $this->getEndpointUrl($name);
        $response = null;
        if($this->token){
            $url = url()->query($url, ['token' => $this->token]);
        }
        if( $this->user ){
            $response = $this->actingAs($this->user)
                             ->get($url);
        }else{
            $response = $this->get($url);
        }
        $this->response = $response;
    }

    private function createToken() {
        $token = TimedSecurityToken::factory()->create();
        $this->token = $token;
    }

    private function getEndpointUrl($name) {
        switch($name){
            case self::$VIEW_ENDPOINT:
            case self::$EDIT_ENDPOINT:
                $event = $this->generateEvent();
                $url = route($name, compact('event'));
                break;
            case self::$CREATE_ENDPOINT:
                $url = route($name);
                break;
        }
        return $url;
    }

    private function reset() {
        unset( $this->user );
        unset( $this->token );
        unset( $this->response );
    }

    private function makeUser() {
        $member = Member::factory()->create();
        $user = $member->user;
        $user->member = $member;
        return $user;
    }

    private function upgradeUserToAdmin(User $user) {
        $member = $user->member; //this field is created within this test temporarily.
        $member->role = "ADMIN";
        $member->save();
        $member->refresh();
    }

    private function generateEvent() {
        $event = Event::factory()->create();
        return $event;
    }

    private function assertSuccessfulDisplay($result) {
        if($result==true){
            $this->response->assertSuccessful();
        }
        else {
            $this->response->assertUnauthorized();
        }
    }

}

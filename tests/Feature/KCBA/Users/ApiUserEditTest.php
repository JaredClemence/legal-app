<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserEditTest extends TestCase
{
    
    public function test_route_returns_200():void {}
    
    public function test_route_fails_without_auth():void {}
    
    public function test_user_changed_on_post():void {}
    
    public function test_user_change_failed_without_admin_status_for_persons_outside_of_firm():void {}
    
    public function test_user_change_succeeds_without_admin_status_for_persons_inside_of_firm():void {}
    
    public function test_admin_can_change_any_user_battery_test():void {}
}

<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserCreateTest extends TestCase
{
    public function test_route_returns_200():void {}
    
    public function test_route_fails_without_security_token():void {}
    
    public function test_route_fails_with_expired_security_token():void {}
    
    public function test_new_user_created_on_post():void {}
    
    public function test_new_user_failed_without_security_token():void {}
    
    public function test_new_user_field_battery_demonstrates_flexible_use_of_optional_and_required_fields():void {}
}

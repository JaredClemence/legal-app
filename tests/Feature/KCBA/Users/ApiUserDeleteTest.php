<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserDeleteTest extends TestCase
{
    public function test_user_auto_deletes_six_years_after_expiration():void {}
    
    public function test_admin_can_delete_users():void {}
    
    public function test_user_cannot_delete_self():void {}
}

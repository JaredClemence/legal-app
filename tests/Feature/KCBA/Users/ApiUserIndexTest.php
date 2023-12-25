<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiUserIndexTest extends TestCase
{
    public function test_route_returns_200():void {}
    
    public function test_shows_all_users_for_admin():void{}
    
    public function test_shows_firm_users_for_general_users():void{}
    
    public function test_index_fails_without_authentication():void{}
}

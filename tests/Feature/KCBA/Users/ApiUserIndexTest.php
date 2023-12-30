<?php

namespace Tests\Feature\KCBA\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\Testing\KCBA\MemberSeeder;

class ApiUserIndexTest extends TestCase
{
    /**
     * @beforeClass
     */
    public function setUpDatabase(){
        $this->artisan('migrate:fresh');
        
        //$this->seed(MemberSeeder::class);
    }
    
    public function cleanUpDatabase(){
        //$this->artisan('migrate:fresh');
    }
    
    public function test_route_returns_200():void {}
    
    public function test_shows_all_users_for_admin():void{}
    
    public function test_shows_firm_users_for_general_users():void{}
    
    public function test_index_fails_without_authentication():void{}
}

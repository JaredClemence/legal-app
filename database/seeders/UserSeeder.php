<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try{
            User::factory()->create([
                'name'=>"Jared R. Clemence",
                'email'=>"jaredclemence@gmail.com",
                'password'=>Hash::make("password")
            ]);
        }catch( \Exception $e ){
            
        }
    }
}

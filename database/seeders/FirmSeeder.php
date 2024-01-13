<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KCBA\Firm;

class FirmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            null,
            "Coleman & Horowitt",
            "Darling & Wilson",
            "Kline DeNatale Goldman",
            "Clifford & Brown"
        ];
        collect($array)->each( function($firmName){
            try{
                Firm::factory()->create([
                    'firm_name'=>$firmName
                ]);
            }catch(\Exception $e){
                
            }
        });
    }
}

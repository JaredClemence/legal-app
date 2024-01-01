<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\KCBA\MemberFactory;
use App\Models\KCBA\WorkEmail;
use App\Models\User;

class Member extends Model
{
    use HasFactory;
    
    /** 
    * Create a new factory instance for the model.
    */
   protected static function newFactory(): Factory
   {
       return MemberFactory::new();
   }
    
    public function getMembersFromMyFirm(){
        $members = Members::where('firm_id','=',$this->firm_id)
                ->get();
        
        if($members->count == 0){
            $members = collect($this);
        }
        
        return $members;
    }
    
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function firm(): HasOne
    {
        return $this->hasOne(Firm::class,'id', 'firm_id');
    }
}

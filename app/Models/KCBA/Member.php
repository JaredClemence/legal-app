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
        $work_email = $this->work_email;
        $firm = $work_email->firm_name;
        
        $members = DB::table('members')
                ->leftJoin('work_emails','members.email_id','=','work_emails.id')
                ->where('firm_name','=',$firm)
                ->get();
        
        if($members->count == 0){
            $members = collect($this);
        }
        
        return $members;
    }
    
    public function work_email(): HasOne
    {
        return $this->hasOne(WorkEmail::class, 'id', 'email_id');
    }
    
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

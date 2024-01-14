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
    
    public $fillable = [
        'user_id',
        'firm_id',
        'barnum',
        'status',
        'work_email'
    ];
    
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
    
    public function isAdmin() : bool
    {
        return $this->role == 'ADMIN';
    }

    public function getFormData() {
        $user = $this->user;
        $firm = $this->firm;
        return [
            'name'=>$user->name,
            'email'=>$user->email,
            'password'=>$user->password,
            'work_email'=>$this->work_email,
            'barnum'=>$this->barnum,
            'user_id'=>$this->user_id,
            'firm_id'=>$this->firm_id,
            'firm_name'=>$firm->firm_name,
            'status'=>$this->status,
            'role'=>$this->role
        ];
    }

}

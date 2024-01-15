<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Database\Factories\KCBA\TimedSecurityTokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;

class TimedSecurityToken extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'hash',
        'minutes_to_expire',
    ];
    
    protected static function newFactory() : Factory
    {
        return TimedSecurityTokenFactory::new();
    }


    public function isExpired(): bool {
        $time_now = Carbon::now();
        
        $expiration = $this->created_at->addMinutes($this->minutes_to_expire);
        
        return $expiration->lessThanOrEqualTo($time_now); 
    }

    public static function requestHasValidToken(Request $request) {
        $token = $request->input('token','');
        if($token){
            $tokenObj = TimedSecurityToken::where('hash','=',$token)->orderBy('id','DESC')->first();
            if($tokenObj){
                $tokenIsValid = ($tokenObj->isExpired() == false);
                return $tokenIsValid;
            }
        }
        return false;
    }

}

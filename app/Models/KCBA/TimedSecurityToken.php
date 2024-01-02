<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Database\Factories\KCBA\TimedSecurityTokenFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}

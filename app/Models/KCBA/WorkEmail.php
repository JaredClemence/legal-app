<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\KCBA\WorkEmailFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KCBA\Member;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkEmail extends Model
{
    use HasFactory;
    
    /** 
    * Create a new factory instance for the model.
    */
   protected static function newFactory(): Factory
   {
       return WorkEmailFactory::new();
   }
   
   public function member():HasOne
   {
       return $this->hasOne(Member::class,'email_id','id');
   }
}

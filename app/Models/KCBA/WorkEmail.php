<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\KCBA\WorkEmailFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}

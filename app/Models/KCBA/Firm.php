<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KCBA\Member as BarMember;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\KCBA\FirmFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class Firm extends Model
{
    use HasFactory;
    
    /**
    * Create a new factory instance for the model.
    */
    protected static function newFactory(): Factory
    {
        return FirmFactory::new();
    }
    
    public function members():HasMany
    {
        return $this->hasMany(BarMember::class, 'firm_id', 'id');
    }
}

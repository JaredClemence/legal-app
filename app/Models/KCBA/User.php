<?php

namespace App\Models\KCBA;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    
    public function isAdministrator(){
        return $this->email == "jclemence@ch-law.com";
    }
    
    /**
     * 
     * @return boolean
     */
    public function isSectionMember(){
        //if the record exists at all, then the user is a section member
        $isMember = ($this->email !== null) &&
                ($this->notExpired());
        return $isMember;
    }
    
    /**
     * @return boolean
     * @todo Update with DateTime comparison of now against the expiration date.
     */
    private function notExpired(){
        return true;
    }
}

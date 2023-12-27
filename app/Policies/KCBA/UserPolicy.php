<?php

namespace App\Policies\KCBA;

use App\Models\KCBA\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    
    public function before(User $user, string $ability)
    {
        if( $ability == "forceDelete" ){
            return false; //no person can force delete. Not even administrators.
        }
        
        if ($user->isAdministrator()) {
            return true;
        }

        return null;
    }
    
    /**
     * All bar members may use the viewAny/index method, the limitation applies
     * to individual methods.
     * 
     * @param User $actor
     * @param User $subject
     * @return boolean
     */
    public function viewAny(User $actor, User $subject){
        return true;
    }
    
    /**
     * Only people from the same firm may view other records.
     * @param User $actor
     * @param User $subject
     */
    public function view(User $actor, User $subject){
        return $actor->getFirm() == $subject->getFirm();
    }
    
    /**
     * Users can create their own registration record if they possess a security 
     * token. Must update this later, when security tokens are developed.
     * @param User $actor
     * @return boolean
     */
    public function create(User $actor){
        return false;
    }
    
    /**
     * Users can update the records of any person from the same firm.
     * @param User $actor
     * @param User $subject
     */
    public function update(User $actor, User $subject){
        return $actor->getFirm() == $subject->getFirm();
    }
    
    /**
     * Users can soft delete the records of any person from the same firm.
     * @param User $actor
     * @param User $subject
     */
    public function delete(User $actor, User $subject){
        return $actor->getFirm() == $subject->getFirm();
    }
    
    /**
     * Users can restore the records of any person from the same firm.
     * @param User $actor
     * @param User $subject
     */
    public function restore(User $actor, User $subject){
        return $actor->getFirm() == $subject->getFirm();
    }
    
    /**
     * No person can force delete a record.
     * @param User $actor
     * @param User $subject
     * @return type
     */
    public function forceDelete(User $actor, User $subject){
        return false;
    }
}

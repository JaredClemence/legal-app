<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

use App\Http\Controllers\Paypal\Classes\PlanPaymentPreferences;
use App\Http\Controllers\Paypal\Classes\BillingCycle;

/**
 * Description of Plan
 *
 * @author jaredclemence
 */
class Plan extends ApiBaseClass {
    public function __construct(){
        $this->status = "ACTIVE";
    }
    public function setProductId( $id ){
        $this->validateStringLength($id, 50, 6);
        $this->product_id = $id;
        return $this;
    }
    public function setName( $name ){
        $this->validateStringLength($name, 127, 1);
        $this->name = $name;
        return $this;
    }
    public function setDescription($description){
        $this->validateStringLength($description, 127, 1);
        $this->description = $description;
        return $this;
    }
    public function addBillingCycleObject(BillingCycle $cycle, $allowOverride=true){
        if( isset($this->billing_cycles) == false ){
            $this->billing_cycles = [];
        }
        if(in_array($cycle, $this->billing_cycles, true)===true){
            return; //don't add an object that is already added.
        }
        if($allowOverride){
            //override sequence by order added.
            $sequence = count($this->billing_cycles) + 1;
            $cycle->setSequence($sequence);
        }
        $this->billing_cycles[] = $cycle;
        return $this;
    }
    public function setPaymentPlanPreference(PlanPaymentPreferences $preference){
        $this->payment_preferences = $preference;
        return $this;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes\CommonDataStructures;
use App\Http\Controllers\Paypal\Classes\ApiBaseClass;
/**
 * Description of Value
 *
 * @author jaredclemence
 */
class Value extends ApiBaseClass{
    static public function makeValue($amount, $currency="USD"){
        $value = new Value();
        $value->setAmount($amount)->setCurrency($currency);
        return $value;
        
    }
    public function setAmount($amount){
        $formattedAmount = $this->padCents($amount);
        $this->validateStringLength($formattedAmount, 32);
        $this->value = $amount;
        return $this;
    }
    public function setCurrency($type){
        $this->validateStringLength($type, 3, 3);
        $this->currency_code = $type;
        return $this;
    }
}

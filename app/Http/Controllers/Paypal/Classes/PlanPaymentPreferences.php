<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

/**
 * Description of PlanPaymentPreferences
 *
 * @author jaredclemence
 */
class PlanPaymentPreferences extends ApiBaseClass {
    const SETUP_FAILURE_CONTINUE = "CONTINUE";
    const SETUP_FAILURE_CANCEL = "CANCEL";
    public function setAutoBillOutstanding($option){
        $this->validateEnumratedOption($option, [TRUE, FALSE]);
        $this->auto_bill_outstanding = $option;
        return $this;
    }
    
    public function setSetupFeeFailureAction($option){
        $this->validateEnumratedOption($option, [self::SETUP_FAILURE_CONTINUE, self::SETUP_FAILURE_CANCEL]);
        $this->setup_fee_failure_action = $option;
        return $this;
    }
    
    public function setPaymentFailureThreshold($int)
    {
        $this->validateInteger($int);
        $this->payment_failure_threshold = $int;
        return $this;
    }
    
    public function setSetupFee( $fee, $currency="USD"){
        $this->validateStringLength($fee, 32, 1);
        $formatedFee = $this->padCents($fee);
        $this->setup_fee = (object)[
            "currency_code"=>$currency,
            "value"=>$formatedFee
        ];
        return $this;
    }

}

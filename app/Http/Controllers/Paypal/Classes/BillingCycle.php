<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

/**
 * Description of BillingCycle
 *
 * @author jaredclemence
 */
class BillingCycle extends ApiBaseClass {
    const DAY="DAY";
    const WEEK="WEEK";
    const MONTH="MONTH";
    const YEAR="YEAR";
    const TENURE_REGULAR = "REGULAR";
    const TENURE_TRIAL = "TRIAL";
    public function setTenureType($type){
        $this->validateEnumratedOption($type, 
                [self::TENURE_REGULAR, self::TENURE_TRIAL]);
        $this->tenure_type = $type;
        return $this;
    }
    public function setSequence($int){
        $this->validateInteger($int, 1, 99);
        $this->sequence = $int;
        return $this;
    }
    public function setTotalCycles($cycles){
        $this->validateInteger($cycles, 0, 999);
        $this->total_cycles=$cycles;
        return $this;
    }
    public function setFixedPricePerCycle($price, $currency="USD"){
        $this->validateStringLength($price, 32);
        $formatedPrice = $this->padCents($price);
        $this->pricing_scheme=(object)[
            "fixed_price"=>(object)[
                "currency_code"=>$currency,
                "value"=>$formatedPrice
            ]
        ];
        return $this;
    }
    public function setFrequency($unit, $length=1){
        $this->validateEnumratedOption($unit, [
            self::DAY, self::WEEK, self::MONTH, self::YEAR
        ]);
        $this->validateCycleLength($unit, $length);
        $this->frequency = (object)[
            "interval_unit"=>$unit,
            "interval_count"=>$length
        ];
        return $this;
    }
    protected function validateCycleLength($unit, $length) {
        $max = 365;
        switch($unit){
            case self::DAY:
                $max = 365;
                break;
            case self::WEEK:
                $max = 52;
                break;
            case self::MONTH:
                $max = 12;
                break;
            case self::YEAR:
                $max = 1;
                break;
        }
        if($length > $max){
            throw new \Exception("Billing cycle length cannot be larger than $max for periods measured in $unit units.");
        }
    }

}

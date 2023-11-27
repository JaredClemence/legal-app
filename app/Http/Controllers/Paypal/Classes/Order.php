<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

use App\Http\Controllers\Paypal\Classes\ApiBaseClass;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\PurchaseUnit;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\Value;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\ExperienceContext;

/**
 * Description of Order
 *
 * @author jaredclemence
 * @var array [Required] purchase_units
 * @var string [Required] intent
 * @var Value amount
 */
class Order extends ApiBaseClass {
    const IntentCapture = "CAPTURE";
    const IntentAuthorizeFuturePayment = "AUTHORIZE";
    static public $intentOptions = [
        'CAPTURE',
        'AUTHORIZE'
    ];
    public function __construct() {
        $this->setIntent(self::IntentCapture);
        $this->purchase_units = [];
    }
    public function addPurchaseUnit(PurchaseUnit $unit){
        if(!in_array($unit, $this->purchase_units)){
            $this->purchase_units[] = $unit;
        }
        return $this;
    }
    public function setIntent($intent){
        $this->validateEnumratedOption($intent, self::$intentOptions);
        $this->intent = $intent;
        return $this;
    }
    
    /**
     * @return ExperienceContext $context
     */
    public function getEmptyPaypalExperienceContext(): ExperienceContext{
        $context = new ExperienceContext();
        $paypal = new \stdClass();
        $source = new \stdClass();
        $source->paypal = $paypal;
        $paypal->experience_context = $context;
        $this->payment_source = $source;
        return $context;
    }
}

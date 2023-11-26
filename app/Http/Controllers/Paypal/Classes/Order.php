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
    public function setAmount($value, $currency="USD", $itemTotal=null, 
            $shipping=null, $handlingTotal=null, $taxTotal=null, $insuranceTotal=null,
            $shippingDiscountTotal=null, $discountTotal=null ){
        $this->amount = (object)[
            "value"=>$this->padCents($value),
            "currency_code"=>$currency
        ];
        $breakdown = [];
        if($itemTotal) $breakdown["item_total"]=Value::makeValue($itemTotal, $currency);
        if($shipping) $breakdown["shipping"]=Value::makeValue($shipping, $currency);
        if($handlingTotal) $breakdown["handling"]=Value::makeValue($handlingTotal, $currency);
        if($taxTotal) $breakdown["tax_total"]=Value::makeValue($taxTotal, $currency);
        if($insuranceTotal) $breakdown["insurance"]=Value::makeValue($insuranceTotal, $currency);
        if($shippingDiscountTotal) $breakdown["shipping_discount"]=Value::makeValue($shippingDiscountTotal, $currency);
        if($discountTotal) $breakdown["discount"]=Value::makeValue($discountTotal, $currency);
        if(count($breakdown)>0){
            $this->amount->breakdown = (object)$breakdown;
        }
        return $this;
    }
}

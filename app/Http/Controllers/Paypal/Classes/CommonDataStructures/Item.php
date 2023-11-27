<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes\CommonDataStructures;
use App\Http\Controllers\Paypal\Classes\ApiBaseClass;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\Value;

/**
 * Description of Item
 *
 * @author jaredclemence
 */
class Item extends ApiBaseClass {
    const DIGITAL_GOOD = "DIGITAL_GOODS";
    const PHYSICAL_GOODS = "PHYSICAL_GOODS";
    /* Required Values */
    public function setName($text){
        $this->validateStringLength($text, 127, 1);
        $this->name = $text;
        return $this;
    }
    public function setQuantity($count){
        $this->validateStringLength($count, 10);
        $this->quantity = $count;
        return $this;
    }
    public function setUnitAmount($amount, $currency="USD"){
        $this->unit_amount = Value::makeValue($amount, $currency);
        return $this;
    }
    
    /* Optional Values */
    public function setDescription($text){
        $this->validateStringLength($text, 127);
        $this->description = $text;
        return $this;
    }
    public function setSku($text){
        $this->validateStringLength($text, 127);
        $this->sku = $text;
        return $this;
    }
    public function setCategory($category){
        $this->validateEnumratedOption($category, [self::DIGITAL_GOOD, self::PHYSICAL_GOODS]);
        $this->category = $category;
        return $this;
    }
    public function setTax($amount, $currency="USD"){
        $this->tax = Value::makeValue($amount,$currency);
        return $this;
    }
}

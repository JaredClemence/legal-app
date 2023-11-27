<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes\CommonDataStructures;
use App\Http\Controllers\Paypal\Classes\ApiBaseClass;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\Value;
use App\Http\Controllers\Paypal\Classes\CommonDataStructures\Item;

/**
 * Description of PurchaseUnit
 *
 * @author jaredclemence
 * @var string reference_id The API caller-provided external ID for the purchase unit. Required for multiple purchase units when you must update the order through PATCH. If you omit this value and the order contains only one purchase unit, PayPal sets this value to default.
 * @var string description The purchase description. The maximum length of the character is dependent on the type of characters used. The character length is specified assuming a US ASCII character. Depending on type of character; (e.g. accented character, Japanese characters) the number of characters that that can be specified as input might not equal the permissible max length.
 * @var string custom_id The API caller-provided external ID. Used to reconcile client transactions with PayPal transactions. Appears in transaction and settlement reports but is not visible to the payer.
 * @var string invoice_id The API caller-provided external invoice number for this order. Appears in both the payer's transaction history and the emails that the payer receives.
 * @var string soft_descriptor The soft descriptor is the dynamic text used to construct the statement descriptor that appears on a payer's card statement.
 * @var Value amount [Required] The total order amount with an optional breakdown that provides details, such as the total item amount, total tax amount, shipping, handling, insurance, and discounts, if any. If you specify amount.breakdown, the amount equals item_total plus tax_total plus shipping plus handling plus insurance minus shipping_discount minus discount. The amount must be a positive number. The amount.value field supports up to 15 digits preceding the decimal. For a list of supported currencies, decimal precision, and maximum charge amount, see the PayPal REST APIs Currency Codes.
 * @var array items An array of items that the customer purchases from the merchant.
 */
class PurchaseUnit extends ApiBaseClass {
    public function setReferenceId($id){
        $this->validateStringLength($id, 256, 1);
        $this->reference_id = $id;
        return $this;
    }
    public function setDescription($text){
        $this->validateStringLength($text, 127, 1);
        $this->description = $text;
        return $text;
    }
    public function setLocalReferenceId($id){
        $this->validateStringLength($id, 127, 1);
        $this->custom_id = $id;
        return $this;
    }
    public function setLocalInvoiceId($id){
        $this->validateStringLength($id, 127, 1);
        $this->invoice_id = $id;
        return $this;
    }
    public function setSoftDescription($text){
        $this->validateStringLength($text, 22, 1);
        $this->soft_descriptor = $text;
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
    public function addItem(Item $item){
        if( !isset($this->items) ){
            $this->items = [];
        }
        $this->items[] = $item;
        return $this;
    }
}

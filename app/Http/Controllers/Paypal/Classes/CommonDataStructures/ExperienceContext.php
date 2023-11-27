<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes\CommonDataStructures;
use App\Http\Controllers\Paypal\Classes\ApiBaseClass;
/**
 * Description of ExperienceContext
 *
 * @author jaredclemence
 * @var string brand_name [0..127] The label that overrides the business name in the PayPal account on the PayPal site. The pattern is defined by an external party and supports Unicode.
 * @var string shipping_preference [1..24] The location from which the shipping address is derived.
 * @var string landing_page [1..13] The type of landing page to show on the PayPal site for customer checkout.
 * @var string user_action [1..8] Configures a Continue or Pay Now checkout flow.
 * @var string payment_method_preference [1..255]
 * @var string locale [ 2 .. 10 ] The BCP 47-formatted locale of pages that the PayPal payment experience shows. PayPal supports a five-character code. For example, da-DK, he-IL, id-ID, ja-JP, no-NO, pt-BR, ru-RU, sv-SE, th-TH, zh-CN, zh-HK, or zh-TW.
 * @var string return_url
 * @var string cancel_url
 */
class ExperienceContext extends ApiBaseClass{
    const SHIP_PREF_GET_FROM_FILE = "GET_FROM_FILE";
    const SHIP_PREF_NO_SHIPPING = "NO_SHIPPING";
    const SHIP_PREF_SET_PROVIDED_ADDRESS = "SET_PROVIDED_ADDRESS";
    
    const LANDING_PAGE_LOGIN = "LOGIN";
    const LANDING_PAGE_GUEST_CHECKOUT = "GUEST_CHECKOUT";
    const LANDING_PAGE_NO_PREFERENCE = "NO_PREFERENCE";
    
    const USER_ACTION_CONTINUE = "CONTINUE";
    const USER_ACTION_PAY_NOW = "PAY_NOW";
    
    const PAY_PREFERENCE_UNRESTRICTED = "UNRESTRICTED";
    const PAY_PREFERENCE_IMMEDIATE_PAYMENT_REQUIRED = "IMMEDIATE_PAYMENT_REQUIRED";
    
    public function setReturnUrl($url){
        $this->return_url = $url;
        return $this;
    }
    public function setCancelUrl($url){
        $this->cancel_url = $url;
        return $this;
    }
    public function setLocale($localeCode){
        $this->validateStringLength($localeCode, 5, 5);
        $this->locale = $localeCode;
        return $this;
    }
    public function setPaymentMethodPreference($preference){
        $this->validateEnumratedOption($preference, [
            self::PAY_PREFERENCE_UNRESTRICTED, self::PAY_PREFERENCE_IMMEDIATE_PAYMENT_REQUIRED
        ]);
        $this->payment_method_preference = $preference;
        return $this;
    }
    public function setUserAction($action){
        $this->validateEnumratedOption($action, [
            self::USER_ACTION_CONTINUE, self::USER_ACTION_PAY_NOW
        ]);
        $this->user_action = $action;
        return $this;
    }
    public function setLandingPage($pageSetting){
        $this->validateEnumratedOption($pageSetting, [
            self::LANDING_PAGE_GUEST_CHECKOUT, 
            self::LANDING_PAGE_LOGIN,
            self::LANDING_PAGE_NO_PREFERENCE
        ]);
        $this->landing_page = $pageSetting;
        return $this;
    }
    public function setShippingPreference($preference){
        $this->validateEnumratedOption($preference, [
            self::SHIP_PREF_GET_FROM_FILE, 
            self::SHIP_PREF_NO_SHIPPING,
            self::SHIP_PREF_SET_PROVIDED_ADDRESS
        ]);
        $this->shipping_preference = $preference;
        return $this;
    }
    public function setBrandName($name){
        $this->validateStringLength($name, 127);
        $this->brand_name = $name;
        return $this;
    }
    
}

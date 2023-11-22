<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

/**
 * Description of AuthToken
 *
 * @author jaredclemence
 */
class AuthToken {
    public $type;
    public $token;
    
    public function __construct($type, $token) {
        $this->type=$type;
        $this->token=$token;
    }
}

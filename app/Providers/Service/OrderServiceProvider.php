<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Providers\Service;

use App\Providers\Service\BaseServiceProvider;
use App\Http\Controllers\Paypal\Classes\Order;
use App\Http\Controllers\Paypal\Classes\Curl;

/**
 * Description of OrderServiceProvider
 *
 * @author jaredclemence
 */
class OrderServiceProvider extends BaseServiceProvider {
    public function all() {
        
    }

    public function id($id) {
        
    }

    public function save(\stdClass $order) {
        $authString = $this->getAuthorizationHeader();
        $endpoint = "/v2/checkout/orders";
        
        $curl = new Curl();
        $curl->setUrlByShortpath($endpoint)
                ->addHeader($authString)
                ->addHeader("Content-Type: application/json")
                ->addHeader("Accept: application/json")
                ->addHeader("Prefer: return=representation")
                ->commitHeader()
                ->setPost(json_encode($order))
                ->exec();
        $jsonResult = $curl->getJson();
        return $jsonResult;
    }
}

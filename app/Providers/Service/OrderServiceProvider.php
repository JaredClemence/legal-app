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
use stdClass;
use App\Models\Order as OrderRecord;

/**
 * Description of OrderServiceProvider
 *
 * @author jaredclemence
 */
class OrderServiceProvider extends BaseServiceProvider {

    const ID_KEY = "current_paypal_order_id";

    public function all() {
        
    }

    public function id($id) {
        
    }

    public function save(stdClass $order) {
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
        $this->saveLocalOrderData($order, $jsonResult);
        return $jsonResult;
    }
    
    public function saveLocalOrderData(stdClass $order, stdClass $result){
        $record = OrderRecord::firstOrNew(['paypal_id'=>$result->id]);
        $record->paypal_id = $result->id;
        $record->status = $result->status;
        $record->intent = $result->intent;
        $record->paypal_order_obj = json_encode($order);
        $record->payment_source = json_encode($result->payment_source);
        $record->purchase_units = json_encode($result->purchase_units);
        $record->links = json_encode($result->links);
        $record->save();
        
        Session([self::ID_KEY=>$record->id]);
    }
}

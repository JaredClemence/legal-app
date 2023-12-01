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
        return OrderRecord::all();
    }

    public function id($id) {
        return OrderRecord::find($id);
    }
    
    public function captureCurrentOrder(){
        $id = session(self::ID_KEY);
        $orderRecord = $this->id($id);
        $endpoint = "/v2/checkout/orders/{$orderRecord->paypal_id}/capture";
        $authString = $this->getAuthorizationHeader();
        
        $curl = new Curl();
        $curl->setUrlByShortpath($endpoint)
                ->addHeader($authString)
                ->addHeader("Content-Type: application/json")
                ->addHeader("Accept: application/json")
                ->addHeader("Prefer: return=representation")
                ->commitHeader()
                ->setPost("")
                ->exec();
        $jsonResult = $curl->getJson();
        $this->updateOrderData($jsonResult);
        return $jsonResult;
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

    public function updateOrderData($result) {
        $id = $result->id;
        $orderRecord = OrderRecord::where('paypal_id', $id)->first();
        if(isset($orderRecord)){
            if(isset($result->status)) $orderRecord->status = json_encode($result->status);
            if(isset($result->payment_source)) $orderRecord->payment_source = json_encode($result->payment_source);
            if(isset($result->purchase_units)) $orderRecord->purchase_units = json_encode($result->purchase_units);
            if(isset($result->payer)) $orderRecord->payer = json_encode($result->payer);
            if(isset($result->links)) $orderRecord->links = json_encode($result->links);
            $orderRecord->save();
        }
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Providers\Service;

use App\Providers\Service\BaseServiceProvider;
use App\Http\Controllers\Paypal\Classes\Curl;

/**
 * Description of ProductServiceProvider
 *
 * @author jaredclemence
 */
class ProductServiceProvider extends BaseServiceProvider {
    public function all() {
        $authString = $this->getAuthorizationHeader();
        $endpoint = "/v1/catalogs/products";
        
        $curl = new Curl();
        $curl->setUrlByShortpath($endpoint)
                ->addHeader($authString)
                ->addHeader("Content-Type: application/json")
                ->addHeader("Accept: application/json")
                ->addHeader("Prefer: return=representation")
                ->commitHeader()
                ->exec();
        $jsonResult = $curl->getJson();
        return $jsonResult;
    }

    /**
     * Get a product by id.
     * @param \stdClass $item
     * @return type
     */
    public function id($id) {
        $authString = $this->getAuthorizationHeader();
        $endpoint = "/v1/catalogs/products/$id";
        
        $curl = new Curl();
        $curl->setUrlByShortpath($endpoint)
                ->addHeader($authString)
                ->addHeader("Content-Type: application/json")
                ->commitHeader()
                ->exec();
        $jsonResult = $curl->getJson();
        return $jsonResult;
    }

    /**
     * Create a new product in the PayPal system.
     * @param \stdClass $item
     * @return type
     */
    public function save(\stdClass $item) {
        $authString = $this->getAuthorizationHeader();
        $endpoint = "/v1/catalogs/products";
        
        $curl = new Curl();
        $curl->setUrlByShortpath($endpoint)
                ->addHeader($authString)
                ->addHeader("Content-Type: application/json")
                ->addHeader("Accept: application/json")
                ->addHeader("Prefer: return=representation")
                ->commitHeader()
                ->setPost(json_encode($item))
                ->exec();
        $jsonResult = $curl->getJson();
        return $jsonResult;
    }

}

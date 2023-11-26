<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Factories;

use App\Http\Controllers\Paypal\Classes\Product;

/**
 * Description of ProductFactory
 *
 * @author jaredclemence
 */
class ProductFactory {
    public static function make( $json ) : Product 
    {
        $json = (object)$json;
        $product = new Product();
        $product->setId($json->id);
        $product->setName($json->name);
        $product->setDescription($json->description);
        $product->setType($json->type);
        $product->setCategory($json->category);
        $product->setCreateTime($json->create_time);
        $product->setUpdateTime($json->update_time);
        
        
        if(isset($json->payee)){
            $payeeJson = (object)$json->payee;
            $product->setPayeeByAttr($payeeJson->merchant_id, $payeeJson->display_data);
        }
        
        return $product;
    }
}

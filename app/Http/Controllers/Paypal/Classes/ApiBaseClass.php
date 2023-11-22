<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

/**
 * Description of ApiBaseClass
 *
 * @author jaredclemence
 */
abstract class ApiBaseClass extends \stdClass {
    protected function validateStringLength($string, $maxLength, $minLength=0){
        $len = strlen($string);
        if( $len >= $maxLength ){
            throw new \Exception( "'$string' exceeds the maximum permitted length of $maxLength.");
        }
        if( $len < $minLength ){
            throw new \Exception( "'$string' does not contain the minimum character length of $minLength.");
        }
    }

    protected function validateEnumratedOption($option, $array) {
        $exists = in_array($option, $array, true);
        if( !$exists ) throw new \Exception("'$option' is not a valid selection.");
    }

    protected function validateInteger($int, $min=null, $max=null) {
        if( !is_int($int) ) throw new \Exception("'$int' must be an integer value.");
        if($min!==null & $int<$min){
            throw new \Exception("'$int' must be at least $min.");
        }
        if($max!==null & $int>$max){
            throw new \Exception("'$int' must be no more than $max.");
        }
    }
    public function __toString() {
        return json_encode($this);
    }
    
    protected function padCents($valueString, $decimals=2){
        $parts = explode(".", $valueString);
        if( count($parts)==1 ){
            $parts[1] = "00";
        }else{
            while( strlen($parts[1]) < 2 ){
                $parts[1] .= "0";
            }
        }
        $newValueString = implode(".", $parts);
        return $newValueString;
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Paypal\Classes;

/**
 * Description of Curl
 *
 * @author jaredclemence
 */
class Curl {
    private $handle;
    private $headers;
    private $url;
    private $jsonResult;
    private $rawResult;

    public function __construct() {
        $this->headers = [];
        $this->url = null;
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($this->handle, CURLOPT_HEADER, 0);
    }
    
    public function setUrlByShortpath( $shortPath ){
        $url = $this->getCurlEndpoint($shortPath);
        $this->setUrlDirectly($url);
        $this->url = $url;
        return $this;
    }
    
    public function addHeader( $string ){
        if( !in_array($string, $this->headers, true)){
            $this->headers[] = $string;
        }
        return $this;
    }
    
    public function commitHeader(){
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
        return $this;
    }
    
    public function setPost( $dataString ){
        curl_setopt($this->handle, CURLOPT_POST, true);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $jsonString);
        return $this;
    }
    
    public function setGetData( $queryData ){
        if(strlen($this->url) == 0 ) throw new \Exception("User must set url before setting data.");
        $modifiedUrl = $this->url;
        if(strlen(trim($queryData))>0){
            $modifiedUrl .= "?" . $modifiedUrl;
        }
        $this->setUrlDirectly($modifiedUrl);
    }
    
    public function exec(){
        $output = curl_exec($this->handle);
        $this->rawResult = $output;
        $this->jsonResult = json_decode($output);
    }
    
    public function getJson(){
        return $this->jsonResult;
    }
    
    public function getRaw(){
        return $this->rawResult;
    }
    
    public function __destruct() {
        $ch = $this->handle;
        unset($this->handle);
        curl_close($ch);
    }
    
    private function getCurlEndpoint($endpoint, $queryString=null){
        $mode = $this->getModeFromEnvironment();
        $urlBase = env('PAYPAL_ENDPOINT_'.strtoupper($mode));
        
        $url = $urlBase . $endpoint;
        if($queryString){
            $url .= "?" . $queryString;
        }
        return $url;
    }
    
    private function setUrlDirectly( $url ){
        curl_setopt($this->handle, CURLOPT_URL, $url);
        return $this;
    }
    
    private function getModeFromEnvironment(){
        return env('PAYPAL_MODE', 'sandbox');
    }
}

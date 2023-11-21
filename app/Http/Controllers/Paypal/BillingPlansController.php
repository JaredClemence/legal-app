<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\PaypalController;

class BillingPlansController extends PaypalController
{
    public function show(Request $request){
        $response = $this->getPlans("jaredclemence.com");
        $plans = $response->plans;
        return view('paypal.plans.list', compact('plans'));
    }
    
    public function deactivate(Request $request, $plan_id){
        try{
        $response = $this->deactivatePlan("jaredclemence.com", $plan_id);
        }catch(\Exception $e){}
        return redirect(route('paypal.plans.list'));
    }
    
    protected function getPlans($clientName, $page=1){
        $provider = $this->getProviderForPaypalClient($clientName);
        $authId = $provider->getAccessToken();
        $token = $authId["access_token"];
        $token_type = $authId["token_type"];
        $url = env("PAYPAL_ENDPOINT_ROOT");
        $endpoint = "/v1/billing/plans";
        $query = http_build_query(
                [
                    "page_size"=>20,
                    "page"=>$page
                ]
                );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $endpoint. "?$query");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $token_type $token",
            "Content-Type: application/json",
            "Accept: application/json",
            "Prefer: return=representation",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        //dd($output);
        return $output;
    }
    
    private function deactivatePlan($clientName, $id){
        $provider = $this->getProviderForPaypalClient($clientName);
        $authId = $provider->getAccessToken();
        $token = $authId["access_token"];
        $token_type = $authId["token_type"];
        $url = env("PAYPAL_ENDPOINT_ROOT");
        $endpoint = "/v1/billing/plans/$id/deactivate";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $endpoint);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $token_type $token",
            "Content-Type: application/json",
            "Accept: application/json",
            "Prefer: return=representation",
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        return $output;
    }
    

}

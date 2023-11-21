<?php

namespace App\Http\Controllers\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\PaypalController;

use App\Http\Controllers\Paypal\Classes\BillingCycle;
use App\Http\Controllers\Paypal\Classes\Plan;
use App\Http\Controllers\Paypal\Classes\PlanPaymentPreferences;

class BillingPlansController extends PaypalController
{
    public function showNewPlanForm(Request $request){
        $cycle1 = new BillingCycle();
        $cycle1->setFrequency(BillingCycle::WEEK, 4)
                ->setTenureType(BillingCycle::TENURE_TRIAL)
                ->setTotalCycles(1)
                ->setFixedPricePerCycle("0.00")
                ->setSequence(1);
        $cycle2 = new BillingCycle();
        $cycle2->setFixedPricePerCycle("99.00")
                ->setFrequency(BillingCycle::WEEK, 4)
                ->setTenureType(BillingCycle::TENURE_REGULAR)
                ->setSequence(2)
                ->setTotalCycles(6);
        
        $preferences = new PlanPaymentPreferences();
        $preferences
                ->setAutoBillOutstanding(true)
                ->setPaymentFailureThreshold(3)
                ->setSetupFee("10.00")
                ->setSetupFeeFailureAction(PlanPaymentPreferences::SETUP_FAILURE_CANCEL);
        
        $description = <<<DESCRIBED
$99 Subscription billed every 4 weeks after a 4-week trial. Setup of $10 to test card. Ends after 6 paid cycles.
DESCRIBED;
        $plan = new Plan();
        $plan->addBillingCycleObject($cycle1, false)
                ->addBillingCycleObject($cycle2, false)
                ->setPaymentPlanPreference($preferences)
                ->setName("Subscription Model A")
                ->setDescription($description)
                ->setProductId("SUBSCRIPTION_PLAN_A");
        dd(json_decode($plan.""));
    }
    public function create(Request $request){
        
    }
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

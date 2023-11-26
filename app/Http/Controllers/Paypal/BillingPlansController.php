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
    public function showNewPlanForm(Request $request, $apiNickname){
        return view('paypal.plans.newplan', compact('apiNickname'));
    }
    public function create(Request $request, $apiNickname){
        $billingCycle = $this->buildPaymentPlan($request);
        $endpoint = "/v1/billing/plans";
        $url = $this->getCurlEndpoint($endpoint);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        
        $jsonString = json_encode($billingCycle);
        $authorizationHeader = $this->getAuthorizationHeader($apiNickname);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $authorizationHeader,
            "Content-Type: application/json",
            "Accept: application/json",
            "Prefer: return=representation",
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        dd($output);
        $token = $request->token;
        return redirect(route('paypal.plans.list', compact('apiNickname','token')));
    }
    public function show(Request $request, $apiNickname){
        $this->activateById($apiNickname, "99MO_4FREE_10SU_6MO");
        $plans = $this->getAllPlans($apiNickname);
        return view('paypal.plans.list', compact('plans','apiNickname'));
    }
    public function deactivate(Request $request, $plan_id){
        try{
        $response = $this->deactivatePlan("jaredclemence.com", $plan_id);
        }catch(\Exception $e){}
        return redirect(route('paypal.plans.list'));
    }
    /**
     * 
     * @param type $nickname
     * @param type $id
     * @todo Modify to remove product id. This should point to the plan id.
     */
    protected function activateById($nickname, $id ){
        $endpoint = "/v1/billing/plans/{$id}/activate";
        
        $authorizationHeader = $this->getAuthorizationHeader($nickname);
        $url = $this->getCurlEndpoint($endpoint);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $authorizationHeader,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        dd($output);
    }
    
    protected function getAllPlans($nickname){
        $page = 1;
        $allPlans = [];
        do{
            $continue = false;
            $response = $this->getPlans($nickname, $page++);
            $plans = $response->plans;
            if(count($plans)>0){
                $continue=true;
                $allPlans = array_merge($allPlans, $plans);
            }
        }while($continue==true && $page<100);
        return $allPlans;
    }
    
    protected function getPlans($nickname, $page=1){
        $endpoint = "/v1/billing/plans";
        $query = http_build_query(
                [
                    "page_size"=>20,
                    "page"=>$page
                ]
                );
        $url = $this->getCurlEndpoint($endpoint, $query);
        $authorizationHeader = $this->getAuthorizationHeader($nickname);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $authorizationHeader,
            "Content-Type: application/json",
            "Accept: application/json",
            "Prefer: return=representation",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode( curl_exec($ch) );
        curl_close($ch);
        return $output;
    }
    
    private function deactivatePlan($apiNickname, $id){
        $endpoint = "/v1/billing/plans/$id/deactivate";
        $url = $this->getCurlEndpoint($endpoint);
        $authorizationHeader = $this->getAuthorizationHeader($apiNickname);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $authorizationHeader,
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
    
    protected function buildPaymentPlan(Request $request){
        $preference = $this->buildPrefenrceObjFromRequest($request);
        $cycles = [];
        for($cycleIndex=1; $cycleIndex<=3; $cycleIndex++){
            $cycle = $this->buildCycleFromRequest($request, $cycleIndex);
            if($cycle) $cycles[] = $cycle;
        }
        $plan = $this->buildPlanByRequestWithObjects($request, $preference, $cycles);
        return $plan;
    }
    
    protected function buildPrefenrceObjFromRequest(Request $request){
        $preference = new PlanPaymentPreferences();
        $preference
                ->setAutoBillOutstanding($request->preference_autocollect=="TRUE"?true:false)
                ->setPaymentFailureThreshold((int)$request->preference_failure_threshold)
                ->setSetupFee($request->preference_setup_fee)
                ->setSetupFeeFailureAction($request->preference_failure_action);
        return $preference;
    }
    protected function buildCycleFromRequest(Request $request, $index){
        $cycle = null;
        $price = $request->input("cycle{$index}_price");
        $unit = $request->input("cycle{$index}_unit");
        $sequence = (int)$request->input("sequence{$index}");
        $type = $request->input("cycle{$index}_tenure");
        $length = (int)$request->input("cycle{$index}_length");
        $cycles = (int)$request->input("cycle{$index}_repeat");
        $used = $request->input("cycle{$index}_used")=="TRUE"?true:false;
        if($used){
            $cycle = new BillingCycle();
            $cycle->setFixedPricePerCycle($price)
                    ->setFrequency($unit, $length)
                    ->setSequence($sequence)
                    ->setTenureType($type)
                    ->setTotalCycles($cycles);
        }
        return $cycle;
    }
    protected function buildPlanByRequestWithObjects(
            Request $request, PlanPaymentPreferences $preference, $cycles){
        
        $id = $request->input('plan_id');
        $name = $request->input('plan_name');
        $description = $request->input('plan_description');
        
        $plan = new Plan();
        $plan->setPaymentPlanPreference($preference);
        foreach($cycles as $cycle){
            $plan->addBillingCycleObject($cycle, false);
        }
        $plan->setProductId($id);
        $plan->setName($name);
        $plan->setDescription($description);
        
        return $plan;
    }
}

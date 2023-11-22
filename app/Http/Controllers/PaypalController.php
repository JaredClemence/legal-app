<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Http\Controllers\Paypal\Classes\AuthToken;

enum BillingType
{
    case TRIAL;
    case REGULAR;
}
enum BillingIntervalUnit{
    case DAY;
    case WEEK;
    case YEAR;
    case MONTH;
}

class PaypalController extends Controller
{
    private $auths = [];
    
    public function payment(Request $request){
      try{
        $clientId = env("PAYPAL_JRC_CLIENTID");
        $secret = env("PAYPAL_JRC_SECRET");
        $returnUrl = route('paypal_success');
        $cancelUrl = route('paypal_cancel');
        $order = $this->processOrderForImmediatePayment(
          $clientId,
          $secret,
          "JRC_FIXED_PRICE_800",
          "Probate Champions Program Membership (1 year)",
          1,
          800,
          "Jared R. Clemence, Esq.",
          $returnUrl,
          $cancelUrl
        );
        //dd($order);
        if( $order["status"] == "PAYER_ACTION_REQUIRED"){
            $redirectUrl = null;
            foreach($order["links"] as $linkRef){
              if($linkRef["rel"] == "payer-action"){
                $redirectUrl = $linkRef["href"];
              }
            }
            if( $redirectUrl === null ){
              throw new \Exception("[0x02] Paypal did not return redirect link.");
            }
            return redirect()->away($redirectUrl);
          }else{
            throw new \Exception("[0x01] Site failed to create order.");
          }
      }catch(\Exception $e){
        //log bad data
        return response()
            ->json([
                'code'      =>  500,
                'message'   =>  'Server-side error. Please contact jclemence@ch-law.com to report this error. Problem requires programmer support. Error: ' . $e->getMessage()
            ], 500);
      }
    }
    public function establishPaymentPlan(Request $request){
      try{
        $clientId = env("PAYPAL_JRC_CLIENTID");
        $secret = env("PAYPAL_JRC_SECRET");
        $returnUrl = route('paypal_success');
        $cancelUrl = route('paypal_cancel');
        $plan_id = $request->plan_id; // ISSUE: Unable to request by id - "P-7CX04065LF029763WMVNJ3PA";
        $plan = $this->getPlanById($clientId, $secret, $plan_id);
        $plan = $this->createBillingPlan(
          $clientId,
          $secret,
          "SKU0001",
          "Probate Champions Program Membership (Payment Plan)",
          1,
          "0.00",
          12,
          "99.00",
          $returnUrl,
          $cancelUrl,
          "Probate Champions Program Membership (Payment Plan)",
          $maxFailAttempts=3
        );
        //dd($plan);
        return view('testPayPlan',[
          "plan_id"=>$plan_id,
          "client_id"=>$clientId
        ]);
      }catch(\Exception $e){
        //log bad data
        return response()
            ->json([
                'code'      =>  500,
                'message'   =>  'Server-side error. Please contact jclemence@ch-law.com to report this error. Problem requires programmer support. Error: ' . $e->getMessage()
            ], 500);
      }
    }
    public function processOrderForImmediatePayment($clientId, $secret, $sku, $itemName, $itemQuantity, $price, $brandName, $returnUrl, $cancelUrl, $description="", $softDescriptor="661-832-1300"){
      $provider = $this->getProvider($clientId,$secret);
      if($description==null) $description=$itemName;

        $data = [
              "intent" => "CAPTURE",
              "purchase_units" => [
                [
                  "description"=>$description,
                  "soft_descriptor"=>$softDescriptor,
                  "amount" => [
                    "currency_code" => "USD",
                    "value" => $price,
                    "breakdown"=>[
                      "item_total"=>[
                        "currency_code"=>"USD",
                        "value"=>$price
                      ],
                    ]
                  ],
                  "items"=>[
                    [
                        "name"=>$itemName,
                        "quantity"=>$itemQuantity,
                        "unit_amount"=>[
                          "currency_code"=>"USD",
                          "value"=>$price
                        ],
                        "description"=>$description,
                        "sku"=>$sku,
                        "category"=>"DIGITAL_GOODS"
                    ]
                  ],
                ],
              ],
              "payment_source"=>[
                "paypal"=>[
                  "experience_context"=>[
                    "brand_name"=>$brandName,
                    "user_action"=>"PAY_NOW",
                    "payment_method_preference"=>"IMMEDIATE_PAYMENT_REQUIRED",
                    "return_url"=>$returnUrl,
                    "cancel_url"=>$cancelUrl
                  ]
                ]
              ]
          ];
        $order = $provider->createOrder($data);
        return $order;
    }
    public function createBillingPlan($clientId, $secret, $sku, $itemName, $trialWeeksCount, $trialWeeksPrice, $paidWeeksCount, $paidWeeksPrice, $returnUrl, $cancelUrl, $description="", $maxFailAttempts=3){
      $provider = $this->getProvider($clientId,$secret);
      if($description==null) $description=$itemName;
      $product = $this->getOrCreateProduct($provider, $sku, $itemName, $description);
      $data = [
            "product_id"=>$product["id"],
            "name"=>$product["name"],
            "description"=>$product["description"],
            "billing_cycles"=>[
               $this->makeBillingCycle(1, BillingType::TRIAL->name, 4, BillingIntervalUnit::WEEK->name, 1, "0.00"),
               $this->makeBillingCycle(2, BillingType::REGULAR->name, 4, BillingIntervalUnit::WEEK->name, 12, "99.00")
            ],
            "payment_preferences"=>[
              "auto_bill_outstanding"=>TRUE,
              "setup_fee_failure_action"=>"CONTINUE",
              "payment_failure_threshold"=>3,
            ]
        ];
        //dd($data);
        $plan = $provider->createPlan($data);
        if($plan["status"]!="ACTIVE"){
            $plan_id = $plan["id"];
            $plan = $provider->activatePlan($plan_id);
        }
        return $plan;
    }
    public function success(Request $request){
       //dd($request->token);
       dd($request->PayerID);
       //http://127.0.0.1:8000/paypal/success?token=5V599905AE532421Y&PayerID=PW6VFZQEMFEQW
       return view('paypal.success');
    }
    public function cancel(Request $request){
      return view('paypal.cancel');
    }
    protected function getProvider($clientId, $secret){
      $config = config('paypal');
      $mode = env('PAYPAL_MODE');
      $config[$mode]['client_id']=$clientId;
      $config[$mode]['client_secret']=$secret;
      $provider = new PayPalClient($config);
      $provider->getAccessToken();
      return $provider;
    }

    private function makeBillingCycle($sequence, $type, $intervalCount, $intervalUnit, $cycles, $intervalPrice){
      switch( $type ){
        case "TRIAL":
        case "REGULAR":
          break;
        default:
          throw new \Exception("0x03 Invalid Billing Cycle type submitted. Untable to process type '$type'.");
      }
      switch($intervalUnit){
        case "WEEK":
        case "DAY":
        case "YEAR":
        case "MONTH":
          break;
          default:
            throw new \Exception("0x04 Invalid Billing Interval Unit type submitted. Untable to process type '$intervalUnit'.");
      }
      return [
        "sequence"=>$sequence,
        "tenure_type"=>$type,
        "frequency"=>[
          "interval_unit"=>$intervalUnit,
          "interval_count"=>$intervalCount
        ],
        "total_cycles"=>$cycles,
        "pricing_scheme"=>[
          "fixed_price"=>[
            "currency_code"=>"USD",
            "value"=>$intervalPrice
          ]
        ],
      ];
    }
    private function getOrCreateProduct($provider, $sku, $productName, $productDescription){
      $product = $provider->showProductDetails($sku);
      if( isset($product["error"]) && $product["error"]["name"]=="RESOURCE_NOT_FOUND"){
        $product = $this->createProduct($provider, $sku, $productName, $productDescription);
      }
      return $product;
    }
    private function createProduct($provider, $sku, $productName, $productDescription){
      $data = [
        "id"=>$sku,
        "name"=>$productName,
        "description"=>$productDescription,
        "type"=>"SERVICE",
        "category"=>"CONSULTING_SERVICES" //"LEGAL_SERVICES_AND_ATTORNEYS"
      ];

      $request_id = 'create-product-'.time();

      $product = $provider->createProduct($data, $request_id);
      return $product;
    }
    private function getPlanById( $clientId, $secret, $plan_id ){
      $provider = $this->getProvider($clientId,$secret);
      $plan_id="NAD";
      $plan = $provider->showPlanDetails($plan_id);
      if( isset( $plan["error"] )){
        $plan = null;
      }
      return $plan;
    }
    private function subscribeToPaymentPlan($clientId, $secret, $plan){
      $provider = $this->getProvider($clientId, $secret);
      $data = [
        "plan_id"=>$plan["id"],
        //"start_time"=>"2018-11-01T00:00:00Z",
        //"quantity"=>"20",
        /*"shipping_amount"=>[
          "currency_code"=>"USD",
          "value"=>"10.00"
        ],*/
        /*
        "subscriber"=>[
          "name"=>[
            "given_name"=>"John",
            "surname"=>"Doe"
          ],
          "email_address"=>"customer@example.com",
          "shipping_address"=>[
            "name"=>[
              "full_name"=>"John Doe"
            ],
            "address"=>[
              "address_line_1"=>"2211 N First Street",
              "address_line_2"=>"Building 17",
              "admin_area_2"=>"San Jose",
              "admin_area_1"=>"CA",
              "postal_code"=>"95131",
              "country_code"=>"US"
            ]
          ]
        ], */
        "application_context"=> [
          "brand_name"=>"Jared R. Clemence, Esq.",
          "locale"=>"en-US",
          "shipping_preference"=>"SET_PROVIDED_ADDRESS",
          "user_action"=>"SUBSCRIBE_NOW",
          "payment_method"=>[
            "payer_selected"=>"PAYPAL",
            "payee_preferred"=>"IMMEDIATE_PAYMENT_REQUIRED"
          ],
          "return_url"=>"https://example.com/returnUrl",
          "cancel_url"=>"https://example.com/cancelUrl"
        ]
      ];

      $subscription = $provider->createSubscription($data);
      dd($subscription);
    }
    protected function getClientAndSecret($index){
        $client_id = null;
        $secret = null;
        
        switch($index){
            case "jaredclemence.com":
                $client_id = env("PAYPAL_JRC_CLIENTID");
                $secret = env("PAYPAL_JRC_SECRET");
                break;
        }
        if($client_id==null){
            throw new \Exception("0x06 No valid paypal client index provided.");
        }
        return [$client_id, $secret];
    }
    protected function getProviderForPaypalClient($clientName) {
        list($clientId, $secret)=$this->getClientAndSecret($clientName);
        $provider = $this->getProvider($clientId, $secret);
        return $provider;   
    }
    
    protected function getCurlEndpoint($endpoint, $queryString=null){
        $mode = $this->getModeFromEnvironment();
        $urlBase = env('PAYPAL_ENDPOINT_'.strtoupper($mode));
        
        $url = $urlBase . $endpoint;
        if($queryString){
            $url .= "?" . $queryString;
        }
        return $url;
    }
    
    protected function getAuthorizationHeader($nickname){
        if(!isset($this->auths[$nickname])){
            $this->loadAuth($nickname);
        }
        $auth = $this->auths[$nickname];
        $type = $auth->type;
        $token = $auth->token;
        return "Authorization: $type $token";
    }
    
    protected function loadAuth($nickname){
        $endpoint = "/v1/oauth2/token";
        list( $client_id, $secret ) = $this->getCredentialsForNickname( $nickname );
        $url = $this->getCurlEndpoint($endpoint);
        //dd( compact('client_id','secret','url'));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PASSWORD, $secret );
        curl_setopt($ch, CURLOPT_USERNAME, $client_id );
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $output = json_decode(curl_exec($ch));
        curl_close($ch);
        
        $type = $output->token_type;
        $token = $output->access_token;
        $auth = new AuthToken($type, $token);
        $this->auths[$nickname] = $auth;
    }
    
    protected function getCredentialsForNickname( $nickname ){
        $mode = $this->getModeFromEnvironment();
        $key = $this->getNicknameKey($nickname);
        $clientIndex = strtoupper("PAYPAL_{$key}_{$mode}_CLIENTID");
        $secretIndex = strtoupper("PAYPAL_{$key}_{$mode}_SECRET");
        $client_id = env($clientIndex);
        $secret = env($secretIndex);
        return [$client_id, $secret];
    }
    
    private function getModeFromEnvironment(){
        $mode = env('PAYPAL_MODE', 'sandbox');
        return $mode;
    }
    
    private function getNicknameKey($nickname){
        $key = "";
        switch($nickname){
            case 'jared':
                $key = "JRC";
                break;
            default:
                throw new \Exception("Invalid client nickname for paypal client.");
        }
        return $key;
    }
}

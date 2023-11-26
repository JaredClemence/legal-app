<?php

namespace App\Http\Controllers;

use App\Models\ProbateChampionMembership;
use Illuminate\Http\Request;
use App\Providers\Service\ProductServiceProvider;
use App\Http\Controllers\Paypal\Factories\ProductFactory;

class ProbateChampionMembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $unencryptedQueryDataArray = $request->all();
        $unencryptedQueryDataString = serialize($unencryptedQueryDataArray);
        $viewData = [
            "products"=>$this->getAllProducts(),
            "payment_type"=>$request->payment_type,
            "encryptedSerializedData"=>$this->encrypt($unencryptedQueryDataString)
        ];
        return view('pcp.internal.create', $viewData);
    }
    
    /**
     * 
     */
    public function createFixed(Request $request){
        $decryptedQueryData = $this->extractPhpDataFromQuery($request);
        $amount = $decryptedQueryData->amount;
        $product_id = $decryptedQueryData->product_id;
        
        $product = $this->getProductById($product_id);
        
        dd(compact('decryptedQueryData','product'));
        
    }
    
    /**
     * 
     */
    public function createSubscription(Request $request){
        $decryptedQueryData = $this->extractPhpDataFromQuery($request);
        $amountPerCycle = $decryptedQueryData->amount;
        $unitsInCycle = $decryptedQueryData->cycle_length;
        $cycleUnits = $decryptedQueryData->cycle_units;
        $payPeriods = $decryptedQueryData->payment_period;
        $freePeriods = $decryptedQueryData->free_periods;
        $productId = $decryptedQueryData->product_id;
        
        $product = $this->getProductById($id);
        
        dd(compact('decryptedQueryData','product'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProbateChampionMembership $probateChampionMembership)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProbateChampionMembership $probateChampionMembership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProbateChampionMembership $probateChampionMembership)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProbateChampionMembership $probateChampionMembership)
    {
        //
    }
    
    private function encrypt($rawText){
        $opensslParams = $this->getOpenSSLParams();
        extract($opensslParams, EXTR_OVERWRITE);
        $enc = openssl_encrypt($rawText, $cypher, $pass, 0, $iv);
        $dec = openssl_decrypt($enc, $cypher, $pass, 0, $iv);
        assert($dec==$rawText);
        assert($enc!=$dec);
        return $enc;
    }

    private function makeIv($cypher, $pass) {
        $hash = hash('sha256', $cypher.$pass );
        $iv = substr($hash,0,16);
        return $iv;
    }

    private function decrypt($encryptedData) {
        $opensslParams = $this->getOpenSSLParams();
        extract($opensslParams, EXTR_OVERWRITE);
        $dec = openssl_decrypt($encryptedData, $cypher, $pass, 0, $iv);
        return $dec;
    }

    private function getOpenSSLParams() {
        $cypher = env('CYPHER_ALGO');
        $pass = env('OPENSSL_PASSPHRASE');
        $iv = $this->makeIv($cypher, $pass);
        return compact('cypher','pass','iv');
    }

    private function extractPhpDataFromQuery(Request $request) {
        if(!isset($request->sec_token)){
            abort(403);
        }
        $encryptedData = $request->sec_token;
        $rawDataString = $this->decrypt($encryptedData);
        $data = (object) unserialize($rawDataString);
        return $data;
        
    }
    
    private function getAllProducts(){
        $provider = new ProductServiceProvider('jared');
        $paypalResponse = $provider->all();
        $jsonCollection = collect($paypalResponse->products);
        $productCollection = $jsonCollection->map( function ($productJson) {
            return ProductFactory::make($productJson);
        });
        return $productCollection;
    }

    private function getProductById($id) {
        $provider = new ProductServiceProvider('jared');
        $productJson = $provider->id($id);
        $product = ProductFactory::make($productJson);
        return $product;
    }

}

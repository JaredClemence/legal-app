<?php

namespace App\Http\Controllers\Paypal;

use Illuminate\Http\Request;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\Paypal\Classes\Curl;
use App\Http\Controllers\Paypal\Factories\ProductFactory;

class ProductController extends PaypalController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $apiNickname)
    {
        $result = $this->getAll($apiNickname);
        $products = [];
        foreach($result->products as $productJson){
            $product = ProductFactory::make($productJson);
            $products[] = $product;
        }
        return view('paypal.products.index', compact('apiNickname', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $apiNickname)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $apiNickname)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $apiNickname, string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $apiNickname, string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $apiNickname, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $apiNickname, string $id)
    {
        //
    }
    
    protected function getAll($nickname){
        $authString = $this->getAuthorizationHeader($nickname);
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
}

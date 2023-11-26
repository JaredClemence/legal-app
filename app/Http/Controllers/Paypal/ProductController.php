<?php

namespace App\Http\Controllers\Paypal;

use Illuminate\Http\Request;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\Paypal\Classes\Curl;
use App\Http\Controllers\Paypal\Factories\ProductFactory;
use App\Http\Controllers\Paypal\Classes\Product;
use App\Providers\Service\ProductServiceProvider;

class ProductController extends PaypalController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $apiNickname)
    {
        $provider = new ProductServiceProvider($apiNickname);
        $result = $provider->all();
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
        $typeOptions = Product::$typeChoices;
        $categoryOptions = Product::$categoryOptions;
        array_unshift($typeOptions, "NONE");
        array_unshift($categoryOptions, "NONE");
        return view('paypal.products.create', compact('apiNickname','typeOptions','categoryOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $apiNickname)
    {
        $product = new Product();
        $product->setId($request->product_id);
        $product->setType($request->product_type);
        $product->setName($request->product_name);
        $product->setDescription($request->product_description);
        $product->setCategory($request->product_category);
        $this->createProduct($apiNickname, $product);
        $token = $request->token;
        return redirect( route('paypal.product.list', compact('apiNickname','token') ) );
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
    
    public function createProduct($nickname, Product $product) {
        $provider = new ProductServiceProvider($nickname);
        $jsonResult = $provider->save($product);
        return $jsonResult;
    }
}

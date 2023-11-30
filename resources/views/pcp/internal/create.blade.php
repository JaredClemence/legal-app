@extends('layouts.basic')
@section('title','Payment Plan - Create New')
@section('body')
<h1>Available Products</h1>
<table>
    <tr>
        <th>Product Id</th>
        <th>Name</th>
        <th>Description</th>
    </tr>
    @foreach( $products as $product )
        <tr>
            <td>{{$product->id}}</td>
            <td>{{$product->name}}</td>
            <td>{{$product->description}}</td>
        </tr>
    @endforeach
</table>
<h1>Create Plan Form</h1>
<form action='{{route('champions.new.links')}}' method='GET'>
    @csrf
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'Product Id',
        'id'=>'product_id',
        'name'=>'product_id',
        'type'=>'text'
    ])
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'Purchase Amount',
        'id'=>'amount',
        'name'=>'amount',
        'type'=>'number'
    ])
    
    @include('bootstrap.form.select',[
        "name"=>"payment_type",
        "label_text"=>"Payment Type",
        "options"=>[
            "SINGLE",
            "SUBSCRIPTION",
        ]
    ])
    
    @include('bootstrap.form.select',[
        "name"=>"cycle_units",
        "label_text"=>"Cycle Units",
        "options"=>[
            "DAY",
            "WEEK",
            "MONTH",
            "YEAR"
        ]
    ])
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'Cycle Length',
        'id'=>'cycle_length',
        'name'=>'cycle_length',
        'type'=>'number'
    ])
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'Payment Periods',
        'id'=>'payment_period',
        'name'=>'payment_period',
        'type'=>'number'
    ])
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'Free Periods',
        'id'=>'free_periods',
        'name'=>'free_periods',
        'type'=>'number'
    ])
    @include('bootstrap.form.control',
    [
        'label_text'=>'Success Url',
        'id'=>'return_url',
        'name'=>'return_url',
        'type'=>'text'
    ])
    @include('bootstrap.form.control',
    [
        'label_text'=>'Cancel Url',
        'id'=>'cancel_url',
        'name'=>'cancel_url',
        'type'=>'text'
    ])
    <button type="submit">Submit</button>
</form>
@if( isset( $payment_type ) )
<div>
    <h1>Link</h1>
    
    @if($payment_type=='SINGLE')
    @php 
    $link = route('champions.new.single', ["sec_token"=>$encryptedSerializedData]);
    $link2 = route('champions.order.link', ["sec_token"=>$encryptedSerializedData]);
    @endphp
    <p>
        Redirect Link Text: <a href="{{$link}}">{{$link}}</a><br/>
        Paypal JSON Link: <a href="{{$link2}}">{{$link2}}</a>
    </p>
    @elseif($payment_type=='SUBSCRIPTION')
    @php( $link = route('champions.new.subscription', ["sec_token"=>$encryptedSerializedData]))
    <p>
        Link Text: <a href="{{$link}}">{{$link}}</a>
    </p>
    @endif
</div>
@endif
@endsection
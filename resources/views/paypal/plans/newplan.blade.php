@extends('layouts.basic')
@yield('title','Payment Plan - Create New')
@section('body')

<h1>Create Plan Form</h1>
<form action='' method='POST'>
    @csrf
    @include('bootstrap.form.control', 
    [ 'label_text'=>"Plan Id", 'id'=>"plan_id", 'name'=>"plan_id" ])
    
    @include('bootstrap.form.control', [ 'label_text'=>"Plan Name", 'id'=>"plan_name", 'name'=>"plan_name" ])
    @include('bootstrap.form.textarea', 
    [ 
    'label_text'=>"Plan Description", 
    'id'=>"plan_description", 
    'name'=>"plan_description" 
    ])
    @include('bootstrap.form.control', 
    [ 
    "type"=>"hidden", 'label_text'=>"Preference - Autocollect Outstanding", 'id'=>"preference_autocollect", 'name'=>"preference_autocollect", "value"=>"TRUE" ])
    @include('bootstrap.form.control', 
    [ "type"=>"number", 'label_text'=>"Preference - Payment Failure Threshhold", 'id'=>"preference_failure_threshold", 'name'=>"preference_failure_threshold", "value"=>"0" ])
    @include('bootstrap.form.control', 
    [ "type"=>"text", 'label_text'=>"Preference - Setup Fee", 'id'=>"preference_setup_fee", 'name'=>"preference_setup_fee", "value"=>"0.00" ])
    
    @include('bootstrap.form.radio',
    [
        "radio_group_label"=>"Preference - Failure Action",
        "name"=>"preference_failure_action",
        "options"=>[
        (object)["label_text"=>"Continue","value"=>"CONTINUE", "is_checked"=>FALSE],
        (object)["label_text"=>"Cancel","value"=>"CANCEL", "is_checked"=>TRUE],
        ]
    ])
    @include('paypal.plans.billing_cycle')
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection

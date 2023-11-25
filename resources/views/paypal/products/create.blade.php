@extends('layouts.basic')
@section('title','Product - Create New')
@section('body')

<h1>Create Product Form</h1>
<form action='{{route('paypal.product.new',["apiNickname"=>$apiNickname])}}' method='POST'>
    @csrf
    @include('bootstrap.form.control', 
    [ 'label_text'=>"Product Id", 'id'=>"product_id", 'name'=>"product_id" ])
    
    @include('bootstrap.form.select', 
    [ 'label_text'=>"Product Type *", 'name'=>"product_type", "options"=>$typeOptions ])
    @include('bootstrap.form.control', [ 'label_text'=>"Product Name *", 'id'=>"product_name", 'name'=>"product_name" ])
    @include('bootstrap.form.textarea', 
    [ 
    'label_text'=>"Product Description", 
    'id'=>"product_description", 
    'name'=>"product_description" 
    ])
    @include('bootstrap.form.select', 
    [ 'label_text'=>"Product Category", 'name'=>"product_category", "options"=>$categoryOptions ])
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection

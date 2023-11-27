@extends('layouts.basic')
@section('title','{{$product->name}}')
@section('body')
<h1>{{$product->name}}</h1>
<p>{{$product->description}}</p>
<form action="{{route('champions.new.single')}}" method="POST">
    @csrf
    
    @include('bootstrap.form.control',
    [
        'label_text'=>'',
        'id'=>'sec_token',
        'name'=>'sec_token',
        'type'=>'hidden',
        'value'=>$sec_token
    ])
    <button type="submit">Pay Now</button>
</form>
@endsection
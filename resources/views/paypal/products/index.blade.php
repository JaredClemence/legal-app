@extends('layouts.basic')

@section('title', 'Products List')
@section('body')
<h1>Product List</h1>
<style type=""css">
    table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 10px;
}
    </style>
<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Type</th>
    </tr>
    @foreach($products as $product)
    <tr>
        <td>{{$product->id}}</td>
        <td>{{$product->name}}</td>
        <td>{{$product->description}}</td>
        <td>{{$product->category}}</td>
        <td>{{$product->type}}</td>
    </tr>   
    @endforeach
</table>
    <div style="padding:20px;">
        <form action="{{route('paypal.product.new',compact('apiNickname'))}}" method="GET">
            @csrf
            <button type="submit">Create New</a>
        </form>
    </div>
@endsection

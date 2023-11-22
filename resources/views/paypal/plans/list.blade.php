@extends('layouts.basic')
@section('title', 'Paypal Plan List')
@section('body')
<h1>Active Paypal Plans List</h1>
<table>
    <tr>
        <td>Id</td>
        <td>Status</td>
        <td>Name</td>
        <td>Description</td>
        <td>Deactivate Link</td>
    </tr>
    @php
        $count = 0;
    @endphp
    @if(count($plans)>0)
        @foreach($plans as $plan)
            @if($plan->status=="ACTIVE")
            @php
            $count++;
            @endphp
            <tr>
                <td>{{$plan->id}}</td>
                <td>{{$plan->status}}</td>
                <td>{{$plan->name}}</td>
                <td>{{$plan->description}}</td>
                <td><a href="{{route('paypal.plans.deactivate', ["id"=>$plan->id])}}">Deactivate</a></td>
            </tr>
            @endif
        @endforeach
    @endif
    @if($count == 0)
        <tr>
            <td><em><strong>None</strong></em></td>
        <td colspan="4">No Active plans to display at this time.</td>
    </tr>
    @endif
</table>
<div>
<a href="{{route('paypal.plans.new', compact('apiNickname'))}}">Create New Plan</a>
</div>
@endsection
@extends('mail.html-base')

@section('content')
<h1>Kern County Bar: Probate Section</h1>
<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">Dear {{$recipientName}}:</p>

<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">
    Please confirm that you would like to receive mail from the Kern County Probate 
Section. We will attempt to confirm your interest over the next four months. If
we do not receive an interaction that confirms your interest, we will drop your 
email from our list.
</p>

<h1>Confirm Now</h1>

<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">
    Confirm at any time by using this link: <a href="{{$confirmLink}}">Yes, Send me probate section emails.</a>
</p>

<h1>Unsubscribe</h1>
<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">
    To prevent further emails, use this link: <a href="{{$unsubscribeNow}}">No, I'm not interested in the Probate Section.</a>
</p>

<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">
    Kind regards,</p>

<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">Jared R. Clemence</p>
<p style="margin-bottom: 15px;
    font-size: 15px;
    line-height: 25px;">Kern County Probate Section<p/>
    
@endsection

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Payment Integration</title>
</head>
<body>
  <h1>Paypal Test</h1>
  <h2>Item: Test Item</h2>
  <form action="{{route('paypal_payment')}}" method="post">
      @csrf
      <input type="hidden" name="item_price" value="800.00" />
      <button type="submit">Pay With PayPal</button>
  </form>
</body>
</htlm>

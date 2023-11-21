<!DOCTYPE html>
<head>
   <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices. -->
</head>
<body>
  <script src="https://www.paypal.com/sdk/js?client-id=Ac16FWjP9MdgS95WoIaxkFMPugvFLOtURMBezv7lV8DMUmDxiXnfHZybO6dTHy3-sYt0IuZcowQfPOWe&intent=subscription&vault=true"></script> // Add your client_id
     <div id="paypal-button-container"></div>
      <script>
       paypal.Buttons({
        createSubscription: function(data, actions) {
          return actions.subscription.create({
           'plan_id': 'P-9AV89919R5551893CMVODKMY' // Creates the subscription
           });
         },
         onApprove: function(data, actions) {
           alert('You have successfully subscribed to ' + data.subscriptionID); // Optional message given to subscriber
         },
         onError: function(e){
           console.error("Error",e);
         },
       }).render('#paypal-button-container'); // Renders the PayPal button
      </script>
  </body>
</html>

// Create a Stripe client.
var stripe = Stripe($("#stripe_key").html());

// Create an instance of Elements.
var elements = stripe.elements();

if($(document).find('.stripe_request').size()>0 && $(document).find('.stripe_request').css('display') != "none"){
	request_load();
}

function request_load(){
	
	var paymentRequest = stripe.paymentRequest({
	  country: $("#request_country").html(),
	  currency: $("#request_currency").html(),
	  total: {
		label: 'Spiriteo',
		amount: Number($("#request_amount").html()),
	  },
	  requestPayerName: true,
	  requestPayerEmail: true,
	});
	
	var prButton = elements.create('paymentRequestButton', {
	  paymentRequest: paymentRequest,
	});
	

	// Check the availability of the Payment Request API first.
	paymentRequest.canMakePayment().then(function(result) {
	  if (result) {
		  $("#request_error").hide();
		prButton.mount('#payment-request-button');
	  } else {
		document.getElementById('payment-request-button').style.display = 'none';
		 $("#request_error").html('Paiment impossible, merci de sélectionner un autre mode de paiement.');
		  $("#request_error").show();
	  }
	});
	
/*	paymentRequest.on('token', function(ev) {
	  // Send the token to your server to charge it!
		nxMain.ajaxRequest("/paymentrequest/save_cart", {source:ev.token.id}, function(t) {
						if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = $("#request_return").html();
						}
					});	
	});*/
	
paymentRequest.on('paymentmethod', function(ev) {
  // Confirm the PaymentIntent without handling potential next actions (yet).
  stripe.confirmCardPayment(
    $("#client_secret").html(),
    {payment_method: ev.paymentMethod.id},
    {handleActions: false}
  ).then(function(confirmResult) {
    if (confirmResult.error) {
      // Report to the browser that the payment failed, prompting it to
      // re-show the payment interface, or show an error message and close
      // the payment interface.
      ev.complete('fail');
    } else {
      // Report to the browser that the confirmation was successful, prompting
      // it to close the browser payment method collection interface.
      ev.complete('success');
      // Check if the PaymentIntent requires any actions and if so let Stripe.js
      // handle the flow. If using an API version older than "2019-02-11" instead
      // instead check for: `paymentIntent.status === "requires_source_action"`.
      if (confirmResult.paymentIntent.status === "requires_action") {
        // Let Stripe.js handle the rest of the payment flow.
        stripe.confirmCardPayment($("#client_secret").html()).then(function(result) {
          if (result.error) {
            // The payment failed -- ask your customer for a new payment method.
			  $("#request_error").html(response.error.message);
          } else {
            // The payment has succeeded.
			   nxMain.ajaxRequest("/paymentrequest/save_cart", {source:confirmResult.paymentIntent.id}, function(t) {
				if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = $("#request_return").html();
						}		
			  		
				});
          }
        });
      } else {
        // The payment has succeeded.
		   // The payment has succeeded.
			   nxMain.ajaxRequest("/paymentrequest/save_cart", {source:confirmResult.paymentIntent.id}, function(t) {
				if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = $("#request_return").html();
						}		
			  		
				});
      }
    }
  });
});
	

}

function handleServerResponse(response) {
  if (response.error) {
	 // console.log(response.error.message);
    // Show error from server on payment form
	  var errorElement = document.getElementById('card-errors');
      errorElement.textContent = response.error.message;
	  $(document).find('#payment-form-stripe .btn').show();//.removeAttr("disabled");
	  $(document).find('.btn-cart-stripe').show();//removeAttr("disabled");
  } 
}
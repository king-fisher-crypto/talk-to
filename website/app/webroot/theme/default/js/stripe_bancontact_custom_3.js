// Create a Stripe client.
var stripe = Stripe($("#stripe_key").html());

// Create an instance of Elements.
var elements = stripe.elements();

if($(document).find('.stripe_bancontact').size()>0 && $(document).find('.stripe_bancontact').css('display') != "none"){
	bancontact_load();
}

function bancontact_load(){
	
	stripe.confirmBancontactPayment(
	  $("#client_secret").html(),
	  {
		payment_method: {
		  billing_details: {
			name: $("#bancontact_firstname").html()
		  }
		},
		return_url: $("#bancontact_return").html(),
	  }
	).then(function(result) {
	  if (result.error) {
		// Inform the customer that there was an error.
		  $("#bancontact_error").html(result.error.message);
	  }
	});
	
	
}
stripe.retrievePaymentIntent($("#client_secret").html()).then(function(response) {
  if (response.error) {
    // Handle error here
	  $("#bancontact_error").html(response.error.message);
  } else if (response.paymentIntent && response.paymentIntent.status === 'succeeded') {
    // Handle successful payment here
	  nxMain.ajaxRequest("/paymentbancontact/save_cart", {source:response.paymentIntent.id}, function(t) {
				if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = $("#bancontact_return").html();
						}		
			  		
				});
  }
});
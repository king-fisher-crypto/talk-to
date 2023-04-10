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
	
	paymentRequest.on('token', function(ev) {
	  // Send the token to your server to charge it!
		nxMain.ajaxRequest("/paymentrequest/save_cart", {source:ev.token.id}, function(t) {
						if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = $("#request_return").html();
						}
					});	
	/*  fetch('/charges', {
		method: 'POST',
		body: JSON.stringify({token: }),
		headers: {'content-type': 'application/json'},
	  })
	  .then(function(response) {
		if (response.ok) {
		  // Report to the browser that the payment was successful, prompting
		  // it to close the browser payment interface.
		  ev.complete('success');
		} else {
		  // Report to the browser that the payment failed, prompting it to
		  // re-show the payment interface, or show an error message and close
		  // the payment interface.
		  ev.complete('fail');
		}
	  });*/
	});
	
	
	/*
	
	stripe.createSource({
	  type: 'bancontact',
	  amount: $("#bancontact_amount").html(),
	  currency: $("#bancontact_currency").html(),
	  statement_descriptor: $("#bancontact_desc").html(),
	  owner: {
		name: $("#bancontact_firstname").html(),
	  },
	  redirect: {
		return_url: $("#bancontact_return").html(),
	  },
	}).then(function(result) {
	  // handle result.error or result.source
		//console.log(result.error);
		if(result.error){
			$("#bancontact_error").html(result.error.message);
		}else{
			//redirect vers source url
			console.log(result.source);

			
		}

	});*/
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
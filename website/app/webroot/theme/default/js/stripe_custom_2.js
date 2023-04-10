// Create a Stripe client.
var stripe = Stripe($("#stripe_key").html());

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
  base: {
    color: '#32325d',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '14px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {
  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
});

// Handle form submission.
var form = document.getElementById('payment-form-stripe');
form.addEventListener('submit', function(event) {
  event.preventDefault();

 /* stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the user if there was an error.
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server.
      stripeTokenHandler(result.token);
    }
  });*/
	$(document).find('#payment-form-stripe .btn').hide();//attr('disabled','disabled');
	stripe.createPaymentMethod(
	  'card',
	  card
	).then(function(result) {
	  if (result.error) {
		// Show error in payment form
		  var errorElement = document.getElementById('card-errors');
      		errorElement.textContent = result.error.message;
		  $(document).find('#payment-form-stripe .btn').show();//removeAttr("disabled");
		  //console.log('error');
	  } else {
		  nxMain.ajaxRequest("/paymentstripe/confirm_payment", {payment_method_id:result.paymentMethod.id, cardholdername:$('input[name=cardholdername]').val()}, function(t) {
					//console.log(t);
					handleServerResponse(t);
			  		
				});	
		// Send paymentMethod.id to server
		/*fetch('/paymentstripe/confirm_payment', {
		  method: 'POST',
		  headers: {
			'Content-Type': 'application/json'
		  },
		  body: JSON.stringify({
			payment_method_id: result.paymentMethod.id
		  })
		}).then(function(result) {
		  // Handle server response (see Step 3)
		  result.json().then(function(json) {
			handleServerResponse(json);
		  })
		});*/
	  }
	});
	
});

// Submit the form with the token ID.
/*function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById('payment-form-stripe');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  // Submit the form
  form.submit();
}*/

function handleServerResponse(response) {
  if (response.error) {
	 // console.log(response.error.message);
    // Show error from server on payment form
	  var errorElement = document.getElementById('card-errors');
      errorElement.textContent = response.error.message;
	  $(document).find('#payment-form-stripe .btn').show();//.removeAttr("disabled");
	  $(document).find('.btn-cart-stripe').show();//removeAttr("disabled");
  } else if (response.requires_action) {
    // Use Stripe.js to handle required card action
    handleAction(response);
  } else {
    // Show success message
	  var form = document.getElementById('payment-form-stripe');
	  var hiddenInput = document.createElement('input');
	  hiddenInput.setAttribute('type', 'hidden');
	  hiddenInput.setAttribute('name', 'stripeToken');
	  hiddenInput.setAttribute('value', response.id);
	  form.appendChild(hiddenInput);

	  // Submit the form
	  form.submit();
  }
}

function handleAction(response) {
  stripe.handleCardAction(
    response.payment_intent_client_secret
  ).then(function(result) {
    if (result.error) {
      // Show error in payment form
		var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
		$(document).find('#payment-form-stripe .btn').show();//.removeAttr("disabled");
		$(document).find('.btn-cart-stripe').show();
    } else {
		
		$.ajax({
            type: "POST",
            //dataType: n,
            url: "/paymentstripe/confirm_payment",
            data: {payment_intent_id:result.paymentIntent.id},
            success: function(t) {
               handleServerResponse(t);
            }
        });
		
		
		/*ajaxRequest: function(t, i, e, n) {
        void 0 == n && (n = "json"), $.ajax({
            type: "POST",
            dataType: n,
            url: t,
            data: i,
            success: function(t) {
                void 0 != e && e(t)
            }
        })
    },*/
		
	  
		//console.log(response);
      // The card action has been handled
      // The PaymentIntent can be confirmed again on the server
		/*if(result.success){
			handleServerResponse(response);
			
		}else{
			nxMain.ajaxRequest("/paymentstripe/confirm_payment", {payment_intent_id:result.paymentIntent.id}, function(t) {
				}).then(function(confirmResult) {
			return confirmResult.json();
		  }).then(handleServerResponse);
			
		}*/
		 
		
     /* fetch('/paymentstripe/confirm_payment', {
        method: 'POST',
           body: {
          payment_intent_id: result.paymentIntent.id
        }
      }).then(function(confirmResult) {
        return confirmResult.json();
      }).then(handleServerResponse);*/
    }
  });
}
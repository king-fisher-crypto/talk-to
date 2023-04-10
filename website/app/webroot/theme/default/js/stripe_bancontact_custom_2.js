// Create a Stripe client.
var stripe = Stripe($("#stripe_key").html());

// Create an instance of Elements.
var elements = stripe.elements();

if($(document).find('.stripe_bancontact').size()>0 && $(document).find('.stripe_bancontact').css('display') != "none"){
	bancontact_load();
}

function bancontact_load(){
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

			nxMain.ajaxRequest("/paymentbancontact/save_cart", {source:result.source.client_secret}, function(t) {
						if(!t.return && t.msg){
							cartFlashMessage(t.msg);
						}else{
							window.location = result.source.redirect.url;
						}
					});	
		}

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
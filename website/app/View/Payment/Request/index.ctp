<?php 
	echo $this->Html->script('https://js.stripe.com/v3/', array('block' => 'script')); 
	echo $this->Html->script('/theme/default/js/bootstrap-formhelpers-min.js', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/bootstrapValidator-min.js', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/stripe_request_custom_3.js?'.date('YmdHi'), array('block' => 'script'));


?>
<style>

/**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
.StripeElement {
  box-sizing: border-box;

  height: 40px;

  padding: 10px 12px;

  border: 1px solid transparent;
  border-radius: 4px;
  background-color: white;

  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}

.box-stripe form {
    max-width: 496px !important;
    padding: 0 15px;
    margin: 0 auto;
}
	
.box-stripe fieldset {

    border-style: none;
    padding: 5px;
    margin-left: -5px;
    margin-right: -5px;
    background: rgba(18, 91, 152, 0.05);
    border-radius: 8px;

}
.box-stripe .card-only {

    display: block;
	text-align: center;
	padding:5px 0;

}
.box-stripe fieldset legend {

    float: left;
    width: 100%;
    text-align: center;
    font-size: 13px;
    color: #8898aa;
    padding: 3px 10px 7px;

}
	.form-stripe{
		width:100%;
		color: #32325d;
		font-family: "Helvetica Neue", Helvetica, sans-serif;
		font-size: 14px;
	}
	
	.form-stripe::placeholder { 
  color: #aab7c4;
  opacity: 1; /* Firefox */
}
</style>

	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"> <?php
        /* titre de page */
        echo __('Votre commande');
    ?></h1>
		<!--h1/h2 both works here-->
	</section>
<div class="container">
		<section class="pricing-page">
			<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.2s">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
						<div class="steps-breadcrumbs">
								<ul class="nav nav-wizard text-center">
									<li class="step1 done"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level">Choix du pack</span></a></li>
									<li class="done"><a href="/accounts/cart"><span class="badge badge-step">2</span> - <span class="step step-level">Panier</span></a></li>
									<li class="done"><a href="/accounts/cart"><span class="badge badge-step">3</span> - <span class="step step-level">Paiement</span></a></li>
								</ul>
						</div><!--steps-breadcrumbs-->
					</div>
				</div>
				<?php
				echo $this->Session->flash();
			?>

        	<div class="row">
				<div class="col-sm-12">
					<div class="box-stripe box-stripe-request">
						
						<form>
					<div id="stripe_key" style="display:none"><?=$public_key ?></div>
					
					
					
					<div class="stripe_request" style="padding-bottom: 20px;border:0px;box-shadow: none">
						<div id="request_country" style="display:none"><?=$request_country ?></div>
						<div id="request_amount" style="display:none"><?=$request_amount ?></div>
						<div id="request_currency" style="display:none"><?=$request_currency ?></div>
						<div id="request_desc" style="display:none"><?=$request_desc ?></div>
						<div id="request_firstname" style="display:none"><?=$request_firstname ?></div>
						<div id="request_return" style="display:none"><?=$request_return ?></div>
						<div id="client_secret" style="display:none"><?=$client_secret ?></div>
						<div id="request_error" style="text-align: center">En cours de connexion...</div>
						<div id="payment-request-button" style="border:0px;box-shadow: none;margin-top:-20px;">
						  <!-- A Stripe Element will be inserted here. -->
						</div>
					</div>
					
				</div>
			</div>
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
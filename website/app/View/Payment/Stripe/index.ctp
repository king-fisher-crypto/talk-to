<?php 
	echo $this->Html->script('https://js.stripe.com/v3/', array('block' => 'script')); 
	echo $this->Html->script('/theme/default/js/bootstrap-formhelpers-min.js', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/bootstrapValidator-min.js', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/stripe_custom_2.js?'.date('YmdHi'), array('block' => 'script'));


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
									<li class="step1 done"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level"><?php echo __('Choix du pack') ?></span></a></li>
									<li class="done"><a href="#"><span class="badge badge-step">2</span> - <span class="step step-level"><?php echo __('Panier') ?></span></a></li>
									<li class="done"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level"><?php echo __('Paiement') ?></span></a></li>
								</ul>
						</div><!--steps-breadcrumbs-->
					</div>
				</div>
				<?php
				echo $this->Session->flash();
			?>

        	<div class="row">
				<div class="col-sm-12">
					<div class="box-stripe">
					<form action="<?=$form_url ?>" method="POST" id="payment-form-stripe" class="form-horizontal">
						<?php
							if(isset($stripe_customer) && $stripe_customer)
							{
							?>
						<div class="row bdtop mt20 linepayement"><div class="col-xs-12"><div class="buy_title_content "> <span class="title_bgg"><?php echo __('Moyens de paiement') ?>  &gt;</span> <span class="stripe_payment_mod"><?php echo __('Modifier') ?></span></div></div></div>

						<input type="hidden" name="stripeCustomer" value="<?=$stripe_customer ?>" id="stripeCustomer">
						<input type="hidden" name="stripeCard" value="<?=$stripe_default_card ?>" id="stripeCard">
						<div id="stripe_cards_default">
						<?php
								foreach($cartes as $k_card => $card){
						?>
						<div class="card_payment active"> <span class="action" rel="<?=$card->id ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/logos/<?=$card->type ?>.png" style="width:auto;height:40px"></div>
									<div class="title">&nbsp;</div>
									<div class="desc" style="font-size:14px;padding-top:5px !important"><?php
										echo __('Se terminant par *****').$card->num;
									?>
									</div>
								</div>
							</div>
						<?php
									break;
								}
							?>
						</div>
						<div id="stripe_cards_other"  style="display:none">
							<?php
								foreach($cartes as $k_card => $card){
						?>
							
							<div class="card_payment"> <span class="action" rel="<?=$card->id ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/logos/<?=$card->type ?>.png" style="width:auto;height:40px"></div>
									<div class="title">&nbsp;</div>
									<div class="desc" style="font-size:14px;padding-top:5px !important"><?php
										echo __('Se terminant par *****').$card->num;
									?>
									</div>
								</div>
								<div class="remove_content">X</div>
							</div>
						<?php
								}
							?>
							<div class="card_payment card_payment_add">
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="title">&nbsp;</div>
									<div class="desc desc_addcard" style="padding-top:20px;"><?php echo __('Ajouter une carte') ?>
									</div>
								</div>
							</div>
						</div>
						<?php
							}
						  ?>
						<span id="stripe_key" style="display: none;"><?=$public_key ?></span>
						 <fieldset <?php if(isset($stripe_customer) && $stripe_customer) echo 'style="display:none"' ?> id="form_fieldset_stripe_payment">
           					 <label class="card-only"><img src="/theme/default/img/strile-pay.png" alt="Stripe" style="width:184px;height:auto;"></label>
					  <div class="form-row">
						  
						 <div class="form-group">
							  <div class="col-sm-12">
								<input type="text" name="cardholdername" maxlength="70" placeholder="Propriétaire de la carte" class="card-holder-name StripeElement form-stripe" required>
							  </div>
							</div>
						
						<div id="card-element" class="form-control">
						  <!-- A Stripe Element will be inserted here. -->
						</div>

						
						  
					  </div>
						<div class="form-row text-center mt10 mb10">
					  <button class="btn btn-pink btn-slider  fadeInUp scroll animated" style="text-transform: uppercase" > <?=number_format($price,2).' '.$devise ?></button>
						</div>
						  </fieldset>
						<!-- Used to display form errors. -->
						<div id="card-errors" role="alert"></div>
						<?php if(isset($stripe_customer) && $stripe_customer) { ?>
						<div class="cart_box_stripe"><button type="button" class="btn-cart-stripe"> <?php echo __('confirmer et payer') ?> </button></div>
						<?php } ?>
						<?php
						if(isset($stripe_customer) && $stripe_customer && !$cartes){
						?>
						<div class="stripe_payment_mod_activ"></div>
						<?php
						}
						?>
					</form>
					</div>
				</div>
			</div>
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
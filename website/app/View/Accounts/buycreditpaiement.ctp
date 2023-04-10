
<!--	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"> <?php
        /* titre de page */
        echo __('Paiement');
    ?></h1>
	</section>-->

<div class="container">
		<section class="pricing-page">
			
			<div  class="pricing-tile-container mt20">
					<?php echo __('Choisissez parmi nos packs'); ?><br />
					<strong><?php echo __('Avec Spiriteo, vous n’avez jamais de mauvaises surprises'); ?></strong>	
				</div>
			<div class="content_box  mb40 wow fadeIn" data-wow-delay="0.2s">
				
           <!-- <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s">TARIFS</h2>-->
            <div class="row">
				<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class="step1 done"><a href="<?php echo $this->FrontBlock->getProductsLink(); ?>"><span class="badge badge-step">1</span> - <span class="step step-level"><?php echo __('Choix du pack'); ?></span></a></li>
					    <li class="done"><a href="/accounts/cart"><span class="badge badge-step">2</span> - <span class="step step-level"><?php echo __('Panier'); ?></span></a></li>
					    <li class="active"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level"><?php echo __('Paiement'); ?></span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
				</div></div>
			
			<div class="row">
				
				<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
					<?php 
				if(!$is_connected){
				?>
					<div class="row">
						<div class="col-xs-12 col-sm-6 buy_col buy_col_active">
							<?php echo $this->element('account_cart_subscribe',array('country' => $selected_countries)); ?>
						</div>
						<div class="col-xs-12 col-sm-6 buy_col">
							<?php echo $this->element('account_cart_login',array()); ?>
						</div>
					</div>
					<?php 
				}
				?><div class="row">
						<div class="col-xs-12">
					<?php
			
			echo $this->Session->flash();
        	?>
							</div>
						</div>
					<div class="row bdtop mt20 linepayement">
						<div class="col-xs-12">
							<div class="buy_title_content ">
								<span class="title_bgg"><?php echo __('Mode de réglement  >'); ?></span>
								<span class="title_ss"><?php echo __('Choisissez votre mode de réglement'); ?></span>
							</div>
						</div>
						<div class="row" style="display:none">
							<input type="hidden" value="<?php echo $cart['id_cart'] ?>" id="cart_id" name="cart_id" />
						</div>
						<div class="row" style="display:none">
						<?php 
							$page = $this->FrontBlock->getPageBlocTexte(170);
							if(isset($cart['voucher']) && $cart['voucher']['buy_only']){
								$page = $this->FrontBlock->getPageBlocTexte(218);
							} 
						    $page = str_replace("##CART_TOTAL##", $this->Nooxtools->displayPrice($cart['total_price']), $page);
							if($page !== false)
								echo $page;
						?>
						</div>
					</div>
					
					 <div class="row choose-payment">
							
                   <?php
					if(isset($cart['voucher']) && $cart['voucher']['buy_only']){
						echo '<div class="mode_payment mode_payment_coupon">
								<span class="action" type="link" rel="/paymentcoupons"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/images/payment/coupon.jpg"></div>
									<div class="title">'.__('Payer par coupon de réduction').'</div>
									<div class="desc">'.__('Valider afin de bénéficier').' '.__('de ce code promotionnel sans paiement').'</div>
								</div>
							</div>';
					}else{
						//decla par pays
						$activ_bancontact = true;
						$domain = $_SERVER['SERVER_NAME'];
						switch ($domain) {
							case 'fr.devspi.com':
							case 'www.talkappdev.com':
							case 'fr.spiriteo.com':
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_fr.png';
								$min= '180px';
								$activ_bancontact = false;
							break;
							case 'be.devspi.com':
							case 'be.spiriteo.com':
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_be.png';
								$min= '210px';
								
							break;
							case 'ca.devspi.com':
							case 'ca.spiriteo.com':
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_ca.png';
								$min= '180px';
								
							break;
							case 'ch.devspi.com':
							case 'ch.spiriteo.com':
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_ch.png';
								$min= '180px';
								
							break;	
							case 'lu.devspi.com':
							case 'lu.spiriteo.com':
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_lu.png';
								$min= '220px';
								
							break;	
							default:
								$frase1 = 'Paiement par carte bancaire sécurisé par Stripe';
								$frase2 = 'Intitulé sur votre compte : Glassgen Limited';
								$image = 'cb_fr.png';
								$min= '180px';
							break;
						}
						
						
						//hipay
					/*?>
						 <div class="mode_payment mode_payment_hipay">
								<span class="action" type="form" rel="#hipay_form"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/<?php echo $image; ?>"></div>
									<div class="title"><?php echo $frase1; ?></div>
									<div class="desc"><?php echo $frase2; ?></div>
								</div>
							</div>
					 <?php */
							//stripe
					?>
						 <div class="mode_payment mode_payment_stripe">
								<span class="action" type="link" rel="<?php

            echo $this->Html->url(array(
                'controller' => 'paymentstripe',
                'action'     => 'index'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/<?php echo $image; ?>"></div>
									<div class="desc"><?php echo __('Paiement par carte bancaire sécurisé par Stripe.'); ?> <?php echo __('Intitulé sur votre compte : Glassgen Limited'); ?></div>
								</div>
							</div>
					 <?php
       //paypal
            if(!$is_restricted){
            ?>
						 <div class="mode_payment mode_payment_paypal">
								<span class="action" type="link" rel="#"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/paypal.png"></div>
									<div class="title"><?php echo __('Payer par carte ou par compte Paypal'); ?></div>
									<div class="desc"><?php echo __('Intitulé sur votre compte Paypal : Glassgen Limited'); ?></div>
								</div>
							</div>
						 	 <?php
            }
						 //stripe bancontact
						if($activ_bancontact){
					?>
						 <div class="mode_payment mode_payment_stripe_bancontact">
								<span class="action" type="link" rel="<?php

            echo $this->Html->url(array(
                'controller' => 'paymentbancontact',
                'action'     => 'index'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/bancontact.png"></div>
									<div class="desc"><?php echo __('Bancontact - MisterCash paiement sécurisé. Intitulé sur votre compte : Glassgen Limited'); ?></div>
									<div class="desc">&nbsp;</div>
								</div>
							</div>
				 <?php
						}
						 //stripe paymentrequest
					?>
						 <div class="mode_payment mode_payment_stripe_payment">
								<span class="action" type="link" rel="<?php

            echo $this->Html->url(array(
                'controller' => 'paymentrequest',
                'action'     => 'index'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/request.png"></div>
									<div class="desc"><?php echo __('Apple Pay - Google Pay - Microsoft Pay'); ?></div>
									<div class="desc">&nbsp;</div>
								</div>
							</div>
					
		 <?php
		 
?>
				<!--<div class="mode_payment mode_payment_bankwire">
								<span class="action" type="link" rel="<?php

            echo $this->Html->url(array(
                'controller' => 'paymentbankwire',
                'action'     => 'index'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/bankwire.png"></div>
									<div class="title"><?php echo __('Payer par virement bancaire :'); ?></div>
									<div class="desc"><?php echo __('Traitement plus long le temps de recevoir celui-ci.'); ?></div>
								</div>
							</div>-->
						 <?php
						 //stripe sepa
					?>
						 <div class="mode_payment mode_payment_stripe_sepa">
								<span class="action" type="link" rel="<?php

            echo $this->Html->url(array(
                'controller' => 'paymentsepa',
                'action'     => 'index'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/sepa.png"></div>
									<div class="desc"><?php echo __('Payer par virement SEPA (disponible en Europe)'); ?></div>
									<div class="desc">&nbsp;</div>
								</div>
							</div>
						 
					
					
					<?php
					
					}
					?>
				</div><!--row END-->
				<div class="cart_box_buy">
					<div class="cart_box_buy_error"></div>
					<button type="button" class="btn-cart-buy">
						<?php 
				if(!$is_connected){
				?>
					<?php echo __('continuer'); ?>
				<?php }else{
					?>
						<?php echo __('continuer'); ?>
				<?php } ?>
						</button></div>	
					<p style="text-align: center;font-size:12px;"><?php echo __('J\'accepte'); ?> <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => '', 'style' => 'text-decoration:none;color:#933c8f'), __('les conditions générales')) ?> <?php echo __('et la'); ?> <?php echo $this->FrontBlock->getPageLink(13, array('target' => '_blank', 'class' => '', 'style' => 'text-decoration:none;color:#933c8f'), __('politique de confidentialité')) ?> <?php echo __('du site Spiriteo et je souhaite que l\'exécution de la prestation de service commence avant la fin du délai de rétractation de quatorze jours.'); ?></p>
				</div>
			</div>
			
	</section><!--pricing page END-->
</div><!--container END-->
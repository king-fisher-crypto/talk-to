<?php
	//echo $this->Html->css('/theme/default/css/daterangepicker', array('block' => 'css'));
	//echo $this->Html->css('/assets/plugins/bootstrap-datepicker/css/datepicker', array('block' => 'css'));
	//echo $this->Html->script('/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker', array('block' => 'script'));
	//echo $this->Html->script('/theme/default/js/nx_datepicker', array('block' => 'script'));
?>
<div class="container-gift">
	<div class="container">
		<div class="page mt20 mb20">
			<div class="breadcumb-gift hidden-sm hidden-xs">
				<ul>
					<li class="step1 "><a href="/gifts/index"><?=__('1-MA E-CARTE CADEAU') ?></a></li>
					<li class="active"><a href="#"><?=__('2-PAIEMENT') ?></a></li>
				</ul>
			</div>
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
								<span class="title_bgg"><?=__('Mode de réglement  >') ?></span>
								<span class="title_ss"><?=__('Choisissez votre mode de réglement') ?></span>
							</div>
						</div>
						<div class="row" style="display:none">
							<input type="hidden" value="<?php echo $giftorder_id ?>" id="giftorder_id" name="giftorder_id" />
						</div>
						
					</div>
					
					 <div class="row choose-payment">
							
                   <?php
					
						//decla par pays
						$domain = $_SERVER['SERVER_NAME'];
						switch ($domain) {
							case 'fr.devspi.com':
							case 'www.talkappdev.com':
							case 'fr.spiriteo.com':
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
								$image = 'cb_fr.png';
								$min= '180px';
								
							break;
							case 'be.devspi.com':
							case 'be.spiriteo.com':
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
								$image = 'cb_be.png';
								$min= '210px';
								
							break;
							case 'ca.devspi.com':
							case 'ca.spiriteo.com':
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
								$image = 'cb_ca.png';
								$min= '180px';
								
							break;
							case 'ch.devspi.com':
							case 'ch.spiriteo.com':
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
								$image = 'cb_ch.png';
								$min= '180px';
								
							break;	
							case 'lu.devspi.com':
							case 'lu.spiriteo.com':
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
								$image = 'cb_lu.png';
								$min= '220px';
								
							break;	
							default:
								$frase1 = 'Par carte bancaire avec le système de paiement sécurisé Hipay.';
								$frase2 = 'L\'intitulé qui apparaîtra sur votre relevé bancaire est "Hipay Zconnect".';
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
                'action'     => 'index_gift'
            ));

            ?>"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/<?php echo $image; ?>"></div>
									<div class="desc"><?php echo __('Par carte bancaire avec le système de paiement sécurisé Stripe.'); ?></div>
									<div class="desc">&nbsp;</div>
								</div>
							</div>
					 <?php
					 
       //paypal
            ?>
						 <div class="mode_payment mode_payment_paypal">
								<span class="action" type="link" rel="#"></span>
								<div class="checkbox_content"><div class="checkox"><span class="square"></span></div></div>
								<div class="right_content">
									<div class="logo"><img src="/theme/default/img/payment/paypal.png"></div>
									<div class="title"><?php echo __('Payer par carte ou par compte Paypal'); ?></div>
									<div class="desc"><?php echo __('Intitulé sur votre compte Paypal : Société Zconnect '); ?></div>
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
					
				</div><!--row END-->
				<div class="cart_box_buy">
					<div class="cart_box_buy_error"></div>
					<button type="button" class="btn-cart-buy">
						<?php 
				if(!$is_connected){
				?>
					<?=__('continuer') ?>
				<?php }else{
					?>
						<?=__('continuer') ?>
				<?php } ?>
						</button></div>	
				</div>
			</div>
			
		</div>
	</div><!--container END-->
</div>

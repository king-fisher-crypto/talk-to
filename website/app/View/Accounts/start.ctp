<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-12  mt150">
			<section class="pricing-page">
				<?php 
					if(!$is_connected){
						$date = $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s')),'%Y/%m/%d %H%M%S');
						$time = new DateTime($date);
						$time->modify('+15 minutes');

						$stamp = $time->format('Y-m-d H:i:s');
						
					?>
				<div class="content_box_start fadeIn">
					<div class="row-box">
						<div class="col-xs-12 col-sm-7 box_txt_payment">
							<p style="font-size:25px;color:#8478b0;" class="mt20 mb0"><?=__('Ça commence vraiment bien entre nous !') ?></p>
							<h1 style="font-size:19px;color:#943c8f;font-weight:500;text-transform: uppercase" class="mt0 mb20"><?=__('Offre cadeau Découverte') ?></h1>
							<p style="font-size:40px;color:#8777b4;text-transform: uppercase;font-weight:600;line-height:30px;" class="mb0"><?=__('5 min') ?></p>
							<p style="font-size:24px;color:#43434c;" class="mb20"><?=__('OFFERTES') ?></p>
							<h2 style="font-size:16px;color:#43434c;" class="start-h-sep mb0"><?=__('pour 10 min de consultation à') ?><br /><span style="font-size:20px;color:#943c8f;font-weight:600;"><?php echo $this->Nooxtools->displayPrice($product['Product']['tarif']); ?></span></h2>
							<p style="font-size:16px;color:#43434c;font-weight:600;" class="mb10"><?=__('Il ne vous reste plus que') ?></p>
							<p class="clock_min mb10" rel="<?=$stamp; ?>" style="line-height:20px;text-align:center;font-size:30px;color:#943c8f;font-weight:600;" ></p>
							<p style="font-size:16px;color:#43434c;font-weight:600;" class="mb10"><?=__('pour en profiter !') ?></p>
						</div>
						<div class="col-xs-12 col-sm-5 buy_col buy_col_active box_payment_subscribe">
								<?php echo $this->element('account_cart_subscribe',array('country' => $selected_countries)); ?>
								<?php
								echo $this->Session->flash();
								?>
								<button class="btn btn-pink btn-2 right mb20" id="btn_box_payment" style="display:block;margin:0 auto"><?=__('Continuer') ?></button>
						</div>
						<div class="col-xs-12 col-sm-5 buy_col" style="display:none">
								<?php echo $this->element('account_cart_login',array()); ?>
						</div>
						<div id="box_payment" class="col-xs-12 col-sm-5 buy_col2" >
							<div class="row mt20 linepayement">
								<div class="col-xs-12">
									<div class="buy_title_content ">
										<span class="title_bgg"><?=__('Mode de réglement  >') ?></span>
										<span class="title_ss"><?=__('Choisissez votre mode de réglement') ?></span>
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
						<?=__('payer') ?>
					<?php }else{
						?>
							<?=__('payer') ?>
					<?php } ?>
							</button></div>	
						<p style="text-align: center;font-size:12px;"><?=__('J\'accepte') ?> <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => '', 'style' => 'text-decoration:none;color:#933c8f'), __('les conditions générales')) ?> <?=__('et la') ?> <?php echo $this->FrontBlock->getPageLink(13, array('target' => '_blank', 'class' => '', 'style' => 'text-decoration:none;color:#933c8f'), __('politique de confidentialité')) ?> <?=__('du site Spiriteo et je souhaite que l\'exécution de la prestation de service commence avant la fin du délai de rétractation de quatorze jours.') ?></p>
						</div><!-- Hide devi payment -->
					</div>
				</div>
			<?php 
					}
					?>
			</section><!--pricing page END-->
		</div>
		<aside class="col-sm-12 col-md-12 mt20 mb20 hidden-sm hidden-xs">
			<div class=" widget2 subscribe-block-container no-sep col-sm-12 col-md-9">
				<?php
				if($txt['SubscribeLang']['block1']){
					echo '<div class="subscribe-block-content col-sm-12 col-md-4" style="min-height:138px">'.$txt['SubscribeLang']['block1'].'</div>';
				}
				if($txt['SubscribeLang']['block2']){
					echo '<div class="subscribe-block-content col-sm-12 col-md-4" style="min-height:138px">'.$txt['SubscribeLang']['block2'].'</div>';
				}
				if($txt['SubscribeLang']['block3']){
					echo '<div class="subscribe-block-content col-sm-12 col-md-4" style="min-height:138px">'.$txt['SubscribeLang']['block3'].'</div>';
				}
					
				?>
			</div>
			<div class=" widget2 subscribe-review-container block-subscribe-min col-sm-12 col-md-3">
				<div class="widget2-title2 text-center"><?=__('Derniers avis clients') ?></div>
					<?php
					App::import("Controller", "AppController");
									$leftblock_app = new AppController();
									$lang = '';
									
									
									if(isset($this->request->params['language'])){
										$lang = 	$this->request->params['language'];
										
									}else{
										$lang = 	$this->Session->read('Config.language');	
									}	   
		   	?>
			<div class="carousel-clients wow fadeIn" data-wow-delay="0.6s">    				
						<div class="carousel slide" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
							<!-- Carousel indicators -->
                            <?php
								$reviews = $this->FrontBlock->getLastReview(3);
							?>
							<ol class="carousel-indicators">
								<li data-target="#fade-quote-carousel" data-slide-to="0"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="1"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="2" class="active"></li>
								<span class="more-testi">
                                <?php //echo $this->Html->link(__('Voir tous les avis clients'), array('controller' => 'reviews', 'action' => 'display')); 
									
									//echo '<a href="'.$leftblock_app->getReviewsLink($lang).'" class="voirplus">'.__('Voir plus').'</a>';
									//echo '<span class="voirplus">'.__('Voir plus').'</span>';
								?>
                                </span>
							</ol>
							<!-- Carousel items -->
							<div class="carousel-inner">
                            	<?php 
								$ireview = 0;
								foreach ($reviews AS $review){ 
									$activereview = '';
									if($ireview == 0 ) $activereview = 'active';
								?>
								<div class="item <?php echo $activereview; ?>">
									<p>”<?php echo $this->Nooxtools->cleanCut(h($review['Review']['content']), 120, '...'); ?>” <span class="client-name"><?php echo h($review['User']['firstname']); ?></span></p>
								</div>
								<?php $ireview++; }  ?>
							</div>
						</div>
					</div><!--carousel-clients END-->
			</div>
		</aside>
	</div>		
	
</div><!--container END-->
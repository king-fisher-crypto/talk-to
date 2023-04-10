<!--	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"> <?php
        /* titre de page */
        echo __('Panier');
    ?></h1>
	</section>-->
	<?php

?>

<div class="container">
	
	<section class="pricing-page">
			<div  class="pricing-tile-container mt20">
					<?=__('Choisissez parmi nos packs') ?><br />
					<strong><?=__('Avec Spiriteo, vous n’avez jamais de mauvaises surprises') ?></strong>	
				</div>
			<div class="content_box  mb40 wow fadeIn" data-wow-delay="0.2s">
				
           <!-- <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s">TARIFS</h2>-->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class="step1 done"><a href="<?php echo $this->FrontBlock->getProductsLink(); ?>"><span class="badge badge-step">1</span> - <span class="step step-level"><?=__('Choix du pack') ?></span></a></li>
					    <li class="active"><a href="#"><span class="badge badge-step">2</span> - <span class="step step-level"><?=__('Panier') ?></span></a></li>
					    <li class="disabled"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level"><?=__('Paiement') ?></span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
				</div></div>
            
            			
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
					<!--<div  class="pricing-subtile-container left">	
						<strong>votre panier  ></strong>
					</div>-->
					<div class="cart_table hidden-xs">
						<div class="cart_table_pack cart_table_head"><?=__('pack') ?></div>
						<div class="cart_table_time cart_table_head"><?=__('durée') ?></div>
						<div class="cart_table_min cart_table_head"><?=__('€/min') ?></div>
						<div class="cart_table_price cart_table_head"><?=__('sous-total') ?></div>
						<div class="cart_table_pack cart_table_line"><?php echo str_replace('<br />',' ',$pack['ProductLang']['name']); ?></div>
						<div class="cart_table_time cart_table_line <?php if($pack['Product']['promo_credit'] > 0)echo "desc_promo"; ?>"><?php echo 
						str_replace('<strong>','<br /><strong>',$pack['ProductLang']['description']); ?>
						<?php if($pack['Product']['promo_credit'] > 0){
						$promo_min = number_format($pack['Product']['promo_credit'] / 60,0);
					?>
					<br /><p class="promo_min">+<?php echo $promo_min  ?> <?=__('min offertes') ?></p>
					<?php } ?>
						</div>
						<div class="cart_table_min cart_table_line"><?php 
							$devise_cout = '€';
							if($product['Product']['country_id'] == 13){$devise_precout = '$';}
							if($product['Product']['country_id'] == 3)$devise_cout = 'CHF';		
							echo $pack['Product']['cout_min'].$devise_cout.'/min'; ?></div>
						<div class="cart_table_price cart_table_line"><?php 
							if($pack['Product']['promo_amount'] > 0 || $pack['Product']['promo_percent']){
							echo '<span class="price-trait">'.$this->Nooxtools->displayPrice($pack['Product']['tarif']).'</span>';
							if($pack['Product']['promo_amount'] > 0){
										$diff = $pack['Product']['tarif'] - $pack['Product']['promo_amount'];
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
									if($pack['Product']['promo_percent'] > 0){
										$diff = $pack['Product']['tarif'] * (1 - $pack['Product']['promo_percent'] /100);
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
						}else{
							echo $this->Nooxtools->displayPrice($pack['Product']['tarif']);
						}
							 ?></div>
					</div>
					<div class="cart_table_mobile hidden-sm  hidden-md hidden-lg">
						<div class="cart_table_mobile_pack cart_table_head"><?=__('votre choix') ?></div>
						<div class="cart_table_mobile_pack cart_table_line">
							<p class="title"><?php echo str_replace('<br />',' ',$pack['ProductLang']['name']); ?></p>
							<span class="cart_table_mobile_pack_sep"></span>
							<p class="desc"><?php echo str_replace('<strong>','<br /><strong>',$pack['ProductLang']['description']) ?></p>
							<?php if($pack['Product']['promo_credit'] > 0){
						$promo_min = number_format($pack['Product']['promo_credit'] / 60,0);
					?>
					<p class="promo_min" style="margin-bottom:0">+<?php echo $promo_min  ?> <?=__('min offertes') ?></p>
					<?php } ?>
						</div>
						<div class="cart_table_mobile_price cart_table_head">sous-total</div>
						<div class="cart_table_mobile_price cart_table_mobile_line"><?php if($pack['Product']['promo_amount'] > 0 || $pack['Product']['promo_percent']){
							echo '<span class="price-trait">'.$this->Nooxtools->displayPrice($pack['Product']['tarif']).'</span>';
							if($pack['Product']['promo_amount'] > 0){
										$diff = $pack['Product']['tarif'] - $pack['Product']['promo_amount'];
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
									if($pack['Product']['promo_percent'] > 0){
										$diff = $pack['Product']['tarif'] * (1 - $pack['Product']['promo_percent'] /100);
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
						}else{
							echo $this->Nooxtools->displayPrice($pack['Product']['tarif']);
						} ?></div>
					</div>
					<a href="<?php echo $this->FrontBlock->getProductsLink(); ?>" class="cart_table_back"><?=__('> Modifier le pack') ?></a>
					<?php
					if($pack['Product']['promo_label']){ ?><div class="cart_promo_label"><?php echo $pack['Product']['promo_label'] ?></div><?php } ?>
					<div class="cart_alert"><?php echo $this->Session->flash(); ?></div>
					<div class="row mt20">
						<div class="col-xs-12 col-sm-6">
							<div class="cart_box_promo">
							<p class="cart_box_promo_desc"><?=__('Vous bénéficiez d\'un code <strong>« Offre Spéciale »</strong> ?') ?></p>
							<p class="cart_box_promo_desc"><?=__('Ou vous possédez <strong>« une Carte Cadeau »</strong> ?') ?></p>
							 <?php
								echo $this->Form->create('Account', array('action' => 'cart', 'nobootstrap' => 1,'class' => 'form', 'default' => 1,
									'inputDefaults' => array(
										'div' => '',
										'between' => '',
										'after' => '',
										'class' => 'form-control  form-control2-w'
									)
								));
		
								echo $this->Form->inputs(array(
									'produit'   => array('type' => 'hidden', 'id' => 'produit', 'value' => $pack['Product']['id']),
									'voucher'   => array('type' => 'text', 'id' => 'AccountVoucher', 'value' => '', 'placeholder' => 'code offre','label'=>false)
								));

								 echo $this->Form->button(''.__('ok'), array(
									'escape' => false,
									'type'  => 'submit',
									'class' => 'btn-promo-cart',
								));
							
								 echo $this->Form->end();
								?>	
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="cart_box_total">
								<?php
								echo $this->Form->create('Account', array('action' => 'buycreditpaiement', 'nobootstrap' => 1,'class' => 'form', 'default' => 1,
									'inputDefaults' => array(
										'div' => '',
										'between' => '',
										'after' => '',
										'class' => 'form-control'
									)
								));
								?>
								
								<div class="cart_box_total_line_remise ">
									<div class="cart_box_total_line_label"><?=__('total remise') ?><span class="cart_box_total_line_remise_sep"></span></div>
									<div class="cart_box_total_line_value">
									<?php if($pack['Product']['promo_amount'] > 0 || $pack['Product']['promo_percent']){
										if($pack['Product']['promo_amount'] > 0){
													$diff =$pack['Product']['promo_amount'];
													echo $this->Nooxtools->displayPrice($diff);		
												}
												if($pack['Product']['promo_percent'] > 0){
													$diff = $pack['Product']['tarif'] - ($pack['Product']['tarif'] * (1 - $pack['Product']['promo_percent'] /100));
													echo $this->Nooxtools->displayPrice($diff);		
												}
									}else{
										echo '0 €';
									} ?>
									<span class="cart_box_total_line_remise_sep"></span>
									</div>
								</div>
								<div class="cart_box_total_line_mt">
									<div class="cart_box_total_line_label"><?=__('total à payer') ?></div>
									<div class="cart_box_total_line_value"><?php if($pack['Product']['promo_amount'] > 0 || $pack['Product']['promo_percent']){
							echo '<span class="price-trait">'.$this->Nooxtools->displayPrice($pack['Product']['tarif']).'</span>';
							if($pack['Product']['promo_amount'] > 0){
										$diff = $pack['Product']['tarif'] - $pack['Product']['promo_amount'];
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
									if($pack['Product']['promo_percent'] > 0){
										$diff = $pack['Product']['tarif'] * (1 - $pack['Product']['promo_percent'] /100);
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
						}else{
							echo $this->Nooxtools->displayPrice($pack['Product']['tarif']);
						} ?></div>
								</div>
								<div class="cart_box_total_btn">
								<?php
								echo $this->Form->inputs(array(
									'produit'   => array('type' => 'hidden', 'id' => 'produit', 'value' => $pack['Product']['id']),
									'voucher'   => array('type' => 'hidden', 'id' => 'AccountVoucher', 'value' => $promo)
								));

								 echo $this->Form->button(''.__('validez votre panier'), array(
									'escape' => false,
									'type'  => 'submit',
									'class' => 'btn-cart-valid',
								));
								echo '</div>';
								 echo $this->Form->end();
								?>	
							</div>
						</div>
					</div>
				</div>
			</div>

        
            
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
<?php

if(empty($products)) :
    echo __('Nous n\'avons aucun produit à vous proposer.');
else : 

if(!isset($is_page_cms))$is_page_cms = 0;
?>

    <div class="price-table text-center hidden-sm hidden-xs">
		<div class="row">
       <!-- <div<?php if(empty($user) || $user['role'] !== 'client'){ echo ' class="hover_tooltip" data-toggle="tooltip" title="'.__('Veuillez vous inscrire ou vous connecter ci-dessous afin de créditer votre compte.').'"'; } ?>></div>

        <div class="table-responsive">
        <table class="table table-tarif table_products<?php if(!empty($user) && $user['role'] === 'client'){ echo ' table_hovered';} ?>">
            <tbody>-->
            <ul><?php 
			
			if(!$is_promo_total){	
			$tarif_base = 0;	
			
			$colonne = floor(12/count($products));	
			$index_prod = 0;	
			$maxx = count($products);
			foreach($products as $product): 
				$index_prod ++;
				
				$devise_cout = '€';
									$devise_precout = '';
				if($product['Product']['country_id'] == 13){$devise_precout = '$';$devise_cout = '';	}
				if($product['Product']['country_id'] == 3)$devise_cout = 'CHF';	
			
			?><li class="col-produit <?php if( $product['Product']['id'] == $produit_promo_select) echo ' selected' ?> <?php if($index_prod == 3)echo ' bestseller'; if($index_prod == $maxx)echo ' bestprice'; ?>">
					<p class="title"><?php echo $product['ProductLang']['name']; ?></p>
					<p class="desc <?php if($product['Product']['promo_credit'] > 0)echo "desc_promo"; ?>"><?php echo nl2br($product['ProductLang']['description']);  ?></p>
					<?php if($product['Product']['promo_credit'] > 0){
						$promo_min = number_format($product['Product']['promo_credit'] / 60,0);
					?>
					<p class="promo_min">+<?php echo $promo_min  ?> min offertes</p>
					<?php } ?>
					<p class="price <?php if($product['Product']['promo_credit'] > 0)echo "price_promo"; if($product['Product']['promo_amount'] > 0 || $product['Product']['promo_percent'] > 0)echo "price_discount"; ?>"><?php 
						
						if($product['Product']['promo_amount'] > 0 || $product['Product']['promo_percent']){
							echo '<span class="price-trait">'.$this->Nooxtools->displayPrice($product['Product']['tarif']).'</span>';
							if($product['Product']['promo_amount'] > 0){
										$diff = $product['Product']['tarif'] - $product['Product']['promo_amount'];
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
									if($product['Product']['promo_percent'] > 0){
										$diff = $product['Product']['tarif'] * (1 - $product['Product']['promo_percent'] /100);
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
						}else{
							echo $this->Nooxtools->displayPrice($product['Product']['tarif']);
						}
				
						?></p>
					
                	<!--<p class="minutes"><?php echo $product['ProductLang']['name'];
						if($product['Product']['promo_credit'] > 0){
							if($product['Product']['promo_label'] !=''){
								echo '<br /><span class="minutes_promo">'.$product['Product']['promo_label'].'</span>';
							}else{
								$nbminplus =  str_replace('.00','',number_format($product['Product']['promo_credit'] / 60,2));
								echo '<br /><span class="minutes_promo">+'.$nbminplus.' min</span>';	
							}
						}
						
					
					 ?></p>-->
					<!--<ul class="price-list">
								<li><span class="price-li <?php if($product['Product']['promo_amount'] > 0 || $product['Product']['promo_percent']) echo 'price-trait' ?>"><?php echo $this->Nooxtools->displayPrice($product['Product']['tarif']).' '; ?></span>
                                <?php
									$diff = 0;
									if($product['Product']['promo_amount'] > 0){
										$diff = $product['Product']['tarif'] - $product['Product']['promo_amount'];
										echo '<br /><span class="price_promo price-bold">-'.$devise_precout.$product['Product']['promo_amount'].''.$devise_cout.' = '.number_format($diff,2).''.$devise_cout.'</span>';		
									}
									if($product['Product']['promo_percent'] > 0){
										$diff = $product['Product']['tarif'] * (1 - $product['Product']['promo_percent'] /100);
										echo '<br /><span class="price_promo price-bold">-'.$product['Product']['promo_percent'].'% = '.$devise_precout.number_format($diff,2).''.$devise_cout.'</span>';		
									}
								?>
                                </li>
								<li><?php 
									
										if($product['Product']['cout_min']){
											
											$cout = $product['Product']['cout_min'];
											$cout_promo = 0;
											$economy = $product['Product']['economy_pourcent'];
											
											
											if($cout > 0){
												if($diff > 0 || $product['Product']['promo_credit']){
												echo '<span class="price-trait">'.$devise_precout.number_format($cout,2).' '.$devise_cout.'/min</span><br />';
												}else{
													echo '<span class="">'.$devise_precout.number_format($cout,2).' '.$devise_cout.'/min</span><br />';
												}
											}
											if($diff > 0){
												$nb_minute = $product['Product']['credits'] / 60;//Configure::read('Site.secondePourUnCredit')
												$cout_promo = number_format($diff / $nb_minute,2);
												if($product['Product']['promo_percent'])
												echo '<span class="price-bold">'.$devise_precout.$cout_promo.''.$devise_cout.'/min</span><br />';
												
											}else{
											if($product['Product']['promo_credit'] > 0){
												$nb_minute = ($product['Product']['credits']+ $product['Product']['promo_credit']) / 60;
												$cout_promo = number_format($product['Product']['tarif'] / $nb_minute,2);
												//echo '<span class="price-bold">'.$cout_promo.''.$devise_cout.'/min</span><br />';
											}}
											if($tarif_base > 0){
												if($cout_promo > 0){
													$economy =  ($cout_promo - $tarif_base  )  / $tarif_base * 100;
													
												}else{
													$economy = ($cout - $tarif_base  )  / $tarif_base * 100;
												}
												 $economy = number_format($economy,1);
												if($economy < 0)$economy = $economy *-1;
												
											}
											if($economy){
												//echo '('.$cout_promo.' - '.$tarif_base.'  )  / '.$tarif_base.' = '.$economy;
												if($product['Product']['promo_percent'])
												echo __('Vous économisez ').$economy.'%';
												
											}else{
												if($product['Product']['promo_percent'])
												echo nl2br($product['ProductLang']['description']); 
											}
											
										}else{
											echo nl2br($product['ProductLang']['description']); 
										}
				
									
									
									?></li>
								<li><?php 
								
								if($product['Product']['promo_credit'] > 0){
									echo '<span class="price-trait">'.$product['Product']['credits'].' '.__('crédits').'</span>';
									$total_credit = $product['Product']['credits'] + $product['Product']['promo_credit'];
									echo '<br /><span class="credits_promo price-bold">+'.$total_credit.' '.__('crédits').'</span>';	
								}else{
									echo $product['Product']['credits'].' '.__('crédits');
								}
								 ?></li>
					</ul>-->
					<?php
                        echo '<p class="pbtn">';
				
						// if(!empty($user) && $user['role'] === 'client') :
				
                        echo '<p class="pbtn">';
				if($is_page_cms){
							echo $this->Form->button(__('Choisir'), array(
								'class' => 'btn btn-default buy-btn btn-redir',
								'role' => 'button',
								'type'  => 'button',
								'param' => $product['Product']['id']
							));
						}else{
					
                        echo $this->Form->button(__('Choisir'), array(
                            'class' => 'btn btn-default buy-btn',
                            'role' => 'button',
                            'type'  => 'button',
                            'param' => $product['Product']['id']
                        ));
				}
                        echo '</p>';
				/*	else:
						
				if($is_landing){
					 echo '<p class="pbtn">';
                       // echo '<a href="'.$this->Html->url(array('controller' => 'home', 'action' => 'media_phone')).'" class="btn btn-default buy-btn nx_openinlightbox" data-target="'.$this->Html->url(array('controller' => 'home', 'action' => 'media_phone')).'" data-toggle="modal">'.__('S\'inscrire').'</a>';
								echo '<a class="btn btn-default buy-btn" data-target="#inscription" data-toggle="modal">'.__('S\'inscrire').'</a>';
                        echo '</p>';
				}else{
					 echo '<p class="pbtn">';
                        echo '<a class="btn btn-default buy-btn" data-target="#connection" data-toggle="modal">'.__('Choisir').'</a>';
                        echo '</p>';
				}
						
                    endif;*/
				
				/*
				
                        echo $this->Form->button(__('Acheter'), array(
                            'class' => 'btn btn-default buy-btn',
                            'role' => 'button',
                            'type'  => 'button',
                            'param' => $product['Product']['id']
                        ));
                        echo '</p>';*/
                    ?>
                </li>
            <?php 
				if(!$tarif_base){
					if($cout_promo>0){
						$tarif_base = $cout_promo;
						
					}else{
						$tarif_base =$cout;
					}
				}
				endforeach;
			}else{?>
            	<div class="col-sm-12">
					<p class="price_table_result_hide">
						<?php echo __('VALIDEZ AFIN DE BENEFICIER DE CE CODE PROMOTIONNEL SANS PAIEMENT'); ?>
					</p>
					<?php
                        echo '<p class="pbtn">';
                        echo $this->Form->button(__('Valider'), array(
                            'class' => 'btn btn-default buy-btn',
                            'role' => 'button',
                            'type'  => 'button',
                            'param' => 1
                        ));
                        echo '</p>';
						
						//hack passer etape 2
						
						
						
                    ?>
                </div>
            
				<?php } ?></ul>
          <!--  </tbody>
        </table>
        </div>-->
        <?php
        /*if(empty($user) || $user['role'] !== 'client'):
            echo '<p>'.__('Connectez-vous à votre compte client ou inscrivez-vous :').'</p>';
            echo $this->element('login_modal');
        endif;*/
        ?>
    </div>
    </div>
    <?php
   // if(!empty($user) && $user['role'] === 'client') :
        echo $this->Form->create('Account', array('action' => 'cart', 'nobootstrap' => 1,'class' => 'form', 'default' => 1,
            'inputDefaults' => array(
                'div' => '',
                'between' => '',
                'after' => '',
                'class' => 'form-control'
            )
        ));
		
		echo $this->Form->inputs(array(
            'produit'   => array('type' => 'hidden', 'id' => 'produit', 'value' => 0),
            'voucher'   => array('type' => 'hidden', 'id' => 'AccountVoucher', 'value' => $promo)
        ));

	
		?>
  
  
  
    <div class="hidden-md hidden-lg table_mobile_products mb30">
    <div class="row">
    	 <?php 
		$index_prod = 0;
		$maxx = count($products);
		foreach($products as $product): 
		$index_prod ++;
			
		?>
         		
                <div class="col-xs-12">
					 <div class="prod_mobile <?php if($index_prod == 3)echo ' bestseller'; if($index_prod == $maxx)echo ' bestprice'; ?>">
                   
                   <?php
                 /*  if($is_landing){
					   
					   ?>
					   <a href="javascript:void('');" class="box-color" data-target="#inscription" data-toggle="modal">
					   <?php
						}else{
					   
					   if($is_page_cms){
							?>
							<a href="javascript:void('');" class="box-color btn-redir" rol="button" param="<?php echo $product['Product']['id']; ?>">
							<?php
						}else{
					   ?>
					    <a href="javascript:void('');" class="box-color" rol="button" param="<?php echo $product['Product']['id']; ?>">
					   <?php
						}
				   }*/
                   ?>
                   
                   
                      <a href="javascript:void('');" class="prod_mobile_box" rol="button" param="<?php echo $product['Product']['id']; ?>">
                    	<p class="title"><?php echo str_replace('<br />', ' ',$product['ProductLang']['name']); ?></p>
						<div class="prod_mobile_left">
                        	
							<p class="desc <?php if($product['Product']['promo_credit'] > 0)echo "desc_promo"; ?>"><?php echo str_replace('télé', '<br />télé',str_replace('<strong>', '<br /><strong>',str_replace('<br />', ' ',$product['ProductLang']['description'])));  ?></p>
							<?php if($product['Product']['promo_credit'] > 0){
						$promo_min = number_format($product['Product']['promo_credit'] / 60,0);
					?>
							<p class="promo_min">+<?php echo $promo_min  ?> min offertes</p>
					<?php } ?>
						</div>
						<div class="prod_mobile_right">
                         	<p class="price <?php if($product['Product']['promo_credit'] > 0)echo "price_promo"; if($product['Product']['promo_amount'] > 0 || $product['Product']['promo_percent'] > 0)echo "price_discount"; ?>"><?php 
						
						if($product['Product']['promo_amount'] > 0 || $product['Product']['promo_percent']){
							echo '<span class="price-trait">'.$this->Nooxtools->displayPrice($product['Product']['tarif']).'</span>';
							if($product['Product']['promo_amount'] > 0){
										$diff = $product['Product']['tarif'] - $product['Product']['promo_amount'];
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
									if($product['Product']['promo_percent'] > 0){
										$diff = $product['Product']['tarif'] * (1 - $product['Product']['promo_percent'] /100);
										echo '<span class="price-bold">'.$this->Nooxtools->displayPrice($diff).'</span>';		
									}
						}else{
							echo $this->Nooxtools->displayPrice($product['Product']['tarif']);
						}
				
						?></p>
							<span class="price_btn"><?php echo __('Choisir'); ?></span>
						</div>
                        
                    </a><!--box-color END-->
                </div>
                </div><!--xs-12 END-->
         
         
            <?php endforeach; ?>

    </div><!--row END-->
    </div><!--visible-xs END-->
    <?php
	if(!empty($user) && $user['role'] === 'client' && !$is_landing) :
		
		//echo $this->FrontBlock->getBlockPromoMobile(); 
	?>
   <?php endif;
?>
    <?php
if(!empty($user) && $user['role'] === 'client' && !$is_landing && !$is_page_cms){
	?>
    <div class="text-center cgu_div_container">
        <div class="form-group wow fadeIn animated" data-wow-delay="0.4s" style="visibility: visible;-webkit-animation-delay: 0.4s; -moz-animation-delay: 0.4s; animation-delay: 0.4s;">
            <div class="checkbox cgu_div">
                <label>
                    <input id="AccountCgu" type="checkbox"  required="required" value="1" name="data[Account][cgu]">
                    <span></span>
                    <?php echo __('J\'ai lu et j\'approuve sans réserve les').' '.$this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox','style' => 'text-decoration:underline')
                        ) ?>
                </label>
                <div id="dialog-confirm" title="Conditions Générales d'utilisation" style="display:none">
                  <p style="color:#5a449b;font-size:13px;">Merci de valider les<br /><?php echo $this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox close_cgv_read','style' => 'text-decoration:underline;color:#5a449b;')
                        ) ?>.</p>
                </div>
            </div>
        </div>
    </div>
    
    
    
    <?php

		
        echo '<div class="btn_action_cart " style="text-align:center">';
        echo $this->Form->button(''.__('Acheter'), array(
            'escape' => false,
            'type'  => 'button',
            'class' => 'btn btn-pink btn-pink-modified btn_valid_cart center',
        ));
		 echo '</div>';

}
        echo $this->Form->end();
   // endif;
    ?>
    <script type="text/javascript">nx_select_product.noproductmsg = '<?php echo __('Veuillez sélectionner un produit'); ?>'; nx_select_product.nocgv = '<?php echo addslashes(__('Vous devez accepter nos Conditions Générales d\'Utilisation')); ?>';
		<?php
			if($product_preselect)
			echo 'preselect_product('.$product_preselect.')';
		?>
		</script>
<?php endif;
?>
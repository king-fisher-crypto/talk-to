<?php
if(empty($products)) :
    echo __('Nous n\'avons aucun produit à vous proposer.');
else : 

if(!isset($is_page_cms))$is_page_cms = 0;
if(!isset($is_impaye))$is_impaye = 0;
?>

	<div class="table_single_container_page hidden-sm hidden-xs">
    	<div class="price-table text-center">
		<div class="row">
       <!-- <div<?php if(empty($user) || $user['role'] !== 'client'){ echo ' class="hover_tooltip" data-toggle="tooltip" title="'.__('Veuillez vous inscrire ou vous connecter ci-dessous afin de créditer votre compte.').'"'; } ?>></div>

        <div class="table-responsive">
        <table class="table table-tarif table_products<?php if(!empty($user) && $user['role'] === 'client'){ echo ' table_hovered';} ?>">
            <tbody>-->
            <ul><?php 
			$colonne = floor(12/count($products));
			$index_prod = 0;
				$maxx = count($products);
			foreach($products as $product):
				$index_prod ++;
			?><li class="col-produit <?php if($index_prod == 3)echo ' bestseller'; if($index_prod == $maxx )echo ' bestprice'; ?>">
                	<p class="title"><?php echo $product['ProductLang']['name']; ?></p>
					<p class="desc"><?php echo nl2br($product['ProductLang']['description']);  ?></p>
					<p class="price"><?php 
						echo $this->Nooxtools->displayPrice($product['Product']['tarif']);
						
						?></p>
					<!--
					<ul class="price-list">
								<li><span class="price-li"><?php echo $this->Nooxtools->displayPrice($product['Product']['tarif']).' '; ?></span></li>
								<li><?php 
									$devise_cout = '€';
									$devise_precout = '';
				if($product['Product']['country_id'] == 13){$devise_precout = '$';$devise_cout = '';	}
				if($product['Product']['country_id'] == 3)$devise_cout = 'CHF';					
									if($product['Product']['cout_min']){
											
											$cout = $product['Product']['cout_min'];
											$economy = $product['Product']['economy_pourcent'];
											
											if($cout > 0){
												echo $devise_precout.number_format($cout,2).' '.$devise_cout.'/min<br />';
											}
											if($economy > 0){
												echo __('Vous économisez ').$economy.'%';
											}else{
												echo nl2br($product['ProductLang']['description']); 
											}
											
										}else{
											echo nl2br($product['ProductLang']['description']); 
										}
									
									//echo nl2br($product['ProductLang']['description']); ?></li>
								<li <?php if($product['Product']['credits'] < 0) echo 'style="display:none"'; ?>><?php echo $product['Product']['credits'].' '.__('crédits'); ?></li>
					</ul>-->
					<?php
                  //  if(!empty($user) && $user['role'] === 'client') :
                        echo '<p class="pbtn">';
					
						if($is_page_cms && !$is_impaye){
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
					/*else:
						
							if($is_landing){
								  echo '<p class="pbtn">';
									echo '<a class="btn btn-default buy-btn" data-target="#inscription" data-toggle="modal">'.__('S\'inscrire').'</a>';
									echo '</p>';
							}else{
								  echo '<p class="pbtn">';
									echo '<a class="btn btn-default buy-btn" data-target="#connection" data-toggle="modal">'.__('Choisir').'</a>';
									echo '</p>';
							}
						
                    endif;*/
                    ?>
                </li>
				<?php endforeach; ?></ul>
          <!--  </tbody>
        </table>
        </div>-->
        <?php
        /*if(empty($user) || $user['role'] !== 'client'):
            echo '<p>'.__('Connectez-vous à votre compte client ou inscrivez-vous :').'</p>';
            echo $this->element('login_modal');
        endif;*/
        ?>
    </div></div></div>
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
            'voucher'   => array('type' => 'hidden', 'id' => 'AccountVoucher', 'value' => '')
        ));

       /* echo '<div class="voucher_box well well-light text-center">';
        echo $this->Form->inputs(array(

            'produit'   => array('type' => 'hidden', 'id' => 'produit', 'value' => 0),
            'voucher'   => array(
                'class' => 'form-control',
                'label' => array(
                    'text' => '<p>'.__('Code promo').'</p><p class="small">'.__('Si vous disposez d\'un code PROMO, indiquez le ci-dessous : ').'</p>',
                    'class' => 'norequired'
                ),
            )
        ));
        echo '</div>';*/
		
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
                 /* if(isset($is_landing)){
					   
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
                        	
							<p class="desc"><?php echo str_replace('télé', '<br />télé',str_replace('<strong>', '<br /><strong>',str_replace('<br />', ' ',$product['ProductLang']['description'])));  ?></p>
						</div>
						<div class="prod_mobile_right">
                         	<p class="price" ><?php echo $this->Nooxtools->displayPrice($product['Product']['tarif']); ?></p>
							<span class="price_btn"><?php echo __('Choisir'); ?></span>
						</div>
                        
                    </a><!--box-color END-->
					</div>
                </div><!--xs-12 END-->
         
         
            <?php endforeach; ?>

    </div><!--row END-->
    </div><!--visible-xs END-->
    
    <?php
		if(!isset($is_landing))$is_landing=0;
	if(!empty($user) && $user['role'] === 'client' && ((!$is_landing  && !$is_page_cms) || $is_impaye)) :
		
		//echo $this->FrontBlock->getBlockPromoMobile(); 
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
	endif;
	
    /*    echo '<div class="pricing-footer"><div class="row"><div class="col-sm-7 col-md-8">
							<div class="valid_box"><div class="form-group wow fadeIn" data-wow-delay="0.4s">
						       <div class="checkbox cgu_div">';
      
	  echo '<label class=""><input id="AccountCgu_" type="hidden" value="0" name="data[Account][cgu]">
			<input id="AccountCgu" type="checkbox" checked="checked" required="required" value="1" name="data[Account][cgu]">
					 <span></span>'.__('J\'ai lu et j\'approuve sans réserve les').' '.$this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox','style' => 'text-decoration:underline')
                        ).'</label>';*/
	  
   /*   echo $this->Form->inputs(array(
            'cgu'    => array(
                'label' => array(
                    'text' => __('J\'ai lu et j\'approuve sans réserve les').' '.$this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox hidden-xs','style' => 'text-decoration:underline')
                        ),
                    'class' => 'cgv_label'
                ),
                'type' => 'checkbox',
                'between' => false,
                'after' => false,
                'value' => 1,
                'required' => true, 'class' => false, 'div' => array('class' => 'checkbox cgu_div hidden-xs'))
        ));*/
		
		
		/* echo '<label class="visible-xs"><input id="AccountCgu_" type="hidden" value="0" name="data[Account][cgu]">
			<input id="AccountCgu" type="checkbox" checked="checked" required="required" value="1" name="data[Account][cgu]">
					 <span></span>'.__('J\'ai lu et j\'approuve sans réserve les').' '.$this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox','style' => 'text-decoration:underline')
                        ).'</label>';*/
		
		/*
		 echo $this->Form->inputs(array(
            'cgu'    => array(
                'label' => array(
                    'text' => .' '.$this->FrontBlock->getPageLink(
                            1,
                            array('target' => '_blank', 'class' => 'nx_openinlightbox visible-xs','style' => 'text-decoration:underline')
                        ),
                    'class' => 'cgv_label'
                ),
                'type' => 'checkbox',
                'between' => false,
                'after' => false,
                'value' => 1,
                'required' => true, 'class' => false,'checked' => true, 'div' => array('class' => 'cgu_div_mobile visible-xs'))
        ));*/

		//echo '</div></div>';
		if(!empty($user) && $user['role'] === 'client' &&( (!$is_landing  && !$is_page_cms) || $is_impaye)) :
        echo '<div class="btn_action_cart " style="text-align:center">';
		
		if($is_impaye)
			$btn_label = 'Payer';
			else
			$btn_label = __('Acheter');	
					
        echo $this->Form->button(''.$btn_label, array(
            'escape' => false,
            'type'  => 'button',
            'class' => 'btn btn-pink btn-pink-modified btn_valid_cart center',
        ));
		 echo '</div>';
					endif;
      //  echo '</div></div></div></div>';
       // echo '</div>';

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
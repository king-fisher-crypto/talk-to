<?php
    echo $this->Html->script('/theme/default/js/select_product', array('block' => 'script'));
    $user = $this->Session->read('Auth.User');
?>
	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Choisissez le nombre de minutes à acheter') ?></h1>
		<!--h1/h2 both works here-->
	</section>

<div class="container">
		<section class="pricing-page">
			<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.2s">
            <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s"><?=__('TARIFS') ?></h2>
            
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class="active"><a href="#"><span class="badge badge-step">1</span> <span class="step step-level"><?=__('Choix des<br/>minutes') ?></span></a></li>
					    <li class="disabled"><a href="#"><span class="badge badge-step">2</span> <span class="step step-level"><?=__('Moyen de<br/>paiement') ?></span></a></li>
					    <li class="disabled"><a href="#"><span class="badge badge-step">3</span> <span class="step step-level"><?=__('Confirmation<br/>de paiement') ?></span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
            <div class="pricing-page-container">
            <?php
		
			echo $this->element('cart_products_promo', array('products' => $products, 'user' => $user, 'promo' => $code_promo, 'produit_promo_select' => $produit_promo_select));
			
            
        ?>
        </div>
        
            <div class="pricing-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <?php
              
                            $idlang = $this->Session->read('Config.id_lang');
                            $parts = explode('.', $_SERVER['SERVER_NAME']);
                            if(sizeof($parts)) $extension = end($parts); else $extension = '';
                            if($idlang == 1){
                                if($extension == 'ca')$idlang=8;	
                                //if($extension == 'ch')$idlang=10;
                                //if($extension == 'be')$idlang=11;
                                if($extension == 'lu')$idlang=12;
                            }
                          
                            //On affiche la page pour le tarif
                            $page = $this->FrontBlock->getPageBlocTextebyLang(90,$idlang);
                            if($page !== false){
                                ?>
                                <div class="price-desc">
                                <?php
                                 echo $page;
                                 ?>
                                 </div>
                                <?php
                            }
                        ?>
                    </div>
                    <div class="col-sm-12 col-md-4">
							<div class="voucher_box well well-light text-center">
								<p>Code promo</p>
                                <?php 
								if($promo_title){
									?>
                                    <p class="small"><?=$promo ?> : <?=$promo_title ?></p>
                                    <?php }else{ ?>
								<p class="small"><?=__('Si vous disposez d\'un code PROMO, indiquez le ci-dessous :') ?></p>
								<input id="code_promo" class="form-control" type="text" required="" placeholder="Code promo">
								<a class="btn btn-pink btn-pink-modified btn-small-modified mt10" id="promo_live"><?=__('Valider') ?></a>
                                <?php } ?>
							</div>
					</div>
                </div>
            </div><!--pricing-footer END-->
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
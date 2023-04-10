<?php
    echo $this->Html->script('/theme/default/js/select_product2', array('block' => 'script'));
    $user = $this->Session->read('Auth.User');


//<!--slider to show once the user is logged in-->
if($slideprice){
	
	$image = DS.Configure::read('Site.pathSlideprice').DS.$slideprice['SlidepriceLang']['name'];
	$image_mobile = DS.Configure::read('Site.pathSlideprice').DS.$slidepricemobile['SlidepricemobileLang']['name_mobile'];
	list($width_mobile, $height_mobile) = getimagesize ( $_SERVER[DOCUMENT_ROOT].'/app/webroot'.$image_mobile );
	if($height_mobile > 125)$hauteur_mobile = $height_mobile; else $hauteur_mobile = 125;
	list($width, $height) = getimagesize ( $_SERVER[DOCUMENT_ROOT].'/app/webroot'.$image );
	if($height > 125)$hauteur = $height; else $hauteur = 125;
	
	$countdown = '';
	if($slideprice['SlidepriceLang']['date_fin']  && $slideprice['SlidepriceLang']['date_fin'] != '0000-00-00 00:00:00'){	
		
		$text_compteur = '';
				if($slideprice['SlidepriceLang']['text_compteur'])$text_compteur = '<p style="margin-top:10px;text-align:center;font-size:'. $slideprice['SlidepriceLang']['date_fin_size'].';color:'. $slideprice['SlidepriceLang']['date_fin_color'].'">'.$slideprice['SlidepriceLang']['text_compteur'].'</p>';
		
				$countdown =$text_compteur.'<span class="clock clock_price" rel="'.$slideprice['SlidepriceLang']['date_fin'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'.$slideprice['SlidepriceLang']['date_fin_size'].'px;color:'.$slideprice['SlidepriceLang']['date_fin_color'].'"></span>';
			}
	$countdown_mobile = '';
	$text_compteur_mobile = '';
	if($slidepricemobile['SlidepricemobileLang']['date_fin']  && $slidepricemobile['SlidepricemobileLang']['date_fin'] != '0000-00-00 00:00:00'){	
				if($slidepricemobile['SlidepricemobileLang']['text_compteur'])$text_compteur_mobile = '<p style="margin-top:10px;text-align:center;font-size:'. $slidepricemobile['SlidepricemobileLang']['date_fin_mobile_size'].';color:'. $slidepricemobile['SlidepricemobileLang']['date_fin_mobile_color'].'">'.$slidepricemobile['SlidepricemobileLang']['text_compteur'].'</p>';
		
				$countdown_mobile =$text_compteur_mobile.'<span class="clock clock_price" rel="'.$slidepricemobile['SlidepricemobileLang']['date_fin'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'.$slidepricemobile['SlidepricemobileLang']['date_fin_mobile_size'].'px;color:'.$slidepricemobile['SlidepricemobileLang']['date_fin_mobile_color'].'"></span>';
			}
	
	$slide['LandingLang']['font_font_4'] = 'AvenirLT-Book';	
	$list_font_interne = array('Fjalla One');
	
	if($slideprice['SlidepriceLang']['font_font_1'] && !in_array($slideprice['SlidepriceLang']['font_font_1'],$list_font_interne))echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slideprice['SlidepriceLang']['font_font_1']).'">';
	if($slideprice['SlidepriceLang']['font_font_2'] && !in_array($slideprice['SlidepriceLang']['font_font_2'],$list_font_interne))echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slideprice['SlidepriceLang']['font_font_2']).'">';
	
	if(!$slideprice['SlidepriceLang']['font_font_1'])$slideprice['SlidepriceLang']['font_font_1'] = 'AvenirLT-Medium';
	if(!$slideprice['SlidepriceLang']['font_font_2'])$slideprice['SlidepriceLang']['font_font_2'] = 'AvenirLT-Medium';
	
	?>
	<section class="slider-small" style="background:url('<?=$image ?>') no-repeat center top;background-size: cover;min-height:<?=$hauteur ?>px;padding:10px 0">
	<h1 class="" style="display:none"><?php echo $titre_h1; ?></h1>
	<div class="h1" data-wow-delay="0.5s" style="font-family:<?php echo $slideprice['SlidepriceLang']['font_font_1']; ?>;font-size:<?php echo $slideprice['SlidepriceLang']['font_size_1']; ?>px;color:<?php echo $slideprice['SlidepriceLang']['font_color_1']; ?>;"><?php echo $slideprice['SlidepriceLang']['title']; ?></div>
	<div class="h2" data-wow-delay="0.5s" style="font-family:<?php echo $slideprice['SlidepriceLang']['font_font_2']; ?>;font-size:<?php echo $slideprice['SlidepriceLang']['font_size_2']; ?>px;color:<?php echo $slideprice['SlidepriceLang']['font_color_2']; ?>;"><?php echo $slideprice['SlidepriceLang']['alt']; ?></div>
	<?=$countdown ?>
</section>
<?php if($slidepricemobile){ ?>
<section class="slider-small visible-xs" style="background:url('<?=$image_mobile ?>') no-repeat center top;background-size: cover;min-height:<?=$hauteur_mobile ?>px;">
	<div class="h1" data-wow-delay="0.5s" style="font-size:<?php echo $slidepricemobile['SlidepricemobileLang']['font_size_mobile_1']; ?>px;color:<?php echo $slidepricemobile['SlidepricemobileLang']['font_color_mobile_1']; ?>;"><?php echo $slidepricemobile['SlidepricemobileLang']['title_mobile']; ?></div>
	<?=$countdown_mobile ?>
</section>
<?php } ?>
	<?php
}else{
	?>
	<!--<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Choisissez le nombre de minutes à acheter') ?></h1>

	</section>-->
	<?php
}

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
					  	<li class="step1 active"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level"><?=__('Choix du pack') ?></span></a></li>
					    <li class="disabled"><a href="#"><span class="badge badge-step">2</span> - <span class="step step-level"><?=__('Panier') ?></span></a></li>
					    <li class="disabled"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level"><?=__('Paiement') ?></span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
				</div></div>
			<div  class="pricing-subtile-container">	
				<strong><?=__('Les + Spiriteo  >') ?></strong>   <?=__('Aucune attente / De vrais voyants 7j/7-24h/24 / 98% de clients satisfaits de leurs consultations !') ?>
			</div>	
				
            <div class="pricing-page-container">
            <?php
			if( isset($promo) && $promo){
				echo $this->element('cart_products_promo', array('products' => $products, 'user' => $user, 'promo' => $promo, 'promo_title' => $promo_title , 'is_promo_total' => $is_promo_total));
			}else{
				echo $this->element('cart_products', array('products' => $products, 'user' => $user));
			}
			
			
            
        ?>
        </div>
        
            <div class="pricing-footer">
                <div class="row">
                	<?php //echo $this->FrontBlock->getBlockPromoMobile(); ?>
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
                    <?php 
					//echo  $this->FrontBlock->getBlockPromo(); 
					/*if($promo_title && !$is_promo_public)
						echo $this->FrontBlock->getBlockPromoDone($promo_title);
						else 
					echo $this->FrontBlock->getBlockPromo();*/ ?>
                </div>
            </div><!--pricing-footer END-->
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->

<?php 
/*if($is_promo_total){
?>
<script>
	$(document).ready(function() {
		var autovalid = 1;
		autovalidproduct(autovalid);
	});
</script>

<?php
}*/
?>
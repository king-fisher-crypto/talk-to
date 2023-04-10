<?php echo $this->Html->script('/theme/default/js/select_product', array('block' => 'script')); ?>

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
            <!--<h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s"><?php echo __('Règlement par Hipay'); ?></h2>-->
            
            <div class="row">
				<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class="step1 done"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level">Choix du pack</span></a></li>
					    <li class="done"><a href="#"><span class="badge badge-step">2</span> - <span class="step step-level">Panier</span></a></li>
					    <li class="done"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level">Paiement</span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
				</div></div>
            <?php
		
			echo $this->Session->flash();

            
        ?>
        
        <div class="row">
				<div class="col-sm-12">
					<div class="box_account well well-account">
						 <?php
        echo $this->Session->flash();


        $html = isset($contenu['PageLang']['content'])?$contenu['PageLang']['content']:'';

        $html = str_replace("##CART_TOTAL##", isset($cart_datas['total_price'])?$this->Nooxtools->displayPrice($cart_datas['total_price']):'', $html);
        $html = str_replace("##CART_USER_MAIL##", isset($cart_datas['user']['email'])?$cart_datas['user']['email']:'', $html);

        echo $html;


    ?>

					</div>
				</div>
       <?php
   
		if($cart_datas['user']['careers'] == 'landing'){
			echo '<iframe src="http://www.weedoit.fr/tracking/tracklead.php?idcpart=10763&idr=0'.$cart_datas['id_cart'].'&email='.$cart_datas['user']['email'].'&transaction='.$cart_datas['product']['Product']['tarif'].'" height="1" width="1" frameborder="0"></iframe>';
		}
	
		?>

<ul class="list-inline slider-button-group" style="text-align: center"><li><a href="/" class="btn btn-pink btn-slider  fadeInUp scroll animated" data-wow-delay="2.4s" style="color:#fff;background:rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0;visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;">Voir la liste des voyants</a></li></ul>
        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
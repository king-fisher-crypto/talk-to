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
           <!-- <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s"><?php echo __('Règlement par Coupon réduction'); ?></h2>-->
            
            <div class="row">
				<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class="step1 done"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level"><?=__('Choix du pack') ?></span></a></li>
					    <li class="done"><a href="#"><span class="badge badge-step">2</span> - <span class="step step-level"><?=__('Panier') ?></span></a></li>
					    <li class="done"><a href="#"><span class="badge badge-step">3</span> - <span class="step step-level"><?=__('Paiement') ?></span></a></li>
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


    //On affiche la page pour le tarif
    $page = $this->FrontBlock->getPageBlocTexte(219);

    $replace = array(
        '##CART_TOTAL##'            =>  $this->Nooxtools->displayPrice($cart['total_price']),
        '##CART_USER_MAIL##'        =>  $cart['user']['email'],
        '##CART_ORDER_REF##'        =>  $cart['cart_reference'],
    );

    $page = str_replace(array_keys($replace), array_values($replace), $page);
    if($page !== false)
        echo $page;

    ?>
<ul class="list-inline slider-button-group" style="text-align: center"><li><a href="/" class="btn btn-pink btn-slider  fadeInUp scroll animated" data-wow-delay="2.4s" style="color:#fff;background:rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0;visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;"><?=__('Voir la liste des voyants') ?></a></li></ul>
					</div>
				</div>

    <!-- Google Code for Transaction Conversion Page --> <script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 943164839;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "itU2CMTfw2IQp5vewQM"; var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript"
src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt=""
src="//www.googleadservices.com/pagead/conversion/943164839/?label=itU2CMTfw2IQp5vewQM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
	

        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
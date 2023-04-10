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
            <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s"><?php echo __('Erreur'); ?></h2>
            
           <div class="row">
					<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1">
						<div class="steps-breadcrumbs">
								<ul class="nav nav-wizard text-center">
									<li class="step1 done"><a href="#"><span class="badge badge-step">1</span> - <span class="step step-level">Choix du pack</span></a></li>
									<li class="done"><a href="/accounts/cart"><span class="badge badge-step">2</span> - <span class="step step-level">Panier</span></a></li>
									<li class="done"><a href="/accounts/cart"><span class="badge badge-step">3</span> - <span class="step step-level">Paiement</span></a></li>
								</ul>
						</div><!--steps-breadcrumbs-->
					</div>
				</div>
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



        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
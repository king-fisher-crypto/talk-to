<?php echo $this->Html->script('/theme/default/js/select_product', array('block' => 'script')); ?>

	<section class="slider-logged">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"> <?php
        /* titre de page */
        echo __('VOTRE COMMANDE');
    ?></h1>
		<!--h1/h2 both works here-->
	</section>

<div class="container">
		<section class="pricing-page">
			<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.2s">
            <h2 class="uppercase text-center wow fadeIn" data-wow-delay="0.3s"><?=__('CONFIRMATION') ?></h2>
            
            <div class="steps-breadcrumbs">
					<ul class="nav nav-wizard text-center">
					  	<li class=""><a href="#"><span class="badge badge-step">1</span> <span class="step step-level"><?=__('Choix des<br/>minutes<') ?>/span></a></li>
					    <li class=""><a href="#"><span class="badge badge-step">2</span> <span class="step step-level"><?=__('Moyen de<br/>paiement') ?></span></a></li>
					    <li class="active"><a href="#"><span class="badge badge-step">3</span> <span class="step step-level"><?=__('Confirmation<br/>de paiement') ?></span></a></li>
					</ul>
			</div><!--steps-breadcrumbs-->
<?php
         $this->Session->flash();

        echo $this->element('title', array(
            'title' => __('Votre compte a été crédité !'),
            //'icon' => 'shopping-cart'
        ));

        echo str_replace("##CREDITS##", '<strong>'.CakeSession::read('Auth.User.credit_recharge').'</strong>', $this->FrontBlock->getPageBlocTexte(144));
    ?>



        </div><!--content_box-box END-->
	</section><!--pricing page END-->
</div><!--container END-->
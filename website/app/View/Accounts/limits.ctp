<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes paiements') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes limites') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Mes limites').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

				  	<div class="box_account well well-account well-small">
                    
                    
                     <?php

            $parms = array(
                '##PARM_TOTAL_CREDITS_HEBDO##'  =>  $total_amount_hebdo,
                '##PARM_TOTAL_BUYABLE##'        =>  $total_buyable,
				'##PARM_CURRENCY_BUYABLE##'=>  $total_currency,
            );

            echo str_replace(array_keys($parms), array_values($parms),$this->FrontBlock->getPageBlocTexte(159));



           echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                                      'inputDefaults' => array(
                                                          'div' => '',
                                                          'between' => '<div class="col-lg-7 mb20">',
                                                          'after' => '</div>',
                                                          'class' => 'form-control'
                                                      )
            ));

            echo $this->Form->input('limit_credit', array(
                'label' => array('text' => __('Entrez votre limite (montant)'), 'class' => 'col-sm-12 col-md-4 control-label'),
                'value' => $limit,
                'type'  => 'text'
            ));

            echo $this->Form->end(array('label' => __('Enregistrer'), 'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0', 'before' => '<div class="col-sm-12 col-md-offset-0 col-md-12" style="text-align:center">', 'after' => '</div>'));
      
	   
	    ?>


					</div><!--content_box END-->
				
					</div>
				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
</div><!--row END-->
</section><!--expert-list END-->

</div>
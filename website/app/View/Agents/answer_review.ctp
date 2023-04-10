<?php
	  echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
        
    ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Répondre a un avis client') ?></h1>
</section>
<div class="container">
	<section class="page profile-page mt20 mb40">
    	<div class="row">
        	<div class="col-sm-12 col-md-9">
            	<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
                	<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Répondre') ?></h2>
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
										'name'  =>  '<span class="active">'.__('Répondre').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->
                    <div class="form-horizontal box_account well well-account well-small">
                    
                    
                            <?php
            echo $this->Form->create('Agent', array('action' => 'review_resp', 'nobootstrap' => 1,'class' => 'form-horizontal mt40', 'default' => 1,
                'inputDefaults' => array(
                    'div' => 'form-group',
                    'between' => '<div class="col-lg-7">',
                    'after' => '</div>',
                    'class' => 'form-control',
                )
            ));
			?>
            <div class="form-group wow fadeIn" data-wow-delay="0.4s">
               <div class="col-sm-12 col-md-12"> <p><?php echo '<strong>'.$review['User']['firstname'].'</strong> à publié cet avis :<br />'.$review['Review']['content']; ?></p>
                <input type="hidden" name="data[Agent][review_id]" id="data[Agent][review_id]" value="<?php echo $review['Review']['review_id']; ?>" /></div>
            </div>

            <?php echo $this->Form->input('content', array(
                    'label' => array(
                        'text' => __('Votre réponse').' <span class="star-condition">*</span>',
                        'class' => 'col-sm-12 col-md-4 control-label required'
                    ),
                    'required' => true,
                    'type' => 'textarea',
                    'between' => '<div class="col-sm-12 col-md-7">'
                )
            );
			
            echo $this->Form->end(array('label' => __('Envoyer'), 'class' => 'btn btn-pink btn-pink-modified', 'before' => '<div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.4s" style="visibility: visible;-webkit-animation-delay: 0.4s; -moz-animation-delay: 0.4s; animation-delay: 0.4s;">
<div class="col-sm-12 col-md-offset-4 col-md-8">', 'after' => '</div></div>', 'div' => array('class' => 'form-group mt20 wow fadeIn')));
        ?>

				  	</div>
    			</div><!--content_box END-->
			</div><!--col-9 END-->
                            
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
        </div><!--row END-->
    </section><!--expert-list END-->
</div>
<?php
	  echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
	  echo $this->Html->script('/theme/default/js/jquery.raty-fa', array('block' => 'script'));
        
    ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Experts favoris') ?></h1>
</section>
<div class="container">
	<section class="page profile-page mt20 mb40">
    	<div class="row">
        	<div class="col-sm-12 col-md-9">
            	<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
                	<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Votre avis nous intéresse') ?></h2>
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
										'name'  =>  '<span class="active">'.__('Votre avis nous intéresse').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->
                    <div class="form-horizontal box_account well well-account ">
                    	<p><?php 
							$idlang = $this->Session->read('Config.id_lang');
							 $page = $this->FrontBlock->getPageBlocTextebyLang(322,$idlang);
							echo $page;
							?></p>
                    
                            <?php
						
						
						
        if(!empty($voyants)){
			//echo '<div class="col-lg-12">';
            echo $this->Form->create('Account', array('action' => 'review', 'nobootstrap' => 1,'class' => 'form-horizontal mt40', 'default' => 1,
                'inputDefaults' => array(
                    'div' => 'form-group',
                    'between' => '<div class="col-lg-7">',
                    'after' => '</div>',
                    'class' => 'form-control',
                )
            ));
			//echo '<div class="row">

            echo $this->Form->input('agent_number', array(
                'label' => array('text' => __('Sélectionner l\'expert').' <span class="star-condition">*</span>', 'class' => 'control-label col-lg-4 required'),
                'options' => $voyants,
                'empty' => __('Choisissez un expert'),
                'between' => '<div class="col-sm-12 col-md-7 control-label">',
                'selected'  => (isset($expert) && !empty($expert) && in_array($expert, array_keys($voyants)) ?$expert:false),
                'required' => true
            )); ?>
            <div class="form-group wow fadeIn" data-wow-delay="0.4s">
                <label class="col-sm-12 col-md-4 control-label required"><?php echo __('Evaluation de l\'expert'); ?> <span class="star-condition">*</span></label>
                <div class="col-sm-12 col-md-7">
                <?php 
				
					if($this->request->isMobile()){
						?>
                        <select id="AccountRate" name="data[Account][rate]"><option value="0.5">0.5</option><option value="1">1</option><option value="1.5">1.5</option><option value="2">2</option><option value="2.5">2.5</option><option value="3">3</option><option value="3.5">3.5</option><option value="4">4</option><option value="4.5">4.5</option><option value="5" selected>5</option></select>
                        <?php
					}else{
				?>
                <div class="rating-container" id="review_stars2"></div>
               <div id="evaluation_expert" style="display:none;float:left;margin-left:5px;">5/5</div>
               <br /> <?php echo $this->Form->input('rate', array('type' => 'hidden', 'value' => 5)) ;
					}
			   ?>
               </div>
            </div>

            <?php echo $this->Form->input('content', array(
                    'label' => array(
                        'text' => __('Votre avis').' <span class="star-condition">*</span>',
                        'class' => 'col-sm-12 col-md-4 control-label required'
                    ),
                    'required' => true,
                    'type' => 'textarea',
                    'between' => '<div class="col-sm-12 col-md-7">'
                )
            );
			
            echo $this->Form->end(array('label' => __('Envoyer'), 'class' => 'btn btn-pink btn-pink-modified', 'before' => '<div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.4s" style="visibility: visible;-webkit-animation-delay: 0.4s; -moz-animation-delay: 0.4s; animation-delay: 0.4s;">
<div class="col-sm-12 col-md-offset-4 col-md-8">', 'after' => '</div></div>', 'div' => array('class' => 'form-group mt20 wow fadeIn')));
      // echo '</div>';
	   }else{
            echo __('Vous n\'avez consulté aucun expert ou aucun expert récemment.').' '. $this->Html->link(__('Rendez-vous ici'),array('controller' => 'home', 'action' => 'index')) .' '.__('pour en consulter un.');
        }
        ?>
				  	</div>
    			</div><!--content_box END-->
			</div><!--col-9 END-->
                            
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
				<!--col-md-3 END-->
        </div><!--row END-->
    </section><!--expert-list END-->
</div>
<script>
$('#review_stars2').raty({
  half   : true,
  size   : 15,
  score:   5,
  target : '#evaluation_expert',
  targetText: '',
  targetType: 'hint',
  hints  : ['<?php echo __('Mauvais'); ?>', '<?php echo __('Peu satisfaisant'); ?>','<?php echo __('Satisfaisant'); ?>','<?php echo __('Bon'); ?>','<?php echo __('Excellent'); ?>'],
   click: function(score, evt) {
   	$("#AccountRate").val($("input[name=score]").val());
  }
});
</script>
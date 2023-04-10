<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes parrainages') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes parrainages') ?></h2>
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
										'name'  =>  '<span class="active">'.__('Mes parrainages').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->
					<?php if($this->FrontBlock->getPageBlocTexte(326)){ ?>
				  	<div class="box_sponsorshi_info well well-account well-small">
						<div class="row">
							<div class="col-sm-12 col-md-12">
				  				<?php echo $this->FrontBlock->getPageBlocTexte(326); ?>
							</div>
							
						</div>
				  	</div>
						<?php } ?>
					<?php	
						if($nb_accept > 0 || $nb_done > 0 || $nb_win > 0 ){
					?>
					<div class="box_sponsorshi_slide" style="margin-bottom:60px;">
							<div class="row">
								<div class=" hidden-xs col-sm-4 col-md-4 sponsor_slide_left">

								</div>
								<div class="col-xs-12  col-sm-8 col-md-8 sponsor_slide_center">
									<div class="col-xs-12  col-sm-6 col-md-6">
										<span class="sponsor_slide_center_titre_agent"><?php echo __('total de vos'); ?><br /><?php echo __('gains obtenus'); ?></span>
									</div>
									<div class="col-xs-12  col-sm-6 col-md-6">
										<span class="sponsor_slide_center_titre_agent_gain"><?php echo $nb_win ?><i></i></span>
									</div>
								</div>
								
							</div>
						</div>
					<div class="box_sponsorship_dash well well-account well-small">
						<div class="row">
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_accept"></i><span class="box_sponsorship_dash_label"><?php echo __('Filleul(s) ayant vu votre invitation'); ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_accept; ?></span>
							</div>
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_donet"></i><span class="box_sponsorship_dash_label"><?php echo __('Filleul(s) inscrit après invitation'); ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_done; ?></span>
							</div>
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_bonus"></i><span class="box_sponsorship_dash_label"><?php echo __('Bonus gagné(s) grâce à vos filleuls'); ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_win; ?></span>
							</div>
						</div>
				  	</div>
					<div class=" well well-account well-small">	
						<div class="row">
								<div class="col-sm-12 col-md-12">
									<p style="text-align:center"><?php echo __('Obtenez plus de revenus supplémentaires en invitant de nouveaux consultants filleuls !'); ?></p>
								</div>
								<div class="col-sm-12 col-md-12">
									<p style="display:block;margin:10px 0;text-align:center;"><?php echo $this->Html->link(__('Parrainez'), array('controller' => 'sponsorship', 'action' => 'agent'), array('title' => __('Parrainez'), 'class' => 'btn btn-pink ')); ?></p>
								</div>
							</div>
				  	</div>
					<?php
						}else{
					?>
						<div class="box_sponsorshi_info well well-account well-small">
							<div class="row">
								<div class="col-sm-12 col-md-12">
									<h2  style="text-align:center"><?php echo __('Vous n\'avez pas encore de gains parrainage'); ?></h2>
									<p><?php echo __('Vous pouvez gagner des revenus supplémentaires en invitant ou parrainant des consultants-filleuls ! Faîtes-les profiter d\'une agents privée de qualité. Lors de leurs prochaines consultations sur Spiriteo, avec vous ou un autre expert, vous obtiendrez ').$sponsor_gain.__('% à vie des revenus générés par vos filleuls en plus de votre rémunération !'); ?></p>
								</div>
								<div class="col-sm-12 col-md-12">
									<p style="display:block;margin:10px 0;text-align:center;"><?php echo $this->Html->link(__('Parrainez'), array('controller' => 'sponsorship', 'action' => 'agent'), array('title' => __('Parrainez'), 'class' => 'btn btn-pink ')); ?></p>
								</div>
							</div>
						</div>
					<?php
						}
					?>
					
						
					

					</div><!--content_box END-->
				

				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes gains parrainages') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes gains parrainages') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  __('Accueil'),
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Mes gains parrainages').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

				  
						
					<?php	
						if($nb_accept > 0 || $nb_done > 0 || $nb_win > 0 || $nb_win_wait > 0){
					?>
					<div class="box_sponsorship_dash well well-account well-small">
						<div class="row">
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_accept"></i><span class="box_sponsorship_dash_label"><?php echo __('Filleul(s) ayant vu votre invitation') ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_accept; ?></span>
							</div>
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_donet"></i><span class="box_sponsorship_dash_label"><?php echo __('Filleul(s) inscrit(s) apès invitation') ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_done; ?></span>
							</div>
							<div class="col-sm-12 col-md-4">
				  				<i class="box_sponsorship_dash_bonus"></i><span class="box_sponsorship_dash_label"><?php echo __('Bonus gagné(s) grâce à vos filleul(s)') ?></span><span class="box_sponsorship_dash_value"><?php echo $nb_win; ?></span>
							</div>
						</div>
					</div>
					<div class="box_sponsorshi_info well well-account well-small">
						<div class="row">
							<div class="col-sm-12 col-md-6">
				  				<!--<i class="box_sponsorship_dash_bonus_reclam"></i>--><p style="text-align:center"><?php echo __('Bonus en attente de réclamation') ?></p><p class="box_sponsorship_dash_value"  style="text-align:center"><?php echo $nb_win_wait; ?></p>
							</div>
							<div class="col-sm-12 col-md-6">
									<p style="text-align:center"><?php echo __('Cliquez pour débloquer votre récompense'); ?></p>
									<p style="display:block;margin:10px 0;text-align:center;"><?php echo $this->Html->link(__('Débloquer'), array('controller' => 'sponsorship', 'action' => 'unlock'), array('title' => __('Réclamer ma récompense'), 'class' => 'btn btn-pink '.$btn_class)); ?></p>
							</div>
						</div>
				  	</div>
					<div class=" well well-account well-small">	
						<div class="row">
								<div class="col-sm-12 col-md-12">
									<p style="text-align:center"><?php echo __('Obtenez plus de crédits supplémentaires en invitant de nouveaux amis !'); ?></p>
								</div>
								<div class="col-sm-12 col-md-12">
									<p style="display:block;margin:10px 0;text-align:center;"><?php echo $this->Html->link(__('Parrainez'), array('controller' => 'sponsorship', 'action' => 'client'), array('title' => __('Parrainez'), 'class' => 'btn btn-pink ')); ?></p>
								</div>
							</div>
				  	</div>
					<?php
						}else{
					?>
						<div class="box_sponsorshi_info well well-account well-small">
							<div class="row">
								<div class="col-sm-12 col-md-12">
									<h2><?php echo __('Vous n\'avez pas encore de gains parrainage'); ?></h2>
									<p><?php echo __('Vous pouvez gagner des crédits supplémentaires en invitant vos amis ! Faîtes-les profiter de 5 Min de agents privée de qualité. Lors de leur première consultation sur Spiriteo vous obtiendrez ').$sponsor_gain.__('€ de crédits offerts – Plus vous parrainez, plus vous gagnez !'); ?>
									</p>
								</div>
								<div class="col-sm-12 col-md-12">
									<p style="display:block;margin:10px 0;text-align:center;"><?php echo $this->Html->link(__('Parrainez'), array('controller' => 'sponsorship', 'action' => 'client'), array('title' => __('Parrainez'), 'class' => 'btn btn-pink ')); ?></p>
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
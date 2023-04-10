<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes bonus mensuels et rémunérations') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes bonus mensuels et rémunérations') ?></h2>
							 <?php
						echo $this->Session->flash();
						/* titre de page */
						echo $this->element('title', array(
							'breadcrumb' => array(
								0   =>  array(
									'name'  =>  'Accueil',
									'link'  =>  Router::url('/',true)
								),
								1   =>  array(
									'name'  =>  __('Mes bonus mensuels et rémunérations'),
									'link'  =>  ''
								)
							)
						));
		?>

						</div><!--page-header END-->
						
						<div class=" well well-account well-small"><div class="row">
							<div class="col-sm-12 col-md-6">
								<?php
								echo $this->Frontblock->getAccountBonus();
								?>
							</div>
							<div class="col-sm-12 col-md-6">
								<?php
								echo $this->Frontblock->getAccountRemuneration();
								?>
							</div>
						</div></div>
						
					</div>
			</div><!--col-9 END-->
            <?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?><!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
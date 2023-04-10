<div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-12">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Support - Traitement') ?>
							 <?php
							if(!empty($user_level) && $user_level != 'moderator'){
								echo $this->Metronic->getLinkButton(
                            __('Modifier'),
                            array('controller' => 'pages', 'action' => 'edit', 464,'admin' => true),
                            'btn green pull-right ',
                            ''
                        );
							}
							
						echo $this->Session->flash();
		?>
</h2>
						</div><!--page-header END-->
 <?php 
				$idlang = $this->Session->read('Config.id_lang');
						echo $this->FrontBlock->getPageBlocTextebyLang(464,$idlang);
						
						?>

						
                    </div><!--content_box END-->
			</div><!--col-9 END-->
            <!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
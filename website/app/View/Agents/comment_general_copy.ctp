<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Comment ça marche ?') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40 page-img">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mode d\'emploi') ?></h2>
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
									'name'  =>  __('Mode d\'emploi'),
									'link'  =>  ''
								)
							)
						));
		?>

						</div><!--page-header END-->
 <?php 
				$idlang = $this->Session->read('Config.id_lang');
						$page = $this->FrontBlock->getPageBlocTextebyLang(314,$idlang);
						if(substr_count($page,'.pdf')){
							$cut = explode('src="',$page);
							$end_cut = explode('"',$cut[1]);
							$pdf = $end_cut[0];
							echo '<div><object  data="'.$pdf.'" type="application/pdf" title="'.__('Mode d\'emploi').'" style="width:100%;height:800px">
              </object></div>';
						}else{
							echo $page;
						}
						
						?>

						
                    </div><!--content_box END-->
			</div><!--col-9 END-->
            <?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
            <!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>

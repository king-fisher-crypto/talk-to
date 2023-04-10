<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Parrainer') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Parrainer') ?></h2>
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
											'name'  =>  '<span class="active">'.__('Parrainer').'</span>',
											'class' => 'active'
										)
									)
								));
							?>

						</div><!--page-header END-->

						<div class="box_sponsorshi_slide box_sponsorshi_slide_min">
							<div class="row2">
								<div class=" hidden-xs col-sm-4 col-md-4 sponsor_slide_left">

								</div>
								<div class="col-xs-12  col-sm-4 col-md-4 sponsor_slide_center">
									<span class="sponsor_slide_center_titre"><?php echo __('parrainez'); ?></span>
									<span class="sponsor_slide_center_titre_bold"><?php echo __('et gagnez'); ?></span>
									<span class="sponsor_slide_center_price"><?php echo $sponsor_gain ?><i>€*</i></span>
								</div>
								<div class="hidden-xs col-sm-4 col-md-4 sponsor_slide_right">

								</div>
							</div>
						</div>
						<div class="box_sponsorshi_info">	
							<?php echo $this->FrontBlock->getPageBlocTexte(324); ?>	
						</div>	
						<div class="box_sponsorshi_graph">
							<div class="row">
								<div class=" col-xs-4 col-sm-4 col-md-4 box_sponsorshi_graph_arrow box_sponsorshi_graph_content">
									<span class="sponsorshi_graph_content_num">1</span>
									<span class="sponsorshi_graph_content_icon sponsorshi_graph_content_icon1"></span>
									<span class="sponsorshi_graph_content_txt"><?php echo __('<b>Je parraine ou<br />j\'envoie mon code privé </b><br />à mes amis'); ?></span>
								</div>
								<div class="col-xs-4 col-sm-4 col-md-4 box_sponsorshi_graph_arrow box_sponsorshi_graph_content">
									<span class="sponsorshi_graph_content_num">2</span>
									<span class="sponsorshi_graph_content_icon sponsorshi_graph_content_icon2"></span>
									<span class="sponsorshi_graph_content_txt"><?php echo __('<b>Mes amis<br />s\'inscrivent</b> sur<br />talkappdev.com'); ?></span>
								</div>
								<div class="col-xs-4  col-sm-4 col-md-4 box_sponsorshi_graph_content">
									<span class="sponsorshi_graph_content_num">3</span>
									<span class="sponsorshi_graph_content_icon sponsorshi_graph_content_icon3"></span>
									<span class="sponsorshi_graph_content_txt"><?php echo __('<b>Je reçois').' '.$sponsor_gain.__('€* de crédit</b><br />dès la 1ère<br />consultation d’un filleul !'); ?></span>
								</div>
							</div>
						</div>
						<div class="box_sponsorship_share_content well well-account well-small">
							<div class="box_sponsorship_share_content_titrebox">
								<p class="box_sponsorship_share_content_titre"><?php echo __('Je partage mon lien privé'); ?></p>
							</div>
							<div class="box_sponsorship_share_content_urlbox">
								<div class="">
									<p><?php echo __('Partagez votre code privé à l\'aide de votre lien personnalisé :'); ?></p>
									<p><span class="box_sponsorship_share_content_url"><?php echo $url_share ; ?></span><span class="box_sponsorship_share_content_btn"><?php echo __('copier'); ?></span></p>
								</div>
							</div>
							<div class="box_sponsorship_share_content_sharebox">
								<div class="col-sm-12 col-md-12">
									<p ><?php echo __('Ou choisissez un autre mode de partage :'); ?></p>
									<div class="row">
										<a class="box_sponsorship_share_content_sharebox_content  box_sponsorship_share_content_sharebox_content_mail">
										</a>
										<a class="box_sponsorship_share_content_sharebox_content  box_sponsorship_share_content_sharebox_content_facebook">
										</a>
										<a class="box_sponsorship_share_content_sharebox_content  box_sponsorship_share_content_sharebox_content_twitter">
										</a>
										<a class="box_sponsorship_share_content_sharebox_content  box_sponsorship_share_content_sharebox_content_google">
										</a>
									</div>
								</div>
							</div>
							<div class="box_sponsorship_invit">
							
								<div class="col-sm-12 col-md-12">
								
									<p><?php echo __('Renseignez les emails de vos amis'); ?></p>
									<?php echo $this->Form->create('Sponsorship', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
														  'inputDefaults' => array(
															  'div' => '',
															 
															  'class' => 'form-control'
														  )
										));

										echo $this->Form->input('email1', array(
											'label' => '',
											'value' => '',
											'after' => '<span class="box_sponsorship_invit_gain">+'.$sponsor_gain.'€<supp>*</supp></span>',
											'placeholder' => 'Email',
											'type'  => 'text'
										));
									echo $this->Form->input('email2', array(
											'label' => '',
											'value' => '',
											'after' => '<span class="box_sponsorship_invit_gain">+'.$sponsor_gain.'€<supp>*</supp></span>',
											'placeholder' => 'Email',
											'type'  => 'text'
										));
									echo $this->Form->input('email3', array(
											'label' => '',
											'value' => '',
											'after' => '<span class="box_sponsorship_invit_gain">+'.$sponsor_gain.'€<supp>*</supp></span>',
											'placeholder' => 'Email',
											'type'  => 'text'
										));
										echo $this->Form->end(array('label' => __('envoyer'), 'class' => 'btn btn-sponsor', 'before' => '<div class="col-xs-12 col-sm-6 col-md-6"><a class="btn btn-sponsorwhite" data-toggle="modal" data-target="#preview_mail_sponsor">Prévisualiser</a></div><div class="col-xs-12 col-sm-6 col-md-6">', 'after' => '</div>')); ?>
								</div>
							</div>
						</div>
						<div class="box_sponsorship_txt_legal">
							<p><?php echo __('* Un crédit de').' '.$sponsor_gain.__('€ vous sont offerts dès lors que l\'un de vos filleuls effectue une consultation agents d\'un montant minimum de').' '.$sponsor_palier.__('€. Les crédits cumulés sont consultables dans votre tableau de bord et peuvent être débloqués dès que vous le souhaitez. Les gains débloqués sont crédités automatiquement sur votre compte client.'); ?></p>
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

<div class="modal fade" id="preview_mail_sponsor" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header modal-header-mail" style="padding:5px;border-bottom:0px;"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
aria-hidden="true">&times;</span></button></div>
			<div class="modal-body" style="padding:0">
				<?php echo $this->FrontBlock->getMailBlock(325); ?>	
			</div>
		</div>
	</div>
</div>
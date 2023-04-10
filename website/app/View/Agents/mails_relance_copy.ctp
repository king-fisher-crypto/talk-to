<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes relances') ?></h1>
	</section>
   <?php
    echo $this->Html->css('/theme/default/css/daterangepicker', array('block' => 'css'));
	echo $this->Html->script('/theme/default/js/nx_mail_relance2', array('block' => 'script'));
	 echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
	  echo $this->Html->css('/assets/plugins/font-awesome/css/font-awesome.min', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
      echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
	 echo $this->Html->script('/assets/scripts/app', array('block' => 'script'));
	//  echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
?> <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
<?php

$titre = 'Relance par message';
?>



					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s">  <?php echo $titre; ?></h2>
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
									'name'  =>  $titre,
									'link'  =>  ''
								)
							)
						));
		?>

					</div><!--page-header END-->
					<?php  
							$idlang = $this->Session->read('Config.id_lang');
							echo $this->FrontBlock->getPageBlocTextebyLang(310,$idlang); 
						?>
					</div><!--content_box END-->
                    
					<div class="row">
						
							<div class="col-md-12 col-sm-12">
							<div class="content_box mb20 wow fadeIn" data-wow-delay="0.4s">
							
							<ul class="nav nav-tabs nav-justified mails-tabs" role="tablist">
								 <li role="relance_new"  class="customTab singl-line active mailRelanceNew" param="message"><a href="#relance_new" data-toggle="tab"><span class="glyphicon glyphicon-inbox"></span> <?php echo __('Nouvelle relance'); ?></a></li>
								 <li role="relance_refus"  class="customTab singl-line mailRelanceRefus" param="message"><a href="#relance_refus" data-toggle="tab"><span class="glyphicon glyphicon-remove"></span> <?php echo __('Refusées'); if(isset($nb_refus) && $nb_refus > 0) echo ' ('.$nb_refus.')'; ?></a></li>
							</ul>
							<div class="clearifix"></div>
							<div class="tab-content" style="margin-top:25px;">
							<div id="relance_new" class="active tab-pane" role="tabpanel">
							
							<?php
							 echo $this->Form->create('Agent', array('action' => 'mails_relance','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-6">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control'
                                               )
        							));
								
								echo $this->Form->inputs(array(
									 'listing_client' => array('type' => 'hidden', 'value' => ''),
									'title' => array('label' => false, 'required' => true, 'type' => 'text', 'value' => __('Vous avez un nouveau message de '.$agent_pseudo), 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">')
								));
								echo '<span class="alerte_bonjour" style="display:block;margin:-20px 0 15px 0;font-size:12px;">Champs titre par défaut, mais celui-ci est modifiable par vos soins.</span>';
								
								
								
								echo $this->Form->inputs(array(
									'bonjour' => array('label' => false, 'required' => true, 'type' => 'text', 'value' => __('Bonjour'), 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">')
								));
								echo '<span class="alerte_bonjour" style="display:block;margin:-20px 0 15px 0;font-size:12px;">Nous vous remercions de personnaliser le nom ou pseudo de votre consultant par son vrai prénom.</span>';
								//echo '<fieldset><h3>Bonjour CLIENT,</h3>';
								
								echo $this->Form->inputs(array(
									'content' => array('label' => false, 'required' => true,'maxlength' => 1000, 'type' => 'textarea', 'placeholder' => __('Votre message.'), 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 12)
								));
								
								echo '<div class="row"><div class="col-lg-3 col-md-3 col-sm-12">'.$this->Html->image($agent_photo, array('class'			=> 'small-profile2 img-responsive img-circle',
											'alt'			=> $agent_pseudo )).'</div>';
								
								
								echo '<div class="col-lg-9 col-md-9 col-sm-12" style="margin-left:-15px">'.$this->Form->inputs(array(
									'signature' => array('label' => false, 'required' => true, 'type' => 'textarea', 'placeholder' => __('Votre signature personnalisé'), 'between' => '<div>', 'rows' => 2)
								)).'</div></div>';
								
								echo $this->Form->end(array(
									'label' => __('Envoyer'),
									'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0',
									'div' => array('class' => 'form-group margin-tp')
								));
								
								
								?>
									<div style="display:inline-block;margin:10px 0;width:100%;border-top:1px solid #eee;"><div class="form-group wow fadeIn mb0" data-wow-delay="0.4s">
										<p><br /><?php echo __('Choisissez le ou les clients auxquels vous souhaitez envoyer votre message…'); ?></p>
											<div class="col-sm-5 col-md-5 col-xs-12" style="padding-left:0px;">
													 <div class="form-group wow fadeIn mb0 date_relance" data-wow-delay="0.4s">
													<?php echo $this->Metronic->getDateInputBigFront(); ?>
											  </div>
										  </div>
											<div class="col-sm-4 col-xs-12 col-md-4">
											<?php
											$options = array('' => __('Déjà relancé ce mois-ci'),
															 '0' => __('0 fois'),
															 '1' => __('1 fois'),
															 '2' => __('2 fois')
											);
											echo $this->Form->select('input', $options, array('id' => 'select_relance', 'class' => 'form-control', 'value' => $this->params->here.(isset($this->params->query['relance']) ?'?relance='.$this->params->query['relance']:''), 'empty' => false));
											?>
											</div>
											<?php
											
											echo $this->Form->inputs(array(
												'content' => array('label' => false, 'type' => 'text', 'id' => 'pseudo_relance', 'placeholder' => __('Pseudo'),'between' => '<div class="col-sm-3 col-md-3 col-xs-12">')
											));
											?>
										</div></div>
											<div class="table-responsive" >
			  	 
				  	 <table class="table table-striped no-border table-mobile text-center table_client_relance"> 
                     	 <?php 
						 
						 if(empty($clients)) : ?>
                <?php echo __('Vous n\'avez eu aucune communication avec un client.'); ?>
            <?php else : ?>
				  	 	<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><!--<input type="checkbox" />--></th> 
				  	 			<th class="text-left"><?php echo __('Client'); ?></th> 
				  	 			<th class="text-left"><?php echo __('Dernier message'); ?></th> 
				  	 			<th class="text-left"><?php echo __('Dernière consultation'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Avis'); ?></th>
				  	 			<th class="text-center"><?php echo __('Notes'); ?></th> 
								<th class="text-center"><a style="cursor:pointer;margin-bottom:0px" class="trie_date_relance btn btn-pink btn-pink-modified btn-small-modified"><?php echo __('A relancer'); ?></a></th>
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody> 
                        	<?php 
							
							
							
							foreach($clients as $client) : ?>
				  	 		<tr> 
								<td>
			  	 				<?php 
									if($client['message'] < 1){ ?>
			  	 			<input type="radio" rel="<?php echo $client['user_id'];?>" name="relance_client" />
			  	 			<?php }else{ ?>
			  	 			&nbsp;
			  	 			<?php } ?>
			  	 			</td>
				  	 			<td class="resize-img" style="text-align:left"><?php echo $client['pseudo'];?></td> 
				  	 			<td class="" style="text-align:left"><?php if($client['last_relance']){ echo $client['last_relance']; }else{ echo '<span>'.__('Aucun envoi').'</span>'; }
									//if($client['message']){
								echo $this->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'mails_relance_show'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );													
								//} ?></td> 
				  	 			<td class="" style="text-align:left"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$client['last_com']),'%d/%m/%y %Hh%M');
									echo $this->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'consult_history'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );
									?></td> 
			  	 			<td class=""><?php 
									if($client['reviews']){
									echo $this->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'consult_reviews'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );
										}
									?></td>
				  	 			<td class=""><?php if($client['note_id']){ ?><i class="glyphicon glyphicon-pencil lfloat phonenote_edit" rel="<?=$client['note_id'] ?>"></i><?php } ?></td>
				  	 			 
								<td class=""><?php if($client['date_relance']){ echo '<span>'.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$client['date_relance']),'%d/%m/%y').'</span> <i class="glyphicon glyphicon-remove lfloat daterelance_cancel" rel="'.$client['user_id'].'"></i>'; }else{ ?> <span><i class="glyphicon glyphicon-pencil lfloat daterelance_edit" rel="<?=$client['user_id'] ?>"></i></span><?php } ?>
									</td>
				  	 			
				  	 		</tr> 
							<?php endforeach; ?>
				  	 	</tbody>
                        <?php endif; ?> 
				  	 </table> 
				  	</div>
								
								</div>
								<div id="relance_refus" class="tab-pane" role="tabpanel">
								 <table class="table table-striped no-border table-mobile text-center table_mail_relance_refus"> 
								  <?php 
						 
						 if(empty($refus)) : ?>
                <?php echo __('Vous n\'avez eu aucun message refusé.'); ?>
            <?php else : ?>
				  	 	<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><input type="checkbox" /></th> 
				  	 			<th class="text-center"><?php echo __('Client'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Date'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Message'); ?></th> 
				  	 			<th class="text-center"><?php
								echo $this->Form->button('<i class="glyphicon glyphicon-remove icon_margin_right_5"></i> '.__('Supprimer votre sélection'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 archive_all_relancemail',
																'href' => $this->Html->url(array('controller' => 'agents', 'action' => 'closeAllMessageRelance'),true),
																'label' => __('Voulez-vous vraiment supprimer ces messages ?'),
																'type'  => 'button'
															)
														);
								?></th>
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody> 
                        	<?php 
							
							
							
							foreach($refus as $refu) : ?>
				  	 		<tr> 
								<td>	<input type="checkbox" rel="<?php echo $refu['Message']['id'];?>" /></td>
								<td>
			  	 				<?php echo $refu['User']['firstname']; ?>
			  	 			</td>
				  	 			<td>
			  	 				<?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$refu['Message']['date_add']),' %d/%m/%y %H:%M'); ?>
			  	 			</td>
			  	 			<td style="text-align:left;line-height:14px">
			  	 				<?php 
								
								$tab1 = explode('###',$refu['Message']['content']);
								$tabcontent = explode('<!---->',$tab1[1]);
								$contenu = nl2br($tabcontent[1]).'<br /><br />'.nl2br($tabcontent[2]).'<br /><br />'.nl2br($tabcontent[3]);
								echo '<p>Raison:<br /><strong>'.$tab1[0].'</strong></p><hr>';
								echo '<p>'.$contenu.'</p>'; ?>
			  	 			</td>
			  	 			<td>
			  	 				<?php
								echo $this->Form->button('<i class="glyphicon glyphicon-remove icon_margin_right_5"></i> '.__('Supprimer'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 archive_relancemail',
																'href' => $this->Html->url(array('controller' => 'agents', 'action' => 'closeMessageRelance'),true),
																'mail' => $refu['Message']['id'],
																'label' => __('Voulez-vous vraiment supprimer ce message ?'),
																'type'  => 'button'
															)
														);
								?>
			  	 			</td>
				  	 		</tr> 
							<?php endforeach; ?>
				  	 	</tbody>
                        <?php endif; ?> 
								 
								 
									</table>
								 
								</div></div>

							</div><!--content-box END-->

							
							</div><!--col-sm-12-->

						</div><!--row END-->


				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
<div id="dialog-confirm-mail" title="Envoyer une relance" style="display:none">
	<p style="color:#5a449b;font-size:13px;"><?php echo __('Votre message sera envoyé a'); ?> <!--<span class="nb_destinataires"></span> destinataire(s)--> :</p>
	<p style="color:#5a449b;font-size:13px;" class="destinataires_listing"></p>
  <p>&nbsp;</p>
   <p style="color:#5a449b;font-size:13px;"><?php echo __('Confirmer l\'envoi de ce message ?'); ?></p>
	<p>&nbsp;</p>
	<div id="message_relance_body" style="display:inline-block;width:100%;background:#eee;text-align: left;padding:10px;font-size:12px;"></div>
	<p>&nbsp;</p>
</div>
<div id="dialog-relance-date" title="Programmer une relance" style="display:none">
	<p style="color:#5a449b;font-size:13px;"><?php echo __('En émettant une date de relance, pensez à vous faire un pense bête sur le sujet dans le bloc « notes » client.'); ?></p>
  <p>&nbsp;</p>
   <p style="color:#5a449b;font-size:13px;"><?php echo __('Date de relance :'); ?> <input id="relance_date_input" placeholder="JJ/MM/AAAA"></p>
	<p>&nbsp;</p>
	<p style="color:#5a449b;font-size:13px;"><?php echo __('Un email de rappel vous sera automatiquement envoyé à la date indiquée par vos soins.'); ?></p>
</div>
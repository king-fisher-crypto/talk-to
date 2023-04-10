<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Votre mail') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
                    	<?php
						$fiche_link = $this->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
								'agent_number'  => $agent['User']['agent_number']
							),
							array(
								'title'         => $agent['User']['pseudo']
							)
						);
						
						?>
                        
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Posez votre question à').' '.$agent['User']['pseudo'] ?></h2>
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
										'name'  =>  '<span class="active">'.__('Votre mail').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

				  	<div class="form-horizontal box_account well well-account well-small">
                    
    <?php
        echo '<h3>Conseils :</h3>';

        echo $page_content;

       
			if($agent['User']['mail_infos_v']){			
		 echo '<h3>'.__('Ce dont ').$agent['User']['pseudo'].__(' a besoin pour effectuer une consultation par Email :').'</h3>';				
						
			echo '<p style="padding-bottom:15px;">'.nl2br($agent['User']['mail_infos_v']).'</p>';	
			}
						
		 //echo '<span class=" col-lg-4 required" for="MessageContent"></span><p class="col-lg-7"><span class="credit-mail">'.$creditMail.'</span> '.__('crédits seront prélévés pour votre consultation.').'</p>';				
						
						
    //echo '<p>'.__('Une réponse vous sera apportée par notre agent dans un délai maximum de 6 heures').'</p>';
        echo $this->Form->create('Message', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
                                                  'inputDefaults' => array(
                                                      'div' => 'form-group',
                                                      'between' => '<div class="col-lg-7">',
                                                      'after' => '</div>',
                                                      'class' => 'form-control'
                                                  )
        ));

        echo $this->Form->inputs(array(
            'to_id' => array('type' => 'hidden', 'value' => $agent['User']['id']),
            'content' => array('label' => array('text' => __('Votre message'), 'class' => 'control-label col-lg-4 required'), 'required' => true, 'type' => 'textarea', 'value'=>$message_in_live),
            //'attachment[]' => array('label' => array('text' => __('Joindre une ou deux photo(s)<br /> (.jpg .png .gif)'), 'class' => 'control-label col-lg-4 norequired'), 'type' => 'file', 'accept' => 'image/*', 'multiple' => true),
			//'attachment2' => array('label' => array('text' => '', 'class' => 'control-label col-lg-4 norequired'), 'type' => 'file', 'accept' => 'image/*')
        ));
		//echo '<div class="form-group"><label for="MessageAttachment" class="control-label col-lg-4 norequired">'.__('Joindre une ou deux photo(s)<br> (.jpg .png .gif)').'</label><div class="col-lg-7"><input type="file" name="data[Message][attachment][]" class="form-control inputfiletwo" accept="image/*" multiple="multiple" id="MessageAttachment"></div></div>';
 echo '<div class="form-group mailcustomerupload">
						<label for="MessageAttachment" class="control-label col-lg-4 norequired ">Joindre une ou deux photo(s)<br> (.jpg .png .gif)</label>
						<div class="col-lg-7">
							<input type="file" name="data[Message][attachment]" multiple="multiple" data-fileuploader-limit="2">
						</div>
					</div>';
        echo $this->Form->end(array(
            'label' => 'Envoyer',
            'class' => ' btn btn-pink btn-pink-modified btn-newmail',
            'div' => array('class' => 'form-group margin-tp')
        ));
    ?>
<span class="goback">
								<a href="<?php echo $fiche_link ?>" title="<?php echo $agent['Agent']['pseudo'] ?>"><?=__('Retour sur le profil de l\'expert') ?></a> 
							</span>
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
<div id="dialog-confirm-mail" title="<?=__('Envoyer un email') ?>" style="display:none">
	<p style="color:#5a449b;font-size:13px;"><?=__('Je confirme l\'envoi de cette consultation par Email ainsi que le débit de 15 minutes de mon compte') ?></p>
</div>
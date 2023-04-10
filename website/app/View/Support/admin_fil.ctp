<?php
	echo $this->Html->script('/theme/default/js/selectize.js', array('block' => 'script'));

	echo $this->Html->css('/theme/default/css/selectize', array('block' => 'css'));
	echo $this->Html->css('/theme/default/css/selectize.default', array('block' => 'css'));

    echo $this->Metronic->titlePage(__('Supports'),__('Messages'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Support'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'message', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>
<?php
    if( $is_live && ($userConnectedSupportLevel['SupportAdmin']['level']>=2 ) ){

    ?>
<div data-logo="/media/logo/default.jpg" data-site="SpiriteoDev" class="text-center alert alert- alert-dismissable">
    <?php
    echo $this->Metronic->getLinkButton(
        __('Debloquer acces ('.$live_person.')')   ,
        array('controller' => 'support', 'action' => 'debloque_support', 'admin' => true, 'id' => $support['Support']['id']),
        'btn red',
        'icon-check'
    );
    ?>
    </div>
<br>
<?php }?>
<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Ticket'); ?> #<?=$support['Support']['id']  ?> : <?php echo CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$support['Support']['date_add']),'%d/%m/%y %H:%M:%S'); ?> - <?=$support['Support']['title']  ?></div>
        </div>
        <div class="portlet-body" style="display:inline-block;width:100%;">
			
			<div class="row-fluid dashboard-stat" style="padding:5px;margin-bottom:0px">
				<div data-desktop="span3" class="span3 responsive">
					<fieldset>
    					<legend style="font-size:14px;">CONTACT</legend>
						<div style="background:#ddd;padding:5px;min-height:150px;">
							<div class="visual" style="padding-left:0;"><i class="<?php if($user['User']['role'] == 'agent') echo 'icon-user-md'; else echo 'icon-user'; ?>"></i>
							<?php if($user['User']['role'] == 'guest'):?>
								<?= __('Invité'); ?>
							<?php endif;?>

							<?php if($user['User']['role'] == 'agent'):?>
								<?= __('Agent'); ?>
							<?php endif;?>

							<?php if($user['User']['role'] == 'client'):?>
								<?= __('Client'); ?>
							<?php endif;?>
							</div>
							<div class="infos">
								<?php
									if($user['User']['role'] == 'guest'){
										?>
										<div class="desc"><?=$user['User']['firstname'].' '.$user['User']['lastname']  ?></div>
										<div class="desc"><?=$user['User']['email']  ?></div>
										<?php
									}

									if($user['User']['role'] == 'agent'){
										?>
										<div class="desc"><?=$user['User']['pseudo']  ?></div>
										<div class="desc"><?=$user['User']['firstname'].' '.$user['User']['lastname']  ?></div>
										<div class="desc"><?=$user['User']['email']  ?></div>
										<div style="margin-top:15px;"><?php echo $this->Html->link(__('Voir le profil'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false, 'target'=>'_blank')
                                        ) ?></div>
										<?php
									}

									if($user['User']['role'] == 'client'){
										?>
										<div class="desc"><?=$user['User']['firstname'].' '.$user['User']['lastname']  ?></div>
										<div class="desc"><?=$user['User']['email']  ?></div>
										<div style="margin-top:15px;"><?php echo $this->Html->link(__('Voir le profil'),
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false, 'target'=>'_blank')
                                        ) ?></div>
										<?php
									}

								?>
							</div>
						</div>
  					</fieldset>
				</div>
				<div data-desktop="span3" class="span3 responsive">
					<fieldset>
    					<legend style="font-size:14px;">SUPPORT</legend>
						<div style="background:#ddd;padding:15px 5px 5px 5px;min-height:140px;">
							 <?php
								echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
								?>
								<label style="float:left;line-height:30px;width:55px;"><?php echo __('Service :'); ?></label><?php
								?>
								<select id="service" name="data[Support][service]" style="margin-left:5px;float:left;width:105px;" <?php if($is_live) echo 'readonly'; ?>  >
								<?php
									foreach($services as $serv){
										$selected = '';
										if($serv['SupportService']['id'] == $support['Support']['service_id'])$selected = 'selected';
										echo '<option value="'.$serv['SupportService']['id'].'" '.$selected.'>'.$serv['SupportService']['name'].'</option>';
									}

								 ?>
								</select>
								<?php
								echo '<input name="data[Support][id]" type="hidden" value="'.$support['Support']['id'].'">';
								if(!$is_live)
								echo '<input class="btn green" type="submit" value="Changer" style="float:left;margin-left:5px;padding:5px 10px">';
								echo '</form>'
							?>
							 <?php
								echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
								?>
								<label style="float:left;line-height:30px;width:55px;"><?php echo __('Level :'); ?></label>
								<select id="level" name="data[Support][level]" style="margin-left:5px;float:left;width:105px;" <?php if($is_live) echo 'readonly'; ?>>
								<?php
									foreach($levels as $level){
										$selected = '';
										if($support['Support']['level'] == $level)$selected = 'selected';
										$name = $level;
										if($level == 1)$name .= ' - Admin';
										if($level == 2)$name .= ' - Admin Supp';
										if($level == 3)$name .= ' - Technique';
										if($level == 4)$name .= ' - RH';
										if($level == 5)$name .= ' - Manager';
										echo '<option value="'.$level.'" '.$selected.'>'.$name.'</option>';
									}

								 ?>
								</select>
								<?php

								echo '<input name="data[Support][id]" type="hidden" value="'.$support['Support']['id'].'">';
								if(!$is_live)
								echo '<input class="btn green" type="submit" value="Changer"  style="float:left;margin-left:5px;padding:5px 10px">';
								echo '</form>'
							?>
						</div>
  					</fieldset>
				</div>
				
				<div data-desktop="span4" class="span6 responsive">
					<fieldset>
    					<legend style="font-size:14px;">COMMENTAIRE</legend>
						<div style="background:#ddd;padding:5px;min-height:150px;">
							 <?php
									$readonly = '';
								 if($is_live) $readonly = 'readonly';
								echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1, 'style'=>'width:90%;margin-bottom:0'));
								echo '<textarea name="data[Support][comm]" class="input  margin-left margin-right" placeholder="Commentaires privés" style="width:100%;" rows="4" id="SupportComm" '.$readonly.'>'.$support['Support']['comm'].'</textarea>';
								echo '<input name="data[Support][id]" type="hidden" value="'.$support['Support']['id'].'">';
								if(!$is_live)
								echo '<input class="btn green" type="submit" value="Enregistrer" style="margin-top:5px;margin-left:5px;">';
								echo '</form>'
							?>
						</div>
  					</fieldset>
				</div>
			
			</div>
			<div class="row-fluid dashboard-stat" style="padding:5px;">
				<div data-desktop="span12" class="span12 responsive">
					<fieldset>
    					<legend style="font-size:14px;">CLASSSIFICATION</legend>
						<div style="background:#ddd;padding:5px;min-height:150px;">
							<label >Choisir</label>
							<?php
								
								$classif_selected = array();
								foreach($classification_message as $classifmessage){
									array_push($classif_selected, $classifmessage['SupportClassificationMessage']['classification_id']);
								}
									
								echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1, 'style'=>'width:100%;'));
								echo '<select id="support_message_classif" name="data[Support][classifs][]" multiple="multiple" style="width:100%">';	
									foreach($list_classification as $classi){
										foreach($classi as $classif_id =>  $classification_name){
											$selected = '';
											if(in_array($classif_id, $classif_selected)) $selected = 'selected';
											echo '<option value="'.$classif_id.'" '.$selected.'>'.$classification_name.'</option>';
										}
									}
								echo '</select>';
								echo '<input name="data[Support][id]" type="hidden" value="'.$support['Support']['id'].'">';
								if(!$is_live)
								echo '<input class="btn green" type="submit" value="Enregistrer" style="margin-top:5px;margin-left:5px;">';
								echo '</form>';
							?>
						</div>
  					</fieldset>
				</div>
			</div>
			<div class="row-fluid dashboard-stat" style="padding:5px;">
				<?php
					foreach($classification_message as $classimessage){
					?>
					<div data-desktop="span3" class="span3 responsive">
							<div style="border:1px solid #CCC;padding:5px;min-height:100px;">
								<p style="font-size:14px;font-weight:600;"><?=$classimessage['SupportClassification']['num'] .' '.$classimessage['SupportClassification']['name'] ?></p>
								<p><?=$classimessage['SupportClassification']['description'] ?></p>
								<?php if($classimessage['SupportClassification']['solution_link']){ ?>
								<p><a target="_blank" href="<?=$classimessage['SupportClassification']['solution_link'] ?>"><?=_('Voir le traitement') ?></a></p>
								<?php } ?>
							</div>
					</div>
				<?php
					}			
				?>
			</div>
			<div style="clear:both;margin:30px 0;background-color:#ccc;height:1px;width:100%"></div>
			<div class="discussion">
				<?php
                      $moderate_content = '';
				foreach($messages as $mes){
          if($support['Support']['moderate_response'] && $mes['SupportMessage']['id'] == $support['Support']['moderate_response']){

						$moderate_content = $mes['SupportMessage']['content'];
					}
					$align = 'left';
					if($mes['SupportMessage']['from_id'] != $support['Support']['from_id']) {
						$align = 'right';
					}
					//username
					$username = ($mes['SupportMessage']['from_id'] != $support['Support']['from_id']) ? 'Administrateur' : $user['User']['firstname'].' '.$user['User']['lastname'];
				?>
					<div class="fil-line" style="display:inline-block;width:100%;margin-bottom:25px;">
						<div class="span7 pull-<?=$align ?>" style="background:#<?php if($username == 'Administrateur') echo 'ddd'; else echo 'F9F9F9'; ?>;padding:15px;border-radius:15px !important;border:1px solid #<?php if($username == 'Administrateur') echo 'ddd'; else echo 'F9F9F9'; ?>">
							<div class="fil-message-content bulle"><?=nl2br($mes['SupportMessage']['content']); ?></div>
							<?php if(!empty($mes['SupportMessageAttachment'])):?>
								<?php foreach($mes['SupportMessageAttachment'] as $attachmentModel):?>
									<?php
										$attachment = $attachmentModel['name'];
										$attachmentExtension = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
										$img_class = '';
										$displayDownloadLinkText = __('Télécharger la pièce jointe');
										$pictureExtensions = array('png', 'jpg', 'jpeg');//TODO: make it global in future
										$videoExtensions = array('mp4');//TODO: make it global in future
										$audioExtensions = array('mp3');//TODO: make it global in future
										$allowedExtensions = array_merge($pictureExtensions, $videoExtensions, $audioExtensions);
									?>
									<?php if(in_array($attachmentExtension, $allowedExtensions)):?>
										<?php
											$folder = (strlen($mes['SupportMessage']['support_id']) > 1)
														? $mes['SupportMessage']['support_id'][0].'/'.$mes['SupportMessage']['support_id'][1]
														: $mes['SupportMessage']['support_id'];
											$directory = Configure::read('Site.pathSupport') . '/' . $folder;
											if(in_array($attachmentExtension, $pictureExtensions)) {//picture condition
												$front = '<img src="/' . $directory . '/' . $attachment . '" style="height:200px;width:auto;" />';
												$img_class = 'chat_picture';
											} else if(in_array($attachmentExtension, $videoExtensions)) {//video condition
												$front = '<video width="320" height="240" controls><source src="/' . $directory . '/' . $attachment . '" type="video/' . $attachmentExtension . '">Your browser does not support the video tag.</video>';
											} else {
												$front = '<audio controls><source src="/' . $directory . '/' . $attachment . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
											}
											echo $this->Html->link('<div class="fil-message-file" style="margin:15px 0;"><span class="glyphicon glyphicon-paperclip margin_right_5"></span>'.$front.'</div>',
												array(
													'controller' => 'support',
													'action' => 'downloadFile/' . $attachmentModel['id'],
												),
												array(
													'escape' => false,
													'class' => ' btn-xs attachment ' . $img_class
												)
											);
										?>
									<?php else:?>
									<?=$this->Html->link('<div class="fil-message-file" style="margin:15px 0;"><span class="glyphicon glyphicon-paperclip margin_right_5"></span>'.$displayDownloadLinkText.'</div>',
										array(
											'controller' => 'support',
											'action' => 'downloadFile/' . $attachmentModel['id'],
										),
										array(
											'escape' => false,
											'class' => 'btn-xs attachment '
										)
									);?>
									<?php endif;?>
								<?php endforeach;?>
							<?php endif;?>
							<div class="fil-message-who" style="margin-top:15px;"><b><?=$username; ?></b> - <?php echo CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'), $mes['SupportMessage']['date_message']),'%d/%m/%y %H:%M:%S'); ?></div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
				<?php if(!$is_live && $support['Support']['status'] < 2){ ?>
			<div class="support_answer" style="margin-top:30px;">
				<?php
					$user = $this->Session->read('Auth.User');

					$content_value = '<br /><br /><br />Cordialement,<br />'.$user['firstname'].'<br />Et toute l\'équipe Spiriteo';
          $content_value .= '<br /><br /><p style="font-size:10px">Nous vous remercions de répondre dans la continuité de ce message afin que nos équipes et collaborateurs puissent suivre cet échange et à ne pas ouvrir un nouveau ticket à chacune de vos réponses s\'il vous plait.</p>';
					$btn_value = 'Envoyer';
					$url_form = 'admin_fil';
					$btnDeleteModerate=false;

					if($moderate_content)$content_value = $moderate_content;
					if($moderate_content)$btn_value = 'Valider / Modifier';
					if($moderate_content)$url_form = 'admin_fil_moderate';
                    if($moderate_content)$btnDeleteModerate=true;


					//echo '<h3>'.__('Votre réponse').'</h3>';
					echo $this->Form->create('Support', array('action' => $url_form,'nobootstrap' => 1,'class' => 'form-horizontal FormSupportFil', 'default' => 1, 'enctype' => 'multipart/form-data',
														   'inputDefaults' => array(
															   'div' => 'form-group',
															   'between' => '<div class="span8">',
															   'after' => '</div>',
															   'class' => 'form-control span8'
														   )
					));



					echo $this->Form->inputs(array(
						'support_id' => array('type' => 'hidden', 'value' => $support['Support']['id']),
						'content' => array('label' => array('text' => __('Votre message'), 'class' => 'control-label span3  required'), 'required' => true, 'type' => 'textarea', 'class' => 'tinymce', 'style'=> 'width:100%', 'tinymce' => 'tinymce', 'value' => $content_value)
					));

					echo '<div class="form-group"><label for="SupportAttachment" class="control-label span3 norequired ">Joindre un fichier</label><div class="span8"><input type="file" name="data[Support][attachment][]" class="form-control inputfiletwo" multiple="multiple" id="SupportAttachment"></div></div>';
	
	
					if($is_control){
						?>
						 <div class="form-group offset5 span4" style="clear:both;padding:30px 0;">
							 <div style="background:#d8d8d8;border:1px solid #ccc;border-radius:10px !important;display:inline-block;width:100%">
								<label for="SupportModerate" class="control-label span6 required "><?php echo __('Demander une modération ?').''; ?></label>
								<div class="span6 " style="padding-top:5px">
									<div class="radio" style="width:80px;margin-left:25px">
										<label class="" for="SupportSupportModerate1">
										<input name="data[Support][support_moderate]" checked required="required" id="SupportSupportModerate1" value=1 type="radio" style="float:left;margin-right:10px;">
										<?php echo __('Oui'); ?></label>
									 </div>
									<div class="radio" style="width:80px;">
										<label class="" for="SupportSupportModerate2">
									<input name="data[Support][support_moderate]" required="required" id="SupportSupportModerate2" value=0 type="radio" style="float:left;margin-right:10px;">
									<?php echo __('Non'); ?></label>
									</div>
								 </div>
							 </div>
						</div>
						<?php
					}else{
						?>
						<input type="hidden" name="data[Support][support_moderate]" id="SupportSupportModerate" value="0">
						<?php
					}
	

				   $span='10';
				   if($btnDeleteModerate)
				       $span='1';
					

					echo $this->Form->end(array(
						'label' => $btn_value,
						'class' => 'btn btnmessage',
						'div' => array('class' => ' span'.$span.' offset2 support_btn_submit')
					));

					if($btnDeleteModerate){
                        echo $this->Metronic->getLinkButton(
                            __('Supprimer'),
                            array('controller' => 'support', 'action' => 'fil_delete', $support['Support']['id'],'admin' => true),
                            'btn red pull-left delete_messge_support offset3',
                            ''
                        );
                    }else{
						if($support['Support']['status'] < 2)
						 echo $this->Metronic->getLinkButton(
                            __('Cloturer'),
                            array('controller' => 'support', 'action' => 'close_fil', $support['Support']['id'],'admin' => true),
                            'btn green pull-right ',
                            ''
                        );
					}
				?>
			</div>
			<?php }else{
					if($support['Support']['status'] != 2)
					echo $this->Metronic->getLinkButton(
                            __('Rétablir'),
                            array('controller' => 'support', 'action' => 'unclose_fil', $support['Support']['id'],'admin' => true),
                            'btn green pull-right ',
                            ''
                        );
	
} ?>
        </div>
    </div>
</div>
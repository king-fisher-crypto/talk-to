<?php
	if(!isset($mail['Message']['private']))$mail['Message']['private'] = 0;
	if(!isset($mail['Message']['archive']))$mail['Message']['archive'] = 0;
?>
<div id="messages" class="active tab-pane " role="tabpanel">
	<div class="panel-group mails_container " id="accordion" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default mails " url="<?php 
													if(!isset($pass))$pass='';
													
													echo $this->Html->url(array('controller' => $controller, 'action' => 'readMail', 'pass' => $pass), true); ?>">	
        	<?php if(empty($mails)):
				echo '<p>'. __('Aucun message') .'</p>';
			else: 	
        		foreach($mails as $mail): ?>
                	<?php
			
					if($controller === 'accounts'){
						//client
						$in_live = true;
						if($mail['LastMessage']['from_id'] == $mail['FirstMessage']['from_id'] && $mail['LastMessage']['etat'] != 3)$in_live = false;

						$trClass = '';
						if(($mail['LastMessage']['etat'] == 0 && $mail['LastMessage']['to_id'] == $id) || ($mail['FirstMessage']['etat'] == 0 && $mail['FirstMessage']['to_id'] == $id))
							$trClass.= ' noread';
						if($mail['Message']['private'] == 1)
							$trClass.= ' private';
						if($mail['LastMessage']['etat'] != 3 && $in_live && $mail['Message']['private'] == 0)
							$trClass.= ' alert-inlive';
						if($mail['LastMessage']['etat'] == 1 && !$in_live && $mail['Message']['private'] == 0 )//pas encore repondu par expert
							$trClass.= ' alert-norespond';
						if($mail['LastMessage']['etat'] == 3 && $mail['Message']['private'] == 0)
							$trClass.= ' alert-disable';
						//if($mail['LastMessage']['etat'] == 3 && $controller === 'accounts')
						//	$trClass.= ' alert-disable';//alert-bg
						if($mail['LastMessage']['etat'] == 1 && $controller === 'agents' && $mail['LastMessage']['from_id'] == $id  && $mail['Message']['private'] == 0)
							$trClass.= ' alert-done';
						if($mail['LastMessage']['etat'] == 1 && $mail['LastMessage']['to_id'] == $id && $controller === 'agents'  && $mail['Message']['private'] == 0)
							$trClass.= ' alert-norespond';
						
					}else{
						//expert
						$in_live = true;
						if($mail['LastMessage']['from_id'] != $mail['FirstMessage']['from_id'] && $mail['LastMessage']['etat'] != 3 && $controller === 'accounts')$in_live = false;

						$trClass = '';
						if(($mail['LastMessage']['etat'] == 0 && $mail['LastMessage']['to_id'] == $id) || ($mail['FirstMessage']['etat'] == 0 && $mail['FirstMessage']['to_id'] == $id))
							$trClass.= ' noread';
						if($mail['Message']['private'] == 1)
							$trClass.= ' private';
						if($mail['LastMessage']['etat'] != 3 && $in_live && $mail['Message']['private'] == 0)
							$trClass.= ' alert-inlive';
						if($mail['LastMessage']['etat'] == 1 && !$in_live && $mail['Message']['private'] == 0 )//pas encore repondu par expert
							$trClass.= ' alert-norespond';
						if($mail['LastMessage']['etat'] == 3 && $mail['Message']['private'] == 0)
							$trClass.= ' alert-disable';
						//if($mail['LastMessage']['etat'] == 3 && $controller === 'accounts')
						//	$trClass.= ' alert-disable';//alert-bg
						if($mail['LastMessage']['etat'] == 1 && $controller === 'agents' && $mail['LastMessage']['from_id'] == $id  && $mail['Message']['private'] == 0)
							$trClass.= ' alert-done';
						if($mail['LastMessage']['etat'] == 1 && $mail['LastMessage']['to_id'] == $id && $controller === 'agents'  && $mail['Message']['private'] == 0)
							$trClass.= ' alert-norespond';
					}
			
					
						
					$key = ($mail['Message']['from_id'] == $id ?'To':'From');	
					
					?>
                    <div class="panel-heading panel-heading-modified discussion<?php echo $trClass; ?>"   <?php if($mail['Message']['archive'] == 2 && $controller === 'accounts'){ echo 'style="display:none"'; } ?> role="tab" mail="<?php echo $mail['Message']['id']; ?>">
                        <div class="table-responsive ">
                        	<table class="table table-striped no-border table-mobile text-center mb0"> 
                            	<thead class="hidden-xs text-center"> 
                                	<tr> 
                                    	<th class="text-center"><strong>Discussion avec</strong></th> 
                                    	<th class="text-center"></th>
                                    </tr> 
                                 </thead> 
                                 <tbody> 
                                 	<tr> 
                                    	<td class="veram resize-img"> 
											
                                        	<?php 
											
											if(strpos($trClass, 'noread') !== false)
													echo '<i class="glyphicon glyphicon-envelope margin_right_5"></i> ';
											
											if($controller != 'agents'){
											
											echo $this->Html->link(
                                       '<span class="messageligne">'. $this->Html->image($this->FrontBlock->getAvatar($mail[$key]), array(
                                                'alt' => $mail[$key]['pseudo'],
                                                'title' => $mail[$key]['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle',
                                                )).'</span>',
                                        array(
                                            'language'      => $this->Session->read('Config.language'),
                                            'controller'    => 'agents',
                                            'action'        => 'display',
                                            'link_rewrite'  => strtolower(str_replace(' ','-',$mail[$key]['pseudo'])),
                                            'agent_number'  => $mail[$key]['agent_number']
                                        ),
                                        array('escape' => false, 'class'=>'sm-sid-photo')
                                    );
										echo '<br class="visible-xs" />';
                                        echo $this->Html->link(
                                            $mail[$key]['pseudo'],
                                            array(
                                                'language'      => $this->Session->read('Config.language'),
                                                'controller'    => 'agents',
                                                'action'        => 'display',
                                                'link_rewrite'  => strtolower($mail[$key]['pseudo']),
                                                'agent_number'  => $mail[$key]['agent_number']
                                            ),
                                            array('class' => 'agent-pseudo')
                                        );
									}
                                    ?>
                                    <?php
										if($controller === 'agents'){
					
												$key = ($mail['Message']['from_id'] == $id ?'To':'From');
												echo ($mail[$key]['role'] === 'admin'
													?Configure::read('Site.name')
													:(empty($mail[$key]['pseudo'])
														?$mail[$key]['firstname']
														:$mail[$key]['pseudo'])
												);
										}
											?>
										<?php
										if($controller === 'accounts' && $mail['LastMessage']['etat'] == 1 && $mail['LastMessage']['private']){
					
												echo '<p style="font-size:12px;color:#029418"><i class="fa fa-check-circle" aria-hidden="true"></i> Lu par l\'expert</p> ';
										}

											if($controller === 'agents' && $mail['LastMessage']['etat'] && $mail['LastMessage']['private']){
					
												echo '<p style="font-size:12px;color:#029418;margin-top:3px;">Client avisé de la lecture<br />de son message.</p> ';
										}
											?>
										<?php
											if($mail['LastMessage']['etat'] == 3 && $controller === 'accounts')
												echo '<p style="font-size:12px;color:#af0200;margin-top:3px;"><i class="glyphicon glyphicon-warning-sign icon_alert-factured"" ></i>&nbsp;&nbsp;Email non répondu.<br />Remboursé.</p> ';
											
											if($mail['LastMessage']['etat'] == 3 && $controller === 'agents')
												echo '<p style="font-size:12px;color:#af0200;margin-top:3px;"><i class="glyphicon glyphicon-warning-sign icon_alert-factured"" ></i>&nbsp;&nbsp;Email non répondu.<br />&nbsp;&nbsp;&nbsp;&nbsp;Client remboursé.</p> ';
											?>
											
                                        </td> 
                                        <td class="veram whitespace-normal">
                                        	<div class="msg-area-mobile">
                                            	<p class="date-mobile"><small><?php //La date du dernier message
                            echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$mail['LastMessage']['date_add']),' %d/%m/%y %H:%M');
                        ?></small></p>
                                            	<p class="mob-msg">
                                                	<?php echo (empty($mail['LastMessage']['attachment']) ?'':'');//<i class="glyphicon glyphicon-paperclip"></i>
									$contenu = 	substr(strip_tags($mail['LastMessage']['content']),0,Configure::read('Site.previewMail'));
									$thecontenu = explode('&',$contenu);
									
													
                            echo $thecontenu[0].(strlen($mail['LastMessage']['content']) < Configure::read('Site.previewMail') ?'':'...');
                                                	?>
												</p>
                                         	</div>
                                         </td>
                                         <?php if($controller === 'agents' && !$mail['Message']['private']):
											//echo '<td>'.$mail['Message']['total_credit'].' crédits</td>';
										endif; ?>
                                         <td class="veram msg-last-btn"> 
                                         	<?php
											if($mail['Message']['archive'] == 0){
												if($mail['LastMessage']['etat'] == 3){
													if($controller === 'accounts'){
														
													}else{
														echo $this->Form->button('<i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Email annulé'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 answer',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'answerForm'),true),
																'mail' => $mail['Message']['id'],
																'type'  => 'button'
															)
														);
													}
												}else{
													echo $this->Form->button('<i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Voir la discussion'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 answer',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'answerForm'),true),
																'mail' => $mail['Message']['id'],
																'type'  => 'button'
															)
														);
												}
											}else{
												echo $this->Form->button('<i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Voir'),
														array(
															'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 answer',
															'href' => '',
															'mail' => $mail['Message']['id'],
															'type'  => 'button'
														)
													);	
											}
											if($mail['Message']['archive'] == 0  && $controller === 'accounts' && $mail['LastMessage']['etat'] == 3){
													//if($controller === 'accounts')
														echo $this->Form->button('<i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Relancer'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 answer',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'answerForm'),true),
																'mail' => $mail['Message']['id'],
																'type'  => 'button'
															)
														);
											}
											if($mail['Message']['archive'] == 0  && $controller === 'accounts' && ($mail['Message']['private'] == 1 || ($mail['LastMessage']['etat'] != 0 && ($mail['LastMessage']['to_id'] != $mail['FirstMessage']['to_id'] ||  ($in_live || $mail['LastMessage']['etat'] == 3))))){
													//if($controller === 'accounts')
														echo '&nbsp;'.$this->Form->button('<i class="glyphicon glyphicon-remove icon_margin_right_5"></i> '.__('Clôturer'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 archive',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'closeMessage'),true),
																'mail' => $mail['Message']['id'],
																'label' => __('Voulez-vous vraiment archiver cette discussion ? Vous ne recevrez plus de messages pour cette discussion.'),
																'type'  => 'button'
															)
														);
											}
											 
											 
										
													
											if($mail['Message']['archive'] == 1 && $controller === 'accounts'){
														echo $this->Form->button('<i class="glyphicon glyphicon-upload icon_margin_right_5"></i> '.__('Désarchiver'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 archive',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'restoreMessage'),true),
																'mail' => $mail['Message']['id'],
																'label' => __('Voulez-vous vraiment désarchiver cette discussion ?'),
																'type'  => 'button'
															)
														);	
														echo '&nbsp;&nbsp;'.$this->Form->button('<i class="glyphicon glyphicon-remove icon_margin_right_5"></i> ',
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 mt0 archive',
																'href' => $this->Html->url(array('controller' => $controller, 'action' => 'deleteMessage'),true),
																'mail' => $mail['Message']['id'],
																'label' => __('Voulez-vous vraiment supprimer cette discussion ?'),
																'type'  => 'button'
															)
														);	
											}
											?>
											
                                    	</td>
                                	</tr> 
                            	</tbody> 
                        	</table> 
                    	</div>
                        
                        <?php if(!isset($onlyBlockMail)): ?>
                            <div class="mail-content" id="mail-content-<?php echo $mail['Message']['id']; ?>"></div>
                            <div class="mail-answer" id="mail-answer-<?php echo $mail['Message']['id']; ?>"></div>
                        <?php endif; ?>
                        
                 	</div>
                    
                    
                <?php endforeach; ?>
    		<?php endif; ?>
        </div>
     	<div class="text-center">
			  	<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObjPage($this->Paginator); ?>
		</div>
        
	</div>
</div>
<?php
//Avons-nous une discussion à ouvrir ??
if(isset($idMail) && !empty($idMail) && is_numeric($idMail)) : ?>
            <script type="text/javascript">$(document).ready(function(){ nx_message.simulateOpenDiscussion(<?php echo $idMail; ?>); });</script>
<?php endif; ?>
<?php 
	if (empty($agents)){ ?>
    <section class="bg-white filter-box col-lg-12" id="agentlistempty" style="display:none;margin:10px 0;text-align: center">
		<div class=""><?php echo __('Il n\'y a aucun expert'); ?></div>
	</section>
<?php }else{ 
	
	//Pour modifier les noms des filtres
    $queryIndex = array('term' => 't','orderby' => 'o','filterby' => 'f','ajax_for_agents' => 'afa', 'media' => 'm', 'id_category' => 'cat');

 	if(!isset($no_pagination)){
        $pagination = $this->FrontBlock->getPagination($page,$countAgents, 'Site.limitAgentPage', $this->params, true);
		
        //Si on est sur la page d'accueil ou si la categorie == 1 (categorie pour page d'accueil)
        if(($this->params['controller'] == 'home' && $this->params['action'] == 'index')
            || ($this->params['pass'][0] == 1)){
            //Redirection sur la page d'accueil
            $pagination['link']['controller'] = 'home';
            $pagination['link']['action'] = 'index';
            unset($pagination['link']['link_rewrite']);
            unset($pagination['link']['id']);
        }

        //Si on a des paramètres query
        if(isset($this->params->query[$queryIndex['ajax_for_agents']])){
            //On construit le lien
            foreach ($this->params->query as $k => $val){
                $pagination['link'][$k] = $val;
            }
            //Non necessraire et fausse la reecriture de lien
            unset($pagination['link']['term_novalue']);
            unset($pagination['link']['page']);
        }

        //Si on a des paramètres POST && que l'un des paramètres n'est pas vide
        if(!empty($this->data)
            && (!empty($this->data['term'])
                || !empty($this->data['orderby'])
                || !empty($this->data['media'])
                || !empty($this->data['filterby']))){
            //On modifie les noms des parametres
            foreach ($this->data as $k => $val){
                if(isset($queryIndex[$k])) $pagination['link'][$queryIndex[$k]] = $val;
                else $pagination['link'][$k] = $val;
            }
            //Non necessraire et fausse la reecriture de lien
            unset($pagination['link']['term_novalue']);
            unset($pagination['link']['page']);
        }

        //On sépare les autres parametres des parametres query
        $noQueryParams = array('controller','action','language','id','link_rewrite');
        $params = array();
        foreach ($noQueryParams as $val){
            if(isset($pagination['link'][$val])){
                $params[$val] = $pagination['link'][$val];
                unset($pagination['link'][$val]);
            }
        }
    }
	
	
	$i = 0;
    foreach($agents as $agent):
        $i++;
		/* On récupère les médias de l'agent courant */
            $mediaPaths = $this->FrontBlock->getAgentMedias($agent['User']['agent_number'], $agent['User']['has_photo'] == 1, $agent['User']['has_audio'] == 1);

        $fiche_link = $this->Html->url(
            array(
                'language'      => $this->Session->read('Config.language'),
                'controller'    => 'agents',
                'action'        => 'display',
                'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
                'agent_number'  => $agent['User']['agent_number']
            ),
            array(
                'title'         => 'agents en ligne avec '.$agent['User']['pseudo']
            )
        );
		
		$is_new = false;
			$date_old = new DateTime($agent['User']['date_new']);
			$date_new = new DateTime(date('Y-m-d H:i:s'));
        	$interval = $date_new->diff($date_old);
			$nb_jour = $interval->format('%a');
		if($agent['User']['flag_new'] &&  $nb_jour <= $agent['User']['flag_new'] ){
			$is_new = true;
		 }
		
	?>
		
		<div class="expert-line">
			<?php if($is_new ){?>
				<div class="nou ribbon">
					<span class="ribbon-content"><?=__('Nouveau') ?></span>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-2 col-sm-2 col-xs-3 box-picture">
					<div class="ephoto ae_pseudo">
						<a href="<?php echo $fiche_link; ?>" title="agents en ligne avec <?php echo $agent['User']['pseudo']; ?>" class="agentpicture">
                        <?php
                    		echo
                            $this->Html->image($this->FrontBlock->getAvatar($agent['User']), array(
                                                'alt' => 'agents en ligne '.$agent['User']['pseudo'],
                                                'class' => 'img-circle status-'.$agent['User']['agent_status']
                                                ));
                        
                ?>
						</a>
					</div>
					
				</div>
				<div class="col-md-7 col-sm-7 col-xs-4 box-data">
					<a class="expert-name" href="<?php echo $fiche_link; ?>" title="agents en ligne avec <?php echo $agent['User']['pseudo']; ?>">
						<span class="visible-xs"><?php
							if(strlen($agent['User']['pseudo'] )>12){
								echo substr($agent['User']['pseudo'],0,12).'...';
							}else{
								echo $agent['User']['pseudo'] ;
							}
						?></span>
						<span class="hidden-xs"><?=$agent['User']['pseudo'] 
						?></span>
						</a>
					<ul class="agentlist_categories">
										<?php 
										if(isset($agent['Categories']) && is_array($agent['Categories'])){
										$limit = 4;
										$nb_cat = 0;	
										foreach($agent['Categories'] as $key => $categoryagent):
											
										if(!empty($categoryagent['CategoryLang']['name'])): $nb_cat++; ?>
											<li>
												<?php
													echo $this->Html->link($categoryagent['CategoryLang']['name'],
														array(
															'language' => $this->Session->read('Config.language'),
															'controller' => 'category',
															'action' => 'display',
															'id' => $categoryagent['CategoryLang']['category_id'],
															'link_rewrite' => $categoryagent['CategoryLang']['link_rewrite']
														),
														array(
															'title' => $categoryagent['CategoryLang']['name']
														)
													);
													
												?>
											</li>
										<?php  if($nb_cat>$limit)break;  endif;
									endforeach; }?>
					</ul>
					<div class="expert-description"><?php

                        $presentation = isset($agent['UserPresentLang']['texte'])?strip_tags($agent['UserPresentLang']['texte']):'';
						$presentation = html_entity_decode($presentation);
                        $limitChr = 290;
						
						if (strlen($presentation) > $limitChr){
						  $chaine=substr($presentation,0,$limitChr); 
						  // position du dernier espace
						  $espace=strrpos($chaine," "); 
						  // test si il ya un espace
						  if($espace)
						  // si ya 1 espace, coupe de nouveau la chaine
						  $chaine=substr($chaine,0,$espace);
						  // Ajoute ... à la chaine
						  $chaine .= '...';
							echo htmlentities($chaine);
						}else{ 
							echo htmlentities($presentation); 
						}
                        ?>
					</div>
					<div class="expert-sep"></div>
					<div class="expert-rating">
						<div class="expert_rates" <?php if(!$agent['User']['reviews_avg']) echo ' style="display:none"'; ?>><i class="expert-star-purple"></i> <span class="purple"><?=number_format($agent['User']['reviews_avg'],1) ?></span> - <span><?=$agent['User']['reviews_nb']; ?></span> <?=__('avis') ?></div>
						<div class="expert_expert_consults"><span><?=$agent['User']['consults_nb']+$agent['User']['nb_consult_ajoute'] ?></span> <?=__('Consultations') ?> </div>
					</div>
					<div class="expert-data-action">
						<ul class="list-inline">
							<?php if ((int)$agent['User']['has_audio'] == 1){ ?>
								<li class="aeb_audio"><span title="Ecouter sa présentation audio" class="icon-expert-audio nxtooltip">
                                                <span class="agent_audio_url" style="display:none"><?php echo $this->Html->url(array('controller' => 'agents', 'action' => 'modalPresentation')); ?></span>
                                                <span class="agent_audio_pseudo" style="display:none"><?php echo $agent['User']['pseudo']; ?></span>
                                                <span class="agent_audio_audio" style="display:none"><?php echo $mediaPaths['audio_filename']; ?></span>
                                            </span>
							</li>
							<?php } ?>
							<li><?php
									$lien = $this->Html->url(
												array(
													 'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
												)
											);
								?><span title="Recevoir une alerte sms/email" class=" nxtooltip icon-expert-alert icon-link"></span>
								<span class="icon_url nx_openlightbox action-box-alerte" style="display:none"><?=$lien ?></span>
							</li>
							<li><span title="Ajouter à vos favoris"  class=" icon-expert-favoris nxtooltip icon-link"></span>
								<span class="icon_url nx_openlightbox " style="display:none"><?php
														
															echo $this->Html->url(
																array(
																	'language'      => $this->Session->read('Config.language'),
																	'controller'    => 'accounts',
																	'action'        => 'add_favorite',
																	$agent['User']['id']
																)
															);
														  
													?></span>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-5 box-action">
					<?php
		
						$action_title = 'consulter par';
						if ($agent['User']['agent_status'] == 'unavailable')$action_title = 'indisponible';
		
		?>
					<div class="action-box">
						<p class="title <?php if ($agent['User']['agent_status'] == 'unavailable') echo 'unavailable' ; ?>"><?=$action_title; ?></p>
						<div class="action-container">		
						<ul class="list-inline action-btn">
							
							<?php
								$css_bloc_busy = '';
								if($agent['User']['agent_status'] == 'busy'){
											$agent_busy_mode = $this->FrontBlock->agentModeBusy($agent['User']['id']);
											$css_bloc_busy = $agent_busy_mode;
										}
										if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'phone')
													$css_phone = ' t-'.$agent['User']['agent_status'];
												else
													$css_phone = ' disabled';	
											}else{
												$css_phone = ' t-'.$agent['User']['agent_status'];
											}
											
										}else{
											$css_phone = ' disabled';
										} 
										if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->FrontBlock->agentActif($agent['User']['date_last_activity'])){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'tchat')
													$css_tchat = 't-'.$agent['User']['agent_status'];
												else
													$css_tchat = ' disabled';
											}else{
												$css_tchat = 't-'.$agent['User']['agent_status'];
											}
											
										}else{
											$css_tchat = ' disabled';
										} 
										
										if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												$css_email = 't-available';
											}else{
												$css_email = 't-'.$agent['User']['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}
		
		
							?>
									<li class="tel <?=$css_phone ?>">
                                     <?php 
                                        if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):
																			 
											/* $lien = $this->Html->url(
												array(
													'controller' => 'home',
                                                                    'action' => 'media_phone'
												)
											);*/
											$lien = 'agents par téléphone';
											echo '<div data-toggle="tooltip" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon "><span class="linklink">'.$lien.'</span><p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span></div>';								 
										
											else:
												echo  '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon"><p>Tel</p></div>';
											endif; ?>
                                    </li>

									<li class="chat <?=$css_tchat ?>">
                                    <?php 
                                        if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->FrontBlock->agentActif($agent['User']['date_last_activity'])):
										/*$lien = $this->Html->url(
												array(
													'controller' => 'chats',
                                                    'action' => 'create_session',
                                                    'id' => $agent['User']['id']
												)
											);*/
											$lien = 'agents par tchat - '.$agent['User']['id'];
											echo  '<div data-toggle="tooltip" data-placement="top" title="Chat" class="nx_chatboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tchat</p></div>';
                                       
											else:
												echo  '<div data-toggle="tooltip" data-placement="top" title="Tchat" class="aicon"><p>Tchat</p></div>';
											endif; ?>
                                    </li>
									
									<li class="mail <?=$css_email ?>">
                                    <?php 
                                        if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):
										/* $lien = $this->Html->url(
												array(
													'controller' => 'accounts',
													'action' => 'new_mail',
													'id' => $agent['User']['id']
												)
											);*/
										$lien = 'agents par mail - '.$agent['User']['id'];
											echo '<div  data-toggle"tooltip" data-placement="top" title="Email" class="nx_emailboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Email</p></div>';
                                       
											else:
												echo '<div data-toggle="tooltip" data-placement="top" title="Email" class="aicon"><p>Email</p></div>';
											endif; ?>
                                    </li>
								</ul>
								</div>
								<?php
		
								if ($agent['User']['agent_status'] == 'available'){
									 if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1){
										 echo '<div  class="action-box-phone" >'.__('Code Expert').' <span>'.$agent['User']['agent_number'].'</span></div>';
									 }
								}elseif ($agent['User']['agent_status'] == 'busy'){
									echo '<div  class="action-box-busy '.$css_bloc_busy.'" ><span class="hidden-xs">'.__('En consultation ').'</span>'.__('depuis ').$this->FrontBlock->secondsToHis($agent['0']['second_from_last_status']).'</div>';
								}elseif ($agent['User']['agent_status'] == 'unavailable'){
									
									$lien = $this->Html->url(
												array(
													 'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
												)
											);
											echo '<a title="Recevoir une alerte sms/email" class="action-box-alerte nx_openinlightbox nxtooltip" href="'.$lien.'">'.__('Recevoir une alerte sms/email').'</a>';
								}

								?>
						
					</div>
				</div>
			</div>
		</div>

	<?php endforeach; ?>
	<?php if(isset($pagination) && (int)$pagination['pages'] > 1):
			echo $this->FrontBlock->getPaginateCategory($pagination,$params);
		endif; ?>
<?php } ?>
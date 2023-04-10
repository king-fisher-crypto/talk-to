<?php
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
<div class="row-fluid">
    <div class="portlet box blue">
        <!--<div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les messages en cours'); ?> | Connectés : <span class="agent_in"></span> | Occupés : <span class="agent_busy"></span> | Ratio : <span class="agent_ratio"></span> | Agents souhaités : <span class="agent_need"></span></div>
        </div>-->
		<div class="portlet-title">
            <div class="caption"><?php echo __('Tickets support'); ?></div>

			<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('name', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'value' => $name, 'label' => __('Nom/Pseudo').' :', 'div' => false));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'value' => $email, 'label' => __('Email').' :', 'div' => false));
					echo $this->Form->input('status', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'select', 'label' => __('Statut').' :', 'div' => false, 'options' => array('' => 'Choisir','0' => 'A traiter', '1'=>'Répondu')));
					echo $this->Form->input('classification', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'select', 'label' => __('Classification').' :', 'div' => false, 'options' => $list_classification,'value'=> $classif));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                ?>
            </div>
        </div>
        <div class="portlet-body" style="display:inline-block;width:100%;">
				<?php
				if($messages){
					foreach($messages as $message){
						$hasAttachment = (bool) $message['Support']['hasAttachment'];
						$color = 'blue';
						$font_color = '#fff';
						$btn_label = __('A traiter');
						if($message['Support']['status']){
							if($message['Support']['status'] == 2){
								$color = 'grey';
								$font_color = '#000';
							}else{
								$color = 'green';
							}
							
							$btn_label = __('Consulter');
						} else{
							$date1 = new DateTime($message['Support']['date_upd']);
							$date2 = new DateTime(date('Y-m-d H:i:s'));

							$diff = $date2->diff($date1);

							$hours = $diff->h;
							$hours = $hours + ($diff->days*24);

							if($hours >= 12 && $hours < 24)
								$color = 'yellow';

							if($hours >= 24)
								$color = 'red';
						}
					$icon = "icon-user";
					if($message['User']['role'] == 'agent')$icon = "icon-user-md";
            
          if($message['Support']['moderate_response']){
							$color = 'red';
							$btn_label = __('A moderer');
						}
				?>
				<div class="row-fluid">

						<div data-desktop="span12" class="span12 responsive">
							<div class="dashboard-stat <?=$color; ?>" style="padding:5px;color:<?=$font_color ?>;font-size:16px;">
								<div class="span2 responsive">
									 <div class="date" style="line-height:40px"><?php echo CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$message['Support']['date_upd']),'%d/%m/%y %H:%M:%S'); ?></div>
									<div class="desc"><?php echo __('Ticket'); ?> : #<?=$message['Support']['id'] ?></div>
									<div class="desc"><?php echo __('Service'); ?> : <?=$message['SupportService']['name'] .' (Level '.$message['Support']['level'].')'; ?></div>
									<div class="desc" style="margin-top:10px;"><?php echo $message['Support']['classified']; ?></div>
								</div>
								<div class="span2 responsive" style="overflow: hidden;color: <?=$font_color ?>">
                                        <div class="visual"><i class="<?=$icon; ?>"></i><?php
										if($message['User']['role'] == 'agent')echo ' Agent';
										if($message['User']['role'] == 'client')echo ' Client';
										if(!$message['User']['role'])echo ' Invité';
										echo '</div><br />'.$message['User']['firstname'].' '.$message['User']['lastname'];
										echo '<br /><span style="font-size:12px;">'.$message['User']['email'].'</span>';


                                         ?>
								</div>
								<div class="span3 responsive">
									<div class="desc"><?=$message['Support']['comm']; ?></div>
								</div>
								<div class="span3 responsive">
									<div class="desc"><?=$message['Support']['title']; ?><br /><?=substr(strip_tags($message['Support']['message']),0,150).'...'; ?>
                  <?php if($hasAttachment):?>
								<div class="responsive" title="Ce message contient des fichiers">
									<i class="icon-paperclip"></i>
								</div>
								<?php endif;?>
                  </div>
								</div>
								<div class="span2 responsive">
								<?php
									if(!$message['Support']['owner_id']) echo 'En attente';
									if($message['Support']['owner']) echo 'Répondu par '.$message['Support']['owner'];
								?>
								<br /><br /><a href="/admin/support/fil/<?=$message['Support']['id'] ?>" class="btn" style="float:left" target="_blank"><?=$btn_label ?></a>
									<?php if($message['Support']['status'] < 2){
										if(!empty($user_level) && $user_level != 'moderator' && !$message['Support']['moderate_response']){
									?>
									<a href="/admin/support/close/<?=$message['Support']['id'] ?>" class="btn" style="float:right">Cloturer</a>
									<?php } } ?>
								</div>
								
							</div>
						</div>

				</div>
				<?php
					}
					if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
				} else{ echo __('Aucun message'); }
				?>
        </div>
    </div>
</div>

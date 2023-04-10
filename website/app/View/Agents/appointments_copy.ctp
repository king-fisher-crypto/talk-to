<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes RDV') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Vos rendez-vous') ?></h2>
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
									'name'  =>  __('Mes RDV'),
									'link'  =>  ''
								)
							)
						));
		?>

						</div><!--page-header END-->
 <?php if(empty($appointments)):
             echo __('Vous n\'avez aucune demande de consultation.');
        else: ?>

						<div class="table-responsive">
                       
				  	 <table class="nx_table table table-striped no-border table-mobile text-left"> 
				  	 	<thead> 
				  	 		<tr> 
				  	 			<th class="hidden-xs"><?php echo __('Client'); ?></th>
                                <th class="hidden-xs"></th>
                                <th class="hidden-xs"><?php echo __('Date'); ?></th>
				  	 			<th>
									<select class="appointments_status form-control" style="padding:2px 8px; height:32px;">
										<option value=""><?php echo __('Tous') ?></option>
										<option value="0"><?php echo __('Répondre') ?></option>
										<option value="1"><?php echo __('Confirmé') ?></option>
										<option value="-1"><?php echo __('RDV refusé') ?></option>
										<option value="-2"><?php echo __('RDV annulé par le client') ?></option>
									</select>
								</th>
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody> 
                        <?php 
							$color = 'fff';
							foreach($appointments as $date => $appointment): ?>
							<?php foreach($appointment as $horaire):
							if($horaire['status']){
							if($color == 'fff')$color = 'f9f9f9';else $color = 'fff';
							
								if(!$timezone)$timezone = $this->Session->read('Config.timezone_user');
							?>
								<tr rel="<?=$horaire['valid']?>">
									<td style="background:#<?php echo $color; ?>;"><?php echo $horaire['firstname']; ?></td>
									<td style="background:#<?php echo $color; ?>;"><?php echo __('souhaite vous rencontrer le'); ?></td>
									<td style="background:#<?php echo $color; ?>;line-height:15px"><?php 
								
								
								if($horaire['user_utc'] != $horaire['agent_utc']){
									
									date_default_timezone_set($horaire['user_utc']);
									$d_client = date('YmdH');
									date_default_timezone_set($horaire['agent_utc']);
									$d_agent = date('YmdH');
									date_default_timezone_set('UTC');
									$offset = intval($d_agent) - intval($d_client);
									//if($horaire['agent_utc'] == 'America/Chicago') $offset = $offset + 1;
									//if($horaire['user_utc'] == 'America/Chicago') $offset = $offset - 1;
									
									/*if($horaire['user_utc'] != 'Europe/Paris' && $horaire['agent_utc'] == 'Europe/Paris' ){
										
										
										$userTimezone = new DateTimeZone($horaire['user_utc']);
										$gmtTimezone = new DateTimeZone($horaire['agent_utc']);
										date_default_timezone_set($horaire['user_utc']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
									}
									if($horaire['user_utc'] == 'Europe/Paris' && $horaire['agent_utc'] != 'Europe/Paris' ){
										date_default_timezone_set($horaire['user_utc']);
										$userTimezone = new DateTimeZone($horaire['agent_utc']);
										$gmtTimezone = new DateTimeZone($horaire['user_utc']);
										
										var_dump(date('Y-m-d H:i:s'));
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
										var_dump($userTimezone->getOffset($myDateTime));
									}
									
									if($horaire['user_utc'] != 'Europe/Paris' && $horaire['agent_utc'] != 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($horaire['agent_utc']);
										$gmtTimezone = new DateTimeZone($horaire['user_utc']);
										date_default_timezone_set($horaire['user_utc']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
									}*/
									
									$dx = new DateTime($date.' '.$horaire['H'].':'.$horaire['Min']);
									$dx->modify($offset.' hour');
									$dd = $dx->format('Y-m-d H:i:s');


									echo $this->Time->format($dd,'%d/%m/%y <br />à %Hh%M');
								}else{
									$dx = new DateTime($date.' '.$horaire['H'].':'.$horaire['Min']);
									$dd = $dx->format('Y-m-d H:i:s');
									echo $this->Time->format($dd,'%d/%m/%y <br />à %Hh%M');
								}
								
								
								
								 ?></td>
									<td class="veram" style="background:#<?php echo $color; ?>;text-align:left;white-space:normal;line-height:15px">
										<?php
										switch ($horaire['valid']) {
											case -2:
												echo __('RDV annulé par le client'); 
												break;
											case -1:
												echo __('RDV refusé'); 
												break;
											case 0:
												echo '<button class="btn btn-pink btn-pink-modified btn-small-modified mb0 appointmentAnswerBtn" href="/agents/answerAppointments" appointment="'.$horaire['id'].'" type="button"><i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Répondre').'</button>'; 
												break;
											case 1:
												echo __('<b>Confirmé</b>'); 
												echo '<br /><button class="btn btn-pink btn-pink-modified btn-small-modified mb0 appointmentAnswerBtn" href="/agents/answerAppointments" appointment="'.$horaire['id'].'" type="button" style="margin-top:5px;"><i class="fa fa-plus-circle" aria-hidden="true"></i> '.__('Annuler').'</button>'; 
												break;
										}
										?>
								</tr>
								<tr class="appointmentAnswer" rel="<?php echo $horaire['id']; ?>" style="display:none;background:#fff;">
									<td colspan="4" style="text-align:left;white-space: normal">
										<?php
										 echo $this->Form->create('agents', array('action' => 'appointments','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-12">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control',
                                               )
									        ));
										echo '<div class="panel-body">';
									if($horaire['valid'] >0 ){
										echo '<div class="radio">
												<input name="data[agents][ChoiceRDV]" value=2  id="agentChoiceRDV2" type="radio" checked>
												<label class="nxtooltip" for="agentChoiceRDV2" data-toggle="tooltip" title="" data-original-title="'.__('Cochez cette option afin d\'écrire votre message').'">'.__('Je ne pourrais être présent(e) à l\'horaire validé :').'</label>
											 </div>';
										echo $this->Form->inputs(array(
											'appoint_id' => array('type' => 'hidden', 'value' => $horaire['id']),
											'content' => array('label' => false, 'required' => false,'type' => 'textarea', 'value' => __('Je ne pourrais finalement honorer le RDV initialement prévu, voici une nouvelle proposition :   '),'maxlength' =>120, 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 2,'data-toggle'=>"tooltip",'data-original-title'=>"'.__('Cochez cette option afin d'écrire votre message').'", 'class'=>'nxtooltip form-control', 'style' => 'width:100%;margin-top:10px')
										));
									}else{
										echo '<div class="radio">
												<input name="data[agents][ChoiceRDV]" checked id="agentChoiceRDV1" value=1 type="radio">
												<label class="" for="agentChoiceRDV1">'.__('Je confirme ce rendez-vous').'</label>
											 </div>';
										echo '<div class="radio">
												<input name="data[agents][ChoiceRDV]" value=3  id="agentChoiceRDV3" type="radio">
												<label class="" for="agentChoiceRDV3">'.__('Je ne pourrais être présent(e) et j\'annule ce rendez-vous').'</label>
											 </div>';
										echo '<div class="radio">
												<input name="data[agents][ChoiceRDV]" value=2  id="agentChoiceRDV2" type="radio">
												<label class="nxtooltip" for="agentChoiceRDV2" data-toggle="tooltip" title="" data-original-title="'.__('Cochez cette option afin d\'écrire votre message').'">'.__('Je ne pourrais être présent(e) à l\'horaire souhaité mais je serai disponible :').'</label>
											 </div>';
										echo $this->Form->inputs(array(
											'appoint_id' => array('type' => 'hidden', 'value' => $horaire['id']),
											'content' => array('label' => false, 'required' => false,'type' => 'textarea', 'placeholder' => __('Votre nouvelle disponibilité...'),'disabled'=>'disabled', 'maxlength' =>120, 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 2,'data-toggle'=>"tooltip",'data-original-title'=>"'.__('Cochez cette option afin d'écrire votre message').'", 'class'=>'nxtooltip form-control', 'style' => 'width:100%;margin-top:10px')
										));
									}
										
										
										echo '<br />';
										
										echo $this->Form->end(array(
											'label' => __('Envoyer'),
											'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0',
											'div' => array('class' => 'form-group margin-tp')
										));
										echo '</div>';
										?>
									</td>
								</tr>
							<?php 
							}
								endforeach; ?>
						<?php endforeach; ?>
				  	 	</tbody> 
				  	 </table> 
                     
				  	</div><!--table-responsive-->

				  	<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>
                    <?php endif; ?>
                    </div><!--content_box END-->
					
				
					


			</div><!--col-9 END-->
            <?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
            <!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
<?php 
/*echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
	echo $this->Html->script('/assets/scripts/app', array('block' => 'script'));
	*/
	echo $this->Html->css('/theme/default/css/daterangepicker', array('block' => 'css'));
	
	echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
	 echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
	  echo $this->Html->css('/assets/plugins/font-awesome/css/font-awesome.min', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
      echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
	 echo $this->Html->script('/assets/scripts/app', array('block' => 'script'));
	  echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
	  
 ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Facturation'); ?></h1>
</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
<?php
        
        /* titre de page */
        $title = __('Ma Facturation');

       
		
		?>
					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s">  <?php echo $title ?></h2>
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
										'name'  =>  '<span class="active">'.__('Historique').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->


					<div class="form-horizontal box_account well well-account">

                        <div class="row">
                        
								<div class="col-sm-6 col-md-6">
    							 <div class="form-group wow fadeIn mb0" data-wow-delay="0.4s">
                                <?php echo $this->Metronic->getDateInputFront(); ?>
                          </div>
                      </div>
				  </div><!--row END-->
				  	<hr/>

				  	<div class="table-responsive">
				  	 <table class="table table-striped no-border table-mobile text-center"> 
                     	 <?php 
						 
						 $total = 0;
					$total_phone = 0;
					$total_chat = 0;
					$total_email = 0;
						$calcul_in_live = 0;
						 
						 $nb_email = 0;
						 $nb_chat = 0;
						 $nb_phone = 0;
						 
						 if(empty($historiqueComs)) : ?>
                <?php echo __('Vous n\'avez eu aucune communication avec un client.'); ?>
            <?php else : ?>
				  	 	<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><?php echo __('Client'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Media'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Durée'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Date'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Côte/part'); ?></th> 
				  	 			<th class="text-center"></th> 
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody> 
                        	<?php 
							
							$fact_min = '';
							$fact_max = '';
							
							foreach($historiqueComs as $historique) :
							$hash = base64_encode($historique['UserCreditLastHistory']['user_credit_last_history']);
							if(!$fact_min)$fact_min = $historique['UserCreditLastHistory']['user_credit_last_history'];
              if($historique['UserCreditLastHistory']['user_credit_last_history'])
							$fact_max = $historique['UserCreditLastHistory']['user_credit_last_history'];
							?>
				  	 		<tr> 
				  	 			<td class="veram resize-img"><div class="agent-pseudo-desc"><?php 
						
						if(substr_count($historique['User']['firstname'],'AUDIOTEL')){
							$id = 'AUDIO'.(substr($historique['UserCreditLastHistory']['phone_number'], -4)*15);
							$historique['User']['firstname'] = 'AUDIOTEL '.$id;	
						}
						
						echo $historique['User']['firstname'].'<span class="date-small small mt10 visible-xs">'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$historique['UserCreditLastHistory']['date_start']),'%d/%m/%y %Hh%M').'</span>'; ?>
                        </div>
                        
				  	 			</td> 
				  	 			<td class="veram">
								<span class="hidden-xs"><?php echo __($consult_medias[$historique['UserCreditLastHistory']['media']]);?></span>
				  	 				<span class="visible-xs">
				  	 						<i class="fa fa-<?php echo __($consult_medias[$historique['UserCreditLastHistory']['media']]);?>" aria-hidden="true"></i> <span class="bold pink"> <?php echo (empty($historique['UserCreditLastHistory']['seconds'])
                                ?__('N/D')
                                :gmdate('H:i:s', $historique['UserCreditLastHistory']['seconds'])
                            ); ?></span>
				  	 						|
				  	 						Rém <span class="bold pink"><?php
								
								if(is_numeric($historique['UserCreditLastHistory']['price'] )){
								 echo number_format($historique['UserCreditLastHistory']['price'], 2).' €';  
								}else{
									echo 'calcul en cours';	
								}
                                ?></span> <?php
								if(isset($historique['UserCreditLastHistory']['is_factured']) && !$historique['UserCreditLastHistory']['is_factured']){
									echo '<i class="glyphicon glyphicon-warning-sign icon_alert-factured" style="cursor:pointer" ></i>';	
									echo '<div class="box-factured" style="position: absolute;background-color: #000033;border-radius: 5px;color: #ffffff;padding: 5px;font-size: 11px;width: 200px;z-index: 999;display:none">'.$historique['UserCreditLastHistory']['text_factured'].'</div>';	
								}
							?>
				  	 					</span>
								
								
								</td> 
				  	 			<td class="veram hidden-xs"><?php echo (empty($historique['UserCreditLastHistory']['seconds'])
                                ?__('N/D')
                                :gmdate('H:i:s', $historique['UserCreditLastHistory']['seconds'])
                            ); ?></td> 
				  	 			<td class="veram hidden-xs"><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$historique['UserCreditLastHistory']['date_start']),'%d/%m/%y %Hh%M'); ?></td> 
				  	 			<td class="veram hidden-xs"> <?php
								if($historique['UserCreditLastHistory']['is_factured'] || $historique['UserCreditLastHistory']['media'] == 'other'){
									if(is_numeric($historique['UserCreditLastHistory']['price'] )){
									 echo number_format($historique['UserCreditLastHistory']['price'], 2).' €';  

										switch ($historique['UserCreditLastHistory']['media']) {
											case 'phone':
												$total += number_format($historique['UserCreditLastHistory']['price'], 2);
												$total_phone += number_format($historique['UserCreditLastHistory']['price'], 2);
												$nb_phone += $historique['UserCreditLastHistory']['seconds'];
											break;
											case 'email':
												$total += number_format($historique['UserCreditLastHistory']['price'], 2);
												$total_email += number_format($historique['UserCreditLastHistory']['price'], 2);
												$nb_email ++;
											break;
											case 'refund':
												$total += number_format($historique['UserCreditLastHistory']['price'], 2);
												$total_email += number_format($historique['UserCreditLastHistory']['price'], 2);
												$nb_email --;
											break;
											case 'chat':
												$total += number_format($historique['UserCreditLastHistory']['price'], 2);
												$total_chat += number_format($historique['UserCreditLastHistory']['price'], 2);
												$nb_chat += $historique['UserCreditLastHistory']['seconds'];
											break;
											case 'other':
												$total += number_format($historique['UserCreditLastHistory']['price'], 2);
											break;
										}
									}else{
										echo 'calcul en cours';	
										$calcul_in_live = 1;
									}
								}else{
									echo '0.00 €';	
								}
                                ?></td> 
				  	 			<td class="veram hidden-xs">
                               <?php
								if(isset($historique['UserCreditLastHistory']['is_factured']) && !$historique['UserCreditLastHistory']['is_factured']){
									echo '<i class="glyphicon glyphicon-warning-sign icon_alert-factured" style="cursor:pointer;color:#A3A1A1" ></i>';	
									echo '<div class="box-factured" style="position: absolute;background-color: #000033;border-radius: 5px;color: #ffffff;padding: 5px;font-size: 11px;width: 200px;z-index: 999;display:none">'.$historique['UserCreditLastHistory']['text_factured'].'</div>';	
								}
							?>
                               </td> 
								<td class="veram">
								<?php 
									if($invoice_agent && $historique['UserCreditLastHistory']['user_credit_last_history'] >= 211646){ ?>
								<a href="/fact_account.php?c=<?=$hash ?>" target="_blank" class="" style="color:#A3A1A1"><i class="icon-file" style="cursor:pointer !important"></i></a>
								<?php } ?>
								</td>
                               
				  	 		</tr> 
							<?php endforeach; ?>
							<?php
								if($invoice_agent){
									$hash_all = base64_encode($fact_min.'#'.$fact_max);
							?>
							<tr>
								<td colspan="6" align="right" class="veram"><?php echo __('Toutes les factures'); ?></td>
								<td class="veram">
								<a href="/fact_account_all.php?c=<?=$hash_all ?>" target="_blank" class=""  style="color:#A3A1A1"><i class="icon-file" style="cursor:pointer !important"></i></a>
								</td>
							</tr>
							<?php } ?> 
				  	 	</tbody>
                        <?php endif; ?> 
				  	 </table> 
				  	</div><!--table-responsive-->
					<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>
					
                      
         <div class="portlet box yellow mt20">
                <div class="portlet-body">
					<?php 
						if($total != 0){ 
						
						?>
                	<table class="table table-striped table-hover table-bordered">
						<thead>
                        	<th width="33%"><?php echo __('Total Téléphone'); ?></th>
                            <th width="33%"><?php echo __('Total Chat'); ?></th>
                            <th width="33%"><?php echo __('Total Email'); ?></th>
                        </thead>
                       	<tbody>
							<tr>
                            	<td><?php echo sprintf('%dj %02dh %02dmin. %02dsec.', $nb_phone/86400, $nb_phone/3600%24, $nb_phone/60%60, $nb_phone%60) ?></td>
                                <td><?php echo sprintf('%dj %02dh %02dmin. %02dsec.', $nb_chat/86400, $nb_chat/3600%24, $nb_chat/60%60, $nb_chat%60) ?></td>
                                <td><?php echo $nb_email ?> <?php echo __('mails'); ?></td>
                            </tr>
                        	<tr>
                            	<td><?php echo $total_phone ?> €</td>
                                <td><?php echo $total_chat ?> €</td>
                                <td><?php echo $total_email ?> €</td>
                            </tr>
                        </tbody>
                    </table>
					<?php } ?>
					<?php
						if($invoice_agent){
					?>
						<table class="table table-striped table-hover table-bordered">
						<!--<thead>
                        	<th>Type</th>
                            <th>Montant</th>
                        </thead>-->
                       	<tbody>
							<tr>
                            	<td width="66%"><?php echo __('Chiffre d\'affaire généré'); ?></td>
                                <td width="33%"><?php echo number_format($invoice_agent['InvoiceAgent']['ca'] + $invoice_agent['InvoiceAgent']['other'],2,'.',''); ?> €</td>
                            </tr>
							<tr>
                            	<td><?php echo __('Bonus'); ?></td>
                                <td><?php echo number_format($invoice_agent['InvoiceAgent']['bonus'],2,'.',''); ?> €</td>
                            </tr>
							<tr>
                            	<td><?php echo __('Parrainage'); ?></td>
                                <td><?php echo number_format($invoice_agent['InvoiceAgent']['sponsor'],2,'.',''); ?> €</td>
                            </tr>
							<tr>
                            	<td><?php echo __('Côte/part Expert'); ?></td>
                                <td><?php echo number_format($invoice_agent['InvoiceAgent']['paid_total'],2,'.',''); ?> €</td>
                            </tr>
							
                        </tbody>
                    </table>	
					<div class="row" style="text-align: center">
						<?php 
	
									//bloquer le mois dernier jusqu au 4 mois suivant
									$now = new DateTime(date('Y-m-d H:i:s'));
										if($date_fact){
											$fact = new DateTime($date_fact['min']);
										}else{
											$fact = new DateTime(date('Y-m-d H:i:s'));
										}
											
									$end_month = 0;
											
									if($now->format('Ym') == $fact->format('Ym')){
										$end_month = 1;
									}
											
									if($now->format('d') < 2 && ($now->format('m')-1 == $fact->format('m'))){
										$end_month = 1;
									}
									
									$type_facture = 'fact';
									$show_fact = true;
							
							
									if($fact->format('Ym') > 201906 && $show_fact){
										$type_facture = 'fact2_us';
										if($invoice_agent['InvoiceAgent']['status'] >= 10){
											echo '<a href="#" class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="opacity:0.4" data-toggle="tooltip" data-placement="top" data-original-title="'.__('Votre modèle facture est indisponible').'">'.__('Facture').'</a>';
										}else{
											if(is_array($date_fact) && count($date_fact) > 1 && !$calcul_in_live && !$end_month){
												echo '<a href="/'.$type_facture.'.php" target="_blank" class="btn btn-pink btn-pink-modified btn-small-modified mb0">Facture<!--<i class="icon-file" style="cursor:pointer !important"></i>--></a>';	
											}else{
												if($calcul_in_live){
												echo '<a href="#" class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="opacity:0.4" data-toggle="tooltip" data-placement="top" data-original-title="'.__('Une ou plusieurs consultations sont en cours de calcul, votre facture sera disponible demain matin à 6h00').'">'.__('Facture').'</a>';
												}else{
													if($end_month)
														echo '<a href="#" class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="opacity:0.4" data-toggle="tooltip" data-placement="top" data-original-title="'.__('Votre modèle facture sera disponible le 2 vers 6h').'">'.__('Facture').'</a>';
												}
											}
										}
									}else{
										echo '<a href="#" class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="opacity:0.4" data-toggle="tooltip" data-placement="top" data-original-title="'.__('Votre modèle facture n\'est pas disponible').'">'.__('Facture').'</a>';
									}
									
								 ?>
					</div>
					<?php	
						}else{
							echo '<div class="row" style="text-align: center"><a href="#" class="btn btn-pink btn-pink-modified btn-small-modified mb0" style="opacity:0.4" data-toggle="tooltip" data-placement="top" data-original-title="'.__('Votre modèle facture n\'est pas disponible').'">'.__('Facture').'</a></div>';
						}						
					?>
                </div></div>

				  </div><!--box-account END-->


					</div><!--content_box END-->
						
					




				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
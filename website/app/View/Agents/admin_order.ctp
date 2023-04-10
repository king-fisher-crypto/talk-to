<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agent'),__('Prime'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Factures'),
            'classes' => 'icon-euro',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'order', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="pull-left">
                <?php
                   // echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                   // echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
    <div class="portlet box red">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Facture agents'); ?></div>
		<?php echo $this->Metronic->getLinkButton(
                __('Export Compta'),
                array('controller' => 'agents', 'action' => 'export_comptabilite', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
			
          <?php echo $this->Metronic->getLinkButton(
                __('Export CSV'),
                array('controller' => 'agents', 'action' => 'export_facturation', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
			
			<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('agent', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'value' => $name, 'label' => __('Agent').' :', 'div' => false));
					echo $this->Form->input('min', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Mt min').' :', 'div' => false));
					echo $this->Form->input('max', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Mt max').' :', 'div' => false));
					echo $this->Form->input('mode', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'select', 'label' => __('Mode').' :', 'div' => false, 'options' => array('' => 'Paiement', 'Virement'=>'Virement', 'Hipay Wallet'=>'Hipay', 'Paypal'=>'Paypal', 'Stripe'=>'Stripe')));
					
					echo $this->Form->input('status', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'select', 'label' => __('Payé').' :', 'div' => false, 'options' => array('' => 'Choisir','0' => 'Non', '1'=>'Oui', 'TVA INVALIDE' => 'TVA INVALIDE', 'ERROR' => 'Erreur', 'AVOIR' => 'Avoir Généré', 'TVA APPLIQUE' => 'TVA % appliqué')));
					
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastOrder)): ?>
                <?php echo __('Pas de facture'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Agent'); ?></th>
						<th><?php echo __('Nom'); ?></th>
						<th><?php echo __('Societe'); ?></th>
						<th><?php echo __('Actif'); ?></th>
						<th><?php echo __('Gain'); ?></th>
						<th><?php echo __('Consolide'); ?></th>
						<th><?php echo __('Prime'); ?></th>
						<th><?php echo __('Parrainage'); ?></th>
						<th><?php echo __('Penalité'); ?></th>
						<th><?php echo __('Frais'); ?></th>
						<th><?php echo __('Total'); ?></th>
						<td><?php echo __('Facture'); ?></td>
						<th><?php echo __('Fond roulement'); ?></th>
						<th><?php echo __('Dispo Stripe'); ?></th>
						<th><?php echo __('Solde Stripe'); ?></th>
						<th><?php echo __('Statut Stripe'); ?></th>
						<th><?php echo __('Payé'); ?></th>
						<th><?php echo __('Réglé le'); ?></th>
						<th><?php echo __('TVA'); ?></th>
						<th><?php echo __('Mode'); ?></th>
						<th><?php echo __('Validation'); ?></th>
						<td><?php echo __('Frais'); ?></td>
						<td><?php echo __('Email'); ?></td>
						<td>&nbsp;</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
						$total = 0;$total_gain = 0;$total_prime = 0;$total_parrainage = 0;$total_penalite = 0;$total_tva = 0;$total_fees = 0;
						$total_paid = 0;$total_gain_paid = 0;$total_prime_paid = 0;$total_parrainage_paid = 0;$total_penalite_paid = 0;$total_tva_paid = 0;$total_fees_paid = 0;
						$total_unpaid = 0;$total_gain_unpaid = 0;$total_prime_unpaid = 0;$total_parrainage_unpaid = 0;$total_penalite_unpaid = 0;$total_tva_unpaid = 0;$total_fees_unpaid = 0;
						$total_unfact = 0;$total_gain_unfact = 0;$total_prime_unfact = 0;$total_parrainage_unfact = 0;$total_penalite_unfact = 0;$total_tva_unfact = 0;$total_fees_unfact = 0;
						$total_consolid = 0;$total_gain_consolid = 0;$total_prime_consolid = 0;$total_parrainage_consolid = 0;$total_penalite_consolid = 0;$total_tva_consolid = 0;$total_fees_consolid = 0;
						
						$total_line_gain = 0;
						$total_line_consolide = 0;
						$total_line_prime = 0;
						$total_line_parrainage = 0;
						$total_line_penalite = 0;
						$total_line_frais = 0;
						$total_line_total = 0;
						$total_line_fond = 0;
						$total_line_dispo = 0;
						$total_line_stripe = 0;
						
						foreach ($lastOrder as $k => $row):
						
							$type_facture = 'fact';
						?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
							<td><?php 
								 echo $this->Html->link($row['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $row['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								
							 ?></td>
							<td><?php echo $row['name']; ?></td>
							<td><?php echo $row['societe']; ?></td>
							<td><?php 
								if($row['active'])
								echo 'Oui';
								else
								echo 'Non'; 
								?></td>
							<td><?php 
								if($row['consolide'] > 0){
									$total_line_gain += $row['consolide'];
									echo $row['consolide'];
								}else{
									echo $row['gain'];
									$total_line_gain += $row['gain'];
								}
									 ?></td>
							<td><?php 
								if($row['consolide'] > 0){
									echo $row['gain'] - $row['consolide'];
									$total_line_consolide += $row['gain'] - $row['consolide'];
								}else{
									echo 0;
								}
								 ?></td>
							<td><?php echo $row['prime']; $total_line_prime += $row['prime']; ?></td>
							<td><?php echo $row['sponsor']; $total_line_parrainage += $row['sponsor']; ?></td>
							<td><?php 
                if($row['penality'] < 0){
                 // $row['penality'] = ($row['penality'] * -1) + 17;
                  echo $row['penality'];
                }else{
                  if(!$row['is_avoir'] && $row['invoice_id'] && !$row['stripe_account']){
                    $pen = $row['penality'] ;//- 17;
                    if($pen < 0) $pen = $pen * -1;
                    echo '-'.$pen; 
                  }else
                  echo '-'.$row['penality']; 
                }
                $total_line_penalite += $row['penality']; ?></td>
							<td><?php 
								
								if(!$row['is_avoir'] && $row['invoice_id'] && !$row['stripe_account']){
									echo '-17.5';
									$total_line_frais += 17.5;
								}
								
								?></td>
							<td><span  style="color:#0B1297;font-weight:bold"><?php 
								
								if($row['paid_total_valid'] > 0 && $row['paid_total_valid'] != $row['paid_total'] ){
									echo '<span style="font-size:10px;text-decoration:line-through">'.$row['paid_total'].'</span> <b>'.$row['paid_total_valid'].'</b>';
									$total_line_total += $row['paid_total_valid'];
								}else{
									if($row['paid_total']){
										echo $row['paid_total'];
										$total_line_total += $row['paid_total'];
									}else{
										echo $row['total'];
										$total_line_total += $row['total'];
									}
									
								}
								
								 ?></span></td>
							<td>
								<?php
								
									//check if fact fusion
									$start    = new DateTime($row['fact_min']);
									$start_fact    = new DateTime($row['fact_min_date']);
									//echo $row['fact_min_date'];
								if($start->format("Y-m-d") == $start_fact->format("Y-m-d")){
								?>
								<a href="/<?=$type_facture?>.php?idagent=<?=$row['id']?>&fact_min=<?=$row['fact_min']?>&fact_max=<?=$row['fact_max']?>" target="_blank"><i class="icon-file" style="cursor:pointer !important;color:#777"></i></a>
								<?php 
								}else{
									//echo $row['fact_min']. ' -> '.$row['fact_min_date'];
									$diff = $start->diff($start_fact);
									$months = round($diff->y * 12 + $diff->m + ceil($diff->d / 30));
									//echo ' DIFF : '.$diff->y.' + '.$diff->m;
									while($months > 0){
									//for($mm = $months;$mm <= 0;$mm--){
									//	echo $mm;
										$old_fact_start   = new DateTime($row['fact_min']);
										$old_fact_start->modify('- '.$months.' month');
										$old_fact_end = (new DateTime($old_fact_start->format("Y-m-d H:i:s")))->modify('last day of this month');
										?>
										<a href="/<?=$type_facture?>.php?idagent=<?=$row['id']?>&fact_min=<?=$old_fact_start->format("Y-m-d H:i:s") ?>&fact_max=<?=$old_fact_end->format("Y-m-d 23:59:59") ?>" target="_blank"><i class="icon-file" style="cursor:pointer !important;color:#777"></i></a>&nbsp;&nbsp;
										<?php
											$months = $months - 1;
									}
								?>
								<a href="/<?=$type_facture?>.php?idagent=<?=$row['id']?>&fact_min=<?=$row['fact_min']?>&fact_max=<?=$row['fact_max']?>" target="_blank"><i class="icon-file" style="cursor:pointer !important;color:#777"></i></a>
								
								
								
								<?php
								}
								?>
							</td>
							<td><span ><?php echo $row['stripe_base']; $total_line_fond += $row['stripe_base']; ?></span></td>
							<td><span style="font-weight:600"><?php echo $row['stripe_available']; $total_line_dispo += $row['stripe_available']; ?></span></td>
							<td><span ><?php echo $row['stripe'];$total_line_stripe += $row['stripe']; ?></span></td>
							<td><span style="font-weight:bold;color:#940A0C"><?php if($row['mode'] != 'Virement' && $row['stripe_payout_status']) echo '<i class="icon-exclamation-sign" style="cursor:pointer" data-toggle="tooltip" data-original-title="'.$row['stripe_payout_status'].'" ></i>'; ?></span></td>
							<td><input type="checkbox" class="agent_facture_sold" rel="<?php echo $row['date']; ?>" value="<?php echo $row['id']; ?>" <?php	if($row['is_sold']) echo "checked disabled"; ?>/></td>
							<td align="center" style="text-align: center" class="veram"><?php if($row['paid_date']) echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['paid_date']),'%d/%m/%y %Hh%M'); ?></td>
							<td><?php 
									/*if($row['invoice_status'] != 11 && $row['stripe_account'] && (!$row['vat_num'] ||  $row['vat_status'] == 'invalide' )  ){
											if($row['vat_amount']){
												echo $this->Html->link('Confirmer TVA payé',
													array(
														'controller' => 'agents',
														'action' => 'order_vat_regul',
														'admin' => true,
														'id' => $row['invoice_id']
													),
													array('class' => 'btn red', 'escape' => false)
												);
											}else{
												echo 'Stripe erreur TVA';
											}
											
										}else{*/
										echo $row['vat_status'];
									//}
								?></td>
							<td align="center" style="text-align: center" class="veram"><?php 
								
								if(!$row['stripe_account']){
									echo $row['mode'];
								}else{
									if($row['stripe_account'] && !$row['iban']){
										echo 'Stripe erreur IBAN';
									}else{
										if($row['invoice_status'] == 11){
											echo 'TVA Régularisé - Facture non payé';
										}else{
											
											if(!$row['vat_amount'] && $row['stripe_account'] && $row['country_id'] != 3 &&  (!$row['vat_num'] ||  $row['vat_status'] == 'invalide' )  ){
												if($row['is_sold'])
													echo 'Virement';
												else
													echo 'Stripe erreur TVA';
											}else{
												if($row['invoice_status'] > 0 && $row['invoice_status'] < 2){
													echo $row['mode'];
												}else{
													if($row['invoice_id'] && $row['invoice_status'] < 7){
														echo $this->Html->link('<img src="/theme/default/img/stripe.png" style="width:50px;height:auto;">',
															array(
																'controller' => 'agents',
																'action' => 'order_pop',
																'admin' => true,
																'id' => $row['invoice_id']
															),
															array('class' => 'nx_modal_stripe', 'escape' => false)
														);
													}else{
														echo 'Stripe';
													}
												}
											}
										}
									}
									
								}
						
								
								?></td>
							<td>
								<?php
									if($row['invoice_id']){
									
										if($row['is_sold'] && $row['invoice_status'] <= 7){
											echo 'Validé et payé';
										}else{
										
											if($row['invoice_status'] == 2 || $row['invoice_status'] == 3){

												if($row['invoice_valid_1'] == $admin_id || $row['invoice_valid_2'] == $admin_id){
													echo 'Attente double opt-in';
												}else{
													echo $this->Html->link('Valider',
													array(
														'controller' => 'agents',
														'action' => 'order_stripe_valid',
														'admin' => true,
														'id' => $row['invoice_id']
													),
													array('class' => 'btn blue', 'escape' => false)
												);
												}


											}
											if($row['invoice_status'] == 5){
												if($row['stripe_available'] >= $row['paid_total'])
													echo 'Erreur documents';
												else
													echo 'Attente fond disponible';

											/*	if( ( $row['invoice_valid_1'] == $admin_id && !$row['invoice_valid_2']) || ($row['invoice_valid_2'] == $admin_id && !$row['invoice_valid_2'])){
													echo 'Attente double opt-in';
												}else{
													if(!$row['invoice_valid_1'] || !$row['invoice_valid_2']){
														echo $this->Html->link('Valider',
																array(
																	'controller' => 'agents',
																	'action' => 'order_stripe_valid',
																	'admin' => true,
																	'id' => $row['invoice_id']
																),
																array('class' => 'btn blue', 'escape' => false)
															);
													}else{
														echo 'Attente fond disponible';
													}
												}*/
											}
											if($row['invoice_status'] == 6){
												if( ( $row['invoice_valid_1'] == $admin_id && !$row['invoice_valid_2']) || ($row['invoice_valid_2'] == $admin_id && !$row['invoice_valid_2'])){
													echo $this->Html->link('Valider',
														array(
															'controller' => 'agents',
															'action' => 'order_stripe_valid',
															'admin' => true,
															'id' => $row['invoice_id']
														),
														array('class' => 'btn blue', 'escape' => false)
													);
												}else{
													echo $this->Html->link('Confirmer Virement',
														array(
															'controller' => 'agents',
															'action' => 'order_stripe_valid',
															'admin' => true,
															'id' => $row['invoice_id']
														),
														array('class' => 'btn green', 'escape' => false)
													);
												}
											}
											if($row['invoice_status'] == 7){
												echo 'Validé, Virement en attente';
											}
											if($row['invoice_status'] == 8){
												echo $this->Html->link('Echoué, relancer',
														array(
															'controller' => 'agents',
															'action' => 'order_stripe_valid',
															'admin' => true,
															'id' => $row['invoice_id']
														),
														array('class' => 'btn red', 'escape' => false)
													);
											}
											if($row['invoice_status'] == 9){
												echo 'Validé, Virement en transit';
											}
											if($row['invoice_status'] > 0 && $row['invoice_status'] < 2){
												echo 'Validé';
											}
											if($row['invoice_status'] < 1){
												echo 'Non traité';
											}
										}
									}
								
								if($row['tva_applique'])
									echo ' - TVA appliqué';
								
								if($row['is_avoir'])
									echo ' - Avoir généré';
								
								?>
							</td>
							
							<td>
								<?php
									if($row['invoice_id']){
								?>
								<a href="/<?=$type_facture?>2_us.php?idagent=<?=$row['id']?>&fact_min=<?=$row['fact_min']?>&fact_max=<?=$row['fact_max']?>" target="_blank"><i class="icon-file" style="cursor:pointer !important"></i></a>
								<?php
									}
								?>
							</td>
							<td>
								<?php
									if($row['invoice_id']){
									
										if(!$row['is_send']){
								 			echo $this->Html->link('<i class="icon-envelope" style="cursor:pointer !important"></i>',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'order_pop_mail',
                                                'admin' => true,
                                                'id' => $row['invoice_id']
                                            ),
                                            array('class' => 'nx_modal_stripe', 'escape' => false)
                                        );
										}else{
											if($row['date_send']) echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['date_send']),'%d/%m/%y %Hh%M'); 
										}
									}
								?>
							</td>
							<td><input type="checkbox" class="AdminOrderCheckbox" name="AdminOrderCheckAll_<?=$row['invoice_id'] ?>" rel="<?=$row['invoice_id'] ?>" /></td>
                        </tr>
                    <?php
	
						
						/*if(!$row['is_avoir']){*/
							
							if(!$row['invoice_id']){
								//unfact
								if($row['is_sold']){
									if($row['paid_total'] > 0)
										$total_paid +=$row['paid_total']; 
									else
										$total_paid +=$row['total']; 

									$total_gain_paid += $row['gain'];
									$total_prime_paid += $row['prime'];
									$total_parrainage_paid += $row['sponsor'];
									$total_penalite_paid += $row['penality'];
									$total_tva_paid += $row['vat_amount'];
								}else{
									if($row['paid_total'] > 0)
										$total_unfact +=$row['paid_total']; 
									else
										$total_unfact +=$row['total']; 

									$total_gain_unfact += $row['gain'];
									$total_prime_unfact += $row['prime'];
									$total_parrainage_unfact += $row['sponsor'];
									$total_penalite_unfact += $row['penality'];
									$total_tva_unfact += $row['vat_amount'];
								}
									
							}else{
								if(!$row['stripe_account'])$total_fees += 17;//PATCH CHANGEMENT FULL STRIPE
								if($start->format("Y-m-d") == $start_fact->format("Y-m-d")){
									if($row['invoice_id'] && $row['is_sold']){
										//paid
										if($row['paid_total'] > 0)
											$total_paid +=$row['paid_total']; 
										else
											$total_paid +=$row['total']; 

										$total_gain_paid += $row['gain'];
										$total_prime_paid += $row['prime'];
										$total_parrainage_paid += $row['sponsor'];
										$total_penalite_paid += $row['penality'];
										$total_tva_paid += $row['vat_amount'];
										if(!$row['stripe_account'])$total_fees_paid += 17;
									}else{
										//unpaid
										if($row['paid_total'] != 0)
											$total_unpaid +=$row['paid_total']; 
										else
											$total_unpaid +=$row['total']; 

										$total_gain_unpaid += $row['gain'];
										$total_prime_unpaid += $row['prime'];
										$total_parrainage_unpaid += $row['sponsor'];
										$total_penalite_unpaid += $row['penality'];
										$total_tva_unpaid += $row['vat_amount'];
										if(!$row['stripe_account'])$total_fees_unpaid += 17;
									}
								}else{
									//consolid
									if($row['invoice_id']  && $row['is_sold'] ){
										if($row['consolide'] > 0){
											$total_consolid +=$row['consolide'];
											$total_gain_paid += $row['consolide'];
										}else{
											$total_consolid +=$row['paid_total']; 
											$total_gain_paid += $row['gain'];
										}

										
										$total_prime_consolid += $row['prime'];
										$total_parrainage_consolid += $row['sponsor'];
										$total_penalite_consolid += $row['penality'];
										$total_tva_consolid += $row['vat_amount'];
										if(!$row['stripe_account'])$total_fees_consolid += 17;
									}else{
										//unpaid
										if($row['consolide'] > 0){
											$total_unpaid +=$row['consolide'];
											$total_gain_unpaid += $row['consolide'];
										}else{
											$total_unpaid +=$row['paid_total']; 
											$total_gain_unpaid += $row['gain'];
										}

										$total_prime_unpaid += $row['prime'];
										$total_parrainage_unpaid += $row['sponsor'];
										$total_penalite_unpaid += $row['penality'];
										$total_tva_unpaid += $row['vat_amount'];
										if(!$row['stripe_account'])$total_fees_unpaid += 0;
									}
								}
							}
							if($row['consolide'] > 0){
								$total +=$row['gain'];
								$total_gain += $row['consolide'];
							}elseif($row['paid_total_valid'] > 0){
								$total +=$row['paid_total_valid']; 
								$total_gain += $row['gain'];
								$total_tva += $row['vat_amount'];
							}else{
								if($row['paid_total'] != 0)
									$total +=$row['paid_total']; 
								else
									$total +=$row['total'];
								
								$total_gain += $row['gain'];
								$total_tva += $row['vat_amount'];
							}
							$total_prime += $row['prime'];
							$total_parrainage += $row['sponsor'];
							$total_penalite += $row['penality'];
							
						/*}*/
						endforeach; ?>
						<tr>
                            <td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?=$total_line_gain ?></td>
							<td><?=$total_line_consolide ?></td>
							<td><?=$total_line_prime ?></td>
							<td><?=$total_line_parrainage ?></td>
							<td><?=$total_line_penalite ?></td>
							<td><?=$total_line_frais ?></td>
							<td><?=$total_line_total ?></td>
							<td>&nbsp;</td>
							<td><?=$total_line_fond ?></td>
							<td><?=$total_line_dispo ?></td>
							<td><?=$total_line_stripe ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><a class="btn blue" id="AdminOrderValidAll">Tous valider</a></td>
                        </tr>
                    </tbody>
                </table>
				 <table class="table table-striped table-hover table-bordered" style="width:50%;">
                    <thead>
                    <tr>
                        <th><?php echo __('Facture'); ?></th>
						<th><?php echo __('Gain'); ?></th>
						<th><?php echo __('Prime'); ?></th>
						<th><?php echo __('Parrainage'); ?></th>
						<th><?php echo __('Penalité'); ?></th>
						<th><?php echo __('Frais'); ?></th>
						<th><?php echo __('TVA impacté'); ?></th>
						<th><?php echo __('Total généré'); ?></th>
                    </tr>
                    </thead>
					<tbody>
						<tr>
							<td><?php echo __('Payé'); ?></td>
							<td><?php echo number_format($total_gain_paid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_prime_paid+$total_prime_consolid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_parrainage_paid+$total_parrainage_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_penalite_paid+$total_penalite_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_fees_paid+$total_fees_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_tva_paid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_paid+$total_consolid,2,'.',' '); ?></td>
                        </tr>
						<!--<tr>
							<td><?php echo __('Consolidé'); ?></td>
							<td><?php echo number_format($total_gain_consolid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_prime_consolid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_parrainage_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_penalite_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_fees_consolid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_tva_consolid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_consolid,2,'.',' '); ?></td>
                        </tr>-->
						<tr>
							<td><?php echo __('Non payé'); ?></td>
							<td><?php echo number_format($total_gain_unpaid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_prime_unpaid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_parrainage_unpaid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_penalite_unpaid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_fees_unpaid,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_tva_unpaid,2,'.',' '); ?></td>
							<td><?php echo number_format($total_unpaid,2,'.',' '); ?></td>
                        </tr>
						<tr>
							<td><?php echo __('Non facturé'); ?></td>
							<td><?php echo number_format($total_gain_unfact,2,'.',' '); ?></td>
							<td><?php echo number_format($total_prime_unfact,2,'.',' '); ?></td>
							<td><?php echo number_format($total_parrainage_unfact,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_penalite_unfact,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_fees_unfact,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_tva_unfact,2,'.',' '); ?></td>
							<td><?php echo number_format($total_unfact,2,'.',' '); ?></td>
                        </tr>
						<tr>
							<td><b><?php echo __('TOTAL'); ?></b></td>
							<td><?php echo number_format($total_gain,2,'.',' '); ?></td>
							<td><?php echo number_format($total_prime,2,'.',' '); ?></td>
							<td><?php echo number_format($total_parrainage,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_penalite,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_fees,2,'.',' '); ?></td>
							<td>-<?php echo number_format($total_tva,2,'.',' '); ?></td>
							<td><b><?php echo number_format($total,2,'.',' '); ?></b></td>
                        </tr>
					</tbody>
				</table>
            <?php endif; ?>
        </div>
    </div>
</div>
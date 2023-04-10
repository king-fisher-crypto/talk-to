<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Historique des communications'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user-md',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Communications'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'com', 'admin' => true))
        ),
        3 => array(
            'text' => (!isset($user['User']['pseudo']) || empty($user['User']['pseudo'])?__('Agent'):$user['User']['pseudo']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        4 => array(
            'text' => __('Historique des communications'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'com_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les communications de').' '.$this->Html->link($user['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de toutes les communications de '.$user['User']['pseudo']),
                array('controller' => 'agents', 'action' => 'export_com', 'admin' => true, '?' => array('user' => $user['User']['id'])),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($allComs)): ?>
                <?php echo '<p>'.__('Aucune communication.').'</p>' ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.media', __('Media')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.phone_number', __('Numéro de téléphone')); ?></th>
                        <th><?php echo __('Type appel'); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.seconds', __('Durée')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.date_start', __('Date consultation')); ?></th>
                        <th><?php echo __('Rémunération'); ?></th>
                        <th><?php echo __('Facturé'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
					
					$total = 0;
					$total_phone = 0;
					$total_chat = 0;
					$total_email = 0;
					
					foreach ($allComs as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['user_id'], 'full_base' => true)); ?></td>
                            <td><?php echo __($consult_medias[$row['UserCreditHistory']['media']]); ?></td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['phone_number'])
                                    ?__('N/D')
                                    :$row['UserCreditHistory']['phone_number']
                                ); ?>
                            </td>
                            <td>
                                <?php 
									switch ($row['UserCreditHistory']['called_number']) {
										case 901801885:
											echo 'suisse audiotel';
											break;
										case 225183456:
											echo 'suisse prépayé';
											break;
										case 90755456:
											echo 'Belgique audiotel';
											break;
										case 3235553456:
											echo 'Belgique prépayé';
											break;
										case 90128222:
											echo 'Luxembourg audiotel';
											break;
										case 27864456:
											echo 'Luxembourg prépayé';
											break;
										case 4466:
											echo 'Canada audiotel mobile';
											break;
										case 19007884466:
											echo 'Canada audiotel fixe';
											break;
										case 18442514456:
											echo 'Canada prépayé';
											break;
										case 33970736456:
											echo 'France prépayé';
											break;
									}
								 ?>
                            </td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $row['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %H:%I:%S'); ?></td>
                        	<td>
                                <?php
								
								if(is_numeric($row['UserCreditHistory']['price'] )){
								 echo number_format($row['UserCreditHistory']['price'], 2).' €';  
								
									switch ($row['UserCreditHistory']['media']) {
										case 'phone':
											$total += number_format($row['UserCreditHistory']['price'], 2);
											$total_phone += number_format($row['UserCreditHistory']['price'], 2);
										break;
										case 'email':
											$total += number_format($row['UserCreditHistory']['price'], 2);
											$total_email += number_format($row['UserCreditHistory']['price'], 2);
										break;
										case 'chat':
											$total += number_format($row['UserCreditHistory']['price'], 2);
											$total_chat += number_format($row['UserCreditHistory']['price'], 2);
										break;
									}
								}else{
									echo 'calcul en cours';	
								}
                                ?>
                            </td>
                            <td>
							<input type="checkbox" class="agent_facture_choice" value="<?php echo $row['UserCreditHistory']['user_credit_history']; ?>" <?php	if($row['UserCreditHistory']['is_factured']) echo "checked"; ?>/>
							<i class="icon-pencil show_text_factured"></i>
                            <textarea class="text_agent_factured" id="text_agent_factured_<?php echo $row['UserCreditHistory']['user_credit_history']; ?>" style="display:none;"><?php echo $row['UserCreditHistory']['text_factured']; ?></textarea>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="portlet box yellow">
                <div class="portlet-title">
                	<div class="caption">
                    	Rémunération
                    </div>
                </div>
                <div class="portlet-body">
                	<table class="table table-striped table-hover table-bordered">
						<thead>
                        	<th>Total Téléphone</th>
                            <th>Total Chat</th>
                            <th>Total Email</th>
                            <th>TOTAL</th>
                            <th>&nbsp;</th>
                        </thead>
                       	<tbody>
                        	<tr>
                            	<td><?php echo $total_phone ?> €</td>
                                <td><?php echo $total_chat ?> €</td>
                                <td><?php echo $total_email ?> €</td>
                                <td><?php echo $total ?> €</td>
                                <td><?php 
									var_dump($date_fact);
								
								 ?> </td>
                            </tr>
                        </tbody>
                    </table>
                </div></div>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
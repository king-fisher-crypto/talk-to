<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Communications des clients'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Clients'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Appels perdu'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'com', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Appels perdu'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($allComs)): ?>
                <?php echo __('Pas de communication'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Callinfo.timestamp', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
						<th>Temps attente</th>
                        <th><?php echo $this->Paginator->sort('Callinfo.status', __('Vu par agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('Callinfo.date_send', __('Mail alerte')); ?></th>
                        <th class="text-center"></th> 
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allComs as $k => $row): ?>
                        <tr>
                           <td><?php echo $this->Time->format(date('Y-m-d, H:i:s',$row['Callinfo']['timestamp']),'%d %B %Y %H:%M'); ?></td>
                           <td><?php 
							   if($row['Callinfo']['agent1']){
								  echo $this->Html->link($row['Agent1']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent1']['id'], 'full_base' => true)).'<br />'; 
							   }
							   if($row['Callinfo']['agent2']){
								  echo $this->Html->link($row['Agent2']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent2']['id'], 'full_base' => true)).'<br />'; 
							   }
							   if($row['Callinfo']['agent3']){
								  echo $this->Html->link($row['Agent3']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent3']['id'], 'full_base' => true)).'<br />'; 
							   }
							   if($row['Callinfo']['agent4']){
								  echo $this->Html->link($row['Agent4']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent4']['id'], 'full_base' => true)).'<br />'; 
							   }
							   if($row['Callinfo']['agent5']){
								  echo $this->Html->link($row['Agent5']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent5']['id'], 'full_base' => true)).'<br />'; 
							   }
							  // if($row['Callinfo']['accepted'] == 'no' || $row['Callinfo']['reason'] == 'NOANSWER' || $row['Callinfo']['reason'] == 'BUSY' || $row['Callinfo']['reason'] == 'CHANUNAVAIL' || $row['Callinfo']['reason'] == 'CANCEL' ){
							   
							   	echo $this->Html->link($row['Agent']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent']['id'], 'full_base' => true));
							   //}
							   ?></td>
                            <td><?php 
								
								if($row['User']['firstname']){
								echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true));
								}else{
									echo 'AUDIO'.(substr($row['Callinfo']['callerid'], -4)*15);
								}
								?></td>
								 <td>
								<?php
								$date_start = new DateTime(date('Y-m-d H:i:s',$row['Callinfo']['time_getstatut']));
								$date_end = new DateTime(date('Y-m-d H:i:s',$row['Callinfo']['time_stop']));
								$consult_date_start = new DateTime(date('Y-m-d H:i:s',$row['Callinfo']['time_start']));
								
								if($row['Callinfo']['time_start'])	
								$interval = $date_start->diff($consult_date_start);
									else
								$interval = $date_start->diff($date_end);	

								$diff = $interval->format('%H:%I:%S');
								echo $diff;
								?>
							</td>
                       			<td>
                       			<?php
									if($row['Callinfo']['status'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       			</td>
                       			<td><?php
									if($row['Callinfo']['date_send']){
									echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Callinfo']['date_send']),'%d %B %Y %H:%M');
									}
									?></td>
                       			<td>
								<?php
									  if(!$row['Callinfo']['date_send'] && !$row['Callinfo']['agent1']){
										if(!$row['Callinfo']['status'])
									echo $this->Form->button('<i class="glyphicon glyphicon-eye icon_margin_right_5 "></i> '.__('Avertir par mail'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 alertelost',
																'href' => $this->Html->url(array('controller' => 'agents', 'action' => 'alertlostcall'),true),
																'comm' => $row['Callinfo']['callinfo_id'],
																'label' => '',
																'type'  => 'button'
															)
														);		
									  }
										?>
			  	 		</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
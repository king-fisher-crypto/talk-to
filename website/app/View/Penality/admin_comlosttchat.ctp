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
            'text' => __('Tchat perdu'),
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
            <div class="caption"><?php echo __('Tchat perdu'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($allComs)): ?>
                <?php echo __('Pas de communication'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Chat.date_start', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
						<th>Temps attente</th>
                        <th><?php echo $this->Paginator->sort('Chat.status', __('Vu par agent')); ?></th>
						<th><?php echo $this->Paginator->sort('UserPenality.reason', __('Raison')); ?></th>
                        <th><?php echo $this->Paginator->sort('Chat.date_send', __('Mail alerte')); ?></th>
                        <th class="text-center"></th> 
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allComs as $k => $row): ?>
                        <tr>
                           <td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['Chat']['date_start']),'%d %B %Y %H:%M'); ?></td>
                           <td><?php echo $this->Html->link($row['Agent']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent']['id'], 'full_base' => true)); ?></td>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true)); ?></td>
                           <td>
								<?php
								$date_start = new DateTime($row['Chat']['date_start']);
								/*$date_end = new DateTime($row['Chat']['date_end']);
								$consult_date_start = new DateTime($row['Chat']['consult_date_start']);
								
								if($row['Chat']['consult_date_start'])	
								$interval = $date_start->diff($consult_date_start);
									else
								$interval = $date_start->diff($date_end);	

								$diff = $interval->format('%H:%I:%S');*/
								echo $row['UserPenality']['delay'].' sec.';
								?>
							</td>
							<td>
                       			<?php
									if($row['UserPenality']['is_view'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       			</td>
							<td>
                       			<?php
									echo $row['UserPenality']['reason'];	
                       			?>	
                       			</td>
                       			<td><?php
									if($row['Chat']['date_send']){
									echo $this->Time->format($row['Chat']['date_send'],'%d %B %Y %H:%M');
									}
									?></td>
                       			<td><?php
										if(!$row['Chat']['date_send']){
										if(!$row['UserPenality']['is_view'])
									echo $this->Form->button('<i class="glyphicon glyphicon-eye icon_margin_right_5 "></i> '.__('Avertir par mail'),
															array(
																'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 alertelost',
																'href' => $this->Html->url(array('controller' => 'agents', 'action' => 'alertlostchat'),true),
																'comm' => $row['Chat']['id'],
																'label' => '',
																'type'  => 'button'
															)
														);	
										}
										?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
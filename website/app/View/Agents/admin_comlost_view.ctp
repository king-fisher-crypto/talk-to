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
            'text' => __('Historique des appels perdus'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'com_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Tous les appels perdus de').' '.$this->Html->link($user['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
            
        </div>
        <div class="portlet-body">
            <?php if(empty($allComs)): ?>
                <?php echo '<p>'.__('Aucune communication.').'</p>' ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo $this->Paginator->sort('Callinfo.sessionid', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('Callinfo.timestamp', __('Date consultation')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
					
					foreach ($allComs as $k => $row): ?>
                        <tr>
							<td><?php echo $row['Callinfo']['sessionid']; ?></td>
                            <td><?php 
								
								$client_name = '';
								if($row['User']['firstname']){
									$client_name = $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true));
								}else{
									$client_name = 'AUDIO'.(substr($row['Callinfo']['callerid'], -4)*15);
									
								}
								echo $client_name; ?></td>
                            <td><?php echo $this->Time->format(date('Y-m-d H:i:s',$row['Callinfo']['timestamp']),'%d %B %Y %H:%M:%S'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
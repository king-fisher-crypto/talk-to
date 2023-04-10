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
            'text' => __('CA'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Communication'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'com', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInputComTranche($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Communication'); ?></div>
            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de toutes les communications'),
                array('controller' => 'accounts', 'action' => 'export_com', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastCom)): ?>
                <?php echo __('Pas de communication'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo $this->Paginator->sort('UserCreditHistory.date_start', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Nom complet')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.agent_pseudo', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.media', __('Media')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.credits', __('Coût de la com.')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.seconds', __('Durée')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.ca', __('CA')); ?></th>
						<th><?php echo $this->Paginator->sort('Order.total', __('Total achat client')); ?></th>
						<th>Rémunération expert</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastCom as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %H:%M'); ?></td>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['user_id'], 'full_base' => true)); ?></td>
                            <td><?php echo $this->Html->link($row['UserCreditHistory']['agent_pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['agent_id'], 'full_base' => true)); ?></td>
                            <td><?php echo __($consult_medias[$row['UserCreditHistory']['media']]); ?></td>
                            <td><?php echo '-'.$row['UserCreditHistory']['credits']; ?></td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $row['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
							<td><?php echo $row['UserCreditHistory']['ca'].' '.$row['UserCreditHistory']['ca_devise']; ?></td>
                            <td><?php echo $row['UserCreditHistory']['order']; ?></td>
							<td><?php echo $row['UserCreditHistory']['pay']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Historique des communications'));
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
            'text' => __('Communications'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'com', 'admin' => true))
        ),
        3 => array(
            'text' => (!isset($user['User']['firstname']) || empty($user['User']['firstname'])?__('Client'):$user['User']['firstname']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        4 => array(
            'text' => __('Historique des communications'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'com_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les communications de').' '.$this->Html->link($user['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de toutes les communications de '.$user['User']['firstname']),
                array('controller' => 'accounts', 'action' => 'export_com', 'admin' => true, '?' => array('user' => $user['User']['id'])),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($allComs)):
                echo '<p>'.__('Aucune communication').'</p>';
            else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo $this->Paginator->sort('UserCreditHistory.sessionid', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.agent_pseudo', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.media', __('Media')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.credits', __('Coût de la com.')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.seconds', __('Durée')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.user_credits_before', __('Crédit avant')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.date_start', __('Date')); ?></th>
						<th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allComs as $k => $row): ?>
                        <tr>
							<td><?php echo $row['UserCreditHistory']['sessionid']; ?></td>
                            <td><?php echo $this->Html->link($row['UserCreditHistory']['agent_pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['agent_id'], 'full_base' => true)); ?></td>
                            <td><?php echo __($consult_medias[$row['UserCreditHistory']['media']]); ?></td>
                            <td><?php echo '-'.$row['UserCreditHistory']['credits']; ?></td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $row['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
                            <td><?php echo $row['UserCreditHistory']['user_credits_before'];; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %Hh%M'); ?></td>
							<td><a class="btn blue nx_viewcomm" href="/admins/getCommunicationData-<?php echo $row['UserCreditHistory']['user_credit_history']; ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
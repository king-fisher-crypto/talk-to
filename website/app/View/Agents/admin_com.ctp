<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Communications des agents'));
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
            'text' => __('Communication'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'com', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInputCom($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Dernière communication des agents'); ?></div>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de toutes les communications'),
                array('controller' => 'agents', 'action' => 'export_com', 'admin' => true),
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
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.agent_pseudo', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.media', __('Media')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.seconds', __('Durée')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.date_start', __('Date')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastCom as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Html->link($row['UserCreditHistory']['agent_pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['agent_id'], 'full_base' => true)); ?></td>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['user_id'], 'full_base' => true)); ?></td>
                            <td><?php echo __($consult_medias[$row['UserCreditHistory']['media']]); ?></td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $row['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %H:%M'); ?></td>
                            <td><?php echo $this->Metronic->getLinkButton(
                                    __('Voir l\'historique complet de l\'agent')   ,
                                    array('controller' => 'agents', 'action' => 'com_view', 'admin' => true, 'id' => $row['UserCreditHistory']['agent_id']),
                                    'btn blue',
                                    'icon-zoom-in'
                                ); ?>
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
<div class="row-fluid">
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. __(' communications par mode'); ?></div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('Phone'); ?></th>
                        <th><?php echo __('Tchat'); ?></th>
                        <th><?php echo __('Email'); ?></th>
                        <th><?php echo __('Total'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $nbComPhone ?> (<?php echo gmdate("H:i:s", $nbMinComPhone); ?>)</td>
                            <td><?php echo $nbComTchat ?> (<?php echo gmdate("H:i:s", $nbMinComTchat);  ?>)</td>
                            <td><?php echo $nbComMail ?> (<?php echo gmdate("H:i:s", $nbMinComMail);  ?>)</td>
                            <td><?php 
									$nbComTotal = $nbComPhone + $nbComTchat + $nbComMail;
								$nbMinComTotal = $nbMinComPhone + $nbMinComTchat;
								echo $nbComTotal ?> (<?php echo gmdate("H:i:s", $nbMinComTotal);  ?>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
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
            'text' => __('Gains'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'com', 'admin' => true))
        ),
        3 => array(
            'text' => (!isset($user['User']['firstname']) || empty($user['User']['firstname'])?__('Client'):$user['User']['firstname']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        4 => array(
            'text' => __('Historique des gains'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'loyalty_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les gains de').' '.$this->Html->link($user['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
           
        </div>
        <div class="portlet-body">
            <?php if(empty($allLoyaltys)):
                echo '<p>'.__('Aucun gain').'</p>';
            else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Libéllé</th>
                        <th>Credit</th>
                        <th><?php echo $this->Paginator->sort('LoyaltyCredit.date_add', __('Date')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allLoyaltys as $k => $row): ?>
                        <tr>
                            <td>Gain fidélité +10 Minutes</td>
                            <td>+600 crédits</td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['LoyaltyCredit']['date_add']),'%d %B %Y %Hh%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
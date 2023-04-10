<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Historique des gains'));
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
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'sponsorship_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
	 <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Tous les gains de').' '.$this->Html->link($user['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
           
        </div>
        <div class="portlet-body">
            <?php if(empty($allSponsorships)):
                echo '<p>'.__('Aucun gain').'</p>';
            else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Filleul</th>
                        <th>Gain</th>
                        <th><?php echo $this->Paginator->sort('Sponsorship.date_recup', __('Date')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allSponsorships as $k => $row): ?>
                        <tr>
                            <td><?php
								echo $this->Html->link($row['Filleul']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['Sponsorship']['id_customer'], 'full_base' => true));
								?></td>
                            <td><?php echo $row['Sponsorship']['bonus'].' '.$row['Sponsorship']['bonus_type'] ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Sponsorship']['date_recup']),'%d %B %Y %H:%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
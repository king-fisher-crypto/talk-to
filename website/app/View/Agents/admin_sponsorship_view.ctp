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
            'text' => __('Historique des gains'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'sponsorship_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
	<?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Tous les gains de').' '.$this->Html->link($user['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
           
        </div>
        <div class="portlet-body">
            <?php if(empty($allSponsorships)):
                echo '<p>'.__('Aucun gain').'</p>';
            else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Filleul</th>
						<th><?php echo __('Session ID') ?></th>
						<th><?php echo __('Credits') ?></th>
                        <th>Gain</th>
                        <th><?php echo $this->Paginator->sort('UserCreditHistory.date_start', __('Date')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allSponsorships as $k => $row): ?>
                        <tr>
                            <td><?php
								echo $this->Html->link($row['Filleul']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['Sponsorship']['id_customer'], 'full_base' => true));
								?></td>
							<td><?php echo $row['UserCreditHistory']['sessionid']; ?></td>
							<td><?php echo $row['UserCreditHistory']['credits']; ?></td>
                            <td><?php 
								$bonus = str_replace(',','.',$row['Sponsorship']['bonus']) /60 * $row['UserCreditHistory']['credits'];
								echo number_format($bonus,2,',',' ').' '.$row['Sponsorship']['bonus_type'] ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %H:%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
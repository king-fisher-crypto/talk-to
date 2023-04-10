<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Consultations des agents'));
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
            'text' => __('Consultations'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'consults_agent', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInputComNbr($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Consultations'); ?></div>
           
        </div>
        <div class="portlet-body">
            <?php if(empty($lastCom)): ?>
                <?php echo __('Pas de communication'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.pseudo', __('Agent')); ?></th>
                        <th><?php echo  __('Email'); ?></th>
                        <th><?php echo  __('Tchat'); ?></th>
                        <th><?php echo  __('Tél.'); ?></th>
						<th><?php echo  __('Total'); ?></th>
						<th><?php echo  __('Statut TVA'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastCom as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Html->link($row['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true)); ?></td>
                            <td><?php echo $row[0]['total_consult_email']; ?></td>
                            <td><?php echo $row[0]['total_consult_chat']; ?></td>
							<td><?php echo $row[0]['total_consult_phone']; ?></td>
							<td><?php echo $row[0]['total_consult']; ?></td>
							<td><?php echo $row['User']['vat_num_status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
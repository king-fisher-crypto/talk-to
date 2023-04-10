<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Admin'),__('Logs'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Logs'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'logs', 'admin' => true))
        ),
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Logs'); ?></div>
			<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Log', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('fullname', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'value'=> $filtre_name, 'label' => __('Administrateur').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
					echo '</form>'
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($allLogs)): ?>
                <?php echo __('Pas de log'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('AdminLog.date_add', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Employe')); ?></th>
						<th><?php echo $this->Paginator->sort('AdminLog.type', __('Type')); ?></th>
						<th><?php echo $this->Paginator->sort('AdminLog.url', __('Page')); ?></th>
						<th><?php echo $this->Paginator->sort('AdminLog.object', __('Action')); ?></th>
						<th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allLogs as $k => $row): ?>
                        <tr>
                           <td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['AdminLog']['date_add']),'%d %B %Y %H:%M'); ?></td>
                           <td><?php echo $row['User']['firstname']; ?></td>
                           <td><?php echo $row['AdminLog']['type']; ?></td>
							<td><?php echo $row['AdminLog']['url']; ?></td>
							<td><?php echo $row['AdminLog']['object']; ?></td>
                       		<td>
								&nbsp;	
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
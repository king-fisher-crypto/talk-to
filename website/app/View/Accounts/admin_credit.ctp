<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Achats des clients'));
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
            'text' => __('Achats'),
            'classes' => 'icon-euro',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'credit', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="pull-left">
                <?php
                    echo $this->Form->create('UserCredit', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('IP', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('IP').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
    <div class="portlet box red">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Dernier achat des clients'); ?></div>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les achats'),
                array('controller' => 'accounts', 'action' => 'export_credit', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastCredit)): ?>
                <?php echo __('Pas d\'achat'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Nom complet')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCredit.credits', __('Credit')); ?></th>
						<th><?php echo $this->Paginator->sort('UserCredit.credits', __('Minutes')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCredit.product_name', __('Produit')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCredit.payment_mode', __('Mode paiement')); ?></th>
                        <th><?php echo $this->Paginator->sort('Orders.total', __('Montant')); ?></th>
                        <th><?php echo $this->Paginator->sort('Orders.currency', __('Devise')); ?></th>
                        <th><?php echo $this->Paginator->sort('Orders.IP', __('IP')); ?></th>
                        <th><?php echo $this->Paginator->sort('UserCredit.date_upd', __('Date d\'achat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastCredit as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['UserCredit']['users_id'], 'full_base' => true)); ?></td>
                            <td><?php echo $row['UserCredit']['credits']; ?></td>
							<td><?php 
								
								$min = $row['UserCredit']['credits'] / 60;
								
								echo $min.' min.'; ?></td>
                            <td><?php echo $row['UserCredit']['product_name']; ?></td>
                            <td><?php echo $row['UserCredit']['payment_mode']; ?></td>
                            <td><?php echo number_format($row['Orders']['total'],2); ?></td>
                            <td><?php echo $row['Orders']['currency']; ?></td>
                            <td><?php echo $row['Orders']['IP']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCredit']['date_upd']),'%d %B %Y %H:%M'); ?></td>
                            <td><?php echo $this->Metronic->getLinkButton(
                                    __('Voir l\'historique complet')   ,
                                    array('controller' => 'accounts', 'action' => 'credit_view', 'admin' => true, 'id' => $row['UserCredit']['users_id']),
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
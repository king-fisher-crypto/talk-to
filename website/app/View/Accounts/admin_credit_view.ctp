<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Historique des achats'));
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
        ),
        3 => array(
            'text' => (!isset($user['User']['firstname']) || empty($user['User']['firstname'])?__('Client'):$user['User']['firstname']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        4 => array(
            'text' => __('Historique des achats'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'credit_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
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
            <div class="caption"><?php echo __('Tous les achats de').' '.$this->Html->link($user['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>

            <?php 
			if(!empty($user_level) && $user_level != 'moderator'){
			echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les achats de '.$user['User']['firstname']),
                array('controller' => 'accounts', 'action' => 'export_credit', 'admin' => true, '?' => array('user' => $user['User']['id'])),
                'btn blue pull-right',
                'icon-file'
            );
			}?>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('ProductLang.name', __('Pack')); ?></th>
                    <th><?php echo $this->Paginator->sort('Product.tarif', __('Prix')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserCredit.credits', __('Crédit')); ?></th>
					<th><?php echo $this->Paginator->sort('UserCredit.credits', __('Minutes')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserCredit.product_name', __('Produit')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserCredit.payment_mode', __('Mode paiement')); ?></th>
					<th><?php echo __('Transaction ID') ?></th>
                    <th><?php echo $this->Paginator->sort('Orders.voucher_name', __('Voucher')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserCredit.date_upd', __('Date d\'achat')); ?></th>
                    <th><?php echo $this->Paginator->sort('Orders.IP', __('IP')); ?></th>
					<th><?php echo __('Statut') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($allCredits as $k => $row): ?>
                    <tr>

                        <td><?php echo $row['ProductLang']['name']; ?></td>
                        <td><?php echo $row['Product']['tarif'].' '.$devises[$row['Product']['country_id']]; ?></td>
                        <td><?php echo $row['UserCredit']['credits']; ?></td>
						<td><?php 
								
								$min = $row['UserCredit']['credits'] / 60;
								
								echo $min.' min.'; ?></td>
                        <td><?php 
							
							if($row['UserCredit']['payment_mode']== 'refund'){
								echo $row['UserCredit']['product_name'].' ( ID : '.$row['Orders']['id_com'].' )';
							}else
							echo $row['UserCredit']['product_name']; ?></td>
                        <td><?php echo $row['UserCredit']['payment_mode']; ?>
                        <?php if(($row['Orders']['valid'] == 3 && $row['UserCredit']['payment_mode'] == 'hipay') || ($row['Orders']['valid'] == 4 && $row['UserCredit']['payment_mode'] == 'paypal') ) echo ' - <b>remboursé</b>'; ?>
                        </td>
						<td><?php 
								if($row['UserCredit']['payment_mode']== 'hipay') echo $row['Hipay']['transaction'];
								if($row['UserCredit']['payment_mode']== 'paypal') echo $row['Paypal']['payment_transactionid'];
								if($row['UserCredit']['payment_mode']== 'stripe') echo $row['Stripe']['id'];
								if($row['UserCredit']['payment_mode']== 'sepa') echo $row['Sepa']['charge_id'];
								
								?></td>
                        <td><?php if($row['Orders']['voucher_name']) echo $row['Orders']['voucher_name'].' ('.$row['Orders']['voucher_code'].')'; ?></td>
                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCredit']['date_upd']),' %d %B %Y'); ?></td>
                        <td><?php echo $row['Orders']['IP']; ?></td>
						<td><?php 
								if($row['UserCredit']['payment_mode']== 'hipay'){
									if($row['Orders']['valid']== '0') echo 'refusé';	
									if($row['Orders']['valid']== '1') echo 'accepté';
									if($row['Orders']['valid']== '2') echo 'impayé';
									if($row['Orders']['valid']== '3') echo 'remboursé';
									if($row['Orders']['valid']== '4') echo '';
								}

								if($row['UserCredit']['payment_mode']== 'paypal'){
									if($row['Orders']['valid']== '0') echo 'refusé';
									if($row['Orders']['valid']== '1') echo 'accepté';
									if($row['Orders']['valid']== '2') echo 'en attente';
									if($row['Orders']['valid']== '3') echo 'impayé';
									if($row['Orders']['valid']== '4') echo 'remboursé';
								}
								if($row['UserCredit']['payment_mode']== 'bankwire'){
									if($row['Orders']['valid']== '0') echo 'refusé';
									if($row['Orders']['valid']== '1') echo 'accepté';
									if($row['Orders']['valid']== '2') echo 'en attente';
									if($row['Orders']['valid']== '3') echo '';
									if($row['Orders']['valid']== '4') echo '';
								}
								
								
								?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
        </div>
    </div>
</div>
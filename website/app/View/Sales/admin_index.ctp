<?php
    echo $this->Metronic->titlePage(__('Sales Reconciliation'),__('Sales Reconciliation'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Sales Reconciliation'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sales', 'action' => 'index', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Sales Reconciliation'); ?></div>
			 <?php /*echo $this->Metronic->getLinkButton(
                __('Créér'),
                array('controller' => 'penality', 'action' => 'create', 'admin' => true),
                'btn red pull-right',
                'icon-pen'
            ); */ ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastOrder)) :
                echo __('Aucune donnée');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.date_reconciliation', __('Date')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.stripe', __('Total Stripe')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.paypal', __('Total Paypal')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.stripe_unused', __('Total Stripe non utilise')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.paypal_unused', __('Total Paypal non utilise')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.unused_credit', __('Total credits des mois precedent utilisé')); ?></th>
						
                        
						<th><?php echo $this->Paginator->sort('SaleReconciliation.invoice_prepaid', __('Total CA prépayé')); ?></th>
						<!--<th><?php echo $this->Paginator->sort('SaleReconciliation.invoice_premium', __('Total CA audiotel')); ?></th>-->
                        <th><?php echo $this->Paginator->sort('SaleReconciliation.invoice_agent', __('Total Facture Frais Glassgen')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.credit_note', __('Total credit note')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.id', __('Facture frais Glassgen Net')); ?></th>
						
						<th><?php echo $this->Paginator->sort('SaleReconciliation.bankwire_agent', __('Total virement expert')); ?></th>
						<!--<th><?php echo $this->Paginator->sort('SaleReconciliation.working_capital', __('Fond de roulement')); ?></th>-->
						
						<th><?php echo $this->Paginator->sort('SaleReconciliation.owed_agent', __('Total transfert expert')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.error_agent', __('Total erreur expert')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.premium_number', __('Total audiotel')); ?></th>
                        <th><?php echo $this->Paginator->sort('SaleReconciliation.vat_invoice_agent', __('Total TVA')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.id', __('Sales per accounts')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.currency_diff', __('Total conversion devise')); ?></th>
						<th><?php echo $this->Paginator->sort('SaleReconciliation.id', __('Balance')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
						
						foreach ($lastOrder as $order):
							$balance = 0;
						
							$top = $order['SaleReconciliation']['invoice_agent'] - $order['SaleReconciliation']['credit_note'];
							$bottom = $order['SaleReconciliation']['stripe'] + $order['SaleReconciliation']['paypal'] - $order['SaleReconciliation']['bankwire_agent'] - $order['SaleReconciliation']['unused_credit'] - $order['SaleReconciliation']['owed_agent'] - $order['SaleReconciliation']['vat_invoice_agent'] + $order['SaleReconciliation']['error_agent'] + $order['SaleReconciliation']['premium_number'];
						
							$balance = $top - $bottom + $order['SaleReconciliation']['currency_diff'];
							
						?>
                        <tr>
                            <td><?php echo $order['SaleReconciliation']['id']; ?></td>
							<td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$order['SaleReconciliation']['date_reconciliation']),'%Y-%m'); ?></td>
							
							<td><span  style="color:#0CD41D;font-weight:700"><?php echo $order['SaleReconciliation']['stripe']; ?></span></td>
							<td><span  style="color:#0CD41D;font-weight:700"><?php echo $order['SaleReconciliation']['paypal']; ?></span></td>
							<td><span  style="">-<?php echo $order['SaleReconciliation']['stripe_unused']; ?></span></td>
							<td><span  style="">-<?php echo $order['SaleReconciliation']['paypal_unused']; ?></span></td>
							<td><?php echo $order['SaleReconciliation']['unused_credit']; ?></td>
							
							<td><?php echo $order['SaleReconciliation']['invoice_prepaid']; ?></td>
						<!--	<td><?php echo $order['SaleReconciliation']['invoice_premium']; ?></td>-->
							<td><span  style="color:#0CD41D;font-weight:700"><?php echo $order['SaleReconciliation']['invoice_agent']; ?></span></td>
							<td><span  style="color:#0CD41D;font-weight:700">-<?php echo $order['SaleReconciliation']['credit_note']; ?></span></td>
							<td><strong><?php echo $top ?></strong></td>
							
							<td><span  style="color:#0CD41D;font-weight:700">-<?php echo $order['SaleReconciliation']['bankwire_agent']; ?></span></td>
							<!--<td><?php echo $order['SaleReconciliation']['working_capital']; ?></td>-->
							
							<td><span  style="color:#0CD41D;font-weight:700">-<?php echo $order['SaleReconciliation']['owed_agent']; ?></span></td>
							<td><?php echo $order['SaleReconciliation']['error_agent']; ?></td>
							<td><?php echo $order['SaleReconciliation']['premium_number']; ?></td>
							<td><span  style="color:#0CD41D;font-weight:700">-<?php echo $order['SaleReconciliation']['vat_invoice_agent']; ?></span></td>
							<td><strong><?php echo $bottom ?></strong></td>
							<td><?php echo $order['SaleReconciliation']['currency_diff']; ?></td>
							
							<td><strong><?php echo $balance ?></strong></td>
							
                            <td><?php
									if(!$order['SaleReconciliation']['status']){
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'sales', 'action' => 'edit', 'admin' => true, 'id' => $order['SaleReconciliation']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
								  	echo $this->Metronic->getLinkButton(
                                        __('Valider')   ,
                                        array('controller' => 'sales', 'action' => 'validate', 'admin' => true, 'id' => $order['SaleReconciliation']['id']),
                                        'btn green',
                                        'icon-tick'
                                    );
									}
                                ?>
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
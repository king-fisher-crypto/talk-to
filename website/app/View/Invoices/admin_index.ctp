<?php
    echo $this->Metronic->titlePage(__('Factures'),__('Les factures'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Facture'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'invoices', 'action' => 'index', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les factures'); ?></div>
			 <?php /*echo $this->Metronic->getLinkButton(
                __('Créér'),
                array('controller' => 'penality', 'action' => 'create', 'admin' => true),
                'btn red pull-right',
                'icon-pen'
            ); */ ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastOrder)) :
                echo __('Aucune factures');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('InvoiceOther.order_id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('InvoiceSociety.name', __('Societe')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceCustomer.name', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceOther.amount_total', __('Montant')); ?></th>
						<th><?php echo $this->Paginator->sort('InvoiceOther.date_order', __('Date Facture')); ?></th>
						<th><?php echo $this->Paginator->sort('InvoiceOther.id', __('Facture')); ?></th>
						<!--<th><?php echo $this->Paginator->sort('InvoiceOther.status', __('Statut')); ?></th>-->
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastOrder as $order): ?>
                        <tr>
                            <td><?php echo $order['InvoiceOther']['order_id']; ?></td>
							<td><?php echo $order['InvoiceSociety']['name']; ?></td>
							<td><?php echo $order['InvoiceCustomer']['name']; ?></td>
							<td><?php echo $order['InvoiceOther']['amount_total'].' '.$order['InvoiceOther']['currency']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$order['InvoiceOther']['date_order']),'%d/%m/%Y'); ?></td>
                           <!-- <td><?php  echo ($order['InvoiceOther']['status'] == "1"
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>-->
							<td><a href="/fact_other2.php?id=<?=$order['InvoiceOther']['id']?>" target="_blank"><i class="icon-file" style="cursor:pointer !important"></i></a>
							<?php
								 if($order['InvoiceOther']['status'] == 2){
							?>
								&nbsp;&nbsp;&nbsp;<a href="/fact_other_voucher.php?id=<?=$order['InvoiceOther']['id']?>" target="_blank"><i class="icon-file" style="cursor:pointer !important"></i></a>
							<?php
								 }
							?>
							</td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'invoices', 'action' => 'edit', 'admin' => true, 'id' => $order['InvoiceOther']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
								   if($order['InvoiceOther']['status'] <2){
									  echo $this->Metronic->getLinkButton(
                                        __('Créér avoir')   ,
                                        array('controller' => 'invoices', 'action' => 'voucher', 'admin' => true, 'id' => $order['InvoiceOther']['id']),
                                        'btn yellow',
                                        'icon-file'
                                    );  
									   
								    echo $this->Metronic->getLinkButton(
                                        __('Supprimer')   ,
                                        array('controller' => 'invoices', 'action' => 'remove', 'admin' => true, 'id' => $order['InvoiceOther']['id']),
                                        'btn red',
                                        'icon-remove'
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
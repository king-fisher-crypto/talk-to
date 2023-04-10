<?php
    echo $this->Metronic->titlePage(__('Avoir'),__('Les avoirs agent'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('TVA'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'voucher_list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les avoirs Agent'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($invoices)) :
                echo __('Aucun avoir');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('InvoiceVoucherAgent.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('InvoiceVoucherAgent.date_add', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVoucherAgent.user_id', __('Agent')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVoucherAgent.invoice_id', __('Date Facture Source')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVoucherAgent.amount', __('Montant')); ?></th>
						<th><?php echo __('Pdf'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?php echo $invoice['InvoiceVoucherAgent']['id']; ?></td>
							<td align="center" style="text-align: center" class="veram"><?php if($invoice['InvoiceVoucherAgent']['date_add']) echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$invoice['InvoiceVoucherAgent']['date_add']),'%d/%m/%y'); ?></td>
							<td><?php 
								 echo $this->Html->link($invoice['User']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $invoice['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								
							 ?></td>
							<td align="center" style="text-align: center" class="veram"><?php if($invoice['InvoiceAgent']['date_max']) echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$invoice['InvoiceAgent']['date_max']),'%m/%y'); ?></td>
							
							<td><?php echo $invoice['InvoiceVoucherAgent']['amount']; ?>€</td>
							<td><a href="/fact_avoir.php?id=<?=$invoice['InvoiceVoucherAgent']['id'] ?>" target="_blank"><i class="icon-file" style="cursor:pointer !important"></i></a></td>
                            <td><?php
                                   /* echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $vat['InvoiceVat']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );*/
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
<?php
echo $this->Metronic->titlePage(__('Paiements par ').' '.$page_title);
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Paiements par ').' '.$page_title, 'classes' => 'icon-euro'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"><?php echo __('Paiements par ').' '.$page_title; ?></div>

    </div>
    <div class="portlet-body">
        <?php if(empty($orders)): ?>
            <?php echo __('Pas de paiement'); ?>
        <?php else: ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Order.id', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.reference', __('Référence')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.lastname', __('Client')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.product_credits', __('Crédits')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.total', __('Code promo')); ?></th>


                    <th><?php echo $this->Paginator->sort('Order.total', __('Mode réduction')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.total', __('Prix')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.total', __('Réduction')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.total', __('Total')); ?></th>


                    <th><?php echo $this->Paginator->sort('Order.total', __('Valide')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.total', __('Date')); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $k => $row): ?>
                    <tr>
                        <td><?php echo $row['Order']['id']; ?></td>
                        <td><?php echo $row['Order']['reference']; ?></td>
                        <td><?php echo $row['User']['lastname'].' '.$row['User']['firstname']; ?></td>
                        <td><?php echo $row['Order']['product_credits'].((!empty($row['Order']['voucher_credits']))?'+'.$row['Order']['voucher_credits']:''); ?></td>
                        <td><?php echo $row['Order']['voucher_code']; ?></td>


                        <td><?php

                                switch ($row['Order']['voucher_mode']){
                                    case 'amount': echo '<span class="badge" style="background-color:#00d1d9;">'.__('Montant').'</span>'; break;
                                    case 'percent': echo '<span class="badge" style="background-color:#00b5bd;">'.__('Remise %').'</span>'; break;
                                    case 'credit': echo '<span class="badge" style="background-color:#009aa2;">'.__('Crédit').'</span>'; break;
                                    default: echo '<span class="badge">'.__('Aucune').'</span>'; break;
                                }
                                 ?></td>
                        <td><?php echo $this->Nooxtools->displayPrice($row['Order']['product_price'], $row['Order']['currency']); ?></td>
                        <td><?php
                            switch ($row['Order']['voucher_mode']){
                                case 'amount': echo $this->Nooxtools->displayPrice($row['Order']['voucher_amount']); break;
                                case 'percent': echo $row['Order']['voucher_percent'].'%'; break;
                                case 'credit': echo '+'.$row['Order']['voucher_credits'].' credits'; break;

                            }

                            ?></td>
                        <td><strong><?php echo $this->Nooxtools->displayPrice($row['Order']['total'], $row['Order']['currency']); ?></strong></td>



                        <td><?php echo $row['Order']['valid']; ?></td>
                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Order']['date_add']),'%d %B %Y'); ?></td>

                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
        <?php endif; ?>
    </div>
</div>
</div>
<?php
echo $this->Metronic->titlePage(__('Remboursement'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Remboursement').' '.$page_title, 'classes' => 'icon-euro'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"><?php echo $this->Html->link(__('Créér un remboursement'),array('controller' => 'refund', 'action' => 'create', 'admin' => true)); ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Refund', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client').' :', 'div' => false));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Email').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                
                ?>
            </div>
    </div>
    <div class="portlet-body">
        <?php if(empty($refunds)): ?>
            <?php echo __('Pas d\'écriture'); ?>
        <?php else: ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Order.id', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('Client.firstname', __('Client')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.product_name', __('Type')); ?></th>


                    <th><?php echo $this->Paginator->sort('Order.product_price', __('Prix')); ?></th>
                    <th><?php echo $this->Paginator->sort('Order.product_credits', __('Crédits')); ?></th>
<th><?php echo $this->Paginator->sort('Order.commentaire', __('Commentaire')); ?></th>

                    <th><?php echo $this->Paginator->sort('Order.date_add', __('Date')); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($refunds as $k => $row): ?>
                    <tr>
                        <td><?php echo $row['Order']['id']; ?></td>
                        <td><?php


                            echo $this->Html->link('<span class="icon-user"></span> '.$row['Client']['firstname'], array(
                                'controller' => 'accounts',
                                'action'     => 'view',
                                'admin'      => true,
                                'id'         => $row['Client']['id']
                            ), array(
                                'target' => '_blank',
                                'title'  => __('Voir la fiche client #'.$row['CLient']['id']),
                                'escape' => false
                            ));



                            ?></td>
                        <td><?php echo $row['Order']['product_name']; ?></td>
                        <td><?php echo number_format($row['Order']['product_price'],2); ?></td>
						<td><?php echo $row['Order']['product_credits']; ?></td>
						<td><?php echo nl2br($row['Order']['commentaire']); ?></td>
                       
                      
                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Order']['date_add']),'%d %B %Y %H:%M'); ?></td>
                       
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
        <?php endif; ?>
    </div>
</div>
</div>
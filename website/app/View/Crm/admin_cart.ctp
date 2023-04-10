<?php
    echo $this->Metronic->titlePage(__('Paniers'),__('Paniers'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Les paniers'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'cart', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les paniers'); ?></div>
            <div class="pull-right">
              <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Crm', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                   // echo $this->Form->input('vouchers_title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Titre').' :', 'div' => false));
                   // echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>
                <?php
                   /* echo $this->Form->create('Vouchers', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('vouchers_code', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Code').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'*/
                ?>

            <?php /*echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les vouchers'),
                array('controller' => 'vouchers', 'action' => 'export_voucher', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); */?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($carts)) :
                echo __('Aucun panier');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('CartLoose.id', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('CartLoose.date_cart', __('Date')); ?></th>
                        
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('Product.name', __('Produit')); ?></th>
                        <th><?php echo $this->Paginator->sort('Cart.voucher_code', __('Bon réduction')); ?></th>
                        
                        
                        <th><?php echo $this->Paginator->sort('CartLoose.status', __('Etat')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($carts as $cart): ?>
                        <tr>
                            <td><?php echo $cart['CartLoose']['id']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$cart['CartLoose']['date_cart']),'%d %B %Y %Hh%M'); ?></td>
                            <td><?php echo $this->Html->link($cart['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $cart['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ) ?></td>
                            <td><?php echo $cart['Product']['name']; ?></td>
                            <td><?php echo $cart['Cart']['voucher_code']; ?></td>
                            <td><?php 
								if($cart['CartLoose']['status'] == -1) echo 'Panier abandonné';
								if($cart['CartLoose']['status'] == 0) echo 'Achat non finalisé';
								if($cart['CartLoose']['status'] == 1) echo 'Validé';
								
								?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
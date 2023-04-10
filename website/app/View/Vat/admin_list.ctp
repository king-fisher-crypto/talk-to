<?php
    echo $this->Metronic->titlePage(__('TVA'),__('Les TVA agent'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('TVA'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'vat', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les TVA Agent'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($vats)) :
                echo __('Aucune TVA');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('InvoiceVat.id', __('#')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVat.country_id', __('Pays')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVat.society_type_id', __('Type Structure')); ?></th>
                        <th><?php echo $this->Paginator->sort('InvoiceVat.rate', __('TVA')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vats as $vat): ?>
                        <tr>
                            <td><?php echo $vat['InvoiceVat']['id']; ?></td>
                            <td><?php echo $vat['Country']['name']; ?></td>
                            <td><?php echo $vat['Society']['name']; ?></td>
                            <td><?php echo $vat['InvoiceVat']['rate']; ?> %</td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'vat', 'action' => 'edit', 'admin' => true, 'id' => $vat['InvoiceVat']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
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
<?php
    echo $this->Metronic->titlePage(__('Bon de réduction'), 'Test');
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => 'Retour index',
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo 'Page à supprimer'; ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Voucher', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>'
                )));

                echo $this->Form->input('code', array('label' => array('text' => __('Code'), 'class' => 'control-label required')));

                echo $this->Form->end(array(
                    'label' => __('Utiliser'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>
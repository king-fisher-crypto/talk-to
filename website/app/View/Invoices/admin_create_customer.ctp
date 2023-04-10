<?php
    echo $this->Metronic->titlePage(__('Facture'),__('Création d\'un client'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un client'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'invoices', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un client'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('InvoiceCustomer', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span12">
                       <?php
                            //Les inputs du formulaire
                           $conf = array(
                                'name'   => array(
										'label' => array('text' => __('Nom'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
									),
							   'customer'   => array(
										'label' => array('text' => __('Personne'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
									),
							    'address'   => array(
										'label' => array('text' => __('Adresse'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true, 'type' => 'textarea'
									),
							   'phone'   => array(
										'label' => array('text' => __('Tél.'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
									),
							   'mail'   => array(
										'label' => array('text' => __('Email'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'required' => true,
									),
							   'info'   => array(
										'label' => array('text' => __('Information annexe (SIRET / TVA NUM)'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,
									),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>

            <?php
                echo $this->Form->end(array(
                    'label' => __('Créer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>
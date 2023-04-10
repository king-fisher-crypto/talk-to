<?php
    echo $this->Metronic->titlePage(__('TVA'),__('Création'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter une TVA'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'vat', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'une TVA'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('InvoiceVat', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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
								'country_id'   => array(
									'label' => array('text' => __('Pays'), 'class' => 'col-sm-12 col-md-4 control-label required'),
									'options' => $select_countries,
									'required' => true,
								),
								'society_type_id'   => array(
									'label' => array('text' => __('Société'), 'class' => 'col-sm-12 col-md-4 control-label required'),
									'options' => $select_societies,
									'required' => true,
								),
								
								'rate'             => array('label' => array('text' => __('Taux TVA'), 'class' => 'control-label required'), 'required' => true),
								'description'             => array('label' => array('text' => __('Text facture'), 'class' => 'control-label'), 'required' => false),
								'show_vat_num'   => array(
									'label' => array('text' => __('Afficher TVA Num sur facture ?'), 'class' => 'col-sm-12 col-md-4 control-label required'),
									'options' => array(0 => 'Non', 1 => 'Oui'),
									'required' => false,
								),
								'show_siret'   => array(
									'label' => array('text' => __('Afficher SIRET sur facture ?'), 'class' => 'col-sm-12 col-md-4 control-label required'),
									'options' => array(0 => 'Non', 1 => 'Oui'),
									'required' => false,
								)
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
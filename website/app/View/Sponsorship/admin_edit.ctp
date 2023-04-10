<?php
    echo $this->Metronic->titlePage(__('Parrainage'),__('Création d\'une regle'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Modifier une regle'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   <?php
                echo $this->Form->create('SponsorshipRule', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                                'type_user'        => array('label' => array('text' => __('Pour qui'), 'class' => 'control-label required'), 'required' => true),
                                'palier'             => array('label' => array('text' => __('Palier'), 'class' => 'control-label required'), 'required' => true),
								 'palier_type'             => array('label' => array('text' => __('Palier type'), 'class' => 'control-label required'), 'required' => true),
								'data'             => array('label' => array('text' => __('Action'), 'class' => 'control-label required'), 'required' => true),
								 'data_type'             => array('label' => array('text' => __('Action type'), 'class' => 'control-label required'), 'required' => true),
								 'declenche'             => array('label' => array('text' => __('Declenche'), 'class' => 'control-label required'), 'required' => true),
								 'palier_declenche'             => array('label' => array('text' => __('Palier declenche'), 'class' => 'control-label required'), 'required' => true),
								 'palier_declenche_type'             => array('label' => array('text' => __('Palier declenche type'), 'class' => 'control-label required'), 'required' => true),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>
<div class="row-fluid">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat save_cost',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>
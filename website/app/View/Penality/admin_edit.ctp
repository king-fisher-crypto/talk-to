<?php
    echo $this->Metronic->titlePage(__('Penalité'),__('Modification d\'une regle'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Modifier une regle'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'penality', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   <?php
                echo $this->Form->create('Penality', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                               'type'        => array('label' => array('text' => __('Pour quoi'), 'class' => 'control-label required'), 'required' => true),
                                'delay_min'             => array('label' => array('text' => __('Delai min.'), 'class' => 'control-label required'), 'required' => true),
								 'delay_max'             => array('label' => array('text' => __('Delai max.'), 'class' => 'control-label required'), 'required' => true),
								'cost'             => array('label' => array('text' => __('Cout'), 'class' => 'control-label required'), 'required' => true),
								 'active'             => array('label' => array('text' => __('Actif'), 'class' => 'control-label required'), 'required' => true),
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
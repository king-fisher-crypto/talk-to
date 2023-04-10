<?php
    echo $this->Metronic->titlePage(__('Penalité'),__('Création d\'une regle'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un regle'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'penality', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'une regle'); ?>
            </div>
        </div>
        <div class="portlet-body form">
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
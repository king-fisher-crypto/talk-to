<?php
    echo $this->Metronic->titlePage(__('Redirect'),__('Création d\'une redirection'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter une redirection'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'redirect', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'une redirection'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Redirect', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                                'type'              => array('label' => array('text' => __('Type'), 'class' => 'control-label required'), 'required' => true, 'value' => 301),
								'domain_id'              => array('label' => array('text' => __('Domain'), 'class' => 'control-label required'), 'required' => true,  'options' => array(19 => 'fr',11=> 'be',13 => 'ch',22=> 'lu', 29 => 'ca')),
                                'old'             => array('label' => array('text' => __('Ancienne URL'), 'class' => 'control-label required'), 'required' => true),
								'new'             => array('label' => array('text' => __('Nouvelle URL'), 'class' => 'control-label required'), 'required' => true),
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
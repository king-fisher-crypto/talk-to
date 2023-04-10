<?php
    echo $this->Metronic->titlePage(__('Bonus'),__('Création d\'un bonus'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un bonus'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'bonus', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un bonus'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Bonus', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                                'name'              => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
                                'bearing'             => array('label' => array('text' => __('Palier'), 'class' => 'control-label required'), 'required' => true),
								'amount'             => array('label' => array('text' => __('Montant'), 'class' => 'control-label required'), 'required' => true),
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
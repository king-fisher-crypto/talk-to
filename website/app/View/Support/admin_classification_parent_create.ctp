<?php
    echo $this->Metronic->titlePage(__('Classification'),__('Création d\'une classification'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter une classification'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'classification_create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'une classification'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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
							$list_index = array();
							for($nn=1;$nn<=100;$nn++){
								$list_index[$nn] = $nn;
							}
                           $conf = array(
							   'num'   => array(
										'label' => array('text' => __('Numero index'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => true,'type'=>'select','options' => $list_index
									),
							   'name'   => array(
										'label' => array('text' => __('Titre'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => true
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
                    'div' => array('class' => 'controls submit_classification')
                ));
            ?>
        </div>
    </div>
</div>
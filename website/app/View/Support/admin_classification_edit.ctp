<?php
    echo $this->Metronic->titlePage(__('Classification'),__('Modification d\'une classification'));
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
                <?php echo __('Modification d\'une classification'); ?>
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
                           $conf = array(
							    'parent_id'   => array(
										'label' => array('text' => __('Parent'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'type'=>'select','options' => $list_classification_parent,'value' => $classification['SupportClassification']['parent_id']
									),
							   'num'   => array(
										'label' => array('text' => __('Numero index'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => true,'placeholder'=>' ex : 1.1.1','value' => $classification['SupportClassification']['num']
									),
							   'name'   => array(
										'label' => array('text' => __('Titre'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => true,'value' => $classification['SupportClassification']['name']
									),
							   'description'   => array(
										'label' => array('text' => __('Information'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'type'=>'textarea','value' => $classification['SupportClassification']['description']
									),
							   'solution_link'   => array(
										'label' => array('text' => __('URL solution ( mode emploi )'), 'class' => 'col-sm-12 col-md-4 control-label'),
										'required' => false,'value' => $classification['SupportClassification']['solution_link']
									),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>

            <?php
                echo $this->Form->end(array(
                    'label' => __('Modifier'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls submit_classification')
                ));
            ?>
        </div>
    </div>
</div>
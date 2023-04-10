<?php
    echo $this->Metronic->titlePage(__('Remboursement'),__('Création'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Rembourser'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Remboursement'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Refund', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                                 'client'             => array('label' => array('text' => __('CLient'), 'class' => 'control-label required'), 'required' => true, 'placeholder'=>'Code client ou email'),
                                'price'             => array('label' => array('text' => __('Montant'), 'class' => 'control-label'), 'required' => false),
								 'credits'             => array('label' => array('text' => __('Crédits'), 'class' => 'control-label required'), 'required' => true),
								'label'             => array('label' => array('text' => __('Libéllé'), 'class' => 'control-label required'), 'required' => true),
								 'type_com'             => array('label' => array('text' => __('Lié à'), 'class' => 'control-label'), 'options' => array(1=>'Rien',2=>'Telephone',3=> 'Tchat',4=> 'Mail'), 'required' => true),
								 'id_com'             => array('label' => array('text' => __('ID communication'), 'class' => 'control-label'), 'required' => false),
								 'commentaire'             => array('label' => array('text' => __('Commentaire visible dans le Mail client'), 'class' => 'control-label '), 'required' => false, 'type' => 'textarea'),
								 
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
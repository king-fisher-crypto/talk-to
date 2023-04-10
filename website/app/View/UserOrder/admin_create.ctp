<?php
    echo $this->Metronic->titlePage(__('Facturation'),__('Création'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter une ligne'),
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
                <?php echo __('Facturation'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('UserOrder', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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

                                'user_id'             => array('label' => array('text' => __('Agent'), 'class' => 'control-label required'), 'required' => true, 'options'=> $list_agents),
                                'type'             => array('label' => array('text' => __('Type'), 'class' => 'control-label required'), 'required' => true, 'options' => array(2=>'Pénalité',3=>'Remboursement',1=>'Autre')),
							   //	'date_add'             => array('label' => array('text' => __('Date'), 'class' => 'control-label required'), 'required' => true, 'value'=> date('Y-m-d H:i:s')),
								 'amount'             => array('label' => array('text' => __('Montant'), 'class' => 'control-label required'), 'required' => true, 'placeholder'=> 'ex : 15 ou -50'),
								'label'             => array('label' => array('text' => __('Libéllé facture'), 'class' => 'control-label required'), 'required' => true),
								 'type_com'             => array('label' => array('text' => __('Lié à'), 'class' => 'control-label'), 'options' => array(1=>'Rien',2=>'Telephone',3=> 'Tchat',4=> 'Mail'), 'required' => true),
								 'id_com'             => array('label' => array('text' => __('ID communication'), 'class' => 'control-label'), 'required' => false),
								 'commentaire'             => array('label' => array('text' => __('Commentaire'), 'class' => 'control-label required'), 'required' => false),
								 
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
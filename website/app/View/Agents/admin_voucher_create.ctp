<?php
    echo $this->Metronic->titlePage(__('Avoir'),__('Création'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un avoir'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'voucher_create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un Avoir'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Agents', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
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
							
							if($select_invoice){
								 $conf = array(
									'invoice_id'   => array(
										'label' => array('text' => __('Factures'), 'class' => 'col-sm-12 col-md-4 control-label required'),
										'options' => $select_invoice,
										'required' => true,
									),
									

								);
							}else{
								 $conf = array(
									'agent_number'             => array('label' => array('text' => __('Renseigner un numéro Expert'), 'class' => 'control-label required'), 'required' => true),

								);
							}
                           
                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                
            </div>

            <?php
				if($select_invoice){
					echo $this->Form->end(array(
						'label' => __('Créer'),
						'class' => 'btn blue',
						'div' => array('class' => 'controls')
					));
				}else{
					echo $this->Form->end(array(
						'label' => __('Valider'),
						'class' => 'btn blue',
						'div' => array('class' => 'controls')
					));
				}
            ?>
        </div>
    </div>
</div>
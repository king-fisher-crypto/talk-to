<?php
    echo $this->Metronic->titlePage(__('Fidélité'),__('Création d\'un programme fidélité'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
       1 => array(
            'text' => __('Ajouter un programme fidélité'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'loyalty', 'action' => 'edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   <?php
                echo $this->Form->create('Loyalty', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span4">
                       <?php
                            //Les inputs du formulaire
                            $conf = array(

                                'name'              => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
                                'pourcent'             => array('label' => array('text' => __('Pourcentage'), 'class' => 'control-label required'), 'required' => true),
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                <div class="span6">
                    <p><?php echo __('Sélectionner le produit'); ?></p>
                    <div class="row-fluid">
                        <div class="span12">
                            <?php
							$product_ids = array();
							if(isset($edit) && $edit)
							 $product_ids = array($this->request->data['Loyalty']['product_id']);
                        if (!empty($products)): ?>
                            <div id="list-of-products" style="display:block; clear:both; background-color:#EEE; padding:10px; max-height:200px; overflow:auto">

                                <?php
                              
                                echo '<div class="lop-check" >';

                                $i=0;
                                foreach($products as $id => $name):
                                    echo $this->Form->input('product.'.$id, array(
                                        'label' => array('text' => $name, 'class' => 'lbl-inline'),'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false,
                                        'checked' => is_array($product_ids)?in_array($id, array_values($product_ids))?true:false:false
                                    ));
                                endforeach;

                                echo '</div>';


                                ?>
                                <div style="clear:both"></div>
                            </div>
                        <?php
                        endif;
                        ?>
                        </div>
                    </div>
                </div>
            </div>
<div class="row-fluid">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat save_loyalty',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>
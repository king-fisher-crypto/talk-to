<?php
    echo $this->Metronic->titlePage(__('Gift'),__('Création d\'un bon'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un bon'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'gifts', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Form->create('Gift', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'class' => 'span12',
                                                     'after' => '</div>'
                                                 ))); ?>

    <div class="row-fluid">
       
        <div class="span8 block_edit_admin panel-contenu">
            <div class="row-fluid">
                <div class="span4">
                    <?php
                        echo $this->Metronic->inputActive('Gift', 1);
                        //Les inputs du formulaire
                        echo $this->Form->inputs(array(
                            'id'                => array('type' => 'hidden', 'value' => $giftDatas['id']),
							 'name'          => array('label' => array('text' => __('Nom'), 'class' =>  'control-label required'), 'required' => true, 'value' => $giftDatas['name'], 'after' => '</div>'),
                            'amount'          => array('label' => array('text' => __('Montant'), 'class' =>  'control-label required'), 'required' => true, 'value' => $giftDatas['amount'], 'after' => '</div>'),
							
                           // 'voucher_buyer'          => array('label' => array('text' => __('Declenchement bon reduction acheteur'), 'class' =>  'control-label required'), 'required' => true, 'value' => $giftDatas['voucher_buyer'], 'after' => '<p>'.__('0 a l achat / 1 a l utilisation').'</p></div>'),
							// 'voucher_credit'          => array('label' => array('text' => __('Nb Crédit bon de reduction'), 'class' =>  'control-label required'), 'required' => true, 'value' => $giftDatas['voucher_credit'], 'after' => '</div>'),
                            
                        ));
                    ?>
                </div>
                <div class="span8">
                    <p><?php echo __('Sélectionner les domaines où le bon sera visible'); ?></p>
                    <div class="row-fluid">
                        <div class="span6">
                            <?php
                                echo $this->Form->input('alldomain', array('label' => array('text' => __('Tous'), 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false));
                                $i=0;
                                foreach($domain_select as $id => $name):
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false, 'checked' => (in_array($id,$slideDatas['domain']) ?true:false)));
                                    unset($domain_select[$id]);
                                    $i++;
                                    if($i == $half)
                                        break;
                                endforeach;
                            ?>
                        </div>
                        <div class="span6">
                            <?php
                                foreach($domain_select as $id => $name):
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false, 'checked' => (in_array($id,$giftDatas['domain']) ?true:false)));
                                endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue rfloat',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>
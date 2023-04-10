<?php
    echo $this->Html->script('/theme/default/js/admin_slide', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Slide'),__('Création d\'un slide'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un slide'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'slideprices', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un slide'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Slideprice', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span4">
                    <?php
                        echo $this->Metronic->inputActive('Slideprice', 1);
                        //Les inputs du formulaire
                        echo $this->Form->inputs(array(
                            'position'          => array('label' => array('text' => __('Position du slide'), 'class' =>  'control-label required'), 'required' => true, 'value' => 1, 'after' => '<p>'.__('Plus le nombre est petit plus il sera en tête de liste').'</p></div>'),
                            'validity_start'    => array(
                                'label'         => array('text' => __('Début de validité'), 'class' => 'control-label required'),
                                'required'      => true,
                                'type'          => 'text',
                                'maxlength'     => 16,
                                'placeholder'   => __('JJ-MM-AAAA HH:II'),
                                'value'         => $this->Time->format('now', '%d-%m-%Y %R')
                            ),
                            'validity_end'      => array(
                                'label'         => array('text' => __('Fin de validité'), 'class' => 'control-label'),
                                'type'          => 'text',
                                'maxlength'     => 16,
                                'placeholder'   => __('JJ-MM-AAAA HH:II'),
                                'after'         => '<p>'.__('Si le champ n\'est pas renseigné, alors durée illimitée').'</p></div>'
                            )
                        ));
                    ?>
                </div>
                <div class="span6">
                    <p><?php echo __('Sélectionner les domaines où le slide sera visible'); ?></p>
                    <div class="row-fluid">
                        <div class="span6">
                            <?php
                            
                                echo $this->Form->input('alldomain', array('label' => array('text' => __('Tous'), 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false));
                                $i=0;
                                foreach($domain_select as $id => $name):
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false));
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
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false));
                                endforeach;
                            ?>
                        </div>
                    </div>
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
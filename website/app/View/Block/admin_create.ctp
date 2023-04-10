<?php
    echo $this->Metronic->titlePage(__('Block'),__('Création d\'un block'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Ajouter un block'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'block', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Ajout d\'un block'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Block', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span4">
                    <?php
                        echo $this->Metronic->inputActive('Block', 1);
                    ?>
                </div>
                <div class="span6">
                    <p><?php echo __('Sélectionner les domaines où le block sera visible'); ?></p>
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
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
    <?php echo $this->Form->create('Block', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'class' => 'span12',
                                                     'after' => '</div>'
                                                 ))); ?>

    <div class="row-fluid">
        <div class="span4">
            <div class="tabbable tabbable-custom tabbable-full-width">
                <ul class="nav nav-tabs">
                    <?php $i=0; foreach($langs as $id => $val):
                        echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                        $i = 1;
                    endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?php $i=0; foreach($langs as $id => $val): ?>
                        <div id="tab<?php echo $id; ?>" class="tab-pane<?php echo ($i==0 ?' active':''); ?>">
                            <?php echo $this->Metronic->formBlockLang($blockDatas['id'],$id,(isset($langDatas[$id])?$langDatas[$id]:array())); ?>
                        </div>
                        <?php $i = 1; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="span8 block_edit_admin panel-contenu">
            <div class="row-fluid">
                <div class="span4">
                    <?php
                        echo $this->Metronic->inputActive('Block', 1);
                       //Les inputs du formulaire
                        echo $this->Form->inputs(array(
                            'id'                => array('type' => 'hidden', 'value' => $blockDatas['id'])
                        ));
                    ?>
                </div>
                <div class="span8">
                    <p><?php echo __('Sélectionner les domaines où le block sera visible'); ?></p>
                    <div class="row-fluid">
                        <div class="span6">
                            <?php
                                echo $this->Form->input('alldomain', array('label' => array('text' => __('Tous'), 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false));
                                $i=0;
                                foreach($domain_select as $id => $name):
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false, 'checked' => (in_array($id,$blockDatas['domain']) ?true:false)));
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
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false, 'checked' => (in_array($id,$blockDatas['domain']) ?true:false)));
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
            'class' => 'btn blue rfloat save_slide',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>
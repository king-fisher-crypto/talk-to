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
            'link' => $this->Html->url(array('controller' => 'slidemobiles', 'action' => 'create', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Form->create('Slidemobile', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
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
                            <?php echo $this->Metronic->formSlidemobileLang($slideDatas['id'],$id,(isset($langDatas[$id])?$langDatas[$id]:array())); ?>
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
                        echo $this->Metronic->inputActive('Slidemobile', 1);
                        //Les inputs du formulaire
                        echo $this->Form->inputs(array(
                            'id'                => array('type' => 'hidden', 'value' => $slideDatas['id']),
                            'position'          => array('label' => array('text' => __('Position du slide'), 'class' =>  'control-label required'), 'required' => true, 'value' => $slideDatas['position'], 'after' => '<p>'.__('Plus le nombre est petit plus il sera en tête de liste').'</p></div>'),
                            'validity_start'    => array(
                                'label'         => array('text' => __('Début de validité'), 'class' => 'control-label required'),
                                'required'      => true,
                                'type'          => 'text',
                                'maxlength'     => 16,
                                'placeholder'   => __('JJ-MM-AAAA HH:II'),
                                'value'         => $this->Time->format($slideDatas['validity_start'],'%d-%m-%Y %H:%M')
                            ),
                            'validity_end'      => array(
                                'label'         => array('text' => __('Fin de validité'), 'class' => 'control-label'),
                                'type'          => 'text',
                                'maxlength'     => 16,
                                'placeholder'   => __('JJ-MM-AAAA HH:II'),
                                'after'         => '<p>'.__('Si le champ n\'est pas renseigné, alors durée illimitée').'</p></div>',
                                'value'         => (empty($slideDatas['validity_end'])
                                        ?false
                                        :$this->Time->format($slideDatas['validity_end'],'%d-%m-%Y %H:%M')
                                    )
                            )
                        ));
                    ?>
                </div>
                <div class="span8">
                    <p><?php echo __('Sélectionner les domaines où le slide sera visible'); ?></p>
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
                                    echo $this->Form->input('domain.'.$id, array('label' => array('text' => $name, 'class' => 'lbl-inline'), 'type' => 'checkbox', 'between' => false, 'after' => false, 'hiddenField' => false, 'checked' => (in_array($id,$slideDatas['domain']) ?true:false)));
                                endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 block_duplicate_admin panel-contenu">
            <?php
            echo $this->Metronic->formDuplicateLangs('Slidemobile', $this->Session->read('Config.id_lang'));
            ?>
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
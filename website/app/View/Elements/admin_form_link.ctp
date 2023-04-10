<div class="row-fluid">
    <div class="span9">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <ul class="nav nav-tabs">
                <?php $i=0; foreach($langs as $id => $val):
                    echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                    $i = 1;
                endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php $i=0; foreach($langs as $id => $val): ?>
                    <div id="tab<?php echo $id; ?>" lang="<?php echo $id; ?>" class="tab-pane tab-link<?php echo ($i==0 ?' active':''); ?>">
                        <?php
                            if($idLink == 0)
                                echo $this->Form->inputs(array(
                                    'MenuLinkLang.'.$id.'.lang_id'  => array('type' => 'hidden', 'value' => $id),
                                    'MenuLinkLang.'.$id.'.title'    => array('label' => array('text' => __('Titre'), 'class' => 'control-label required'), 'class' => 'input-xlarge'),
                                    'MenuLinkLang.'.$id.'.link'     => array('label' => array('text' => __('Url'), 'class' => 'control-label required'), 'class' => 'input-xlarge', 'type' => 'url')
                                ));
                            else
                                echo $this->Form->inputs(array(
                                    'MenuLinkLang.'.$id.'.lang_id'  => array('type' => 'hidden', 'value' => $id),
                                    'MenuLinkLang.'.$id.'.title'    => array(
                                        'label' => array('text' => __('Titre'), 'class' => 'control-label required'),
                                        'class' => 'input-xlarge',
                                        'value' => (isset($data[$id]) ?$data[$id]['title']:'')
                                    ),
                                    'MenuLinkLang.'.$id.'.link'     => array(
                                        'label' => array('text' => __('Url'), 'class' => 'control-label required'),
                                        'class' => 'input-xlarge',
                                        'value' => (isset($data[$id]) ?$data[$id]['link']:'')
                                    )
                                ));
                        ?>
                    </div>
                    <?php $i = 1; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="span3 link_edit panel-contenu">
        <?php
            if($idLink != 0)
                echo $this->Form->input('MenuLink.id', array('type' => 'hidden', 'value' => $idLink));
            echo $this->Metronic->inputCheckbox('MenuLink', 'Target', __('Nouvel onglet'), 'target', ($idLink == 0 ?0:$data['MenuLink']['target_blank']));
        ?>
    </div>
</div>
<div class="row-fluid">
    <?php
        if($idLink == 0)
            echo $this->Metronic->getLinkButton(
                __('Créer'),
                array('controller' => 'menus', 'action' => 'create_link', 'admin' => true),
                'btn blue pull-right create_link',
                ''
            );
        else
            echo $this->Metronic->getLinkButton(
                __('Enregistrer'),
                array('controller' => 'menus', 'action' => 'edit_link', 'admin' => true),
                'btn blue pull-right edit_link',
                ''
            );
    ?>
</div>
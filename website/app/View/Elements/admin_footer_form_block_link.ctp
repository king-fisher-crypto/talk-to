<div class="row-fluid">
    <div class="span12">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <ul class="nav nav-tabs">
                <?php $i=0; foreach($langs as $id => $val):
                    echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                    $i = 1;
                endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php $i=0; foreach($langs as $id => $val): ?>
                    <div id="tab<?php echo $id; ?>" lang="<?php echo $id; ?>" class="tab-pane tab-block<?php echo ($i==0 ?' active':''); ?>">
                        <?php
                            if($idLink == 0)
                                echo $this->Form->inputs(array(
                                    'FooterBlockLang.'.$id.'.lang_id'  => array('type' => 'hidden', 'value' => $id),
                                    'FooterBlockLang.'.$id.'.title'    => array('label' => array('text' => __('Titre'), 'class' => 'control-label required'), 'class' => 'input-xlarge')
                                ));
                            else
                                echo $this->Form->inputs(array(
                                    'FooterBlockLang.'.$id.'.lang_id'  => array('type' => 'hidden', 'value' => $id),
                                    'FooterBlockLang.'.$id.'.title'    => array(
                                        'label' => array('text' => __('Titre'), 'class' => 'control-label required'),
                                        'class' => 'input-xlarge',
                                        'value' => (isset($data[$id]) ?$data[$id]['title']:'')
                                    )
                                ));
                        ?>
                    </div>
                    <?php $i = 1; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
        if($idLink != 0)
            echo $this->Form->input('FooterBlockLang.footer_block_id', array('type' => 'hidden', 'value' => $idLink));
    ?>
</div>
<div class="row-fluid">
    <?php
        if($idLink == 0)
            echo $this->Metronic->getLinkButton(
                __('Créer'),
                array('controller' => 'footers', 'action' => 'create_block_link', 'admin' => true),
                'btn blue pull-right create_block_link',
                ''
            );
        else
            echo $this->Metronic->getLinkButton(
                __('Enregistrer'),
                array('controller' => 'footers', 'action' => 'edit_block_link', 'admin' => true),
                'btn blue pull-right edit_block_link',
                ''
            );
    ?>
</div>
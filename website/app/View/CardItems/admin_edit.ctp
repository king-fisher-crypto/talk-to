<?php

    echo $this->Metronic->titlePage(__('Pages'),__('Edition de').' '.$namePage);
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Item'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'cardItems', 'action' => 'list', 'admin' => true))
        )
    ));

echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Form->create('CardItem', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class'    => 'span12',
            'after' => '</div>'
        ))); ?>
    <div class="row-fluid">
        <div class="span12">
                            <?php echo $this->Metronic->formItemCard($idPage,(isset($pages)?$pages:array()), $page_parameters,$card_options); ?>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php
    echo $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue save_page',
        'div' => array('class' => 'controls')
    ));
    ?>
</div>



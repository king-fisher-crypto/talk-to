<?php
    if(isset($data))
        $options = $data;

    echo $this->Form->input('block_link', array(
        'label'     => array('text' => __('Bloc de lien :'), 'class' => 'lbl-inline'),
        'div'       => false,
        'between'   => false,
        'empty'     => __('Sélectionner un bloc de lien'),
        'class'     => 'margin-left',
        'after'     => false,
        'id'        => 'select_block_link',
        'update'    => $this->Html->url(array('controller' => 'footers', 'action' => 'update', 'admin' => true)),
        'options'   => $options
    ));
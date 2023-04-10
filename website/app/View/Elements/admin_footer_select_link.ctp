<?php
    $options = array();
    foreach($data as $row){
        $options[$row['FooterLink']['id']] = $row['FooterLinkLang']['title'];
    }

    echo $this->Form->input('link', array(
        'label'     => array('text' => __('Lien :'), 'class' => 'lbl-inline'),
        'div'       => false,
        'between'   => false,
        'empty'     => __('Sélectionner un lien'),
        'class'     => 'margin-left',
        'after'     => false,
        'id'        => 'select_link',
        'update'    => $this->Html->url(array('controller' => 'footers', 'action' => 'update', 'admin' => true)),
        'options'   => $options
    ));
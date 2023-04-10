<?php
    echo $this->Metronic->titlePage(__('Agents'),__('Validation des présentations audio'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user-md',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Présentations audio en attente'),
            'classes' => 'icon-music',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_audio', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    if(empty($rows))
        echo __('Aucune présentation audio en attente.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('pseudo' => $this->Paginator->sort('User.pseudo', __('Agent')), 'presentation_validation' => __('Présentation audio en attente'), 'presentation_actuelle' => __('Présentation audio actuelle')),
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Accepter'),
                    array('controller' => 'agents','action' => 'accept_valid_audio', 'admin' => true, 'id' => $row['id']),
                    'btn green',
                    'icon-check',
                    __('Voulez-vous vraiment accepter la présentation audio ? L\'agent en sera informé par mail.')).' '.
                $caller->Metronic->getLinkButton(
                    __('Refuser'),
                    array('controller' => 'agents','action' => 'refuse_valid_audio', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this);

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }
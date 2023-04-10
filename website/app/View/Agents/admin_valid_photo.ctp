<?php
    echo $this->Metronic->titlePage(__('Agents'),__('Validation des photos'));
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
            'text' => __('Photos en attente'),
            'classes' => 'icon-camera',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_photo', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    if(empty($rows))
        echo __('Aucune photo en attente.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('pseudo' => $this->Paginator->sort('User.pseudo', __('Agent')), 'photo_validation' => __('Photo en attente'), 'photo_actuelle' => __('Photo actuelle')),
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Accepter'),
                    array('controller' => 'agents','action' => 'accept_valid_photo', 'admin' => true, 'id' => $row['id']),
                    'btn green',
                    'icon-check',
                    __('Voulez-vous vraiment accepter la photo ? L\'agent en sera informé par mail.')).' '.
                $caller->Metronic->getLinkButton(
                    __('Refuser'),
                    array('controller' => 'agents','action' => 'refuse_valid_photo', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this);

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }
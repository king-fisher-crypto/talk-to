<?php
echo $this->Metronic->titlePage(__('Agents'),__('Information Email'));
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
        'text' => __('Informations en attente'),
        'classes' => 'icon-quote-right',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_mailinfos', 'admin' => true))
    )
));

echo $this->Session->flash();

    if(empty($rows))
        echo __('Aucune informations en attente.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('pseudo' => $this->Paginator->sort('User.pseudo', __('Pseudo')), 'texte_actuelle' => $this->Paginator->sort('User.mail_infos_v', __('Infos actuelles')),
                                                         'texte_validation' => $this->Paginator->sort('User.mail_infos', __('Infos en attentes'))),
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Accepter'),
                    array('controller' => 'agents','action' => 'accept_valid_mailinfos', 'admin' => true, 'id' => $row['id']),
                    'btn green',
                    'icon-check',
                    __('Voulez-vous vraiment accepter la modification de la présentation ? L\'agent en sera informé par mail.')).' '.
                $caller->Metronic->getLinkButton(
                    __('Refuser'),
                    array('controller' => 'agents','action' => 'refuse_valid_mailinfos', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this, array( 'texte_validation' => array('','id'), 'texte_actuelle' => array('class="td_texte_actuelle"')), 'td-callback',
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Modifier'),
                    array('controller' => 'agents','action' => 'edit_valid_mailinfos', 'admin' => true, 'id' => $row['id']),
                    'btn blue nx_editmailinfos',
                    'icon-edit-sign'
                );
            },'texte_validation','td_edit_presentation',array($this->Paginator->sort('User.mail_infos', __('Infos en attentes')) => 2)
        );

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }
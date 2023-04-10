<?php
echo $this->Metronic->titlePage(__('Agents'),__('Présentations'));
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
        'text' => __('Présentations en attente'),
        'classes' => 'icon-quote-right',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_presentations', 'admin' => true))
    )
));

echo $this->Session->flash();

    if(empty($rows))
        echo __('Aucune présentation en attente.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('pseudo' => $this->Paginator->sort('User.pseudo', __('Pseudo')), 'langue' => $this->Paginator->sort('UserPresentValidation.lang_id', __('Langue')), 'texte_actuelle' => $this->Paginator->sort('UserPresentLang.texte', __('Présentation actuelle')),
                                                         'texte_validation' => $this->Paginator->sort('UserPresentValidation.texte', __('Présentation en attente')), 'date_add' => $this->Paginator->sort('UserPresentValidation.date_add', __('Date d\'ajout'))),
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Accepter'),
                    array('controller' => 'agents','action' => 'accept_valid_presentation', 'admin' => true, 'id' => $row['id']),
                    'btn green',
                    'icon-check',
                    __('Voulez-vous vraiment accepter la modification de la présentation ? L\'agent en sera informé par mail.')).' '.
                $caller->Metronic->getLinkButton(
                    __('Refuser'),
                    array('controller' => 'agents','action' => 'refuse_valid_presentation', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this, array('date_add' => array('class="td-width"'), 'texte_validation' => array('','id'), 'texte_actuelle' => array('class="td_texte_actuelle"')), 'td-callback',
            function($row, $caller){
                return $caller->Metronic->getLinkButton(
                    __('Modifier'),
                    array('controller' => 'agents','action' => 'edit_valid_presentation', 'admin' => true, 'id' => $row['id']),
                    'btn blue nx_editpresentation',
                    'icon-edit-sign'
                );
            },'texte_validation','td_edit_presentation',array($this->Paginator->sort('UserPresentValidation.texte', __('Présentation en attente')) => 2)
        );

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }
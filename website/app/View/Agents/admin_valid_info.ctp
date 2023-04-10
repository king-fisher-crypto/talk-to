<?php
echo $this->Metronic->titlePage(__('Agents'),__('Validation des données'));
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
        'text' => __('Données en attente'),
        'classes' => 'icon-hdd',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true))
    )
));

echo $this->Session->flash();

$this->user_level=$user_level;
    if(empty($rows))
        echo __('Aucune donnée en attente.');
    else{
        echo $this->Metronic->getSimpleTable($rows,array('fullname' => $this->Paginator->sort('UserValidation.firstname', __('Nom complet')), 'pseudo' => $this->Paginator->sort('UserValidation.pseudo', __('Pseudo')), 'country_id' => $this->Paginator->sort('UserValidation.country_id', __('Pays de résidence')),
                                                         'birthdate' => $this->Paginator->sort('UserValidation.birthdate', __('Date de naissance')), 'fulladdress' => $this->Paginator->sort('UserValidation.address', __('Adresse')), 'sexe' => $this->Paginator->sort('UserValidation.sexe', __('Sexe')),
                                                         'siret' => $this->Paginator->sort('UserValidation.siret', __('Siret')),'societe' => $this->Paginator->sort('UserValidation.societe', __('Societe')), 'rib' => $this->Paginator->sort('UserValidation.rib', __('RIB')), 'bank_name' => $this->Paginator->sort('UserValidation.bank_name', __('Nom banque')), 'bank_country' => $this->Paginator->sort('UserValidation.bank_country', __('Pays banque')), 'iban' => $this->Paginator->sort('UserValidation.iban', __('IBAN')), 'swift' => $this->Paginator->sort('UserValidation.swift', __('BIC / SWIFT')), 'date_add' => $this->Paginator->sort('UserValidation.date_add', __('Date d\'ajout')), 'phone_number' => $this->Paginator->sort('UserValidation.phone_number', __('Numéro de téléphone'))),
            function($row, $caller){
                if((!empty($this->user_level) && $this->user_level == 'moderator') && $row['UserVat'] != $row['vat_num']){
                    return false;
                }

                return $caller->Metronic->getLinkButton(
                    __('Comparer'),
                    array('controller' => 'agents','action' => 'valid_info_view', 'admin' => true, 'id' => $row['id']),
                    'btn blue',
                    'icon-zoom-in').' '.
                $caller->Metronic->getLinkButton(
                    __('Accepter'),
                    array('controller' => 'agents','action' => 'accept_valid_info', 'admin' => true, 'id' => $row['id']),
                    'btn green',
                    'icon-check',
                    __('Voulez-vous vraiment accepter la modification ? L\'agent en sera informé par mail.')).' '.
                $caller->Metronic->getLinkButton(
                    __('Refuser'),
                    array('controller' => 'agents','action' => 'refuse_valid_info', 'admin' => true, 'id' => $row['id']),
                    'btn red nx_refuselightbox',
                    'icon-remove');
            },$this);

        if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator);
    }

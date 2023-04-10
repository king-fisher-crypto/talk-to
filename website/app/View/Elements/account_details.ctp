<?php

echo $this->Form->inputs(array(
    'legend' => false,
    'phone_number' => array(
        'label'     => array('text' => __('Numéro de téléphone'), 'class' => 'control-label col-lg-4 norequired'),
        'before' => '<h3 class="tabs-heading">'. __('Informations complémentaires') .'</h3>',
        'type'      => 'tel',
        'between'    => '<br /><div class="col-xs-4 col-lg-3 col-md-3">'.$this->FrontBlock->getIndicatifTelInputIns().'</div><div class="col-xs-8 col-md-3 col-lg-3">',
        'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
    ),
    'address' => array('label' => array('text' => __('Adresse'), 'class' => 'control-label norequired col-lg-4'))
)); ?>

<div class="form-group">
    <?php
    echo $this->Form->input('postalcode', array(
        'label' => array('text' => __('Code postal'), 'class' => 'control-label col-lg-4 norequired'),
        'div' => false,
        'between' => '<div class="col-lg-2">'
    ));

    echo $this->Form->input('city', array(
        'label' => array('text' => __('Ville'), 'class' => 'control-label col-lg-1 norequired'),
        'div' => false,
        'between' => '<div class="col-lg-3">'
    ));
    ?>
</div> <?php

    echo $this->Form->input('country_id', array('label' => array('text' => __('Pays'), 'class' => 'control-label col-lg-4 required'), 'options' => $select_countries, 'required'  => true));

    echo $this->Form->input('optin', array('label' => __('Recevoir les offres exclusives de ').' '.Configure::read('Site.name'), 'type' => 'checkbox', 'class' => false, 'value' => 1, 'between' => false, 'after' => false, 'div' => array('class' => 'checkbox col-lg-offset-4')));

echo $this->Form->input('save_bank_card', array('label' => __('Je souhaite que mes coordonnées de paiement par carte soient enregistrées par Stripe, prestataire de paiement').' '.Configure::read('Site.name'), 'type' => 'checkbox', 'class' => false, 'value' => 1, 'between' => false, 'after' => false, 'div' => array('class' => 'checkbox col-lg-offset-4')));
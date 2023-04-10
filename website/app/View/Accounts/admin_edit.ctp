<?php
echo $this->Metronic->titlePage(__('Clients'),__('Edition d\'un client'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Clients'),
        'classes' => 'icon-user',
        'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true))
    ),
	 2 => array(
            'text' => (!isset($user['User']['firstname']) || empty($user['User']['firstname'])?__('Client'):$user['User']['firstname']),
            'classes' => 'icon-hdd',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
    3 => array(
        'text' => __('Editer ').(!isset($user['User']['firstname']) && empty($user['User']['firstname'])?__('client'):$user['User']['firstname']),
        'classes' => 'icon-edit-sign',
        'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'edit', 'admin' => true, 'id' => $user['User']['id']))
    )
));
echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Editer le client'); ?></div>
        </div>
        <div class="portlet-body form">
            <?php
            echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array('class' => 'span10')));
            echo '<h3 class="form-section">'.__('Informations du client').'</h3>';

            //Les inputs du formulaire
            $inputs = array(
                'firstname' => array('label' => array('text' => __('Prénom'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'lastname' => array('label' => array('text' => __('Nom'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'passwd2' => array('label' => array('text' => __('Confirmation mot de passe'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'password'),
                'email' => array('label' => array('text' => __('Email'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'address' => array('label' => array('text' => __('Adresse'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'postalcode' => array('label' => array('text' => __('Code postal'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'city' => array('label' => array('text' => __('Ville'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'phone_number' => array(
                    'label'     => array('text' => __('Numéro de téléphone'), 'class' => 'control-label'),
                    'placeholder' => 'Ex : 33XXXXXXXXX ou 0XXXXXXXXX',
                    'div' => 'control-group span4',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'type'      => 'tel',
                    'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                ),
                'country_id' => array('label' => array('text' => __('Pays de résidence'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_countries),
				
				'credit' => array('label' => array('text' => __('Crédit client'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
				'credit_old' => array('label' => array('text' => __('Crédit périmé'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
				'personal_code' => array('label' => array('text' => __('Code client'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
				'subscribe_mail' => array('label' => array('text' => __('Recevoir les emails'), 'class' => 'control-label'), 'div' => 'control-group span6', 'between' => '<div class="controls" style="margin-left:0px;display:inline-block;margin-bottom: -12px;">', 'after' => '</div>'),
            );//protege avec code admin level

            echo $this->Metronic->inputsAdminEdit($inputs);


            echo $this->Form->end(array(
                'label' => __('Enregistrer'),
                'class' => 'btn blue',
                'div' => array('class' => 'controls')
            ));
            ?>
        </div>
    </div>
</div>
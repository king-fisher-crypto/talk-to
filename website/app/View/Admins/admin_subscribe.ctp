<?php
    echo $this->Metronic->titlePage('Backoffice',__('Ajouter un administrateur'));
    echo $this->Metronic->breadCrumb(array(0 => array('text' => __('Accueil'), 'classes' => 'icon-home')));
?>

<div class="row-fluid">
   <div class="portlet box green span8 offset2">
       <div class="portlet-title">
           <div class="caption"><?php echo __('Nouvel administrateur'); ?></div>
       </div>
       <div class="portlet-body form">
            <?php
               echo $this->Form->create('User', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1));

               //Les inputs du formulaire
               $inputs = array(
                   'firstname' => array('label' => array('text' => __('Prénom'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'class' => 'span9'),
                   'lastname' => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'class' => 'span12'),
                   'email' => array('label' => array('text' => __('Email'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'class' => 'span9'),
                   'country_id' => array('label' => array('text' => __('Pays de résidence'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_countries, 'class' => 'span12'),
                   'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label required'), 'div' => 'control-group span5', 'required' => true, 'between' => '<div class="controls">', 'after' => '</div>', 'class' => 'span9'),
                   'passwd2' => array('label' => array('text' => __('Confirmation mot de passe'), 'class' => 'control-label required'), 'div' => 'control-group span4', 'required' => true, 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'password', 'class' => 'span12')
               );

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

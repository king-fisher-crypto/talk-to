<?php

if(!isset($inputs)){
    $inputs = array('nameEmail' => 'email', 'namePasswd' => 'passwd');
}

if(!isset($email)) $email = '';
if(!isset($before)) $before = false;

if(!isset($passwdRequis)) $passwdRequis = true;

$t1 = __('Email');
$t2 = __('Modifier votre e-mail');


$inputsForm = array(
    $inputs['nameEmail'] => array('label' => array(
        'text' =>   (isset($inscription))?$t1:$t2,
        'class' => 'control-label col-lg-4 required'), 'type' => 'email', 'required' => true, 'value' => $email, 'before' => $before),//, 'readonly' => true
    'email2' => array('label' => array('text' => __('Confirmez votre Email'), 'class' => 'control-label col-lg-4 required'), 'required' => true, 'type' => 'email'),
    $inputs['namePasswd'] => array('label' => array(
        'text' => isset($inscription)?__('Mot de passe'):__('Nouveau mot de passe'),
        'class' => 'control-label'. ($passwdRequis?' required':' norequired') .' col-lg-4'), 'required' => $passwdRequis, 'type' => 'password', 'after' => __('(8 caractères min.)').'</div>'),
    'passwd2' => array('label' => array(
        'text' => __('Confirmez votre mot de passe'),
        'class' => 'control-label'. ($passwdRequis?' required':' norequired') .' col-lg-4'), 'type' => 'password', 'required' => $passwdRequis, 'type' => 'password')
);

if(!isset($inscription)) unset($inputsForm['email2']);

echo $this->Form->inputs($inputsForm);

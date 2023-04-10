<?php

    if(!isset($inputs)){
        $inputs = array('nameEmail' => 'email', 'namePasswd' => 'passwd');
    }

    if(!isset($email)) $email = '';

    if(!isset($passwdRequis)) $passwdRequis = true;

    $inputsForm = array(
        $inputs['nameEmail'] => array('label' => array('text' => __((isset($inscription) ?'Email':'Modifier votre e-mail')).' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true, 'value' => $email, 'between' => '<div class="col-sm-12 col-md-8">','after'=>'</div>'),
        'email2' => array('label' => array('text' => __('Confirmer votre Email').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true, 'between' => '<div class="col-sm-12 col-md-8">','after'=>'</div>'),
        $inputs['namePasswd'] => array('label' => array('text' => __((isset($inscription) ?'Mot de passe':'Nouveau mot de passe')).' <span class="star-condition">*</span>', 'class' => 'control-label'. ($passwdRequis?' required':' norequired') .' col-sm-12 col-md-4'), 'required' => $passwdRequis, 'type' => 'password', 'between' => '<div class="col-sm-12 col-md-8">','after' => '<span class="help">'.__('(8 caractères min.)').'</span></div>'),
        
    );
//'passwd2' => array('label' => array('text' => __('Confirmez votre mot de passe').' <span class="star-condition">*</span>', 'class' => 'control-label'. ($passwdRequis?' required':' norequired') .' col-sm-12 col-md-4'), 'type' => 'password', 'required' => $passwdRequis, 'after' => '</div>')
    if(!isset($inscription)) unset($inputsForm['email2']);

    echo $this->Form->inputs($inputsForm);
?>
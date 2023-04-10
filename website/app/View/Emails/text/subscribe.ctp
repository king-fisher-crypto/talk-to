<?php


    $mail = $this->Frontblock->getMailBlock(151);
    $tplVars = array(
        '##SITE_NAME##'                 =>     Configure::read('Site.name'),
        '##PARAM_EMAIL##'               =>     $param['email'],
        '##PARAM_URLCONFIRMATION##'     =>     $param['urlConfirmation'],
        '##PARAM_URLSITE##'             =>     $urlSite
    );
    echo strip_tags(str_replace(array_keys($tplVars), array_values($tplVars), $mail));

    /*
    echo __('Bienvenue sur').' '.Configure::read('Site.name')."\n";
    echo __('Vous venez de vous inscrire sur').' '.Configure::read('Site.name')."\n";
    echo __('Votre identifiant est').' : '. $param['email']."\n";

    echo __('Pour confirmer votre inscription, veuillez cliquer sur le lien suivant').' :<a href="'. $param['urlConfirmation'] .'">'. $param['urlConfirmation'] .'</a>';

    echo __('A très vite sur').' '. $urlSite;
    */
    ?>
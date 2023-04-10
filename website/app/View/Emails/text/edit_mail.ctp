<?php
    echo Configure::read('Site.name');
    echo __('Bonjour,');
    echo __('Vous avez modifié votre adresse mail.');
    echo __('Pour confirmer votre nouvelle adresse mail, un simple clique sur le lien suivant suffit : ').'<a href="'. $param['urlConfirmation'] .'">'. $param['urlConfirmation'] .'</a>';

    echo __('Votre identifiant est').' : <b>'. $param['email'] .'</b>';

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
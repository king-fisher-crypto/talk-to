<?php
    echo Configure::read('Site.name')."\n";
    echo __('Bonjour').','."\n";
    echo __('Vous avez demandé à réinitialiser votre mot de passe.')."\n";
    echo __('Votre identifiant est').' : '. $param['email'] ."\n";

    echo __('Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant').' : <a href="'. $param['urlReinitialisation'] .'">'. $param['urlReinitialisation'] .'</a>'."\n";

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
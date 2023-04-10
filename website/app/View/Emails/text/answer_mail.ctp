<?php
    if(!isset($param['admin'])) :
        echo Configure::read('Site.name');
        echo __('Bonjour,');
        echo $param['pseudo'].' '.__('a répondu à un de vos messages.');
        echo __('La réponse est disponible dans votre messagerie.');
    else :
        echo Configure::read('Site.name');
        echo __('Bonjour,');
        echo __('Vous avez reçu un message de la part d\'un administrateur.');
        echo __('Le message est disponible dans votre messagerie.');
    endif;

    if(isset($param['urlMail'])):
        echo '<a href="'. $param['urlMail'] .'">'.__('Cliquez-ici pour consulter votre message').'</a>';
    endif;

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
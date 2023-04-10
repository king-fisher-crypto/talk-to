<?php
    echo '<h1>'.Configure::read('Site.name').'</h1>';
    echo '<p>'.__('Bonjour').' '.$param['name'].',</p>';
    echo '<p>'.__('Vous avez reçu un nouveau message.').'</p>';
    echo '<p>'.__('Le message est disponible dans votre messagerie.').'</p>';

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
<?php
    echo Configure::read('Site.name');
    echo __('Bonjour,');
    echo __('Un administrateur a répondu à votre message.');
    echo '"'.$param['content'].'"';

    if(isset($param['urlMail'])):
        echo '<a href="'. $param['urlMail'] .'">'.__('Cliquez-ici pour répondre à ce message').'</a>';
    endif;

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
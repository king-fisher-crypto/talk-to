<?php

    echo Configure::read('Site.name')."\n";
    echo __('Bonjour').', '."\n";
    echo __('L\'expert').' '.'&quote;'.$agent['pseudo'].'&quote; est disponible pour votre consultation !'."\n";

    $consult = __('Il est disponible pour une consultation par :');
    //Pour chaque media
    foreach($customer['Alert']['media'] as $media){
        $consult.= "\n".'- '.__($nameMedia[$media['name']]);
    }
    $consult.= "\n";

    echo $consult;

    echo __('Nous vous invitons à le consulter dès maintenant avant qu\'il ne soit occupé !')."\n";

    echo __('Pour cela, connectez-vous dès maintenant sur notre site !')."\n";

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>'."\n";

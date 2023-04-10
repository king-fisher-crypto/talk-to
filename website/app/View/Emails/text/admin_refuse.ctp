<?php
    echo Configure::read('Site.name')."\n";
    echo __('Bonjour').','."\n";
    echo __($data['content']).'.'."\n";
    if(!empty($data['motif']))
        echo __('Pour le motif suivant').' : '. $data['motif'] ."\n";

    echo __('Pour toutes réclamations, contactez').' : '. $data['emailAdmin'] ."\n";

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
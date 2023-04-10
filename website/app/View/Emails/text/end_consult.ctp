<?php
    echo Configure::read('Site.name');
    echo __('Bonjour,');

    echo __('Vous venez de communiquer avec').' '.$param['pseudo'].' '.__('pendant').' '.$param['timeCom'].__('min').'.';

    echo __('Laissez un avis sur cette communication et sur').' '.$param['pseudo'];

    echo __('Pour cela rendez-vous dans votre profil, dans la rubrique "Votre avis sur un expert"');

    echo __('Cliquez sur le lien suivant pour accéder à cette rubrique : ').' <a href="'.$param['linkReview'].'">'.$param['linkReview'].'</a>';

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
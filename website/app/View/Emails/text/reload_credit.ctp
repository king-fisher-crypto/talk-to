<?php
    echo Configure::read('Site.name');
    echo $param['pseudo'].' '.__('a tenté de répondre à une de vos questions.');
    echo __('Mais il n\'a pas pu, car vous n\'avez pas assez de crédit.');

    echo __('Pensez à recharger votre crédit, pour avoir une réponse dans les meilleurs délais.');

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
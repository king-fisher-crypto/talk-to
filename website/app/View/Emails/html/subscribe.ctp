<div>
    <?php


    $mail = $this->Frontblock->getMailBlock(151);
    $tplVars = array(
        '##SITE_NAME##'                 =>     Configure::read('Site.name'),
        '##PARAM_EMAIL##'               =>     $param['email'],
        '##PARAM_URLCONFIRMATION##'     =>     $param['urlConfirmation'],
        '##PARAM_URLSITE##'             =>     $urlSite

    );
    echo str_replace(array_keys($tplVars), array_values($tplVars), $mail);

    /*
    echo '<h1>'.__('Bienvenue sur').' '.Configure::read('Site.name').'</h1>';
    echo '<p>'.__('Vous venez de vous inscrire sur').' '.Configure::read('Site.name').'</p>';
    echo '<p>'.__('Votre identifiant est').' : <b>'. $param['email'] .'</b></p>';
    echo '<p>'.__('Pour confirmer votre inscription, veuillez cliquer sur le lien suivant').' : <a href="'. $param['urlConfirmation'] .'">'. $param['urlConfirmation'] .'</a></p>';
    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    */
    ?>
</div>
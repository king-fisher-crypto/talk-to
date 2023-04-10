<div>
    <?php
    echo '<h1>'.Configure::read('Site.name').'</h1>';
    echo '<p>'.__('Bonjour').',</p>';
    echo '<p>'.__('Vous avez demandé à réinitialiser votre mot de passe.').'</p>';
    echo '<p>'.__('Votre identifiant est').' : <b>'. $param['email'] .'</b></p>';

    echo '<p>'.__('Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant').' : <a href="'. $param['urlReinitialisation'] .'">'. $param['urlReinitialisation'] .'</a></p>';

    echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
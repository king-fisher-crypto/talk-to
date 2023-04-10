<div>
    <?php
        echo __('Bienvenue sur').' '.Configure::read('Site.name');
        echo __('Votre compte administrateur a été crée.').'</p>';
        echo __('Votre identifiant est').' : <b>'. $param['email'] .'</b>';
        echo __('Votre mot de passe est').' : <b>'. $param['passwd'] .'</b>';

        echo __('Il est vivement conseillé de modifier votre mot de passe lors de vote première connexion.').'</p>';
        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
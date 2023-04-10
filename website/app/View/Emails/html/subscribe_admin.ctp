<div>
    <?php
        echo '<h1>'.__('Bienvenue sur').' '.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Votre compte administrateur a été crée.').'</p>';
        echo '<p>'.__('Votre identifiant est').' : <b>'. $param['email'] .'</b></p>';
        echo '<p>'.__('Votre mot de passe est').' : <b>'. $param['passwd'] .'</b></p>';

        echo '<p>'.__('Il est vivement conseillé de modifier votre mot de passe lors de vote première connexion.').'</p>';
        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
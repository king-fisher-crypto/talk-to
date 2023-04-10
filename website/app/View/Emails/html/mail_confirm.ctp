<div>
    <?php
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour,').'</p>';
        echo '<p>'.__('Vous n\'avez toujours pas confirmé votre adresse mail').'</p>';
        echo '<p>'.__('Pour confirmer votre adresse mail, un simple clique sur le lien suivant suffit : ').'<a href="'. $param['urlConfirmation'] .'">'. $param['urlConfirmation'] .'</a></p>';

        echo '<p>'.__('Votre identifiant est').' : <b>'. $param['email'] .'</b></p>';

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
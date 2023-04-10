<div>
    <?php
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour,').'</p>';

        echo '<p>'.__('Vous venez de communiquer avec').' '.$param['pseudo'].' '.__('pendant').' '.$param['timeCom'].__('min').'.</p>';

        echo '<p>'.__('Laissez un avis sur cette communication et sur').' '.$param['pseudo'].'</p>';

        echo '<p>'.__('Pour cela rendez-vous dans votre profil, dans la rubrique "Votre avis sur un expert"').'</p>';

        echo '<p>'.__('Cliquez sur le lien suivant pour accéder à cette rubrique : ').' <a href="'.$param['linkReview'].'">'.$param['linkReview'].'</a></p>';

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
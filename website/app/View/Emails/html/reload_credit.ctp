<div>
    <?php
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.$param['pseudo'].' '.__('a tenté de répondre à une de vos questions.').'</p>';
        echo '<p>'.__('Mais il n\'a pas pu, car vous n\'avez pas assez de crédit.').'</p>';

        echo '<p>'.__('Pensez à recharger votre crédit, pour avoir une réponse dans les meilleurs délais.').'</p>';

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
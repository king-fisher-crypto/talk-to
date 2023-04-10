<div>
    <?php if(!isset($param['admin'])) :
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour,').'</p>';
        echo '<p>'.$param['pseudo'].' '.__('a répondu à un de vos messages.').'</p>';
        echo '<p>'.__('La réponse est disponible dans votre messagerie.').'</p>';
    else :
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour,').'</p>';
        echo '<p>'.__('Vous avez reçu un message de la part d\'un administrateur.').'</p>';
        echo '<p>'.__('Le message est disponible dans votre messagerie.').'</p>';
    endif;

        if(isset($param['urlMail'])):
            echo '<p><a href="'. $param['urlMail'] .'">'.__('Cliquez-ici pour consulter votre message').'</a></p>';
        endif;

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
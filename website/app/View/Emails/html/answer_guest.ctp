<div>
    <?php
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour,').'</p>';
        echo '<p>'.__('Un administrateur a répondu à votre message.').'</p>';
        echo '<p>"'.$param['content'].'"</p>';

        if(isset($param['urlMail'])):
            echo '<p><a href="'. $param['urlMail'] .'">'.__('Cliquez-ici pour répondre à ce message').'</a></p>';
        endif;

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
    ?>
</div>
<div>
    <?php
    echo '<h1>'.Configure::read('Site.name').'</h1>';
    echo '<p>'.__('Bonjour').',</p>';
    echo '<p>'.__($data['content']).'</p>';
    echo '<p>'.__('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a></p>';
    ?>
</div>
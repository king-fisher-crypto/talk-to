<?php
echo Configure::read('Site.name')."\n";
echo __('Bonjour').','."\n";
echo __($data['content'])."\n";
echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
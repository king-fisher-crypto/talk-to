<?php if (!isset($scene)){ 
        
    $scene = array(
         'src'     =>  '/theme/default/images/ad_tmp.png',
         'link'    =>  '',
         'target'  =>  '_blank',
         'alt'     =>  ''    
    );  
        
} ?>
<div class="scene">
    <?php
    
    if (!empty($scene['link']))echo '<a href="'.$scene['link'].'"'.(!empty($scene['target'])?' target="'.$scene['target'].'"':'').'>';
    echo '<img src="'.$scene['src'].'" '.(!empty($scene['alt'])?' alt="'.$scene['alt'].'"':'').' />';
    if (!empty($scene['link']))echo '</a>';
    
    ?>
</div>

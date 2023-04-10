<div class="div_vignette">
    
<img src="/theme/black_blue/img/menu_open.svg" class="btn_menu menu_open" /> </img> 
<img src="/theme/black_blue/img/menu_close.svg" class="btn_menu menu_close" /> </img> 



<div class="content">
    
    <div class="vignette_box2">
    <div class="vignette_box">
	<img src="https://picsum.photos/200/300" class="rounded">
	<img src="/theme/black_blue/img/modifier.svg" class="picto_modif" /> 
	<?php   if ($userRole == 'agent') {?>
	<img src="/theme/black_blue/img/profile_ok.svg" class="profile_ok" />
	<?php } ?>

    </div>
	<a class="btn btn_modif  black " title="<?= __('voir ma page') ?> ">
    <?= __('voir ma page') ?>
    </a>
    </div>
    <div class="name">
	
	<?php if($userRole=="agent") echo "James Tye"; else echo "Lily Potter"; 
	//echo"<br>".$userRole;
	
	?>
	
    </div>

    
    
    <div class="pictos"> <img src="/theme/black_blue/img/medias/tel2.svg"><img src="/theme/black_blue/img/medias/chat2_gris.svg"><img src="/theme/black_blue/img/medias/webcam2.svg"><img src="/theme/black_blue/img/medias/sms2_gris.svg"><img src="/theme/black_blue/img/medias/email2_gris.svg"></div>
    
    <div class="name_s">
	<?php if($userRole=="agent") echo "James Tye"; else echo "Lily Potter"; 
	//echo"<br>".$userRole;	?>
	
	<div class="pictos"> <img src="/theme/black_blue/img/medias/tel2.svg"><img src="/theme/black_blue/img/medias/chat2_gris.svg"><img src="/theme/black_blue/img/medias/webcam2.svg"><img src="/theme/black_blue/img/medias/sms2_gris.svg"><img src="/theme/black_blue/img/medias/email2_gris.svg"></div>
    </div>
    
</div>
</div>
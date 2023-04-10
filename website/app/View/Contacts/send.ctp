<script src='https://www.google.com/recaptcha/api.js'></script>
<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Support Spiriteo'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">

    <?php
	
	$idlang = $this->Session->read('Config.id_lang');
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($idlang == 1){
				if($extension == 'ca')$idlang=8;	
				//if($extension == 'ch')$idlang=10;
				//if($extension == 'be')$idlang=11;
				if($extension == 'lu')$idlang=12;
			}
	
    /* Block explicatif */
    echo $this->FrontBlock->getPageBlocTextebyLang(453,$idlang);

    ?>
		</div>
    </section>

</div>

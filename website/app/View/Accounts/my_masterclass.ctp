<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="my_masterclass-page page ">

    <?php
   // $this->Session->setFlash('Thanks for your payment.');
    ?>



    <article class="">
	<h1 class="">  <?= __('Mes Masterclass') ?></h1>
	<?= __("Vos MasterClass passées et à venir sont répertoriées ci-dessous. Il vous appartient le jour J d'avoir une connexion de qualité pour accéder à celle-ci.") ?>


    </article>
    
    
    <div class="title lh30-35 fw500"><?= __('Votre prochaine MasterClass') ?></div>
    
    <?php 
    for($i=0;$i<2;$i++)
	{
	
	
    ?>
    
    
    <div class="cadre_master">
	
	<div class="master_id">
	    <div class="vignette"><img class="foto_id" src="https://picsum.photos/200/300"></div>
	    <div class="name lh24-28h fw500">James Tye</div>
	    <div class="job lh22-33 fw300">Photographe</div>
	</div>
	
	<div class="video_txt lgrey2 fw400">
	    <div class="video">
		  <img class=" play" src="/theme/black_blue/img/btn/play_red.svg">
		  <video>
			
    			<source src="/img/effacer/Videoarte.mp4#t=1" type="video/mp4">
    			<source src="maVideo.webm" type="video/webm">
    			<p>Votre navigateur ne prend pas en charge les vidéos HTML5.
    			    Voici <a href="myVideo.mp4">un lien pour télécharger la vidéo</a>.</p>
    		    </video>
	    <div class="maj lh18-27 "><?= __('Mise à jour')." ". __('le') ?> : 15/02/2022</div>
	    </div>
	     <div class="txts  lh22-26b">
		<div class="titre bar_field">Titre de la vidéo formation</div>
		<div class="ss_titre bar_field">Sous-titre de la vidéo formation</div>
		<div class="descriptif bar_field">Descriptif de la vidéo formation 
		
		<a href="" class="lire blue2 lh18-27 fw500"><?= __('Lire la suite') ?>&nbsp; <img class="arrow"  src="/theme/black_blue/img/arrow_right.svg"></a>
		</div>
	    </div>
	</div>
	
	<div class="date blue2 lh30-35c fw500">25 mai 2022</div>
	
	<div class="btn orange multi ">
	    10 jours 22 heures 25 min<br/><?= __('Accéder à la MasterClass') ?>
	</div>
	
	<div class="rappel lgrey2 lh22-28">
	    <?= __("Vous recevrez également un Email et un SMS contenant le lien d'accès à votre MasterClass une heure avant le début de celle-ci.") ?>
	</div>
	
    </div>
    
    
    <?php } ?>
    
    
    
    </section>



<script>

document.addEventListener("DOMContentLoaded", function() {
	
	/*
        $(".vignette_btns_txt .delete, .cadre_videos .delete2").click(function ()
        {
	   console.log("click");
	   var target = $(this).closest(".vignette_btns_txt");
	   if( confirm("Sure to  delete ?"))
	   target.remove();  
        })
	
	*/
	
	
	$(".video .play, video").click(function ()
        {
	
	    let cadre = $(this).closest(".video");
	    let btn_play = cadre.find(".play")
	    let media = cadre.find("video")[0]
	    //console.log("media",media);
	    if (media.paused) 
	    {	
	      media.play();
	      btn_play.fadeOut('fast'); 
	    } 
	    else 
	    {
	    btn_play.fadeIn('fast'); 
	     media.pause();
	    }
	})






   });
</script>
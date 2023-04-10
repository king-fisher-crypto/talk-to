<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="my_videos-page my_masterclass-page page ">

    <?php
   // $this->Session->setFlash('Thanks for your payment.');
    ?>



    <article class="marge">
	<h1 class="">  <?= __('Mes Vidéos  Formation') ?></h1>
	<?= __("Félicitation, sur cette page vous avez accès à toutes les Vidéos formation de vos LiviMasters, vous pouvez les regarder autant de fois que vous le souhaitez ! ") ?>


	<div class="btns"> 

	    <a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2"    title="<?= __('agent') ?>">
	    <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
	    <form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __('agent') ?>"></form>
	    </a> 	
	    
	     <div class="btn chercher h70 lh24-28 p20spe blue2 up_case"    title="<?= __('chercher') ?>"><?= __('chercher') ?></div> 	
	</div>  
	
	
    </article>
    
   
    
    <?php 
    for($i=0;$i<2;$i++)
	{
	
	$name = "James Tye";
	$job = "Photographe";
	$video="Videoarte.mp4";
	if($i==1){
	    $name = "Alexander Platz";
	    $job="Artist";
	    $video="Otavia.mp4";
	    }
    ?>
    
<!--    <div class="div_video_num_tablet" >
	<div class="video_num_div">
	    <div class="video_num fw500 blue2"><?=$v; ?></div>
	    <div class="trait_fin"></div>
	    </div>-->
	
	
    
    <div class="cadre_master">
	
	
	
	
	<div class="master_id">
	    <div class="vignette"><img class="foto_id" src="https://picsum.photos/200/400"></div>
	    <div class="name lh24-28h fw500"><?=$name;?></div>
	    <div class="job lh22-33 fw300"><?=$job;?></div>
	</div>
	
	  <?php 
    for($v=1;$v<=2;$v++)
	{
    ?>
	<div class="video_txt lgrey2 fw400">
	    <div class="video_num_div">
	    <div class="video_num fw500 blue2"><?=$v; ?></div>
	    <div class="trait_fin"></div>
	    </div>
	    <div class="video">
		<div class="video_title1 blue2 fw400 lh24-28i"><div class='div_disk'><div class="white2  disk"><?=$v;?></div></div> &nbsp;<?= __('Titre de la vidéo formation') ?></div>
		  <div class="video_title lh22-26b fw600"><?= __('Video Titre') ?></div>
		  <div class="video_subtitle lh18-27b fw300">Lorem ipsum dolor sit amet...</div>
		  <img class=" play" src="/theme/black_blue/img/btn/play.svg">
		<video>
			
    			<source src="/img/effacer/<?=$video;?>#t=1" type="video/mp4">
    			<source src="maVideo.webm" type="video/webm">
    			<p>Votre navigateur ne prend pas en charge les vidéos HTML5.
    			    Voici <a href="myVideo.mp4">un lien pour télécharger la vidéo</a>.</p>
    		 </video>
	    <div class="maj lh18-27 "><?= __('Mise à jour')." ". __('le') ?> : 15/02/2022</div>
	    </div>
	     <div class="txts  lh22-26b">
		<div class="titre bar_field"><?= __('Titre de la vidéo formation') ?></div>
		<div class="ss_titre bar_field"><?= __('Sous-titre de la vidéo formation') ?></div>
		<div class="descriptif bar_field"><?= __('Descriptif de la vidéo formation') ?> 
		
		<a href="" class="lire blue2 lh18-27 fw500"><?= __('Lire la suite') ?>&nbsp; <img class="arrow"  src="/theme/black_blue/img/arrow_right.svg"></a>
		</div>
	    </div>
	</div>	
    <?php } ?>
	
	
   </div><!--  fin cadre_master-->
    
    </div>
    <?php } ?>
    
    
    
    </section>



<script>

document.addEventListener("DOMContentLoaded", function() {
	
	
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
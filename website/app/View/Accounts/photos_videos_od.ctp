<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();

//	echo $this->element('modal');
	
	$message =  __("Confirmez vous la suppression définitive ?");
	$this->set('message', $message);
	echo $this->element('Utils/modal-confirmation');
?>


<section class="photos_videos_od-page page ">

    <?php
    $this->Session->setFlash('Thanks for your payment.');
    ?>



    <article class="">
	<h1 class="">  <?= __('Photos / Vidéos à la demande') ?></h1>
	<?= __("Lorsque vous avez acheté des photos ou Vidéos à la demande auprès d'un LiviMaster, celles-ci sont disponibles ci-dessous dès que ce dernier vous l'aura envoyé.  ") ?>


    </article>

    <div class="btns"> 

	<a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2"    title="<?= __('agent') ?>">
	    <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
	    <form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __('agent') ?>"></form>
	</a> 	

	<div class="btn chercher h70 lh24-28 p20spe blue2 up_case" title="<?= __('chercher') ?>"  ><?= __('chercher') ?></div> 	
     

    </div>  


    <?php
    $photos_ar = ["Rectangle-4314.jpg", "Rectangle-4315.jpg", "Rectangle-4316.jpg",
	"Rectangle-4314.jpg", "Rectangle-4315.jpg"];
    $names_ar = ["James Tye", " Alex Black", " Alen Smith  ", " Sirius Black", " Lily Potter",
	" Bernard Cartier"];
    $videos_ar = ["Darkness.mp4", "Otavia.mp4", "Videoarte.mp4", "Otavia.mp4"];
    ?>

    <div class="cadre_table ">

	<div class="title photo lh30-35C lgrey2"><?= __('Photos à la demande') ?></div>


	<div class="cadre_vignettes ">

	    <?php
	    $i = 0;
	    $id_item= 0;
	    foreach ($photos_ar as $photos)
		{
		?>
    	    <div class="vignette_btns_txt" id="item_<?=$id_item?>" >

    		<div class="vignette_btns" >
    		    <img class="button delete" src="/theme/black_blue/img/croix.svg">
		    <a href="/img/effacer/<?=$photos?>" download="<?=$photos?>"><img class="button download" src="/theme/black_blue/img/download.svg" ></a>

    		    <img class="vignette" src="/img/effacer/<?=$photos?>">
    		</div>	    
    		<div class="date lgrey2 lh22-26">05/05/2022<br/><?= $names_ar[$i] ?></div>

    	    </div>


		<?php
		$id_item++;
		$i++;
	    }
	    ?>
	</div>   <!-- fin cadre vignette-->

	
	<div class="cadre_n_vignettes " >
<div class="link photos blue2 up_case underline lh22-28"><?= __('Voir plus') ?> <img class="arrow" src="/theme/black_blue/img/arrow_right.svg"></div>
	</div>   <!-- fin cadre vignette-->

	
<div class="title video lh30-35C lgrey2"><?= __('Vidéos à la demande') ?></div>


	<div class="cadre_videos ">
	
	    <?php
	
	    $i = 0;
	    foreach ($videos_ar as $video)
		{
		
		?>
    	    <div class="vignette_btns_txt" id="item_<?=$id_item?>">

    		<div class="video_btns" >
		  
		    <img class=" play" src="/theme/black_blue/img/btn/play.svg">
		    
		    <div class="button delete delete2"></div>
		    <img  class="button delete " src="/theme/black_blue/img/croix.svg">
		    
		     <a href="/img/effacer/<?=$photos?>" download="<?=$video?>"><img class="button download" src="/theme/black_blue/img/download.svg" ></a><!-- comment -->
		     
    		    <video>
    			<source src="/img/effacer/<?=$video?>#t=1" type="video/mp4">
    			<source src="maVideo.webm" type="video/webm">
    			<p>Votre navigateur ne prend pas en charge les vidéos HTML5.
    			    Voici <a href="myVideo.mp4">un lien pour télécharger la vidéo</a>.</p>
    		    </video>



    		</div>	    
    		<div class="date lgrey2 lh22-26">05/05/2022<br/><?= $names_ar[$i] ?></div>

    	    </div>


		<?php
		$i++;
		$id_item++;
	    }
	    ?>
	</div>   <!-- fin cadre videos-->

	<div class="cadre_n_vignettes ">
	   
	<div class="link videos blue2 up_case underline lh22-28"><?= __('Voir plus') ?> <img class="arrow"  src="/theme/black_blue/img/arrow_right.svg"></div>
	</div>
	
    </div> <!-- fin cadre table-->
<?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



    <?php
//echo $this->Frontblock->getRightSidebar();
    ?>



</section>


<script>

document.addEventListener("DOMContentLoaded", function() {
	
	let id_delete;
	
	
	  $(".vignette_btns_txt .delete, .cadre_videos .delete2").click(function ()
        {
	    console.log("this",$(this));
	    var target = $(this).closest(".vignette_btns_txt");
	    id_delete = $(target).attr("id")
	    console.log("id_delete",id_delete);
	    $("#modal-confirmation").modal();
})
	
	
		
addEventListener('confirm', delete_item, false);
function delete_item() { 
   console.log("id_delete",id_delete);
   $("#"+id_delete).remove();  
    }

	
/*
        $(".vignette_btns_txt .delete, .cadre_videos .delete2").click(function ()
        {
	   console.log("click");
	   var target = $(this).closest(".vignette_btns_txt");
	   if( confirm("Sure to  delete ?"))
	   target.remove();  
        })
	
	*/
	
	
	$(".video_btns video, .video_btns .play").click(function ()
        {
	
	    let cadre = $(this).closest(".video_btns");
	    let btn_play = cadre.find(".play")
	    let media = cadre.find("video")[0]
	    console.log("media",media);
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

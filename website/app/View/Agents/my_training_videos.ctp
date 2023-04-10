<?= $this->Html->script('/theme/black_blue/js/autosize.js'); ?>
<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();

//	echo $this->element('modal');

$message = __("Confirmez vous la suppression ?");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');
?>


<section class="marg180 my_videos-page my_training_video-page my_masterclass-page page ">

    <?php
    // $this->Session->setFlash('Thanks for your payment.');  
    ?>



    <article class="">
	<h1 class="">  <?= __('Mes vidéos formation') ?></h1>
	<h4 class="top">  <?= __('Réalisez vos Vidéos pour vendre votre savoir faire et vos conseils') ?></h4>
	<?= __("Proposer une formation en Vidéo est une formidable opportunité de générer des revenus passifs, une fois votre formation créée, celle-ci peut être vendue à l'infini. Formation en trading, coaching, sport, diététique, patisserie/cuisine, marketing, mais bien plus encore, il existe des milliers de sujets à illustrer en vidéo. A vous d'imaginer le contenu lié à votre activité et nous vous aiderons à faire connaitre votre formation via notre réseau d'affiliation, d'influenceurs et de professionnels&nbsp;!") ?>
    </article>


    <img src="/theme/black_blue/img/malte-helmhold-Vy2Y1cCLiT8-unsplash 1.jpg" class="big_img">

    <div class="div_protection">

	<h4>  <?= __('protection de vos vidéos formation') ?></h4>
	<?= __("Les Vidéos formation vendues ne seront pas téléchargeables par les clients, seront uniquement consultable dans leur compte LiviTalk et seront protégées par un bandeau avec nom, prénom et numéro de téléphone du client pour éviter une diffusion éventuelle en dehors de LiviTalk.") ?>

    </div>



    <?php
    echo $this->Form->create
	    (
	    'agents',
	    array(
		'type' => 'file',
		'action' => 'my_training_videos/presentation',
		'nobootstrap' => 1,
		'inputDefaults' => array(
		    'label' => false,
		    'div' => false,
		),
		'class' => '',
		'default' => 1)
    );
    ?>


    <div class="cadre_master cadre_videos cadre_presentation">

	<div class="div_presentation">

	    <h4>  <?= __('vidéo de présentation') ?></h4>
<?= __("Téléchargez votre video de présentation, celle-ci sera accessible et visible gratuitement. Nous vous conseillons un format court entre 1 et 2 minutes par exemple, dans laquelle vous vous présentez ou présentez le contenu de votre vidéo formation.") ?>

	</div>

	
    <div class="cadre_video">
	
	<div class="video_txt lgrey2 fw400">

	    <div class="video">

		<div class="video_title p22 t22 m18 fw600"><?= __('Video Titre') ?></div>
		<div class="video_subtitle p18 t18 m16 fw300">Lorem ipsum dolor sit amet...</div>
		<img class="video_clap play" src="/theme/black_blue/img/video_clap.svg">
		<img class="play red" src="/theme/black_blue/img/btn/play_red.svg">
		<img class="play bleu" src="/theme/black_blue/img/btn/play.svg">
		<img class="close_video " src="/theme/black_blue/img/croix.svg">

		<video>
		    <source src="" type="video/mp4">
		    <?=__('Votre navigateur ne prend pas en charge les vidéos HTML5.') ?>
		</video>

	    </div>
	    <div class="txts  p22 t18 m16">



<?php
echo $this->Form->input("title",
	[
	    'label' => false,
	    'class' => 'titre bar_field',
	    'placeholder' => __('Titre de la vidéo formation'),
	]
);

echo $this->Form->input("title",
	[
	    'label' => false,
	    'class' => 'ss_titre bar_field',
	    'placeholder' => __('Sous-titre de la vidéo formation'),
	]
);

echo $this->Form->input('description',
	[
	    'type' => 'textarea',
	    'label' => false,
	    'placeholder' => __('Descriptif de la vidéo formation'),
	    'escape' => false,
	    'class' => 'descriptif bar_field p22 t14 m13',
	    'rows' => '3',
	    'cols' => '40'
]);
?>



	    </div>
	</div>



	<div class="btns">  

<?php
echo $this->Form->input("file",
	[
	    'type' => 'file',
	    'label' => [
		'text' => __('Télécharger'),
		'class' => 'label_file btn white h85g up_case'
	    ],
	    'class' => 'file preview btn_like',
	    'id' => 'video_pres'
]);


?>
	</div>
	<div class="btns alt">  
<!--
	    <div class="btn white label_file modify_video  h85g p23 t18 m16 <?= __('modifier') ?> up_case blue2" onclick="modify_video(this)"><?= __('modifier') ?></div>-->
	    <div class="label_file delete_video underline h50 p23 t16 m16 <?= __('supprimer') ?> up_case blue2" onclick="delete_video(this)"><?= __('supprimer') ?></div>
	    <?php 
	    echo $this->Form->submit(__('valider'),
		    array(
		'class' => 'label_file btn white h85g up_case'
	    ));
	    ?>
	    
	</div>

	</div>

	<div class="note"><?= __('Choisissez les formats Vidéos standards') ?></div>

    </div><!--  fin cadre_master-->
<?php echo $this->Form->end(); ?>



    <?php
    echo $this->Form->create
	    (
	    'agents',
	    array(
		'type' => 'file',
		'action' => 'my_training_videos/formations',
		'nobootstrap' => 1,
		'inputDefaults' => array(
		    'label' => false,
		    'div' => false,
		),
		'class' => '',
		'default' => 1)
    );
    ?>


    <div class="cadre_master cadre_videos cadre_formations">

	<div class="div_presentation">

	    <h4>  <?= __('vidéos formation') ?></h4>
<?= __("Téléchargez votre première vidéo formation, celle-ci ne sera visible de vos clients qu'après achat. Vous avez la possibilité de séquencer une vidéo en plusieurs sous-vidéos pour pouvoir les modifier et améliorer dans le temps sans tout refaire de A à Z et en ne modifiant que l'une d'entre elles par exemple.") ?>

	</div>

    <DIV class="div_seq">
	<div class="cadre_video">
	
	    <div class="video_num_div">
	<div class="video_num fw500 blue2">1.1</div>
	<div class="trait_fin"></div>
	</div>
	    
	<div class="video_txt lgrey2 fw400">

	    <div class="video">

		<div class="video_title p22 t22 m18 fw600"><?= __('Video Titre') ?></div>
		<div class="video_subtitle p18 t18 m16 fw300">Lorem ipsum dolor sit amet...</div>
		<img class="video_clap play" src="/theme/black_blue/img/video_clap.svg">
		<img class="play red" src="/theme/black_blue/img/btn/play_red.svg">
		<img class="play bleu" src="/theme/black_blue/img/btn/play.svg">
		<img class="close_video " src="/theme/black_blue/img/croix.svg">

		<video>
		    <source src="" type="video/mp4">

		    <p>Votre navigateur ne prend pas en charge les vidéos HTML5.
			Voici <a href="myVideo.mp4">un lien pour télécharger la vidéo</a>.</p>
		</video>

	    </div>
	    <div class="txts  p22 t18 m16">



<?php
echo $this->Form->input("title",
	[
	    'label' => false,
	    'class' => 'titre bar_field',
	    'placeholder' => __('Titre de la vidéo formation'),
	]
);

echo $this->Form->input("title",
	[
	    'label' => false,
	    'class' => 'ss_titre bar_field',
	    'placeholder' => __('Sous-titre de la vidéo formation'),
	]
);

echo $this->Form->input('description',
	[
	    'type' => 'textarea',
	    'label' => false,
	    'placeholder' => __('Descriptif de la vidéo formation'),
	    'escape' => false,
	    'class' => 'descriptif bar_field p22 t14 m13',
	    'rows' => '3',
	    'cols' => '40'
]);
?>



	    </div>
	</div>



	<div class="btns"> 

	    <label  class="label_file btn white h85g up_case"><?= __('Télécharger') ?>
		<input type="file" name="data[agents][file]" class="file preview btn_like" id="video_pres3">
	    </label>

<?php
/*
  echo $this->Form->input("file",
  [
  'type' => 'file',
  'label' => [
  'text' =>  __('Télécharger'),
  'class' => 'label_file btn white h85g up_case'
  ],
  'class' => 'file preview btn_like',
  'id' => 'video_pres'
  ]);
 */

/*

*/
?>
	</div>
	<div class="btns alt">  

	    <!--<div class="btn white label_file modify_video  h85g p23 t18 m16 <?= __('modifier') ?> up_case blue2"><?= __('modifier') ?></div>-->
	    <div class="   label_file delete_video underline h50 p23 t18 m16 <?= __('supprimer') ?> up_case blue2"><?= __('supprimer') ?></div>
	    
	    <?php 
	    echo $this->Form->submit(__('valider'),
			    array(
			'class' => 'label_file btn white h85g up_case'
		    ));
	    
	    ?>
	</div>



	<div class="note formats"><?= __('Choisissez les formats Vidéos standards') ?></div>

	<DIV class="div_sub_seq">
	</DIV>


	<div class="center_h">
	    <div class="btn lgrey2 add_sub_seq p22 t18 m14">
<?= __('Ajouter une sous-vidéo-séquencée') ?>
		<img class="" src="/theme/black_blue/img/circle-plus.svg">
	    </div>
	</div>
	

	
	    
	</DIV>
	</div>
	
	<div class="btn blue2 add_seq p24 t18 m14">
	    <img class="" src="/theme/black_blue/img/btn/btn_plus.svg">
<?= __('Ajouter une nouvelle Vidéo') ?>
	</div>




    </div><!--  fin cadre_master-->
<?php echo $this->Form->end(); ?>  



    <div class="sub_seq_clone">

	    <?php
	    echo $this->element('video_form');
	    ?>


    </div>
    
    
    <div class="div_affiliation bordark">
	<h3 class="p28 t22 m18 fw500 black2"><?= __('AFFILIATION') ?></h3>
	<h4 class="p24 t18 m16"><?= __('Offrez une commission à ceux qui feront la promotion de votre formation !') ?></h4>
	
	<div class="p24 t16 m14">
	<?= __('<span class="blue2">LiviTalk</span> propose une page <a href=""><span class="blue2"> “Affiliation Videos formation”</span></a> via laquelle d\'autres LiviMasters mais également influenceurs et bien d\'autres peuvent promouvoir votre Vidéo formation auprès de leurs Followers, clients et entourage, plus la commission d\'affiliation est importante, plus vous avez de chances que votre Vidéo formation soit diffusée.') ?>
	</div>
	
	<div class="je_souhaite blue2 p23 t16 m14"><?= __('Je souhaite attribuer la somme suivante aux affiliés lors de chaque vente effectuée par ces&nbsp;derniers:') ?></div>
	
	
	<div class="div_commission center_h">
	 <div class="btn_commission blue2 btn_like p24 t24 m16"  >
	    <div class="commission  " contenteditable="true"></div>$
	</div>
	</div>
	
	<div class="lgrey2 fw400 p18 t14 m14 center_h"><?= __('La commission doit être inférieure à votre prix de&nbsp;vente') ?></div>
    </div>
    
   
    

    <style>
	#modal-confirmation .modal-content{
	    height:calc(300px*var(--coef))
	}
    </style>


    <script>

	document.addEventListener("DOMContentLoaded", function ()
	{

	    /////////////// PREVIEW VIDEO BEFORE UPLOAD  ////////////
	    $(document).on("change", ".preview", function (evt)
	    {
		console.log("this", $(this));
		let cadre_video = $(this).closest(".cadre_video")
		let target = $(cadre_video).find("video source")
		console.log("cadre_video", cadre_video);
		target[0].src = URL.createObjectURL(this.files[0]);
		target.parent()[0].load();
		$(cadre_video).addClass("video_on")
	    });



	    /////////////// VIDEO PLAY  ////////////
	    window.play = function(elmt)
	    {
		console.log("play",elmt);
		let video = $(elmt).closest(".video");
		
		let src = $(video).find("source").attr("src")
		console.log("src",src[0]);
		if(src=="") return;
		
		let btn_play = video.find(".play")
		let media = video.find("video")[0]
		//console.log("media",media);
		if (media.paused)
		{
		    $(video).addClass("playing")
		    media.play();
//		    btn_play.fadeOut('fast');
		} else
		{
//		    btn_play.fadeIn('fast');
		    $(video).removeClass("playing")
		    media.pause();
		}
	    }
	    
	     $("video, .video > .play").click(function ()
		{
//		   console.log(".video .play, video CLICK");
		   play(this) 		    
		})

	    /////////////// VIDEO modify  ////////////
	    window.modify_video = function (elmt)
	    {
		let cadre_video = $(elmt).closest(".cadre_video")
		
		$(cadre_video).removeClass('preview_video').addClass("video_on")
	    }

	    $(".btn.modify_video").click(function ()
	    {
		modify_video(this)    
	    })
	    ////////////// VIDEO delete  ////////////
	    window.delete_video_target 
	    
	    window.delete_video =  function ()
	    {
		let elmt = window.delete_video_target
		console.log("delete_video elmt",elmt);
		let cadre_video = $(elmt).closest(".cadre_video")
		$(cadre_video).removeClass('preview_video').removeClass("video_on")
		
		let video = $(elmt).closest(".video")
		$(cadre_video).removeClass('playing')
		
		let target = $(cadre_video).find("video source")
		console.log("target cadre_video",target);
		target[0].src = "";
		target.parent()[0].load();
	    }
/*	    
	    $(".delete_video, .close_video").click(function ()
	    {
		    window.delete_video_target = this;  

		   $("#modal-confirmation").modal () ;

	    })
*/
	    $(document).on('click', '.delete_video, .close_video', function() {
		    window.delete_video_target = this;  
		   $("#modal-confirmation").modal () ;
	    });


	    addEventListener('confirm', window.delete_video, false);


	    /* RECHERCHE DU NUM POUR ' Ajouter une sous-vidéo-séquencée' */
	    function get_subseq(elm){
		
		console.log("get_subseq elm",elm);
		/* cherche si sub seq existe*/
		let div_sub_seq =  $(elm).prevAll(".div_sub_seq ");
		//console.log("div_sub_seq .html", div_sub_seq.html() );
		let num = 0
		
		if( $.trim($(div_sub_seq).html())!='')
		{
		    let last_sub_seq = $(div_sub_seq).find(".cadre_video").last();
		    console.log("last sub_seq ",last_sub_seq);
		    num  = $(last_sub_seq).find(".video_num").text()
		    return num_to_arr(num);
		}
		else
		{
		  
		    let cadre_video = $(elm).closest(".cadre_video");
		    num  = $(cadre_video).find(".video_num").text()
		     return num_to_arr(num);
		    
			    
		}
		 
		
	    }
	    
	     /* RECHERCHE DU NUM POUR 'Ajouter une nouvelle Vidéo' */
	     function get_seq(elm){

		/* sinon cherche la seq parente principale */
		
//		let div_seq =  $(elm).prevAll(".div_seq ");
		let div_seq =  $(".div_seq ");
		
		/* si dejà des seq ajoutées*/
		if( $.trim($(div_seq).html())!='')
		{
		    let last_seq = $(".div_seq >.cadre_video").last();
		   
		    num  = $(last_seq).children(".video_num_div").children(".video_num").text()
		    console.log("last seq",last_seq, "num", num);
		    return num_to_arr(num);
		}
		else
		{
		
		let cur_parent =  $(elm).prevAll(".video_txt");
		console.log("cur_parent", cur_parent);
		num  = $(cur_parent).find(".video_num").text()
				
		console.log("num",num);
		return num_to_arr(num);
		}
		
		
	    }
	    

	    function num_to_arr(num)
	    {
		console.log("num_to_arr num",num);
		let seq_ar = num.split(".");
		let seq = parseInt(seq_ar[0])
		let subseq = parseInt(seq_ar[1])
		return [seq, subseq]
	    }
	    
	    ///////////////  add VIDEO sub sequence //////////// 
	    window.add_sub_seq_click = 	    function (elmt)
	    {
		console.log("F° add_sub_seq_click");
		let seq_arr =  get_subseq($(elmt).parent())
		let id_seq = seq_arr[0]
		let id_subseq = seq_arr[1]

		let $sub_seq  = $( ".sub_seq_clone .cadre_video" ).clone() 
		//console.log("$sub_seq",$sub_seq);
		$($sub_seq).find(".video_num").text(id_seq+"."+(id_subseq+1))
		$($sub_seq).find(".add_sub_seq").css("display", "none")
		$($sub_seq).find(".div_sub_seq").css("display", "none")
		
		let div_sub_seq =  $(elmt).parent().prevAll(".div_sub_seq ");
		
		$( div_sub_seq ).append( $( $sub_seq ) );		    
		
	    }
	    
	    
	    
	    $(".btn.add_sub_seq").click(function ()
	    {
		
		//let cur_parent =  $(this).closest(".video_txt");
		console.log("btn.add_sub_seq click");
		add_sub_seq_click(this)
		
	    })
	    
	    
	    	    ///////////////  add VIDEO sequence ////////////  
		   
	    $(".btn.add_seq").click(function ()
	    {
		
		//let cur_parent =  $(this).closest(".video_txt");

		let seq_arr =  get_seq($(this))
		let id_seq = seq_arr[0]
		let id_subseq = seq_arr[1]
		
		
	
		let $sub_seq  = $( ".sub_seq_clone .cadre_video" ).clone() 
		//console.log("$sub_seq",$sub_seq);
		$($sub_seq).find(".video_num").text( (id_seq+1)+"."+id_subseq)
		
		$( ".div_seq" ).append( $( $sub_seq ) );
		
		
		/*
		$($sub_seq).find(".btn.add_sub_seq").click(function ()
		{
		    //let cur_parent =  $(this).closest(".video_txt");
		    console.log("btn.add_sub_seq click APPEND");
		    add_sub_seq_click(this)

		})
		*/
		
		
	    })
	    
	    
	    
	    
	});
    </script>

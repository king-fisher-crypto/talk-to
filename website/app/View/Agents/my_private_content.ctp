<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();

//	echo $this->element('modal');

$message = __("Confirmez vous la suppression définitive ?");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');
echo $this->element('Utils/modal-media');
?>


<section class="photos_videos_od-page my_private_content-page page ">



<?php
// $this->Session->setFlash('Thanks for your payment.');  
?>



    <article class="">
	<h1 class="">  <?= __('Mes contenus privés') ?></h1>
	<h4 class="top">  <?= __('Proposez vos photos et vidéos privées à votre communauté') ?></h4>
<?= __("Proposez des contenus privés à vos Followers, ces derniers pourront y accéder via un abonnement mensuel ou gratuitement. Nous vous invitons à alimenter vos contenus privés de manière régulière pour conserver vos Followers sur le long terme.") ?>


    </article>

    <div class="form_envoi"> 

	<h4 class="">  <?= __('Ajouter une nouvelle photo / Vidéo privée') ?></h4>

<?php
echo $this->Form->create
	(
	'Agent',
	array(
	    'type' => 'file',
	    'action' => 'my_private_content',
	    'nobootstrap' => 1,
	    'inputDefaults' => array(
		'label' => false,
		'div' => false,
	    ),
	    'class' => 'form_files',
	    'default' => 1)
);
?>
	<div class="preview_media vignette_btns_txt">
	    
	    <div class="preview vignette_btns">
		<img class="button delete" src="/theme/black_blue/img/croix.svg">
		
		<?php // IMG ?>
		<img class="vignette type image" src="">
		
		<?php // VIDEO ?>
		<div class="type video">
		    <img class=" play" src="/theme/black_blue/img/btn/play.svg">
		    <video> <source src="" type="video/mp4"><p>
			    <?= __('Votre navigateur ne prend pas en charge les vidéos HTML5.'); ?>
			    <?= __('Voici') ?> 
			    <a href=""> 
			    <?= __('un lien pour télécharger la vidéo') ?></a>
			    .</p> </video>
		</div>
		
		<img class="video_clap play" src="/theme/black_blue/img/menu/contenus.svg" />
		
	    </div>
	<div class="txts">
<?php
echo $this->Form->input("title",
	[
	    'label' => false,
	    'class' => 'title bordark p22 t18 m14 lgrey2',
	    'placeholder' => __('Titre de ma nouvelle photo / vidéo'),
	]
);

echo $this->Form->input('description',
	[
	    'type' => 'textarea',
	    'label' => false,
	    'placeholder' => __('Présentation de ma nouvelle photo / vidéo'),
	    'escape' => false,
	    'class' => 'description bordark p22 t18 m14 lgrey2',
	    'rows' => '2',
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
		'class' => 'label_file btn_like h85f up_case'
	    ],
	    'class' => 'preview file  btn_like',
]);

echo $this->Form->end(array('label' => __('valider'), 'class' => 'btn xlarge h85f valider white  up_case'
));
?>
	</div>
	<div class="note_format p20 t18 m15 lgrey2 txt_cent"><?= __('Choisissez les formats photo / vidéo standards') ?> </div>

    </div>  


<?php
$media_ar = [];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4314.jpg", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4315.jpg", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4316.jpg", "titre" => ""];
$media_ar[] = ["type" => "video", "media" => "Darkness.mp4", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4314.jpg", "titre" => ""];
$media_ar[] = ["type" => "video", "media" => "Otavia.mp4", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4315.jpg", "titre" => ""];
$media_ar[] = ["type" => "video", "media" => "Videoarte.mp4", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4316.jpg", "titre" => ""];
$media_ar[] = ["type" => "photo", "media" => "Rectangle-4314.jpg", "titre" => ""];
?>



    <div class="cadre_vignettes ">

    <?php
    $i = 0;
    $id_item = 0;
    foreach ($media_ar as $media)
	{
	?>
    	<div class="vignette_btns_txt" id="item_<?= $id_item ?>" >

    	    <div class="vignette_btns" >
    		<img class="button delete" src="/theme/black_blue/img/croix.svg">

	    <?php
	    switch ($media["type"])
		{
		case "photo":
		    ?>

	    		<img class="vignette" src="/img/effacer/<?= $media["media"] ?>">

			    <?php
			    break;
			case "video":
			    ?>

	    		<img class=" play" src="/theme/black_blue/img/btn/play.svg">
	    		<video>
	    		    <source src="/img/effacer/<?= $media["media"] ?>#t=1" type="video/mp4">
	    		   
	    		    <p><?= __('Votre navigateur ne prend pas en charge les vidéos HTML5.') ?> 
	    <?= __('Voici') ?>
	    			<a target="_blank" href="/img/effacer/<?= $media["media"] ?>" download>
	    <?= __('un lien pour télécharger la vidéo') ?></a>.</p>
	    		</video>

				    <?php
				    break;
				} // fin switch
			    ?>
			
    	    </div>
    	    <div class = "titre lgrey2 p22 t18 m16"><?= __('Titre du photo / vidéo ')
			    ?></div>
    	    <div class="description p20 t14 m14 lgrey2 fw300 ">Lorem ipsum dolor sit amet sit ...</div>
    	    <div class="voir_plus p18 t16 m16 lgrey2 fw400 up_case "><?= __('Voir plus') ?></div>

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








    <!-- fin cadre table-->
<?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>



</section>

<style>
    #modal-confirmation .modal-content{
	height:calc(300px*var(--coef))
    }
</style>


<script>

    document.addEventListener("DOMContentLoaded", function ()
    {

        let id_delete;




        $(".cadre_vignettes  .vignette_btns_txt .delete, .cadre_videos .delete2").click(
                function ()
                {
                    console.log("this", $(this));
                    var target = $(this).closest(".vignette_btns_txt");
                    id_delete = $(target).attr("id")
                    console.log("id_delete", id_delete);
                    $("#modal-confirmation").modal();
                })



        addEventListener('confirm', delete_item, false);
        function delete_item()
        {
            console.log("id_delete", id_delete);
            $("#" + id_delete).remove();
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

	let media
	$('body').on('click', 'video, .vignette_btns .play',
	function ()
        {

            let cadre = $(this).closest(".vignette_btns");
            let btn_play = cadre.find(".play")
            media = cadre.find("video")[0]
            console.log("media", media);
            if (media.paused)
            {
                media.play();
                btn_play.fadeOut('fast');
            } else
            {
                btn_play.fadeIn('fast');
                media.pause();
            }
        })


	$('body').on('mousedown', '.close-modal',
	function ()
        {
	    if (typeof media != 'undefined') { media.pause(); }
            
        })


//    autosize(document.querySelectorAll('textarea'));


        $(".voir_plus ").click(function ()
        {
	    let cadre = $(this).closest(".vignette_btns_txt");
	    
	    let media = cadre.find(".vignette_btns").clone()
	    $( media ).remove( ".delete" ); 
	   // console.log("media",media);
	    
	    $("#modal-media .vignette_btns").html(media)
	    
	    
	    
            $("#modal-media").modal({
	    body:'.my_private_content-page'
	    });
	    
        });

 /////////////// PREVIEW VIDEO BEFORE UPLOAD  ////////////

  
    $(document).on("change", ".preview.file", function (evt)
    {

	let cadre_media = $(this).closest(".form_files")
	if(! $(this).prop("files")) return;
	if(! $(this).prop("files")[0]) return;
	const file = $(this).prop("files")[0];
	console.log("file",file);	
	let type = file.type
	//console.log("Type du fichier : " + type);
	let target
	
	$(".preview_media  .preview .type").css("display","none")
	$(".preview_media  .preview .delete").css("display","block")	
	
	if(type.includes('image'))
	{
	    target =  $(".preview_media  .preview .image")
	    $(target).css("display","block")
	}
	else
	if(type.includes('video'))
	{
	    target =  $(".preview_media  .preview  video")
	    $(".preview_media  .preview  .video").css("display","block")
	}


	

	const url = URL.createObjectURL(file);
	target.prop('src', url);
	console.log("url",url);
//	$(cadre_video).addClass("video_on")
	if(type.includes('video'))
		{
		    target =  $(".preview_media  .preview  video")
		}
	
    });
    

	$(".preview_media .delete").click(
                function ()
                {
                    $(".preview_media  .preview .image").prop('src', '')
		    $(".preview_media  .preview  video").prop('src', '')
		    $(".preview_media  .preview .type").css("display","none")
		    $(".preview_media .delete").css("display","none")
                })


    });
    
    
</script>

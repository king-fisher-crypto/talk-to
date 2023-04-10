<div class="cadre_video">

	<div class="video_num_div">
	<div class="video_num fw500 blue2">1.1</div>
	<div class="trait_fin"></div>
	</div>
    

    <div class="video_txt lgrey2 fw400">
	

	
	
	

	<div class="video" onclick="play(this)">

	    <div class="video_title p22 t22 m18 fw600"><?= __('Video Titre') ?></div>
	    <div class="video_subtitle p18 t18 m16 fw300">Lorem ipsum dolor sit amet...</div>
	    <img class="video_clap play" src="/theme/black_blue/img/video_clap.svg">
	    <img class="play red" src="/theme/black_blue/img/btn/play_red.svg" >
	    <img class="play bleu" src="/theme/black_blue/img/btn/play.svg" >
	    <img class="close_video " src="/theme/black_blue/img/croix.svg" onclick="delete_video(this)">

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

	    echo $this->Form->input("subtitle",
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
	
	<label  class="label_file btn white h85g up_case"><?=__('Télécharger')?>
	<input type="file" name="data[agents][training_video]" class="file preview btn_like" >
	</label>
<?php

/*
echo $this->Form->input("file",
	[
	    'type' => 'file',
	    'label' => [
		'text' => __('Télécharger'),
		'class' => 'label_file btn white h85g up_case'
	    ],
	    'class' => 'file preview btn_like',
	    'id' => 'video_pres'.$id
]);
*/

/*
echo $this->Form->submit(__('valider'),
	array(
    'class' => 'label_file btn white h85g up_case'
));
*/
?>
    </div>
    <div class="btns alt">  

<!--	<div class="btn white label_file modify_video  h85g p23 t18 m16 <?= __('modifier') ?> up_case blue2"  onclick="modify_video(this)"><?= __('modifier') ?></div>-->
	<div class="  underline label_file delete_video h50 p23 t18 m16 <?= __('supprimer') ?> up_case blue2" ><?= __('supprimer') ?></div>
	
	
	 <?php 
	    echo $this->Form->submit(__('valider'),
			    array(
			'class' => 'label_file btn white h85g up_case'
		    ));
	    ?>
	
    </div>
   
    <div class="note formats"><?= __('Choisissez les formats Vidéos standards') ?></div>
    
    
    <DIV class="div_sub_seq">	</DIV>


	<div class="center_h">
	    <div class="btn lgrey2 add_sub_seq p22 t18 m14" onclick="add_sub_seq_click(this)">
<?= __('Ajouter une sous-vidéo-séquencée') ?>
		<img class="" src="/theme/black_blue/img/circle-plus.svg">
	    </div>
	</div>
    
    
</div>
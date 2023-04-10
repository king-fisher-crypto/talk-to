<?php
echo $this->Session->flash();
?>


<section class="mod_consult-page page">


    <article class="">
	<h1 class="">  <?= __('Mes modes de consultation') ?></h1>
	<?= __("Sur LiviTalk vous avez la liberté d'utiliser tous les modes de communication ou bien un seul, vous êtes totalement libre de vos horaires et de choisir sur quel numéro de téléphone re-diriger vos appels téléphoniques. La couleur de vos modes changent automatiquement en fonction de vos choix et passent au bleu lorsque vous êtes disponible, en gris lorsque lorsque vous indisponible et en orange lorsque vous êtes occupés ou en communication.") ?>


    </article> 


    <div class="consult_modes">
	<h3>
	    <?= __('Je suis disponible par') ?>
	</h3>

	<div class="pictos">

	    <DIV class="picto on">
		<img class="on" src="/theme/black_blue/img/medias/tel.svg"> 
		<img class="off" src="/theme/black_blue/img/medias/tel_gris.svg"> 

		<label class="switch">
		    <input type="checkbox" checked>
		    <span class="slider round"></span>
		</label>
	    </DIV>

	    <DIV class="picto ">
		<img class="on" src="/theme/black_blue/img/medias/chat.svg"> 
		<img class="off" src="/theme/black_blue/img/medias/chat_gris.svg"> 
		<label class="switch">
		    <input type="checkbox" >
		    <span class="slider round"></span>
		</label>
	    </DIV>

	    <DIV class="picto ">
		<img class="on" src="/theme/black_blue/img/medias/webcam.svg"> 
		<img class="off" src="/theme/black_blue/img/medias/webcam_gris.svg"> 
		<label class="switch">
		    <input type="checkbox" >
		    <span class="slider round"></span>
		</label>
	    </DIV>

	    <DIV class="picto on">
		<img class="on" src="/theme/black_blue/img/medias/sms.svg"> 
		<img class="off" src="/theme/black_blue/img/medias/sms_gris.svg"> 
		<label class="switch">
		    <input type="checkbox" checked>
		    <span class="slider round"></span>
		</label>
	    </DIV>

	    <DIV class="picto on">
		<img class="on" src="/theme/black_blue/img/medias/email.svg"> 
		<img class="off" src="/theme/black_blue/img/medias/email_gris.svg"> 
		<label class="switch">
		    <input type="checkbox" checked>
		    <span class="slider round"></span>
		</label>
	    </DIV>


	</div>



	<a class="blue2 modif_tarif up_case underline" role="presentation" title="Modify"> <?= __('Modifier mes tarifs') ?></a>


    </div>


    <div class="consult_status borshad">

	<h3 class="">
	    <?= __('Vous êtes') ?>
	</h3>

	<div class="btn_statu dispo">
	    <img src="/theme/black_blue/img/dispo.svg"> 
	    <?= __('disponible') ?>
	</div>

	<div class="btn_statu indispo">
	    <img src="/theme/black_blue/img/indispo.svg"> 
	    <?= __('indisponible') ?>
	</div>

	<div class="statu_occupe_time">
	    <div class="btn_statu occupe">
		<img src="/theme/black_blue/img/occupe.svg"> 
		<?= __('occupé') ?>
	    </div>

	    <div class="btns_time">
		<div class="btn_time" data-time="15">15<?= __('mn') ?></div>
		<div class="btn_time" data-time="30">30<?= __('mn') ?></div>
		<div class="btn_time" data-time="60">60<?= __('mn') ?></div>
	    </div>
	</div>


	<div  id="time_msg" class="xsmall m14">
	    <?= __('mettre mon profil en mode "Occupé" pendant ') ?> <span class="time"></span><?= __('mn') ?>
	</div>

	<div class="activ_dispo borshad">
	    <div class="text p24 t18 m14">
		<?= __('Activer ma disponibilité & indisponibilité automatiquement en fonction de mon planning LiviTalk.') ?> <a href="" class="underline">	<?= __('Voir mon planning. ') ?></a> </div>
	    
	<div id="div_activ_dispo">
	    <div class="btn activer up_case borshad"><?= __('Activer') ?></div>
	    
	    <div>
		<label class="switch">
		    <input type="checkbox" id="checkbox_activ_dispo" >
		    <span class="slider round"></span>
		</label>
	    </div>
	</div>	
	</div>

    </div>

    <div class="transfert borshad">
	<h3 class="">
	    <?= __('Transfert appels vers') ?>
	</h3>

	<div class="text">
	    <?= __('Sélectionner le numéro souhaité pour recevoir vos appels') ?>
	</div>

	<div class="numeros p24 t16 m14 fw400">


	    <label class="checkbox_div "> 
		<input type="radio" name="num_transfert" >
		<span class="checkmark"></span>
		<img src="/theme/black_blue/img/tel_bleu.svg">  
		<span class="b txt">   <?= __('mobile') ?> 1:</span>
		<span class="numero">  +33855964875</span> 

	    </label>

	    <label class="checkbox_div "> 

		<input type="radio" name="num_transfert">
		<span class="checkmark"></span>
		<img src="/theme/black_blue/img/tel_bleu.svg">  
		<span class="b txt">   <?= __('mobile') ?> 2:</span>
		<span class="numero">  +33855964875</span> 
	    </label>

	    <label class="checkbox_div "> 

		<input type="radio" name="num_transfert">
		<span class="checkmark"></span>
		<img src="/theme/black_blue/img/tel_bleu.svg">  
		<span class="b txt">   <?= __('tel-fixe') ?> 1:</span>
		<span class="numero">  +33855964368</span> 
	    </label>

	    <label class="checkbox_div "> 

		<input type="radio" name="num_transfert">
		<span class="checkmark"></span>
		<img src="/theme/black_blue/img/tel_bleu.svg">  
		<span class="b txt">   <?= __('tel-fixe') ?> 2:</span>
		<span class="numero">  +33855964875</span> 
	    </label>





	</div> 
	<a class="blue2 modif_num up_case underline" role="presentation" title="Modify"> <?= __('Modifier mes numéros') ?></a>
    </div>



    <script>
        /*—————————————————————————————————
         PROFILE PAGE
         
         4 TOP BOUTONS + SCREENS
         —————————————————————————————————*/
        window.onload = function ()
        {

            $(".btn_statu ").click(function ()
            {

                $(".btn_statu ").removeClass("actif")
                $(this).addClass("actif")

                if ($(this).hasClass("occupe"))
                {
                    $(".btn_time ").css("display", "flex")
                } else
                {
                    $(".btn_time ").hide();
                    $("#time_msg").hide();
                }


            });

            $(".btn_time ").click(function ()
            {

                $(".btn_time").removeClass("actif")
                $(this).addClass("actif")

                $("#time_msg").show("fast");

                let delai = $(this).data("time")
                console.log("delai", delai);
                $(".time").text(delai)

            });


	
	
	/* CLIC ON CHECKBOXES */
	    $(".consult_modes .pictos .picto ").mousedown(function ()
            {
		//console.log("picto click");
		event.stopPropagation();
//		let img = $(this).find("img");

		//console.log(".consult_modes .pictos .picto",$(this));
		
		
		 var classList = $(this)[0].classList;
    
		 /*
		 for(var i = 0; i < classList.length; i++){
		    console.log( "class",classList[i]);
		}
		*/
			
		let target  = $(this).find("input")
		$(target).click();
		
		if($(this).hasClass("on"))
		{		
		   console.log("class on, added");
		   $(this).removeClass('on')
		}
		else
		{
		   console.log("No class on, removed");
		   $(this).addClass('on')
		}
	
		    
            });

/*
            $(" .btn.activer ").mousedown(function ()
            {
		let parent = $(this).parent();
		let target  = parent.find("input")
		$(target).click();
		    
            });
*/


	    $("#div_activ_dispo").mousedown(function ()
            {
		event.stopPropagation();
		
		if($(".btn.activer ").hasClass("on"))
		{
		   console.log(".hasClass(on",);
		 
		   $(".btn.activer ").text('<?= __('activer') ?>') 
		   $(".btn.activer ").removeClass('on')
		}
		else
		{
		   console.log("no class hasClass(on",);
		   $(".btn.activer ").text('<?= __('désactiver') ?>') 
		   $(".btn.activer ").addClass('on')
		}
		

		let target  = $(this).find("input")
		$(target).click();
		    
		    
            });



        };
    </script>
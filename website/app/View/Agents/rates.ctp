<?php
//echo $this->Session->flash();
// $loyalty_credit = [];


//echo $this->Html->script('/theme/black_blue/js/helpers',array('block' => 'script2'));
//echo $this->Html->script('/theme/black_blue/js/jquery-ui',array('block' => 'script2'));

$message = __('Pour activer ce mode vous devez avoir renseigné un tarif au préalable via la page  " tarifs " ');
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');

?>
<section class="rates-page page  my_training_video-page  marg180">


    <article class="first">
	<h1 class="">  <?= __('Tarifs de mes modes de communication et prestations') ?></h1>
	<?= __("La personne qui vous consulte n’aura jamais votre numéro de téléphone, votre emails ou vos coordonnées. ") ?>

    </article> 


    <div class="cs-utc-selector"> 
	
	<div class="choisissez_tarif"><?= __("Choisissez les tarifs de vos prestations") ?></div>
	
	<div class="cs-utc lh16-24 blue2 fw500">

	    <span class="cs-current-utc p24 t16">

<?= __("modifier ma devise") ?> (<span class="currency_symbol">€</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">
	    
	    <div class="cs-utc-list cs-utc-type-2">
		
		<?php
		foreach ($currencies as $key => $currency)
		    {

		    echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
		    }
		?>

	    </div>
	</div>
    </div>		

    <?php
    $content = [];

    $col1 = ["", "", __("téléphone"), __("chat"), __("webcam"), __("sms"), "10 sms",
	"20 sms", "30 sms", __("email"), __("formation vidéo"), __("masterclass"), __("pdf-document"),
	__("photos à la demande"), "", "", __("vidéos à la demande"), "", "", __("contenu privé"),
	"1 " . __("mois"), "3 " . __("mois"), "6 " . __("mois"), "12 " . __("mois")];

    $col2 = ["", __("minimum") . " " . "1,00$", 2.99, 2.99, 2.99, __("minimum") . " " . "10,00$",
	9.99, 12.99, 15.99, 24.9, 299, 99, 59, __("requête de paiement"), __("ou, si tarif indiqué"),
	59, __("requête de paiement"), __("ou, si tarif indiqué"), 59, __("zéro si gratuit"),
	14.90, 14.90, 14.90, 14.90];

    $col3 = ["", "", __("par minute") . ", " . __("soit"), __("par minute") . ", " . __("soit"),
	__("par minute") . ", " . __("soit"), "", __("soit"), __("soit"), __("soit"), __("prix unitaire") . ", " . __("soit"),
	__("prix unitaire") . ", " . __("soit"), __("prix unitaire") . ", " . __("soit"),
	__("prix unitaire") . ", " . __("soit"), "", "", __("prix unitaire") . ", " . __("soit"),
	"", "", __("prix unitaire") . ", " . __("soit"), "", __("soit"), __("soit"), __("soit"),
	__("soit")];

    echo $this->Form->create('agents',
	    array('action' => 'rates/edit',
		'nobootstrap' => 1,
		'inputDefaults' => array(
		    'label' => false,
		    'div' => false
		),
		'class' => '',
		'default' => 1));
    ?>



    <div class="cadre_table ">


	<div class="overflow jswidth">
<img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg" style="display: block;">
	    <div class="grid ">

		<?php
		$css = "";

		$max_line = 24;
		$max_col = 4;

		for ($l = 1; $l < $max_line; $l++) // lignes
		    {

		    
		    /* supprime accents et espaces */
		    if(!empty( $col1[$l]))
		    $slug = Inflector::slug($col1[$l]);
		    
		    if (!isset($content[$l])) $content[$l] = [];
		    $content[$l][1] = ucfirst($col1[$l]);

		    /* selon montant ou mention ds la col2 */
		    if (in_array($l, [1, 5, 13, 14, 16, 17, 19]))
			{
			if(in_array($l, [ 13,  16]))
			    {
			    $content[$l][2] = "<div class='paiem_request'>".ucfirst($col2[$l])."</div>";
			    }
			    else
			$content[$l][2] = ucfirst($col2[$l]);
			
			}
		    else
			{
			$value = CakeNumber::currency($col2[$l], "",
					array(
				    'wholePosition' => 'after',
				    'thousands' => ' ',
				    'decimals' => ',',
			));
				
		
			$content[$l][2] = "<div>".$this->Form->input("",
				array(
				    'type' => 'text',
				    'value' => $value,
				    'div' => false,
				    'label' => false,
				    'id' => $slug,
				    'name' => $slug,
				    'class' => 'field' . $l . "_2 only_numbers",
				    'target' => 'g' . $l . "_4",
				))
				. "<span class='currency'>$<span></div>";

			$content[$l][4] = "";
			}


		    $content[$l][3] = $col3[$l];

		    for ($c = 1; $c <= $max_col; $c++) // colonnes
			{


			echo "<div class='cell g" . $l . "_" . $c . " lin$l col$c' >" . $content[$l][$c] . "</div>";

			$css .= ".g" . $l . "_" . $c . "{grid-row: " . $l . " / " . ($l + 1) . ";   grid-column: " . $c . " / " . ($c
				+ 1) . ";}" . chr(13) . chr(10);
			}
		    }
		?>


	    </div> <!--grid -->

	</div><!--  overflow_y -->

	<div class="_notes p20 t16 m16 lgrey2"><?= ucfirst(__("dernière modification")) ?> : 12/04/2022 à 12h 52 min, Paris gmt+2</div>

	<div class="_notes p20 t16  m16  lgrey2"><?= __("Afin d'éviter les plaintes de clients inattentifs, les modifications de tarifs à la baisse sont immédiates, les modifications de tarifs à la hausse sont validées sous 6h.") ?></div>

	<div class=" conseils lgrey2 p25 t16 m16    fw500"><?= __("Conseils concernant vos tarifs") ?></div>


	<div class="_notes p20 t16  m16   lgrey2"><?= __("Les tarifs sont libres, mais nous vous conseillons de privilégier des tarifs progressifs en fonction de votre activité, de votre notoriété ou de la demande clients. Pour plus de conseils sur vos tarifs,")?> <a href="" class="lgrey2 underline"> <?=__("cliquez ici.") ?></a></div>

    </div><!-- cadre_table-->


<?php
echo $this->Form->end(array('label' => __('valider'), 'class' => 'btn xlarge h85b valider white  up_case',
    'div' => array('class' => 'div_btn_valider')));
?>




<?php
//echo $this->Frontblock->getRightSidebar();
?>



    <article class="bis">
	<h1 class="">  <?= __('Activer mes prestations complémentaires ') ?></h1>
<?= __("Activer ou désactiver ces fonctions sur le site en cochant la case prévue à cet effet et modifier la position de mes options sur le site.") ?>

    </article> 


    <div class="modif_position  first lgrey2 fw500 p22 t16 m14"><?= __('Modifier position en faisant glisser le classement 2,3,4 etc'); ?></div>
    
    
    <div class="cadre_video first">

	<div class="video_num_div">
	    <div class="video_num fw500 white2">1</div>
	    <div class="trait_fin"></div>
	    <div class="trait_fin2"></div>
	    <div class="trait_fin3"></div>
	</div>

	<div class="video_txt media_txt lgrey2 fw400">


	    <div class="media">
		<div class="btn_like white h85g pc"><?= __('modes de communication') ?> </div>
		<div class="btn_like white h85g tablet"><?= __('modes de<br/>communication') ?> </div>
	    </div>

	    <div class="txts  p22 t16 m14">
<?= __("Que vous utilisiez ou non les modes de communication téléphone, Chat, Webcam, SMS et Email, cette option sera toujours la première visible de vos clients.") ?>
	    </div>
	</div>

    </DIV>


    <ul id="div_seq" class="div_seq">

	<li class="cadre_video" >

	    <div class="video_num_div">
		<div class="video_num fw500 blue2">2</div>
		<div class="trait_fin"></div>
		<div class="trait_fin2"></div>
	    </div>

	    <div class="video_txt media_txt lgrey2 fw400">

		<div class="media" data-input="formation_video">
		    <div class="btn_like white h85g "><?= __('formation vidéo') ?> </div>

		    <label class="switch">
			<input type="checkbox" >
			<span class="slider round"></span>
		    </label>
		</div>

		<div class="txts  p22 t16 m14">
<?= __("Pour activer ce mode vous devez avoir renseigné un tarif et avoir ajouté des vidéos formation.") ?>
		</div>
	    </div>

	</li>
	
	
	
	
	

	<li class="cadre_video" >

	    <div class="video_num_div">
		<div class="video_num fw500 blue2">3</div>
		<div class="trait_fin"></div>
		<div class="trait_fin2"></div>
	    </div>

	    <div class="video_txt media_txt lgrey2 fw400">

		<div class="media" data-input="">
		    <div class="btn_like white h85g pc"><?= __('contenus à la demande') ?> </div>
		    <div class="btn_like white h85g tablet"><?= __('contenus à la<br/>demande') ?> </div>


		    <label class="switch">
			<input type="checkbox" >
			<span class="slider round"></span>
		    </label>
		</div>

		<div class="txts  p22 t16 m14">

		</div>
	    </div>

	    <div class="modif_position lgrey2  fw500 p22 t16 m14"><?= __('Modifier position en faisant glisser le classement 1,2,3,4 etc'); ?>
	    <div class="trait_fin3"></div>
	    </div>
	    
	    
	    
	    <ul id="div_sub_seq" class="div_sub_seq">


		<li class="cadre_video div_sub_seq_item">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">1</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
			
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="masterclass">
			    <div class="btn_like white h85g "><?= __('masterclass') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("Pour activer ce mode vous devez avoir renseigné un tarif et ajouté des dates de MasterClass.") ?>
			</div>
		    </div>

		</li>


		
		
		
		
		
		<li class="cadre_video div_sub_seq_item">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">2</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="photos_a_la_demande">
			    <div class="btn_like white h85g "><?= __('photo à la demande') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("2 options, tarif fixe, ou requête de paiement via laquelle le client vous sollicitera pour une demande spécifique et un tarif") ?>
			</div>
		    </div>

		</li>
		
		
		
		
		
		
		<li class="cadre_video div_sub_seq_item">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">3</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="videos_a_la_demande">
			    <div class="btn_like white h85g "><?= __('vidéo à la demande') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("2 options, tarif fixe, ou requête de paiement via laquelle le client vous sollicitera pour une demande spécifique et un tarif") ?>
			</div>
		    </div>

		</li>
		
		
		
		
		
		<li class="cadre_video div_sub_seq_item">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">4</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="pdf_document">
			    <div class="btn_like white h85g "><?= __('pdf-documents') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("Pour activer ce mode vous devez avoir renseigné un tarif et ajouté un document-Pdf à vendre.") ?>
			</div>
		    </div>

		</li>
		
		
		
		
		

	    </ul> <?php // fin div_sub_seq ?>


	    
	    
	</li>

	
	<li class="cadre_video">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">4</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="1_mois">
			    <div class="btn_like white h85g "><?= __('contenus privés') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("Pour activer ce mode vous devez avoir renseigné un tarif.") ?>
			</div>
		    </div>

		</li>
	
    </ul> <?php // fin div_seq ?>


    
    
<ul id="" class="div_seq pourboire">
 
 <li class="cadre_video">

		    <div class="video_num_div">
			<div class="video_num fw500 blue2">4</div>
			<div class="trait_fin"></div>
			<div class="trait_fin2"></div>
		    </div>

		    <div class="video_txt media_txt lgrey2 fw400">

			<div class="media" data-input="1_mois">
			    <div class="btn_like white h85g "><?= __('pourboire') ?> </div>

			    <label class="switch">
				<input type="checkbox" >
				<span class="slider round"></span>
			    </label>
			</div>

			<div class="txts  p22 t16 m14">
<?= __("En activant cette fonction vos clients  pourront vous envoyer des pourboires dont ils choisiront le montant") ?>
			</div>
		    </div>

		</li>

		
		 </ul>
    
    
    
    
    

    

</section>



<style>


    .rates-page .grid{
	display: grid;;
	grid-template-rows: calc(27px*var(--coef)) ;
	grid-gap: 0;
    }


<?= $css ?>
</style>

<script>
    window.onload = function ()
    {
	console.log("rates js START");
	
	 let _device_type =  device_type()

	
	input_width_fit_content(".col2 input")

        let conversion_ar = [];

    <?php
    foreach ($currencies as $key => $currency)
	{
	// echo '<p class="lh18-27 fw600"><span>'.$currency["Currency"]["code"].' ('.$currency["Currency"]["label"].')</span><span></span></p>';
	echo "conversion_ar['" . $currency["Currency"]["label"] . "']=" . $currency["Currency"]["amount"] . ";" . chr(13) . chr(10);
	}
    ?>
        let tx_conv;
        let currency = "$";


        /*
         $("body").click(function() {
         $(".cs-utc-list").css("max-height", "0")
         });
         */

	 /* CLICK ANYWHERE TO CLOSE */
	 $(window).click(function() {
	   
	     let img = $(".cs-utc-selector .cs-utc > img");
	      $(img).removeClass('active');
              //$(img).closest('.cs-dad-right').addClass('cs-layer');
	  });  
	  
	  
	  
        $('body').on('click', '.cs-utc-selector .cs-utc', function (e)
        {

            e.preventDefault();
	    e.stopPropagation();
            let img = $(this).find('> img');
            console.log("click img", img);
            if ($(img).hasClass('active'))
            {
                $(img).toggleClass('active');
                $(img).closest('.cs-dad-right').removeClass('cs-layer');
            } else
            {
                $(img).toggleClass('active');
                $(img).closest('.cs-dad-right').addClass('cs-layer');
            }
        });

        function convert_currency_to(amount, monnaie)
        {
	
	    if(amount=="")amount=0;
	    amount = parseFloat(amount);
            let prev_tx_conv = conversion_ar[currency]
            let _tx_conv = conversion_ar[monnaie] / prev_tx_conv
            return amount * _tx_conv;
        }

        $('body').on('click', '.cs-utc-selector .cs-utc .cs-utc-list > p',
                function (e)
                {
                    e.preventDefault();


                    let prev_tx_conv = conversion_ar[currency]
                    currency = $(this).data("currency")
                    tx_conv = conversion_ar[currency] / prev_tx_conv


		    
		    // CHANGE LE SYMBOLE EN HAUT DU COMPOSANT
                    let $parent = $(this).closest('.cs-utc-selector');
                    currency = $(this).data("currency");
		    
//                    let html = $(this).html();
                    if ($(this).hasClass('active'))
                        return false;
                    $parent.find('.cs-utc .cs-utc-list > p').removeClass(
                            'active');
                    $(this).addClass('active');
                    $(this).closest('.cs-utc').find('.currency_symbol').html(
                            currency);
		    /*
                    //$(this).closest('.cs-utc-selector .cs-utc').click();
		    */
		       
		    if(_device_type=="mobile" )  {	    

		    $('.cadre_table > .overflow .grid').animate({//lancement de l'animation de scroll
			scrollLeft: "+350"
			    }, {
			  queue: false,
			  duration: 200
			});
		    }
		   
		   convert_col4()
		   
                   

                });


        function convert_col4()
        {
            let cur_lin
            let new_val

          //  console.log("convert_col4 currency", currency, "tx_conv", tx_conv);

            for (let l = 0; l < <?= $max_line ?>; l++)
            {
		if( !$(".field" + l + "_2").hasClass("only_numbers")) continue;
		
                cur_lin = $(".field" + l + "_2").val()
		cur_lin = parseFloat(cur_lin)
                new_val = cur_lin * tx_conv;

                console.log("cur_lin", cur_lin, "new_val", new_val);
                $(".g" + l + "_4").html(new_val.toFixed(2)+currency)

            }
            //$(".grid .currency").html(currency)
	
		
	    
        }


        function ajust_col4(elemt)
        {
	    let val = $(elemt).val()

	    if(val=="")val=0;

            val = parseFloat(val)
	    
            let new_val = convert_currency_to(val, "€");
            let target = $(elemt).attr("target");
            $("." + target).html(new_val.toFixed(2) + "€")
        }


        $(".grid .col2 input").keyup(function (event)
        {
            ajust_col4(this)
        });


        function ajust_all_col4()
        {
            console.log("ajust_all_col4");
            for (let l = 0; l < <?= $max_line ?>; l++)
            {
                ajust_col4($(".field" + l + "_2"))
            }
        }

        ajust_all_col4()



        /* DRAG N DROP  */
	
	
	 $(".div_seq>.cadre_video >.video_num_div").mousedown(function() {
	
	     $(".div_sub_seq, .modif_position, .video_num_div").css("height","0px")
	     });
	
        $(".div_seq").sortable(
                {
//		    containment: "#div_seq", 
		    items: "> li",
		    cursor: "grab",
//		    tolerance: 'pointer',
//		     axis: "y",
		
		    start: dragging,
                    stop: stop_draggin		  
                }
        );
 
        $(".div_sub_seq").sortable(
                {
		    items: ".div_sub_seq_item",
		    containment: "#div_sub_seq", 
		    cursor: "grab",
//		    tolerance: 'pointer',
	
		  
		    start: dragging,
		    stop: stop_draggin,
//		    axis: "y",
		 
	
		  
	
                }
        );
	
	
	

	/* WHEN MODES GRABBED */
	function reorder_mods(eltm)
	{
	    console.log("***** reorder_mods");
	    let target = $(eltm).closest(".ui-sortable")

	    let inc = 1;
	    if($(target).hasClass('div_seq')) {
	    console.log("hasClass('div_seq')");
	    inc=2;
	    }
	    $(target).find( ">.cadre_video " ).each(function( index ) {

		     $( this ).find( ">.video_num_div > .video_num " ).text(index+inc);
		     console.log( index + ": " + $( this ).find(".btn").text(),  "",index+inc );
		     });
	}
	
	function dragging(event, ui)
	{
	     console.log("dragging");
//	     $(ui.item).addClass("draggin")
	     $(".rates-page").addClass("draggin")
	}


	function stop_draggin(event, ui)
	{
	    console.log("stop_draggin event",event, "ui.item", ui.item);
	     $(".rates-page").removeClass("draggin")
	    $(".div_sub_seq, .modif_position, .video_num_div").css("height","100%")
	    reorder_mods(ui.item)
	}
	
	/* CHECKBOXES */
	
        $(".cadre_video .media,  .div_pourboire .div_activer ").mousedown(function ()
        {
            console.log("CHECKBOXES click");
            event.stopPropagation();
	    let input = $(this).data("input")
	  
            let target = $(this).find("input")
            let btn = $(this).find(".btn_like")
	      console.log("input",input, "val", $("#"+input).val());
	    if($("#"+input).val()==0 || $(input).val()=="")
	    {
		   $("#modal-confirmation").modal();
		   return;
	    }
	  
	    
            $(target).click();

            if ($(this).hasClass("on"))
            {
                console.log("class on, added");
                $(this).removeClass('on')
                $(btn).removeClass('blue').addClass('white')
            } else
            {
                //console.log("No class on, removed");
                $(this).addClass('on')
                $(btn).removeClass('white').addClass('blue')
            }


        });



//////// CONVERTIT LES EVENTS TOUCH EN MOUSE POUR QUE LES JQUERY-UI SORTABLES MARCHENT

function touchHandler(e) {
    var touches = e.changedTouches;
    var first = touches[0];
    var type = "";
    console.log("e.type",e.type);

    switch(e.type) {
      case "touchstart":
        type = "mousedown";
        break;
      case "touchmove":
        type="mousemove";
        break;        
      case "touchend":
        type="mouseup";
        break;
      default:
        return;
    }
      
    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1, first.screenX, first.screenY, first.clientX, first.clientY, false, false, false, false, 0, null);

    first.target.dispatchEvent(simulatedEvent);
    e.preventDefault();
  }

  function init() {
   
    const sortables = document.getElementsByClassName('video_num_div');
    
    for (let i = 0; i < sortables.length; i++) {

    sortables[i].addEventListener("touchstart", touchHandler, true);
    sortables[i].addEventListener("touchstart", touchHandler, true);
    sortables[i].addEventListener("touchmove", touchHandler, true);
    sortables[i].addEventListener("touchend", touchHandler, true);
    sortables[i].addEventListener("touchcancel", touchHandler, true);   

}
  
    
    /* SO TABLET ON laisse les textes sans drag'n drop, car difficulté à manipuler la page*/ 
    

    if(_device_type=="mobile" || _device_type=="tablet")  return;

    
    const sortables2 = document.getElementsByClassName('txts');
    
    for (let i = 0; i < sortables2.length; i++) {

    sortables2[i].addEventListener("touchstart", touchHandler, true);
    sortables2[i].addEventListener("touchstart", touchHandler, true);
    sortables2[i].addEventListener("touchmove", touchHandler, true);
    sortables2[i].addEventListener("touchend", touchHandler, true);
    sortables2[i].addEventListener("touchcancel", touchHandler, true);   

}
    
    
  }
init()


    }
</script>


<?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js');   ?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand());   ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css');   ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>

<?php
$message = __("Vous devez sélectionner un client");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');
?>

<section class="request_payment-page page jswidth marg180">


    <article class="">
	<h1 class="">  <?= __('Mes requêtes paiement client') ?></h1>
<?= __("Via cette page vous avez la possibilité d'envoyer un lien de paiement à vos clients, sélectionnez ce dernier dans la liste ci-dessous, indiquez un montant et validez, le client recevra un lien de paiement.") ?>
    </article>

    <h3 class="p30 t24 m18">  <?= __('Mes dernières requêtes clients') ?></h3>

    <div class="div_chevron top txt_cent"><img class="chevron " src="/theme/black_blue/img/menu/chevron.svg"></div>
    <div class="div_requests">
	<div id="div_requests_content">
	    <DIV class="div_req_rep lu">
		<div class="request_bar  bordark" data-id="1">
		    <img class="rounded"  src="/theme/black_blue/img/effacer/portrait1.jpg" />
		    <span><?= __('message de') ?>  Lily Potter</span>
		</div>

		<div class="message_bar bordark close" id="msg1">
		    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
		    <div class="btn_lu p22 t18 m16" ><?= __('marquer comme lu') ?></div>
		</div>
	    </DIV>

	    <DIV class="div_req_rep ">
		<div class="request_bar closed bordark" data-id="2">
		    <img class="rounded"  src="/theme/black_blue/img/effacer/portrait2.jpg" />
		    <span><?= __('message de') ?>  Lily Potter</span>
		</div>

		<div class="message_bar bordark close" id="msg2">
		    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		    <div class="btn_lu p22 t18 m16" ><?= __('marquer comme lu') ?></div>
		</div>
	    </DIV>

	    <DIV class="div_req_rep ">
		<div class="request_bar closed bordark" data-id="3">
		    <img class="rounded"  src="/theme/black_blue/img/effacer/portrait3.jpg" />
		    <span><?= __('message de') ?>  Lily Potter</span>
		</div>	


		<div class="message_bar bordark close" id="msg3">
		    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
		    <div class="btn_lu p22 t18 m16" ><?= __('marquer comme lu') ?></div>
		</div>
	    </DIV>

	    <DIV class="div_req_rep ">
		<div class="request_bar closed bordark" data-id="4">
		    <img class="rounded"  src="/theme/black_blue/img/effacer/portrait4.jpg" />
		    <span><?= __('message de') ?>  Lily Potter</span>
		</div>	


		<div class="message_bar bordark close" id="msg4">
		    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
		    <div class="btn_lu p22 t18 m16" ><?= __('marquer comme lu') ?></div>
		</div>
	    </DIV>
	</div>
    </div>

    <div class="div_chevron bas txt_cent"><img class="chevron" src="/theme/black_blue/img/menu/chevron.svg"></div>






    <h3 class="p30 t24 m18">  <?= __('Envoyez une requête ou lien de paiement par email') ?></h3>
    <span class="lgrey2 p22 t18 m16"><?= __('Les clients n\'auront jamais vos emails ou coordonnées personnelles.') ?></span>


    <div class="div_send bordark">


	<div class="top_send bordark">
	    <div class="title b p24 t18 m16"><?= __("Bonjour") ?>,</div>
	    <span class="txt lgrey2 p22 t16 m14"><?= __("Récapitulez simplement l'objet de cette requête de paiement pour que votre client en comprenne l'origine.") ?></span>	
	</div>

	<textarea id="send_msg" placeholder="<?= __("Taper votre message") ?>..." class="lh20-30a lgrey2 "></textarea>
	

    </div>



    <div class="div_signature ">

	<img src="https://picsum.photos/200/300" class="rounded">

	<textarea id="signature" class="signature bordark lgrey2 p20 t18 m16" placeholder="<?= __("Votre Pseudo LiviTalk ou signature/phrase personnalisée...") ?>..." class="lh20-30a lgrey2 "></textarea>


    </div>


    <div class="btns">
	<a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2" title="agent"> 
	    <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
	    <form> 
		<input class="lh22-33 lgrey2" type="text" placeholder="Customer">
	    </form> </a>
	<div class="btn chercher h70 lh24-28 p20spe  blue2 up_case" title="<?= __("chercher") ?>"><?= __("chercher") ?></div>

    </div>


    <div class="div_montant">

	<div class="left bordark cadre_table">
	    <table class=" stries" > 

		<thead class=""> 
		    <tr>  
			<th class="client"><?php echo __('Client'); ?></th> 
			<th class="check"></th> 
		    </tr> 
		</thead> 
		<tbody>

<?php for ($i = 0; $i < 15; $i++)
    {
    ?>

    		    <tr> 
    			<td class="client">
    			    <label for="<?= __("client") . $i ?>">
    				Lorem ipsum
    			    </label>
    			</td> 
    			<td class="check"> 
    			    <input id="<?= __("client") . $i ?>" class="square_radio" type="checkbox" name="checkbox">
    			</td> 
    		    </tr> 

<?php } ?>
		</tbody>   
	    </table> 
	</div>



	<div class="right ">
	    <div class="montant bordark cs-dad-right ">

		<span class="label_montant p26 t22 m18"><?= __("montant") ?></span>

		<div class="cs-utc-selector"> 
                    <div class="cs-utc lh16-24 blue2 fw500">

                        <span class="cs-current-utc p24 t16"><?= __("modifier ma devise") ?> (€)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">
                        <div class="cs-utc-list cs-utc-type-2">
                            <p class="lh18-27 fw600"><span>USD ($)</span><span></span></p>
                            <p class="lh18-27 fw600"><span>EUR (€)</span><span></span></p>
                            <p class="lh18-27 fw600"><span>YEN (&yen;)</span><span></span></p>
                            <p class="lh18-27 fw600"><span>POUND (&pound;)</span><span></span></p>
                        </div>
                    </div>
                </div>		     

		<div class="btn_like btn_montant h60 lh24-28 p20spe  blue2 up_case" title="" ><input type="text" id="btn_montant" class="blue2 p25 t18 m18" min="0" value="0.22"  >$</div>
		<div class="devise orange2 p25 t18 m18" title="" >0,92€</div>

	    </div>

	    <DIV class="div_btn_envoyer">
		<div class="btn envoyer h85 lh24-28 p20spe  blue2 up_case" title="<?= __("envoyer") ?>"><?= __("envoyer") ?></div>
	    </DIV>




	</div>

    </div>

    <h3 class="titre_table p30 t24 m18">  <?= __('Requêtes de paiement envoyées') ?></h3>
    <div class="cadre_table ">	
	<div class="btns"> <a id="btn_datepicker" class="btn spe1  date transparent daterange b " title="dates"><img src="/theme/black_blue/img/calendrier.svg"> 01/04/22 - 24/04/22</a></div>
<?php
$appointments = [];

$appointment = [];
$appointment['date'] = "24/04/22 15:11:25 ";
$appointment['Agent']["pseudo"] = "Lorem Ipsum";

$appointment['duree'] = "5%";

$appointment['statut'] = [];

$mois = [1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3];

$montants = ["30,00$", "50,00$", "75,00$", "Via requête Paiement ", "99,00$", "30,00$",
    "50,00$", "75,00$", "Via requête Paiement ", "99,00$", "50,00$", "75,00$",];
$duree = ["15", "30", "30", "60", "15", "15", "60", "30", "30", "60", "30", "30"];

$statuts = ["Validé", "Annulé", "En cours", "Annulé", "En cours", "Annulé", "Annulé",
    "Validé", "Validé", "Annulé", "Annulé"];

$fin = ["", "", "Rembourser", "", "Rembourser", "", "", "", "", "", ""];

$statuts_color = ["blue2", "orange2", "", "orange2", "", "orange2", "orange2", "blue2",
    "blue2", "orange2", "orange2"];

$k = 0;
for ($j = 1; $j <= 3; $j++)
    {
    for ($i = 1; $i <= 6; $i++)
	{
	$appointment['duree'] = $duree[$k] . " min";
	$appointment['prestation'] = $prestations[$k];
	$appointment['statut']["label"] = $statuts[$k];
	$appointment['mois'] = $mois[$k];
	$appointment['fin'] = $fin[$k];

	$appointment['statut']["color"] = $statuts_color[$k];
	$k++;
	if ($k > 10) $k = 0;


	$appointments[] = $appointment;
	}
    }
?>
	<?php if (empty($appointments)) : ?>
    	<div class="txt_cent">
	    <?php echo __('Aucun rendez-vous'); ?>	</div>
	    <?php else : ?>
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  

    			<th class="client"><?php echo __('client'); ?></th> 

    			<th class="date"><?php echo __('date'); ?></th> 
    			<th class="montant"><?php echo __('montant'); ?></th>

    			<th class="statu"><?php echo __('statut'); ?></th> 


    		    </tr> 
    		</thead> 
    		<tbody>

    <?php foreach ($appointments as $appointment) : ?>
			    <tr> 

				<td class="client"> 
	<?php echo $appointment['Agent']["pseudo"];
	?> 
				</td> 


				<td class="date"><?= $appointment['date']; ?></td> 
				<td class="duree">14,90$</td> 

				<td class="statut <?php echo $appointment['statut']['color']; ?>"><?php echo $appointment['statut']['label']; ?></td> 

			    </tr> 
    <?php endforeach; ?>





    		</tbody>
<?php endif; ?> 
	    </table> 

	</div>

    </div>

    <input type="hidden" id="daterange" class="form-control"  >
</section>    


<style>



    #modal-confirmation .modal-content{
	width: calc(600px*var(--coef));
	height: 200px;
    }

    @media only screen   and (max-width : 767px)
    {

	#modal-confirmation .modal-content{
	    width: calc(347px*var(--coef));
	    height:  calc(291px*var(--coef));
	}


    }


</style>


<script>
    window.onload = function ()
    {

         $(window).click(function() {
	    console.log("$(window).click");
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

        $('body').on('click', '.cs-utc-selector .cs-utc .cs-utc-list > p',
                function (e)
                {
                    e.preventDefault();
                    let $parent = $(this).closest('.cs-utc-selector');
                    let html = $(this).html();
                    if ($(this).hasClass('active'))      return false;
                    $parent.find('.cs-utc .cs-utc-list > p').removeClass(
                            'active');
                    $(this).addClass('active');
                    $(this).closest('.cs-utc').find('.cs-current-utc').html(
                            html);
                    $(this).closest('.cs-utc').find('> img').click();
                });



        /////////// DATE PICKER //////////

        //var btn_datepicker = document.getElementById('.cs-selecteur-de-criteres-container .cs-sdc-date');
        var btn_datepicker = $("#btn_datepicker");

        console.log("btn_datepicker", btn_datepicker);

        btn_datepicker.click(function ()
        {
            duDatepicker('#daterange', 'show')
        });


        duDatepicker('#daterange', {
            range: true,
            events: {
                onRangeFormat: function (from, to)
                {
                    var fromFormat = 'mmmm d, yyyy', toFormat = 'mmmm d, yyyy';

                    console.log("from", from, "to", to);

                    if (from.getMonth() === to.getMonth() && from.getFullYear()
                            === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'd, yyyy'
                    } else if (from.getFullYear() === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'mmmm d, yyyy'
                    }

                    return from.getTime() === to.getTime() ?
                            this.formatDate(from, 'mmmm d, yyyy') :
                            [this.formatDate(from, fromFormat),
                                this.formatDate(to, toFormat)].join('-');
                }
            }
        });



        ///////////  request_bar //////////
        $(".message_bar").hide(0)


        $(".request_bar").click(function ()
        {

            let target_id = $(this).data("id");
            let target = $("#msg" + target_id)


            console.log("target", "msg" + target_id);

            if ($(target).hasClass("close"))
            {
                // if(!$(this).hasClass("underline") ) // pour le btn du bas en view profile screen
                $(target).addClass("open").removeClass("close")
                $(target).slideDown('fast')
            } else
            {
                $(target).addClass("close").removeClass("open")
                $(target).slideUp('fast')
            }
        });


        ///////////  CHEVRON HAUT / BAS //////////
        var scroll_step = 100;

let container = $(".div_requests")

	 $(".div_chevron.bas").click(function ()
        {
	       var scrollTo = $(".div_req_rep:last-child");
	   
    var position = scrollTo.offset().top - container.offset().top + container.scrollTop();
	container.animate({
		scrollTop: position
	});
	})
	
	
	 $(".div_chevron.top").click(function ()
        {
	 var scrollTo = $(".div_req_rep:first-child");
	var position = scrollTo.offset().top - container.offset().top  + container.scrollTop();
	container.animate({
			scrollTop: position
	});
	})


        ///////////  MARQUER COMME LU //////////

        $(".btn_lu").click(function ()
        {
            $(this).parentsUntil(".div_requests_content", ".div_req_rep").
                    addClass("lu");
            ;
        });

        ///////////  send message //////////

        $(".btn.envoyer").click(function ()
        {
            $("#modal-confirmation").modal();
        });



	///////////////    ////////////

	input_width_fit_content('#btn_montant')
	    
	    
	    
	    
	    }
</script>
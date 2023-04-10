<style>


    /* POPUPS */

    #modal-consult-chat{
	width: calc(995px*var(--coef));
	height: calc(984px*var(--coef));

    }


    #modal-consult-chat  .grey3{
	color:#B3B3B3;
    }


    #modal-consult-chat .modal-content{
	/*width:  calc(100% - 50px*var(--coef) );*/
	height: calc(934px*var(--coef));
	padding:calc(16px*var(--coef)) calc(25px*var(--coef)) calc(30px*var(--coef)) calc(25px*var(--coef));
	align-items: start;
	text-align:left;
	display: flex;

	flex-direction: column;
	justify-content: space-around;
	
    }

    #modal-consult-chat .div_btn,
    #modal-consult-chat .demarrer{
	width:  calc(100% - 50px*var(--coef) );
	text-align: center;
    }




    #modal-consult-chat  .tarifs_div{
	width:100%;
	display: inline-flex;
	justify-content: space-between;
	gap:calc(20px*var(--coef));
	align-items: flex-end;
    }

    #modal-consult-chat .tarif {
	width: calc(173px*var(--coef));
	height: calc(60px*var(--coef));
	background-color: var(--grey);
	border-radius: calc(10px*var(--coef));
	font-weight: 600;
	font-size: calc(25px*var(--coef));
	cursor:pointer;
	color:var(--blue);
    }


    #modal-consult-chat  .populaire{
	text-align: center;
	position: relative;
    }

    #modal-consult-chat  .populaire > div{
	background-color: var(--blue);
	color: var(--main-bg-color);
	text-align: center;
	margin-left: auto;
	margin-right: auto;
    }

    #modal-consult-chat .le_plus_pop{
	width: calc(118px*var(--coef));
	height: calc(21px*var(--coef));
	font-variant: all-small-caps;
	border-top-left-radius: calc(5px*var(--coef));
	border-top-right-radius: calc(5px*var(--coef));
    }



    #modal-consult-chat  .cs-utc > i, .cs-utc > img {
	margin-left:0;
	font-size: calc(20px*var(--coef));
	width: calc(20px*var(--coef));
    }


    #modal-consult-chat .cs-current-utc{
	cursor: pointer;
    }


    #modal-consult-chat .ou_bien{

	width:calc(150px*var(--coef));
	background: var(--main-bg-color);
	top:calc(13px*var(--coef));
	position: relative;
	text-align:center;
	margin-left: auto;
	margin-right: auto;
	height:calc(26px*var(--coef));
	line-height: calc(26px*var(--coef));
    }

    #modal-consult-chat  .div_blue_line{
	border-bottom: 1px var(--blue) dashed;
	position: relative;
	width: 100%;
	height:calc(26px*var(--coef));
	margin-bottom:calc(10px*var(--coef))


    }


    #modal-consult-chat  .div_a_la_mn{
	width:calc(100% - 34px*var(--coef));
	display: inline-flex;
	justify-content: space-between;
	align-items: center;
	background: var(--grey);
	height:calc(60px*var(--coef));
	padding:calc(17px*var(--coef));
	border-radius: calc(10px*var(--coef));
    }

    #modal-consult-chat  .div_a_la_mn > .blue2{
	text-align: right;
    }

    #modal-consult-chat .code_promo_rect{
	width:calc(183px*var(--coef));
	height:calc(60px*var(--coef));
	background: #EAEAEA;
	border-radius: calc(10px*var(--coef));
    }

    #modal-consult-chat  .code_promo{
	display: inline-flex;
	align-items: center;
	gap:calc(20px*var(--coef));
    } 


    #modal-consult-chat  input[type="checkbox"].square_radio{

	margin-top:calc(20px*var(--coef));
	flex: 0 0 auto !important;
    }

    #modal-consult-chat .cs-utc-selector .cs-utc {
	margin-top: 0;
	max-width: calc(270px*var(--coef));
    }

    #modal-consult-chat .cs-utc-selector.bis .cs-utc {
	margin-top: 0;
    }

    #modal-consult-chat  .par_tel{
	position: relative;
	top:  calc(-10px*var(--coef));
    }


    #modal-consult-chat label{
	display: inline-flex;
	align-items: last baseline;
	gap:  calc(15px*var(--coef));
	cursor:pointer;
    }

    #modal-consult-chat .btn.validate {
	margin-top: calc(20px*var(--coef));
	font-variant: small-caps;
	width: calc(303px*var(--coef));
	height: calc(52px*var(--coef));
	font-variant: small-caps;

    }


    /* tablets ----------- */
    @media only screen and (max-width : 1024px) {

	#modal-consult-chat{
	    width: calc(629px*var(--coef));
	    height: calc(1106px*var(--coef));

	}

	#modal-consult-chat .modal-content{
	    height: calc(1056px*var(--coef));
	    align-items: start;
	    text-align:left;
	}





	#modal-consult-chat  .tarifs_div{
	    width:70%;
	    justify-content: center;
	    align-items: flex-end;
	    flex-wrap: wrap;
	    margin-left: auto;
	    margin-right: auto;
	    
	}

	#modal-consult-chat .populaire{
	      width:100%;
	}

	#modal-consult-chat  .cs-utc > i, .cs-utc > img {
	    margin-left:0;
	    font-size: calc(15px*var(--coef));
	    width: calc(20px*var(--coef));
	}


	#modal-consult-chat .cs-current-utc{
	    cursor: pointer;
	}


	#modal-consult-chat .ou_bien{

	    display:none;
	}


	#modal-consult-chat  .div_a_la_mn{
	    width:calc(100% - 34px*var(--coef));
	    display: inline-flex;
	    justify-content: space-between;
	    align-items: center;
	    background: var(--grey);
	    height:calc(60px*var(--coef));
	    padding:calc(17px*var(--coef));
	    border-radius: calc(10px*var(--coef));
	}


	#modal-consult-chat .code_promo_rect{
	    width:calc(183px*var(--coef));
	    height:calc(60px*var(--coef));
	    background: #EAEAEA;
	    border-radius: calc(10px*var(--coef));
	}

	#modal-consult-chat  .code_promo{
	    display: inline-flex;
	    align-items: center;
	    gap:calc(20px*var(--coef));
	}


	#modal-consult-chat  input[type="checkbox"].square_radio {
	
	    margin-top:calc(20px*var(--coef));
	    width: calc(23px*var(--coef));
	    height: calc(23px*var(--coef));
	    
	}

	#modal-consult-chat .cs-utc-selector .cs-utc {
	    width: calc(190px*var(--coef));
	  
	}

	#modal-consult-chat .cs-utc-selector.bis .cs-utc {
	    margin-top: 0;
	  
	}

	#modal-consult-chat  .par_tel{
	    position: relative;
	    top:  calc(-10px*var(--coef));
	}




	#modal-consult-chat .btn.validate {
	    margin-top: calc(20px*var(--coef));
	    font-variant: small-caps;
	    width: calc(303px*var(--coef));
	    height: calc(52px*var(--coef));
	    font-variant: small-caps;

	}



    }



    /* mobile ----------- */
    @media only screen   and (max-width : 767px)
    {

	#modal-consult-chat{
	    width: calc(347px*var(--coef));
	    height: calc(1250px*var(--coef));
	    width:  calc(100% - 30px*var(--coef) );

	}

	#modal-consult-chat .modal-content{
	    height: calc(1200px*var(--coef));
	    padding:calc(16px*var(--coef)) calc(15px*var(--coef)) calc(30px*var(--coef)) calc(15px*var(--coef));
	    width:  calc(100% - 30px*var(--coef) );
	}

	#modal-consult-chat .cs-utc-selector.prim{
	    margin-top: calc(36px*var(--coef));
	}

	#modal-consult-chat  .tarifs_div{
	    width:100%;
	}

	#modal-consult-chat  .div_a_la_mn{
	    width:calc(100% - 45px*var(--coef));
	    justify-content: space-between;
	    padding:calc(11px*var(--coef)) calc(20px*var(--coef)) calc(11px*var(--coef)) calc(25px*var(--coef));
	    gap:calc(10px*var(--coef));
	    margin-top:calc(20px*var(--coef)); 

	}

	#modal-consult-chat  .div_a_la_mn > div{
	      width: auto;
	}

	#modal-consult-chat .code_promo_rect{
	    width:calc(163px*var(--coef));
	    height:calc(60px*var(--coef));
	    background: #EAEAEA;
	    border-radius: calc(10px*var(--coef));
	}

	#modal-consult-chat  .code_promo{
	    display: inline-flex;
	    align-items: center;
	    gap:calc(20px*var(--coef));
	    margin-bottom: calc(15px*var(--coef));
	}

	#modal-consult-chat  .code_promo2{
	    white-space: nowrap;
	}
	
	
	#modal-consult-chat label {
	    align-items: start; 
	    margin-bottom:calc(20px*var(--coef));
	}
	
	#modal-consult-chat  input[type="checkbox"].square_radio {
	
	    margin-top:calc(20px*var(--coef));
	    width: calc(23px*var(--coef));
	    height: calc(23px*var(--coef));
	    
	}
	
	#modal-consult-chat .cs-utc-selector{
	    margin-bottom: calc(20px*var(--coef)); 
	}

	#modal-consult-chat .cs-utc-selector .cs-utc {
	    width: calc(190px*var(--coef));
	  
	} 

	#modal-consult-chat .cs-utc-selector.bis .cs-utc {
	    margin-top: calc(20px*var(--coef));
	}

	#modal-consult-chat  .par_tel{
	    position: relative;
	    top: 0;
	}

	#modal-consult-chat .div_btn{
	      width:  100%;
	}


	#modal-consult-chat .btn.validate {
	   
	    width: calc(240px*var(--coef));
	    height: calc(52px*var(--coef));

	    padding:0;

	}
	
	
	#modal-consult-chat  label > span{
	    position: relative;
	    top:calc(17px*var(--coef));
	}

    }



</style>

<div class="modal  fade" id="modal-consult-chat"  role="dialog" >

    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>

    <div class="modal-content">


	<div class="demarrer fw400 p28 m18">
	    <?= (__("Démarrer ma consultation avec", null, true)) . "<br/> <span class='fw600'>" . $User['pseudo'] . "</span>"; ?>
	</div>	


	<div>
	    <div class="fw600 p22 m18">
		<?= (__("crédits sur mon compte", null, true)) . " : <span class='blue2'>0,00$</span>"; ?> 
	    </div>	
	    <div class="fw300 p18 m14 grey3">
		<?= (__("vos crédits seront valables avec tous les LiviMasters", null,
			true)); ?> 
	    </div>	
	</div>


	<div>
	    <div class="fw500 p25 m18">
<?= (__("recharger mon compte", null, true)); ?> 
	    </div>	
	    <div class="fw300 p18 m14 grey3">
<?= (__("une fois mon crédit épuisé, l’appel est coupé.", null, true)); ?> 
	    </div>	
	</div>




	<div class="tarifs_div">
	    <div class="tarif  btn_like">29,99$</div>
	    <div class="tarif  btn_like">49,99$</div>
	    <div class="populaire">
		<div class="le_plus_pop p16 fw300">le + populaire</div>
		<div class="tarif blue  btn_like">99,99$</div>
	    </div>
	    <br/>  <div class="tarif btn_like">199,99$</div>
	    <div class="tarif btn_like">299,99$</div>

	</div>



	<div class="cs-utc-selector prim" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p18 t15">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

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

	<div class="div_blue_line">
	    <div class="ou_bien fw400 p22 grey3"><?= __("ou bien") ?></div>
	</div>


	<div class="fw500 p25 t22 m18">
<?= (__("Payer à la minute", null, true)); ?> 
	</div>	

	<div class="div_a_la_mn">
	    <div class="fw400 p22 t18 m16">
<?= (__("Je ne paie que ce que je consomme", null, true)); ?> 
	    </div>
	    <div class="fw600 blue2 p25 t22 m16 ">
		<span class="montant">2,99$</span>/<?= (__("min", null, false)); ?>
	    </div>	
	</div>

	<div class="par_tel fw300 p18 t16 m14 grey3">
<?= (__("par téléphone, chat ou webcam")); ?>  
	</div>	




	<div class="cs-utc-selector bis"  data-tx_conv=""> 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p18 t15">

<?= __("modifier ma devise") ?> (<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

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


	<div class="div_blue_line"></div>


	<div class="code_promo">
	    <div class="code_promo2 fw500 p25 m18">
	    <?= (__("Code Promo", null, true)); ?> 
	    </div>	

	    <div class="code_promo_rect"></div>

	</div>



	<label> <input class="square_radio" type="checkbox" name="checkbox"> <span class="fw400 p18 t16 ">
<?= (__("j’ai lu et j’accepte les Conditions Générales de Services ", null,
	true)); ?>
	</label>
	<label> <input class="square_radio" type="checkbox" name="checkbox"> <span class="fw400 p18 t16">
<?= (__("J’accepte de débuter la prestation immédiatement après la validation de ma commande et je renonce expressément à mon droit de rétractation",
	null, true)); ?>
	</label>


	<div class="div_btn">
	    <div class=" btn validate white up_case fw600 p24 t24 m18" onclick="dispatch_event()" href="#close-modal" rel="modal:close">
<?= __('valider ma sélection', null, true) ?>  
	    </div>
	</div>










    </div>
</div>   




<script>

    let conversion_ar_chat = [];
<?php
foreach ($currencies as $key => $currency)
    {
    // echo '<p class="lh18-27 fw600"><span>'.$currency["Currency"]["code"].' ('.$currency["Currency"]["label"].')</span><span></span></p>';
    echo "conversion_ar_chat['" . $currency["Currency"]["label"] . "']=" . $currency["Currency"]["amount"] . ";" . chr(13) . chr(10);
    }
?>



    /* AFFICHE LES LISTE DEROULANTE OPTIONS currencies  */
    $('body').on('click', '#modal-consult-chat .cs-utc-selector .cs-utc',
            function (e)
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




    /* CHOIX D UNE AUTRE CURRENCY */
    $('body').on('mousedown',
            '#modal-consult-chat .cs-utc-selector .cs-utc .cs-utc-list > p',
            function (e)
            {
                e.preventDefault();
		e.stopPropagation();
                let $parent = $(this).closest('.cs-utc-selector');
                console.log(" $parent", $parent);
                let prev_currency = $parent.find('.currency_symbol').html()
                console.log("prev_currency", prev_currency);
                let prev_tx_conv = conversion_ar_chat[prev_currency]
                //   console.log("prev_currency",prev_currency);
                //   console.log("prev_tx_conv",prev_tx_conv);

                let currency = $(this).data("currency")

                //   console.log("currency",currency);
                //   console.log("tx_conv",conversion_ar_chat[currency]);

                let tx_conv = conversion_ar_chat[currency] / prev_tx_conv
                $parent.data("tx_conv", tx_conv)
                console.log("tx_conv", tx_conv);


                // CHANGE LE SYMBOLE EN HAUT DU COMPOSANT
                if ($(this).hasClass('active'))
                    return false;
                $parent.find('.cs-utc .cs-utc-list > p').removeClass('active');
                $(this).addClass('active');
                $(this).closest('.cs-utc').find('.currency_symbol').html(
                        currency);

                // convert_col4()
            });

    /* click sur 1er convertisseur */
        
	/* CLICK ANYWHERE TO CLOSE */
	
	$(window).mouseup(function() {

	    let img = $('#modal-consult-chat .cs-utc-selector .cs-utc > img');
	    console.log("click img", img);
	    $(img).removeClass('active')
	});  
	
	


    $('body').on('mouseup',
            '#modal-consult-chat .cs-utc-selector.prim .cs-utc .cs-utc-list > p',
            function (e)
            {
                e.preventDefault();
		e.stopPropagation();
                change_currency_bloc1()
            });

    /* click sur 2e convertisseur */
    $('body').on('mouseup',
            '#modal-consult-chat .cs-utc-selector.bis .cs-utc .cs-utc-list > p',
            function (e)
            {
                e.preventDefault();
		e.stopPropagation();
		change_currency_bloc2()
            });


    function change_currency_bloc1()
    {

        let $target = $("#modal-consult-chat .cs-utc-selector.prim")
        let currency = $target.find('.currency_symbol').html()
        //  console.log("change_currency_bloc1 currency",currency);
        let tx_conv = $target.data("tx_conv")
        console.log("tx_conv", tx_conv);
        let val
        let new_val;

        $("#modal-consult-chat .tarifs_div .tarif ").each(function ()
        {
            val = $(this).html();
            val = val.replace(',', '.')
            val = parseFloat(val);
            new_val = val * tx_conv;
            new_val = new_val.toFixed(2)
            new_val = new_val + "";
            new_val = new_val.replace('.', ',')
            $(this).html(new_val + currency)
        });
    }

    function change_currency_bloc2()
    {

        console.log("change_currency_bloc2");
        let $target = $("#modal-consult-chat .cs-utc-selector.bis")
        let currency = $target.find('.currency_symbol').html()

        let tx_conv = $target.data("tx_conv")
        console.log("tx_conv", tx_conv);

        let val
        let new_val;

        $("#modal-consult-chat .div_a_la_mn .montant").each(function ()
        {
            val = $(this).html();
            console.log("val", val);
            val = val.replace(',', '.')
            val = parseFloat(val);

            new_val = val * tx_conv;
            console.log(val, new_val);
            new_val = new_val.toFixed(2)
            new_val = new_val + "";
            new_val = new_val.replace('.', ',')
            $(this).html(new_val + currency)
        });
    }

    /* FIN CONVERTISSEURS */

</script>

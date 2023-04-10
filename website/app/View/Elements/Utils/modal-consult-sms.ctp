<style>


    /* POPUPS */
    

    #modal-consult-sms{
	width: calc(995px*var(--coef));
	height: calc(754px*var(--coef));

    }


    #modal-consult-sms  .grey3{
	color:#B3B3B3;
    }


    #modal-consult-sms .modal-content{
	 display: flex;
	/*width:  calc(100% - 50px*var(--coef) );*/
	height: calc(695px*var(--coef));
	padding:calc(16px*var(--coef)) calc(25px*var(--coef)) calc(30px*var(--coef)) calc(25px*var(--coef));
	align-items: start;
	text-align:left;
   
	flex-direction: column;
	justify-content: space-around;
   

 
    
}  
    

    #modal-consult-sms .div_btn,
    #modal-consult-sms .demarrer{
	width:  calc(100% - 50px*var(--coef) );
	text-align: center;
    }




    #modal-consult-sms  .tarifs_div{
	width:calc(550px*var(--coef));
	display: inline-flex;
	justify-content: space-between;
	gap:calc(60px*var(--coef));
	align-items: flex-end;
	margin-left: auto;
	margin-right: auto;
	margin-top:calc(20px*var(--coef));
    }

    #modal-consult-sms  .tarifs_div > div {
	text-align: center;
    }
    
    #modal-consult-sms .tarif {
	width: calc(173px*var(--coef));
	height: calc(60px*var(--coef));
	background-color: var(--grey);
	border-radius: calc(10px*var(--coef));
	font-weight: 500;
	font-size: calc(25px*var(--coef));
	cursor:pointer;
	color:var(--blue2);
    }

      #modal-consult-sms .nbre_sms  {
	font-weight: 400;
	font-size: calc(22px*var(--coef));
    }

    #modal-consult-sms  .populaire{
	text-align: center;
	position: relative;
    }

    #modal-consult-sms  .populaire > div{
	background-color: var(--blue);
	color: var(--main-bg-color);
	text-align: center;
	margin-left: auto;
	margin-right: auto;
    }

    #modal-consult-sms .le_plus_pop{
	width: calc(118px*var(--coef));
	height: calc(21px*var(--coef));
	font-variant: all-small-caps;
	border-top-left-radius: calc(5px*var(--coef));
	border-top-right-radius: calc(5px*var(--coef));
    }



    #modal-consult-sms  .cs-utc > i, .cs-utc > img {
	margin-left:0;
	font-size: calc(20px*var(--coef));
	width: calc(20px*var(--coef));
    }


    #modal-consult-sms .cs-current-utc{
	cursor: pointer;
    }



    #modal-consult-sms  .div_blue_line{
	border-bottom: 1px var(--blue) dashed;
	position: relative;
	width: 100%;
	height:calc(26px*var(--coef));
	margin-bottom:calc(10px*var(--coef))
    }

    
    
    
    #modal-consult-sms  .div_a_la_mn{
	width:calc(100% - 34px*var(--coef));
	display: inline-flex;
	justify-content: space-between;
	align-items: center;
	background: var(--grey);
	height:calc(60px*var(--coef));
	padding:calc(17px*var(--coef));
	border-radius: calc(10px*var(--coef));
    }

    #modal-consult-sms  .div_a_la_mn > .blue2{
	text-align: right;
    }


    #modal-consult-sms .code_promo_rect{
	width:calc(183px*var(--coef));
	height:calc(60px*var(--coef));
	background: #EAEAEA;
	border-radius: calc(10px*var(--coef));
    }

    #modal-consult-sms  .code_promo{
	display: inline-flex;
	align-items: center;
	gap:calc(20px*var(--coef));
    }


    #modal-consult-sms  input[type="checkbox"].square_radio{

	margin-top:calc(20px*var(--coef));
	flex: 0 0 auto !important;
    }

    #modal-consult-sms .cs-utc-selector .cs-utc {
	margin-top: 0;
	max-width: calc(230px*var(--coef));
    }

    #modal-consult-sms label{
	display: inline-flex;
	align-items: last baseline;
	gap:  calc(15px*var(--coef));
	cursor:pointer;
    }

    #modal-consult-sms .btn.validate {
	margin-top: calc(20px*var(--coef));
	font-variant: small-caps;
	width: calc(303px*var(--coef));
	height: calc(52px*var(--coef));
	font-variant: small-caps;

    }


    /* tablets ----------- */
    @media only screen and (max-width : 1024px) 
    {
	
    #modal-consult-sms  .tarifs_div{
	width:calc(550px*var(--coef));

	gap:calc(20px*var(--coef));

    }

    
    #modal-consult-sms .tarif {
	width: calc(147px*var(--coef));
	height: calc(51px*var(--coef));
	color:var(--blue);
	font-weight: 600;
	font-size: calc(23px*var(--coef));

    }

      #modal-consult-sms .nbre_sms  {
	font-size: calc(20px*var(--coef));
    }


    #modal-consult-sms .le_plus_pop{
	width: calc(100px*var(--coef));
	height: calc(21px*var(--coef));
    }



    #modal-consult-sms  .cs-utc > i, .cs-utc > img {
	margin-left:0;
	font-size: calc(15px*var(--coef));
	width: calc(20px*var(--coef));
    }




    #modal-consult-sms  .div_blue_line{
	margin-bottom:calc(20px*var(--coef))
    }


    	#modal-consult-sms  .div_a_la_mn{
	    width:calc(100% - 34px*var(--coef));
	    display: inline-flex;
	    justify-content: space-between;
	    align-items: center;
	    background: var(--grey);
	    height:calc(60px*var(--coef));
	    padding:calc(17px*var(--coef));
	    border-radius: calc(10px*var(--coef));
	}


    #modal-consult-sms  .code_promo{
	display: inline-flex;
	align-items: center;
	gap:calc(36px*var(--coef));
    }


    #modal-consult-sms  input[type="checkbox"].square_radio{
	margin-top:calc(20px*var(--coef));
	flex: 0 0 auto !important;
	width:calc(23px*var(--coef));
	height:calc(23px*var(--coef));
    }

    #modal-consult-sms .cs-utc-selector .cs-utc {
	margin-top: 0;
	max-width: calc(220px*var(--coef));
    }

    #modal-consult-sms label{
	display: inline-flex;
	align-items: last baseline;
	gap:  calc(15px*var(--coef));
	cursor:pointer;
    }

    #modal-consult-sms .btn.validate {

	width: calc(303px*var(--coef));
	height: calc(52px*var(--coef));
    }
	
	
	
    }



    /* mobile ----------- */
    @media only screen   and (max-width : 767px)
    {
	
	
	#modal-consult-sms{
	    width: calc(347px*var(--coef));
	    height: calc(990px*var(--coef));
	    width:  calc(100% - 30px*var(--coef) );
	}

	#modal-consult-sms .modal-content{
	    height: calc(940px*var(--coef));
	    padding:calc(16px*var(--coef)) calc(15px*var(--coef)) calc(30px*var(--coef)) calc(15px*var(--coef));
	    width:  calc(100% - 30px*var(--coef) );
	}

	#modal-consult-sms .cs-utc-selector.prim{
	    margin-top: calc(36px*var(--coef));
	}

	#modal-consult-sms  .tarifs_div{
	    width:100%;
	    flex-direction: column;
	    align-items: unset;
	    gap:calc(12px*var(--coef));
	}

	
	#modal-consult-sms  .tarifs_div .tarif{
	   font-size: calc(22px*var(--coef));
	}
	
	#modal-consult-sms .nbre_sms {
	    font-size: calc(18px*var(--coef));
	  }

	
	#modal-consult-sms .code_promo_rect{
	    width:calc(163px*var(--coef));
	    height:calc(60px*var(--coef));
	    background: #EAEAEA;
	    border-radius: calc(10px*var(--coef));
	}

	#modal-consult-sms  .code_promo{
	    display: inline-flex;
	    align-items: center;
	    gap:calc(20px*var(--coef));
	    margin-bottom: calc(15px*var(--coef));
	}

	#modal-consult-sms  .code_promo2{
	    white-space: nowrap;
	}
	
	
	#modal-consult-sms label {
	    align-items: start; 
	    margin-bottom:calc(20px*var(--coef));
	}
	
	#modal-consult-sms  input[type="checkbox"].square_radio {
	
	    margin-top:calc(20px*var(--coef));
	    width: calc(23px*var(--coef));
	    height: calc(23px*var(--coef));
	    
	}

	#modal-consult-sms .cs-utc-selector .cs-utc {
	    width: calc(190px*var(--coef));
	  
	} 

	#modal-consult-sms .cs-utc-selector.bis .cs-utc {
	    margin-top: calc(20px*var(--coef));
	}



	#modal-consult-sms .div_btn{
	      width:  100%;
	}


	#modal-consult-sms .btn.validate {
	   
	    width: calc(240px*var(--coef));
	    height: calc(52px*var(--coef));

	    padding:0;

	}
	
	#modal-consult-sms  .div_a_la_mn{
	    width:calc(100% - 45px*var(--coef));

	    justify-content: space-between;

	    padding:calc(11px*var(--coef)) calc(20px*var(--coef)) calc(11px*var(--coef)) calc(25px*var(--coef));
	    gap:calc(10px*var(--coef));

	}

	#modal-consult-sms  .div_a_la_mn > div{
	      width: auto;
	}

	#modal-consult-sms  label > span{
	    position: relative;
	    top:calc(17px*var(--coef));
	}
    }




</style>

<div class="modal  fade" id="modal-consult-sms"  role="dialog" >

    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>

    <div class="modal-content">


	<div class="demarrer fw400 p28 m18">
	    <?= (__("Achat de forfait SMS", null, true)); ?>
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
<?= (__("une fois mes SMS épuisés la mise en relation est coupée", null, true)); ?> 
	    </div>	
	</div>




	<div class="tarifs_div">
	    <div>
		<div class="tarif  btn_like">9,99$</div>
		<div class="nbre_sms orange2">10 sms</div>
	    </div>

	    <div>
		<div class="populaire">
		    <div class="le_plus_pop p16 fw300">le + populaire</div>
		    <div class="tarif blue  btn_like">99,99$</div>
		</div>
		<div class="nbre_sms orange2">20 sms</div>
	    </div>
	   
	    <div>
		<div class="tarif btn_like">15,99$</div>
		<div class="nbre_sms orange2">30 sms</div>
	    </div>
	    
	</div>



	<div class="cs-utc-selector prim" data-tx_conv="" > 
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



	<label> <input class="square_radio" type="checkbox" name="checkbox"> <span class="fw400 p18 t16 m12">
<?= (__("j’ai lu et j’accepte les Conditions Générales de Services ", null,
	true)); ?>
	</label>
	<label> <input class="square_radio" type="checkbox" name="checkbox"> <span class="fw400 p18 t16 m12">
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

    let conversion_ar_sms = [];
<?php
foreach ($currencies as $key => $currency)
    {
    // echo '<p class="lh18-27 fw600"><span>'.$currency["Currency"]["code"].' ('.$currency["Currency"]["label"].')</span><span></span></p>';
    echo "conversion_ar_sms['" . $currency["Currency"]["label"] . "']=" . $currency["Currency"]["amount"] . ";" . chr(13) . chr(10);
    }
?>

	/* CLICK ANYWHERE TO CLOSE */
	
	$(window).click(function() {

	    let img = $('#modal-consult-sms .cs-utc-selector .cs-utc > img');
	    console.log("click img", img);
	    $(img).removeClass('active')
	});  

    /* AFFICHE LES LISTE DEROULANTE OPTIONS currencies  */
    $('body').on('click', '#modal-consult-sms .cs-utc-selector .cs-utc',
            function (e)
            {
		console.log("click cs-utc-selector .cs-utc");
                e.preventDefault();
		e.stopPropagation();
                let img = $(this).find('> img');
               
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
            '#modal-consult-sms .cs-utc-selector .cs-utc .cs-utc-list > p',
            function (e)
            {
                e.preventDefault();
                let $parent = $(this).closest('.cs-utc-selector');
                console.log(" $parent", $parent);
                let prev_currency = $parent.find('.currency_symbol').html()
                console.log("prev_currency", prev_currency);
                let prev_tx_conv = conversion_ar_sms[prev_currency];
                let currency = $(this).data("currency");

                let tx_conv = conversion_ar_sms[currency] / prev_tx_conv
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
    $('body').on('mouseup',
            '#modal-consult-sms .cs-utc-selector.prim .cs-utc .cs-utc-list > p',
            function (e)
            {
                e.preventDefault();
                change_currency_bloc3()
            });

   


    function change_currency_bloc3()
    {

        let $target = $("#modal-consult-sms .cs-utc-selector.prim")
        let currency = $target.find('.currency_symbol').html()
        //  console.log("change_currency_bloc1 currency",currency);
        let tx_conv = $target.data("tx_conv")
        console.log("tx_conv", tx_conv);
        let val
        let new_val;

        $("#modal-consult-sms .tarifs_div .tarif ").each(function ()
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

    /* FIN CONVERTISSEURS */

</script>

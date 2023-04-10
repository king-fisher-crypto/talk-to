<style>
    /* POPUPS */
    
    #modal-consult-rates{
	width: calc(580px*var(--coef));
	height: calc(680px*var(--coef));

    }

    #modal-consult-rates.modal .header{
	background: var(--main-bg-color);
    }
    
    #modal-consult-rates.modal .header .titre{
	padding-left: calc(20px*var(--coef));
	padding-top: calc(13px*var(--coef));
    }

    
    #modal-consult-rates.modal .cs-utc-selector .cs-utc{
	margin-top: 0;
	justify-content: right;
    }
    
    
    #modal-consult-rates .modal-content{
	/*width:  calc(100% - 50px*var(--coef) );*/
	height: calc(630px*var(--coef));
	padding:calc(5px*var(--coef)) calc(20px*var(--coef)) calc(20px*var(--coef)) calc(20px*var(--coef));

    }
    
    #modal-consult-rates .modal-content .bar{
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: #EFEFEF;
	min-height:calc(40px*var(--coef));
	border-radius:calc(10px*var(--coef));
	font-size: calc(21px*var(--coef));
	padding: calc(9px*var(--coef)) calc(20px*var(--coef)) calc(9px*var(--coef)) calc(20px*var(--coef));
	margin-top: calc(5px*var(--coef));
    }
    
    
    #modal-consult-rates .modal-content .label{
	color:var(--blue);
	
    }
    
    #modal-consult-rates .modal-content .label:first-letter {
    text-transform: uppercase;
}
    
     #modal-consult-rates .modal-content .value{
	color:var(--orange);
	
    }


</style>


<div class="modal  fade" id="modal-consult-rates"  role="dialog" >

    <div class="header">
	<div class="titre fw600 p22"><?= __("Tarifs de").' '.$User['pseudo']; ?></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>

    <div class="modal-content">
	
	
	<div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p18 t15 m12">

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
	
	<div class="bar">
	    <div class="label"><?= __("téléphone")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("chat")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("webcam")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("sms")?></div>
	    <div>
		<div class="orange2">10 <?= __("sms")?></div>
		<div class="orange2">20 <?= __("sms")?></div>
		<div class="orange2">30 <?= __("sms")?></div>
	    </div>
	     <div style="text-align:right">
	    <div class="value">9,99$/<?= __("min")?></div>
	    <div class="value">12,99$/<?= __("min")?></div>
	    <div class="value">15,99$/<?= __("min")?></div>
	    </div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("email")?></div>
	    <div class="orange2 ucfirst"><?= __("Délai de réponse 24h")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("formation vidéo")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("masterclass")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("pdf-document")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("photo à la demande")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("vidéo à la demande")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	<div class="bar">
	    <div class="label"><?= __("contenu privé")?></div>
	    <div class="value">2,99$/<?= __("min")?></div>
	</div>
	
	
    </div>   
    
    </div>   



<script>
    
    /* CONVERTISSEUR */
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
	    
	    
	    

            /* CLICK ANYWHERE TO CLOSE */
            $(window).click(function ()
            {

                let img = $(".cs-utc-selector .cs-utc > img");
                $(img).removeClass('active');
                $(img).closest('.cs-dad-right').removeClass('cs-layer');
            });


            $('body').on('click',
                    '#modal-consult-rates .cs-utc-selector .cs-utc',
                            function (e)
                            {

                                e.preventDefault();
                                e.stopPropagation();
                                let img = $(this).find('> img');
                            //    console.log("click img", img);
                                if ($(img).hasClass('active'))
                                {
                                    $(img).toggleClass('active');
                                    $(img).closest('.cs-dad-right').
                                            removeClass('cs-layer');
                                } else
                                {
                                    $(img).toggleClass('active');
                                    $(img).closest('.cs-dad-right').addClass(
                                            'cs-layer');
                                }
                            });

           $('body').on('click', '#modal-consult-rates .cs-utc-selector .cs-utc .cs-utc-list > p',
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
	
		   
		    convert_rates_price()
		   
                   

                });

				     
		function convert_rates_price()
		{
			let new_val
			let cur_val
			
			
			$( "#modal-consult-rates .value" ).each(function( index ) {
			cur_val=  $( this ).html();
			cur_val = cur_val.replace(',', '.')
			cur_val = parseFloat(cur_val)
			new_val = cur_val * tx_conv;
			new_val = new_val.toFixed(2)
			new_val = new_val+""; 
			new_val = new_val.replace('.', ',')
			$( this ).html(new_val+currency)
				
			 });
			
			
		}
		
		
</script>
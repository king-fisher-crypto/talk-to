<style>
    
	
/* POPUPS */
#modal-payment {
   width: calc(629px*var(--coef));
   height: calc(913px*var(--coef));
}

#modal-payment .modal-content{
    width: 100%;
    height: calc(863px*var(--coef));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    padding:calc(16px*var(--coef)) calc(72px*var(--coef)) calc(20px*var(--coef)) calc(25px*var(--coef));
}


#modal-payment  .div_cartes{
    display: flex;
    align-items: center;
    justify-content: space-between;
    width:100%;
}

#modal-payment  .div_cards .card{
    height:calc(20px*var(--coef));
    width:auto;
}

#modal-payment .bar_field{
box-shadow: 0px 3px 35px 0px #00000014;
-webkit-appearance: none;
appearance: none;
border-radius: 5px;
background: var(--main-bg-color);

margin-bottom: calc(10px*var(--coef));
display: flex;
align-items: center;
width:100%;
height:calc(60px*var(--coef));

display: flex;
align-items: center;
gap: calc(20px*var(--coef));
}

#modal-payment .bar_field.bis,
#modal-payment .bar_field.tris
{

width:calc(225px*var(--coef));
display: inline-flex;

}

#modal-payment .bar_field_duo{
    display: flex;
align-items: center;
 justify-content: space-between;
 gap:calc(81px*var(--coef));
}

#modal-payment .bar_field > input{
    border: 0;
    background: none;
    color: var(--light-grey);
    overflow: hidden;
    font-size: calc(22px*var(--coef));
}



#modal-payment .bar_field .carre{
  width:calc(60px*var(--coef));
  height: calc(60px*var(--coef));
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
  background: #FBFBFB;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.08);
}

#modal-payment .bar_field .card {width:calc(30px*var(--coef)); height:auto;}
#modal-payment .bar_field .calendar {width:calc(27px*var(--coef)); height:auto;}
#modal-payment .bar_field .lock {width:calc(21px*var(--coef)); height:auto;}
#modal-payment .bar_field .user {width:calc(28px*var(--coef)); height:auto;}

#modal-payment .div_labels{
   text-align: left; 
}

#modal-payment .div_labels label{
  display: flex;
  align-items: center;
  gap:calc(15px*var(--coef));
  cursor:pointer;
}

#modal-payment input[type="checkbox"].square_radio{
   flex: 0 0 auto;
}

#modal-payment  .btn.validate{
    width:calc(303px*var(--coef));
    height:calc(52px*var(--coef));
}



#modal-payment .payment_details-page .radio_btns .btn input[type="radio"].square_radio{
    display:none;
}


#modal-payment .payment_details-page .div_crypto, .payment_details-page .div_virement {
    width: auto;
}


#modal-payment .payment_details-page{
    margin-left: 0;
    margin-right: 0;
    width:100%;
}


#modal-payment .payment_details-page .radio_btns .btn.radio{
    padding:0;
    justify-content: start;
    padding-left: calc(17px*var(--coef));
    /*background-color:#F5F5F5;*/
}

/* mobile ----------- */
@media only screen   and (max-width : 767px)
{

	#modal-payment {
	width: calc(347px*var(--coef));
	/*height: calc(414px*var(--coef));*/
	}

	#modal-payment  .modal-content{
	width: 100%;
	/*height: calc(445px*var(--coef));*/
	padding: calc(16px*var(--coef)) ;
	}

	#modal-payment .bar_field_duo {
	 display: unset;   
	}
   
	#modal-payment .bar_field.bis, #modal-payment .bar_field.tris {
  width: 100%;

}

</style>
       
<div class="modal  fade" id="modal-payment"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content ">

	<div class="center_h">
	    <div class="titre">
		 Abonnement du Contenu Privé
	    </div>

	    <div class="pseudo fw600 p28 m18 form_video">
	       <?=$User['pseudo'] ?> 
	    </div>	
	</div>
	
	
	<div class="div_montant  fw400 p22 m16">
	    <span class="ucfirst"><?=(__("montant du paiement")) ; ?> :</span>
	    <span class="blue2 montant">9,99$</span>   
	</div>
	
	
	<div class="div_cartes">
	    <span class="ucfirst fw500 p25 blue2"><?=(__("carte bancaire"))?></span>
	    <div class="div_cards">
		<img class="card" src="/theme/black_blue/img/masterclass.svg" >
		<img class="card" src="/theme/black_blue/img/american_express.svg" >
		<img class="card" src="/theme/black_blue/img/visa.svg" >
	    </div>   
	</div>
	
	<div style="width:100%;">
	<div class="bar_field ">
	    <div class="carre"><img class="card" src="/theme/black_blue/img/card_grey.svg" ></div>
	    <input class="" placeholder="<?=(__("numéro de la carte"))?>">
	</div>
	
	<div class="bar_field_duo">
	<div class="bar_field bis">
	    <div class="carre">
		<img class="calendar" src="/theme/black_blue/img/menu/calendrier_grey.svg" >
	    </div>
	    <input class="" placeholder="MM/AA">
	</div>
	
	<div class="bar_field tris"> 
	      <div class="carre">
		<img class="lock" src="/theme/black_blue/img/lock_grey.svg" >
	    </div>
	    <input class="" placeholder="<?=(__("code de sécurité"))?>">
	</div>
	</div>
	
	
	<div class="bar_field "> 
	    <div class="carre">
		<img class="user" src="/theme/black_blue/img/user_grey.svg" >
	    </div>
	    <input class="" placeholder="<?=(__("nom sur la carte"))?>">
	</div>
	</div>
	
	<div class="div_labels">
	<label class="CGV ">
	    <input class="square_radio" type="checkbox" name="checkbox">
	    <div class="fw400 p18 "><?= __('J’ai lu et j’accepte');?>
		<a href="" class="blue2"><?= __('les Conditions Générales de Services ')?></a></div>
	</label>

	<label class="retractation ">
	    <input class="square_radio" type="checkbox" name="checkbox">
	    <div class="fw400 p18 "><?= __('J’accepte de débuter la prestation immédiatement après la validation de ma commande et je renonce expressément à mon droit de rétractation');?></div>
	</label>
	</div>
	
	
	
	 <div class=" btn validate white up_case p24 m18 ucfirst" onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('valider') ?>  
</div>

	
	<div class="payment_details-page">
	    
	<div class="radio_btns">
	    <label class="virement">
		
		<div class="btn bar_field radio fw600 p20 m20 ucfirst">
	    <img class="user" src="/theme/black_blue/img/cryptos/vir_bank.svg" >
	    <?= __('virement bancaire') ?><input class="square_radio" type="radio" name="radio"></div>
		
	    </label>
	    <div class="div_virement2">
	    <div class="div_virement">
	    <?php

$fields_ar =[ "firstname" => "prénom","lastname" => "nom de famille", 
	    "numero_compte" => "Numéro de compte", "IBAN" => "IBAN", "Code BIC/SWIFT" => "Code BIC/SWIFT",
	    "Nom banque" => "Nom banque", "Adresse banque" => "Adresse banque", "pays" => "Pays banque"
	   ];


	   
		foreach($fields_ar as $var => $label)
		{
		$label = ucwords($label);
		$img="";
		 ?>
		<div class="bar_field <?=$var;?>"> 
		<?php
		switch($var)
		    {
		    case "numero_compte":
		    ?>
		    <input placeholder="<?=$label;?>" class="<?=$var;?>"/><img class="card" src="/theme/black_blue/img/card.svg">
		    <?php
		    break;
		    case "pays":
		    ?>
		    <input readonly="readonly"   class="<?=$var;?>" placeholder="<?=$label;?>"/><img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">
		      </div> 
		      <div class="cadre_pays">
		       <?php
		      foreach($select_countries as $id => $country){
		       ?>
			  <div class="btn white bar_pays lh22-26"><?=$country;?></div>
			<?php  
		      }
		      ?>
		     
		    <?php
		    break;
		    default:
		    ?>
		    <input class="<?=$var;?>" placeholder="<?=$label;?>"/>
		    <?php
		    break;
		    }
		?>
		</div> 
		<?php
		}


?>
	    </div> 
	  
	    <label class="CGV "> <input class="square_radio" type="checkbox" name="checkbox"> <span class="  lh24-28d lgrey2"> <?= __("Je certifie que ces coordonnées de paiement m'appartiennent."); ?></span> </label>
		
	<div class="div_btn"><div class="btn lh25-29c h85b enregistrer up_case blue2">  <?= __('enregistrer'); ?></div></div>
	     </div> 


	    <label class="crypto">
		
	    <div class=" btn bar_field radio fw600 p20 m20  ucfirst">
	    <img class="user" src="/theme/black_blue/img/cryptos/crypto.svg" >
	    <?= __('cypto-monnaie'); ?><input  class="square_radio" type="radio" name="radio"></div>
	    </label>


	    <div class="div_crypto2">
	    <div class="div_crypto">

		<div class="bar_field crypto lh20-30">
		    
		    <span id="selected_crypto"><?= __('Coin'); ?> </span> <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg" ></div>
		 <div class="cadre_crypto"> 
		 
		 
		    <div class="btn white bar_crypto lh22-26"> 
			<img class="crypto" src="/theme/black_blue\img\cryptos\usdt.svg" >
			USDT
		    </div>
		    <div class="btn white bar_crypto lh22-26">
			<img class="crypto" src="/theme/black_blue\img\cryptos\usdc.png" >
			USDC
		    </div>
		    <div class="btn white bar_crypto lh22-26">
			<img class="crypto" src="/theme/black_blue\img\cryptos\busd.png" >
			BUSD
		    </div>
		    <div class="btn white bar_crypto lh22-26">
			<img class="crypto" src="/theme/black_blue\img\cryptos\dai.png" >
			DAI
		    </div>
		 
		 </div>
		
		
		<div class="bar_field network  lh20-30">
		    
		    <span id="selected_network"><?= __('Network'); ?> </span> <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg" ></div>
		
		<div class="cadre_network_2">
		    <div class="cadre_network"> 
		     <div class="btn white bar_crypto lh22-26">BSC <span class="fw300 lgrey2"> BNB Smart Chain (BEP20)</span></div>
		     <div class="btn white bar_crypto lh22-26">AVAXC <span class="fw300 lgrey2"> AVAX C-Chain</span></div>
		     <div class="btn white bar_crypto lh22-26">BNB <span class="fw300 lgrey2"> BNB Beacon Chain (BEP2)</span></div>
		     <div class="btn white bar_crypto lh22-26">ETH <span class="fw300 lgrey2"> Ethereum (ERC20)</span></div>
		     <div class="btn white bar_crypto lh22-26">MATIC <span class="fw300 lgrey2"> Polygon</span></div>
		     <div class="btn white bar_crypto lh22-26">SOL <span class="fw300 lgrey2"> Solana</span></div>
		     <div class="btn white bar_crypto lh22-26">TRX <span class="fw300 lgrey2"> 
Tron (TRC20)</span></div>
		   
		 </div>
		 
		    <div class="lh20-30b lgrey2 fw300"> 
		    
			<div class="txt2 t1">
		    <?= __('Assurez vous que le réseau que vous choisissez pour déposer vos Crypto-monnaies, correspond au réseau de retrait, sinon vos actifs pourraient être perdus.'); ?>
		    </div>
			
		<div class="bar_field   wallet">
		   <input class="wallet" placeholder="<?= __('Adresse Wallet'); ?>"/>
		</div>
		
		<div class="txt2 t2">
		    <?= __("Attention : Les paiements en crypto-monnaies nécessitent des connaissances quant au fonctionnement de l'univers Crypto, si vous faites une erreur concernant le type de Coin, le Réseau/Network ou l'adresse Wallet et que nous envoyons les fonds sur les indications erronées indiquées par vos soins, vos fonds seront perdus et irrécupérables."); ?>
		     </div> 
		</div>
		</div>
		 
		
	
		
	    </div>

	<label class="CGV "> <input class="square_radio" type="checkbox" name="checkbox"> <span class="lh24-28d lgrey2"> <?= __("Je confirme avoir pris connaissance des indications ci-dessus et valider mes informations de paiement en Crypto-monnaies"); ?></span> </label>
		
		<div class="div_btn"><div class="btn lh25-29c h85b enregistrer up_case blue2">  <?= __('enregistrer'); ?></div></div>
	</div>
	 </div>
    </div>
	
	
	
	
	
	
	
	
	
    </div>   

</div>




<script>


    
     $(".payment_details-page label.virement").mouseup(function ( )
        {
	     $(".div_virement2").toggle("fast");
	     $(".div_crypto2").hide()
	})
	
	
	
     $(".payment_details-page label.crypto").mouseup(function ()
        {
	     $(".div_crypto2").toggle("fast");
	     $(".div_virement2").hide()
	})
    
    
    $(".payment_details-page .div_virement .bar_field.pays").click(function ()
        {
	    if( $(".cadre_pays").css("display")=='none')
	    $(".bar_field.pays .chevron").css("transform","rotate(180deg)");
	else
	    $(".bar_field.pays .chevron").css("transform","rotate(0deg)"); 
	
	    $(".cadre_pays").toggle("fast");
//	    console.log("css",$(".cadre_pays").css("display"));
	   
	})

    $(".payment_details-page .div_virement .bar_pays").click(function ()
        {
	  let pays = $(this).text()
	  $("input.pays").val(pays)
	   $(".cadre_pays").hide("fast")
	   $(".bar_field.pays .chevron").css("transform","rotate(0deg)");
	})
    
   


 $(".payment_details-page .div_crypto .bar_field.crypto").click(function ()
        {
	    if( $(".cadre_crypto").css("display")=='none')
	    $(".bar_field.crypto .chevron").css("transform","rotate(180deg)");
	else
	    $(".bar_field.crypto  .chevron").css("transform","rotate(0deg)"); 
	
	    $(".cadre_crypto").toggle("fast");
//	    console.log("css",$(".cadre_pays").css("display"));
	   
	})
	
 $(".payment_details-page .div_crypto .bar_field.network").click(function ()
        {
	    if( $(".cadre_network").css("display")=='none')
	    $(".bar_field.network .chevron").css("transform","rotate(180deg)");
	else
	    $(".bar_field.network  .chevron").css("transform","rotate(0deg)"); 
	
	    $(".cadre_network").toggle("fast");
//	    console.log("css",$(".cadre_pays").css("display"));
	   
	})	
	
	
	
	
	$(".payment_details-page .cadre_network .btn.bar_crypto ").mouseup(function ()
        {
	    $(".cadre_network").toggle("fast");
	    let network = $(this).html()
	    $("#selected_network").html(network)
	    $(".bar_field.network  .chevron").css("transform","rotate(0deg)");  
	})
    
	
	$(".payment_details-page .cadre_crypto .btn.bar_crypto ").mouseup(function ()
        {
	     $(".cadre_crypto").toggle("fast");
	     
	    let crypto = $(this).text()
	    $("#selected_crypto").text(crypto)
	    $(".bar_field.crypto  .chevron").css("transform","rotate(0deg)");  
	})
    
	

</script>

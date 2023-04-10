<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="payment_details-page page jswidth">


    <article class="">
	<h1 class="">  <?= __('Mes coordonnées de paiement') ?></h1>
	<?php
	//$userRole = 'agent';
	if ($userRole == 'agent') 
	 {
	    	echo __("Choisissez votre mode de rémunération, virement bancaire international ou Crypto-Monnaie.");
	 }
	
	 else
	 if ($userRole == 'client') 
	 {
	echo __("Afin de payer vos gains Affiliation, nous avons besoin de connaître vos coordonnées de paiement. Les reversions des gains liés à l'Affiliation sont effectués une fois par mois. ");
	 }
	 ?>
	  
    </article>

   <div class=" ">
	<div class="radio_btns">
	    <label class="virement">
		
		<span class="btn radio b lh25-29c h70b"><?= __('Etre payé par virement bancaire') ?><input class="square_radio" type="radio" name="radio"></span>
		
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

<br/>
	    <label class="crypto">
		
		<span class="btn radio b lh25-29c h70b"><?= __('Etre payé par Crypto-monnaie'); ?><input  class="square_radio" type="radio" name="radio"></span>
		
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
   

    
    <?php //if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>


</section>


<script>

document.addEventListener("DOMContentLoaded", function() {
    
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
    
	

 });
</script>


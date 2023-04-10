<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="buycredits-page page jswidth">


    <article class="marge">
	<h1 class="">  <?= __('Mes dépôts/crédits') ?></h1>
	<?= __("Cette page répertorie l'ensemble des dépôts/crédits que vous avez effectué sur LiviTalk.") ?>
	
	<div class="btns2 "> 
	    <span class="txt"> <?= __("Crédits sur mon compte") ?></span>
	<div class="btns"> 	
	    
	     <div class="btn blue credits lh24-28b h70 up_case"    title="<?= __('crédits') ?>">XXX $</div> 	
	    
	    
	     <div class="btn acheter  lh24-28 p17b h70 blue2 up_case"    title="<?= __('acheter des crédits') ?>"><?= __('acheter des crédits') ?></div> 	
	</div> 
    </div>
    </article>

    
    

    <div class="cadre_table ">
	

	<?php
	$agentsFavoris = [];

	$agent = [];

	$agent['statut'] = [];
	
	
	$credits = ["+15,25$","+155,25$","+1555,25$","","","+1555,25$","","","+1555,25$","+1555,25$", "+1555,25$","","","", "+1555,25$", "+1555,25$", ""];
	$debits = ["","","","-15,25$","-155,25$","","-1555,25$","-1555,25$","","","", "-15,25$","-155,25$","-1555,25$","","","-1555,25$"];

	
	$mode = ["Carte bancaire","Crypto monnaie","Retour LiviTalk","","","Crypto monnaie","","","Retour LiviTalk","Crypto monnaie","Virement bancaire", "", "", "", "Crypto monnaie","Virement bancaire"];

	$prestations =["","","","Téléphone","Chat","","Webcam","SMS","","","","Email","MasterClass", "Photo/Demande", "","" ,"Photo/Demande"];
	

	$statuts = ["Remboursé","Remboursé","Retour/crédits","","","","","","Retour/crédits","","Remboursé"];
	$statuts_color = ["blue2","blue2","","","","","","","","","blue2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $agent['credit']  = $credits[$k]; 
	    $agent['debit']  = $debits[$k]; 
	  
	    $agent['mode']  = $mode[$k]; 
	    $agent['prestations']  = $prestations[$k]; 
	    $agent['statut']["label"]  = $statuts[$k]; 
	    $agent['statut']["color"]  = $statuts_color[$k]; 
	    $k++;	    if($k>16) $k=0;
	    
	  
	    $agentsFavoris[] = $agent; 
	    }
	}
	
	
	?>
	<?php if (empty($agentsFavoris)) : ?>
    	<div class="txt_cent">
		<?php echo __('Aucun favoris'); ?>	</div>
	<?php else : ?>
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  
			
    			
    		
    			
    			<th class="date"><?php echo __('Date'); ?></th> 
			<th class="credit"><?= __('Montant')."<br/>".__('Crédit'); ?></th> 
    			<th class="debit"><?= __('Montant')."<br/>".__('Dédit'); ?></th> 
    			<th class="mode"><?= __('Mode')."<br/>".__('paiement'); ?></th> 
    			<th class="Prestation"><?= __('Prestation'); ?></th> 
			<th class="Solde"><?= __('Solde')."<br/>".__('Compte'); ?></th> 
    			<th class="Remarque"><?= __('Remarque'); ?></th> 
    			 
    			
    		

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($agentsFavoris as $agent) : ?>
    <tr> 

	<td class="date"> 24/04/22 15:11:25   </td> 
	<td class="agent"><?=$agent['credit'];?></td> 
	<td class="agent"><?=$agent['debit'];?></td> 
	<td class="agent"><?=$agent['mode'];?></td> 
	<td class="agent"><?=$agent['prestations'];?></td> 
	<td class="agent">10,25$</td> 
	<td class="agent"><span class="<?=$agent['statut']["color"];?>"><?= $agent['statut']["label"]; ?></span></td> 
	
	 
</tr> 
<?php endforeach; ?>





</tbody>
    <?php endif; ?> 
	    </table> 

	</div>

    </div>
    <?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>

  

</section>

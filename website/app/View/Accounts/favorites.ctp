<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="favorites-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes Favoris') ?></h1>
	<?= __("Choisissez votre liste de favoris pour une recherche rapide.") ?>
    </article>

    <div class="btns"> 

	    <a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2"    title="<?= __('agent') ?>">
	    <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
	    <form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __('agent') ?>"></form>
	    </a> 	
	    
	     <div class="btn chercher h70 lh24-28 p20spe blue2 up_case"    title="<?= __('chercher') ?>"><?= __('chercher') ?></div> 	
	</div> 
    

    <div class="cadre_table ">
	

	<?php
	$agentsFavoris = [];

	$agent = [];
	$agent['date'] = "24/04/22";
	$agent['Agent']["pseudo"] = "Lorem Ipsum";

	$agent['duree'] = "5%";

	$agent['statut'] = [];
	
	$mois = [1,2,3,1,2,3,1,2,3,1,2,3];
	
	$montants = ["30,00$","50,00$","75,00$","Via requ�te Paiement ","99,00$","30,00$","50,00$","75,00$","Via requ�te Paiement ","99,00$", "50,00$","75,00$",];
	$duree = ["15","30","30","60","15","15","60","30","30","60","30","30"];
	

	
	$fin = ["","","Mettre fin","","Mettre fin","","","","","",""];
	$statuts = ["Payer","Payer","Validé","Payer","Validé","Payer","Payer","Annulé","Annulé","Annulé","Annulé"];
	$statuts_color = ["","","blue2","","blue2","","","orange2","orange2","orange2","orange2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $agent['duree']  = $duree[$k]." min"; 
	    $agent['statut']["label"]  = $statuts[$k]; 
	    $agent['mois']  = $mois[$k]; 
	    $agent['fin']  = $fin[$k]; 
	   
	    $agent['statut']["color"]  = $statuts_color[$k]; 
	    $k++;	    if($k>10) $k=0;
	    
	  
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
			
    			<th class="agent"><?php echo __('Agent'); ?></th> 
    		
    			<th class="detail"><?php echo __('Détail'); ?></th> 
    			<th class="action txt_cent"><?php echo __('Action'); ?></th> 
    			 
    			
    		

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($agentsFavoris as $agent) : ?>
    <tr> 
	
	<td class="agent"> 
				<a href="" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a>

			    </td> 
<td class="detail"><a href="" class="underline"><?= __('Voir sa page')?></a></td> 


<td class="action txt_cent"><img class="croix" src="/theme/black_blue/img/croix.svg"></td> 
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


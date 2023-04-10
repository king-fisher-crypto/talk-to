<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="docs_pdf-page page jswidth">


    <article>
	<h1 class="">  <?= __('Documents-Pdf') ?></h1>
	<?= __("Cette page récapitule l'ensemble des documents-Pdf que vous avez acheté auprès de vos LiviMasters.") ?>
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
	$statuts = ["Reçu","en attente","en attente","en attente","en attente","Reçu","en attente","en attente","en attente","en attente","en attente"];
	$statuts_color = ["blue2","","","","","blue2","","","","",""];
	
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
			
    			
    		
    			<th class=""></th> 
    			<th class="date"><?php echo __('Date'); ?></th> 
			<th class="agent"><?php echo __('Agent'); ?></th> 
    			<th class="statut"><?php echo __('Statut'); ?></th> 
    			<th class="action"></th> 
    			 
    			
    		

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($agentsFavoris as $agent) : ?>
    <tr> 
	<td class=""></td> 
	<td class="date"> 24/04/22 15:11:25   </td> 
	<td class="agent">Lorem ipsum</td> 
	<td class="agent"><span class="<?=$agent['statut']["color"];?>"><?= $agent['statut']["label"]; ?></span></td> 
	
	 <td class="action"><a href="" class="underline "><?=($agent['statut']["color"]=="blue2")?__('Voir'):""?></a></td> 
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

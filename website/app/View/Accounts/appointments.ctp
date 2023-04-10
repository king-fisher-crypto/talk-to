<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="appointments-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes Rdv LiviMasters') ?></h1>
	<?= __("Vous avez sollicité un RDV auprès d'un LiviMaster, suivez l'état de votre demande. Un RDV doit être validé par ce dernier, qui peut également le refuser ou vous proposer une autre date ou horaire, un email vous sera envoyé pour vous en informer.") ?>
	
	
    </article>

    
    

    <div class="cadre_table ">

	<?php
	$appointments = [];

	$appointment = [];
	$appointment['date'] = "24/04/22 15:11/25";
	$appointment['Agent']["pseudo"] = "Lorem Ipsum";

	$appointment['duree'] = "5%";

	$appointment['statut'] = [];
	

	$duree = ["15","30","30","60","15","15","60","30","30","60","30","30"];
	$statuts = ["En attente","En attente","Validé","En attente","Validé","En attente","En attente","Refusé","Refusé","Refusé","Refusé"];
	
	$statuts_color = ["","","blue2","","blue2","","","orange2","orange2","orange2","orange2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $appointment['duree']  = $duree[$k]." min"; 
	    $appointment['statut']["label"]  = $statuts[$k]; 
	   
	    $appointment['statut']["color"]  = $statuts_color[$k]; 
	    $k++;	    if($k>10) $k=0;
	    
	  
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
			
    			<th class="agent"><?php echo __('Agent'); ?></th> 
    			<th class="date"><?php echo __('Date'); ?></th> 
    			<th class="duree"><?php echo __('Durée'); ?></th> 
    			<th class="statu"><?php echo __('Statut'); ?></th> 
    			<th class="duree"><?php echo __('Voir son') . "<br/>" . __('planning'); ?></th> 

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($appointments as $appointment) : ?>
    <tr> 
	
	<td class="agent"> 
	<?php echo $appointment['Agent']["pseudo"];
	?> 
</td> 

<td class="date"><?php echo $appointment['date']; ?></td> 
<td class="duree"><?php echo $appointment['duree']; ?></td> 
<td class="statut <?php echo $appointment['statut']['color']; ?>"><?php echo $appointment['statut']['label']; ?></td> 
<td class="voir "> <a class="underline"><?=__('Accéder'); ?></a> 
</td> 
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



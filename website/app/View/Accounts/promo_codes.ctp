<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="promo_codes-page page ">


    <article class="marge">
	<h1 class="">  <?= __('Mes codes promos') ?></h1>
	<?= __("Le tableau ci-dessous répertorie l'ensemble de vos codes promos, les LiviMasters avec lesquels ils sont valables, les médias via lesquels ils sont également valables, ainsi que leurs dates de validité. Ces promos seront automatiquement appliquées  lorsque vous contacterez un LiviMaster.") ?>
	
	<div class="btns"> 

	    <a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2"    title="<?= __('agent') ?>">
	    <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
	    <form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __('agent') ?>"></form>
	    </a> 	
	    
	     <div class="btn chercher h70 lh24-28 p20spe blue2 up_case"    title="<?= __('chercher') ?>"><?= __('chercher') ?></div> 	
	</div>  
	
    </article>

    
    

    <div class="cadre_table ">

	<?php
	$codes_promos = [];

	$code_promo = [];
	$code_promo['date_start'] = "24/04/22 15:11/25";
	$code_promo['Agent'] = ["pseudo", "agent_number"];
	$code_promo['Agent']["pseudo"] = "Lorem Ipsum";
	$code_promo['Agent']["agent_number"] = "233";
	$code_promo['remise'] = "5%";

	$code_promo['code_promo'] = "Lorem Ipsum";
	$code_promo['date_max'] = "24/04/22 15:11/25";

	$medias = ["Téléphone","Chat","Webcam","SMS","Email","Vidéos formation","MasterClass","Documents-Pdf","Vidéos/demande","Photos/demande","Contenus Privés"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $code_promo['remise']  = 5*$i."%"; 
	    $code_promo['media']  = $medias[$k]; 
	    $k++;
	    if($k>10) $k=0;
	    $codes_promos[] = $code_promo; 
	    }
	}
	
	
	?>
	<?php if (empty($codes_promos)) : ?>
    	<div class="txt_cent">
		<?php echo __('Aucun code promo'); ?>	</div>
	<?php else : ?>
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  
    			<th class="date"><?php echo __('Date'); ?></th> 
    			<th class="agent"><?php echo __('Agent'); ?></th> 
    			<th class="mode"><?php echo __('Remise'); ?></th> 
    			<th class="cout"><?php echo __('Média'); ?></th> 
    			<th class="cout"><?php echo __('Codes') . "<br/>" . __('Promos'); ?></th> 
    			<th class="duree"><?php echo __('date de') . "<br/>" . __('validité Max'); ?></th> 

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($codes_promos as $code_promo) : ?>
    <tr> 
	<td class="date">
	<?php echo $code_promo['date_start'];  ?>
	</td> 
	<td class="agent"> 
	<?php echo $this->Html->link($code_promo['Agent']['pseudo'], array('language' => $this->Session->read('Config.language'), 'controller' => 'agents','action' => 'display', 'link_rewrite' => strtolower(str_replace(' ', '-',	$code_promo['Agent']['pseudo'])), 'agent_number' => $code_promo['Agent']['agent_number']), array('class' => 'agent-pseudo', 'escape' => false));
	?> 
</td> 

<td class="remise"><?php echo $code_promo['remise']; ?></td> 
<td class=""><?php echo $code_promo['media']; ?></td> 
<td class=""><?php echo $code_promo['code_promo']; ?></td> 
<td class="date_max"><?php echo $code_promo['date_max']; ?>
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



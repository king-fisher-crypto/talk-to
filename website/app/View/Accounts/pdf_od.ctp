 <?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js'); ?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>
<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="pdf_od-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes Documents-Pdf') ?></h1>
	<?= __("Vos achats de Documents-Pdf sont répertoriés sur la page ci-dessous, en cliquant sur le bouton \" Voir \" vous accédez à la page répertoriant l'ensemble de vos Documents-Pdf achetés.") ?>
	
	
    </article> 

    
    

    <div class="cadre_table ">
	
	
	<div class="btns"> 
	    <a id="btn_datepicker" class="btn spe1  date transparent daterange b "    title="<?= __('dates') ?>"><img src="/theme/black_blue/img/calendrier.svg"> 01/04/22 - 24/04/22</a> 
	   
	</div>  	
	
	
	

	<?php
	$masterclasses = [];

	$masterclass = [];
	$masterclass['date'] = "24/04/22 15:11/25";
	$masterclass['Agent']["pseudo"] = "Lorem Ipsum";

	$masterclass['montant'] = "5%";

	$masterclass['detail'] = [];
	

	$montants = ["15,25$","20,00$","15,25$","15,25$","15,25$","20,00$","15,25$","Refusé","20,00$","20,00$","20,00$"];
	
	$statuts_color = ["","","blue2","","blue2","","","orange2","orange2","orange2","orange2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $masterclass['montant']  = $montants[$k]; 
	   
	    $masterclass['statut']["color"]  = $statuts_color[$k]; 
	    $k++;	    if($k>11) $k=0;
	    
	  
	    $masterclasses[] = $masterclass; 
	    }
	}
	
	
	?>
	<?php if (empty($masterclasses)) : ?>
    	<div class="txt_cent">
		<?php echo __('Aucune masterclass'); ?>	</div>
	<?php else : ?>
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  
			
    			
    			<th class="date"><?php echo __('Date'); ?></th> 
			<th class="agent"><?php echo __('Agent'); ?></th> 
    			<th class="duree">Document-Pdf</th> 
    			<th class="statu"><?php echo __('Coût'); ?></th> 
    			<th class="duree"><?php echo __('Détail'); ?></th> 

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($masterclasses as $masterclass) : ?>
    <tr> 
	
	<td class="date"> 
	<?php echo $masterclass['date']; ?>
</td> 

<td class="agent">
    <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a>
<?php
/*
				echo $this->Html->link(
				$this->Html->image($historique['Agent']['photo'],array('class' => 'rounded', 'alt' => $historique['Agent']['pseudo'])) ,
					array(
					    'language' => $this->Session->read('Config.language'),
					    'controller' => 'agents',
					    'action' => 'display',
					    'link_rewrite' => strtolower(str_replace(' ', '-',
							    $historique['Agent']['pseudo'])),
					    'agent_number' => $historique['Agent']['agent_number']
					), array('escape' => false, 'class' => 'sm-sid-photo')
				);
					
			echo $this->Html->link(	$historique['Agent']['pseudo'],		array(					    'language' => $this->Session->read('Config.language'),					    'controller' => 'agents',					    'action' => 'display',					    'link_rewrite' => strtolower(str_replace(' ', '-',							    $historique['Agent']['pseudo'])),					    'agent_number' => $historique['Agent']['agent_number']					), array('class' => 'agent-pseudo', 'escape' => false)				);
			*/	
			?> 
				
<!--				<span class="visible-xs visible-only-768 h6 mt15 pull-right"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$historique['UserCreditLastHistory']['date_start']), '%d/%m/%y %Hh%M'); ?></span>-->
</td> 
<td class="">Lorem ipsum</td> 
<td class="montant blue2"><?php echo $masterclass['montant']; ?></td> 
<td class="voir "> <a class="underline"><?=__('Voir'); ?></a> 
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

   <input type="hidden" id="daterange" class="form-control"  >

</section>


<script>

    (function ()
    {

        var btn_datepicker = document.getElementById('btn_datepicker');

        btn_datepicker.addEventListener('click', function ()
        {
            duDatepicker('#daterange', 'show')
        }, false);


        duDatepicker('#daterange', {
            range: true,
            events: {
                onRangeFormat: function (from, to)
                {
                    var fromFormat = 'mmmm d, yyyy', toFormat = 'mmmm d, yyyy';

                    console.log(from, to);

                    if (from.getMonth() === to.getMonth() && from.getFullYear()
                            === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'd, yyyy'
                    } else if (from.getFullYear() === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'mmmm d, yyyy'
                    }

                    return from.getTime() === to.getTime() ?
                            this.formatDate(from, 'mmmm d, yyyy') :
                            [this.formatDate(from, fromFormat),
                                this.formatDate(to, toFormat)].join('-');
                }
            }
        });



     
    })();

</script>

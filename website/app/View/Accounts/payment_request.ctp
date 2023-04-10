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


<section class="payment_request-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes Requête de paiement') ?></h1>
	<?= __("Le tableau ci-dessous répertorie l'ensemble des requêtes de paiement que vous avez reçu de vos LiviMasters, pour une photo ou une vidéo à la demande par exemple.") ?>
	
	
    </article>

    
    

    <div class="cadre_table ">
	
	
	<div class="btns"> 
	    <a id="btn_datepicker" class="btn spe1  date transparent daterange b "    title="<?= __('dates') ?>"><img src="/theme/black_blue/img/calendrier.svg"> 01/04/22 - 24/04/22</a> 
	   
	</div>  	

	<?php
	$appointments = [];

	$appointment = [];
	$appointment['date'] = "24/04/22";
	$appointment['Agent']["pseudo"] = "Lorem Ipsum";

	$appointment['duree'] = "5%";

	$appointment['statut'] = [];
	
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
	    $appointment['duree']  = $duree[$k]." min"; 
	    $appointment['statut']["label"]  = $statuts[$k]; 
	    $appointment['mois']  = $mois[$k]; 
	    $appointment['fin']  = $fin[$k]; 
	   
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
    			<th class="duree"><?php echo __('Montant'); ?></th> 
    			<th class="statu"><?php echo __('Statut'); ?></th> 
    			<th class="croix"></th> 
    		

    		    </tr> 
    		</thead> 
    		<tbody>









<?php foreach ($appointments as $appointment) : ?>
    <tr> 
	
	<td class="agent"> 
	<?php echo $appointment['Agent']["pseudo"];
	?> 
	</td> 
<td class="date"><?= $appointment['date']; ?></td> 


<td class="duree">25,25$</td> 
<td class="statut <?php echo $appointment['statut']['color']; ?>"><span class="<?=($appointment['statut']['label']=="Payer")?"underline":""; ?>"><?php echo $appointment['statut']['label']; ?></span></td> 
<td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td> 
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
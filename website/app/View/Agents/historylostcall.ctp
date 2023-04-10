<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
//echo $this->Session->flash();
?>


<section class="subscription-page a-historylostcall-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes appels perdus') ?></h1>
		<?= __("Par mesure de sécurité, le mode téléphone est automatiquement désactivé après 3 appels perdus, il vous appartient ensuite de réactiver ce   mode. Nous attirons votre attention sur la nécessité de répondre à vos appels et à l'image que vous donnez en ne répondant pas, pouvant créer un désengagement de vos clients.") ?>	
    </article>
 

    <div class="cadre_table ">
	

	<?php
	$appointments = [];

	$appointment = [];
	$appointment['date'] = "24/04/22 15:11:25 ";
	$appointment['Agent']["pseudo"] = "Lorem Ipsum";

	$appointment['duree'] = "5%";

	$appointment['statut'] = [];
	
	$mois = [1,2,3,1,2,3,1,2,3,1,2,3];
	
	$montants = ["30,00$","50,00$","75,00$","Via requête Paiement ","99,00$","30,00$","50,00$","75,00$","Via requête Paiement ","99,00$", "50,00$","75,00$",];
	$duree = ["15","30","30","60","15","15","60","30","30","60","30","30"];
	
	$statuts = ["oui","","","oui","","","oui","","","oui","",""];
	
	$fin = ["","","Mettre fin","","Mettre fin","","","","","",""];
	
	$statuts_color = ["orange2","","blue2","orange2","blue2","","orange2","orange2","orange2","orange2","orange2"];
	
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
	    $k++;	    if($k>11) $k=0;
	    
	  
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
			<th class="date"><?php echo __('Date'); ?></th> 
    			<th class="agent"><?php echo __('client'); ?></th> 
			<th class="desactivation"><div style="display:inline-block;text-align:left;"><?php echo __('Désactivation<br/>mode Téléphone'); ?></div></th> 
    			

    		    </tr> 
    		</thead> 
    		<tbody>

<?php foreach ($appointments as $appointment) : ?>
    <tr> 
	
	
<td class="date"><?= $appointment['date']; ?></td> 
<td class="agent"> 
	<?php echo $appointment['Agent']["pseudo"];?> 
</td> 
<td class="desactivation <?php echo $appointment['statut']['color']; ?>"><?php echo $appointment['statut']['label']; ?></td> 

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
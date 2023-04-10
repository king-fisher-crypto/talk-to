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


<section class="affiliate_payment-page page jswidth">


    <article class="marge">
	<h1 class="">  <?= __('Mes Versements Affiliation') ?></h1>
	<?= __("Les reversements sont effectués une fois par mois sur les coordonnées de paiement indiquées par vos soins dans la page \" Mes coordonnées de paiement \".") ?>
	
    </article>

    
    

    <div class="cadre_table ">
	
	
		

	<?php
	$appointments = [];

	$appointment = [];
	$appointment['date'] = "24/04/22";
	$appointment['Agent']["pseudo"] = "Lorem Ipsum";

	$appointment['duree'] = "5%";

	$appointment['statut'] = [];
	
	$mois = ["Mai, 2022","April, 2022","Mars, 2022","Février, 2022","Janvier, 2022","Décembre, 2021","Novembre, 2021","Octobre, 2021", "Septembre, 2021", "Août, 2021", "Juillet, 2021", "Juin, 2021"];
	
	$montants = ["15,25$","155,25$","1555,25$","15,25$","155,25$","1555,25$","1555,25$","1555,25$","1555,25$","1555,25$","1555,25$","1555,25$","1555,25$","1555,25$"];
	$commission = ["3,05$","31,05$","311,05$","3,05$","31,05$","311,05$","3,05$","31,05$","311,05$","3,05$","31,05$","311,05$"];
	

	
	$Total= ["En cours","124,20$","1 244,2$","149,02$","124,20$","1 244,2$","149,02$","124,20$","1 244,2$","149,02$","124,20$", "1 244,2$"];
	
	$statuts = ["À venir","En cours","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué","Effectué"];
	$statuts_color = ["","orange2","blue2","blue2","blue2","blue2","blue2","blue2","blue2","blue2","blue2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $appointment['mois']  = $mois[$k]; 
	    $appointment['montants']  = $montants[$k]; 
	    $appointment['commission'] = $commission[$k]; 
	    
	    $appointment['total']  = $Total[$k]; 
	   
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
			
    			<th class="agent"><?php echo __('Mois / An'); ?></th> 
    			<th class="duree"><?php echo __('Montant'); ?></th> 
    			<th class="duree"><?php echo __('Commission')."<br/>".__("site");; ?></th> 
			<th class="duree"><?php echo __('Total'); ?></th> 
			<th class="date"><?php echo __('Date')."<br/>".__("reversement"); ?></th> 
    			<th class="statut"><?php echo __('Statut'); ?></th> 
    			<th class="fichier"></th> 
    		

    		    </tr> 
    		</thead> 
    		<tbody>









<?php 
$a=0;
foreach ($appointments as $appointment) :
    $a++;
    ?>
    <tr> 
	
	<td class="mois"> 
	<?php echo $appointment['mois'];
	?> 
	</td> 
<td class="montants"><?= $appointment['montants']; ?></td> 
<td class="commission"><?= $appointment['commission']; ?></td> 
<td class="total <?=($appointment['total']=='En cours')?"":"blue2"; ?> "><?php echo $appointment['total']; ?></span></td> 
<td class="date">24/04/22</td> 
<td class="statut <?= $appointment['statut']["color"]; ?> "><?php echo $appointment['statut']["label"]; ?></span></td> 
<td class=""><?php if($a>2 && $a!=12 && $a!=13 ){?><img class="croix" src="/theme/black_blue/img/fichier.svg"><?php } ?></td> 
</tr> 
<?php endforeach; ?>





</tbody>
    <?php endif; ?> 
	    </table> 

	</div>

    </div>
    <a  href="">
    <div class="txt_bas">Retourner sur la page " Ma Facturation " pour déclencher un paiement</div></a>
    
    
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
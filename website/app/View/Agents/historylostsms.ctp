<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
//echo $this->Session->flash();
?>


<section class="subscription-page a-historylostemail-page page ">


    <article>
	<h1 class="">  <?= __('Mes SMS perdus') ?></h1>
		<?= __("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nibh fermentum amet in et etiam. Elementum amet consequat tellus eget euismod velit at. At blandit eu viverra gravida eleifend dignissim purus. Tellus ultrices interdum turpis ultricies imperdiet consequat. At non feugiat quam ultrices tincidunt tempus risus. Venenatis nunc diam, sed adipiscing. Purus nisi erat purus pretium at tempus ut.") ?>	
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
	
	$statuts = ["","","","","","","","oui","oui","oui","oui"];
	
	$fin = ["","","Mettre fin","","Mettre fin","","","","","",""];
	
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
    	<div  class="overflow ">
    	   

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  
			<th class="date"><?php echo __('Date'); ?></th> 
    			<th class="agent"><?php echo __('client'); ?></th> 
		
    		    </tr> 
    		</thead> 
    		<tbody>

<?php foreach ($appointments as $appointment) : ?>
<tr> 
	
<td class="date"><?= $appointment['date']; ?></td> 
<td class="agent"> 
	<?php echo $appointment['Agent']["pseudo"];?> 
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
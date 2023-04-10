<?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js'); ?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>
<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));

$message = __("En validant cette demande de remboursement je confirme que cette somme sera déduite de mon compte LiviMaster, que le client sera remboursé et que je ne pourrais plus me rétracter.");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');

echo $this->Session->flash();
?>

<section class="subscription-page a-clients_refund-page page jswidth">


    <article>
	<h1 class="">  <?= __('Mes remboursements Clients') ?></h1>
	<?= __("En cas de litige ou autre, vous avez la possibilité de rembourser un client, cette action est immédiate et irréversible, les fonds seront automatiquement prélevés sur vos crédits en cours, attribués au client que vous aurez sélectionné et celui-ci en sera averti par email.") ?>	
    </article>
 
    
    
    <div class="div_criteres"> 

	   	<div id="tabs_k2" class="cs-selecteur-de-criteres-container _form_input">
        <div class="cs-selecteur-de-criteres">
            <div class="cs-sdc-date cs-search">
		<img src="/theme/black_blue/img/loupe_bleu.svg"> 
		<input type="text" id="datepicker1" class="form-control lh22-33 no_select" placeholder="date" readonly="readonly"></div>
            <div class="cs-sdc-client cs-search">
		<input name="client-search" class="lh22-33" placeholder="client"></div>
            <div class="cs-sdc-mode cs-search"><span class="lh22-33">mode</span>
		
	    <img class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg">

	    </div>
        </div>
        <div class="cs-mode-list cs-hover-type">
	    
	    <p class="lh20-30">Téléphone</p>
            <p class="lh20-30">Chat</p>
            <p class="lh20-30">Email</p>
            <p class="lh20-30">SMS</p>
            <p class="lh20-30">Webcam</p>
            <p class="lh20-30">Masterclass</p>
            <p class="lh20-30">vidéos formation</p>
            <p class="lh20-30">Photos à la demande</p>
            <p class="lh20-30">Documents-PDF</p>
        </div>
    </div>
	    
	     <div class="btn chercher h70 lh24-28 p20spe blue2 up_case"    title="<?= __('chercher') ?>"><?= __('chercher') ?></div> 	
	</div>  
    
    
    


    <div class="cadre_table ">	

	<?php
	$appointments = [];

	$appointment = [];
	$appointment['date'] = "24/04/22 15:11:25 ";
	$appointment['Agent']["pseudo"] = "Lorem Ipsum";
	
	$prestations = ["Téléphone","Chat","Email","Email","Vidéos formation","Documents-PDF","Téléphone","Chat","Photos à la demande","Chat", "Videos à la demande","Masterclass","Documents-PDF","SMS",];;

	$appointment['duree'] = "5%";

	$appointment['statut'] = [];
	
	$mois = [1,2,3,1,2,3,1,2,3,1,2,3];
	
	$montants = ["30,00$","50,00$","75,00$","Via requête Paiement ","99,00$","30,00$","50,00$","75,00$","Via requête Paiement ","99,00$", "50,00$","75,00$",];
	$duree = ["15","30","30","60","15","15","60","30","30","60","30","30"];
	
	$statuts = ["En cours","En cours","","En cours","","En cours","En cours","Remboursé","Remboursé","En cours","En cours"];
	
	$fin = ["","","Rembourser","","Rembourser","","","","","",""];
	
	$statuts_color = ["orange2","orange2","blue2","orange2","blue2","orange2","orange2","blue2","blue2","orange2","orange2"];
	
	$k=0;
	for($j=1;$j<=3;$j++)
	    {
	for($i=1;$i<=6;$i++)
	    {
	    $appointment['duree']  = $duree[$k]." min"; 
	    $appointment['prestation']  = $prestations[$k]; 
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
			
    			<th class="client"><?php echo __('client'); ?></th> 
			<th class="prestation "><?php echo __('prestation'); ?></th> 
    			<th class="date"><?php echo __('date'); ?></th> 
    			<th class="montant"><?php echo __('montant'); ?></th>
			<th class="action"><?php echo __('action'); ?></th> 
    			<th class="statu"><?php echo __('statut'); ?></th> 
    			

    		    </tr> 
    		</thead> 
    		<tbody>

<?php foreach ($appointments as $appointment) : ?>
    <tr> 
	
	<td class="client"> 
	<?php echo $appointment['Agent']["pseudo"];
	?> 
</td> 

<td class="prestation"><?php echo $appointment['prestation']; ?></td> 
<td class="date"><?= $appointment['date']; ?></td> 
<td class="duree">14,90$</td> 
<td class="action "> <a class="underline"><?= $appointment['fin']; ?></a> 
</td>
<td class="statut <?php echo $appointment['statut']['color']; ?>"><?php echo $appointment['statut']['label']; ?></td> 
 
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

<style>
    
    #modal-confirmation .modal-content{
    width: calc(629px*var(--coef)); 
    height: calc(270px*var(--coef));
}
#modal-confirmation #message{
    font-size: calc(24px*var(--coef));
    line-height: calc(28px*var(--coef));
}

@media only screen   and (max-width : 767px)
{
   
    
    #modal-confirmation .modal-content{
    width: calc(347px*var(--coef)); 
    height: calc(270px*var(--coef));
}
    #modal-confirmation #message{
	font-size: calc(18px*var(--coef));
	line-height: calc(21px*var(--coef));
    }
    
}


</style>

<script>
window.onload = function () {
        
	
	$('body').on('click','.cs-selecteur-de-criteres .cs-sdc-mode',function(e){
		e.preventDefault();
		let $this = $(this);
		let $parent = $(this).closest('.cs-selecteur-de-criteres-container');
		if($this.find('> img').hasClass('active')){
			$parent.find('.cs-mode-list').removeClass('active');
			$this.find('> img').removeClass('active');
			
		}else{
		    
			$parent.find('.cs-mode-list').addClass('active');
			$this.find('> img').addClass('active');
		}
	});

	$('body').on('click','.cs-selecteur-de-criteres-container .cs-mode-list.active > p',function(e){
		e.preventDefault();
		let $this = $(this);
		let $parent = $(this).closest('.cs-selecteur-de-criteres-container');
		let value = $this.html();
		$parent.find('.cs-selecteur-de-criteres .cs-sdc-mode > span').html(value);
		$this.closest('.cs-mode-list').removeClass('active');
		$parent.find('.cs-selecteur-de-criteres .cs-sdc-mode > img').removeClass('fa-angle-up').addClass('fa-angle-down');
	});
	
	
	
	$("table.stries .action a").click(function ()
        {
             $("#modal-confirmation").modal();

        });
	
	
	/////////// DATE PICKER //////////
	
	//var btn_datepicker = document.getElementById('.cs-selecteur-de-criteres-container .cs-sdc-date');
	var btn_datepicker = $(".cs-selecteur-de-criteres-container .cs-sdc-date")[0]

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

                    console.log("from" ,from,"to", to);

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
	
	
	
    }

</script>
<?php 
/*
 
 * lié à AgentsController > function history
 * et 
 * ExtranetController
 * function _history($role){
 * 
 */

?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js'); ?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>

<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
//echo $this->Session->flash();
?>


<section class="a-history-page page jswidth">
    
    
    <article class="">
	<h1 class="">  <?= __('Mes Communications') ?></h1>
    </article>
   
    
    <div class="cadre_table ">

	<div class="btns"> 
	    <a id="btn_datepicker" class="btn spe1  date transparent daterange b "    title="<?= __('dates') ?>"><img src="/theme/black_blue/img/calendrier.svg"> 01/04/22 - 24/04/22</a> 
	</div>  	

	  	
		
<?php

$historiqueComs = [];

for($i=0;$i<4 ;$i++)
{    
$historiqueComs[] = [ "media"=>"Téléphone", "info"=>"", "Durée"=>"00:01:25","date"=>"24/04/22 15:11:25","montant"=>"25 651,25$"];
$historiqueComs[] = [ "media"=>"Chat", "info"=>"", "Durée"=>"00:01:25","date"=>"24/04/22 15:11:25","montant"=>"25,22$"];
$historiqueComs[] = [ "media"=>"SMS", "info"=>"Forfait 1", "Durée"=>"00:01:25","date"=>"24/04/22 15:11:25","montant"=>"65,78$"];
$historiqueComs[] = [ "media"=>"Email", "info"=>"Email-6h", "Durée"=>"00:01:25","date"=>"24/04/22 15:11:25","montant"=>"25 651,25$"];
$historiqueComs[] = [ "media"=>"SMS", "info"=>"Forfait 2", "Durée"=>"00:01:25","date"=>"24/04/22 15:11:25","montant"=>"251,25$"];
}
    
?>



<?php if (empty($historiqueComs)) : ?>
	<div class="txt_cent">
		<?php echo __('Vous n\'avez eu aucune communication avec un expert.'); ?>	</div>
<?php else : ?>

<div class="overflow jswidth"><img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">
    
    <table class=" stries" ><thead class=""> 
    		<tr>  
		    <th class="client"><?php echo __('Client'); ?></th> 
    		    <th class="media"><?php echo __('Media'); ?></th> 
    		    <th class="info"><?php echo __('Info'); ?></th> 
    		    <th class="duree"><?php echo __('Durée'); ?></th> 
    		    <th class="date"><?php echo __('Date'); ?></th> 
    		    <th class="montant"><?php echo __('Montant'); ?></th> 
    		</tr> 
    	    </thead> 
    	    <tbody>
		 
		  
			    
		  
<?php foreach ($historiqueComs as $historique) : ?>
			<tr> 
			    
			    <td class="client"> 
				Lorem ipsum dolor sit amet
			    </td> 
			    <td class="media"><?=$historique["media"];?></td> 
			    
			    <td class="info"><?=$historique["info"];?></td> 
			    <td class="duree">00:01:25</td> 
			    
			    <td class="date">24/04/22 15:11:25 </td> 

			     <td class="montant"><?=$historique["montant"];?></td> 
			     
			     
			   
			</tr> 
    <?php endforeach; ?>
		
		
			
			
    	    </tbody>
<?php endif; ?> 
	</table> 
	    
	    </div>
	
	
 	
	</table>
	
	
	<div class="div_total">
	    <span><?= __('total'); ?></span>
	    <span>625 545,56 $</span>
	</div>
	    
    </div>
    
    
    
    <div class="div_btn_pre">
    <a class="btn_pre  p25 t18 m17 up_case" title="<?=__('Voir mes gains et reversements');?>"><?=__('Voir mes gains et reversements');?></a>
    </div>
    
<?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>

    <input type="hidden" id="daterange" class="form-control"  >

</section>

<style>
 .ui-menu .ui-menu-item-wrapper {
    padding:  calc(10px*var(--coef)) calc(5px*var(--coef)) calc(10px*var(--coef)) calc(60px*var(--coef));
    font-weight: 600;
    font-size: calc(18px*var(--coef));
    line-height: calc(24px*var(--coef));
}

.mode-de-communication {
    position: relative;
    margin-left: calc(90px*var(--coef)); 
    /*padding: 0 calc(38px*var(--coef)) 0 calc(28px*var(--coef));*/
}

.mode-de-communication>input{
  /*margin-left: 0px !important;*/
  border: unset;
  padding-left: calc(61px*var(--coef));
  outline: unset;
  cursor: text;
  color: #878787 !important;
  text-align: left;
  /*background: #fbfbfb !important;*/
  height: calc(45px*var(--coef));
  width: calc(354px*var(--coef));
}

.mode-de-communication > img {
  position: absolute;
  top: 50%;
  transform: translate(0,-50%);
  left: calc(30px*var(--coef));
  height: calc(20px*var(--coef));
  margin-right: calc(15px*var(--coef));
  cursor: pointer;
}

</style>
<script>

window.onload = function () {



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





     
 };

</script>
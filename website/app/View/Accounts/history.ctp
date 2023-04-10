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


<section class="history-page page jswidth">
    
    
    <article class="marge">
	<h1 class="">  <?= __('Mes Consultations') ?></h1>
	<?= __('Toutes vos consultations par Téléphone, Chat, Webcam, Sms et Email sont répertoriées et vous avez la possibilité de relire les consultations par Chat, SMS et Email.') ?>
    </article>
   
    
    <div class="cadre_table ">

	<div class="btns"> 
	    <a id="btn_datepicker" class="btn spe1  date transparent daterange b "    title="<?= __('dates') ?>"><img src="/theme/black_blue/img/calendrier.svg"> 01/04/22 - 24/04/22</a> 
	    
    <div class="mode-de-communication _form_input btn spe2  mode transparent" >
        <input type="text" class="  lgrey2 lh22-33" placeholder="mode de communication" title="<?= __('mode de communication') ?>">
	    <img src="/theme/black_blue/img/loupe_bleu.svg">
    </div>  	
	
	    
	    
	</div>  	
	
	
<?php if (empty($historiqueComs)) : ?>
	<div class="txt_cent">
		<?php echo __('Vous n\'avez eu aucune communication avec un expert.'); ?>	</div>
<?php else : ?>
	<div class="overflow jswidth">
	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">
	<table class=" stries" > 
	   
    	    <thead class=""> 
    		<tr>  
		    <th class="date"><?php echo __('Date'); ?></th> 
    		    <th class="agent"><?php echo __('Agent'); ?></th> 
    		    <th class="mode"><?php echo __('Mode'); ?></th> 
    		    <th class="cout"><?php echo __('Coût')."<br/>/". __('min'); ?></th> 
    		    <th class="duree"><?php echo __('Durée'); ?></th> 
    		    <th class="montant"><?php echo __('Montant'); ?></th> 
    		    <th class="detail"><?php echo __('Détail'); ?></th> 
    		    <th class="remarque"><?php echo __('Remarque'); ?></th> 
    		</tr> 
    	    </thead> 
    	    <tbody>
		 
		  
		  
		  
		  <tr><td class="date">24/04/22 15:11:25 </td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">Téléphone</td><td class="">2,99$</td><td class="duree">01:24:52</td><td class="montant">15,25$ </td><td class="detail"></td><td class="remarque"></td></tr>

			<tr><td class="date">24/04/22 15:11:25 </td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">Webcam</td><td class="">2,99$</td><td class="duree">01:24:52</td><td class="montant">15,25$ </td><td class="detail"></td><td class="remarque">Remboursé</td></tr>


<tr><td class="date">24/04/22 15:11:25</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">Email</td><td class="">20,00$</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">20,00$</td><td class="detail"><img class="redirection" src="/theme/black_blue/img/redirection.svg" alt="See"></td><td class="remarque"></td></tr>	


<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">Chat</td><td class="">2,99$</td><td class="duree">01:24:52</td><td class="montant">15,25$</td><td class="detail"><img class="redirection" src="/theme/black_blue/img/redirection.svg" alt="See"></td><td class="remarque"></td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	
		  
		  
		  
		  
    <?php foreach ($historiqueComs as $historique) : ?>
			<tr> 
			    <td class="date"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),
					$historique['UserCreditLastHistory']['date_start']), '%d/%m/%y %Hh%M'); ?></td> 
			    <td class="agent"> 
				<?php
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
				?> 
				
<!--				<span class="visible-xs visible-only-768 h6 mt15 pull-right"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$historique['UserCreditLastHistory']['date_start']), '%d/%m/%y %Hh%M'); ?></span>-->
		

			    </td> 
			    <td class="mode"><?php echo __($consult_medias[$historique['UserCreditLastHistory']['media']]); ?></td> 
			    <td class=""><?php echo '-' . $historique['UserCreditLastHistory']['credits'] . ' ' . __('crédit(s)'); ?></td> 
			    <td class="duree"><?php
				echo (empty($historique['UserCreditLastHistory']['seconds']) ?  '<img class="croix" src="/theme/black_blue/img/croix.svg">'  : gmdate('H:i:s',
						$historique['UserCreditLastHistory']['seconds'])
				);
				?></td> 
			     <td class="montant">15.25$</td> 
			     <td class="detail">
				 
				 <?php
				switch ($historique['UserCreditLastHistory']['media'])
				    {
				    case 'chat' :
					if (isset($historique['discussion']))
					    {
					    echo $this->Html->link('<img class="redirection" src="/theme/black_blue/img/redirection.svg" alt="'.__('Voir').'">'
						   ,
						    array('controller' => 'accounts', 'action' => 'chat_history'),
						    array('escape' => false, 'class' => 'redirection',
							'param' => $historique['discussion'])
					    );
					    }
					break;
				    case 'email' :
					if (isset($historique['discussion']))
					    {
					    echo $this->Html->link('<img class="redirection" src="/theme/black_blue/img/redirection.svg" alt="'.__('Voir').'">',
						    array('controller' => 'accounts', 'action' => 'mails', 'idMail' => $historique['discussion']),
						    array('escape' => false, 'class' => '')
					    );
					    }
					break;
				    }
				?>
				 
				 
			     </td> 
			    <td class="remarque">
				
			    </td> 
			</tr> 
    <?php endforeach; ?>
			
    


<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">04/02/22 07h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Cécilia Jentrun</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">5.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-1</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/23 00h35</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jen Christophe D.</a></td><td class="mode">Téléphone</td><td class="">12.78€</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">755.25$</td><td class="detail"></td><td class="remarque"></td></tr>	


<tr><td class="date">27/02/23 00h35</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jen Christophe D.</a></td><td class="mode">Téléphone</td><td class="">12.78€</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">755.25$</td><td class="detail"></td><td class="remarque"></td></tr>	

<tr><td class="date">04/02/22 07h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Cécilia Jentrun</a></td><td class="mode">SMS</td><td class="">Forfait-2</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">5.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	


<tr><td class="date">27/02/23 00h35</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jen Christophe D.</a></td><td class="mode">Téléphone</td><td class="">12.78€</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">755.25$</td><td class="detail"></td><td class="remarque"></td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-3</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-3</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	

<tr><td class="date">27/02/22 13h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Jean-Pierre Boris</a></td><td class="mode">SMS</td><td class="">Forfait-3</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">15.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	


<tr><td class="date">04/02/22 07h40</td><td class="agent"> <a href="/fre/agents-en-ligne/pierre-j-3356" class="sm-sid-photo"><img src="/media/photo/3/3/3356_listing.jpg" class="rounded" alt="Pierre J"></a><a href="/fre/agents-en-ligne/pierre-j-3356" class="agent-pseudo">Cécilia Jentrun</a></td><td class="mode">SMS</td><td class="">Forfait-3</td><td class="duree"><img class="croix" src="/theme/black_blue/img/croix.svg"></td><td class="montant">5.25$</td><td class="detail"></td><td class="remarque">Remboursé</td></tr>	
			
			
			
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


    /* COMPOSANT HAI YEN */
	let mode = ['<?= __('Téléphone') ?>','<?= __('Webcam') ?>','<?= __('Email') ?>','<?= __('Chat') ?>','<?= __('SMS') ?>'];
	
	
	$('.mode-de-communication > input').autocomplete({
        source: function (request, response) {
        	if(request.term == 911){
        		response(mode);
        	}else{
        		var results = $.ui.autocomplete.filter(mode, request.term);
            	response(results.slice(0, 10));      
        	}
        },
        change: function (event, ui) {
            if (ui.item === null) {
                // $('#university_name').val("");
            }
        },
    }).focus(function () {
	    $(this).autocomplete("search", "911");
	});
        
        
    $('body').on('keyup','.mode-de-communication> input',function(){
    	let length = $(this).val().length;
    	if(length == 0){
    		$(this).autocomplete("search", "911");
    	}
    });

     
 };

</script>
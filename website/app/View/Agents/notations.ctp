<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
$message = __("Souhaitez vous valider ce message  ?");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');
echo $this->element('Utils/modal-notes');
?>


<section class="a-notations-page page marg180 ">


    <article class="">
	<h1 class="">  <?= __('Mes Notes') ?></h1>


	<div class="btns"> 

	    <a class="btn spe2 h77 p22spe recherche agent transparent bord2 lgrey2"    title="<?= __('agent') ?>">
		<img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg">
		<form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __('client') ?>"></form>
	    </a> 	

	    <div class="btn chercher h70 lh24-28 p20spe blue2 up_case"    title="<?= __('chercher') ?>"><?= __('chercher') ?></div> 	
	</div>  

    </article>




    <div class="cadre_table ">

	<?php
	$datas = [];

	for ($i = 0; $i < 4; $i++)
	    {
	    $datas[] = ["media" => "Téléphone", "info" => "", "Durée" => "00:01:25", "date" => "24/04/22 15:11:25",
		"montant" => "25 651,25$"];
	    $datas[] = ["media" => "Chat", "info" => "", "Durée" => "00:01:25", "date" => "24/04/22 15:11:25",
		"montant" => "25,22$"];
	    $datas[] = ["media" => "SMS", "info" => "Forfait 1", "Durée" => "00:01:25",
		"date" => "24/04/22 15:11:25", "montant" => "65,78$"];
	    $datas[] = ["media" => "Email", "info" => "Email-6h", "Durée" => "00:01:25",
		"date" => "24/04/22 15:11:25", "montant" => "25 651,25$"];
	    $datas[] = ["media" => "SMS", "info" => "Forfait 2", "Durée" => "00:01:25",
		"date" => "24/04/22 15:11:25", "montant" => "251,25$"];
	    }
	?>

	<?php if (empty($datas)) : ?>
    	<div class="txt_cent">
    <?php echo __('Aucune note'); ?>	</div>
<?php else : ?>
    	<div  class="overflow jswidth">
    	    <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	    <table class=" stries" > 

    		<thead class=""> 
    		    <tr>  
    			<th class="date"><?php echo __('Client'); ?></th> 
    			<th class="link"><?php echo __('Dernière<br/>consultation'); ?></th> 
    			<th class="date"><?php echo __('Date'); ?></th> 
    			<th class="notes"><?php echo __('Mes notes'); ?></th> 

    		    </tr> 
    		</thead> 
    		<tbody>



				<?php foreach ($datas as $data) : ?>
			    <tr> 
				<td class="client">
				    <?php
				    /* echo $this->Html->link($code_promo['Agent']['pseudo'], array('language' => $this->Session->read('Config.language'), 'controller' => 'agents','action' => 'display', 'link_rewrite' => strtolower(str_replace(' ', '-',	$code_promo['Agent']['pseudo'])), 'agent_number' => $code_promo['Agent']['agent_number']), array('class' => 'agent-pseudo', 'escape' => false));
				     */
				    ?> 
				    Lorem ipsum sit amet
				</td> 
				<td class="link"> <img class="redirection link" src="/theme/black_blue/img/redirection.svg" alt="See"></td> 
				<td class="date">24/04/22 15:11:25 </td> 
				<td class="note">
				    <img class="redirection notes" src="/theme/black_blue/img/notes.svg" alt="See">
				</td> 

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

<style>
    #modal-confirmation.modal .modal-content {
	width: calc(600px*var(--coef));
	height: calc(350px*var(--coef));
    }

    #modal-confirmation.modal .btn.validate {
	padding: 0 calc(40px*var(--coef)) 0 calc(38px*var(--coef));
    }

    #modal-confirmation.modal .modal-content  .msg_date{
	font-weight: 400;
	color: #878787;
	margin-top: calc(30px*var(--coef));
    }

    /* tablets ----------- */
    @media only screen and (max-width : 1024px) {
	#modal-confirmation.modal .modal-content {
	    width: calc(400px*var(--coef));
	    height: calc(300px*var(--coef));
	}
    }

    /* Smartphones ----------- */
    @media only screen   and (max-width : 767px)
    {
	#modal-confirmation.modal .modal-content {
	    width: calc(347px*var(--coef));
	    height: calc(300px*var(--coef));
	}

	#modal-confirmation.modal .modal-content  .msg_date{
	    margin-top: calc(45px*var(--coef));
	}

    }


</style>


<script>
    document.addEventListener("DOMContentLoaded", function ()
    {

	/*  POPUP */ 
        $("#modal-confirmation .btn.validate").html("<?= __('continuer sur') . " " . Configure::read('Site.name'); ?>");


        $(".link").click(function ()
        {
            let tr = $(this).parentsUntil("table.stries", "tr");
            let client = $(tr).find(".client").text();
            let date = $(tr).find(".date").text();
            let msg = "<div class='consultation'><?= __('dernière consultation<br/>avec ') ?>" + client
                    + "<div>";
            msg += "<div class='msg_date'>" + date + "<div>";

            $("#message").html(msg)
            $("#modal-confirmation").modal();

        });

        addEventListener('confirm', go_to_link, false);
        function go_to_link()
        {
            window.location.href = "<?= Configure::read('Site.baseUrlFull'); ?>";
        }

	/*  POPUP NOTES */ 

        $("table.stries .notes").click(function ()
        {
	    /*
            let tr = $(this).parentsUntil("table.stries", "tr");
            let client = $(tr).find(".client").text();
            let date = $(tr).find(".date").text();
            let msg = "<div class='consultation'><?= __('dernière consultation<br/>avec ') ?>" + client
                    + "<div>";
            msg += "<div class='msg_date'>" + date + "<div>";

            $("#message").html(msg)
	    */
            $("#modal-notes").modal();

        });

        addEventListener('save', save_notes, false);
        function save_notes()
        {
            //window.location.href = "<?= Configure::read('Site.baseUrlFull'); ?>";
        }

    }); // fin  DOMContentLoaded
</script>


<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="certif_account-page page jswidth">


    <article class="">
	<h1 class="">  <?= __('Certification du compte') ?></h1>

	<?= __("Pour pouvoir certifier votre compte, pour paiement Affiliation, veuillez nous fournir les documents suivants:") ?>

    </article>


    <div class="bloc_image ">
	<img class="img_btn2" src="/theme/black_blue/img/certif_compte.png">





	<table border="0" cellspacing="0" cellpadding="0" class="lh26-34">
	    <tr>
		<td class="blue2">1.</td>
		<td><?= __("Scan ou photo de votre carte d’identité.") ?></td>
	    </tr>
	    <tr>
		<td >&nbsp;</td>
		<td></td>
	    </tr>
	    <tr>
		<td class="blue2">2.</td>
		<td><?= __("Photo de vous tenant la carte d’identité ainsi qu’une pancarte avec écrit “<span2 class='blue2 '>Livitalk</span2>”, votre pseudo et la date du jour.") ?></td>
	    </tr>
	</table>
    </div>


    <div class="status">
	<DIV class="b lh24-28c txt"><?= __('Quel est votre statut ?') ?></DIV>
	<div class="radio_btns">
	    <label>
		<input class="square_radio" type="radio" name="radio">
		<span class="btn  b lh28-42 h80"><?= __('Compte Personnel') ?></span>
	    </label>

	    <label>
		<input  class="square_radio" type="radio" name="radio">
		<span class="btn multi b lh28-42 h80"><?= __('Compte Professionnel') . "<br/>" . __('Société'); ?></span>
	    </label>
	</div>
    </div>



    <div class="white_block_mobile">

	<div class="white_block white_block1">
	    <div class=" bloc1">
		<div class="bloc bloc1_1">
		    <div class="title b lh26-33"><?= __('Scan ou photo de votre carte d’identité.') ?></div>
		    <div class="note lh22-28"><?= __('Taille maximum 10 Mo.') ?></div>

		</div>

		<div class="modifier_identite bloc bloc1_2 blue2 lh22-28 underline up_case"><a class="blue2" href=""><?= __('Modifier document Téléchargé') ?></a></div>

	    </div>

	    <div class=" bloc2">

		<div class="bloc bloc2_1">
		    <div class="title b lh26-33"><?= __("Photo de vous tenant la carte d’identité ainsi qu’une pancarte avec écrit: “<span2 class='blue2'>Livitalk</span2>”, votre pseudo et la date du jour.") ?></div>
		    <div class="note lh22-28"><?= __('Taille maximum 10 Mo.') ?></div>

		</div>

		<div class="btn ajouter up_case bloc bloc2_2 blue2 lh25-29b h70c"> <img class="img_btn4" src="/theme/black_blue/img/ajouter.svg"> <?= __('ajouter') ?></div>

	    </div>

	</div>
	

	<div class="white_block white_block2">

	    <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">

	    <div class="bloc1">

		<div class="bloc bloc1_1">
		    <div class="title b lh26-33"><?= __("Document société avec numéro de registre, nom et adresse") ?></div>
		    <div class="note lh22-28">
			<?= __('Uniquement si vous êtes déclaré comme professionnel.') ?>
		    </div>
		    <div class="note lh22-28">
			<?= __('Taille maximum 10 Mo.') ?>
		    </div>
		</div>
		
		<div class="btn sgrey ajouter2 up_case bloc bloc1_2 lh25-29b h70c"> <img class="img_btn4" src="/theme/black_blue/img/ajouter_white.svg"> <?= __('ajouter') ?></div>

	    </div>

	</div>


   

    <label class="CGV ">
	<input class="square_radio" type="checkbox" name="checkbox">
	<span class="  lh24-28d "><?= __('J’ai lu et j’accepte');?>
	    <a href="" class="blue2"><?= __('les Conditions Générales de Services ')?></a></span>
    </label>

   

</div> <!--  fin white_block_mobile-->

 <div class="div_btn">
    <div class="btn lh25-29c h85b certifier up_case blue2">
	<?= __('certifier');?>
    </div>
    </div>


    <?php //if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



    <?php
//echo $this->Frontblock->getRightSidebar();
    ?>


</section>


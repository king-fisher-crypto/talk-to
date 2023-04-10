<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class="loyalty_bonus-page page jswidth">


    <article class="">
	<h1 class="">  <?= __('Mes Bonus fidélités') ?></h1>
	<?= __("Lors de chaque paiement vous cumulez des points fidélité vous permettant d'accéder à des offres personnalisées. Ces offres seront régulièrement mises à jour et visibles en peu plus bas sur cette page, nous vous réservons plein de surprises ! Consultations gratuites, Fast-Pass, mais aussi la possibilité de participer à des concours ou de rencontrer vos LiviMasters préférés en live, LiviTlak s'occupe de tout ! ") ?>
	  
    </article>

   <div class="btns blue2 uppercase txt_cent">
	<?= __('A VENIR TRES BIENTOT, UNE ENORME SURPRISE !') ?>
	
    </div>
   

    
    <?php //if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>


</section>
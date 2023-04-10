<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes gains') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Mes gains') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Mes gains').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->

				  	<div class="box_account well well-account well-small">
					<?php
					if(!count($sponsorship)){
						echo '<p>Aucun gain obtenu pour le moment.</p>';
					}else{
					?>
				  	<div class="table-responsive">
                    <table class="table table-striped no-border pricing-table table-mobile text-center">
        				<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><?php echo __('Libellé'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Crédit'); ?></th> 
				  	 			<th class="text-center"><?php echo __('Date d\'acquisition'); ?></th> 
				  	 		</tr> 
				  	 	</thead>
            		
            		<tbody>	
                    <?php foreach($sponsorship as $key => $loyal):  ?>
                    <tr>
                        <td><?=__('Gain parrainage') ?></td>
                        <td><?php echo $loyal['Sponsorship']['bonus'].' '.$loyal['Sponsorship']['bonus_type']; ?></td>
                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$loyal['Sponsorship']['date_recup']),' %d/%m/%y %Hh%M'); ?></td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
                    </table>
                    </div>
					<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator);
					
					}
					 ?>
				  	</div>

					</div><!--content_box END-->
				

				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
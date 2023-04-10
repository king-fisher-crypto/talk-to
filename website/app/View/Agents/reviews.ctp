<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes avis') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

						<div class="page-header">
							<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Répondre aux avis reçu') ?></h2>
							 <?php
						echo $this->Session->flash();
						/* titre de page */
						echo $this->element('title', array(
							'breadcrumb' => array(
								0   =>  array(
									'name'  =>  'Accueil',
									'link'  =>  Router::url('/',true)
								),
								1   =>  array(
									'name'  =>  __('Mes avis'),
									'link'  =>  ''
								)
							)
						));
		?>

						</div><!--page-header END-->
 <?php if(empty($reviews)):
            echo __('Vous n\'avez aucun avis client.');
        else: ?>

						<div class="table-responsive">
                       
				  	 <table class="nx_table table table-striped no-border table-mobile text-center table-review-agent"  style="display:inline-block;width:100%;"> 
				  	 	<thead class="hidden-xs text-center" style="display:inline-block;width:100%;"> 
				  	 		<tr style="display:inline-block;width:100%;"> 
				  	 			<th class="text-center"><?php echo __('Client'); ?></th> 
				  	 			<!--<th class="text-center"><?php echo __('Date de l\'avis'); ?></th> -->
                                <th class="text-center"><?php echo __('Message'); ?></th>
				  	 			<th class="text-center"></th> 
				  	 			
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody style="display:inline-block;width:100%;"> 
                        <?php foreach($reviews as $review): ?>
				  	 		<tr  style="display:inline-block;width:100%;"> 
				  	 			<td class="veram resize-img"> 
				  	 				<?php echo $review['User']['firstname']; ?> 
				  	 			</td> 
				  	 			<!--<td class="veram"><?php echo $this->Time->format($review['Review']['date_add'],'%d/%m/%y %Hh%M'); ?></td> -->
                                <td class="veram" style="line-height:16px"><?php echo h($review['Review']['content']);
								if($review['Review']['reponse']){
									echo '<br /><strong>Votre réponse :</strong><br />'.h($review['Review']['reponse']['content']);	
								}
								
								 ?></td> 
				  	 			<td class="veram"> <?php 
								if(!$review['Review']['reponse']){
									 echo $this->Html->link('<i class="glyphicon glyphicon-edit-in"></i> '.__('Répondre'),
                                                array('controller' => 'agents', 'action' => 'answer_review', '?' => array('id'=>$review['Review']['review_id'])),
                                                array('escape' => false, 'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 ')
                                            );
								}
								 ?></td> 
				  	 		</tr> 

				  	 		<?php endforeach; ?>
				  	 	</tbody> 
				  	 </table> 
                     
				  	</div><!--table-responsive-->

				  	<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>
                    <?php endif; ?>
                    </div><!--content_box END-->
					
				
					


			</div><!--col-9 END-->
            <?php
				echo $this->Frontblock->getRightSidebar($agentStatus);
			?><!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>
<?php
//Ajax, l'historique de la conversation
    if(isset($isAjax) && $isAjax) : ?>
    <div class="cb_pictures"><?php echo $picture;  ?></div>
    <div class="historic_chat box_account well well-account well-small chat-details">
            <ul class="list-unstyled ml0">
                <?php
                    foreach($messages as $message) :
						if(empty($message['User']['pseudo'])) echo '<li class="me">'; else echo '<li class="he">';
						
						echo '<span class="small">'.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$message['ChatMessage']['date_add']),'%R').'</span>';
				
						echo ' - ';
				
						if(empty($message['User']['pseudo'])) echo '<span class="who">Moi</span>'; else echo '<span class="who">'.$message['User']['pseudo'].'</span>';
				
						echo ' '.$message['ChatMessage']['content'].'</li>';
                    endforeach;
                ?>
            </ul>
        </div>
<?php else: ?>
<section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mes consultations écrites') ?></h1>
	</section><div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s">  <?php echo __('Chat') ?></h2>
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
										'name'  =>  '<span class="active">'.__('Chat').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->
					

					<div class="table-responsive">
				  	 <table class="table table-striped no-border table-mobile text-center mb0"> 
				  	 	<thead class="hidden-xs text-center"> 
				  	 		<tr> 
				  	 			<th class="text-center"><strong><?php echo __('Chat avec'); ?></strong></th> 
				  	 			<th class="text-center"><?php echo __('Date'); ?></th>
				  	 			<th class="text-center"><?php echo __('Actions'); ?></th>
				  	 		</tr> 
				  	 	</thead> 
				  	 	<tbody> 
                        <?php foreach($chats as $chat) :
						
						 ?>
                                <tr>
                                    <td class="veram resize-img"> 
                                       <?php echo $this->Html->link(
                                       '<span>'. $this->Html->image($this->FrontBlock->getAvatar($chat['User']), array(
                                                'alt' => $chat['User']['pseudo'],
                                                'title' => $chat['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle',
                                                )).'</span>',
                                        array(
                                            'language'      => $this->Session->read('Config.language'),
                                            'controller'    => 'agents',
                                            'action'        => 'display',
                                            'link_rewrite'  => strtolower($chat['User']['pseudo']),
                                            'agent_number'  => $chat['User']['agent_number']
                                        ),
                                        array('escape' => false, 'class'=>'sm-sid-photo')
                                    );
                                        echo $this->Html->link(
                                            $chat['User']['pseudo'],
                                            array(
                                                'language'      => $this->Session->read('Config.language'),
                                                'controller'    => 'agents',
                                                'action'        => 'display',
                                                'link_rewrite'  => strtolower($chat['User']['pseudo']),
                                                'agent_number'  => $chat['User']['agent_number']
                                            ),
                                            array('class' => 'agent-pseudo')
                                        );
                                    ?> <span class="date-small visible-xs"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$chat['Chat']['consult_date_start']),' %d/%m/%y'); ?></span>
                                    	<div class="visible-xs mb10 cboth">
                                        <?php
                                            echo $this->Html->link('<i class="glyphicon glyphicon-zoom-in"></i> '.__('Voir la conversation'),
                                                array(),
                                                array('escape' => false, 'data-toggle' => 'modal','data-target' => '#chatdetails', 'title' => 'Chatdetails', 'aria-hidden' => 'true',  'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 nx_openlightbox', 'param' => $chat['Chat']['id'])
                                            );
                                        ?>
                                        </div>
                                    
                                    </td>
                                   <td class="veram whitespace-normal hidden-xs"><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$chat['Chat']['consult_date_start']),' %d/%m/%y'); ?></td>
                                   <td class="veram hidden-xs"> 
                                        <?php
                                            echo $this->Html->link('<i class="glyphicon glyphicon-zoom-in"></i> '.__('Voir la conversation'),
                                                array(),
                                                array('escape' => false, 'data-toggle' => 'modal','data-target' => '#chatdetails', 'title' => 'Chatdetails', 'aria-hidden' => 'true',  'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 nx_openlightbox', 'param' => $chat['Chat']['id'])
                                            );
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

				  	 	</tbody> 
				  	 </table> 

				  	<?php if($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>

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
<?php endif; ?>
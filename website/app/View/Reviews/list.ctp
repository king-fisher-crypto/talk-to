    <?php
	

        if(empty($reviews)) :
            echo __('<p style="text-align:center;padding-top:15px;">Il n\'y a plus d\'avis</p>');
        else : ?>
          
                <?php

                    foreach ($reviews as $review):
                        $fiche_link = $this->Html->url(
                            array(
                                'language'      => $this->Session->read('Config.language'),
                                'controller'    => 'agents',
                                'action'        => 'display',
                                'link_rewrite'  => strtolower(str_replace(' ','-',$review['Agent']['pseudo'])),
                                'agent_number'  => $review['Agent']['agent_number']
                            ),
                            array(
                                'title'         => $review['Agent']['pseudo']
                            )
                        );
                        $mediaPaths = $this->FrontBlock->getAgentMedias($review['Agent']['agent_number'], $review['Agent']['has_photo'] == 1);
                        ?>
						<li class="msg msg_show">
                        	<div class="row">
								<div class="col-sm-12 col-md-2" style="text-align: center">
									 <a href="<?php echo $fiche_link; ?>" class="sm-sid-photo" title="agents en ligne avec <?=$review['Agent']['pseudo'] ?>"><span><img alt="<?php echo h($review['Agent']['pseudo']); ?>" style="width: 65px;" src="<?php echo $mediaPaths['photo_filename']; ?>" class="small-profile img-responsive img-circle"></span></a>
                                    <p class="msg-name black bold mt10"><a href="<?php echo $fiche_link; ?>" title="agents en ligne avec <?=$review['Agent']['pseudo'] ?>"><?php echo h($review['Agent']['pseudo']); ?></a></p>
									 <?php //echo number_format($review['Review']['rate_avg'],1).'% d\'avis positifs'; ?>
								</div>
                           		 <div class="col-sm-12 col-md-3">
									 <strong><?php echo  $review['User']['firstname'] ?></strong><br />
									 <?php echo $this->FrontBlock->getReviewRate($review['Review']['rate']); ?><br />
									 
									 <?php 
									 	$date_min_review = '20181116';
									 	$date_review = CakeTime::format($review['Review']['date_add'], '%Y%m%d');
									 if(intval($date_review) >= intval($date_min_review)){
									 ?>
									 
									 <span class="review_publishdate">Publié le <?php echo CakeTime::format($review['Review']['date_add'], '%d-%m-%Y'); ?></span>
									 <?php } ?>
								</div>
								
								<div class="col-sm-12 col-md-7">
									 <div class="reviews-content bulle"><p><?php echo h($review['Review']['content']); ?></p></div>
    
								   <div class="reponde-avis">
										<?php if($review['Review']['reponse']){ ?>
									   <!-- <p>Réponse de <?php echo h($review['Agent']['pseudo']); ?></p>
										<p><?php echo h($review['Review']['reponse']['content']); ?></p>-->

										<?php } ?>
										<ul class="vote list-inline ml0">
											<li><?php echo __('Cet avis vous a-t-il été utile?') ?></li>
											<li><span class="btn btn-xs btn-pink">OUI</span> <span class="vote-number" rel="<?php echo $review['Review']['review_id']; ?>"><?php echo h($review['Review']['utile']); ?></span></li>
										</ul>
									</div>
								</div>
								
                        	</div>
						</li>

                    <?php endforeach; endif; ?>
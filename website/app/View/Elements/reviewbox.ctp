<?php
    $reviews = $this->FrontBlock->getLastReview(3);

if (!empty($reviews)):
?><div class="box gray hidden-xs" id="box_review">
    <div class="box_title"><span class="box_icon glyphicon glyphicon-comment"></span> <?php echo __('Derniers avis clients'); ?></div>
    <div class="box_content">
        <?php foreach ($reviews AS $review){ ?>
            <div class="box_review">

                <div class="br_comment"><div class="br_arrow"></div><?php echo $this->Nooxtools->cleanCut(h($review['Review']['content']), 120, '...'); ?></div>

                <div class="br_options">
                    <div class="br_rate"><?php

                        echo $this->FrontBlock->getStarsRate($review['Review']['rate']);
                        echo h($review['Review']['rate']);

                    ?>/5</div>
                    <div class="br_pseudo"><?php echo h($review['User']['firstname']); ?></div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php //echo $this->Html->link(__('Voir tous les avis clients'), array('controller' => 'reviews', 'action' => 'display')); 
		App::import("Controller", "AppController");
		$leftblock_app = new AppController();
		$lang = '';
		
		
		if(isset($this->request->params['language'])){
			$lang = 	$this->request->params['language'];
			
		}else{
			$lang = 	$this->Session->read('Config.language');	
		}
		echo '<a href="'.$leftblock_app->getReviewsLink($lang).'">'.__('Voir tous les avis clients').'</a>';
	
	?>
</div>
<?php endif; 
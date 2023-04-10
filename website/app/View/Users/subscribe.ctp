<section class="slider-small">
	<?php if(!$sponsor_id && !$sponsor_user_id){ ?>
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Devenir membre'); ?></h1>
	<?php }else{ ?>
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Parrainage'); ?></h1>
	<?php } ?>
</section>
<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-9">
			<div class="subscribe_intro_container mt20">
				<span class="subscribe_intro_timing" style="display:none"><?php echo $txt['SubscribeLang']['timing'] ?></span>
				<?php
				if($txt['SubscribeLang']['intro1']){
					echo '<div class="subscribe_intro_content subscribe_intro_content_1 subscribe_intro_content_active">'.$txt['SubscribeLang']['intro1'].'</div>';
				}
				if($txt['SubscribeLang']['intro2']){
					echo '<div class="subscribe_intro_content subscribe_intro_content_2 ">'.$txt['SubscribeLang']['intro2'].'</div>';
				}
				if($txt['SubscribeLang']['intro3']){
					echo '<div class="subscribe_intro_content subscribe_intro_content_3 ">'.$txt['SubscribeLang']['intro3'].'</div>';
				}
				if($txt['SubscribeLang']['intro4']){
					echo '<div class="subscribe_intro_content subscribe_intro_content_4 ">'.$txt['SubscribeLang']['intro4'].'</div>';
				}
				if($txt['SubscribeLang']['intro5']){
					echo '<div class="subscribe_intro_content subscribe_intro_content_5 ">'.$txt['SubscribeLang']['intro5'].'</div>';
				}
				?>
			</div>
			<section class="single-page form-page">
			<div class="content_box  mb20 wow fadeIn" data-wow-delay="0.4s">
				<!--<h2 class="text-center wow fadeIn" data-wow-delay="0.5s"><?php echo __('Cette page est réservée à l\'inscription client, pour devenir Expert <a href="/users/subscribe_agent" >cliquez ici</a>'); ?></h2>-->
<?php echo $this->Session->flash(); ?>
<div class="row">
<div class="col-sm-12 col-md-8 col-md-offset-2">
			        <?php

        if(!isset($inscription)){ 
	 ?>
                    <?php 
			
			if($is_sponsorship){
				echo $this->element('account_subscribe_2',array('country' => $selected_countries,'source_ins' => $source_ins, 'sponsor_id'=>$sponsor_id, 'sponsor_user_id'=>$sponsor_user_id, 'sponsor_email'=>$sponsor_email));
			}else{
				echo $this->element('account_subscribe_2',array('country' => $selected_countries));
			}
			 ?>
        <?php
        }
        else{
			?>
			 
                    <?php 
			
					if($is_sponsorship)
						header('Location: /users/subscribe_merci_parrainage');
					else
						header('Location: /users/subscribe_merci');
					
 					exit();
					
					//$this->Html->url(array('controller' => 'users', 'action' => 'subscribe_merci')) ; ?>
                
            <?php
            echo __('Votre inscription a été enregistrée. Vous allez recevoir un email dans quelques instants pour confirmer votre inscription.');
        }
        ?>

</div></div>

			</div><!--expert-box END-->
			<div class="content_box subscribe_box  mb20 wow fadeIn">
				<p class="subscribe_box_title"><?php echo __('Déjà inscrit ? Connectez-vous'); ?></p>
				<a class="btn btn-pink btn-2" style="padding:15px 40px" href="/users/login"><?php echo __('Accéder à mon compte'); ?></a>
			</div>
		</section><!--expert-list END-->
		</div>
		<aside class="col-sm-12 col-md-3 mt20 hidden-sm hidden-xs">
			<div class="mb10 widget2 subscribe-block-container">
				<?php
				if($txt['SubscribeLang']['block1']){
					echo '<div class="subscribe-block-content">'.$txt['SubscribeLang']['block1'].'</div>';
				}
				if($txt['SubscribeLang']['block2']){
					echo '<div class="subscribe-block-content">'.$txt['SubscribeLang']['block2'].'</div>';
				}
				if($txt['SubscribeLang']['block3']){
					echo '<div class="subscribe-block-content">'.$txt['SubscribeLang']['block3'].'</div>';
				}
					
				?>
			</div>
			<div class="mb10 widget2 subscribe-review-container">
				<div class="widget2-title text-center"><?php echo __('Derniers avis clients'); ?></div>
					<?php
					App::import("Controller", "AppController");
									$leftblock_app = new AppController();
									$lang = '';
									
									
									if(isset($this->request->params['language'])){
										$lang = 	$this->request->params['language'];
										
									}else{
										$lang = 	$this->Session->read('Config.language');	
									}	   
		   	?>
			<div class="carousel-clients wow fadeIn" data-wow-delay="0.6s">    				
						<div class="carousel slide" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
							<!-- Carousel indicators -->
                            <?php
								$reviews = $this->FrontBlock->getLastReview(3);
							?>
							<ol class="carousel-indicators">
								<li data-target="#fade-quote-carousel" data-slide-to="0"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="1"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="2" class="active"></li>
								<span class="more-testi">
                                <?php //echo $this->Html->link(__('Voir tous les avis clients'), array('controller' => 'reviews', 'action' => 'display')); 
									
									//echo '<a href="'.$leftblock_app->getReviewsLink($lang).'" class="voirplus">'.__('Voir plus').'</a>';
									//echo '<span class="voirplus">'.__('Voir plus').'</span>';
								?>
                                </span>
							</ol>
							<!-- Carousel items -->
							<div class="carousel-inner">
                            	<?php 
								$ireview = 0;
								foreach ($reviews AS $review){ 
									$activereview = '';
									if($ireview == 0 ) $activereview = 'active';
								?>
								<div class="item <?php echo $activereview; ?>">
									<p>”<?php echo $this->Nooxtools->cleanCut(h($review['Review']['content']), 120, '...'); ?>” <span class="client-name"><?php echo h($review['User']['firstname']); ?></span></p>
								</div>
								<?php $ireview++; }  ?>
							</div>
						</div>
					</div><!--carousel-clients END-->
			</div>
		</aside>
	</div>
	
	
		
</div><!--container END-->

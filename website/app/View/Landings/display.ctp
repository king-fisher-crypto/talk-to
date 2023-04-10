<?php
 echo $this->Html->script('/theme/default/js/select_product2', array('block' => 'script'));
App::import("Controller", "AppController");
									$leftblock_app = new AppController();
									$lang = '';


									if(isset($this->request->params['language'])){
										$lang = 	$this->request->params['language'];

									}else{
										$lang = 	$this->Session->read('Config.language');
									}
/* Environnement AJAX (light box par exemple) */
if (isset($isAjax) && $isAjax === 1){
    echo json_encode(array(
                            'title' => $page['LandingLang']['name'],
                            'content' => $page['LandingLang']['content'],
                            'button'  => __('Fermer'))
                            );

    exit;}
  echo $this->FrontBlock->getLandingCaroussel($landingpage['Landing']['id']);

$template = $landingpage['LandingLang']['template'];

?>


 <div class="container mb40 landing-page">

<?php
	 if($template == 0){
?>
<?php if($landingpage['LandingLang']['show_pricetable']){
 if( isset($promo) && $promo){
						$tt =  $this->element('cart_products_promo', array('products' => $products, 'user' => $this->Session->read('Auth.User'), 'promo' => $promo, 'promo_title' => $promo_title , 'is_promo_total' => $is_promo_total, 'is_landing' => 1));
					}else{
						$tt =  $this->element('cart_products', array('products' => $products, 'user' => $this->Session->read('Auth.User'), 'is_landing' => 1));
					}

					echo '<section class="bg-white filter-box" style="margin-bottom:-20px;"><div class="table_single_container_page">'.$tt.'</div></section>';


  } ?>

 	<?php if($landingpage['LandingLang']['content']){ ?>
	<section class="bg-white filter-box hidden-xs" id="cat_description">
    	<div class="row">
        	<div  class="alert-info alert-dismissible alert-custom" role="alert">
                <?php echo $landingpage['LandingLang']['content']; ?>
            </div>
        </div>
	</section>
	<?php } ?>
	<?php if($landingpage['LandingLang']['content_mobile']){ ?>
	<section class="bg-white filter-box visible-xs" id="cat_description_mobile">
        	<div  class="alert-info alert-dismissible alert-custom" role="alert">
                <?php echo $landingpage['LandingLang']['content_mobile']; ?>
            </div>
	</section>
	<?php } ?>
	 <?php

    /* Filtres */
	 if($landingpage['LandingLang']['show_agents']){
    echo $this->element('agent_filters', array(
        'filters' => compact(array('filter_orderby','filter_filterby')),
        'datas' => compact(array('mediaChecked','count_html','page'))
    ));


    ?>

	<section class="expert-list listsimplifyexpert" id="agents_list">
        <?php echo $this->element('agentslist', array('id_category' => 1, 'agents' => $agents, 'phones' => $phones)); ?>
    </section><!--expert-list END-->

<?php  } ?>
<?php	 }
?>
<?php
	 if($template == 1){
?>
<?php if($landingpage['LandingLang']['reassurance_1'] && $landingpage['LandingLang']['reassurance_2'] && $landingpage['LandingLang']['reassurance_3']){ ?>
<div class="bg-white row" style="margin-bottom:8px;">
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_1']; ?>
			</div>
		</aside>
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_2']; ?>
			</div>
		</aside>
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_3']; ?>
			</div>
		</aside>
</div>
<?php } ?>
<div class="row bg-white">
	<div class="col-sm-12 col-md-7">
		<div class="visible-xs">
			<?php echo $landingpage['LandingLang']['content_mobile']; ?>
		</div>
		<div class="hidden-xs">
			<?php echo $landingpage['LandingLang']['content']; ?>
		</div>
	</div>
	<div class="col-sm-12 col-md-5" style="background:#fff;border-left:2px solid #f2f2f2">
		<?php
			 if($landingpage['LandingLang']['show_reviews']){
		?>
		<a href="<?php echo $leftblock_app->getReviewsLink($lang); ?>" class="voirplus"><div class="mid-box" style="text-align: center">
					<p class="mid-title avis"  style="color: #42424c;font-size: 24px;margin-top: 25px;"><?=__('Derniers avis clients') ?></p>
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
									echo '<span class="voirplus">'.__('Voir plus').'</span>';
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
					</div></a><!--mid-box END-->
		<?php
			 }
		?>
		<div class="visible-xs" style="margin:20px 0;padding:20px;border-top:2px solid #f2f2f2;border-bottom:2px solid #f2f2f2">
			<?php echo $landingpage['LandingLang']['content_preview_mobile']; ?>
		</div>
		<div class="hidden-xs" style="margin:20px 0;padding:20px;border-top:2px solid #f2f2f2;border-bottom:2px solid #f2f2f2">
			<?php echo $landingpage['LandingLang']['content_preview']; ?>
		</div>
		<?php echo $this->element('account_subscribe',array('source_ins' => 'landing')); ?>
	</div>
</div>
<div class="row" style="margin-top:8px;">
	 <?php

    /* Filtres */
	 if($landingpage['LandingLang']['show_agents']){
    echo $this->element('agent_filters', array(
        'filters' => compact(array('filter_orderby','filter_filterby')),
        'datas' => compact(array('mediaChecked','count_html','page'))
    ));


    ?>

	<section class="expert-list listsimplifyexpert" id="agents_list">
        <?php echo $this->element('agentslist', array('id_category' => 1, 'agents' => $agents, 'phones' => $phones)); ?>
    </section><!--expert-list END-->

<?php  } ?>
</div>
<?php
	 }
?>
<?php
	 if($template == 2){
?>
<div class="row bg-white">
	<div class="col-sm-12 col-md-7">
		<div class="visible-xs">
			<?php echo $landingpage['LandingLang']['content_mobile']; ?>
		</div>
		<div class="hidden-xs">
			<?php echo $landingpage['LandingLang']['content']; ?>
		</div>
	</div>
	<div class="col-sm-12 col-md-5">

		<div class="visible-xs" style="margin-top:20px;">
			<?php echo $landingpage['LandingLang']['content_preview_mobile']; ?>
		</div>
		<div class="hidden-xs" style="margin-top:20px;">
			<?php echo $landingpage['LandingLang']['content_preview']; ?>
		</div>
		<?php echo $this->element('account_subscribe',array('source_ins' => 'landing')); ?>
	</div>
</div>
<?php if($landingpage['LandingLang']['reassurance_1'] && $landingpage['LandingLang']['reassurance_2'] && $landingpage['LandingLang']['reassurance_3']){ ?>
<div class="bg-white row" style="margin-bottom:8px;">
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_1']; ?>
			</div>
		</aside>
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_2']; ?>
			</div>
		</aside>
		<aside class="col-sm-4 col-xs-12">
			<div class="mid-box-landing" style="padding-top:5px;">
				<?php echo $landingpage['LandingLang']['reassurance_3']; ?>
			</div>
		</aside>
</div>
<?php } ?>
<div class="bg-white row" style="margin-bottom:8px;">
	<?php
			 if($landingpage['LandingLang']['show_reviews']){
		?>
		<a href="<?php echo $leftblock_app->getReviewsLink($lang); ?>" class="voirplus"><div class="mid-box" style="text-align: center">
					<p class="mid-title avis"  style="color: #42424c;font-size: 24px;margin-top: 25px;"><?=__('Derniers avis clients') ?></p>
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
									echo '<span class="voirplus">'.__('Voir plus').'</span>';
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
					</div></a><!--mid-box END-->
		<?php
			 }
		?>
</div>
<div class="row" style="margin-top:8px;">
	 <?php

    /* Filtres */
	 if($landingpage['LandingLang']['show_agents']){
    echo $this->element('agent_filters', array(
        'filters' => compact(array('filter_orderby','filter_filterby')),
        'datas' => compact(array('mediaChecked','count_html','page'))
    ));


    ?>

	<section class="expert-list listsimplifyexpert" id="agents_list">
        <?php echo $this->element('agentslist', array('id_category' => 1, 'agents' => $agents, 'phones' => $phones)); ?>
    </section><!--expert-list END-->

<?php  } ?>
</div>
<?php
	 }
?>

 <?php
 if($template >= 3){
     ?>
     <div class="template_3" id="template_3">
		 
		  <?php
 if($template == 6){
     ?>
		 <p class="mid-title avis"  style="color: #943c8f;font-size: 22px;margin-top: 25px;margin-bottom: 3%;text-align: center;line-height: 22px;"><?=__('Pour savoir rapidement comment votre situation va évoluer,<br />consultez maintenant l\'un de nos experts :') ?></p>
             <div class="row ">
                 <div class="effect2 box col-md-12 col-sm-12 col-xs-12 mb20">
					 
                    <?php echo $this->element('account_subscribe_template_3',array('source_ins' => 'landing')); ?>
                 </div>
                 </div>
<?php } ?>
		
     <?php if( ($template == 3 || $template == 6)&& $landingpage['LandingLang']['reassurance_1'] && $landingpage['LandingLang']['reassurance_2'] && $landingpage['LandingLang']['reassurance_3']){ ?>
         <div class="hidden-xs bg row mb40 mt30 " style="margin-bottom:8px;display: flex">
             <aside class="col-sm-4 col-xs-12 mid-box-landing box effect2 reassurance_1">
                 <div class="" style="">
                     <?php echo $landingpage['LandingLang']['reassurance_1']; ?>
                 </div>
             </aside>
             <aside class="col-sm-4 col-xs-12 mid-box-landing box effect2 reassurance_2">
                 <div class="" style="">
                     <?php echo $landingpage['LandingLang']['reassurance_2']; ?>
                 </div>
             </aside>
             <aside class="col-sm-4 col-xs-12  mid-box-landing box effect2 reassurance_3">
                 <div class="" style="">
                     <?php echo $landingpage['LandingLang']['reassurance_3']; ?>
                 </div>
             </aside>
         </div>

         <div class="visible-xs bg row mb40 mt20 " style="margin-bottom:10px;">
             <aside class="col-sm-4 col-xs-12 mb10">
                 <div class="mid-box-landing box effect2" style="position: relative;padding-top:15px">
                     <?php echo $landingpage['LandingLang']['reassurance_1']; ?>
                 </div>
             </aside>
             <aside class="col-sm-4 col-xs-12 mb10">
                 <div class="mid-box-landing box effect2" style="position: relative;padding-top:15px">
                     <?php echo $landingpage['LandingLang']['reassurance_2']; ?>
                 </div>
             </aside>
             <aside class="col-sm-4 col-xs-12 mb10">
                 <div class="mid-box-landing box effect2" style="position: relative;padding-bottom:15px">
                     <?php echo $landingpage['LandingLang']['reassurance_3']; ?>
                 </div>
             </aside>
         </div>
     <?php } ?>
		 		
	 <?php
 if($template == 3 || $template == 6){
     ?>
     <div class="row ">
         <div class="col-sm-12 col-md-12">
             <div class="visible-xs txtlandingmobile">
                 <?php echo $landingpage['LandingLang']['content_mobile']; ?>
             </div>
             <div class="hidden-xs text_1 ">
                 <?php echo $landingpage['LandingLang']['content']; ?>
             </div>
         </div>
     </div>
	<?php } ?>
		  <?php
 if($template != 5){
     ?>
         <div class="row bg-white hidden-xs mt20 mb20">
             <div class="col-sm-12 col-md-12 effect2 box ">
                 <div class="text_2">
                     <?php echo $landingpage['LandingLang']['content_2']; ?>
                 </div>
             </div>
         </div>

         <div class="row visible-xs box" style="width: 99%;margin: 20px auto;">
             <div class="col-sm-12 col-md-12 effect2   txtlandingmobile">
                 <div class="txt2">
                     <?php echo $landingpage['LandingLang']['content_2_mobile']; ?>
                 </div>
             </div>
         </div>
		 <?php } ?>
		  <?php
 if($template == 3 || $template == 6){
     ?>
         <div class="row hidden-xs ">
         <div class="col-sm-12 col-md-12" style="">
             <?php
             if($landingpage['LandingLang']['show_reviews']){
                 ?>
                 <a href="<?php echo $leftblock_app->getReviewsLink($lang); ?>" class="voirplus"><div class="mid-box" style="text-align: center">
                         <p class="mid-title avis"  style="color: #943c8f;font-size: 24px;margin-top: 25px;margin-bottom: 3%;"><?=__('Derniers avis clients') ?></p>
                         <div class="carousel-clients wow fadeIn" data-wow-delay="0.6s">
                             <div class="carousel slide" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
                                 <!-- Carousel indicators -->
                                 <?php
                                 $reviews = $this->FrontBlock->getLastReview(5);
                                 ?>
                                 <ol class="carousel-indicators">
                                     <li data-target="#fade-quote-carousel" data-slide-to="0"></li>
                                     <li data-target="#fade-quote-carousel" data-slide-to="1"></li>
                                     <li data-target="#fade-quote-carousel" data-slide-to="2" ></li>
                                     <li data-target="#fade-quote-carousel" data-slide-to="3" ></li>
                                     <li data-target="#fade-quote-carousel" data-slide-to="4" class="active"></li>
                                     <span class="more-testi"><br>
                            <?php
                            ?>
                            </span>
                                 </ol>
                                 <!-- Carousel items -->
                                 <div class="carousel-inner" style="    height: 326px;">
                                     <?php
                                     $ireview = 0;
                                     foreach ($reviews AS $review){

                                         $activereview = '';
                                         if($ireview == 0 ) $activereview = 'active';

                                         App::import("Model", "User");
                                         $model = new User();

                                         $user = $model->find('first', array(
                                             'fields'        => array('User.*'),
                                             'conditions'    => array('User.id' => $review['Review']['agent_id']),
                                             'recursive'     => -1
                                         ));

                                         ?>
                                         <div class="item  <?php echo $activereview; ?> " style="">
                                         <aside class=" col-sm-4 col-xs-12 ">
                                             <div class="mid-box-landing box effect2" style="height: 300px">
                                             <div class="sm-sid-photo" style="padding-top: 5%;"><span>
                                                         <?php echo
									 $this->Html->image($this->FrontBlock->getAvatar($user['User'],true,true), array(
                                                'alt' => 'agents en ligne '.$user['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle status-'.$user['User']['agent_status']
                                                ));
									?>
                                                         </span>
                                                 <p class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score"><?php echo number_format($user['User']['reviews_avg'],1)?></span></p>

                                             </div>

                                                 <p style="width: 60%;text-align: center;margin: 0 auto;font-size: 17px;">”<?php echo $this->Nooxtools->cleanCut(h($review['Review']['content']), 150, '...'); ?>” </p>
                                                 <br>
                                                 <p class="client-name"><b><?php echo h($review['User']['firstname']); ?></b></p>

                                             </div>
                                         </div>
                                         </aside>
                                         <?php $ireview++; }  ?>

                             </div>
                         </div><!--carousel-clients END-->
                     </div></a><!--mid-box END-->
                 <?php
             }
             ?>
             <div class="visible-xs" style="">
                 <?php echo $landingpage['LandingLang']['content_preview_mobile']; ?>
             </div>
             <div class="hidden-xs" style="">
                 <?php echo $landingpage['LandingLang']['content_preview']; ?>
             </div>
         </div>
         </div>
         </div>

     <div class="row visible-xs">
         <div class="col-sm-12 col-md-12" style="">
             <?php
             if($landingpage['LandingLang']['show_reviews']){
                 ?>
                 <a href="<?php echo $leftblock_app->getReviewsLink($lang); ?>" class="voirplus"><div class="mid-box" style="text-align: center">
                         <p class="mid-title avis"  style="color: #943c8f;font-size: 24px;margin-top: 25px;margin-bottom: 3%;"><?=__('Derniers avis clients') ?></p>
                         <div class="carousel-clients wow fadeIn" data-wow-delay="0.6s">
                             <div class="carousel slide" id="fade-quote-carousel-for-mobile" data-ride="carousel" data-interval="3000">
                                 <!-- Carousel indicators -->
                                 <?php
                                 $reviews = $this->FrontBlock->getLastReview(5);
                                 ?>
                                 <ol class="carousel-indicators">
                                     <li style="border: 1px solid #943c8f" data-target="#fade-quote-carousel" data-slide-to="0"></li>
                                     <li style="border: 1px solid #943c8f" data-target="#fade-quote-carousel" data-slide-to="1"></li>
                                     <li style="border: 1px solid #943c8f" data-target="#fade-quote-carousel" data-slide-to="2" ></li>
                                     <li style="border: 1px solid #943c8f" data-target="#fade-quote-carousel" data-slide-to="3" ></li>
                                     <li style="border: 1px solid #943c8f" data-target="#fade-quote-carousel" data-slide-to="4" class="active"></li>
                                     <span class="more-testi"><br>
                            <?php
                            ?>
                            </span>
                                 </ol>
                                 <!-- Carousel items -->
                                 <div class="carousel-inner" style="    height: 326px;">
                                     <?php
                                     $ireview = 0;
                                     foreach ($reviews AS $review){

                                         $activereview = '';
                                         if($ireview == 0 ) $activereview = 'active';

                                         App::import("Model", "User");
                                         $model = new User();

                                         $user = $model->find('first', array(
                                             'fields'        => array('User.*'),
                                             'conditions'    => array('User.id' => $review['Review']['agent_id']),
                                             'recursive'     => -1
                                         ));

                                         ?>
                                         <div class="item  <?php echo $activereview; ?> " style="">
                                             <aside class=" col-sm-4 col-xs-12 ">
                                                 <div class="mid-box-landing box effect2" style="height: 300px">
                                                     <div class="sm-sid-photo" style="padding-top: 5%;"><span>
                                                         <?php echo
                                                         $this->Html->image($this->FrontBlock->getAvatar($user['User'],true,true), array(
                                                             'alt' => 'agents en ligne '.$user['User']['pseudo'],
                                                             'class' => 'small-profile img-responsive img-circle status-'.$user['User']['agent_status']
                                                         ));
                                                         ?>
                                                         </span>
                                                         <p class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score"><?php echo number_format($user['User']['reviews_avg'],1)?></span></p>

                                                     </div>

                                                     <p style="width: 60%;text-align: center;margin: 0 auto;font-size: 17px;">”<?php echo $this->Nooxtools->cleanCut(h($review['Review']['content']), 150, '...'); ?>” </p>
                                                     <br>
                                                     <p class="client-name"><b><?php echo h($review['User']['firstname']); ?></b></p>

                                                 </div>
                                         </div>
                                         </aside>
                                         <?php $ireview++; }  ?>

                                 </div>
                             </div><!--carousel-clients END-->
                         </div></a><!--mid-box END-->
                 <?php
             }
             ?>
             <div class="visible-xs" style="">
                 <?php echo $landingpage['LandingLang']['content_preview_mobile']; ?>
             </div>
             <div class="hidden-xs" style="">
                 <?php echo $landingpage['LandingLang']['content_preview']; ?>
             </div>
         </div>
     </div>
 </div>
		 <?php } ?>
		 <?php
 if($template == 3){
     ?>
		 <p class="mid-title avis"  style="color: #943c8f;font-size: 22px;margin-top: 25px;margin-bottom: 3%;text-align: center;line-height: 22px;"><?=__('Pour savoir rapidement comment votre situation va évoluer,<br />consultez maintenant l\'un de nos experts :') ?></p>
             <div class="row ">
                 <div class="effect2 box col-md-12 col-sm-12 col-xs-12 mb20">
					 
                    <?php echo $this->element('account_subscribe_template_3',array('source_ins' => 'landing')); ?>
                 </div>
                 </div>
<?php } ?>
     </div>

     <div class="row" style="margin-top:8px;">
         <?php

         /* Filtres */
         if($landingpage['LandingLang']['show_agents']){
             echo $this->element('agent_filters', array(
                 'filters' => compact(array('filter_orderby','filter_filterby')),
                 'datas' => compact(array('mediaChecked','count_html','page'))
             ));


             ?>

             <section class="expert-list listsimplifyexpert" id="agents_list">
                 <?php echo $this->element('agentslist', array('id_category' => 1, 'agents' => $agents, 'phones' => $phones)); ?>
             </section><!--expert-list END-->

         <?php  } ?>
     </div>
     </div>
     <?php
 }
 ?>


</div><!--container END-->


<!-- Modal -->
<div class="modal modal-small fade" id="inscription" tabindex="-1" role="dialog" >
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<div class="modal-title" ><?=__('INSCRIPTION') ?></div>
			</div>
			<div class="modal-body">
			<?php echo $this->element('ins_modal', array('colInput' => 7, 'colButton' => 8)); ?>
			</div>
		</div>
	</div>
</div>

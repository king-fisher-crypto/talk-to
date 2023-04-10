<?php
	
	if($popup_redir){
		echo $popup_redir;	
	}
	
	if(!$this->request->isMobile()){
       $carousel = $this->FrontBlock->getCaroussel();
	}
	 	//echo $this->Session->flash();//
	   
	   if($this->request->params['controller'] == 'home'){
        	echo $carousel;
		  
		  	$idlang = $this->Session->read('Config.id_lang');
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($idlang == 1){
				if($extension == 'ca')$idlang=8;	
				//if($extension == 'ch')$idlang=10;
				//if($extension == 'be')$idlang=11;
				if($extension == 'lu')$idlang=12;
			}
		  
		    //On affiche la page pour le tarif
			$user = $this->Session->read('Auth.User');
			
			if(isset($user) && $user['role'] == 'client'){
				$text1 = $this->FrontBlock->getPageBlocTexteHomebyLang(3,$idlang);
            	$text2 = $this->FrontBlock->getPageBlocTexteHomebyLang(4,$idlang);
			}else{
            	$text1 = $this->FrontBlock->getPageBlocTexteHomebyLang(1,$idlang);
            	$text2 = $this->FrontBlock->getPageBlocTexteHomebyLang(2,$idlang);
			}
			
			if(isset( $text1['link']) && substr_count($text1['link'],'#')){
				$link_popup = 'data-target="'.$text1['link'].'" data-toggle="modal"';
				$text1['link'] = '';
			}else{
				$link_popup = '';	
			}
			if(isset( $text2['link']) && substr_count($text2['link'],'#')){
				$link_popup2 = 'data-target="'.$text2['link'].'" data-toggle="modal"';
				$text2['link'] = '';
			}else{
				$link_popup2 = '';	
			}
			
			/*
        ?>
            
          
        <div class="bg-white middle-section hidden-xs">
		<div class="container">
       <?php
       if(isset( $text2['text1'])){
?>
		<aside class="col-sm-4 col-xs-12">
				<a href="<?=$text2['link'] ?>" <?=$link_popup2 ?> onclick="stepnext(1);"><div class="mid-box">
					<p class="mid-title wow fadeIn" data-wow-delay="0.2s"><?=$text2['text1'] ?></p>
                    <div class="text-big price wow fadeIn" data-wow-delay="0.6s"><!--<span class="color" style="font-size:50px;"><?=$text2['text2_1'] ?></span>--><span class="p-big2"><?=$text2['text2_2'] ?></span><span class="p-big3"><?=$text2['text2_3'] ?></span></div>
                    <div class="mid-foot2 uppercase wow fadeIn" data-wow-delay="1.0s"><?=$text2['text3'] ?></div>
				</div></a><!--mid-box END-->
			</aside>
<?php }
?>
       
       
        <?php if(isset( $text1['text1'])){  ?>
			<aside class="col-sm-4 col-xs-12">
				<a href="<?=$text1['link'] ?>" <?=$link_popup ?> onclick="stepnext(1);"><div class="mid-box">
				<p class="mid-title wow fadeIn" data-wow-delay="0.2s"><?=$text1['text1'] ?></p>
					<?php
			if(!$text1['text2_1']){
				?>
				<div class="mid-img-hor"><img src="/theme/default/img/horoscope/horoscope-du-jour.jpg" alt="horoscope du jour gratuit" class="img-responsive" /></div>
			<?php
			}else{
			?>	
			
					
                    <div class="text-big price wow fadeIn" data-wow-delay="0.6s"><!--<span class="color" style="font-size:50px;"><?=$text1['text2_1'] ?></span>--><span class="p-big2"><?=$text1['text2_2'] ?></span><span class="p-big3"><?=$text1['text2_3'] ?></span></div>
                    <div class="mid-foot2 uppercase wow fadeIn" data-wow-delay="1.0s"><?=$text1['text3'] ?></div>
                    <?php
			}
										  ?>
				</div></a><!--mid-box END-->
			</aside>
<?php }
		   
	App::import("Controller", "AppController");
									$leftblock_app = new AppController();
									$lang = '';
									
									
									if(isset($this->request->params['language'])){
										$lang = 	$this->request->params['language'];
										
									}else{
										$lang = 	$this->Session->read('Config.language');	
									}	   
		   
		   
?>
			<aside class="col-sm-4 col-xs-12">
				<a href="<?php echo $leftblock_app->getReviewsLink($lang); ?>" class="voirplus"><div class="mid-box">
					<p class="mid-title avis wow fadeIn" data-wow-delay="0.2s">Derniers avis clients</p>
					<div class="carousel-clients wow fadeIn" data-wow-delay="0.6s">    				
						<div class="carousel slide" id="fade-quote-carousel" data-ride="carousel" data-interval="3000">
							<!-- Carousel indicators -->
                            <?php
								$reviews = $this->FrontBlock->getLastReview(3);
							?>
							
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
							<ol class="carousel-indicators">
								<li data-target="#fade-quote-carousel" data-slide-to="0"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="1"></li>
								<li data-target="#fade-quote-carousel" data-slide-to="2" class="active"></li>
							</ol>
								<span class="more-testi">
                                <?php //echo $this->Html->link(__('Voir tous les avis clients'), array('controller' => 'reviews', 'action' => 'display')); 
									
									//echo '<a href="'.$leftblock_app->getReviewsLink($lang).'" class="voirplus">'.__('Voir plus').'</a>';
									echo '<span class="voirplus">'.__('Voir plus').'</span>';
								?>
                                </span>
							
						</div>
					</div><!--carousel-clients END-->
					</div></a><!--mid-box END-->
			</aside>
		</div>
	</div><!--middle-section END-->

            <?php*/
	   }else{
		   $titre_h1 = '';
$content = $categoryLang['CategoryLang']['description'];
if(substr_count($content, '</h1>')){
	$split_content = explode('</h1>',$content);
	$split_titre = explode('>',$split_content[0]);
	$titre_h1 = $split_titre[1];
	$content = $split_content[1];
}

if(!$titre_h1) $titre_h1 = __('Consultez les meilleurs voyants d\'Europe');
		   
			?>
            <div class="slider-logged hidden-xs">
				<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?=$titre_h1 ?></h1>
				<!--h1/h2 both works here-->
			</div>
            <?php   
	   }
?>
<div class="container">
<?php if($this->request->params['controller'] == 'home'){ ?>

	 <?php //if($this->request->params['controller'] != 'home'){ ?>
	
	
	<?php //} ?>
	 <?php
	 
    /* Filtres */
    echo $this->element('agent_filters', array(
        'filters' => compact(array('filter_orderby','filter_filterby')),
        'datas' => compact(array('mediaChecked','page'))
    ));

    ?>
	<?php /*if (isset($category_id) && $category_id !== 1): ?>
		<span id="category_id_inf" style="display: none"><?php if(isset($category_id))echo $category_id; ?></span>
       <!-- <script type="text/javascript">$(document).ready(function(){nxMain.agentListFilters.id_category = '<?php echo $category_id; ?>';
			$('#cat_description').find('a.saymore').trigger('click');
		
		});</script>-->
    <?php endif; */?>
    
	
    
	<section class="expert-list" id="agents_list">
        <?php 
		if(!isset($agents))$agents = array();
		if(!isset($phones))$phones = array();
		echo $this->element('agentslist', array('id_category' => isset($category_id)?$category_id:1, 'agents' => $agents, 'phones' => $phones)); 
    ?>
    </section><!--expert-list END-->
	<div class="bg-white filter-box mt10" id="cat_description">
    	<div class="row">
        	<div  class="alert-info alert-dismissible alert-custom col-lg-12">
               <?php echo str_replace('</p>','',str_replace('<p>','',$this->element('categoryDescription', array('categorie' => (empty($categoryLang) ?array():$categoryLang))))); ?>
          </div>
      </div>
	</div>
	 <?php /* if($this->request->params['controller'] == 'home'){ ?>
	<div class="bg-white filter-box" id="cat_description">
    	<div class="row">
        	<div  class="alert-info alert-dismissible alert-custom col-lg-12">
                <?php echo str_replace('</p>','',str_replace('<p>','',$this->element('categoryDescription', array('categorie' => (empty($categoryLang) ?array():$categoryLang))))); ?>
            </div>
        </div>
	</div>
	<?php } */ ?>
  
  <?php }else{ 
  
  $is_right = 1 ;
  ?>
  
	<div class="single-page cms-page">
		<div class="row">
		<?php 
			if($is_right) echo '<div class="col-sm-12 col-md-9 ">';
			?>
		
		<div class="content_box content_box_page wow fadeIn" data-wow-delay="0.4s">
			<?php echo str_replace('</p>','',str_replace('<p>','',$this->element('categoryDescriptionFull', array('categorie' => (empty($categoryLang) ?array():$categoryLang))))); ?>
      
			  </div>
			 <?php
	 
			
			
    /* Filtres */
	 if($show_agents){
    echo $this->element('agent_filters', array(
        'filters' => compact(array('filter_orderby','filter_filterby')),
        'datas' => compact(array('mediaChecked','count_html','page'))
    ));
	

    ?>
  
	<section class="expert-list listsimplifyexpert" id="agents_list">
        <?php echo $this->element('agentslist', array('id_category' => 1, 'agents' => $agents, 'phones' => $phones)); ?>
    </section><!--expert-list END-->

<?php  } ?>
			
			
      
		
        	<?php
		if($is_right)echo '</div>';
			if($is_right)echo $this->Frontblock->getRightSidebar();
			?>
		</div>
     </div>

  
  
  <?php } ?>
</div><!--container END-->

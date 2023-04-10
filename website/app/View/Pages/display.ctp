<?php



if(substr_count($thepage['PageLang']['content'],'--TABLE--')||substr_count($thepage['PageLang']['content'],'--TABLE_RECOUVRE--')){
echo $this->Html->script('/theme/default/js/select_product2', array('block' => 'script'));
}
/* Environnement AJAX (light box par exemple) */
if (isset($isAjax) && $isAjax === 1){
    echo json_encode(array(
                            'title' => $thepage['PageLang']['name'],
                            'content' => $thepage['PageLang']['content'],
                            'button'  => __('Fermer'))
                            );
    
    exit;}


//cut h1
$titre_h1 = '';
$content = $thepage['PageLang']['content'];
if(substr_count($content, '</h1>')){
	$split_content = explode('</h1>',$content);
	$split_titre = explode('>',$split_content[0]);
	$titre_h1 = $split_titre[1];
	$content = $split_content[1];
}

if(!$titre_h1) $titre_h1 = $thepage['PageLang']['name'];

$is_right = true;
$is_slide_cms = false;
$show_agents = 0;
if($thepage['Page']['page_category_id'] == 7 || $thepage['Page']['page_category_id'] == 8 || $thepage['Page']['page_category_id'] == 5 )$is_right = true;

if($thepage['Page']['id'] == 33 || $thepage['Page']['id'] == 34 || $thepage['Page']['id'] == 35){
	$show_agents = 1;
	$is_right = 0;
} 
$bad_cat = array(1,3,4,6,9,10,11,12);
if(!in_array($thepage['Page']['page_category_id'],$bad_cat)){
	//$show_agents = 1;
	//$is_right = 0;
	$is_slide_cms = true;
}

//<!--slider to show once the user is logged in-->
if($thepage['Page']['page_category_id'] == 6 && $slideprice){
	$is_slide_cms = false;
	$image = DS.Configure::read('Site.pathSlideprice').DS.$slideprice['SlidepriceLang']['name'];
	$image_mobile = DS.Configure::read('Site.pathSlideprice').DS.$slidepricemobile['SlidepricemobileLang']['name_mobile'];
	list($width_mobile, $height_mobile) = getimagesize ( $_SERVER[DOCUMENT_ROOT].'/app/webroot'.$image_mobile );
	if($height_mobile > 205)$hauteur_mobile = $height_mobile; else $hauteur_mobile = 205;
	list($width, $height) = getimagesize ( $_SERVER[DOCUMENT_ROOT].'/app/webroot'.$image );
	if($height > 205)$hauteur = $height; else $hauteur = 205;
	$countdown = '';
	if($slideprice['SlidepriceLang']['date_fin']  && $slideprice['SlidepriceLang']['date_fin'] != '0000-00-00 00:00:00'){
		
				$text_compteur = '';
				if($slideprice['SlidepriceLang']['text_compteur'])$text_compteur = '<p style="margin-top:10px;margin-bottom:0px;text-align:center;font-size:'. $slideprice['SlidepriceLang']['date_fin_size'].'px;color:'. $slideprice['SlidepriceLang']['date_fin_color'].'">'.$slideprice['SlidepriceLang']['text_compteur'].'</p>';
		
				$countdown =$text_compteur.'<span class="clock clock_price" rel="'.$slideprice['SlidepriceLang']['date_fin'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'.$slideprice['SlidepriceLang']['date_fin_size'].'px;color:'.$slideprice['SlidepriceLang']['date_fin_color'].'"></span>';
			}
	$countdown_mobile = '';
	if($slidepricemobile['SlidepricemobileLang']['date_fin']  && $slidepricemobile['SlidepricemobileLang']['date_fin'] != '0000-00-00 00:00:00'){	
			$text_compteur_mobile = '';
				if($slidepricemobile['SlidepricemobileLang']['text_compteur'])$text_compteur_mobile = '<p style="margin-top:10px;margin-bottom:0px;text-align:center;font-size:'. $slidepricemobile['SlidepricemobileLang']['date_fin_mobile_size'].'px;color:'. $slidepricemobile['SlidepricemobileLang']['date_fin_mobile_color'].'">'.$slidepricemobile['SlidepricemobileLang']['text_compteur'].'</p>';
		
				$countdown_mobile =$text_compteur_mobile.'<span class="clock clock_price" rel="'.$slidepricemobile['SlidepricemobileLang']['date_fin'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'.$slidepricemobile['SlidepricemobileLang']['date_fin_mobile_size'].'px;color:'.$slidepricemobile['SlidepricemobileLang']['date_fin_mobile_color'].'"></span>';
			}
	?>
	<div class="slider-small" style="background:url('<?=$image ?>') no-repeat center top;background-size: cover;min-height:<?=$hauteur ?>px;">
	<h1 class="" style="display:none"><?php echo $titre_h1; ?></h1>
	<div class="h1" data-wow-delay="0.5s" style="font-size:<?php echo $slideprice['SlidepriceLang']['font_size_1']; ?>px;color:<?php echo $slideprice['SlidepriceLang']['font_color_1']; ?>;"><?php echo $slideprice['SlidepriceLang']['title']; ?></div>
	<div class="h2" data-wow-delay="0.5s" style="font-size:<?php echo $slideprice['SlidepriceLang']['font_size_2']; ?>px;color:<?php echo $slideprice['SlidepriceLang']['font_color_2']; ?>;"><?php echo $slideprice['SlidepriceLang']['alt']; ?></div>
	<?=$countdown ?>
</div>
	<?php if($slidepricemobile){ 

		if($slidepricemobile['SlidepricemobileLang']['font_font_mobile_1'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slidepricemobile['SlidepricemobileLang']['font_font_mobile_1']).'">';	
		if(!$slidepricemobile['SlidepricemobileLang']['font_font_mobile_1']) $slidepricemobile['SlidepricemobileLang']['font_font_mobile_1'] = 'AvenirLT-Book';
?>
	<section class="slider-small visible-xs" style="background:url('<?=$image_mobile ?>') no-repeat center top;background-size: cover;min-height:<?=$hauteur_mobile ?>px;">
	<div class="h1" data-wow-delay="0.5s" style="font-family:<?php echo $slidepricemobile['SlidepricemobileLang']['font_font_mobile_1']; ?>;font-size:<?php echo $slidepricemobile['SlidepricemobileLang']['font_size_mobile_1']; ?>px;color:<?php echo $slidepricemobile['SlidepricemobileLang']['font_color_mobile_1']; ?>;"><?php echo $slidepricemobile['SlidepricemobileLang']['title_mobile']; ?></div>
	<?=$countdown_mobile ?>
</section>
<?php } ?>
	<?php
}else{
	
	if(!$is_slide_cms || $thepage['Page']['page_category_id'] == 22 || $thepage['Page']['page_category_id'] == 23 || $thepage['Page']['page_category_id'] == 24 || $thepage['Page']['page_category_id'] == 25 ){
	?>
	<div class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo $titre_h1; ?></h1>
	</div>
	<?php
	}else{
		if($this->request->isMobile()){
		if($thepage['PageLang']['bg_mobile']){
			$image = DS.Configure::read('Site.pathPageMobile').DS.$thepage['PageLang']['bg_mobile'];
		}else{
			$image = '/media/slidemobile/5-slidemobile-1.jpg';
			
		}
		if($thepage['PageLang']['phrase1_mobile']){
			$phrase1 = $thepage['PageLang']['phrase1_mobile'];
		}else{
			$phrase1 = __('agents privée en ligne par téléphone, chat ou mail');
		}
		if($thepage['PageLang']['phrase2_mobile']){
			$phrase2 = $thepage['PageLang']['phrase2_mobile'];
		}else{
			$phrase2 = __('98.2% de clients satisfaits, avis authentiques !');
		}
		if($thepage['PageLang']['btn_url']){
			$btn_url = $thepage['PageLang']['btn_url'];
		}else{
			$btn_url = '/users/subscribe';
		}
		if($thepage['PageLang']['btn_text']){
			$btn_text = $thepage['PageLang']['btn_text'];
		}else{
			$btn_text = __('Inscription gratuite');
		}
		$classbtn = '';
		if($this->Session->read('Auth.User')){
			$btn_text = __('Voir tous les experts');
			$btn_url = '/';//'#agents_list';
			$classbtn = 'btnscroll';
		}
	?>	
		<section class="slidermobile visible-xs" style="  background: #fff url('<?=$image ?>') no-repeat center center;padding: 0;">
			<div id="slidermobile" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner" role="listbox">
					<div class="item active">
						<div class="caro-caption">
							<ul class="slidermobile-tick-ul">
								<li class=" slideInRight animated" data-wow-delay="0.5s" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">
									<h1 style="text-align:center;font-family:Montserrat;font-size:18px;color:#5A449B;margin:0 auto 15px auto;width:80%"><?php echo $titre_h1; ?></h1>
								</li>
								<li class=" slideInRight animated" data-wow-delay="0.5s" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><p style="text-align:center;font-family:Montserrat;font-size:14px;color:#5A449B;margin:0"><?=$phrase1 ?></p></li>
								<li class=" slideInRight animated" data-wow-delay="0.5s" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><p style="text-align:center;font-family:Montserrat;font-size:14px;color:#5A449B;margin:0"><?=$phrase2 ?></p></li>
							</ul>
							<ul class="list-inline slider-button-group">
								<li><a href="<?=$btn_url ?>" class="btn btn-pink btn-slider  fadeInUp animated <?=$classbtn ?>" data-wow-delay=".5s" style="color:;background:#933C8F;visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;"><?=$btn_text ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>
	<?php	
		}else{
		if($thepage['PageLang']['bg_desktop']){
			$image = DS.Configure::read('Site.pathPageDesktop').DS.$thepage['PageLang']['bg_desktop'];
		}else{
			$image = '/media/slide/26-slide-1.jpg';
		}
		if($thepage['PageLang']['phrase1_desktop']){
			$phrase1 = $thepage['PageLang']['phrase1_desktop'];
		}else{
			$phrase1 = __('agents privée en ligne par téléphone, chat ou mail');
		}
		if($thepage['PageLang']['phrase2_desktop']){
			$phrase2 = $thepage['PageLang']['phrase2_desktop'];
		}else{
			$phrase2 = __('98.2% de clients satisfaits, avis authentiques !');
		}
		if($thepage['PageLang']['btn_url']){
			$btn_url = $thepage['PageLang']['btn_url'];
		}else{
			$btn_url = '/users/subscribe';
		}
		if($thepage['PageLang']['btn_text']){
			$btn_text = $thepage['PageLang']['btn_text'];
		}else{
			$btn_text = __('Inscription gratuite');
		}
			$classbtn = '';
		if($this->Session->read('Auth.User')){
			$btn_text = __('Voir tous les experts');
			$btn_url = '/';//'#search_filters';
			$classbtn = 'btnscroll';
		}
		?>
			<section class="slider hidden-xs" style="  background: #fff url('<?=$image ?>') no-repeat center center;">
				<div id="carousel" class="container carousel slide" data-ride="carousel">
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="caro-caption">
								<h1 style="text-align:inherit;color:#933C8F;font-family:AvenirLT-Book;font-size:45px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin:10px auto;width:80%"><?php echo $titre_h1; ?></h1><ul class="slider-tick-ul">
								<li class=" slideInRight animated" data-wow-delay="0.5s" style="display:block;text-align:inherit;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><p style="text-align:center;color:#933C8F;font-family:Fjalla One;font-size:18px"><?=$phrase1 ?></p></li>
								<li class=" slideInRight animated" data-wow-delay="1s" style="display:block;text-align:inherit;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><p style="text-align:center;color:#933C8F;font-family:Fjalla One;font-size:18px"><?=$phrase2 ?></p></li>
								<li class=" slideInRight animated" data-wow-delay="1.5s" style="display:block;text-align:inherit;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"></li>
								</ul>
								<ul class="list-inline slider-button-group"><li><a href="<?=$btn_url ?>" class="btn btn-pink btn-slider  fadeInUp animated <?=$classbtn ?>" data-wow-delay="2.0s" style=";visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;"><?=$btn_text ?></a></li></ul>
							</div>
						</div>
					</div>
				</div>
			</section>
		<?php
		}
	}
}


?>


<div class="container">
	<div class="single-page cms-page">
		<div class="row">
		<?php 
			if($is_right)echo '<div class="col-sm-12 col-md-9 ">';
			?>
		
		<div class="content_box content_box_page wow fadeIn" data-wow-delay="0.4s">
			<?php
			
			
				if(substr_count($content,'--TABLE--')){
					$tt = '';
					if( isset($promo) && $promo){
						$tt =  $this->element('cart_products_promo', array('products' => $products, 'user' => $this->Session->read('Auth.User'), 'promo' => $promo, 'promo_title' => $promo_title , 'is_promo_total' => $is_promo_total, 'is_page_cms' => 1));
					}else{
						$tt =  $this->element('cart_products', array('products' => $products, 'user' => $this->Session->read('Auth.User'), 'is_page_cms' => 1));
					}
					
					$table =  '<div class="table_single_container_page">'.$tt.'</div>';	
					$content = str_replace('<p>--TABLE--</p>', $table,$content);
				}
			
				if(substr_count($content,'--TABLE_RECOUVRE--')){
					$table =  '';	
					$tt =  $this->element('cart_products', array('products' => $products, 'user' => $this->Session->read('Auth.User'), 'is_page_cms' => 1, 'is_impaye' => 1));
					
					$table =  '<div class="table_single_container_page">'.$tt.'</div>';
					$content = str_replace('<p>--TABLE_RECOUVRE--</p>', $table,$content);
					$content = str_replace('<p style="text-align: center;">--TABLE_RECOUVRE--</p>', $table,$content);
					
				}
				
				if(!empty($_GET['kw'])){
					$word = htmlentities($_GET['kw']);
					$content = str_replace($word,'<mark>'.$_GET['kw'].'</mark>',$content);
				}
				
				echo $content;
				if($thepage['Page']['id'] != 4){
				$utm_source = str_replace('&','et',$thepage['PageLang']['name']);
				if(!substr_count($content,'btn-inscription-spiriteo') && !substr_count($content,'btn-pink-modified')){
				if(!$this->Session->read('Auth.User')){//
					
				?>
				<p style="margin-top:20px;" class="btn_cms_action">
					<a title="Inscription gratuite" href="/users/subscribe">
						<img class="img-page-action" style="display: block; margin-left: auto; margin-right: auto;" src="https://www.spiriteo.com/media/cms_photo/image/btn-inscription-spiriteo.png" width="300" height="76">
					</a>
				</p>
				<?php }else{ ?>
					<p style="margin-top:20px;" class="btn_cms_action">
					<a title="Consulter" href="/">
						<img class="img-page-action" style="display: block; margin-left: auto; margin-right: auto;" src="https://www.spiriteo.com/media/cms_photo/image/btn-consulter2-spiriteo.png" width="300" height="76">
					</a>
				</p>
				<?php } } } ?>
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
    <!-- <section class="page_widget" id="agents_bottom">
     	<?php
			//echo $this->Frontblock->getBottomWidget();
			?>
     </section>-->
 </div>

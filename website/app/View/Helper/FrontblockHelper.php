<?php
App::uses('AppHelper', 'View/Helper');


class FrontblockHelper extends AppHelper {
    public $helpers = array('Html','Session','Form','Time');

    public function getLastReview($limit=10)
    {
        return $this->getReviews(array('date_add DESC'), 0, $limit);
    }
    public function getReviews($order=array('date_add DESC'), $page=0, $limit=10)
    {
        App::import("Model", "Review");
        $review = new Review();

        $rows = $review->find("all",array(
            'conditions'  =>  array(
                'Review.status' => 1,
				'Review.parent_id' => NULL

            ),
            'order'       =>  $order,
            'limit'       =>  $limit,
            'page'        =>  $page
        ));
		//'Review.lang_id' => $this->Session->read('Config.id_lang')
        return $rows;
    }

    public function voisins($data, $direction){
        if(empty($data) || !in_array($direction, array('prev', 'next')))
            return '';

        $blockLink = '<div class="agent-'.$direction.'"><div class="voisins">';
        $blockLink.= '<span class="glyphicon glyphicon-chevron-'. ($direction === 'prev' ?'left':'right') .'"></span>';
        $blockLink.= '<span class="name">'.$data[$direction]['User']['pseudo'].'</span></div></div>';

        $out = $this->Html->link($blockLink,
            array(
                'controller' => 'agents',
                'action'    => 'display',
                'link_rewrite' => strtolower(str_replace(' ','-',$data[$direction]['User']['pseudo'])),
                'agent_number' => $data[$direction]['User']['agent_number'],
                'language'    => $this->Session->read('Config.language')
            ),
            array('escape' => false));

        return $out;
    }

    public function getStarsRate($rate=0, $maxRate=5)
    {
        if(empty($rate))
            return '';

       /* $html = array("<div class=\"br_stars\">");
        for ($i=1; $i<$maxRate+1; $i++){
            $html[] = '<span class="star_rate '.(($i<=$rate)?' star_enabled':' star_disabled').'"></span>';
        }
        $html[] = '</div>';
        return implode("\n", $html);*/

		$html = '<div class="br_stars">';
		$html .= '<i class="fa fa-star'; if($rate == 0.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 0.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 1.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 1.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 2.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 2.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 3.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 3.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 4.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 4.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '</div>';
		return $html;
    }
	public function getStarsRateListing($rate=0, $pourcent = '', $nb = 0, $maxRate=5)
    {
        if(empty($rate))
            return '';

       /* $html = array("<div class=\"br_stars\">");
        for ($i=1; $i<$maxRate+1; $i++){
            $html[] = '<span class="star_rate '.(($i<=$rate)?' star_enabled':' star_disabled').'"></span>';
        }
        $html[] = '</div>';
        return implode("\n", $html);*/

		//$html = '<div class="br_stars">';
		if($nb)
			$html = '<li class="per"> <strong>'.$pourcent.'</strong> sur <strong>'.$nb.'</strong> avis</li>';
		else
			$html = '<li class="per"> '.$pourcent.'</li>';

		$html .= '<li><i class="fa fa-star '; if($rate == 0.5) $html .= '-half-o'; if($rate >= 0.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 1.5) $html .= '-half-o'; if($rate >= 1.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 2.5) $html .= '-half-o'; if($rate >= 2.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 3.5) $html .= '-half-o'; if($rate >= 3.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 4.5) $html .= '-half-o'; if($rate >= 4.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';

		/*$html .= '<i class="fa fa-star'; if($rate == 0.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 0.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 1.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 1.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 2.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 2.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 3.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 3.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 4.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 4.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';*/
		//$html .= '<span class="ae_presentation"> '.$pourcent.'</span></div>';
		return $html;
    }
	public function getStarsRateDisplay($rate=0, $maxRate=5)
    {
        if(empty($rate))
            return '';
		$html = '';
		$html .= '<li><i class="fa fa-star '; if($rate == 0.5) $html .= '-half-o'; if($rate >= 0.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 1.5) $html .= '-half-o'; if($rate >= 1.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 2.5) $html .= '-half-o'; if($rate >= 2.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 3.5) $html .= '-half-o'; if($rate >= 3.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 4.5) $html .= '-half-o'; if($rate >= 4.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';

		return $html;
    }

	public function getStarsRateListingInv($rate=0, $pourcent = '', $maxRate=5)
    {
        if(empty($rate))
            return '';

       /* $html = array("<div class=\"br_stars\">");
        for ($i=1; $i<$maxRate+1; $i++){
            $html[] = '<span class="star_rate '.(($i<=$rate)?' star_enabled':' star_disabled').'"></span>';
        }
        $html[] = '</div>';
        return implode("\n", $html);*/

		//$html = '<div class="br_stars">';
		$html = '';
		$html .= '<li><i class="fa fa-star '; if($rate == 0.5) $html .= '-half-o'; if($rate >= 0.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 1.5) $html .= '-half-o'; if($rate >= 1.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 2.5) $html .= '-half-o'; if($rate >= 2.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 3.5) $html .= '-half-o'; if($rate >= 3.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 4.5) $html .= '-half-o'; if($rate >= 4.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li class="per"> '.$pourcent.'</li>';

		/*$html .= '<i class="fa fa-star'; if($rate == 0.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 0.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 1.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 1.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 2.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 2.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 3.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 3.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '<i class="fa fa-star'; if($rate == 4.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 4.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';*/
		//$html .= '<span class="ae_presentation"> '.$pourcent.'</span></div>';
		return $html;
    }

    public function secondsToHis($seconds, $header = false) {
        $t = round($seconds);

        if($header)
            return sprintf('%02dh %02dmin %02ds', ($t/3600),($t/60%60), $t%60);
        else
            return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
    }
    /*public function  getCatDescription($id){
        App::import("Model", "CategoryLang");
        $catLang = new CategoryLang();
        $cat = $catLang->find('first',array(
            'fields' => array('name', 'description', 'link_rewrite', 'meta_title'),
            'conditions' => array('category_id' => $id, 'lang_id' => $this->Session->read('Config.id_lang')),
            'recursive' => -1
        ));

        return $cat;
    }*/
/*
    public function getPageLink($id=0, $parms=array(), $title='')
    {
        App::import("Model", "Page");
        $model = new Page();

        $row = $model->PageLang->find('first',array(
            'fields'     => array('link_rewrite','meta_title','name'),
            'conditions' => array(
                'Page.id'           => $id,
                'Page.active'       => 1,
                'PageLang.lang_id'  => $this->Session->read('Config.id_lang')
            )));

        $link = $this->Html->link(!empty($title)?$title:$row['PageLang']['name'], array(
            'language'      => $this->Session->read('Config.language'),
            'controller'        => 'pages',
            'action'            => 'display',
            'id'                => 1,
            'link_rewrite'      => $row['PageLang']['link_rewrite']
        ), $parms);
        return $link;
    }
*/
    public function getPageLink($id=0, $parms=array(), $title='')
    {
        App::import("Model", "Page");
        $model = new Page();
        $row = $model->PageLang->find('first',array(
            'fields'     => array('link_rewrite','meta_title','name','Page.page_category_id'),
            'conditions' => array(
                'Page.id'           => $id,
                'Page.active'       => 1,
                'PageLang.lang_id'  => $this->Session->read('Config.id_lang')
            )));

        $langage_code = $this->Session->read('Config.language');
		$seo_words_from_lang_code = array();
		App::import("Model", "PageCategoryLang");
        $pagecatLang = new PageCategoryLang();
        $cat = $pagecatLang->find('first',array(
            'fields' => array('name'),
            'conditions' => array('page_category_id' => $row['Page']['page_category_id'],'lang_id' => $this->Session->read('Config.id_lang')),
            'recursive' => -1
        ));
		$word = $this->slugify($cat['PageCategoryLang']['name']);
		$seo_word = isset($word)?$word:'category';
        $opt  = array(
            'language' => $this->Session->read('Config.language'),
            'controller' => 'pages',
            'action' => 'display',
            'admin' => false,
            'link_rewrite' => $row['PageLang']['link_rewrite'],
            'seo_word' => $seo_word
        );

        $link = $this->Html->link(!empty($title)?$title:$row['PageLang']['name'], $opt, $parms);
        return $link;
    }
    public function getCaroussel(){

        App::import('Model', 'Slide');

        $model = new Slide();
        $dateNow = date('Y-m-d H:i:00');
        //Les slides (active, dans une période de validité, pour ce site, dans la langue actuelle)
        $slides = $model->find('all', array(
            'fields' => array('Slide.*', 'SlideLang.*'),
            'conditions' => array(
                'Slide.active' => 1,
                'Slide.validity_start <=' => $dateNow,
				'Slide.domain LIKE' => '%'.$this->Session->read('Config.id_domain').'%',
                'OR'    => array(
                    array('Slide.validity_end >=' => $dateNow),
                    array('Slide.validity_end IS NULL')
                )
            ),
            'joins' => array(
                array(
                    'table' => 'slide_langs',
                    'alias' => 'SlideLang',
                    'type'  => 'inner',
                    'conditions' => array(
                        'SlideLang.slide_id = Slide.id',
                        'SlideLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'Slide.position asc',
            'recursive' => -1
        ));

		$user = $this->Session->read('Auth.User');

        $current_credit = false;



        //Pour chaque slide est-il affecté à ce domaine
        foreach($slides as $key => $slide){
            $domainsId = explode(',', $slide['Slide']['domain']);
            //Si le slide n'est pas affecté à ce domain
            if(!in_array($this->Session->read('Config.id_domain'), $domainsId))
                unset($slides[$key]);
        }

        //Plus de slide, alors on retourne la scene par défault
        if(empty($slides))
            return false;


		if (!empty($user) && ($user['role'] == 'agent' || $user['role'] == 'client')){
        	//<!--slider to show once the user is logged in-->
			$out = '<section class="slider-logged hidden-xs">
				<h1>Consultez les meilleurs voyants d\'Europe</h1>
				<!--h1/h2 both works here-->
			</section>';

		}else{



			//$paddingslider = 'padding: 130px 0 0 0;';
			/*$dataSlide = getimagesize(Configure::read('Site.pathSlide').'/'.$slide['SlideLang']['name']);
			if($dataSlide[1] <= Configure::read('Slide.height')){
				$delta = ((Configure::read('Slide.height') - $dataSlide[1]) / 2);
				if(130 >= $delta)
					$padd_top = ceil(130 - $delta-5);
				else
					$padd_top = ceil(130 -5);
				if(30 > $delta)
					$padd_bottom = ceil(30 - $delta);
				else
					$padd_bottom = 30;
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0;';
			}
			if($dataSlide[1] > Configure::read('Slide.height')){
				$delta = (($dataSlide[1] - Configure::read('Slide.height') ) / 2);
				$padd_top = ceil(130 + $delta-10);
				$padd_bottom = ceil(30 + $delta-10);
				//$marge_top = $dataSlide[1] - Configure::read('Slide.height')+8;
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0;';
			}*/


			$linkslide = ' style="  background: transparent url(\'/'.Configure::read('Site.pathSlide').'/'.$slide['SlideLang']['name'].'?'.date('YmdHis').'\') no-repeat center center;"';
			$out = '
				<section class="slider hidden-xs" '.$linkslide.'>
					<div id="carousel" class="container carousel slide" data-ride="carousel">
						<div class="carousel-inner" role="listbox">';

			$i=0;
			foreach($slides as $slide){

			if($i==0)$active = 'active';else $active = '';

			if(!$slide['SlideLang']['color']) $slide['SlideLang']['color'] = '#5A449B';
			if(!$slide['SlideLang']['color1']) $slide['SlideLang']['color1'] = $slide['SlideLang']['color'];
			if(!$slide['SlideLang']['color2']) $slide['SlideLang']['color2'] = $slide['SlideLang']['color'];
			if(!$slide['SlideLang']['color3']) $slide['SlideLang']['color3'] = $slide['SlideLang']['color'];
			if(!$slide['SlideLang']['color4']) $slide['SlideLang']['color4'] = $slide['SlideLang']['color'];
			if(!$slide['SlideLang']['color5']) $slide['SlideLang']['color5'] = $slide['SlideLang']['color'];

			if(!$slide['SlideLang']['size1']) $slide['SlideLang']['size1'] = '45';
			if(!$slide['SlideLang']['size2']) $slide['SlideLang']['size2'] = '18';
			if(!$slide['SlideLang']['size3']) $slide['SlideLang']['size3'] = '18';
			if(!$slide['SlideLang']['size4']) $slide['SlideLang']['size4'] = '18';
			if(!$slide['SlideLang']['size5']) $slide['SlideLang']['size5'] = '18';

			$list_font = array();
			$list_font_interne = array('Fjalla One');

			if($slide['SlideLang']['font1'] && !in_array($slide['SlideLang']['font1'],$list_font_interne)  ){
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlideLang']['font1']).'">';
				array_push($list_font,$slide['SlideLang']['font1']);
			}
			if(!$slide['SlideLang']['font1']) $slide['SlideLang']['font1'] = 'AvenirLT-Book';
			if($slide['SlideLang']['font2'] && !in_array($slide['SlideLang']['font2'],$list_font) && !in_array($slide['SlideLang']['font2'],$list_font_interne)){
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlideLang']['font2']).'">';
				array_push($list_font,$slide['SlideLang']['font2']);
			}
			if(!$slide['SlideLang']['font2']) $slide['SlideLang']['font2'] = 'AvenirLT-Book';
			if($slide['SlideLang']['font3'] && !in_array($slide['SlideLang']['font3'],$list_font) && !in_array($slide['SlideLang']['font3'],$list_font_interne)){
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlideLang']['font3']).'">';
				array_push($list_font,$slide['SlideLang']['font3']);
			}
			if(!$slide['SlideLang']['font3']) $slide['SlideLang']['font3'] = 'AvenirLT-Book';
			if($slide['SlideLang']['font4'] && !in_array($slide['SlideLang']['font4'],$list_font) && !in_array($slide['SlideLang']['font4'],$list_font_interne)){
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlideLang']['font4']).'">';
				array_push($list_font,$slide['SlideLang']['font4']);
			}
			if(!$slide['SlideLang']['font4']) $slide['SlideLang']['font4'] = 'AvenirLT-Book';
			if($slide['SlideLang']['font5'] && !in_array($slide['SlideLang']['font5'],$list_font) && !in_array($slide['SlideLang']['font5'],$list_font_interne)){
				echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlideLang']['font5']).'">';
				array_push($list_font,$slide['SlideLang']['font5']);
			}
			if(!$slide['SlideLang']['font5']) $slide['SlideLang']['font5'] = 'AvenirLT-Book';

			if(!$slide['SlideLang']['align1']) $slide['SlideLang']['align1'] = 'inherit';
			if(!$slide['SlideLang']['align2']) $slide['SlideLang']['align2'] = 'inherit';
			if(!$slide['SlideLang']['align3']) $slide['SlideLang']['align3'] = 'inherit';
			if(!$slide['SlideLang']['align4']) $slide['SlideLang']['align4'] = 'inherit';
			if(!$slide['SlideLang']['align5']) $slide['SlideLang']['align5'] = 'inherit';


			if($slide['SlideLang']['code1'] == 'H1')$slide['SlideLang']['titre1'] = '<h1 style="text-align:'.$slide['SlideLang']['align1'].';color:'.$slide['SlideLang']['color1'].';font-family:'.$slide['SlideLang']['font1'].';font-size:'.$slide['SlideLang']['size1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre1'].'</h1>';
			if($slide['SlideLang']['code1'] == 'P')$slide['SlideLang']['titre1'] = '<p style="text-align:'.$slide['SlideLang']['align1'].';color:'.$slide['SlideLang']['color1'].';font-family:'.$slide['SlideLang']['font1'].';font-size:'.$slide['SlideLang']['size1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre1'].'</p>';

			if($slide['SlideLang']['code1'] == 'H2') $slide['SlideLang']['titre1'] = '<h2 style="text-align:'.$slide['SlideLang']['align1'].';color:'.$slide['SlideLang']['color1'].';font-family:'.$slide['SlideLang']['font1'].';font-size:'.$slide['SlideLang']['size1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre1'].'</h2>';

			if($slide['SlideLang']['code2'] == 'H1') $slide['SlideLang']['titre2'] = '<h1 style="text-align:'.$slide['SlideLang']['align2'].';color:'.$slide['SlideLang']['color2'].';font-family:'.$slide['SlideLang']['font2'].';font-size:'.$slide['SlideLang']['size2'].'px;text-align:'.$slide['SlideLang']['align1'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre2'].'</h1>';
			if($slide['SlideLang']['code2'] == 'H2') $slide['SlideLang']['titre2'] = '<h2 style="text-align:'.$slide['SlideLang']['align2'].';color:'.$slide['SlideLang']['color2'].';font-family:'.$slide['SlideLang']['font2'].';font-size:'.$slide['SlideLang']['size2'].'px;text-align:'.$slide['SlideLang']['align2'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre2'].'</h2>';
			if($slide['SlideLang']['code2'] == 'P') $slide['SlideLang']['titre2'] = '<p style="text-align:'.$slide['SlideLang']['align2'].';color:'.$slide['SlideLang']['color2'].';font-family:'.$slide['SlideLang']['font2'].';font-size:'.$slide['SlideLang']['size2'].'px">'.$slide['SlideLang']['titre2'].'</p>';
			if($slide['SlideLang']['code3'] == 'P') $slide['SlideLang']['titre3'] = '<p style="text-align:'.$slide['SlideLang']['align3'].';color:'.$slide['SlideLang']['color3'].';font-family:'.$slide['SlideLang']['font3'].';font-size:'.$slide['SlideLang']['size3'].'px">'.$slide['SlideLang']['titre3'].'</p>';
			if($slide['SlideLang']['code4'] == 'P') $slide['SlideLang']['titre4'] = '<p style="text-align:'.$slide['SlideLang']['align4'].';color:'.$slide['SlideLang']['color4'].';font-family:'.$slide['SlideLang']['font4'].';font-size:'.$slide['SlideLang']['size4'].'px">'.$slide['SlideLang']['titre4'].'</p>';
			if($slide['SlideLang']['code5'] == 'P') $slide['SlideLang']['titre5'] = '<p style="text-align:'.$slide['SlideLang']['align5'].';color:'.$slide['SlideLang']['color5'].';font-family:'.$slide['SlideLang']['font5'].';font-size:'.$slide['SlideLang']['size5'].'px">'.$slide['SlideLang']['titre5'].'</p>';
			if($slide['SlideLang']['code2'] == 'SPAN') $slide['SlideLang']['titre2'] = '<span style="text-align:'.$slide['SlideLang']['align2'].';color:'.$slide['SlideLang']['color2'].';font-family:'.$slide['SlideLang']['font2'].';font-size:'.$slide['SlideLang']['size2'].'px">'.$slide['SlideLang']['titre2'].'</span>';
			if($slide['SlideLang']['code3'] == 'SPAN') $slide['SlideLang']['titre3'] = '<span style="text-align:'.$slide['SlideLang']['align3'].';color:'.$slide['SlideLang']['color3'].';font-family:'.$slide['SlideLang']['font3'].';font-size:'.$slide['SlideLang']['size3'].'px">'.$slide['SlideLang']['titre3'].'</span>';
			if($slide['SlideLang']['code4'] == 'SPAN') $slide['SlideLang']['titre4'] = '<span style="text-align:'.$slide['SlideLang']['align4'].';color:'.$slide['SlideLang']['color4'].';font-family:'.$slide['SlideLang']['font4'].';font-size:'.$slide['SlideLang']['size4'].'px">'.$slide['SlideLang']['titre4'].'</span>';
			if($slide['SlideLang']['code5'] == 'SPAN') $slide['SlideLang']['titre5'] = '<span style="text-align:'.$slide['SlideLang']['align5'].';color:'.$slide['SlideLang']['color5'].';font-family:'.$slide['SlideLang']['font5'].';font-size:'.$slide['SlideLang']['size5'].'px">'.$slide['SlideLang']['titre5'].'</span>';


			if(!$slide['SlideLang']['color_btn1']) $slide['SlideLang']['color_btn1'] = '#fff';
			if(!$slide['SlideLang']['color_btn2']) $slide['SlideLang']['color_btn2'] = '#fff';
			if(!$slide['SlideLang']['back_btn1']) $slide['SlideLang']['back_btn1'] = 'rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0';
			if(!$slide['SlideLang']['back_btn2']) $slide['SlideLang']['back_btn2'] = 'rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0';

			if($slide['SlideLang']['date_fin']  && $slide['SlideLang']['date_fin'] != '0000-00-00 00:00:00'){

				$size_compteur = '20px';
				if($slide['SlideLang']['size_compteur'])$size_compteur = $slide['SlideLang']['size_compteur'];
				$color_compteur = $slide['SlideLang']['color'];
				if($slide['SlideLang']['color_compteur'])$color_compteur = $slide['SlideLang']['color_compteur'];
				$text_compteur = '';
				if($slide['SlideLang']['text_compteur'])$text_compteur = '<p style="text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'">'.$slide['SlideLang']['text_compteur'].'</p>';
				$countdown =$text_compteur.'<span class="clock" rel="'.$slide['SlideLang']['date_fin'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'"></span>';
			}
			if(!isset($countdown))	$countdown = '';
			$out.= '<div class="item '.$active.'">
					<div class="caro-caption">
						<ul class="slider-tick-ul">
							<li class=" slideInRight animated" style="display:block;text-align:'.$slide['SlideLang']['align1'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre1'].'</li>
							<li class=" slideInRight animated" style="display:block;text-align:'.$slide['SlideLang']['align2'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre2'].'</li>
							<li class=" slideInRight animated" style="display:block;text-align:'.$slide['SlideLang']['align3'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre3'].'</li>
							<li class=" slideInRight animated" style="display:block;text-align:'.$slide['SlideLang']['align4'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre4'].'</li>
							<li class=" slideInRight animated" style="display:block;text-align:'.$slide['SlideLang']['align5'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['SlideLang']['titre5'].'</li>
						</ul>
							'.$countdown.'
						<ul class="list-inline slider-button-group">';
				if($slide['SlideLang']['titre_btn1'])
						$out.= '<li><a href="'.$slide['SlideLang']['link_btn1'].'" class="btn btn-pink btn-slider  fadeInUp animated"  style="color:'.$slide['SlideLang']['color_btn1'].';background:'.$slide['SlideLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;">'.$slide['SlideLang']['titre_btn1'].'</a></li>';
				if($slide['SlideLang']['titre_btn2'])
							$out.= '<li><a href="'.$slide['SlideLang']['link_btn2'].'" class="btn btn-pink btn-slider  fadeInUp scroll animated" style="color:'.$slide['SlideLang']['color_btn2'].';background:'.$slide['SlideLang']['back_btn2'].';visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;">'.$slide['SlideLang']['titre_btn2'].'</a></li>';
						$out.= '</ul>

					</div>
				</div>	';
				$i++;
			}


			$out.= '</div>';


			if(count($slides) > 1){
			$out.= '<!-- Controls -->
			<a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
				<!-- <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> -->
				<img src="/theme/default/img/icons/slide_left.png" alt="Spiriteo - agents en ligne" >
				<!-- <span class="sr-only">Précédent</span> -->
			</a>
			<a class="right carousel-control" href="#carousel" role="button" data-slide="next">
				<img src="/theme/default/img/icons/slide_right.png" alt="Spiriteo - agents en ligne" >
				<!-- <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> -->
				<!-- <span class="sr-only">Suivant</span> -->
			</a>';
			}
		$out.= '</div>

		<!--<div class="scroll-next">
			<a class="scroll" href="#agents_list"><i class="fa fa-2x fa-chevron-down" aria-hidden="true"></i></a>
		</div>-->

	</section><!--slider END-->';




		}
        return $out;
    }

	public function getLandingCaroussel($landing_id = 0){

		App::import('Model', 'Landing');

        $model = new Landing();
        //Les slides (active, dans une période de validité, pour ce site, dans la langue actuelle)
        $slides = $model->find('all', array(
            'fields' => array('Landing.*', 'LandingLang.*'),
            'conditions' => array(
                'Landing.active' => 1,
				'Landing.id' => $landing_id,
            ),
            'joins' => array(
                array(
                    'table' => 'landing_langs',
                    'alias' => 'LandingLang',
                    'type'  => 'inner',
                    'conditions' => array(
                        'LandingLang.landing_id = Landing.id',
                        'LandingLang.lang_id = 1'//.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'Landing.id asc',
            'recursive' => -1
        ));

		$user = $this->Session->read('Auth.User');


        //Pour chaque slide est-il affecté à ce domaine
       /* foreach($slides as $key => $slide){
            $domainsId = explode(',', $slide['Slide']['domain']);
            //Si le slide n'est pas affecté à ce domain
            if(!in_array($this->Session->read('Config.id_domain'), $domainsId))
                unset($slides[$key]);
        }*/


			$paddingslider = 'padding: 105px 0 30px 0;';
			$dataSlide = getimagesize(Configure::read('Site.pathLandingSlide').'/'.$slides[0]['LandingLang']['slide']);
			if($dataSlide[1] < Configure::read('LandingSlide.height')){
				$delta = ((Configure::read('LandingSlide.height') - $dataSlide[1]) / 2);
				$padd_top = ceil(110 - $delta-5);
				$padd_bottom = ceil(30 - $delta);
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}
			if($dataSlide[1] > Configure::read('LandingSlide.height')){
				$delta = (($dataSlide[1] - Configure::read('LandingSlide.height') ) / 2);
				$padd_top = ceil($delta-20);
				$padd_bottom = ceil(30 + $delta-10);
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}


			$linkslide = ' style="  background: #fff url(\'/'.Configure::read('Site.pathLandingSlide').'/'.$slides[0]['LandingLang']['slide'].'\') no-repeat center center;'.$paddingslider.'"';
			$out = '
				<section class="slider hidden-xs" '.$linkslide.'>
					<div id="carousel" class="container carousel slide" data-ride="carousel">
						<div class="carousel-inner" role="listbox">';


			$i=0;
			foreach($slides as $slide){

				if($i==0)$active = 'active';else $active = '';

				if(!$slide['LandingLang']['font_type_1']) $slide['LandingLang']['font_type_1'] = 'H1';
				if(!$slide['LandingLang']['font_type_2']) $slide['LandingLang']['font_type_2'] = 'P';
				if(!$slide['LandingLang']['font_type_3']) $slide['LandingLang']['font_type_3'] = 'P';
				if(!$slide['LandingLang']['font_type_4']) $slide['LandingLang']['font_type_4'] = 'P';

				if(!$slide['LandingLang']['font_color_1']) $slide['LandingLang']['font_color_1'] = '#5A449B';
				if(!$slide['LandingLang']['font_color_2']) $slide['LandingLang']['font_color_2'] = '#5A449B';
				if(!$slide['LandingLang']['font_color_3']) $slide['LandingLang']['font_color_3'] = '#5A449B';
				if(!$slide['LandingLang']['font_color_4']) $slide['LandingLang']['font_color_4'] = '#5A449B';

				if(!$slide['LandingLang']['font_size_1']) $slide['LandingLang']['font_size_1'] = '45';
				if(!$slide['LandingLang']['font_size_2']) $slide['LandingLang']['font_size_2'] = '18';
				if(!$slide['LandingLang']['font_size_3']) $slide['LandingLang']['font_size_3'] = '18';
				if(!$slide['LandingLang']['font_size_4']) $slide['LandingLang']['font_size_4'] = '18';

				$list_font_interne = array('Fjalla One');

				if($slide['LandingLang']['font_font_1'] && !in_array($slide['LandingLang']['font_font_1'],$list_font_interne))echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_1']).'">';
				if(!$slide['LandingLang']['font_font_1']) $slide['LandingLang']['font_font_1'] = 'AvenirLT-Book';
				if($slide['LandingLang']['font_font_2'] && !in_array($slide['LandingLang']['font_font_2'],$list_font_interne))echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_2']).'">';
				if(!$slide['LandingLang']['font_font_2']) $slide['LandingLang']['font_font_2'] = 'AvenirLT-Book';
				if($slide['LandingLang']['font_font_3'] && !in_array($slide['LandingLang']['font_font_3'],$list_font_interne))echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_3']).'">';
				if(!$slide['LandingLang']['font_font_3']) $slide['LandingLang']['font_font_3'] = 'AvenirLT-Book';
				if($slide['LandingLang']['font_font_4'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_4']).'">';
				if(!$slide['LandingLang']['font_font_4'] && !in_array($slide['LandingLang']['font_font_4'],$list_font_interne)) $slide['LandingLang']['font_font_4'] = 'AvenirLT-Book';

				if(!$slide['LandingLang']['font_align_1']) $slide['LandingLang']['font_align_1'] = '';
				if(!$slide['LandingLang']['font_align_2']) $slide['LandingLang']['font_align_2'] = '';
				if(!$slide['LandingLang']['font_align_3']) $slide['LandingLang']['font_align_3'] = '';
				if(!$slide['LandingLang']['font_align_4']) $slide['LandingLang']['font_align_4'] = '';



				if($slide['LandingLang']['font_type_1'] == 'H1' && $slide['LandingLang']['titre1']) $slide['LandingLang']['titre1'] = '<h1 style="text-align:'.$slide['LandingLang']['font_align_1'].';color:'.$slide['LandingLang']['font_color_1'].';font-family:'.$slide['LandingLang']['font_font_1'].';font-size:'.$slide['LandingLang']['font_size_1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin:0px 0 20px 0 !important;">'.$slide['LandingLang']['titre1'].'</h1>';
				if($slide['LandingLang']['font_type_1'] == 'H2' && $slide['LandingLang']['titre1']) $slide['LandingLang']['titre1'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_1'].';color:'.$slide['LandingLang']['font_color_1'].';font-family:'.$slide['LandingLang']['font_font_1'].';font-size:'.$slide['LandingLang']['font_size_1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre1'].'</h2>';
				if($slide['LandingLang']['font_type_1'] == 'P' && $slide['LandingLang']['titre1']) $slide['LandingLang']['titre1'] = '<p style="text-align:'.$slide['LandingLang']['font_align_1'].';color:'.$slide['LandingLang']['font_color_1'].';font-family:'.$slide['LandingLang']['font_font_1'].';font-size:'.$slide['LandingLang']['font_size_1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin-bottom:0px;">'.$slide['LandingLang']['titre1'].'</p>';
				if($slide['LandingLang']['font_type_1'] == 'SPAN' && $slide['LandingLang']['titre1']) $slide['LandingLang']['titre1'] = '<span style="text-align:'.$slide['LandingLang']['font_align_1'].';color:'.$slide['LandingLang']['font_color_1'].';font-family:'.$slide['LandingLang']['font_font_1'].';font-size:'.$slide['LandingLang']['font_size_1'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre1'].'</span>';



				if($slide['LandingLang']['font_type_2'] == 'H2' && $slide['LandingLang']['titre2']) $slide['LandingLang']['titre2'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_2'].';color:'.$slide['LandingLang']['font_color_2'].';font-family:'.$slide['LandingLang']['font_font_2'].';font-size:'.$slide['LandingLang']['font_size_2'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre2'].'</h2>';
				if($slide['LandingLang']['font_type_2'] == 'P' && $slide['LandingLang']['titre2']) $slide['LandingLang']['titre2'] = '<p style="padding:4px 0;text-align:'.$slide['LandingLang']['font_align_2'].';color:'.$slide['LandingLang']['font_color_2'].';font-family:'.$slide['LandingLang']['font_font_2'].';font-size:'.$slide['LandingLang']['font_size_2'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin-bottom:0px;">'.$slide['LandingLang']['titre2'].'</p>';
				if($slide['LandingLang']['font_type_2'] == 'SPAN' && $slide['LandingLang']['titre2']) $slide['LandingLang']['titre2'] = '<span style="text-align:'.$slide['LandingLang']['font_align_2'].';color:'.$slide['LandingLang']['font_color_2'].';font-family:'.$slide['LandingLang']['font_font_2'].';font-size:'.$slide['LandingLang']['font_size_2'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre2'].'</span>';

				if($slide['LandingLang']['font_type_3'] == 'H2' && $slide['LandingLang']['titre3']) $slide['LandingLang']['titre3'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_3'].';color:'.$slide['LandingLang']['font_color_3'].';font-family:'.$slide['LandingLang']['font_font_3'].';font-size:'.$slide['LandingLang']['font_size_3'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre3'].'</h2>';
				if($slide['LandingLang']['font_type_3'] == 'P' && $slide['LandingLang']['titre3']) $slide['LandingLang']['titre3'] = '<p style="padding:4px 0;text-align:'.$slide['LandingLang']['font_align_3'].';color:'.$slide['LandingLang']['font_color_3'].';font-family:'.$slide['LandingLang']['font_font_3'].';font-size:'.$slide['LandingLang']['font_size_3'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin-bottom:0px;">'.$slide['LandingLang']['titre3'].'</p>';
				if($slide['LandingLang']['font_type_3'] == 'SPAN' && $slide['LandingLang']['titre3']) $slide['LandingLang']['titre3'] = '<span style="text-align:'.$slide['LandingLang']['font_align_3'].';color:'.$slide['LandingLang']['font_color_3'].';font-family:'.$slide['LandingLang']['font_font_3'].';font-size:'.$slide['LandingLang']['font_size_3'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre3'].'</span>';

				
				
				
				if($slide['LandingLang']['font_type_4'] == 'H2' && $slide['LandingLang']['titre4']) $slide['LandingLang']['titre4'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_4'].';color:'.$slide['LandingLang']['font_color_4'].';font-family:'.$slide['LandingLang']['font_font_4'].';font-size:'.$slide['LandingLang']['font_size_4'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre4'].'</h2>';
				if($slide['LandingLang']['font_type_4'] == 'P' && $slide['LandingLang']['titre4']) $slide['LandingLang']['titre4'] = '<p style="padding:4px 0;text-align:'.$slide['LandingLang']['font_align_4'].';color:'.$slide['LandingLang']['font_color_4'].';font-family:'.$slide['LandingLang']['font_font_4'].';font-size:'.$slide['LandingLang']['font_size_4'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;margin-bottom:0px;">'.$slide['LandingLang']['titre4'].'</p>';
				if($slide['LandingLang']['font_type_4'] == 'SPAN' && $slide['LandingLang']['titre4']) $slide['LandingLang']['titre4'] = '<span style="text-align:'.$slide['LandingLang']['font_align_4'].';color:'.$slide['LandingLang']['font_color_4'].';font-family:'.$slide['LandingLang']['font_font_4'].';font-size:'.$slide['LandingLang']['font_size_4'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre4'].'</span>';

				if(!isset($slide['LandingLang']['color_btn1'])) $slide['LandingLang']['color_btn1'] = '#fff';
				if(!isset($slide['LandingLang']['back_btn1'])) $slide['LandingLang']['back_btn1'] = 'rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0';

				$countdown = '';
				if($slide['LandingLang']['date_compteur']  && $slide['LandingLang']['date_compteur'] != '0000-00-00 00:00:00'){

					$size_compteur = '20px';
					if($slide['LandingLang']['size_compteur'])$size_compteur = $slide['LandingLang']['size_compteur'];
					$color_compteur = $slide['LandingLang']['color'];
					if($slide['LandingLang']['color_compteur'])$color_compteur = $slide['LandingLang']['color_compteur'];
					$text_compteur = '';
					if($slide['LandingLang']['text_compteur'])$text_compteur = '<p style="text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.';margin-bottom:-2px;">'.$slide['LandingLang']['text_compteur'].'</p>';
					$countdown =$text_compteur.'<span class="clock" rel="'.$slide['LandingLang']['date_compteur'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'"></span>';
				}
				if(!isset($slide['LandingLang']['align2']))$slide['LandingLang']['align2'] = '';
				if(!isset($slide['LandingLang']['align3']))$slide['LandingLang']['align3'] = '';
				if(!isset($slide['LandingLang']['align4']))$slide['LandingLang']['align4'] = '';
				$out.= '<div class="item '.$active.'">
						<div class="caro-caption">
							'.$slide['LandingLang']['titre1'].'
							<ul class="slider-tick-ul">';
								if($slide['LandingLang']['titre2'])
								$out.= '<li class=" slideInRight animated" style="display:block;text-align:'.$slide['LandingLang']['align2'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre2'].'</li>';
								if($slide['LandingLang']['titre3'])
								$out.= '<li class=" slideInRight animated" style="display:block;text-align:'.$slide['LandingLang']['align3'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre3'].'</li>';
								if($slide['LandingLang']['titre4'])
								$out.= '<li class=" slideInRight animated" style="display:block;text-align:'.$slide['LandingLang']['align4'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre4'].'</li>';
							$out.= '</ul>'.$countdown;

								if($slide['LandingLang']['btn1_txt'] || $slide['LandingLang']['btn2_txt']){

								    $user = $this->Session->read('Auth.User');
									$out.= '<ul class=" list-inline slider-button-group" style="text-align: center;">';
									if(!empty($user) && $slide['LandingLang']['btn1_url'] == '#inscription')$slide['LandingLang']['btn1_txt'] = '';
									$background=$slide['LandingLang']['btn1_bg'];
                                    $btnColor=$slide['LandingLang']['btn1_color'];

									if($slide['LandingLang']['btn1_txt']){
										$out.= '<li>';

										if($slide['LandingLang']['btn1_url'] == '#inscription'){
											$out.= '<a style="background-color:'.$background.';color:'.$btnColor.'" data-target="#inscription" data-toggle="modal" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}else{
											$out.= '<a style="background-color:'.$background.';color:'.$btnColor.'" href="'.$slide['LandingLang']['btn1_url'].'" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}
										$out.= $slide['LandingLang']['btn1_txt'].'</a>';

										$out.= '</li>';
									}
									if(!empty($user) && $slide['LandingLang']['btn2_url'] == '#inscription')$slide['LandingLang']['btn2_txt'] = '';
									if($slide['LandingLang']['btn2_txt']){
										$out.= '<li>';

										if($slide['LandingLang']['btn2_url'] == '#inscription'){
											$out.= '<a data-target="#inscription" data-toggle="modal" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}else{
											$out.= '<a href="'.$slide['LandingLang']['btn2_url'].'" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}
										$out.= $slide['LandingLang']['btn2_txt'].'</a>';

										$out.= '</li>';
									}
							$out.= '</ul>';
								}
						$out.= '</div>
					</div>	';
					$i++;
				}


				$out.= '</div>';


		$out.= '</div>


	</section><!--slider END-->';
			$paddingslider = 'padding: 0px 20px;';
			$dataSlide = getimagesize(Configure::read('Site.pathLandingSlide').'/'.$slides[0]['LandingLang']['slide_mobile']);
			/*if($dataSlide[1] < Configure::read('LandingSlide.height')){
				$delta = ((Configure::read('LandingSlide.height') - $dataSlide[1]) / 2);
				$padd_top = ceil(110 - $delta-5);
				$padd_bottom = ceil(30 - $delta);
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}
			if($dataSlide[1] > Configure::read('LandingSlide.height')){
				$delta = (($dataSlide[1] - Configure::read('LandingSlide.height') ) / 2);
				$padd_top = ceil($delta-20);
				$padd_bottom = ceil(30 + $delta-10);
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}*/


			$linkslide = ' style="  background: #fff url(\'/'.Configure::read('Site.pathLandingSlide').'/'.$slides[0]['LandingLang']['slide_mobile'].'\') no-repeat center center;'.$paddingslider.'"';
			$out .= '
				<section class="slider visible-xs" '.$linkslide.'>
					<div id="carousel_mobile" class="container carousel slide" >
						<div class="carousel-inner" role="listbox">';


			$i=0;
			foreach($slides as $slide){

				if($i==0)$active = 'active';else $active = '';

				if(!$slide['LandingLang']['font_type_titre_mobile']) $slide['LandingLang']['font_type_titre_mobile'] = 'H1';
				if(!$slide['LandingLang']['font_type_ligne2_mobile']) $slide['LandingLang']['font_type_ligne2_mobile'] = 'P';
				if(!$slide['LandingLang']['font_type_ligne3_mobile']) $slide['LandingLang']['font_type_ligne3_mobile'] = 'P';

				if(!$slide['LandingLang']['font_color_titre_mobile']) $slide['LandingLang']['font_color_titre_mobile'] = '#5A449B';
				if(!$slide['LandingLang']['font_color_ligne2_mobile']) $slide['LandingLang']['font_color_ligne2_mobile'] = '#5A449B';
				if(!$slide['LandingLang']['font_color_ligne3_mobile']) $slide['LandingLang']['font_color_ligne3_mobile'] = '#5A449B';

				if(!$slide['LandingLang']['font_size_titre_mobile']) $slide['LandingLang']['font_size_titre_mobile'] = '45';
				if(!$slide['LandingLang']['font_size_ligne2_mobile']) $slide['LandingLang']['font_size_ligne2_mobile'] = '18';
				if(!$slide['LandingLang']['font_size_ligne3_mobile']) $slide['LandingLang']['font_size_ligne3_mobile'] = '18';


				if($slide['LandingLang']['font_font_titre_mobile'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_titre_mobile']).'">';
				if(!$slide['LandingLang']['font_font_titre_mobile']) $slide['LandingLang']['font_font_titre_mobile'] = 'AvenirLT-Book';
				if($slide['LandingLang']['font_font_ligne2_mobile'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_ligne2_mobile']).'">';
				if(!$slide['LandingLang']['font_font_ligne2_mobile']) $slide['LandingLang']['font_font_ligne2_mobile'] = 'AvenirLT-Book';

				if($slide['LandingLang']['font_font_ligne3_mobile'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['LandingLang']['font_font_ligne3_mobile']).'">';
				if(!$slide['LandingLang']['font_font_ligne3_mobile']) $slide['LandingLang']['font_font_ligne3_mobile'] = 'AvenirLT-Book';


				if(!$slide['LandingLang']['font_align_titre_mobile']) $slide['LandingLang']['font_align_titre_mobile'] = '';
				if(!$slide['LandingLang']['font_align_ligne2_mobile']) $slide['LandingLang']['font_align_ligne2_mobile'] = '';
				if(!$slide['LandingLang']['font_align_ligne3_mobile']) $slide['LandingLang']['font_align_ligne3_mobile'] = '';

				if($slide['LandingLang']['font_type_titre_mobile'] == 'H1' && $slide['LandingLang']['titre_mobile']) $slide['LandingLang']['titre_mobile'] = '<h1 style="text-align:'.$slide['LandingLang']['font_align_titre_mobile'].';color:'.$slide['LandingLang']['font_color_titre_mobile'].';font-family:'.$slide['LandingLang']['font_font_titre_mobile'].';font-size:'.$slide['LandingLang']['font_size_titre_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;font-weight:bold;text-transform:uppercase;">'.$slide['LandingLang']['titre_mobile'].'</h1>';
				if($slide['LandingLang']['font_type_titre_mobile'] == 'H2' && $slide['LandingLang']['titre_mobile']) $slide['LandingLang']['titre_mobile'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_titre_mobile'].';color:'.$slide['LandingLang']['font_color_titre_mobile'].';font-family:'.$slide['LandingLang']['font_font_titre_mobile'].';font-size:'.$slide['LandingLang']['font_size_titre_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre_mobile'].'</h2>';
				if($slide['LandingLang']['font_type_titre_mobile'] == 'P' && $slide['LandingLang']['titre_mobile']) $slide['LandingLang']['titre_mobile'] = '<p style="text-align:'.$slide['LandingLang']['font_align_titre_mobile'].';color:'.$slide['LandingLang']['font_color_titre_mobile'].';font-family:'.$slide['LandingLang']['font_font_titre_mobile'].';font-size:'.$slide['LandingLang']['font_size_titre_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre_mobile'].'</p>';
				if($slide['LandingLang']['font_type_titre_mobile'] == 'SPAN' && $slide['LandingLang']['titre_mobile']) $slide['LandingLang']['titre_mobile'] = '<span style="text-align:'.$slide['LandingLang']['font_align_titre_mobile'].';color:'.$slide['LandingLang']['font_color_titre_mobile'].';font-family:'.$slide['LandingLang']['font_font_titre_mobile'].';font-size:'.$slide['LandingLang']['font_size_titre_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['titre_mobile'].'</span>';



				if($slide['LandingLang']['font_type_ligne2_mobile'] == 'H2' && $slide['LandingLang']['ligne2_mobile']) $slide['LandingLang']['ligne2_mobile'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_ligne2_mobile'].';color:'.$slide['LandingLang']['font_color_ligne2_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne2_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne2_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne2_mobile'].'</h2>';
				if($slide['LandingLang']['font_type_ligne2_mobile'] == 'P' && $slide['LandingLang']['ligne2_mobile']) $slide['LandingLang']['ligne2_mobile'] = '<p style="padding:4px 0;text-align:'.$slide['LandingLang']['font_align_ligne2_mobile'].';color:'.$slide['LandingLang']['font_color_ligne2_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne2_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne2_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne2_mobile'].'</p>';
				if($slide['LandingLang']['font_type_ligne2_mobile'] == 'SPAN' && $slide['LandingLang']['ligne2_mobile']) $slide['LandingLang']['ligne2_mobile'] = '<span style="text-align:'.$slide['LandingLang']['font_align_ligne2_mobile'].';color:'.$slide['LandingLang']['font_color_ligne2_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne2_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne2_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne2_mobile'].'</span>';

				if($slide['LandingLang']['font_type_ligne3_mobile'] == 'H2' && $slide['LandingLang']['ligne3_mobile']) $slide['LandingLang']['ligne3_mobile'] = '<h2 style="text-align:'.$slide['LandingLang']['font_align_ligne3_mobile'].';color:'.$slide['LandingLang']['font_color_ligne3_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne3_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne3_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne3_mobile'].'</h2>';
				if($slide['LandingLang']['font_type_ligne3_mobile'] == 'P' && $slide['LandingLang']['ligne3_mobile']) $slide['LandingLang']['ligne3_mobile'] = '<p style="padding:4px 0;text-align:'.$slide['LandingLang']['font_align_ligne3_mobile'].';color:'.$slide['LandingLang']['font_color_ligne3_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne3_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne3_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne3_mobile'].'</p>';
				if($slide['LandingLang']['font_type_ligne3_mobile'] == 'SPAN' && $slide['LandingLang']['ligne3_mobile']) $slide['LandingLang']['ligne3_mobile'] = '<span style="text-align:'.$slide['LandingLang']['font_align_ligne3_mobile'].';color:'.$slide['LandingLang']['font_color_ligne3_mobile'].';font-family:'.$slide['LandingLang']['font_font_ligne3_mobile'].';font-size:'.$slide['LandingLang']['font_size_ligne3_mobile'].'px;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne3_mobile'].'</span>';


				$countdown = '';
				if($slide['LandingLang']['date_compteur']  && $slide['LandingLang']['date_compteur'] != '0000-00-00 00:00:00'){

					$size_compteur = '20px';
					if($slide['LandingLang']['size_compteur_mobile'])$size_compteur = $slide['LandingLang']['size_compteur_mobile'];
					$color_compteur = $slide['LandingLang']['color'];
					if($slide['LandingLang']['color_compteur_mobile'])$color_compteur = $slide['LandingLang']['color_compteur_mobile'];
					$text_compteur = '';
					if($slide['LandingLang']['text_compteur'])$text_compteur = '<p style="text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'">'.$slide['LandingLang']['text_compteur'].'</p>';
					$countdown=$text_compteur.'<span class="clock_mobile" rel="'.$slide['LandingLang']['date_compteur'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'"></span>';
				}
				$url_mobile = '<a>';
				if(!empty($user)){
					if($slide['LandingLang']['url_nologged_mobile'] == '#inscription')
						$url_mobile = '<a data-target="#inscription" data-toggle="modal">';
					else
						$url_mobile = '<a href="'.$slide['LandingLang']['url_nologged_mobile'].'">';
				}else{
					$url_mobile = '<a href="'.$slide['LandingLang']['url_logged_mobile'].'">';
				}

				if(!$slide['LandingLang']['align2'])$slide['LandingLang']['align2']= '';
				if(!$slide['LandingLang']['align3'])$slide['LandingLang']['align3']= '';

				$out.= '<div class="item active">
						<div class="caro-caption2">
							'.$url_mobile.' '.$slide['LandingLang']['titre_mobile'].'
							<ul class="slider-tick-ul" style="padding-left:0px">';
								if($slide['LandingLang']['ligne2_mobile'])
								$out.= '<li class=" slideInRight animated" style="display:block;text-align:'.$slide['LandingLang']['align2'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne2_mobile'].'</li>';
								if($slide['LandingLang']['ligne3_mobile'])
								$out.= '<li class=" slideInRight animated" style="display:block;text-align:'.$slide['LandingLang']['align3'].';visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.$slide['LandingLang']['ligne3_mobile'].'</li>';
							$out.= '</ul></a>';
					if($slide['LandingLang']['btn_mobile_txt']){

						if($slide['LandingLang']['btn_mobile_url'] == '#inscription'){
							$out.= '<ul class="list-inline slider-button-group" style="text-align: center;">
							<li><a data-target="#inscription" data-toggle="modal" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;">'.$slide['LandingLang']['btn_mobile_txt'].'</a></li>

							</ul>';
						}else{
							$out.= '<ul class="list-inline slider-button-group" style="text-align: center;">
							<li><a href="'.$slide['LandingLang']['btn_mobile_url'].'" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['LandingLang']['color_btn1'].';background:'.$slide['LandingLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 2.4s; -moz-animation-delay: 2.4s; animation-delay: 2.4s;">'.$slide['LandingLang']['btn_mobile_txt'].'</a></li>

							</ul>';
						}

					}
							$out.= $countdown;
								if($slide['LandingLang']['btn1_txt'])

						$out.= '</div>
					</div>	';
					$i++;
				}


				$out.= '</div>';


		$out.= '</div>

		<!--<div class="scroll-next">
			<a class="scroll" href="#agents_list"><i class="fa fa-2x fa-chevron-down" aria-hidden="true"></i></a>
		</div>-->

	</section><!--slider END-->';


        return $out;
    }


	public function getSliderMobile(){

        App::import('Model', 'Slidemobile');

        $model = new Slidemobile();
        $dateNow = date('Y-m-d H:i:00');

        //Les slides (active, dans une période de validité, pour ce site, dans la langue actuelle)
        $slidemobiles = $model->find('all', array(
            'fields' => array('Slidemobile.*', 'SlidemobileLang.*'),
            'conditions' => array(
                'Slidemobile.active' => 1,
                'Slidemobile.validity_start <=' => $dateNow,
                'OR'    => array(
                    array('Slidemobile.validity_end >=' => $dateNow),
                    array('Slidemobile.validity_end IS NULL')
                )
            ),
            'joins' => array(
                array(
                    'table' => 'slidemobile_langs',
                    'alias' => 'SlidemobileLang',
                    'type'  => 'inner',
                    'conditions' => array(
                        'SlidemobileLang.slide_id = Slidemobile.id',
                        'SlidemobileLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'Slidemobile.position asc',
            'recursive' => -1
        ));

		$user = $this->Session->read('Auth.User');

		//Pour chaque slide est-il affecté à ce domaine
        foreach($slidemobiles as $key => $slide){
			if(!empty($slide['Slide']['domain'])){
				$domainsId = explode(',', $slide['Slide']['domain']);
				//Si le slide n'est pas affecté à ce domain
				if(!in_array($this->Session->read('Config.id_domain'), $domainsId))
					unset($slides[$key]);
			}
        }

        //Plus de slide, alors on retourne la scene par défault
        if(empty($slidemobiles))
            return false;

		$countdownmobile = '';

		//$paddingslidermobile = 'padding: 30px 0 15px 0;';
			//$dataSlide = getimagesize(Configure::read('Site.pathLandingSlide').'/'.$slidemobiles[0]['SlidemobileLang']['name']);
			/*if($dataSlide[1] < Configure::read('Slidemobile.height')){
				$delta = ((Configure::read('Slidemobile.height') - $dataSlide[1]) / 2);
				$padd_top = ceil(110 - $delta-5);
				$padd_bottom = ceil(30 - $delta);
				$paddingslidermobile = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}*/
			/*if($dataSlide[1] > Configure::read('Slidemobile.height')){
				$delta = (($dataSlide[1] - Configure::read('Slidemobile.height') ) / 2);
				$padd_top = ceil($delta-20);
				$padd_bottom = ceil(30 + $delta-10);
				$paddingslider = 'padding: '.$padd_top.'px 0 '.$padd_bottom.'px 0 !important;';
			}*/


		$linkslide = ' style="  background: #fff url(\'/'.Configure::read('Site.pathSlidemobile').'/'.$slidemobiles[0]['SlidemobileLang']['name'].'\') no-repeat center center;background-size: cover;"';

		$out = '
				<section class="slidermobile visible-xs" '.$linkslide.'>
					<div id="slidermobile" class="carousel slide" data-ride="carousel"><div class="carousel-inner" role="listbox">';
		$i=0;
		foreach($slidemobiles as $slide){
			if($i==0)$active = 'active';else $active = '';
			$out .= '<div class="item '.$active.'">
					<div class="caro-caption">';

				$size_compteur = '20px';
				if($slide['SlidemobileLang']['size_compteur'])$size_compteur = $slide['SlidemobileLang']['size_compteur'];
				if(!empty($slide['SlidemobileLang']['color']))$color_compteur = $slide['SlidemobileLang']['color'];
				if($slide['SlidemobileLang']['color_compteur'])$color_compteur = $slide['SlidemobileLang']['color_compteur'];

			$out .= '<ul class="slidermobile-tick-ul">';

				if($slide['SlidemobileLang']['font_font_mobile_1'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlidemobileLang']['font_font_mobile_1']).'">';
				if(!$slide['SlidemobileLang']['font_color_mobile_1']) $slide['SlidemobileLang']['font_color_mobile_1'] = '#fff';
				if(!$slide['SlidemobileLang']['font_size_mobile_1']) $slide['SlidemobileLang']['font_size_mobile_1'] = '18';
				if(!$slide['SlidemobileLang']['font_font_mobile_1']) $slide['SlidemobileLang']['font_font_mobile_1'] = 'AvenirLT-Book';
				if(!$slide['SlidemobileLang']['font_code_1']) $slide['SlidemobileLang']['font_code_1'] = 'p';
				if(!$slide['SlidemobileLang']['font_align_mobile_1']) $slide['SlidemobileLang']['font_align_mobile_1'] = 'center';
				if($slide['SlidemobileLang']['alt'])$out .= '<li class=" slideInRight animated" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><'.$slide['SlidemobileLang']['font_code_1'].' style="text-align:'.$slide['SlidemobileLang']['font_align_mobile_1'].';font-family:'. $slide['SlidemobileLang']['font_font_mobile_1'].';font-size:'. $slide['SlidemobileLang']['font_size_mobile_1'].'px;color:'. $slide['SlidemobileLang']['font_color_mobile_1'].';margin-top:0px;text-transform:uppercase">'.$slide['SlidemobileLang']['alt'].'</'.$slide['SlidemobileLang']['font_code_1'].'></li>';


				if($slide['SlidemobileLang']['font_font_mobile_2'] && $slide['SlidemobileLang']['font_font_mobile_2'] != $slide['SlidemobileLang']['font_font_mobile_1'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlidemobileLang']['font_font_mobile_2']).'">';
				if(!$slide['SlidemobileLang']['font_color_mobile_2']) $slide['SlidemobileLang']['font_color_mobile_2'] = '#fff';
				if(!$slide['SlidemobileLang']['font_size_mobile_2']) $slide['SlidemobileLang']['font_size_mobile_2'] = '16';
				if(!$slide['SlidemobileLang']['font_font_mobile_2']) $slide['SlidemobileLang']['font_font_mobile_2'] = 'AvenirLT-Book';
				if(!$slide['SlidemobileLang']['font_code_2']) $slide['SlidemobileLang']['font_code_2'] = 'p';
				if(!$slide['SlidemobileLang']['font_align_mobile_2']) $slide['SlidemobileLang']['font_align_mobile_2'] = 'center';
				if($slide['SlidemobileLang']['title2'])$out .= '<li class=" slideInRight animated" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><'.$slide['SlidemobileLang']['font_code_2'].' style="text-align:'.$slide['SlidemobileLang']['font_align_mobile_2'].';font-family:'. $slide['SlidemobileLang']['font_font_mobile_2'].';font-size:'. $slide['SlidemobileLang']['font_size_mobile_2'].'px;color:'. $slide['SlidemobileLang']['font_color_mobile_2'].';;text-transform:uppercase">'.$slide['SlidemobileLang']['title2'].'</'.$slide['SlidemobileLang']['font_code_2'].'></li>';

				if($slide['SlidemobileLang']['font_font_mobile_3'] && $slide['SlidemobileLang']['font_font_mobile_3'] != $slide['SlidemobileLang']['font_font_mobile_1'])echo '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family='.str_replace(' ','%20',$slide['SlidemobileLang']['font_font_mobile_3']).'">';
				if(!$slide['SlidemobileLang']['font_color_mobile_3']) $slide['SlidemobileLang']['font_color_mobile_3'] = '#fff';
				if(!$slide['SlidemobileLang']['font_size_mobile_3']) $slide['SlidemobileLang']['font_size_mobile_3'] = '16';
				if(!$slide['SlidemobileLang']['font_font_mobile_3']) $slide['SlidemobileLang']['font_font_mobile_3'] = 'AvenirLT-Book';
				if(!$slide['SlidemobileLang']['font_code_3']) $slide['SlidemobileLang']['font_code_3'] = 'p';
				if(!$slide['SlidemobileLang']['font_align_mobile_3']) $slide['SlidemobileLang']['font_align_mobile_3'] = 'center';
				if($slide['SlidemobileLang']['title3'])$out .= '<li class=" slideInRight animated" style="display:block;text-align:Center;visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><'.$slide['SlidemobileLang']['font_code_2'].' style="text-align:'.$slide['SlidemobileLang']['font_align_mobile_3'].';font-family:'. $slide['SlidemobileLang']['font_font_mobile_3'].';font-size:'. $slide['SlidemobileLang']['font_size_mobile_3'].'px;color:'. $slide['SlidemobileLang']['font_color_mobile_3'].';">'.$slide['SlidemobileLang']['title3'].'</'.$slide['SlidemobileLang']['font_code_2'].'></li>';

			$out .= '</ul>';

			//$out .= '<a href="'.$slide['SlidemobileLang']['link'].'"><img alt="'.$slide['SlidemobileLang']['alt'].'" class="img-responsive" src="/'.Configure::read('Site.pathSlidemobile').'/'.$slide['SlidemobileLang']['name'].'/" /></a>';
			if($slide['SlidemobileLang']['date_compteur']  && $slide['SlidemobileLang']['date_compteur'] != '0000-00-00 00:00:00' && $slide['SlidemobileLang']['date_compteur'] > date('Y-m-d H:i:s')){


				$text_compteur = '';
				if($slide['SlidemobileLang']['text_compteur'])$text_compteur = '<p style="margin-bottom:-5px;text-align:center;font-size:'. $size_compteur.'px;color:'. $color_compteur.'">'.$slide['SlidemobileLang']['text_compteur'].'</p>';
				$countdownmobile =$text_compteur.'<span class="clock_mobile" rel="'.$slide['SlidemobileLang']['date_compteur'].'" style="display:block;margin-bottom:10px;text-align:center;font-size:'. $slide['SlidemobileLang']['size_compteur'].'px;color:'. $color_compteur.'"></span>';
				$out .= $countdownmobile;
			}

			if($slide['SlidemobileLang']['titre_btn1']){

				if(!isset($slide['SlidemobileLang']['color_btn1'])) $slide['SlidemobileLang']['color_btn1'] = '#fff';
				if(!isset($slide['SlidemobileLang']['back_btn1'])) $slide['SlidemobileLang']['back_btn1'] = 'rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(195, 107, 194, 1) 0%, rgba(186, 98, 185, 1) 25%, rgba(154, 66, 153, 1) 100%, rgba(148, 60, 147, 1) 100%) repeat scroll 0 0';
				$out.= '<ul class="list-inline slider-button-group">';
					$out.= '<li style="padding-right:0px;">';
					if($slide['SlidemobileLang']['link_btn1'] == '#inscription'){
											$out.= '<a data-target="#inscription" data-toggle="modal" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['SlidemobileLang']['color_btn1'].';background:'.$slide['SlidemobileLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}else{
											$out.= '<a href="'.$slide['SlidemobileLang']['link_btn1'].'" class="btn btn-pink btn-slider  fadeInUp animated" style="color:'.$slide['SlidemobileLang']['color_btn1'].';background:'.$slide['SlidemobileLang']['back_btn1'].';visibility: visible;-webkit-animation-delay: 1.0s; -moz-animation-delay: 1.0s; animation-delay: 1.0s;">';
										}
					$out.= $slide['SlidemobileLang']['titre_btn1'].'</a>';
					$out.= '</li>';
				$out.= '</ul>';
			}
			$out .= '</div></div>';
			$i++;
		}


		$out .= '	</div>
				</section>
				';

		//ne pas retourner ce slide sur ces pages type ( landing)
		$params = $this->request->params;
		$type_page_forbiden = array('landings', 'pages');
		if(!in_array($params['controller'],$type_page_forbiden ))
        return $out;
		else return '';
    }


    public function getAgentStatusMenu($agent_status){

		$disabled = ' s-occupe ';
		$donttouch = '';
		if($this->request->isMobile()){
			$disabled = ' s-occupe2 ';
			$donttouch = 'donttouch';
		}
        $out = '<div class="vous-etes mb10"><div class="widget agent_statusmenu" timer="'. Configure::read('Site.timerAgentMenuStatus') .'" an="'. $this->Session->read('Auth.User.agent_number') .'" urlChangeStatus="'. $this->Html->url(array('controller' => 'agents', 'action' => 'changeAgentStatus'),true) .'" urlGetStatus="'.$this->Html->url(array('controller' => 'agents', 'action' => 'getAgentStatus'),true).'">'.
            '<div class="widget-title text-center">'.__('Vous êtes :').' </div><ul class="list-group mt20 ml10 mr10">'.
            '<li rel="available" class="list-group-item s-disponsible available'. ($agent_status == 'available'?' active':'') .'"><i class="glyphicon glyphicon-ok"></i> '. __('Disponible') .'</li>'.
            '<li rel="busy"  class="list-group-item '.$disabled.' busy '. ($agent_status == 'busy'? ' active ' :'') .' '.$donttouch .'"><i class="glyphicon glyphicon-warning-sign"></i> '. __('Occupé').'</li>'.
            '<li rel="unavailable" class="list-group-item s-indisponible unavailable'. ($agent_status == 'unavailable'?' active':'') .'"><i class="glyphicon glyphicon-remove"></i> '. __('Indisponible') .'</li>';

        $out.='</ul>';
        $out.='</div></div>';

        return $out;
    }

    public function getAgentOptions(){
        $user = $this->Session->read('Auth.User');

        App::import("Model", "User");
        $model = new User();

        $user = $model->find('first', array(
            'fields'        => array('User.*'),
            'conditions'    => array('User.id' => $user['id']),
            'recursive'     => -1
        ));



		$out = '<div class="consultation-par mb10">';
			$out .= '<div class="widget">';
				$out .= '<div class="widget-title text-center">'.__('Je suis disponible par :').'</div>';
				$out .= '<div class="row mt20 mb0 ml10 mr10">';
				$out.= $this->Form->create('Agent', array('action' => 'edit_options', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'id' => 'login_modal',
                                                 'inputDefaults' => array(
                                                     'class' => 'form-control'
                                                 )));

						$alertemail = "";
						$tooltipaletmail = "";
						if($user['User']['consult_email'] < 0 && $user['User']['consult_phone'] < 1 && $user['User']['consult_chat'] < 1){
							$alertemail = "En raison de votre présence quasi uniquement par Email, vous ne pourrez désormais activer celui-ci que si vos modes 'téléphone et tchat' sont actifs. Votre mode email pourra être débloqué et utilisé seul, après échange avec un administrateur Spiriteo.";

							$tooltipaletmail = 'data-toggle="tooltip" data-placement="top" data-original-title="'.$alertemail.'"';
						}

						$out .= '<div class="list-group text-center mb10" data-toggle="items">';
							$out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_email'] == 1 ?'checked="checked"':'').' '. (($user['User']['consult_email'] == -1 )?'disabled="disabled" readonly="readonly" class="icon_alert-factured"':'') .' value="0" id="AgentConsult0" style="display:none">';
							$out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_email'] == 1 ?' active ':'').' '. (($user['User']['consult_email'] == -1 )?' disabled':'') .'"  '.($alertemail ? $tooltipaletmail :'').'><span class="small-icon email-icon"></span> <p class="block">Email</p></a>';
        					$out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_phone'] == 1 ?'checked="checked"':'').' '. (($user['User']['consult_phone'] == -1)?'disabled="disabled" readonly="readonly"':'') .' value="1" id="AgentConsult1" style="display:none">';
							$out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_phone'] == 1 ?' active ':'').' '. (($user['User']['consult_phone'] == -1 )?' disabled':'') .'"><span class="small-icon phone-icon"></span> <p class="block">Tel</p></a>';
        					$out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_chat'] == 1 ?'checked="checked"':'').' '. (($user['User']['consult_chat'] == -1)?'disabled="disabled" readonly="readonly"':'') .' value="2" id="AgentConsult2" style="display:none">';

							//if($this->request->isMobile()){
							//	if($user['User']['consult_chat'] == 1)
							//		$out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block active "><span class="small-icon chat-icon"></span> <p class="block">Tchat</p></a>';
							//	else
							//		$out .= '<a data-toggle="tooltip" data-placement="top" title="" alt="" data-original-title="Le tchat ne peut être activé sur mobile et tablette" class="col-md-4 col-sm-4 col-xs-4 list-group-item-block  disabled"><span class="small-icon chat-icon"></span> <p class="block"  >Tchat</p></a>';
							//}else{
								$out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_chat'] == 1 ?' active ':'').' '. (($user['User']['consult_chat'] == -1 )?' disabled':'') .'"><span class="small-icon chat-icon"></span> <p class="block">Tchat</p></a>';
							//}
						$out .= '</div>';
						$out .= '<p class="small text-center">Activer ou désactiver le mode souhaité en cliquant sur l\'icône concerné.</p>';
        $out = '<div class="consultation-par mb10">';
        $out .= '<div class="widget">';
        $out .= '<div class="widget-title text-center">'.__('Je suis disponible par :').'</div>';
        $out .= '<div class="row mt20 mb0 ml10 mr10">';
        $out.= $this->Form->create('Agent', array('action' => 'edit_options', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'id' => 'login_modal',
            'inputDefaults' => array(
                'class' => 'form-control'
            )));

        $alertemail = "";
        $tooltipaletmail = "";
        if($user['User']['consult_email'] < 0 && $user['User']['consult_phone'] < 1 && $user['User']['consult_chat'] < 1){
            $alertemail = "En raison de votre présence quasi uniquement par Email, vous ne pourrez désormais activer celui-ci que si vos modes 'téléphone et tchat' sont actifs. Votre mode email pourra être débloqué et utilisé seul, après échange avec un administrateur Spiriteo.";

            $tooltipaletmail = 'data-toggle="tooltip" data-placement="top" data-original-title="'.$alertemail.'"';
        }

        $out .= '<div class="list-group text-center mb10" data-toggle="items">';
        $out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_email'] == 1 ?' checked="checked"':'').' '. (($user['User']['consult_email'] == -1 )?'disabled="disabled" readonly="readonly" class="icon_alert-factured"':'') .' value="0" id="AgentConsult0" style="display:none">';
        $out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_email'] == 1 ?' active ':'').' '. (($user['User']['consult_email'] == -1 )?' disabled':'') .'"  '.($alertemail ? $tooltipaletmail :'').'><span class="small-icon email-icon"></span> <p class="block">Email</p></a>';
        $out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_phone'] == 1 ?' checked="checked"':'').' '. (($user['User']['consult_phone'] == -1)?'disabled="disabled" readonly="readonly"':'') .' value="1" id="AgentConsult1" style="display:none">';
        $out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_phone'] == 1 ?' active ':'').' '. (($user['User']['consult_phone'] == -1 )?' disabled':'') .'"><span class="small-icon phone-icon"></span> <p class="block">Tel</p></a>';
        $out.= '<input type="checkbox" name="data[Agent][consult][]"'.($user['User']['consult_chat'] == 1 ?' checked="checked"':'').' '. (($user['User']['consult_chat'] == -1)?'disabled="disabled" readonly="readonly"':'') .' value="2" id="AgentConsult2" style="display:none">';

        //if($this->request->isMobile()){
        //	if($user['User']['consult_chat'] == 1)
        //		$out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block active "><span class="small-icon chat-icon"></span> <p class="block">Tchat</p></a>';
        //	else
        //		$out .= '<a data-toggle="tooltip" data-placement="top" title="" alt="" data-original-title="Le tchat ne peut être activé sur mobile et tablette" class="col-md-4 col-sm-4 col-xs-4 list-group-item-block  disabled"><span class="small-icon chat-icon"></span> <p class="block"  >Tchat</p></a>';
        //}else{
        $out .= '<a class="col-md-4 col-sm-4 col-xs-4 list-group-item-block '.($user['User']['consult_chat'] == 1 ?' active ':'').' '. (($user['User']['consult_chat'] == -1 )?' disabled':'') .'"><span class="small-icon chat-icon"></span> <p class="block">Tchat</p></a>';
        //}
        $out .= '</div>';
        $out .= '<p class="small text-center">Activer ou désactiver le mode souhaité en cliquant sur l\'icône concerné.</p>';

        		$out.= $this->Form->end();
				$out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="consultation-par mb10">';
        $out .= '<div class="widget">';
        $out .= '<div class="widget-title text-center">'.__('Transferts Appels vers :').'</div>';
        $out .= '<div class="row mt20 mb0 ml10 mr10">';
        $out.= $this->Form->create('Agent', array('action' => 'edit_options_num', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'id' => 'modal_num',
            'inputDefaults' => array(
                'class' => 'form-control'
            )));

        $out .= '<div class="list-group text-center mb10" data-toggle="items">';

        $out.= '<input class="input_num" type="checkbox" value="'.$user['User']['phone_number'].'" name="data[Agent][phone_number_to_use][]"'.($user['User']['phone_number'] == $user['User']['phone_api_use'] ?' checked="checked"':'').' '. (($user['User']['phone_number'] == $user['User']['phone_api_use'])?'':'') .'  id="AgentNumUsed1" style="display:none">';
        $out .= '<a  id_input="AgentNumUsed1" class="col-md-12 col-sm-12 col-xs-12 list-group-item-block AgentNumUsed1'.($user['User']['phone_number'] == $user['User']['phone_api_use'] ?' active ':'').' '. ((empty($user['User']['phone_number'])  )?' disabled':'') .'"><span class="small-icon phone-icon" style="float:left"></span> <p class="block" style="margin-top:10px">'.__('Tel-fixe 1').' : +'.$user['User']['phone_number'].' </p></a>';
		if($user['User']['phone_number2']){
			$out.= '<input class="input_num"  type="checkbox" value="'.$user['User']['phone_number2'].'" name="data[Agent][phone_number_to_use][]"'.($user['User']['phone_number2'] == $user['User']['phone_api_use'] ?' checked="checked"':'').' '. (($user['User']['phone_number2'] != $user['User']['phone_api_use'])?'':'') .'  id="AgentNumUsed2" style="display:none">';
			$out .= '<a id_input="AgentNumUsed2" class="col-md-12 col-sm-12 col-xs-12 list-group-item-block AgentNumUsed2'.($user['User']['phone_number2'] == $user['User']['phone_api_use'] ?' active ':'').' '. ((empty($user['User']['phone_number2']) )?' disabled':'') .'"><span class="small-icon phone-icon" style="float:left"></span> <p class="block" style="margin-top:10px">'.__('Tel-fixe 2').' : +'.$user['User']['phone_number2'].'</p></a>';
		}
		if($user['User']['phone_mobile']){
			
			$surcout = '0.10';
			App::import("Model", "CostPhone");
			$costPhone = new CostPhone();

			$indicatifs = $costPhone->find("all",array(
				'conditions'  =>  array(),
			));
			foreach($indicatifs as $indicatif){
					if(substr($user['User']['phone_mobile'],0,strlen($indicatif['CostPhone']['indicatif'])) == $indicatif['CostPhone']['indicatif'])
						$surcout = $indicatif['CostPhone']['cost'];
				}
			
			
			$out.= '<input class="input_num"  type="checkbox" value="'.$user['User']['phone_mobile'].'" name="data[Agent][phone_number_to_use][]"'.($user['User']['phone_mobile'] == $user['User']['phone_api_use'] ?' checked="checked"':'').' '. (($user['User']['phone_mobile'] != $user['User']['phone_api_use'])?'':'') .'  id="AgentNumUsed3" style="display:none">';
			$out .= '<a id_input="AgentNumUsed3" class="col-md-12 col-sm-12 col-xs-12 list-group-item-block '.($user['User']['phone_mobile'] == $user['User']['phone_api_use'] ?' active ':'').' '. ((empty($user['User']['phone_mobile'])  )?' disabled':'') .'"><span class="small-icon phone-icon" style="float:left"></span> <p class="block" style="margin-top:10px">'.__('Mobile').' : +'.$user['User']['phone_mobile'].' &nbsp;<i class="glyphicon glyphicon-warning-sign icon_alert-factured" style="cursor:pointer" data-toggle="tooltip" data-original-title="'.__('Surcoût vers mobile -'.$surcout.'€/min sur les communications par téléphone uniquement et à la charge de l\'expert.').'" ></i> </p></a> ';
			
		}

        $out .= '</div>';
        $out .= '<p class="small text-center">'.__('Sélectionner le numéro souhaité pour recevoir vos appels').'</p>';
			$out .= '</div>';
		$out .= '</div>';
        $out .= '</div>';
        return $out;
    }

    public function hasAppointment($appointments, $date, $h, $m){
        if(isset($appointments[$date])){
            foreach($appointments[$date] as $horaire){
                if($h == $horaire['H'] && $m == $horaire['Min'])
                    return true;
            }
        }
        return false;
    }

    public function appointmentOf($appointments, $idUser, $date, $h, $m){
        if(isset($appointments[$date]) && $idUser != 0){
            foreach($appointments[$date] as $horaire){
                if($h == $horaire['H'] && $m == $horaire['Min'] && $idUser == $horaire['user_id'])
                    return true;
            }
        }
        return false;
    }

    public function badgeSidebar($action, $param = array()){
        if(empty($action))
            return '';

        switch ($action){
            case 'mails' :
                $count = $this->countNewMessage($param);
                break;
			case 'messages' :
                $count = $this->countAllNewMessage();
                break;
            case 'appointments' :
                $count = $this->countRDV();
                break;
			case 'perdus' :
                $count = $this->countLOST('all');
                break;
			case 'perdus_call' :
                $count = $this->countLOST('call');
                break;
			case 'perdus_chat' :
                $count = $this->countLOST('chat');
                break;
			case 'perdus_email' :
                $count = $this->countLOST('email');
                break;
            default :
                return '';
        }

        //Si le compteur est positif
        if($count > 0)
            return ' <span class="label label-primary label-email">'. $count .'</span>';
        else
            return '';
    }

	public function countLOST($why){


        App::import("Model", "UserPenality");
        $model = new UserPenality();

        $countCall = $model->find('count', array(
            'conditions' => array(
				'is_view' => 0,
				'callinfo_id !=' => NULL,
				'user_id' => $this->Session->read('Auth.User.id')
											   ),
            'recursive' => -1
        ));

		$countChat = $model->find('count', array(
            'conditions' => array(
				'is_view' => 0,
				'tchat_id !=' => NULL,
				'user_id' => $this->Session->read('Auth.User.id')
											   ),
            'recursive' => -1
        ));

		$countEmail = $model->find('count', array(
            'conditions' => array(
				'is_view' => 0,
				'message_id !=' => NULL,
				'user_id' => $this->Session->read('Auth.User.id')
											   ),
            'recursive' => -1
        ));


		switch ($why) {
			case 'all':
				return $countCall+ $countChat+ $countEmail;
				break;
			case 'chat':
				return $countChat;
				break;
			case 'call':
				return $countCall;
				break;
			case 'email':
				return $countEmail;
				break;
		}

        return '';
    }

    public function countRDV(){
        //Date d'aujourd'hui explosé
        $dateNow = CakeTime::format('now', '%d-%m-%Y');
        $dateNow = Tools::explodeDate($dateNow);
        $dateEnd = CakeTime::format(strtotime('+'.(Configure::read('Site.limitPlanning')-1).' days'), '%d-%m-%Y');
        $dateEnd = Tools::explodeDate($dateEnd);

        //On importe le model
        App::import("Model", "CustomerAppointment");
        $model = new CustomerAppointment();

        //Le nombre de RDV dans cette période
        $countRDV = $model->find('count', array(
            'conditions' => $model->getConditionsValid($this->Session->read('Auth.User.id'), $dateNow, $dateEnd),
            'recursive' => -1
        ));


        return $countRDV;
    }

    public function countNewMessage($param = array()){
        //On importe le model
        App::import("Model", "Message");
        $model = new Message();

        if(isset($param['private']))
            $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 1);
        else
            $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 0);

        //On compte les messages non lu
        $countMsg = $model->find('count', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));

		//mail perimé
		$countMsg2 = 0;
		if(!isset($param['private'])){
		$conditions = array('Message.from_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 3, 'Message.archive' => 0, 'Message.private' => 0);

        //On compte les messages non lu
        $countMsg2 = $model->find('count', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
		}
		return $countMsg + $countMsg2;
    }

	public function countAllNewMessage(){
        //On importe le model
        App::import("Model", "Message");
        $model = new Message();

         $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 1);

	    //On compte les messages non lu
        $countMsg = $model->find('count', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));
          $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 0);

        //On compte les messages non lu
        $countMsg2 = $model->find('count', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));

		//mail perimé
		$conditions = array('Message.from_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 3, 'Message.archive' => 0, 'Message.private' => 0);

        //On compte les messages non lu
        $countMsg3 = $model->find('count', array(
            'conditions' => $conditions,
            'recursive' => -1
        ));


        return $countMsg + $countMsg2 + $countMsg3;
    }

	public function getAccountSubmenu()
    {
		$html = '<div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-account-monCompte">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'profil')).'">
												'.__('Mon Compte').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'history')).'">
												'.__('Mes consultations').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-parrainage">
											<a  href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'client_gain', )).'">
												'.__('Mes parrainages').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-messages">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'mails')).'">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-experts">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'favorites')).'">
												'.__('Mes experts préférés').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'appointments')).'">
												'.__('Mes rdv experts').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-paiements">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'payments')).'">
												'.__('Mes paiements').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-gain">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'loyalty')).'">
												'.__('Mes gains fidélité').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-account-deconnection">
											<a class="accordion-toggle" href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div>';
			return $html;

	}

	public function getAccountSubmenuMobile()
    {
		$html = '<div  id="accordion-m" class="panel-group"><div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-account-monCompte">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'profil')).'">
												'.__('Mon Compte').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'history')).'">
												'.__('Mes consultations').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-parrainage">
											<a  href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'client_gain', )).'">
												'.__('Mes parrainages').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-messages">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'mails')).'">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-experts">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'favorites')).'">
												'.__('Mes experts préférés').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'appointments')).'">
												'.__('Mes rdv experts').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-paiements">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'payments')).'">
												'.__('Mes paiements').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-gain">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'loyalty')).'">
												'.__('Mes gains fidélité').'
											</a>
										</div>
									</div>
								</div>

								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-deconnection">
											<a class="accordion-toggle" href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div></div>';
			return $html;

	}

	public function getAccountSubmenuRight()
    {
		$html = '<div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-account-monCompte">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'profil')).'">
												'.__('Mon Compte').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'history')).'">
												'.__('Mes consultations').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-parrainage">
											<a  href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'client_gain', )).'">
												'.__('Mes parrainages').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-messages">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'mails')).'">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-experts">
											<a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'favorites')).'">
												'.__('Mes experts préférés').'
											</a>
										</div>
									</div>
								</div>
									<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-historique">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'appointments')).'">
												'.__('Mes rdv experts').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-paiements">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'payments')).'">
												'.__('Mes paiements').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-account-gain">
											<a  href="'.$this->Html->url(array('controller' => 'accounts','action' => 'loyalty')).'">
												'.__('Mes gains fidélité').'
											</a>
										</div>
									</div>
								</div>
								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-account-deconnection">
											<a href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div>';
			return $html;

	}

    public function getAccountSidebar($returnOnlyLinks=false)
    {
        $items = array();
        /*$items[] = array('action' => 'index', 'text' => __('Mon compte'), 'icon' => 'home');
        $items[] = array('action' => 'profil', 'text' => __('Mon profil'), 'icon' => 'user');
        $items[] = array('action' => 'history', 'text' => __('Mes communications'), 'icon' => 'list', 'parent' => 1, 'label' => __('Historique'));
        $items[] = array('action' => 'mails', 'text' => __('Ma messagerie'), 'icon' => 'envelope', 'child' => 1);
        $items[] = array('action' => 'buycredits', 'text' => __('Acheter des minutes'), 'icon' => 'shopping-cart');
        $items[] = array('action' => 'payments', 'text' => __('Mes paiements'), 'icon' => 'list-alt');
        $items[] = array('action' => 'favorites', 'text' => __('Mes experts favoris'), 'icon' => 'star');
        $items[] = array('action' => 'review', 'text' => __('Votre avis sur un expert'), 'icon' => 'comment');
        $items[] = array('action' => 'chat_history', 'text' => __('Mes chats'), 'icon' => 'comment', 'child' => 1);
        $items[] = array('action' => 'limits', 'text' => __('Mes limites'), 'icon' => 'bell');*/

        $items[] = array('action' => 'index', 'text' => __('Mes informations'), 'icon' => 'home', 'parent' => 2, 'label' => __('Mon compte'));
        $items[] = array('action' => 'profil', 'text' => __('Mon profil'), 'icon' => 'user', 'child' => 2);
        $items[] = array('action' => 'history', 'text' => __('Mes communications'), 'icon' => 'list', 'parent' => 1, 'label' => __('Historique des communications'), 'nosidebar' => true);
        $items[] = array('action' => 'history', '?' => array('media' => 'phone'), 'text' => __('Par téléphone'), 'icon' => 'earphone', 'child' => 1, 'no_select' => true);
        $items[] = array('action' => 'history', '?' => array('media' => 'email'), 'text' => __('Par écrit'), 'icon' => 'pencil', 'child' => 1, 'no_select' => true);
        $items[] = array('action' => 'history', '?' => array('media' => 'chat'), 'text' => __('Par chat'), 'icon' => 'comment', 'child' => 1, 'no_select' => true);
        $items[] = array('action' => 'mails', 'text' => __('Mes mails'), 'icon' => 'envelope', 'parent' => 5, 'label' => __('Mes consultations écrites'));
        $items[] = array('action' => 'chat_history', 'text' => __('Mes chats'), 'icon' => 'comment', 'child' => 5);
        $items[] = array('action' => 'mails', '?' => array('private' => true), 'text' => __('Messages privés'), 'icon' => 'envelope', 'child' => 5);
        $items[] = array('action' => 'favorites', 'text' => __('Mes experts préférés'), 'icon' => 'star', 'parent' => 4, 'label' => __('Mes experts favoris'));
        $items[] = array('action' => 'review', 'text' => __('Votre avis sur un expert'), 'icon' => 'comment', 'child' => 4);
		$items[] = array('action' => 'appointments', 'text' => __('RDV Expert'), 'icon' => 'comment', 'child' => 4);
        $items[] = array('action' => 'payments', 'text' => __('Mes paiements'), 'icon' => 'list-alt', 'parent' => 3, 'label' => __('Mes paiements'));
        $items[] = array('action' => 'buycredits', 'text' => __('Acheter des minutes'), 'icon' => 'shopping-cart', 'child' => 3);
        $items[] = array('action' => 'limits', 'text' => __('Mes limites'), 'icon' => 'bell', 'child' => 3);

        if ($returnOnlyLinks){
            $out = array();
            foreach ($items AS $item){
                if(isset($item['no_select']))
                    continue;


                $selected = false;
                if ($this->params['action'] == $item['action']){
                    if (isset($item['?']) && !empty($item['?'])){
                        if ($this->params->query == $item['?'])
                            $selected = true;
                        else $selected = false;
                    }else{
                        $selected = true;
                    }
                }

                $out[] = array('url'  => $this->Html->url(array('controller' => 'accounts','action' => $item['action'], '?' => isset($item['?'])?$item['?']:'')),
                               'icon' => $item['icon'],
                               'text' => $item['text'],
                               'selected' => $selected
                );
            }
        }else{
            $out = '<ul class="sidebar_menu hidden-xs">';

            //1er passage dans le tableau pour construire le tableau des enfants
            $itemsChildren = array();
            foreach($items as $item){
                if(isset($item['child'])){
                    if(isset($itemsChildren[$item['child']]))
                        $itemsChildren[$item['child']][] = $item;
                    else{
                        $itemsChildren[$item['child']] = array();
                        $itemsChildren[$item['child']][] = $item;
                    }
                }
            }

            foreach ($items as $k => $item){
                if(!isset($item['parent']) && !isset($item['child'])){
                    $out.= '<li'. $this->getLiClass($item) .'>'.
                        '<i class="glyphicon glyphicon-'. $item['icon'] .'"></i>'.
                        $this->Html->link($item['text'], array('controller' => 'accounts','action' => $item['action'], '?' => (isset($item['?']) ?$item['?']:false))). $this->badgeSidebar($item['action'], (isset($item['?']) ?$item['?']:array())) .'</li>';
                }
                elseif(isset($item['parent'])){

                    $out.= '<li class="label-parent"><span class="txt-bold">'.$item['label'].'</span>';
                    $out.= '<ul>';
                    if (!isset($item['nosidebar']) || !$item['nosidebar']){
                    $out.= '<li'. $this->getLiClass($item) .'>'.
                        '<i class="glyphicon glyphicon-'. $item['icon'] .'"></i>'.
                        $this->Html->link($item['text'], array('controller' => 'accounts','action' => $item['action'], '?' => (isset($item['?']) ?$item['?']:false))). $this->badgeSidebar($item['action'], (isset($item['?']) ?$item['?']:array())).'</li>';
                    }
                    if(isset($itemsChildren[$item['parent']]) && !empty($itemsChildren[$item['parent']])){
                        //Pour chaque enfant
                        foreach($itemsChildren[$item['parent']] as $child){

                            $out.= '<li'. $this->getLiClass($child) .'>'.
                                '<i class="glyphicon glyphicon-'. $child['icon'] .'"></i>'.
                                $this->Html->link($child['text'], array('controller' => 'accounts','action' => $child['action'], '?' => (isset($child['?']) ?$child['?']:false))). $this->badgeSidebar($child['action'], (isset($child['?']) ?$child['?']:array())) .'</li>';
                        }
                    }
                    $out.= '</ul>';
                    $out.= '</li>';
                }
            }

            $out.= '<li><i class="glyphicon glyphicon-off"></i>'.$this->Html->link(__('Déconnexion'), array('controller' => 'users','action' => 'logout')).'</li>';
            $out.= '</ul>';
        }
        return $out;
    }

	public function getAgentSubmenu()
    {
		$html = '<div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-monCompte">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#monCompte">
												'.__('Mon Compte').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="monCompte" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'index')).'">'.__('Mes informations').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'profil')).'">'.__('Mon profil').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'presentations')).'">'.__('Ma présentation').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'bonus')).'">'.__('Bonus / Rémunération').'</a></li>
										</ul>

									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-historique">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#historique">
												'.__('Historique'). $this->badgeSidebar('perdus', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="historique" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history')).'">'.__('Mes communications').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'phone'))).'">'.__('Par téléphone').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'email'))).'">'.__('Par écrit').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'chat'))).'">'.__('Par chat').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostcall')).'">'.__('Mes appels perdus'). $this->badgeSidebar('perdus_call', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostchat')).'">'.__('Mes chats perdus'). $this->badgeSidebar('perdus_chat', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostemail')).'">'.__('Mes emails perdus'). $this->badgeSidebar('perdus_email', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'notations')).'">'.__('Mes notes').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'reviews')).'">'.__('Mes avis').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'order')).'">'.__('Ma facturation').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-parrainage">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#parrainage">
												'.__('Parrainages').'<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="parrainage" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent', )).'">'.__('Parrainer').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent_gain', )).'">'.__('Mes gains').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-consultations">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#consultations">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="consultations" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails')).'">'.__('Consultations mail'). $this->badgeSidebar('mails', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails', '?' => array('private' => true))).'">'.__('Messages privés'). $this->badgeSidebar('mails', array('private' => true)) .'</a></li>
											<li class="hidden-xs"><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails_relance', '?' => array('private' => true))).'">'.__('Relances par messages'). $this->badgeSidebar('mails_relance', array('private' => true)) .'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-paiements">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#paiements">
												'.__('Mon calendrier'). $this->badgeSidebar('appointments', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="paiements" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'planning')).'">'.__('Mon planning').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'appointments')).'">'.__('Mes RDV'). $this->badgeSidebar('appointments', array()) .'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-comments">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#comments">
												'.__('Comment ça marche ?').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="comments" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_communication')).'">'.__('Recommandations & infos').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_general')).'">'.__('Mode d\'emploi').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'cgu')).'">'.__('CGU & Code déontologie').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'gain')).'">'.__('Gains sur Chiffre d\'affaires Expert').'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-deconnection">
											<a class="accordion-toggle" href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div>';
			return $html;
	}

	public function getAgentSubmenuMobile()
    {
		$html = '<div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-monCompte">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-m" href="#monCompte-m">
												'.__('Mon Compte').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="monCompte-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'index')).'">'.__('Mes informations').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'profil')).'">'.__('Mon profil').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'presentations')).'">'.__('Ma présentation').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'bonus')).'">'.__('Bonus / Rémunération').'</a></li>
										</ul>

									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-historique">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-m" href="#historique-m">
												'.__('Historique'). $this->badgeSidebar('perdus', array()).'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="historique-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history')).'">'.__('Mes communications').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'phone'))).'">'.__('Par téléphone').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'email'))).'">'.__('Par écrit').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'chat'))).'">'.__('Par chat').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostcall')).'">'.__('Mes appels perdus'). $this->badgeSidebar('perdus_call', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostchat')).'">'.__('Mes chats perdus'). $this->badgeSidebar('perdus_chat', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostemail')).'">'.__('Mes emails perdus'). $this->badgeSidebar('perdus_email', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'notations')).'">'.__('Mes notes').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'reviews')).'">'.__('Mes avis').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'order')).'">'.__('Ma facturation').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-parrainage">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#parrainage-m">
												'.__('Parrainages').'<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="parrainage-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent', )).'">'.__('Parrainer').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent_gain', )).'">'.__('Mes gains').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-consultations">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-m" href="#consultations-m">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="consultations-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails')).'">'.__('Consultations mail'). $this->badgeSidebar('mails', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails', '?' => array('private' => true))).'">'.__('Messages privés'). $this->badgeSidebar('mails', array('private' => true)) .'</a></li>
											<li class="hidden-xs"><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails_relance', '?' => array('private' => true))).'">'.__('Relances par messages'). $this->badgeSidebar('mails_relance', array('private' => true)) .'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-paiements">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-m" href="#paiements-m">
												'.__('Mon calendrier'). $this->badgeSidebar('appointments', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="paiements-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'planning')).'">'.__('Mon planning').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'appointments')).'">'.__('Mes RDV'). $this->badgeSidebar('appointments', array()) .'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-comments">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-m" href="#comments-m">
												'.__('Comment ça marche ?').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="comments-m" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_communication')).'">'.__('Recommandations & infos').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_general')).'">'.__('Mode d\'emploi').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'cgu')).'">'.__('CGU & Code déontologie').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'gain')).'">'.__('Gains sur Chiffre d\'affaires Expert').'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-deconnection">
											<a class="accordion-toggle" href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div>';
			return $html;
	}

	public function getAgentSubmenuRight()
    {
		$html = '<div class="panel panel-default">
									<div class="panel-heading no-border">
										<div class="panel-title title-monCompte">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-r" href="#monCompte-r">
												'.__('Mon Compte').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="monCompte-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'index')).'">'.__('Mes informations').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'profil')).'">'.__('Mon profil').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'presentations')).'">'.__('Ma présentation').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'bonus')).'">'.__('Bonus / Rémunération').'</a></li>
										</ul>

									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-historique">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-r" href="#historique-r">
												'.__('Historique'). $this->badgeSidebar('perdus', array()).'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="historique-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history')).'">'.__('Mes communications') .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'phone'))).'">'.__('Par téléphone').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'email'))).'">'.__('Par écrit').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'history', '?' => array('media' => 'chat'))).'">'.__('Par chat').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostcall')).'">'.__('Mes appels perdus'). $this->badgeSidebar('perdus_call', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostchat')).'">'.__('Mes chats perdus'). $this->badgeSidebar('perdus_chat', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'historylostemail')).'">'.__('Mes emails perdus'). $this->badgeSidebar('perdus_email', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'notations')).'">'.__('Mes notes').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'reviews')).'">'.__('Mes avis').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'order')).'">'.__('Ma facturation').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-parrainage">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#parrainage-r">
												'.__('Parrainages').'<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="parrainage-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent', )).'">'.__('Parrainer').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent_gain', )).'">'.__('Mes gains').'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-consultations">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-r" href="#consultations-r">
												'.__('Ma messagerie'). $this->badgeSidebar('messages', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="consultations-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails')).'">'.__('Consultations mail'). $this->badgeSidebar('mails', array()) .'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails', '?' => array('private' => true))).'">'.__('Messages privés'). $this->badgeSidebar('mails', array('private' => true)) .'</a></li>
											<li class="hidden-xs"><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'mails_relance', '?' => array('private' => true))).'">'.__('Relances par messages'). $this->badgeSidebar('mails_relance', array('private' => true)) .'</a></li>
										</ul>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-paiements">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-r" href="#paiements-r">
												'.__('Mon calendrier'). $this->badgeSidebar('appointments', array()) .'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="paiements-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'planning')).'">'.__('Mon planning').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'appointments')).'">'.__('Mes RDV'). $this->badgeSidebar('appointments', array()) .'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="panel-title title-comments">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-r" href="#comments-r">
												'.__('Comment ça marche ?').'
												<i class="fa fa-sort-asc" aria-hidden="true"></i>
											</a>
										</div>
									</div>
									<div id="comments-r" class="panel-collapse collapse">
										<ul>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_communication')).'">'.__('Recommandations & infos').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'comment_general')).'">'.__('Mode d\'emploi').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'cgu')).'">'.__('CGU & Code déontologie').'</a></li>
											<li><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'gain')).'">'.__('Gains sur Chiffre d\'affaires Expert').'</a></li>
										</ul>
									</div>
								</div>

								<div class="panel panel-default panel-deco">
									<div class="panel-heading">
										<div class="panel-title title-deconnection">
											<a class="accordion-toggle" href="'. $this->Html->url(array('controller' => 'users', 'action' => 'logout')) .'">
												'.__('Déconnexion').'
											</a>
										</div>
									</div>
								</div>';
			return $html;
	}


    public function getAgentSidebar($returnOnlyLinks=false){
        $items = array();
        /*$items[] = array('action' => 'index', 'text' => __('Accueil'), 'icon' => 'home');
        $items[] = array('action' => 'profil', 'text' => __('Mon profil'), 'icon' => 'user');
        $items[] = array('action' => 'history', 'text' => __('Mes communications'), 'icon' => 'list', 'parent' => 1, 'label' => __('Historique'));
        $items[] = array('action' => 'mails', 'text' => __('Ma messagerie'), 'icon' => 'envelope', 'child' => 1);
        $items[] = array('action' => 'presentations', 'text' => __('Ma présentation'), 'icon' => 'bullhorn');
        $items[] = array('action' => 'planning', 'text' => __('Mon planning'), 'icon' => 'calendar');
        $items[] = array('action' => 'appointments', 'text' => __('Mes RDV'), 'icon' => 'check');*/

        $items[] = array('action' => 'index', 'text' => __('Mes informations'), 'icon' => 'home', 'parent' => 1, 'label' => __('Mon compte'));
        $items[] = array('action' => 'profil', 'text' => __('Mon profil'), 'icon' => 'user', 'child' => 1);
        $items[] = array('action' => 'presentations', 'text' => __('Ma présentation'), 'icon' => 'bullhorn', 'child' => 1);
        $items[] = array('action' => 'history', 'text' => __('Mes communications'), 'icon' => 'list', 'parent' => 2, 'label' => __('Historique'));
        $items[] = array('action' => 'history', '?' => array('media' => 'phone'), 'text' => __('Par téléphone'), 'icon' => 'earphone', 'child' => 2, 'no_select' => true);
        $items[] = array('action' => 'history', '?' => array('media' => 'email'), 'text' => __('Par écrit'), 'icon' => 'pencil', 'child' => 2, 'no_select' => true);
        $items[] = array('action' => 'history', '?' => array('media' => 'chat'), 'text' => __('Par chat'), 'icon' => 'comment', 'child' => 2, 'no_select' => true);
		$items[] = array('action' => 'notations', 'text' => __('Mes notes'), 'icon' => 'pencil', 'child' => 2);
        $items[] = array('action' => 'mails', 'text' => __('Consultations mail'), 'icon' => 'envelope', 'parent' => 4, 'label' => __('Ma messagerie'));
        $items[] = array('action' => 'chat_history', 'text' => __('Mes chats'), 'icon' => 'comment', 'child' => 4);
        $items[] = array('action' => 'mails', '?' => array('private' => true), 'text' => __('Messages privés'), 'icon' => 'envelope', 'child' => 4);
        $items[] = array('action' => 'planning', 'text' => __('Mon planning'), 'icon' => 'calendar', 'parent' => 3, 'label' => __('Mon calendrier'));
        $items[] = array('action' => 'appointments', 'text' => __('Mes RDV'), 'icon' => 'check', 'child' => 3);

        if ($returnOnlyLinks){
            $out = array();
            foreach ($items AS $item){
                if(isset($item['no_select']))
                    continue;

                $out[] = array('url'  => $this->Html->url(array('controller' => 'agents','action' => $item['action'])),
                               'icon' => $item['icon'],
                               'text' => $item['text'],
                               'selected' => ($this->params['action'] == $item['action'])?1:0);
            }
        }else{
            $out = '<ul class="sidebar_menu hidden-xs">';

            //1er passage dans le tableau pour construire le tableau des enfants
            $itemsChildren = array();
            foreach($items as $item){
                if(isset($item['child'])){
                    if(isset($itemsChildren[$item['child']]))
                        $itemsChildren[$item['child']][] = $item;
                    else{
                        $itemsChildren[$item['child']] = array();
                        $itemsChildren[$item['child']][] = $item;
                    }
                }
            }

            foreach ($items as $k => $item){
                if(!isset($item['parent']) && !isset($item['child']))
                    $out.= '<li'. $this->getLiClass($item) .'>'.
                        '<i class="glyphicon glyphicon-'. $item['icon'] .'"></i>'.
                        $this->Html->link($item['text'], array('controller' => 'agents','action' => $item['action'], '?' => (isset($item['?']) ?$item['?']:false))). $this->badgeSidebar($item['action'], (isset($item['?']) ?$item['?']:array())) .'</li>';
                elseif(isset($item['parent'])){
                    $out.= '<li class="label-parent"><span class="txt-bold">'. $item['label'] .'</span>';
                    $out.= '<ul>';
                    $out.= '<li'. $this->getLiClass($item) .'>'.
                        '<i class="glyphicon glyphicon-'. $item['icon'] .'"></i>'.
                        $this->Html->link($item['text'], array('controller' => 'agents','action' => $item['action'], '?' => (isset($item['?']) ?$item['?']:false))). $this->badgeSidebar($item['action'], (isset($item['?']) ?$item['?']:array())).'</li>';

                    if(isset($itemsChildren[$item['parent']]) && !empty($itemsChildren[$item['parent']])){
                        //Pour chaque enfant
                        foreach($itemsChildren[$item['parent']] as $child){
                            $out.= '<li'. $this->getLiClass($child) .'>'.
                                '<i class="glyphicon glyphicon-'. $child['icon'] .'"></i>'.
                                $this->Html->link($child['text'], array('controller' => 'agents','action' => $child['action'], '?' => (isset($child['?']) ?$child['?']:false))). $this->badgeSidebar($child['action'], (isset($child['?']) ?$child['?']:array())) .'</li>';
                        }
                    }
                    $out.= '</ul>';
                    $out.= '</li>';
                }
            }

            $out.= '<li><i class="glyphicon glyphicon-off"></i>'.$this->Html->link(__('Déconnexion'), array('controller' => 'users','action' => 'logout')).'</li>';
            $out.= '</ul>';
        }
        return $out;
    }

    private function getLiClass($item){
        if($this->params['action'] == $item['action']){
            //Paramètre query ??
            if(isset($item['?'])){
                //Identique à celui de l'item
                if($this->params->query == $item['?'])
                    return ' class="selected"';
            }
            //Avons-nous des paramètres query dans l'url
            elseif(empty($this->params->query))
                return ' class="selected"';
        }
        return '';
    }

    public function getAgentMedias($agent_number=0, $has_photo=false, $has_audio=false, $listing = true)
    {
        $return = array(
            'photo_filename' => '/'.Configure::read('Site.defaultImage'),
            'audio_filename' => false
        );

        /* Si pas de photo ni audio, on retourne le tableau vide */
        if (!$has_photo && !$has_audio)return $return;

        /* On s'occupe de la photo */
        if ($has_photo == 1){
            if($listing) $return['photo_filename'] = '/'.Configure::read('Site.pathPhoto').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'_listing.jpg';
            else $return['photo_filename'] = '/'.Configure::read('Site.pathPhoto').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.jpg';
        }

        /* On s'occupe de l'audio */
        if ($has_audio == 1){
            $return['audio_filename'] = '/'.Configure::read('Site.pathPresentation').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp3';
        }

        return $return;
    }

    public function getAvatar($agent,$listing = true){
        $photoFilename = '/'.Configure::read('Site.defaultImage');
        if(empty($agent)) return $photoFilename;

        if (isset($agent['has_photo']) && $agent['has_photo'] == 1 && isset($agent['agent_number']['0']) && isset($agent['agent_number']['1'])){
            $photoFilename = '/'.Configure::read('Site.pathPhoto').'/'.$agent['agent_number'][0].'/'.$agent['agent_number'][1].'/'.$agent['agent_number'].($listing?'_listing':'').'.jpg';
        }

        return $photoFilename;
    }

    public function agentActif($date){
        if(empty($date))
            return false;

        $dateNow = date('Y-m-d H:i:s');
        $tmstmpStart = new DateTime($date);
        $tmstmpStart = $tmstmpStart->getTimestamp();
        $tmstmpEnd = new DateTime($dateNow);
        $tmstmpEnd = $tmstmpEnd->getTimestamp();
        $sec = ($tmstmpEnd - $tmstmpStart);

        //Inactif si délai plus grand que le max autorisé
        if($sec > Configure::read('Chat.maxTimeInactif'))
            return false;

        return true;
    }

	 public function agentModeBusy($agent_id){

		App::import("Model", "Chat");
		$model = new Chat();
		$chat = $model->find('first', array(
							'fields' => array('Chat.id'),
							'conditions' => array('Chat.to_id' => $agent_id,'Chat.date_end' => NULL),
							'recursive' => -1
						));
		 if($chat){
			 return 'tchat';
		 }


        return 'phone';
    }

    public function getLogo(){
		/*if($this->request->isMobile())
        	$file = new File(Configure::read('Site.pathLogo').'/'.$this->Session->read('Config.id_domain').'_logo_mobile.jpg');
		else
			$file = new File(Configure::read('Site.pathLogo').'/'.$this->Session->read('Config.id_domain').'_logo.jpg');

        $filename = 'default.jpg';
        if($file->exists()){

			if($this->request->isMobile())
				 $filename = $this->Session->read('Config.id_domain').'_logo_mobile.jpg';
			else
				 $filename = $this->Session->read('Config.id_domain').'_logo.jpg';
		}

        $out = $this->Html->link(
            $this->Html->image('/'.Configure::read('Site.pathLogo').'/'.$filename),
            array('controller' => 'home', 'action' => 'index'),
            array('escape' => false)
        );*/

        $out = '<a class="navbar-brand animated bounceIn" href="'.Router::url('/',true).'">';
        $out.= $this->Html->image(Router::url('/',true).'theme/black_blue/img/logo.png', array("alt"=>ucfirst(Configure::read('Site.nameDomain')), "class"=> 'img-responsive'));
        $out.= '</a>';


        return $out;
    }

	public function getCreditLightString($credit=0)
    {
        if (!$credit){
            $user = $this->Session->read('Auth.User');
            $credit = isset($user['credit'])?$user['credit']:0;
        }

        return __('Crédits : ').'<span>'.$credit.' '.__('soit ').$this->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true).'</span>';
		//return '<span>'.$this->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true).'</span>';
    }

	public function getCreditStringMobile($credit=0)
    {
        if (!$credit){
            $user = $this->Session->read('Auth.User');
            $credit = isset($user['credit'])?$user['credit']:0;
        }

        return $credit.' '.__('soit ').$this->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true);
    }

    public function getCreditString($credit=0)
    {
        if (!$credit){
            $user = $this->Session->read('Auth.User');
            $credit = isset($user['credit'])?$user['credit']:0;
        }

        return __('Crédits : ').'<span>'.$credit.' '.__('soit ').$this->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true).'</span>';
    }
	public function getCreditStringMenu($credit=0)
    {
        if (!$credit){
            $user = $this->Session->read('Auth.User');
            $credit = isset($user['credit'])?$user['credit']:0;
        }
        return '<span class="title">'.__('Temps restants').'</span><span class="value">'.$this->secondsToHis(($credit * Configure::read('Site.secondePourUnCredit')), true).'</span>';
    }
    public function getClientLogged()
    {
        $user = $this->Session->read('Auth.User');
        if ($user && isset($user['role']) && in_array($user['role'],array('client')))
            return $user;



        return array();
    }

	public function getHeaderMobileUserBlock()
	{
		$user = $this->Session->read('Auth.User');

        $current_credit = false;
        if (!empty($user)){
            App::import("Model", "User");
            $obj = new User();
            $current_credit = $obj->getCredit($user['id']);
            $user['credit'] = $current_credit;
        }

		$out = '<span class="lclick"><a class="login-a" role="button" data-toggle="collapse" href="#loginCollapse" aria-expanded="false" aria-controls="loginCollapse">';
		if ($user && isset($user['role']) && in_array($user['role'],array('client','agent'))){
			$out .= __('Bonjour').' '.h($user['firstname']);
		}else{
			$out .= __('Connexion');
		}
		 $out .= '<i class="glyphicon glyphicon-chevron-down"></i></a></span>';
		return $out;
	}


    public function getHeaderUserBlock()
    {
        $user = $this->Session->read('Auth.User');

        $current_credit = false;
        if (!empty($user)){
            App::import("Model", "User");
            $obj = new User();
            $current_credit = $obj->getCredit($user['id']);
            $user['credit'] = $current_credit;

	    App::import("Model", "CountryLangPhone");
            $obj_phone = new CountryLangPhone();
            $phones = $obj_phone->getPhones($this->Session->read('Config.id_country'), $this->Session->read('Config.id_lang'));

        }


        if ($user && isset($user['role']) && in_array($user['role'],array('client','agent'))){
	    
	 
            $role = $user['role'];

            if ($role == 'client')$role = 'accounts';

			$out = '<ul class="headermenucon">';
/*
				if ($user['role'] == 'client'){
   // RUBRIQUES DE N° TéLéPHONE à 'ACHETER DES MINUSTES'  
					$out .= '<li class="user-data buy">'.$this->Html->link('acheter des minutes', array('controller' => $role, 'action' => 'buycredits'), array( 'escape'=>false )).'</li>';
					$out .= '<li class="user-data credits">'.$this->getCreditStringMenu($current_credit).'</li>';
					$out .= '<li class="user-data code"><span class="title">'.__('Code personnel').'</span><span class="value">'.$user['personal_code'].'</span></li>';
					$out .= '<li class="user-data phone"><span class="title">'.__('N° téléphone').'</span><span class="value">'.$this->formatPhoneNumber($phones['0']['CountryLangPhone']['prepayed_phone_number']).'</span></li>';
					
				}
*/

			$out .= '<li class="user-logged dropdown dropdown-accordion " data-accordion="#accordion">';
			$name = '';
			if ($role == 'agent')
				$name = $user['pseudo'];
			else
				$name = $user['firstname'];

			if(strlen($name)>10 && $role == 'client'){
				$name = substr($name,0,7).'...';
			}

			$out .= '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown"><span class="title">'.__('Bienvenue').'</span><span class="value">'.h($name).' </span><i class="fa fa-angle-down" aria-hidden="true"></i></a>';


			if ($user['role'] == 'client')
                $links = $this->getAccountSubmenu();
            elseif ($user['role'] == 'agent')
		
	/* RUBRIQUES deroulantes DU MENU 'Bienvenu AGent' */ 	
       $links = $this->getAgentSubmenu();

				$out .= '<ul class="dropdown-menu" role="menu">';
					$out .= '<li>';
						$out .= '<div class="panel-group dd-menu" id="accordion">';
							$out.= $links;
						$out .= '</div>';
					$out .= '</li>';
				$out .= '</ul>';
			$out .= '</li>';

			$out .='</ul>';
			
            //return $out;
        }else{
            /* Pas connecté 

	     * __('Connectez-vous')
	     * 	     */
			$out = '<a href="#connection" rel="modal:open" title="'.__('Inscription gratuite sur '. Configure::read('Site.name')).'" class="btn large blue  subscribe">'.__('Register').'</a>'.
				'<a class="btn connect"  rel="modal:open" href="#connection" title="'.__('Connectez-vous à ').Configure::read('Site.name').'">'.__('Se connecter').'</a>';
			
            return $out;
	    
        }
    }
    
    public function getHeaderUserBlockMobile()
    {
		$user = $this->Session->read('Auth.User');

		$css_bg = '';
		if(!$user)$css_bg = 'user-nologged';
		if(!$user){
		$html = '<nav class="navbar navbar-custom navbar-collapse navbar-fixed-top navbar-offcanvas-account collapse '.$css_bg.'" id="offcanvasaccount">';
        	$html .= '<div class="nav navbar-nav navbar-main">';

					$html .= '<div class="sidebar-mobile-form">';
						$html .= '<p class="title">'.__('Connectez-vous').'</p>';
						$html .= '<p class="txt">'.__('Bienvenue sur spiriteo').'</p>';
						$html .= '<form action="/users/login" class="form-inline" id="UserLoginFormMobile" method="post" accept-charset="utf-8">';
							$html .= '
								<div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>
							<input type="hidden" name="data[User][compte]" value="client" id="UserCompteMobile"/>
							<div class="input email ch"><input name="data[User][email]" placeholder="E-mail" maxlength="200" type="email" id="UserEmailMobile" required /></div>
							<div class="input password ch"><input name="data[User][passwd]" placeholder="Mot de passe" type="password" id="UserPasswdMobile" required /></div>';
						$html.=  $this->Form->submit(__('Se connecter'), array('before' => '', 'after' => '', 'class'=> 'btn-sidebar btn-white'));
						$html.= '<p class="forget">'.$this->Html->link(__('Mot de passe oublié ?'), array('controller' => 'users', 'action' => 'passwdforget')).'</p>';
					$html.= '</form>';
					$html .= '</div>';
					$html .= '<div class="sidebar-mobile-btn">';
						$html .= '<p class="stitle">'.__('vous n’êtes pas inscrit ?').'</p>';
						$html.= $this->Html->link(__('s‘inscrire'), array('controller' => 'users', 'action' => 'subscribe'), array('class' => 'btn btn-sidebar btn-gold'));
					$html .= '</div>';


			$html .= '</div>';
			$html .= '</nav>';
		}else{
			$html = '<nav class="navbar navbar-custom navbar-connect navbar-collapse navbar-fixed-top navbar-offcanvas-account collapse '.$css_bg.'" id="offcanvasaccount">';

				$html .= '<div class="txt">'.__('Bienvenue').'</div>';
				if ($user['role'] == 'client'){

					App::import("Model", "User");
					$obj = new User();
					$current_credit = $obj->getCredit($user['id']);


					$name = $user['firstname'];
					if(strlen($name)>15)$name = substr($name,0,12).'...';
					$html .= '<div class="name">'.$name.'</div>';
					$html .= '<hr />';
					$html .= $this->getCreditStringMenu($current_credit);//'<span class="stitle">'.__('Temps restants').'</span>';
					$html .= '<a class="buy" href="'.$this->getProductsLink().'" >acheter des minutes</a>';
					$html .= $this->getAccountSubmenuMobile();
				}
				if ($user['role'] == 'agent'){
					$name = $user['pseudo'];
					if(strlen($name)>15)$name = substr($name,0,12).'...';
					$html .= '<div class="name">'.$name.'</div>';
					$html .= '<hr />';
					$html .= $this->getAgentSubmenuMobile();
				}

			$html .= '</nav>';
		}

		return $html;
    }
    public function getThirdNumberIfExists($phones=false)
    {
        if (empty($phones))return false;
        if (isset($phones['0']['CountryLangPhone']))
            $phones = $phones['0']['CountryLangPhone'];
        if (empty($phones['third_phone_number']))return false;

        return array(
            'numero'        =>  $phones['third_phone_number'],
            'pricemin'      =>  $phones['third_minute_cost'],
            'title'         =>  $phones['mention_legale_num3'],
        );
        $third = Configure::read('ThirdTelNumber');
        $thirdNumberRule = false;
        if (!empty($third) && is_array($third)){
            foreach ($third AS $thi){
                if ($thi['country_id'] == $this->Session->read('Config.id_domain') && $thi['lang_id'] == $this->Session->read('Config.id_lang')){
                    $thirdNumberRule = $thi;
                    break;
                }
            }
        }
        return $thirdNumberRule;
    }
    public function getNavigation(){
        $cacheKey = 'menu-navigation-'.$this->Session->read('Config.language');

        $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));

        if($cache !== false)
            $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        else{
            App::import('Controller', 'Menus');
            $menu = new MenusController();
            $menu->generateMenu();
            $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        }

        /* Conditionnelles AUDIOTEL lien */
            if ((int)$this->Session->read('Config.id_domain') === 19){
                $cache = $this->removeLinkFromHtmlNav(36, $cache);
            }else{
                $cache = str_replace("subc_tarifs-modalites","", $cache);
            }

		$cache = str_replace('navbar-main-collapse','navbar-main-collapse',$cache);

	    return $cache;
    }
	public function getNavigationMobile(){

		App::import("Controller", "AppController");
		$leftblock_app = new AppController();
		$lang = 	$this->Session->read('Config.language');
        $cacheKey = 'menu-navigation-'.$this->Session->read('Config.language');

        $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));

        if($cache !== false)
            $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        else{
            App::import('Controller', 'Menus');
            $menu = new MenusController();
            $menu->generateMenu();
            $cache = Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        }

        /* Conditionnelles AUDIOTEL lien */
        if ((int)$this->Session->read('Config.id_domain') === 19){
           $cache = $this->removeLinkFromHtmlNav(36, $cache);
        }else{
           $cache = str_replace("subc_tarifs-modalites","", $cache);
        }

		$cache = str_replace('navbar-main-collapse','navbar-main-collapse navbar-spe-mobile',$cache);
		$cache = str_replace('col-md-3 col-sm-4 col-xs-12','col-md-3 col-sm-12 col-xs-12',$cache);
		$cache = str_replace('col-md-3 col-sm-3 col-xs-12','col-md-3 col-sm-12 col-xs-12',$cache);
		$cache = str_replace('<div class="collapse navbar-collapse navbar-left navbar-main-collapse navbar-spe-mobile">','<nav class="navbar navbar-custom navbar-spe-mobile navbar-fixed-top navbar-offcanvas " id="offcanvas">',$cache);
		$cache .="</nav>";
		$cache = str_replace('</ul></div></nav>','</ul></nav>',$cache);


		//cree N+3
		$cache = str_replace('dropdown-header"','smenu_title"',$cache);

		$stabcache = explode('<div class="col-md-3 col-sm-12 col-xs-12">',$cache);
		$new_menu = $stabcache[0];
		for($nn=1;$nn<=count($stabcache);$nn++){
			$new_menu2 = '';
			if($nn < count($stabcache))$new_menu .= '<div class="col-md-3 col-sm-12 col-xs-12">';
			if(!empty($stabcache[$nn])){
				$tt = explode('</li>',$stabcache[$nn]);
				$new_menu .= $tt[0];
				for($nn2=1;$nn2<=count($tt);$nn2++){
					if($nn2 < count($tt) && $nn2 < 2 && !substr_count($stabcache[$nn],'link_purple'))$new_menu2 .= ' <i class="fa fa-angle-down" aria-hidden="true"></i>';
					if($nn2 < count($tt) && $nn2 < 2)$new_menu2 .= '</li><div class="dropdown-submenu"><ul class="list-unstyled list-sub">';
					if($nn2 < count($tt) && $nn2 >= 2)$new_menu2 .= '</li>';
					if(!empty($tt[$nn2]))
					$new_menu2 .= $tt[$nn2];
				}
				$new_menu2 = str_replace('</ul></div>','</ul></div></ul></div>',$new_menu2);
				$new_menu .= $new_menu2;
			}
		}

		$new_menu = str_replace('link_white','link_white disabled',$new_menu);
		$cache = $new_menu;

		//bouton acheter
		$cache = str_replace('<ul class="nav navbar-nav navbar-main">','<ul class="nav navbar-nav navbar-main"><li class="animated fadeIn buy-container"><a class="buy" href="'.$this->getProductsLink().'" >acheter des minutes</a></li>',$cache);

		//tous les experts
		$cache = str_replace('<a href="/">Accueil</a>','<a href="/">Tous les experts</a>',$cache);

		//ajout menu
		$add_menu = '<li class="animated fadeIn m-sep-container"><a href="#"><span class="m-sep">|</span></a></li>';
		$add_menu .=  '<li class="animated fadeIn">'.$this->getPageLink(337,array('style'=>''),'Devenir expert').'</li>';
		$cache = str_replace('</ul></nav>',$add_menu.'</ul></nav>',$cache);


		//patch icon menu mobile
		$cache = str_replace('Tous les experts','<i class="icon-menu icon-menu-expert"></i> Tous les experts',$cache);
		$cache = str_replace('Derniers avis','<i class="icon-menu icon-menu-review" aria-hidden="true"></i> Derniers avis',$cache);
		$cache = str_replace('Avis','<i class="icon-menu icon-menu-review" aria-hidden="true"></i> Avis',$cache);
		$cache = str_replace('Spécialités','<i class="icon-menu icon-menu-speciality" aria-hidden="true"></i> Spécialités',$cache);
		$cache = str_replace('Thèmes','<i class="icon-menu icon-menu-teme" aria-hidden="true"></i> Thèmes',$cache);
		$cache = str_replace('Horoscope du jour','<i class="icon-menu icon-menu-horoscope" aria-hidden="true"></i> Horoscope du jour',$cache);
    if(!substr_count($cache,'Horoscope du jour' ))
		$cache = str_replace('Horoscope','<i class="icon-menu icon-menu-horoscope" aria-hidden="true"></i> Horoscope du jour',$cache);
		$cache = str_replace('Blog','<i class="icon-menu icon-menu-blog" aria-hidden="true"></i> Blog',$cache);
		$cache = str_replace('Cadeaux','<i class="icon-menu icon-menu-gift" aria-hidden="true"></i> Cadeaux',$cache);
		$cache = str_replace('Devenir expert','<i class="icon-menu icon-menu-become" aria-hidden="true"></i> Devenir expert',$cache);
		$cache = str_replace('Consultations','<i class="icon-menu icon-menu-consult" aria-hidden="true"></i> Consultations',$cache);
		$cache = str_replace('Tirages Tarots','<i class="icon-menu icon-menu-card" aria-hidden="true"></i> Tirages Tarots',$cache);
		return $cache;
    }

    private function removeLinkFromHtmlNav($id_to_find=0, $html=false)
    {

        if (!$id_to_find || !$html)return false;
        $pos = strpos($html, 'id="pelt'.$id_to_find.'"');
        if ($pos !== false){
            $deb = $pos;
            for ($i=0; $i<200; $i++){
                if ($html[$deb] === '<')
                    break;
                $deb-= 1;
            }
            $end = $pos;
            for ($i=0; $i<200; $i++){
                if ($html[$end] === '<')
                    break;
                $end+= 1;
            }
            $link = '<li>'.substr($html, $deb, ($end-$deb))."</a></li>";
            $html = str_replace($link, '', $html);
        }
        return $html;
    }
    public function getFooter(){
        $cacheKey = 'footer-navigation-'.$this->Session->read('Config.language');

        //Cache::delete($cacheKey, Configure::read('nomCacheNavigation'));

        if(Cache::read($cacheKey, Configure::read('nomCacheNavigation')) !== false)
            return Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        else{
            App::import('Controller', 'Footers');
            $footer = new FootersController();
            $footer->generateFooter();
            return Cache::read($cacheKey, Configure::read('nomCacheNavigation'));
        }
    }

    public function getHeaderSearchBlock()
    {
        $html = $this->Form->create('Search', array('nobootstrap' => 1,'class' => 'form-vertical login-form'));
        $html.= $this->Form->input('search', array('label' => __('Rechercher :'),'div' => false, 'placeholder' => __('Votre recherche ...')));
        $html.= $this->Form->submit(__('OK'), array('label' => false, 'div' => false, 'name' => 'search_term', 'id' => 'search_term'));
        $html.= $this->Form->end();
        return $html;
    }

    public function getAgentBusy(){
        App::import("Model", "User");
        $user = new User();
        return $user->find('all', array(
            'conditions' => array(
                'role' => 'agent',
                'deleted' => 0,
                'agent_status !=' => 'unavailable',
                'active' => 1
            ),
            'recursive' => -1
        ));
    }

	public function getAgentBusyData($offset = 0,$limit = 15){

		//if($offset == 1)$offset = 0;

		$offset = $offset * $limit;

        App::import("Model", "User");
        $user = new User();

				if (empty($orderBy)){

    		$orderBy = array();
    		$orderBy[] = 'IF(User.agent_status=\'available\' OR User.agent_status =\'busy\',1,0) DESC';  /* disponibilite*/
			$orderBy[] = '
							IF(
								(
									User.consult_email
										+
									IF(
										(IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_phone
								)
							=3,1,0) DESC'; /* Email telephone chat actif  date last activity +3600*/

			//$orderBy[] = 'IF(User.consult_phone+User.consult_chat+User.consult_email=3,1,0) DESC';/* Email telephone chat */

			//$orderBy[] = 'IF(User.consult_phone+User.consult_chat=2,1,0) DESC';  /* Telephone et chat */
			$orderBy[] = '
							IF(
								(

									IF(
										(IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_phone
								)
							=2,1,0) DESC'; /* Telephone et chat +3600 */

			$orderBy[] = 'IF(User.consult_phone+User.consult_email=2,1,0) DESC';  /* Telephone et mail */
   			$orderBy[] = 'User.consult_phone DESC';  /* Telephone seulement */
    		//$orderBy[] = 'IF(User.consult_chat+User.consult_email=2,1,0) DESC';  /* Chat et mail */
			$orderBy[] = '
							IF(
								(

									IF(
										(IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_email
								)
							=2,1,0) DESC'; /* Chat et mail */

    		//$orderBy[] = 'User.consult_chat DESC';  /* Chat*/
			$orderBy[] = 'IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) DESC';  /* Chat*/

    		$orderBy[] = 'User.consult_email DESC';  /* email*/

    		//$orderBy[] = '(SELECT COUNT(*) FROM user_credit_history WHERE agent_id = CategoryUser.user_id AND media = \'phone\') DESC';  /* + consultes */
			//$orderBy[] = '(SELECT ROUND(AVG(rate)) FROM reviews WHERE agent_id = CategoryUser.user_id) DESC';  /* mieux noté */
   			//$orderBy[] = 'IF(User.agent_status=\'unavailable\',1,0) DESC';  /* disponibilite*/

			$orderBy[] = 'User.list_pos ASC';  /* position aleatoire*/
			/*
			$orderBy = array(
				0 => 'agent_status ASC',
				1 => 'countPhoneCall DESC',
				2 => 'User.consult_phone DESC',
				3 => 'User.consult_email DESC',
				4 => 'User.consult_chat DESC'

			);
			*/
		}



		$dateNow = date('Y-m-d H:i:s');
        return $user->find('all', array(
			'fields' => array('User.id','User.pseudo','User.agent_number','User.langs','User.agent_status','User.date_last_activity','User.has_photo','User.consult_email','User.consult_phone','User.consult_chat','User.reviews_avg','IF((agent_status = \'busy\'),
                    (SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status'),
            'conditions' => array(
                'role' => 'agent',
                'deleted' => 0,
                'agent_status !=' => 'unavailable',
                'active' => 1
            ),
			'order' => $orderBy,
			'limit' => $limit,
			'offset' => $offset,
            'recursive' => -1
        ));
    }

    public function getPlanningDispo($idAgent){
        App::import("Model", "Planning");
        $planning = new Planning();
		#var_dump($this->Session->read('Config.timezone_user'));
        //Date actuelle, date du serveur
        $dateNow = date('d-m-Y H:i:s');
        //Date de l'utilisateur
        $dateNow = Tools::dateUser($this->Session->read('Config.timezone_user'),$dateNow);

        //Date sous un autre format uniquement, pour faire correspondre avec explodeDate()
        $dateNow = $this->Time->format($dateNow, '%d-%m-%Y %H:%M');
		#var_dump($dateNow);
        //Date éclatée
        $dateNow = Tools::explodeDate($dateNow);
        //Le premier horaire ou l'agent est dispo
        $dispo = $planning->getFirstDispo($idAgent, $dateNow);
        //si vide, planning non renseigné
        if(empty($dispo))
            return '';
        else{
            //Date de la dispo sous un format date
            $dateDispo = $dispo['Planning']['A'].'-'.$dispo['Planning']['M'].'-'.$dispo['Planning']['J'];

            //Dispo aujourd'hui
            if($this->Time->isToday($dateDispo))
                return ''.__('Retour prévu :').'<span class="depuis">'.str_pad($dispo['Planning']['H'],2,'0',STR_PAD_LEFT).'h'.str_pad($dispo['Planning']['Min'],2,'0',STR_PAD_LEFT).'</span>';
            elseif($this->Time->isTomorrow($dateDispo)) //Dispo demain
                return ''.__('Retour prévu :').'<span class="depuis">'.__('Demain à').' '
                    .str_pad($dispo['Planning']['H'],2,'0',STR_PAD_LEFT).'h'.str_pad($dispo['Planning']['Min'],2,'0',STR_PAD_LEFT).'</span>';
            elseif($dateDispo < (date('Y-m-d', strtotime('+7 days'))))
                return ''.__('Retour prévu :').'<span class="depuis">'
                    .ucfirst($this->Time->format($dateDispo,'%A')).' '.__('à').' '
                    .str_pad($dispo['Planning']['H'],2,'0',STR_PAD_LEFT).'h'.str_pad($dispo['Planning']['Min'],2,'0',STR_PAD_LEFT).'</span>';
            else
                return ''.__('Retour prévu :').'<span class="depuis">'
                .ucfirst($this->Time->format($dateDispo,'%A %d/%m')).' '.__('à').' '
                .str_pad($dispo['Planning']['H'],2,'0',STR_PAD_LEFT).'h'.str_pad($dispo['Planning']['Min'],2,'0',STR_PAD_LEFT).'</span>';
        }
    }


    private function getCmsPage($cms_id=0, $lang_id=0)
    {
        if (!$cms_id)return false;
        if (!$lang_id)$lang_id = $this->Session->read('Config.id_lang');

        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $page = $model->find('first', array(
            'fields' => array('PageLang.content', 'PageLang.meta_title', 'PageLang.meta_description', 'PageLang.meta_keywords'),
            'conditions' => array('Page.id' => $cms_id, 'Page.page_category_id' => Configure::read('Site.catBlocTexteID'), 'Page.active' => 1),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$lang_id
                    )
                )
            ),
            'recursive' => -1
        ));

        if (!empty($page))return $page;
        if (empty($page) && $lang_id != 1){
            return $this->getCmsPage($cms_id, 1);
        }
        return false;
    }

	private function getPage($cms_id=0, $lang_id=0)
    {
        if (!$cms_id)return false;
        if (!$lang_id)$lang_id = $this->Session->read('Config.id_lang');

        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $page = $model->find('first', array(
            'fields' => array('PageLang.content', 'PageLang.meta_title', 'PageLang.meta_description', 'PageLang.meta_keywords'),
            'conditions' => array('Page.id' => $cms_id, 'Page.active' => 1),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$lang_id
                    )
                )
            ),
            'recursive' => -1
        ));

        if (!empty($page))return $page;
        return false;
    }

	public function getPageBlocTexteHomebyLang($idBlock, $lang_id=0){
        if(empty($idBlock) || !is_numeric($idBlock))
            return false;

        if (!$lang_id)$lang_id = $this->Session->read('Config.id_lang');

        //On récupère la page
        App::import("Model", "Block");
        $model = new Block();
        $block = $model->find('first', array(
			'fields' => array('Block.*','BlockLang.*'),
            'conditions' => array('Block.id' => $idBlock, 'Block.active' => 1),
            'joins' => array(
                array(
                    'table' => 'block_langs',
                    'alias' => 'BlockLang',
                    'conditions' => array(
                        'BlockLang.block_id = Block.id',
                        'BlockLang.lang_id = '.$lang_id
                    )
                )
            ),
            'recursive' => -1
        ));
        if (!empty($block['BlockLang']['text1']))return $block['BlockLang'];

        return '';
    }
	public function getPageTextebyLang($idPage, $lang_id=0){
        if(empty($idPage) || !is_numeric($idPage))
            return false;

        $page = $this->getPage($idPage,$lang_id);

        if(empty($page) || empty($page['PageLang']['content']))
           return false;

        $title = $page['PageLang']['meta_title'];
        $out= $page['PageLang']['content'];


        return $out;
    }

	public function getPageBlocTextebyLang($idPage, $lang_id=0){
        if(empty($idPage) || !is_numeric($idPage))
            return false;

        $page = $this->getCmsPage($idPage,$lang_id);

        if(empty($page) || empty($page['PageLang']['content']))
           return false;

        $out = '<div class="cms_container"><div class="cms_text2">';

        $title = $page['PageLang']['meta_title'];
        $out.= $page['PageLang']['content'];
        $out.= '<div class="clear"></div></div></div>';

        return $out;
    }
    public function getPageBlocTexte($idPage, $h1 = false, &$title=false, &$page=false){
        if(empty($idPage) || !is_numeric($idPage))
            return false;

        $page = $this->getCmsPage($idPage);

        if(empty($page) || empty($page['PageLang']['content']))
           return false;

        $out = '<div id="cms_container"><div class="cms_text2">';

        $title = $page['PageLang']['meta_title'];
        if($h1)
            $out.= '<h1>'.$page['PageLang']['meta_title'].'</h1>';
        $out.= $page['PageLang']['content'];
        $out.= '<div class="clear"></div></div></div>';

        return $out;
    }
    public function getMailBlock($idPage=0){
        if(empty($idPage) || !is_numeric($idPage))
            return false;

        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $page = $model->find('first', array(
            'fields' => array('PageLang.content', 'PageLang.meta_title'),
            'conditions' => array('Page.id' => $idPage),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1
        ));

        if (empty($page)){
            /* On tente de rechercher sur le francais */
                $page = $model->find('first', array(
                    'fields' => array('PageLang.content', 'PageLang.meta_title'),
                    'conditions' => array('Page.id' => $idPage),
                    'joins' => array(
                        array(
                            'table' => 'page_langs',
                            'alias' => 'PageLang',
                            'conditions' => array(
                                'PageLang.page_id = Page.id',
                                'PageLang.lang_id = 1'
                            )
                        )
                    ),
                    'recursive' => -1
                ));
        }

        if (empty($page)){
            /* On tente de rechercher sur toutes les langues */
            $page = $model->find('first', array(
                'fields' => array('PageLang.content', 'PageLang.meta_title'),
                'conditions' => array('Page.id' => $idPage),
                'joins' => array(
                    array(
                        'table' => 'page_langs',
                        'alias' => 'PageLang',
                        'conditions' => array(
                            'PageLang.page_id = Page.id'
                        )
                    )
                ),
                'recursive' => -1
            ));
        }

        if(empty($page) || empty($page['PageLang']['content']))
            return false;

        return $page['PageLang']['content'];
    }
    private function hidePositionFromString($string=false)
    {
        return preg_replace('/^[0-9]+\./', '', $string);
    }

    public function getColumnBlocks(){
        if (!$this->Session->read('Config.id_lang'))return false;

        //On récupère le block
        App::import("Model", "LeftColumn");
        $model = new LeftColumn();

        $dateNow = date('Y-m-d 00:00:00');
        $blocks = $model->find('all', array(
            'fields' => array('LeftColumn.id', 'LeftColumn.domain', 'LeftColumnLang.*'),
            'conditions' => array(
                'LeftColumn.active' => 1,
                'LeftColumn.validity_start <=' => $dateNow,
                'OR'    => array(
                    array('LeftColumn.validity_end >=' => $dateNow),
                    array('LeftColumn.validity_end IS NULL')
                )
            ),
            'joins' => array(
                array(
                    'table' => 'left_column_langs',
                    'alias' => 'LeftColumnLang',
                    'type'  => 'inner',
                    'conditions' => array(
                        'LeftColumnLang.left_column_id = LeftColumn.id',
                        'LeftColumnLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'LeftColumn.position ASC',
            'recursive' => -1
        ));

        //Disponible pour ce site
        foreach($blocks as $key => $block){
            //Liste des domaines
            $domains = explode(',', $block['LeftColumn']['domain']);

            if(!in_array($this->Session->read('Config.id_domain'), $domains))
                unset($blocks[$key]);

        }

        return $blocks;
    }

    public function getLeftColumnBlocks_old(){
        //On récupère la page
        App::import("Model", "Page");
        $model = new Page();
        $page = $model->find('all', array(
            'fields' => array('PageLang.content', 'PageLang.meta_title', 'PageLang.name'),
            'conditions' => array('Page.page_category_id' => 11, 'Page.active' => 1),
            'joins' => array(
                array(
                    'table' => 'page_langs',
                    'alias' => 'PageLang',
                    'conditions' => array(
                        'PageLang.page_id = Page.id',
                        'PageLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'PageLang.name ASC',
            'recursive' => -1
        ));

        if (empty($page))return false;
        foreach ($page AS $k => $v)
            $page[$k]['PageLang']['name'] = $this->hidePositionFromString($v['PageLang']['name']);

        return $page;
    }

    private function getCategorieLink($id_category = 0){
        //On charge le model categorieLang
        App::import("Model", "CategoryLang");
        $model = new CategoryLang();

        $data = $model->find('first', array(
            'fields' => 'link_rewrite',
            'conditions' => array('Category.active' => 1, 'category_id' => $id_category, 'lang_id' => $this->Session->read('Config.id_lang'))
        ));

        return $data;
    }

    public function getCategories($id_category=1)
    {
        $categories = $this->_getCategories();

        $li = array();
        $li[]= '<li'.(($id_category==0 || $id_category== 1)?' class="selected"':'').'>'.(($id_category==0)?'<div class="arrow"></div>':'').$this->Html->link(__('Accueil'),
                array(
                    'controller' => 'home',
                    'action' => 'index'
                )).'</li>';

		$place = array(
			1 => 0,
			5 => 1,
			7 => 2,
			2 => 3,
			3 => 4,
			6 => 5,
			20 => 6,
			25 => 7
		);

		$categories_reoder = array();
		foreach ($categories AS $catr){
			$p = $place[$catr['Category']['id']];
			$categories_reoder[$p] = $catr;

		}
		ksort($categories_reoder);
		#var_dump($categories_reoder);

        foreach ($categories_reoder AS $k => $cat){
            $classes = array();
            if ($k == (count($categories)-1))
                $classes[] = 'last';
            if ($id_category == $cat['Category']['id'])
                $classes[] = 'selected';

            if ($cat['Category']['id'] != 1){
                $li[]= '<li class="'.implode(" ",$classes).'">'.(($id_category==$cat['Category']['id'])?'<div class="arrow"></div>':'').$this->Html->link($cat['CategoryLang']['name'] ,
                        array(
                            'language' => $this->Session->read('Config.language'),
                            'controller' => 'category',
                            'action' => 'display',
                            'id' => $cat['Category']['id'],
                            'link_rewrite' => $cat['CategoryLang']['link_rewrite']
                        )).'</li>';
            }
        }
        return '<ul>'.implode("<li class=\"sep\"></li>", $li)."</ul>";
    }

    private function _getCategories()
    {
        $result = Cache::read('categories_nav_'.$this->Session->read('Config.language'), 'layout_element');

        if (!$result){
            App::import("Model", "CategoryLang");
            $model = new CategoryLang();
            $categories = $model->find("all",
                array('conditions' => array(
                    'Lang.language_code' => $this->Session->read('Config.language'),
                    'Category.active'    => 1),
                      'fields'     => array('CategoryLang.name','Category.id','CategoryLang.link_rewrite'),
                      'order'      => array('CategoryLang.name ASC')
                )
            );
            Cache::write('categories_nav_'.$this->Session->read('Config.language'), $categories, 'layout_element');
            return $categories;
        }
        return $result;
    }

    public function getBandeauCat($id_category){

        $li = array();
        $li[]= '<li>'.$this->Html->link(__('Accueil'),
                array(
                    'controller' => 'home',
                    'action' => 'index'
                )).'</li>';

        //On charge le model categorieLang
        App::import("Model", "CategoryLang");
        $model = new CategoryLang();

        $cat = $model->find('first', array(
            'fields' => array('link_rewrite'),
            'conditions' => array('CategoryLang.category_id' => $id_category, 'CategoryLang.lang_id' => $this->Session->read('Config.id_lang')),
            'recursive' => -1
        ));

        if(!empty($cat)){
            if ($id_category == 1){
                $li[] = '<li>'.$this->Html->link(__('Voir les experts'),
                        array(
                            'controller' => 'home',
                            'action' => 'index'
                        )).'</li>';

            }else{
                $li[] = '<li>'. $this->Html->link(__('Voir les experts'),
                        array(
                            'language' => $this->Session->read('Config.language'),
                            'controller' => 'category',
                            'action' => 'display',
                            'id' => $id_category,
                            'link_rewrite' => $cat['CategoryLang']['link_rewrite']
                        )) .'</li>';
            }

        }
        return '<ul>'.implode("<li class=\"sep\"></li>", $li)."</ul>";
    }

   /* public function _old_getHeaderLangBlock()
    {
        $result = Cache::read('languages_nav', 'layout_elements');
        if (!$result){
            App::import("Model", "Lang");
            $model = new Lang();
            $langs = $model->find("all",
                array('conditions' => array(
                    'Lang.active'  => 1,
                ),
                    'fields'     => array('Lang.name','Lang.language_code', 'Lang.id_lang'),
                    'order'      => array('Lang.name ASC'),
                    'recursive' => 0
                )
            );
            Cache::write('languages_nav', $langs, 'layout_elements');
            return $langs;
        }
        return $result;
    }
    public function _getHeaderLangBlock()
    {
        $result = Cache::read('languages_nav', 'layout_elements');

        if (!$result){
            App::import("Model", "DomainLang");
            $model = new DomainLang();
            $langs = $model->find("all",
                array('conditions' => array(
                    'Domain.country_id' => $this->Session->read('Config.id_country'),
                    'DomainLang.domain_id'  => $this->Session->read('Config.id_domain'),
                    'Lang.active' => 1
                ),

                    'recursive' => 0
                )
            );

            Cache::write('languages_nav', $langs, 'layout_elements');
            return $langs;
        }
        return $result;
    }
    public function getHeaderLangBlock()
    {
        $langs = $this->_getHeaderLangBlock();
        $li = array();

        foreach ($langs AS $l){
            $li[]= '<li>'.
                $this->Html->link(
                    '<span class="'.(($this->Session->read('Config.language') == $l['Lang']['language_code'])?'lang_selected ':'').' lang_'.$l['Lang']['language_code'].' lang_flags">'.$l['Lang']['name'].'</span>',
                    array('language' => $l['Lang']['language_code'],
                        'controller' => 'home',
                        'action'    => 'index'),
                    array('escape' => false)
                ).'</li>';
        }

        return '<ul>'.implode("<li class=\"sep\"></li>", $li)."</ul>";
    }*/

    public function _getHeaderCountryBlock(){
        //Cache::delete('countries_nav', Configure::read('nomCacheNavigation'));
        $result = Cache::read('countries_nav', Configure::read('nomCacheNavigation'));

        if ($result === false){
            App::import("Model", "Domain");

			$namesite = Configure::read('Site.nameDomain');
			//if(Configure::read('Site.nameDomain') != 'spiriteo') $namesite = '';
			$idlang = 1;//$this->Session->read('Config.id_lang');

            $model = new Domain();
            $countries = $model->find('all', array(
                'fields' => array('Domain.domain', 'Domain.country_id', 'CountryLang.name', 'Domain.id', 'Lang.language_code'),
                'conditions' => array('Domain.active' => 1, 'Domain.domain LIKE' => '%'.$namesite.'%'),
                'joins' => array(
                    array(
                        'table' => 'countries',
                        'alias' => 'Country',
                        'type'  => 'inner',
                        'conditions' => array(
                            'Country.active = 1',
                            'Country.id = Domain.country_id'
                        )
                    ),
                    array(
                        'table' => 'country_langs',
                        'alias' => 'CountryLang',
                        'type'  => 'left',
                        'conditions' => array(
                            'CountryLang.country_id = Domain.country_id',
                            'CountryLang.id_lang = '.$idlang
                        )
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = Domain.default_lang_id')
                    )
                ),
                'order' => 'order_on_generiq_page ASC',
                'recursive' => -1
            ));
            $generiq_domains = explode(',',Configure::read('Site.id_domain_com'));

            //On supprime les domaines (.net, .org .info ...)
            $needle = array('.net', '.org', '.info', '.biz');
            //Pour chaque domaine
            foreach($countries as $key => $domain){
                //Pour chaque needle
                foreach($needle as $search){
                    //Si on trouve un des needle
                    if(stripos($domain['Domain']['domain'], $search) !== false){
                        unset($countries[$key]);
                        break;
                    }
                }
            }

            foreach ($countries AS $key => $domain){
                if (in_array($domain['Domain']['id'], $generiq_domains))
                    unset($countries[$key]);
            }


            Cache::write('countries_nav', $countries, Configure::read('nomCacheNavigation'));
            return $countries;
        }
        return $result;
    }
    public function getHeaderCountryBlock(){
        $countries = $this->_getHeaderCountryBlock();

        $html = '';
		$html_list = '<ul class="dropdown-menu">';

		foreach ($countries AS $domain){

			$name_flag_title = str_replace('Canada','Quebec',$domain['CountryLang']['name']);

			if($this->Session->read('Config.id_domain') == $domain['Domain']['id']){
				$uri = $this->getUrlInterSite($domain['Domain']['id']);
				if(!$uri)$uri='#';
				$html .= '<a title="agents '.$name_flag_title.'" href="'.$uri.'" class="dropdown-toggle main-drop" data-toggle="dropdown"><img src="/theme/default/img/flag/'.strtolower($domain['CountryLang']['name']).'.png" alt="Drapeau '.$domain['CountryLang']['name'].'"> '.$domain['CountryLang']['name'].' </a>';
			}else{
				$uri = $this->getUrlInterSite($domain['Domain']['id']);
				if(!$uri)$uri='#';
				$html_list .= '<li><a title="agents '.$name_flag_title.'" href="'.$uri.'"><span class="desk-flag"><img src="/theme/default/img/flag/'.strtolower($domain['CountryLang']['name']).'.png" alt="Drapeau '.$domain['CountryLang']['name'].'"></span> '.$domain['CountryLang']['name'].'</a></li>	';
			}
		}
		$html_list .= '</ul>';

        return $html.$html_list;
    }
	public function getHeaderCountryBlockMobile(){
        $countries = $this->_getHeaderCountryBlock();
        $html = '';
		$html_list = '<ul class="dropdown-menu">';
		foreach ($countries AS $domain){
            if($this->Session->read('Config.id_domain') == $domain['Domain']['id']){
				$html .= '<a href="https://'.$domain['Domain']['domain'].'/'.'" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents '.$domain['CountryLang']['name'].'"><img src="/theme/black_blue/img/flag/'.strtolower($domain['CountryLang']['name']).'.png" alt="Drapeau '.$domain['CountryLang']['name'].'">&nbsp;</a>';
			}else{
				$html_list .= '<li><a  href="https://'.$domain['Domain']['domain'].'/'.'" title="agents '.$domain['CountryLang']['name'].'"><span class="desk-flag"><img src="/theme/black_blue/img/flag/'.strtolower($domain['CountryLang']['name']).'.png" alt="Drapeau '.$domain['CountryLang']['name'].'"></span> '.substr($domain['CountryLang']['name'],0,2).'</a></li>	';
			}
		}
		$html_list .= '</ul>';

		/*SOFTPEOPLE POUR DEMO,  a SUPPRIMER */
		//if(empty($html.$html_list))
		   return  '<a style="color:white" href="https://talkto_php.local/" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents France">Fr <img src="/theme/black_blue/img/flag/france.png" alt="Drapeau France">&nbsp;</a>';
		
		
        return $html.$html_list;
    }

    private function isNoox()
    {
        return $_SERVER["REMOTE_ADDR"] == "109.190.94.104";
    }
    
    public function _getHeaderLangBlock(){
        $cacheAlias = 'languages_nav_'.$this->Session->read('Config.id_domain').'_'.$this->Session->read('Config.id_lang');
        $result = Cache::read($cacheAlias, Configure::read('nomCacheNavigation'));

        if ($result === false){
            App::import("Model", "DomainLang");
            $model = new DomainLang();




            /* On recupere les traductions des langues */
            App::import("Model", "LangLang");
            $ll = new LangLang();
            $trad = $ll->find("list", array('fields' => array('lang_id','name'),'conditions' => array('in_lang_id' => $this->Session->read('Config.id_lang'))));


            $langs = $model->find("all",
                array(
                    'conditions' => array(
                        'Domain.country_id' => $this->Session->read('Config.id_country'),
                        'DomainLang.domain_id'  => $this->Session->read('Config.id_domain'),
                        'Lang.active' => 1
                    ),
                    'recursive' => 0
                )
            );

            /* On remplace le nom de la langue par sa traduction */
            foreach ($langs AS $k => $v)
                if (isset($trad[$v['Lang']['id_lang']]))
                    $langs[$k]['Lang']['name'] = $trad[$v['Lang']['id_lang']];

            /*
            $langs = $model->find("all",
                array(
                    'conditions' => array(
                        'Domain.country_id' => $this->Session->read('Config.id_country'),
                        'DomainLang.domain_id'  => $this->Session->read('Config.id_domain'),
                        'Lang.active' => 1
                    ),
                    'recursive' => 0
                )
            );
            */

            Cache::write($cacheAlias, $langs, Configure::read('nomCacheNavigation'));
            return $langs;
        }
        return $result;
    }
    public function getHeaderLangBlock(){
        $langs = $this->_getHeaderLangBlock();
        //On génère le select
        $out = '<select name="user_languages" class="nxselect" id="user_languages" onchange="document.location.href = this.value">';
        foreach($langs as $lang){
            $out.= '<option value="'.$this->Html->url(array(
                    'language'  => $lang['Lang']['language_code'],
                    'controller' => 'home',
                    'action'    => 'index'
                ));

			$tab_l = explode('(',$lang['Lang']['name']);

            $out.= '" icon=":empty lang_flags lang_'.$lang['Lang']['language_code'].'" '.($this->Session->read('Config.id_lang') == $lang['Lang']['id_lang'] ?' selected':'').'>'.trim($tab_l[0]).'</option>';
        }
        $out.= '</select>';

        return $out;
    }

    public function formatPhoneNumber($phonestring="")
    {
        return $phonestring;
    }

    //Pour la pagination manuelle
    public function getPagination($page, $qteElements, $config, $params = array(), $categorie = false){
        $pagination = array();
        //Nombre d'elements par page
        $limitElement = Configure::read($config);
		if(!$limitElement)$limitElement = 20;
		if(!$page)$page = 1;
        //Paramètres pour le lien
        $link = array(
            'controller' => $params['controller'],
            'action' => $params['action'],
            'language' => $this->Session->read('Config.language')
        );

        if($categorie){
            //On récupère link_rewrite de la catégorie
            if(!empty($params['pass']) && strcmp($params['pass'][0],'1') != 0){
                //On récupère le link_rewrite
                $link_rewrite = $this->getCategorieLink($params['pass']['0']);
                if(!empty($link_rewrite)){
                    $link = array_merge(
                        array(
                            'id' => $params['pass']['0'],
                            'link_rewrite' => $link_rewrite['CategoryLang']['link_rewrite']
                        ),
                        $link
                    );
                }
            }
        }

        //Nombre de pages au total et derniere page du coup
        $pages = ceil($qteElements/$limitElement);

        $template = array(
            'pages' =>  $pages,
            'limit' =>  $page * $limitElement,                  //L'indice du dernier agent à afficher
            'first' =>  ($page-1) * $limitElement,              //L'indice du premier agent à afficher
            'prev'  =>  (($page-1) < 1?1:$page-1),              //Page précédente
            'next'  =>  (($page+1) > $pages?$pages:$page+1),    //Page suivante
            'link'  =>  $link                                   //Lien
        );

        foreach($template as $k => $val){
            $pagination[$k] = $val;
        }

        return $pagination;
    }

    //Pour la pagination automatique (Paginator)
    public function pagination($paginator){
        $html = '<div class="pull-right"><ul class="pagination">';
        if($paginator->param('pageCount') >= 3)
            $html.= $paginator->first(__('Début'),array('tag' => 'li'));
        if($paginator->param('prevPage'))
                $html.= $paginator->prev('< '.__('Précedent'),array('tag' => 'li'));
        $html.= $paginator->numbers(array('first' => 2,'last' => 2, 'separator' => false, 'tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'disabled', 'modulus' => 1, 'ellipsis' => '<li><a>...</a></li>'));
        if($paginator->param('nextPage'))
            $html.= $paginator->next(__('Suivant').' >',array('tag' => 'li'));
        if(($paginator->param('pageCount') - $paginator->param('page')) >= 3)
            $html.= $paginator->last(__('Fin'),array('tag' => 'li'));
        $html.= '</ul></div>';

        return $html;
    }


    public function parseTelStringForIndicatif($phone_number=false)
    {
        if (!$phone_number)return false;

        //Le model Country
        App::import("Model", "Country");
        $model = new Country();

        //La liste des indicatifs
        $indicatifs = $model->getIndicatifForSelect();

        foreach ($indicatifs AS $ind){
            if ($ind === substr($phone_number, 0, strlen($ind)))
                return array(
                    'phone' => substr($phone_number, strlen($ind), strlen($phone_number)),
                    'indicatif' => $ind
                );
        }
        return array(
            'phone' => $phone_number,
            'indicatif' => ''
        );
    }

    //Renvoie un input avec les indicatifs des pays
    public function getIndicatifTelInput($required = false, $selected = false, $class = false, $field_name='indicatif_phone'){
        //Le model Country
        App::import("Model", "Country");
        $model = new Country();

        //La liste des indicatifs
        $indicatifs = $model->getIndicatifForSelect();

        if(empty($indicatifs))
            return '';

        //Les options de base
        $options = array(
            'label'     => false,
            'div'       => false,
            'between'   => false,
            'empty'     => '--',
            'after'     => '<span class="ind_legend" style="margin-top: 5px;">'. __('Indicatif pays') .'</span>',
            'class'     => 'form-control ind-form',
            'options'   => $indicatifs
        );

        //Si une classe spécifique
        if($class !== false)
            $options['class'] = $class;
        //Si obligatoire
        if($required)
            $options['required'] = true;
        //Si une option sélectionnée
        if($selected !== false && isset($indicatifs[$selected]))
            $options['selected'] = $selected;

        $out = '<span class="ind_plus">+</span>'.$this->Form->input($field_name, $options);

        return $out;
    }

	    //Renvoie un input avec les indicatifs des pays
    public function getIndicatifTelInputIns($required = false, $selected = false, $class = false, $field_name='indicatif_phone'){
        //Le model Country
        App::import("Model", "Country");
        $model = new Country();

        //La liste des indicatifs
        $indicatifs = $model->getIndicatifForSelectIns();

        if(empty($indicatifs))
            return '';

        //Les options de base
        $options = array(
            'label'     => false,
            'div'       => false,
            'between'   => false,
            'empty'     => '--',
//            'after'     => '<span class="help ind_legend" style="margin-top: 5px;">'. __('Indicatif pays') .'</span>',
            'class'     => 'form-control form',
            'options'   => $indicatifs
        );

        //Si une classe spécifique
        if($class !== false)
            $options['class'] = $class;
        //Si obligatoire
        if($required)
            $options['required'] = true;
        //Si une option sélectionnée
        if($selected !== false && isset($indicatifs[$selected]))
            $options['selected'] = $selected;

        $out = $this->Form->input($field_name, $options);

        return $out;
    }


    //Renvoie la note d'un avis avec les étoiles
    public function getReviewRate($rate){
        /*$out = '';
        for($i=1; $i<=5; $i++){
            if($i <= $rate)
                $out.= '<span class="bigStar bigStar_enabled"></span>';
            else
                $out.= '<span class="bigStar bigStar_disabled"></span>';
        }

        return $out;*/

		$html = '<ul class="list-inline list-star">';
		$html .= '<li><i class="fa fa-star '; if($rate == 0.5) $html .= '-half-o'; if($rate >= 0.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 1.5) $html .= '-half-o'; if($rate >= 1.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 2.5) $html .= '-half-o'; if($rate >= 2.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 3.5) $html .= '-half-o'; if($rate >= 3.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		$html .= '<li><i class="fa fa-star '; if($rate == 4.5) $html .= '-half-o'; if($rate >= 4.5) $html .= 'star-selected'; else $html .=  'star-inactive'; $html .='"></i></li>';
		#$html .= '<i class="fa fa-star'; if($rate == 0.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 0.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		#$html .= '<i class="fa fa-star'; if($rate == 1.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 1.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		#$html .= '<i class="fa fa-star'; if($rate == 2.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 2.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		#$html .= '<i class="fa fa-star'; if($rate == 3.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 3.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		#$html .= '<i class="fa fa-star'; if($rate == 4.5) $html .= '-half-o'; $html .= '" style="color:#'; if($rate >= 4.5) $html .= 'ffa800'; else $html .=  'aaaaaa'; $html .='"></i>';
		$html .= '</ul>';
		return $html;

    }

	public function getHeaderMobileUserLogout(){
		$user = $this->Session->read('Auth.User');
        if (!empty($user)){
			echo '<div class="logout">
			<ul class="list-inline">
				<li class="hide">
					<div class="flag">
						'.$this->getHeaderCountryBlock().'
					</div>
				</li>
				<li>
					<a href="/logout" class="btn btn-default logout-btn btn-xs"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp;'. __('Déconnexion').'</a>
				 </li>
			</div>';
    	}
	}

	public function getHeaderMobileMenuTop(){
		$user = $this->Session->read('Auth.User');
			$params = $this->request->params;

		echo '<div class="visible-xs mobile-filter">
				<div class="container-fluid">
					<ul class="list-inline">';

		//ajouter class active dans le li quand on est sur la page
		$css_act_horo = '';
		$css_act_cone = '';
		$css_act_compt = '';
		$css_act_sub = '';
		$css_act_achet = '';

		if($params['controller'] == 'accounts' && $params['action'] != 'buycredits')$css_act_compt = 'active';
		if($params['controller'] == 'horoscopes')$css_act_horo = 'active';
		if($params['controller'] == 'users' && $params['action'] == 'login')$css_act_cone = 'active';
		if($params['controller'] == 'users' && $params['action'] == 'subscribe')$css_act_sub = 'active';
		if($params['controller'] == 'accounts' && $params['action'] == 'buycredits')$css_act_achet = 'active';

		if($params['controller'] != 'home'){//if($params['action'] == 'buycredits' || $params['controller'] == 'horoscopes'){
			echo '<li class="home"><a href="/"><i class="glyphicon glyphicon-home"></i> <span>'.__('ACCUEIL').'</span></a></li>';
		}else{

			if($params['controller'] == 'home'){
				echo '<li class="search"><a href="#mobileCollapse" data-toggle="collapse" aria-expanded="false" aria-controls="mobileCollapse"><i class="glyphicon glyphicon-search"></i> <span>'.__('RECHERCHE').'</span></a></li>';
			}else{
				echo '<li class="search "><a href="/"><i class="glyphicon glyphicon-search"></i> <span>'.__('RECHERCHE').'</span></a></li>';
			}
		}
		if (!empty($user)){
			echo '<li class="account '.$css_act_compt.' user-logged dropdown dropdown-accordion" data-accordion="#accordion"><a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> <span>'.__('COMPTE').'</span></a>'.$this->getHeaderUserBlockMobile().'</li>';
		}else{
			echo '<li class="connection '.$css_act_cone.'"><a href="#" data-toggle="modal" data-target="#connection"><i class="glyphicon glyphicon-log-in"></i> <span>'.__('CONNEXION').'</span></a></li>';
		}

		echo '<li class="horoscope '.$css_act_horo.'"><a href="/horoscopes/index"><i class="glyphicon glyphicon-star"></i> <span>'.__('HOROSCOPE').'</span></a></li>';

		if (!empty($user)){
			echo '<li class="acheter '.$css_act_achet.'"><a href="'.$this->getProductsLink().'"><i class="glyphicon glyphicon-shopping-cart"></i> <span>'.__('ACHETER').'</span></a></li>';
		}else{
			echo '<li class="subscribe '.$css_act_sub.'"><a href="/users/subscribe"><span>'.__('Inscription <br/> gratuite!').'</span></a></li>';
		}


		echo'</ul>
				</div>
			</div>';
	}

	public function getHeaderMobileFilterAgent($filters = array()){
		$filtre_active = '';
									foreach ($filters['filter_orderby'] AS $alias => $parms){
										if (!empty($parms['enabled'])){
											if(!empty($parms['active']))
												$filtre_active = $alias;
										}
									}
		echo '<a class="close_search_filters_mobile"></a><form class="form-horizontal" id="filters_form_mobile"><div class="advance-option" id="search_filters_mobile">';

		echo '<div class="search-input">
						<div class="input-group">
							<input type="text" class="nxselectmobile search-query form-control" placeholder="'.__('Rechercher un expert').'" name="sf_term_mobile" id="sf_term_mobile">
							<span class="input-group-btn">
								<button class="btn" type="submit">
									<span class="glyphicon glyphicon-search"></span>
								</button>
							</span>
						</div>
                 </div>';

		echo '<div class="filtre_container">
						<p class="title">compétences</p>
						<div class="row list-group filtre-category" data-toggle="items">
							<a href="#" class="list-group-item">Voyants</a>
							<a href="#" class="list-group-item">Mediums</a>
							<a href="#" class="list-group-item">Tarologues</a>
							<a href="#" class="list-group-item">Astrologues</a>
							<a href="#" class="list-group-item">Cartomanciens</a>
							<a href="#" class="list-group-item">Numerologues</a>
							<a href="#" class="list-group-item">Magnetiseurs</a>
							<a href="#" class="list-group-item">Channeling</a>
						</div>
					</div>';
		echo '<div class="filtre_container">
								<p class="title">additionnels</p>
								<div class="row list-group filtre-addy" data-toggle="items">
									<a class="list-group-item filtre-consult ';
		if(isset($filtre_active) && $filtre_active == 'nombreconsult') echo 'active';
		echo '"> <span class="rel">nombreconsult</span>
										<span class="type">orderby</span>Les plus consultés</a>
									<a class="list-group-item filtre-note ';
		if(isset($filtre_active) && $filtre_active == 'meilleuresnotes') echo 'active';
		echo '"><span class="rel">meilleuresnotes</span>
										<span class="type">orderby</span>Les mieux notés</a>
									<a class="list-group-item  ';
		if(isset($filtre_active) && $filtre_active == 'newagents') echo 'active';
		echo '"><span class="rel">newagents</span>
										<span class="type">filterby</span>Les nouveaux experts</a>

								</div>
							</div>';

		echo '<div class="filtre_container">
								<p class="title">consulter par</p>
								<div class="row list-group filtre-mode-content" data-toggle="items">
									<a class="list-group-item filtre-mode ';
		if(isset($filtre_active) && $filtre_active == 'telephone') echo 'active';
		echo '"><span class="type">sf_media_phone</span>Téléphone</a>
									<a class="list-group-item filtre-mode ';
		if(isset($filtre_active) && $filtre_active == 'chat') echo 'active';
		echo '"><span class="type">sf_media_chat</span>Chat</a>
									<a class="list-group-item filtre-mode';
		if(isset($filtre_active) && $filtre_active == 'email') echo 'active';
		echo '"><span class="type">sf_media_email</span>Email</a>

		</div>
							</div>';



		echo '</div></form>';
	}

	 public function getFooterMobileCountryBlock(){
        $countries = $this->_getHeaderCountryBlock();

		echo '<ul class="list-unstyled list-inline country-flag">';
        foreach ($countries AS $domain){
			echo '<li class="'.(($this->Session->read('Config.id_domain') == $domain['Domain']['id'])?'country_selected ':'').'"><a href="https://'.$domain['Domain']['domain'].'/'.'" title="agents '.$domain['CountryLang']['name'].'"><span class="desk-flag"><img src="/theme/default/img/flag/mobile/'.strtolower($domain['CountryLang']['name']).'.jpg" alt="Drapeau '.$domain['CountryLang']['name'].'" /></span></a></li>';
        }
		echo '</ul>';
    }

	public function getFooterMobileLinkBlock(){

		$lang_id = $this->Session->read('Config.id_lang');

		echo ' <ul class="list-inline foot-list visible-xs">';

        if (empty($langage_code)){
            $langage_code = $this->Session->read('Config.language');
        }

        if (empty($langage_code))return false;

        //$seo_words_from_lang_code = Configure::read('Routing.pages');

		App::import("Model", "Lang");
        $model = new Lang();
		$r = $model->find('first',array(
            'conditions' => array('Lang.language_code' => $langage_code),
            'fields' => array('Lang.id_lang'),
            'recursive' => -1
        ));

		$id_lang = $r['Lang']['id_lang'];


		App::import("Model", "Page");
        $model = new Page();
		$r = $model->find('first',array(
            'conditions' => array('Page.id' => 32),
            'fields' => array('Page.page_category_id'),
            'recursive' => -1
        ));
		$id_page_category = $r['Page']['page_category_id'];

		App::import("Model", "PageLang");
        $model = new PageLang();
		$r = $model->find('first',array(
            'conditions' => array('PageLang.page_id' => 32, 'PageLang.lang_id' => $id_lang),
            'fields' => array('PageLang.link_rewrite'),
            'recursive' => -1
        ));
		$link_rewrite = $r['PageLang']['link_rewrite'];

		App::import("Model", "PageCategoryLang");
        $model = new PageCategoryLang();
		$r = $model->find('first',array(
            'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $id_lang),
            'fields' => array('PageCategoryLang.name'),
            'recursive' => -1
        ));
		$name_page_category =  '';
		if(array_key_exists('PageCategoryLang', $r)){
			$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
		}

		$l = '/'.$langage_code.'/'.$link_rewrite;
		echo '<li style="padding-right: 15px; padding-left: 15px;"><a href="'.$l.'"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> <p>'.__('CONTACT & SUPPORT').'</p></a></li>';

		App::import("Model", "Page");
        $model = new Page();
		$r = $model->find('first',array(
            'conditions' => array('Page.id' => 13),
            'fields' => array('Page.page_category_id'),
            'recursive' => -1
        ));
		$id_page_category = $r['Page']['page_category_id'];

		App::import("Model", "PageLang");
        $model = new PageLang();
		$r = $model->find('first',array(
            'conditions' => array('PageLang.page_id' => 13, 'PageLang.lang_id' => $id_lang),
            'fields' => array('PageLang.link_rewrite'),
            'recursive' => -1
        ));
		$link_rewrite = $r['PageLang']['link_rewrite'];

		App::import("Model", "PageCategoryLang");
        $model = new PageCategoryLang();
		$r = $model->find('first',array(
            'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $id_lang),
            'fields' => array('PageCategoryLang.name'),
            'recursive' => -1
        ));

		$name_page_category =  '';
		if($r['PageCategoryLang']['name']){
			$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
		}

		$l = '/'.$langage_code.'/'.$name_page_category.'/'.$link_rewrite;
		echo '<li><a href="'.$l.'"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <p>'.__('PROTECTION DES DONNÉES').'</p></a></li>';

		App::import("Model", "Page");
        $model = new Page();
		$r = $model->find('first',array(
            'conditions' => array('Page.id' => 1),
            'fields' => array('Page.page_category_id'),
            'recursive' => -1
        ));
		$id_page_category = $r['Page']['page_category_id'];

		App::import("Model", "PageLang");
        $model = new PageLang();
		$r = $model->find('first',array(
            'conditions' => array('PageLang.page_id' => 1, 'PageLang.lang_id' => $id_lang),
            'fields' => array('PageLang.link_rewrite'),
            'recursive' => -1
        ));
		$link_rewrite = $r['PageLang']['link_rewrite'];

		App::import("Model", "PageCategoryLang");
        $model = new PageCategoryLang();
		$r = $model->find('first',array(
            'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $id_lang),
            'fields' => array('PageCategoryLang.name'),
            'recursive' => -1
        ));

		$name_page_category =  '';
		if($r['PageCategoryLang']['name']){
			$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
		}

		$l = '/'.$langage_code.'/'.$name_page_category.'/'.$link_rewrite;

		echo '<li><a href="'.$l.'"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> <p>'.__('CONDITIONS GÉNÉRALES').'</p></a></li>';
		echo '</ul>';
	}

		public function slugify($str)
	{

		$str = strip_tags($str);
		$str = $this->remove_accents($str);
		$str = preg_replace('/[\r\n\t ]+/', ' ', $str);
		$str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
		$str = strtolower($str);
		$str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
		$str = htmlentities($str, ENT_QUOTES, "utf-8");
		$str = str_replace("&amp;", 'et', $str);
		$str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
		$str = str_replace(' ', '-', $str);
		$str = rawurlencode($str);
		$str = str_replace('%', '-', $str);
		return $str;
	}

	public function remove_accents($string) {
	   if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		 if ($this->seems_utf8($string)) {
		   $chars = array(
			// Decompositions for Latin-1 Supplement
			chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
			chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
			chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
			chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
			chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
			chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
			chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
			chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
			chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
			chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
			chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
			chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
			chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
			chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
			chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
			chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Decompositions for Latin Extended-B
			chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
			chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '',
			// Vowels with diacritic (Vietnamese)
			// unmarked
			chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
			chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
			// grave accent
			chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
			chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
			chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
			chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
			chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
			chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
			chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
			// hook
			chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
			chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
			chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
			chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
			chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
			chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
			chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
			chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
			chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
			chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
			chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
			chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
			// tilde
			chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
			chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
			chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
			chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
			chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
			chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
			chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
			chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
			// acute accent
			chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
			chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
			chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
			chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
			chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
			chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
			// dot below
			chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
			chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
			chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
			chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
			chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
			chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
			chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
			chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
			chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
			chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
			chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
			chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin)
			chr(201).chr(145) => 'a',
			// macron
			chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
			// acute accent
			chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
			// caron
			chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
			chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
			chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
			chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
			chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
			// grave accent
			chr(199).chr(155) => 'U', chr(199).chr(156) => 'u',
			);

			// Used for locale-specific rules
				$chars[ chr(195).chr(134) ] = 'Ae';
				$chars[ chr(195).chr(166) ] = 'ae';
				$chars[ chr(195).chr(152) ] = 'Oe';
				$chars[ chr(195).chr(184) ] = 'oe';
				$chars[ chr(195).chr(133) ] = 'Aa';
				$chars[ chr(195).chr(165) ] = 'aa';

			$string = strtr($string, $chars);
		} else {
			$chars = array();
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars = array();
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
		return $string;
	}

	public function seems_utf8($str) {
		/*mbstring_binary_safe_encoding();
		$length = strlen($str);
		reset_mbstring_encoding();
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; // 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; // 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; // 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; // 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; // 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; // 1111110b
			else return false; // Does not match any model
			for ($j=0; $j<$n; $j++) { // n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}*/
		return true;
	}

	public function getPaginateLine($pagination){

		$page_nb = $pagination['pages'];
		if($pagination['first'] == 0)
			$page_actuel = 1;
		else
			$page_actuel = $pagination['prev'] + 1;

		$page_prev = $page_actuel - 1;
		$page_prev2 = $page_actuel - 2;
		$page_next = $page_actuel + 1;
		$page_next2 = $page_actuel + 2;
		$page_first = 1;
		$page_last = $pagination['pages'];

		//var_dump($pagination);

		$html = '
                    <div class="text-center">
			  			<ul class="list-inline pagination">
                        	<li';
		if($page_actuel < 2) $html .=  ' class="active"';

		$html .='>'.$this->Html->link($page_first, array_merge($pagination['link'],array('page' => $page_first))).'</li>';
        if($page_actuel >= 4):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		if($page_prev2 > 1):
        	$html .= '<li>'.$this->Html->link($page_prev2, array_merge($pagination['link'],array('page' => $page_prev2))).'</li>';
        endif;

        if($page_prev > 1 && $page_prev > $page_prev2):
        	$html .='<li>'.$this->Html->link($page_prev, array_merge($pagination['link'],array('page' => $page_prev))).'</li>';
        endif;

        if($page_actuel > 1):
        	$html .= '<li class="active">'.$this->Html->link($page_actuel, array_merge($pagination['link'],array('page' => $page_actuel))).'</li>';
        endif;

        if($page_next > $page_actuel && $page_next <= $page_nb):
        	$html .='<li>'.$this->Html->link($page_next, array_merge($pagination['link'],array('page' => $page_next))).'</li>';
        endif;

		if($page_next2 > $page_next && $page_next2 <= $page_nb):
        	$html .= '<li>'.$this->Html->link($page_next2, array_merge($pagination['link'],array('page' => $page_next2))).'</li>';
        endif;

		if($page_last >= 5 && $page_next2 < $page_last):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		if($page_last >= 5 && $page_next2 < $page_last):
        	$html .='<li>'.$this->Html->link($page_last, array_merge($pagination['link'],array('page' => $page_last))).'</li>';
        endif;
        $html .='</ul>
                    </div>
               ';
		return $html;
	}

	public function getPaginateCategory($pagination,$params){



		$page_nb = $pagination['pages'];
		if($pagination['first'] == 0)
			$page_actuel = 1;
		else
			$page_actuel = $pagination['prev'] + 1;

		$page_prev = $page_actuel - 1;
		$page_prev2 = $page_actuel - 2;
		$page_next = $page_actuel + 1;
		$page_next2 = $page_actuel + 2;
		$page_first = 1;
		$page_last = $pagination['pages'];

		if($page_actuel < $page_last)
		$html = '<div class="pagination-mobile"><span class="paginate-button expert"> <i class="fa fa-angle-down"></i> '.__('Voir plus d\'experts').'</span></div>';

		//var_dump($pagination);

		/*$html .= '
                    <div class="text-center pagination-categories">
			  			<ul class="list-inline pagination">
                        	<li';
		if($page_actuel < 2) $html .=  ' class="active"';

		$link = $this->Html->link($page_first, array_merge(array_merge($params,array('page' => $page_first)),array('?' => $pagination['link'])));
		$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
		$link = str_replace('-1','',$link);

		$html .='>'.$link.'</li>';
        if($page_actuel >= 5):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		if($page_prev2 > 1):
			$link = $this->Html->link($page_prev2, array_merge(array_merge($params,array('page' => $page_prev2)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
			$link = str_replace('-1','',$link);
        	$html .= '<li>'.$link.'</li>';
        endif;

        if($page_prev > 1 && $page_prev > $page_prev2):
			$link = $this->Html->link($page_prev, array_merge(array_merge($params,array('page' => $page_prev)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
			$link = str_replace('-1','',$link);
        	$html .='<li>'.$link.'</li>';
        endif;

        if($page_actuel > 1):
			$link = $this->Html->link($page_actuel, array_merge(array_merge($params,array('page' => $page_actuel)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
        	$html .= '<li class="active">'.$link.'</li>';
        endif;

        if($page_next > $page_actuel && $page_next <= $page_nb):
			$link = $this->Html->link($page_next, array_merge(array_merge($params,array('page' => $page_next)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
        	$html .='<li>'.$link.'</li>';
        endif;

		if($page_next2 > $page_next && $page_next2 <= $page_nb):
			$link = $this->Html->link($page_next2, array_merge(array_merge($params,array('page' => $page_next2)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
        	$html .= '<li>'.$link.'</li>';
        endif;

		if($page_last >= 5 && $page_next2 < $page_last):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		if($page_last >= 4 && $page_next2 < $page_last):
			$link = $this->Html->link($page_last, array_merge(array_merge($params,array('page' => $page_last)),array('?' => $pagination['link'])));
			$link = str_replace($this->Session->read('Config.language').'/category-display-1','',$link);
        	$html .='<li>'.$link.'</li>';
        endif;
        $html .='</ul>
                    </div>
               ';*/
		return $html;
	}

	public function getPaginateLoad($pagination,$params, $who){



		$page_nb = $pagination['pages'];
		if($pagination['first'] == 0)
			$page_actuel = 1;
		else
			$page_actuel = $pagination['prev'] + 1;

		$page_prev = $page_actuel - 1;
		$page_prev2 = $page_actuel - 2;
		$page_next = $page_actuel + 1;
		$page_next2 = $page_actuel + 2;
		$page_first = 1;
		$page_last = $pagination['pages'];

		if($page_actuel < $page_last)
		$html = '<div class="pagination-mobile"><span class="paginate-button '.$who.'"> <i class="fa fa-angle-down"></i> '.__('Voir plus').'</span></div>';


		return $html;
	}

	public function getPaginateEnligne($nb, $offset){



		$page_nb = ceil($nb / 15) - 1;
		if($offset == 0)
			$page_actuel = 1;
		else
			$page_actuel = $offset;//ceil($offset / 5);

		$page_prev = $page_actuel - 1;
		$page_prev2 = $page_actuel - 2;
		$page_next = $page_actuel + 1;
		$page_next2 = $page_actuel + 2;
		$page_first = 1;
		$page_last = $page_nb;

		//var_dump($pagination);

		$html = '
                    <div class="text-center">
			  			<ul class="list-inline pagination">
                        	<li';
		if($page_actuel < 2) $html .=  ' class="active"';

		$html .='><a href="#">'.$page_first.'</a></li>';
        if($page_actuel >= 4):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		/*if($page_prev2 > 1):
        	$html .= '<li><a href="#">'.$page_prev2.'</a></li>';
        endif;*/

        if($page_prev > 1 && $page_prev > $page_prev2):
        	$html .='<li><a href="#">'.$page_prev.'</a></li>';
        endif;

        if($page_actuel > 1):
        	$html .= '<li class="active"><a href="#">'.$page_actuel.'</a></li>';
        endif;

        if($page_next > $page_actuel && $page_next <= $page_nb):
        	$html .='<li><a href="#">'.$page_next.'</a></li>';
        endif;

		/*if($page_next2 > $page_next && $page_next2 <= $page_nb):
        	$html .= '<li><a href="#">'.$page_next2.'</a></li>';
        endif;*/

		if($page_last >= 5 && $page_next2 < $page_last):
        	$html .='<li class="more-pages"><span>...</span></li>';
        endif;

		if($page_last >= 5 && $page_next < $page_last):
        	$html .='<li><a href="#">'.$page_last.'</a></li>';
        endif;
        $html .='</ul>
                    </div>
               ';
		return $html;
	}


	public function getPaginateObj($pagination){


		$html = $pagination->numbers(array('before' => '<div class="page text-center fadeIn animated" style="visibility: visible;-webkit-animation-delay: 0.4s; -moz-animation-delay: 0.4s; animation-delay: 0.4s;">
<ul class="list-inline pagination">', 'after' => '</ul></div>', 'tag' => 'li', 'currentClass' => 'active', 'separator' => '', 'modulus' => 4, 'currentTag' => 'a'));
		return $html;
	}

	public function getPaginateObjPage($pagination){

		$private = 0;
		$archive = 0;
		if(isset($this->params->query['private']))$private = 1;
		if(isset($this->request->data)){
			$param = $this->request->data;
			if($param['param'] == 'archive' )$archive = 1;
			if($param['param'] == 'private' )$private = 1;
		}
		$html = '';
		if(!$archive){

		$html = $pagination->numbers(array('before' => '<div class="page text-center fadeIn animated" style="visibility: visible;-webkit-animation-delay: 0.4s; -moz-animation-delay: 0.4s; animation-delay: 0.4s;">
<ul class="list-inline pagination">', 'after' => '</ul></div>', 'tag' => 'li', 'currentClass' => 'active', 'separator' => '', 'modulus' => 4, 'currentTag' => 'a'));
		$html = str_replace('mails-','mails/page/',$html);
		$html = str_replace('getMails-','mails/page/',$html);
		if($private){
			for($xx=1;$xx<100;$xx++){
				$html = str_replace($xx.'">'.$xx,$xx.'?private=1">'.$xx,$html);
			}
		}
		}
		return $html;
	}

	public function getAgentStatusBlock($agentStatus = ''){
		if($agentStatus && $this->request->isMobile()){
        	echo  $this->getAgentStatusMenu($agentStatus);
			echo  $this->getAgentOptions().'<br />';
		}
	}

	public function getRightSidebar($agentStatus = '', $title_barre = 'Nos experts'){



		$user = $this->Session->read('Auth.User');

		$html = '<aside class="col-sm-12 col-md-3 account-sidebar hidden-sm hidden-xs">';

		if ($user['role'] == 'client' && !$agentStatus){
                $html .= '<div class="widget account-logged user-logged mb10">
			<div class="panel-group dd-menu mb0" id="menuAccordion">'.$this->getAccountSubmenuRight().'</div></div>';

			$html .= '<div class="account-logged user-logged mb10">
			<div class="panel-group dd-menu mb0" id="blockGain">'.$this->getAccountGain().'</div></div>';

			/*$html .= '<div class="widget account-logged user-logged mb10">
			<div class="panel-group dd-menu mb0" id="blockLoyalty">'.$this->getAccountLoyalty().'</div></div>';

			$html .= '<div class="widget account-logged user-logged mb10">
			<div class="panel-group dd-menu mb0" id="blockSponsorship">'.$this->getAccountClientSponsorship().'</div></div>';*/

		}elseif ($user['role'] == 'agent'){
                $html .= '<div class="widget account-logged user-logged mb10">
			<div class="panel-group dd-menu mb0" id="menuAccordion">'.$this->getAgentSubmenuRight().'</div></div>';


		}

		App::import("Model", "User");
        $user_rqt = new User();

		$expert = $user_rqt->find('first', array(
			'fields' => array('User.id','User.rib','User.iban'),
            'conditions' => array(
                'id' => $user['id'],
            ),
            'recursive' => -1
        ));

		$iban = false;
		if($expert['User']['iban'] || $expert['User']['rib']){
			$iban = true;
		}

		if($user['role'] == 'agent'){
			if(!$iban)$html .= '<div class="div_readonly"><p>'._('Vos modes de consultation sont inaccessibles tant que vous n\'avez pas renseigné vos coordonnées de paiement').'</p>';
			$html .= $this->getAgentOptions();
			if($agentStatus && !$this->request->isMobile())
        	$html .=  $this->getAgentStatusMenu($agentStatus);
			if(!$iban)$html .= '</div>';
			$html .= '<div class="account-logged user-logged mb10">
			<div class="panel-group dd-menu mb10" id="blockSponsorship">'.$this->getAccountAgentSponsorship().'</div></div>';

			/*$html .= '<div class="widget mb10">
			<div class="panel-group dd-menu mb0" id="menuAccordion">'.$this->getAccountBonus().'</div></div>';

			$html .= '<div class="widget mb10">
			<div class="panel-group dd-menu mb0" id="menuAccordion2">'.$this->getAccountRemuneration().'</div></div>';*/



		}

		if (!$user || $user['role'] != 'agent'){

			$offset = 0;
			$nbview = 15;
			$agentlist = $this->getAgentBusyData($offset, $nbview);
			$nb = count($this->getAgentBusy());

           $html .= ' <div class="experts-online mb20">
            	<div class="widget">
                	<div class="widget-title text-center">';//<span class="bold-number">'.$nb .' </span>
					/*if(count($agentlist) > 1)
					$html .= 'experts';
					else
					 $html .= 'expert';*/
					// $html .= ' en ligne</div>
					 $html .= $title_barre.'</div>
                    <ul class="online-list">';
                    foreach($agentlist as $agent ){

						$fiche_link = $this->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
								'agent_number'  => $agent['User']['agent_number']
							),
							array(
								'title'         => $agent['User']['pseudo']
							)
						);

						 if ($agent['User']['agent_status'] == 'available'){
							$set_title = __('Disponible');
							$set_title_css = 'available';
						}elseif ($agent['User']['agent_status'] == 'busy'){
							$set_title = __('En consultation').'  <span class="depuis_widget">depuis '.$this->secondsToHis($agent[0]['second_from_last_status']).'</span>';
							$set_title_css = 'consultation';

						}elseif ($agent['User']['agent_status'] == 'unavailable'){
							$set_title = __('Indisponible');
							$set_title_css = 'retour';
						}

						$csstooltip = 'tooltip';
						if($this->request->isMobile())$csstooltip = '';

                    	$html .= '<li class="fadeIn"><a href="'.$fiche_link.'" title="agents en ligne '.$agent['User']['pseudo'].'">

                            <div class="online-name">
                                <span class="uppercase">
                                    <span class="inline-block h4">'.$agent['User']['pseudo'].'</span>
                                </span>
                                <span class="name-flag">';
								$userLangs = explode(",",$agent['User']['langs']);

								App::import("Model", "Lang");
								$model = new Lang();
								$langs = $model->find("list", array(
									'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
									'conditions'    => array('Lang.active' => 1),
									'recursive' => -1
								));
                                    foreach ($userLangs AS $idLang){
										if (isset($langs[$idLang]) && $idLang != 8 && $idLang != 10 && $idLang != 11 && $idLang != 12 ){
											$tmp = array_values($langs[$idLang]);
											$html .=  '<i class="lang_flags lang_'.key($langs[$idLang]).' " title="'.$tmp[0].' '.__('parlé couramment').'" data-original-title="'.$tmp[0].' '.__('parlé couramment').'" data-toggle="tooltip"></i>';
										}
									}
                                $html .= '</span>
                            </div>

                            <div class="row">
                                <div class="col-sm-5 pr5">
                                <div class="online-expert-pic">
                                    <div class="sm-sid-photo"><span>';

									$html .= $this->Html->image($this->getAvatar($agent['User']), array(
                                                'alt' => 'agents en ligne '.$agent['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle status-'.$agent['User']['agent_status']
                                                ));
									$html .= '</span></div>';



									if($agent['User']['reviews_avg']){
										$html .= '<p class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score">'.number_format($agent['User']['reviews_avg'],1).'</span></p>';
									}
                                $html .= '</div>
                                </div><!--col-sm-5 END-->

                                <div class="col-sm-7 pr0 pl0 action-box-min">
                                    <div class="status-box">
                                        <!--<p class="status '.$set_title_css.'">'.$set_title.'</p>-->
                                       <ul class="list-inline medium-btn';
										//if($agent['User']['agent_status'] == 'busy') $html .= ' alert-btn ';
										$html .= '">';
										$css_bloc_busy = '';
										if($agent['User']['agent_status'] == 'busy'){
											$agent_busy_mode = $this->agentModeBusy($agent['User']['id']);
											$css_bloc_busy = $agent_busy_mode;
										}
										if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'phone')
													$css_phone = ' t-'.$agent['User']['agent_status'];
												else
													$css_phone = ' disabled';
											}else{
												$css_phone = ' t-'.$agent['User']['agent_status'];
											}

										}else{
											$css_phone = ' disabled';
										}
										if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'tchat')
													$css_tchat = 'c-'.$agent['User']['agent_status'];
												else
													$css_tchat = ' disabled';
											}else{
												$css_tchat = 'c-'.$agent['User']['agent_status'];
											}

										}else{
											$css_tchat = ' disabled';
										}

										if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												$css_email = 'm-available';
											}else{
												$css_email = 'm-'.$agent['User']['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}




											/*	if($agent['User']['agent_status'] == 'busy'){
												$html .= '	 <li class="alert-li"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Recevoir une alerte sms/email" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" class="alerte-a aebutton nx_openinlightbox nxtooltip">Recevoir une<br />alerte sms/email</a></li>';
												}else{*/
												$html .= '<li class="tel '.$css_phone. '">';

                                                if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):
													$lien = 'agents par téléphone';
                                                    /* $lien = $this->Html->url(
												array(
													'controller' => 'home',
                                                                    'action' => 'media_phone'
												)
											);*/
											$html .=  '<div data-toggle="'.$csstooltip.'" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span></div>';


                                                        else:

															$html .=  '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon"><p>Tel</p></div>';
                                                        endif;
                                                $html .= '</li>';

                                                $html .= '<li class="chat '.$css_tchat. '">';
                                                    if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])):
														$lien = 'agents par tchat - '.$agent['User']['id'];

													/* $lien = $this->Html->url(
												array(
													'controller' => 'chats',
                                                    'action' => 'create_session',
                                                    'id' => $agent['User']['id']
												)
											);*/
											$html .=  '<div data-toggle="'.$csstooltip.'" data-placement="top" title="Chat" class="nx_chatboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tchat</p></div>';

                                                        else:

															$html .=  '<div data-toggle="tooltip" data-placement="top" title="Chat" class="aicon"><p>Tchat</p></div>';
                                                        endif;
                                                $html .= '</li>';
												 $html .= '<li class="mail '.$css_email. '">';

                                                if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):
													$lien = 'agents par mail - '.$agent['User']['id'];
                                                   /*  $lien = $this->Html->url(
												array(
													'controller' => 'accounts',
													'action' => 'new_mail',
													'id' => $agent['User']['id']
												)
											);*/
											$html .= '<div  data-toggle="'.$csstooltip.'" data-placement="top" title="Email" class="nx_emailboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Email</p></div>';

                                                        else:
															$html .= '<div data-toggle="tooltip" data-placement="top" title="Email" class="aicon"><p>Email</p></div>';
                                                        endif;
                                                $html .= '</li>';
												//}
                                        $html .= '</ul>
                                    </div><!--code-box END-->
									';
								if ($agent['User']['agent_status'] == 'busy'){
									$html .= '<div  class="action-box-busy '.$css_bloc_busy.'" ><span class="hidden-xs">'.__('depuis ').$this->secondsToHis($agent['0']['second_from_last_status']).'</div>';
								}
                               $html .= ' </div><!--col-sm-7 END-->
                            </div><!--row END-->
    						</a>
                        </li>';
                     }
                 $html .= ' </ul>
                </div><!--widget End-->
               '.$this->getPaginateEnligne($nb, $offset).'
			</div><!--experts-online END-->
			';
		}
			$html .= '</aside>';

		return $html;
	}

    public function getRightSidebar2($agentStatus = '') {
		$user = $this->Session->read('Auth.User');

		$html = '';

        if ($user['role'] == 'client' && !$agentStatus) {
            $html .= '<div class="widget account-logged user-logged mb10">
                <div class="panel-group dd-menu mb0" id="menuAccordion">'.$this->getAccountSubmenuRight().'</div></div>';

			$html .= '<div class="account-logged user-logged mb10">
                <div class="panel-group dd-menu mb0" id="blockGain">'.$this->getAccountGain().'</div></div>';
		} elseif ($user['role'] == 'agent' ){
            $html .= '<div class="widget account-logged user-logged mb10">
                    <div class="panel-group dd-menu mb0" id="menuAccordion">'.$this->getAgentSubmenuRight().'</div></div>';
		}

		App::import("Model", "User");
        $user_rqt = new User();

		$expert = $user_rqt->find('first', array(
			'fields' => array('User.id','User.rib','User.iban'),
            'conditions' => array(
                'id' => $user['id'],
            ),
            'recursive' => -1
        ));

		$iban = false;
		if (!empty($expert['User']) && ($expert['User']['iban'] || $expert['User']['rib'])) {
			$iban = true;
		}

		if ($user['role'] == 'agent') {
			if (!$iban) {
                $html .= '<div class="div_readonly"><p>' . _('Vos modes de consultation sont inaccessibles tant que vous n\'avez pas renseigné vos coordonnées de paiement') . '</p>';
            }

			$html .= $this->getAgentOptions();
			if ($agentStatus && !$this->request->isMobile()) {
                $html .=  $this->getAgentStatusMenu($agentStatus);
            }
			if (!$iban) {
                $html .= '</div>';
            }
			$html .= '<div class="account-logged user-logged mb10">
			<div class="panel-group dd-menu mb10" id="blockSponsorship">' . $this->getAccountAgentSponsorship() . '</div></div>';
		}

		if (!$user || $user['role'] != 'agent'){
			$offset = 0;
			$nbview = 15;
			$agentlist = $this->getAgentBusyData($offset, $nbview);
			$nb = count($this->getAgentBusy());


            $html .= '<div class="avwr-wrapper">';
            $html .= '<div class="avwr-title">' . __('Experts similaires') . '</div>';
            $html .= '<div class="avwr-content">';

			$html .= '<div class="experts-list2">';
            foreach ($agentlist as $agent) {
                $fiche_link = $this->Html->url(
                    array(
                        'language'      => $this->Session->read('Config.language'),
                        'controller'    => 'agents',
                        'action'        => 'display',
                        'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
                        'agent_number'  => $agent['User']['agent_number']
                    ),
                    array(
                        'title'         => $agent['User']['pseudo']
                    )
                );

                if ($agent['User']['agent_status'] == 'available') {
                    $set_title = __('Disponible');
                    $set_title_css = 'available';
                } elseif ($agent['User']['agent_status'] == 'busy') {
                    $set_title = __('En consultation').'  <span class="depuis_widget">depuis ' . $this->secondsToHis($agent[0]['second_from_last_status']) . '</span>';
                    $set_title_css = 'consultation';
                } elseif ($agent['User']['agent_status'] == 'unavailable') {
                    $set_title = __('Indisponible');
                    $set_title_css = 'retour';
                }

                $csstooltip = 'tooltip';
                if ($this->request->isMobile()) {
                    $csstooltip = '';
                }

                $link_pre = '<a href="'.$fiche_link.'" title="agents en ligne '.$agent['User']['pseudo'].'">';

                $html .= '<div class="expert-cont">';

                $html .= '<div class="ec-pic">';
                $html .= $link_pre;
                $html .= $this->Html->image($this->getAvatar($agent['User']), array(
                    'alt' => 'agents en ligne '.$agent['User']['pseudo'],
                    'class' => 'small-profile img-responsive img-circle '
                ));
                $html .= '</a>';
                $html .= '<span class="ecp-status ecp-status-' . $agent['User']['agent_status'] . '" title="' . __($agent['User']['agent_status']) . '"></span>';
                $html .= '</div>';

                $html .= '<div class="ec-info">';
                if ($agent['User']['reviews_avg']) {
                    $html .= '<div class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score">'.number_format($agent['User']['reviews_avg'],1).'</span></div>';
                }
                $html .= '<div class="eci-title">' . $link_pre . $agent['User']['pseudo'] . '</a>' . '</div>';
                $html .= '</div>';

                $html .= '<div class="ec-actions">';
                $css_bloc_busy = '';
                if ($agent['User']['agent_status'] == 'busy') {
                    $agent_busy_mode = $this->agentModeBusy($agent['User']['id']);
                    $css_bloc_busy = $agent_busy_mode;
                }
                if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1) {
                    if($agent['User']['agent_status'] == 'busy') {
                        if ($agent_busy_mode == 'phone')
                            $css_phone = ' '.$agent['User']['agent_status'];
                        else
                            $css_phone = ' disabled';
                    } else {
                        $css_phone = ' '.$agent['User']['agent_status'];
                    }
                } else {
                    $css_phone = ' disabled';
                }
                $html .= '<div class="av-tel av-icn-small '.$css_phone. '">';

                if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):
                    $lien = 'agents par téléphone';
                    $html .=  '<div data-toggle="'.$csstooltip.'" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span></div>';
                else:
                    $html .=  '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon"><p>Tel</p></div>';
                endif;

                $html .= '</div>';
                $html .= '</div>';

                $html .= '</div>';
            }

            $html .= '<div class="expert-cont">';
            $html.= '<a class="see-more-experts-btn" href="/">' . __('Voir plus') . ' <i class="fa fa-chevron-right"></i></a>';
            $html .= '</div>';


            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

		}

		return $html;
	}

    public function getPlanningAgentMobile($user_id){

		App::import('Controller', 'Agents');
        $agent = new AgentsController();
		//App::import('Model', 'CustomerAppointment');
		//$appointment = new CustomerAppointment();
		App::import('Model', 'Planning');
		$planning = new Planning();

		$date = date('d-m-Y');
		$transit = explode('-',$date);
        //INFOS :  0: jour, 1: mois, 2: année
		$data = array();
        $data['A'] = $transit[2];
        $data['M'] = $transit[1];
        $data['J'] = $transit[0];

		$debutExplode = $data;

		$date = date('d-m-Y', strtotime('+'.(Configure::read('Site.limitPlanning')-1).' days'));
		$transit = explode('-',$date);
        //INFOS :  0: jour, 1: mois, 2: année
		$data = array();
        $data['A'] = $transit[2];
        $data['M'] = $transit[1];
        $data['J'] = $transit[0];
        $finExplode = $data;

       // $appointments = $appointment->appointments($user_id, $debutExplode, $finExplode);
		$cal = $planning->agent_planning($user_id,$debutExplode,$finExplode);

		if(!count($cal)){
			echo __('<p>Votre expert n\'a pas renseigné de planning mais peut se connecter régulièrement</p>');
		}else{
			echo '<p class="black" style="text-align:justify;">'.__('Le planning de votre expert vous est donné à titre indicatif, mais ce dernier est susceptible de le modifier quand il le souhaite, voir d\'élargir son temps de présence.').'</p>';

		}

		$html = ' <table class="table table-striped no-border vam">';
		$nb_show = 2;
		$nb_count = 0;
		foreach($cal as $date => $tab_horaire ){
			$classshow = '';
			if($nb_count >= $nb_show){
				if($nb_count == $nb_show)
				$html .= '<tr><td colspan="2" class="planningmobile_moreinfo"><i class="glyphicon glyphicon-plus-sign"></i> Voir plus</td></tr><tr style="height:0px;font-size:0px;display:none !important"><td  class="" colspan="2" style="height:0px;font-size:0px;display:none !important">&nbsp;</td></td>';
				$classshow = ' style="display:none"';
			}
			$html .=  '<tr '.$classshow.'>
                            <td>'.$date.'</td>';
			$html .= '<td>
                                <ul>';


			foreach($tab_horaire as $hor){
				if($hor['Min'] == '0') $hor['Min'] = '00';
				if($hor['type'] == 'debut')
					$html .= '<li>'.$hor['H'].'h'.$hor['Min'].' ';
				else
					$html .= 'à '.$hor['H'].'h'.$hor['Min'].'</li>';
			}
			$html .= '</ul>
                            </td>';
			$html .= '</tr>';
			$nb_count ++;
		}
                   $html .= '</table>';

		return $html;
	}

	public function getBottomWidget(){
			$offset = 0;
			$nbview = 4;
			$agentlist = $this->getAgentBusyData($offset, $nbview);
			$nb = count($this->getAgentBusy());
			$html = '';
           $html .= ' <div class="widget-experts-online mb20  hidden-sm hidden-xs">
            	<div class="widget">
                	';
					 $html .= '<i class="fa fa-chevron-left expert-arrow-left" aria-hidden="true"></i>
                    <ul class="online-list">';
                    foreach($agentlist as $agent ){

						$fiche_link = $this->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
								'agent_number'  => $agent['User']['agent_number']
							),
							array(
								'title'         => $agent['User']['pseudo']
							)
						);

						 if ($agent['User']['agent_status'] == 'available'){
							$set_title = __('Disponible');
							$set_title_css = 'available';
						}elseif ($agent['User']['agent_status'] == 'busy'){
							$set_title = __('En consultation').'  <span class="depuis_widget">depuis '.$this->secondsToHis($agent[0]['second_from_last_status']).'</span>';
							$set_title_css = 'consultation';

						}elseif ($agent['User']['agent_status'] == 'unavailable'){
							$set_title = __('Indisponible');
							$set_title_css = 'retour';
						}

						$csstooltip = 'tooltip';
								if($this->request->isMobile())$csstooltip = '';

                    	$html .= '<li class="fadeIn col-md-3">

                            <div class="online-name">
                                <span class="uppercase">
                                    <a href="'.$fiche_link.'" title="'.$agent['User']['pseudo'].'"><span class="inline-block h4">'.$agent['User']['pseudo'].'</span></a>
                                </span>
                                <span class="name-flag">';
								$userLangs = explode(",",$agent['User']['langs']);

								App::import("Model", "Lang");
								$model = new Lang();
								$langs = $model->find("list", array(
									'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
									'conditions'    => array('Lang.active' => 1),
									'recursive' => -1
								));
                                    foreach ($userLangs AS $idLang){
										if (isset($langs[$idLang]) && $idLang != 8 && $idLang != 10 && $idLang != 11 && $idLang != 12 ){
											$tmp = array_values($langs[$idLang]);
											$html .=  '<i class="lang_flags lang_'.key($langs[$idLang]).' " title="'.$tmp[0].' '.__('parlé couramment').'" data-original-title="'.$tmp[0].' '.__('parlé couramment').'" data-toggle="tooltip"></i>';
										}
									}
                                $html .= '</span>
                            </div>

                            <div class="row">
                                <div class="col-sm-5 pr5">
                                <div class="online-expert-pic">
                                    <a href="'.$fiche_link.'" class="sm-sid-photo" title="'.__('agents en ligne').' '.$agent['User']['pseudo'].'"><span>';

									$html .= $this->Html->image($this->getAvatar($agent['User']), array(
                                                'alt' => __('agents en ligne ').$agent['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle status-'.$agent['User']['agent_status']
                                                ));
									$html .= '</span></a>';

									if($agent['User']['reviews_avg']){
										$html .= '<p class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score">'.number_format($agent['User']['reviews_avg'],1).'</span></p>';

									}
                                $html .= '</div>
                                </div><!--col-sm-5 END-->

                                <div class="col-sm-7 pr0 pl0">
                                    <div class="status-box">
                                        <!--<p class="status '.$set_title_css.'">'.$set_title.'</p>-->
                                        <ul class="list-inline medium-btn';
										//if($agent['User']['agent_status'] == 'busy') $html .= ' alert-btn ';
										$html .= '">';

										if($agent['User']['agent_status'] == 'busy'){
											$agent_busy_mode = $this->agentModeBusy($agent['User']['id']);
										}
										if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'phone')
													$css_phone = ' t-'.$agent['User']['agent_status'];
												else
													$css_phone = ' disabled';
											}else{
												$css_phone = ' t-'.$agent['User']['agent_status'];
											}

										}else{
											$css_phone = ' disabled';
										}
										if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'tchat')
													$css_tchat = 'c-'.$agent['User']['agent_status'];
												else
													$css_tchat = ' disabled';
											}else{
												$css_tchat = 'c-'.$agent['User']['agent_status'];
											}

										}else{
											$css_tchat = ' disabled';
										}

										if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												$css_email = 'm-available';
											}else{
												$css_email = 'm-'.$agent['User']['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}

											/*	if($agent['User']['agent_status'] == 'busy'){
												$html .= '	 <li class="alert-li"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Recevoir une alerte sms/email" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" class="alerte-a aebutton nx_openinlightbox nxtooltip">Recevoir une<br />alerte sms/email</a></li>';
												}else{*/
												$html .= '<li class="tel '.$css_phone. '">';

                                                    if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):

                                                    $html .=  $this->Html->link('<p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span>',
                                                                array(
                                                                    'controller' => 'home',
                                                                    'action' => 'media_phone'
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_phonebox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Tel',
                                                                    'escape' => false,
																	'rel'=>'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a rel="nofollow" title="Tel" data-toggle="tooltip" data-placement="top" href=""><p>Tel</p></a>';
                                                        endif;
                                                $html .= '</li>';

                                                $html .= '<li class="chat '.$css_tchat. '">';

                                                    if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])):

                                                    $html .=  $this->Html->link('<p>Tchat</p>',
                                                                array(
                                                                    'controller' => 'chats',
                                                                    'action' => 'create_session',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_chatbox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Chat',
																	'rel'=>'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .=  '<a rel="nofollow" title="Chat" data-toggle="modal" href=""><p>Tchat</p></a>';
                                                        endif;
                                                $html .= '</li>';
												$html .= '<li class="mail '.$css_email. '">';

                                                if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):

                                                    $html .=  $this->Html->link('<p>Email</p>',
                                                                array(
                                                                    'controller' => 'accounts',
                                                                    'action' => 'new_mail',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_emailbox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Email',
																	'rel' => 'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a rel="nofollow" title="Email" data-toggle="tooltip" href=""><p>Email</p></a>';
                                                        endif;
                                                $html .= '</li>';
												//}
                                        $html .= '</ul>
                                    </div><!--code-box END-->
                                </div><!--col-sm-7 END-->
                            </div><!--row END-->

                        </li>';
                     }
                 $html .= ' </ul><i class="fa fa-chevron-right expert-arrow-right" aria-hidden="true"></i>
                </div><!--widget End-->';
               //'.$this->getPaginateEnligne($nb, $offset).'
			$html .= '</div><!--experts-online END-->
			';
		return $html;
	}

	public function getBottomWidgetHoroscope(){
			$offset = 0;
			$nbview = 1;
			$agentlist = $this->getAgentBusyData($offset, $nbview);
			$nb = count($this->getAgentBusy());
			$html = '';
           $html .= ' <div class="widget-experts-online widget-horo-bottom mb20">
            	<div class="widget">
                	';
					 $html .= '<i class="fa fa-chevron-left expert-arrow-left" aria-hidden="true"></i>
                    <ul class="online-list">';
                    foreach($agentlist as $agent ){

						$fiche_link = $this->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
								'agent_number'  => $agent['User']['agent_number']
							),
							array(
								'title'         => $agent['User']['pseudo']
							)
						);

						 if ($agent['User']['agent_status'] == 'available'){
							$set_title = __('Disponible');
							$set_title_css = 'available';
						}elseif ($agent['User']['agent_status'] == 'busy'){
							$set_title = __('En consultation').'  <span class="depuis_widget">depuis '.$this->secondsToHis($agent[0]['second_from_last_status']).'</span>';
							$set_title_css = 'consultation';

						}elseif ($agent['User']['agent_status'] == 'unavailable'){
							$set_title = __('Indisponible');
							$set_title_css = 'retour';
						}

						$csstooltip = 'tooltip';
								if($this->request->isMobile())$csstooltip = '';

                    	$html .= '<li class="fadeIn col-md-3">

                            <div class="online-name">
                                <span class="uppercase">
                                    <a href="'.$fiche_link.'" title="'.$agent['User']['pseudo'].'"><span class="inline-block h4">'.$agent['User']['pseudo'].'</span></a>
                                </span>
                                <span class="name-flag">';
								$userLangs = explode(",",$agent['User']['langs']);

								App::import("Model", "Lang");
								$model = new Lang();
								$langs = $model->find("list", array(
									'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
									'conditions'    => array('Lang.active' => 1),
									'recursive' => -1
								));
                                    foreach ($userLangs AS $idLang){
										if (isset($langs[$idLang]) && $idLang != 8 && $idLang != 10 && $idLang != 11 && $idLang != 12 ){
											$tmp = array_values($langs[$idLang]);
											$html .=  '<i class="lang_flags lang_'.key($langs[$idLang]).' " title="'.$tmp[0].' '.__('parlé couramment').'" data-original-title="'.$tmp[0].' '.__('parlé couramment').'" data-toggle="tooltip"></i>';
										}
									}
                                $html .= '</span>
                            </div>

                            <div class="row">
                                <div class="col-sm-5 pr5">
                                <div class="online-expert-pic">
                                    <a href="'.$fiche_link.'" class="sm-sid-photo" title="'.__('agents en ligne ').$agent['User']['pseudo'].'"><span>';

									$html .= $this->Html->image($this->getAvatar($agent['User']), array(
                                                'alt' => 'agents en ligne '.$agent['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle status-'.$agent['User']['agent_status']
                                                ));
									$html .= '</span></a>';

									if($agent['User']['reviews_avg']){
                                    	$html .= '<p class="on-per rate-data"><i class="expert-star-purple"></i> <span class="rate_score">'.number_format($agent['User']['reviews_avg'],1).'</span></p>';
									}
                                $html .= '</div>
                                </div><!--col-sm-5 END-->

                                <div class="col-sm-7 pr0 pl0">
                                    <div class="status-box">
                                        <!-- <p class="status '.$set_title_css.'">'.$set_title.'</p>-->
                                        <ul class="list-inline medium-btn';
										//if($agent['User']['agent_status'] == 'busy') $html .= ' alert-btn ';
										$html .= '">';


										if($agent['User']['agent_status'] == 'busy'){
											$agent_busy_mode = $this->agentModeBusy($agent['User']['id']);
										}
										if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'phone')
													$css_phone = ' t-'.$agent['User']['agent_status'];
												else
													$css_phone = ' disabled';
											}else{
												$css_phone = ' t-'.$agent['User']['agent_status'];
											}

										}else{
											$css_phone = ' disabled';
										}
										if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])){
											if($agent['User']['agent_status'] == 'busy'){
												if($agent_busy_mode == 'tchat')
													$css_tchat = 'c-'.$agent['User']['agent_status'];
												else
													$css_tchat = ' disabled';
											}else{
												$css_tchat = 'c-'.$agent['User']['agent_status'];
											}

										}else{
											$css_tchat = ' disabled';
										}

										if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1){
											if($agent['User']['agent_status'] == 'busy'){
												$css_email = 'm-available';
											}else{
												$css_email = 'm-'.$agent['User']['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}

											/*	if($agent['User']['agent_status'] == 'busy'){
												$html .= '	 <li class="alert-li"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Recevoir une alerte sms/email" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$this->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" rel="nofollow" class="alerte-a aebutton nx_openinlightbox nxtooltip">Recevoir une<br />alerte sms/email</a></li>';
												}else{*/
												$html .= '<li class="tel '.$css_phone. '">';

                                                    if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):

                                                    $html .=  $this->Html->link('<p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span>',
                                                                array(
                                                                    'controller' => 'home',
                                                                    'action' => 'media_phone'
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_phonebox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Tel',
                                                                    'escape' => false,
																	'rel'=>'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a rel="nofollow" title="Tel" data-toggle="tooltip" data-placement="top" href=""><p>Tel</p></a>';
                                                        endif;
                                                $html .= '</li>';

                                                $html .= '<li class="chat '.$css_tchat. '">';

                                                    if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $this->agentActif($agent['User']['date_last_activity'])):

                                                    $html .=  $this->Html->link('<p>Tchat</p>',
                                                                array(
                                                                    'controller' => 'chats',
                                                                    'action' => 'create_session',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_chatbox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Chat',
																	'rel'=>'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .=  '<a rel="nofollow" title="Chat" data-toggle="modal" href=""><p>Tchat</p></a>';
                                                        endif;
                                                $html .= '</li>';
												$html .= '<li class="mail '.$css_email. '">';

                                                if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):

                                                    $html .=  $this->Html->link('<p>Email</p>',
                                                                array(
                                                                    'controller' => 'accounts',
                                                                    'action' => 'new_mail',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_emailbox',
                                                                    'data-toggle' => $csstooltip,
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Email',
																	'rel' => 'nofollow'
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a rel="nofollow" title="Email" data-toggle="tooltip" href=""><p>Email</p></a>';
                                                        endif;
                                                $html .= '</li>';
												//}
                                        $html .= '</ul>
                                    </div><!--code-box END-->
                                </div><!--col-sm-7 END-->
                            </div><!--row END-->

                        </li>';
                     }
                 $html .= ' </ul><i class="fa fa-chevron-right expert-arrow-right" aria-hidden="true"></i>
                </div><!--widget End-->';
               //'.$this->getPaginateEnligne($nb, $offset).'
			$html .= '</div><!--experts-online END-->
			';
		return $html;
	}

	public function getBlockPromo(){
		$out = '';
		if(!$this->request->isMobile()){
			$out = '<div class="col-sm-12 col-md-4">
							<div class="voucher_box well well-light text-center">
								<p>'.__('Code promo').'</p>
								<p class="small">'.__('Si vous disposez d\'un code PROMO, indiquez le ci-dessous :').'</p>
								<input id="code_promo" class="form-control" type="text" required="" placeholder="'.__('Code promo').'">
								<a class="btn btn-pink btn-pink-modified btn-small-modified mt10" id="promo_live">'.__('Valider').'</a>
							</div>
					</div>';
		}

        return $out;
    }

	public function getBlockPromoDone($title){
		$out = '';
		if(!$this->request->isMobile()){
			$out = '<div class="col-sm-12 col-md-4">
							<div class="voucher_done_box well well-light text-center">
								<p><b>'.__('Promo en cours :').'</b> '.$title.'</p>
								<a class="btn btn-pink btn-pink-modified btn-small-modified mt10" id="promo_reset">'.__('Annuler').'</a>
							</div>
							<div class="voucher_box well well-light text-center">
								<p>'.__('Code promo').'</p>
								<p class="small">'.__('Si vous disposez d\'un code PROMO, indiquez le ci-dessous :').'</p>
								<input id="code_promo" class="form-control" type="text" required="" placeholder="'.__('Code promo').'">
								<a class="btn btn-pink btn-pink-modified btn-small-modified mt10" id="promo_live">'.__('Valider').'</a>
							</div>
					</div>';
		}

        return $out;
    }

	public function getBlockPromoMobile(){
		$out = '';
		if($this->request->isMobile()){
			$out = '<div class="row"><div class="col-sm-12 col-md-4" style="margin-top:-30px" id="mobile_voucher">
							<div class="voucher_box well well-light text-center">
								<!--<p>Code promo</p>-->
								<p class="small">'.__('Si vous disposez d\'un code PROMO, indiquez le ci-dessous :').'</p>
								<input id="code_promo" class="form-control" type="text" required="" placeholder="'.__('Code promo').'" style="width:65%;float:left;margin-bottom:0 !important;">
								<a class="btn btn-pink btn-pink-modified btn-small-modified mt10" id="promo_live" style="width:30%;float;left;margin-left:5%;margin-top:0 !important;margin-bottom:0 !important;">'.__('Valider').'</a>
							</div>
					</div></div>';
		}

        return $out;
    }

	public function getAccountGain()
	{
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget">';
			App::import("Model", "Sponsorship");
			$Sponsorship = new Sponsorship();
			App::import("Model", "SponsorshipRule");
			$SponsorshipRule = new SponsorshipRule();
			$conditions = array(
								'SponsorshipRule.type_user' => 'client',
			);
			$rule = $SponsorshipRule->find('first',array('conditions' => $conditions));
			$sponsor_gain = $rule['SponsorshipRule']['data'];

			$sponsorships_user_nb = $Sponsorship->find('count', array(
					'conditions' => array('Sponsorship.user_id' => $user['id'], 'Sponsorship.is_recup' => 0, 'Sponsorship.status' => 3),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));
			$current_gain = $sponsor_gain *$sponsorships_user_nb ;
			$html .= '<a href="/sponsorship/client_gain"><div class="boxgain boxgain_sponsor">';
				$html .= '<div class=" boxgain_title">vos Gains<br />Parrainage</div>';
				$html .= '<div class=" boxgain_gain">'.$current_gain.'€</div>';
				$html .= '<div class=" boxgain_link"><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'client_gain', )).'">'.__('> voir en détail').'</a></div>';
			$html .= '</div></a>';
			App::import("Model", "LoyaltyUserBuy");
			$LoyaltyUserBuy = new LoyaltyUserBuy();
			$current_pourcent = 0;
				$loyalty_user = $LoyaltyUserBuy->find('first', array(
					'conditions' => array('LoyaltyUserBuy.user_id' => $user['id']),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));
			if($loyalty_user['LoyaltyUserBuy']['pourcent_current']) $current_pourcent = $loyalty_user['LoyaltyUserBuy']['pourcent_current'];

			App::import("Model", "LoyaltyCredit");
			$LoyaltyCredit = new LoyaltyCredit();
			$current_unlock = 0;
				$loyalty_unlock = $LoyaltyCredit->find('first', array(
					'conditions' => array('LoyaltyCredit.user_id' => $user['id'], 'LoyaltyCredit.valid'=>0),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));

			$html .= '<a href="/accounts/loyalty"><div class="boxgain boxgain_loyalty">';
				$html .= '<div class=" boxgain_title">vos Gains<br />fidélité</div>';
				if(!$loyalty_unlock){
					$html .= '<div class=" boxgain_gain">'.$current_pourcent.'%</div>';
					$html .= '<div class=" boxgain_link"><a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'loyalty', )).'">'.__('> voir en détail').'</a></div>';
				}else{
					$html .= '<div class=" boxgain_gain" >10 Min</div>';
					$html .= '<div class=" boxgain_link"><a href="'.$this->Html->url(array('controller' => 'accounts','action' => 'loyalty', )).'">'.__('> débloquer').'</a></div>';
				}

				//$html .= '<div class=" boxgain_link">'.$this->getPageLink(240,array('class'=>''),'> voir en détail').'</div>';
			$html .= '</div></a>';
		$html .= '</div>';
		return $html;
	}

	public function getAccountLoyalty()
    {
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget"><div class="widget-title text-center">Fidélité</div><div style="padding:10px">';
		$html .= $this->getPageLink(240,array('class'=>'loyalty_title'),'100 % = 10 Minutes offertes');
		App::import("Model", "LoyaltyUserBuy");
		$LoyaltyUserBuy = new LoyaltyUserBuy();

			$current_pourcent = 0;
			$loyalty_user = $LoyaltyUserBuy->find('first', array(
                'conditions' => array('LoyaltyUserBuy.user_id' => $user['id']),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));
		if($loyalty_user['LoyaltyUserBuy']['pourcent_current']) $current_pourcent = $loyalty_user['LoyaltyUserBuy']['pourcent_current'];

		$html .= '<progress  max="100" value="'.$current_pourcent.'"><div class="progress-bar"><span style="width: '.$current_pourcent.'%">'.$current_pourcent.'%</span></div></progress>';
		$html .= '<p class="loyalty_pourcent">'.$current_pourcent.'% / 100%</p>';

		App::import("Model", "LoyaltyCredit");
		$LoyaltyCredit = new LoyaltyCredit();
		$loyalty_credit = $LoyaltyCredit->find('all', array(
                'conditions' => array('LoyaltyCredit.user_id' => $user['id'], 'LoyaltyCredit.valid' => 0),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));

		if(count($loyalty_credit)){
			$html .= '<hr><p style="text-align:center;display:block;"><strong>Gains accumulés</strong> :</p>';
			foreach($loyalty_credit as $loyal){
					$html .= '<p style="text-align:center;display:block;">'.__('10 minutes le ').$this->Time->format($loyal['LoyaltyCredit']['date_add'], '%d-%m-%Y %H:%M').'</p>';
				}

			$html .= '<p style="display:block;margin:10px 0;text-align:center;">'.$this->Html->link(__('Débloquer mes gains'), array('controller' => 'loyalty', 'action' => 'unlock'), array('title' => __('Débloquer mes gains'), 'class' => 'btn btn-pink subscribe')).'</p>';
		}

		$html .= '<br /><p>'.$this->getPageLink(240,array('style'=>'text-align:justify;display:block;'),__('Spiriteo vous récompense de votre fidélité, lors de l\'achat d\'un forfait vous cumulez des points.')).'</p>';
		$html .= '<p>'.$this->getPageLink(240,array('style'=>'text-align:justify;display:block;'),__('Le nombre de points attribués augmente en fonction du forfait acheté.')).'</p>';
		$html .= '</div></div>';
		return $html;

	}

	public function getAccountClientSponsorship()
    {
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget"><div class="widget-title text-center">Parrainage</div><div style="padding:10px">';
		//$html .= $this->getPageLink(240,array('class'=>'loyalty_title'),'100 % = 10 Minutes offertes');
		$html .= '<p style="text-align:center;display:block;">'.__('100% = 10€ en crédits offerts').'</p>';
		App::import("Model", "Sponsorship");
		$Sponsorship = new Sponsorship();

		$current_pourcent = 0;
		$sponsorships_user = $Sponsorship->find('all', array(
                'conditions' => array('Sponsorship.user_id' => $user['id'], 'Sponsorship.is_recup' => 0, 'Sponsorship.status' => 3),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));
		App::import("Model", "Order");
		$Order = new Order();
		$best_filleul_current = 0;
		foreach($sponsorships_user as $sponsorship_user){
			//if($sponsorship_user['Sponsorship']['bonus']) $current_pourcent = $current_pourcent + $sponsorship_user['Sponsorship']['bonus'];
			$current = 0;
			$order_user = $Order->find('all', array(
                'conditions' => array('Order.user_id' => $sponsorship_user['Sponsorship']['id_customer'], 'Order.date_add >' => $sponsorship_user['Sponsorship']['date_add'], 'Order.valid' => 1),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));
			foreach($order_user as $ordu){
				$current = $current + $ordu['Order']['total'];
			}
			if($current > $best_filleul_current)$best_filleul_current=$current;
		}

		if($best_filleul_current > 30)$best_filleul_current=30;

		$html .= '<progress  max="30" value="'.$best_filleul_current.'"><div class="progress-bar"><span style="width: '.$best_filleul_current.'%">'.$best_filleul_current.'%</span></div></progress>';
		$html .= '<p class="sponsorship_pourcent" style="text-align:center;display:none;">'.$best_filleul_current.'% / 100%</p>';
		if($best_filleul_current >= 30)
		$html .= '<p style="display:block;margin:10px 0;text-align:center;"><a href="/sponsorship/unlock" title="'.__('Réclamer ma récompense').'" class="btn btn-pink  enabled">'.__('Réclamer ma récompense').'</a></p>';

		$html .= '<p style="text-align:justify"><a href="/sponsorship/client">'.__('Spiriteo vous récompense lorsque vous parrainez vos proches en vous offrant des crédits de consultation. Voir comment parrainer mes proches').'</a></p>';


		$html .= '</div></div>';
		return $html;

	}
	public function getAccountAgentSponsorship()
    {
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget">';

		App::import("Model", "Sponsorship");
		$Sponsorship = new Sponsorship();
		App::import("Model", "UserCreditLastHistory");
		$UserCreditLastHistory = new UserCreditLastHistory();

		$current_pourcent = 0;
		$sponsorships_user = $Sponsorship->find('all', array(
                'conditions' => array('Sponsorship.user_id' => $user['id'],'Sponsorship.status <=' => 4),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));// 'Sponsorship.is_recup' => 1, 'Sponsorship.date_add >=' => date('Y-m-01 00:00:00')

		foreach($sponsorships_user as $sponsorship_user){

			if($sponsorship_user['Sponsorship']['bonus']){
				$total = 0;
				if($sponsorship_user['Sponsorship']['type_user'] == 'client'){
					if($sponsorship_user['Sponsorship']['is_recup']){
						$current_pourcent = $current_pourcent + $sponsorship_user['Sponsorship']['bonus'];
					}
				}else{
					$lastComs = $UserCreditLastHistory->find('all', array(
								'conditions'    => array('UserCreditLastHistory.users_id' => $sponsorship_user['Sponsorship']['id_customer'],'UserCreditLastHistory.date_start >=' => date('Y-m-01 00:00:00'), 'UserCreditLastHistory.date_start >' => $sponsorship_user['Sponsorship']['date_add'],
								'UserCreditLastHistory.is_factured' => 1
														),
								'recursive'     => -1
							));

							foreach($lastComs as $comm){
								$total = $total + $comm['UserCreditLastHistory']['credits'];
							}

					$current_pourcent = $current_pourcent + ($sponsorship_user['Sponsorship']['bonus']/60 * $total);
				}

			}
		}

		$html .= '<a href="/sponsorship/agent_gain"><div class="boxgain boxgain_sponsor">';
				$html .= '<div class=" boxgain_title">vos Gains<br />'.__('Parrainage').'</div>';
				$html .= '<div class=" boxgain_gain">'.number_format($current_pourcent,2).'€</div>';
				$html .= '<div class=" boxgain_link"><a href="'.$this->Html->url(array('controller' => 'sponsorship','action' => 'agent_gain', )).'">'.__('> voir en détail').'</a></div>';
			$html .= '</div></a>';

		$html .= '<a href="/agents/bonus"><div class="boxgain boxgain_loyalty">';
				$html .= '<div class=" boxgain_title">Bonus<br />'.__('mensuels').'</div>';
				$html .= '<div class=" boxgain_title" style="border:0;margin-bottom:4px">et<br />'.__('Rémunérations').'</div>';
					$html .= '<div class=" boxgain_link"><a href="'.$this->Html->url(array('controller' => 'agents','action' => 'bonus', )).'">'.__('> Voir en détail').'</a></div>';
			$html .= '</div></a>';


		$html .= '</div>';

		/*$html = '<div class="widget"><div class="widget-title text-center">Parrainage</div><div style="padding:10px">';

		App::import("Model", "Sponsorship");
		$Sponsorship = new Sponsorship();
		App::import("Model", "UserCreditLastHistory");
		$UserCreditLastHistory = new UserCreditLastHistory();

		$current_pourcent = 0;
		$sponsorships_user = $Sponsorship->find('all', array(
                'conditions' => array('Sponsorship.user_id' => $user['id'], 'Sponsorship.is_recup' => 1, 'Sponsorship.date_add >=' => date('Y-m-01 00:00:00')),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));

		foreach($sponsorships_user as $sponsorship_user){

			if($sponsorship_user['Sponsorship']['bonus']){
				$total = 0;
				$lastComs = $UserCreditLastHistory->find('all', array(
								'conditions'    => array('UserCreditLastHistory.users_id' => $sponsorship_user['Sponsorship']['id_customer'], 'UserCreditLastHistory.date_start >' => $sponsorship_user['Sponsorship']['date_add']),
								'recursive'     => -1
							));

							foreach($lastComs as $comm){
								$total = $total + $comm['UserCreditLastHistory']['seconds'];
							}

				$current_pourcent = $current_pourcent + ($sponsorship_user['Sponsorship']['bonus']/60 * $total);
			}
		}

		$current_pourcent = str_replace(',','.',$current_pourcent);
		$debut = explode('.',$current_pourcent);
		$taille = strlen($debut[0]) - 1;
		$centaine = substr($current_pourcent,0,1);

		$max = 100;
		if($taille ==3)$max=1000;
		if($taille ==4)$max=10000;

		$html .= '<progress  max="'.$max.'" value="'.$current_pourcent.'"><div class="progress-bar"><span style="width: '.$current_pourcent.'%">'.$current_pourcent.'%</span></div></progress>';
		$html .= '<p class="sponsorship_pourcent" style="display:block;text-align:center">'.$current_pourcent.'€</p>';// / '.$max.'€

		$html .= '<p style="display:block;text-align:justify"><a href="/sponsorship/agent">Spiriteo vous rémunère lorsque vous parrainez un de vos clients, lorsque celui-ci vous consultera vous gagnerez 10% des revenus générés par ce dernier en plus de votre rémunération habituelle. Si votre client consulte un autre expert que vous, vous êtes toujours gagnant, vous toucherez quand même les 10% des revenus générés par votre client.Voir comment parrainer un client.</a></p>';
		*/

		/*$html .= '<br /><p>'.$this->getPageLink(240,array('style'=>'text-align:justify;display:block;'),'Spiriteo vous récompense de votre fidélité, lors de l\'achat d\'un forfait vous cumulez des points.').'</p>';
		$html .= '<p>'.$this->getPageLink(240,array('style'=>'text-align:justify;display:block;'),'Le nombre de points attribués augmente en fonction du forfait acheté.').'</p>';*/

		/*$html .= '</div></div>';*/
		return $html;

	}

	public function getAccountBonus()
    {
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget"><div class="widget-title text-center">'.__('Bonus mensuels').'</div><div style="padding:10px">';

		$html .= '<p style="display:block;text-align:justify;width:100%">'.__('Les bonus sont mensuels et donc calculés du 1er au dernier jour de chaque mois. Ces primes seront automatiquement ajoutées sur votre facture-modèle.').'</p>';

		App::import("Model", "BonusAgent");
		$BonusAgent = new BonusAgent();

		$current_pourcent = 0;
		$bonus_agent = $BonusAgent->find('first', array(
                'conditions' => array('BonusAgent.id_agent' => $user['id'], 'active' => 1, 'annee' => date('Y'), 'mois' => date('m')),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));
		if($bonus_agent){
			if($bonus_agent['BonusAgent']['min_total']) $current_pourcent = floor($bonus_agent['BonusAgent']['min_total'] / 60);

			$next_bearing = $bonus_agent['BonusAgent']['id_bonus'] + 1;

			App::import('Model', 'Bonus');
			$BonusModel = new Bonus();
			$bonus = $BonusModel->find('first', array(
									'conditions' => array('Bonus.id' => $next_bearing),
									'recursive' => -1
								));
			if(!empty($bonus)){
				$max = 	$bonus['Bonus']['bearing'];
			}else{
				$max = 	$current_pourcent;
			}
			$html .= '<progress  max="'.$max.'" value="'.$current_pourcent.'"><div class="progress-bar"><span style="width: '.$current_pourcent.' min">'.$current_pourcent.' min</span></div></progress>';
			$html .= '<p class="loyalty_pourcent">'.$current_pourcent.' min / '.$max.' min</p>';

			//affichage du bareme
			$bonus = $BonusModel->find('all', array(
									'order' => array('id'=> 'asc'),
									'recursive' => -1
								));
			$html .= '<div class="bearing_echel">';
			if(!empty($bonus)){
				foreach($bonus as $bobo){
					foreach($bobo as $b){
						$current_bearing = '';
						if($bonus_agent['BonusAgent']['id_bonus'] == $b['id'] ){
							$current_bearing = ' active ';
						}
						$html .= '<p class="bearing_agent '.$current_bearing.'"> '.$b['bearing'].' minutes : +'.$b['amount'].'€  HT de prime</p>';
					}
				}
			}
			$html .= '</div>';
		}
		$html .= '</div></div>';
		return $html;

	}

	public function getAccountRemuneration()
    {
		$user = $this->Session->read('Auth.User');
		$html = '<div class="widget"><div class="widget-title text-center">'.__('Rémunérations').'</div><div style="padding:10px">';

		$html .= '<p style="display:block;text-align:justify;width:100%">'.__('La tranche de rémunération se calcule automatiquement en fonction du nombre de minutes cumulées lors de vos consultations par téléphone et tchat.').'</p>';

		App::import("Model", "CostAgent");
		$CostAgent = new CostAgent();

		$current_minutes = 0;
		$cost_agent = $CostAgent->find('first', array(
                'conditions' => array('CostAgent.id_agent' => $user['id']),
                'recursive' => -1
            ));
		if($cost_agent){
			if($cost_agent['CostAgent']['nb_minutes']) $current_minutes = $cost_agent['CostAgent']['nb_minutes'];

			$next_level = $cost_agent['CostAgent']['id_cost'];

			App::import('Model', 'Cost');
			$CostModel = new Cost();
			$cost = $CostModel->find('first', array(
									'conditions' => array('Cost.id' => $next_level),
									'recursive' => -1
								));
			if(!empty($cost)){
				$max = 	$cost['Cost']['level'];
			}else{
				$max = 	$current_minutes;
			}

			$current_secondes = $current_minutes * 60;

			/*$current_s = gmdate("s", $current_secondes);
			$current_h = gmdate("H", $current_secondes) *60;
			$current_m = gmdate("i", $current_secondes) + $current_h;
			//$current_minutes = str_replace('.',',',str_replace(',','',date('i.s',$current_secondes)));
			$current_minutes = $current_m.','.$current_s;*/
			$current_minutes = str_replace('.',',',$current_minutes);
			$html .= '<progress  max="'.$max.'" value="'.str_replace(',','.',$current_minutes).'"><div class="progress-bar"><span style="width: '.$current_minutes.' min">'.$current_minutes.' min</span></div></progress>';
			$html .= '<p class="loyalty_pourcent">'.$current_minutes.' min / '.$max.' min</p>';

			//affichage du bareme
			if($next_level < 5)
			$costs = $CostModel->find('all', array(
									'conditions' => array('Cost.id <' => 5),
									'recursive' => -1
								));
			if($next_level >= 5)
			$costs = $CostModel->find('all', array(
									'conditions' => array('OR'=>array('Cost.id <' => 5, 'Cost.id'=>$next_level)),
									'recursive' => -1
								));
			$html .= '<div class="bearing_echel">';
			if(!empty($costs)){
				foreach($costs as $bobo){
					foreach($bobo as $b){
						$current_bearing = '';
						if($cost_agent['CostAgent']['id_cost'] == $b['id'] ){
							$current_bearing = ' active ';
						}
						$symbol = '<';
						if($b['level'] == 100000)$symbol = '>';
						$html .= '<p class="bearing_agent '.$current_bearing.'" style="font-size:12px">'.$symbol.''.$b['level'].' min : '.$b['name'].'</p>';
					}
				}
			}

			$html .= '</div>';
		}
		$html .= '</div></div>';
		return  $html;

	}

	/*
	//On compte les messages non lu
	 * 	 */
	public function getAgentAlertes()
    {
		$user = $this->Session->read('Auth.User');
		$html = '';

		if ($user['role'] == 'agent'){

			//On importe le model
			App::import("Model", "Message");
			$model = new Message();

			 $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 1);

			//On compte les messages non lu
			$nb_messages = $model->find('count', array(
				'conditions' => $conditions,
				'recursive' => -1
			));
			  $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 0);

			//On compte les messages non lu
			$nb_mails = $model->find('count', array(
				'conditions' => $conditions,
				'recursive' => -1
			));

			if($nb_messages && !$nb_mails){
				$url = '/agents/mails?private=1';
			}else{
				$url = '/agents/mails';
			}

			$lost = $this->countLOST('all');

			$messages = $nb_mails + $nb_messages + $lost;
			//si aucun message
			if(!$messages){
				$html .= '<a href="'.$url.'" class="icone-alert-messages-down hidden-xs"></a>';
			}else{
				$html .= '<span class="txt-alert-messages-up hidden-xs">'.$messages.'</span><a href="'.$url.'" class="icone-alert-messages-up hidden-xs"></a>';
			}

		}
		return $html;
	}
	public function getAccountAlertes()
    {
		$user = $this->Session->read('Auth.User');
		$html = '';

		if ($user['role'] == 'client'){

			//On importe le model
			App::import("Model", "Message");
			$model = new Message();

			 $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 1);

			//On compte les messages non lu
			$nb_messages = $model->find('count', array(
				'conditions' => $conditions,
				'recursive' => -1
			));
			  $conditions = array('Message.to_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 0, 'Message.archive' => 0, 'Message.private' => 0);

			//On compte les messages non lu
			$nb_mails = $model->find('count', array(
				'conditions' => $conditions,
				'recursive' => -1
			));

			 $conditions = array('Message.from_id' => $this->Session->read('Auth.User.id'), 'Message.etat' => 3, 'Message.archive' => 0, 'Message.private' => 0);

			//On compte les messages perime
			$nb_mails2 = $model->find('count', array(
				'conditions' => $conditions,
				'recursive' => -1
			));

			if($nb_messages && !$nb_mails){
				$url = '/accounts/mails?private=1';
			}else{
				$url = '/accounts/mails';
			}

			$messages = $nb_mails + $nb_messages + $nb_mails2;
			//si aucun message
			if(!$messages){
				$html .= '<a href="'.$url.'" class="icone-alert-client-messages-down hidden-xs"></a>';
			}else{
				$html .= '<span class="txt-alert-client-messages-up hidden-xs">'.$messages.'</span><a href="'.$url.'" class="icone-alert-client-messages-up hidden-xs"></a>';
			}

		}
		return $html;
	}
	public function getProductsLink($langage_code=false, $full=false)
    {
        if (empty($langage_code)){
            $langage_code = $this->Session->read('Config.language');
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = Configure::read('Routing.products');

        $seo_word = isset($seo_words_from_lang_code[$langage_code])?$seo_words_from_lang_code[$langage_code]:'products/tarif';

        return '/'.$langage_code.'/'.$seo_word;
    }
	public function getGiftLink($langage_code=false, $full=false)
    {
        if (empty($langage_code)){
            $langage_code = $this->Session->read('Config.language');
        }
        if (empty($langage_code))return false;
        $seo_words_from_lang_code = 'carte-cadeau';

        $seo_word = isset($seo_words_from_lang_code)?$seo_words_from_lang_code:'gifts/index';

        return '/'.$langage_code.'/'.$seo_word;
    }

	public function getUrlInterSite($domain_id)
	{
		$params = $this->request->params;
		$controller = $params['controller'];
		$action = $params['action'];
		//var_dump($params);

		App::import("Model", "Domain");
		$model = new Domain();
		$langs = $model->find('first', array(
                'fields' => array('Lang.language_code','Domain.domain','Domain.default_lang_id'),
                'conditions' => array('Domain.id' => $domain_id),
                'joins' => array(
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = Domain.default_lang_id')
                    )
                ),
                'recursive' => -1
            ));
		$language_code = $langs['Lang']['language_code'];
		$lang_id = $langs['Domain']['default_lang_id'];
		if(!$language_code)$language_code = $this->Session->read('Config.language');

		$out = 'https://'.$langs['Domain']['domain'];

		//switch controller
		$makeit = false;
		if($controller == 'pages' && $action == 'display') $makeit = true;
		if($controller == 'horoscopes' && $action == 'display') $makeit = true;
		if($controller == 'horoscopes' && $action == 'index') $makeit = true;
		if($controller == 'category' && $action == 'display') $makeit = true;
		if($controller == 'agents' && $action == 'display') $makeit = true;
		if($controller == 'home' && $action == 'display') $makeit = true;
		if($controller == 'home' && $action == 'index') $makeit = true;
		if($controller == 'products' && $action == 'tarif') $makeit = true;
		if($controller == 'reviews' && $action == 'display') $makeit = true;
    if($controller == 'cards' && $action == 'display') $makeit = true;

		if(!$makeit){
			$controller = 'home';
			$action = 'display';
		}

		switch ($controller) {
			case 'home'://display
				/*$theurl = $this->Html->url(
					array(
						'controller' => $controller,
						'action'    => $action,
						)
					);*/
				$out .= '';
				break;
			case 'products'://display

				$out .= '/'.$language_code.'/tunnel-1-choisissez-le-nombre-de-minutes-a-acheter';//$this->getProductsLink($language_code);
				break;
			case 'agents'://display
				$out .= $this->Html->url(

					array(
						'controller' => $controller,
						'action'    => $action,
						'language'    => $language_code,
						'link_rewrite'    => $params['link_rewrite'],
						'agent_number'    => $params['agent_number']
						)
					);
				break;
			case 'category'://display
				App::import("Model", "CategoryLang");
				$model = new CategoryLang();
				$info = $model->find('first', array(
						'fields' => array('CategoryLang.link_rewrite'),
						'conditions' => array('CategoryLang.category_id' => $params['id'],'CategoryLang.lang_id' => $lang_id),
						'recursive' => -1
					));
				$out .= $this->Html->url(
					array(
						'controller' => $controller,
						'action'    => $action,
						'language'    => $language_code,
						'link_rewrite'    => $info['CategoryLang']['link_rewrite'],
						'id'    => $params['id']
						)
					);
				break;
			case 'horoscopes':
					if($action != 'index'){
						$cut = explode('/',$_SERVER['REQUEST_URI']);
						$out .= '/'.$language_code.'/'.'horoscope-du-jour'.'/'.$cut[3];
					}else{
						$urll = $this->Html->url(
						array(
							'controller' => $controller,
							'action'    => $action,
							)
						).'/index';
						$out .= str_replace('/horoscopes/index','/'.$language_code.'/horoscope-du-jour',$urll);

					}
				break;
      case 'cards':
						$cut = explode('/',$_SERVER['REQUEST_URI']);
						$out .= '/'.$language_code.'/'.'tarots-en-ligne'.'/'.$cut[3];
				break;
			case 'pages'://display
				App::import("Model", "PageLang");
				$model = new PageLang();
				$info = $model->find('first', array(
						'fields' => array('PageLang.page_id'),
						'conditions' => array('PageLang.link_rewrite' => $params['link_rewrite'],'PageLang.lang_id' => $this->Session->read('Config.id_lang')),
						'recursive' => -1
					));
				$page_id = $info['PageLang']['page_id'];

				$info = $model->find('first', array(
						'fields' => array('PageLang.link_rewrite'),
						'conditions' => array('PageLang.page_id' => $info['PageLang']['page_id'],'PageLang.lang_id' => $lang_id),
						'recursive' => -1
					));
				$link_rewrite = $info['PageLang']['link_rewrite'];

				App::import("Model", "Page");
				$model = new Page();
				$info = $model->find('first', array(
						'fields' => array('Page.page_category_id'),
						'conditions' => array('Page.id' => $page_id),
						'recursive' => -1
					));
				$page_category_id =  $info['Page']['page_category_id'];

				App::import("Model", "PageCategory");
				$model = new PageCategory();
				$r = $model->find('first',array(
					'conditions' => array('PageCategory.id' => $page_category_id),
					'fields' => array('PageCategory.id_parent'),
					'recursive' => -1
				));
				$id_page_category_parent = $r['PageCategory']['id_parent'];
				if($id_page_category_parent){
					App::import("Model", "PageCategoryLang");
					$model = new PageCategoryLang();
					$info = $model->find('first', array(
							'fields' => array('PageCategoryLang.name'),
							'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category_parent,'PageCategoryLang.lang_id' => $lang_id),
							'recursive' => -1
						));

					$seo_word = $this->slugify($info['PageCategoryLang']['name']);
					$info = $model->find('first', array(
							'fields' => array('PageCategoryLang.name'),
							'conditions' => array('PageCategoryLang.page_category_id' => $page_category_id,'PageCategoryLang.lang_id' => $lang_id),
							'recursive' => -1
						));

					$seo_word2 = $this->slugify($info['PageCategoryLang']['name']);
					if($page_id == 36 && $language_code == 'fre'){
						$out .= '';
					}else{
						$out .= $this->Html->url(
						array(
							'controller' => $controller,
							'action'    => $action,
							'language'    => $language_code,
							'link_rewrite'    => $link_rewrite,
							'seo_word'    => $seo_word.'/'.$seo_word2,
							)
						);
					}

				}else{
					App::import("Model", "PageCategoryLang");
					$model = new PageCategoryLang();
					$info = $model->find('first', array(
							'fields' => array('PageCategoryLang.name'),
							'conditions' => array('PageCategoryLang.page_category_id' => $page_category_id,'PageCategoryLang.lang_id' => $lang_id),
							'recursive' => -1
						));

					$seo_word = $this->slugify($info['PageCategoryLang']['name']);
					if($page_id == 36 && $language_code == 'fre'){
						$out .= '';
					}else{
            if($seo_word){
              $out .= $this->Html->url(
                array(
                  'controller' => $controller,
                  'action'    => $action,
                  'language'    => $language_code,
                  'link_rewrite'    => $link_rewrite,
                  'seo_word'    => $seo_word
                  )
                );
            }else{
              $out .= $this->Html->url(
                array(
                  'controller' => $controller,
                  'action'    => $action,
                  'language'    => $language_code,
                  'link_rewrite'    => $link_rewrite,
                  )
                );
            }
				  }
				}


        if(substr_count($out,'pages/display') || substr_count($out,'bloctexte')) $out = '';

				break;
				case 'reviews':
						$out .= '/'.$language_code.'/'.'avis-clients';
				break;
		}
		//var_dump($params);
		if(!$makeit){
			$out = '';
		}
		return $out;

	}

	public function gethreflang(){
		 $countries = $this->_getHeaderCountryBlock();

        $html = '';

		foreach ($countries AS $domain){

			$name_flag_title = str_replace('Canada','Quebec',$domain['CountryLang']['name']);
			//if($this->Session->read('Config.id_domain') == $domain['Domain']['id']){
				//href de base
				//$html.= '<link rel="alternate" href="'.$this->getUrlInterSite($domain['Domain']['id']).'" hreflang="x-default" />';
			//}else{
				$lang = '';
				switch ($domain['Domain']['id']) {
					case 19:
						$lang =  "fr-fr";
						break;
					case 22:
						$lang =  "fr-lu";
						break;
					case 29:
						$lang =  "fr-ca";
						break;
					case 11:
						$lang =  "fr-be";
						break;
					case 13:
						$lang = "fr-ch";
						break;
				}
				$the_url = $this->getUrlInterSite($domain['Domain']['id']);
				if($lang && $the_url  && !substr_count($the_url , 'link_rewrite') && !substr_count($the_url , 'seo_word'))
				$html.= '<link rel="alternate" href="'.$the_url.'" hreflang="'.$lang.'" />';

				if($lang && $domain['Domain']['id'] == 19 && $the_url )
				$html.= '<link rel="alternate" href="'.$the_url.'" hreflang="x-default" />';
			//}
		}

        return $html;
	}

	public function getAccountBarInfo($agent = null, $depuis = 0)
	{
		$html = '<div class="mobileexpertbar visible-xs">';
		/*$user = $this->Session->read('Auth.User');
		$current_credit = false;
		if (!empty($user)){
            App::import("Model", "User");
            $obj = new User();
            $current_credit = $obj->getCredit($user['id']);
            $user['credit'] = $current_credit;
        }
		App::import("Model", "CountryLangPhone");
		$objphone = new CountryLangPhone();
        $phones = $objphone->getPhones($this->Session->read('Config.id_country'), $this->Session->read('Config.id_lang'));




		$html .= '<div class="mobileaccountbar-code-per">'.__('Code perso :').' <span>'.$user['personal_code'].'</span></div>';
		$html .= '<div class="mobileaccountbar-phone"><a href="tel:'.$this->formatPhoneNumber($phones['0']['CountryLangPhone']['prepayed_phone_number']).'"><i class="fa fa-phone"></i> '.$this->formatPhoneNumber($phones['0']['CountryLangPhone']['prepayed_phone_number']).'</a></span></div><br />';
		$html .= '<div class="mobileaccountbar-credits">'.$this->Html->link($this->getCreditLightString($current_credit), array('controller' => 'accounts', 'action' => 'buycredits'), array( 'escape'=>false )).'</div>';*/

		$html .= '<p class="title">consulter <span>'.$agent['pseudo'].'</span> par</p>';

		if($agent){
			switch ($agent['agent_status']) {
				case 'available':
					$html .= '<ul class="list-inline action-btn">';

					if($agent['agent_status'] == 'busy'){
											$agent_busy_mode = $this->agentModeBusy($agent['id']);
										}
										if (isset($agent['consult_phone']) && (int)$agent['consult_phone'] == 1){
											if($agent['agent_status'] == 'busy'){
												if($agent_busy_mode == 'phone')
													$css_phone = ' t-'.$agent['agent_status'];
												else
													$css_phone = ' disabled';
											}else{
												$css_phone = ' t-'.$agent['agent_status'];
											}

										}else{
											$css_phone = ' disabled';
										}
										if (isset($agent['consult_chat']) && (int)$agent['consult_chat'] == 1 && $this->agentActif($agent['date_last_activity'])){
											if($agent['agent_status'] == 'busy'){
												if($agent_busy_mode == 'tchat')
													$css_tchat = 't-'.$agent['agent_status'];
												else
													$css_tchat = ' disabled';
											}else{
												$css_tchat = 't-'.$agent['agent_status'];
											}

										}else{
											$css_tchat = ' disabled';
										}

										if (isset($agent['consult_email']) && (int)$agent['consult_email'] == 1){
											if($agent['agent_status'] == 'busy'){
												$css_email = 't-available';
											}else{
												$css_email = 't-'.$agent['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}


									$html .= '<li class="tel '.$css_phone.'">';
                                        if (isset($agent['consult_phone']) && (int)$agent['consult_phone'] == 1):

											 $lien = $this->Html->url(
												array(
													'controller' => 'home',
                                                                    'action' => 'media_phone'
												)
											);
											$html .= '<div class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span><span class="ae_phone_param" style="display:none">'.$agent['id'].'</span></div>';

											else:
												$html .= '<div class="aicon"></div>';
											endif;
                                    $html .= '</li>';

									$html .= '<li class="chat '.$css_tchat.'">';
                                        if (isset($agent['consult_chat']) && (int)$agent['consult_chat'] == 1 && $this->agentActif($agent['date_last_activity'])):
										$lien = $this->Html->url(
												array(
													'controller' => 'chats',
                                                    'action' => 'create_session',
                                                    'id' => $agent['id']
												)
											);
											$html .= '<div class="nx_chatboxinterne aicon"><span class="linklink">'.$lien.'</span></div>';

											else:
												$html .= '<div class="aicon"></div>';
											endif;
                                    $html .= '</li>';


									$html .= '<li class="mail '.$css_email.'">';
                                        if (isset($agent['consult_email']) && (int)$agent['consult_email'] == 1):
										 $lien = $this->Html->url(
												array(
													'controller' => 'accounts',
													'action' => 'new_mail',
													'id' => $agent['id']
												)
											);
											$html .= '<div class="nx_emailboxinterne aicon"><span class="linklink">'.$lien.'</span></div>';

											else:
												$html .= '<div class="aicon"><p>Email</p></div>';
											endif;
                                    $html .= '</li>';
								$html .= '</ul>';
					break;
				case 'unavailable':
					$html .= '<div class="blox_unavailable"><p class="stitle">'.__('indisponible').'</p><p class="txt"><a href="'.$this->Html->url(
													array(
														'language'      => $this->Session->read('Config.language'),
														'controller'    => 'alerts',
														'action'        => 'setnew',
														$agent['id']
													)
												).'" rel="nofollow" class="nx_openinlightbox nxtooltip">Recevoir une alerte sms/email</a></p></div>';
					break;
				case 'busy':
					$html .= '<ul class="list-inline action-btn action-btn-busy">';

					if($agent['agent_status'] == 'busy'){
											$agent_busy_mode = $this->agentModeBusy($agent['id']);
					}
					if($agent_busy_mode == 'tchat')
					$mode = 'chat';
					else
						$mode = 'tel';
						$html .= '<li class="'.$mode.' t-busy">';
                                        if (isset($agent['consult_phone']) && (int)$agent['consult_phone'] == 1):

											 $lien = $this->Html->url(
												array(
													'controller' => 'home',
                                                                    'action' => 'media_phone'
												)
											);
											$html .= '<div  class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span><span class="ae_phone_param" style="display:none">'.$agent['id'].'</span></div><div  class="action-bar-busy" >'.__('En consultation ').'<br />'.__('depuis ').$this->secondsToHis($depuis).'</div>';

											else:
												$html .= '<div class="aicon"></div>';
											endif;
                                    $html .= '</li>';
									if (isset($agent['consult_email']) && (int)$agent['consult_email'] == 1){
											if($agent['agent_status'] == 'busy'){
												$css_email = 't-available';
											}else{
												$css_email = 't-'.$agent['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}
									$html .= '<li class="mail '.$css_email.'">';
                                        if (isset($agent['consult_email']) && (int)$agent['consult_email'] == 1):
										 $lien = $this->Html->url(
												array(
													'controller' => 'accounts',
													'action' => 'new_mail',
													'id' => $agent['id']
												)
											);
											$html .= '<div class="nx_emailboxinterne aicon"><span class="linklink">'.$lien.'</span></div>';

											else:
												$html .= '<div class="aicon"></div>';
											endif;
                                    $html .= '</li>';
								$html .= '</ul>';

					break;
			}
		}
		$html .= '</div>';
		return $html;
	}

	public function getGiftMenu(){
		$html = '<a class="menu_gift hidden-xs hidden-sm" href="'.$this->getGiftLink().'">
			cadeaux
			</a>';
		return $html;
	}
	public function beginCachePageHtml(){

		/*$params = $this->request->params;
		$cacheFile = '';
		switch ($params['controller']) {
			case 'pages':
				$cacheFile = TMP . 'pages/cms/' . $params['language'].'-'.$params['seo_word'].'-'.$params['seo_word2'].'-'.$params['link_rewrite']. '.html';
			break;
		}
		if($cacheFile && !is_file($cacheFile)){
			ob_start();
		}else{
			$contentHTML = file_get_contents($cacheFile);
			echo $contentHTML;exit;
		}*/
	}
	public function endCachePageHtml(){
		/*$params = $this->request->params;
		$cacheFile = '';
		switch ($params['controller']) {
			case 'pages':
				$cacheFile = TMP . 'pages/cms/' . $params['language'].'-'.$params['seo_word'].'-'.$params['seo_word2'].'-'.$params['link_rewrite']. '.html';
			break;
		}
		if($cacheFile && !is_file($cacheFile)){
			$contentHTML = ob_get_contents();
			file_put_contents($cacheFile, $contentHTML);
		}*/
	}

    public function ratingStarsHtml($rate, $gray = false) {
        for ($i = 0; $i < (int) $rate; ++$i) {
            echo '<i class="fa fa-star"></i>';
        }
        if ($i < $rate) {
            echo '<i class="fa fa-star fa-star-s-', ($rate - $i) * 10, '"></i>';
            if ($gray) {
                echo '<i class="fa fa-star fa-star-d-', (1 - $rate + $i) * 10, '"></i>';
            }
        }
        if ($gray) {
            for ($i = ceil($rate); $i < 5; ++$i) {
                echo '<i class="fa fa-star fa-star-d"></i>';
            }
        }
    }
}

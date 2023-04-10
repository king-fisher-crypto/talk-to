<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('File', 'Utility');

class MenusController extends AppController {
    public $components = array('Paginator');
    public $uses = array('Category', 'PageCategory', 'MenuLink', 'MenuLinkLang','Page','MenuBlockLang');
    public $helpers = array('Paginator' => array('url' => array('controller' => 'menus')));

    public function beforeFilter() {

        parent::beforeFilter();
    }
    public function admin_tmp()
    {
        $this->layout = false;
        $this->autoRender = false;

        $this->admin_ajax_get_elements('blocks');
    }
    public function admin_menu()
    {
        //Tout les liens
        $menu_select['link'] = $this->MenuLink->find('all',  array(
            'fields' => array('MenuLink.id', 'MenuLinkLang.title'),
            'joins' => array(
                array(
                    'table' => 'menu_link_langs',
                    'alias' => 'MenuLinkLang',
                    'type' => 'left',
                    'conditions' => array(
                        'MenuLinkLang.menu_link_id = MenuLink.id',
                        'MenuLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'order' => 'MenuLinkLang.title asc',
            'recursive' => -1
        ));

        //Tout les blocs de liens
        $menu_select['block_link'] = $this->MenuBlockLang->find('list', array(
            'fields'        => array('menu_block_id', 'title'),
            'conditions'    => array('MenuBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
            'recursive'     => -1
        ));

        $this->set(compact('menu_select'));
    }
    public function admin_ajax_init_tree(){
        if($this->request->is('ajax')){
            $file = new File(Configure::read('Site.pathMenu').'menu.txt');
            $menu = $file->read();
            $menu = unserialize($menu);
            $menuDatas = $this->genereTree($menu, 1);
            $file->close();

            $this->jsonRender(array('data' => $menuDatas));
        }
    }
    public function admin_ajax_get_elements($type=false)
    {
        if (!$type || $type == 'action')
            $type = isset($this->request->data['type'])?$this->request->data['type']:false;
        $this->autoRender = false;
        $this->layout = false;

        $elements = array();
        if ($type == 'category'){
            $rows = $this->Category->find('all', array(
                'fields' => array('Category.id', 'Category.active', 'CategoryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'category_langs',
                        'alias' => 'CategoryLang',
                        'type' => 'inner',
                        'conditions' => array(
                            'CategoryLang.category_id = Category.id',
                            'CategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'CategoryLang.name asc',
                'recursive' => -1
            ));


            if (!empty($rows)){
                foreach ($rows AS $row){
                    $elements[] = array(
                        'id'        =>  $row['Category']['id'],
                        'active'    =>  $row['Category']['active'],
                        'name'      =>  $row['CategoryLang']['name'],
                        'type'      =>  'univers'
                    );
                }
            }
        }elseif ($type == 'cmscategories'){
            $rows = $this->PageCategory->find('all', array(
                'fields' => array('PageCategory.id', 'PageCategory.active', 'PageCategoryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'page_category_langs',
                        'alias' => 'PageCategoryLang',
                        'type' => 'inner',
                        'conditions' => array(
                            'PageCategoryLang.page_category_id = PageCategory.id',
                            'PageCategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'PageCategoryLang.name asc',
                'recursive' => -1
            ));

            if (!empty($rows)){
                foreach ($rows AS $row){
                    $elements[] = array(
                        'id'        =>  $row['PageCategory']['id'],
                        'active'    =>  $row['PageCategory']['active'],
                        'name'      =>  $row['PageCategoryLang']['name'],
                        'type'      =>  'cms'
                    );
                }
            }
        }elseif ($type == 'cms'){
            $this->Page->hasMany['PageLang']['fields'] = array('PageLang.name');
            $this->Page->hasMany['PageLang']['conditions'] = array(
                'PageLang.lang_id' => $this->Session->read('Config.id_lang')
            );



            $rows = $this->Page->find('all', array(
                'fields' => array('Page.id','Page.active'),
                'conditions' => array(),
                'order'     => 'Page.page_category_id ASC',
                'recursive' => 2
            ));

            if (!empty($rows)){
                foreach ($rows AS $row){
                    if (isset($row['PageLang']['0']['name'])){
                        $elements[] = array(
                            'id'        =>  $row['Page']['id'],
                            'active'    =>  $row['Page']['active'],
                            'name'      =>  $row['PageLang']['0']['name'],
                            'type'      =>  'cms'
                        );
                    }
                }
            }

            usort($elements, array($this, 'compareByName'));


        }elseif ($type == 'link'){
            $rows = $this->MenuLink->find('all',  array(
                'fields' => array('MenuLink.id', 'MenuLinkLang.title'),
                'joins' => array(
                    array(
                        'table' => 'menu_link_langs',
                        'alias' => 'MenuLinkLang',
                        'type' => 'left',
                        'conditions' => array(
                            'MenuLinkLang.menu_link_id = MenuLink.id',
                            'MenuLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'MenuLinkLang.title asc',
                'recursive' => -1
            ));

            if (!empty($rows)){
                foreach ($rows AS $row){
                    $elements[] = array(
                        'id'        =>  $row['MenuLink']['id'],
                        'active'    =>  1,
                        'name'      =>  $row['MenuLinkLang']['title'],
                        'type'      =>  'links'
                    );
                }
            }
        }elseif ($type == 'block'){
            $rows = $this->MenuBlockLang->find('all',  array(
                'fields' => array('MenuBlockLang.*'),
                'conditions'    => array('MenuBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));
            if (!empty($rows)){
                foreach ($rows AS $row){
                    $elements[] = array(
                        'id'        =>  $row['MenuBlockLang']['menu_block_id'],
                        'active'    =>  1,
                        'name'      =>  $row['MenuBlockLang']['title'],
                        'type'      =>  'blocks'
                    );
                }
            }
        }

        $this->jsonRender(array('items' => $elements));

    }
    public function admin_ajax_save_and_make()
    {
        $this->autoRender = false;
        $this->layout = false;

        $items = isset($this->request->data['items'])?$this->request->data['items']:array();
        if (empty($items))return false;

        $itemsSerialize = serialize($items);
        $file = new File(Configure::read('Site.pathMenu').'menu.txt', true, 0644);
        $file->write($itemsSerialize);
        $file->close();

        //On va chercher les langues
        $this->loadModel('Lang');
        $langs = $this->Lang->find('list', array(
            'fields'        => array('language_code'),
            'conditions'    => array('active' => 1),
            'recursive'     => -1
        ));

        //Pour chaque langue
        foreach($langs as $lang_id => $language){
            $menuDatas = $this->genereMenuDatas($items, $lang_id, $language);
            $menuHtml = '<div class="collapse navbar-collapse navbar-left navbar-main-collapse"><ul class="nav navbar-nav navbar-main">';
            $menuHtml.= $this->genereMenuHtml($menuDatas);
            $menuHtml.= '</ul></div>';
			$menuHtml = str_replace('@',$language,$menuHtml);
            //On supprime l'ancien cache
            Cache::delete('menu-navigation-'.$language, Configure::read('nomCacheNavigation'));
            //On crée le cache
            Cache::write('menu-navigation-'.$language, $menuHtml, Configure::read('nomCacheNavigation'));
        }

        $this->Session->setFlash(__('Le menu a été enregistré'), 'flash_success');
        $this->jsonRender(array('return' => true, 'url' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true))));
    }
    private function _getDatas($type="", $id=0, $lang_id=1, $lang_suffix=false)
    {
        if (!$type || !$id || !$lang_suffix)return false;
        switch ($type){
            case 'category':
                $valeurSpe = array(1);
                if(in_array($id, $valeurSpe)){
                    $result = $this->genereValeurSpe('category', $id, $lang_id, $lang_suffix);
                    if($result !== false)
                        return $result;
                }

                $page = $this->Category->CategoryLang->find("first", array(
                    'fields'     => array('name','link_rewrite'),
                    'conditions' => array(
                        'lang_id' => $lang_id,
                        'category_id' => $id,
                        'Category.active' => 1
                    )
                ));

                if (empty($page))return false;
                /*
                return array(
                    'title' => $page['CategoryLang']['name'],
                    'target' => '',
                    'link' =>  Router::url(array(
                        'language' => $lang_suffix,
                        'controller' => 'category',
                        'action' => 'displayUnivers',
                        'admin' => false,
                        'id' => $id,
                        'link_rewrite' => $page['CategoryLang']['link_rewrite']
                    ))
                );*/
                return array(
                    'title' => $page['CategoryLang']['name'],
                    'target' => '',
                    'link' =>  $this->getCategoryLink($page['CategoryLang']['link_rewrite'], $lang_suffix)
                );
                break;
            case 'block':
                $valeurSpe = array();
                if(in_array($id, $valeurSpe)){
                    $result = $this->genereValeurSpe('block', $id, $lang_id, $lang_suffix);
                    if($result !== false)
                        return $result;
                }

                $page = $this->MenuBlockLang->find("first", array(
                    'fields'     => array('title'),
                    'conditions' => array(
                        'lang_id' => $lang_id,
                        'menu_block_id' => $id
                    )
                ));
                if (empty($page))return false;
                return array(
                    'title' => $page['MenuBlockLang']['title'],
                    'target' => '',
                    'link' =>  ''
                );
            break;
            case 'link':
                $page = $this->MenuLink->find("first", array(
                    'fields' => array('MenuLink.target_blank', 'MenuLinkLang.title', 'MenuLinkLang.link'),
                    'conditions' => array(
                        'menu_link_id' => $id,
                        'lang_id' => $lang_id
                    ),
                    'joins' => array(
                        array(
                            'table' => 'menu_link_langs',
                            'alias' => 'MenuLinkLang',
                            'type' => 'inner',
                            'conditions' => array(
                                'MenuLinkLang.menu_link_id = MenuLink.id',
                                'MenuLinkLang.lang_id = '.$lang_id
                            )
                        )
                    ),
                ));
				
                if (empty($page))return false;
				$id_horoscope = '';
				if(substr_count($page['MenuLinkLang']['link'], 'horoscopes')){
					$tab_l = explode('/',$page['MenuLinkLang']['link']);
					$id_horoscope = $tab_l[3];
					$page['MenuLinkLang']['link'] = '/horoscopes';	
				}
				switch ($page['MenuLinkLang']['link']) {
					case '/reviews/display':
						$ret_link = $this->getReviewsLink($lang_suffix);
						
						return array(
							'title' => $page['MenuLinkLang']['title'],
							'target' => (int)$page['MenuLink']['target_blank']==1?'_blank':'',
							'link' =>   $this->getReviewsLink($lang_suffix)
						);
						break;
					case '/products/tarif':
						$ret_link = $this->getProductsLink($lang_suffix);
						
						return array(
							'title' => $page['MenuLinkLang']['title'],
							'target' => (int)$page['MenuLink']['target_blank']==1?'_blank':'',
							'link' =>   $this->getProductsLink($lang_suffix)
						);
						break;
					case '/horoscopes':
						return array(
							'title' => $page['MenuLinkLang']['title'],
							'target' => (int)$page['MenuLink']['target_blank']==1?'_blank':'',
							'link' =>   $this->getHoroscopeLink($id_horoscope,$lang_suffix)
						);
						break;
					default:
						return array(
							'title' => $page['MenuLinkLang']['title'],
							'target' => (int)$page['MenuLink']['target_blank']==1?'_blank':'',
							'link' =>  $page['MenuLinkLang']['link']
						);
						break;
				}
                break;
            case 'cms':
                $page = $this->Page->PageLang->find("first", array(
                    'fields'     => array('Page.id','PageLang.link_rewrite','PageLang.name'),
                    'conditions' => array(
                        'PageLang.lang_id' => $lang_id,
                        'PageLang.page_id' => $id,
                        'Page.active'   => 1
                    )
                ));
                if (empty($page))return false;
                /*
                return array(
                    'title' => $page['PageLang']['name'],
                    'target' => '',
                    'link' =>  Router::url(array(
                        'language' => $lang_suffix,
                        'controller' => 'pages',
                        'action' => 'display',
                        'admin' => false,
                        'id' => $id,
                        'link_rewrite' => $page['PageLang']['link_rewrite']
                    ))
                );
                */

                return array(
                    'title' => $page['PageLang']['name'],
                    'target' => '',
                    'id'   => $page['Page']['id'],
                    'link' =>  $this->getCmsPageLink($page['PageLang']['link_rewrite'], $lang_suffix)
                );
                break;
        }
    }
    private function genereValeurSpe($type, $id, $lang_id = 0, $lang_suffix= ''){
        if(empty($type) || empty($id))
            return false;

        switch($type){
            case 'category' :
                //Page d'accueil
                if($id == 1){
                    $page = $this->Category->CategoryLang->find("first", array(
                        'fields'     => array('name'),
                        'conditions' => array(
                            'lang_id' => $lang_id,
                            'category_id' => $id
                        )
                    ));

                    if (empty($page))return false;
                    return array(
                        'title' => $page['CategoryLang']['name'],
                        'target' => '',
                        'link' =>  str_replace($lang_suffix,'',Router::url(array(
                                'language' => $lang_suffix,
                                'controller' => 'home',
                                'action' => 'index',
                                'admin' => false
                            )))
                    );
                }
                break;
            case 'block' :
                //Horoscope
                if($id == 2){
                    //On récupère les signes
                    $this->loadModel('HoroscopeSign');
                    $signs = $this->HoroscopeSign->find('all', array(
                        'fields'        => array('sign_id', 'link_rewrite', 'name'),
                        'conditions'    => array('lang_id' => $lang_id),
                        'recursive'     => -1
                    ));

                    //Si pas de signes ou pas complet en récupère en français
                    if(empty($signs) || count($signs) < 12){
                        $signs = $this->HoroscopeSign->find('all', array(
                            'fields'        => array('sign_id', 'link_rewrite', 'name'),
                            'conditions'    => array('lang_id' => 1),
                            'recursive'     => -1
                        ));

                        if(empty($signs))
                            return false;
                    }
                    //Nom du bloc
                    $page = $this->MenuBlockLang->find("first", array(
                        'fields'     => array('title'),
                        'conditions' => array(
                            'lang_id' => $lang_id,
                            'menu_block_id' => $id
                        )
                    ));
                    if (empty($page))return false;

                    //On génère les liens de l'horosope
                    $children = array();
                    foreach($signs as $sign){
                        array_push($children, array(
                            'title'     => __('Horoscope du jour').' '.$sign['HoroscopeSign']['name'],
                            'target'    => '',
                            'link'      => Router::url(array(
                                    'language' => $lang_suffix,
                                    'controller' => 'horoscopes',
                                    'action' => 'display',
                                    'id'    => $sign['HoroscopeSign']['sign_id'],
                                    'admin' => false
                                ))
                        ));

                    }

                    return array(
                        'title' => $page['MenuBlockLang']['title'],
                        'target' => '',
                        'link' =>  '',
                        'children'  => $children
                    );
                }
                break;
        }

    }
    private function genereMenuDatas($node=false, $lang_id=0, $lang_suffix=false)
    {
        if (!$node || !$lang_id || !$lang_suffix)return false;
        $out = array();
        foreach ($node AS $row){
            $tmp = $this->_getDatas($row['type'], $row['id'], $lang_id, $lang_suffix);
            if ($tmp){
                if (!empty($row['children']) && $this->allowedChildrenValeurSpe($row['type'], $row['id'])){
                    $datas = $this->genereMenuDatas($row['children'], $lang_id, $lang_suffix);
                    if ($datas)
                        $tmp['children'] = $datas;
                }
                $out[] = $tmp;
            }
        }
        return $out;
    }
    private function allowedChildrenValeurSpe($type, $id){
        switch($type){
            case 'block' :
                break;
        }

        return true;
    }
    private function genereTree($node, $lang_id = 0){
        if(empty($node) || $lang_id == 0)
            return false;

        $out = array();
        foreach($node as $row){
            if(isset($row['children']) && !empty($row['children'])){
                $out[] = array(
                    'label'     => $this->getLabel($row['type'], $row['id'], $lang_id),
                    'type'      => $row['type'],
                    'id'        => $row['id'],
                    'children'  => self::genereTree($row['children'], $lang_id)
                );
            }else{
                $out[] = array(
                    'label' => $this->getLabel($row['type'], $row['id'], $lang_id),
                    'type'  => $row['type'],
                    'id'    => $row['id']
                );
            }
        }

        return $out;
    }
    private function genereMenuHtml($node, $level_depth = 1){
        if(empty($node))
            return '';

        $out = '';
        //On parcours le tableau
        foreach($node as $row){
            //Selon le niveau de profondeur
            switch($level_depth){
                case 1 :
					if($row['title'] == 'Horoscope'){
						$row['link'] = '/horoscopes/index';
						$row['children'] = '';
					}
					
					//Pas de lien
                    if(empty($row['link'])){
						
						if($row['title'] == 'Tarifs'){
                        $out.= '<li class="animated fadeIn dropdown small-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row['title'].' <i class="fa fa-angle-down" aria-hidden="true"></i></a>';
 								}else{
                        $out.= '<li class="animated fadeIn dropdown mega-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row['title'].' <i class="fa fa-angle-down" aria-hidden="true"></i></a>';
 							}
						
					}else{
						
						if(substr_count( $row['title'] , 'm-sep')){
							$out.= '<li class="animated fadeIn m-sep-container"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a>';
						}else{
						
                    	if($row['title'] != 'Acheter des minutes' && $row['title'] != 'Tarifs'){
							$out.= '<li class="animated fadeIn"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a>';
							}else{
								$out.= '<li class="animated fadeIn hidden-navmobile"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a>';
							//	$out.= '<li class="animated fadeIn"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'><i class="fa fa-shopping-cart" aria-hidden="true"></i> <strong> <span class="hide-tablet show-mobile">'.$row['title'].'</span></strong></a>';	
							}
						}
					}

                    //Enfant ??
                    if(isset($row['children']) && !empty($row['children'])){//cntelts_'.count($node).' subc_'.Inflector::slug(strtolower($row['title']), '-').'
					
						if($row['title'] == 'Consultations' || $row['title'] == 'Tarots' || $row['title'] == 'Tirages Tarots'){
							 $out.= '<div class="dropdown-menu small-dropdown-menu"><div class="container"><div class="row">';
 								}else{
							 $out.= '<div class="dropdown-menu mega-dropdown-menu "><div class="container"><div class="row">';	
 							}
					
                       
                        $out.= self::genereMenuHtml($row['children'], ($level_depth+1));
                        $out.= '</div></div></div>';
                    }
                    $out.= '</li>';
                    break;
                case 2 :
				
					if($row['title'] == 'Tarologues & Cartomanciens') $row['link2'] = '/@/tarologue-7';
					if($row['title'] == 'Mediums & Voyants') $row['link2'] = '/@/voyants-5';
					if($row['title'] == 'Astrologues') $row['link2'] = '/@/astrologue-2';
					if($row['title'] == 'Numérologues') $row['link2'] = '/@/numerologue-6';
					if($row['title'] == 'Magnétiseurs') $row['link2'] = '/@/magnetiseur-20';
					if($row['title'] == 'Médiums') $row['link2'] = '/@/mediums-27';
					if($row['title'] == 'Voyants') $row['link2'] = '/@/voyants-5';
					
					if($row['title'] == 'agents Amour et Sentiment') $row['link2'] = '/@/agents-amour-et-sentiment/consultation-amour';
					if($row['title'] == 'agents travail et carrière') $row['link2'] = '/@/agents-travail-et-carriere/consultation-agents-travail';
					if($row['title'] == 'agents famille et enfant') $row['link2'] = '/@/agents-famille-et-enfant/consultation-agents-famille';
					if($row['title'] == 'agents Finance et Matériel') $row['link2'] = '/@/agents-finance-et-materiel/consultation-agents-finance';
					
					
					//if($row['title'] == 'agents par téléphone') $row['link2'] = '/@/consultations/agents-par-telephone';
					//if($row['title'] == 'agents par tchat') $row['link2'] = '/@/consultations/agents-par-tchat';
					//if($row['title'] == 'agents par mail') $row['link2'] = '/@/consultations/agents-par-mail';
					
					if(empty($row['link2'])){
						//Pas de lien
						if(empty($row['link'])){
							//$out.= '<div class="sub_link"><li class="child_title">'.$row['title'].'</li>';
							//Enfant ??
							if(isset($row['children']) && !empty($row['children'])){
								$out.= '<div class="col-md-3 col-sm-3 col-xs-12">
										<ul class="list-unstyled"><li class="dropdown-header"><span class="link_white">'.$row['title'].'</span></li>';
								$out.= self::genereMenuHtml($row['children'], ($level_depth+1));
								$out.= '</ul></div>';
							}
							//$out.= '</div>';
						}
						else{
							//PATCH
							if( $row['link'] != '/fre/tarifs-et-modalites/acces-direct-en-consultation-audiotel'){
								if(substr_count($row['link'],'tarifs-et-modalites')){
									$out.= '<div class="col-md-12 col-sm-12 col-xs-12"><ul class="list-unstyled"><li><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a></li></ul></div>';
								}else{
									$force_title = false;
									if($row['title'] == 'agents par téléphone')$force_title = true;
									if($row['title'] == 'agents par tchat')$force_title = true;
									if($row['title'] == 'agents par mail')$force_title = true;
									if($row['title'] == 'Tirage amour')$force_title = true;
									if($row['title'] == 'Tirage célibataire')$force_title = true;
									if($row['title'] == 'Tirage de tarot Oui Non')$force_title = true;
									if($row['title'] == 'Tirage agents')$force_title = true;
									if($row['title'] == 'Tirage tarot marseille')$force_title = true;
									
									if($force_title){
										$out.= '<div class="col-md-3 col-sm-3 col-xs-12"><ul class="list-unstyled"><li><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').' class="link_purple">'.$row['title'].'</a></li></ul></div>';
									}else{
										$out.= '<div class="col-md-3 col-sm-3 col-xs-12"><ul class="list-unstyled"><li><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a></li></ul></div>';
									}
									
								}
							}
						}
					   }
                    else{
						//$out.= '<div class="sub_link"><li class="child_title"><a href="'.$row['link2'].'" class="link_white">'.$row['title'].'</a></li>';
							//Enfant ??
							if(isset($row['children']) && !empty($row['children'])){
								$out.= '<div class="col-md-3 col-sm-3 col-xs-12">
										<ul class="list-unstyled"><li class="dropdown-header"><a href="'.$row['link2'].'" class="link_white">'.$row['title'].'</a></li>';
								$out.= self::genereMenuHtml($row['children'], ($level_depth+1));
								$out.= '</ul></div>';
							}else{
								if($row['title'] == 'Tarifs'){
									$out.= '<li class="animated fadeIn dropdown small-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row['title'].' <i class="fa fa-angle-down" aria-hidden="true"></i></a>';
								}else{
									$out.= '<li class="animated fadeIn dropdown mega-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$row['title'].' <i class="fa fa-angle-down" aria-hidden="true"></i></a>';
								}
							}
						//	$out.= '</div>';
					}
				    break;
                case 3 :
                    //Pas de lien
                    if(empty($row['link'])){
                        $out.= '<li class="animated fadeIn">'.$row['title'];
                        //Enfant ??
                        if(isset($row['children']) && !empty($row['children'])){
                            $out.= '<div class="dropdown-menu mega-dropdown-menu"><div class="container">
								<div class="row">';
                            $out.= self::genereMenuHtml($row['children'], ($level_depth+1));
                            $out.= '</div></div></div>';
                        }
                        $out.= '</li>';
                    }
                    else
                        $out.= '<li class="animated fadeIn"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a></li>';
                    break;
                default :
                    //Pas de lien
                    if(empty($row['link'])){
                        $out.= '<li class="animated fadeIn">'.$row['title'];
                        //Enfant ??
                        if(isset($row['children']) && !empty($row['children'])){
                            $out.= '<div class="dropdown-menu mega-dropdown-menu"><div class="container">
								<div class="row">';
                            $out.= self::genereMenuHtml($row['children'], ($level_depth+1));
                            $out.= '</div></div></div>';
                        }
                        $out.= '</li>';
                    }
                    else
                        $out.= '<li class="animated fadeIn"><a href="'.$row['link'].'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').'>'.$row['title'].'</a></li>';
            }
        }

        return $out;
    }
    private function getLabel($type, $id, $lang_id){
        if(empty($type) || empty($id))
            return false;

        //Selon le type
        switch($type){
            case 'category' :
                $label = $this->Category->CategoryLang->field("name", array(
                    'lang_id' => $lang_id,
                    'category_id' => $id
                ));
                break;
            case 'block' :
                $label = $this->MenuBlockLang->field("title", array(
                    'lang_id' => $lang_id,
                    'menu_block_id' => $id
                ));
                break;
            case 'cms' :
                $label = $this->Page->PageLang->field("name", array(
                    'PageLang.lang_id' => $lang_id,
                    'PageLang.page_id' => $id
                ));
                break;
            case 'link' :
                $label = $this->MenuLinkLang->field("title", array(
                    'MenuLinkLang.menu_link_id' => $id,
                    'MenuLinkLang.lang_id'      => $lang_id
                ));
                break;
        }

        //Si pas de label
        if(!$label)
            $label = __('Sans nom');

        return $label;
    }

    protected function compareByName($a, $b) {

        return strcmp($a['name'], $b['name']);
    }








    public function admin_add_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(isset($requestData['idLink'])){
                $this->getView($requestData['idLink'],'/Elements/admin_form_link');
            }
            $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau lien.')));
        }
    }

    public function admin_add_block_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(isset($requestData['idBlockLink'])){
                $this->getView($requestData['idBlockLink'],'/Elements/admin_form_block_link');
            }
            $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau bloc de lien.')));
        }
    }

    public function admin_create_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            $requestData['MenuLink'] = Tools::checkFormField($requestData['MenuLink'], array('target_blank'));
            if($requestData['MenuLink'] === false)
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));

            //Pour chaque lang
            foreach($requestData['MenuLinkLang'] as $key => $val){
                //Si le formulaire est vide
                if(count(array_keys($val, '')) >= 2)
                    //On le supprime
                    unset($requestData['MenuLinkLang'][$key]);
            }

            //Si aucune langue n'est renseigné
            if(empty($requestData['MenuLinkLang']))
                $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

            //Les langues restantes
            foreach($requestData['MenuLinkLang'] as $lang){
                $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                if($lang === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));
            }

            //On save le nouveau lien
            $this->MenuLink->create();
            if($this->MenuLink->save($requestData['MenuLink'])){
                //On sauvegarde les langues
                foreach($requestData['MenuLinkLang'] as $lang){
                    $lang['menu_link_id'] = $this->MenuLink->id;
                    $this->MenuLinkLang->save($lang);
                }

                $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été crée.'),
                                        'update' => Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true))));
            }else
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la sauvegarde du lien.')));
        }
    }

    public function admin_create_block_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            //Pour chaque lang
            foreach($requestData['MenuBlockLang'] as $key => $val){
                //Si le formulaire est vide
                if(count(array_keys($val, '')) >= 1)
                    //On le supprime
                    unset($requestData['MenuBlockLang'][$key]);
            }

            //Si aucune langue n'est renseigné
            if(empty($requestData['MenuBlockLang']))
                $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

            //Les langues restantes
            foreach($requestData['MenuBlockLang'] as $lang){
                $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                if($lang === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le bloc de lien')));
            }


            //On save le nouveau bloc
            $this->loadModel('MenuBlock');
            $this->MenuBlock->create();
            if($this->MenuBlock->save(array('active' => 1))){
                //On sauvegarde les langues
                foreach($requestData['MenuBlockLang'] as $lang){
                    $lang['menu_block_id'] = $this->MenuBlock->id;
                    $this->MenuBlockLang->save($lang);
                }

                $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été crée.'),
                                        'update' => Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true))));
            }else
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la sauvegarde du bloc de lien.')));
        }
    }

    public function admin_update(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(isset($requestData['mode'])){
                //Selon l'update demandé
                switch($requestData['mode']){
                    case 'menu_link' :
                        //Tout les liens
                        $links = $this->MenuLink->find('all', array(
                            'fields' => array('MenuLink.id', 'MenuLinkLang.title'),
                            'joins' => array(
                                array(
                                    'table' => 'menu_link_langs',
                                    'alias' => 'MenuLinkLang',
                                    'type' => 'left',
                                    'conditions' => array(
                                        'MenuLinkLang.menu_link_id = MenuLink.id',
                                        'MenuLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                                    )
                                )
                            ),
                            'order' => 'MenuLinkLang.title asc',
                            'recursive' => -1
                        ));

                        $this->getView(0, '/Elements/admin_select_link', $links);
                        break;
                    case 'menu_block_link' :
                        //Tout les blocs de liens
                        $blocks = $this->MenuBlockLang->find('list', array(
                            'fields'        => array('menu_block_id', 'title'),
                            'conditions'    => array('MenuBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                            'recursive'     => -1
                        ));

                        $this->getView(0, '/Elements/admin_select_block_link', $blocks);
                        break;
                    case 'link_action' :
                        $this->getView($requestData['idLink'], '/Elements/admin_link_action');
                        break;
                    case 'block_link_action' :
                        $this->getView($requestData['idLink'], '/Elements/admin_block_link_action');
                        break;
                    case 'data' :
                        //On récupère les infos du lien
                        $link = $this->MenuLink->find('all', array(
                            'fields' => array('MenuLink.*', 'MenuLinkLang.*'),
                            'conditions' => array('MenuLink.id' => $requestData['idLink']),
                            'joins' => array(
                                array(
                                    'table' => 'menu_link_langs',
                                    'alias' => 'MenuLinkLang',
                                    'type' => 'left',
                                    'conditions' => array('MenuLinkLang.menu_link_id = MenuLink.id')
                                )
                            ),
                            'recursive' => -1
                        ));

                        if(empty($link))
                            $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Lien introuvable')));

                        //Pour chaque langue
                        $data = array();
                        foreach($link as $key => $lang){
                            $data[$lang['MenuLinkLang']['lang_id']]['title'] = $lang['MenuLinkLang']['title'];
                            $data[$lang['MenuLinkLang']['lang_id']]['link'] = $lang['MenuLinkLang']['link'];
                        }

                        //On rajoute les infos du lien
                        $data['MenuLink'] = $link[0]['MenuLink'];

                        //On va chercher la vue
                        $this->getView($data['MenuLink']['id'], '/Elements/admin_form_link', $data);
                        break;
                    case 'block_data' :
                        //On récupère les infos du block
                        $block = $this->MenuBlockLang->find('all', array(
                            'fields'        => array('MenuBlockLang.*'),
                            'conditions'    => array('MenuBlockLang.menu_block_id' => $requestData['idLink']),
                            'recursive'     => -1
                        ));

                        if(empty($block))
                            $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Bloc introuvable')));

                        //Pour chaque langue
                        $data = array();
                        foreach($block as $key => $lang){
                            $data[$lang['MenuBlockLang']['lang_id']]['title'] = $lang['MenuBlockLang']['title'];
                        }

                        //On rajoute les infos du lien
                        $data['menu_block_id'] = $block[0]['MenuBlockLang']['menu_block_id'];

                        //On va chercher la vue
                        $this->getView($data['menu_block_id'], '/Elements/admin_form_block_link', $data);
                        break;
                }
            }
        }
    }

    public function admin_edit_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            //L'url pour de l'action update
            $updateUrl = Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true));

            $requestData['MenuLink'] = Tools::checkFormField($requestData['MenuLink'], array('target_blank', 'id'), array('id'));
            if($requestData['MenuLink'] === false)
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));

            //Pour chaque lang
            foreach($requestData['MenuLinkLang'] as $key => $val){
                //Si le formulaire est vide
                if(count(array_keys($val, '')) >= 2)
                    //On le supprime
                    unset($requestData['MenuLinkLang'][$key]);
            }

            //Si aucune langue n'est renseigné
            if(empty($requestData['MenuLinkLang']))
                $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

            //Les langues restantes
            foreach($requestData['MenuLinkLang'] as $lang){
                $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                if($lang === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));
            }

            //On supprime les langues actuelles
            $this->MenuLinkLang->deleteAll(array('MenuLinkLang.menu_link_id' => $requestData['MenuLink']['id']), false);

            //On met à jour le lien
            $this->MenuLink->id = $requestData['MenuLink']['id'];
            if($this->MenuLink->saveField('target_blank', $requestData['MenuLink']['target_blank'])){
                //On sauvegarde les langues
                foreach($requestData['MenuLinkLang'] as $lang){
                    $lang['menu_link_id'] = $requestData['MenuLink']['id'];
                    $this->MenuLinkLang->save($lang);
                }

                //On va charcher le nom du lien pour la langue en cours
                $link_name = $this->MenuLinkLang->find('first', array(
                    'fields' => array('MenuLinkLang.title'),
                    'conditions' => array('MenuLinkLang.menu_link_id' => $requestData['MenuLink']['id'], 'MenuLinkLang.lang_id' => $this->Session->read('Config.id_lang')),
                    'recursive' => -1
                ));

                $link_name = (isset($link_name['MenuLinkLang']['title']) && !empty($link_name['MenuLinkLang']['title']) ?$link_name['MenuLinkLang']['title']:__('Sans titre'));

                $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été modifié.'), 'id' => $requestData['MenuLink']['id'], 'name' => $link_name,
                                        'update' => $updateUrl));
            }else
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la MAJ du lien.')));
        }
    }

    public function admin_edit_block_link(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            //L'url pour de l'action update
            $updateUrl = Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true));

            //L'id du bloc
            if(!isset($requestData['MenuBlockLang']['menu_block_id']) || empty($requestData['MenuBlockLang']['menu_block_id']))
                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));

            $idBlock = $requestData['MenuBlockLang']['menu_block_id'];
            unset($requestData['MenuBlockLang']['menu_block_id']);

            //Pour chaque lang
            foreach($requestData['MenuBlockLang'] as $key => $val){
                //Si le formulaire est vide
                if(count(array_keys($val, '')) >= 1)
                    //On le supprime
                    unset($requestData['MenuBlockLang'][$key]);
            }

            //Si aucune langue n'est renseigné
            if(empty($requestData['MenuBlockLang']))
                $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

            //Les langues restantes
            foreach($requestData['MenuBlockLang'] as $lang){
                $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                if($lang === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));
            }

            //On supprime les langues actuelles
            $this->MenuBlockLang->deleteAll(array('MenuBlockLang.menu_block_id' => $idBlock), false);

            //On met à jour le bloc
            //On sauvegarde les langues
            foreach($requestData['MenuBlockLang'] as $lang){
                $lang['menu_block_id'] = $idBlock;
                $this->MenuBlockLang->save($lang);
            }

            $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été modifié.'), 'update' => $updateUrl));
        }
    }

    public function admin_delete_link($id){
        if(empty($id) || !is_numeric($id)){
            $this->Session->setFlash(__('Impossible de supprimer le lien'),'flash_warning');
            $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true));
        }

        //On supprime les langues
        $this->MenuLinkLang->deleteAll(array('MenuLinkLang.menu_link_id' => $id), false);
        //On supprime le lien
        $this->MenuLink->deleteAll(array('MenuLink.id' => $id), false);
        //On le supprime du menu
        $this->TopMenu->deleteAll(array('TopMenu.contenu_type' => 'link', 'TopMenu.contenu_id' => $id), false);

        $this->Session->setFlash(__('Le lien a été supprimé.'),'flash_success');
        $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true), false);
    }

    public function admin_delete_block_link($id){
        if(empty($id) || !is_numeric($id)){
            $this->Session->setFlash(__('Impossible de supprimer le bloc lien'),'flash_warning');
            $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true));
        }

        //On supprime les langues
        $this->MenuBlockLang->deleteAll(array('MenuBlockLang.menu_block_id' => $id), false);
        //On supprime le lien
        $this->MenuBlock->deleteAll(array('MenuBlock.id' => $id), false);

        $this->Session->setFlash(__('Le bloc de lien a été supprimé.'),'flash_success');
        $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true), false);
    }

    private function getView($idLink, $view, $data = array()){
        $this->layout = '';
        $this->loadModel('Lang');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);
        $this->set(compact('idLink', 'langs', 'data'));
        $response = $this->render($view);

        //On retourne la vue
        $this->jsonRender(array('return' => true, 'html' => $response->body()));
    }

    public function generateMenu(){
        $file = new File(Configure::read('Site.pathMenu').'menu.txt');
        $items = $file->read();
        $items = unserialize($items);
        $file->close();

        //On va chercher les langues
        $this->loadModel('Lang');
        $langs = $this->Lang->find('list', array(
            'fields'        => array('language_code'),
            'conditions'    => array('active' => 1),
            'recursive'     => -1
        ));

        //Pour chaque langue
        foreach($langs as $lang_id => $language){
            $menuDatas = $this->genereMenuDatas($items, $lang_id, $language);
            $menuHtml = '<div class="collapse navbar-collapse navbar-left navbar-main-collapse"><ul class="nav navbar-nav navbar-main">';
            $menuHtml.= $this->genereMenuHtml($menuDatas);
            $menuHtml.= '</ul></div>';
			$menuHtml = str_replace('@',$language,$menuHtml);
            //On supprime l'ancien cache
            Cache::delete('menu-navigation-'.$language, Configure::read('nomCacheNavigation'));
            //On crée le cache
            Cache::write('menu-navigation-'.$language, $menuHtml, Configure::read('nomCacheNavigation'));
        }
    }
	
	public function admin_killroutes(){
		$this->autoRender = false;
        $this->layout = false;
		foreach (new DirectoryIterator(TMP . 'routes') as $fileInfo) {
			if(!$fileInfo->isDot()) {
				unlink($fileInfo->getPathname());
			}
		}
		$this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
	}
}
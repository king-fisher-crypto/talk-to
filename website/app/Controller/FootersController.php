<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');
    App::uses('File', 'Utility');

    class FootersController extends AppController {
        public $components = array('Paginator');
        public $uses = array('Category', 'PageCategory', 'FooterLink', 'FooterLinkLang','Page','FooterBlockLang', 'FooterBlock');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'footers')));

        public function beforeFilter() {

            parent::beforeFilter();
        }
        public function admin_tmp()
        {
            $this->layout = false;
            $this->autoRender = false;

            $this->admin_ajax_get_elements('blocks');
        }
        public function admin_footer()
        {
            //Tout les liens
            $footer_select['link'] = $this->FooterLink->find('all',  array(
                'fields' => array('FooterLink.id', 'FooterLinkLang.title'),
                'joins' => array(
                    array(
                        'table' => 'footer_link_langs',
                        'alias' => 'FooterLinkLang',
                        'type' => 'left',
                        'conditions' => array(
                            'FooterLinkLang.footer_link_id = FooterLink.id',
                            'FooterLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'FooterLinkLang.title asc',
                'recursive' => -1
            ));

            //Tout les blocs de liens
            $footer_select['block_link'] = $this->FooterBlockLang->find('list', array(
                'fields'        => array('footer_block_id', 'title'),
                'conditions'    => array('FooterBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive'     => -1
            ));

            $this->set(compact('footer_select'));
        }
        public function admin_ajax_init_tree(){
            if($this->request->is('ajax')){
                $file = new File(Configure::read('Site.pathMenu').'footer.txt');
                $footer = $file->read();
                $footer = unserialize($footer);
                $footerDatas = $this->genereTree($footer, 1);
                $file->close();

                $this->jsonRender(array('data' => $footerDatas));
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
                $rows = $this->FooterLink->find('all',  array(
                    'fields' => array('FooterLink.id', 'FooterLinkLang.title'),
                    'joins' => array(
                        array(
                            'table' => 'footer_link_langs',
                            'alias' => 'FooterLinkLang',
                            'type' => 'left',
                            'conditions' => array(
                                'FooterLinkLang.footer_link_id = FooterLink.id',
                                'FooterLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                            )
                        )
                    ),
                    'order' => 'FooterLinkLang.title asc',
                    'recursive' => -1
                ));

                if (!empty($rows)){
                    foreach ($rows AS $row){
                        $elements[] = array(
                            'id'        =>  $row['FooterLink']['id'],
                            'active'    =>  1,
                            'name'      =>  $row['FooterLinkLang']['title'],
                            'type'      =>  'links'
                        );
                    }
                }
            }elseif ($type == 'block'){
                $rows = $this->FooterBlockLang->find('all',  array(
                    'fields' => array('FooterBlockLang.*'),
                    'conditions'    => array('FooterBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                    'recursive' => -1
                ));
                if (!empty($rows)){
                    foreach ($rows AS $row){
                        $elements[] = array(
                            'id'        =>  $row['FooterBlockLang']['footer_block_id'],
                            'active'    =>  1,
                            'name'      =>  $row['FooterBlockLang']['title'],
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
            $file = new File(Configure::read('Site.pathMenu').'footer.txt', true, 0644);
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
                $footerDatas = $this->genereFooterDatas($items, $lang_id, $language);
                $footerHtml = $this->genereFooterHtml($footerDatas, 1, true);
                $footerHtml.= '</div>';

                //On supprime l'ancien cache
                Cache::delete('footer-navigation-'.$language, Configure::read('nomCacheNavigation'));
                //On crée le cache
                Cache::write('footer-navigation-'.$language, $footerHtml, Configure::read('nomCacheNavigation'));
            }

            $this->Session->setFlash(__('Le footer a été enregistré'), 'flash_success');
            $this->jsonRender(array('return' => true, 'url' => Router::url(array('controller' => 'footers', 'action' => 'footer', 'admin' => true))));
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
					
					$seo_word = '';
					App::import("Model", "CategoryLang");
					$model = new CategoryLang();
                    $page = $model->find("first", array(
                        'fields'     => array('name','link_rewrite','cat_rewrite'),
                        'conditions' => array(
                            'lang_id' => $lang_id,
                            'category_id' => $id,
                            'Category.active'   => 1,
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
					/*$seo_words_from_lang_code = Configure::read('Routing.categories');
					$cat_rewrite = $page['CategoryLang']['cat_rewrite'];
					$seo_word = isset($cat_rewrite)?$cat_rewrite:$seo_words_from_lang_code[$lang_suffix];
					$opt  = array(
						'language' => $lang_suffix,
						'controller' => 'category',
						'action' => 'displayUnivers',
						'admin' => false,
						'link_rewrite' => $page['CategoryLang']['link_rewrite'],
						'seo_word' => 'category'
					);
					$test = $this->getCategoryLink($page['CategoryLang']['link_rewrite'], $lang_suffix, true);
					var_dump($test);
					$roots =  Router::url($opt, false);*/
					
					$roots = '/'.$lang_suffix.'/'.$page['CategoryLang']['link_rewrite'].'-'.$id;
					
					//$this->getCategoryLink($page['CategoryLang']['link_rewrite'], $lang_suffix, true)
                    return array(
                        'title' => $page['CategoryLang']['name'],
                        'target' => '',
						'rel' => '',
                        'link' => $roots 
                    );
                    break;
                case 'block':
                    $valeurSpe = array();
                    if(in_array($id, $valeurSpe)){
                        $result = $this->genereValeurSpe('block', $id, $lang_id, $lang_suffix);
                        if($result !== false)
                            return $result;
                    }

                    $page = $this->FooterBlockLang->find("first", array(
                        'fields'     => array('title'),
                        'conditions' => array(
                            'lang_id' => $lang_id,
                            'footer_block_id' => $id
                        )
                    ));
                    if (empty($page))return false;
                    return array(
                        'title' => $page['FooterBlockLang']['title'],
                        'target' => '',
						'rel' => '',
                        'link' =>  ''
                    );
                    break;
                case 'link':
                    $page = $this->FooterLink->find("first", array(
                        'fields' => array('FooterLink.target_blank', 'FooterLinkLang.title', 'FooterLinkLang.link'),
                        'conditions' => array(
                            'footer_link_id' => $id,
                            'lang_id' => $lang_id
                        ),
                        'joins' => array(
                            array(
                                'table' => 'footer_link_langs',
                                'alias' => 'FooterLinkLang',
                                'type' => 'inner',
                                'conditions' => array(
                                    'FooterLinkLang.footer_link_id = FooterLink.id',
                                    'FooterLinkLang.lang_id = '.$lang_id
                                )
                            )
                        ),
                    ));
                    if (empty($page))return false;
					$rel='';
					#if($page['FooterLink']['target_blank'])$rel='nofollow';
                    return array(
                        'title' => $page['FooterLinkLang']['title'],
						'rel' => $rel,
                        'target' => (int)$page['FooterLink']['target_blank']==1?'_blank':'',
                        'link' =>  $page['FooterLinkLang']['link']
                    );
                    break;
                case 'cms':
                    $page = $this->Page->PageLang->find("first", array(
                        'fields'     => array('PageLang.link_rewrite','PageLang.name'),
                        'conditions' => array(
                            'PageLang.lang_id' => $lang_id,
                            'PageLang.page_id' => $id,
                            'Page.active'      => 1
                        )
                    ));
                //    if(empty($page) ||
            //((int)$this->Session->read('Config.id_domain') === 19 && $id == 36))
           
         if(empty($page))return false;
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
						'rel' => '',
                        'link' =>  $this->getCmsPageLink($page['PageLang']['link_rewrite'], $lang_suffix)
                    );
                    break;
                case 'sautdecolonne' :
                    return array(
                        'title' => 'new_column',
                        'target' => '',
						'rel' => '',
                        'link'  => ''
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
                            'link' =>  Router::url(array(
                                    'language' => $lang_suffix,
                                    'controller' => 'home',
                                    'action' => 'index',
                                    'admin' => false
                                ))
                        );
                    }
                    break;
                case 'block' :
                    //Horoscope
                    if($id == 0){
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
                        $page = $this->FooterBlockLang->find("first", array(
                            'fields'     => array('title'),
                            'conditions' => array(
                                'lang_id' => $lang_id,
                                'footer_block_id' => $id
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
                                        'language' => $lang_id,//$lang_suffix
                                        'controller' => 'horoscopes',
                                        'action' => 'display',
                                        'id'    => $sign['HoroscopeSign']['sign_id'],
                                        'admin' => false
                                    ))
                            ));

                        }

                        return array(
                            'title' => $page['FooterBlockLang']['title'],
                            'target' => '',
                            'link' =>  '',
                            'children'  => $children
                        );
                    }
                    else if($id == 4 || $id == 11){
                        return array(
                            'title' => 'new_column',
                            'target' => '',
                            'link'  => ''
                        );
                    }
                    break;
            }

        }
        private function genereFooterDatas($node=false, $lang_id=0, $lang_suffix=false)
        {
            if (!$node || !$lang_id || !$lang_suffix)return false;
            $out = array();
            foreach ($node AS $row){
                $tmp = $this->_getDatas($row['type'], $row['id'], $lang_id, $lang_suffix);
                if ($tmp){
                    if (!empty($row['children']) && $this->allowedChildrenValeurSpe($row['type'], $row['id'])){
                        $datas = $this->genereFooterDatas($row['children'], $lang_id, $lang_suffix);
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
        private function genereFooterHtml($node, $level_depth = 1, $start = false){
            if(empty($node))
                return '';

            $column = 1;
            $out = '';
            if($start)
                $out.= '<div class="col-sm-3 col-md-3 col-lg-3">';
            //On parcours le tableau
            foreach($node as $key => $row){
                //Selon le niveau de profondeur
				
				$rel = '';
				if($row['rel']=="nofollow")$rel = ' rel="nofollow" ';
				
                switch($level_depth){
                    case 1 :
                        //Pas de lien
                        if(empty($row['link'])){
                            if($row['title'] === 'new_column'){
                                $column++;
                                $out.= '</div>'.(($column-1)%4 == 0 ?'<div class="clearfix"></div>':'').'<div class="col-sm-3'.(($column)%4 == 0 ?' ft_last':'').'">';
                            }
                            else
                                $out.= '<div class="foot-title">'.$row['title'].'</div>';
                        }
                        else
                            $out.= '<li><a href="'.$row['link'].'" title="'.strip_tags($row['title']).'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').' '.$rel.'>'.$row['title'].'</a></li>';

                        //Enfant ??
                        if(isset($row['children']) && !empty($row['children'])){
                            $out.= '<ul class="list-unstyled menu-list">';
                            $out.= self::genereFooterHtml($row['children'], ($level_depth+1));
                            $out.= '</ul>';
                        }
                        break;
                    case 2 :
                        //Pas de lien
                        if(empty($row['link'])){
                            $out.= '<li>';
                            $out.= '<h6>'.$row['title'].'</h6>';
                            //Enfant ??
                            if(isset($row['children']) && !empty($row['children'])){
                                $out.= '<ul>';
                                $out.= self::genereFooterHtml($row['children'], ($level_depth+1));
                                $out.= '</ul>';
                            }
                            $out.= '</li>';
                        }
                        else{
							if($row['title'] != 'Accès direct en consultation audiotel' || ($row['title'] == 'Accès direct en consultation audiotel' && !substr_count($row['link'], '/fre/')))
							$out.= '<li><a href="'.$row['link'].'" title="'.strip_tags($row['title']).'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').' '.$rel.'>'.$row['title'].'</a></li>';
						}
                            
                        break;
                    case 3 :
                        //Pas de lien
                        if(empty($row['link'])){
                            $out.= '<li class="ss-cat">'.$row['title'];
                            //Enfant ??
                            if(isset($row['children']) && !empty($row['children'])){
                                $out.= '<ul>';
                                $out.= self::genereFooterHtml($row['children'], ($level_depth+1));
                                $out.= '</ul>';
                            }
                            $out.= '</li>';
                        }
                        else
                            $out.= '<li><a href="'.$row['link'].'" title="'.strip_tags($row['title']).'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').' '.$rel.'>'.$row['title'].'</a></li>';
                        break;
                    default :
                        //Lien ?
                        if(!empty($row['link']))
                            $out.= '<li><a href="'.$row['link'].'" title="'.strip_tags($row['title']).'"'.(empty($row['target']) ?'':' target="'.$row['target'].'"').' '.$rel.'>'.$row['title'].'</a></li>';
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
                    $label = $this->FooterBlockLang->field("title", array(
                        'lang_id' => $lang_id,
                        'footer_block_id' => $id
                    ));
                    break;
                case 'cms' :
                    $label = $this->Page->PageLang->field("name", array(
                        'PageLang.lang_id' => $lang_id,
                        'PageLang.page_id' => $id
                    ));
                    break;
                case 'link' :
                    $label = $this->FooterLinkLang->field("title", array(
                        'FooterLinkLang.footer_link_id' => $id,
                        'FooterLinkLang.lang_id'      => $lang_id
                    ));
                    break;
                case 'sautdecolonne' :
                    $label = __('Saut de colonne');
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
                    $this->getView($requestData['idLink'],'/Elements/admin_footer_form_link');
                }
                $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau lien.')));
            }
        }

        public function admin_add_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                if(isset($requestData['idBlockLink'])){
                    $this->getView($requestData['idBlockLink'],'/Elements/admin_footer_form_block_link');
                }
                $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau bloc de lien.')));
            }
        }

        public function admin_create_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                $requestData['FooterLink'] = Tools::checkFormField($requestData['FooterLink'], array('target_blank'));
                if($requestData['FooterLink'] === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));

                //Pour chaque lang
                foreach($requestData['FooterLinkLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 2)
                        //On le supprime
                        unset($requestData['FooterLinkLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['FooterLinkLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

                //Les langues restantes
                foreach($requestData['FooterLinkLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));
                }

                //On save le nouveau lien
                $this->FooterLink->create();
                if($this->FooterLink->save($requestData['FooterLink'])){
                    //On sauvegarde les langues
                    foreach($requestData['FooterLinkLang'] as $lang){
                        $lang['footer_link_id'] = $this->FooterLink->id;
                        $this->FooterLinkLang->save($lang);
                    }

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été crée.'),
                                            'update' => Router::url(array('controller' => 'footers', 'action' => 'update', 'admin' => true))));
                }else
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la sauvegarde du lien.')));
            }
        }

        public function admin_create_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //Pour chaque lang
                foreach($requestData['FooterBlockLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 1)
                        //On le supprime
                        unset($requestData['FooterBlockLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['FooterBlockLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

                //Les langues restantes
                foreach($requestData['FooterBlockLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le bloc de lien')));
                }


                //On save le nouveau bloc
                $this->FooterBlock->create();
                if($this->FooterBlock->save(array('active' => 1))){
                    //On sauvegarde les langues
                    foreach($requestData['FooterBlockLang'] as $lang){
                        $lang['footer_block_id'] = $this->FooterBlock->id;
                        $this->FooterBlockLang->save($lang);
                    }

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été crée.'),
                                            'update' => Router::url(array('controller' => 'footers', 'action' => 'update', 'admin' => true))));
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
                        case 'footer_link' :
                            //Tout les liens
                            $links = $this->FooterLink->find('all', array(
                                'fields' => array('FooterLink.id', 'FooterLinkLang.title'),
                                'joins' => array(
                                    array(
                                        'table' => 'footer_link_langs',
                                        'alias' => 'FooterLinkLang',
                                        'type' => 'left',
                                        'conditions' => array(
                                            'FooterLinkLang.footer_link_id = FooterLink.id',
                                            'FooterLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                                        )
                                    )
                                ),
                                'order' => 'FooterLinkLang.title asc',
                                'recursive' => -1
                            ));

                            $this->getView(0, '/Elements/admin_footer_select_link', $links);
                            break;
                        case 'footer_block_link' :
                            //Tout les blocs de liens
                            $blocks = $this->FooterBlockLang->find('list', array(
                                'fields'        => array('footer_block_id', 'title'),
                                'conditions'    => array('FooterBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                                'recursive'     => -1
                            ));

                            $this->getView(0, '/Elements/admin_footer_select_block_link', $blocks);
                            break;
                        case 'link_action' :
                            $this->getView($requestData['idLink'], '/Elements/admin_footer_link_action');
                            break;
                        case 'block_link_action' :
                            $this->getView($requestData['idLink'], '/Elements/admin_footer_block_link_action');
                            break;
                        case 'data' :
                            //On récupère les infos du lien
                            $link = $this->FooterLink->find('all', array(
                                'fields' => array('FooterLink.*', 'FooterLinkLang.*'),
                                'conditions' => array('FooterLink.id' => $requestData['idLink']),
                                'joins' => array(
                                    array(
                                        'table' => 'footer_link_langs',
                                        'alias' => 'FooterLinkLang',
                                        'type' => 'left',
                                        'conditions' => array('FooterLinkLang.footer_link_id = FooterLink.id')
                                    )
                                ),
                                'recursive' => -1
                            ));

                            if(empty($link))
                                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Lien introuvable')));

                            //Pour chaque langue
                            $data = array();
                            foreach($link as $key => $lang){
                                $data[$lang['FooterLinkLang']['lang_id']]['title'] = $lang['FooterLinkLang']['title'];
                                $data[$lang['FooterLinkLang']['lang_id']]['link'] = $lang['FooterLinkLang']['link'];
                            }

                            //On rajoute les infos du lien
                            $data['FooterLink'] = $link[0]['FooterLink'];

                            //On va chercher la vue
                            $this->getView($data['FooterLink']['id'], '/Elements/admin_footer_form_link', $data);
                            break;
                        case 'block_data' :
                            //On récupère les infos du block
                            $block = $this->FooterBlockLang->find('all', array(
                                'fields'        => array('FooterBlockLang.*'),
                                'conditions'    => array('FooterBlockLang.footer_block_id' => $requestData['idLink']),
                                'recursive'     => -1
                            ));

                            if(empty($block))
                                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Bloc introuvable')));

                            //Pour chaque langue
                            $data = array();
                            foreach($block as $key => $lang){
                                $data[$lang['FooterBlockLang']['lang_id']]['title'] = $lang['FooterBlockLang']['title'];
                            }

                            //On rajoute les infos du lien
                            $data['footer_block_id'] = $block[0]['FooterBlockLang']['footer_block_id'];

                            //On va chercher la vue
                            $this->getView($data['footer_block_id'], '/Elements/admin_footer_form_block_link', $data);
                            break;
                    }
                }
            }
        }

        public function admin_edit_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //L'url pour de l'action update
                $updateUrl = Router::url(array('controller' => 'footers', 'action' => 'update', 'admin' => true));

                $requestData['FooterLink'] = Tools::checkFormField($requestData['FooterLink'], array('target_blank', 'id'), array('id'));
                if($requestData['FooterLink'] === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));

                //Pour chaque lang
                foreach($requestData['FooterLinkLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 2)
                        //On le supprime
                        unset($requestData['FooterLinkLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['FooterLinkLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigné au moins une langue.')));

                //Les langues restantes
                foreach($requestData['FooterLinkLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));
                }

                //On supprime les langues actuelles
                $this->FooterLinkLang->deleteAll(array('FooterLinkLang.footer_link_id' => $requestData['FooterLink']['id']), false);

                //On met à jour le lien
                $this->FooterLink->id = $requestData['FooterLink']['id'];
                if($this->FooterLink->saveField('target_blank', $requestData['FooterLink']['target_blank'])){
                    //On sauvegarde les langues
                    foreach($requestData['FooterLinkLang'] as $lang){
                        $lang['footer_link_id'] = $requestData['FooterLink']['id'];
                        $this->FooterLinkLang->save($lang);
                    }

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été modifié.'), 'update' => $updateUrl));
                }else
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la MAJ du lien.')));
            }
        }

        public function admin_edit_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //L'url pour de l'action update
                $updateUrl = Router::url(array('controller' => 'footers', 'action' => 'update', 'admin' => true));

                //L'id du bloc
                if(!isset($requestData['FooterBlockLang']['footer_block_id']) || empty($requestData['FooterBlockLang']['footer_block_id']))
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));

                $idBlock = $requestData['FooterBlockLang']['footer_block_id'];
                unset($requestData['FooterBlockLang']['footer_block_id']);

                //Pour chaque lang
                foreach($requestData['FooterBlockLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 1)
                        //On le supprime
                        unset($requestData['FooterBlockLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['FooterBlockLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigner au moins une langue.')));

                //Les langues restantes
                foreach($requestData['FooterBlockLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));
                }

                //On supprime les langues actuelles
                $this->FooterBlockLang->deleteAll(array('FooterBlockLang.footer_block_id' => $idBlock), false);

                //On met à jour le bloc
                //On sauvegarde les langues
                foreach($requestData['FooterBlockLang'] as $lang){
                    $lang['footer_block_id'] = $idBlock;
                    $this->FooterBlockLang->save($lang);
                }

                $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été modifié.'), 'update' => $updateUrl));
            }
        }

        public function admin_delete_link($id){
            if(empty($id) || !is_numeric($id)){
                $this->Session->setFlash(__('Impossible de supprimer le lien'),'flash_warning');
                $this->redirect(array('controller' => 'footers', 'action' => 'footer', 'admin' => true));
            }

            //On supprime les langues
            $this->FooterLinkLang->deleteAll(array('FooterLinkLang.footer_link_id' => $id), false);
            //On supprime le lien
            $this->FooterLink->deleteAll(array('FooterLink.id' => $id), false);

            $this->Session->setFlash(__('Le lien a été supprimé.'),'flash_success');
            $this->redirect(array('controller' => 'footers', 'action' => 'footer', 'admin' => true), false);
        }

        public function admin_delete_block_link($id){
            if(empty($id) || !is_numeric($id)){
                $this->Session->setFlash(__('Impossible de supprimer le bloc lien'),'flash_warning');
                $this->redirect(array('controller' => 'footers', 'action' => 'footer', 'admin' => true));
            }

            //On supprime les langues
            $this->FooterBlockLang->deleteAll(array('FooterBlockLang.footer_block_id' => $id), false);
            //On supprime le lien
            $this->FooterBlock->deleteAll(array('FooterBlock.id' => $id), false);

            $this->Session->setFlash(__('Le bloc de lien a été supprimé.'),'flash_success');
            $this->redirect(array('controller' => 'footers', 'action' => 'footer', 'admin' => true), false);
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

        public function generateFooter(){
            $file = new File(Configure::read('Site.pathMenu').'footer.txt');
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
                $footerDatas = $this->genereFooterDatas($items, $lang_id, $language);
                $footerHtml = $this->genereFooterHtml($footerDatas, 1, true);
                $footerHtml.= '</div>';

                //On supprime l'ancien cache
                Cache::delete('footer-navigation-'.$language, Configure::read('nomCacheNavigation'));
                //On crée le cache
                Cache::write('footer-navigation-'.$language, $footerHtml, Configure::read('nomCacheNavigation'));
            }
        }
    }
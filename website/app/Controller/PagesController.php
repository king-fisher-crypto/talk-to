<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Category');

class PagesController extends AppController {
    public $components = array('Paginator');
    public $helpers = array('Paginator');

    public function beforeFilter()
    {
        if ($this->request->is('ajax')){
            $this->layout = 'ajax';
            $this->set('isAjax',1);
        }
        
        parent::beforeFilter();
    }
    public function display( $link_rewrite="")
    {
		$categoryctrl = new CategoryController();
        
		/* On récupère le lien attendu */
         /*   $link = $this->getCmsPageLink($link_rewrite);
		

            if ($link !== '/'.$this->request->url){
                $this->response->statusCode(301);
                $this->redirect($link);
            }*/
		
		$idlang = $this->Session->read('Config.id_lang');
		//$parts = explode('.', $_SERVER['SERVER_NAME']);
		//if(sizeof($parts)) $extension = end($parts); else $extension = '';
		//if($idlang == 1){
			//if($extension == 'ca')$idlang=8;	
			//if($extension == 'ch')$idlang=10;
			//if($extension == 'be')$idlang=11;
			//if($extension == 'lu')$idlang=12;
		//}

        $conditions = array(
           // 'Page.id'               => $id,
            'PageLang.link_rewrite' => $link_rewrite,
            'Page.active'           => 1,
            'PageLang.lang_id'      => $idlang
        );
        $this->Page->PageLang->bindModel(array(
            'belongsTo' => array(
                'PageCategory' => array(
                    'className' => 'PageCategory',
                    'foreignKey' => '',
                    'conditions' => 'Page.page_category_id = PageCategory.id',
                    'fields' => '',
                    'order' => ''
                )
            )
        ));
        $page = $this->Page->PageLang->find('first',array(
                            'fields'     => 'PageLang.*, Page.*',
                            'conditions' => $conditions));

       // if ((isset($page['PageCategory']['display']) && $page['PageCategory']['display'] == 0) || $page['Page']['active'] == 0){
         if ($page['Page']['active'] == 0){
			$this->return404(false);
            $this->Session->setFlash(__('Cette page n\'existe pas.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
        }

        if (empty($page)){
            unset($conditions['PageLang.link_rewrite']);
            $page = $this->Page->PageLang->find('first',array(
                'fields'     => 'PageLang.*, Page.id, Page.active, Page.page_category_id',
                'conditions' => $conditions));
            if (!empty($page)){
                /* Redirection, le link_rewrite était faux */
                /*
                $url = $this->getCmsPageLink($link_rewrite);
                $this->redirect($url);
                */
                /*
                $this->redirect(array(
                    'controller' => 'pages',
                    'action'     => 'display',
                    'id'         => $id,
                    'link_rewrite' => $page['PageLang']['link_rewrite']
                ));
                */
            }
        }

        //Si pas de page, redirection page d'accueil
        if(empty($page) ||
            ((int)$this->Session->read('Config.id_domain') === 19 && $page['Page']['id'] == 36)
            || $this->isSystemNonPublicPage($page['Page']['page_category_id'])
        ){
            $this->Session->setFlash(__('Cette page n\'existe pas.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
        }
		
		
		//check product table
		if(substr_count($page['PageLang']['content'],'--TABLE--')){
			$this->loadModel('Product');
			$products = $this->Product->find('all',array(
                'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
                'conditions' => array(
                    'Product.active' => 1,
					'Product.credits >' => 0,
                    'Product.country_id' => $this->Session->read('Config.id_country')
                ),
                'joins' => array(
                    array(
                        'table' => 'product_langs',
                        'alias' => 'ProductLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'ProductLang.product_id = Product.id',
                            'ProductLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
                'order' => 'Product.credits ASC'
            ));

            //check si promo public
			$this->loadModel('Voucher');
			if($this->Session->read('promo_landing')){
				$vouchers = $this->Voucher->find('all',array(
					'conditions' => array(
						'Voucher.active' => 1,
						'Voucher.code' => $this->Session->read('promo_landing'),
					),
					'recursive' => -1,
				));
			}else{
				$vouchers = $this->Voucher->find('all',array(
					'conditions' => array(
						'Voucher.active' => 1,
						'Voucher.public' => 1,
					),
					'recursive' => -1,
				));
			}
			foreach($vouchers as $voucher){
				$promo = '';
				$promo_title = '';
				$rightToUse_once = false;
				$prod_promo = array();
				$produit_promo_select = '';
				
				 foreach($products as $produit){
				 
					//Le client peut-il l'utiliser ??
					 if($this->Auth->user('id'))
						 $rightToUse = $this->Voucher->rightToUse($voucher['Voucher']["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					 else
						$rightToUse = $this->Voucher->rightToUsePublic($voucher['Voucher']["code"], $produit['Product']['id']);

					 if($rightToUse){
						 $rightToUse_once = true;
						
						$label = '';
						if($this->Session->read('Config.id_country') == 1 && $voucher['Voucher']['label_fr']) $label = $voucher['Voucher']['label_fr'];
						if($this->Session->read('Config.id_country') == 3 && $voucher['Voucher']['label_ch']) $label = $voucher['Voucher']['label_ch'];
						if($this->Session->read('Config.id_country') == 4 && $voucher['Voucher']['label_be']) $label = $voucher['Voucher']['label_be'];
						if($this->Session->read('Config.id_country') == 5 && $voucher['Voucher']['label_lu']) $label = $voucher['Voucher']['label_lu'];
						if($this->Session->read('Config.id_country') == 13 && $voucher['Voucher']['label_ca']) $label = $voucher['Voucher']['label_ca'];
		
						$produit['Product']['promo_credit'] = (int)$voucher['Voucher']['credit']; 
						$produit['Product']['promo_label'] = $label; 
						$produit['Product']['promo_amount'] = (int)$voucher['Voucher']['amount'];
						$produit['Product']['promo_percent'] = (int)$voucher['Voucher']['percent'];
						$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
						if(!$produit_promo_select){
							$produit_promo_select = $produit['Product']['id'];
						}
						
						$promo = $voucher['Voucher']["code"];
						$promo_title = $voucher['Voucher']["title"];
						//array_push($prod_promo, $produit);
					}
					 array_push($prod_promo, $produit);
				 }
				if($promo)$products = $prod_promo;
			}
			$is_promo_total = 0;
			
			$this->loadModel('Slideprice');
			$slideprice = $this->Slideprice->find('first',array(
				'fields' => array('Slideprice.*','SlidepriceLang.*'),
                'conditions' => array(
                    'Slideprice.active' => 1,
					'Slideprice.domain LIKE' => '%'.$this->Session->read('Config.id_domain').'%',
					'OR' => array(
					'Slideprice.validity_end' => NULL,
					'Slideprice.validity_end >' => date('Y-m-d H:i:s')
					)
                ),
				'joins' => array(
                    array(
                        'table' => 'slideprice_langs',
                        'alias' => 'SlidepriceLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'SlidepriceLang.slide_id = Slideprice.id',
                            'SlidepriceLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
            ));
			
			$this->loadModel('Slidepricemobile');
			$slidepricemobile = $this->Slidepricemobile->find('first',array(
				'fields' => array('Slidepricemobile.*','SlidepricemobileLang.*'),
                'conditions' => array(
                    'Slidepricemobile.active' => 1,
					'Slidepricemobile.domain LIKE' => '%'.$this->Session->read('Config.id_domain').'%',
					'OR' => array(
					'Slidepricemobile.validity_end' => NULL,
					'Slidepricemobile.validity_end >' => date('Y-m-d H:i:s')
					)
                ),
				'joins' => array(
                    array(
                        'table' => 'slidepricemobile_langs',
                        'alias' => 'SlidepricemobileLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'SlidepricemobileLang.slide_id = Slidepricemobile.id',
                            'SlidepricemobileLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
            ));
			$this->set(compact('products','promo','promo_title','is_promo_total','slideprice','slidepricemobile'));

		}
		
		//check product table
		if(substr_count($page['PageLang']['content'],'--TABLE_RECOUVRE--')){
			$this->loadModel('Product');
			$products = $this->Product->find('all',array(
                'fields' => array('Product.id','Product.credits', 'Product.tarif', 'ProductLang.name', 'ProductLang.description'),
                'conditions' => array(
                    'Product.active' => 1,
					'Product.credits' => -1,
                    'Product.country_id' => $this->Session->read('Config.id_country')
                ),
                'joins' => array(
                    array(
                        'table' => 'product_langs',
                        'alias' => 'ProductLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'ProductLang.product_id = Product.id',
                            'ProductLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
                'order' => 'Product.credits ASC'
            ));

            $this->set(compact('products'));
		}

                            
        /* Metas */
        $this->site_vars['meta_title']       = $page['PageLang']['meta_title'];
        $this->site_vars['meta_keywords']    = $page['PageLang']['meta_keywords'];
        $this->site_vars['meta_description'] = $page['PageLang']['meta_description'];
                            
        
		 $param_query = $this->params->query;
		 unset($param_query["gclid"]);
		 unset($param_query["utm_source"]);
		   unset($param_query["utm_medium"]);
		unset($param_query["utm_campain"]);
		   unset($param_query["utm_campaign"]);
		   unset($param_query["utm_term"]);
		   unset($param_query["utm_content"]);
		   unset($param_query["campaign"]);
		   unset($param_query["network"]);
		   unset($param_query["device"]);
		unset($param_query["_ga"]);
		unset($param_query["_gac"]);
		unset($param_query["aclk"]);
		
		
		//load data agents
		$filters = $categoryctrl->initFilterProperties();

        //Permet de garder en mémoire les choix des filtres (orderby et filterby)
        if(isset($this->params->query[$this->queryIndex['ajax_for_agents']])){
            if(!empty($this->params->query[$this->queryIndex['orderby']]))    $filters['filter_orderby'][$this->params->query[$this->queryIndex['orderby']]]['active'] = true;
            if(!empty($this->params->query[$this->queryIndex['filterby']]))   $filters['filter_filterby'][$this->params->query[$this->queryIndex['filterby']]]['active'] = true;
        }

        $this->set($filters);

        // Sommes-nous en mode recherche ?
        $inSearchOrAjaxMode =  (isset($this->request->data['searchpost']) || isset($this->request->data['ajax_for_agents']))?1:0;
		
		 if (!$inSearchOrAjaxMode){
			
			$codelang = $this->Session->read('Config.language');
			
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($codelang == 'fre'){
				if($extension == 'ca')$codelang='frc';	
				if($extension == 'ch')$codelang='frh';
				if($extension == 'be')$codelang='frb';
				if($extension == 'lu')$codelang='frl';
			}
			$this->loadModel('Category');
			$this->loadModel('CategoryLang');
            $category = $this->Category->CategoryLang->find('first', array(
                                           'conditions' => array(
                                               'Lang.language_code' => $codelang,
                                               'Category.active' => 1,
                                               'Category.id' => 1
                                           ))
            );

            // Si on ne trouve pas la catégorie, on arrête 
                if (!$category) {
                    throw new NotFoundException(__('Invalid category'));
                }

            $categoryLang['CategoryLang'] = $category['CategoryLang'];

            $this->set(compact('categoryLang'));

        }
		
		
		
		// Retour ajax 
       if (isset($this->request->data['ajax_for_agents'])){
            $id = isset($this->request->data['id_category'])?$this->request->data['id_category']:$id;
            $categories = isset($this->request->data['categories'])?$this->request->data['categories']:array();

            //Datas de la categorie
          //  $datasForView = $categoryctrl->getDatasForCategory(1, $this->request->data['page'], $this->params->query, $categories);
		   $datasForView =  $this->requestAction(array('controller' => 'category', 'action' => 'getDatasForCategory'),array('args'=>$this->params->query, 'categories'=>$categories));
            $this->layout = '';
                $this->set('ajax_refresh_interval', 8000);

            //Texte explicatif
            $tuto = '';
            if(!empty($this->request->data['media'])){
                $this->loadModel('PageLang');
                //Selon le media sélectionné
                $idPage = 0;
                switch(end($this->request->data['media'])){
                    case 'phone' :
						if($this->request->isMobile()) $idPage = 227; else $idPage = 33;
                        break;
                    case 'chat' :
                        if($this->request->isMobile()) $idPage = 229; else $idPage = 35;
                        break;
                    case 'email' :
                        if($this->request->isMobile()) $idPage = 228; else $idPage = 34;
                        break;
                }
                //Le contenu de la page
				$idlang = $this->Session->read('Config.id_lang');
				$parts = explode('.', $_SERVER['SERVER_NAME']);
				if(sizeof($parts)) $extension = end($parts); else $extension = '';
				if($idlang == 1){
					if($extension == 'ca')$idlang=8;	
					if($extension == 'ch')$idlang=10;
					if($extension == 'be')$idlang=11;
					if($extension == 'lu')$idlang=12;
				}
				
                $tmp = $this->PageLang->find('first', array(
                    'fields' => array('PageLang.content'),
                    'conditions' => array('PageLang.page_id' => $idPage, 'PageLang.lang_id' => $idlang),
                    'recursive' => -1
                ));

                //Si la page existe bien
                if(!empty($tmp))
                    $tuto = $tmp['PageLang']['content'];
            }
			
			$datasForView['isMobile'] = $this->request->isMobile();	

                $view = new View($this, false);
                $view->set($datasForView);
                $json = array(
                    'html'          => $view->render('ajax_agentlist'),
                    'count'         => $datasForView['countAgents'],
                    'count_html'    => $datasForView['count_html'],
                    'tuto'          => $tuto
                );
               // if (isset($this->request->data['debug']) && $this->isNoox()){
                //    $json['query'] = $this->Category->CategoryUser->getDataSource()->getLog(false, false);
               // }
                $this->jsonRender($json);
        }else{
		  
			   $datasForView = $this->requestAction(array('controller' => 'category', 'action' => 'getDatasForCategory'),array('args'=>$param_query));
			   // $datasForView = $categoryctrl->getDatasForCategory(1, 1, $this->params->query);
				$datasForView['isMobile'] = $this->request->isMobile();	
			 
            $this->set($datasForView);
        }
		
		$this->set('thepage', $page);
    }

    public function admin_create($post = true)
    {
        if ($this->request->is('post') && !isset($this->request->data['PageLang']['search']) && $post) {
            $requestData = $this->request->data;
			
            /*$name = array(
                'meta_keywords' => 'mots-clés',
                'meta_description' => 'description'
            );

            //Vérification des balises méta
            if(!$this->checkMeta($requestData['PageLang'][0], $name)){
                $this->admin_create(false);
                return;
            }*/

            //Modification des url pour les images
            $this->request->data['PageLang'][0]['content'] = Tools::clearUrlImage($this->request->data['PageLang'][0]['content']);
            if($this->request->data['PageLang'][0]['content'] === false){
                $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                $this->admin_create(false);
                return;
            }

            $this->Page->create();
            $this->Page->saveAssociated($this->request->data);

            //On regénère le footer
            $this->regenCacheFooter();

            $this->Session->setFlash(__('La page a bien été enregistrée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
            $this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        $this->loadModel('PageCategory');
        $this->set('lang_options', $this->Lang->find('list', array(
            'conditions' => array('active' => 1)
        )));

        //La liste des catégories des pages
        $cat_options = $this->PageCategory->getCategorySelect($this->Session->read('Config.id_lang'), $this->request->params['action'] != 'admin_list');

        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //Recherche----------------------------------
        $conditions = array();
        if(isset($this->request->data['PageLang']['search'])){
            //Si une indication de page existe, on la supprime
            if(isset($this->request->query['page']))
                unset($this->request->query['page']);
            //Par titre
            if(isset($this->request->data['PageLang']['title']))
                $conditions = array(
                    'OR' => array(
                        'PageLang.meta_title LIKE' => '%'. $this->request->data['PageLang']['title'] .'%',
                        'PageLang.name LIKE' => '%'. $this->request->data['PageLang']['title'] .'%'
                    )

                );
            //Par catégorie
            elseif($this->request->data['PageLang']['category'])
                $conditions = array('Page.page_category_id' => $this->request->data['PageLang']['category']);
                $this->Session->write('Admin.PageLang.category', trim($this->request->data['PageLang']['category']));
        }
        if(!empty($this->Session->read('Admin.PageLang.category'))) {
            $conditions = array('Page.page_category_id' => $this->Session->read('Admin.PageLang.category'));
        }

        $this->Paginator->settings = array(
            'fields' => array('PageLang.*', 'Page.id', 'PageCategoryLang.name', 'Page.page_category_id', 'Page.active', 'Lang.name', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'pages',
                    'alias' => 'Page',
                    'type' => 'left',
                    'conditions' => array('Page.id = PageLang.page_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = PageLang.lang_id')
                ),
                array(
                    'table' => 'page_category_langs',
                    'alias' => 'PageCategoryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'PageCategoryLang.page_category_id = Page.page_category_id',
                        'PageCategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1,
            'group' => 'Page.id',
            'order' => 'PageCategoryLang.name asc, PageLang.meta_title asc',
            'paramType' => 'querystring',
            'limit' => 50
        );

        $tmp_pages = $this->Paginator->paginate($this->Page->PageLang);
		$categoryctrl = new CategoryController();
        $pages = array();
        foreach ($tmp_pages AS $page){
            $pageTransit = end($pages);

            //S'il y a un élément d'enregistrer
            if($pageTransit != false){
                if($pageTransit['page_id'] == $page['Page']['id']){
                    $keys = array_keys($pages);
                    $lastKey = end($keys);
                    $pages[$lastKey]['lang_name'].= ', '.$page['Lang']['name'];
                    continue;
                }
            }

            $pages[] = array(
                'langs'         =>  str_replace(",",", ",isset($page['0']['langs'])?$page['0']['langs']:''),
                'page_id'       =>  $page['PageLang']['page_id'],
                'page_category_id' => $page['Page']['page_category_id'],
                'name'          =>  $page['PageLang']['name'],
				'bg_desktop'          =>  $page['PageLang']['bg_desktop'],
				'phrase1_desktop'          =>  $page['PageLang']['phrase1_desktop'],
				'phrase2_desktop'          =>  $page['PageLang']['phrase2_desktop'],
				'bg_mobile'          =>  $page['PageLang']['bg_mobile'],
				'phrase1_mobile'          =>  $page['PageLang']['phrase1_mobile'],
				'phrase2_mobile'          =>  $page['PageLang']['phrase2_mobile'],
				'btn_text'          =>  $page['PageLang']['btn_text'],
				'btn_url'          =>  $page['PageLang']['btn_url'],
                'category_name' =>  (empty($page['PageCategoryLang']['name']) ?__('Pas de nom'):$page['PageCategoryLang']['name']),
                'etat'          =>  ($page['Page']['active']
                                        ?'<span class="badge badge-success">'.__('Active').'</span>'
                                        :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                    ),
                'lang_name'     =>  $page['Lang']['name'],
                'link_rewrite'  =>  $this->getCmsPageLink($page['PageLang']['link_rewrite'],'fre'),
                'active'        =>  $page['Page']['active'],
                'hidden_page'   =>  $this->isSystemNonPublicPage($page['Page']['page_category_id'])?true:false
            );
        }

        $this->set(compact('pages', 'langs', 'cat_options'));
    }

    public function admin_delete($id){
        $this->Page->id = $id;
        if($this->Page->saveField('active', 0)){
            //On regénère le footer
            $this->regenCacheFooter();
            $this->Session->setFlash(__('La page est désactivée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la désactivation.'),'flash_warning');

        $this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_true_delete($id){
        //On supprime les langues
        $this->Page->PageLang->deleteAll(array('PageLang.page_id' => $id), false);
        //On supprime la page
        if($this->Page->deleteAll(array('Page.id' => $id), false)){
            //On regénère le footer
            $this->regenCacheFooter();
            $this->Session->setFlash(__('La page a été supprimée. Si votre suppression affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la suppression.'),'flash_warning');

        $this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_add($id){
        $this->Page->id = $id;
        if($this->Page->saveField('active', 1)){
            //On regénère le footer
            $this->regenCacheFooter();
            $this->Session->setFlash(__('La page est activée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de l\'activation.'),'flash_warning');

        $this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_edit($id, $post=true){
		$this->loadModel('PageLang');
        if($this->request->is('post') && $post){
			
            $requestData = $this->request->data;
			
			if($requestData['AdminEmailIdPage'] != ''){
				$idpage = $requestData['AdminEmailIdPage'];
				$email = $requestData['AdminEmailTest'];
				
				$page = $this->Page->PageLang->find('first',array(
                'conditions' => array('Page.id' => $idpage)));
				
				$is_send = $this->sendCmsTemplateByMail($idpage, 1, $email, array(
					  'URL_CONNEXION' =>   '',
					  'PIXEL' =>   '',
				),true);
				if($is_send){
					$this->Session->setFlash(__('Mail test envoyé.'),'flash_success');
				}else{
					$this->Session->setFlash(__('Erreur lors envoi du mail test.'),'flash_warning');
				}	
				
			}else{
			
				//Pour chaque lang
				
				foreach($requestData['PageLang'] as $key => $val){
					//Si le formulaire est vide
					if(count(array_keys($val, '')) >= 13){
						//On le supprime
						unset($requestData['PageLang'][$key]);
						continue;
					}
				}

				//Si aucune page n'est renseigné
				if(empty($requestData['PageLang'])){
					$this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
					$this->redirect(array('controller' => 'pages', 'action' => 'edit', 'admin' => true, 'id' => $id),false);
				}
				//Check les langues restantes
				if ($this->isSystemNonPublicPage($requestData['Page']['page_category_id'])){
					/* Cas des templates de mail */
						$the_fields = array('lang_id','page_id','content', 'name');
						$the_required = array('lang_id','page_id','name');


					/* Nettoyage */
					foreach ($requestData['PageLang'] AS $key => $val){
						if (empty($val['name']) && empty($val['content']))
							unset($requestData['PageLang'][$key]);
					}

				}else{
					/* Nettoyage */
					foreach ($requestData['PageLang'] AS $key => $val){
						if (empty($val['name']) && empty($val['content']))
							unset($requestData['PageLang'][$key]);
					}
					
					/* Cas des pages normales */
						$the_fields = array('lang_id','page_id','meta_title','link_rewrite','meta_keywords','meta_description','content', 'name','bg_desktop','phrase1_desktop','phrase2_desktop','bg_mobile','phrase1_mobile','phrase2_mobile','btn_text','btn_url');
						$the_required = array('lang_id','page_id','meta_title', 'name');
				}

				foreach($requestData['PageLang'] as $key => $val){
					//Vérification des champs requis
					$val = Tools::checkFormField($val, $the_fields, $the_required);
					if($val === false){
						$this->Session->setFlash(__('Veuillez remplir les champs obligatoires pour chaque langue renseignée.'),'flash_warning');
						$this->redirect(array('controller' => 'pages', 'action' => 'edit', 'admin' => true, 'id' => $id),false);
					}

					//Vérification des balises méta
					/*$name = array(
						'meta_keywords' => 'mots-clés',
						'meta_description' => 'description'
					);
					if(!$this->checkMeta($val, $name)){
						$this->admin_edit($id, false);
						return;
					}*/

					//Modification des url pour les images
					$requestData['PageLang'][$key]['content'] = Tools::clearUrlImage($requestData['PageLang'][$key]['content']);
					$requestData['PageLang'][$key]['content'] = Tools::clearUrlImage($requestData['PageLang'][$key]['content'],'"/media','"'.Configure::read('Site.baseUrlFull').'/media');
					$requestData['PageLang'][$key]['content'] = Tools::clearUrlImage($requestData['PageLang'][$key]['content'],'"/theme','"'.Configure::read('Site.baseUrlFull').'/theme');
					if($requestData['PageLang'][$key]['content'] === false){
						$this->Session->setFlash(__('Une erreur est survenue avec une des langues, votre contenu est sûrement vide.'),'flash_warning');
						$this->admin_edit($id, false);
						return;
					}
					$countGood = 0;
                	$countBad = 0;
					$countFile = 0;
                $countSize = 0;
                $countMime = 0;
					if($this->isUploadedFile($requestData['PageLang'][$key]['bg_desktop'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['PageLang'][$key]['bg_desktop']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                        if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            $countMime++;
							unset($requestData['PageLang'][$key]['bg_desktop']); 
                            continue;
                        }

                        //Le nom du slide
                        $filename = $id.'-PageDesktop-'.$requestData['PageLang'][$key]['lang_id'].'.jpg';
                        //On save le nom du fichier
                       

                        //En déplace le fichier

                        if(!move_uploaded_file($requestData['PageLang'][$key]['bg_desktop']['tmp_name'], Configure::read('Site.pathPageDesktop').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;
						 $requestData['PageLang'][$key]['bg_desktop'] = $filename;
                    }else{
						$pp = $this->PageLang->find('first',array(
								'conditions' => array('page_id' => $id, 'lang_id' => $key),
								'recursive' => -1,
							));
						$requestData['PageLang'][$key]['bg_desktop'] = $pp['PageLang']['bg_desktop']; 
					}
					
					if($this->isUploadedFile($requestData['PageLang'][$key]['bg_mobile'])){

                        //Les infos de la photo de la langue
						
                        $dataSlide = getimagesize($requestData['PageLang'][$key]['bg_mobile']['tmp_name']);
                        //Est-ce un fichier image autorisé ??

                        if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            $countMime++;
							unset($requestData['PageLang'][$key]['bg_mobile']); 
                            continue;
                        }


                        //Le nom du slide
                        $filename = $id.'-PageMobile-'.$requestData['PageLang'][$key]['lang_id'].'.jpg';
                        //On save le nom du fichier
                       

                        //En déplace le fichier
                        if(!move_uploaded_file($requestData['PageLang'][$key]['bg_mobile']['tmp_name'], Configure::read('Site.pathPageMobile').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;
						
						 $requestData['PageLang'][$key]['bg_mobile'] = $filename;
                    }else{
						$pp = $this->PageLang->find('first',array(
								'conditions' => array('page_id' => $id, 'lang_id' => $key),
								'recursive' => -1,
							));
						$requestData['PageLang'][$key]['bg_mobile'] = $pp['PageLang']['bg_mobile']; 
					}
					
				}
				//var_dump($requestData);exit;
			//je recup current url
			foreach($requestData['PageLang'] as $key => $val){	

				$r = $this->Page->find('first',array(
					'conditions' => array('Page.id' => $id),
					'fields' => array('Page.page_category_id'),
					'recursive' => -1
				));
				$id_page_category = $r['Page']['page_category_id'];
				$this->loadModel('PageCategoryLang');
				$r = $this->PageCategoryLang->find('first',array(
					'conditions' => array('PageCategoryLang.page_category_id' => $id_page_category, 'PageCategoryLang.lang_id' => $key),
					'fields' => array('PageCategoryLang.name'),
					'recursive' => -1
				));
				$name_page_category =  '';
				if(array_key_exists('PageCategoryLang', $r)){
					$name_page_category = $this->slugify($r['PageCategoryLang']['name']);
				}
				
				
				$url = $this->PageLang->find('first',array(
								'conditions' => array('page_id' => $id, 'lang_id' => $key),
								'recursive' => -1,
							));
				if($requestData['PageLang'][$key]['link_rewrite'] && $url['PageLang']['link_rewrite'] != $requestData['PageLang'][$key]['link_rewrite']){
					$this->loadModel('Redirect');
					$this->loadModel('Lang');
					$this->loadModel('Domain');
					$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
					$langue = $this->Lang->find('first',array(
								'conditions' => array('id_lang' => $key),
								'recursive' => -1,
							));
					$domaine = $this->Domain->find('first',array(
								'conditions' => array('default_lang_id' => $key, 'active' => 1),
								'recursive' => -1,
							));
					$redirectData = array();
					$redirectData['Redirect'] = array();
					$redirectData['Redirect']['type'] = "301";
					$redirectData['Redirect']['domain_id'] = $domaine['Domain']['id'];
					$redirectData['Redirect']['old'] = '/'.$langue['Lang']['language_code'].'/'.$name_page_category."/".$url['PageLang']['link_rewrite'];
					$redirectData['Redirect']['new'] = $protocol.$domaine['Domain']['domain'].'/'.$langue['Lang']['language_code'].'/'.$name_page_category.'/'.$requestData['PageLang'][$key]['link_rewrite'];
					$this->Redirect->create();
					$this->Redirect->save($redirectData);
				}
			}
				//L'etat de la page
				$this->Page->id = $id;
				$requestData['Page']['date_upd'] = date('Y-m-d H:i:s');
				$this->Page->save($requestData['Page']);
				//On supprime toutes les langues
				$this->Page->PageLang->deleteAll(array('PageLang.page_id' => $id), false);
				/*foreach($requestData['PageLang'] as $page){
					if($this->Page->PageLang->exist($page['page_id'], $page['lang_id'])){
						$page['meta_title'] = '"'.$page['meta_title'].'"';
						$page['link_rewrite'] = '"'.$page['link_rewrite'].'"';
						$page['meta_keywords'] = '"'.$page['meta_keywords'].'"';
						$page['meta_description'] = '"'.$page['meta_description'].'"';
						$page['content'] = '"'.$page['content'].'"';

						$this->Page->PageLang->updateAll($page, array('page_id' => $page['page_id'], 'lang_id' => $page['lang_id']));
					}else{
						$this->Page->PageLang->create();
						$this->Page->PageLang->save($page);
					}
				}*/

				$this->Page->PageLang->saveMany($requestData['PageLang']);

				//On regénère le footer
				$this->regenCacheFooter();

				//$this->Session->setFlash(__('La page a été modifiée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
				$this->Session->setFlash(__('La page a bien été enregistrée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'killroutes', 'admin' => true), true), 'messageLink' => __('ICI')));


				$this->redirect(array('controller' => 'pages', 'action' => 'list', 'admin' => true), false);
			}
        }

        $this->loadModel('Lang');
        $this->loadModel('PageCategory');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //La liste des catégories des pages
        $cat_options = $this->PageCategory->getCategorySelect($this->Session->read('Config.id_lang'));

        //On récupère toutes les infos des pages
        $pages = $this->Page->find('all',array(
            'conditions' => array('Page.id' => $id),
            'recursive' => 1
        ));

        //Un tableau qui contient les données pour chaque langue renseigné
        foreach($pages[0]['PageLang'] as $pageLang){
            $langDatas[$pageLang['lang_id']] = $pageLang;
        }

        //Etat de la page
        $activePage = $pages[0]['Page']['active'];
        //Nom Page
        $namePage = $pages[0]['PageLang'][0]['meta_title'];
        //Category
        $catPage = $pages[0]['Page']['page_category_id'];

        //Variable qui stocke l'id de la page, pour un accès plus rapide
        $idPage = $pages[0]['Page']['id'];
		$linkpage =  $this->getCmsPageLink($pages[0]['PageLang'][0]['link_rewrite'],'fre');

        $isHiddenSystemPage = false;
        $page_parameters = array();
        if (isset($pages['0']['Page']['page_category_id'])){
            $isHiddenSystemPage = $this->isSystemNonPublicPage($pages['0']['Page']['page_category_id']);
            $page_parameters = $this->Page->getVarsOfPage($id);
        }

        $this->set(compact('page_parameters','isHiddenSystemPage','langDatas', 'langs', 'idPage', 'activePage', 'namePage', 'cat_options', 'catPage','linkpage'));
    }

    public function admin_list()
    {
        $this->admin_create();

        $this->render('admin_create');
    }

    public function admin_create_category(){
        if($this->request->is('post')){
            $requestData = $this->request->data;
            //Check les formulaires
            $requestData['PageCategory'] = Tools::checkFormField($requestData['PageCategory'], array('active'));
            $requestData['PageCategoryLang'][0] = Tools::checkFormField($requestData['PageCategoryLang'][0], array('lang_id', 'name'), array('lang_id', 'name'));
            if($requestData['PageCategory'] === false || $requestData['PageCategoryLang'][0] === false){
                $this->Session->setFlash(__('Le formulaire est incomplet.'), 'flash_warning');
                $this->redirect(array('controller' => 'pages', 'action' => 'create_category', 'admin' => true), false);
            }
            //On save le tout
            $this->loadModel('PageCategory');
            $this->PageCategory->create();
            if($this->PageCategory->saveAssociated($requestData)){
                //On regénère le footer
                $this->regenCacheFooter();
                $this->Session->setFlash(__('La catégorie a bien été enregistrée.'),'flash_success');
            }
            else
                $this->Session->setFlash(__('Erreur, la catégorie n\'a pu être sauvegardé.'),'flash_warning');

            $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
        }


        $this->loadModel('PageCategory');
        $tmp_pagesCat = $this->PageCategory->find('all',array(
            'fields' => array('PageCategory.*', 'PageCategoryLang.*', 'Lang.name'),
            'joins' => array(
                array(
                    'table' => 'page_category_langs',
                    'alias' => 'PageCategoryLang',
                    'type' => 'left',
                    'conditions' => array('PageCategoryLang.page_category_id = PageCategory.id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type'  => 'left',
                    'conditions' => array('Lang.id_lang = PageCategoryLang.lang_id')
                )
            ),
            'recursive' => -1
        ));

        $this->loadModel('Lang');
        $this->set('lang_options', $this->Lang->find('list'));

        $pagesCat = array();
        foreach($tmp_pagesCat as $pageCat){

            $pageCatTransit = end($pagesCat);

            //S'il y a un élément d'enregistrer
            if($pageCatTransit != false){
                if($pageCatTransit['page_category_id'] == $pageCat['PageCategory']['id']){
                    //Les clés du tableau
                    $keys = array_keys($pagesCat);
                    //La dernière clé
                    $lastKey = end($keys);
                    //On rajoute le nom de l'autre langue
                    $pagesCat[$lastKey]['lang_name'].= ', '.$pageCat['Lang']['name'];
                    //On compte le nombre de page pour cette catégorie
                    if(!empty($pageCat['Page']['id'])){
                        $pagesCat[$lastKey]['count']++;
                    }
                    continue;
                }
            }

            $pagesCat[] = array(
                'page_category_id'  => $pageCat['PageCategory']['id'],
                'name'              => $pageCat['PageCategoryLang']['name'],
                'count'             => $this->Page->countPageInCategory($pageCat['PageCategory']['id']),
                'etat'              => ($pageCat['PageCategory']['active']
                                            ?'<span class="badge badge-success">'.__('Active').'</span>'
                                            :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                        ),
                'footer'            => ($pageCat['PageCategory']['footer']
                                            ?'<span class="badge badge-success">'.__('Oui').'</span>'
                                            :'<span class="badge badge-danger">'.__('Non').'</span>'
                                        ),
                'lang_name'         => $pageCat['Lang']['name'],
                'active'            => $pageCat['PageCategory']['active']
            );
        }

        $this->set(compact('pagesCat'));
    }

    public function admin_list_category(){
        $this->admin_create_category();

        $this->render('admin_create_category');
    }

    public function admin_edit_category($id){
        if($this->request->is('post')){
            //Les models
            $this->loadModel('PageCategory');
            $this->loadModel('PageCategoryLang');

            $requestData = $this->request->data;

            //Pour chaque lang
            foreach($requestData['PageCategoryLang'] as $key => $val){
                //Si le formulaire est vide
                if(empty($val['name'])){
                    //On le supprime
                    unset($requestData['PageCategoryLang'][$key]);
                    continue;
                }
            }

            //Si aucune langue n'est renseigné
            if(empty($requestData['PageCategoryLang'])){
                $this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
                $this->redirect(array('controller' => 'pages', 'action' => 'edit_category', 'admin' => true, 'id' => $id),false);
            }

            //Check les langues restantes
            foreach($requestData['PageCategoryLang'] as $val){
                //Vérification des champs requis
                $val = Tools::checkFormField($val,
                    array('lang_id','page_category_id','name'),
                    array('lang_id','page_category_id','name')
                );
                if($val === false){
                    $this->Session->setFlash(__('Erreur dans le formulaire.'),'flash_warning');
                    $this->redirect(array('controller' => 'pages', 'action' => 'edit_category', 'admin' => true, 'id' => $id),false);
                }
            }

            //L'etat de la catégorie de la page
            //Si c'est la catégorie BlocTexte, on modifie pas son etat
            if($id != Configure::read('Site.catBlocTexteID')){
                $this->PageCategory->id = $id;
                $this->PageCategory->save($requestData['PageCategory']);
            }
            //On supprime toutes les langues et on sauve les nouvelles
            $this->PageCategoryLang->deleteAll(array('PageCategoryLang.page_category_id' => $id), false);
            $this->PageCategoryLang->saveMany($requestData['PageCategoryLang']);

            //On regénère le footer
            $this->regenCacheFooter();

            $this->Session->setFlash(__('La catégorie a été modifiée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));

            $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        $this->loadModel('PageCategory');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //On récupère toutes les infos de la catégorie de la page
        $pagesCat = $this->PageCategory->find('all',array(
            'conditions' => array('PageCategory.id' => $id),
            'recursive' => 1
        ));

        //Un tableau qui contient les données pour chaque langue renseigné
        foreach($pagesCat[0]['PageCategoryLang'] as $pageCatLang){
            $langDatas[$pageCatLang['lang_id']] = $pageCatLang;
        }

        //Etat de la catégorie de la page
        $activePageCat = $pagesCat[0]['PageCategory']['active'];
        //Dans le footer ou pas
        $footerPageCat = $pagesCat[0]['PageCategory']['footer'];
        //Nom de la catégorie de la page
        $namePageCat = $pagesCat[0]['PageCategoryLang'][0]['name'];
		
		$id_parentPageCat = $pagesCat[0]['PageCategory']['id_parent'];

        //Variable qui stocke l'id de la catégorie, pour un accès plus rapide
        $idPageCat = $pagesCat[0]['PageCategory']['id'];

        $this->set(compact('langDatas', 'langs', 'idPageCat', 'activePageCat', 'namePageCat', 'footerPageCat', 'id_parentPageCat'));
    }

    public function admin_add_category($id){
        if($id == Configure::read('Site.catBlocTexteID')){
            $this->Session->setFlash(__('Ohh le vilain, impossible d\'activer cette catégorie.'),'flash_warning');
            $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
        }
        $this->loadModel('PageCategory');
        $this->PageCategory->id = $id;
        if($this->PageCategory->saveField('active', 1)){
            //On regénère le footer
            $this->regenCacheFooter();
            $this->Session->setFlash(__('La catégorie est activée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de l\'activation.'),'flash_success');

        $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
    }

    public function admin_delete_category($id){
        if($id == Configure::read('Site.catBlocTexteID')){
            $this->Session->setFlash(__('Impossible de désactiver cette catégorie.'),'flash_warning');
            $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
        }
        $this->loadModel('PageCategory');
        $this->PageCategory->id = $id;
        if($this->PageCategory->saveField('active', 0)){
            //On regénère le footer
            $this->regenCacheFooter();
            $this->Session->setFlash(__('La catégorie est désactivée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la désactivation.'),'flash_success');
        $this->redirect(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true), false);
    }

    private function regenCacheFooter(){
        //On récupère les langues du site
        $this->loadModel('Lang');
        $langs = $this->Lang->find('list', array(
            'fields'        => array('Lang.language_code'),
            'conditions'    => array('Lang.active' => 1),
            'recursive'     => -1
        ));

        //On détruit le cache de chaque langue
        foreach($langs as $code){
            Cache::delete('footer-navigation-'.$code, Configure::read('nomCacheNavigation'));
        }
    }
	
	public function widgetlisting(){
		
		if($this->request->is('ajax')){
			$requestData = $this->request->data;
			App::uses('FrontblockHelper', 'View/Helper');
			$fbH = new FrontblockHelper(new View());
			$nb = count($fbH->getAgentBusy());
			$offset = $requestData["page"];
			$agentlist = $fbH->getAgentBusyData($offset);
			
			$html = '
            	<div class="widget">
                	<div class="widget-title text-center">';//<span class="bold-number">'.$nb .' </span>
					/*if(count($agentlist) > 1)
					$html .= 'experts';
					else
					 $html .= 'expert';*/
					// $html .= ' en ligne</div>
					 $html .= 'Nos experts</div>
                    <ul class="online-list">';
                    foreach($agentlist as $agent ){ 

						$fiche_link = $fbH->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower($agent['User']['pseudo']),
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
							$set_title = __('En consultation').'  <span class="depuis_widget">depuis '.$fbH->secondsToHis($agent[0]['second_from_last_status']).'</span>';
							$set_title_css = 'consultation';
							
						}elseif ($agent['User']['agent_status'] == 'unavailable'){
							$set_title = __('Indisponible');
							$set_title_css = 'retour';
						}
					
                    	$html .= '<li class="wow fadeIn" data-wow-delay="0.1s">

                            <div class="online-name">
                                <span class="uppercase">
                                    <a href="'.$fiche_link.'" title="'.$agent['User']['pseudo'].'"><h4 class="inline-block">'.$agent['User']['pseudo'].'</h4></a> 
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
                                <div class="online-expert-pic text-center">
                                    <a href="'.$fiche_link.'" class="sm-sid-photo"><span>';
									
									$html .= $fbH->Html->image($fbH->getAvatar($agent['User']), array(
                                                'alt' => $agent['User']['pseudo'],
                                                'title' => $agent['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle'
                                                ));
									$html .= '</span></a>';
									
									if($agent['0']['AverageRating']){
                                    $html .= '<p class="on-per">'.number_format($agent['0']['AverageRating'],1).'%</p>
    
                                    <div class="star-area">
                                            <ul class="list-inline list-star">
                                                '.$fbH->getStarsRateListing($agent['0']['AverageRating']).'
                                            </ul>
                                        </div>';

									}
                                $html .= '</div>
                                </div><!--col-sm-5 END-->
    
                                <div class="col-sm-7 pr0">
                                    <div class="status-box">
                                        <p class="status '.$set_title_css.'">'.$set_title.'</p>
                                        <ul class="list-inline medium-btn';
										if($agent['User']['agent_status'] == 'busy') $html .= ' alert-btn ';
										$html .= '">';
												if($agent['User']['agent_status'] == 'busy'){
												$html .= '	 <li class="alert-li"><a href="'.$fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" data-toggle="tooltip" data-placement="top" title="Recevoir une alerte sms/email" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" class="alerte-a aebutton nx_openinlightbox nxtooltip">Recevoir une<br />alerte sms/email</a></li>';
												}else{
												$html .= '<li class="tel ';
												if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1): $html .= 't-available'; else: $html .= ' disabled'; endif; 
												$html .= '">';
												 
                                                    if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span>',
                                                                array(
                                                                    'controller' => 'home',
                                                                    'action' => 'media_phone'
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_phonebox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Tel',
                                                                    'escape' => false
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a title="Tel" data-toggle="tooltip" data-placement="top" href=""><p>Tel</p></a>';
                                                        endif;
                                                $html .= '</li>';
                                                $html .= '<li class="mail ';
												if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1): $html .= ' m-available'; else: $html .= ' disabled'; endif; 
												$html .= '">';
                                                 
                                                if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Email</p>',
                                                                array(
                                                                    'controller' => 'accounts',
                                                                    'action' => 'new_mail',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_emailbox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Email',
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a title="Email" data-toggle="tooltip" href=""><p>Email</p></a>';
                                                        endif;
                                                $html .= '</li>';
                                                $html .= '<li class="chat ';
												if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $fbH->agentActif($agent['User']['date_last_activity'])): $html .= 'c-available'; else:  $html .= 'disabled'; endif; 
												$html .= '">';
                                                 
                                                    if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $fbH->agentActif($agent['User']['date_last_activity'])):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Chat</p>',
                                                                array(
                                                                    'controller' => 'chats',
                                                                    'action' => 'create_session',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_chatbox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Chat',
                                                                )
                                                            );
                                                        else:
                                                            $html .=  '<a title="Chat" data-toggle="modal" href=""><p>Chat</p></a>';
                                                        endif; 
                                                $html .= '</li>';
												}
                                        $html .= '</ul>
                                    </div><!--code-box END-->
                                </div><!--col-sm-7 END-->
                            </div><!--row END-->
    
                        </li>';
                     } 
                 $html .= ' </ul>
                </div><!--widget End-->
               '.$fbH->getPaginateEnligne($nb, $offset).'
			
			';
			
			$this->jsonRender(array('html' => $html));
		}
		
	}
	public function widgetbottomlisting(){
		
		if($this->request->is('ajax')){
			$nbview = 4;
			$requestData = $this->request->data;
			App::uses('FrontblockHelper', 'View/Helper');
			$fbH = new FrontblockHelper(new View());
			$nb = count($fbH->getAgentBusy());
			$offset = $requestData["page"];
			$agentlist = $fbH->getAgentBusyData($offset,$nbview);
			$stopright = 0;
			
			if(ceil($nb / $nbview) - 1 == $offset) $stopright = 1;
			
			$html = '';
                    foreach($agentlist as $agent ){ 

						$fiche_link = $fbH->Html->url(
							array(
								'language'      => $this->Session->read('Config.language'),
								'controller'    => 'agents',
								'action'        => 'display',
								'link_rewrite'  => strtolower($agent['User']['pseudo']),
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
							$set_title = __('En consultation').'  <span class="depuis_widget">depuis '.$fbH->secondsToHis($agent[0]['second_from_last_status']).'</span>';
							$set_title_css = 'consultation';
							
						}elseif ($agent['User']['agent_status'] == 'unavailable'){
							$set_title = __('Indisponible');
							$set_title_css = 'retour';
						}
					
                    	$html .= '<li class="wow fadeIn col-md-3" data-wow-delay="0.1s">

                            <div class="online-name">
                                <span class="uppercase">
                                    <a href="'.$fiche_link.'" title="'.$agent['User']['pseudo'].'"><h4 class="inline-block">'.$agent['User']['pseudo'].'</h4></a> 
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
                                <div class="online-expert-pic text-center">
                                    <a href="'.$fiche_link.'" class="sm-sid-photo"><span>';
									
									$html .= $fbH->Html->image($fbH->getAvatar($agent['User']), array(
                                                'alt' => $agent['User']['pseudo'],
                                                'title' => $agent['User']['pseudo'],
                                                'class' => 'small-profile img-responsive img-circle'
                                                ));
									$html .= '</span></a>';
									
									if($agent['0']['AverageRating']){
                                    $html .= '<p class="on-per">'.number_format($agent['0']['AverageRating'],1).'%</p>
    
                                    <div class="star-area">
                                            <ul class="list-inline list-star">
                                                '.$fbH->getStarsRateListing($agent['0']['AverageRating']).'
                                            </ul>
                                        </div>';

									}
                                $html .= '</div>
                                </div><!--col-sm-5 END-->
    
                                <div class="col-sm-7 pr0">
                                    <div class="status-box">
                                        <p class="status '.$set_title_css.'">'.$set_title.'</p>
                                        <ul class="list-inline medium-btn';
										if($agent['User']['agent_status'] == 'busy') $html .= ' alert-btn ';
										$html .= '">';
												if($agent['User']['agent_status'] == 'busy'){
												$html .= '	 <li class="alert-li"><a href="'.$fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" data-toggle="tooltip" data-placement="top" title="Recevoir une alerte sms/email" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" class="alerte-a aebutton nx_openinlightbox nxtooltip">Recevoir une<br />alerte sms/email</a></li>';
												}else{
												$html .= '<li class="tel ';
												if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1): $html .= 't-available'; else: $html .= ' disabled'; endif; 
												$html .= '">';
												 
                                                    if (isset($agent['User']['consult_phone']) && (int)$agent['User']['consult_phone'] == 1):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Tel</p><span class="ae_phone_param" style="display:none">'.$agent['User']['id'].'</span>',
                                                                array(
                                                                    'controller' => 'home',
                                                                    'action' => 'media_phone'
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_phonebox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Tel',
                                                                    'escape' => false
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a title="Tel" data-toggle="tooltip" data-placement="top" href=""><p>Tel</p></a>';
                                                        endif;
                                                $html .= '</li>';
                                                $html .= '<li class="mail ';
												if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1): $html .= ' m-available'; else: $html .= ' disabled'; endif; 
												$html .= '">';
                                                 
                                                if (isset($agent['User']['consult_email']) && (int)$agent['User']['consult_email'] == 1):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Email</p>',
                                                                array(
                                                                    'controller' => 'accounts',
                                                                    'action' => 'new_mail',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_emailbox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Email',
                                                                )
                                                            );
                                                        else:
                                                            $html .= '<a title="Email" data-toggle="tooltip" href=""><p>Email</p></a>';
                                                        endif;
                                                $html .= '</li>';
                                                $html .= '<li class="chat ';
												if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $fbH->agentActif($agent['User']['date_last_activity'])): $html .= 'c-available'; else:  $html .= 'disabled'; endif; 
												$html .= '">';
                                                 
                                                    if (isset($agent['User']['consult_chat']) && (int)$agent['User']['consult_chat'] == 1 && $fbH->agentActif($agent['User']['date_last_activity'])):
                                                    
                                                    $html .=  $fbH->Html->link('<p>Chat</p>',
                                                                array(
                                                                    'controller' => 'chats',
                                                                    'action' => 'create_session',
                                                                    'id' => $agent['User']['id']
                                                                ),
                                                                array(
                                                                    'escape' => false,
                                                                    'class' => 'nx_chatbox',
                                                                    'data-toggle' => 'tooltip',
                                                                    'data-placement' => 'top',
                                                                    'title' => 'Chat',
                                                                )
                                                            );
                                                        else:
                                                            $html .=  '<a title="Chat" data-toggle="modal" href=""><p>Chat</p></a>';
                                                        endif; 
                                                $html .= '</li>';
												}
                                        $html .= '</ul>
                                    </div><!--code-box END-->
                                </div><!--col-sm-7 END-->
                            </div><!--row END-->
    
                        </li>';
                     } 
                 $html .= '';
			
			$this->jsonRender(array('html' => $html, 'stopright' => $stopright));
		}
		
	}

}
<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::import('Controller', 'Category');

class LandingsController extends AppController {

 	public $components = array('Paginator');
    public $helpers = array('Paginator','Time');

    public function beforeFilter()
    {
        if ($this->request->is('ajax')){
            $this->layout = 'ajax';
            $this->set('isAjax',1);
        }

        parent::beforeFilter();
		$this->Auth->allow('initFilterProperties', 'getDatasForCategory', 'popup_ins');
    }


    public function display( $link_rewrite="")
    {

		$this->Session->write('type_modal','ins');
        $categoryctrl = new CategoryController();


        /* On récupère le lien attendu */
           /* $link = $this->getCmsPageLink($link_rewrite);

            if ($link !== '/'.$this->request->url){
                $this->response->statusCode(301);
                //$this->redirect($link);
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
		$idlang = 1;//patch langue plus conditions, on passe par domain
        $conditions = array(
           // 'Page.id'               => $id,
            'LandingLang.link_rewrite' => $link_rewrite,
            'Landing.active'           => 1,
            'LandingLang.lang_id'      => $idlang,
			'Landing.domain'			=> (int)$this->Session->read('Config.id_domain')
        );
        $this->Landing->LandingLang->bindModel(array(
            'belongsTo' => array(
                'PageCategory' => array(
                    'className' => 'PageCategory',
                    'foreignKey' => '',
                    'conditions' => 'Landing.page_category_id = PageCategory.id',
                    'fields' => '',
                    'order' => ''
                )
            )
        ));
        $page = $this->Landing->LandingLang->find('first',array(
                            'fields'     => 'LandingLang.*, Landing.*',
                            'conditions' => $conditions));
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

		$promo = '';
		$promo_title = '';
		$is_promo_total ='';

		//check if promo code
		if($page['LandingLang']['code_promo']){

			$this->Session->write('promo_landing',$page['LandingLang']['code_promo']);

		$this->loadModel('Voucher');
			$vouchers = $this->Voucher->find('all',array(
                'conditions' => array(
                    'Voucher.active' => 1,
					'Voucher.code' => $page['LandingLang']['code_promo'],
                ),
                'recursive' => -1,
            ));
			foreach($vouchers as $voucher){
				$promo = '';
				$promo_title = '';
				$rightToUse_once = false;
				$prod_promo = array();
				$produit_promo_select = '';

				 foreach($products as $produit){

					//Le client peut-il l'utiliser ??
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

					}
					 array_push($prod_promo, $produit);
				 }
				if($promo)$products = $prod_promo;
			}
			$is_promo_total = 0;

		}
		$this->set(compact('products','promo','promo_title','is_promo_total'));
        if (isset($page['PageCategory']['display']) && $page['PageCategory']['display'] == 0){
            $this->return404(false);
            $this->Session->setFlash(__('Cette page n\'existe pas.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
        }

        if (empty($page)){
            unset($conditions['LandingLang.link_rewrite']);
            $page = $this->Landing->LandingLang->find('first',array(
                'fields'     => 'LandingLang.*, Landing.id, Landing.active, Landing.page_category_id',
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
        if(empty($page) ){
            $this->Session->setFlash(__('Cette page n\'existe pas.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
        }


        /* Metas */
        $this->site_vars['meta_title']       = $page['LandingLang']['meta_title'];
        $this->site_vars['meta_keywords']    = $page['LandingLang']['meta_keywords'];
        $this->site_vars['meta_description'] = $page['LandingLang']['meta_description'];


		//load data agents
		$filters = $categoryctrl->initFilterProperties();

        //Permet de garder en mémoire les choix des filtres (orderby et filterby)
        if(isset($this->params->query[$this->queryIndex['ajax_for_agents']])){
            if(!empty($this->params->query[$this->queryIndex['orderby']]))    $filters['filter_orderby'][$this->params->query[$this->queryIndex['orderby']]]['active'] = true;
            if(!empty($this->params->query[$this->queryIndex['filterby']]))   $filters['filter_filterby'][$this->params->query[$this->queryIndex['filterby']]]['active'] = true;
        }

        $this->set($filters);

        /* Sommes-nous en mode recherche ? */
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

            /* Si on ne trouve pas la catégorie, on arrête */
                if (!$category) {
                    throw new NotFoundException(__('Invalid category'));
                }

            $categoryLang['CategoryLang'] = $category['CategoryLang'];

            $this->set(compact('categoryLang'));

        }



		/* Retour ajax */
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
		   $param_query = $this->params->query;
		   unset($param_query["gclid"]);
		   unset($param_query["utm_source"]);
		   unset($param_query["utm_medium"]);
		   unset($param_query["utm_campaign"]);
		   unset($param_query["utm_term"]);
		   unset($param_query["utm_content"]);
		   unset($param_query["campaign"]);
		   unset($param_query["network"]);
		   unset($param_query["device"]);
		   unset($param_query["_ga"]);
		   unset($param_query["_gac"]);
		   unset($param_query["aclk"]);
			   $datasForView = $this->requestAction(array('controller' => 'category', 'action' => 'getDatasForCategory'),array('args'=>$param_query));
			   // $datasForView = $categoryctrl->getDatasForCategory(1, 1, $this->params->query);
				$datasForView['isMobile'] = $this->request->isMobile();

            $this->set($datasForView);
        }

		/* On récupère la liste des pays disponibles */
        $this->loadModel('UserCountry');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
		$dbb_r = new DATABASE_CONFIG();
		$dbb_route = $dbb_r->default;
		$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
		$result_routing_page = $mysqli_conf_route->query("SELECT name from country_langs where country_id = '{$this->Session->read('Config.id_country')}' AND id_lang = '{$this->Session->read('Config.id_lang')}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
		$coutryname = $row_routing_page['name'];

		$result_routing_page = $mysqli_conf_route->query("SELECT user_countries_id from user_country_langs where name = '{$coutryname}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);

		$this->set('selected_countries', $row_routing_page['user_countries_id']);

		$this->loadModel('Review');
		$reviewPpage = Configure::read('Site.limitReviewPage');
        $offset = 0 * Configure::read('Site.limitReviewPage');


        $reviews = $this->Review->find('all',array(
            'fields' => array('Review.review_id', 'Review.user_id', 'Review.agent_id', 'Review.lang_id', 'Review.content', 'Review.rate', 'Review.date_add', 'Review.utile',
                'User.firstname',
                'Agent.id', 'Agent.pseudo', 'Agent.agent_number', 'Agent.has_photo'
            ),
            'conditions' => array(
                'Review.status' => 1, 'Agent.active' => 1, 'Review.parent_id' => NULL

            ),// 'Review.lang_id = '.$this->Session->read('Config.id_lang')
            'order' => array('Review.date_add' => 'desc'),
            'limit' => $reviewPpage,
            'offset' => $offset
        ));

		$review = array();

		foreach($reviews as $r){
			$avg = $this->Review->find('all',array(
				'fields' => array('avg(Review.pourcent) as av'
				),
				'conditions' => array(
					'Review.agent_id' => $r['Review']['agent_id'],
					'Review.status' => 1,
					'Review.parent_id' => NULL

				),
				'recursive' => -1
			));
			$response = $this->Review->find('first',array(
				'conditions' => array(
					'Review.parent_id' => $r['Review']['review_id'],
				     'Review.status' => 1
				),
				'recursive' => -1
			));
			$r['Review']['rate_avg'] = $avg[0][0]['av'];
			if($response){
				$r['Review']['reponse'] = $response['Review'];
			}

			$review[] = $r;
		}
		$reviews = $review;
        $this->set(compact('reviesws'));

        $this->set('landingpage', $page);
    }

    public function admin_create($post = true)
    {

		//Les domains pour les checkbox
		$this->loadModel('Domain');
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('domain_select', 'count', 'half'));


        if ($this->request->is('post') && !isset($this->request->data['LandingLang']['search']) && $post) {
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
			$this->request->data['Landing']['domain'] = '';
			if(is_array($this->request->data['domain'])){
				foreach($this->request->data['domain'] as $kd => $vd){
					if($vd)
						$this->request->data['Landing']['domain'] = $kd;
				}
			}
            //Modification des url pour les images
            $this->request->data['LandingLang'][0]['content'] = Tools::clearUrlImage($this->request->data['LandingLang'][0]['content']);
            if($this->request->data['LandingLang'][0]['content'] === false){
                $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                $this->admin_create(false);
                return;
            }

            $this->Landing->create();
            $this->Landing->saveAssociated($this->request->data);

            //On regénère le footer
            //$this->regenCacheFooter();

            $this->Session->setFlash(__('La page a bien été enregistrée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
            $this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        $this->loadModel('PageCategory');
        $this->set('lang_options', $this->Lang->find('list', array(
            'conditions' => array('active' => 1)
        )));

        //La liste des catégories des pages
        $cat_options = $this->PageCategory->getCategorySelectById($this->Session->read('Config.id_lang'), 15);

        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //Recherche----------------------------------
        $conditions = array();
        if(isset($this->request->data['LandingLang']['search'])){
            //Si une indication de page existe, on la supprime
            if(isset($this->request->query['page']))
                unset($this->request->query['page']);
            //Par titre
            if(isset($this->request->data['LandingLang']['title']))
                $conditions = array(
                    'OR' => array(
                        'LandingLang.meta_title LIKE' => '%'. $this->request->data['LandingLang']['title'] .'%',
                        'LandingLang.name LIKE' => '%'. $this->request->data['LandingLang']['title'] .'%'
                    )

                );
            //Par catégorie
            elseif($this->request->data['LandingLang']['category'])
                $conditions = array('Landing.page_category_id' => $this->request->data['LandingLang']['category']);
        }

        $this->Paginator->settings = array(
            'fields' => array('LandingLang.*', 'Landing.*', 'PageCategoryLang.name', 'Landing.page_category_id', 'Landing.active', 'Lang.name', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'landings',
                    'alias' => 'Landing',
                    'type' => 'left',
                    'conditions' => array('Landing.id = LandingLang.landing_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = LandingLang.lang_id')
                ),
                array(
                    'table' => 'page_category_langs',
                    'alias' => 'PageCategoryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'PageCategoryLang.page_category_id = Landing.page_category_id',
                        'PageCategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1,
            'group' => 'Landing.id',
            'order' => 'Landing.id DESC',
            'paramType' => 'querystring',
            'limit' => 250
        );

        $tmp_pages = $this->Paginator->paginate($this->Landing->LandingLang);

        $pages = array();
        foreach ($tmp_pages AS $page){
            $pageTransit = end($pages);

            //S'il y a un élément d'enregistrer
            if($pageTransit != false){
                if($pageTransit['landing_id'] == $page['Landing']['id']){
                    $keys = array_keys($pages);
                    $lastKey = end($keys);
                    $pages[$lastKey]['lang_name'].= ', '.$page['Lang']['name'];
                    continue;
                }
            }


            $pages[] = array(
                'langs'         =>  str_replace(",",", ",isset($page['0']['langs'])?$page['0']['langs']:''),
                'landing_id'       =>  $page['LandingLang']['landing_id'],
                'page_category_id' => $page['Landing']['page_category_id'],
				'domain' => $page['Landing']['domain'],
                'name'          =>  $page['LandingLang']['name'],
                'category_name' =>  (empty($page['PageCategoryLang']['name']) ?__('Pas de nom'):$page['PageCategoryLang']['name']),
                'etat'          =>  ($page['Landing']['active']
                                        ?'<span class="badge badge-success">'.__('Active').'</span>'
                                        :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                    ),
                'lang_name'     =>  $page['Lang']['name'],
                'link_rewrite'  =>  $page['LandingLang']['link_rewrite'],
                'active'        =>  $page['Landing']['active'],
                'hidden_page'   =>  $this->isSystemNonPublicPage($page['Landing']['page_category_id'])?true:false
            );
        }

        $this->set(compact('pages', 'langs', 'cat_options'));
    }

    public function admin_delete($id){
        $this->Landing->id = $id;
        if($this->Landing->saveField('active', 0)){
            //On regénère le footer
           // $this->regenCacheFooter();
            $this->Session->setFlash(__('La page est désactivée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la désactivation.'),'flash_warning');

        $this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_true_delete($id){
        //On supprime les langues
        $this->Landing->LandingLang->deleteAll(array('LandingLang.landing_id' => $id), false);
        //On supprime la page
        if($this->Landing->deleteAll(array('Landing.id' => $id), false)){
            //On regénère le footer
           // $this->regenCacheFooter();
            $this->Session->setFlash(__('La page a été supprimée. Si votre suppression affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la suppression.'),'flash_warning');

        $this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_add($id){
        $this->Landing->id = $id;
        if($this->Landing->saveField('active', 1)){
            //On regénère le footer
            //$this->regenCacheFooter();
            $this->Session->setFlash(__('La page est activée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de l\'activation.'),'flash_warning');

        $this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_edit($id, $post=true){

		//Les domains pour les checkbox
		$this->loadModel('Domain');
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('domain_select', 'count', 'half'));

        if($this->request->is('post') && $post){
            $requestData = $this->request->data;

			if($requestData['AdminEmailIdPage'] != ''){
			/*	$idpage = $requestData['AdminEmailIdPage'];
				$email = $requestData['AdminEmailTest'];

				$page = $this->Landing->LandingLang->find('first',array(
                'conditions' => array('Landing.id' => $idpage)));

				$is_send = $this->sendCmsTemplateByMail($idpage, 1, $email, array(
					  'URL_CONNEXION' =>   '',
					  'PIXEL' =>   '',
				),true);
				if($is_send){
					$this->Session->setFlash(__('Mail test envoyé.'),'flash_success');
				}else{
					$this->Session->setFlash(__('Erreur lors envoi du mail test.'),'flash_warning');
				}	*/

			}else{

				//Pour chaque lang
				/*foreach($requestData['LandingLang'] as $key => $val){
					//Si le formulaire est vide
					if(count(array_keys($val, '')) >= 5){
						//On le supprime
						unset($requestData['LandingLang'][$key]);
						continue;
					}
				}*/
				$tab_duplicate = $requestData['Landing'][1];
				if(is_array($tab_duplicate)){
					if($tab_duplicate['duplicate_belgique']){
						$requestData['LandingLang'][11] = $requestData['LandingLang'][1];
						$requestData['LandingLang'][11]['lang_id'] = 11;
					}
					if($tab_duplicate['duplicate_canada']){
						$requestData['LandingLang'][8] = $requestData['LandingLang'][1];
						$requestData['LandingLang'][8]['lang_id'] = 8;
					}
					if($tab_duplicate['duplicate_suisse']){
						$requestData['LandingLang'][10] = $requestData['LandingLang'][1];
						$requestData['LandingLang'][10]['lang_id'] = 10;
					}
					if($tab_duplicate['duplicate_luxembourg']){
						$requestData['LandingLang'][12] = $requestData['LandingLang'][1];
						$requestData['LandingLang'][12]['lang_id'] = 12;
					}
				}
				foreach($requestData['LandingLang'] as $key => $val){

					//Si le formulaire est vide
					if(!$requestData['LandingLang'][$key]['name']){
						//On le supprime
						unset($requestData['LandingLang'][$key]);
						continue;
					}
				}


				//Si aucune page n'est renseigné
				if(empty($requestData['LandingLang'])){
					$this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
					$this->redirect(array('controller' => 'landings', 'action' => 'edit', 'admin' => true, 'id' => $id),false);
				}
					/* Cas des pages normales */
					$the_fields = array('lang_id','landing_id','meta_title','link_rewrite','meta_keywords','meta_description','content','content_mobile', 'name','show_pricetable', 'show_agents', 'slide', 'titre1', 'font_size_1', 'font_color_1', 'titre2', 'titre3', 'titre4', 'font_size_2', 'font_color_2', 'btn1_txt', 'btn1_url', 'btn2_txt', 'btn2_url','font_font_1','font_font_2','font_font_3','font_font_4','font_type_1','font_type_2','font_type_3','font_type_4','font_size_3','font_size_4','font_color_3','font_color_4','date_compteur','text_compteur','size_compteur','color_compteur'
					,'code_promo','slide_mobile','titre_mobile', 'font_type_titre_mobile','font_size_titre_mobile', 'font_color_titre_mobile', 'font_align_titre_mobile', 'font_type_ligne2_mobile', 'font_size_ligne2_mobile', 'font_color_ligne2_mobile', 'font_align_ligne2_mobile', 'font_type_ligne3_mobile', 'font_size_ligne3_mobile', 'font_color_ligne3_mobile', 'font_align_ligne3_mobile', 'ligne2_mobile', 'ligne3_mobile', 'font_font_titre_mobile', 'font_font_ligne2_mobile'	, 'font_font_ligne3_mobile'	, 'show_reviews', 'template', 'content_preview', 'content_preview_mobile', 'reassurance_1','reassurance_2','reassurance_3','btn1_color','btn1_bg','btn_mobile_bg','btn_mobile_color','content_2','content_2_mobile'
									   );
						$the_required = array('lang_id','landing_id','meta_title', 'name');

				foreach($requestData['LandingLang'] as $key => $val){
					//Vérification des champs requis


					//$val = 1;//Tools::checkFormField($val, $the_fields, $the_required);
					/*if($val === false){
						$this->Session->setFlash(__('Veuillez remplir les champs obligatoires pour chaque langue renseignée.'),'flash_warning');
						$this->redirect(array('controller' => 'landings', 'action' => 'edit', 'admin' => true, 'id' => $id),false);
					}*/
					$countFile = 0;
                $countSize = 0;
                $countMime = 0;
                $countGood = 0;
                $countBad = 0;
					if($this->isUploadedFile($requestData['LandingLang'][$key]['file'])){

                        $countFile++;
                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['LandingLang'][$key]['file']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                        if(!in_array($dataSlide['mime'], array('image/jpeg', 'image/pjpeg'))){
                            $countMime++;
                            continue;
                        }


                        //Le nom du slide
                        $filename = $requestData['LandingLang'][$key]['landing_id'].'-Landingslide-'.$requestData['LandingLang'][$key]['lang_id'].'.jpg';
                        //On save le nom du fichier
                        $requestData['LandingLang'][$key]['slide'] = $filename;

                        //En déplace le fichier
                        if(!move_uploaded_file($requestData['LandingLang'][$key]['file']['tmp_name'], Configure::read('Site.pathLandingSlide').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;

                    }
						$countFile = 0;
                $countSize = 0;
                $countMime = 0;
                $countGood = 0;
                $countBad = 0;
					if($this->isUploadedFile($requestData['LandingLang'][$key]['file_mobile'])){

                        $countFile++;
                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['LandingLang'][$key]['file_mobile']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                        if(!in_array($dataSlide['mime'], array('image/jpeg', 'image/pjpeg'))){
                            $countMime++;
                            continue;
                        }


                        //Le nom du slide
                        $filename = $requestData['LandingLang'][$key]['landing_id'].'-Landingslidemobile-'.$requestData['LandingLang'][$key]['lang_id'].'.jpg';
                        //On save le nom du fichier
                        $requestData['LandingLang'][$key]['slide_mobile'] = $filename;

                        //En déplace le fichier
                        if(!move_uploaded_file($requestData['LandingLang'][$key]['file_mobile']['tmp_name'], Configure::read('Site.pathLandingSlide').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;

                    }


					//Modification des url pour les images
					$requestData['LandingLang'][$key]['content'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['content']);
					$requestData['LandingLang'][$key]['content'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['content'],'"/media','"'.Configure::read('Site.baseUrlFull').'/media');
					$requestData['LandingLang'][$key]['content'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['content'],'"/theme','"'.Configure::read('Site.baseUrlFull').'/theme');
					if($requestData['LandingLang'][$key]['content'] === false){
						$this->Session->setFlash(__('Une erreur est survenue avec une des langues, votre contenu est sûrement vide.'),'flash_warning');
						$this->admin_edit($id, false);
						return;
					}
				}

				//je recup current url
			foreach($requestData['LandingLang'] as $key => $val){

				$r = $this->Landing->find('first',array(
					'conditions' => array('Landing.id' => $id),
					'fields' => array('Landing.page_category_id'),
					'recursive' => -1
				));
				$id_page_category = $r['Landing']['page_category_id'];
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

				$this->loadModel('LandingLang');
				$url = $this->LandingLang->find('first',array(
								'conditions' => array('landing_id' => $id, 'lang_id' => $key),
								'recursive' => -1,
							));
				if($requestData['LandingLang'][$key]['link_rewrite'] && $url['LandingLang']['link_rewrite'] != $requestData['LandingLang'][$key]['link_rewrite']){
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
					$redirectData['Redirect']['old'] = '/'.$langue['Lang']['language_code'].'/'.$name_page_category."/".$url['LandingLang']['link_rewrite'];
					$redirectData['Redirect']['new'] = $protocol.$domaine['Domain']['domain'].'/'.$langue['Lang']['language_code'].'/'.$name_page_category.'/'.$requestData['LandingLang'][$key]['link_rewrite'];
					$this->Redirect->create();
					$this->Redirect->save($redirectData);
				}
			}
				//L'etat de la page
				$this->Landing->id = $id;
				$this->Landing->save($requestData['Landing']);
				//On supprime toutes les langues
				$this->Landing->LandingLang->deleteAll(array('LandingLang.landing_id' => $id), false);
				/*foreach($requestData['PageLang'] as $page){
					if($this->Page->PageLang->exist($page['landing_id'], $page['lang_id'])){
						$page['meta_title'] = '"'.$page['meta_title'].'"';
						$page['link_rewrite'] = '"'.$page['link_rewrite'].'"';
						$page['meta_keywords'] = '"'.$page['meta_keywords'].'"';
						$page['meta_description'] = '"'.$page['meta_description'].'"';
						$page['content'] = '"'.$page['content'].'"';

						$this->Page->PageLang->updateAll($page, array('landing_id' => $page['landing_id'], 'lang_id' => $page['lang_id']));
					}else{
						$this->Page->PageLang->create();
						$this->Page->PageLang->save($page);
					}
				}*/



				$this->Landing->LandingLang->saveMany($requestData['LandingLang']);

				//On regénère le footer
				//$this->regenCacheFooter();

				$this->Session->setFlash(__('La page a été modifiée.'), 'flash_success');

				$this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
			}
        }

        $this->loadModel('Lang');
        $this->loadModel('PageCategory');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //La liste des catégories des pages
        $cat_options = $this->PageCategory->getCategorySelect($this->Session->read('Config.id_lang'));

        //On récupère toutes les infos des pages
        $pages = $this->Landing->find('all',array(
            'conditions' => array('Landing.id' => $id),
            'recursive' => 1
        ));

        //Un tableau qui contient les données pour chaque langue renseigné
        foreach($pages[0]['LandingLang'] as $pageLang){
            $langDatas[$pageLang['lang_id']] = $pageLang;
        }

        //Etat de la page
        $activePage = $pages[0]['Landing']['active'];
        //Nom Page
        $namePage = $pages[0]['LandingLang'][0]['meta_title'];
        //Category
        $catPage = $pages[0]['Landing']['page_category_id'];

		$domainPage = $pages[0]['Landing']['domain'];
		$linkPage = $pages[0]['LandingLang'][0]['link_rewrite'];

        //Variable qui stocke l'id de la page, pour un accès plus rapide
        $idPage = $pages[0]['Landing']['id'];


        $isHiddenSystemPage = false;
        $page_parameters = array();
        if (isset($pages['0']['Landing']['page_category_id'])){
            $isHiddenSystemPage = $this->isSystemNonPublicPage($pages['0']['Landing']['page_category_id']);
            $page_parameters = $this->Landing->getVarsOfPage($id);
        }

        $this->set(compact('page_parameters','isHiddenSystemPage','langDatas', 'langs', 'idPage', 'activePage', 'namePage', 'cat_options', 'catPage','domainPage','linkPage'));
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
                $this->redirect(array('controller' => 'landings', 'action' => 'create_category', 'admin' => true), false);
            }
            //On save le tout
            $this->loadModel('PageCategory');
            $this->PageCategory->create();
            if($this->PageCategory->saveAssociated($requestData)){
                //On regénère le footer
               // $this->regenCacheFooter();
                $this->Session->setFlash(__('La catégorie a bien été enregistrée.'),'flash_success');
            }
            else
                $this->Session->setFlash(__('Erreur, la catégorie n\'a pu être sauvegardé.'),'flash_warning');

            $this->redirect(array('controller' => 'landings', 'action' => 'list_category', 'admin' => true), false);
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

        //Variable qui stocke l'id de la catégorie, pour un accès plus rapide
        $idPageCat = $pagesCat[0]['PageCategory']['id'];

        $this->set(compact('langDatas', 'langs', 'idPageCat', 'activePageCat', 'namePageCat', 'footerPageCat'));
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

        $this->redirect(array('controller' => 'landings', 'action' => 'list_category', 'admin' => true), false);
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
        $this->redirect(array('controller' => 'landings', 'action' => 'list_category', 'admin' => true), false);
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

	public function popup_ins(){
		$id_lang = $this->Session->read('Config.id_lang');

        //Requete ajax
       // if($this->request->is('ajax')){
            //Utilisateur non connecté
            if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
                $this->layout = '';

				$content = $this->render('/Elements/ins_modal');

				$intro = $this->getCmsPage(308, $id_lang);
				$this->loadModel('UserCountry');
				$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
                $this->set(array('title' => __('Accès client'), 'content' => $intro["PageLang"]['content'].$content, 'button' => __('Annuler')));
                $response = $this->render('/Elements/modal');
				//$this->set('response');
                $this->jsonRender(array('html' => $response->body(), 'return' => false));
            }

            $this->jsonRender(array('return' => true));
       // }
    }

	public function admin_duplicate($id, $post=true){

		$this->loadModel('LandingLang');

		$r = $this->Landing->find('first',array(
					'conditions' => array('Landing.id' => $id),
					'recursive' => -1
				));
		$rl = $this->LandingLang->find('all',array(
					'conditions' => array('LandingLang.landing_id' => $id),
					'recursive' => -1
				));

		unset($r['Landing']['id']);
		$this->Landing->create();
		$this->Landing->save($r['Landing']);
		$idnew = $this->Landing->id;

		foreach($rl as $landingl){
			$landingl['LandingLang']['landing_id'] = $idnew;
			unset($landingl['LandingLang']['id']);
			$this->LandingLang->create();
			$this->LandingLang->save($landingl['LandingLang']);
		}

		$this->Session->setFlash(__('La page a été dupliqué.'), 'flash_success');

		$this->redirect(array('controller' => 'landings', 'action' => 'list', 'admin' => true), false);
	}

}

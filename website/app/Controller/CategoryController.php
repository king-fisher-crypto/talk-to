<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');


class CategoryController extends AppController {
    protected $filter_orderby = array();
    protected $filter_filterby = array();
    public $helpers = array('Time');
	public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('initFilterProperties', 'getDatasForCategory');

    }


    public function initFilterProperties()
    {
        $this->filter_orderby = array(
            'default'  =>   array(
                'label'      =>      __('Trier par : défaut'),
                'orderby'    =>      '',
                'enabled'    =>      true
            ),
            'disponibilite'  =>   array(
                'label'      =>      __('Disponible'),
                'orderby'    =>      'Field(User.agent_status,\'available\',\'busy\',\'unavailable\')',
                'enabled'    =>      true
            ),
            'topvoyantday'  =>   array(
                'label'      =>      __('Top experts / jour'),
                'orderby'    =>      '',
                'enabled'    =>      false
            ),
            'nombreconsult'  =>   array(
                'label'      =>      __('Les plus consultés'),
                'orderby'    =>      array('User.consults_nb desc'),
                'enabled'    =>      true,
				'conditions' =>       array('User.agent_status => \'available\''),
            ),
            'meilleuresnotes'  =>   array(
                'label'      =>      __('Les mieux notés'),
                'orderby'    =>      array('CAST(User.reviews_avg AS DECIMAL(4,1)) desc, User.reviews_nb desc'),
                'enabled'    =>      true,
				'conditions' =>       array('User.agent_status => \'available\''),
            ),
        );

        $this->filter_filterby = array(
            'allagents'  =>   array(
                'label'      =>      __('Tous les expert(e)s'),
                'conditions' =>       array(),
                'enabled'    =>      true
            ),
            'newagents'  =>   array(
                'label'      =>      __('Nouveaux experts'),
                'conditions' =>      array('User.date_new > \''.date("Y-m-d H:i:s",time() - (86400 * 60)).'\''),
                'enabled'    =>      true
            ),
			'availableagents'  =>   array(
                'label'      =>      __('Disponible actuellement'),
                'conditions' =>      array('User.agent_status = \'available\''),
                'enabled'    =>      true
            ),
            'homme'      =>   array(
                'label'      =>      __('Hommes'),
                'conditions' =>      array('User.sexe = 1'),
                'enabled'    =>      true
            ),
            'femme'      =>   array(
                'label'      =>      __('Femmes'),
                'conditions' =>      array('User.sexe = 2'),
                'enabled'    =>      true
            )
        );

        /*
        if ($this->Session->check('Config.domain_langs')){
            $langs = $this->Session->read('Config.domain_langs');
            if (count($langs)>1){
                foreach ($langs AS $id_lang => $lang)
                    $this->filter_filterby['parle'.$id_lang] =   array(
                            'label'      =>      __('Parle').' '.$lang,
                            'conditions' =>      array('FIND_IN_SET(\''.(int)$id_lang.'\', User.langs)'),
                            'enabled'    =>      true
                    );
            }
        }*/

        $parle_langs = array(
            '1' => __('Parle Français'),
            '2' => __('Parle Anglais'),
            '3' => __('Parle Allemand'),
            '4' => __('Parle Espagnol'),
            '5' => __('Parle Italien'),
            '7' => __('Parle Portugais'),
            '8' => __('Parle Français Canadien')
        );
        foreach ($parle_langs AS $id_lang => $lang){
            $this->filter_filterby['parle'.$id_lang] =   array(
                'label'      =>      $lang,
                'conditions' =>      array('FIND_IN_SET(\''.(int)$id_lang.'\', User.langs)'),
                'enabled'    =>      true
            );
        }


        return array(
            'filter_orderby'  => $this->filter_orderby,
            'filter_filterby' => $this->filter_filterby
        );
    }

    public function display($id=1, $link_rewrite=false, $page = 1, $limitAgents = '')
    {
		ini_set("memory_limit",-1);
        $filters = $this->initFilterProperties();

        //Permet de garder en mémoire les choix des filtres (orderby et filterby)
        if(isset($this->params->query[$this->queryIndex['ajax_for_agents']])){
            if(!empty($this->params->query[$this->queryIndex['orderby']]))    $filters['filter_orderby'][$this->params->query[$this->queryIndex['orderby']]]['active'] = true;
            if(!empty($this->params->query[$this->queryIndex['filterby']]))   $filters['filter_filterby'][$this->params->query[$this->queryIndex['filterby']]]['active'] = true;
        }
		

        $this->set($filters);

        /* Sommes-nous en mode recherche ? */
            $inSearchOrAjaxMode =  (isset($this->request->data['searchpost']) || isset($this->request->data['ajax_for_agents']))?1:0;

        /* Avons-nous un id et un link_rewrite, ou bien sommes-nous en mode recherche ? */
            if ((!$id || !$link_rewrite) && (!$inSearchOrAjaxMode)) {
                //throw new NotFoundException(__('Invalid category'));
            }

        /* On récupère les infos catégorie */
        if (!$inSearchOrAjaxMode){
			$this->Session->write('type_modal','login');
			$codelang = $this->Session->read('Config.language');
			
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($codelang == 'fre'){
				if($extension == 'ca')$codelang='frc';	
				if($extension == 'ch')$codelang='frh';
				if($extension == 'be')$codelang='frb';
				if($extension == 'lu')$codelang='frl';
			}

            $category = $this->Category->CategoryLang->find('first', array(
                                           'conditions' => array(
                                               'Lang.language_code' => $codelang,
                                               'Category.active' => 1,
                                               'Category.id' => $id
                                           ))
            );

            /* Si on ne trouve pas la catégorie, on arrête */
                if (!$category) {
                    throw new NotFoundException(__('Invalid category'));
                }

            $categoryLang['CategoryLang'] = $category['CategoryLang'];

            $this->set(compact('categoryLang'));

            /* Metas */
                $this->site_vars['meta_title']          = $category['CategoryLang']['meta_title2'];
                $this->site_vars['meta_keywords']       = $category['CategoryLang']['meta_keywords2'];
                $this->site_vars['meta_description']    = $category['CategoryLang']['meta_description2'];

        }

        /* Retour ajax */
        if (isset($this->request->data['ajax_for_agents'])){
            $id = isset($this->request->data['id_category'])?$this->request->data['id_category']:$id;
            $categories = isset($this->request->data['categories'])?$this->request->data['categories']:array();

            //Datas de la categorie
            $datasForView = $this->getDatasForCategory($id, $this->request->data['page'], $this->params->query, $categories, $this->request->data['limitAgents']);
            $this->layout = '';
            /* On envoie le délai de rafraichissement ajax à la vue */
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
			$datasForView['page'] = $this->request->data['page'];
			$datasForView['limitAgents'] = $this->request->data['limitAgents'];
			
            /* On genere la vue */
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
            /* Datas de la catégorie */
            $datasForView = array();//$this->getDatasForCategory($id, $page, $this->params->query);
			$datasForView['page']= $page;
			$datasForView['limitAgents']= $limitAgents;
			$datasForView['mediaChecked']= array();
			$datasForView['isMobile'] = $this->request->isMobile();	
            $this->set($datasForView);
        }


        /* On construit le lien de la catégorie */
            $category_link = $this->getCategoryLink($link_rewrite, false, true);
            $this->set('category_link', $category_link);
		
		$this->set('category_id', $id);
    }

    public function displayUnivers($link_rewrite = false){
		$this->Session->write('type_modal','login');		
        /* On récupère le lien attendu */
        $link = $this->getCategoryLink($link_rewrite);

        if ($link !== '/'.$this->request->url){
            $this->response->statusCode(301);
            $this->redirect($link);
        }

        //On récupère la catégorie
        $options = array(
            'fields' => array('CategoryLang.*'),
            'conditions' => array(

                'CategoryLang.lang_id' => $this->Session->read('Config.id_lang'),
                'CategoryLang.link_rewrite' => $link_rewrite
            ),
            'joins' => array(
                array(
                    'table' => 'categories',
                    'alias' => 'Category',
                    'type' => 'inner',
                    'conditions' => array(
                        'Category.id = CategoryLang.category_id',
                        'Category.active = 1'
                    )
                )
            ),
            'recursive' => -1
        );

        if ($link_rewrite == 1)
            unset($options['conditions']['CategoryLang.link_rewrite']);
        $cat = $this->Category->CategoryLang->find('first', $options);



        if(empty($cat)){
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        /* Metas */
        $this->site_vars['meta_title']          = $cat['CategoryLang']['meta_title'];
        $this->site_vars['meta_keywords']       = $cat['CategoryLang']['meta_keywords'];
        $this->site_vars['meta_description']    = $cat['CategoryLang']['meta_description'];

        $this->set(compact('cat'));
    }

    public function getFiltersConditions($args = array())
    {
        $conditions = array();
        $orderby = array();

		//Si on a des paramètres query on retranscrit les vrais noms des filtres
        if(!empty($args)){
            foreach($this->queryIndex as $fullname => $queryName){
                if(isset($args[$queryName])){
                    $args[$fullname] = $args[$queryName];
                    unset($args[$queryName]);
                }
            }
        }
		
		

        $filters = (isset($this->request->data))?$this->request->data:false;
        if(!empty($args) && !$filters) $filters = $args;
        if (!$filters)return array();

        /* Medias possibles */
            $medias = array_keys($this->consult_medias);
            $mediaChecked = array();

        /* Avons-nous une demande par media ? */
            if (isset($filters['media']) && is_array($filters['media'])){
                /* On nettoie le tableau des éléments indésirables */
                    foreach ($filters['media'] AS $k => $v)
                        if (!in_array($v, $medias))
                            unset($filters['media'][$k]);

                /* On ajoute les conditions */
                foreach ($filters['media'] AS $media){
                    if ($media == 'chat'){
                        $conditions['OR'][] = '(User.consult_chat = 1 AND  User.date_last_activity > \''.date("Y-m-d H:i:s",(time() - Configure::read('Chat.maxTimeInactif'))).'\')';
                    }else{
                        $conditions['OR'][] = 'User.consult_'.$media.' = 1';
                    }
                    array_push($mediaChecked, $media);
                }

            }

        /* Order by */
        if (isset($filters['orderby']) && !empty($filters['orderby'])){
            /* le filtre est-il dans le tableau des paramètres ? */

            if (isset($this->filter_orderby[$filters['orderby']]) && !empty($this->filter_orderby[$filters['orderby']])){
                /* Le filtre est-il activé ? */
                if ($this->filter_orderby[$filters['orderby']]['enabled'] == true){
                    if (!empty($this->filter_orderby[$filters['orderby']]['orderby']))
                        $orderby[] = $this->filter_orderby[$filters['orderby']]['orderby'];
                }
            }
        }


        /* Filter by */
        if (isset($filters['filterby']) && !empty($filters['filterby'])){
            /* Le filtre est-il dans le tableau des paramètres ? */
            if (isset($this->filter_filterby[$filters['filterby']])){
                /* Le filtre est-il activé ? */
                if ($this->filter_filterby[$filters['filterby']]['enabled'] == true){
                    $conditions[] = $this->filter_filterby[$filters['filterby']]['conditions'];
                }
            }
        }


        /* Terme de recherche */
            if (isset($filters['term']) && !empty($filters['term']) && isset($filters['term_novalue']) && !empty($filters['term_novalue'])){
                if ($filters['term'] != $filters['term_novalue']){
                    $this->loadModel('AgentPseudo');
					$this->loadModel('User');
					if(is_numeric($filters['term'])){
						$idAgents = $this->User->find('list', array(
							'fields'        => array('User.id'),
							'conditions'    => array('User.agent_number = \''.$filters['term'].'\' '),
							'recursive'     => -1
						));
					}else{
						$idAgents = $this->AgentPseudo->find('list', array(
							'fields'        => array('AgentPseudo.user_id'),
							//'conditions'    => array('MATCH(AgentPseudo.pseudo) AGAINST(\''.$filters['term'].'*\' IN BOOLEAN MODE)'),
							'conditions'    => array('AgentPseudo.pseudo LIKE \'%'.$filters['term'].'%\' '),
						   // 'order'         => 'MATCH(AgentPseudo.pseudo) AGAINST(\''.$filters['term'].'*\') desc',
						   'order'         => 'AgentPseudo.pseudo ASC',
							'recursive'     => -1
						));
					}
                    $conditions[] = array('User.id' => $idAgents);
                }
            }

        return array(
            'conditions' => $conditions,
            'orderby'    => $orderby,
            'mediaChecked' => $mediaChecked
        );
    }

    public function getDatasForCategory($id_category=1, $page = 1, $args = array(), $categories = array(), $limitAgents = '')
    {
		if(isset($this->params) && is_array($this->params)){
			$params = $this->params;
			if(is_array($params['args'])){
				$args = $params['args'];
			}
			if(is_array($params['categories'])){
				$categories = $params['categories'];
			}
		}

		/* On récupère les agents de la catégorie courante */
        if ($id_category == 0 || $id_category == 1){
            $data = $this->getAgentsForHomepage($this->Session->read('Config.id_country'), $this->Session->read('Config.id_lang'),$page, $args, $categories,$limitAgents);
            $agents = $data['rows'];
            $countAgents = $data['countAgent'];
            $mediaChecked = $data['mediaChecked'];
        }else{
            $data = $this->getAgentsForCategoryId($id_category, $this->Session->read('Config.id_country'), $this->Session->read('Config.id_lang'), $page, $args, $categories,$limitAgents);
            $agents = $data['rows'];
            $countAgents = $data['countAgent'];
            $mediaChecked = $data['mediaChecked'];
        }


        /* On récupère les numéros de téléphone du pays et de la langue courante */
            $this->loadModel('CountryLangPhone');
            $phones = $this->CountryLangPhone->getPhones($this->Session->read('Config.id_country'), $this->Session->read('Config.id_lang'));
        /* On récupère les langues */
            /*
            $this->loadModel('Lang');
            $langs = $this->Lang->find("list", array(
                'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
                'conditions'    => array('Lang.active' => 1),
                'recursive' => -1
            ));
            */
            $langs = $this->getActiveLangs();
        


        /* Compteur texte */
            if ($countAgents == 0){
                $agents = array();
                $count_html = __('Aucun expert ne correspond à vos critères');
            }elseif ($countAgents == 1){
                $count_html = __('Nous avons ').'<div class="textcolor">1 '.__('expert').'</div> '.__("à vous proposer");
            }elseif ($countAgents > 1){
                $count_html = __('Nous avons ').'<div class="textcolor">'.$countAgents.' '.__('experts').'</div> '.__("à vous proposer");
            }
            $this->set('count_html', $count_html);

        /* Variable category */
            $category_id = $id_category;

        /* On envoie les datas à la vue */
        return compact('agents','category','phones','category_id','langs','count_html','countAgents', 'page', 'mediaChecked');
    }
    
    public function getAgentsForCategoryId($id_category=0, $id_country=0, $id_lang=0, $page = 1, $args = array(), $categories = array() ,$limitAgents= '')
    {
		
		if (isset($categories) && is_array($categories) && count($categories)){
			$conditions = array(
                            'User.role'   => 'agent',
                            'User.deleted'=> 0,
                            'User.active' => 1,
                            'FIND_IN_SET('.(int)$id_country.',User.countries)',
                            'FIND_IN_SET('.(int)$id_lang.',User.langs)'                                    
                            );
		}else{
        $conditions = array(
                            'Category.id' => (int)$id_category,
                            'User.role'   => 'agent',
                            'User.deleted'=> 0,
                            'User.active' => 1,
                            'FIND_IN_SET('.(int)$id_country.',User.countries)',
                            'FIND_IN_SET('.(int)$id_lang.',User.langs)'                                    
                            );
		}
        $order = array();
        $arrayReturn = $this->getAgents($conditions, $order, $id_lang, array(), $page, $args, $categories, $limitAgents);
                     
        return $arrayReturn;
    }
    public function getAgentsForHomepage($id_country=0, $id_lang=0, $page = 1, $args = array(), $categories = array(),$limitAgents= '')
    {
        $conditions = array(
                            'User.role'   => 'agent',
                            'User.deleted'=> 0,
                            'User.active' => 1,
                            'FIND_IN_SET('.(int)$id_country.',User.countries)',
                            'FIND_IN_SET('.(int)$id_lang.',User.langs)'                                    
                            );

        $order = array();
        $arrayReturn = $this->getAgents($conditions, $order, $id_lang, array('group' => array('User.id')), $page, $args, $categories,$limitAgents);
                     
        return $arrayReturn;
    }

	public function getAgents($conditions=array(), $order=array(), $id_lang=0, $parms=array(), $page = 1, $args = array(), $categories = array(), $limitAgents = '')
    {
		$arrayReturn = array();
		
		$orderBy = array();
		
		$is_filter = false;
		
		if(!$limitAgents){
			$limitAgents=Configure::read('Site.limitAgentPage');
		}
		
		if($limitAgents > Configure::read('Site.limitAgentPage')){
			$is_filter = true;
		}

        /* On récupère les filtres utilisateur */
            $filters = $this->getFiltersConditions($args);

        /* On ajoute les conditions des filtres */
            if (isset($filters['conditions']) && !empty($filters['conditions'])){
                $conditions = array_merge($conditions, $filters['conditions']);
				$is_filter = true;
			}
				
		if (isset($categories) && is_array($categories) && count($categories)){
			$list_cat_filter = array();
			foreach($categories as $catfilter){
				switch ($catfilter) {
					case "Voyants &amp; Mediums":
					case "Voyants":
					case "Voyant":
						array_push($list_cat_filter , 5);
						break;
					case "Tarologues":
						array_push($list_cat_filter , 7);
						break;
					case "Astrologues":
						array_push($list_cat_filter , 2);
						break;
					case "Cartomanciens":
						array_push($list_cat_filter , 3);
						break;
					case "Numerologues":
						array_push($list_cat_filter , 6);
						break;
					case "Magnetiseurs":
						array_push($list_cat_filter , 20);
						break;
					case "Coaching":
						array_push($list_cat_filter , 25);
						break;
					case "Interprétation des rêves":
						array_push($list_cat_filter , 26);
						break;
					case "Médium":
					case "Médiums":
					case "Medium":
					case "Mediums":
						array_push($list_cat_filter , 27);
						break;
					case "Channeling":
						array_push($list_cat_filter , 28);
						break;
					case "Tous":
						array_push($list_cat_filter , 5);
						array_push($list_cat_filter , 7);
						array_push($list_cat_filter , 2);
						array_push($list_cat_filter , 3);
						array_push($list_cat_filter , 6);
						array_push($list_cat_filter , 20);
						array_push($list_cat_filter , 25);
						array_push($list_cat_filter , 26);
						array_push($list_cat_filter , 27);
						array_push($list_cat_filter , 28);
						break;
				}	
			}
			$conditions['CategoryUser.category_id'] = $list_cat_filter ;
			$is_filter = true;
		}
		
		
        /* On ajoute les conditions d'order */
            if (isset($filters['orderby']) && !empty($filters['orderby'])){
                $orderBy = $filters['orderby'];
            	$orderBy = array_merge($orderBy, $order);
				$is_filter = true;
			}

		$now = date('Y-m-d H:i:s');
		if($id_lang == 8 || $id_lang == 10 || $id_lang == 11 || $id_lang == 12 )$id_lang = 1;
        
		/* On prépare les paramètres de requete */
        
		/*$params = array(
                                'fields'     => array('UserPresentLang.*','User.id','User.pseudo','User.agent_status','User.agent_number','User.consult_chat','User.consult_email','User.consult_phone',
                                                'User.has_photo','User.has_audio', 'User.langs','User.date_last_activity',
                                                'IF((agent_status = \'busy\'),
                                                (SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$now.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status'),
                                'conditions' => $conditions,
                                'order'      => $orderBy,
                                'recursive'  => 1,
                                'joins' => array(
                                    array('table' => 'user_present_lang',
                                        'alias' => 'UserPresentLang',
                                        'type' => 'left',
                                        'conditions' => array(
                                            'UserPresentLang.user_id = CategoryUser.user_id',
                                            'UserPresentLang.lang_id = '.$id_lang
                                        )
                                    )
                                )

        );*/
		if (empty($orderBy)){

    		$orderBy = array();
			$chat_dec = Configure::read('Site.chat_dec');
			
			//DEBUT VERSION LIBRE SI TEL / CHAT OU PHONE
			
			$orderBy[] = 'IF(User.agent_status=\'available\' OR User.agent_status =\'busy\' OR User.agent_status =\'occupied\',1,0) DESC';  /* disponibilite*/
			
			#$orderBy[] = 'User.consult_phone DESC'; /* Phone */
			$orderBy[] = '
							IF(
								(
									User.consult_email
										+
									IF(
										(IF(((UNIX_TIMESTAMP(now())-'.$chat_dec.') - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_phone
								)
							=3,1,0) DESC'; /* Email telephone chat actif  date last activity +3600*/
			$orderBy[] = 'User.list_pos ASC';  /* position aleatoire*/
			#$orderBy[] = 'IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) DESC';  /* Chat*/
			$orderBy[] = 'User.consult_email DESC'; /* email*/
			
			//VERSION PRIO NBR DE MODE
			//$orderBy[] = 'IF(User.agent_status=\'available\' OR User.agent_status =\'busy\',1,0) DESC';  /* disponibilite*/
			
			/*$orderBy[] = '
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
							=3,1,0) DESC';*/ /* Email telephone chat actif  date last activity +3600*/

			#$orderBy[] = 'IF(User.consult_phone+User.consult_chat+User.consult_email=3,1,0) DESC';/* Email telephone chat */

			#$orderBy[] = 'IF(User.consult_phone+User.consult_chat=2,1,0) DESC';  /* Telephone et chat */
			/*$orderBy[] = '
							IF(
								(
									
									IF(
										(IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_phone
								)
							=2,1,0) DESC';*/ /* Telephone et chat +3600 */
			
			//$orderBy[] = 'IF(User.consult_phone+User.consult_email=2,1,0) DESC';  /* Telephone et mail */
   			//$orderBy[] = 'User.consult_phone DESC'; /* Phone */
    		#$orderBy[] = 'IF(User.consult_chat+User.consult_email=2,1,0) DESC';  /* Chat et mail */
			/*$orderBy[] = '
							IF(
								(
									
									IF(
										(IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) + User.consult_chat) = 2
										,1,0
									  )
										+
									User.consult_email
								)
							=2,1,0) DESC';*/ /* Chat et mail */
							
    		
			//$orderBy[] = 'IF((UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <= 60,1,0) DESC';  /* Chat*/
			//$orderBy[] = 'User.consult_email DESC'; /* email*/
			//$orderBy[] = 'User.list_pos ASC';  /* position aleatoire*/
			
			//OLD CODE
			
			/*$orderBy[] = 'CASE WHEN User.consult_chat = 0 User.consult_phone = 0 THEN ';
			
			
			$orderBy[] = ' ELSE ';
			
			
			 
			$orderBy[] = 'END';*/
			
    		/*
			
    		//$orderBy[] = '(SELECT COUNT(*) FROM user_credit_history WHERE agent_id = CategoryUser.user_id AND media = \'phone\') DESC';  /* + consultes */
			//$orderBy[] = '(SELECT ROUND(AVG(rate)) FROM reviews WHERE agent_id = CategoryUser.user_id) DESC';  /* mieux noté */
   			
			
			
			/*
			$orderBy = array(
				0 => 'agent_status ASC',
				1 => 'countPhoneCall DESC',   AND media = \'phone\'
				2 => 'User.consult_phone DESC',
				3 => 'User.consult_email DESC',
				4 => 'User.consult_chat DESC'
		
			);
			*/
		}
		$dateNow = date('Y-m-d H:i:s');
		
		if ($id_lang){
			if(count($conditions) == 5 && empty($filters['orderby']) && empty($filters['filterby'])){//PATCH  && 1==2  if (isset($filters['orderby']) && !empty($filters['orderby']))
				$params = array(
					'fields'     => array('UserPresentLang.*','User.id','User.pseudo','User.date_add','User.flag_new','User.date_new','User.nb_consult_ajoute','User.agent_status','User.agent_number','User.consult_chat','User.consult_email','User.consult_phone',
						'User.has_photo','User.has_audio', 'User.langs','User.date_last_activity','User.reviews_avg','User.reviews_nb','User.consults_nb','User.list_pos',
						'IF((agent_status = \'busy\'),
						(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status',
					),
					'conditions' => $conditions,
					'order'      => $orderBy,
					'recursive'  => 1,
					'joins' => array(
						array(
							'table' => 'user_present_lang',
							'alias' => 'UserPresentLang',
							'type' => 'left',
							'conditions' => array(
								'UserPresentLang.user_id = CategoryUser.user_id',
								'UserPresentLang.lang_id = '.$id_lang
							)
						)
					)
				);

			    $params = array_merge($params, $parms);
				
				if($is_filter){
					$countAgents = $this->Category->CategoryUser->find('count',$params);
				}else{
					if (($countAgents = Cache::read('category_filter_count','request_long')) === false) {
						$countAgents = $this->Category->CategoryUser->find('count',$params);
						Cache::write('category_filter_count', $countAgents,'request_long');
					}
				}
				
				$conditions_busy = $conditions;
				$conditions_busy['User.agent_status'] = 'busy' ;
				$params0 = array(
					'fields'     => array('UserPresentLang.*','User.id','User.pseudo','User.date_add','User.flag_new','User.date_new','User.nb_consult_ajoute','User.agent_status','User.agent_number','User.consult_chat','User.consult_email','User.consult_phone',
						'User.has_photo','User.has_audio', 'User.langs','User.date_last_activity','User.reviews_avg','User.reviews_nb','User.consults_nb','User.list_pos',
						'IF((agent_status = \'busy\'),
						(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status',
					),
					'conditions' => $conditions_busy,
					'order'      => $orderBy,
					'recursive'  => 1,
					'joins' => array(
						array(
							'table' => 'user_present_lang',
							'alias' => 'UserPresentLang',
							'type' => 'left',
							'conditions' => array(
								'UserPresentLang.user_id = CategoryUser.user_id',
								'UserPresentLang.lang_id = '.$id_lang
							)
						)
					)
				);
					
			$params0 = array_merge($params0, $parms);
			$params0 = array_merge($params0, array('limit' => $limitAgents, 'offset' => ($page-1)*$limitAgents));
				
			if($is_filter){
				$rows_busy = $this->Category->CategoryUser->find("all", $params0);
			}else{
		    	if (($rows_busy = Cache::read('category_filter2','request_short')) === false) {
					$rows_busy = $this->Category->CategoryUser->find("all", $params0);
					Cache::write('category_filter2', $rows_busy,'request_short');
				}
			}
			$params1 = array(
					'fields'     => array('UserPresentLang.*','User.id','User.pseudo','User.date_add','User.flag_new','User.date_new','User.nb_consult_ajoute','User.agent_status','User.agent_number','User.consult_chat','User.consult_email','User.consult_phone',
						'User.has_photo','User.has_audio', 'User.langs','User.date_last_activity','User.reviews_avg','User.reviews_nb','User.consults_nb','User.list_pos',
						'IF((agent_status = \'busy\'),
						(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status',
					),
					'conditions' => $conditions,
					'order'      => $orderBy,
					'recursive'  => 1,
					'joins' => array(
						array(
							'table' => 'user_present_lang',
							'alias' => 'UserPresentLang',
							'type' => 'left',
							'conditions' => array(
								'UserPresentLang.user_id = CategoryUser.user_id',
								'UserPresentLang.lang_id = '.$id_lang
							)
						)
					)
				);

				$params1 = array_merge($params1, $parms);
				
				$nb_limit = $limitAgents;
				$nb_offset = ($page-1)*$limitAgents;
				
				
				$params1 = array_merge($params1, array('limit' => $nb_limit  , 'offset' => $nb_offset));
				if($is_filter){
					$rows = $this->Category->CategoryUser->find("all", $params1);
				}else{
					if (($rows = Cache::read('category_filter','request_short')) === false) {
					$rows = $this->Category->CategoryUser->find("all", $params1);
					Cache::write('category_filter', $rows,'request_short');
					}
				}
				if(count($rows_busy) && count($rows_busy) > 0 && count($rows_busy) < 14  ){//nb agent max pour forcer classement
					$n_agent_reclasser = 2;
					$index_agent_reclasser = 0;
					foreach($rows as $kk => $voyant){
						if($voyant['User']["agent_status"] == "busy"){
							foreach($rows_busy as $k => $voyant_busy){
								if($voyant['User']["id"] == $voyant_busy['User']["id"]){
									if($index_agent_reclasser >= $n_agent_reclasser){
										unset($rows_busy[$k]);
									}else{
										unset($rows[$kk]);
									}
									
								}
							}
							$index_agent_reclasser ++;
							
						}
						
					}
					/*foreach($rows as $kk => $voyant){
						if($voyant['User']["agent_status"] == "busy"){
							unset($rows[$kk]);
						}
					}*/
					
					
					$listing = array();
					
					//reclass num index array
					$rows = array_values(array_filter($rows));
					$rows_busy = array_values(array_filter($rows_busy));
					
					/*$k_min = 0;
					for($key=0;$key<=count($rows);$key++){
						if($key < 2){
							if(isset($rows[$key])){
								$listing[$k_min] = $rows[$key];
								$k_min ++;
							}
								
							if(isset($rows_busy[$key])){
								$listing[$k_min] = $rows_busy[$key];
								$k_min ++;
							}
							
						}else{
							if(isset($rows[$key])){
								//var_dump($key);
								//var_dump($rows[$key]['User']['list_pos']);
								$kindex = $rows[$key]['User']['list_pos'];// + $key;
								//if($kindex <= 2)$kindex += 2;
								//var_dump($kindex);exit;
								$listing[$kindex] = $rows[$key];
							}
							if(isset($rows_busy[$key])){
								$kindex = $rows_busy[$key]['User']['list_pos'];// + $key;
								//if($kindex <= 4)$kindex += 4;
								$listing[$kindex] = $rows_busy[$key];
							}
						}
						
					}
					ksort($listing);*/
					
					for($key=0;$key<=count($rows);$key++){
						if(isset($rows[$key]))
						array_push($listing,$rows[$key]);
						if(isset($rows_busy[$key]))
						array_push($listing,$rows_busy[$key]);
						
					}

					$rows = $listing;
				}
				
			
			}else{
				
			
				$params = array(
					'fields'     => array('UserPresentLang.*','User.id','User.pseudo','User.date_add','User.flag_new','User.date_new','User.nb_consult_ajoute','User.agent_status','User.agent_number','User.consult_chat','User.consult_email','User.consult_phone',
						'User.has_photo','User.has_audio', 'User.langs','User.date_last_activity','User.reviews_avg','User.reviews_nb','User.consults_nb',
						'IF((agent_status = \'busy\'),
						(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1),0) AS second_from_last_status',
					),
					'conditions' => $conditions,
					'order'      => $orderBy,
					'recursive'  => 1,
					'joins' => array(
						array(
							'table' => 'user_present_lang',
							'alias' => 'UserPresentLang',
							'type' => 'left',
							'conditions' => array(
								'UserPresentLang.user_id = CategoryUser.user_id',
								'UserPresentLang.lang_id = '.$id_lang
							)
						)
					)
				);
				
			$params = array_merge($params, $parms);
				
				if (!isset($_POST['term']) || empty($_POST['term']) ){
					//$page = 1;
					$cond_status = array('User.agent_status'=> 'available');
					$params["conditions"] = array_merge($params["conditions"], $cond_status);
				}

			//On récupère le nombre d'agents par rapport aux conditions
			if($is_filter){
				$countAgents = $this->Category->CategoryUser->find('count',$params);
			}else{	
				if (($countAgents = Cache::read('category_count','request_long')) === false) {
					$countAgents = $this->Category->CategoryUser->find('count',$params);
					Cache::write('category_count', $countAgents,'request_long');
				}	
			}
				
			if($countAgents == 0){
				$params["conditions"]['OR']= '';
				if($is_filter){
					$countAgents = $this->Category->CategoryUser->find('count',$params);
				}else{	
					if (($countAgents = Cache::read('category_count2','request_short')) === false) {
						$countAgents = $this->Category->CategoryUser->find('count',$params);
						Cache::write('category_count2', $countAgents,'request_short');
					}	
				}
			}
				
				
				$params = array_merge($params, array('limit' => $limitAgents, 'offset' => ($page-1)*$limitAgents));
				if($is_filter){
					$rows = $this->Category->CategoryUser->find("all", $params);
				}else{
					if (($rows = Cache::read('request_category','request_short')) === false) {
						$rows = $this->Category->CategoryUser->find("all", $params);
						Cache::write('request_category', $rows,'request_short');
					}
				}

			}

		}else{
			$countAgents = 0;
			$rows = false;
		}
       // $log = $this->Category->CategoryUser->getDataSource()->getLog(false, false);
		
	   $this->site_vars['NbAgents'] = $countAgents;
		
	   $this->loadModel('CategoryUser');
	   foreach($rows as &$rowagent){
		   
		   $categoryLangs = $this->CategoryUser->find('all',array(
				'fields' => array('CategoryLang.category_id', 'CategoryLang.name', 'CategoryLang.link_rewrite'),
				'conditions' => array('CategoryUser.user_id' => $rowagent['User']['id']),
				'joins' => array(
					array(
						'table' => 'category_langs',
						'alias' => 'CategoryLang',
						'type'  => 'left',
						'conditions' => array(
							'CategoryLang.category_id = CategoryUser.category_id',
							'CategoryLang.lang_id = '.$id_lang
						)
					)
				),
				'recursive' => -1
			));

			foreach ($categoryLangs AS $k => $v){
				$categoryLangs[$k]['CategoryLang']['link'] = $this->getCategoryLink($v['CategoryLang']['link_rewrite']);
			}
		   $rowagent['Categories']  = $categoryLangs;
	   }
		
	   
        $arrayReturn = array(
            'rows' => $rows,
            'countAgent' => $countAgents,
            'mediaChecked' => (isset($filters['mediaChecked'])?$filters['mediaChecked']:array())
        );
		
        return $arrayReturn;
    }

    //---------------------------------------------------------------------------------------------------------------ADMIN------------------------------------------------------------------------------------------------------

    public function admin_create($post = true)
    {
        if ($this->request->is('post') && $post) {
            $requestData = $this->request->data;

            /*$name = array(
                'meta_keywords' => 'mots-clés',
                'meta_description' => 'description'
            );

            if(!$this->checkMeta($requestData['CategoryLang'][0], $name)){
                $this->admin_create(false);
                return;
            }*/

            //Modification des url pour les images
            $this->request->data['CategoryLang'][0]['description'] = Tools::clearUrlImage($this->request->data['CategoryLang'][0]['description']);
            if($this->request->data['CategoryLang'][0]['description'] === false){
                $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                $this->admin_create(false);
                return;
            }

            $this->Category->create();
            $this->Category->saveAssociated($this->request->data);

            //On regénère le cache
            $this->regenCache();

            $this->Session->setFlash(__('La catégorie a bien été enregistrée.'),'flash_success');
            $this->redirect(array('controller' => 'category', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        $this->set('lang_options', $this->Lang->find('list'));

        /* Liste des catégories */
        $tmp_categories = $this->Category->CategoryLang->find('all', array('recursive'  => 1));

        //On récupère les id des catégories des agents
        $countAgentParCat = $this->Category->CategoryUser->find('all',array('fields' => array('category_id'), 'recursive' => -1));
        //On ne garde que les id des catégories (tableau sans dimension)
        $countAgentParCat = Set::extract('/CategoryUser/category_id', $countAgentParCat);
        //On compte le nombre d'agent par catégorie
        $countAgentParCat = array_count_values($countAgentParCat);

        $categories = array();

        foreach ($tmp_categories AS $cat){

            //Le dernier élément enregistrer
            $categorieTransit = end($categories);

            //S'il y a un élément d'enregistrer
            if($categorieTransit != false){
                if($categorieTransit['category_id'] == $cat['Category']['id']){
                    $keys = array_keys($categories);
                    $lastKey = end($keys);
                    $categories[$lastKey]['lang_name'].= ', '.$cat['Lang']['name'];
                    continue;
                }
            }

            $categories[] = array(
                'category_id'   =>  $cat['CategoryLang']['category_id'],
                'name'          =>  $cat['CategoryLang']['name'],
                'etat'          =>  ($cat['Category']['active']
                                        ?'<span class="badge badge-success">'.__('Active').'</span>'
                                        :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                    ),
                'lang_name'     =>  $cat['Lang']['name'],
                'agents'        =>  (isset($countAgentParCat[$cat['Category']['id']])?$countAgentParCat[$cat['Category']['id']]:0),
                'date_add'      =>  CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$cat['Category']['date_add']), '%d %B %Y'),
                'active'        =>  $cat['Category']['active']
            );
        }
        $this->set(compact('categories', 'langs'));
    }

    public function admin_delete($id){
        $this->Category->id = $id;
        if($this->Category->saveField('active', 0)){
            //On régénère le cache
            $this->regenCache();
            $this->Session->setFlash(__('La catégorie est désactivée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur dans la désactivation de la catégorie'),'flash_warning');

        $this->redirect(array('controller' => 'category', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_add($id){
        $this->Category->id = $id;
        if($this->Category->saveField('active', 1)){
            //On régénère le cache
            $this->regenCache();
            $this->Session->setFlash(__('La catégorie est activée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur dans l\'activation de la catégorie'),'flash_warning');

        $this->redirect(array('controller' => 'category', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_edit($id){
        if($this->request->is('post')){

            $this->request->data['CategoryLang'] = Tools::checkFormField($this->request->data['CategoryLang'],
                array('lang_id', 'category_id', 'name', 'link_rewrite','cat_rewrite',  'meta_title2', 'meta_keywords2', 'meta_description2', 'description'),
                array('lang_id', 'category_id', 'name')
            );//'meta_title', 'meta_keywords', 'meta_description',

            if($this->request->data['CategoryLang'] === false){
                $this->Session->setFlash(__('Le formulaire est incomplet.'), 'flash_warning');
                $this->redirect(array('controller' => 'category', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }
			//je recup current url
			$this->loadModel('CategoryLang');
			$url = $this->CategoryLang->find('first',array(
							'conditions' => array('category_id' => $this->request->data['CategoryLang']['category_id'], 'lang_id' => $this->request->data['CategoryLang']['lang_id']),
							'recursive' => -1,
						));
			if($url['CategoryLang']['link_rewrite'] != $this->request->data['CategoryLang']['link_rewrite']){
				$this->loadModel('Redirect');
				$this->loadModel('Lang');
				$this->loadModel('Domain');
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$langue = $this->Lang->find('first',array(
							'conditions' => array('id_lang' => $this->request->data['CategoryLang']['lang_id']),
							'recursive' => -1,
						));
				$domaine = $this->Domain->find('first',array(
							'conditions' => array('default_lang_id' => $this->request->data['CategoryLang']['lang_id'], 'active' => 1),
							'recursive' => -1,
						));
				
				/*$url = Router::url(
                   array(
                       'controller'      => 'category',
                       'action'          => 'display',
                       'language'        => $langue['Lang']['language_code'],
                       'link_rewrite'    => $this->request->data['CategoryLang']['link_rewrite'],
                       'id'              => $this->request->data['CategoryLang']['category_id']
                   ), true
                );*/
				
				$redirectData = array();
				$redirectData['Redirect'] = array();
				$redirectData['Redirect']['type'] = "301";
				$redirectData['Redirect']['domain_id'] = $domaine['Domain']['id'];
				$redirectData['Redirect']['old'] = '/'.$langue['Lang']['language_code'].'/'.$url['CategoryLang']['link_rewrite']."-".$url['CategoryLang']['category_id'];
				$redirectData['Redirect']['new'] = $protocol.$domaine['Domain']['domain'].'/'.$langue['Lang']['language_code'].'/'.$this->request->data['CategoryLang']['link_rewrite'].'-'.$this->request->data['CategoryLang']['category_id'];
				$this->Redirect->create();
                $this->Redirect->save($redirectData);
			}
			
            //On supprime la configuration actuelle pour la langue en question
            $this->Category->CategoryLang->deleteAll(array(
                'CategoryLang.category_id' => $this->request->data['CategoryLang']['category_id'],
                'CategoryLang.lang_id' => $this->request->data['CategoryLang']['lang_id']
            ),false);

            //Modification des url pour les images
            $this->request->data['CategoryLang']['description'] = Tools::clearUrlImage($this->request->data['CategoryLang']['description']);
            if($this->request->data['CategoryLang']['description'] === false){
                $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                $this->redirect(array('controller' => 'category', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //On save la configuration
            $this->Category->CategoryLang->save($this->request->data);
			
            //L'etat de la catégorie
            $this->Category->id = $id;
            $this->Category->saveField('active', $this->request->data['Category']['active']);
			
            //On régénère le cache
            $this->regenCache();
            $this->Session->setFlash(__('La catégorie a été modifiée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
            $this->redirect(array('controller' => 'category', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //On récupère toutes les infos de la catégorie
        $categories = $this->Category->find('all',array(
            'conditions' => array('Category.id' => $id),
            'recursive' => 1
        ));

        //Un tableau qui contient les données pour chaque langue renseigné
        foreach($categories[0]['CategoryLang'] as $catLang){
            $langDatas[$catLang['lang_id']] = $catLang;
        }

        //Etat de la catégorie
        $activeCat = $categories[0]['Category']['active'];
        //Nom catégorie
        $nameCat = $categories[0]['CategoryLang'][0]['name'];

        //L'état de la catégorie
        $langDatas['Category']['active'] = $categories[0]['Category']['active'];

        //Variable qui stocke l'id de la categorie, pour un accès plus rapide
        $idCat = $categories[0]['Category']['id'];

        $this->set(compact('langDatas', 'langs', 'idCat', 'activeCat','nameCat'));
    }

    public function admin_list()
    {
        $this->admin_create();

        $this->render('admin_create');
    }

    private function regenCache(){
        //On récupère les langues du site
        $this->loadModel('Lang');
        $langs = $this->Lang->find('list', array(
            'fields'        => array('Lang.language_code'),
            'conditions'    => array('Lang.active' => 1),
            'recursive'     => -1
        ));

        //On détruit le cache de chaque langue
        foreach($langs as $code){
            Cache::delete('categories_nav_'.$code, 'layout_element');
            Cache::delete('footer-navigation-'.$code, Configure::read('nomCacheNavigation'));
        }
    }
}

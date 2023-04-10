<?php
App::uses('AppController', 'Controller');
App::uses('ExtranetController', 'Controller');
App::uses('AlertsController', 'Controller');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('File', 'Utility');
App::uses('CakeTime', 'Utility');
App::import('Vendor', 'Noox/Api');

class AgentsController extends ExtranetController {
    protected $myRole = 'agent';

    //On charge le model User pour tout le controller
    public $uses = array('User');
    public $components = array('Paginator');
    public $helpers = array('Paginator', 'Time', 'Language', 'Form');

    
 public function coucou()
    {
     
     }
    
    public function beforeRender()
    {
	
        //$this->declareCssLink('/theme/default/css/agents.css');
        parent::beforeRender();

        //On envoie le status de l'agent sur chaque action non admin
        if(!isset($this->params['admin'])){
            $agentStatus = $this->User->field('agent_status',array('id' => $this->Auth->user('id'), 'role' => 'agent'));
            if($agentStatus !== false)
                $this->set(array('agentStatus' => $agentStatus));
        }
	
    }

    public function beforeFilter() {

        parent::beforeFilter();
	
       $this->Auth->deny();

        $this->Auth->allow('display', 'modalPresentation', 'appointment', 'abus', 'msg_private','mails_relance_show', 'consult_history', 'consult_reviews', 'mails_relance_filtre', 'closeMessageRelance', 'save_relance_date', 'cancel_relance_date', 'checkvucall', 'checkvuchat','checkvuemail','alertlostcall','alertlostchat','updatemodeconsult','appointmentrdv','appointments',
		'profilremove','survey','survey_agent','OrderStripeValidGroup','admin_vatObservation','admin_saveAgentFactured',
		'updateselectcountries','updateselectstatus','visio',
		'mod_consult', 'tips');

        $user = $this->Auth->user();
	/*
        if (!empty($user) && $user['role'] === 'admin' && strpos($this->params['action'], 'admin') === 0)
            return true;
        if (!empty($user) && $user['role'] !== $this->myRole && !in_array($this->params['action'], array('display', 'modalPresentation', 'appointment', 'abus', 'msg_private','appointmentrdv','survey','survey_agent')))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	*/
    }

    public function index(){
        $customer = $this->User->find('first', array(
            'conditions'    => array('User.id' => $this->Auth->user('id')),
            'recursive'     => -1
        ));

        //Les univers de l'expert
        $this->loadModel('CategoryUser');
        $univers = $this->CategoryUser->find('all',array(
            'fields' => array('CategoryLang.name'),
            'conditions' => array('CategoryUser.user_id' => $this->Auth->user('id')),
            'joins' => array(
                array(
                    'table' => 'category_langs',
                    'alias' => 'CategoryLang',
                    'type'  => 'left',
                    'conditions' => array(
                        'CategoryLang.category_id = CategoryUser.category_id',
                        'CategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1
        ));

        /* On ajoute le pays */
        $this->loadModel('UserCountry');
        $customer['country'] = $this->UserCountry->UserCountryLang->find("all", array(
            'fields'     => 'UserCountries.id,UserCountryLang.name',
            'conditions' => array(
                'UserCountries.active'      =>  1,
                'UserCountries.id'            =>  $customer['User']['country_id'],
                'UserCountryLang.lang_id'   =>  $this->Session->read('Config.id_lang')
            )
        ));

        //La dernière communication
        $this->loadModel('UserCreditHistory');
        $lastCom = $this->UserCreditHistory->find('first', array(
            'fields'        => array('UserCreditHistory.*', 'User.firstname'),
            'conditions'    => array('UserCreditHistory.agent_id' => $customer['User']['id']),
            'order'         => 'UserCreditHistory.user_credit_history desc',
            'recursive'     => 1
        ));

        $this->set(compact('customer', 'lastCom', 'univers'));
    }

    public function display($agent_number, $link_rewrite, $tab = 'profil')
    {
        $this->loadModel('User');
        $this->loadModel('UserCreditHistory');
	$this->loadModel('UserStateHistory');
        $this->loadModel('UserCreditLastHistory');
        $this->loadModel('Review');
        $this->loadModel('CategoryLang');
        $this->loadModel('CategoryUser');
        $this->loadModel('Planning');
        $this->loadModel('CustomerAppointment');
        $this->loadModel('DomainLang');
        $this->loadModel('Lang');
	$this->loadModel('Message');
	
	$this->layout = 'black_blue_root';

        //On désactive le model Planning
        $this->User->unbindModel(array('hasMany' => array('Planning','UserCreditHistory')));
		$id_lang = $this->Session->read('Config.id_lang');
		if($id_lang == 8 || $id_lang == 10 || $id_lang == 11 || $id_lang == 12 )$id_lang = 1;
        //Les infos de l'agent, sa présentation pour la langue actuelle, le nombre de consultation et sa note
		$dateNow = date('Y-m-d H:i:s');

        $rows = $this->User->find('first', array(
            'fields' => array('User.*', 'UserPresentLang.*', 'CountryLangPhone.*',
				'(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1) AS second_from_last_status'
            ),
            'conditions' => array(
                'User.agent_number'      => $agent_number,
                'User.role'              => 'agent',
                'User.active'            => 1,
                'User.deleted'           => 0
            ),
            'joins' => array(
                array(
                    'table' => 'user_present_lang',
                    'alias' => 'UserPresentLang',
                    'type'  => 'left',
                    'conditions' => array(
                        'UserPresentLang.user_id = User.id',
                        'UserPresentLang.lang_id = '.$id_lang
                    )
                ),
                array(
                    'table' => 'country_lang_phone',
                    'alias' => 'CountryLangPhone',
                    'type'  => 'left',
                    'conditions' => array(
                        'CountryLangPhone.country_id = '.$this->Session->read('Config.id_country'),
                        'CountryLangPhone.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1
        ));

		//save agent view
		if($this->Auth->user('id') && $this->Auth->user('role') == 'client'){
			$this->loadModel('AgentView');

			$views = $this->AgentView->find('first',array(
            'conditions' => array('AgentView.user_id' => $this->Auth->user('id'), 'AgentView.agent_id' => $rows['User']['id'], 'AgentView.date_view > ' => date('Y-m-d H:').'00:00'),
            'recursive' => -1
			));
			if(!$views && $rows['User']['id']){
				$viewData = array();
				$viewData['AgentView'] = array();
				$viewData['AgentView']['user_id'] = $this->Auth->user('id');
				$viewData['AgentView']['agent_id'] = $rows['User']['id'];
				$viewData['AgentView']['domain_id'] = $this->Session->read('Config.id_country');
				$viewData['AgentView']['date_view'] = date('Y-m-d H:i:s');

				$this->AgentView->create();
				$this->AgentView->save($viewData);
			}
		}else{
			if($this->Auth->user('role') != 'agent'){
				$this->loadModel('AgentView');
				$dt = new DateTime(date('Y-m-d H:i:s'));
				$dt->modify('- 1 minutes');
				$delai = $dt->format('Y-m-d H:i:s');
				$views = $this->AgentView->find('first',array(
				'conditions' => array('AgentView.user_id' => 0, 'AgentView.agent_id' => $rows['User']['id'], 'AgentView.date_view > ' => $delai),
				'recursive' => -1
				));
				if(!$views && $rows['User']['id']){
					$viewData = array();
					$viewData['AgentView'] = array();
					$viewData['AgentView']['user_id'] = '0';
					$viewData['AgentView']['agent_id'] = $rows['User']['id'];
					$viewData['AgentView']['domain_id'] = $this->Session->read('Config.id_country');
					$viewData['AgentView']['date_view'] = date('Y-m-d H:i:s');

					$this->AgentView->create();
					$this->AgentView->save($viewData);
				}
			}
		}

        //Les univers de l'agent
        $categoryLangs = $this->CategoryUser->find('all',array(
            'fields' => array('CategoryLang.category_id', 'CategoryLang.name', 'CategoryLang.link_rewrite'),
            'conditions' => array('CategoryUser.user_id' => $rows['User']['id']),
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

        //Si l'agent n'a pas été trouvé
        if (empty($rows)){
            $this->redirect('/');
        }

        //Les langues de l'agent
        /*
        $langs = explode(',', $rows['User']['langs']);
        $langsAgent = $this->Lang->find('list', array(
            'fields'        => array('Lang.language_code', 'Lang.name'),
            'conditions'    => array(
                'Lang.id_lang' => $langs,
                'Lang.active' => 1
            ),
            'recursive'     => -1,

        ));*/


        //Les langues de l'agent
        $langs = explode(',', $rows['User']['langs']);
        $lrows = $this->Lang->find('all', array(
            // 'fields'        => array('Lang.language_code', 'Lang.name'),
            'conditions'    => array(
                'Lang.id_lang' => $langs,
                'Lang.active' => 1
            ),
            'recursive'     => 1,

        ));
        /* traduction liste langues dans la langue courante */
        $langsAgent = array();
        $session_lang_id = $this->Session->read('Config.id_lang');
        foreach ($lrows AS $lrow){
            foreach ($lrow['LangLang'] AS $l){
                $langsAgent[$lrow['Lang']['language_code']] = false;
                if ((int)$l['in_lang_id'] == (int)$session_lang_id){
                    $langsAgent[$lrow['Lang']['language_code']] = $l['name'];
                    break;
                }
            }
            if ($langsAgent[$lrow['Lang']['language_code']] === false)
                $langsAgent[$lrow['Lang']['language_code']] = $lrow['Lang']['name'];
        }

	    $this->loadModel('Currency');
	    $currencies= $this->Currency->find('all');
	    $this->set('currencies', $currencies);

        //Une présentation audio ??
        $audio = $this->mediaAgentExist($rows['User']['agent_number'],'Audio');

        //Une présentation video ??
        $video = $this->mediaAgentExist($rows['User']['agent_number'],'Video');

        //Agent précédent et suivant
        $voisins = $this->User->voisins($rows['User']['id']);

        //Si l'onglet avis -----------------------------------------------------------------------------
        if($tab == 'reviews'){
            //Les derniers avis sur l'expert
            $reviews = $this->Review->find('all',array(
                'fields' => array('User.firstname', 'content', 'rate', 'utile', 'date_add'),
                'conditions' => array('agent_id' => $rows['User']['id'], 'Review.status' => 1),
                'order' => 'Review.date_add desc',
                'limit' => Configure::read('Site.limitReviewAgent')
            ));
            $this->set(compact('reviews'));
        //Si onglet profil -------------------------------------------------------------------------------
        }elseif($tab == 'profil'){

			 //Les derniers avis sur l'expert
			$ratingCount = $this->Review->find('count',array(
                'conditions' => array('agent_id' => $rows['User']['id'], 'Review.status' => 1, 'Review.parent_id' => NULL),
                'limit' => -1
            ));
            $reviews = $this->Review->find('all',array(
                'fields' => array('User.firstname', 'content', 'rate','review_id', 'utile','date_add'),
                'conditions' => array('agent_id' => $rows['User']['id'], 'Review.status' => 1, 'Review.parent_id' => NULL),
                'order' => 'Review.date_add desc',
                'limit' => Configure::read('Site.limitReviewAgent')
            ));

			$review = array();

			foreach($reviews as $r){

				$response = $this->Review->find('first',array(
					'conditions' => array(
						'Review.parent_id' => $r['Review']['review_id'],
					     'Review.status' => 1
					),
					'recursive' => -1
				));
				if($response){
					$r['Review']['reponse'] = $response['Review'];
				}

				$review[] = $r;
			}
			$reviews = $review;
			$moy = $rows['User']['reviews_avg'];
			$ratingValue = number_format($moy/20,2);
            $debutExplode = $this->explodeDate(date('d-m-Y'));
            $finExplode = $this->explodeDate(date('d-m-Y', strtotime('+'.(Configure::read('Site.limitPlanning')-1).' days')));

            //On génère l'intervalle de date
            for($i=0; $i<Configure::read('Site.limitPlanning'); $i++){
                $intervalle[$i]['date'] = CakeTime::format(strtotime('+'.$i.' days'), '%d-%m-%Y');
                $intervalle[$i]['label'] = CakeTime::format(strtotime('+'.$i.' days'), '%d/%m');
                $intervalle[$i]['day'] = ucfirst(CakeTime::format(strtotime('+'.$i.' days'), '%A'));
                $intervalle[$i]['explode'] = $this->explodeDate($intervalle[$i]['date']);
            }

            //Le planning de l'agent
            $planning = $this->Planning->agent_planning($rows['User']['id'],$debutExplode,$finExplode);
            //Les rdv de l'agent
            $appointments = $this->CustomerAppointment->appointments($rows['User']['id'], $debutExplode, $finExplode);
            //Les autres agents consultés
            $associatedAgent = $this->UserCreditLastHistory->associatedAgent($rows['User']['id']);
            //Leurs photos
            foreach($associatedAgent as $key => $agent){
                $file = $this->mediaAgentExist($agent['Agent']['agent_number'],'Image');
                if($file !== false)
                    $associatedAgent[$key]['Agent']['photo'] = $file;
                else
                    $associatedAgent[$key]['Agent']['photo'] = Configure::read('Site.defaultImage');
            }
            $this->set(compact('planning', 'appointments', 'associatedAgent', 'intervalle', 'reviews'));
        //Si onglet insérer un avis ----------------------------------------------------------------------------------------
        }elseif($tab == 'add_review'){

        }

		if($this->Auth->loggedIn() && $this->Auth->user('role') === 'client'){
                //dernière communication avec l'agent
                $lastCom = $this->UserCreditLastHistory->find('first', array(
                    'fields'        => array('UserCreditLastHistory.date_start'),
                    'conditions'    => array('UserCreditLastHistory.users_id' => $this->Auth->user('id'), 'UserCreditLastHistory.agent_id' => $rows['User']['id']),
                    'order'         => 'UserCreditLastHistory.user_credit_last_history desc',
                    'recursive'     => -1
                ));

                //S'il y a bien une communication
                if(!empty($lastCom)){
                    if(!Configure::read('Site.unlimitedReview')){
                        //La date du dernier avis pour cet expert
                        $lastReview = $this->Review->find('first', array(
                            'fields'        => array('Review.date_add'),
                            'conditions'    => array('Review.agent_id' => $rows['User']['id'], 'Review.user_id' => $this->Auth->user('id'), 'Review.status !=' => 0),
                            'order'         => 'Review.date_add desc',
                            'recursive'     => -1
                        ));

                        //Si pas d'avis, alors autorisé à déposer un avis
                        if(empty($lastReview))
                            $this->set('postReview', true);
                        else{   //Il faut comparer les dates
                            //Si l'avis est plus ancien que le dernier contact, alors autorisé à déposer un avis
                            if($lastCom['UserCreditLastHistory']['date_start'] > $lastReview['Review']['date_add'])
                                $this->set('postReview', true);
                        }
                    }
                    else
                        $this->set('postReview', true);
                }
            }


		$user_client = $this->Session->read('Auth.User');

		$messages = $this->Message->find('all',array(
                'conditions' => array('Message.to_id' => $rows['User']['id'],'Message.from_id' => $user_client['id'],'Message.private' => 1, 'Message.date_add >' => date('Y-m-d 00:00:00')),
            ));

		$sending_message = count($messages);


		//check favoris
		$is_favorite = 0;
		if($user_client['id']){
            $this->loadModel('Favorite');
            $row = $this->Favorite->find('first', array(
                'conditions'    => array('user_id' => $user_client['id'], 'agent_id' => $rows['User']['id']),
                'recursive'     => -1
            ));

            if(!empty($row)){
                $is_favorite = 1;
            }

		}

        //Les balises métas
		$nCaracAuth = 56;
		$nCarac = strlen($rows['User']['pseudo'].' - - Spiriteo');
		$nCaracLeft = $nCaracAuth - $nCarac;
		$title_category = '';
		$desc_category = '';
		foreach($categoryLangs as $cat){
			$nn = $cat["CategoryLang"]['name'];
			if(strlen($nn.' ')< $nCaracLeft){
				$title_category = $nn.' ';
				$nCaracLeft = $nCaracLeft - strlen($nn.' ');
			}
		}
		switch ($categoryLangs[0]['CategoryLang']['name']) {
			case 'Voyant & Medium':
				$lettre_alpha = strtoupper(substr($rows['User']['pseudo'],0,1));
				$list_1 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
				$list_2 = array('O','P','Q','R','S','T','U','V','W','X','Y','Z');
				if(in_array($lettre_alpha,$list_1))$title_category = 'Agents immediate avec';
				if(in_array($lettre_alpha,$list_2))$title_category = 'Agents qualité avec';
				$desc_category = __('Agents de qualité avec notre médium - Consultation précise et privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Tarologue':
			case 'Cartomancien':
				$title_category = __('Tarologue');
				$desc_category = __('Agents de qualité avec notre tarologue - Consultation précise et privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Astrologue':
				$title_category = __('Astrologue');
				$desc_category = __('Etude complète avec notre astrologue - Consultation précise et privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Numérologue':
				$title_category = __('Numerologue');
				$desc_category = __('Numérologie approfondie avec notre numerologue - Consultation précise et privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Magnétiseur':
				$title_category = __('Magnetiseur guerisseur');
				$desc_category = __('Soin holistique avec notre magnetiseur guerisseur - Consultation privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Coaching':
				$title_category = __('Coaching avec');
				$desc_category = __('Coaching avec - Consultation privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
			case 'Interprétation des rêves':
				$title_category = __('Interprétation');
				$desc_category = __('Interprétation avec - Consultation privée 7/7j sur Spiriteo 1er site de agents sérieuse en ligne');
				break;
		}
		$categorie_une = $categoryLangs[0]['CategoryLang']['name'];

       /* $this->site_vars['meta_title'] = $title_category.' '.$rows['User']['pseudo'].' - Spiriteo';
        if(!isset($this->site_vars['meta_keywords']))
            $this->site_vars['meta_keywords'] = '';
        foreach($categoryLangs as $categoryLang){
            $this->site_vars['meta_keywords'].= $categoryLang['CategoryLang']['name'].', ';
        }
        //On retire les 2 derniers caractères
        $this->site_vars['meta_keywords'] = substr($this->site_vars['meta_keywords'],0,-2);

        $this->site_vars['meta_description'] = str_replace('PSEUDO',$rows['User']['pseudo'],$desc_category);//$rows['UserPresentLang']['texte'];*/

		$domain_id = $this->Session->read('Config.id_domain');

		switch ($domain_id) {
				case 19:
					$this->site_vars['meta_title']       = __('Agents en ligne : ').$rows['User']['pseudo'].__(' | Spiriteo France');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Agents en ligne avec ').$rows['User']['pseudo'].__(': je vous guide avec bienveillance 24/7. Consultez mes excellents avis maintenant !');
					break;
				case 29:
					$this->site_vars['meta_title']       = __('Agents en ligne : ').$rows['User']['pseudo'].__(' | Spiriteo Canada');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Agents en ligne avec ').$rows['User']['pseudo'].__(': je vous guide avec bienveillance 24/7. Consultez mes excellents avis maintenant !');
					break;
				case 22:
					$this->site_vars['meta_title']       = __('Agents en ligne : ').$rows['User']['pseudo'].__(' | Spiriteo Luxembourg');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Agents en ligne avec ').$rows['User']['pseudo'].__(': je vous guide avec bienveillance 24/7. Consultez mes excellents avis maintenant !');
					break;
			   case 13:
					$this->site_vars['meta_title']       = __('Agents en ligne : ').$rows['User']['pseudo'].__(' | Spiriteo Suisse');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Agents en ligne avec ').$rows['User']['pseudo'].__(': je vous guide avec bienveillance 24/7. Consultez mes excellents avis maintenant !');
					break;
			   case 11:
					$this->site_vars['meta_title']       = __('Agents en ligne : ').$rows['User']['pseudo'].__(' | Spiriteo Belgique');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Agents en ligne avec ').$rows['User']['pseudo'].__(': je vous guide avec bienveillance 24/7. Consultez mes excellents avis maintenant !');
					break;
			}



		//noindex tabs
		$this->site_vars['robots'] = '';

		if($tab != 'profil')
			$this->site_vars['robots'] = 'noindex';

		$countConsult = 0;
		if(!empty($rows[0]['countConsult']))$countConsult = $rows[0]['countConsult'];

        $this->set(array('categoryLangs' => $categoryLangs, 'countConsult' => $countConsult + $rows['User']['nb_consult_ajoute'], 'is_favorite' => $is_favorite, 'depuis' => $rows[0]['second_from_last_status'],'reviews_limit' => Configure::read('Site.limitReviewAgent')));
        $this->set(compact('audio', 'video', 'tab', 'voisins', 'langsAgent', 'sending_message','ratingValue','categorie_une'));
        //On unset l'élément 0
        unset($rows[0]);
        $this->set($rows);
    }

    //Liste les RDV clients
    public function appointments(){

        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] !== 'agent')
			$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
            //throw new Exception("Erreur de sécurité !", 1);

		//Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('CustomerAppointment');
		$this->loadModel('User');
		$this->loadModel('Domain');
		$this->loadModel('Lang');
		$this->loadModel('Message');

		if($this->request->is('post')){
            $requestData = $this->request->data;
			$appoint_id = $requestData["agents"]["appoint_id"];
			$appoint_resp = $requestData["agents"]["content"];
			$appoint_choice = $requestData["agents"]["ChoiceRDV"];

			if($appoint_id && $appoint_choice){
				$user_id = $this->CustomerAppointment->field('user_id', array('CustomerAppointment.id' => $appoint_id));
				$user_lang_id = $this->User->field('lang_id', array('User.id' => $user_id));
				$user_email = $this->User->field('email', array('User.id' => $user_id));
				$user_firstname = $this->User->field('firstname', array('User.id' => $user_id));
				$user_domain_id = $this->User->field('domain_id', array('User.id' => $user_id));

				$agent_id = $this->CustomerAppointment->field('agent_id', array('CustomerAppointment.id' => $appoint_id));
				$agent_pseudo = $this->User->field('pseudo', array('User.id' => $agent_id));
				$agent_number = $this->User->field('agent_number', array('User.id' => $agent_id));

				$conditions = array(
						'Domain.id' => $user_domain_id
					);
				$domain = $this->Domain->find('first',array('conditions' => $conditions));
				if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';
				$conditions = array(
						'Lang.id_lang' => $user_lang_id
				);
				$lang = $this->Lang->find('first',array('conditions' => $conditions));

				$url_expert = 'https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/agents-en-ligne/'.strtolower(str_replace(' ','-',$agent_pseudo)).'-'.$agent_number;

				$dateAppoint = $this->CustomerAppointment->field('A', array('CustomerAppointment.id' => $appoint_id)).'-'.$this->CustomerAppointment->field('M', array('CustomerAppointment.id' => $appoint_id)).'-'.
                        $this->CustomerAppointment->field('J', array('CustomerAppointment.id' => $appoint_id)).' '.
                        str_pad($this->CustomerAppointment->field('H', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT).':'.
                        str_pad($this->CustomerAppointment->field('Min', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT).':00';

				//$rdv = CakeTime::format($dateAppoint, '%d %B à %Hh%M');
				$utc_dec = Configure::read('Site.utc_dec');
				$dx = new DateTime($dateAppoint);
				$dateAppoint = $dx->format('Y-m-d H:i:s');
				$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');

				$this->CustomerAppointment->id = $appoint_id;
				switch ($appoint_choice) {
					case 1:
						$this->CustomerAppointment->saveField('valid', 1);
						$this->sendCmsTemplateByMail(354, $user_lang_id, $user_email, array(
							'PARAM_CLIENT' => $user_firstname,
							'PARAM_PSEUDO' => $agent_pseudo,
							'PARAM_RENDEZVOUS' => $rdv,
							'PAGE_EXPERT' => $url_expert

						));
						$this->Session->setFlash(__('Votre réponse a été transmise au client.'),'flash_success');
						break;
					case 2:

						if(!$appoint_resp){
							$this->Session->setFlash(__('Merci de saisir une réponse pour ce choix.'), 'flash_warning');
						}else{

							$etat = 0;
							//on filtre le contenu
							$this->loadModel('FiltreMessage');

							$filtres = $this->FiltreMessage->find("all", array(
									'conditions' => array(
									)
							));
							foreach($filtres as $filtre){
								if(substr_count(strtolower($appoint_resp), strtolower($filtre["FiltreMessage"]["terme"])))
									$etat = 1;
							}

							if(!$etat){
								$this->sendCmsTemplateByMail(355, $user_lang_id, $user_email, array(
									'PARAM_CLIENT' => $user_firstname,
									'PARAM_PSEUDO' => $agent_pseudo,
									'PARAM_RENDEZVOUS' => $rdv,
									'PAGE_EXPERT' => $url_expert,
									'PARAM_REPONSE' => nl2br($appoint_resp)
								));
								//$this->CustomerAppointment->delete($appoint_id, false);
								$this->CustomerAppointment->saveField('valid', -1);
								$this->CustomerAppointment->saveField('txt', $appoint_resp);
						 		$this->Session->setFlash(__('Votre réponse a été transmise au client.'),'flash_success');
							}else{
								$this->CustomerAppointment->saveField('status', 0);
								$this->CustomerAppointment->saveField('txt', $appoint_resp);
								$this->CustomerAppointment->saveField('txt', addslashes($appoint_resp));
								App::import('Controller', 'Extranet');
								$extractrl = new ExtranetController();
								//Les datas pour l'email
								$datasEmail = array(
									'content' => __('Une réponse rdv client requiert check terme interdit.') ,
									'PARAM_URLSITE' => 'https://fr.spiriteo.com'
								);
								//Envoie de l'email
								$extractrl->sendEmail('contact@talkappdev.com',__('Reponse rdv client terme interdit'),'default',$datasEmail);
							}

						}
						break;
					case 3:
						$this->sendCmsTemplateByMail(356, $user_lang_id, $user_email, array(
							'PARAM_CLIENT' => $user_firstname,
							'PARAM_PSEUDO' => $agent_pseudo,
							'PARAM_RENDEZVOUS' => $rdv,
							'PAGE_EXPERT' => $url_expert,
						));
						//$this->CustomerAppointment->delete($appoint_id, false);
						$this->CustomerAppointment->saveField('valid', -1);
						 $this->Session->setFlash(__('Votre réponse a été transmise au client.'),'flash_success');
						break;
				}
			}

			$this->redirect(array('controller' => 'agents', 'action' => 'appointments', 'admin' => false),false);
		}



        //Date d'aujourd'hui explosé
        $dateNow = CakeTime::format(strtotime('-'.(2).' days'), '%d-%m-%Y');//CakeTime::format('now', '%d-%m-%Y');
        $dateNow = Tools::explodeDate($dateNow);
        $dateEnd = CakeTime::format(strtotime('+'.(Configure::read('Site.limitPlanning')).' days'), '%d-%m-%Y');
        $dateEnd = Tools::explodeDate($dateEnd);
        $this->Paginator->settings = array(
            'fields' => array('CustomerAppointment.*', 'User.firstname'),
            'conditions' => $this->CustomerAppointment->getConditions($user['id'], $dateNow, $dateEnd),
            'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = CustomerAppointment.user_id')
                )
            ),
            //'order' => array('CustomerAppointment.A' => 'ASC', 'CustomerAppointment.M' => 'ASC', 'CustomerAppointment.J' => 'ASC', 'CustomerAppointment.H' => 'ASC', 'CustomerAppointment.Min' => 'ASC'),
            'order' => "date_format(CONCAT(A,'-',M,'-',J,' ',H,':',Min,':00'),'%Y-%m-%d %H:%i:%s') ASC",
            'limit' => 15,
            'recursive' => -1
        );

        $appointments = $this->Paginator->paginate($this->CustomerAppointment);
        $appointments = $this->CustomerAppointment->restructureAppointmentV2($appointments);


		  $country_agent = $this->User->field('country_id', array('id' => $user['id']));
		$this->loadModel('UserCountry');

					 $cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_agent),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));
		$timezone = '';
					if($cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
						$timezone = $countryInfo['Country']['timezone'];
					}
		$utc_dec = Configure::read('Site.utc_dec');
		$this->set(array('utc_dec' => $utc_dec));
		$this->set(array('timezone' => $timezone));
        $this->set(compact('appointments'));
    }

    //Permet de prendre RDV avec l'agent
    public function appointment(){


        if($this->request->is('ajax')){
            $requestData = $this->request->data;
            $this->layout = '';
            //Utilisateur non connecté
            if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
                $content = $this->render('/Elements/login_modal');
                $this->set(array('title' => __('Accès client'), 'content' => $content, 'button' => __('Annuler')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('modal' => true, 'content' => $response->body(), 'return' => false));
            }
            //Si agent non identifiable
            if(empty($requestData['agent_number']))
                $this->jsonRender(array('reload' => true, 'return' => false));
            //Si on reçoit une date correcte
            //flag pour la date
            $flag = false;
            for($i=0; $i<Configure::read('Site.limitPlanning'); $i++){
                if(strcmp($requestData['date'], date('d-m-Y', strtotime('+'.$i.' days'))) == 0){
                    $flag = true;
                    break;
                }
            }
            //Les heures et minutes
            if(!is_numeric($requestData['h']) || (int)$requestData['h'] < 0 || (int)$requestData['h'] > 23)
                $flag = false;
            if(!is_numeric($requestData['m']) || !in_array($requestData['m'],array('0','30')))
                $flag = false;
            //Si un des élèments de la date est incorrect (cas anormal, modification par le client en dur)
            if(!$flag)
                $this->jsonRender(array('return' => false));
            //On récupère l'id de l'agent
            $idAgent = $this->User->field('id', array('agent_number' => $requestData['agent_number']));
            //On explose la date
            $dateExplode = $this->explodeDate($requestData['date']);
            //On ajoute l'heure
            $dateExplode['H'] = $requestData['h'];
            $dateExplode['Min'] = $requestData['m'];

			$mois = str_pad($dateExplode['M'], 2, '0', STR_PAD_LEFT);
			$jour = str_pad($dateExplode['J'], 2, '0', STR_PAD_LEFT);
			$heure = str_pad($dateExplode['H'], 2, '0', STR_PAD_LEFT);
			$min = str_pad($dateExplode['Min'], 2, '0', STR_PAD_LEFT);

			$dd = $dateExplode['A'].'-'.$mois.'-'.$jour.' '.$heure.':'.$min;
			if($dd < date('Y-m-d H:i:s')){
				//On génére le contenu de la box
						$this->set(array('isAjax' => 1, 'statut' => 'busy'));
						$response = $this->render('/Elements/agent_box_appointment');
						$this->jsonRender(array('return' => false, 'html' => $response->body()));
			}else{

				//L'agent a un rdv ??
				$this->loadModel('CustomerAppointment');
				$appointment = $this->CustomerAppointment->hasAppointment($idAgent, $dateExplode);
				//Si l'agent a déjà un rdv pour cette date
				if(!empty($appointment)){
					//Si l'utilisateur actuel est à l'origine du rdv
					if(strcmp($this->Auth->user('id'), $appointment['User']['id']) == 0){
						//On supprime le rdv
						$this->CustomerAppointment->deleteAll(array(
							'CustomerAppointment.user_id' => $appointment['User']['id'],
							'CustomerAppointment.agent_id' => $idAgent,
							'CustomerAppointment.A' => $appointment['CustomerAppointment']['A'],
							'CustomerAppointment.M' => $appointment['CustomerAppointment']['M'],
							'CustomerAppointment.J' => $appointment['CustomerAppointment']['J'],
							'CustomerAppointment.H' => $appointment['CustomerAppointment']['H'],
							'CustomerAppointment.Min' => $appointment['CustomerAppointment']['Min']
						),false);

						//Le pseudo du client
						$client = $this->User->field('firstname', array('id' => $this->Auth->user('id')));

						//Le pseudo de l'agent
						$pseudo = $this->User->field('pseudo', array('id' => $idAgent));
						$email_agent = $this->User->field('email', array('id' => $idAgent));

						//envoi email
						$this->loadModel('Lang');
						$this->User->id = $idAgent;
						$user_lang_id = $this->User->field('lang_id');
						$this->Lang->id = $user_lang_id;
						$locale = $this->Lang->field('lc_time');
						if (empty($locale))$locale = 'fr_FR.utf8';
						setlocale(LC_ALL, $locale);

						$dateAppoint = $dateExplode['A'].'-'.$dateExplode['M'].'-'.
								$dateExplode['J'].' '.
								str_pad($dateExplode['H'], 2, '0', STR_PAD_LEFT).':'.
								str_pad($dateExplode['Min'], 2, '0', STR_PAD_LEFT).':00';

						//$rdv = CakeTime::format($dateAppoint, '%d %B à %Hh%M');
						if( $this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)) != $this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id))){
							/*if($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)) != 'Europe/Paris' && $this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id))== 'Europe/Paris' ){
											$userTimezone = new DateTimeZone($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)));
											$gmtTimezone = new DateTimeZone($this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)));
											$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
											$offset = ($userTimezone->getOffset($myDateTime) / 3600 ) * -1;
							}
							if($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)) == 'Europe/Paris' && $this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)) != 'Europe/Paris' ){
											$userTimezone = new DateTimeZone($this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)));
											$gmtTimezone = new DateTimeZone($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)));
											$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
											$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
							}

							if($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)) != 'Europe/Paris' && $this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)) != 'Europe/Paris' ){
											$userTimezone = new DateTimeZone($this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)));
											$gmtTimezone = new DateTimeZone($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)));
											$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
											$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
							}*/
							date_default_timezone_set($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)));
									$d_client = date('YmdH');
									date_default_timezone_set($this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)));
									$d_agent = date('YmdH');
									date_default_timezone_set('UTC');
									$offset = intval($d_agent) - intval($d_client);
									//if($this->CustomerAppointment->field('agent_utc', array('CustomerAppointment.id' => $appoint_id)) == 'America/Chicago') $offset = $offset + 1;
									//if($this->CustomerAppointment->field('user_utc', array('CustomerAppointment.id' => $appoint_id)) == 'America/Chicago') $offset = $offset - 1;


							$utc_dec = $offset;//Configure::read('Site.utc_dec');

							//$utc_dec = Configure::read('Site.utc_dec') * -1;
							$dx = new DateTime($dateAppoint);
							$dx->modify($utc_dec.' hour');
							$dateAppoint = $dx->format('Y-m-d H:i:s');
							$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
						}else{
							$dx = new DateTime($dateAppoint);
							$dateAppoint = $dx->format('Y-m-d H:i:s');
							$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
						}

						$this->sendCmsTemplateByMail(358, $user_lang_id, $email_agent, array(
								'PARAM_PSEUDO'      =>  $pseudo,
								'PARAM_CLIENT'      =>  $client,
								'PARAM_RENDEZVOUS'  =>  $rdv
							));


						//Le jour avec 1er lettre en Maj
						$day = ucfirst(CakeTime::format($requestData['date'], '%A'));
						//On génére le contenu de la box
						$this->set(array('isAjax' => 1, 'pseudo' => $appointment['Agent']['pseudo'], 'day' => $day, 'h' => $appointment['CustomerAppointment']['H'], 'm' => $appointment['CustomerAppointment']['Min']));
						$response = $this->render('/Elements/agent_box_appointment');
						$this->jsonRender(array('return' => true, 'action' => 'delete', 'html' => $response->body()));
					}else{
						//On génére le contenu de la box
						$this->set(array('isAjax' => 1, 'statut' => 'busy'));
						$response = $this->render('/Elements/agent_box_appointment');
						$this->jsonRender(array('return' => false, 'html' => $response->body()));
					}
				}else{

					//Le pseudo du client
					$client = $this->User->field('firstname', array('id' => $this->Auth->user('id')));
					$country_client = $this->User->field('country_id', array('id' => $this->Auth->user('id')));
					$domain_client = $this->User->field('domain_id', array('id' => $this->Auth->user('id')));

					//Le pseudo de l'agent
					$pseudo = $this->User->field('pseudo', array('id' => $idAgent));
					$email_agent = $this->User->field('email', array('id' => $idAgent));
					$country_agent = $this->User->field('country_id', array('id' => $idAgent));
					$domain_agent = $this->User->field('domain_id', array('id' => $idAgent));

					$this->loadModel('UserCountry');

					 $cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_agent),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));

					if($cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo_agent = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
					}else{
						$this->loadModel('Domain');
						$domainInfo = $this->Domain->find('first', array(
							'fields' => array('country_id'),
							'conditions' => array('Domain.id' => $domain_agent),
							'recursive' => -1
						));


					$this->loadModel('Country');
						$countryInfo_agent = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
							'recursive' => -1
						));
					}

					$cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_client),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));

					if($cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo_client = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
					}else{
						$this->loadModel('Domain');
						$domainInfo = $this->Domain->find('first', array(
							'fields' => array('country_id'),
							'conditions' => array('Domain.id' => $domain_client),
							'recursive' => -1
						));


					$this->loadModel('Country');
						$countryInfo_client = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
							'recursive' => -1
						));
					}


					//En sauvegarde la demande de consultation
					$this->CustomerAppointment->create();
					$this->CustomerAppointment->save(array(
						'user_id'   => $this->Auth->user('id'),
						'agent_id'  => $idAgent,
						'user_utc' => $countryInfo_client['Country']['timezone'],
						'agent_utc' => $countryInfo_agent['Country']['timezone'],
						'A'         => $dateExplode['A'],
						'M'         => $dateExplode['M'],
						'J'         => $dateExplode['J'],
						'JS'        => CakeTime::format($requestData['date'], '%u'),
						'H'         => $dateExplode['H'],
						'Min'       => $dateExplode['Min'],
					));


					//envoi email
					$this->loadModel('Lang');
					$this->User->id = $idAgent;
					$user_lang_id = $this->User->field('lang_id');
					$this->Lang->id = $user_lang_id;
					$locale = $this->Lang->field('lc_time');
					if (empty($locale))$locale = 'fr_FR.utf8';
					setlocale(LC_ALL, $locale);

					$dateAppoint = $dateExplode['A'].'-'.$dateExplode['M'].'-'.
							$dateExplode['J'].' '.
							str_pad($dateExplode['H'], 2, '0', STR_PAD_LEFT).':'.
							str_pad($dateExplode['Min'], 2, '0', STR_PAD_LEFT).':00';

					//calcul date rdv en UTC
					if($countryInfo_client['Country']['timezone'] != $countryInfo_agent['Country']['timezone']){


						/*if($countryInfo_client['Country']['timezone'] != 'Europe/Paris' && $countryInfo_agent['Country']['timezone'] == 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo_agent['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 ) * -1;
						}
						if($countryInfo_client['Country']['timezone'] == 'Europe/Paris' && $countryInfo_agent['Country']['timezone'] != 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo_agent['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
						}

						if($countryInfo_client['Country']['timezone'] != 'Europe/Paris' && $countryInfo_agent['Country']['timezone'] != 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo_agent['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
						}*/
						date_default_timezone_set($countryInfo_client['Country']['timezone']);
									$d_client = date('YmdH');
									date_default_timezone_set($countryInfo_agent['Country']['timezone']);
									$d_agent = date('YmdH');
									date_default_timezone_set('UTC');
									$offset = intval($d_agent) - intval($d_client);
									//if($countryInfo_agent['Country']['timezone'] == 'America/Chicago') $offset = $offset + 1;
									//if($countryInfo_client['Country']['timezone'] == 'America/Chicago') $offset = $offset - 1;


						$utc_dec = $offset;//Configure::read('Site.utc_dec');
						$dx = new DateTime($dateAppoint);
						$dx->modify($utc_dec.' hour');
						$dateAppoint = $dx->format('Y-m-d H:i:s');


						$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
					}else{
						$dx = new DateTime($dateAppoint);
						$dateAppoint = $dx->format('Y-m-d H:i:s');
						$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
					}
					$this->sendCmsTemplateByMail(152, $user_lang_id, $email_agent, array(
							'PARAM_PSEUDO'      =>  $pseudo,
							'PARAM_CLIENT'      =>  $client,
							'PARAM_RENDEZVOUS'  =>  $rdv
						));

					//Le jour avec 1er lettre en Maj
					$day = ucfirst(CakeTime::format($requestData['date'], '%A'));
					//On génére le contenu de la box
					$this->set(array('isAjax' => 1, 'statut' => 'cancel', 'pseudo' => $pseudo, 'day' => $day, 'h' => $dateExplode['H'], 'm' => $dateExplode['Min']));
					$response = $this->render('/Elements/agent_box_appointment');
					$this->jsonRender(array('return' => true, 'action' => 'add', 'html' => $response->body()));
				}
			}
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false),false);
    }

    public function abus(){
        if($this->request->is('post')){
            $requestData = $this->request->data;
            //On vérifie les champs du formulaire
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'],array('agent_id', 'consultation', 'msg'), array('agent_id', 'consultation', 'msg'), array('consultation'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

            //Les infos de l'agent
            $agent = $this->User->find('first', array(
                'fields' => array('User.id', 'User.agent_number', 'User.pseudo'),
                'conditions' => array('User.id' => $requestData['Agent']['agent_id'], 'User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1),
                'recursive' => -1
            ));
            //si pas d'agent
            if(empty($agent)){
                $this->Session->setFlash(__('L\'agent est introuvable.'), 'flash_error');
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

            //Si l'utilisateur est connecté et si c'est un client
            if($this->Auth->loggedIn() && $this->Auth->user('role') === 'client'){

				$this->loadModel('Support');
				$this->loadModel('SupportMessage');

				$message = 'Dénoncer un abus<br /><br />'.
				$message .= 'Expert : '.$agent['User']['pseudo'].' ('.$agent['User']['agent_number'].')';
				$message .='
				';
				$message .='Date consultation : '.$requestData['Agent']['consultation']['day'].'/'.$requestData['Agent']['consultation']['month'].'/'.$requestData['Agent']['consultation']['year'];
				$message .='
				';
				$message .=$requestData['Agent']['msg'];
				/*
                $this->loadModel('Message');
                //Save message
                $this->Message->create();
                if($this->Message->save(array(
                    'from_id'   => $this->Auth->user('id'),
                    'to_id'     => Configure::read('Admin.id'),
                    'content'   => $message,
                    'private'   => 1,
                    'etat'      => 0
                )))*/


				$this->Support->create();
				$this->Support->save(array(
						'service_id'   => 1,
						'from_id'  => $this->Auth->user('id'),
						'title'   => 'Dénoncer un abus',
						'date_add'   => date('Y-m-d H:i:s'),
						'date_upd'   => date('Y-m-d H:i:s'),
						'status'   => 0,
				   ));
				 $this->SupportMessage->create();

				if($this->Support->id && $this->SupportMessage->save(array(
						'support_id'   => $this->Support->id,
						'from_id'   => $this->Auth->user('id') ,
						'to_id'     => Configure::read('Admin.id'),
						'content'   => $message,
						'date_add'   => date('Y-m-d H:i:s'),
						'date_message'   => date('Y-m-d H:i:s'),
						'etat'      => 0,
						'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
				)))
                    $this->Session->setFlash(__('Votre requête a bien été enregistrée et sera traitée dans les plus brefs délais'), 'flash_success');
                else
                    $this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre requête.'), 'flash_warning');
            }else
                $this->Session->setFlash(__('Veuillez vous connecter sur votre compte client.'), 'flash_warning');

            $this->redirect(array(
                'language'      => $this->Session->read('Config.language'),
                'controller'    => 'agents',
                'action'        => 'display',
                'link_rewrite'  => strtolower(str_replace(' ','-',$agent['User']['pseudo'])),
                'agent_number'  => $agent['User']['agent_number'],
                //'tab'           => 'abus'
            ),false);
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function msg_private(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs requis
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('to_id', 'content', 'attachment', 'attachment2'), array('to_id', 'content'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Formulaire incomplet.'),'flash_error');
                $this->redirect(array('controller' => 'home', 'action' =>'index'));
            }

            //Info agent
            $agent = $this->User->find('first', array(
                'fields' => array('id', 'pseudo', 'agent_number', 'email'),
                'conditions' => array('id' => $requestData['Agent']['to_id'], 'role' => 'agent', 'active' => 1),
                'recursive' => -1
            ));

            //Le lien vers la fiche agent
            $link = array(
                'language'      => $this->Session->read('Config.language'),
                'controller'    => 'agents',
                'action'        => 'display',
                'link_rewrite'  => strtolower($agent['User']['pseudo']),
                'agent_number'  => $agent['User']['agent_number'],
            );

            //Avons-nous un fichier ??
            if($this->isUploadedFile($requestData['Agent']['attachment'])){
                //Est-ce un fichier image autorisé ??
                if(!Tools::formatFile($this->allowed_mime_types, $requestData['Agent']['attachment']['type'],'Image')){
                    $this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté'), 'flash_warning');
                    $this->redirect($link);
                }
            }
			//S'il y a eu une erreur dans l'upload du fichier
            elseif($requestData['Agent']['attachment']['error'] != 4){
                $this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
                $this->redirect($link);
            }
			if($this->isUploadedFile($requestData['Agent']['attachment2'])){
                //Est-ce un fichier image autorisé ??
                if(!Tools::formatFile($this->allowed_mime_types, $requestData['Agent']['attachment2']['type'],'Image')){
                    $this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté'), 'flash_warning');
                    $this->redirect($link);
                }
            }
            //S'il y a eu une erreur dans l'upload du fichier
            elseif($requestData['Agent']['attachment2']['error'] != 4){
                $this->Session->setFlash(__('Erreur dans le chargement de votre deuxieme fichier.'),'flash_warning');
                $this->redirect($link);
            }

            //Si l'utilisateur est un client et connecté
            if($this->Auth->loggedIn() && $this->Auth->user('role') === 'client'){
                $this->loadModel('Message');

				$quota = Configure::read('Site.maxMessagePerMinutes');
				$dx = new DateTime(date('Y-m-d H:i:s'));
				$dx->modify('- 5 minutes');
				$datecheck = $dx->format('Y-m-d H:i:s');

				$nb_send = $this->Message->find('count', array(
					'conditions' => array('Message.from_id' => $this->Auth->user('id'), 'Message.date_add >=' =>$datecheck),
					'recursive' => -1
				));
				$etat = 0;

				if($nb_send > $quota){
					$etat = 2;
				}


					//on filtre le contenu
					$this->loadModel('FiltreMessage');

					$filtres = $this->FiltreMessage->find("all", array(
						'conditions' => array(
						)
					));
					foreach($filtres as $filtre){
						if(substr_count(strtolower($requestData['Agent']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
							$etat = 2;
					}
					if($etat == 2){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						//Les datas pour l'email
						$datasEmail = array(
							'content' => __('Un Message privé requiert check terme interdit.') ,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com'
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com',__('Message prive terme interdit'),'default',$datasEmail);
					}

					//On save le message
					$this->Message->create();
					if($this->Message->save(array(
						'from_id'       => $this->Auth->user('id'),
						'to_id'         => $agent['User']['id'],
						'content'       => $requestData['Agent']['content'],
						'private'       => 1,
						'etat'          => $etat
					))){
						//S'il y a une image à sauvegarder
						if($requestData['Agent']['attachment']['error'] === 0){
							//Si erreur pendant la sauvegarde de l'image
							if(!Tools::saveAttachment($requestData['Agent']['attachment'], Configure::read('Site.pathAttachment'), $agent['User']['agent_number'], $this->Message->id)){
								$this->Session->setFlash(__('Votre message a été envoyé, mais la pièce jointe n\'a pu être envoyée.'), 'flash_warning');
								$this->redirect($link);
							}

							//On save le nom de la pièce jointe
							$this->Message->saveField('attachment', $agent['User']['agent_number'].'-'. $this->Message->id .'.jpg');
						}
						if($requestData['Agent']['attachment2']['error'] === 0){
							//Si erreur pendant la sauvegarde de l'image
							if(!Tools::saveAttachment($requestData['Agent']['attachment2'], Configure::read('Site.pathAttachment'), $agent['User']['agent_number'].'-2', $this->Message->id)){
								$this->Session->setFlash(__('Votre message a été envoyé, mais la pièce jointe n\'a pu être envoyée.'), 'flash_warning');
								$this->redirect($link);
							}

							//On save le nom de la pièce jointe
							$this->Message->saveField('attachment2', $agent['User']['agent_number'].'-2'.'-'. $this->Message->id .'.jpg');
						}

						//Envoi de l'email
					    // $this->sendEmail($agent['User']['email'],'Nouveau message','new_mail',array('param' => array('name' => $agent['User']['pseudo'])));

            $title_mail = __('Vous avez un message privé en attente !');

					    if($etat == 0){
							 $this->sendCmsTemplateByMail(179, $this->Session->read('Config.id_lang'), $agent['User']['email'], array(
								'PSEUDO_NAME_DEST' => $agent['User']['pseudo'], 'MAIL_SUBJECT' => $title_mail
							 ));
					    }

						//check last message
						$last_message_private = $this->Message->find('first', array(
							'conditions' => array('Message.private' => 1, 'Message.from_id' => $agent['User']['id'],'Message.to_id' => $this->Auth->user('id'), 'Message.id !=' =>$this->Message->id),
							'recursive' => -1,
							'order' => 'Message.id DESC',
						));

						if($last_message_private){
							$date = new DateTime($last_message_private['Message']['date_add']);
							$messagelastmodified = $date->getTimestamp();

							$diff = time() - $messagelastmodified;
							$nb_days = 30 - round($diff / 86400);
							if($nb_days < 0){
								$this->Session->setFlash(__('Votre message est envoyé.').'<br />'.__('L\'expert ne pourra répondre qu\'à 1 message privé tous les 30 jours.'),'flash_success');
							}else{
								$this->Session->setFlash(__('Votre message est envoyé.').'<br />'.__('L\'expert ne pourra répondre qu\'à 1 message privé tous les 30 jours.').'<br />'.__('La prochaine réponse possible de sa part par message privé sera dans '.$nb_days.' jour(s)'),'flash_success');
							}


						}else{
							$this->Session->setFlash(__('Votre message privé est envoyé.').'<br />'.__('L\'expert ne pourra répondre qu\'à 1 message privé tous les 30 jours'),'flash_success');
						}

					}else
                    	$this->Session->setFlash(__('Erreur lors de l\'envoi du message.'),'flash_warning');
				//}else{
				//	$this->Session->setFlash(__('Le message n\'a pas été envoyé. Trop de messages soumis dernièrement.'),'flash_warning');
				//}

            }else
                $this->Session->setFlash(__('Seuls les clients peuvent envoyer des messages privés.'),'flash_warning');

           // $this->redirect($link);
			$this->redirect(array('controller' => 'accounts', 'action' => 'mails','?' => array('private' => true)));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    //Modifie le compte agent (mail, passwd)
    public function editAgentCompte(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

			if(!$this->request->data['Agent']['passwd2']){
				 $this->request->data['Agent']['passwd2'] = $this->request->data['Agent']['passwd'];
				$requestData['Agent']['passwd2'] = $requestData['Agent']['passwd'];
			}
            //On vérifie les champs du formulaire
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('email', 'passwd', 'passwd2'), array('email'));

            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil'));
            }

            //Vérification sur l'adresse mail
            if(!filter_var($this->request->data['Agent']['email'], FILTER_VALIDATE_EMAIL)){
                $this->Session->setFlash(__('Email invalide.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil'));
            }
            $this->_editCompte('Agent', $this->request->data);
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
    }

    //Modifie les informations de l'agent
    public function editAgentInfos(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formulaire
            $champForm = array('pseudo','firstname','lastname','sexe','birthdate','country_id', 'phone_number', 'phone_number2','phone_mobile', 'address', 'postalcode', 'city', 'siret', 'indicatif_phone','indicatif_phone2','indicatif_mobile','phone_operator','phone_operator2','phone_operator3','rib','bank_name','bank_address','bank_country','iban','swift','societe','mode_paiement','societe_adress','societe_adress2','societe_cp','societe_ville','societe_pays','vat_num','societe_statut','society_type_id');
            $champRequired = array('pseudo','firstname','lastname','sexe','birthdate','country_id', 'address', 'postalcode', 'city', 'siret', 'indicatif_phone', 'phone_number','phone_operator');
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], $champForm, $champRequired, array('birthdate'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
            }

			//data facultative
			if(isset($this->request->data['Agent']['belgium_save_num']))
				$requestData['Agent']['belgium_save_num'] = $this->request->data['Agent']['belgium_save_num'];
			if(isset($this->request->data['Agent']['belgium_society_num']))
				$requestData['Agent']['belgium_society_num'] = $this->request->data['Agent']['belgium_society_num'];
			if(isset($this->request->data['Agent']['canada_id_hst']))
				$requestData['Agent']['canada_id_hst'] = $this->request->data['Agent']['canada_id_hst'];
			if(isset($this->request->data['Agent']['spain_cif']))
				$requestData['Agent']['spain_cif'] = $this->request->data['Agent']['spain_cif'];
			if(isset($this->request->data['Agent']['luxembourg_autorisation']))
				$requestData['Agent']['luxembourg_autorisation'] = $this->request->data['Agent']['luxembourg_autorisation'];
			if(isset($this->request->data['Agent']['luxembourg_commerce_registrar']))
				$requestData['Agent']['luxembourg_commerce_registrar'] = $this->request->data['Agent']['luxembourg_commerce_registrar'];
			if(isset($this->request->data['Agent']['marocco_ice']))
				$requestData['Agent']['marocco_ice'] = $this->request->data['Agent']['marocco_ice'];
			if(isset($this->request->data['Agent']['marocco_if']))
				$requestData['Agent']['marocco_if'] = $this->request->data['Agent']['marocco_if'];
			if(isset($this->request->data['Agent']['portugal_nif']))
				$requestData['Agent']['portugal_nif'] = $this->request->data['Agent']['portugal_nif'];
			if(isset($this->request->data['Agent']['senegal_ninea']))
				$requestData['Agent']['senegal_ninea'] = $this->request->data['Agent']['senegal_ninea'];
			if(isset($this->request->data['Agent']['senegal_rccm']))
				$requestData['Agent']['senegal_rccm'] = $this->request->data['Agent']['senegal_rccm'];
			if(isset($this->request->data['Agent']['tunisia_rc']))
				$requestData['Agent']['tunisia_rc'] = $this->request->data['Agent']['tunisia_rc'];

            //On vérifie le numero de téléphone
            $this->loadModel('Country');
            //Indicatif invalide
            $flag_tel = $this->Country->allowedIndicatif($requestData['Agent']['indicatif_phone']);
            if($flag_tel === -1 || !$flag_tel){
                $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
            }
            //On assemble l'indicatif et le numéro de tel
            $requestData['Agent']['phone_number'] = Tools::implodePhoneNumber($requestData['Agent']['indicatif_phone'], $requestData['Agent']['phone_number']);

            //On check le numéro de téléphone
            $requestData['Agent']['phone_number'] = $this->checkPhoneNumber($requestData['Agent']['phone_number'],3, $this->Auth->user('id'));
            if(!$requestData['Agent']['phone_number'])
                $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'profil'));


            //Indicatif 2 invalide
            if (!empty($requestData['Agent']['phone_number2'])){
                if (empty($requestData['Agent']['indicatif_phone2'])){
                    $this->Session->setFlash(__('Veuillez indiquer un indicatif pays sur votre numéro de téléphone secondaire'),'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
                }

                $flag_tel = $this->Country->allowedIndicatif($requestData['Agent']['indicatif_phone2']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique du numéro secondaire n\'est pas valide.')." (".$requestData['Agent']['indicatif_phone2'].")",'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
                }

                //On assemble l'indicatif et le numéro de tel 2
                $requestData['Agent']['phone_number2'] = Tools::implodePhoneNumber($requestData['Agent']['indicatif_phone2'], $requestData['Agent']['phone_number2']);

                //On check le numéro de téléphone
                $requestData['Agent']['phone_number2'] = $this->checkPhoneNumber($requestData['Agent']['phone_number2'],3, $this->Auth->user('id'));
                if(!$requestData['Agent']['phone_number2'])
                    $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'profil'));
            }
			
			//Indicatif 2 invalide
            if (!empty($requestData['Agent']['phone_mobile'])){
                if (empty($requestData['Agent']['indicatif_mobile'])){
                    $this->Session->setFlash(__('Veuillez indiquer un indicatif pays sur votre numéro de téléphone mobile'),'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
                }

                $flag_tel = $this->Country->allowedIndicatif($requestData['Agent']['indicatif_mobile']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique du numéro mobile n\'est pas valide.')." (".$requestData['Agent']['indicatif_mobile'].")",'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
                }

                //On assemble l'indicatif et le numéro de tel 2
                $requestData['Agent']['phone_mobile'] = Tools::implodePhoneNumber($requestData['Agent']['indicatif_mobile'], $requestData['Agent']['phone_mobile']);

                //On check le numéro de téléphone
                $requestData['Agent']['phone_mobile'] = $this->checkPhoneNumber($requestData['Agent']['phone_mobile'],3, $this->Auth->user('id'));
                if(!$requestData['Agent']['phone_mobile'])
                    $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'profil'));
            }



            //Avant tout, il faut savoir s'il y a des modification en attente
            //On charge un model
            $this->loadModel('UserValidation');
            //Retourne false s'il n'y pas de modification sinon l'id
            $idEdit = $this->UserValidation->hasValidation($this->Auth->user('id'));

            //Date de la modification par l'user
            $requestData['Agent']['date_upd'] = date('Y-m-d H:i:s');
            $requestData['Agent']['etat'] = 0;
            $requestData['UserValidation'] = $requestData['Agent'];
            unset($requestData['Agent']);

            //S'il y a des modifications en attente
            if($idEdit !== false){
                $this->UserValidation->id = $idEdit;
                $this->UserValidation->save($requestData['UserValidation']);
                $this->Session->setFlash(__('Votre modification est enregistrée. Elle est en attente de validation par un administrateur.'),'flash_success');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
            }else{  //Pas de modification en attente
                //On insert en BDD les données actuelles
                $user = $this->User->find('first',array(
                    'conditions' => array('id' => $this->Auth->user('id')),
                    'recursive' => -1
                ));
                //On initialise les datas pour UserValidation
                $user['UserValidation'] = $user['User'];
                $user['UserValidation']['users_id'] = $user['User']['id'];
                //On unset les données User
                unset($user['User']);
                unset($user['UserValidation']['id']);
                //On save
                $this->UserValidation->create();
                $this->UserValidation->save($user['UserValidation']);

                //Et maintenant on sauvegarde la modification des données
                $requestData['UserValidation']['users_id'] = $this->Auth->user('id');
                $this->UserValidation->save($requestData['UserValidation']);

                $this->Session->setFlash(__('Votre modification est enregistrée. Elle est en attente de validation par un administrateur'),'flash_success');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
            }
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
    }

	//Modifie les informations de l'agent
    public function editAgentInfosAdmin(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formulaire
            $champForm = array('absence');
            $champRequired = array();
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], $champForm, $champRequired);
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'profil'));
            }
            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('absence',$requestData['Agent']['absence']);

            // save status message used next in admins info
            $this->loadModel('AgentMessage');
            $this->AgentMessage->create();
            $this->AgentMessage->save(array(
                'agent_id' =>  $this->User->id,
                'last_message' =>  $requestData['Agent']['absence'],
                'date_add' =>  date('Y-m-d H:i:s'),
                'status'    => 'Envoyer'
            ));
            $this->Session->setFlash(__('Votre modification est enregistrée.'),'flash_success');
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));

        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
    }


    public function edit_options(){
        if($this->request->is('post')){
            $url = $this->referer();
            if(empty($url) || $url === false)
                $url = array('controller' => 'agents', 'action' => 'index');

            //Si l'agent est en communication, modification des options experts impossible
            $agent_status = $this->User->field('agent_status', array('id' => $this->Auth->user('id')));
            if(strcmp($agent_status, 'busy') == 0){
                $this->Session->setFlash(__('La modification de vos options est impossible, lorsque vous êtes en communication.'),'flash_warning');
                $this->redirect($url);
            }

            $requestData = $this->request->data;

            //Si vide, erreur
            if(empty($requestData)){
                $this->Session->setFlash(__('Au moins une option doit être sélectionnée.'), 'flash_warning');
                $this->redirect($url);
            }

            //On transforme les données du champ consult
            //0 : Email     1 : Téléphone       2 : Chat
            //On initialise les champs consult
            foreach ($this->consult_medias as $k => $val){
                $requestData['Agent']['consult_'.$k] = 0;
            }

            foreach ($requestData['Agent']['consult'] as $value){
                if($value == 0)
                    $requestData['Agent']['consult_email'] = 1;
                elseif ($value == 1)
                    $requestData['Agent']['consult_phone'] = 1;
                else
                    $requestData['Agent']['consult_chat'] = 1;
            }

			$agent_consult_email = $this->User->field('consult_email', array('id' => $this->Auth->user('id')));
			if((int)$agent_consult_email < 0 || $agent_consult_email == "2") $requestData['Agent']['consult_email'] = $agent_consult_email;
			#if($agent_consult_email == 2) $requestData['Agent']['consult_email'] = 2;
			#if($agent_consult_email == -1) $requestData['Agent']['consult_email'] = -1;

			$agent_consult_phone = $this->User->field('consult_phone', array('id' => $this->Auth->user('id')));
			if((int)$agent_consult_phone < 0 || $agent_consult_phone == "2") $requestData['Agent']['consult_phone'] = $agent_consult_phone;
			#if($agent_consult_phone == 2) $requestData['Agent']['consult_phone'] = 2;
			#if($agent_consult_phone == -1) $requestData['Agent']['consult_phone'] = -1;

			$agent_consult_chat = $this->User->field('consult_chat', array('id' => $this->Auth->user('id')));
			if((int)$agent_consult_chat < 0 || $agent_consult_chat == "2") $requestData['Agent']['consult_chat'] = $agent_consult_chat;
			#if($agent_consult_chat == 2) $requestData['Agent']['consult_chat'] = 2;
			#if($agent_consult_chat == -1) $requestData['Agent']['consult_chat'] = -1;

			//patch pour debloquer email si autre mode ok
			/*if($requestData['Agent']['consult_chat'] > 0 && $requestData['Agent']['consult_phone'] > 0 && $agent_consult_email < 1){
				$requestData['Agent']['consult_email'] = 1;
			}*/


            $api = new Api();
            //Si l'agent est disponible
            if(strcmp($agent_status, 'available') == 0){
                if($requestData['Agent']['consult_phone'] == 1){
                    //Connection de l'agent sur la plateforme téléphonique
                    $result = $api->connectAgent($this->Auth->user('agent_number'));
				}else
                    //Déconnection de l'agent sur la plateforme téléphonique
                    $result = $api->deconnectAgent($this->Auth->user('agent_number'));
            }

            //On unset le champ consult
            unset($requestData['Agent']['consult']);
			$dd = $this->User->field('date_last_activity', array('id' => $this->Auth->user('id')));
			//new order agent

			if(
				($agent_consult_phone != $requestData['Agent']['consult_phone'] && $requestData['Agent']['consult_chat'] == 0)
				||
				($requestData['Agent']['consult_phone'] == 0 && $agent_consult_chat != $requestData['Agent']['consult_chat'] )
			){
				$tabdd = explode(' ',$dd);
				$tabd1 = explode('-',$tabdd[0]);
				$tabd2 = explode(':',$tabdd[1]);
				$timestamp = mktime($tabd2[0],$tabd2[1],$tabd2[2],$tabd1[1],$tabd1[2],$tabd1[0]);
				if($requestData['Agent']['consult_phone'] == 1 ||  ($requestData['Agent']['consult_chat'] == 1 && (time() - $timestamp) <= 60)) {
					 $pos = $this->User->find('first',array(
								'fields' => array('list_pos'),
								'conditions' => array('User.agent_status' => 'available', 'User.role' => 'agent', 'User.active' => 1, 'User.deleted' => 0,
									'OR' => array(
										array('User.consult_phone' => '1'),
										array(
											'User.consult_chat' => '1',
											'(UNIX_TIMESTAMP(now()) - (unix_timestamp(User.date_last_activity))) <=' => 60
										)
									)
								),
								'order' => 'list_pos DESC',
								'recursive' => -1
							));
					$requestData['Agent']['list_pos']	= $pos["User"]['list_pos'];
				}else{
					if($requestData['Agent']['consult_email'] == 1){
						 $pos = $this->User->find('first',array(
								'fields' => array('list_pos'),
								'conditions' => array('User.agent_status' => 'available', 'User.role' => 'agent', 'User.consult_email' => 1, 'User.consult_phone' => 0, 'User.consult_chat' => 0,
								),
								'order' => 'list_pos DESC',
								'recursive' => -1
							));
						$requestData['Agent']['list_pos']	= $pos["User"]['list_pos'];
					}else{
						$requestData['Agent']['list_pos'] = 9999;
					}
				}
			}
            //Pour finir on save les données coté User
            $this->User->id = $this->Auth->user('id');
			$requestData['Agent']['id'] = $this->User->id;
            $this->User->save($requestData['Agent']);

			//on actualise data connexion par mode
			$this->loadModel('UserConnexion');
			$connexion = $this->UserConnexion->find('first', array(
						'conditions' => array('user_id' => $this->Auth->user('id'), 'session_id' =>  session_id()),
					    'order' => 'id DESC',
						'recursive' => -1
					));
			if($requestData['Agent']['consult_email'] != $connexion['UserConnexion']['mail']
			   || $requestData['Agent']['consult_chat'] != $connexion['UserConnexion']['tchat']
			   || $requestData['Agent']['consult_phone'] != $connexion['UserConnexion']['phone']
			  ){

				if($agent_status != 'unavailable'){
				$connexion = array(
							'user_id'          	=> $this->Auth->user('id'),
							'session_id'        => session_id(),
							'date_connexion'    => date('Y-m-d H:i:s'),
							'who'				=> $this->Auth->user('id'),
							'date_lastactivity' => date('Y-m-d H:i:s'),
							'mail'            	=> $requestData['Agent']['consult_email'],
							'tchat'      		=> $requestData['Agent']['consult_chat'],
							'phone'    			=> $requestData['Agent']['consult_phone']
						);
					}else{
								$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => session_id(),
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> 0,
									'tchat'      		=> 0,
									'phone'    			=> 0
								);
							}
						$this->UserConnexion->create();
						$this->UserConnexion->save($connexion);

			}




            //S'il n'y a pas eu de requete pour l'API OU s'il y a eu une requete pour l'API et que tout est OK
            if(!isset($result) || (isset($result) && isset($result['response_code']) && $result['response_code'] == 0))
                $this->Session->setFlash(__('Vos options ont été modifiées'),'flash_success');
            else
                $this->Session->setFlash(__('Vos options ont été modifiées. Une erreur est survenue au niveau de la plateforme téléphonique.'),'flash_warning');

            $this->redirect($url);
        }
    }


    public function edit_options_num(){
        if($this->request->is('post')){
			$this->layout = '';
            $url = $this->referer();
            if(empty($url) || $url === false)
                $url = array('controller' => 'agents', 'action' => 'index');

            $requestData = $this->request->data;

            $new_use_value=$requestData['Agent']['phone_number_to_use'];

            //Si vide, erreur
            if(empty($new_use_value)){
                $this->Session->setFlash(__('Au moins une option doit être sélectionnée.'), 'flash_warning');
                $this->redirect($url);
            }

            $phone_api_use = $this->User->field('phone_api_use', array('id' => $this->Auth->user('id')));

            // update database
            $this->loadModel('User');
			$this->loadModel('CostPhone');
			
			//check indicatif accepted for mobile
			$is_accepted_ind_mobile = true;
			if($this->User->field('phone_mobile', array('id' => $this->Auth->user('id'))) == $requestData['Agent']['phone_number_to_use']['use']){
				
				$indicatifs = $this->CostPhone->find("all", array(
							'conditions' => array(
							)
				));
				$is_accepted_ind_mobile = false;
				foreach($indicatifs as $indicatif){
					if(substr($requestData['Agent']['phone_number_to_use']['use'],0,strlen($indicatif['CostPhone']['indicatif'])) == $indicatif['CostPhone']['indicatif'])
						$is_accepted_ind_mobile = true;
				}
				
			}
			
			if(!$is_accepted_ind_mobile){
				$this->Session->setFlash(__('Votre numéro Mobile n\'est pas autorisé pour le transfert d\'appels.'), 'flash_warning');
                $this->redirect($url);
			}

            if ( strcmp($phone_api_use, $new_use_value) !== 0) {
                $this->User->updateAll(array('phone_api_use' => $requestData['Agent']['phone_number_to_use']['use']),array('User.id' => $this->Auth->user('id')));
            }

            else {
                $this->Session->setFlash(__('Option déjà sélectionné.'), 'flash_warning');
                $this->redirect($url);
            }

            //on actualise l'api
            $api = new Api();
            $result = $api->updateAgent($this->User->field('agent_number', array('id' => $this->Auth->user('id'))), $requestData['Agent']['phone_number_to_use']['use']);

            //S'il n'y a pas eu de requete pour l'API OU s'il y a eu une requete pour l'API et que tout est OK
            if(!isset($result) || (isset($result) && isset($result['response_code']) && $result['response_code'] == 0))
                $this->Session->setFlash(__('Vos options ont été modifiées'),'flash_success');
            else
                $this->Session->setFlash(__('Vos options ont été modifiées. Une erreur est survenue au niveau de la plateforme téléphonique.'),'flash_warning');

            $this->redirect($url);
        }
    }

    //Modifie les options experts de l'agent
    public function editAgentOptions(){
        if($this->request->is('post')){
            //Si l'agent est en communication, modification des options experts impossible
            $agent_status = $this->User->field('agent_status', array('id' => $this->Auth->user('id')));
            if(strcmp($agent_status, 'busy') == 0){
                $this->Session->setFlash(__('La modification de vos options est impossible, lorsque vous êtes en communication.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'options'));
            }

            $requestData = $this->request->data;

            //On vérifie les champs du formulaire

            $champForm = array('langs', 'countries', 'categories', 'consult', 'mail_infos');
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], $champForm, $champForm, array('langs', 'countries', 'categories'));
            if($requestData['Agent'] === false){
				$old_data = $this->request->data;
				if(!$old_data['Agent']['langs'])
                	$this->Session->setFlash(__('Veuillez remplir votre option langue parlé.'),'flash_warning');
				if(!$old_data['Agent']['countries'])
                	$this->Session->setFlash(__('Veuillez remplir votre option Visibilitée.'),'flash_warning');
				if(!$old_data['Agent']['categories'])
                	$this->Session->setFlash(__('Veuillez remplir votre option Univers.'),'flash_warning');
				if(!$old_data['Agent']['consult'])
                	$this->Session->setFlash(__('Veuillez remplir votre option mode de consultation.'),'flash_warning');

                //$this->Session->setFlash(__('Veuillez remplir toutes vos options experts.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' =>'profil', 'tab' => 'options'));
            }

			//on vide si infos mails changé
			$mail_info = $this->User->field('mail_infos_v', array('id' => $this->Auth->user('id')));
			if($mail_info == $requestData['Agent']['mail_infos'])//ne pas revalider info text identique
				$requestData['Agent']['mail_infos'] = '';

            //On charge le model CategoryUser pour save les univers
            $this->loadModel('CategoryUser');

            //On transforme les données du champ consult
            //0 : Email     1 : Téléphone       2 : Chat
            //On initialise les champs consult
            foreach ($this->consult_medias as $k => $val){
                $requestData['Agent']['consult_'.$k] = 0;
            }

            foreach ($requestData['Agent']['consult'] as $value){
                if($value == 0)
                    $requestData['Agent']['consult_email'] = 1;
                elseif ($value == 1)
                    $requestData['Agent']['consult_phone'] = 1;
                elseif($value == 2)
                    $requestData['Agent']['consult_chat'] = 1;
            }

			$agent_consult_email = $this->User->field('consult_email', array('id' => $this->Auth->user('id')));
			if($agent_consult_email == -1) $requestData['Agent']['consult_email'] = -1;
			$agent_consult_phone = $this->User->field('consult_phone', array('id' => $this->Auth->user('id')));
			if($agent_consult_phone == -1) $requestData['Agent']['consult_phone'] = -1;
			$agent_consult_chat = $this->User->field('consult_chat', array('id' => $this->Auth->user('id')));
			if($agent_consult_chat == -1) $requestData['Agent']['consult_chat'] = -1;

            $api = new Api();
            //Si l'agent est disponible
            if(strcmp($agent_status, 'available') == 0){
                if($requestData['Agent']['consult_phone'] == 1)
                    //Connection de l'agent sur la plateforme téléphonique
                    $result = $api->connectAgent($this->Auth->user('agent_number'));
                else
                    //Déconnection de l'agent sur la plateforme téléphonique
                    $result = $api->deconnectAgent($this->Auth->user('agent_number'));
            }

            //On unset le champ consult
            unset($requestData['Agent']['consult']);

            //On transforme le tableau des langues parlées et des pays en String
            $requestData['Agent']['langs'] = implode(',',$requestData['Agent']['langs']);
			$agent_langs = $this->User->field('langs', array('id' => $this->Auth->user('id')));
			if(substr_count($agent_langs,',9' ))$requestData['Agent']['langs'] .=',9';

			//patch
			$requestData['Agent']['langs'] = str_replace('1','1,8,10,11,12',$requestData['Agent']['langs']);
            $requestData['Agent']['countries'] = implode(',',$requestData['Agent']['countries']);

            //CATGEORY (Univers) : On transforme les données de categories
            foreach ($requestData['Agent']['categories'] as $value){
                $dataCategories[] = array('CategoryUser' => array('user_id' => $this->Auth->user('id'), 'category_id' => $value));
            }
            try{
                //On delete les données des univers pour cet user
                $this->CategoryUser->deleteAll(array('user_id' => $this->Auth->user('id')), false);
                $this->CategoryUser->saveMany($dataCategories);
                //on unset le champ categories
                unset($requestData['Agent']['categories']);

                //Pour finir on save les données coté User
                $this->User->id = $this->Auth->user('id');
				$requestData['Agent']['id'] = $this->User->id;
                $this->User->save($requestData['Agent']);
            }catch (mysqli_sql_exception $err){
                $this->Session->setFlash(__('Erreur dans la modification de vos options experts'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'options'));
            }

            //S'il n'y a pas eu de requete pour l'API OU s'il y a eu une requete pour l'API et que tout est OK
            if(!isset($result) || (isset($result) && isset($result['response_code']) && $result['response_code'] == 0))
                $this->Session->setFlash(__('Vos options ont été modifiées et soumises à un administrateur Spiriteo pour validation'),'flash_success');
            else
                $this->Session->setFlash(__('Vos options ont été modifiées. Une erreur est survenue au niveau de la plateforme téléphonique.'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
    }

    //Modifie les medias de l'agent
    public function editAgentMedia(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('crop', 'photo', 'audio'));

            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Ce formulaire est incorrect. Veuillez ne pas toucher le code HTML du site.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
            }

            $checkModif = array('photo' => false, 'audio' => false);
            $media = array(
                'photo' => array(
                    'format' => 'Image',
                    'formatError' => __('Le fichier image n\'est pas dans un bon format.'),
                    'uploadError' => __('Erreur dans le chargement de votre photo.'),
                    'sizeError' => __('Le fichier image est trop volumineux'),
                    'path' => 'Site.pathPhotoValidation',
                    'extension' => 'jpg'
                ),
                'audio' => array(
                    'format' => 'Audio',
                    'formatError' => __('Le fichier audio n\'est pas dans un bon format.'),
                    'uploadError' => __('Erreur dans le chargement de votre présentation audio.'),
                    'sizeError' => __('Le fichier audio est trop volumineux'),
                    'path' => 'Site.pathPresentationValidation',
                    'extension' => 'mp3'
                )
            );

            //pour chaque media on check si tout est bien
            foreach ($media as $format => $file){
                //Si le fichier a été téléchargé correctement
                if($this->isUploadedFile($requestData['Agent'][$format])){
                    if(!Tools::formatFile($this->allowed_mime_types,$requestData['Agent'][$format]['type'], $file['format'])){
                        $this->Session->setFlash(__($file['formatError']),'flash_warning');
                        $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
                    }
                    $checkModif[$format] = true;
                }elseif($requestData['Agent'][$format]['size'] > 0){    //Si un fichier a bien été téléchargé (partiellement ou complètemenent) mais que isUploadedFile retourne false alors erreur
                    $this->Session->setFlash(__($file['uploadError']),'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
                }
                elseif($requestData['Agent'][$format]['error'] == 1){   //Taille du fichier plus grande que celle de la conf php.   Voir php.ini
                    $this->Session->setFlash(__($file['sizeError']),'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
                }elseif($requestData['Agent'][$format]['error'] != 4 && $requestData['Agent'][$format]['error'] != 0) {  //Sinon autre erreur
                    $this->Session->setFlash(__($file['uploadError']),'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
                }
            }

            //Si le fichier audio est trop volumineux
            //Taille fichier supérieur à la conf Noox
            if($checkModif['audio'] && $requestData['Agent']['audio']['size'] > Configure::read('Site.maxSizeAudio')){
                $this->Session->setFlash(__('Votre fichier audio est trop volumineux'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
            }

            //On ajoute le code de l'agent dans les datas
            $requestData['Agent']['agent_number'] = $this->Auth->user('agent_number');


            //On save les modifications
            foreach ($checkModif as $format => $val){
                if($val){
                    //On supprime un éventuel fichier en attente
					$file = new File(Configure::read('Site.pathAdmin').Configure::read($media[$format]['path']).'/'.$requestData['Agent']['agent_number'][0].'/'.$requestData['Agent']['agent_number'][1].'/'.$requestData['Agent']['agent_number'].'.'.$media[$format]['extension']);
                    if($file->exists()){
                        $file->delete();
                        //La photo listing
                        if(strcmp($format, 'Image') == 0){
                            $file = new File(Configure::read('Site.pathAdmin').Configure::read($media[$format]['path']).'/'.$requestData['Agent']['agent_number'][0].'/'.$requestData['Agent']['agent_number'][1].'/'.$requestData['Agent']['agent_number'].'_listing.'.$media[$format]['extension']);
                            if($file->exists()) $file->delete();
                        }
                    }
                    //On save le fichier
                    $this->saveFile($requestData['Agent'], $format, true);
                    $this->Session->setFlash('La modification de votre présentation est en attente de validation', 'flash_success');
                }
            }
            $this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'profil'));
    }

    //Modifie les présentations d'un voyant
    public function editAgentPresentation(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formulaire
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('lang_id', 'texte'), array('lang_id'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Erreur dans le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' => 'presentations'));
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['Agent']['texte']) || empty($requestData['Agent']['texte']) || ctype_space($requestData['Agent']['texte'])){
                $this->Session->setFlash(__('Votre présentation est vide.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'presentations'));
            }


            $this->loadModel('UserPresentValidation');

            $requestData['UserPresentValidation']['texte'] = $this->request->data['Agent']['texte'];
            $requestData['UserPresentValidation']['lang_id'] = $this->request->data['Agent']['lang_id'];
            $requestData['UserPresentValidation']['date_upd'] = date('Y-m-d H:i:s');
            $requestData['UserPresentValidation']['user_id'] = $this->Auth->user('id');
            $requestData['UserPresentValidation']['etat'] = 0;
            unset($requestData['Agent']);

            //On récupère l'id de la présentation en BDD
            $idEdit = $this->UserPresentValidation->hasPresentation($this->Auth->user('id'),$requestData['UserPresentValidation']['lang_id']);

            //Il y a déjà une présentation pour cet id_lang
            if($idEdit !== false){
                $this->UserPresentValidation->id = $idEdit;
                if(!$this->UserPresentValidation->save($requestData)){
                    $this->Session->setFlash(__('Erreur dans la mise à jour de votre présentation.'), 'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' => 'presentations'));
                }
            }else{  //Pas de modification en attente
                $this->UserPresentValidation->create();
                if(!$this->UserPresentValidation->save($requestData)){
                    $this->Session->setFlash(__('Erreur dans la mise à jour de votre présentation.'), 'flash_error');
                    $this->redirect(array('controller' => 'agents', 'action' => 'presentations'));
                }
            }

            $this->Session->setFlash(__('La modification de votre présentation est en attente de validation.'),'flash_success');
            $this->redirect(array('controller' => 'agents', 'action' => 'presentations'));
        }
    }

    //Affiche le profil agent
    public function profil($tab='infos'){

	
        if(isset($this->params['named']['tab']))
            $tab = $this->params['named']['tab'];

        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception("Erreur de sécurité !", 1);

        // On charge l'agent
        $this->User->id = (int)$user['id'];
        $data = $this->User->read();
		$user = $this->User->find('first',array(
                    'conditions' => array('id' => (int)$user['id']),
                    'recursive' => -1
                ));
        /* On vérifie que l'utilisateur est bien dans son role */
        if ($data['User']['role'] != $this->myRole)
            throw new Exception("Erreur de sécurité", 1);

        /* On récupère la liste des pays disponibles et les langues et les autres models*/
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('UserValidation');
        $this->loadModel('CategoryLang');
        $this->loadModel('CategoryUser');
		    $this->loadModel('SocietyType');
        $this->loadModel('InvoiceVat');

        //La liste des pays utilisateur (ce n'est pas les pays des domaines)
        $countries = $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang'));
        $this->set('select_countries_sites', $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $langs = $this->Lang->getLang(true);
        //La liste des pays de l'agent (le pays correspondant aux domaines)
        $countries_agent = $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang'));
        $this->set('category_langs', $this->CategoryLang->getCategories($this->Session->read('Config.id_lang')));

       	//VAT
        if($user['User']['societe_pays'])$countryid = $user['User']['societe_pays']; else $countryid = $user['User']['country_id'];

        $this->set('select_invoice_vat',$this->InvoiceVat->getVatRateForSelect($this->Session->read('Config.id_lang'),$user['User']['invoice_vat_id'],$countryid,$user['User']['society_type_id']));

        $list_invoice_society = $this->InvoiceVat->getVatSocietybyCountry($this->Session->read('Config.id_lang'),$countryid);


        //Pour l'onglet INFOS GENERALES-------------------------------------------------------------------------------------------------------
        $return = $this->_ongletInfos($countries,$langs, $countries_agent);


        //Données de l'utilisateur
        $donnees = $return['donnees'];

        //Données pour l'affichage
        $userDatas = $return['userDatas'];

	
        //Pour l'onglet MODIFIER VOS DONNEES--------------------------------------------------------------------------------------------------
        $this->_ongletDatas($countries,$donnees);

        //Pour l'onglet MODIFIER VOS OPTIONS--------------------------------------------------------------------------------------------------
        $return = $this->_ongletOptions($donnees);

        //Univers de l'agent
        $univers = $return['univers'];
        //Moyen de consultation de l'agent
        $consult = $return['consult'];

        //Pour l'onglet PRESENTATION----------------------------------------------------------------------------------------------------------
        $this->_ongletPresentations($data);

        unset($this->request->data['Agent']['id']);


		//check VAT
		$vat_a_valide = false;
		/*if($user['User']['vat_num_status'] == 'invalide' && !$user['User']['vat_num_status_reason']){
			$vat_a_valide = true;
		}*/


		//check CGV
		$this->loadModel('Cgv');
		$check_cgv = $this->Cgv->find('first',array(
							'conditions'    => array(
								'user_id' => $this->Auth->user('id'),
							),
							'order' => "date_valid DESC",
							'recursive' => -1
						));
		$cgv_a_valide = false;

		//var_dump($check_cgv);exit;
		if(count($check_cgv)){
			$dd_valid = $check_cgv['Cgv']['date_valid'];
			$this->loadModel('Page');
			$pagecgv = $this->Page->find('first',array(
								'conditions'    => array(
									'id' => 245,
								),
								'recursive' => -1
							));
			$dd_cgv = $pagecgv['Page']['date_upd'];

			$ddvalid = str_replace(' ','',$dd_valid);
			$ddvalid = str_replace('-','',$ddvalid);
			$ddvalid = str_replace(':','',$ddvalid);

			$ddcgv = str_replace(' ','',$dd_cgv);
			$ddcgv = str_replace('-','',$ddcgv);
			$ddcgv = str_replace(':','',$ddcgv);

			if($ddcgv > $ddvalid)$cgv_a_valide = true;

		}else{
			$cgv_a_valide = true;
		}

		//check Conditions utilisation
		$this->loadModel('Cu');
		$check_cu = $this->Cu->find('first',array(
							'conditions'    => array(
								'user_id' => $this->Auth->user('id'),
							),
							'order' => "date_valid DESC",
							'recursive' => -1
						));
		$cu_a_valide = false;

		//var_dump($check_cu);exit;
		
		if(count($check_cu)){
			$dd_valid = $check_cu['Cu']['date_valid'];
			$this->loadModel('Page');
			$pagecu = $this->Page->find('first',array(
								'conditions'    => array(
									'id' => 360,
								),
								'recursive' => -1
							));
			$dd_cu = $pagecu['Page']['date_upd'];

			$ddvalid = str_replace(' ','',$dd_valid);
			$ddvalid = str_replace('-','',$ddvalid);
			$ddvalid = str_replace(':','',$ddvalid);

			$ddcu = str_replace(' ','',$dd_cu);
			$ddcu = str_replace('-','',$dd_cu);
			$ddcu = str_replace(':','',$dd_cu);

			if($ddcu > $ddvalid)$cu_a_valide = true;

		}else{
			$cu_a_valide = true;
		}
		
	
		
		$this->loadModel('Planning');
		$plannings = $this->Planning->find('all',array(
								'conditions'    => array(
									'user_id' => $this->Auth->user('id'),
									'A' => date('Y'),
									'M' => date('m'),
								),
								'recursive' => -1
							));


		$iban = false;
		if($data['User']['iban'] || $data['User']['rib']){
			$iban = true;
		}
		if(!$iban){
			$this->Session->setFlash(__('Merci de saisir votre IBAN ou RIB pour continuer a travailler sur Spiriteo.'), 'flash_warning');
		}

		$cond_data = false;

		if($data['User']['country_id'] != 157 && $data['User']['country_id'] != 120  && $data['User']['country_id'] != 180 && $data['User']['country_id'] != 148 && $data['User']['country_id'] != 66 && $iban){			if(!$data['User']['siret'])$cond_data = true;
			//if(!$data['User']['iban'])$cond_data = true;
			if(!$data['User']['society_type_id'] && !$data['User']['societe_statut'] )$cond_data = true;
			if(!$data['User']['societe'])$cond_data = true;
			//if(!$data['User']['vat_num'])$cond_data = true;
		}

		$planning_a_valide = false;
		if(!$plannings && $iban){
			$planning_a_valide = true;
		}


		$this->set('cond_data', $cond_data);
		$this->set('cond_planning', $planning_a_valide);
		$this->set('cond_cu', $cu_a_valide);
		$this->set('cond_vat', $vat_a_valide);
		$this->set('cgv', $cgv_a_valide);
        $this->set('langs', $this->Lang->getLang(true));
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
		$this->set('selected_countries',$data['User']['country_id']);
		$this->set('select_countries_society', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
		$this->set('selected_countries_society',$data['User']['societe_pays']);
		$this->set('selected_society_types',$data['User']['society_type_id']);
		$this->set('select_society_types', $this->SocietyType->getTypeForSelectExpert($this->Session->read('Config.id_lang'),$list_invoice_society));
        $this->set(compact('donnees', 'userDatas', 'countries', 'consult', 'univers', 'tab', 'user'));
    }
    
    
     public function payment() {}
    

    public function planning(){
        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception("Erreur de sécurité !", 1);

        //On récupère les plannings de l'user
        //On charge le model Planning
        $this->loadModel('Planning');

        $intervalle = array();

        //On récupère l'intervalle depuis request data
         if($this->request->is('post')){
             $intervalle[0]['date'] = $this->request->data['Agent']['intervalle'][0];
             //On met l'indice sur le dernier jour pour plus de facilité
             $intervalle[(Configure::read('Site.limitPlanning')-1)]['date'] = $this->request->data['Agent']['intervalle'][1];
         }else{  //Ou sinon on le génère par rapport à la date du jour
             //On crée le tableau des xx (voir Config.Site.limitPlanning) jours suivants
             for($i=0; $i<Configure::read('Site.limitPlanning'); $i++){
                 $intervalle[$i]['date'] = date('d-m-Y', strtotime('+'.$i.' days'));
                 $intervalle[$i]['label'] = date('d/m', strtotime('+'.$i.' days'));
                 $intervalle[$i]['day'] = date('l', strtotime('+'.$i.' days'));
             }
         }

        //On explose le début de l'intervalle pour une recherche plus rapide dans le find
        $debutExplode = $this->explodeDate($intervalle[0]['date']);
        //On explose la fin de l'intervalle pour une recherche plus rapide dans le find
        $finExplode = $this->explodeDate($intervalle[(Configure::read('Site.limitPlanning')-1)]['date']);

        //-------------------------------------------------------Lors d'une mise à jour--------------------------------------------------------------
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs necessaire
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('intervalle', 'planning'), array('intervalle'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Erreur dans le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'agents', 'action' => 'planning'));
            }

            //On décode planning
            if(isset($requestData['Agent']['planning']))
                $requestData['Agent']['planning'] = json_decode($requestData['Agent']['planning']);

            //Variable qui sera sauvegardé au final
            $savePlanning = array();
            //On supprime les horaires pour l'intervalle en question
            $this->Planning->deleteAll(
                $this->Planning->getConditions($this->Auth->user('id'),$debutExplode,$finExplode),
                false
            );

            //Pour chaque jour du planning (de request data donc pas forcement tout les jours)
            foreach($requestData['Agent']['planning'] as $date => $val){
                //On explose la date en cours et on ajoute le jour de la semaine
                $dateExplode = $this->explodeDate($date);
                $dateExplode['JS'] = date('N', strtotime($date));

                foreach ($val as $horaire){
                    array_push($savePlanning, array(
                        'user_id'   => $this->Auth->user('id'),
                        'type'      => $horaire->type,
                        'J'         => $dateExplode['J'],
                        'M'         => $dateExplode['M'],
                        'A'         => $dateExplode['A'],
                        'H'         => $horaire->h,
                        'Min'       => $horaire->m,
                        'JS'        => $dateExplode['JS']
                    ));
                }
            }

            //S'il y a des horaires à enregistrer
            if(!empty($savePlanning)){
                //On sauvegarde le planning
                $this->Planning->create();
                if($this->Planning->saveMany($savePlanning))
                    $this->Session->setFlash(__('Mise à jour du planning.'),'flash_success');
                else
                    $this->Session->setFlash(__('Erreur dans la modification de votre planning'),'flash_error');
            }else
                $this->Session->setFlash(__('Mise à jour du planning.'),'flash_success');

            $key = 'planning-'.$this->Auth->user('id').'-'.implode("",$debutExplode).'-'.implode("",$finExplode);
            Cache::delete($key, Configure::read('nomCachePlanning'));
            $planning = $this->Planning->_get_planning($this->Auth->user('id'),$debutExplode,$finExplode);
            $planning = $this->Planning->restructurePlanning($planning);
            Cache::write($key, serialize($planning),Configure::read('nomCachePlanning'));

            $this->redirect(array('controller' => 'agents', 'action' => 'planning'));
        }

        //--------------------------------------------------Quand on arrive sur la page planning--------------------------------------------------------------------
        $planning = $this->Planning->agent_planning($this->Auth->user('id'),$debutExplode,$finExplode);

        //Les rdv de l'agent
        $this->loadModel('CustomerAppointment');
        $appointments = $this->CustomerAppointment->appointments($this->Auth->user('id'), $debutExplode, $finExplode);

        $this->set(compact('planning', 'intervalle', 'appointments'));
    }

    //Permet d'afficher les présentations d'un voyant
    public function presentations(){

        //On récupère les présentations, les langues
        $this->loadModel('Lang');

        //On récupère les ids des langues parlées
        $this->User->id = $this->Auth->user('id');
        $userLangs = $this->User->field('langs');
        $userLangs = explode(',', $userLangs);

        $datas = $this->Lang->find('all',array(
            'fields' => array('UserPresentLang.texte','UserPresentValidation.texte', 'Lang.id_lang', 'Lang.name', 'Lang.language_code'),
            'conditions' => array('Lang.id_lang' => $userLangs),
            'joins' => array(
                array('table' => 'user_present_lang',
                      'alias' => 'UserPresentLang',
                      'type' => 'left',
                      'conditions' => array(
                          'UserPresentLang.lang_id = Lang.id_lang',
                          'UserPresentLang.user_id = '.$this->Auth->user('id')
                      )
                ),
                array('table' => 'user_present_validations',
                      'alias' => 'UserPresentValidation',
                      'type' => 'left',
                      'conditions' => array(
                          'UserPresentValidation.etat = 0',
                          'UserPresentValidation.user_id = '.$this->Auth->user('id'),
                          'UserPresentValidation.lang_id = Lang.id_lang'
                      )
                )
            ),
            'order' => 'Lang.id_lang ASC',
            'recursive' => -1,
        ));

        foreach($datas as $val){
            $presentations[$val['Lang']['id_lang']] = $val;
        }
        $this->set(compact('presentations'));
    }

    //Historique des communications
    public function history(){
        $this->_history('agent');
    }

	public function historylostcall(){

		$page = 1;
        if(isset($this->params['page']))
            $page = $this->params['page'];

       //On récupére toutes les communications
        $this->loadModel('UserPenality');
        $this->Paginator->settings = array(
            'fields' => array('UserPenality.id', 'Callinfo.customer', 'Callinfo.timestamp', 'Callinfo.callerid', 'UserPenality.is_view'),
            'conditions' => array(
                'UserPenality.user_id' => $this->Auth->user('id'),
                'UserPenality.callinfo_id !=' => NULL,
            ),
			 'joins' => array(
                array(
                    'table' => 'call_infos',
                    'alias' => 'Callinfo',
                    'type'  => 'left',
                    'conditions' => array('Callinfo.callinfo_id = UserPenality.callinfo_id')
                ),
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
			'page' => $page,
            'limit' => 15
        );

        $calls = $this->Paginator->paginate($this->UserPenality);

		foreach($calls as &$comm){
				if($comm['Callinfo']['customer']){
					$client_sql = $this->User->find('first', array(
						'fields' => array('User.firstname'),
						'conditions' => array('User.personal_code' => $comm['Callinfo']['customer']),
						'recursive' => -1
					));

					if($client_sql['User']['firstname']){
						$comm['User'] = $client_sql['User'];
					}
				}
			}


        $this->set(compact('calls'));
    }

	public function historylostchat(){

		$page = 1;
        if(isset($this->params['page']))
            $page = $this->params['page'];

       //On récupére toutes les communications
        $this->loadModel('UserPenality');
        $this->Paginator->settings = array(
            'fields' => array('UserPenality.id','UserPenality.is_view', 'Chat.from_id', 'Chat.date_start', 'Chat.status', 'User.firstname'),
            'conditions' => array(
                'UserPenality.user_id' => $this->Auth->user('id'),
                'UserPenality.tchat_id !=' => NULL,
            ),
			 'joins' => array(
                array(
                    'table' => 'chats',
                    'alias' => 'Chat',
                    'type'  => 'left',
                    'conditions' => array('Chat.id = UserPenality.tchat_id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Chat.from_id')
                )
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
			'page' => $page,
            'limit' => 15
        );

        $chats = $this->Paginator->paginate($this->UserPenality);

        $this->set(compact('chats'));
    }

	public function historylostemail(){

		$page = 1;
        if(isset($this->params['page']))
            $page = $this->params['page'];

       //On récupére toutes les communications
        $this->loadModel('UserPenality');
        $this->Paginator->settings = array(
            'fields' => array('UserPenality.id','UserPenality.is_view', 'Message.from_id', 'Message.date_add', 'Message.etat', 'User.firstname'),
            'conditions' => array(
                'UserPenality.user_id' => $this->Auth->user('id'),
                'UserPenality.message_id !=' => NULL,
            ),
			 'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'Message',
                    'type'  => 'left',
                    'conditions' => array('Message.id = UserPenality.message_id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Message.from_id')
                )
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
			'page' => $page,
            'limit' => 15
        );

        $messages = $this->Paginator->paginate($this->UserPenality);

        $this->set(compact('messages'));
    }
    
	public function historylostwebcam() {
	    
	    }
	    
	public function historylostsms() {
	    
	    }
    

    public function chat_history(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

            //On va chercher les messages du chat
            $this->loadModel('ChatMessage');
            $messages = $this->ChatMessage->find('all', array(
                'fields' => array('ChatMessage.*', 'User.firstname'),
                'conditions' => array('ChatMessage.chat_id' => $requestData['param']),
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array('User.id = ChatMessage.user_id')
                    )
                ),
                'order' => 'ChatMessage.date_add asc',
                'recursive' => -1
            ));

            $this->layout = '';

            //Si aucun messages
            if(empty($messages)){
                $this->set(array('title' => __('Chat'), 'content' => __('L\'historique de la conversation est introuvable.'), 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('return' => false, 'html' => $response->body()));
            }

			//check if customer picture
			$picture = '';
			$folder = new Folder(Configure::read('Site.pathChatArchiveAdmin').DS.$requestData['param'],true,0755);
			if(is_dir($folder->path)){
				$files = array_diff(scandir($folder->path), array('.','..'));
				foreach ($files as $file) {
					$picture .= '<a href="'.DS.Configure::read('Site.pathChatArchive').DS.$requestData['param'].DS.$file.'" class="chat_picture"><span class="icon_photo"></span></a>';
				}
			}

            $this->set(array('messages' => $messages,'picture' => $picture, 'isAjax' => true));
            $response = $this->render();
            $this->set(array('title' => __('Chat'), 'content' => $response->body(), 'button' => 'Fermer'));
            $response = $this->render('/Elements/modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        //On récupére toutes les communications
        $this->loadModel('Chat');
        $this->Paginator->settings = array(
            'fields' => array('Chat.id', 'Chat.from_id', 'Chat.consult_date_start', 'User.firstname'),
            'conditions' => array(
                'Chat.to_id' => $this->Auth->user('id'),
                'Chat.date_end !=' => null,
                'Chat.consult_date_start !=' => null,
                'Chat.consult_date_end !=' => null
            ),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Chat.from_id')
                )
            ),
            'order' => 'Chat.consult_date_start desc',
            'recursive' => -1,
            'limit' => 15
        );

        $chats = $this->Paginator->paginate($this->Chat);

        $this->set(compact('chats'));
    }

    //Affiche de la messagerie
    public function mails($idMail = 0){
		ini_set("memory_limit",-1);
        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        $this->loadModel('Message');

        if($this->request->is('post')){
            $this->answerMail();
        }

        //Messages privés ??
       /* if(isset($this->params->query['private'])  && $this->params->query['private']){
            $mails = $this->Message->getDiscussion($user['id'], false, false, true);
            $conditions = $this->Message->getConditions($user['id'], false, false, true);
        }
        else{
            $mails = $this->Message->getDiscussion($user['id']);
            $conditions = $this->Message->getConditions($user['id']);
        }*/

       /* $this->Paginator->settings = array(
            'conditions'    => $conditions,
            'paramType'     => 'querystring',
            'limit'         => Configure::read('Site.limitMessagePage')
        );

        $this->Paginator->paginate($this->Message);*/

        //On crée les différentes pages
       /* $pages = array_chunk($mails, Configure::read('Site.limitMessagePage'));

        $page = 0;
        if(isset($this->params->query['page']))
            $page = $this->params->query['page']-1;

        if(isset($pages[$page]))
            $mails = $pages[$page];
        else
            $mails = array();*/
		//Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

		$firstConditions = array(
                'Message.deleted' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $user['id']),
                    array('Message.to_id' => $user['id'], 'Message.etat !=' => 2)
                )
            );

		 if(!isset($this->params->query['private']))
                $firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 0));
            else{
                //Les discussions privés
                $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
            }

		 $theConditions = array_merge($firstConditions, array('Message.etat' => 0));

		$mails_noread = $this->Message->find('all',array(
            'fields' => array('Message.id','Message.date_add','Message.from_id','Message.to_id','Message.content','Message.etat','LastMessage.id','LastMessage.date_add','LastMessage.from_id','LastMessage.to_id','LastMessage.content','LastMessage.etat', 'FirstMessage.id','FirstMessage.date_add','FirstMessage.from_id','FirstMessage.to_id','FirstMessage.content','FirstMessage.etat','From.firstname as pseudo','((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder'),
            'conditions' => $theConditions,
            'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'LastMessage',
                    'type'  => 'left',
					 'conditions' => array('LastMessage.parent_id = Message.id'
										   )
                ),
				array(
                    'table' => 'messages',
                    'alias' => 'FirstMessage',
                    'type'  => 'left',
					 'conditions' => array('FirstMessage.id = Message.id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'From',
                    'type'  => 'left',
                    'conditions' => array(
						'From.id = Message.from_id'
					)
                )
            ),
            'order' => 'dateorder desc',
            'recursive' => -1,
            'limit' => -1
        ));
		$theConditions = array();
		 $theConditions = array_merge($firstConditions, array('Message.etat >=' => 1));

		$this->Paginator->settings = array(
            'fields' => array('Message.id','Message.date_add','Message.from_id','Message.to_id','Message.content','Message.etat','LastMessage.id','LastMessage.date_add','LastMessage.from_id','LastMessage.to_id','LastMessage.content','LastMessage.etat', 'FirstMessage.id','FirstMessage.date_add','FirstMessage.from_id','FirstMessage.to_id','FirstMessage.content','FirstMessage.etat','From.firstname as pseudo','((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder'),
            'conditions' => $theConditions,
            'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'LastMessage',
                    'type'  => 'left',
					 'conditions' => array('LastMessage.parent_id = Message.id'
										   )
                ),
				array(
                    'table' => 'messages',
                    'alias' => 'FirstMessage',
                    'type'  => 'left',
					 'conditions' => array('FirstMessage.id = Message.id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'From',
                    'type'  => 'left',
                    'conditions' => array(
						'From.id = Message.from_id'
					)
                )
            ),
            'order' => 'dateorder desc',
            'recursive' => -1,
            'limit' => Configure::read('Site.limitMessagePage')
        );

        $mails = $this->Paginator->paginate($this->Message);

		$mails_noread = array_merge($mails_noread, $mails);

		$mails = $mails_noread;


		foreach($mails as &$mmail){
			if(!$mmail['LastMessage']['id'])$mmail['LastMessage'] = $mmail['FirstMessage'];
			$mmail['LastMessage']['content'] = htmlentities($mmail['LastMessage']['content']);
		}

        $dataNoRead['mailConsult'] = ($this->Message->hasNoReadMail($user['id']) > 0 ?true:false);
        $dataNoRead['mailPrivate'] = ($this->Message->hasNoReadMail($user['id'], true) > 0 ?true:false);

        //L'id de l'user
        $id = $user['id'];
        $this->set(compact('mails', 'id', 'dataNoRead', 'idMail'));
    }

    public function getMails(){
       //$this->_getMails('agents');

	   if($this->request->is('ajax')){
		   $id = $this->Auth->user('id');

            $requestData = $this->request->data;

            if(isset($requestData['isAjax']) && $requestData['isAjax']){
                $this->loadModel('Message');

					$this->paginatorParams();

		$firstConditions = array(
                'Message.deleted' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $id),
                    array('Message.to_id' => $id, 'Message.etat !=' => 2)
                )
            );

                //Selon le type d'email demandé

                switch($requestData['param']){
                    case 'message' :
                        //Uniquement les messages de consultations
                        $firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 0));
                        $param = 'message';
                        break;
                    case 'private' :
                        //Uniquement les messages privés
                        $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
                        $param = 'private';
                        break;
                    case 'archive' :
                        //Uniquement les messages archivés
						if($requestData['archive'] === 'private'){
							$firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive >=' => 1));
						}else{
							$firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive >=' => 1));
						}

                        $param = 'archive';

                        break;
                    default :
                        //Par défaut les messages de consultations
                         $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
                        $param = 'message';
                }


		$this->Paginator->settings = array(
            'fields' => array('Message.id','Message.date_add','Message.from_id','Message.to_id','Message.content','Message.etat','LastMessage.id','LastMessage.date_add','LastMessage.from_id','LastMessage.to_id','LastMessage.content','LastMessage.etat', 'FirstMessage.id','FirstMessage.date_add','FirstMessage.from_id','FirstMessage.to_id','FirstMessage.content','FirstMessage.etat','From.firstname as pseudo','((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder'),
            'conditions' => $firstConditions,
            'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'LastMessage',
                    'type'  => 'left',
					 'conditions' => array('LastMessage.parent_id = Message.id'
										   )
                ),
				array(
                    'table' => 'messages',
                    'alias' => 'FirstMessage',
                    'type'  => 'left',
					 'conditions' => array('FirstMessage.id = Message.id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'From',
                    'type'  => 'left',
                    'conditions' => array(
						'From.id = Message.from_id'
					)
                )
            ),
            'order' => 'dateorder desc',
            'recursive' => -1,
            'limit' => Configure::read('Site.limitMessagePage')
        );

        $mails = $this->Paginator->paginate($this->Message);
		foreach($mails as &$mmail){
			if(!$mmail['LastMessage']['id'])$mmail['LastMessage'] = $mmail['FirstMessage'];
			$mmail['LastMessage']['content'] = htmlentities($mmail['LastMessage']['content']);
		}

		//Message privé ou pas
                        if(isset($requestData['archive'])){
                            $typeArchive = ($requestData['archive'] === 'private' ?0:1);
                            foreach($mails as $indice => $mail){
                                if($mail['Message']['private'] == $typeArchive || $mail['Message']['private'] == 2)
                                    unset($mails[$indice]);
                            }
                        }



                $this->layout = '';

                $this->set(compact('mails', 'id'));
                if(isset($requestData['onlyBlockMail']))
                    $this->set('onlyBlockMail', true);
                $this->set(array('controller' => 'agents'));
                $response = $this->render('/Elements/mails');

                $this->jsonRender(array('return' => true, 'html' => $response->body(), 'param' => $param));
            }

            $this->jsonRender(array('return' => false));
        }


    }

    public function update_mail(){
        $this->_update_mail();
    }

    //Lors d'une réponse d'un mail
    private function answerMail(){
        //Les datas
        $requestData = $this->request->data;

        //Les champs du formulaire
        $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('mail_id', 'content'), array('mail_id', 'content'));
        if($requestData['Agent'] === false){
            //$this->Session->setFlash(__('Erreur avec le formulaire de réponse'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'mails'));
        }

        $infoMessage = $this->Message->find('first',array(
            'fields' => array('User.id', 'User.lang_id', 'Message.private', 'Message.to_id', 'Message.from_id', 'Message.etat'),
            'conditions' => array('Message.id' => $requestData['Agent']['mail_id'], 'Message.deleted' => 0, 'Message.archive' => 0, 'Message.parent_id' => null, 'OR' => array(array('Message.to_id' => $this->Auth->user('id')), array('Message.to_id' => Configure::read('Admin.id')))),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'inner',
                    'conditions' => array(
                        'User.id = Message.from_id',
                        'User.active = 1',
                        'User.deleted = 0'
                    )
                )
            ),
            'recursive' => -1
        ));
		if(empty($infoMessage)){//message type relance
			$infoMessage = $this->Message->find('first',array(
            'fields' => array('User.id', 'User.lang_id', 'Message.private', 'Message.to_id', 'Message.from_id', 'Message.etat'),
            'conditions' => array('Message.id' => $requestData['Agent']['mail_id'], 'Message.deleted' => 0, 'Message.archive' => 0, 'Message.parent_id' => null, 'OR' => array(array('Message.from_id' => $this->Auth->user('id')), array('Message.to_id' => Configure::read('Admin.id')))),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'inner',
                    'conditions' => array(
                        'User.id = Message.to_id',
                        'User.active = 1',
                        'User.deleted = 0'
                    )
                )
            ),
            'recursive' => -1
        ));
		}
        //Check sur le client-------------------------------------------------------------------------
        //Si pas de client ou pas de message
        if(empty($infoMessage)){
            $this->Session->setFlash(__('Le client demandé n\'existe pas ou il n\'est plus actif ou vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'mails'));
        }

		if($infoMessage['Message']['etat'] == 3){
            $this->Session->setFlash(__('Le délai de réponse est dépassé, vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'mails'));
        }
		//check si dernier message fil discussion est perime
		$infoMessageFil = $this->Message->find('first',array(
            'fields' => array('Message.etat'),
            'conditions' => array('Message.parent_id' => $requestData['Agent']['mail_id'], 'Message.deleted' => 0, 'Message.archive' => 0, 'Message.to_id' => $this->Auth->user('id')),
            'recursive' => -1,
			'order' => 'Message.id desc'
        ));
		if($infoMessageFil && $infoMessageFil['Message']['etat'] == 3){
            $this->Session->setFlash(__('Le délai de réponse est dépassé, vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'mails'));
        }

        //L'email du client
        $emailUser = $this->User->field('email', array('id' => $infoMessage['User']['id']));

		$etat = 0;
		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
					'conditions' => array(
					)
		));
		foreach($filtres as $filtre){
			if(substr_count(strtolower($requestData['Agent']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
				$etat = 2;
		}

        //On save (envoie) le mail
        $this->Message->create();
        if($this->Message->save(array(
            'parent_id' => $requestData['Agent']['mail_id'],
            'from_id' => $this->Auth->user('id'),
            'to_id' => ($infoMessage['Message']['to_id'] == Configure::read('Admin.id') ?Configure::read('Admin.id'):$infoMessage['User']['id']),
            'private'   => $infoMessage['Message']['private'],
            'content' => $this->remove_emoji($requestData['Agent']['content']),
			'etat'          => $etat,
			'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')

        ))){
			if($etat == 2){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						//Les datas pour l'email
						$datasEmail = array(
							'content' => __('Un Mail agent requiert check terme interdit.') ,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com'
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com',__('Mail agent terme interdit'),'default',$datasEmail);
					}
            //Si c'est pas une discussion avec l'admin, donc normalement un client
            if($infoMessage['Message']['to_id'] != Configure::read('Admin.id') && $etat == 0){
                //Envoi de l'email
                //Les datas pour l'email
                $datasEmail = array('pseudo' => $this->Auth->user('pseudo'), 'urlMail' => Router::url(array('controller' => 'accounts', 'action' => 'mails', '?' => ($infoMessage['Message']['private'] == 0 ?false:array('private' => true))),true));
                //On envoie l'email
                //$this->sendEmail($emailUser,'Une réponse vous attend.','answer_mail',array('param' => $datasEmail));

                $prm = array();
                if ($infoMessage['Message']['private'] != 0)
                    $prm['private'] = 1;
                $prm['nosubscribe'] = 1;
                $this->sendCmsTemplateByMail(178, $infoMessage['User']['lang_id'], $emailUser, array(
                    'PSEUDO_AGENT' => $this->Auth->user('pseudo'),
                    'LIEN_REPONSE' => Router::url(array('controller' => 'accounts', 'action' => 'mails', '?' => $prm),true)
                ));
            }
			if($infoMessage['Message']['to_id'] == Configure::read('Admin.id')){
				$bodymail = 'Consulter : <a href="http://fr.spiriteo.com/admin/admins/mails">http://fr.spiriteo.com/admin/admins/mails</a>';
					$this->sendEmail(
						'contact@talkappdev.com',
						__('Vous avez reçu un nouveau message dans la boite " Contact " de Spiriteo'),
						'default',array('content' => $bodymail)
					);
			}

            $this->Session->setFlash(__('Votre réponse a été envoyée.'),'flash_success');
        }else
            $this->Session->setFlash(__('Erreur durant l\'envoi du mail.'),'flash_error');
		if ($infoMessage['Message']['private'] != 0)
        $this->redirect(array('controller' => 'agents', 'action' => 'mails', '?' => array('private'=>1)));
		else
		$this->redirect(array('controller' => 'agents', 'action' => 'mails'));
    }

    public function readMail(){
        $this->_readMail($this->Auth->user('id'), 'agents');
    }

    public function answerForm(){
        $this->_answerForm($this->Auth->user('id'), 'agents');
    }

    public function downloadAttachment($name){
        return $this->_downloadAttachment($name);
    }


	public function mails_relance(){
		ini_set("memory_limit",-1);
		$this->loadModel('UserCreditLastHistory');
		$this->loadModel('User');
		$this->loadModel('Message');
		$this->loadModel('Notes');
		$this->loadModel('Review');

        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));


		if($this->request->is('post')){
            $requestData = $this->request->data;

			$this->loadModel('Message');
			$list_client = explode(',', $requestData["Agent"]['listing_client']);

			//if(count($list_client) > 1) $requestData['Agent']['bonjour'] = 'Bonjour CLIENT,';
			if(!$requestData['Agent']['title']) $requestData['Agent']['title'] = 'Un nouveau message Spiriteo';
			$is_send = 0;
			foreach($list_client as $client_id){

				$client_bcl = $this->User->find('first',array(
						'conditions' => array('id' => $client_id),
						'recursive' => -1
					));
				//if(count($list_client) > 1)
				//$requestData['Agent']['bonjour'] = 'Bonjour '.$client_bcl['User']['firstname'].',';//force prenom client

				$contenu = $requestData['Agent']['title'].'<!---->';
				$contenu .= $requestData['Agent']['bonjour'].'<!---->';
				$contenu .= $requestData['Agent']['content'].'<!---->';
				$contenu .= $requestData['Agent']['signature'];
				$this->Message->create();
        	    $is_send = $this->Message->save(array(
					'parent_id' => NULL,
					'from_id'   => $this->Auth->user('id'),
					'to_id'     => $client_id,
					'private'   => 2,
					'content'   => $contenu,
					'etat'      => 2,
					'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')

				));
			}
			if($is_send){
				$this->loadModel('UserLevel');
				$list_admins = array();
				/*$admins = $this->UserLevel->find('all', array(
						'conditions' => array('UserLevel.level' => 'admin'),
						'recursive' => -1
					));
				
				foreach($admins as $admin){
					array_push($list_admins, $admin['UserLevel']['user_id']);
				}*/
				$admins = $this->UserLevel->find('all', array(
							'conditions' => array('UserLevel.level' => 'moderator'),
							'recursive' => -1
						));
				foreach($admins as $admin){
					array_push($list_admins, $admin['UserLevel']['user_id']);
				}
				$list_admins = array_unique($list_admins);
				$domain = Router::url('/', true);
				foreach($list_admins as $admin_id){

					$ad = $this->User->find('first',array(
								'conditions' => array('id' => $admin_id, 'active' =>1, 'deleted'=>0),
								'recursive' => -1,
							));
					if($ad['User']['email'])
						$this->sendCmsTemplateByMail(460, 1, $ad['User']['email'], array(
						));

				}
				$this->Session->setFlash(__('Votre relance est en cours d\'envoie.'),'flash_success');
			}else
				$this->Session->setFlash(__('Erreur lors de l\'envoi de votre relance.'),'flash_error');

			$this->redirect(array('controller' => 'agents', 'action' => 'mails_relance'),false);


		}


		$lastCom = $this->UserCreditLastHistory->find('all',array(
						'conditions' => array('agent_id' => $user['id'], 'sessionid !=' => '' ),
						'recursive' => -1,
						'order'     => array('date_start' => 'DESC'),
						'limit' => 250
					));

		$clients = array();

		foreach($lastCom as $com){

			if(!is_array($clients[$com['UserCreditLastHistory']['users_id']])){

			$client = $this->User->find('first',array(
							'fields' => 'User.*, Relance.date_relance',
							'conditions' => array('User.id' => $com['UserCreditLastHistory']['users_id']),
							'recursive' => -1,
							'joins' => array(
								array('table' => 'relances',
									  'alias' => 'Relance',
									  'type' => 'left',
									  'conditions' => array('Relance.user_id = User.id', 'Relance.agent_id = '.$user['id'])
								),
							),
						));


			if(!substr_count($client['User']['firstname'],'AUDIOTEL')){
				$lastMes = $this->Message->find('all',array(
							'conditions' => array('from_id' => $user['id'],'to_id' => $com['UserCreditLastHistory']['users_id'], 'date_add >' => date('Y-m-00 00:00:00'), 'content LIKE' => '%<!---->%', 'etat !=' => 3, 'archive' => 0),
							'recursive' => -1,
							'order'     => array('date_add' => 'DESC')
						));


				$last_relance = '';
				$n_c = 1;
				foreach($lastMes as $mess ){
					if($mess['Message']['private'] == 2)
								$last_relance .= 'en cours d\'envoi';
								else
					$last_relance .= CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mess['Message']['date_add']),'%d/%m/%y %Hh%M');
					if($n_c == 1 && count($lastMes) > 1) $last_relance .= '<br />';
					$n_c ++;
				}


				/*$messages = $this->Message->getDiscussion($user['id'], false, true);
				$messs = array();
				foreach($messages as $mess){

					if($mess['Message']['from_id'] == $com['UserCreditLastHistory']['users_id'] || $mess['Message']['to_id'] == $com['UserCreditLastHistory']['users_id']){
						array_push($messs,$mess);break;
					}
				}*/

				$phone_note_title = $client['User']['firstname'];

				/*$note = $this->Notes->find('first',array(
							'conditions' => array('id_client' => $com['UserCreditLastHistory']['users_id'], 'id_agent' => $user['id']),
							'recursive' => -1,

						));
				if(!count($note)){


					$chatid = 0;
					$callid = 0;
					$mailid = 0;
					if($com['UserCreditLastHistory']['media'] == 'chat'){
						$chatid = $com['UserCreditLastHistory']['sessionid'];
						$idclient = $com['UserCreditLastHistory']['users_id'];
					}
					if($com['UserCreditLastHistory']['media'] == 'phone'){
						 $this->loadModel('CallInfo');
							 $callinfo = $this->CallInfo->find('first',array(
								'fields' => array('CallInfo.callinfo_id,CallInfo.callerid'),
								'conditions' => array('sessionid' => $com['UserCreditLastHistory']['sessionid']),
								'recursive' => 0
							));
					      $callid = $callinfo['CallInfo']['callinfo_id'];
						$idclient = $callinfo['CallInfo']['callerid'];
					}
					if($com['UserCreditLastHistory']['media'] == 'email'){
						$mailid = 0;
					}
					$info = array(
								'id_agent'   => $this->Auth->user('id'),
								'id_client'     => $com['UserCreditLastHistory']['users_id'],
								'client'   => $phone_note_title,
								'callinfo_id'     => $callid,
								'tchat_id'     => $chatid,
								'note'   => '',
								'date_crea'      => date('Y-m-d H:i:s'),
								'date_upd'      => date('Y-m-d H:i:s')
							);
					if($callid || $chatid || $mailid){
							$this->Notes->create();
							$this->Notes->save($info);

					$note = $this->Notes->find('first',array(
							'conditions' => array('id_client' => $com['UserCreditLastHistory']['users_id'], 'id_agent' => $user['id']),
							'recursive' => -1,

						));
					}
				}*/

				/*$reviews = $this->Review->find('count',array(
							'conditions' => array('user_id' => $com['UserCreditLastHistory']['users_id'], 'agent_id' => $user['id'], 'status' => 1, 'parent_id' => NULL),
							'recursive' => -1,

						));*/


					$customer = array();
					$customer['pseudo'] = $client['User']['firstname'];
					$customer['user_id'] = $client['User']['id'];
					$customer['agent_id'] = $user['id'];
					$customer['last_relance'] = $last_relance;
					$customer['message'] = count($lastMes);
					$customer['reviews'] = count($reviews);
					$customer['last_com'] = $com['UserCreditLastHistory']['date_start'];
					$customer['note_id'] = $note['Notes']['id'];
					$customer['date_relance'] = $client['Relance']['date_relance'];
					$clients[$com['UserCreditLastHistory']['users_id']] = $customer;
				}
			}

		}
        $agent_pseudo = $user['pseudo'];
		$photo = $this->mediaAgentExist($user['agent_number'],'Image');
            //Pas de photo, photo par défaut
            if($photo === false)
                $agent_photo = '/'.Configure::read('Site.defaultImage');
            else
                $agent_photo = '/'.$photo;

		$refus = $this->Message->find('all',array(
							'fields' => array('Message.*','User.firstname'),
							'conditions' => array('from_id' => $user['id'], 'private' => 2, 'etat' => 3, 'archive' => 0),
							'recursive' => -1,
							'order'     => array('date_add' => 'ASC'),
							'joins' => array(
								array('table' => 'users',
									  'alias' => 'User',
									  'type' => 'left',
									  'conditions' => array(
										  'User.id = Message.to_id',
									  )
								)
							),
						));
		$nb_refus = count($refus);

        $this->set(compact('clients', 'agent_pseudo', 'agent_photo', 'refus', 'nb_refus'));
    }


    
    // Mes modes de consultation
    
    public function mod_consult(){
	//echo"<br> agent mod_consult";exit;
	}


    //Fenêtre de traitement en cours
    public function modalLoading(){
        if($this->request->is('ajax')){
            $this->layout = '';
            $content = '<p class="txt-center"><img src="/'. Configure::read('Site.loadingImage').'"/></p>';
            $this->set(array('title' => __('Veuillez patienter'), 'content' => $content));
            $response = $this->render('/Elements/loading_modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    //La modal pour la presentation de l'agent
    public function modalPresentation(){
        if($this->request->is('ajax')){
            $this->layout = 'ajax';
            if(isset($this->request->data['audio']) && !empty($this->request->data['audio']))
                $content = '<audio src="'.$this->request->data['audio'].'" controls autoplay></audio>';
            else
                $content = __('Erreur dans le chargement de la présentation');
            $this->set(array('title' => __('Présentation audio de '.$this->request->data['pseudo']), 'content' => $content, 'button' => __('Fermer')));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function getAgentStatus(){

        if($this->request->is('ajax')){
            if(!isset($this->request->data['agent_number']) || empty($this->request->data['agent_number']))
                $this->jsonRender(array('status' => false));
            $status = $this->getStatus($this->request->data['agent_number']);
            $this->jsonRender(array('status' => $status));
        }
    }

    private function getAgentNumber()
    {
        if ($this->request->params['controller'] == 'api')
            return $this->request->data['agent_number'];
        else
            return $this->Session->read('Auth.User.agent_number');
    }
    /* 15/06/2015 : changement au commentaire */
    public function changeAgentStatus(){
        if($this->request->is('ajax')){
            $agent_number = $this->getAgentNumber();
            $this->layout = '';

			$consult_email = $this->User->field('consult_email', array('id' => $this->Auth->user('id')));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->Auth->user('id')));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->Auth->user('id')));

			$is_block = false;

			if($consult_email == -1 && $consult_chat == -1 && $consult_phone == -1)$is_block = true;

			if($consult_email > 0 || $consult_chat > 0 || $consult_phone > 0 )$is_block = false; else $is_block = true;

			if(!$is_block)
            	$result = $this->setStatus($agent_number,$this->request->data['status']);
			else{
				 $this->jsonRender(array('status' => 'empty'));
			}
            //$result = $this->setStatus($this->Session->read('Auth.User.agent_number'),$this->request->data['status']);
			//$this->Session->setFlash(__('Votre statut a bien été changé.'),'flash_success');

			$this->loadModel('UserConnexion');
							//if($this->request->data['status'] != 'unavailable'){

								$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => session_id(),
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'status'			=> $this->request->data['status'],
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
								$this->UserConnexion->create();
								$this->UserConnexion->save($connexion);
							//}




            $this->jsonRender((is_array($result)
                ?$result
                :array('status' => $result)
            ));

        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    //Modal pour le crop de l'image
    public function modalPhotoAgent(){
        if($this->request->is('ajax')){
            $this->layout = 'ajax';
            if(isset($this->request->data['image']) && !empty($this->request->data['image']))
                $content = '<img src="'. $this->request->data['image'] .'" id="cropImg">';
            else
                $content = __('Erreur dans le chargement de votre image');
            $this->set(array('title' => __('Redimensionner votre photo'), 'content' => $content, 'button' => __('Annuler')));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

    }

    private function explodeDate($date){
        if(empty($date) || !is_string($date)) return array();

        $transit = explode('-',$date);
        //INFOS :  0: jour, 1: mois, 2: année
        $data['A'] = $transit[2];
        $data['M'] = $transit[1];
        $data['J'] = $transit[0];

        return $data;
    }

    private function getStatus($agent_number){
        $status = $this->User->field('agent_status',array(
            'agent_number'  => $agent_number,
            'role'          => 'agent',
            'active'        => 1,
            'deleted'       => 0
        ));

        return $status;
    }

    /**
     * @param int       $agent_number   Le code personnel de l'agent
     * @param string    $status         Le status demandé
     * @param bool      $is_api         Appel depuis l'api ou pas
     * @return array|bool
     */
    private function setStatus($agent_number, $status, $is_api=false){
        //On vérifie que le status demandé est un status valide
        if(!in_array($status,$this->nx_allowed_agent_statuses)){
            if($is_api)
                return array('response_code' => 16, 'response' => false);
            else
                return false;
        }

        //Le status actuel
        $current_status = $this->getStatus($agent_number);
        //Si aucun status renvoyé
        if(!$current_status){
            if($is_api)
                return array('response_code' => 15, 'response' => false);
            else
                return false;
        }
        //Si le status demandé est le même que celui actuel
        if($status === $current_status){
            if($is_api)
                return array('response_code' => 18, 'response' => false);
            else
                return false;
        }

        //On affecte l'id de l'agent
        $this->User->id = $this->User->field('id',array(
            'agent_number'  => $agent_number,
            'role'          => 'agent',
            'active'        => 1,
            'deleted'       => 0
        ));
        //On charge le model pour l'historique
        $this->loadModel('UserStateHistory');
        //Est-il consultable par tel
        $consultTel = $this->User->field('consult_phone');
        //Cas api ou extranet
        switch($is_api){
            case true :
                if(!$this->User->saveField('agent_status', $status))
                    return array('response_code' => 17, 'response' => false);
                //On ajoute le changement dans la table historique
                $this->UserStateHistory->create();
                $this->UserStateHistory->save(array(
                    'user_id' => $this->User->id,
                    'state' => $status
                ));
                break;
            case false:
                //Si l'agent est occupé ou si il veut se mettre en occupé
                if(strcmp($current_status, 'busy') == 0 || strcmp($status,'busy') == 0)
                    $status = $current_status;
                else{
                    if(!$this->User->saveField('agent_status',$status))
                        return false;
                    //On charge la class de l'api
                    $api = new Api();
                    //S'il est disponible et consultable par téléphone, on le connecte à la plateforme
                    if(strcmp($status, 'available') == 0 && $consultTel == 1){
                        $result = $api->connectAgent($agent_number);
                        //S'il y a eu une erreur, connection avec la plateforme Tel non établie
                        if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0))
                            $apiConnect = __('Echec de la connection avec la plateforme téléphonique.');
                    }
                    //S'il est indisponible on le déconnecte de la plateforme
                    elseif(strcmp($status, 'unavailable') == 0){
                        $result = $api->deconnectAgent($agent_number);
                        //S'il y a eu une erreur, déconnection avec la plateforme Tel
                        if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0))
                            $apiDeconnect = __('La déconnexion de la plateforme téléphonique a échoué.');
                    }
                    //On ajoute le changement dans la table historique
                    $this->UserStateHistory->create();
                    $this->UserStateHistory->save(array(
                        'user_id' => $this->User->id,
                        'state' => $status
                    ));
                }
                break;
        }

        //On alerte les clients qui l'ont demandé si le statut est available
        if ($status == 'available'){
            $alerts = new AlertsController();
            $alerts->alertUsersForUserAvailability($agent_number);
        }

        if($is_api)
            return array('response_code' => 0, 'response' => true);
        else{
            if(isset($apiConnect))
                return array('status' => $status, 'apiConnect' => $apiConnect);
            elseif(isset($apiDeconnect))
                return array('status' => $status, 'apiDeconnect' => $apiDeconnect);
            return $status;
        }
    }

//---------------------------------------------------------------------------------------------ADMIN----------------------------------------------------------------------------------------------------------------------
    public function admin_index(){
		ini_set("memory_limit",-1);
        //On récupère les datas pour la vue
		$users = $this->_adminIndex('Agents');
        $this->set(array('users' => $users));
    }

    public function admin_edit($id){
        //On récupère les données de l'agent
        $agent = $this->_adminEdit($id,
            array('firstname', 'lastname','pseudo','email','phone_number','phone_number2','phone_mobile','phone_api_use','address','postalcode','city','country_id', 'siret','vat_num_spirit','society_type_id','societe_statut','societe','societe_adress','societe_adress2','societe_cp','societe_ville','societe_pays','passwd','passwd2','creditMail','photo','crop', 'texte', 'lang_id', 'order_cat', 'mail_price', 'flag_new', 'date_new', 'nb_consult_ajoute', 'subscribe_mail','alert_phone','alert_sms','alert_mail','alert_night','absence','countries','langs','categories','stripe_account','invoice_vat_id','belgium_save_num','belgium_society_num','canada_id_hst','spain_cif','luxembourg_autorisation','luxembourg_commerce_registrar','marocco_ice','marocco_if','portugal_nif','senegal_ninea','senegal_rccm','tunisia_rc','vat_num_proof'),
            array('firstname','lastname','email','pseudo','country_id', 'phone_number'),
            'Agent');
		$this->loadModel('UserPresentLang');
		$presentations = $this->UserPresentLang->find('all',array(
                'conditions' => array('user_id' => $id),
                'recursive' => -1
            ));

		// update api phone_number quand il y'a new valeur
        $phone_api_use = $this->User->field('phone_api_use', array('id' => $id));

        if(!empty($agent['User']['phone_api_use']) && $phone_api_use != $agent['User']['phone_api_use']){
            //on actualise l'api
            $api = new Api();
            $api->updateAgent($this->User->field('agent_number', array('id' => $id)), $agent['User']['phone_api_use']);
        }


        $agent['User']['texte'] = $presentations[0]['UserPresentLang']['texte'];
		$agent['User']['vat_num_spirit'] = $agent['User']['vat_num'] ;
		$this->loadModel('CostAgent');
		$conditions = array(
			'CostAgent.id_agent' => $id,
			);

		$cost = $this->CostAgent->find('first',array('conditions' => $conditions));
		$id_cost = $cost['CostAgent']['id_cost'];
		$nb_minutes = $cost['CostAgent']['nb_minutes'];

		$countries = explode(',', $agent['User']['countries']);
        $langs = explode(',', $agent['User']['langs']);
		$this->loadModel('Lang');
		$this->loadModel('Country');
		$this->loadModel('CategoryUser');
		$this->loadModel('CategoryLang');
		$this->loadModel('SocietyType');
		$this->loadModel('InvoiceVat');
		$select_langs = $this->Lang->getLang(true);
		$select_countries_sites = $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang'));
		$select_society_type = $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang'));

    if($agent['User']['societe_pays'])$society_country = $agent['User']['societe_pays']; else $society_country = $agent['User']['country_id'];

		$select_invoice_vat = $this->InvoiceVat->getVatForSelect($this->Session->read('Config.id_lang'),$society_country);
		$univers_user = $this->CategoryUser->find('all',array(
                'fields' => array('CategoryUser.category_id'),
                'conditions' => array('user_id' => $agent['User']['id']),
                'recursive' => -1,
                'joins' => array(
                    array('table' => 'category_langs',
                          'alias' => 'CategoryLang',
                          'type' => 'left',
                          'conditions' => array(
                              'CategoryLang.category_id = CategoryUser.category_id',
                              'CategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                          )
                    )
                ),
            ));
		$category_langs = $this->CategoryLang->getCategories($this->Session->read('Config.id_lang'));
		$univers = array();
		foreach($univers_user as $uni){
			array_push($univers,$uni['CategoryUser']['category_id']);
		}
        $this->set(compact('agent','id_cost', 'nb_minutes', 'countries', 'langs', 'select_langs','select_countries_sites','univers','category_langs','select_society_type','select_invoice_vat'));
    }

    public function admin_view($id){
        $agent = $this->User->find('first',array(
            'fields' => array('User.*','UserCountryLang.name','UserCountryLangSociety.name'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent'),
            'joins' => array(
                array('table' => 'user_country_langs',
                      'alias' => 'UserCountryLang',
                      'type' => 'left',
                      'conditions' => array(
                          'UserCountryLang.user_countries_id = User.country_id',
                          'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                      )
                ),
				array('table' => 'user_country_langs',
                      'alias' => 'UserCountryLangSociety',
                      'type' => 'left',
                      'conditions' => array(
                          'UserCountryLangSociety.user_countries_id = User.societe_pays',
                          'UserCountryLangSociety.lang_id = '.$this->Session->read('Config.id_lang')
                      )
                )
            ),
            'recursive' => -1,
        ));

        $presentations = array();
        $univers = array();
        $listData = array();
        $nameField = array();
        $flags = array();
        $pathPhoto = '';
        $pathAudio = array();
		$bonus_agent = array();
		$sponsorships = array();

        if(!empty($agent)){
            //On charge les models
            $this->loadModel('Lang');
            $this->loadModel('UserPresentLang');
            $this->loadModel('CategoryUser');
            $this->loadModel('CategoryLang');
            $this->loadModel('AdminNote');
			$this->loadModel('UserIp');
			$this->loadModel('UserConnexion');
			$this->loadModel('BonusAgent');
			$this->loadModel('Sponsorship');
			$this->loadModel('UserCreditHistory');
			$this->loadModel('Callinfo');
			$this->loadModel('UserPenality');
			$this->loadModel('Survey');
			$this->loadModel('UserDocument');
			$this->loadModel('SocietyType');
			$this->loadModel('UserOrder');

			$this->Callinfo->useTable = 'call_infos';

            //On récupère les présentations
            $presentations = $this->UserPresentLang->find('all',array(
                'conditions' => array('user_id' => $id),
                'recursive' => -1
            ));

            //On récupère la note de l'admin
            $agent['User']['admin_note'] = $this->AdminNote->field('note', array('user_id' => $id));

            //Liste des données à NE PAS afficher
            $listData = array('optin','personal_code','passwd','last_passwd_gen','forgotten_password','deleted','role','credit','id', 'consult_chat', 'consult_email', 'agent_status', 'admin_note', 'valid', 'chat_last_activity', 'limit_credit', 'invoice_vat_id','belgium_save_num','belgium_society_num','canada_id_hst','spain_cif','luxembourg_autorisation','luxembourg_commerce_registrar','marocco_ice','marocco_if','portugal_nif','senegal_ninea','senegal_rccm','tunisia_rc');
            //Nom des champs en format humain
            $nameField = array('firstname' => 'Prenom','lastname' => 'Nom', 'pseudo' => 'Pseudo', 'email' => 'Adresse mail', 'birthdate' => 'Date de naissance', 'address' => 'Adresse', 'postalcode' => 'Code postal',
                               'city' => 'Ville', 'sexe' => 'Sexe', 'country_id' => 'Pays de résidence', 'active' => 'Compte', 'emailConfirm' => 'Email', 'date_add' => 'Inscription', 'date_upd' => 'Dernière modification',
                               'date_lastconnexion' => 'Dernière connexion', 'countries' => 'Visible sur le site', 'langs' => 'Langues parlées', 'agent_number' => 'Code agent', 'has_photo' => 'Photo',
                               'has_audio' => 'Présentation audio', 'has_video' => 'Présentation video', 'siret' => 'Siret', 'society_type_id' => 'Statut', 'societe_statut' => 'Statut autre','vat_num' => 'TVA INTRA','consult_phone' => 'Consultation', 'phone_number' => 'Numéro de téléphone', 'phone_number2' => 'Numéro de téléphone 2','phone_mobile' => 'Numéro de téléphone mobile', 'creditMail' => 'Nombre de crédit pour un mail',
                                'record' => 'Enregistrement téléphonique', 'careers' => 'Parcours professionnel', 'profile' => 'Profil',
                'phone_operator' => 'Opérateur téléphonique',
				'phone_operator2' => 'Opérateur téléphonique fixe 2',
				'phone_operator3' => 'Opérateur téléphonique mobile',
                'date_last_activity' => 'Dernière activité', 'paypal' => 'Email de paiement', 'absence' => 'Infos absence');

            //On récupère les codes des langues
            $flags = $this->Lang->find('list',array(
                'fields' => array('id_lang','language_code'),
                'conditions' => array('active' => 1),
                'recursive' => -1
            ));

			$society_types = $this->SocietyType->find('first',array(
                'fields' => array('name'),
                'conditions' => array('active' => 1, 'id' => $agent['User']['society_type_id']),
                'recursive' => -1
            ));
			if($society_types)
			$agent['User']['society_type_id'] = $society_types['SocietyType']['name'];

            //On stocke le nom du pays de résidence dans User
            $agent['User']['country_id'] = $agent['UserCountryLang']['name'];
          $agent['User']['societe_pays'] = $agent['UserCountryLangSociety']['name'];
            unset($agent['UserCountryLang']);
            //On formate la sortie des données
            foreach($agent['User'] as $key => $val){
                switch ($key){
                    case 'sexe' :
                        $agent['User'][$key] = ($val == 1
                            ?'<span class="badge badge-man">'.__('Homme').'</span>'
                            :'<span class="badge badge-woman">'.__('Femme').'</span>'
                        );
                        break;
                    case 'consult_phone' :
                        $agent['User'][$key] = '';
                        if($val == 1)
                            $agent['User'][$key].= '<span class="icon-phone icon_view_agent" title="'.__('Téléphone').'"></span>';
                        if($agent['User']['consult_email'] == 1)
                            $agent['User'][$key].= '<span class="icon-envelope icon_view_agent" title="'.__('Email').'"></span>';
                        if($agent['User']['consult_chat'] == 1)
                            $agent['User'][$key].= '<span class="icon-comments icon_view_agent" title="'.__('Chat').'"></span>';
                        break;
                    case 'countries' :
                        $idCountries = explode(',',$val);
                        $agent['User'][$key] = '';
                        foreach($idCountries as $idCountry){
                            $agent['User'][$key].= '<span class="country_flags country_'.$idCountry.'"></span>';
                        }
                        break;
                    case 'langs' :
                        $idLangs = explode(',',$val);
                        $agent['User'][$key] = '';
                        foreach($idLangs as $idLang){
							if($flags[$idLang] != 'frb' && $flags[$idLang] != 'frs' && $flags[$idLang] != 'frl' && $flags[$idLang] != 'frc')
                            $agent['User'][$key].= '<span class="lang_flags lang_'.$flags[$idLang].'"></span>';
                        }
                        break;
                    case 'creditMail' :
                        $agent['User'][$key] = (empty($val) ?Configure::read('Site.creditPourUnMail'):$val);
                        break;
                    case 'birthdate' :
                    case 'date_add' :
                    case 'date_upd' :
                        if(empty($val)){
                            $agent['User'][$key] = __('N/D');
                            continue;
                        }
                        $agent['User'][$key] = CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$val), '%d %B %Y');
                        break;
                }
            }

            //On récupère les noms des univers de l'agent
            $univers = $this->CategoryUser->find('all',array(
                'fields' => array('CategoryLang.name'),
                'conditions' => array('user_id' => $id),
                'recursive' => -1,
                'joins' => array(
                    array('table' => 'category_langs',
                          'alias' => 'CategoryLang',
                          'type' => 'left',
                          'conditions' => array(
                              'CategoryLang.category_id = CategoryUser.category_id',
                              'CategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                          )
                    )
                ),
            ));

            //On extrait les noms sans les dimensions parents
            $univers = Set::classicExtract($univers, '{n}.CategoryLang.name');

            //On rassemble les univers en string
            $univers = implode(', ',$univers);

            //On récupère les 15 dernieres communications
            $this->loadModel('UserCreditHistory');
            $lastCom = $this->UserCreditHistory->find('all',array(
                'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.is_sold','UserCreditHistory.called_number', 'User.firstname', 'User.lastname','UserCreditHistory.type_pay','UserCreditHistory.domain_id'),
                'conditions' => array('UserCreditHistory.agent_id' => $id),
                'order' => 'UserCreditHistory.date_start desc',
                'limit' => Configure::read('Site.limitStatistique')
            ));

			//get last date comm
			$last_date = '';
			foreach($lastCom as $comm){
				$last_date = $comm['UserCreditHistory']['date_start'];
			}

			//on fusonne email refund
			if($last_date){
				$new_list = array();
				$conditions_refund = array(
					'UserPenality.date_add >=' => $last_date,
					'UserPenality.date_add <=' => date('Y-m-d H:i:s'),
					'UserPenality.is_factured'=>1,'UserPenality.message_id >'=>0,
					'UserPenality.user_id' => $id
				);

				 $allRefundDatas = $this->UserPenality->find('all', array(
					'fields'        => array('UserPenality.date_add','UserPenality.message_id','UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured',
					'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email','UserPay.price'),
					'conditions'    => $conditions_refund,
					'joins' => array(
						array(
							'table' => 'user_credit_history',
							'alias' => 'UserCreditHistory',
							'type'  => 'left',
							'conditions' => array(
								'UserCreditHistory.sessionid = UserPenality.message_id',
								'UserCreditHistory.media = \'email\'',
							)
						),
						array(
							'table' => 'user_pay',
							'alias' => 'UserPay',
							'type'  => 'left',
							'conditions' => array(
								'UserPay.id_user_credit_history = UserCreditHistory.user_credit_history',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = UserCreditHistory.user_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'Agent',
							'type'  => 'left',
							'conditions' => array(
								'Agent.id = UserCreditHistory.agent_id',
							)
						),

					),
					'order'         => 'UserPenality.date_add DESC'
				));

				/*$conditions_facture = array(
					'UserOrder.date_ecriture >=' => $last_date,
					'UserOrder.date_ecriture <=' => date('Y-m-d H:i:s'),
					'UserOrder.user_id' => $id
				);

				 $allFactureDatas = $this->UserOrder->find('all', array(
					'fields'        => array('UserOrder.*'),
					'conditions'    => $conditions_facture,
					'order'         => 'UserOrder.date_ecriture DESC'
				));*/

				if(count($allRefundDatas)){

					foreach($lastCom as $todo){
						$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
					}

					foreach($allRefundDatas as &$refund){
						$refund['UserCreditHistory']['date_start'] = $refund['UserPenality']['date_add'];
						$refund['UserCreditHistory']['media'] = 'refund';
						$refund['UserCreditHistory']['sessionid'] = $refund['UserPenality']['message_id'];
						$refund['UserPay']['price'] = -12;
						$refund['UserCreditHistory']['credits'] = $refund['UserCreditHistory']['credits'] * -1;
						$new_list[$refund['UserCreditHistory']['date_start']] = $refund;
					}

					krsort($new_list);
					$lastCom = array();
					foreach($new_list as $cc){
						array_push($lastCom,$cc);
					}
				}

				/*if(count($allFactureDatas)){
					$new_list = array();
					foreach($lastCom as $todo){
						$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
					}

					foreach($allFactureDatas as &$facture){
						$facture['UserCreditHistory']['date_start'] = $facture['UserOrder']['date_ecriture'];
						$facture['UserCreditHistory']['media'] = 'other';
						$facture['UserCreditHistory']['sessionid'] = $facture['UserOrder']['id'];
						$facture['UserPay']['price'] = $facture['UserOrder']['amount'];
						$facture['UserCreditHistory']['credits'] = $facture['UserOrder']['amount'];;
						$new_list[$facture['UserCreditHistory']['date_start']] = $facture;
					}

					krsort($new_list);
					$lastCom = array();
					foreach($new_list as $cc){
						array_push($lastCom,$cc);
					}
				}*/
			}


			$nbComPhone = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history'),
                'conditions' => array('UserCreditHistory.agent_id' => $id, 'media' => 'phone'),
                'recursive' => -1,
            ));
			$nbMinComPhone = $this->UserCreditHistory->find('all',array(
                'fields' => array('SUM(UserCreditHistory.seconds) as total'),
                'conditions' => array('UserCreditHistory.agent_id' => $id, 'media' => 'phone'),
                'recursive' => -1,
            ));
			$nbMinComPhone = $nbMinComPhone[0][0]['total'];
			$nbComMail = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history'),
                'conditions' => array('UserCreditHistory.agent_id' => $id, 'media' => 'email'),
                'recursive' => -1,
            ));
			$nbMinComMail = 0;

			$nbComTchat = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history'),
                'conditions' => array('UserCreditHistory.agent_id' => $id, 'media' => 'chat'),
                'recursive' => -1,
            ));

			$nbMinComTchat = $this->UserCreditHistory->find('all',array(
                'fields' => array('SUM(UserCreditHistory.seconds) as total'),
                'conditions' => array('UserCreditHistory.agent_id' => $id, 'media' => 'chat'),
                'recursive' => -1,
            ));
			$nbMinComTchat = $nbMinComTchat[0][0]['total'];
            //On check pour la photo et la présentation audio
            if(empty($agent['User']['agent_number'])){
                $filePhoto = new File(Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$agent['User']['id'].'_listing.jpg');
                $fileAudio = new File(Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$agent['User']['id'].'.mp3');
            }
            else {
                $filePhoto = new File(Configure::read('Site.pathPhoto').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'_listing.jpg');
                $fileAudio = new File(Configure::read('Site.pathPresentation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp3');
            }
            if($filePhoto->exists())
                $pathPhoto = $filePhoto->name;
            if($fileAudio->exists())
                $pathAudio = $fileAudio->name;

			//L'historique des Ip
            $userIp = $this->UserIp->find('all',array(
                'conditions' => array('UserIp.user_id' => $id),
                'order' => 'date_conn DESC',
                'recursive' => -1,
				 'limit' => Configure::read('Site.limitStatistique')
            ));

			$userNotIp = array();
			foreach($userIp as $ip){
				if($ip['UserIp']['IP']){
					$listuserNotIp = $this->UserIp->find('all',array(
						'fields' => array('UserIp.IP','User.id','User.role','User.firstname','User.pseudo'),
						'conditions' => array('UserIp.IP' => $ip['UserIp']['IP'], 'UserIp.user_id !=' => $id),
						'order' => 'date_conn DESC',
						'group' => 'UserIp.user_id',
						 'joins' => array(
								array(
									'table' => 'users',
									'alias' => 'User',
									'type'  => 'left',
									'conditions' => array(
										'User.id = UserIp.user_id',
										'User.role != \'admin\'',
									)
								)
							),
						'recursive' => -1
					));
					foreach($listuserNotIp as $not){
						if($not['User']['id'])
						array_push($userNotIp,$not);
					}
				}
			}




			$lastConnexions = $this->UserConnexion->find('all',array(
                'fields' => array('UserConnexion.*'),
                'conditions' => array('UserConnexion.user_id' => $agent['User']['id']),
                'order' => 'UserConnexion.date_connexion desc',
                'paramType' => 'querystring',
                'limit' => Configure::read('Site.limitStatistique')
            ));

			$bonus_agent = $this->BonusAgent->find('all',array(
                'fields' => array('BonusAgent.min_total','BonusAgent.annee','BonusAgent.mois','BonusAgent.paid','BonusAgent.paid_amount','BonusAgent.date_add', 'Bonuse.bearing', 'Bonuse.amount', 'Bonuse.name'),
                'conditions' => array('BonusAgent.id_agent' => $agent['User']['id'], 'BonusAgent.paid' => 1, 'BonusAgent.id_bonus >'=>0),
                'recursive' => -1,
                'joins' => array(
                    array('table' => 'bonuses',
                          'alias' => 'Bonuse',
                          'type' => 'left',
                          'conditions' => array(
                              'Bonuse.id = BonusAgent.id_bonus',
                          )
                    )
                ),
				'order' => 'BonusAgent.date_add desc',
				'limit' => Configure::read('Site.limitStatistique')
            ));


			$sponsorships = $this->UserCreditHistory->find('all',array(
				'fields' => array('Sponsorship.*','Filleul.*','UserCreditHistory.*'),
				 'conditions' => array('UserCreditHistory.is_factured'=>1),
                'recursive' => -1,
                'joins' => array(
					array('table' => 'sponsorships',
                          'alias' => 'Sponsorship',
                          'type' => 'right',
                          'conditions' => array(
                              'Sponsorship.id_customer = UserCreditHistory.user_id',
								'Sponsorship.status <= 4',
							  'Sponsorship.user_id' => $agent['User']['id'],
							   'Sponsorship.id_customer >' => 0
                          )
                    ),
                    array('table' => 'users',
                          'alias' => 'Filleul',
                          'type' => 'left',
                          'conditions' => array(
                              'Filleul.id = Sponsorship.id_customer',
                          )
                    )
                ),
				'order' => 'UserCreditHistory.date_start desc',
				'limit' => Configure::read('Site.limitStatistique')
            ));

			//On récupère les 15 derniers call raté
			if($agent['User']['agent_number']){


				$conditions_lost = array(
					'UserPenality.is_factured'=>1,
					'UserPenality.callinfo_id >'=>0,
					'UserPenality.user_id' => $agent['User']['id']
				);

				 $lastComlost = $this->UserPenality->find('all', array(
					'fields'        => array('User.firstname, User.lastname, Callinfo.timestamp, Callinfo.callerid,Callinfo.sessionid, User.id'),
					'conditions'    => $conditions_lost,
					'joins' => array(

						array(
							'table' => 'call_infos',
							'alias' => 'Callinfo',
							'type'  => 'left',
							'conditions' => array(
								'Callinfo.callinfo_id = UserPenality.callinfo_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.personal_code = Callinfo.customer',
							)
						),

					),
					'order'         => 'UserPenality.date_add DESC',
					 'paramType' => 'querystring',
					'limit' => Configure::read('Site.limitStatistique')
				));

			}else{
				$lastComlost = array();
			}
			//On récupère les 15 derniers chat raté

				$conditions_lost = array(
					'UserPenality.is_factured'=>1,
					'UserPenality.tchat_id >'=>0,
					'UserPenality.user_id' => $agent['User']['id']
				);

				 $lastComlostchat = $this->UserPenality->find('all', array(
					'fields'        => array('User.firstname, User.lastname, Chat.date_start,Chat.id, User.id'),
					'conditions'    => $conditions_lost,
					'joins' => array(

						array(
							'table' => 'chats',
							'alias' => 'Chat',
							'type'  => 'left',
							'conditions' => array(
								'Chat.id = UserPenality.tchat_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = Chat.from_id',
							)
						),

					),
					'order'         => 'UserPenality.date_add DESC',
					 'paramType' => 'querystring',
					'limit' => Configure::read('Site.limitStatistique')
				));
			//On récupère les 15 derniers mails raté

				$conditions_lost = array(
					'UserPenality.is_factured'=>1,
					'UserPenality.message_id >'=>0,
					'UserPenality.user_id' => $agent['User']['id']
				);

				 $lastComlostmessage = $this->UserPenality->find('all', array(
					'fields'        => array('User.firstname, User.lastname, Message.date_add,Message.id, User.id,UserPenality.delay'),
					'conditions'    => $conditions_lost,
					'joins' => array(

						array(
							'table' => 'messages',
							'alias' => 'Message',
							'type'  => 'left',
							'conditions' => array(
								'Message.id = UserPenality.message_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = Message.from_id',
							)
						),

					),
					'order'         => 'UserPenality.date_add DESC',
					 'paramType' => 'querystring',
					'limit' => Configure::read('Site.limitStatistique')
				));

           $questionnaires = $this->Survey->find('all',array(
                'conditions' => array('user_id' => $id),
                'recursive' => -1,
			    'order'       => 'Survey.id DESC',
            ));

			$questionnaire = $this->Survey->find('first',array(
                'conditions' => array('user_id' => $id,'is_respons' => 1),
                'recursive' => -1,
			    'order'       => 'Survey.id DESC',
            ));

			 $documents = $this->UserDocument->find('all',array(
                'conditions' => array('user_id' => $id, 'active' => 1),
                'recursive' => -1,
			    'order'       => 'UserDocument.id ASC',
            ));


			$stats_agent = $this->AgentStats($agent['User']['id']);

        }

        $this->set(compact('agent','listData', 'nameField', 'flags', 'presentations', 'univers', 'pathPhoto', 'pathAudio', 'lastCom', 'nbComPhone', 'nbComTchat', 'nbComMail', 'userIp','userNotIp','nbMinComPhone', 'nbMinComTchat', 'nbMinComMail', 'lastComlost', 'lastComlostchat', 'lastComlostmessage','stats_agent','lastConnexions','bonus_agent','sponsorships','questionnaires','documents','questionnaire'));
    }

    public function admin_com(){
        //Voir la méthode dans extranet
        $this->_adminCom('agent_id');
    }

    public function admin_com_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){
            //Charge model
            $this->loadModel('UserCreditHistory');
            //Les conditions de base
            $conditions = array('UserCreditHistory.agent_id' => $id);
			$limit = 25;
            //Avons-nous un filtre sur la date ??
			$is_date_filtre = 0;
            if($this->Session->check('Date')){

				$listing_utcdec = Configure::read('Site.utcDec');

				/*$utc_dec = 1;//Configure::read('Site.utc_dec');
				$cut = explode('-',$this->Session->read('Date.start') );
				$mois_comp = $cut[1];
				if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
					$utc_dec = 2;*/


				$dmax = new DateTime($this->Session->read('Date.end').' 23:59:59');
				$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
				$session_date_max =  $dmax->format('Y-m-d H:i:s');
				$dmin = new DateTime($this->Session->read('Date.start'). '00:00:00');
				$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
				$cut = explode('-',$this->Session->read('Date.start') );
				$session_date_min =  $dmin->format('Y-m-d H:i:s');


				/*$datecomp = $cut[2].$cut[1].$cut[0];

				if($datecomp >= '20190301')

				else
				$dmin->modify('-0 hour');


				if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';
				if($session_date_max == '2019-04-30 22:59:59')$session_date_max = '2019-04-30 21:59:59';
				*/

                $conditions = array_merge($conditions, array(
                    'UserCreditHistory.date_start >=' => $session_date_min,
                    'UserCreditHistory.date_start <=' => $session_date_max
                ));
				$limit = 99999;
				$is_date_filtre = 1;
				$is_date_min = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
				$is_date_max = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
            }

            //Avons-nous un filtre sur les medias ??
            if($this->Session->check('Media'))
                $conditions = array_merge($conditions, array('UserCreditHistory.media' => $this->Session->read('Media.value')));

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.is_sold','UserCreditHistory.sessionid','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media', 'UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number','User.firstname', 'User.lastname','UserCreditHistory.type_pay','UserCreditHistory.domain_id'),
                'conditions' => $conditions,
                'order' => 'UserCreditHistory.date_start desc',
                'paramType' => 'querystring',
                'limit' => $limit,
				'maxLimit' => $limit
            );
			$allComs = $this->Paginator->paginate($this->UserCreditHistory);

			$this->loadModel('UserPenality');
			//$last_date = '';
			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			foreach ($allComs as $k => &$row){
				$result = $mysqli_s->query("SELECT price from user_pay WHERE id_user_credit_history = '{$row['UserCreditHistory']['user_credit_history']}'");
				$row2 = $result->fetch_array(MYSQLI_ASSOC);
				$row['UserCreditHistory']['price'] = $row2['price'];
				//$last_date = $row['UserCreditHistory']['date_start'];
			}

			//on fusonne email refund
			//if($last_date){
				$new_list = array();
				$conditions_refund = array(
					'UserPenality.date_add >=' => $session_date_min,
                    'UserPenality.date_add <=' => $session_date_max,
					'UserPenality.is_factured'=>1,'UserPenality.message_id >'=>0,
					'UserPenality.user_id' => $id
				);

				 $allRefundDatas = $this->UserPenality->find('all', array(
					'fields'        => array('UserPenality.date_add','UserPenality.message_id','UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured',
					'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email','UserPay.price'),
					'conditions'    => $conditions_refund,
					'joins' => array(
						array(
							'table' => 'user_credit_history',
							'alias' => 'UserCreditHistory',
							'type'  => 'left',
							'conditions' => array(
								'UserCreditHistory.sessionid = UserPenality.message_id',
								'UserCreditHistory.media = \'email\'',
							)
						),
						array(
							'table' => 'user_pay',
							'alias' => 'UserPay',
							'type'  => 'left',
							'conditions' => array(
								'UserPay.id_user_credit_history = UserCreditHistory.user_credit_history',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = UserCreditHistory.user_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'Agent',
							'type'  => 'left',
							'conditions' => array(
								'Agent.id = UserCreditHistory.agent_id',
							)
						),

					),
					'order'         => 'UserPenality.date_add DESC'
				));
				if(count($allRefundDatas)){

					foreach($allComs as $todo){
						$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
					}

					foreach($allRefundDatas as &$refund){
						$refund['UserCreditHistory']['date_start'] = $refund['UserPenality']['date_add'];
						$refund['UserCreditHistory']['media'] = 'refund';
						$refund['UserCreditHistory']['sessionid'] = $refund['UserPenality']['message_id'];
						$refund['UserCreditHistory']['price'] = -12;
						$refund['UserCreditHistory']['credits'] = $refund['UserCreditHistory']['credits'] * -1;
						$new_list[$refund['UserCreditHistory']['date_start']] = $refund;
					}

					krsort($new_list);
					$allComs = array();
					foreach($new_list as $cc){
						array_push($allComs,$cc);
					}
				}
			//}
			$this->loadModel('UserOrder');
			$conditions_facture = array(
					'UserOrder.date_ecriture >=' => $session_date_min,
					'UserOrder.date_ecriture <=' => $session_date_max,
					'UserOrder.user_id' => $id
				);

				 $allFactureDatas = $this->UserOrder->find('all', array(
					'fields'        => array('UserOrder.*'),
					'conditions'    => $conditions_facture,
					'order'         => 'UserOrder.date_ecriture DESC'
				));
			if(count($allFactureDatas)){
					$new_list = array();
					foreach($allComs as $todo){
						$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
					}

					foreach($allFactureDatas as &$facture){
						$facture['UserCreditHistory']['date_start'] = $facture['UserOrder']['date_ecriture'];
						$facture['UserCreditHistory']['media'] = 'other';
						$facture['UserCreditHistory']['comm'] = $facture['UserOrder']['commentaire'];
						$facture['UserCreditHistory']['price'] = $facture['UserOrder']['amount'];
						$new_list[$facture['UserCreditHistory']['date_start']] = $facture;
					}

					krsort($new_list);
					$allComs = array();
					foreach($new_list as $cc){
						array_push($allComs,$cc);
					}
				}


			$date_fact = array();
			if($is_date_filtre){
				$date_fact['min'] = $is_date_min;
				$date_fact['max'] = $is_date_max;
				$_SESSION['fact_agent'] = $id;
				$_SESSION['fact_min'] = $is_date_min;
				$_SESSION['fact_max'] = $is_date_max;
			}else{
				$_SESSION['fact_agent'] = '';
				$_SESSION['fact_min'] = '';
				$_SESSION['fact_max'] = '';
			}
			$mysqli_s->close();
            $this->set(compact('user','allComs', 'date_fact'));
        }else{
            $this->Session->setFlash(__('Aucun expert trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'com', 'admin' => true),false);
        }
    }

	 public function admin_sponsorship_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){
            $this->loadModel('Sponsorship');
			$this->loadModel('UserCreditHistory');

            //Les conditions de base
           /* $conditions = array('Sponsorship.user_id' => $id, 'Sponsorship.is_recup >=' => 0, 'Sponsorship.id_customer >' => 0, 'Sponsorship.status <=' => 4);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'Sponsorship.date_add >=' => CakeTime::format($this->Session->read('Date.add'), '%Y-%m-%d 00:00:00'),
                    'Sponsorship.date_add <=' => CakeTime::format($this->Session->read('Date.add'), '%Y-%m-%d 23:59:59')
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('Sponsorship.*','Filleul.*','(select sum(H.credits) from user_credit_history H where H.user_id = Sponsorship.id_customer and H.date_start >= Sponsorship.date_add and H.is_factured = 1) as total '),
                'conditions' => $conditions,
                'order' => 'Sponsorship.date_add desc',
                'paramType' => 'querystring',
				'joins' => array(
                    array('table' => 'users',
                          'alias' => 'Filleul',
                          'type' => 'left',
                          'conditions' => array(
                              'Filleul.id = Sponsorship.id_customer',
                          )
                    )
                ),
                'limit' => 25
            );

            $allSponsorships = $this->Paginator->paginate($this->Sponsorship);*/

			$conditions = array('UserCreditHistory.is_factured' => 1);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserCreditHistory.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserCreditHistory.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('Sponsorship.*','Filleul.*','UserCreditHistory.*'),
                'conditions' => $conditions,
                'order' => 'UserCreditHistory.date_start desc',
                'paramType' => 'querystring',
				'joins' => array(
					array('table' => 'sponsorships',
                          'alias' => 'Sponsorship',
                          'type' => 'right',
                          'conditions' => array(
                              'Sponsorship.id_customer = UserCreditHistory.user_id',
							  'Sponsorship.status <= 4',
							  'Sponsorship.user_id' => $id
                          )
                    ),
                    array('table' => 'users',
                          'alias' => 'Filleul',
                          'type' => 'left',
                          'conditions' => array(
                              'Filleul.id = Sponsorship.id_customer',
                          )
                    )
                ),
                'limit' => 25
            );

            $allSponsorships = $this->Paginator->paginate($this->UserCreditHistory);


            $this->set(compact('user','allSponsorships'));
        }else{
            $this->Session->setFlash(__('Aucun client trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'com', 'admin' => true), false);
        }
    }

    //Export des données de communication
    public function admin_export_com(){
        return $this->_adminExportCom('agents');
    }

     public function admin_valid_info(){
        //On charge les models
        $this->loadModel('UserValidation');

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('UserValidation.*', 'UserCountryLang.name', 'User.vat_num as vat_numUser'),
            'conditions' => array('UserValidation.etat' => 0),
            'order' => array('UserValidation.date_add' => 'asc'),
            'recursive' => -1,
            'joins' => array(
                array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLang.user_countries_id = UserValidation.country_id',
                        'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                ),
                array('table' => 'users',
                    'alias' => 'User',
                    'type' => 'left',
                    'conditions' => array('User.id = UserValidation.users_id')
                )
            ),
            'paramType' => 'querystring',
            'limit' => 10
        );

        $tmp_rows = $this->Paginator->paginate($this->UserValidation);

        $rows = array();
        foreach($tmp_rows as $i => $row){
            $rows[] = array(
                'id' => $row['UserValidation']['id'],
                'fullname' =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['UserValidation']['users_id']),true).'">'.$row['UserValidation']['firstname'].' '.$row['UserValidation']['lastname'].'</a>',
                'pseudo' => $row['UserValidation']['pseudo'],
                'phone_number' => $row['UserValidation']['phone_number'],
                'fulladdress' => $row['UserValidation']['address'].' '.$row['UserValidation']['postalcode'].' '.$row['UserValidation']['city'],
                'country_id' => $row['UserCountryLang']['name'],
                'birthdate' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserValidation']['birthdate']), '%d %B %Y'),
                'sexe' => '<span class="badge badge-'.($row['UserValidation']['sexe'] == 1?'man':'woman').'">'.__(($row['UserValidation']['sexe'] == 1?'Homme':'Femme')).'</span>',
                'siret' => $row['UserValidation']['siret'],
				'vat_num' => $row['UserValidation']['vat_num'],
				'belgium_save_num' => $row['UserValidation']['belgium_save_num'],
				'belgium_society_num' => $row['UserValidation']['belgium_society_num'],
				'canada_id_hst' => $row['UserValidation']['canada_id_hst'],
				'spain_cif' => $row['UserValidation']['spain_cif'],
				'luxembourg_autorisation' => $row['UserValidation']['luxembourg_autorisation'],
				'luxembourg_commerce_registrar' => $row['UserValidation']['luxembourg_commerce_registrar'],
				'marocco_ice' => $row['UserValidation']['marocco_ice'],
				'marocco_if' => $row['UserValidation']['marocco_if'],
				'portugal_nif' => $row['UserValidation']['portugal_nif'],
				'senegal_ninea' => $row['UserValidation']['senegal_ninea'],
				'senegal_rccm' => $row['UserValidation']['senegal_rccm'],
				'tunisia_rc' => $row['UserValidation']['tunisia_rc'],
				'society_type_id' => $row['UserValidation']['society_type_id'],
				'societe_statut' => $row['UserValidation']['societe_statut'],
				'rib' => $row['UserValidation']['rib'],
				'bank_name' => $row['UserValidation']['bank_name'],
				'bank_address' => $row['UserValidation']['bank_address'],
				'bank_country' => $row['UserValidation']['bank_country'],
				'iban' => $row['UserValidation']['iban'],
				'swift' => $row['UserValidation']['swift'],
				'societe' => $row['UserValidation']['societe'],
				'societe_adress' => $row['UserValidation']['societe_adress'],
				'societe_adress2' => $row['UserValidation']['societe_adress2'],
				'societe_cp' => $row['UserValidation']['societe_cp'],
				'societe_ville' => $row['UserValidation']['societe_ville'],
				'societe_pays' => $row['UserValidation']['societe_pays'],
				'paypal' => $row['UserValidation']['paypal'],
                'UserVat' => $row['User']['vat_numUser'],
				'mode_paiement' => $row['UserValidation']['mode_paiement'],
                'date_add' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserValidation']['date_add']), '%d %B %Y')
            );
        }

        $this->set(compact('rows','user_level'));
    }

    public function admin_accept_valid_info($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('UserValidation', $id, 'etat', 0, 'Aucune données', $url);

		//On récupère les données en attente
            $data = $this->UserValidation->find('first',array(
                'conditions' => array('id' => $id),
                'recursive' => -1
            ));
		//On affecte l'id de l'agent au model User
            $this->User->id = $data['UserValidation']['users_id'];



        if($this->UserValidation->saveField('etat', 1)){
            //On sauvegarde l'id de l'admin
            $this->UserValidation->saveField('admin_id' ,$this->Auth->user('id'));

            //On retire les données inutiles
            $data['User'] = $data['UserValidation'];
            $data['User']['id'] = $data['UserValidation']['users_id'];
			$data['User']['vat_num_status'] = '';
            unset($data['User']['date_add']);
            unset($data['UserValidation']);

            //Vérifie si le numéro à changer
            $samePhone = $this->User->phoneNumberCmp($data['User']['id'], $data['User']['phone_number']);
            //Si le numéro est différent, on le met à jour sur l'api
            if($samePhone !== 0 && $samePhone !== false){
                //On update l'agent au niveau de l'api
                $api = new Api();
                $result = $api->updateAgent($this->User->field('agent_number', array('id' => $data['User']['id'])), $data['User']['phone_number']);

                //S'il y a eu une erreur
                if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0)){
                    //Les modifications ne sont pas acceptés
                    $this->UserValidation->saveField('etat', 0);
                    $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Echec de la mise à jour de l\'agent.')),'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'valid_info_view', 'admin' => true, 'id' => $id),false);
                }
            }

            //On save les nouvelles données de l'agent
            if($this->User->save($data)){

				//refresh pseudo
				$dbb_patch = new DATABASE_CONFIG();
				$dbb_connect = $dbb_patch->default;
				$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database'],$dbb_connect['port']);
				$agent_id = $data['User']['id'];
				$agent_pseudo = addslashes($data['User']['pseudo']);
				$mysqli_connect->query("update agent_pseudos set pseudo = '{$agent_pseudo}' WHERE user_id = '{$agent_id}'");
				$mysqli_connect->close();


					//refresh stripe data
					$is_update_stripe = true;

					if($this->User->field('stripe_account'))$is_update_stripe = $this->updateStripeAccount($this->User->id);

					if($is_update_stripe){
						//L'email de l'agent
						$emailAgent = $this->User->field('email');
						//Les datas pour l'email
						$datasEmail = array('content' => 'La modification de vos données a été acceptée');
						//On envoie l'email
						//$this->sendEmail($emailAgent,'Modification validée','admin_accept',array('data' => $datasEmail));
						$agent = $this->User->read();

						$this->sendCmsTemplateByMail(187, $agent['User']['lang_id'], $emailAgent);

						$this->Session->setFlash(__('Mise à jour des informations de l\'agent. Email envoyé.'),'flash_success');
					}else{
						$this->UserValidation->id = $id;
                		$this->UserValidation->saveField('etat', 0);
					}

            }else{
                //Problème dans la sauvegarde des données, on remet les données en attente
                $this->UserValidation->id = $id;
                $this->UserValidation->saveField('etat', 0);
                $this->Session->setFlash(__('Echec de la mise à jour des informations de l\'agent. Pas d\'email envoyé.'),'flash_warning');
            }
        }else
            $this->Session->setFlash(__('Erreur dans la mise à jour des données de l\'agent.'),'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true),false);
    }

    public function admin_refuse_valid_info($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true), true);
        //On check les informations
        $this->checkEntite('UserValidation', $id, 'etat', 0, 'Aucune données', $url, array(), ($this->request->is('ajax')));

        //Initialisation des paramètres
        $field = array(
            'name'      => 'etat',
            'value'     => -1,
            'primary'   => 'id',
            'foreign'   => 'users_id'
        );

        $form = array(
            'model' => 'Agent',
            'note'  => __('L\'agent recevra un email pour l\'informer du refus'),
        );

        $message = array(
            'success'   => __('La modification des données de l\'agent a été refusée. Email envoyé.'),
            'error'     => __('Erreur lors du rejet de la modification des données de l\'agent.')
        );

        $email = array(
            'subject'   => __('Modification des données rejetée'),
            'template'  => 'admin_refuse'
        );

        $datasEmail = array(
            'content' => __('La modification de vos données a été refusée'),
            'motif' => (isset($this->request->data['Agent']['motif'])?$this->request->data['Agent']['motif']:''),
            'emailAdmin' => $this->Auth->user('email'),
            'cms_id' => 190
        );

        $this->refuseEntite('UserValidation',$id,$url,$field,$form,__('Voulez-vous vraiment refuser la modification ?'),$message,$email,$datasEmail);
    }

    public function admin_valid_info_view($id){
        //On charge les models
        $this->loadModel('UserValidation');
		$this->loadModel('SocietyType');
		$this->loadModel('InvoiceVat');
        //Il nous faut les données en attente
        $this->UserValidation->id = $id;
        $rows['validation'] = $this->UserValidation->find('first',array(
            'fields' => array('UserValidation.*', 'UserCountryLang.name', 'UserCountryLangSociety.name as name_society'),
            'conditions' => array('UserValidation.id' => $id),
            'joins' => array(
                array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLang.user_countries_id = UserValidation.country_id',
                        'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                ),
				array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLangSociety',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLangSociety.user_countries_id = UserValidation.societe_pays',
                        'UserCountryLangSociety.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1
        ));

        //Les données actuelles
        $rows['actuelle'] = $this->User->find('first',array(
            'fields' => array('User.*', 'UserCountryLang.name', 'UserCountryLangSociety.name as name_society'),
            'conditions' => array('id' => $rows['validation']['UserValidation']['users_id']),
            'joins' => array(
                array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLang.user_countries_id = User.country_id',
                        'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                ),
				array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLangSociety',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLangSociety.user_countries_id = User.societe_pays',
                        'UserCountryLangSociety.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1
        ));

        //Si il manque des données
        if(empty($rows['actuelle']) || empty($rows['validation'])){
            $this->Session->setFlash(__('Erreur lors de la récupération des données'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'valid_info', 'admin' => true), false);
        }

		$list_types = $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang'));

        //On formate les données actuelles et en attente
        foreach($rows as $etat => $val){
            //Les clés des tableaux
            $keys = array_keys($val);
            $data[$etat] = array(
                'id' => $rows[$etat][$keys[0]]['id'],
                'Nom complet' =>  $rows[$etat][$keys[0]]['firstname'].' '.$rows[$etat][$keys[0]]['lastname'],
                'Pseudo' => $rows[$etat][$keys[0]]['pseudo'],
                'Numéro de téléphone' => $rows[$etat][$keys[0]]['phone_number'],
                'Opérateur téléphonique' => $rows[$etat][$keys[0]]['phone_operator'],
                'Numéro de téléphone secondaire' => $rows[$etat][$keys[0]]['phone_number2'],
				'Opérateur téléphonique 2' => $rows[$etat][$keys[0]]['phone_operator2'],
				'Numéro de téléphone mobile' => $rows[$etat][$keys[0]]['phone_mobile'],
				'Opérateur téléphonique mobile' => $rows[$etat][$keys[0]]['phone_operator3'],
                'Adresse' => $rows[$etat][$keys[0]]['address'].' '.$rows[$etat][$keys[0]]['postalcode'].' '.$rows[$etat][$keys[0]]['city'],
                'Pays de résidence' => $rows[$etat][$keys[1]]['name'],
                'Date de naissance' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$rows[$etat][$keys[0]]['birthdate']), '%d %B %Y'),
                'Sexe' => '<span class="badge badge-'.($rows[$etat][$keys[0]]['sexe'] == 1?'man':'woman').'">'.__(($rows[$etat][$keys[0]]['sexe'] == 1?'Homme':'Femme')).'</span>',
                'Siret' => $rows[$etat][$keys[0]]['siret'],
				'Statut' => $list_types[$rows[$etat][$keys[0]]['society_type_id']],
				'Statut autre' => $rows[$etat][$keys[0]]['societe_statut'],
				'TVA INTRA' => $rows[$etat][$keys[0]]['vat_num'],
				'TVA TAUX' => $this->InvoiceVat->getVatForCompare($this->Session->read('Config.id_lang'),$rows[$etat][$keys[0]]['invoice_vat_id']),
				'BELGIQUE NUM ENREGISTREMENT' => $rows[$etat][$keys[0]]['belgium_save_num'],
				'BELGIQUE NUM SOCIETE' => $rows[$etat][$keys[0]]['belgium_society_num'],
				'CANADA HST ID' => $rows[$etat][$keys[0]]['canada_id_hst'],
				'ESPAGNE CIF' => $rows[$etat][$keys[0]]['spain_cif'],
				'LUXEMBOURG AUTORISATION' => $rows[$etat][$keys[0]]['luxembourg_autorisation'],
				'LUXEMBOURG COMMERCE REGISTRE' => $rows[$etat][$keys[0]]['luxembourg_commerce_registrar'],
				'MAROC ICE' => $rows[$etat][$keys[0]]['marocco_ice'],
				'MAROC IF' => $rows[$etat][$keys[0]]['marocco_if'],
				'PORTUGAL NIF' => $rows[$etat][$keys[0]]['portugal_nif'],
				'SENEGAL NINEA' => $rows[$etat][$keys[0]]['senegal_ninea'],
				'SENEGAL RCCM' => $rows[$etat][$keys[0]]['senegal_rccm'],
				'TUNISIE RC' => $rows[$etat][$keys[0]]['tunisia_rc'],
				'RIB' => $rows[$etat][$keys[0]]['rib'],
				'Nom Banque' => $rows[$etat][$keys[0]]['bank_name'],
				'Adresse Banque' => $rows[$etat][$keys[0]]['bank_address'],
				'Pays Banque' => $rows[$etat][$keys[0]]['bank_country'],
				'IBAN' => $rows[$etat][$keys[0]]['iban'],
				'BIC / SWIFT' => $rows[$etat][$keys[0]]['swift'],
				'Societe' => $rows[$etat][$keys[0]]['societe'],
				'Societe adresse' => $rows[$etat][$keys[0]]['societe_adress'],
				'Societe adresse 2' => $rows[$etat][$keys[0]]['societe_adress2'],
				'Societe code postal' => $rows[$etat][$keys[0]]['societe_cp'],
				'Societe ville' => $rows[$etat][$keys[0]]['societe_ville'],
				'Societe pays' => $rows[$etat][$keys[2]]['name_society'],
				'Paypal' => $rows[$etat][$keys[0]]['paypal'],
				'Mode Paiement' => $rows[$etat][$keys[0]]['mode_paiement']
            );
        }

        $this->set(compact('data'));
    }

    public function admin_valid_presentation(){
        //On charge les models
        $this->loadModel('UserPresentLang');
        $this->loadModel('UserPresentValidation');

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('UserPresentValidation.*', 'UserPresentLang.texte', 'Lang.name', 'User.pseudo'),
            'conditions' => array('UserPresentValidation.etat' => 0),
            'order' => array('UserPresentValidation.date_add' => 'asc'),
            'recursive' => -1,
            'joins' => array(
                array('table' => 'user_present_lang',
                    'alias' => 'UserPresentLang',
                    'type' => 'left',
                    'conditions' => array(
                        'UserPresentLang.user_id = UserPresentValidation.user_id',
                        'UserPresentLang.lang_id = UserPresentValidation.lang_id'
                    )
                ),
                array('table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = UserPresentValidation.lang_id')
                ),
                array('table' => 'users',
                    'alias' => 'User',
                    'type' => 'left',
                    'conditions' => array('User.id = UserPresentValidation.user_id')
                )
            ),
            'paramType' => 'querystring',
            'limit' => 10
        );

        $tmp_rows = $this->Paginator->paginate($this->UserPresentValidation);

        $rows = array();
        foreach($tmp_rows as $i => $row){
            $rows[] = array(
                'id' => $row['UserPresentValidation']['id'],
                'pseudo' =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['UserPresentValidation']['user_id']),true).'">'.$row['User']['pseudo'].'</a>',
                'lang_id' => $row['UserPresentValidation']['lang_id'],
                'langue' => $row['Lang']['name'],
                'texte_actuelle' => $row['UserPresentLang']['texte'],
                'texte_validation' => $row['UserPresentValidation']['texte'],
                'date_add' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserPresentValidation']['date_add']), '%d %B %Y')
            );
        }

        $this->set(compact('rows'));
    }

    public function admin_accept_valid_presentation($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_presentation', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('UserPresentValidation', $id, 'etat', 0, 'Aucune présentation trouvée', $url);

        if($this->UserPresentValidation->saveField('etat', 1)){
            //On sauvegarde l'id de l'admin
            $this->UserPresentValidation->saveField('admin_id' ,$this->Auth->user('id'));
            $presentValidation = $this->UserPresentValidation->find('first',array(
                'fields' => array('user_id', 'lang_id', 'texte'),
                'conditions' => array('id' => $id),
                'recursive' => -1
            ));

            //Vérifie si l'agent à déjà une présentation pour cette langue
            $this->loadModel('UserPresentLang');
            //Si oui
            if($this->UserPresentLang->hasPresentation($presentValidation['UserPresentValidation']['user_id'],$presentValidation['UserPresentValidation']['lang_id'])){
                //On unbind les associations
                $this->UserPresentLang->unbindModel(array('hasOne' => array('User','Lang')));
                //On update le champ texte
                $this->UserPresentLang->updateAll(
                    array(
                        'texte' => $this->UserPresentLang->value(htmlentities($presentValidation['UserPresentValidation']['texte'])),
                        'date_upd' => $this->UserPresentLang->value(date('Y-m-d H:i:s'))
                    ),
                    array(
                        'user_id' => $presentValidation['UserPresentValidation']['user_id'],
                        'lang_id' => $presentValidation['UserPresentValidation']['lang_id']
                    ));
            }else{
                //On prépare les valeurs pour save la présentation
                foreach($presentValidation['UserPresentValidation'] as $key => $value){
                    $dataPresent['UserPresentLang'][$key] = $value;
                }

                $dataPresent['UserPresentLang']['date_upd'] = date('Y-m-d H:i:s');
                //On save la présentation
                $this->UserPresentLang->save($dataPresent);
            }
            //L'email pour l'agent
            $emailAgent = $this->getEmail('UserPresentValidation',$id,'user_id','id');
            //Les datas pour l'email
            $datasEmail = array('content' => 'Votre nouvelle présentation a été validée');
            //Envoie de l'email
            $this->loadModel('User');
            $this->User->id = $presentValidation['UserPresentValidation']['user_id'];
            $agent = $this->User->read();

            //$this->sendEmail($emailAgent,'Présentation validée','admin_accept',array('data' => $datasEmail));
            $this->sendCmsTemplateByMail(188, $agent['User']['lang_id'], $emailAgent, array(
                'AGENT_PSEUDO' => $agent['User']['pseudo'],
                'AGENT_FIRSTNAME' => $agent['User']['firstname'],
                'AGENT_LASTNAME' => $agent['User']['lastname']
            ));

            $this->Session->setFlash(__('Présentation validée. L\'email a été envoyé.'),'flash_success');
        }else
            $this->Session->setFlash(__('Erreur lors de la modification de la présentation de l\'agent.'),'flash_warning');

        $this->redirect($url);
    }

    public function admin_refuse_valid_presentation($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_presentation', 'admin' => true),true);
        //On check la présentation
        $this->checkEntite('UserPresentValidation', $id, 'etat', 0, 'Aucune présentation trouvée', $url, array(), ($this->request->is('ajax')));

        //Initialisation des paramètres
        $field = array(
            'name'      => 'etat',
            'value'     => -1,
            'primary'   => 'id',
            'foreign'   => 'user_id'
        );

        $form = array(
            'model' => 'Agent',
            'note'  => __('L\'agent recevra un email pour l\'informer du refus'),
        );

        $message = array(
            'success'   => __('La modification de la présentation de l\'agent a été refusée. Email envoyé.'),
            'error'     => __('Erreur lors du rejet de la modification de la présentation de l\'agent.')
        );

        $email = array(
            'subject'   => __('Présentation refusée'),
            'template'  => 'admin_refuse'
        );

        $datasEmail = array(
            'content' => __('La modification de votre présentation a été refusée'),
            'motif' => (isset($this->request->data['Agent']['motif']) ?$this->request->data['Agent']['motif']:''),
            'emailAdmin' => $this->Auth->user('email'),
            'cms_id' => 189
        );

        $this->refuseEntite('UserPresentValidation',$id,$url,$field,$form,__('Voulez-vous vraiment refuser la présentation ?'),$message,$email,$datasEmail);
    }

	 public function admin_valid_mailinfos(){

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('User.pseudo','User.agent_number', 'User.id', 'User.mail_infos', 'User.mail_infos_v'),
            'conditions' => array('User.mail_infos !=' => ''),
            'order' => array('User.id' => 'asc'),
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 10
        );

        $tmp_rows = $this->Paginator->paginate($this->User);

        $rows = array();
        foreach($tmp_rows as $i => $row){
            $rows[] = array(
                'id' => $row['User']['id'],
                'pseudo' =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id']),true).'">'.$row['User']['pseudo'].'</a>',
                'texte_actuelle' => $row['User']['mail_infos_v'],
                'texte_validation' => $row['User']['mail_infos'],

            );
        }

        $this->set(compact('rows'));
    }

	public function admin_edit_valid_mailinfos($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_mailinfos', 'admin' => true), true);
        //On check la présentation
      //  $this->checkEntite('User', $id, 'mail_infos', 0, 'Aucune info trouvée', $url, array(), ($this->request->is('ajax')));

        //Initialisation des paramètres
        $field = array(
            'name'      => 'mail_infos',
            'primary'   => 'id'
        );
        $form = array(
            'model' => 'Agent',
            'title' => __('Informations email'),
            'note'  => __('Après modification de la présentation, il est impossible de récupérer la présentation originale.')
        );
        $message = array(
            'error'     => __('Erreur lors de la modification de la présentation.'),
            'success'   => __('Présentation modifiée.')
        );
        //Edition de l'entite
        $this->editEntite('User',$id,$url,$field,$form,__('Modification de la présentation'),$message);
    }

	public function admin_accept_valid_mailinfos($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_mailinfos', 'admin' => true), true);

		$this->User->id = $id;
		$agent = $this->User->read();
		if($this->User->saveField('mail_infos_v', $agent['User']['mail_infos'])){

			$this->User->saveField('mail_infos', "");
            $this->sendCmsTemplateByMail(393, $agent['User']['lang_id'], $agent['User']['email'], array(
                'AGENT_PSEUDO' => $agent['User']['pseudo'],
                'AGENT_FIRSTNAME' => $agent['User']['firstname'],
                'AGENT_LASTNAME' => $agent['User']['lastname']
            ));

            $this->Session->setFlash(__('Infos validées. L\'email a été envoyé.'),'flash_success');
        }else
            $this->Session->setFlash(__('Erreur lors de la modification des infos de l\'agent.'),'flash_warning');

        $this->redirect($url);
    }

    public function admin_refuse_valid_mailinfos($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_mailinfos', 'admin' => true),true);

		$this->User->id = $id;
		$agent = $this->User->read();

        //Initialisation des paramètres
        $field = array(
            'name'      => 'mail_infos',
            'value'     => ' ',
            'primary'   => 'id',
			'foreign'   => 'id',
        );

        $form = array(
            'model' => 'Agent',
            'note'  => __('L\'agent recevra un email pour l\'informer du refus'),
        );

        $message = array(
            'success'   => __('La modification des infos email de l\'agent a été refusée. Email envoyé.'),
            'error'     => __('Erreur lors du rejet de la modification des infos email de l\'agent.')
        );

        $email = array(
            'subject'   => __('Informations Email refusées'),
            'template'  => 'admin_refuse'
        );

        $datasEmail = array(
            'content' => __('La modification de votre présentation a été refusée'),
            'motif' => (isset($this->request->data['Agent']['motif']) ?$this->request->data['Agent']['motif']:''),
            'emailAdmin' => $this->Auth->user('email'),
            'cms_id' => 394
        );

        $this->refuseEntiteWithoutSave('User',$id,$url,$field,$form,__('Voulez-vous vraiment refuser les infos ?'),$message,$email,$datasEmail);
    }


	public function admin_present_audio(){
         //Les chemins des présentations audio en attente
        $paths = glob(Configure::read('Site.pathPresentation').'/[0-9]/[0-9]/*.mp3');
        //On récupère les codes des agents
        $agentCodes = Tools::extractData($paths,'.');
		$conditions = array();

		$conditions = array('agent_number' => $agentCodes,'deleted' => 0,'role' => 'agent');
		 if($this->request->is('post') && isset($this->request->data['Agent']['pseudo'])){
		 $conditions = array_merge($conditions, array(
                        'OR' => array(
								array('User.pseudo LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%'),
								//array('User.firstname LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%'),
								//array('User.lastname LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%')
							)
						));

		 }

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('pseudo', 'id', 'agent_number', 'has_audio'),
            'conditions' => $conditions,
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 10
        );

        $agents = $this->Paginator->paginate($this->User);

        $rows = array();
        foreach($agents as $key => $agent){
            $rows[] = array(
                'id'                => $agent['User']['id'],
                'pseudo'            =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id'])).'">'.$agent['User']['pseudo'].'</a>',
                'presentation_actuelle'    => ($agent['User']['has_audio'] == 1
                        ?'<audio src="/'.Configure::read('Site.pathPresentation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp3" controls preload="none" type="audio/mpeg"></audio>'
                        :__('Aucune présentation audio.')
                    ),

            );
        }

        $this->set(compact('rows'));
    }


	public function admin_delete_agent_presentation($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'present_audio', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Le code agent
        $agent_number = $this->User->field('agent_number');

        //Init les paramètres
        $files  = array(Configure::read('Site.pathPresentation').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp3');
        $form   = array(
            'model' => 'Agent',
            'note'  => __('La présentation audio sera effacée.')
        );
        $email  = array(
            'content'   => __('Votre présentation audio a été supprimée'),
            'subject'   => __('Présentation audio effacé')
        );

        //On delete le media
		$this->User->id = $id;
        $this->User->saveField('has_audio', 0);

        $this->deleteMedia($id,$url,__('Voulez-vous vraiment effacer la présentation audio ?'),$files,$form,$email);
    }


    public function admin_edit_valid_presentation($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_presentation', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('UserPresentValidation', $id, 'etat', 0, __('Aucune présentation trouvée'), $url, array(), ($this->request->is('ajax')));

        //Initialisation des paramètres
        $field = array(
            'name'      => 'texte',
            'primary'   => 'id'
        );
        $form = array(
            'model' => 'Agent',
            'title' => 'Présentation',
            'note'  => __('Après modification de la présentation, il est impossible de récupérer la présentation originale.')
        );
        $message = array(
            'error'     => __('Erreur lors de la modification de la présentation.'),
            'success'   => __('Présentation modifiée.')
        );
        //Edition de l'entite
        $this->editEntite('UserPresentValidation',$id,$url,$field,$form,__('Modification de la présentation'),$message);
    }

    public function admin_valid_photo(){
        //Les chemins des photos en attente
        $paths = glob(Configure::read('Site.pathPhotoValidation').'/[0-9]/[0-9]/*_listing.jpg');
        //On récupère les codes des agents
        $agentCodes = Tools::extractData($paths,'_');

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('pseudo', 'id', 'agent_number'),
            'conditions' => array('agent_number' => $agentCodes, 'deleted' => 0, 'role' => 'agent', 'active' => 1),
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 10
        );


        $agents = $this->Paginator->paginate($this->User);

        $rows = array();
        foreach($agents as $agent){
            $rows[] = array(
                'id'                => $agent['User']['id'],
                'pseudo'            =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id'])).'">'.$agent['User']['pseudo'].'</a>',
                'photo_actuelle'    => '<img src="/'.Configure::read('Site.pathPhoto').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'_listing.jpg">',
                'photo_validation'  => '<img src="/'.Configure::read('Site.pathPhotoValidation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'_listing.jpg">'
            );
        }

        $this->set(compact('rows'));
    }

    public function admin_accept_valid_photo($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_photo', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Valide le média
        $this->acceptMedia('Image',$id,$url);
    }

    public function admin_refuse_valid_photo($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_photo', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Le code agent
        $agent_number = $this->User->field('agent_number');

        //Init les paramètres
        $files = array(
            Configure::read('Site.pathPhotoValidationAdmin').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.jpg',
            Configure::read('Site.pathPhotoValidationAdmin').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'_listing.jpg'
        );
        $form   = array(
            'model' => 'Agent',
            'note'  => __('La photo sera effacée.')
        );
        $email  = array(
            'content'   => __('La modification de votre photo a été refusée'),
            'subject'   => __('Photo refusée')
        );

        //On refuse le media
        $this->refuseMedia($id,$url,__('Voulez-vous vraiment refuser la photo ?'),$files,$form,$email);
    }

    public function admin_valid_audio(){
        //Les chemins des présentations audio en attente
        $paths = glob(Configure::read('Site.pathPresentationValidation').'/[0-9]/[0-9]/*.mp3');
        //On récupère les codes des agents
        $agentCodes = Tools::extractData($paths,'.');

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('pseudo', 'id', 'agent_number', 'has_audio'),
            'conditions' => array('agent_number' => $agentCodes, 'deleted' => 0, 'role' => 'agent'),
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 10
        );

        $agents = $this->Paginator->paginate($this->User);

        $rows = array();
        foreach($agents as $key => $agent){
			if(is_file(Configure::read('Site.pathPresentationValidationAdmin').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp3'))
            $rows[] = array(
                'id'                => $agent['User']['id'],
                'pseudo'            =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id'])).'">'.$agent['User']['pseudo'].'</a>',
                'presentation_actuelle'    => ($agent['User']['has_audio'] == 1
                        ?'<audio src="/'.Configure::read('Site.pathPresentation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp3" controls preload="none" type="audio/mpeg"></audio>'
                        :__('Aucune présentation audio.')
                    ),
                'presentation_validation'  => '<audio src="/'.Configure::read('Site.pathPresentationValidation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp3" controls preload="none" type="audio/mpeg"></audio>'
            );
        }

        $this->set(compact('rows'));
    }

    public function admin_accept_valid_audio($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_audio', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Valide le média
        $this->acceptMedia('Audio',$id,$url);
    }

    public function admin_refuse_valid_audio($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_audio', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Le code agent
        $agent_number = $this->User->field('agent_number');

        //Init les paramètres
        $files  = array(Configure::read('Site.pathPresentationValidation').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp3');
        $form   = array(
            'model' => 'Agent',
            'note'  => __('La présentation audio sera effacée.')
        );
        $email  = array(
            'content'   => __('La modification de votre présentation audio a été refusée'),
            'subject'   => __('Présentation audio refusée')
        );

        //On refuse le media
        $this->refuseMedia($id,$url,__('Voulez-vous vraiment refuser la présentation audio ?'),$files,$form,$email);
    }


    public function admin_valid_video(){
        //Les chemins des présentations video en attente
        $paths = glob(Configure::read('Site.pathPresentationVideoValidation').'/[0-9]/[0-9]/*.mp4');
        //On récupère les codes des agents
        $agentCodes = Tools::extractData($paths,'.');

        //Les paramètres pour le paginator
        $this->Paginator->settings = array(
            'fields' => array('pseudo', 'id', 'agent_number', 'has_video'),
            'conditions' => array('agent_number' => $agentCodes, 'deleted' => 0, 'role' => 'agent'),
            'recursive' => -1,
            'paramType' => 'querystring',
            'limit' => 10
        );

        $agents = $this->Paginator->paginate($this->User);

        $rows = array();
        foreach($agents as $key => $agent){
			if(is_file(Configure::read('Site.pathPresentationVideoValidationAdmin').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp4'))
            $rows[] = array(
                'id'                => $agent['User']['id'],
                'pseudo'            =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id'])).'">'.$agent['User']['pseudo'].'</a>',
                'presentation_actuelle'    => ($agent['User']['has_video'] == 1
                        ? '<a href="/'.Configure::read('Site.pathPresentationVideo').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp4" target="_blank">' . __('Ouvrir') . '</a>'
                        :__('Aucune présentation video.')
                    ),
                'presentation_validation'  => '<a href="/'.Configure::read('Site.pathPresentationVideoValidation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'.mp4" target="_blank">' . __('Ouvrir') . '</a>'
            );
        }

        $this->set(compact('rows'));
    }

    public function admin_accept_valid_video($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_video', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Valide le média
        $this->acceptMedia('Video',$id,$url);
    }

    public function admin_refuse_valid_video($id){
        $url = Router::url(array('controller' => 'agents', 'action' => 'valid_video', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('User', $id, 'role', 'agent', 'Aucun agent trouvé', $url);

        //Le code agent
        $agent_number = $this->User->field('agent_number');

        //Init les paramètres
        $files  = array(Configure::read('Site.pathPresentationVideoValidation').'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp4');
        $form   = array(
            'model' => 'Agent',
            'note'  => __('La présentation video sera effacée.')
        );
        $email  = array(
            'content'   => __('La modification de votre présentation video a été refusée'),
            'subject'   => __('Présentation video refusée')
        );

        //On refuse le media
        $this->refuseMedia($id,$url,__('Voulez-vous vraiment refuser la présentation video ?'),$files,$form,$email);
    }

    public function admin_relance_mail_confirm($id){
        if(isset($this->request->query['view']))
            $this->relanceMailConfirm($id,'agents','view');
        else
            $this->relanceMailConfirm($id,'agents');
    }

    public function admin_confirm_mail($id){
        if(isset($this->request->query['view']))
            $this->confirmMail($id,'agents','view');
        else
            $this->confirmMail($id,'agents');
    }

    public function admin_record_audio($page = 1){
       /* //Le chemin des enregistrements audio
        $paths = glob(Configure::read('Site.pathRecord').'/*.wav');

		if(!empty($paths)){

			$this->loadModel('UserCreditLastHistory');

            //On récupère les infos pour chaque fichier
            $infoFile = array();
			$sessionFile = array();
			$userFile = array();
			$userFileID = array();
			$secondFile = array();
			$timesFile = array();
			$nameFile = array();
			$timestampFile = array();
            foreach($paths as $path){
                //Le nom du fichier
                $filename = basename($path);
                //On retire l'extension du fichier
                $filename = substr($filename,0,(strripos($filename,'.') - strlen($filename)));
                //On explose les données
                $tmp = explode('-',$filename);

				$nameFile[$tmp[1]] = $filename;
                if(isset($infoFile[$tmp[1]])){
                    $infoFile[$tmp[1]][] = $tmp[0];
					$sessionFile[$tmp[1]][] = $tmp[3];
				}else{
                    $infoFile[$tmp[1]] = array();
                    $infoFile[$tmp[1]][] = $tmp[0];
					$sessionFile[$tmp[1]] = array();
					$sessionFile[$tmp[1]][] = $tmp[3];
                }


				if($tmp[3]){

					$timestampFile[$tmp[1]] = $tmp[2].'-'.$tmp[3];

					$lastCom = $this->UserCreditLastHistory->find('first',array(
						'conditions' => array('sessionid LIKE' => '%'.$tmp[3]),
						'recursive' => -1
					));

					$user_id = $lastCom["UserCreditLastHistory"]["users_id"];
					$seconds = $lastCom["UserCreditLastHistory"]["seconds"];

					$this->loadModel('User');
					$customer = $this->User->find('first',array(
						'fields'        => array('id','firstname','lastname'),
						'conditions'    => array(
							'id' => $user_id,
						),
						'recursive'     => -1
					));
					$userFileID[$tmp[1]] =$customer["User"]["id"];
					$userFile[$tmp[1]] =$customer["User"]["firstname"];
					if($customer["User"]["lastname"] != 'AUDIOTEL')
					$userFile[$tmp[1]] .= ' '.$customer["User"]["lastname"];
					$minutes = intval(($seconds % 3600) / 60);
					$secondes =intval((($seconds % 3600) % 60));
					$secondFile[$tmp[1]] =$minutes.'m '.$secondes.'s';
					$timesFile[$tmp[1]] = $seconds;
				}
            }

            //A-t-il une recherche par nom ??
            if(isset($this->params->query['expert']) && !empty($this->params->query['expert'])){
                $agent_numbers = $this->User->find('list', array(
                    'fields'        => array('User.id', 'User.agent_number'),
                    'conditions'    => array('User.pseudo LIKE' => '%'.$this->params->query['expert'].'%'),
                    'recursive'     => -1
                ));

                //Si pas de résultat
                if(empty($agent_numbers)){
                    $this->Session->setFlash(__('Aucun résultat retourné.'), 'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true), false);
                }
            }

			//S'il y a une recherche par expert
            if(isset($agent_numbers) && !empty($agent_numbers)){
                foreach($infoFile as $key => $row){
                    if(!in_array($row[0], $agent_numbers))
                        unset($infoFile[$key]);
                }
            }

            //'S'il y a une recherche par date
            if($this->Session->check('Date')){
                $dateStart = CakeTime::fromString(CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'));
                $dateEnd = CakeTime::fromString(CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59'));
                foreach($infoFile as $key => $row){
                    if($key < $dateStart || $key > $dateEnd)
                        unset($infoFile[$key]);
                }
            }

			if(isset($this->params->query['timing']) && is_numeric($this->params->query['timing'])){
				foreach($infoFile as $key => $row){
					if($timesFile[$key] > $this->params->query['timing'])
						unset($infoFile[$key]);
				}
			}

			if(isset($this->params->query['timing_min']) && is_numeric($this->params->query['timing_min'])){
				foreach($infoFile as $key => $row){
					if($timesFile[$key] < $this->params->query['timing_min'])
						unset($infoFile[$key]);
				}
			}


            //Tri par date plus récent au plus vieux
            krsort($infoFile);
            //On sépare le tableau selon la limite de l'affichage
            $infoFile = array_chunk($infoFile, Configure::read('Site.limitFileAudio'), true);

            //Le nombre de page au total
            $pageCount = count($infoFile);

            //On récupère la page en paramètre
            if(isset($this->params->query['page']) && !empty($this->params->query['page']))
                $page = $this->params->query['page'];

            //Pour la page à retourné
            $page--;
            if($page <= 0)
                $page = 0;

            //Les élèments de la page en question
            $infoFile = (isset($infoFile[$page]) ?$infoFile[$page]:array());

            //On incremente la page, pour le rendu utilisateur
            $page++;

            $agentCodes = array();
            //On récupère les numéros des agents
            foreach($infoFile as $numbers){
                foreach($numbers as $number){
                    if(in_array($number, $agentCodes)) continue;
                    $agentCodes[] = $number;
                }
            }

            //Pseudo et id des agents
            $tmp_agents = $this->User->find('all',array(
                'fields' => array('id', 'pseudo', 'agent_number'),
                'conditions' => array('agent_number' => $agentCodes, 'deleted' => 0),
                'recursive' => -1
            ));

            $agents = array();
            //Si on a trouvé des agents
            if(!empty($tmp_agents)){
                //On met agent_number en clé
                foreach($tmp_agents as $agent){
                    $agents[$agent['User']['agent_number']] = $agent['User'];
                    //On unset agent_number
                    unset($agents[$agent['User']['agent_number']]['agent_number']);
                }
            }


            //On restructure infoFile
            $files = array();
            foreach($infoFile as $date => $numbers){
                foreach($numbers as $number){
                    if(isset($agents[$number])){

						$timestamp = $date;
						if($timestampFile[$date][0]) $timestamp .= '|'.str_replace('-','|',$timestampFile[$date]);

						$username = '';
						if($userFile[$date]){
							$username =	'<a href="'.Router::url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $userFileID[$date])).'">'.$userFile[$date].'</a>';
						}

                        $files[] = array(
                            'pseudo' =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agents[$number]['id'])).'">'.$agents[$number]['pseudo'].'</a>',
                            'date' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'), date('Y-m-d H:i:s',$date)), '%d %B %Y %Hh%M'),
                            'record' => '<audio controls="true" preload="none"><source src="/'.Configure::read('Site.pathRecord').'/'.$nameFile[$date].'.wav" type="audio/wav"></audio>',
                            'id' => $agents[$number]['id'],
                            'number' => $number,
							'sessionid' => $sessionFile[$date][0],
							'user' => $username,
							'time' => $secondFile[$date],
                            'timestamp' => $timestamp
                        );
                    }
                }
            }

            $this->set(compact('files', 'pageCount', 'page'));
        }else
            $this->set('files', array());*/
		 $this->loadModel('Record');
        //Les conditions de base
        $conditions = array('Record.archive' => 0, 'Record.deleted' => 0);

		if($this->params->data['Agent']['sessionid']){
			$conditions = array_merge($conditions, array(
                    'Record.sessionid' => $this->params->data['Agent']['sessionid'],
                ));
		}

        //On récupère les infos du dernier achat pour les clients
        $this->Paginator->settings = array(
            'fields' => array('Record.*','Agent.id','Agent.pseudo','Agent.agent_number', 'User.firstname', 'User.id', 'Comm.seconds'),
			'conditions' => $conditions,
            'order' => 'Record.id DESC',
            'paramType' => 'querystring',
			'joins' => array(
						array('table' => 'users',
							'alias' => 'User',
							'type' => 'inner',
							'conditions' => array('User.id = Record.user_id')
						),
						array('table' => 'users',
							'alias' => 'Agent',
							'type' => 'inner',
							'conditions' => array('Agent.id = Record.agent_id')
						),
						array('table' => 'user_credit_last_histories',
							'alias' => 'Comm',
							'type' => 'inner',
							'conditions' => array('Comm.sessionid = Record.sessionid')
						),
					),
            'limit' => 15
        );

        $lastRecord = $this->Paginator->paginate($this->Record);

        $this->set(compact('lastRecord'));

    }

    public function admin_record_audio_archive($page = 1){

		//Le chemin des enregistrements audio
     /*   $paths = glob(Configure::read('Site.pathRecordArchive').'/*.wav');

		if(!empty($paths)){

			$this->loadModel('UserCreditLastHistory');

            //On récupère les infos pour chaque fichier
            $infoFile = array();
			$sessionFile = array();
			$userFile = array();
			$userFileID = array();
			$secondFile = array();
			$timesFile = array();
			$nameFile = array();
			$timestampFile = array();
            foreach($paths as $path){
                //Le nom du fichier
                $filename = basename($path);
                //On retire l'extension du fichier
                $filename = substr($filename,0,(strripos($filename,'.') - strlen($filename)));
                //On explose les données
                $tmp = explode('-',$filename);

				$nameFile[$tmp[1]] = $filename;
                if(isset($infoFile[$tmp[1]])){
                    $infoFile[$tmp[1]][] = $tmp[0];
					$sessionFile[$tmp[1]][] = $tmp[3];
				}else{
                    $infoFile[$tmp[1]] = array();
                    $infoFile[$tmp[1]][] = $tmp[0];
					$sessionFile[$tmp[1]] = array();
					$sessionFile[$tmp[1]][] = $tmp[3];
                }



				if($tmp[3]){

					$timestampFile[$tmp[1]] = $tmp[2].'-'.$tmp[3];

					$lastCom = $this->UserCreditLastHistory->find('first',array(
						'conditions' => array('sessionid LIKE' => '%'.$tmp[3]),
						'recursive' => -1
					));

					$user_id = $lastCom["UserCreditLastHistory"]["users_id"];
					$seconds = $lastCom["UserCreditLastHistory"]["seconds"];

					$this->loadModel('User');
					$customer = $this->User->find('first',array(
						'fields'        => array('id','firstname','lastname'),
						'conditions'    => array(
							'id' => $user_id,
						),
						'recursive'     => -1
					));
					$userFileID[$tmp[1]] =$customer["User"]["id"];
					$userFile[$tmp[1]] =$customer["User"]["firstname"];
					if($customer["User"]["lastname"] != 'AUDIOTEL')
					$userFile[$tmp[1]] .= ' '.$customer["User"]["lastname"];
					$minutes = intval(($seconds % 3600) / 60);
					$secondes =intval((($seconds % 3600) % 60));
					$secondFile[$tmp[1]] =$minutes.'m '.$secondes.'s';
					$timesFile[$tmp[1]] = $seconds;
				}
            }

            //A-t-il une recherche par nom ??
            if(isset($this->params->query['expert']) && !empty($this->params->query['expert'])){
                $agent_numbers = $this->User->find('list', array(
                    'fields'        => array('User.id', 'User.agent_number'),
                    'conditions'    => array('User.pseudo LIKE' => '%'.$this->params->query['expert'].'%'),
                    'recursive'     => -1
                ));

                //Si pas de résultat
                if(empty($agent_numbers)){
                    $this->Session->setFlash(__('Aucun résultat retourné.'), 'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true), false);
                }
            }

			//S'il y a une recherche par expert
            if(isset($agent_numbers) && !empty($agent_numbers)){
                foreach($infoFile as $key => $row){
                    if(!in_array($row[0], $agent_numbers))
                        unset($infoFile[$key]);
                }
            }

            //'S'il y a une recherche par date
            if($this->Session->check('Date')){
                $dateStart = CakeTime::fromString(CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'));
                $dateEnd = CakeTime::fromString(CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59'));
                foreach($infoFile as $key => $row){
                    if($key < $dateStart || $key > $dateEnd)
                        unset($infoFile[$key]);
                }
            }

			if(isset($this->params->query['timing']) && is_numeric($this->params->query['timing'])){
				foreach($infoFile as $key => $row){
					if($timesFile[$key] > $this->params->query['timing'])
						unset($infoFile[$key]);
				}
			}

			if(isset($this->params->query['timing_min']) && is_numeric($this->params->query['timing_min'])){
				foreach($infoFile as $key => $row){
					if($timesFile[$key] < $this->params->query['timing_min'])
						unset($infoFile[$key]);
				}
			}


            //Tri par date plus récent au plus vieux
            krsort($infoFile);
            //On sépare le tableau selon la limite de l'affichage
            $infoFile = array_chunk($infoFile, Configure::read('Site.limitFileAudio'), true);

            //Le nombre de page au total
            $pageCount = count($infoFile);

            //On récupère la page en paramètre
            if(isset($this->params->query['page']) && !empty($this->params->query['page']))
                $page = $this->params->query['page'];

            //Pour la page à retourné
            $page--;
            if($page <= 0)
                $page = 0;

            //Les élèments de la page en question
            $infoFile = (isset($infoFile[$page]) ?$infoFile[$page]:array());

            //On incremente la page, pour le rendu utilisateur
            $page++;

            $agentCodes = array();
            //On récupère les numéros des agents
            foreach($infoFile as $numbers){
                foreach($numbers as $number){
                    if(in_array($number, $agentCodes)) continue;
                    $agentCodes[] = $number;
                }
            }

            //Pseudo et id des agents
            $tmp_agents = $this->User->find('all',array(
                'fields' => array('id', 'pseudo', 'agent_number'),
                'conditions' => array('agent_number' => $agentCodes, 'deleted' => 0),
                'recursive' => -1
            ));

            $agents = array();
            //Si on a trouvé des agents
            if(!empty($tmp_agents)){
                //On met agent_number en clé
                foreach($tmp_agents as $agent){
                    $agents[$agent['User']['agent_number']] = $agent['User'];
                    //On unset agent_number
                    unset($agents[$agent['User']['agent_number']]['agent_number']);
                }
            }


            //On restructure infoFile
            $files = array();
            foreach($infoFile as $date => $numbers){
                foreach($numbers as $number){
                    if(isset($agents[$number])){

						$timestamp = $date;
						if($timestampFile[$date][0]) $timestamp .= '|'.str_replace('-','|',$timestampFile[$date]);

						$username = '';
						if($userFile[$date]){
							$username =	'<a href="'.Router::url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $userFileID[$date])).'">'.$userFile[$date].'</a>';
						}

                        $files[] = array(
                            'pseudo' =>  '<a href="'.Router::url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agents[$number]['id'])).'">'.$agents[$number]['pseudo'].'</a>',
                            'date' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'), date('Y-m-d H:i:s',$date)), '%d %B %Y %Hh%M'),
                            'record' => '<audio controls="true" preload="none"><source src="/'.Configure::read('Site.pathRecord').'/'.$nameFile[$date].'.wav" type="audio/wav"></audio>',
                            'id' => $agents[$number]['id'],
                            'number' => $number,
							'sessionid' => $sessionFile[$date][0],
							'user' => $username,
							'time' => $secondFile[$date],
                            'timestamp' => $timestamp
                        );
                    }
                }
            }

            $this->set(compact('files', 'pageCount', 'page'));
        }else
            $this->set('files', array());
			*/

		 $this->loadModel('Record');
        //Les conditions de base
        $conditions = array('Record.archive' => 1, 'Record.deleted' => 0);


		if($this->params->data['Agent']['sessionid']){
			$conditions = array_merge($conditions, array(
                    'Record.sessionid' => $this->params->data['Agent']['sessionid'],
                ));
		}

        //On récupère les infos du dernier achat pour les clients
        $this->Paginator->settings = array(
            'fields' => array('Record.*','Agent.id','Agent.pseudo','Agent.agent_number', 'User.firstname', 'User.id', 'Comm.seconds'),
			'conditions' => $conditions,
            'order' => 'Record.id DESC',
            'paramType' => 'querystring',
			'joins' => array(
						array('table' => 'users',
							'alias' => 'User',
							'type' => 'inner',
							'conditions' => array('User.id = Record.user_id')
						),
						array('table' => 'users',
							'alias' => 'Agent',
							'type' => 'inner',
							'conditions' => array('Agent.id = Record.agent_id')
						),
						array('table' => 'user_credit_last_histories',
							'alias' => 'Comm',
							'type' => 'inner',
							'conditions' => array('Comm.sessionid = Record.sessionid')
						),
					),
            'limit' => 15
        );

        $lastRecord = $this->Paginator->paginate($this->Record);

        $this->set(compact('lastRecord'));

    }

    public function admin_download_record($id){


		//Si un des deux paramètres est vide alors redirection page enregistrement audio
		if(empty($id) || !is_numeric($id) ){
			$this->Session->setFlash(__('Le fichier est introuvable'), 'flash_error');
			$this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true),false);
		}

		$this->loadModel('Record');
		$record = $this->Record->find('first',array(
					'conditions' => array('id' => $id)
				));
		$filename = '';
		if($record){
			if($record['Record']['archive'])
				$filename = str_replace('/records','/records_archive',Configure::read('Site.pathRecord')).'/'.$record['Record']['filename'];
			else
				$filename = Configure::read('Site.pathRecord').'/'.$record['Record']['filename'];
		}
			//Si le fichier n'existe pas

        if(file_exists($filename) === false){
            //Redirection enregistrement audio
            $this->Session->setFlash(__('Le fichier est introuvable'),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true),false);
        }

        //Sinon téléchargement du fichier
        $this->response->file($filename, array('download' => true));
        return $this->response;
    }

    public function admin_delete_record($id){
       if(empty($id) || !is_numeric($id) ){
			$this->Session->setFlash(__('Le fichier est introuvable'), 'flash_error');
			$this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true),false);
		}

		$this->loadModel('Record');
		$record = $this->Record->find('first',array(
					'conditions' => array('id' => $id)
				));
		$filename = '';
		if($record){
			if($record['Record']['archive'])
				$filename = str_replace('/records','/records_archive',Configure::read('Site.pathRecord')).'/'.$record['Record']['filename'];
			else
				$filename = Configure::read('Site.pathRecord').'/'.$record['Record']['filename'];
		}
			//Si le fichier n'existe pas

        if(file_exists($filename)){
            //Suppression du fichier
            if(unlink($filename))
                $this->Session->setFlash(__('Le fichier a été supprimé.'),'flash_success');
            else
                $this->Session->setFlash(__('Echec de la suppression.'), 'flash_warning');
        }else
            //Redirection enregistrement audio
            $this->Session->setFlash(__('Le fichier est introuvable'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true),false);
    }

    public function admin_activate_record($id){
        $this->_adminChangeRecord($id, 1);
    }

    public function admin_deactivate_record($id){
        $this->_adminChangeRecord($id, 0);
    }

    public function admin_activate_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');
		$pseudo = $this->User->field('pseudo');
		$agent_number = $this->User->field('agent_number');
		$stripe_account = $this->User->field('stripe_account');
        //Si c'est bien un agent
        if($role === 'agent'){

			//rediriger vers home

			$this->loadModel('Redirect');
			$this->loadModel('Domain');
			$this->loadModel('Lang');

			$domains_nolive = explode(',',Configure::read('Site.id_domain_com'));
			$domaines = $this->Domain->find('all',array(
								'conditions' => array('active' => 1),
								'recursive' => -1,
							));
			foreach($domaines as $domain){
				if(! in_array($domain['Domain']['id'],$domains_nolive ) ){
					$lang = $this->Lang->find('first',array(
								'conditions' => array('id_lang' => $domain['Domain']['default_lang_id']),
								'recursive' => -1,
							));

					if($lang['Lang']['language_code']){
						/*$url = Router::url(
							array(
								'controller'      => 'agents',
								'action'          => 'display',
								'language'        => $lang['Lang']['language_code'],
								'link_rewrite'    => strtolower($pseudo),
								'agent_number'    => $agent_number
							),true
						);*/
						$link = 'agents/'.strtolower($pseudo).'-'.$agent_number;
						$url_old = '/'.$lang['Lang']['language_code'].'/'.$link;
						$redirect = $this->Redirect->find('first',array(
								'conditions' => array('old' => $url_old),
								'recursive' => -1,
							));
						$this->Redirect->delete($redirect['Redirect']['id'],false);
					}
				}
			}

			//creer le compte stripe
			$is_create = true;
			if(!$stripe_account)$is_create = $this->createStripeAccount($id);

			if($is_create){
				//valider Survey
				$this->loadModel('Survey');
				$conditions = array(
								'Survey.user_id' => $id, 'Survey.is_valid' => 0
					);

				$survey = $this->Survey->find('first',array('conditions' => $conditions));
				if($survey){
					$this->Survey->id = $survey['Survey']['id'];
					$this->Survey->saveField('is_valid', 1);
				}

				$this->changeCompte($id, 'agents', array(
					'success' => 'Le compte a été activé. Email envoyé.',
					'warning' => 'L\'activation du compte a échoué.',
					'email'   => 'Votre compte a été activé.'
				));
			}
		}else
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true),false);
    }

    public function admin_deactivate_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');
		$pseudo = $this->User->field('pseudo');
		$agent_number = $this->User->field('agent_number');
        //Si c'est bien un agent
        if($role === 'agent'){


			//rediriger vers home

			$this->loadModel('Redirect');
			$this->loadModel('Domain');
			$this->loadModel('Lang');

			$domains_nolive = explode(',',Configure::read('Site.id_domain_com'));
			$domaines = $this->Domain->find('all',array(
								'conditions' => array('active' => 1),
								'recursive' => -1,
							));
			foreach($domaines as $domain){
				if(! in_array($domain['Domain']['id'],$domains_nolive ) ){
					$lang = $this->Lang->find('first',array(
								'conditions' => array('id_lang' => $domain['Domain']['default_lang_id']),
								'recursive' => -1,
							));

					if($lang['Lang']['language_code']){
						/*$url = Router::url(
							array(
								'controller'      => 'agents',
								'action'          => 'display',
								'language'        => $lang['Lang']['language_code'],
								'link_rewrite'    => strtolower($pseudo),
								'agent_number'    => $agent_number
							),true
						);*/
						$link = 'agents/'.strtolower($pseudo).'-'.$agent_number;
						$url_old = '/'.$lang['Lang']['language_code'].'/'.$link;
						$url_new = 'https://'.$domain['Domain']['domain'].'/';

						$redirectData = array();
						$redirectData['Redirect'] = array();
						$redirectData['Redirect']['type'] = "301";
						$redirectData['Redirect']['domain_id'] = $domain['Domain']['id'];
						$redirectData['Redirect']['old'] = $url_old;
						$redirectData['Redirect']['new'] = $url_new;
						$this->Redirect->create();
						$this->Redirect->save($redirectData);
					}
				}
			}

			$this->changeCompte($id, 'agents', array(
                    'success' => 'Le compte a été désactivé.',
                    'warning' => 'La désactivation du compte a échoué.'),
                false);

		}else{
            $this->Session->setFlash(__('Cet utilisateur n\'existe pas ou n\'est pas un agent.'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true),false);
        }
    }

    public function admin_delete_user($id){
        //On récupère les infos de l'user
        $user = $this->User->find('first', array(
            'fields'        => array('User.role', 'User.agent_number'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent'),
            'recursive'     => -1
        ));

        //Si l'agent existe
        if(!empty($user)){
            //On supprime l'agent
            if($this->User->delete_user($id)){
                //On le supprime au niveau de l'api
                $api = new Api();
                $result = $api->deactivateAgent($user['User']['agent_number']);

                //S'il y a eu une erreur
                if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0)){
                    //Il faut annuler la suppression du compte
                    $this->User->id = $id;
                    $this->User->saveField('deleted', 0);
                    $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Echec de la désactivation de l\'agent au niveau de l\'API.')),'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true),false);
                }

                $this->Session->setFlash(__('Le compte a été supprimé.'));
            }
            else
                $this->Session->setFlash(__('Erreur lors de la suppression.'));

            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
    }

    public function admin_note($id){
        //On sauve la note
        $this->_adminNote($id,'Agent');
    }

    public function admin_change_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.agent_status'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
            $result = $this->setStatus($agent['User']['agent_number'], ($agent['User']['agent_status'] === 'available' ?'unavailable':'available'));

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $id));
			$agent_status = $this->User->field('agent_status', array('id' => $id));
			$connexion = array(
									'user_id'          	=> $id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'status'			=> $agent_status,
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result))
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
    }

	public function admin_consult_phone_modify_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_phone'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			$status = 0;
			if($agent['User']['consult_phone'] == 0){
				$status = 1;
			}else{
				if($agent['User']['consult_phone'] == 1)
					$status = 0;
				else{
					if($agent['User']['consult_phone'] == -1)
						$status = -1;
				}
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_phone', $status);
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$api = new Api();
            //Si l'agent est disponible
            if(strcmp($agent_status, 'available') == 0){
                if($status == 1){
                    //Connection de l'agent sur la plateforme téléphonique
                    $result = $api->connectAgent($agent['User']['agent_number']);
				}else{
                    //Déconnection de l'agent sur la plateforme téléphonique
                    $result = $api->deconnectAgent($agent['User']['agent_number']);
				}
            }

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));
			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);



            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result) && $result['response_code'])
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}


    public function admin_consult_phone_change_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_phone'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_phone'] == -1){
				$status = 1;
			}else{
				$status = -1;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_phone', $status);
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$api = new Api();
            //Si l'agent est disponible
            if(strcmp($agent_status, 'available') == 0){
                if($requestData['Agent']['consult_phone'] == 1){
                    //Connection de l'agent sur la plateforme téléphonique
                    //$result = $api->connectAgent($agent['User']['agent_number']);
				}else{
                    //Déconnection de l'agent sur la plateforme téléphonique
                    $result = $api->deconnectAgent($agent['User']['agent_number']);
					//$this->User->saveField('agent_status', 'unavailable');
				}
			}

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

			//Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result) && $result['response_code'] != 0)
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}

	public function admin_consult_mail_modify_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_email'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_email'] == -1 || $agent['User']['consult_email'] == 0){
				$status = 1;
			}else{
				$status = 0;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_email', $status);
			//$this->User->saveField('agent_status', 'unavailable');

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result))
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}



	public function admin_consult_mail_change_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_email'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_email'] == -1){
				$status = 1;
			}else{
				$status = -1;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_email', $status);




			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));


			$consult_mode = 0;
			if($consult_email > 0)$consult_mode = 1;
			if($consult_chat > 0)$consult_mode = 1;
			if($consult_phone > 0)$consult_mode = 1;
			if(!$consult_mode)$this->User->saveField('agent_status', 'unavailable');

			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));

			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result))
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}

	public function admin_consult_chat_modify_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_chat'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_chat'] == -1 || $agent['User']['consult_chat'] == 0  ){
				$status = 1;
			}else{
				$status = 0;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_chat', $status);

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result))
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}


	public function admin_consult_chat_change_status($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_chat'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_chat'] == -1){
				$status = 1;
			}else{
				$status = -1;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->User->saveField('consult_chat', $status);

			$this->loadModel('UserConnexion');
			$consult_email = $this->User->field('consult_email', array('id' => $this->User->id));
			$consult_chat = $this->User->field('consult_chat', array('id' => $this->User->id));
			$consult_phone = $this->User->field('consult_phone', array('id' => $this->User->id));
			$agent_status = $this->User->field('agent_status', array('id' => $this->User->id));
			$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => '',
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'who'				=> $this->Auth->user('id'),
									'mail'            	=> $consult_email,
									'tchat'      		=> $consult_chat,
									'phone'    			=> $consult_phone
								);
			$this->UserConnexion->create();
			$this->UserConnexion->save($connexion);

            //Si false
            if($result === false)
                $this->Session->setFlash(__('Echec lors de la modification du statut.'), 'flash_warning');
            elseif(is_array($result))
                $this->Session->setFlash(__('Statut modifié, cependant une erreur api est survenue : '.(isset($result['apiConnect']) ?$result['apiConnect']:$result['apiDeconnect'])), 'flash_warning');
            else
                $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}

	public function admin_consult_debug($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);

        //L'user existe t-il et est-ce un agent ??
        $agent = $this->User->find('first', array(
            'fields'        => array('User.agent_number', 'User.consult_chat'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Cet expert n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true), false);
        }

        //Si le statut est différent de busy
        if($agent['User']['agent_status'] !== 'busy'){
			if($agent['User']['consult_chat'] == 2){
				$status = 1;
			}else{
				$status = 2;
			}
			 $this->User->id = $this->User->field('id',array(
				'agent_number'  => $agent['User']['agent_number'],
				'role'          => 'agent',
				'active'        => 1,
				'deleted'       => 0
			));
			$this->setStatus($agent['User']['agent_number'], 'available', true);

            $this->Session->setFlash(__('Statut modifié.'), 'flash_success');

        }else
            $this->Session->setFlash(__('L\'expert est occupé, impossible de modifier son statut.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

	}

//--------------------------------------------------------------------------------------------API-------------------------------------------------------------------------
    public function __api_setaudiotelconsult($parms=false)
    {
        $customer_audiotel = 999999;

        //On vérifie les paramètres
        $agent_number    = (isset($parms['agent_number']) && !empty($parms['agent_number']))?(int)$parms['agent_number']:false;
        $phone_number    = (isset($parms['phone_number']) && !empty($parms['phone_number']))?$parms['phone_number']:false;
        $called_number   = (isset($parms['called_number']) && !empty($parms['called_number'])) ?$parms['called_number']:false;
		$sessionid   	 = (isset($parms['sessionid']) && !empty($parms['sessionid'])) ?$parms['sessionid']:false;
        $timestamp_start = (isset($parms['timestamp_start']) && !empty($parms['timestamp_start'])) ?$parms['timestamp_start']:false;
        $timestamp_end   = (isset($parms['timestamp_end']) && !empty($parms['timestamp_end'])) ?$parms['timestamp_end']:false;

        if (!$agent_number)   return array('response_code' => 23, 'response' => false);
        if (!$phone_number)   return array('response_code' => 13, 'response' => false);
        if (!$called_number)  return array('response_code' => 13, 'response' => false);
		if (!$sessionid) 	  return array('response_code' => 13, 'response' => false);
        if (!$timestamp_start)return array('response_code' => 26, 'response' => false);
        if (!$timestamp_end)  return array('response_code' => 26, 'response' => false);
        if ($timestamp_end<=$timestamp_start)  return array('response_code' => 27, 'response' => false);


		if($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database'],$dbb_connect['port']);
			$mysqli_connect->query("UPDATE call_infos SET time_start = '{$timestamp_start}', time_end = '{$timestamp_end}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}

        //Durée de la communication
            $comSecond = $timestamp_end - $timestamp_start;

        //On récupère les infos nécessaires pour enregistrer une consultation
        $this->loadModel('User');
        //Le client
        $agent = $this->User->find('first',array(
            'fields'        => array('id','pseudo'),
            'conditions'    => array(
                'agent_number' => $agent_number,
                'role'          => 'agent',
                'active'        => 1,
                'deleted'       => 0
            ),
            'recursive'     => -1
        ));
        if (empty($agent))return array('response_code' => 15, 'response' => false);
        $agent_id = isset($agent['User']['id'])?(int)$agent['User']['id']:false;
        if (!$agent_id)return array('response_code' => 15, 'response' => false);


        /* On recherche le numero client de Audiotel */
            $customer = $this->User->find('first',array(
                'fields'        => array('id'),
                'conditions'    => array(
                    'personal_code' => $customer_audiotel,
                    'role'          => 'client'
                ),
                'recursive'     => -1
            ));
            if (empty($customer))return array('response_code' => 14, 'response' => false);
            $customer_id = isset($customer['User']['id'])?(int)$customer['User']['id']:false;
            if (!$customer_id)return array('response_code' => 14, 'response' => false);

        /* On enregistre les valeurs */
            $this->loadModel('UserCreditLastHistory');
            $this->UserCreditLastHistory->create();
            $res1 = $this->UserCreditLastHistory->save(array(
                'users_id'      => $customer_id,
                'agent_id'      => $agent_id,
                'agent_pseudo'  => $agent['User']['pseudo'],
                'media'         => 'phone',
                'phone_number'  => $phone_number,
                'called_number' => $called_number,
				'sessionid'     => $sessionid,
                'credits'       => 0,
                'seconds'       => $comSecond,
                'user_credits_before' => 0,
                'user_credits_after'  => 0,
                'date_start'    => date('Y-m-d H:i:s',$timestamp_start),
                'date_end'      => date('Y-m-d H:i:s',$timestamp_end)
            ));

        /* On enregistre les valeurs */
        $this->loadModel('UserCreditHistory');
        $this->UserCreditHistory->create();
        $res2 = $this->UserCreditHistory->save(array(
            'user_id'       => $customer_id,
            'agent_id'      => $agent_id,
            'agent_pseudo'  => $agent['User']['pseudo'],
            'media'         => 'phone',
            'phone_number'  => $phone_number,
            'called_number' => $called_number,
			'sessionid'     => $sessionid,
            'credits'       => 0,
            'seconds'       => $comSecond,
            'user_credits_before' => 0,
            'user_credits_after'  => 0,
            'date_start'    => date('Y-m-d H:i:s',$timestamp_start),
            'date_end'      => date('Y-m-d H:i:s',$timestamp_end)
        ));

        if($res1 && $res2)
            return array('response_code' => 0, 'response' => true);
        else
            return array('response_code' => 17, 'response' => false);


    }

    public function __api_setStatus($parms=false)
    {
        if (!isset($parms['agent_number']) || !isset($parms['statut']))
            return array('response_code' => 13, 'response' => false);

		$this->loadModel('CallInfo');

		$sessionid = (isset($parms['sessionid']) ? $parms['sessionid']:false);
		$timestamp = (isset($parms['timestamp']) ? $parms['timestamp']:false);
		$accepted = (isset($parms['accepted']) ? $parms['accepted']:false);
		$reason = (isset($parms['reason']) ? $parms['reason']:false);

		if($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database'],$dbb_connect['port']);
			if($reason){
				$mysqli_connect->query("UPDATE call_infos SET time_setstatut_end = '{$timestamp}',accepted = '{$accepted}',reason = '{$reason}' WHERE sessionid = '{$sessionid}'");
			}else{

				$result_check = $mysqli_connect->query("SELECT agent,agent1,agent2,agent3,agent4,agent5 from call_infos WHERE sessionid = '{$sessionid}'");
				$row_check = $result_check->fetch_array(MYSQLI_ASSOC);

				if($row_check['agent5']){
					$mysqli_connect->query("UPDATE call_infos SET time_setstatut5 = '{$timestamp}',accepted5 = '{$accepted}',reason5 = '{$reason}' WHERE sessionid = '{$sessionid}'");
				}else{
					if($row_check['agent4']){
						$mysqli_connect->query("UPDATE call_infos SET time_setstatut4 = '{$timestamp}',accepted4 = '{$accepted}',reason4 = '{$reason}' WHERE sessionid = '{$sessionid}'");
					}else{
						if($row_check['agent3']){
							$mysqli_connect->query("UPDATE call_infos SET time_setstatut3 = '{$timestamp}',accepted3 = '{$accepted}',reason3 = '{$reason}' WHERE sessionid = '{$sessionid}'");
						}else{
							if($row_check['agent2']){
								$mysqli_connect->query("UPDATE call_infos SET time_setstatut2 = '{$timestamp}',accepted2 = '{$accepted}',reason2 = '{$reason}' WHERE sessionid = '{$sessionid}'");
							}else{
								if($row_check['agent1']){
									$mysqli_connect->query("UPDATE call_infos SET time_setstatut1 = '{$timestamp}',accepted1 = '{$accepted}',reason1 = '{$reason}' WHERE sessionid = '{$sessionid}'");
								}else{
									$mysqli_connect->query("UPDATE call_infos SET time_setstatut = '{$timestamp}',accepted = '{$accepted}',reason = '{$reason}' WHERE sessionid = '{$sessionid}'");
								}
							}
						}
					}
				}

			}

			$mysqli_connect->close();
		}

		$dt = new DateTime(date('Y-m-d H:i:s'));
		$dt->modify('- 30 minutes');
		$delai = $dt->format('Y-m-d H:i:s');
		$delay2 = $dt->getTimestamp();
		$call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.agent'),
						'conditions' => array('sessionid' => $sessionid),
						'recursive' => 0
		 ));

		$calllive = $this->CallInfo->find('first',array(
						'conditions' => array('sessionid !=' => $sessionid, 'time_stop' => null, 'agent' => $call['CallInfo']['agent'],'time_getstatut >' => $delay2),
						'recursive' => 0
		 ));
		if(empty($calllive) ){
        	return $this->setStatus($parms['agent_number'],$parms['statut'],true);
		}else{
			 return array('response_code' => 0, 'response' => true);
		}

        //Si établissement d'une communication
        if(isset($parms['called_number'])){
            //Un appel surtaxé ??
            $this->loadModel('CountryLangPhone');
            $typeCall = $this->CountryLangPhone->getTypeCall($parms['called_number']);

            if($typeCall === 'surtaxed'){
                //Nouvelle communication surtaxé
                if($parms['statut'] === 'busy')
                    $result = $this->start_consult_surtaxed($parms['agent_number'], $parms['called_number']);
                //Fin d'une communication surtaxé
                elseif($parms['statut'] === 'available')
                    $result = $this->end_consult_surtaxed($parms['agent_number']);

                //Si le retour est un tableau alors erreur
                if(is_array($result))
                    return $result;
            }
        }

        if($parms['statut'] === 'available'){
            $result = $this->end_consult_surtaxed($parms['agent_number']);

            //Si le retour est un tableau alors erreur
            if(is_array($result)){
                if(!isset($result['response_code']) || $result['response_code'] != 21)
                    return $result;
            }
        }

        return $this->setStatus($parms['agent_number'],$parms['statut'],true);
    }

    public function __api_getStatus($parms=false)
    {

		$sessionid = (isset($parms['sessionid']) ?$parms['sessionid']:false);
        $agent_number = isset($parms['agent_number']) ?(int)$parms['agent_number']:false;
		$timestamp = (isset($parms['timestamp']) ?$parms['timestamp']:false);

        if (!$agent_number)
               return array('response_code' => 15, 'response' => false);

		$this->loadModel('User');
		$agent = $this->User->find('first',array(
            'fields'        => array('id','pseudo'),
            'conditions'    => array(
                'agent_number'  => $agent_number,
                'role'          => 'agent',
                'active'        => 1,
                'deleted'       => 0
            ),
            'recursive'     => -1
        ));

		$this->loadModel('UserCreditLastHistory');
        $consult = $this->UserCreditLastHistory->find('first',array(
            'conditions' => array(
                'date_end' => null,
                'agent_id' => $agent['User']['id']
            ),
            'recursive' => -1
        ));


        //Agent en consultation
        if(!empty($consult))
            return array('response_code' => 19, 'response' => false);


        $result = $this->getStatus($agent_number);

		if($sessionid && !empty($result)){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database'],$dbb_connect['port']);

			$result_check = $mysqli_connect->query("SELECT agent from call_infos WHERE sessionid = '{$sessionid}'");
			$row_check = $result_check->fetch_array(MYSQLI_ASSOC);
			$the_agent = $row_check['agent'];

			if(!$the_agent){
				$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}' WHERE sessionid = '{$sessionid}'");
			}else{
				if(!$row_check['agent1'] && $the_agent != $agent_number){
					$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}', agent1 = '{$the_agent}' WHERE sessionid = '{$sessionid}'");
				}else{
					if(!$row_check['agent2'] && $the_agent != $agent_number){
						$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}', agent2 = '{$the_agent}' WHERE sessionid = '{$sessionid}'");
					}else{
						if(!$row_check['agent3'] && $the_agent != $agent_number){
							$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}', agent3 = '{$the_agent}' WHERE sessionid = '{$sessionid}'");
						}else{
							if(!$row_check['agent4'] && $the_agent != $agent_number){
								$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}', agent4 = '{$the_agent}' WHERE sessionid = '{$sessionid}'");
							}else{
								if(!$row_check['agent5'] && $the_agent != $agent_number){
									$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}', agent5 = '{$the_agent}' WHERE sessionid = '{$sessionid}'");
								}else{
									if($row_check['agent5'] && $the_agent != $agent_number){
										$mysqli_connect->query("UPDATE call_infos SET time_getstatut = '{$timestamp}', agent = '{$agent_number}' WHERE sessionid = '{$sessionid}'");
									}
								}
							}
						}
					}
				}
			}


			$mysqli_connect->close();
		}

        if (!$result)
            return array('response_code' => 15, 'response' => false);
        elseif (!empty($result))
            return array('response_code' => 0, 'response' => $result);
        else return array('response_code' => 999, 'response' => false);
    }

	public function __api_agentalert($parms=false)//retour alert agent
    {

        $agent_number = isset($parms['agent']) ?(int)$parms['agent']:false;
		$answer = isset($parms['answer']) ?$parms['answer']:false;

        if (!$agent_number)
               return array('response_code' => 15, 'response' => false);

		$this->loadModel('User');
        $agent = $this->User->find('first',array(
            'conditions' => array(
                'agent_number' => $agent_number
            ),
            'recursive' => -1
        ));

		$this->loadModel('Chat');
        $consult = $this->Chat->find('first',array(
            'conditions' => array(
                'to_id' => $agent['User']['id']
            ),
			'order' => 'id DESC',
            'recursive' => -1
        ));
		if($consult['Chat']['id'] && $answer  == 'yes'){
			$this->Chat->id = $consult['Chat']['id'];
			//$this->Chat->saveField('alert', 2);
			$dbb_r = new DATABASE_CONFIG();
			$dbb_mysql = $dbb_r->default;
			$mysqli_sql = new mysqli($dbb_mysql['host'], $dbb_mysql['login'], $dbb_mysql['password'], $dbb_mysql['database'],$dbb_mysql['port']);
			$mysqli_sql->query("UPDATE chats set alert = 2 WHERE id ='{$this->Chat->id}'");
			$mysqli_sql->close();
		}
		 return array('response_code' => 0, 'response' =>$agent_number.' - '. $answer .' - ' . $consult['Chat']['id']);
		/*
        if (!$result)
            return array('response_code' => 15, 'response' => false);
        elseif (!empty($result))
            return array('response_code' => 0, 'response' => $result);
        else return array('response_code' => 999, 'response' => false);
		*/
    }


	public function __api_showStatus($parms=false)
    {

		$sessionid = (isset($parms['sessionid']) ?$parms['sessionid']:false);
        $agent_number = isset($parms['agent_number']) ?(int)$parms['agent_number']:false;
		$active = isset($parms['active']) ?(int)$parms['active']:false;
		$connected = isset($parms['connected']) ?(int)$parms['connected']:false;
		$timestamp = (isset($parms['timestamp']) ?$parms['timestamp']:false);

        if (!$agent_number)
               return array('response_code' => 15, 'response' => false);

		if($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database'],$dbb_connect['port']);
			$mysqli_connect->query("UPDATE call_infos SET time_check = '{$timestamp}', agent = '{$agent_number}', active = '{$active}', connected = '{$connected}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}

        $result = $this->getStatus($agent_number);

        if (!$result)
            return array('response_code' => 15, 'response' => false);
        elseif (!empty($result))
            return array('response_code' => 0, 'response' => $result);
        else return array('response_code' => 999, 'response' => false);
    }

    private function start_consult_surtaxed($agent_number, $called_number){
        if(empty($agent_number) || empty($called_number))
            return array('response_code' => 13, 'response' => false);

        $dateNow = date('Y-m-d H:i:s');

        //On récupère l'agent
        $agent = $this->User->find('first', array(
            'fields'        => array('User.id', 'User.pseudo'),
            'conditions'    => array('User.agent_number' => $agent_number, 'User.role' => 'agent', 'User.agent_status' => 'available', 'User.deleted' => 0, 'User.active' => 1),
            'recursive'     => -1
        ));

        if(empty($agent))
            return array('response_code' => 15, 'response' => false);

        //L'agent a-t-il une communication en cours
        $this->loadModel('UserCreditHistory');
        $com = $this->UserCreditHistory->find('first', array(
            'conditions'        => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_end' => null),
            'recursive'         => -1
        ));

        //Oui, il y a une communication en cours
        if(!empty($com))
            return array('response_code' => 19, 'response' => false);

        //On save les infos de la communication
        $saveData = array(
            'agent_id'      => $agent['User']['id'],
            'agent_pseudo'  => $agent['User']['pseudo'],
            'media'         => 'phone',
            'called_number' => $called_number,
            'date_start'    => $dateNow
        );

        $this->UserCreditHistory->create();
        if($this->UserCreditHistory->save($saveData))
            return true;
        else
            return array('response_code' => 999, 'response' => false);
    }

    private function end_consult_surtaxed($agent_number){
        if(empty($agent_number))
            return array('response_code' => 13, 'response' => false);

        $dateNow = date('Y-m-d H:i:s');

        //On récupère l'agent
        $agent = $this->User->find('first', array(
            'fields'        => array('User.id', 'User.pseudo'),
            'conditions'    => array('User.agent_number' => $agent_number, 'User.role' => 'agent', 'User.agent_status' => 'busy', 'User.deleted' => 0, 'User.active' => 1),
            'recursive'     => -1
        ));

        if(empty($agent))
            return array('response_code' => 15, 'response' => false);

        //L'agent a-t-il une communication en cours
        $this->loadModel('UserCreditHistory');
        $com = $this->UserCreditHistory->find('first', array(
            'fields'            => array('UserCreditHistory.*'),
            'conditions'        => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_end' => null),
            'recursive'         => -1
        ));

        //Non, il y a aucune communication en cours
        if(empty($com))
            return array('response_code' => 21, 'response' => false);

        //La date de début en secondes
        $timestampStart = new DateTime($com['UserCreditHistory']['date_start']);
        $timestampStart = $timestampStart->getTimestamp();
        //La date de fin
        $timestampEnd = new DateTime($dateNow);
        $timestampEnd = $timestampEnd->getTimestamp();

        //Durée de la communication
        $comSecond = $timestampEnd - $timestampStart;

        //On met à jour les données de l'historique
        $updateCom = array(
            'seconds'   => $comSecond,
            'date_end'  => $dateNow
        );

        $updateCom = $this->UserCreditHistory->value($updateCom);

        //On met à jour l'enregistrement
        if($this->UserCreditHistory->updateAll($updateCom,array('user_credit_history' => $com['UserCreditHistory']['user_credit_history'])))
            return true;
        else
            return array('response_code' => 999, 'response' => false);
    }

	public function date_range(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //Pas de filtre avec la date
            if(empty($requestData['date'])){
                $this->Session->delete('Date');
            }else{
                $explodeDate = explode(' au ', $requestData['date']);

				if(strlen($explodeDate[0]) == 10 && strlen($explodeDate[1]) == 10){
					$this->Session->write('Date.start', $explodeDate[0]);
                	$this->Session->write('Date.end', $explodeDate[1]);
				}else{
					$this->Session->delete('Date');
				}
            }

            //Filtre media
            if(isset($requestData['media'])){
                if(empty($requestData['media']) || $requestData['media'] === 'all')
                    $this->Session->delete('Media');
                else
                    $this->Session->write('Media.value', $requestData['media']);
            }

            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'agents', 'action' => 'history'), false);
            else
                $this->redirect($source);
        }
    }


	public function notations(){
         $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] !== 'agent')
            throw new Exception("Erreur de sécurité !", 1);

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('Notes');
		$this->loadModel('UserCreditLastHistory');

        //Date d'aujourd'hui explosé
       /* $dateNow = CakeTime::format('now', '%d-%m-%Y');
        $dateNow = Tools::explodeDate($dateNow);
        $dateEnd = CakeTime::format(strtotime('+'.(Configure::read('Site.limitPlanning')-1).' days'), '%d-%m-%Y');
        $dateEnd = Tools::explodeDate($dateEnd);*/


		$conditions = array('Notes.id_agent' => $user['id']);

		if($this->request->is('post')){
            $requestData = $this->request->data;
			$client = $requestData['Agent']['nom'];
			if($client){
				$conditions = array('Notes.id_agent' => $user['id'],
								   'OR' => array(
									   		'Notes.client like'=>'%'.$client.'%',
									   		'User.firstname like'=>'%'.$client.'%',
								   			)
								   );
			}
		}

        $this->Paginator->settings = array(
            'fields' => array('Notes.*', 'User.firstname','User.id',
							 /* 'CASE
							  WHEN Notes.client like \'AUDIO%\'
							  THEN (select C.date_start from user_credit_last_histories C , call_infos CA where CA.callinfo_id = Notes.callinfo_id and C.sessionid=CA.sessionid and C.agent_id = Notes.id_agent order by C.date_start DESC LIMIT 1)
							  ELSE
							  (select C.date_start from user_credit_last_histories C where C.users_id=Notes.id_client and C.agent_id = Notes.id_agent order by C.date_start DESC LIMIT 1)
							  END as date_last '*/),
            'conditions' => $conditions,
			'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = Notes.id_client')
                )
            ),
            'limit' => 15,
			'order' => 'User.id DESC',
			//'group' => array('Credit.users_id'),
            'recursive' => -1
        );


	/*	$this->Paginator->settings = array(
            'fields' => array('Notes.*', 'User.*'),
            'conditions' => array('Notes.id_agent' => $user['id']),
			'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = Notes.id_client')
                )
            ),
            'limit' => 15,
			'order' => 'Notes.date_upd DESC',
			//'group' => array('Credit.users_id'),
            'recursive' => -1
        );*/


        $notes = $this->Paginator->paginate($this->Notes);


		/*foreach($notes as &$note){
			$com = $this->UserCreditLastHistory->find('first',array(
							'conditions' => array('users_id' => $note['User']['id'], 'agent_id' => $user['id']),
							'recursive' => -1,
							'order' => 'date_start DESC'

						));
			$note['Notes']['last_com'] = $com["UserCreditLastHistory"]['date_start'];
		}*/


        $this->set(compact('notes'));

    }

	public function admin_notes_client(){
		ini_set("memory_limit",-1);
		$parms = $this->params;
		$page = 1;
		if($parms["page"])$page = $parms["page"];

		$nbpage = 50;
		$limit = 1;
		if($page > 1) $limit = $page * $nbpage;

		$condition = array();

		if(isset($this->params->data['Agent']) && !$this->params->data['Agent']){
                $this->Session->delete('AgentName');
         }else{
			if(isset($this->params->data['Agent']) && $this->params->data['Agent'])
                $this->Session->write('AgentName', $this->params->data['Agent']['name']);
        }

		if($this->Session->read('AgentName')){
			$condition = array('OR' => array('User.pseudo LIKE' => '%'.$this->Session->read('AgentName').'%', 'User.firstname LIKE' => '%'.$this->Session->read('AgentName').'%','User.lastname LIKE' => '%'.$this->Session->read('AgentName').'%','Agen.pseudo LIKE' => '%'.$this->Session->read('AgentName').'%', 'Agen.firstname LIKE' => '%'.$this->Session->read('AgentName').'%','Agen.lastname LIKE' => '%'.$this->Session->read('AgentName').'%','Notes.client LIKE' => '%'.$this->Session->read('AgentName').'%'));
		}


		$this->loadModel('Notes');
        $this->Paginator->settings = array(
            'fields' => array('Notes.*','User.id','User.pseudo','User.lastname','User.firstname','User.email','Agen.*','User.role'),
            'conditions' => $condition,
            'recursive' => 1,
            'order' => 'Notes.date_crea DESC',
			'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = Notes.id_client')
                ),
				array('table' => 'users',
                      'alias' => 'Agen',
                      'type' => 'left',
                      'conditions' => array('Agen.id = Notes.id_agent')
                )
            ),
            'limit' => $nbpage,
			'page' => $page
        );

		$agents = $this->User->find('all', array(
            'conditions'    => array( 'User.deleted' => 0, 'User.active' => 1),
            'recursive'     => -1
        ));
        $notes = $this->Paginator->paginate($this->Notes);


        $this->set(compact('notes','agents'));



    }

	public function admin_note_ip($id){
        //On sauve la note
         if($this->request->is('post')){
			 $this->loadModel('UserIp');
			$check_ip = $this->UserIp->find('first',array(
					'conditions'    => array(
						'user_id' => $id,
					),
					'recursive' => -1
				));

			 if(count($check_ip)){
					$check_ip['UserIp']['note'] = $this->request->data['Agent']['note'];
					$this->UserIp->save($check_ip);
			 }
             $this->Session->setFlash(__('Note sauvegardé.'),'flash_success');
         }
        $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
    }

	public function reviews(){
        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] !== 'agent')
            throw new Exception("Erreur de sécurité !", 1);

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('Review');
        $this->Paginator->settings = array(
			'fields' => array('Review.review_id', 'Review.user_id', 'Review.agent_id', 'Review.lang_id', 'Review.content', 'Review.rate', 'Review.date_add', 'Review.utile',
                'User.firstname',
                'Agent.id', 'Agent.pseudo', 'Agent.agent_number', 'Agent.has_photo'
            ),
            'conditions' => array('Review.agent_id' => $user['id'],'Review.parent_id' => NULL,'Review.status' => 1),
			'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = Review.user_id')
                ),
				array('table' => 'users',
                      'alias' => 'Agent',
                      'type' => 'left',
                      'conditions' => array('Agent.id = Review.agent_id')
                )
            ),
            'order' => "Review.date_add DESC",
            'limit' => 15,
            'recursive' => -1
        );

        $reviews = $this->Paginator->paginate($this->Review);

				$review = array();

		foreach($reviews as $r){
			$response = $this->Review->find('first',array(
				'conditions' => array(
					'Review.parent_id' => $r['Review']['review_id'],
					'Review.status' => 1,
				),

				'recursive' => -1
			));
			if($response){
				$r['Review']['reponse'] = $response['Review'];
			}

			$review[] = $r;
		}
		$reviews = $review;

        $this->set(compact('reviews'));

    }

	public function answer_review(){

		 if($this->request->query){

			$requestData = $this->request->query;

			$this->loadModel('Review');
			$review = $this->Review->find('first',array(
					'fields' => array('Review.*', 'User.*'),
					'conditions' => array(
						'Review.review_id' => $requestData['id'],
					),
					'joins' => array(
                array('table' => 'users',
                      'alias' => 'User',
                      'type' => 'left',
                      'conditions' => array('User.id = Review.user_id')
                ),
				array('table' => 'users',
                      'alias' => 'Agent',
                      'type' => 'left',
                      'conditions' => array('Agent.id = Review.agent_id')
                )
            ),
					'recursive' => -1
				));
			$this->set(compact('review'));
		}
    }


	public function review_resp(){
		 if($this->request->is('post')){

            $requestData = $this->request->data;
            //Pour le retour
            $url = array('controller' => 'agents', 'action' => 'answer_review?id='.$requestData['Agent']['review_id']);
			$url_home = array('controller' => 'agents', 'action' => 'reviews');

            //Vérification des champs du formulaire
            $requestData['Agent'] = Tools::checkFormField($requestData['Agent'], array('review_id', 'content'), array('review_id','content'));
            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_warning');

                $this->redirect($url);
            }

			$this->loadModel('Review');
			$review = $this->Review->find('first',array(
					'conditions' => array(
						'Review.review_id' => $requestData['Agent']['review_id'],
					),
					'recursive' => -1
				));
			$Data = array();
			$Data['Review']['user_id'] = $review['Review']['user_id'];
			$Data['Review']['agent_id'] = $review['Review']['agent_id'];
			$Data['Review']['lang_id'] = $review['Review']['lang_id'];
			$Data['Review']['parent_id'] = $requestData['Agent']['review_id'];
			$Data['Review']['content'] = $requestData['Agent']['content'];
			$Data['Review']['rate'] = 0;
			$Data['Review']['date_add'] = date('Y-m-d H:i:s');
			$Data['Review']['status'] = -1;
            $this->Review->create();
            if($this->Review->save($Data)){
                $this->Session->setFlash(__('Merci. Votre réponse est enregistré et en attente de validation.'),'flash_success');
                $this->redirect($url_home);
            }
            else {
                $this->Session->setFlash(__('Erreur rencontrée lors de la sauvegarde de votre réponse.'),'flash_error');
                $this->redirect($url);
            }
		 }
	}
	public function validcgv(){
		if($this->request->is('ajax')){
			$requestData = $this->request->data;
			$this->loadModel('Cgv');

			$this->Cgv->create();
            $this->Cgv->save(array(
                    'user_id'   => $this->Auth->user('id'),
                    'date_valid'  => date('Y-m-d H:i:s'),
                    'IP'         => getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
                ));

			$this->jsonRender(array('error' => ''));
		}
	}

	public function consult_history(){

		if($this->request->is('ajax')){
            $requestData = $this->request->data;
			 $this->layout = '';
            if(!isset($requestData['param']) || !is_numeric($requestData['param'])){
                $this->set(array('title' => __('Consultation'), 'content' => __('L\'historique est introuvable.'), 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('return' => false, 'html' => $response->body()));
            }

            //On va chercher les dernieres consult

			$this->loadModel('UserCreditLastHistory');
			$this->loadModel('User');

        	$user = $this->Session->read('Auth.User');


			$lastComs = $this->UserCreditLastHistory->find('all',array(
						'conditions' => array('agent_id' => $user['id'], 'users_id' => $requestData['param']),
						'recursive' => -1,
						'order'     => array('date_start' => 'DESC'),
						'limit' => 15
					));

			$customer = $this->User->find('first',array(
						'conditions' => array('id' => $requestData['param']),
						'recursive' => -1,
					));




            //Si aucun messages
            if(empty($lastComs)){
                $this->set(array('title' => __('Consultation'), 'content' => __('L\'historique est introuvable.'), 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('return' => false, 'html' => $response->body()));
            }

            $this->set(array('lastComs' => $lastComs, 'isAjax' => true));
            $response = $this->render();
            $this->set(array('title' => __('Dernières consultations avec ').$customer['User']['firstname'], 'content' => $response->body(), 'button' => 'Fermer'));
            $response = $this->render('/Elements/modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
		}

	}
	public function consult_reviews(){

		if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

            //On va chercher les dernieres consult

			$this->loadModel('Review');
			$this->loadModel('User');

        	$user = $this->Session->read('Auth.User');


			$reviews = $this->Review->find('all',array(
							'conditions' => array('user_id' => $requestData['param'], 'agent_id' => $user['id'], 'status' => 1, 'parent_id' => NULL),
							'recursive' => -1,
							'limit' => 15
						));

			$customer = $this->User->find('first',array(
						'conditions' => array('id' => $requestData['param']),
						'recursive' => -1,
					));


            $this->layout = '';

            //Si aucun messages
            if(empty($reviews)){
                $this->set(array('title' => __('Avis client'), 'content' => __('Les avis sont introuvables.'), 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('return' => false, 'html' => $response->body()));
            }

            $this->set(array('reviews' => $reviews, 'isAjax' => true));
            $response = $this->render();
            $this->set(array('title' => __('Dernièrs avis de ').$customer['User']['firstname'], 'content' => $response->body(), 'button' => 'Fermer'));
            $response = $this->render('/Elements/modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
		}

	}

	public function mails_relance_show(){

		$user = $this->Session->read('Auth.User');
        	if (!isset($user['id']) && $user['role'] != $this->myRole)
            	$this->jsonRender(array('return' => false));


		if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

            //On va chercher les messages du chat
            $this->loadModel('Message');

			$firstConditions = array(
                'Message.deleted' => 0,
				'Message.archive' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $requestData['param'], 'Message.to_id' => $user['id']),
                    array('Message.to_id' => $requestData['param'], 'Message.from_id' => $user['id'], 'Message.etat !=' => 2)
                )
            );


		   $messages = $this->Message->find('all', array(
            'fields' => array('Message.id','Message.date_add','Message.from_id','Message.to_id','Message.content','Message.etat','LastMessage.id','LastMessage.date_add','LastMessage.from_id','LastMessage.to_id','LastMessage.content','LastMessage.etat', 'FirstMessage.id','FirstMessage.date_add','FirstMessage.from_id','FirstMessage.to_id','FirstMessage.content','FirstMessage.etat','From.firstname as pseudo','((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder'),
            'conditions' => $firstConditions,
            'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'LastMessage',
                    'type'  => 'left',
					 'conditions' => array('LastMessage.parent_id = Message.id'
										   )
                ),
				array(
                    'table' => 'messages',
                    'alias' => 'FirstMessage',
                    'type'  => 'left',
					 'conditions' => array('FirstMessage.id = Message.id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'From',
                    'type'  => 'left',
                    'conditions' => array(
						'From.id = Message.from_id'
					)
                )
            ),
            'order' => 'dateorder desc',
            'recursive' => -1,
            'limit' => Configure::read('Site.limitMessagePage')
		   ));


			$customer = $this->User->find('first',array(
						'conditions' => array('id' => $requestData['param']),
						'recursive' => -1,
					));

            $this->layout = '';

            //Si aucun messages
            if(empty($messages)){
                $this->set(array('title' => __('Message'), 'content' => __('L\'historique est vide.'), 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('return' => false, 'html' => $response->body()));
            }

			foreach($messages as &$msg){
				$msg['Message']['content'] = str_replace('<!---->','<br /><br />',$msg['Message']['content']);
				$msg['Message']['content'] = nl2br($msg['Message']['content']);
				if($msg['Message']['from_id'] == $this->Auth->user('id'))
					$msg['Message']['content'] = '<p style="color:#933c8f">'.$msg['Message']['content'].'</p>';

			}

			$this->set(array('messages' => $messages, 'isAjax' => true));
            $response = $this->render();
            $this->set(array('title' => __('Derniers message avec ').$customer['User']['firstname'], 'content' => $response->body(), 'button' => 'Fermer'));
            $response = $this->render('/Elements/modal');

			$coprs = str_replace('text-center', 'text-left',$response->body());
			$coprs = str_replace('veram','',$coprs);

            $this->jsonRender(array('return' => true, 'html' => $coprs));
        }
	}

	public function mails_relance_filtre(){

		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData) || ($requestData['pseudo_f'] && strlen($requestData['pseudo_f'])<3))
                $this->jsonRender(array('return' => false));

			$this->loadModel('UserCreditLastHistory');
			$this->loadModel('User');
			$this->loadModel('Message');
			$this->loadModel('Relance');
			$this->loadModel('Notes');
			$this->loadModel('Review');
			App::uses('FrontblockHelper', 'View/Helper');
        	$fbH = new FrontblockHelper(new View());

        	$user = $this->Session->read('Auth.User');
        	if (!isset($user['id']) && $user['role'] != $this->myRole)
            	$this->jsonRender(array('return' => false));


			$condition = array('UserCreditLastHistory.agent_id' => $user['id']);

			if($requestData['date_min_f']){
				$cut = explode(' GMT',$requestData['date_min_f']);
				$timestamp_d = strtotime($cut[0]);
				$cut = explode(' GMT',$requestData['date_max_f']);
				$timestamp_e = strtotime($cut[0]);
				$condition = array_merge($condition, array(
					'UserCreditLastHistory.date_start >=' => CakeTime::format($timestamp_d, '%Y-%m-%d 00:00:00'),
					'UserCreditLastHistory.date_start <=' => CakeTime::format($timestamp_e, '%Y-%m-%d 23:59:59')
				));

			}
			if($requestData['trie_date_relance']){
				$lastCom = $this->UserCreditLastHistory->find('all',array(
							'fields' => array('UserCreditLastHistory.users_id','UserCreditLastHistory.date_start'),
							'conditions' => $condition,
							'recursive' => -1,
							'joins' => array(
							array('table' => 'relances',
								  'alias' => 'Relance',
								  'type' => 'left',
								  'conditions' => array('Relance.user_id = UserCreditLastHistory.users_id','Relance.agent_id = UserCreditLastHistory.agent_id')
							),
						),
							'order'     => array('Relance.date_relance' => 'ASC')
						));
			}else{
				$lastCom = $this->UserCreditLastHistory->find('all',array(
							'fields' => array('UserCreditLastHistory.users_id','UserCreditLastHistory.date_start'),
							'conditions' => $condition,
							'recursive' => -1,
							'order'     => array('date_start' => 'DESC')
						));
			}
			$clients = array();

			foreach($lastCom as $com){

				$condition = array('User.id' => $com['UserCreditLastHistory']['users_id']);

				if($requestData['pseudo_f']){
					$condition = array_merge($condition, array(
						'User.firstname LIKE' => '%'.$requestData['pseudo_f'].'%',
					));

				}

				$client = $this->User->find('first',array(
							'fields' => 'User.firstname, Relance.date_relance, User.id',
							'conditions' => $condition,
							'recursive' => -1,
							'joins' => array(
								array('table' => 'relances',
									  'alias' => 'Relance',
									  'type' => 'left',
									  'conditions' => array('Relance.user_id = User.id', 'Relance.agent_id = '.$user['id'])
								),
							),
						));
				if(isset($client['User']) && !substr_count($client['User']['firstname'],'AUDIOTEL')){
					$lastMes = $this->Message->find('all',array(
								'conditions' => array('from_id' => $user['id'],'to_id' => $com['UserCreditLastHistory']['users_id'], 'date_add >' => date('Y-m-00 00:00:00'), 'content LIKE' => '%<!---->%', 'etat !=' => 3, 'archive' => 0),
								'recursive' => -1,
								'order'     => array('date_add' => 'DESC')
							));

					if(!is_numeric($requestData['deja_f']) || (is_numeric($requestData['deja_f']) && count($lastMes) == $requestData['deja_f'])){
						$last_relance = '';
						foreach($lastMes as $mess ){
							if($mess['Message']['private'] == 2)
								$last_relance .= 'en cours d\'envoi';
								else
							$last_relance .= CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$mess['Message']['date_add']),'%d/%m/%y %Hh%M');
							if($mess['Message']['date_add']) $last_relance .= '';
						}


						$messages = array();//$this->Message->getDiscussion($user['id'], false, true);
						$firstConditions = array(
							'Message.deleted' => 0,
							'Message.parent_id' => null,
							'Message.private' => 1,
							'OR' => array(
								array('Message.from_id' => $user['id'],'Message.to_id' => $com['UserCreditLastHistory']['users_id']),
								array('Message.from_id' => $com['UserCreditLastHistory']['users_id'],'Message.to_id' => $user['id'])
							)
						);

						$mess = $this->Message->find('all',array(
									'conditions' => $firstConditions,
									'recursive' => -1,

								));


						$note = $this->Notes->find('first',array(
									'conditions' => array('id_client' => $com['UserCreditLastHistory']['users_id'], 'id_agent' => $user['id']),
									'recursive' => -1,

								));

						$reviews = $this->Review->find('first',array(
									'conditions' => array('user_id' => $com['UserCreditLastHistory']['users_id'], 'agent_id' => $user['id'], 'status' => 1, 'parent_id' => NULL),
									'recursive' => -1,

								));


						if(!is_array($clients[$com['UserCreditLastHistory']['users_id']])){
							$customer = array();
							$customer['pseudo'] = $client['User']['firstname'];
							$customer['user_id'] = $client['User']['id'];
							$customer['agent_id'] = $user['id'];
							$customer['last_relance'] = $last_relance;
							$customer['message'] = count($messs);
							$customer['reviews'] = count($reviews);
							$customer['last_com'] = $com['UserCreditLastHistory']['date_start'];
							$customer['note_id'] = $note['Notes']['id'];
							$customer['date_relance'] = $client['Relance']['date_relance'];
							if(($requestData['trie_date_relance'] && $customer['date_relance']) || !$requestData['trie_date_relance'])
							$clients[$com['UserCreditLastHistory']['users_id']] = $customer;
						}
					}
				}

			}

			$html = '';
			foreach($clients as $client){
				$html .= '<tr>';
					$html .= '<td>';
					if($client['message'] < 1)
						$html .= '<input type="radio" rel="'.$client['user_id'].'" />';
					else
						$html .='&nbsp;';
					$html .= '</td>';
				  	$html .= '<td class=" resize-img" style="text-align:left">'.$client['pseudo'].'</td>';
				  	$html .= '<td class="" style="text-align:left">';
				    if($client['last_relance']){ $html .= $client['last_relance']; }else{ $html .= '<span>Aucun envoi</span>'; }
					//if($client['message']){
					$html .= $fbH->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'mails_relance_show'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );
					//}
				    $html .= '</td>';
				  	$html .= '<td class="" style="text-align:left">'.CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$client['last_com']),'%d/%m/%y %Hh%M');
					$html .= $fbH->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'consult_history'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );
					$html .= '</td>';
				  	$html .= '<td class="">';
					if($client['reviews']){
					 $html .= $fbH->Html->link('&nbsp;<i class="glyphicon glyphicon-zoom-in"></i> ',
                                                array('controller' => 'agents', 'action' => 'consult_reviews'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox', 'param' => $client['user_id'])
                                            );
					}
					$html .= '</td>';
					$html .= '<td class="">';
				    if($client['note_id']){ $html .= '<i class="glyphicon glyphicon-pencil lfloat phonenote_edit" rel="'.$client['note_id'].'"></i>';
					}
				    $html .= '</td>';
				  	$html .= '<td class="">';
					if($client['date_relance'])
				    $html .= '<span>'.CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$client['date_relance']),'%d/%m/%y');
					$html .= '</span> <i class="glyphicon glyphicon-pencil lfloat daterelance_edit" rel="'.$client['user_id'].'"></i>';
					$html .= '</td> ';
				$html .= '</tr> ';
			}

			$this->jsonRender(array('return' => true, 'html' => $html));
		}
	}

	public function closeMessageRelance(){

		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData))
                $this->jsonRender(array('return' => false));

			$this->loadModel('Message');

			$this->Message->id = $requestData['id_message'];
			$this->Message->saveField('deleted', 1);

			$this->jsonRender(array('return' => true));
		}
	}

	public function closeAllMessageRelance(){

		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			$user = $this->Session->read('Auth.User');
			$this->loadModel('Message');
			if(is_array($requestData['messages'])){
				foreach($requestData['messages'] as $id){
					$this->Message->id = $id;
					$this->Message->saveField('archive', 1);
				}

			}
			/*$refus = $this->Message->find('all',array(
							'fields' => array('Message.*','User.firstname'),
							'conditions' => array('from_id' => $user['id'], 'private' => 2, 'etat' => 3, 'archive' => 0),
							'recursive' => -1,
							'order'     => array('date_add' => 'ASC'),
							'joins' => array(
								array('table' => 'users',
									  'alias' => 'User',
									  'type' => 'left',
									  'conditions' => array(
										  'User.id = Message.to_id',
									  )
								)
							),
						));
			foreach($refus as $ref){
				$this->Message->id = $ref['Message']['id'];
				$this->Message->saveField('archive', 1);
			}*/
			$this->jsonRender(array('return' => true));
		}
	}

	public function save_relance_date(){

		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData))
                $this->jsonRender(array('return' => false));


			$tabdate = explode('/',$requestData['date_relance']);
			$date_relance = $tabdate[2].'-'.$tabdate[1].'-'.$tabdate[0].' 12:00:00';

			$this->loadModel('Relance');

			$relance = $this->Relance->find('first',array(
									'conditions' => array('user_id' => $requestData['user_id'], 'agent_id' => $this->Auth->user('id')),
									'recursive' => -1,
								));

			if($relance['Relance']['id']){
				$this->Relance->id = $relance['Relance']['id'];
				$this->Relance->saveField('date_relance', $date_relance);
			}else{
				$viewData = array();
				$viewData['Relance'] = array();
				$viewData['Relance']['user_id'] = $requestData['user_id'];
				$viewData['Relance']['agent_id'] = $this->Auth->user('id');
				$viewData['Relance']['date_relance'] = $date_relance;

				$this->Relance->create();
				$this->Relance->save($viewData);

			}
			$this->jsonRender(array('return' => true));
		}
	}

	public function cancel_relance_date(){

		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData))
                $this->jsonRender(array('return' => false));


			$this->loadModel('Relance');

			$relance = $this->Relance->find('first',array(
									'conditions' => array('user_id' => $requestData['user_id'], 'agent_id' => $this->Auth->user('id')),
									'recursive' => -1,
								));

			if($relance['Relance']['id']){
				$this->Relance->id = $relance['Relance']['id'];
				$this->Relance->saveField('date_relance', NULL);
			}
			$this->jsonRender(array('return' => true));
		}
	}

	public function comment_communication(){

	}

	public function comment_general(){

	}

	public function cgu(){

	}

	public function gain(){

	}

	public function checkvucall(){

		if($this->request->is('post')){
            //Les datas
            $requestData = $this->request->data;
			 $this->loadModel('UserPenality');
			if($requestData['Agent']['comm']){
				$this->UserPenality->id = $requestData['Agent']['comm'];
				$this->UserPenality->saveField('reason', $requestData['Agent']['reason']);
				if($this->UserPenality->saveField('is_view', 1)){
					$this->Session->setFlash(__('Votre raison a été enregistré.'),'flash_success');
				}else{
					$this->Session->setFlash(__('Erreur lors de l\'enregistrement.'),'flash_warning');
				}
				$this->redirect(array('controller' => 'agents', 'action' => 'historylostcall'));
			}
		}

		/*if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData)|| !isset($this->request->data['id_comm']))
                $this->jsonRender(array('return' => false));


			//$this->loadModel('Callinfo');
			//$this->Callinfo->useTable = 'call_infos';

			//$this->Callinfo->callinfo_id = $this->request->data['id_comm'];
			//$this->Callinfo->saveField('status', 1);

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			$mysqli_s->query("UPDATE call_infos set status = 1 where callinfo_id = '{$this->request->data['id_comm']}'");
			$mysqli_s->close();
			$this->jsonRender(array('return' => true));
		}*/
	}

	public function checkvuchat(){

		if($this->request->is('post')){
            //Les datas
            $requestData = $this->request->data;
			 $this->loadModel('UserPenality');
			if($requestData['Agent']['comm']){
				$this->UserPenality->id = $requestData['Agent']['comm'];
				$this->UserPenality->saveField('reason', $requestData['Agent']['reason']);
				if($this->UserPenality->saveField('is_view', 1)){
					$this->Session->setFlash(__('Votre raison a été enregistré.'),'flash_success');
				}else{
					$this->Session->setFlash(__('Erreur lors de l\'enregistrement.'),'flash_warning');
				}
				$this->redirect(array('controller' => 'agents', 'action' => 'historylostchat'));
			}
		}

		/*if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData)|| !isset($this->request->data['id_comm']))
                $this->jsonRender(array('return' => false));


			$this->loadModel('Chat');
			$this->Chat->id = $this->request->data['id_comm'];
			$this->Chat->saveField('status', 1);
			$this->jsonRender(array('return' => true));
		}*/
	}

	public function checkvuemail(){

		if($this->request->is('post')){
            //Les datas
            $requestData = $this->request->data;
			 $this->loadModel('UserPenality');
			if($requestData['Agent']['comm']){
				$this->UserPenality->id = $requestData['Agent']['comm'];
				$this->UserPenality->saveField('reason', $requestData['Agent']['reason']);
				if($this->UserPenality->saveField('is_view', 1)){
					$this->Session->setFlash(__('Votre raison a été enregistré.'),'flash_success');
				}else{
					$this->Session->setFlash(__('Erreur lors de l\'enregistrement.'),'flash_warning');
				}
				$this->redirect(array('controller' => 'agents', 'action' => 'historylostemail'));
			}
		}

		/*if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData)|| !isset($this->request->data['id_comm']))
                $this->jsonRender(array('return' => false));


			$this->loadModel('Chat');
			$this->Chat->id = $this->request->data['id_comm'];
			$this->Chat->saveField('status', 1);
			$this->jsonRender(array('return' => true));
		}*/
	}

	
	public function  clients_refund(){}
	
	
	public function admin_comlost_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo', 'User.agent_number'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){

			$this->loadModel('UserPenality');

        //Les conditions de base
            $conditions = array('UserPenality.is_factured'=>1,
					'UserPenality.callinfo_id >'=>0,
					'UserPenality.user_id' => $id
				);
            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('User.firstname, User.lastname, Callinfo.timestamp, Callinfo.callerid, Callinfo.sessionid, User.id'),
                'conditions' => $conditions,
				'joins' => array(

						array(
							'table' => 'call_infos',
							'alias' => 'Callinfo',
							'type'  => 'left',
							'conditions' => array(
								'Callinfo.callinfo_id = UserPenality.callinfo_id',
							),
						),
						array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.personal_code = Callinfo.customer',
								)
							),


					),
                'order' => 'UserPenality.date_add desc',
                'paramType' => 'querystring',
                'limit' => 25
            );

            $allComs = $this->Paginator->paginate($this->UserPenality);

            $this->set(compact('user','allComs'));
        }else{
            $this->Session->setFlash(__('Aucun expert trouvé'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'com', 'admin' => true),false);
        }
    }
	public function admin_comlostchat_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){
			$this->loadModel('UserPenality');
		 //Les conditions de base
        $conditions = array('UserPenality.is_factured'=>1,
					'UserPenality.tchat_id >'=>0,
					'UserPenality.user_id' => $id);

       //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }


       //On récupère l'historique entier
       $this->Paginator->settings = array(
                'fields' => array('User.firstname, User.lastname, Chat.date_start, Chat.id, User.id'),
                'conditions' => $conditions,
		   		'joins' => array(

						array(
							'table' => 'chats',
							'alias' => 'Chat',
							'type'  => 'left',
							'conditions' => array(
								'Chat.id = UserPenality.tchat_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = Chat.from_id',
							)
						),

					),
                'order' => 'UserPenality.date_add desc',
                'paramType' => 'querystring',
                'limit' => 25
            );
            $allComs = $this->Paginator->paginate($this->UserPenality);


            $this->set(compact('user','allComs'));
        }else{
            $this->Session->setFlash(__('Aucun expert trouvé'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'com', 'admin' => true),false);
        }
    }

	public function admin_comlostmessage_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){
			$this->loadModel('UserPenality');
		 //Les conditions de base
        $conditions = array('UserPenality.is_factured'=>1,
					'UserPenality.message_id >'=>0,
					'UserPenality.user_id' => $id);

       //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }


       //On récupère l'historique entier
       $this->Paginator->settings = array(
                'fields' => array('User.firstname, User.lastname, Message.date_add, Message.id, UserPenality.delay, User.id'),
                'conditions' => $conditions,
		   		'joins' => array(

						array(
							'table' => 'messages',
							'alias' => 'Message',
							'type'  => 'left',
							'conditions' => array(
								'Message.id = UserPenality.message_id',
							)
						),
						array(
							'table' => 'users',
							'alias' => 'User',
							'type'  => 'left',
							'conditions' => array(
								'User.id = Message.from_id',
							)
						),

					),
                'order' => 'UserPenality.date_add desc',
                'paramType' => 'querystring',
                'limit' => 25
            );
            $allComs = $this->Paginator->paginate($this->UserPenality);


            $this->set(compact('user','allComs'));
        }else{
            $this->Session->setFlash(__('Aucun expert trouvé'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'com', 'admin' => true),false);
        }
    }

	public function admin_alertlostcall(){
		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData)|| !isset($this->request->data['id_comm']))
                $this->jsonRender(array('return' => false));


			$this->loadModel('Callinfo');
			$this->Callinfo->useTable = 'call_infos';
			$call = $this->Callinfo->find('first', array(
				'conditions' => array('Callinfo.callinfo_id' => $this->request->data['id_comm']),
				'recursive' => -1
			));

			$this->loadModel('User');
			$agent = $this->User->find('first', array(
				'conditions' => array('User.agent_number' => $call['Callinfo']['agent']),
				'recursive' => -1
			));

			if($call['Callinfo']['customer']){
				$client_sql = $this->User->find('first', array(
				'conditions' => array('User.personal_code' => $call['Callinfo']['customer']),
				'recursive' => -1
				));

				if($client_sql['User']['firstname']){
					$client = $client_sql['User']['firstname'];
				}else{
					$client = '';
				}
			}else{
				$client = 'AUDIO'.(substr($call['Callinfo']['callerid'], -4)*15);
			}


			$this->sendCmsTemplateByMail(319, $this->Session->read('Config.id_lang'), $agent['User']['email'], array(
						'PSEUDO_NAME_DEST' => $agent['User']['pseudo'],
						'PARAM_PSEUDO' => $agent['User']['pseudo'],
						'PARAM_CLIENT' => $client,
						'DATE_HEURE_CONSULTATION_PERDUE' => date('d-m-Y H',$call['Callinfo']['timestamp']).'h'.date('i',$call['Callinfo']['timestamp']).'min'.date('s',$call['Callinfo']['timestamp']).'s'
					));

			//$this->Callinfo->id = $call['Callinfo']['callinfo_id'];
			//$this->Callinfo->saveField('date_send', date('Y-m-d H:i:s'));
			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			$mysqli_s->query("UPDATE call_infos set date_send = NOW() WHERE callinfo_id = '{$call['Callinfo']['callinfo_id']}'");
			$mysqli_s->close();
			$this->jsonRender(array('return' => true));
		}
	}

	public function admin_alertlostchat(){
		if($this->request->is('ajax')){

			$requestData = $this->request->data;

			if(!isset($requestData)|| !isset($this->request->data['id_comm']))
                $this->jsonRender(array('return' => false));


			$this->loadModel('Chat');
			$call = $this->Chat->find('first', array(
				'conditions' => array('Chat.id' => $this->request->data['id_comm']),
				'recursive' => -1
			));

			$agent = $this->User->find('first', array(
				'conditions' => array('User.id' => $call['Chat']['to_id']),
				'recursive' => -1
			));

			$client = $this->User->find('first', array(
				'conditions' => array('User.id' => $call['Chat']['from_id']),
				'recursive' => -1
			));

			$this->sendCmsTemplateByMail(318, $this->Session->read('Config.id_lang'), $agent['User']['email'], array(
						'PSEUDO_NAME_DEST' => $agent['User']['pseudo'],
						'PARAM_PSEUDO' => $agent['User']['pseudo'],
						'PARAM_CLIENT' => $client['User']['firstname'],
						'DATE_HEURE_CONSULTATION_PERDUE' => CakeTime::format($call['Chat']['date_start'], '%d-%m-%Y %Hh%Mmin%Ss')
					));
			//$this->Chat->id = $call['Chat']['id'];
			//$this->Chat->saveField('date_send', date('Y-m-d H:i:s'));

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			$mysqli_s->query("UPDATE chats set date_send = NOW() WHERE id = '{$call['Chat']['id']}'");
			$mysqli_s->close();
			$this->jsonRender(array('return' => true));
		}
	}

	public function AgentStats($agent_id)
	{
		$jdelai = 7; //JOURS
		$dashboard = array();
		$this->loadModel('AgentStat');
		$this->loadModel('Review');
		$this->loadModel('UserConnexion');
		$this->loadModel('User');
		$this->loadModel('Callinfo');
		$this->loadModel('Chat');
		$this->loadModel('UserCreditHistory');
		$this->loadModel('AgentView');

		$listing_utcdec = Configure::read('Site.utcDec');

		/*$utc_dec = 1;//Configure::read('Site.utc_dec');
		if($this->Session->read('DateStats.start') )
			$cut = explode('-',$this->Session->read('DateStats.start') );
		else
			$cut = explode('-', date('Y-m-01') );


		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
			$utc_dec = 2;*/


		if($this->Session->check('DateStats')){
			$delai = CakeTime::format($this->Session->read('DateStats.start'), '%Y-%m-%d 00:00:00');
			$delai_max = CakeTime::format($this->Session->read('DateStats.end'), '%Y-%m-%d 23:59:59');
			$dx1 = new DateTime($delai);
			$dx2 = new DateTime($delai_max);
			$dx1->modify('-'.$listing_utcdec[$dx1->format('md')].' hour');
			$dx2->modify('-'.$listing_utcdec[$dx2->format('md')].' hour');
			$delai = $dx1->format('Y-m-d H:i:s');
			$delai_max = $dx2->format('Y-m-d H:i:s');
			$jdelai=$dx1->diff($dx2)->days;
			if(!$jdelai)$jdelai = 1;

        }else{
			$delai = date('Y-m-01 00:00:00');
			$delai_max_live = date('Y-m-d 23:59:59');
			$dx = new DateTime($delai_max_live);
			$dx->modify('last day of this month');
			$delai_max = $dx->format('Y-m-d 23:59:59');

			$dx1 = new DateTime($delai);
			$dx2 = new DateTime($delai_max);
			$dx1->modify('-'.$listing_utcdec[$dx1->format('md')].' hour');
			$dx2->modify('-'.$listing_utcdec[$dx2->format('md')].' hour');
			$delai = $dx1->format('Y-m-d H:i:s');
			$delai_max = $dx2->format('Y-m-d H:i:s');
			$jdelai=$dx1->diff($dx2)->days;
			if(!$jdelai)$jdelai = 1;
		}
		
		$agent_stats = $this->AgentStat->find('first',array(
                'conditions' => array('user_id' => $agent_id,'date_min' => $delai, 'date_max' => $delai_max),
            ));

		if($agent_stats){// && 1 == 2
			$dashboard['Note'] = array();//note en fct des reviews
			$n_reviens = $agent_stats['AgentStat']['note'];
			if($n_reviens){
				$dashboard['Note']['min'] = $n_reviens;
				$dashboard['Note']['max'] = number_format(100 - $dashboard['Note']['min'],1);
			}else{
				$dashboard['Note']['min'] = 0;
				$dashboard['Note']['max'] = 100;
			}
			$dashboard['Note']['label'] = 'Ma note : '.$dashboard['Note']['min'].'%';
			$dashboard['Note']['min_label'] = '';//$dashboard['Note']['min'];//'notes positives';
			$dashboard['Note']['max_label'] = '';//notes négatives';

			$dashboard['PresencePourcent'] = array();//pourcentage de presence sur le site (available => chat + tel )

			$p_min = $agent_stats['AgentStat']['presence'];
			$p_max = number_format(100- $p_min,1) ;
			$dashboard['PresencePourcent']['min'] = $p_min;
			$dashboard['PresencePourcent']['max'] = $p_max;
			$dashboard['PresencePourcent']['label'] = 'Présence : '.$p_min.'% soit '.$agent_stats['AgentStat']['presence_time'];
			$dashboard['PresencePourcent']['min_label'] = '';//ma présence';
			$dashboard['PresencePourcent']['max_label'] = '';//mon absence';

			$dashboard['TxDecroche'] = array();//nbr appel decroché sur nbr appel recu

			$nb_call_min = $agent_stats['AgentStat']['decroche'];
			$nb_call_max = number_format(100 - $nb_call_min,1);
			$dashboard['TxDecroche']['min'] = $nb_call_min;
			$dashboard['TxDecroche']['max'] = $nb_call_max;
			$dashboard['TxDecroche']['label'] = 'Taux décrochés : '.$nb_call_min.'%';
			//$dashboard['TxDecroche']['min_label'] = ' '.$nb_call_ok.' consultations décrochées';
			//$dashboard['TxDecroche']['max_label'] = ' '.$nb_call_nok.' consultations perdues';


			$dashboard['TxTransfoPresent'] = array();//nbr communications par rapport nbr de visit de son profil quand il est co chat ou tel
			if($agent_stats['AgentStat']['transformation']){
				$cut = explode('_',$agent_stats['AgentStat']['transformation']);
				$nb_consult = $cut[0];
				$nb_visite = $cut[1];
			}else{
				$nb_consult = 0;
				$nb_visite = 0;
			}
			if($nb_visite){
				if($nb_visite >= $nb_consult){
					$nb_rate = $nb_visite - $nb_consult;
				}else{
					$nb_rate = 0;
					$nb_visite  = $nb_consult;
				}

				$px = number_format($nb_consult * 100 / $nb_visite,1);
				$px_max = number_format($nb_rate * 100 / $nb_visite,1);
				$dashboard['TxTransfoPresent']['min'] = number_format($px,1);
				$dashboard['TxTransfoPresent']['max'] = number_format($px_max,1);
				$dashboard['TxTransfoPresent']['min_label'] = 'Pourcentage de '.number_format($px,1);
				$dashboard['TxTransfoPresent']['max_label'] = 'Pourcentage de '.number_format($px_max,1);
				$dashboard['TxTransfoPresent']['label'] = 'Taux de transfo. : '.$px.'%';
			}else{
				$dashboard['TxTransfoPresent']['min'] = 0;
				$dashboard['TxTransfoPresent']['max'] = 0;
				$dashboard['TxTransfoPresent']['min_label'] = '';//$nb_consult.' communications clients';
				$dashboard['TxTransfoPresent']['max_label'] = '';//$nb_rate.' comm. potentielles ratées';
				$dashboard['TxTransfoPresent']['label'] = 'Taux de transfo.';
			}

			$dashboard['TMC'] = array();//tps moyen duree comm

			if($agent_stats['AgentStat']['tmc_global'] >= $agent_stats['AgentStat']['tmc']){
				//$max = $durees_total[0][0]['duree'] - $durees[0][0]['duree'];
				$pourcent_tmc = number_format(($agent_stats['AgentStat']['tmc'] * 100 )/ $agent_stats['AgentStat']['tmc_global'],1,'.','');
				$pourcent_tmc_total = number_format(100 - $pourcent_tmc,1,'.','');
			}else{
				$max = $agent_stats['AgentStat']['tmc'];
				$pourcent_tmc = 100;
				$pourcent_tmc_total = 0;
			}
			$dashboard['TMC']['min'] = $pourcent_tmc;
			$dashboard['TMC']['max'] = $pourcent_tmc_total;//number_format($max/60,1);
			$dashboard['TMC']['label'] = 'TMC : '.gmdate("i,s", $agent_stats['AgentStat']['tmc']).' min.';
			$dashboard['TMC']['min_label'] = 'Votre temps moyen en consultation '.gmdate("i,s", $agent_stats['AgentStat']['tmc']).' minutes';//ma durée moyenne';
			$dashboard['TMC']['max_label'] = '';//durée moyenne globale';

			$dashboard['Proportion'] = array();//proportion consultation Téléphone vs Tchat vs Email


			$pourcent_email = $agent_stats['AgentStat']['email'];
			$pourcent_chat = $agent_stats['AgentStat']['tchat'];
			$pourcent_phone = $agent_stats['AgentStat']['tel'];

			$dashboard['Proportion']['max'] = $pourcent_email;
			$dashboard['Proportion']['medium'] = $pourcent_chat;
			$dashboard['Proportion']['min'] = $pourcent_phone;
			$dashboard['Proportion']['label'] = 'Emails: '.$pourcent_email.'% Tchats: '.$pourcent_chat.'% Tel: '.$pourcent_phone.'%';
			//$dashboard['Proportion']['min_label'] = 'Tel. : '.$count_total_phone.' consultations';//durée moyenne globale';
			//$dashboard['Proportion']['max_label'] = 'Emails : '.$count_total_mail.' consultations';//ma durée moyenne';
			//$dashboard['Proportion']['medium_label'] = 'Tchats : '.$count_total_tchat.' consultations';//ma durée moyenne';

		}else{
			/*
			$dashboard['Note'] = array();//note en fct des reviews
			$reviews = $this->Review->find('all',array(
					'fields' => array('count(*) as nb_review','SUM(Review.pourcent) as total_review'),
					'conditions' => array('agent_id' => $agent_id, 'Review.status' => 1, 'Review.parent_id' => NULL),
				));
			$n_reviens = $reviews['0']['0']['nb_review'];
			$total_review = $reviews['0']['0']['total_review'];

			if($n_reviens){
				$dashboard['Note']['min'] = number_format($total_review / $n_reviens,1);
				$dashboard['Note']['max'] = number_format(100 - $dashboard['Note']['min'],1);
			}else{
				$dashboard['Note']['min'] = 0;
				$dashboard['Note']['max'] = 100;
			}

			$dashboard['Note']['label'] = 'Ma note : '.$dashboard['Note']['min'].'%';
			$dashboard['Note']['min_label'] = '';//$dashboard['Note']['min'];//'notes positives';
			$dashboard['Note']['max_label'] = '';//notes négatives';

			$dashboard['PresencePourcent'] = array();//pourcentage de presence sur le site (available => chat + tel )

			//recup tranche dispo
			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			$result_s = $mysqli->query("SELECT * from user_state_history WHERE user_id = '".$agent_id."' and date_add >= '".$delai."' and date_add <= '".$delai_max."' order by date_add");

					$tranches_co = array();
					$tranche_begin = '';
					$tranche_end = '';
					$in_live = true;
					while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
						if(!$tranche_begin && ($row_s['state'] != 'unavailable')){
							$tranche_begin = $row_s['date_add'];
							$in_live = true;
						}


						if($row_s['state'] == 'unavailable'){
							if(!$tranche_begin)$tranche_begin = $row_s['date_add'];
							$tranche_end = $row_s['date_add'];
							$in_live = false;

						}

						if(!$in_live && $tranche_begin && $tranche_end){
							$tt = new stdClass();
							$tt->begin = $tranche_begin;
							$tt->end = $tranche_end;
							array_push($tranches_co,$tt);
							$tranche_begin = '';
							$tranche_end = '';
						}
					}
					if($in_live && $tranche_begin && !$tranche_end){
							$tt = new stdClass();
							$tt->begin = $tranche_begin;
							$tt->end = $delai_max;//date('Y-m-d H:i:s');
							array_push($tranches_co,$tt);
					}
					$tranches = array();
					foreach($tranches_co as $tran){

						$result_s = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$agent_id."' and date_connexion >= '".$tran->begin."' and date_connexion <= '".$tran->end."' order by id");

						$tranche_begin = '';
						$tranche_end = '';
						$in_live = true;
						while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){

							if(!$tranche_begin && ($row_s['tchat'] || $row_s['phone'])){
								$tranche_begin = $row_s['date_connexion'];
								$in_live = true;
							}


							if($row_s['status'] == 'unavailable'){
								if(!$tranche_begin)$tranche_begin = $row_s['date_connexion'];
								if(strtotime($row_s['date_lastactivity']) < strtotime($tran->end))
									$tranche_end = $row_s['date_lastactivity'];
								else
									$tranche_end = $tran->end;
								$in_live = false;
							}

							if(!$row_s['tchat'] && !$row_s['phone'] && $tranche_begin && $in_live){
								//if(!$tranche_begin)$tranche_begin = $tranche_end;
								$tranche_end = $row_s['date_connexion'];
								$in_live = false;
							}

							if(!$in_live && $tranche_begin && $tranche_end){
								$tt = new stdClass();
								$tt->begin = $tranche_begin;
								$tt->end = $tranche_end;
								array_push($tranches,$tt);
								$tranche_begin = '';
								$tranche_end = '';
							}


						}
						
					}
					$connexion_max = $jdelai * 24 * 60 * 60; //mettre un delta heures a enlever
					$connexion_min = 0;


					foreach($tranches as $periode){
							$connexion_min += strtotime($periode->end) - strtotime($periode->begin);
					}

			 $d = floor($connexion_min/86400);
				$_d = ($d < 10 ? '0' : '').$d;

				$h = floor(($connexion_min-$d*86400)/3600);
				$_h = ($h < 10 ? '0' : '').$h;

				$m = floor(($connexion_min-($d*86400+$h*3600))/60);
				$_m = ($m < 10 ? '0' : '').$m;

				$s = $connexion_min-($d*86400+$h*3600+$m*60);
				$_s = ($s < 10 ? '0' : '').$s;

			$hd = $_d * 24 + $_h;

			$dd = $hd.'h '.$_m.'min';


			$p_min = number_format($connexion_min * 100 / $connexion_max,1);
			$p_max = number_format(100- $p_min,1) ;
			$dashboard['PresencePourcent']['min'] = $p_min;
			$dashboard['PresencePourcent']['max'] = $p_max;
			$dashboard['PresencePourcent']['label'] = 'Présence : '.$p_min.'% soit '.$dd;
			$dashboard['PresencePourcent']['min_label'] = '';//ma présence';
			$dashboard['PresencePourcent']['max_label'] = '';//mon absence';


			$dashboard['TxDecroche'] = array();//nbr appel decroché sur nbr appel recu


			$user = $this->User->find('first',array(
					'fields' => array('agent_number'),
					'conditions' => array('id' => $agent_id),
				));
			$agent_number = $user['User']['agent_number'];
			if($agent_number){
				$calls = $this->Callinfo->find('all',array(
						'conditions' => array('agent' => $agent_number, 'timestamp >=' => strtotime($delai),'timestamp <=' => strtotime($delai_max)),
					));
				$nb_call_ok = 0;
				$nb_call_nok = 0;
				foreach($calls as $call){
					if($call['Callinfo']['accepted'] == 'yes')$nb_call_ok ++;
					if($call['Callinfo']['accepted'] == 'no'){$nb_call_nok ++;}else{
						if($call['Callinfo']['accepted'] != 'yes' && $call['Callinfo']['reason'] == 'NOANSWER')$nb_call_nok ++;
						if($call['Callinfo']['accepted'] != 'yes' && $call['Callinfo']['reason'] == 'CANCEL')$nb_call_nok ++;
						if($call['Callinfo']['accepted'] != 'yes' && $call['Callinfo']['reason'] == 'BUSY')$nb_call_nok ++;
					}
				}
				$chats = $this->Chat->find('all',array(
						'conditions' => array('to_id' => $agent_id, 'date_start >=' => $delai,'date_start <=' => $delai_max),
					));
				foreach($chats as $chat){
					if($chat['Chat']['consult_date_start'])$nb_call_ok ++;
					if(!$chat['Chat']['consult_date_start'])$nb_call_nok ++;
				}

				$nb_call_min = number_format($nb_call_ok * 100 / ($nb_call_ok + $nb_call_nok),1);
				$nb_call_max = number_format(100 - $nb_call_min,1);
				$dashboard['TxDecroche']['min'] = $nb_call_min;
				$dashboard['TxDecroche']['max'] = $nb_call_max;
				$dashboard['TxDecroche']['label'] = 'Taux décrochés : '.$nb_call_min.'%';
				$dashboard['TxDecroche']['min_label'] = ' '.$nb_call_ok.' consultations décrochées';
				$dashboard['TxDecroche']['max_label'] = ' '.$nb_call_nok.' consultations perdues';

			}

			$dashboard['TxTransfoPresent'] = array();//nbr communications par rapport nbr de visit de son profil quand il est co chat ou tel
			$connexions = $this->UserConnexion->find('all',array(
					'conditions' => array('user_id' => $agent_id, 'OR' => array('tchat' => 1, 'phone' => 1), 'date_connexion >=' => $delai, 'date_connexion <=' => $delai_max),
				));
			$nb_consult = 0;
			$nb_visite = 0;
			foreach($connexions as $connexion){

				$visites = $this->AgentView->find('all',array(
					'conditions' => array('agent_id' => $agent_id, 'date_view >=' => $connexion['UserConnexion']['date_connexion'], 'date_view <' => $connexion['UserConnexion']['date_lastactivity']),
				));

				$nb_visite += count($visites);

				$consults = $this->UserCreditHistory->find('all',array(
					'conditions' => array('agent_id' => $agent_id, 'date_start >=' => $connexion['UserConnexion']['date_connexion'], 'date_start <' => $connexion['UserConnexion']['date_lastactivity']),
				));

				$nb_consult += count($consults);

			}


			if($nb_visite){
				if($nb_visite >= $nb_consult){
					$nb_rate = $nb_visite - $nb_consult;
				}else{
					$nb_rate = 0;
					$nb_visite  = $nb_consult;
				}

				$px = number_format($nb_consult * 100 / $nb_visite,1);
				$px_max = number_format($nb_rate * 100 / $nb_visite,1);
				$dashboard['TxTransfoPresent']['min'] = number_format($px,1);
				$dashboard['TxTransfoPresent']['max'] = number_format($px_max,1);
				$dashboard['TxTransfoPresent']['min_label'] = 'Pourcentage de '.number_format($px,1);
				$dashboard['TxTransfoPresent']['max_label'] = 'Pourcentage de '.number_format($px_max,1);
				$dashboard['TxTransfoPresent']['label'] = 'Taux de transfo. : '.$px.'%';
			}else{
				$dashboard['TxTransfoPresent']['min'] = 0;
				$dashboard['TxTransfoPresent']['max'] = 0;
				$dashboard['TxTransfoPresent']['min_label'] = '';//$nb_consult.' communications clients';
				$dashboard['TxTransfoPresent']['max_label'] = '';//$nb_rate.' comm. potentielles ratées';
				$dashboard['TxTransfoPresent']['label'] = 'Taux de transfo.';
			}

			$dashboard['TMC'] = array();//tps moyen duree comm


			$durees = $this->UserCreditHistory->find('all',array(
					'fields'     => 'AVG(UserCreditHistory.seconds) as duree',
					'conditions' => array('agent_id' => $agent_id, 'date_start >=' => $delai, 'date_start <=' => $delai_max, 'media !=' => 'email'),
				));
			$durees_total = $this->UserCreditHistory->find('all',array(
					'fields'     => 'AVG(UserCreditHistory.seconds) as duree',
					'conditions' => array('date_start >=' => $delai, 'date_start <=' => $delai_max, 'media !=' => 'email'),
				));
			if($durees_total[0][0]['duree'] >= $durees[0][0]['duree']){
				//$max = $durees_total[0][0]['duree'] - $durees[0][0]['duree'];
				$pourcent_tmc = number_format(($durees[0][0]['duree'] * 100 )/ $durees_total[0][0]['duree'],1,'.','');
				$pourcent_tmc_total = number_format(100 - $pourcent_tmc,1,'.','');
			}else{
				$max = $durees[0][0]['duree'];
				$pourcent_tmc = 100;
				$pourcent_tmc_total = 0;
			}



			$dashboard['TMC']['min'] = $pourcent_tmc;
			$dashboard['TMC']['max'] = $pourcent_tmc_total;//number_format($max/60,1);
			$dashboard['TMC']['label'] = 'TMC : '.gmdate("i,s", $durees[0][0]['duree']).' min.';
			$dashboard['TMC']['min_label'] = 'Votre temps moyen en consultation '.gmdate("i,s", $durees[0][0]['duree']).' minutes';//ma durée moyenne';
			$dashboard['TMC']['max_label'] = '';//durée moyenne globale';


			$dashboard['Proportion'] = array();//proportion consultation Téléphone vs Tchat vs Email


			$comms = $this->UserCreditHistory->find('all',array(
					'fields'     => 'UserCreditHistory.media',
					'conditions' => array('agent_id' => $agent_id, 'date_start >=' => $delai, 'date_start <=' => $delai_max),
				));

			$count_total = count($comms);
			$count_total_mail = 0;
			$count_total_tchat = 0;
			$count_total_phone = 0;

			foreach($comms as $comm){
				if($comm['UserCreditHistory']['media'] == 'email') $count_total_mail ++;
				if($comm['UserCreditHistory']['media'] == 'phone') $count_total_phone ++;
				if($comm['UserCreditHistory']['media'] == 'chat') $count_total_tchat ++;
			}

			$pourcent_email = number_format($count_total_mail * 100 / $count_total,1);
			$pourcent_chat = number_format($count_total_tchat * 100 / $count_total,1);
			$pourcent_phone = number_format($count_total_phone * 100 / $count_total,1);

			$dashboard['Proportion']['max'] = $pourcent_email;
			$dashboard['Proportion']['medium'] = $pourcent_chat;
			$dashboard['Proportion']['min'] = $pourcent_phone;
			$dashboard['Proportion']['label'] = 'Emails: '.$pourcent_email.'% Tchats: '.$pourcent_chat.'% Tel: '.$pourcent_phone.'%';
			$dashboard['Proportion']['min_label'] = 'Tel. : '.$count_total_phone.' consultations';//durée moyenne globale';
			$dashboard['Proportion']['max_label'] = 'Emails : '.$count_total_mail.' consultations';//ma durée moyenne';
			$dashboard['Proportion']['medium_label'] = 'Tchats : '.$count_total_tchat.' consultations';//ma durée moyenne';
			*/
		}
		return $dashboard;
	}

	public function admin_connexion_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.pseudo'),
            'conditions' => array('User.id' => $id, 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive' => -1
        ));

        //Si user
        if(!empty($user)){
			$this->loadModel('UserConnexion');
		 //Les conditions de base
        $conditions = array( 'UserConnexion.user_id' => $user['User']['id']);

       //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserConnexion.date_connexion >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserConnexion.date_connexion <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }


       //On récupère l'historique entier
       $this->Paginator->settings = array(
                'fields' => array('UserConnexion.*'),
                'conditions' => $conditions,
                'order' => 'UserConnexion.date_connexion desc',
                'paramType' => 'querystring',
                'limit' => 25
            );
            $allConnexions = $this->Paginator->paginate($this->UserConnexion);


            $this->set(compact('user','allConnexions'));
        }else{
            $this->Session->setFlash(__('Aucune connexion trouvé'),'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'com', 'admin' => true),false);
        }
    }

	public function Bonus()
	{

	}

	public function appointmentrdv(){


        if($this->request->is('post')){
            $requestData = $this->request->data;
            //On vérifie les champs du formulaire

			$requestData['Agent'] = Tools::checkFormField($requestData['Agent'],array('appointment_agent_id', 'appointment_date', 'appointment_heure'), array('appointment_agent_id', 'appointment_date', 'appointment_heure'));

            if($requestData['Agent'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

            //Les infos de l'agent
            $agent = $this->User->find('first', array(
                'fields' => array('User.id', 'User.agent_number', 'User.pseudo'),
                'conditions' => array('User.id' => $requestData['Agent']['appointment_agent_id'], 'User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1),
                'recursive' => -1
            ));
            //si pas d'agent
            if(empty($agent)){
                $this->Session->setFlash(__('L\'agent est introuvable.'), 'flash_error');
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

            //Si l'utilisateur est connecté et si c'est un client
            if($this->Auth->loggedIn() && $this->Auth->user('role') === 'client'){

				$date = explode('/',$requestData['Agent']['appointment_date']);
				$date = $date[2].'-'.$date[1].'-'.$date[0].' '.$requestData['Agent']['appointment_heure'];

				if($date < date('Y-m-d H:i:s')){
					$this->Session->setFlash(__('Merci de choisir une date à venir.'), 'flash_warning');
				}else{
					//En sauvegarde la demande de consultation

					$this->loadModel('CustomerAppointment');
					$dateExplode = array();
					$dateExplode['A']=CakeTime::format($date, '%Y');
					$dateExplode['M']=CakeTime::format($date, '%m');
					$dateExplode['J']=CakeTime::format($date, '%d');
					$dateExplode['JS']=CakeTime::format($date, '%u');
					$dateExplode['H']=CakeTime::format($date, '%H');
					$dateExplode['Min']=CakeTime::format($date, '%M');
					$appointment = $this->CustomerAppointment->hasAppointment($requestData['Agent']['appointment_agent_id'], $dateExplode);
					//Si l'agent a déjà un rdv pour cette date
					if(!$appointment){

						//Le pseudo du client
						$client = $this->User->field('firstname', array('id' => $this->Auth->user('id')));
						$country_client = $this->User->field('country_id', array('id' => $this->Auth->user('id')));

						//Le pseudo de l'agent
						$pseudo = $this->User->field('pseudo', array('id' => $requestData['Agent']['appointment_agent_id']));
						$email_agent = $this->User->field('email', array('id' => $requestData['Agent']['appointment_agent_id']));
						$country_agent = $this->User->field('country_id', array('id' => $requestData['Agent']['appointment_agent_id']));
						$domain_agent = $this->User->field('domain_id', array('id' => $requestData['Agent']['appointment_agent_id']));

						$this->loadModel('UserCountry');

						 $cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_client),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));

						if($cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo_client = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
					}else{
						$this->loadModel('Domain');
						$domainInfo = $this->Domain->find('first', array(
							'fields' => array('country_id'),
							'conditions' => array('Domain.id' => $domain_agent),
							'recursive' => -1
						));


					$this->loadModel('Country');
						$countryInfo_client = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
							'recursive' => -1
						));
					}

					 $cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.country_id'),
						'conditions' => array('UserCountry.id' => $country_agent),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));

						if($cc_infos['CountryLang']['country_id']){
						$this->loadModel('Country');
						$countryInfo = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $cc_infos['CountryLang']['country_id']),
							'recursive' => -1
						));
					}else{
						$this->loadModel('Domain');
						$domainInfo = $this->Domain->find('first', array(
							'fields' => array('country_id'),
							'conditions' => array('Domain.id' => $domain_agent),
							'recursive' => -1
						));


					$this->loadModel('Country');
						$countryInfo = $this->Country->find('first', array(
							'fields' => array('timezone', 'devise', 'devise_iso'),
							'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
							'recursive' => -1
						));
					}


						$this->CustomerAppointment->create();
						$this->CustomerAppointment->save(array(
							'user_id'   => $this->Auth->user('id'),
							'agent_id'  => $requestData['Agent']['appointment_agent_id'],
							'user_utc'   => $countryInfo_client['Country']['timezone'],
							'agent_utc'   => $countryInfo['Country']['timezone'],
							'A'         => CakeTime::format($date, '%Y'),
							'M'         => CakeTime::format($date, '%m'),
							'J'         => CakeTime::format($date, '%d'),
							'JS'        => CakeTime::format($date, '%u'),
							'H'         => CakeTime::format($date, '%H'),
							'Min'       => CakeTime::format($date, '%M'),
						));


						//envoi email
						$this->loadModel('Lang');
						$this->User->id = $requestData['Agent']['appointment_agent_id'];
						$user_lang_id = $this->User->field('lang_id');
						$this->Lang->id = $user_lang_id;
						$locale = $this->Lang->field('lc_time');
						if (empty($locale))$locale = 'fr_FR.utf8';
						setlocale(LC_ALL, $locale);

						$dateAppoint = CakeTime::format($date, '%Y').'-'.CakeTime::format($date, '%m').'-'.
							   CakeTime::format($date, '%d').' '.
								str_pad(CakeTime::format($date, '%H'), 2, '0', STR_PAD_LEFT).':'.
								str_pad(CakeTime::format($date, '%i'), 2, '0', STR_PAD_LEFT).':00';

						if($countryInfo['Country']['timezone'] != $countryInfo_client['Country']['timezone']){

							/*if($countryInfo_client['Country']['timezone'] != 'Europe/Paris' && $countryInfo['Country']['timezone'] == 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 ) * -1;
						}
						if($countryInfo_client['Country']['timezone'] == 'Europe/Paris' && $countryInfo['Country']['timezone'] != 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
						}

						if($countryInfo_client['Country']['timezone'] != 'Europe/Paris' && $countryInfo['Country']['timezone'] != 'Europe/Paris' ){
										$userTimezone = new DateTimeZone($countryInfo['Country']['timezone']);
										$gmtTimezone = new DateTimeZone($countryInfo_client['Country']['timezone']);
										$myDateTime = new DateTime(date('Y-m-d H:i:s'), $gmtTimezone);
										$offset = ($userTimezone->getOffset($myDateTime) / 3600 );
						}*/

							date_default_timezone_set($countryInfo_client['Country']['timezone']);
									$d_client = date('YmdH');
									date_default_timezone_set($countryInfo['Country']['timezone']);
									$d_agent = date('YmdH');
									date_default_timezone_set('UTC');
									$offset = intval($d_agent) - intval($d_client);
									//if($countryInfo['Country']['timezone'] == 'America/Chicago') $offset = $offset + 1;
									//if($countryInfo_client['Country']['timezone'] == 'America/Chicago') $offset = $offset - 1;

							$utc_dec = $offset;//Configure::read('Site.utc_dec');

							//$utc_dec = Configure::read('Site.utc_dec');
							$dx = new DateTime($date);
							$dx->modify($utc_dec.' hour');
							$date = $dx->format('Y-m-d H:i:s');

							$rdv = CakeTime::format($date,'%d %B &agrave; %Hh%M');
						}else{
							$dx = new DateTime($date);
							$date = $dx->format('Y-m-d H:i:s');
							$rdv = CakeTime::format($date,'%d %B &agrave; %Hh%M');
						}
						if($this->sendCmsTemplateByMail(152, $user_lang_id, $email_agent, array(
								'PARAM_PSEUDO'      =>  $pseudo,
								'PARAM_CLIENT'      =>  $client,
								'PARAM_RENDEZVOUS'  =>  $rdv
							)))
							$this->Session->setFlash(__('Votre demande de rendez-vous a bien été envoyée à votre expert.'), 'flash_success');
						else
							$this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre rendez-vous.'), 'flash_warning');
					}else{
						$this->Session->setFlash(__('Cette date est déjà réservé.'), 'flash_warning');
					}
				}
            }else
                $this->Session->setFlash(__('Veuillez vous connecter sur votre compte client.'), 'flash_warning');

            $this->redirect(array(
                'language'      => $this->Session->read('Config.language'),
                'controller'    => 'agents',
                'action'        => 'display',
                'link_rewrite'  => strtolower($agent['User']['pseudo']),
                'agent_number'  => $agent['User']['agent_number'],
            ));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

	public function validconditionutilisation(){
		if($this->request->is('ajax')){
			$requestData = $this->request->data;
			$this->loadModel('Cu');

			$this->Cu->create();
            $this->Cu->save(array(
                    'user_id'   => $this->Auth->user('id'),
                    'date_valid'  => date('Y-m-d H:i:s'),
                    'IP'         => getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
                ));

			$this->jsonRender(array('error' => ''));
		}
	}

	public function profilsendremove()
    {

        $conditions = array(
									'User.id' => $this->Auth->user('id')
						);
		$client = $this->User->find('first',array('conditions' => $conditions));

		$hash =  $this->crypter($client['User']['email']);
		$url = Router::url(array('controller' => 'agents', 'action' => 'profilremove-'.$hash),true);

		$is_send = $this->sendCmsTemplatePublic(361, (int)$client['User']['lang_id'], $client['User']['email'], array(
									'CLIENT' =>$client['User']['firstname'],
									'URL_REMOVE' =>$url,
								));

        $this->Session->setFlash(__('Un email de confirmation vous a été envoyé.'), 'flash_success');

        $this->redirect(array(
            'controller' => 'agents',
            'action' => 'index',
        ));
    }

	protected function crypter($maChaineACrypter){
		$maCleDeCryptage = md5($this->hash_key);
		$letter = -1;
		$newstr = '';
		$strlen = strlen($maChaineACrypter);
		for($i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineACrypter{$i}) + ord($maCleDeCryptage{$letter});
			if ( $neword > 255 ){
				$neword -= 256;
			}
			$newstr .= chr($neword);
		}
		$k = $this->base64url_encode($newstr);
		return $k;
	}

	public function decrypter($maChaineCrypter){
		$maCleDeCryptage = md5($this->hash_key);
		$letter = -1;
		$newstr = '';
		$maChaineCrypter = $this->base64url_decode($maChaineCrypter);
		$strlen = strlen($maChaineCrypter);
		for ( $i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineCrypter{$i}) - ord($maCleDeCryptage{$letter});
			if ( $neword < 1 ){
				$neword += 256;
			}
			$newstr .= chr($neword);
		}
		return $newstr;
	}

	public function base64url_encode($data) {
	  return rtrim(strtr(base64_encode($data), '+_', '-|'), '=');
	}

	public function base64url_decode($data) {
	  return base64_decode(str_pad(strtr(urldecode($data), '-|', '+_'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}

	public function profilremove()
    {

		$params = $this->request->params;
		$hash = $this->decrypter($params['hash']);
		$test = utf8_decode($hash);
		if(!$test || substr_count($test,'?'))
			$this->redirect(array('controller' => 'home', 'action' => 'index'));

		if($hash){
			$this->loadModel('User');
			$user = $this->User->find('first', array(
				'conditions'    => array('User.email' => $hash),
				'recursive'     => -1
			));
			$this->User->id = $user['User']['id'];
			$this->User->saveField('deleted', '1');
			$this->User->saveField('active', '0');
			$this->User->saveField('email', 'delete_'.$hash);
			$this->User->saveField('date_upd', date('Y-m-d H:i:s'));

			$message = __('Votre compte est desormais supprimé.');
			$template = 'flash_success';

			$this->Session->setFlash($message, $template);
			$this->Cookie->delete('user_remember');

			//$this->destroySessionAndCookie();
			$this->Auth->logout();
		}
        $this->redirect(array(
            'controller' => 'home',
            'action' => 'index',
        ));
    }

	public function admin_agent_deleted(){

		$this->loadModel('User');

		$this->Paginator->settings = array(
                'order' => array('User.date_upd' => 'desc'),
				'conditions' => array('User.email like ' => '%delete_%','role'=>'agent'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $accounts = $this->Paginator->paginate($this->User);

            $this->set(compact('accounts'));
	}

	 public function admin_prime(){

	 	$dbb_r = new DATABASE_CONFIG();
		$dbb_s = $dbb_r->default;

		 //Avons-nous un filtre sur la date ??
        if($this->Session->check('Date')){
			$period_month_end = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$period_month_start = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$period_month = CakeTime::format($this->Session->read('Date.start'), '%Y-%m');
			$period_month2 = CakeTime::format($this->Session->read('Date.end'), '%Y-%m');
        }else{
			$period_month = date('Y-m');
			$dx = new DateTime($period_month);
			$period_month = $dx->format('Y-m');
			$period_month2 = $dx->format('Y-m');
			$dx->modify('last day of this month');
			$period_month_end = $dx->format('Y-m-d 23:59:59');
			$period_month_start = $dx->format('Y-m-01 00:00:01');
		}

		$dd = explode('-',$period_month);
		$dd2 = explode('-',$period_month2);
		$annee_min = $dd[0];
		$annee_max = $dd2[0];
		$mois_min = $dd[1];
		$mois_max = $dd2[1];

		 $lastPrime = array();
		 $mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
		$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' order by id");
		while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
			$line = array();

			$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id ASC");

			while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
				$bonus = '';
				$bonus_montant = 0;
				$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
				$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
				$bonus = $rowbonus['name']. ' '.$rowbonusagent['mois'].'/'.$rowbonusagent['annee'];
				$bonus_montant += $rowbonus['amount'];

				if($bonus_montant){

					$line['date'] = CakeTime::format($rowbonusagent['date_add'], '%Y-%m');
					$line['id'] = $rowagent['id'];
					$line['prenom'] = $rowagent['firstname'];
					$line['nom'] = $rowagent['lastname'];
					$line['pseudo'] = $rowagent['pseudo'];
					$line['prime'] = $bonus;
					$line['montant'] = $bonus_montant;
					array_push($lastPrime,$line);
				}

			}




			$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4 order by id asc");
			while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
				$bonus = '';
			$bonus_montant = 0;
				$line = array();
				$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$period_month_start}' and date_start <= '{$period_month_end}'");
				$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
				$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];

				$bonus = 'Prime mensuelle parrainage ';
				if($bonus_montant){

					$line['date'] = CakeTime::format($rowsponsoragent['date_add'], '%Y-%m');
					$line['id'] = $rowagent['id'];
					$line['prenom'] = $rowagent['firstname'];
					$line['nom'] = $rowagent['lastname'];
					$line['pseudo'] = $rowagent['pseudo'];
					$line['prime'] = $bonus;
					$line['montant'] = $bonus_montant;
					array_push($lastPrime,$line);

				}
			}

		}
		$mysqli->close();

        $this->set(compact('lastPrime'));
    }

    public function admin_export_prime(){
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/prime.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		//Si date
        if($this->Session->check('Date'))
            $label = 'prime_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_prime';

        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

		$dbb_r = new DATABASE_CONFIG();
		$dbb_s = $dbb_r->default;

		 //Avons-nous un filtre sur la date ??
        if($this->Session->check('Date')){
			$period_month_end = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$period_month_start = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$period_month = CakeTime::format($this->Session->read('Date.start'), '%Y-%m');
			$period_month2 = CakeTime::format($this->Session->read('Date.end'), '%Y-%m');
        }else{
			$period_month = date('Y-m');
			$dx = new DateTime($period_month);
			$period_month = $dx->format('Y-m');
			$period_month2 = $dx->format('Y-m');
			$dx->modify('last day of this month');
			$period_month_end = $dx->format('Y-m-d 23:59:59');
			$period_month_start = $dx->format('Y-m-01 00:00:01');
		}

		$dd = explode('-',$period_month);
		$dd2 = explode('-',$period_month2);

		$annee_min = $dd[0];
		$annee_max = $dd2[0];
		$mois_min = $dd[1];
		$mois_max = $dd2[1];

		$line = array();
		$line['date'] = '';
				$line['id'] = '';
				$line['prenom'] = '';
				$line['nom'] = '';
				$line['pseudo'] = '';
				$line['prime']= '';
				$line['montant'] = '';

		fputcsv($fp, array_keys($line), ';', '"');
		 $total = 0;
		 $lastPrime = array();
		 $mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
		$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' order by id");
		while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
			$line = array();
			$bonus = '';
			$bonus_montant = 0;
			$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id asc");

			while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
				$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
				$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
				$bonus = $rowbonus['name']. ' '.$rowbonusagent['mois'].'/'.$rowbonusagent['annee'];
				$bonus_montant += $rowbonus['amount'];
				if($bonus_montant){

					$line['date'] = CakeTime::format($rowbonusagent['date_add'], '%Y-%m');
					$line['id'] = $rowagent['id'];
					$line['prenom'] = $rowagent['firstname'];
					$line['nom'] = $rowagent['lastname'];
					$line['pseudo'] = $rowagent['pseudo'];
					$line['prime'] = $bonus;
					$line['montant'] = $bonus_montant;
					fputcsv($fp, array_values($line), ';', '"');
					$total += $bonus_montant;
				}
			}



			$bonus = '';
			$bonus_montant = 0;
			$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4 order by id asc");
			while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
				$line = array();
				$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$period_month_start}' and date_start <= '{$period_month_end}'");
				$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
				$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];

				$bonus = 'Prime mensuelle parrainage ';

				if($bonus_montant){

					$line['date'] = CakeTime::format($rowsponsoragent['date_add'], '%Y-%m');
					$line['id'] = $rowagent['id'];
					$line['prenom'] = $rowagent['firstname'];
					$line['nom'] = $rowagent['lastname'];
					$line['pseudo'] = $rowagent['pseudo'];
					$line['prime'] = $bonus;
					$line['montant'] = $bonus_montant;
					fputcsv($fp, array_values($line), ';', '"');
					$total += $bonus_montant;
				}
			}


		}
		$mysqli->close();

		$line['date'] = $period_month;
				$line['id'] = '';
				$line['prenom'] = '';
				$line['nom'] = '';
				$line['pseudo'] = '';
				$line['prime'] = 'TOTAL';
				$line['montant'] = $total;
				fputcsv($fp, array_values($line), ';', '"');

        fclose($fp);

        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
    }
	
	public function admin_order_old($invoice_id = NULL){
		 ini_set("memory_limit",-1);
		set_time_limit ( 0 );
		$user_co = $this->Session->read('Auth.User');
		$admin_id = $user_co['id'];

		$this->loadModel('InvoiceAgent');
		$this->loadModel('InvoiceVoucherAgent');

		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

	 	$dbb_r = new DATABASE_CONFIG();
		$dbb_s = $dbb_r->default;

		$listing_utcdec = Configure::read('Site.utcDec');

		//$utc_dec = Configure::read('Site.utc_dec');

		/*$utc_dec = 1;//Configure::read('Site.utc_dec');
		if($this->Session->read('DateStats.start') )
			$cut = explode('-',$this->Session->read('DateStats.start') );
		else
			$cut = explode('-', date('Y-m-01') );


		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
			$utc_dec = 2;*/

		 //Avons-nous un filtre sur la date ??
        if($this->Session->check('Date')){
			$period_month_end = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$dx = new DateTime($period_month_end);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_end = $dx->format('Y-m-d H:i:s');
			$period_month2 = $dx->format('Y-m');
			$period_month_start = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$dx = new DateTime($period_month_start);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_start = $dx->format('Y-m-d H:i:s');
			$period_month = $dx->format('Y-m');

        }else{
			$period_month = date('Y-m');
			$period_month2 = date('Y-m');

			$dx = new DateTime(date('Y-m-01 00:00:00'));
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_start = $dx->format('Y-m-d H:i:s');

			$dx = new DateTime(date('Y-m-d 00:00:00'));
			$dx->modify('last day of this month');
			$period_month_end = $dx->format('Y-m-d 23:59:59');
			$dx = new DateTime($period_month_end);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_end = $dx->format('Y-m-d H:i:s');
		}

		$_SESSION['fact_min'] = $period_month_start;
		$_SESSION['fact_max'] = $period_month_end;

		$dd = explode('-',$period_month);
		$dd2 = explode('-',$period_month2);
		$annee_min = $dd[0];
		$annee_max = $dd2[0];
		$mois_min = $dd[1];
		$mois_max = $dd2[1];

		$name = '';
		$min = '';
		$max = '';
		$cond_mode = '';
		if(isset($this->request->data['Agent'])){

			if($this->request->data['Agent']['agent']){
				$name = $this->request->data['Agent']['agent'];
			}

			if($this->request->data['Agent']['min']){
				$min = str_replace(',','.',$this->request->data['Agent']['min']);
			}

			if($this->request->data['Agent']['max']){
				$max = str_replace(',','.',$this->request->data['Agent']['max']);
			}

			if($this->request->data['Agent']['mode']){
				$cond_mode = $this->request->data['Agent']['mode'];
			}

			if(isset($this->request->data['Agent']['status'])){
				$cond_status = $this->request->data['Agent']['status'];
			}

		}
		if($invoice_id){
			$the_invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.id' => $invoice_id),
					'recursive' => -1
				));
			if($the_invoice_agent){
				$the_agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $the_invoice_agent['InvoiceAgent']['user_id']),
					'recursive' => -1
				));
				$name = $the_agent['User']['pseudo'];
			}
		}

		$condition = '';

		if($name){
			$condition = "and (pseudo LIKE '%".$name."%' or lastname LIKE '%".$name."%')";
		}

		//check date
		$list_date = array();
		if($mois_min != $mois_max || $annee_min != $annee_max){
			$start    = (new DateTime($period_month_start))->modify('first day of this month');
			$end      = (new DateTime($period_month_end))->modify('first day of this month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);

			foreach ($period as $dt) {
				$dmin = $dt->format("Y-m-d");
				$dmax = $dt->format("Y-m-d");
				$minn = (new DateTime($dmin))->modify('first day of this month');
				$maxx = (new DateTime($dmax))->modify('last day of this month');
				$obj = new stdClass();
				$obj->date_min = $minn->format("Y-m-d 00:00:00");
				$obj->date_max = $maxx->format("Y-m-d 23:59:59");
				$minn = new DateTime($obj->date_min);
				$maxx = new DateTime($obj->date_max);
				//$minn->modify('- '.$utc_dec.' hour');
				//$maxx->modify('- '.$utc_dec.' hour');
				$obj->date_min = $minn->format("Y-m-d H:i:s");
				$obj->date_max = $maxx->format("Y-m-d H:i:s");
				$obj->mois_min = $minn->format("m");
				$obj->mois_max = $maxx->format("m");
				$obj->annee_min = $minn->format("Y");
				$obj->annee_max = $maxx->format("Y");
				array_push($list_date,$obj);
			}
		}else{
			$obj = new stdClass();
			$obj->date_min = $period_month_start;
			$obj->date_max = $period_month_end;
			$obj->mois_min = $mois_min;
			$obj->mois_max = $mois_max;
			$obj->annee_min = $annee_min;
			$obj->annee_max = $annee_max;
			array_push($list_date,$obj);
		}

		$lastOrder = array();
		$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
		$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' ".$condition." order by lastname");
		$cond_mode_bis = '';
		if($cond_mode == 'Stripe' || $cond_status == 'TVA INVALIDE'){
			$cond_mode = 'Virement';
			$cond_mode_bis = 'Stripe';
		}

		while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
			$line = array();
			foreach($list_date as $date){

				$dx = new DateTime($date->date_min);
				$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
				$sql_date_min = $dx->format('Y-m-d H:i:s');

				//if($sql_date_min == '2019-02-28 22:00:00')$sql_date_min = '2019-02-28 23:00:00';
				//if($sql_date_min == '2018-12-31 22:00:00')$sql_date_min = '2019-01-01 00:00:00';
				//if($sql_date_min == '2019-01-31 22:00:00')$sql_date_min = '2019-02-01 00:00:00';
				$dx = new DateTime($date->date_max);
				$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
				$sql_date_max = $dx->format('Y-m-d H:i:s');
				//if($sql_date_max == '2019-01-31 21:59:59')$sql_date_max = '2019-01-31 23:59:59';
				//if($sql_date_max == '2019-02-28 21:59:59')$sql_date_max = '2019-02-28 22:59:59';
				$ca = 0;
				$total = 0;
				$total_gain = 0;
				$total_prime = 0;
				$total_sponsor = 0;
				$total_penality = 0;
				$is_sold = 0;
				$paid_date = '';
				$paid_total = 0;
				$paid = 0;
				$is_send = 0;
				$date_send = 0;
				$paid_total_valid = 0;
				$vat_amount = 0;
				$is_avoir = 0;
				$tva_applique = 0;
				$date_min_fact = $sql_date_min;
				$invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.user_id' => $rowagent['id'],'InvoiceAgent.date_max' => $sql_date_max),
					'recursive' => -1
				));//'InvoiceAgent.date_min' => $sql_date_min

				if($invoice_agent){

					$invoice_voucher = $this->InvoiceVoucherAgent->find('first',array(
						'conditions' => array('InvoiceVoucherAgent.invoice_id' => $invoice_agent['InvoiceAgent']['id']),
						'recursive' => -1
					));
					if($invoice_voucher)$is_avoir = 1;

					$ca = $invoice_agent['InvoiceAgent']['ca'];
					$total_sponsor = $invoice_agent['InvoiceAgent']['sponsor'];
					$total_prime = $invoice_agent['InvoiceAgent']['bonus'] ;
					$total_gain = $invoice_agent['InvoiceAgent']['paid'] ;
					$total_penality = $invoice_agent['InvoiceAgent']['penality'] + ($invoice_agent['InvoiceAgent']['other']*-1);
					$total = $total_gain + $total_prime - $total_penality;
					$paid_date = $invoice_agent['InvoiceAgent']['paid_date'];
					$paid_total = $invoice_agent['InvoiceAgent']['paid_total'];
					$paid = $invoice_agent['InvoiceAgent']['paid'];
					$paid_total_valid = $invoice_agent['InvoiceAgent']['paid_total_valid'];
					$is_send = $invoice_agent['InvoiceAgent']['is_send'];
					$date_send = $invoice_agent['InvoiceAgent']['date_send'];
					$vat_amount = $invoice_agent['InvoiceAgent']['vat'];
					if($invoice_agent['InvoiceAgent']['status'] > 0 && $invoice_agent['InvoiceAgent']['status'] < 2)$is_sold = 1;
					if($invoice_agent['InvoiceAgent']['status'] == 10)$is_sold = 1;
					if($invoice_agent['InvoiceAgent']['vat_tx'] > 0)$tva_applique = 1;
					$date_min_fact = $invoice_agent['InvoiceAgent']['date_min'];
					$total_consolide = 0;
					if($date_min_fact != $sql_date_min){
						$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start, C.is_sold , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$rowagent['id']}' and C.date_start >= '{$sql_date_min}' and C.date_start <= '{$sql_date_max}' and P.id_user_credit_history = C.user_credit_history  and C.is_factured = 1");
						while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
							$total_consolide += number_format($row4['price'], 2, ".", "");
						}

						/*$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$rowagent['id']}' and date_add >= '".$sql_date_min."' and date_add <= '".$sql_date_max."' and is_factured = 1 order by id ASC");

						while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
							if($rowpenality['is_factured']){
								if($rowpenality['message_id']){
									$total_penality += 12;
								}
							}
						}*/
					}
				}else{
					$total = 0;
					$total_gain = 0;
					$total_prime = 0;
					$total_sponsor = 0;
					$total_penality = 0;
					$total_consolide = 0;
					$is_sold = 1;
					$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start, C.is_sold , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$rowagent['id']}' and C.date_start >= '{$sql_date_min}' and C.date_start <= '{$sql_date_max}' and P.id_user_credit_history = C.user_credit_history and C.is_factured = 1");
					while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
						$price = number_format($row4['price'], 2, ".", "");
						$total_gain += $price;
						if(!$row4['is_sold'])$is_sold = 0;
						$ca += $row4['price'];
					}

					$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$date->annee_min}' AND annee <= '{$date->annee_max}' ) and (mois >= '{$date->mois_min}' AND mois <= '{$date->mois_max}' ) and active = 1 order by id DESC");
					$month_done = array();
					while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
						$bonus_montant = 0;
						$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
						$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
						$bonus_montant += $rowbonus['amount'];
						if($bonus_montant && !in_array($rowbonusagent['mois'],$month_done)){
							$total_prime += $bonus_montant;
							array_push($month_done, $rowbonusagent['mois']);
						}
					}


					$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4 order by id asc");
					while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
						$bonus_montant = 0;
						$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$sql_date_min}' and date_start <= '{$sql_date_max}'");
						$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
						$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];

						if($bonus_montant){
							$total_sponsor += $bonus_montant;
						}
					}

					$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$rowagent['id']}' and date_ecriture >= '".$sql_date_min."' and date_ecriture <= '".$sql_date_max."' order by id ASC");

					while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){
						$total_gain += number_format($rowfacturation['amount']*-1,2);
						$total_penality += number_format($rowfacturation['amount'] * -1,2);
					}

					$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$rowagent['id']}' and date_add >= '".$sql_date_min."' and date_add <= '".$sql_date_max."' and is_factured = 1 order by id ASC");

					while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
						if($rowpenality['is_factured']){
							//$total -= $rowpenality['penality_cost'];
							//$total_penality += $rowpenality['penality_cost'];

							if($rowpenality['message_id']){
								$total -= 12;
								$total_penality += 12;
							}
						}
					}



					$total = number_format($total_gain + $total_prime + $total_sponsor - $total_penality,2, ".", "");
					$paid_date = '';
					$paid_total = 0;
					$is_send = 0;
					$date_send = 0;
					$paid_total_valid = 0;
				}

				$mode = '';



				if($cond_mode)
					$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and date_add <= '".$sql_date_max."' and mode = '".$cond_mode."' order by id DESC LIMIT 1");
				else
					$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and date_add <= '".$sql_date_max."' order by id DESC LIMIT 1");


				while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
					$mode = $rowmode['mode'];
				}

				if(!$mode && !$cond_mode){
					$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' order by id DESC LIMIT 1");
					while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
						$mode = $rowmode['mode'];
					}
				}

				if(!$mode && $cond_mode){
					$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and mode = '".$cond_mode."' order by id DESC LIMIT 1");
					while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
						$mode = $rowmode['mode'];
					}
				}
				$stripe_balance = 0;
				$stripe_balance_available = 0;
				$stripe_base = 0;
				if(is_numeric($rowagent['stripe_base']) && is_numeric($rowagent['stripe_base'])){
					$stripe_base = $rowagent['stripe_base'];
				}
				if(is_numeric($rowagent['stripe_balance']) && is_numeric($rowagent['stripe_available'])){
					$stripe_balance = $rowagent['stripe_balance'];
					$stripe_balance_available = $rowagent['stripe_available'];
				}
         $is_available_stripe = true;
        $countrie_stripe = Configure::read('Stripe.countries');
        if($rowagent['societe_pays'] && !in_array($rowagent['societe_pays'],$countrie_stripe)){$is_available_stripe = false;}
        if(!$rowagent['societe_pays'] && !in_array($rowagent['country_id'],$countrie_stripe)){$is_available_stripe = false;}
        if($rowagent['bank_country'] && !$is_available_stripe && strtolower($rowagent['bank_country']) == 'france'){
          $is_available_stripe = true;
          $rowagent['societe_pays'] = 1;
        }
				if($cond_mode_bis){
					if($mode == 'Virement' && !$rowagent['stripe_account']) $mode = '';
          if($mode == 'Virement' && !$is_available_stripe) $mode = '';
				}



				if(!$cond_mode_bis && $cond_mode == 'Virement' && $is_available_stripe){
					if($mode == 'Virement' && $rowagent['stripe_account']) $mode = '';
				}

				if($mode == 'Virement' && $rowagent['stripe_account']  && $is_available_stripe) $mode = 'Stripe';

        //check if stripe country auth
        if($rowagent['societe_pays'] && !in_array($rowagent['societe_pays'],$countrie_stripe) && !$cond_mode_bis){$mode = 'Virement';$rowagent['stripe_account'] = '';}
        if(!$rowagent['societe_pays'] && !in_array($rowagent['country_id'],$countrie_stripe)  && !$cond_mode_bis ){$mode = 'Virement';$rowagent['stripe_account'] = '';}

        if($rowagent['bank_country'] && strtolower($rowagent['bank_country']) == 'maroc' ){$mode = 'Virement';$rowagent['stripe_account'] = '';}


				if($date->annee_max == date('Y')){
					$tva_info = $rowagent['vat_num_status'];
					if($rowagent['vat_num_status'] == 'invalide' && $rowagent['vat_num_proof'])
						$tva_info = 'valide';

					if(!$rowagent['vat_num'] && $rowagent['vat_num_proof']){
						$rowagent['vat_num'] = 1;
						$tva_info = 'valide';
					}
				}

				$dm_fact    = new DateTime($date_min_fact);
				$dm_fact->modify('+ '.$listing_utcdec[$dx->format('md')].' hour');

				$line = array();
				$line['date'] = $date->annee_min.'-'.$date->mois_min;
				$line['fact_min'] = $date->date_min;
				$line['fact_min_date'] = $dm_fact->format('Y-m-d H:i:s');
				$line['fact_max'] = $date->date_max;
				$line['id'] = $rowagent['id'];
				$line['active'] = $rowagent['active'];
				$line['vat_num'] = $rowagent['vat_num'];
				$line['vat_status'] = $tva_info;
				$line['iban'] = $rowagent['iban'];
				$line['country_id'] = $rowagent['country_id'];
				$line['pseudo'] = utf8_encode($rowagent['pseudo']);
				$line['name'] = utf8_encode($rowagent['lastname'].' '.$rowagent['firstname']);
				$line['societe'] = utf8_encode($rowagent['societe']);
				$line['gain'] = number_format($total_gain,2, ".", "");
				$line['prime'] = number_format($total_prime,2, ".", "");
				$line['sponsor'] = number_format($total_sponsor,2, ".", "");
				$line['penality'] = number_format($total_penality,2, ".", "");
				$line['total'] = number_format($total,2, ".", "");
				$line['stripe'] = $stripe_balance;
				$line['stripe_available'] = $stripe_balance_available;
				$line['stripe_payout_status'] = $rowagent['stripe_payout_status'];
				$line['stripe_base'] = $stripe_base;
				$line['is_sold'] = $is_sold;
				$line['mode'] = $mode;
				$line['invoice_id'] = $invoice_agent['InvoiceAgent']['id'];
				$line['invoice_status'] = $invoice_agent['InvoiceAgent']['status'];
				$line['invoice_valid_1'] = $invoice_agent['InvoiceAgent']['is_valid_1'];
				$line['invoice_valid_2'] = $invoice_agent['InvoiceAgent']['is_valid_2'];
				$line['is_send'] = $is_send;
				$line['date_send'] = $date_send;
				$line['paid_date'] = $paid_date;
				$line['paid_total'] = $paid_total;
				$line['paid_total_valid'] = $paid_total_valid;
				$line['stripe_account'] = $rowagent['stripe_account'];
				$line['vat_amount'] = $vat_amount;
				$line['tva_applique'] = $tva_applique;
				$line['is_avoir'] = $is_avoir;
				$line['consolide'] = $total_consolide;

				$is_status = 1;
				if(is_numeric($cond_status)){
					if(!$cond_status && $invoice_agent['InvoiceAgent']['status'] == 1)$is_status = 0;
					if($cond_status == 1 && $invoice_agent['InvoiceAgent']['status'] != 1)$is_status = 0;
					if(!$cond_status  && $is_sold)$is_status = 0;
					//if($cond_status == 1  && !$is_sold)$is_status = 0;
				}

				if($cond_status == 'TVA INVALIDE' && $tva_info == 'valide'){
					$is_status = 0;
				}

				if($cond_status == 'TVA APPLIQUE' && !$tva_applique){
					$is_status = 0;
				}

				if($cond_status == 'AVOIR' && !$is_avoir){
					$is_status = 0;
				}

        if($cond_status == 'ERROR' && $invoice_agent['InvoiceAgent']['status'] != 5){
					$is_status = 0;
				}


				if(($cond_mode && !$mode) || !$is_status ){
					//on remonte pas ces lignes
				}else{
					if($total_gain > 0 || $ca > 0){

						if($min){
							if(!$max)$max = 99999999;
							if($total >= $min && $total <= $max){
								array_push($lastOrder,$line);
							}
						}else{
							array_push($lastOrder,$line);
						}

					}
				}

			}
		}
		$mysqli->close();

        $this->set(compact('lastOrder','name','admin_id'));
    }


	public function admin_order($invoice_id = NULL){
		 ini_set("memory_limit",-1);
		set_time_limit ( 0 );
		$user_co = $this->Session->read('Auth.User');
		$admin_id = $user_co['id'];

		$this->loadModel('InvoiceAgent');
		$this->loadModel('InvoiceVoucherAgent');

		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

	 	$dbb_r = new DATABASE_CONFIG();
		$dbb_s = $dbb_r->default;

		$listing_utcdec = Configure::read('Site.utcDec');

		//$utc_dec = Configure::read('Site.utc_dec');

		/*$utc_dec = 1;//Configure::read('Site.utc_dec');
		if($this->Session->read('DateStats.start') )
			$cut = explode('-',$this->Session->read('DateStats.start') );
		else
			$cut = explode('-', date('Y-m-01') );


		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
			$utc_dec = 2;*/

		 //Avons-nous un filtre sur la date ??
        if($this->Session->check('Date')){
			$period_month_end = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$dx = new DateTime($period_month_end);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_end = $dx->format('Y-m-d H:i:s');
			$period_month2 = $dx->format('Y-m');
			$period_month_start = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$dx = new DateTime($period_month_start);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_start = $dx->format('Y-m-d H:i:s');
			$period_month = $dx->format('Y-m');

        }else{
			$period_month = date('Y-m');
			$period_month2 = date('Y-m');

			$dx = new DateTime(date('Y-m-01 00:00:00'));
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_start = $dx->format('Y-m-d H:i:s');

			$dx = new DateTime(date('Y-m-d 00:00:00'));
			$dx->modify('last day of this month');
			$period_month_end = $dx->format('Y-m-d 23:59:59');
			$dx = new DateTime($period_month_end);
			//$dx->modify('- '.$utc_dec.' hour');
			$period_month_end = $dx->format('Y-m-d H:i:s');
		}

		$_SESSION['fact_min'] = $period_month_start;
		$_SESSION['fact_max'] = $period_month_end;

		$dd = explode('-',$period_month);
		$dd2 = explode('-',$period_month2);
		$annee_min = $dd[0];
		$annee_max = $dd2[0];
		$mois_min = $dd[1];
		$mois_max = $dd2[1];

		$name = '';
		$min = '';
		$max = '';
		$cond_mode = '';
		if(isset($this->request->data['Agent'])){

			if($this->request->data['Agent']['agent']){
				$name = $this->request->data['Agent']['agent'];
			}

			if($this->request->data['Agent']['min']){
				$min = str_replace(',','.',$this->request->data['Agent']['min']);
			}

			if($this->request->data['Agent']['max']){
				$max = str_replace(',','.',$this->request->data['Agent']['max']);
			}

			if($this->request->data['Agent']['mode']){
				$cond_mode = $this->request->data['Agent']['mode'];
			}

			if(isset($this->request->data['Agent']['status'])){
				$cond_status = $this->request->data['Agent']['status'];
			}

		}
		if($invoice_id){
			$the_invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.id' => $invoice_id),
					'recursive' => -1
				));
			if($the_invoice_agent){
				$the_agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $the_invoice_agent['InvoiceAgent']['user_id']),
					'recursive' => -1
				));
				$name = $the_agent['User']['pseudo'];
			}
		}

		$condition = '';

		if($name){
			$condition = "and (pseudo LIKE '%".$name."%' or lastname LIKE '%".$name."%')";
		}

		//check date
		$list_date = array();
		if($mois_min != $mois_max || $annee_min != $annee_max){
			$start    = (new DateTime($period_month_start))->modify('first day of this month');
			$end      = (new DateTime($period_month_end))->modify('first day of this month');
			$interval = DateInterval::createFromDateString('1 month');
			$period   = new DatePeriod($start, $interval, $end);

			foreach ($period as $dt) {
				$dmin = $dt->format("Y-m-d");
				$dmax = $dt->format("Y-m-d");
				$minn = (new DateTime($dmin))->modify('first day of this month');
				$maxx = (new DateTime($dmax))->modify('last day of this month');
				$obj = new stdClass();
				$obj->date_min = $minn->format("Y-m-d 00:00:00");
				$obj->date_max = $maxx->format("Y-m-d 23:59:59");
				$minn = new DateTime($obj->date_min);
				$maxx = new DateTime($obj->date_max);
				//$minn->modify('- '.$utc_dec.' hour');
				//$maxx->modify('- '.$utc_dec.' hour');
				$obj->date_min = $minn->format("Y-m-d H:i:s");
				$obj->date_max = $maxx->format("Y-m-d H:i:s");
				$obj->mois_min = $minn->format("m");
				$obj->mois_max = $maxx->format("m");
				$obj->annee_min = $minn->format("Y");
				$obj->annee_max = $maxx->format("Y");
				array_push($list_date,$obj);
			}
		}else{
			$obj = new stdClass();
			$obj->date_min = $period_month_start;
			$obj->date_max = $period_month_end;
			$obj->mois_min = $mois_min;
			$obj->mois_max = $mois_max;
			$obj->annee_min = $annee_min;
			$obj->annee_max = $annee_max;
			array_push($list_date,$obj);
		}

		$lastOrder = array();
		$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
		$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' ".$condition." order by lastname");
		/*$cond_mode_bis = '';
		if($cond_mode == 'Stripe' || $cond_status == 'TVA INVALIDE'){
			$cond_mode = 'Virement';
			$cond_mode_bis = 'Stripe';
		}*/

		while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
			$line = array();
			foreach($list_date as $date){

				$dx = new DateTime($date->date_min);
				$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
				$sql_date_min = $dx->format('Y-m-d H:i:s');

				//if($sql_date_min == '2019-02-28 22:00:00')$sql_date_min = '2019-02-28 23:00:00';
				//if($sql_date_min == '2018-12-31 22:00:00')$sql_date_min = '2019-01-01 00:00:00';
				//if($sql_date_min == '2019-01-31 22:00:00')$sql_date_min = '2019-02-01 00:00:00';
				$dx = new DateTime($date->date_max);
				$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
				$sql_date_max = $dx->format('Y-m-d H:i:s');
				//if($sql_date_max == '2019-01-31 21:59:59')$sql_date_max = '2019-01-31 23:59:59';
				//if($sql_date_max == '2019-02-28 21:59:59')$sql_date_max = '2019-02-28 22:59:59';
				$ca = 0;
				$total = 0;
				$total_gain = 0;
				$total_prime = 0;
				$total_sponsor = 0;
				$total_penality = 0;
				$is_sold = 0;
				$paid_date = '';
				$paid_total = 0;
				$paid = 0;
				$is_send = 0;
				$date_send = 0;
				$paid_total_valid = 0;
				$vat_amount = 0;
				$is_avoir = 0;
				$tva_applique = 0;
				$date_min_fact = $sql_date_min;
				$mode = '';
				$invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.user_id' => $rowagent['id'],'InvoiceAgent.date_max' => $sql_date_max),
					'recursive' => -1
				));//'InvoiceAgent.date_min' => $sql_date_min

				if($invoice_agent){

					$invoice_voucher = $this->InvoiceVoucherAgent->find('first',array(
						'conditions' => array('InvoiceVoucherAgent.invoice_id' => $invoice_agent['InvoiceAgent']['id']),
						'recursive' => -1
					));
					if($invoice_voucher)$is_avoir = 1;
					$mode = $invoice_agent['InvoiceAgent']['payment_mode'];
					$ca = $invoice_agent['InvoiceAgent']['ca'];
					$total_sponsor = $invoice_agent['InvoiceAgent']['sponsor'];
					$total_prime = $invoice_agent['InvoiceAgent']['bonus'] ;
					$total_gain = $invoice_agent['InvoiceAgent']['paid'] ;
					$total_penality = $invoice_agent['InvoiceAgent']['penality'] + ($invoice_agent['InvoiceAgent']['other']*-1);
					$total = $total_gain + $total_prime - $total_penality;
					$paid_date = $invoice_agent['InvoiceAgent']['paid_date'];
					$paid_total = $invoice_agent['InvoiceAgent']['paid_total'];
					$paid = $invoice_agent['InvoiceAgent']['paid'];
					$paid_total_valid = $invoice_agent['InvoiceAgent']['paid_total_valid'];
					$is_send = $invoice_agent['InvoiceAgent']['is_send'];
					$date_send = $invoice_agent['InvoiceAgent']['date_send'];
					$vat_amount = $invoice_agent['InvoiceAgent']['vat'];
					if($invoice_agent['InvoiceAgent']['status'] > 0 && $invoice_agent['InvoiceAgent']['status'] < 2)$is_sold = 1;
					if($invoice_agent['InvoiceAgent']['status'] == 10)$is_sold = 1;
					if($invoice_agent['InvoiceAgent']['vat_tx'] > 0)$tva_applique = 1;
					$date_min_fact = $invoice_agent['InvoiceAgent']['date_min'];
					$total_consolide = 0;
					if($date_min_fact != $sql_date_min){
						$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start, C.is_sold , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$rowagent['id']}' and C.date_start >= '{$sql_date_min}' and C.date_start <= '{$sql_date_max}' and P.id_user_credit_history = C.user_credit_history  and C.is_factured = 1");
						while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
							$total_consolide += number_format($row4['price'], 2, ".", "");
						}

						/*$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$rowagent['id']}' and date_add >= '".$sql_date_min."' and date_add <= '".$sql_date_max."' and is_factured = 1 order by id ASC");

						while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
							if($rowpenality['is_factured']){
								if($rowpenality['message_id']){
									$total_penality += 12;
								}
							}
						}*/
					}
				}else{
					$total = 0;
					$total_gain = 0;
					$total_prime = 0;
					$total_sponsor = 0;
					$total_penality = 0;
					$total_consolide = 0;
					$is_sold = 1;
					$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start, C.is_sold , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$rowagent['id']}' and C.date_start >= '{$sql_date_min}' and C.date_start <= '{$sql_date_max}' and P.id_user_credit_history = C.user_credit_history and C.is_factured = 1");
					while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
						$price = number_format($row4['price'], 2, ".", "");
						$total_gain += $price;
						if(!$row4['is_sold'])$is_sold = 0;
						$ca += $row4['price'];
					}

					$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$date->annee_min}' AND annee <= '{$date->annee_max}' ) and (mois >= '{$date->mois_min}' AND mois <= '{$date->mois_max}' ) and active = 1 order by id DESC");
					$month_done = array();
					while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
						$bonus_montant = 0;
						$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
						$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
						$bonus_montant += $rowbonus['amount'];
						if($bonus_montant && !in_array($rowbonusagent['mois'],$month_done)){
							$total_prime += $bonus_montant;
							array_push($month_done, $rowbonusagent['mois']);
						}
					}


					$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4 order by id asc");
					while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
						$bonus_montant = 0;
						$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$sql_date_min}' and date_start <= '{$sql_date_max}'");
						$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
						$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];

						if($bonus_montant){
							$total_sponsor += $bonus_montant;
						}
					}

					$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$rowagent['id']}' and date_ecriture >= '".$sql_date_min."' and date_ecriture <= '".$sql_date_max."' order by id ASC");

					while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){
						$total_gain += number_format($rowfacturation['amount']*-1,2);
						$total_penality += number_format($rowfacturation['amount'] * -1,2);
					}

					$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$rowagent['id']}' and date_add >= '".$sql_date_min."' and date_add <= '".$sql_date_max."' and is_factured = 1 order by id ASC");

					while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
						if($rowpenality['is_factured']){
							//$total -= $rowpenality['penality_cost'];
							//$total_penality += $rowpenality['penality_cost'];

							if($rowpenality['message_id']){
								$total -= 12;
								$total_penality += 12;
							}
						}
					}



					$total = number_format($total_gain + $total_prime + $total_sponsor - $total_penality,2, ".", "");
					$paid_date = '';
					$paid_total = 0;
					$is_send = 0;
					$date_send = 0;
					$paid_total_valid = 0;
					$mode = '';
				}
				
				
				if(!$mode){

					if($cond_mode)
						$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and date_add <= '".$sql_date_max."' and mode = '".$cond_mode."' order by id DESC LIMIT 1");
					else
						$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and date_add <= '".$sql_date_max."' order by id DESC LIMIT 1");


					while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
						$mode = $rowmode['mode'];
					}

					if(!$mode && !$cond_mode){
						$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' order by id DESC LIMIT 1");
						while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
							$mode = $rowmode['mode'];
						}
					}

					if(!$mode && $cond_mode){
						$resultmode = $mysqli->query("SELECT * from user_pay_mode WHERE user_id = '{$rowagent['id']}' and mode = '".$cond_mode."' order by id DESC LIMIT 1");
						while($rowmode = $resultmode->fetch_array(MYSQLI_ASSOC)){
							$mode = $rowmode['mode'];
						}
					}
					if($cond_mode)$mode = '';
				}else{
					if($mode == 'bankwire')$mode = 'virement';
					$mode = ucfirst($mode);
				}
				
				
				$stripe_balance = 0;
				$stripe_balance_available = 0;
				$stripe_base = 0;
				if(is_numeric($rowagent['stripe_base']) && is_numeric($rowagent['stripe_base'])){
					$stripe_base = $rowagent['stripe_base'];
				}
				if(is_numeric($rowagent['stripe_balance']) && is_numeric($rowagent['stripe_available'])){
					$stripe_balance = $rowagent['stripe_balance'];
					$stripe_balance_available = $rowagent['stripe_available'];
				}
         $is_available_stripe = true;
        $countrie_stripe = Configure::read('Stripe.countries');
        if($rowagent['societe_pays'] && !in_array($rowagent['societe_pays'],$countrie_stripe)){$is_available_stripe = false;}
        if(!$rowagent['societe_pays'] && !in_array($rowagent['country_id'],$countrie_stripe)){$is_available_stripe = false;}
        if($rowagent['bank_country'] && !$is_available_stripe && strtolower($rowagent['bank_country']) == 'france'){
          $is_available_stripe = true;
          $rowagent['societe_pays'] = 1;
        }
			/*	if($cond_mode_bis){
					if($mode == 'Virement' && !$rowagent['stripe_account']) $mode = '';
          if($mode == 'Virement' && !$is_available_stripe) $mode = '';
				}



				if(!$cond_mode_bis && $cond_mode == 'Virement' && $is_available_stripe){
					if($mode == 'Virement' && $rowagent['stripe_account']) $mode = '';
				}

				if($mode == 'Virement' && $is_available_stripe) $mode = 'Stripe';*/

        //check if stripe country auth
        /*if($rowagent['societe_pays'] && !in_array($rowagent['societe_pays'],$countrie_stripe) && !$cond_mode_bis){$mode = 'Virement';$rowagent['stripe_account'] = '';}
        if(!$rowagent['societe_pays'] && !in_array($rowagent['country_id'],$countrie_stripe)  && !$cond_mode_bis ){$mode = 'Virement';$rowagent['stripe_account'] = '';}

        if($rowagent['bank_country'] && strtolower($rowagent['bank_country']) == 'maroc' ){$mode = 'Virement';$rowagent['stripe_account'] = '';}*/
				if($mode == 'Virement')$rowagent['stripe_account'] = '';
				if($cond_mode && $cond_mode != $mode)$mode = '';
				
				if($date->annee_max == date('Y')){
					$tva_info = $rowagent['vat_num_status'];
					if($rowagent['vat_num_status'] == 'invalide' && $rowagent['vat_num_proof'])
						$tva_info = 'valide';

					if(!$rowagent['vat_num'] && $rowagent['vat_num_proof']){
						$rowagent['vat_num'] = 1;
						$tva_info = 'valide';
					}
				}

				$dm_fact    = new DateTime($date_min_fact);
				$dm_fact->modify('+ '.$listing_utcdec[$dx->format('md')].' hour');

				$line = array();
				$line['date'] = $date->annee_min.'-'.$date->mois_min;
				$line['fact_min'] = $date->date_min;
				$line['fact_min_date'] = $dm_fact->format('Y-m-d H:i:s');
				$line['fact_max'] = $date->date_max;
				$line['id'] = $rowagent['id'];
				$line['active'] = $rowagent['active'];
				$line['vat_num'] = $rowagent['vat_num'];
				$line['vat_status'] = $tva_info;
				$line['iban'] = $rowagent['iban'];
				$line['country_id'] = $rowagent['country_id'];
				$line['pseudo'] = utf8_encode($rowagent['pseudo']);
				$line['name'] = utf8_encode($rowagent['lastname'].' '.$rowagent['firstname']);
				$line['societe'] = utf8_encode($rowagent['societe']);
				$line['gain'] = number_format($total_gain,2, ".", "");
				$line['prime'] = number_format($total_prime,2, ".", "");
				$line['sponsor'] = number_format($total_sponsor,2, ".", "");
				$line['penality'] = number_format($total_penality,2, ".", "");
				$line['total'] = number_format($total,2, ".", "");
				$line['stripe'] = $stripe_balance;
				$line['stripe_available'] = $stripe_balance_available;
				$line['stripe_payout_status'] = $rowagent['stripe_payout_status'];
				$line['stripe_base'] = $stripe_base;
				$line['is_sold'] = $is_sold;
				$line['mode'] = $mode;
				$line['invoice_id'] = $invoice_agent['InvoiceAgent']['id'];
				$line['invoice_status'] = $invoice_agent['InvoiceAgent']['status'];
				$line['invoice_valid_1'] = $invoice_agent['InvoiceAgent']['is_valid_1'];
				$line['invoice_valid_2'] = $invoice_agent['InvoiceAgent']['is_valid_2'];
				$line['is_send'] = $is_send;
				$line['date_send'] = $date_send;
				$line['paid_date'] = $paid_date;
				$line['paid_total'] = $paid_total;
				$line['paid_total_valid'] = $paid_total_valid;
				$line['stripe_account'] = $rowagent['stripe_account'];
				$line['vat_amount'] = $vat_amount;
				$line['tva_applique'] = $tva_applique;
				$line['is_avoir'] = $is_avoir;
				$line['consolide'] = $total_consolide;

				$is_status = 1;
				if(is_numeric($cond_status)){
					if(!$cond_status && $invoice_agent['InvoiceAgent']['status'] == 1)$is_status = 0;
					if($cond_status == 1 && $invoice_agent['InvoiceAgent']['status'] != 1)$is_status = 0;
					if(!$cond_status  && $is_sold)$is_status = 0;
					//if($cond_status == 1  && !$is_sold)$is_status = 0;
				}

				if($cond_status == 'TVA INVALIDE' && $tva_info == 'valide'){
					$is_status = 0;
				}

				if($cond_status == 'TVA APPLIQUE' && !$tva_applique){
					$is_status = 0;
				}

				if($cond_status == 'AVOIR' && !$is_avoir){
					$is_status = 0;
				}

        if($cond_status == 'ERROR' && $invoice_agent['InvoiceAgent']['status'] != 5){
					$is_status = 0;
				}


				if(($cond_mode && !$mode) || !$is_status ){
					//on remonte pas ces lignes
				}else{
					if($total_gain > 0 || $ca > 0){

						if($min){
							if(!$max)$max = 99999999;
							if($total >= $min && $total <= $max){
								array_push($lastOrder,$line);
							}
						}else{
							array_push($lastOrder,$line);
						}

					}
				}

			}
		}
		$mysqli->close();

        $this->set(compact('lastOrder','name','admin_id'));
    }

	public function admin_order_pop($invoice_id){
		$this->layout = false;
        $this->autoRender = false;

		if(!$invoice_id){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur technique'));
			$this->render('/Agents/admin_order_pop');
			return false;
		}

		$this->loadModel('InvoiceAgent');

		$invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.id' => $invoice_id),
					'recursive' => -1
				));
		if(!$invoice_agent){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur lors du chargement de cette facture'));
			$this->render('/Agents/admin_order_pop');
			return false;
		}

		$agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
					'recursive' => -1
				));


		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

		$stripe_balance = 0;
		if($agent['User']['stripe_account']){
			try {
				//$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
				$balance = \Stripe\Balance::retrieve(
				  ["stripe_account" => $agent['User']['stripe_account']]
				);
				if($balance->available && is_array($balance->available)){
					$available = $balance->available[0];
					$stripe_balance = $available->amount /100;
				}
			 } catch (\Stripe\Error\Base $e) {

			}
		}

		$stripe_base = 0;
		if(is_numeric($rowagent['stripe_base']) && is_numeric($rowagent['stripe_base'])){
			$stripe_base = $rowagent['stripe_base'];
		}
		$stripe_balance = $stripe_balance - $stripe_base;


        $this->set(array('title' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'], 'stripe_account' => $agent['User']['stripe_account'],'amount_total' => $invoice_agent['InvoiceAgent']['paid_total'],'stripe_balance' => $stripe_balance, 'invoice_id' => $invoice_agent['InvoiceAgent']['id']));
        $this->render('/Agents/admin_order_pop');
	}

	public function admin_order_stripe(){

		 if($this->request->is('ajax')){
			 if(!isset($this->request->data['Agent']['invoice_id']) || empty($this->request->data['Agent']['invoice_id']) || !is_numeric($this->request->data['Agent']['invoice_id'])){
				 $this->jsonRender(array('msg' => 'Erreur lors du traitement de cette facture'));
			 }else{

				 if(!isset($this->request->data['Agent']['total']) || empty($this->request->data['Agent']['total']) || !is_numeric($this->request->data['Agent']['total'])){
					$this->jsonRender(array('msg' => 'Montant non renseigné'));
				 }else{

					 $amount_total = str_replace(',','.',$this->request->data['Agent']['total']);

					 $this->loadModel('InvoiceAgent');

					 $invoice_agent = $this->InvoiceAgent->find('first',array(
								'conditions' => array('InvoiceAgent.id' => $this->request->data['Agent']['invoice_id']),
								'recursive' => -1
							));

					 if($invoice_agent['InvoiceAgent']['status'] == 0){

						 $this->InvoiceAgent->id = $this->request->data['Agent']['invoice_id'];
						 $this->InvoiceAgent->saveField('status', 2);
						 $this->InvoiceAgent->saveField('paid_total_valid', $amount_total);

						 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));


							App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

						 $stripe_balance = 0;
						try {
								$balance = \Stripe\Balance::retrieve(
									  ["stripe_account" => $agent['User']['stripe_account']]
									);
								if($balance->available && is_array($balance->available)){
									$available = $balance->available[0];
									$stripe_balance += $available->amount /100;
								}
							} catch (\Stripe\Error\Base $e) {

							}

						 if($balance >= $amount_total){

							 $url = Router::url(array('controller' => 'agents', 'action' => 'order-'.$this->request->data['Agent']['invoice_id'], 'admin' => true),true);

							 $admin_emails = Configure::read('Site.emailsAdmins');
							 if(is_array($admin_emails)){
								 foreach($admin_emails as $email){
									$is_send = $this->sendCmsTemplatePublic(447, 1, $email, array(
											'AGENT' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'],
											'URL' =>$url,
											'AMOUNT_TOTAL' => $amount_total,
											'PARAM_SITE' => 'Spiriteo.com'
										));
								 }
							 }
						 }
						 $this->jsonRender(array('msg' => 'Montant validé, vous allez recevoir un email pour confirmer ce virement.', 'action' => '<a href="/admin/agents/order_stripe_valid-'.$this->request->data['Agent']['invoice_id'].'" class="btn blue">Valider</a>'));
					 }else{
						 $this->jsonRender(array('msg' => 'Demande déjà traité'));
					 }
				 }
			 }

		 }
		//$this->redirect(array('controller' => 'agents', 'action' => 'order-'.$this->request->data['Agent']['invoice_id'], 'admin' => true),false);
	}

	public function admin_order_vat_regul($invoice_id, $redir = true){
		$this->layout = false;
        $this->autoRender = false;
		if(!$invoice_id){
			$this->Session->setFlash('Erreur lors du chargement de la page', 'flash_warning');
			if($redir)
            	$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true),false);
			else
				$this->jsonRender(array('msg' => 'Erreur dans le chargement d une facture'));
		}

		$this->loadModel('InvoiceAgent');

		$invoice_agent = $this->InvoiceAgent->find('first',array(
								'conditions' => array('InvoiceAgent.id' => $invoice_id),
								'recursive' => -1
							));
		if(!$invoice_agent){
			$this->Session->setFlash('Erreur lors du chargement', 'flash_warning');

			if($redir)
            	$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true),false);
			else
				$this->jsonRender(array('msg' => 'Erreur dans le chargement d une facture'));
		}

		$this->InvoiceAgent->id = $invoice_id;
		$this->InvoiceAgent->saveField('status', 11);

		if($redir)
		$this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
	}

	public function admin_order_stripe_valid_group(){
		ini_set("memory_limit",-1);
		set_time_limit ( 0 );
		$this->layout = false;
        $this->autoRender = false;
		 if($this->request->is('ajax')){
			if($this->request->data['liste']){
				foreach($this->request->data['liste'] as $invoice_id){
					$this->admin_order_stripe_valid($invoice_id, false);
				}
			}
		 }
		$this->Session->setFlash('Votre action groupé a été réalisé.', 'flash_success');
		$this->jsonRender(array(
                'return'          => true,
            ));
	}

	public function admin_order_stripe_valid($invoice_id, $redir = true){
		$this->layout = false;
        $this->autoRender = false;

		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require_once(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);


		$this->layout = false;
        $this->autoRender = false;
		$user_co = $this->Session->read('Auth.User');

		if(!$invoice_id){
			$this->Session->setFlash('Erreur lors du chargement de la page', 'flash_warning');
			if($redir)
            	$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true),false);
			else
				$this->jsonRender(array('msg' => 'Erreur dans le chargement d une facture'));
		}

		$this->loadModel('InvoiceAgent');

		$invoice_agent = $this->InvoiceAgent->find('first',array(
								'conditions' => array('InvoiceAgent.id' => $invoice_id),
								'recursive' => -1
							));
		if(!$invoice_agent){
			$this->Session->setFlash('Erreur lors du chargement', 'flash_warning');

			if($redir)
            	$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true),false);
			else
				$this->jsonRender(array('msg' => 'Erreur dans le chargement d une facture'));
		}

		 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));

		switch ($invoice_agent['InvoiceAgent']['status']) {
			case 0:
				$this->Session->setFlash('Ce virement n\'est pas valide a une confirmation !', 'flash_warning');
				if($redir)
					$this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
				else
					$this->jsonRender(array('msg' => 'Erreur virement selectionné non valable'));
				break;
			case 1:
				$this->Session->setFlash('Ce virement est déjà réalisé !', 'flash_warning');
				if($redir)
					$this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
				else
					$this->jsonRender(array('msg' => 'Erreur virement deja realisé'));
				break;
			case 2:

				//check si deja valide par l auth
				$already_valid = false;
				if($invoice_agent['InvoiceAgent']['is_valid_1'] && $invoice_agent['InvoiceAgent']['is_valid_1'] == $user_co['id']){
					$already_valid = true;
				}
				if($invoice_agent['InvoiceAgent']['is_valid_2'] && $invoice_agent['InvoiceAgent']['is_valid_2'] == $user_co['id']){
					$already_valid = true;
				}
				if(!$already_valid){

					$this->InvoiceAgent->id = $invoice_id;
					$this->InvoiceAgent->saveField('status', 3);
					if(!$invoice_agent['InvoiceAgent']['is_valid_1'])
						$this->InvoiceAgent->saveField('is_valid_1', $user_co['id']);
					if($invoice_agent['InvoiceAgent']['is_valid_1'] && !$invoice_agent['InvoiceAgent']['is_valid_2'])
						$this->InvoiceAgent->saveField('is_valid_2', $user_co['id']);


					$stripe_balance = 0;
						try {
								$balance = \Stripe\Balance::retrieve(
									  ["stripe_account" => $agent['User']['stripe_account']]
									);
								if($balance->available && is_array($balance->available)){
									$available = $balance->available[0];
									$stripe_balance += $available->amount /100;
								}
							} catch (\Stripe\Error\Base $e) {

							}
					if($invoice_agent['InvoiceAgent']['paid_total_valid'] > $stripe_balance ){
						$this->InvoiceAgent->id = $invoice_id;
						$this->InvoiceAgent->saveField('status', 5);
					}


					if($redir){
						$this->Session->setFlash('Votre validation a été prise en compte, il manque encore la deuxième', 'flash_success');
						$this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
					}
				}else{
					if($redir){
						$this->Session->setFlash('Deja validé', 'flash_warning');
						$this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
					}
				}
				break;
			case 3:
			case 6:
			case 8:

        //check si deja valide par l auth
				$already_valid = false;
				if($invoice_agent['InvoiceAgent']['is_valid_1'] && $invoice_agent['InvoiceAgent']['status'] == 3 && $invoice_agent['InvoiceAgent']['is_valid_1'] == $user_co['id']){
					$already_valid = true;
				}
				if($invoice_agent['InvoiceAgent']['is_valid_2'] && $invoice_agent['InvoiceAgent']['status'] == 3 && $invoice_agent['InvoiceAgent']['is_valid_2'] == $user_co['id']){
					$already_valid = true;
				}

        if(!$already_valid){

          $this->InvoiceAgent->id = $invoice_id;
          $this->InvoiceAgent->saveField('status', 4);
          if(!$invoice_agent['InvoiceAgent']['is_valid_1'])
            $this->InvoiceAgent->saveField('is_valid_1', $user_co['id']);
          if($invoice_agent['InvoiceAgent']['is_valid_1'] && !$invoice_agent['InvoiceAgent']['is_valid_2'])
            $this->InvoiceAgent->saveField('is_valid_2', $user_co['id']);
          $this->Session->setFlash('Votre validation a été prise en compte et le virement va être réalisé.', 'flash_success');

          //procede au paiement
           $agent = $this->User->find('first',array(
                  'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
                  'recursive' => -1
                ));
          try {

            if($invoice_agent['InvoiceAgent']['paid_total_valid'] > 0){
              $payout = \Stripe\Payout::create([
                'amount' => $invoice_agent['InvoiceAgent']['paid_total_valid'] * 100,
                'currency' => 'eur',
              ], ['stripe_account' => $agent['User']['stripe_account']]);

              $this->Session->setFlash('La virement extérieur a été initialisé', 'flash_success');

            }else{
              $account = \Stripe\Account::retrieve();
                \Stripe\Transfer::create(
                        [
                        "amount" => $invoice_agent['InvoiceAgent']['paid_total_valid'] * -100,
                        "currency" => "eur",
                        "destination" => $account->id
                        ],
                        ["stripe_account" => $agent['User']['stripe_account']]
                      );
              $this->Session->setFlash('La regularisation a été réalisé', 'flash_success');

            }

            $this->InvoiceAgent->id = $invoice_id;
            $this->InvoiceAgent->saveField('status', 7);
            $this->InvoiceAgent->saveField('paid_date', date('Y-m-d H:i:s'));
            if($payout)
            $this->InvoiceAgent->saveField('payment_id', $payout->id);

            //update de toutes les comm en payé
            $this->loadModel('UserCreditHistory');
            $comms = $this->UserCreditHistory->find('all', array(
                'fields'        => array("UserCreditHistory.user_credit_history"),
                'conditions'    => array("UserCreditHistory.date_start >=" => $invoice_agent['InvoiceAgent']['date_min'],"UserCreditHistory.date_start <=" => $invoice_agent['InvoiceAgent']['date_max'], 'agent_id' => $agent['User']['id']),
                'recursive'     => -1
              ));

            foreach($comms as $comm){
              $this->UserCreditHistory->id = $comm['UserCreditHistory']['user_credit_history'];
              $this->UserCreditHistory->saveField('is_sold', 1);
            }

           } catch (\Stripe\Error\Base $e) {
            $this->Session->setFlash('Erreur suite au virement pour '.$agent['User']['pseudo']. ' '.$e->getMessage(), 'flash_warning');

            $this->InvoiceAgent->id = $invoice_id;
            $this->InvoiceAgent->saveField('status', 5);
          }

          if($redir)
            $this->redirect(array('controller' => 'agents', 'action' => 'order', 'admin' => true),false);
        }
				break;
		}
	}

	public function admin_order_pop_mail($invoice_id){
		$this->layout = false;
        $this->autoRender = false;

		if(!$invoice_id){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur technique'));
			$this->render('/Agents/admin_order_pop');
			return false;
		}

		$this->loadModel('InvoiceAgent');

		$invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.id' => $invoice_id),
					'recursive' => -1
				));
		if(!$invoice_agent){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur lors du chargement de cette facture'));
			$this->render('/Agents/admin_order_pop_mail');
			return false;
		}

		$agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
					'recursive' => -1
				));
		$body_mail = $this->getCmsPage(446, 1);
		$body_mail = html_entity_decode($body_mail["PageLang"]["content"]);

		$body_mail = str_replace('##PARAM_NOM_EXPERT##',$agent['User']['pseudo'],$body_mail);
		$body_mail = str_replace('##PARAM_MONTANT_VIREMENT##',number_format($invoice_agent['InvoiceAgent']['paid_total_valid'],2,'.',''),$body_mail);
		$body_mail = str_replace('##PARAM_MOIS_EN_COURS##',date('m/Y'),$body_mail);
		$body_mail = str_replace('##MODE_PAIEMENT##',$agent['User']['mode_paiement'],$body_mail);
		$body_mail = str_replace('##SITE_NAME##',Configure::read('Site.name'),$body_mail);

        $this->set(array('title' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'], 'mode_paiement' => $agent['User']['mode_paiement'],'amount_total' => $invoice_agent['InvoiceAgent']['paid_total_valid'], 'invoice_id' => $invoice_agent['InvoiceAgent']['id'], 'body_mail' => $body_mail));
        $this->render('/Agents/admin_order_pop_mail');
	}

	public function admin_order_mail(){

		 if($this->request->is('ajax')){
			 if(!isset($this->request->data['Agent']['invoice_id']) || empty($this->request->data['Agent']['invoice_id']) || !is_numeric($this->request->data['Agent']['invoice_id'])){
				 $this->jsonRender(array('msg' => 'Erreur lors du traitement de cette facture'));
			 }else{

				 if(!isset($this->request->data['Agent']['bodymail']) || empty($this->request->data['Agent']['bodymail'])){
					$this->jsonRender(array('msg' => 'Mail vide'));
				 }else{
					 $body_mail = nl2br($this->request->data['Agent']['bodymail']);

					 $this->loadModel('InvoiceAgent');

					 $invoice_agent = $this->InvoiceAgent->find('first',array(
								'conditions' => array('InvoiceAgent.id' => $this->request->data['Agent']['invoice_id']),
								'recursive' => -1
							));

					 if($invoice_agent['InvoiceAgent']['user_id']){

						 $this->InvoiceAgent->id = $this->request->data['Agent']['invoice_id'];
						 $this->InvoiceAgent->saveField('is_send', 1);
						 $this->InvoiceAgent->saveField('date_send', date('Y-m-d H:i:s'));

						 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));

						 $page = $this->getCmsPageMail(446, 1);
						 $page['PageLang']['content'] = $body_mail;

						$is_send = $this->sendBodyTemplatePublic($page, 1, $agent['User']['email'], array(

									));

						 $this->jsonRender(array('msg' => 'Votre email a été envoyé', 'action' => date('d/m/Y Hhm')));
					 }else{
						 $this->jsonRender(array('msg' => 'Demande déjà traité'));
					 }
				 }
			 }

		 }
		//$this->redirect(array('controller' => 'agents', 'action' => 'order-'.$this->request->data['Agent']['invoice_id'], 'admin' => true),false);
	}

	public function order_invoice(){

		 $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] !== 'agent')
            throw new Exception("Erreur de sécurité !", 1);


		$this->loadModel('UserInvoice');

		$this->Paginator->settings = array(
                'order' => array('UserInvoice.date_min' => 'desc'),
				'conditions' => array('UserInvoice.user_id' => $user['id'] ),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $invoices = $this->Paginator->paginate($this->UserInvoice);

            $this->set(compact('invoices'));

	}
	public function order(){
		/*ini_set("memory_limit",-1);*/
		$role = 'agent';
		//Utilisateur non connecté
        if(!$this->Auth->loggedIn())
            throw new Exception("Erreur de sécurité !", 1);

        /* On vérifie que l'utilisateur est bien dans son role */
        if ($this->Auth->user('role') !== $role)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        //On charge le model
        $this->loadModel('UserCreditLastHistory');
		$this->loadModel('InvoiceAgent');

        //Les conditions de base
        $conditions = array(($role === 'client' ?'users_id':'agent_id') => $this->Auth->user('id'));
		$limit = 15;//($role === 'client' ?10:15)
		//Avons-nous un filtre sur la date ??
		$is_date_filtre = 0;

		if(!$this->Session->check('Date')){

			$date_debut = date('01-m-Y');
			$dx = new DateTime($date_debut);
			$dx->modify('last day of this month');
			$delai_fin = $dx->format('d-m-Y');

			$this->Session->write('Date.start', $date_debut);
            $this->Session->write('Date.end', $delai_fin);

		}

        if($this->Session->check('Date')){
			$listing_utcdec = Configure::read('Site.utcDec');
			/*$utc_dec = 1;
				$cut = explode('-',$this->Session->read('Date.start') );
				$mois_comp = $cut[1];
				if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
					$utc_dec = 2;*/

			//if(DateTime::createFromFormat($this->Session->read('Date.start')) && DateTime::createFromFormat($this->Session->read('Date.end'))){
				$dmax = new DateTime($this->Session->read('Date.end').' 23:59:59');
				$dmin = new DateTime($this->Session->read('Date.start'). '00:00:00');
			/*}else{
				$date_debut = date('Y-m-01 00:00:00');
				$dx = new DateTime($date_debut);
				$dmin = new DateTime($dx->format('Y-m-d 00:00:00'));
				$dx->modify('last day of this month');
				$dmax = new DateTime($dx->format('Y-m-d 23:59:59'));
			}*/

			$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
			$session_date_max =  $dmax->format('Y-m-d H:i:s');
			$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
			$session_date_min =  $dmin->format('Y-m-d H:i:s');

			//if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';

                $conditions = array_merge($conditions, array(
                    'UserCreditLastHistory.date_start >=' => $session_date_min,
                    'UserCreditLastHistory.date_start <=' => $session_date_max
               ));
			$limit = 999999;
			$is_date_filtre = 1;
			$is_date_min = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$is_date_max = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
        }

        //Filtre par media ??
        if(isset($this->params->query['media'])){
            //Est-ce un média valide ??
            $medias = array_keys($this->consult_medias);
            if(in_array($this->params->query['media'], $medias))
                $conditions['media'] = $this->params->query['media'];
        }

		//les infos facture
		$invoice_agent = $this->InvoiceAgent->find('first',array(
					'conditions' => array('InvoiceAgent.user_id' => $this->Auth->user('id'),'InvoiceAgent.date_max' => $session_date_max),
					'recursive' => -1
				));// 'InvoiceAgent.date_min' => $session_date_min,

		//Les dernières communications
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'order' => 'date_start desc',
            'limit' => $limit,
			'maxLimit' => $limit
        );

        $historiqueComs = $this->Paginator->paginate($this->UserCreditLastHistory);

        //Pour chaque historique du type chat ou email
        $this->loadModel('Chat');
        $this->loadModel('Message');
		$this->loadModel('Notes');
        foreach($historiqueComs as $key => $com){
            if(in_array($com['UserCreditLastHistory']['media'], array('chat', 'email'))){
                switch($com['UserCreditLastHistory']['media']){
                    case 'chat' :
                        $idChat = $this->Chat->field('id', array('Chat.from_id' => $com['UserCreditLastHistory']['users_id'], 'Chat.to_id' => $com['UserCreditLastHistory']['agent_id'], 'Chat.consult_date_start' => $com['UserCreditLastHistory']['date_start'], 'Chat.consult_date_end !=' => null));

                        //On ajoute l'id du chat
                        if(!empty($idChat))
                            $historiqueComs[$key]['discussion'] = $idChat;
                        break;
                    case 'email' :
                        $message = $this->Message->find('first', array(
                            'fields'        => array('id', 'parent_id'),
                            'conditions'    => array('Message.from_id' => $com['UserCreditLastHistory']['users_id'], 'Message.to_id' => $com['UserCreditLastHistory']['agent_id'], 'Message.date_add' => $com['UserCreditLastHistory']['date_start'], 'Message.deleted' => 0),
                            'recursive'     => -1
                        ));

                        //Si discussion trouvée
                        if(!empty($message)){
                            if(empty($message['Message']['parent_id']))
                                $historiqueComs[$key]['discussion'] = $message['Message']['id'];
                            else
                                $historiqueComs[$key]['discussion'] = $message['Message']['parent_id'];
                        }
                        break;
                }
            }
        }
		$is_refund = false;
		if($role === 'agent'){
			$dbb_r = new DATABASE_CONFIG();
				$dbb_s = $dbb_r->default;
				$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
				$refund = array();
				$facture = array();
				foreach ($historiqueComs as $k => &$row){
					$result = $mysqli_s->query("SELECT user_credit_history from user_credit_history WHERE user_id = '{$row['UserCreditLastHistory']['users_id']}' and agent_id = '{$row['UserCreditLastHistory']['agent_id']}' and date_start = '{$row['UserCreditLastHistory']['date_start']}'");
					$row2 = $result->fetch_array(MYSQLI_ASSOC);
					if($row2['user_credit_history']){
						$result = $mysqli_s->query("SELECT price from user_pay WHERE id_user_credit_history = '{$row2['user_credit_history']}'");
						$row2 = $result->fetch_array(MYSQLI_ASSOC);
						$row['UserCreditLastHistory']['price'] = $row2['price'];
					}

					$note = $this->Notes->find('first', array(
                            'conditions'    => array('id_client' => $row['UserCreditLastHistory']['users_id'], 'id_agent' => $row['UserCreditLastHistory']['agent_id']),
                            'recursive'     => -1
                        ));

					if($note)$row['UserCreditLastHistory']['note_id'] = $note['Notes']['id'];

				}
				 if($this->Session->check('Date')){
					//check si email perdu
					$result_refund = $mysqli_s->query("SELECT * from user_penalities WHERE date_add >= '{$session_date_min}' and date_add <= '{$session_date_max}' and is_factured = 1 and user_id = '{$this->Auth->user('id')}' and message_id > 0");
					while($row_refund = $result_refund->fetch_array(MYSQLI_ASSOC)){

						 $line_supp = $this->UserCreditLastHistory->find('first',array(
							 'fields' => array('User.firstname,UserCreditLastHistory.*'),
							'conditions' => array(
								'sessionid' => $row_refund['message_id']
							),
							'recursive' => -1,
							 'joins' => array(
									array('table' => 'users',
										'alias' => 'User',
										'type' => 'left',
										'conditions' => array(
											'User.id = UserCreditLastHistory.users_id',
										)
									)
								)
						 ));
						$line_supp['UserCreditLastHistory']['media'] = 'refund';
						$line_supp['UserCreditLastHistory']['penality_id'] = $row_refund['id'];
						$line_supp['UserCreditLastHistory']['price'] = -12;
						$line_supp['UserCreditLastHistory']['date_start'] = $row_refund['date_add'];
						array_push($refund,$line_supp);

					}
					if(count($refund)){
						$is_refund = true;
						$historiqueComs = array_merge($historiqueComs,$refund);
					}
					 //check modif facture
					$result_facture = $mysqli_s->query("SELECT * from user_orders WHERE date_ecriture >= '{$session_date_min}' and date_ecriture <= '{$session_date_max}' and user_id = '{$this->Auth->user('id')}'");
					while($row_facture = $result_facture->fetch_array(MYSQLI_ASSOC)){

						$line_supp = array();
						$line_supp['UserCreditLastHistory'] = array();
						$line_supp['UserCreditLastHistory']['media'] = 'other';
						$line_supp['UserCreditLastHistory']['penality_id'] = $row_facture['id'];
						$line_supp['UserCreditLastHistory']['price'] = $row_facture['amount'];
						$line_supp['UserCreditLastHistory']['date_start'] = $row_facture['date_ecriture'];
						$line_supp['UserCreditLastHistory']['text_factured'] = utf8_encode($row_facture['commentaire']);
						$line_supp['UserCreditLastHistory']['is_factured'] = 0;
						array_push($facture,$line_supp);

					}
					if(count($facture)){
						$is_refund = true;
						$historiqueComs = array_merge($historiqueComs,$facture);
					}
				 }
			$date_fact = array();
				if($is_date_filtre){
					$date_fact['min'] = $is_date_min;
					$date_fact['max'] = $is_date_max;
					$_SESSION['fact_agent'] = $this->Auth->user('id');
					$_SESSION['fact_min'] = $is_date_min;
					$_SESSION['fact_max'] = $is_date_max;
				}else{
					$_SESSION['fact_agent'] = '';
					$_SESSION['fact_min'] = '';
					$_SESSION['fact_max'] = '';
				}

			$tabdate = explode(' ',$_SESSION['fact_min']);
			$tabdatec = explode('-',$tabdate[0]);
			$annee_min = '';
			$mois_min  = '';
			$annee_max  = '';
			$mois_max  = '';

			if(isset($tabdatec[0]))
			$annee_min = $tabdatec[0];
			if(isset($tabdatec[1]))
			$mois_min = $tabdatec[1];
			$tabdate = explode(' ',$_SESSION['fact_max']);
			$tabdatec = explode('-',$tabdate[0]);
			if(isset($tabdatec[0]))
			$annee_max = $tabdatec[0];
			if(isset($tabdatec[1]))
			$mois_max = $tabdatec[1];
			if($_SESSION['fact_agent']){
				$resultbonusagent = $mysqli_s->query("SELECT * from bonus_agents WHERE id_agent = '{$_SESSION['fact_agent']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1");
				$total_bonus = 0;
				while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
					if($rowbonusagent['id_bonus']){
						$resultbonus = $mysqli_s->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
						$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
						$total_bonus += $rowbonus['amount'];
					}
				}
			}
			$mysqli_s->close();
		}

		if($is_refund){
			$new_list = array();
			foreach($historiqueComs as $histo){
				$new_list[$histo['UserCreditLastHistory']['date_start']] = $histo;
			}

			ksort($new_list);
			$historiqueComs = array();
			foreach($new_list as $cc){
				array_push($historiqueComs,$cc);
			}
		}

        $this->set(compact('historiqueComs', 'date_fact', 'total_bonus','invoice_agent'));

	}

	 public function refund_detail(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

			$this->loadModel('UserPenality');

           $penality = $this->UserPenality->find('first', array(
				'fields' => array('Agent.pseudo,Mail.date_add'),
                'conditions' => array('UserPenality.id' => $requestData['param']),
                'joins' => array(
                    array(
                        'table' => 'messages',
                        'alias' => 'Mail',
                        'type' => 'left',
                        'conditions' => array('Mail.id = UserPenality.message_id')
                    ),
					array(
                        'table' => 'users',
                        'alias' => 'Agent',
                        'type' => 'left',
                        'conditions' => array('Agent.id = Mail.to_id')
                    )
                ),
                'recursive' => -1
            ));

            $this->layout = '';
			$date_mail = CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$penality['Mail']['date_add']),'%d/%m/%y à %Hh%M');

			$message = 'Consultation Email du '.$date_mail.' avec '.$penality['Agent']['pseudo'].' remboursée, cause délais dépassés.';

            $this->set(array('messages' => $message, 'isAjax' => true));
            $response = $this->render();
            $this->set(array('title' => 'Email remboursé', 'content' => $response->body(), 'button' => 'Fermer'));
            $response = $this->render('/Elements/modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }


    }

	public function createStripeAccount($id = null)
	{
		if($id){

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);


			App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

			$this->User->id = $id;
			//On récupère le role de l'user
			$role = $this->User->field('role');

			$dob = explode('-',$this->User->field('birthdate'));
			$cpt_country = '';
			switch ($this->User->field('country_id')) {
				case 1:
					$cpt_country = 'FR';//France
					break;
				case 2:
					$cpt_country = 'BE';//Belgique
					break;
				case 3:
					$cpt_country = 'CH';//Suisse
					break;
				case 4:
					$cpt_country = 'LU';//Luxembourg
					break;
				case 60:
					$cpt_country = 'ES';//Espagne
					break;
				case 145:
					$cpt_country = 'PT';//Portugal
					break;
			}

			switch ($this->User->field('country_id')) {
				case 1:
					$iso_country = 'FR';//France
					break;
				case 2:
					$iso_country = 'BE';//Belgique
					break;
				case 3:
					$iso_country = 'CH';//Suisse
					break;
				case 4:
					$iso_country = 'LU';//Luxembourg
					break;
				case 5:
					$iso_country = 'CA';//Canada
					break;
				case 60:
					$iso_country = 'ES';//Espagne
					break;
				/*case 120:
					$iso_country = 'MA';//Morocco
					break;*/
				case 145:
					$iso_country = 'PT';//Portugal
					break;

			}

			$iso_country_societe = '';
			switch (strtolower($this->User->field('societe_pays'))) {
				case 1:
					$iso_country_societe = 'FR';//France
					break;
				case 2:
					$iso_country_societe = 'BE';//Belgique
					break;
				case 3:
					$iso_country_societe = 'CH';//Suisse
					break;
				case 4:
					$iso_country_societe = 'LU';//Luxembourg
					break;
				case 60:
					$iso_country_societe = 'ES';//Espagne
					break;
				case 145:
					$iso_country_societe = 'PT';//Portugal
					break;
				case 31:
					$iso_country_societe = 'BG';//Bulgarie
					break;
			}

			$result_ip = $mysqli->query("SELECT IP from user_ips WHERE user_id='".$id."' order by id desc limit 1");
			$row_ip = $result_ip->fetch_array(MYSQLI_ASSOC);


			$take_iban = true;
			$cpt_postalcode = $this->User->field('postalcode');
      if(!$cpt_country) $take_iban = false;
			if(!$cpt_country) $cpt_postalcode = '33000';
			if(!$cpt_country) $cpt_country = 'FR';

			if(!$iso_country_societe) $iso_country_societe = $cpt_country;


			if($role === 'agent' && $cpt_country){

				$data = array();
				if(!$this->User->field('societe')){
					$data['city'] = $this->User->field('city');
					$data['line1'] = $this->User->field('address');
					$data['line2'] = '';
					$data['postal_code'] = $cpt_postalcode;
					$data['name'] = $this->User->field('firstname').' '.$this->User->field('lastname');
				}else{
					$data['city'] = $this->User->field('societe_ville');
					$data['line1'] = $this->User->field('societe_adress');
					$data['line2'] = $this->User->field('societe_adress2');
					$data['postal_code'] = $cpt_postalcode;
					$data['name'] = $this->User->field('societe');
				}

				$data['vat_id'] = $this->User->field('vat_num');
				$data['tax_id'] = $this->User->field('siret');
        if(!$data['tax_id']) $data['tax_id'] = 11111;
				$data['date'] = time();
				if($row_ip['IP'])$data['ip'] = $row_ip['IP']; else $data['ip'] = '90.76.78.149';

				$data['country'] = $cpt_country;
				$data['country_company'] = $iso_country_societe;
				if($this->User->field('iban') && $take_iban)$data['account_number'] = str_replace(' ','',$this->User->field('iban'));

				$data['person_country'] = $iso_country;
				$data['person_line1'] = $this->User->field('address');
				$data['person_postal_code'] = $this->User->field('postalcode');
				$data['person_city'] = $this->User->field('city');
				$data['person_email'] = $this->User->field('email');
				$data['person_first_name'] = $this->User->field('firstname');
				$data['person_last_name'] = $this->User->field('lastname');
				$data['day'] = $dob[2];
				$data['month'] = $dob[1];
				$data['year'] = $dob[0];

				try {
					if($data['account_number']){
						$acct = \Stripe\Account::create([
							'business_type' => 'company',
							'country' => $data['country'],
							'type' => 'custom',
							'default_currency' => 'EUR',
							'email' => $data['person_email'],
							'requested_capabilities' => [
								'transfers',
							  ],
							'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],
								'name' => $data['name'],
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],
							'tos_acceptance' => [
								'date' => $data['date'],
								'ip' => $data['ip'],
							],
							'external_account' => [
								'object' => 'bank_account',
								'country' => $data['country'],
								'currency' => 'EUR',
								'account_number' => $data['account_number'],
							],
							'settings' => [
								'payouts' => [
									'schedule' => [
										'interval' => 'manual',
									],
								],
							],

						]);
					}else{
						$acct = \Stripe\Account::create([
							'business_type' => 'company',
							'country' => $data['country'],
							'type' => 'custom',
							'default_currency' => 'EUR',
							'email' => $data['person_email'],
							'requested_capabilities' => [
								'transfers',
							  ],
							'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],
								'name' => $data['name'],
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],
							'tos_acceptance' => [
								'date' => $data['date'],
								'ip' => $data['ip'],
							],
							'settings' => [
								'payouts' => [
									'schedule' => [
										'interval' => 'manual',
									],
								],
							],

						]);
					}
					\Stripe\Account::createPerson(
					  $acct->id,
					  [
						'address' => [
								'city' => $data['person_city'],
								'country' => $data['person_country'],
								'line1' => $data['person_line1'],
								'line2' => '',
								'postal_code' => $data['person_postal_code'],
								'state' => '',
							],
						'email' => $data['person_email'],
						'first_name' => $data['person_first_name'],
						'last_name' => $data['person_last_name'],
						'dob'=> [
								'day' => $data['day'],
								'month' => $data['month'],
								'year' => $data['year'],
						],
						 'relationship' => [
								 'owner' => true,
								 'director' => true,
							 	 'representative' => true,
								 'percent_ownership' => 100,
							],
					  ]
					);
					\Stripe\Account::update(
					  $acct->id,
					  [
						'company' => [
							'directors_provided' => true
						],
					  ]
					);

					//integre le num
					$this->User->saveField('stripe_account', $acct->id);
				}
				catch (Exception $e) {
					 $this->Session->setFlash($e->getMessage(), 'flash_warning');
					return false;
				}

			}
			$mysqli->close();
			return true;
		}
	}


	public function updateStripeAccount($id = null)
	{

		if($id){


			App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

			$this->User->id = $id;
			//On récupère le role de l'user

			$dob = explode('-',$this->User->field('birthdate'));
			$cpt_country = '';
			$bank_country = '';
			switch ($this->User->field('country_id')) {
				case 1:
					$cpt_country = 'FR';//France
					break;
				case 2:
					$cpt_country = 'BE';//Belgique
					break;
				case 3:
					$cpt_country = 'CH';//Suisse
					break;
				case 4:
					$cpt_country = 'LU';//Luxembourg
					break;
				case 60:
					$cpt_country = 'ES';//Espagne
					break;
				case 145:
					$cpt_country = 'PT';//Portugal
					break;
			}

			if(!$cpt_country){
				switch (strtolower($this->User->field('bank_country'))) {
					case 'france':
						$cpt_country = 'FR';//France
						break;
					case 'belgique':
						$cpt_country = 'BE';//Belgique
						break;
					case 'suisse':
						$cpt_country = 'CH';//Suisse
						break;
					case 'luxembourg':
						$cpt_country = 'LU';//Luxembourg
						break;
					case 'espagne':
						$cpt_country = 'ES';//Espagne
						break;
					case 'portugal':
						$cpt_country = 'PT';//Portugal
						break;
					case 'bulgarie':
						$cpt_country = 'BG';//Portugal
						break;
				}
			}
			switch (strtolower($this->User->field('bank_country'))) {
								case 'allemagne':
									$bank_country = 'DE';//Allemagne
									break;
								case 'france':
									$bank_country = 'FR';//France
									break;
								case 'belgique':
									$bank_country = 'BE';//Belgique
									break;
								case 'suisse':
									$bank_country = 'CH';//Suisse
									break;
								case 'luxembourg':
									$bank_country = 'LU';//Luxembourg
									break;
								case 'espagne':
									$bank_country = 'ES';//Espagne
									break;
								case 'portugal':
									$bank_country = 'PT';//Portugal
									break;
								case 'bulgarie':
									$bank_country = 'BG';//Portugal
									break;
							}

			switch ($this->User->field('country_id')) {
				case 1:
					$iso_country = 'FR';//France
					break;
				case 2:
					$iso_country = 'BE';//Belgique
					break;
				case 3:
					$iso_country = 'CH';//Suisse
					break;
				case 4:
					$iso_country = 'LU';//Luxembourg
					break;
				case 5:
					$iso_country = 'CA';//Canada
					break;
				case 60:
					$iso_country = 'ES';//Espagne
					break;
				/*case 120:
					$iso_country = 'MA';//Morocco
					break;*/
				case 145:
					$iso_country = 'PT';//Portugal
					break;
				/*case 157:
					$iso_country = 'SN';//Senegal
					break;
				case 180:
					$iso_country = 'TN';//Tunisia
					break;*/
			}

			$iso_country_societe = '';
			switch (strtolower($this->User->field('societe_pays'))) {
				case 1:
					$iso_country_societe = 'FR';//France
					break;
				case 2:
					$iso_country_societe = 'BE';//Belgique
					break;
				case 3:
					$iso_country_societe = 'CH';//Suisse
					break;
				case 4:
					$iso_country_societe = 'LU';//Luxembourg
					break;
				case 60:
					$iso_country_societe = 'ES';//Espagne
					break;
				case 145:
					$iso_country_societe = 'PT';//Portugal
					break;
				case 31:
					$iso_country_societe = 'BG';//Bulgarie
					break;
			}
			if(!$iso_country_societe) $iso_country_societe = $cpt_country;

			if($cpt_country && $this->User->field('stripe_account')){
        $postalcode = $this->User->field('postalcode');
        $postalcode_s = $this->User->field('societe_cp');
				if($cpt_country == 'FR' && strlen($postalcode < 5))$postalcode = '33000';
        if($iso_country_societe == 'FR' && strlen($postalcode_s < 5))$postalcode_s = '33000';
				$data = array();
				if($postalcode >= 98000){
					//update pas les infos address
				}else{
					if(!$this->User->field('societe')){
						$data['city'] = $this->User->field('city');
						$data['line1'] = $this->User->field('address');
						$data['line2'] = '';
						$data['postal_code'] = $postalcode;
						$data['name'] = $this->User->field('firstname').' '.$this->User->field('lastname');
					}else{
						$data['city'] = $this->User->field('societe_ville');
						$data['line1'] = $this->User->field('societe_adress');
						$data['line2'] = $this->User->field('societe_adress2');
						$data['postal_code'] = $postalcode_s;
						$data['name'] = $this->User->field('societe');
					}
					if(!$this->User->field('societe_adress')){
						$data['city'] = $this->User->field('city');
						$data['line1'] = $this->User->field('address');
						$data['line2'] = '';
						$data['postal_code'] = $postalcode;
					}
				}

				$data['vat_id'] = $this->User->field('vat_num');
				//$data['tax_id'] = $this->User->field('siret');
				$data['date'] = time();
				if($row_ip['IP'])$data['ip'] = $row_ip['IP']; else $data['ip'] = '90.76.78.149';

				$data['country'] = $cpt_country;
				$data['country_company'] = $iso_country_societe;
				if($this->User->field('iban'))$data['account_number'] = str_replace(' ','',$this->User->field('iban'));
				
				if(!$bank_country)$bank_country = $cpt_country;

				if($this->User->field('bank_country')){
					$country_auth = array('france','belgique','suisse','luxembourg','espagne','portugal','bulgarie','allemagne','italie');

				   if( !in_array(strtolower($this->User->field('bank_country')),$country_auth))$data['account_number'] = '';
				}

				$data['person_country'] = $iso_country;
				$data['person_line1'] = $this->User->field('address');
				$data['person_postal_code'] = $this->User->field('postalcode');
				$data['person_city'] = $this->User->field('city');
				$data['person_email'] = $this->User->field('email');
				$data['person_first_name'] = $this->User->field('firstname');
				$data['person_last_name'] = $this->User->field('lastname');
				$data['day'] = $dob[2];
				$data['month'] = $dob[1];
				$data['year'] = $dob[0];

				try {
					if($data['account_number']){

						\Stripe\Account::update(
						  $this->User->field('stripe_account'),
						  [
							  'email' => $data['person_email'],


							  'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],
							  'external_account' => [
								'object' => 'bank_account',
								'country' => $bank_country,
								'currency' => 'EUR',
								'account_number' => $data['account_number'],
							],
						  ]
						);


					}else{
						\Stripe\Account::update(
						  $this->User->field('stripe_account'),
						  [
							  'email' => $data['person_email'],


							  'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],

						  ]
						);
					}
				}
				catch (Exception $e) {
					 $this->Session->setFlash($e->getMessage(), 'flash_warning');
					return false;
				}

			}
			return true;
		}
	}

	public function admin_send_survey($id){
		$this->layout = false;
        $this->autoRender = false;

		if($id){
			$this->loadModel('User');
			$this->loadModel('Survey');
			$this->loadModel('Domain');

			$agent = $this->User->find('first',array('conditions' => array('User.id' => $id)));

			if($agent){

				$date_add = date('Y-m-d H:i:s');
				$date_block = date('Y-m-d 23:59:59', strtotime('+7 days'));
				$hash =  $this->crypter($agent['User']['email'].$date_add);

				$this->Survey->create();
				$saveData = array();
				$saveData['Survey'] = array();
				$saveData['Survey']['user_id'] = $agent['User']['id'];
				$saveData['Survey']['email'] = $agent['User']['email'];
				$saveData['Survey']['date_add'] = $date_add;
				$saveData['Survey']['hash'] = $hash;
				$saveData['Survey']['date_block'] = $date_block;

				$this->Survey->save($saveData);

				$url = Router::url(array('controller' => 'agents', 'action' => 'survey-'.$hash,'admin' => false),false);
				$conditions = array(
								'Domain.id' => $agent['User']['domain_id'],
							);

				$domain = $this->Domain->find('first',array('conditions' => $conditions));
				if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'fr.spiriteo.com';

				$url = 'https://'.$domain['Domain']['domain'].$url;

				$is_send = $this->sendCmsTemplatePublic(440, (int)$agent['User']['lang_id'], $agent['User']['email'], array(
									'AGENT' =>$agent['User']['pseudo'],
									'URL' =>$url,
								));
				if($is_send){
					$this->Session->setFlash(__('Questionnaire envoyé.'), 'flash_success');
				}
				else{
					$this->Session->setFlash(__('Echec lors de l\'envoi.'), 'flash_warning');
				}
			}
		}
		$this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
	}

	public function subscription(){}
	
	public function survey(){
		$params = $this->request->params;
		$hash = '';
		if(is_array($params))
			$hash = $params['hash'];

		if (empty($hash))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));



		$this->loadModel('User');
		$this->loadModel('Survey');
		$this->loadModel('UserCountry');

		$conditions = array(
								'Survey.hash' => $hash,'Survey.date_block >=' => date('Y-m-d H:i:s'),'Survey.is_respons' => 0,'Survey.is_block' => 0
					);

		$survey = $this->Survey->find('first',array('conditions' => $conditions));

		if(!$survey){

			$conditions2 = array(
								'Survey.hash' => $hash
					);

			$survey2 = $this->Survey->find('first',array('conditions' => $conditions2));

			if($survey2 && !$survey2['Survey']['is_block']){
				$this->Survey->id = $survey2['Survey']['id'];
				$this->Survey->saveField('is_block', 1);
			}
			$this->Session->setFlash(__('Votre questionnaire n\'est plus disponible.'), 'flash_warning');
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		}

		$agent = $this->User->find('first',array('conditions' => array('User.id' => $survey['Survey']['user_id'])));

		 $cc_infos = $this->UserCountry->find('first',array(
						'fields' => array('CountryLang.name'),
						'conditions' => array('UserCountry.id' => $agent['User']['country_id']),
						'joins' => array(
							array('table' => 'user_country_langs',
								  'alias' => 'UserCountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'UserCountryLang.user_countries_id = UserCountry.id',
									  'UserCountryLang.lang_id = 1'
								  )
							),
							array('table' => 'country_langs',
								  'alias' => 'CountryLang',
								  'type' => 'left',
								  'conditions' => array(
									  'CountryLang.name = UserCountryLang.name',
								  )
							)
						),
						'recursive' => -1,
					));
		$agent['User']['country'] = $cc_infos['CountryLang']['name'];

		if(!$survey['Survey']['is_view']){
			$this->Survey->id = $survey['Survey']['id'];
			$this->Survey->saveField('is_view', 1);
			$this->Survey->saveField('date_view', date('Y-m-d H:i:s'));
		}

		$this->set(compact('agent','survey'));

	}

	public function survey_agent(){
		if($this->request->is('post')){
            $requestData = $this->request->data;
			
			//bug empty fields !!
			$first_question_value = trim($requestData['Agents']['lastname']);
			
			if(!$first_question_value){
				$this->Session->setFlash(__('Merci de remplir tous les champs.'), 'flash_warning');
				$this->redirect( Router::url( $this->referer(), true ) );
			}else{

				$this->loadModel('User');
				$this->loadModel('Survey');
				$this->loadModel('SurveyQuestion');
				$this->loadModel('SurveyAnswer');

				//load survey
				$conditions = array(
									'Survey.id' => $requestData['Agents']['survey']
						);

				$survey = $this->Survey->find('first',array('conditions' => $conditions));


				if(!$survey){
					$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
					$this->redirect(array('controller' => 'home', 'action' => 'index'));
				}

				//save answers
				$conditions = array(
									'SurveyQuestion.active' =>1
						);

				$questions = $this->SurveyQuestion->find('all',array('conditions' => $conditions, 'order' => 'SurveyQuestion.id asc'));

				$intervalle = array(
									0 => 'Lundi',
									1 => 'Mardi',
									2 => 'Mercredi',
									3 => 'Jeudi',
									4 => 'Vendredi',
									5 => 'Samedi',
									6 => 'Dimanche'
								);

				foreach($questions as $question){


					if($question['SurveyQuestion']['question'] == 'planning'){
						$planning = '';
						foreach($requestData['Agents'] as $lab => $val){
							if(substr_count($lab,'planning') && $val == 1){
								$tab = explode('-',$lab);
								$planning .= $intervalle[$tab[1]]. ' à '.$tab[2].':'.$tab[3].'#';
							}
						}
						$requestData['Agents']['planning'] = $planning;
					}

					$this->SurveyAnswer->create();
					$saveData = array();
					$saveData['SurveyAnswer'] = array();
					$saveData['SurveyAnswer']['survey_id'] = $survey['Survey']['id'];
					$saveData['SurveyAnswer']['question_id'] = $question['SurveyQuestion']['id'];
					$saveData['SurveyAnswer']['answer'] = $requestData['Agents'][$question['SurveyQuestion']['question']];
					$this->SurveyAnswer->save($saveData);
				}
				//save validation
				$this->Survey->id = $survey['Survey']['id'];
				$this->Survey->saveField('is_respons', 1);
				$this->Survey->saveField('IP', getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR'));
				$this->Survey->saveField('date_valid', date('Y-m-d H:i:s'));
				
				//send email to support level 4
				$this->loadModel('SupportAdmin');
				$admins = $this->SupportAdmin->find('all',array(
									'fields' => array('User.email'),
								'conditions' => array('service_id' => 1,'level' => 4),
									'joins' => array(
												array('table' => 'users',
													  'alias' => 'User',
													  'type' => 'left',
													  'conditions' => array('User.id = SupportAdmin.user_id')
												)
											),
									'recursive' => -1,
								));
				foreach($admins as $adm){
					$this->sendCmsTemplateByMail(470, 1, $adm['User']['email'], array());
				}


				$this->Session->setFlash(__('Votre questionnaire a été soumis.'), 'flash_success');
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			}
		}else{
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		}
	}
	
	
	public function tips(){
	    
	    }

	public function admin_survey_list()
	{



		  $firstConditions = array();

		 if($this->request->data['Agent']['agent']){
                $firstConditions = array_merge($firstConditions,
											   array(
											   'OR' => array(
													array('Agent.pseudo like' => '%'.$this->request->data['Agent']['agent'].'%'),
													array('Agent.firstname like' => '%'.$this->request->data['Agent']['agent'].'%'),
															   array('Agent.lastname like' => '%'.$this->request->data['Agent']['agent'].'%'),
													)
													)
											  );
            }

        if($this->request->data['filter_lu']['filter_lu']){

            if(isset($this->request->data['filter_lu']['filter_lu']) && !empty($this->request->data['filter_lu']['filter_lu'])){
                $valueStatus=$this->request->data['filter_lu']['filter_lu'] == 2 ? 0 : 1 ;
                $firstConditions = array_merge($firstConditions, array('Survey.is_view =' =>$valueStatus ));
            }
        }


        if($this->request->data['filter_traiter']['filter_traiter']){

            if(isset($this->request->data['filter_traiter']['filter_traiter']) && !empty($this->request->data['filter_traiter']['filter_traiter'])){
                $valueStatus=$this->request->data['filter_traiter']['filter_traiter'] == 2 ? 0 : 1 ;
                $firstConditions = array_merge($firstConditions, array('Survey.status =' =>$valueStatus ));
            }
        }

		$this->loadModel('Survey');
		$this->Paginator->settings = array(
				'fields' => array('Survey.*','Agent.*'),
				'conditions' => $firstConditions,
                'order' => array('Survey.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins'      => array(
							array(
								'table' => 'users',
								'alias' => 'Agent',
								'type'  => 'inner',
								'conditions' => array(
									'Agent.id = Survey.user_id'
								)
							)
						),
                'limit' => 25
            );

        $surveys = $this->Paginator->paginate($this->Survey);

        $this->set(compact('surveys'));
	}

	public function admin_survey_view($id)
	{
		$this->loadModel('Survey');
		$this->loadModel('SurveyAnswer');
		$this->loadModel('SurveyQuestion');
		//load survey
		$conditions = array(
								'Survey.id' => $id
					);

		$survey = $this->Survey->find('first',array('conditions' => $conditions));

		$conditions_user = array(
								'User.id' => $survey['Survey']['user_id']
					);

		$user = $this->User->find('first',array('conditions' => $conditions_user));


		if(!$survey){
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
		}

		$conditions = array(
								'SurveyQuestion.active' =>1
					);

		$survey_questions = $this->SurveyQuestion->find('all',array('conditions' => $conditions, 'order' => 'SurveyQuestion.position asc'));

		$conditions = array(
								'SurveyAnswer.survey_id' =>$survey['Survey']['id']
					);

		$survey_answers = $this->SurveyAnswer->find('all',array('conditions' => $conditions, 'order' => 'SurveyAnswer.id asc'));


		$this->set(compact('survey','survey_questions', 'survey_answers','user'));
	}

	public function admin_survey_done($id)
	{
		$this->loadModel('Survey');
		//load survey
		$conditions = array(
								'Survey.id' => $id
					);

		$survey = $this->Survey->find('first',array('conditions' => $conditions));


		if(!$survey){
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
		}

		$this->Survey->id = $survey['Survey']['id'];
		$this->Survey->saveField('status', 1);

		$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
	}

	public function admin_send_demand_passport($id){
		$this->layout = false;
        $this->autoRender = false;

		if($id){
			$this->loadModel('User');
			$this->loadModel('Survey');
			$this->loadModel('Domain');

			$agent = $this->User->find('first',array('conditions' => array('User.id' => $id)));

			if($agent){

				$is_send = $this->sendCmsTemplatePublic(445, (int)$agent['User']['lang_id'], $agent['User']['email'], array(
									'AGENT' =>$agent['User']['pseudo'],
								));
				if($is_send){
					$this->User->id = $id;
					$this->User->saveField('date_demand_doc', date('Y-m-d H:i:s'));


					$this->Session->setFlash(__('Demande envoyé.'), 'flash_success');
				}
				else{
					$this->Session->setFlash(__('Echec lors de l\'envoi.'), 'flash_warning');
				}
			}
		}
		$this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
	}

	public function admin_survey_modify($id){

		if($id){
			$this->loadModel('User');
			$this->loadModel('Survey');

			$this->loadModel('SurveyAnswer');
			$this->loadModel('SurveyQuestion');
			//load survey
			$conditions = array(
									'Survey.id' => $id
						);

			$survey = $this->Survey->find('first',array('conditions' => $conditions));


			if(!$survey){
				$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
				$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
			}

			$conditions = array(
									'SurveyQuestion.active' =>1
						);

			$survey_questions = $this->SurveyQuestion->find('all',array('conditions' => $conditions, 'order' => 'SurveyQuestion.id asc'));

			$conditions = array(
									'SurveyAnswer.survey_id' =>$survey['Survey']['id']
						);

			$survey_answers = $this->SurveyAnswer->find('all',array('conditions' => $conditions, 'order' => 'SurveyAnswer.id asc'));


			$this->set(compact('survey','survey_questions', 'survey_answers'));


		}

	}

	public function admin_survey_modify_agent(){


		if($this->request->is('post')){
            $requestData = $this->request->data;

			$this->loadModel('User');
			$this->loadModel('Survey');
			$this->loadModel('SurveyQuestion');
			$this->loadModel('SurveyAnswer');

			//load survey
			$conditions = array(
								'Survey.id' => $requestData['Agents']['survey']
					);

			$survey = $this->Survey->find('first',array('conditions' => $conditions));


			if(!$survey){
				$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			}

			//save answers
			$conditions = array(
								'SurveyQuestion.active' =>1
					);

			$questions = $this->SurveyQuestion->find('all',array('conditions' => $conditions, 'order' => 'SurveyQuestion.id asc'));

			$intervalle = array(
								0 => 'Lundi',
								1 => 'Mardi',
								2 => 'Mercredi',
								3 => 'Jeudi',
								4 => 'Vendredi',
								5 => 'Samedi',
								6 => 'Dimanche'
							);

			foreach($questions as $question){

				if($question['SurveyQuestion']['question'] != 'planning'){

					$conditions2 = array(
								'SurveyAnswer.survey_id' =>$survey['Survey']['id'],
								'SurveyAnswer.question_id' =>$question['SurveyQuestion']['id'],
					);

					$answer = $this->SurveyAnswer->find('first',array('conditions' => $conditions2));

						$this->SurveyAnswer->id = $answer['SurveyAnswer']['id'];
						$this->SurveyAnswer->saveField('answer', $requestData['Agents'][$question['SurveyQuestion']['question']]);
				}
			}

			$this->Session->setFlash(__('Votre questionnaire a été modifié.'), 'flash_success');
			$this->redirect(array('controller' => 'agents', 'action' => 'survey_view', 'admin' => true, 'id' => $survey['Survey']['id']),false);
		}else{
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
		}
	}

	public function admin_upload_documents(){

		if($this->request->is('post')){
            $requestData = $this->request->data;
			$this->loadModel('User');
			$this->loadModel('UserDocument');


			$conditions = array(
								'User.id' => $requestData['Agent']['user_id']
					);

			$agent = $this->User->find('first',array('conditions' => $conditions));


			if(!$agent){
				$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			}

			/* if($this->isUploadedFile($requestData['Agent']['document'])){
                //Est-ce un fichier image autorisé ??
                if(!Tools::formatFile($this->allowed_mime_types, $requestData['Agent']['document']['type'],'Document')){
                    $this->Session->setFlash(__('Le fichier n\'est pas un fichier accepté'), 'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']),false);
                }
            }
			//S'il y a eu une erreur dans l'upload du fichier
            elseif($requestData['Agent']['document']['error'] != 4){
                $this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']),false);
            }*/

			//virer caractere degue appel
			$requestData['Agent']['document']['name'] = str_replace('?','',utf8_decode($requestData['Agent']['document']['name']));
		    $requestData['Agent']['document']['name'] = str_replace(' ','',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace('è','e',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace('(','',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace(')','',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace("'",'',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace("à",'a',$requestData['Agent']['document']['name']);
		    $requestData['Agent']['document']['name'] = str_replace("-",'',$requestData['Agent']['document']['name']);
			$requestData['Agent']['document']['name'] = str_replace("-",'',$requestData['Agent']['document']['name']);
			$requestData['Agent']['document']['name'] = str_replace("’",'',$requestData['Agent']['document']['name']);
			$requestData['Agent']['document']['name'] = str_replace("é",'e',$requestData['Agent']['document']['name']);
			$requestData['Agent']['document']['name'] = str_replace("à",'a',$requestData['Agent']['document']['name']);

			$folder = new Folder(Configure::read('Site.pathDocumentAdmin').DS.$agent['User']['id'],true,0755);
        	if(!is_dir($folder->path)){
				$this->Session->setFlash(__('Erreur dans la creation du repertoire de votre fichier.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']),false);
			}
			$link = $folder->path;
			$file = new File($requestData['Agent']['document']['tmp_name']);
			if(!$file->copy($link.DS.$requestData['Agent']['document']['name'])){
				$this->Session->setFlash(__('Erreur dans l\'enregistrement de votre fichier.'),'flash_warning');
                $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']),false);
			}


			//save document database
			$this->UserDocument->create();
			$saveData = array();
			$saveData['UserDocument'] = array();
			$saveData['UserDocument']['user_id'] = $agent['User']['id'];
			$saveData['UserDocument']['date_add'] = date('Y-m-d H:i:s');
			$saveData['UserDocument']['name'] = $requestData['Agent']['document']['name'];
			$this->UserDocument->save($saveData);
			$this->Session->setFlash(__('Fichier enregistré.'),'flash_success');
			$this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']),false);
		}
	}

	public function admin_survey_modify_not_view($id)
	{
		$this->loadModel('Survey');
		//load survey
		$conditions = array(
								'Survey.id' => $id
					);

		$survey = $this->Survey->find('first',array('conditions' => $conditions));


		if(!$survey){
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));
		}

		$this->Survey->id = $survey['Survey']['id'];
		$this->Survey->saveField('is_view', 0);
		$this->Survey->saveField('date_view', NULL);

		$this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin'=> true));

	}

	public function admin_delete_documents($id){


		$this->loadModel('UserDocument');
		//load survey
		$conditions = array(
								'UserDocument.id' => $id
					);

		$doc = $this->UserDocument->find('first',array('conditions' => $conditions));


		if(!$doc){
			$this->Session->setFlash(__('Erreur technique.'), 'flash_warning');
			$this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin'=> true));
		}

		$link = Configure::read('Site.pathDocumentAdmin').DS.$doc['UserDocument']['user_id'];
		if(!unlink($link.DS.$doc['UserDocument']['name'])){
			$this->Session->setFlash(__('Erreur lors de la suppression de votre fichier.'),'flash_warning');
		}else{
			$this->UserDocument->id = $doc['UserDocument']['id'];
			$this->UserDocument->saveField('active', 0);
			$this->Session->setFlash(__('Fichier supprimé.'),'flash_success');
		}



		$this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $doc['UserDocument']['user_id']),false);

	}

	public function admin_vatObservation(){
		 if($this->request->is('ajax')){
			  if(isset($this->request->data['id']) && isset($this->request->data['txt'])){
				  $this->User->id = $this->request->data['id'];
				  $this->User->saveField('vat_num_status_reason_obs', $this->request->data['txt']);
			  }
			 $this->jsonRender(array(
                'return'          => true,
            ));
		 }
	}

	public function admin_export_facturation(){
        set_time_limit ( 0 );

		//Charge model
        $this->loadModel('InvoiceAgent');
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		$listing_utcdec = Configure::read('Site.utcDec');

		$conditions = array();
        //Filtre par date ??
        if($this->Session->check('Date')){

			/*$utc_dec = 1;
			$cut = explode('-',$this->Session->read('Date.start') );
			$mois_comp = $cut[1];
			if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/

			$dmax = new DateTime($this->Session->read('Date.end').' 23:59:59');
			$dmin = new DateTime($this->Session->read('Date.start'). '00:00:00');
			$date_export_min = $dmin->format('Y-m-d H:i:s');
			$date_export_max = $dmax->format('Y-m-d H:i:s');
			$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
			$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
			$date_min =  $dmin->format('Y-m-d H:i:s');
			$date_max =  $dmax->format('Y-m-d H:i:s');
			$annee_min =  $dmin->format('Y');
			$annee_max =  $dmax->format('Y');
			$mois_min =  $dmin->format('m');
			$mois_max =  $dmax->format('m');

            $conditions = array_merge($conditions, array(
                'InvoiceAgent.date_add >=' => $date_min,
               // 'InvoiceAgent.date_max <=' => $date_max
            ));
        }


		$allComDatas = $this->InvoiceAgent->find('all',array(
					'fields'        => array('InvoiceAgent.*', 'User.*', 'CountrySociety.name'),
					'conditions' => $conditions,
					'joins' => array(
						array('table' => 'users',
							'alias' => 'User',
							'type' => 'left',
							'conditions' => array(
								'User.id = InvoiceAgent.user_id'
							)
						),
						array(
									'table' => 'user_country_langs',
									'alias' => 'CountrySociety',
									'type'  => 'left',
									'conditions' => array(
										'Country.user_countries_id = User.societe_pays',
										'Country.lang_id = 1'
									)
								)
					),
					'recursive' => -1
				));

		if(empty($allComDatas)){
			//check en manuel
			$allComDatas = array();

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);
			$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' order by lastname");
			$indice = 0;
			while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){

				$allComDatas[$indice] = array();
				$allComDatas[$indice]['InvoiceAgent'] = array();
				$allComDatas[$indice]['User'] = array();

				$sql_date_min = $date_min;
				$sql_date_max = $date_max;

				$total = 0;
					$total_gain = 0;
					$total_prime = 0;
					$total_sponsor = 0;
					$total_penality = 0;
					$ca = 0;
					$is_sold = 1;
					$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start, C.is_sold , P.price, P.order_cat_index, P.mail_price_index, C.ca from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$rowagent['id']}' and C.date_start >= '{$sql_date_min}' and C.date_start <= '{$sql_date_max}' and P.id_user_credit_history = C.user_credit_history");
					while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
						$price = number_format($row4['price'], 2, ".", "");
						$total_gain += $price;
						if(!$row4['is_sold'])$is_sold = 0;
						$ca += number_format($row4['ca'], 2, ".", "");
					}

					$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= {$mois_max} AND mois <= {$mois_max} ) and paid = 1 order by id ASC");

					$month_done = array();
					while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
						/*$bonus_montant = 0;
						$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
						$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
						$bonus_montant += $rowbonus['amount'];
						if($bonus_montant && !in_array($rowbonusagent['mois'],$month_done)){
							$total_prime += $bonus_montant;
							array_push($month_done, $rowbonusagent['mois']);
						}*/
						$total_prime += $rowbonusagent['paid_amount'];
					}


					$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4 order by id asc");
					while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
						$bonus_montant = 0;
						$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$sql_date_min}' and date_start <= '{$sql_date_max}'");
						$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
						$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];

						if($bonus_montant){
							$total_sponsor += $bonus_montant;
						}
					}

					$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$rowagent['id']}' and date_ecriture >= '".$sql_date_min."' and date_ecriture <= '".$sql_date_max."' order by id ASC");

					while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){

						$total_gain += number_format($rowfacturation['amount'],2);
					}

					$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$rowagent['id']}' and date_add >= '".$sql_date_min."' and date_add <= '".$sql_date_max."' and is_factured = 1 order by id ASC");

					while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
						if($rowpenality['is_factured']){
							$total -= $rowpenality['penality_cost'];
							$total_penality += $rowpenality['penality_cost'];

							if($rowpenality['message_id']){
								$total -= 12;
								$total_penality += 12;
							}
						}
					}

					$total = number_format($total_gain + $total_prime + $total_sponsor - $total_penality,2, ".", "");

				//remplir tableau agent
				if($ca > 0){
					$allComDatas[$indice]['InvoiceAgent']['status'] = 0;
					$allComDatas[$indice]['InvoiceAgent']['paid_total_valid'] = $paid_total_valid;
					$allComDatas[$indice]['InvoiceAgent']['paid_total'] = $total;
					$allComDatas[$indice]['User']['firstname'] = $rowagent['firstname'];
					$allComDatas[$indice]['User']['firstname'] = $rowagent['firstname'];
					$allComDatas[$indice]['User']['lastname'] = $rowagent['lastname'];
					$allComDatas[$indice]['User']['pseudo'] = $rowagent['pseudo'];
					$allComDatas[$indice]['User']['email'] = $rowagent['email'];
					$allComDatas[$indice]['User']['societe'] = $rowagent['societe'];
					$allComDatas[$indice]['User']['societe_adress'] = utf8_decode($rowagent['societe_adress']);
					$allComDatas[$indice]['User']['societe_adress2'] = utf8_decode($rowagent['societe_adress2']);
					$allComDatas[$indice]['User']['societe_cp'] = $rowagent['societe_cp'];
					$allComDatas[$indice]['User']['societe_ville'] = utf8_decode($rowagent['societe_ville']);
					$allComDatas[$indice]['User']['societe_pays'] = utf8_decode($allComDatas[$indice]['CountrySociety']['name'] );
					$allComDatas[$indice]['User']['siret'] = $rowagent['siret'];
					$allComDatas[$indice]['User']['vat_num'] = $rowagent['vat_num'];
					$allComDatas[$indice]['User']['mode_paiement'] = $rowagent['mode_paiement'];
					$allComDatas[$indice]['InvoiceAgent']['ca'] = $ca;
					$allComDatas[$indice]['InvoiceAgent']['amount_total'] = $ca;
					$allComDatas[$indice]['User']['stripe_available'] = $rowagent['stripe_available'];
					$allComDatas[$indice]['User']['stripe_balance'] = $rowagent['stripe_balance'];

					$allComDatas[$indice]['User']['id'] = $rowagent['id'];
					$indice ++;
				}

			}
		}

		 //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'agents', 'action' => 'export_order', 'admin' => true), false);
            else
                $this->redirect($source);
        }

        //Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($date_export_min, '%d-%m-%Y').'_'.CakeTime::format($date_export_max, '%d-%m-%Y');
        else
            $label = 'all_export';


		$fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");


		 foreach($allComDatas as $indice => $row){

			 if($row['InvoiceAgent']['paid_total_valid'])
				 $paid_total = $row['InvoiceAgent']['paid_total_valid'];
			 else
				 $paid_total = $row['InvoiceAgent']['paid_total'];

			 if($row['InvoiceAgent']['status']>0 && $row['InvoiceAgent']['status']<2)
				 $etat = 'paye';
			 elseif($row['InvoiceAgent']['status'] == 10){
				 $etat = 'credit note';
			 }else
				 $etat = 'non paye';

			  $line = array(
				'id'    => $row['User']['id'],
                'firstname'    => $row['User']['firstname'],
                'lastname'     => $row['User']['lastname'],
				'pseudo'       => $row['User']['pseudo'],
				  'email'       => $row['User']['email'],
				 'societe'       => $row['User']['societe'],
				  'societe_adress'       => $row['User']['societe_adress'],
				  'societe_adress2'       => $row['User']['societe_adress2'],
				  'societe_cp'       => $row['User']['societe_cp'],
				  'societe_ville'       => $row['User']['societe_ville'],
				  'societe_pays'       => $row['User']['societe_pays'],
				  'siret'       => $row['User']['siret'],
				  'vat_num'       => $row['User']['vat_num'],
				  'mode_paiement'       => $row['User']['mode_paiement'],
				  'date_min'       => $date_export_min,
				  'date_max'       => $date_export_max,
				  'ca'       => $row['InvoiceAgent']['ca'],
				  'paid_total'       => $paid_total,
				  'amount_fees'       => $row['InvoiceAgent']['amount_total'],
				  'stripe_available'       => $row['User']['stripe_available'],
				  'stripe_total'       => $row['User']['stripe_balance'],
				  'status'       => $etat

            );

            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
			if($row['User']['id'] && $row['InvoiceAgent']['ca'] > 0)
           fputcsv($fp, array_values($line), ';', '"');
		 }
		 fclose($fp);
		$this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
	}

	public function admin_export_comptabilite(){
        set_time_limit ( 0 );

		//Charge model
        $this->loadModel('InvoiceAgent');
		$this->loadModel('UserCountry');
		$this->loadModel('InvoiceVoucherAgent');

        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		$listing_utcdec = Configure::read('Site.utcDec');

		$conditions = array();
        //Filtre par date ??
        if($this->Session->check('Date')){

			/*$utc_dec = 1;
			$cut = explode('-',$this->Session->read('Date.start') );
			$mois_comp = $cut[1];
			if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/

			$dmax = new DateTime($this->Session->read('Date.end').' 23:59:59');
			$dmin = new DateTime($this->Session->read('Date.start'). '00:00:00');
			$date_export_min = $dmin->format('Y-m-d H:i:s');
			$date_export_max = $dmax->format('Y-m-d H:i:s');
			$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
			$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
			$date_min =  $dmin->format('Y-m-d H:i:s');
			$date_max =  $dmax->format('Y-m-d H:i:s');
			$annee_min =  $dmin->format('Y');
			$annee_max =  $dmax->format('Y');
			$mois_min =  $dmin->format('m');
			$mois_max =  $dmax->format('m');

            $conditions = array_merge($conditions, array(
                //'InvoiceAgent.date_min >=' => $date_min,
                'InvoiceAgent.date_max' => $date_max
            ));
        }


		$allComDatas = $this->InvoiceAgent->find('all',array(
					'fields'        => array('InvoiceAgent.*', 'User.*'),
					'conditions' => $conditions,
					'joins' => array(
						array('table' => 'users',
							'alias' => 'User',
							'type' => 'left',
							'conditions' => array(
								'User.id = InvoiceAgent.user_id'
							)
						)
					),
					'recursive' => -1
				));
		 //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'agents', 'action' => 'export_order', 'admin' => true), false);
            else
                $this->redirect($source);
        }

        //Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($date_export_min, '%d-%m-%Y').'_'.CakeTime::format($date_export_max, '%d-%m-%Y');
        else
            $label = 'all_export';


		$fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");


		 foreach($allComDatas as $indice => $row){

			 $status_vat = '';
			 if($row['User']['vat_num_status'] == 'valide' || $row['User']['vat_num_proof'] )$status_vat = 'Valid';
			 if($row['User']['vat_num_status'] == 'invalide' && !$row['User']['vat_num_proof'])$status_vat = 'Invalid';
			 if($status_vat == 'Valid' && $row['User']['societe_pays'] == 180)$status_vat = '';
			 if($status_vat == 'Valid' && !$row['User']['societe_pays'] && $row['User']['country_id'] == 180)$status_vat = '';
			 if($status_vat == 'Valid' && $row['User']['societe_pays'] == 3)$status_vat = '';
			 if($status_vat == 'Valid' && !$row['User']['societe_pays'] && $row['User']['country_id'] == 3)$status_vat = '';
			 
			 //check avoir
			 $voucher = $this->InvoiceVoucherAgent->find('first',array(
					'conditions' => array('InvoiceVoucherAgent.invoice_id' => $row['InvoiceAgent']['id']),
					'recursive' => -1
				));

			 if($voucher){
				 $etat = 'Credit Note Generated';
			 }else{
				 if($row['InvoiceAgent']['status'] && $row['InvoiceAgent']['status'] < 2)
					 $etat = 'Payed';
				 else
					 $etat = 'Unpayed';
			 }



			 if(!$row['User']['societe_adress']){
				 $row['User']['societe_adress'] = $row['User']['address'];
			 }
			 if(!$row['User']['societe_cp']){
				 $row['User']['societe_cp'] = $row['User']['postalcode'];
			 }
			 if(!$row['User']['societe_ville']){
				 $row['User']['societe_ville'] = $row['User']['city'];
			 }
			  if(!$row['User']['societe_pays']){
				 $country = $this->UserCountry->UserCountryLang->find("first", array(
					'fields'     => 'UserCountries.id,UserCountryLang.name',
					'conditions' => array(
						'UserCountries.active'      =>  1,
						'UserCountries.id'            =>  $row['User']['country_id'],
						'UserCountryLang.lang_id'   =>  1
					)
				));
				 $row['User']['societe_pays'] = $country['UserCountryLang']['name'];
			 }else{
				   $country = $this->UserCountry->UserCountryLang->find("first", array(
					'fields'     => 'UserCountries.id,UserCountryLang.name',
					'conditions' => array(
						'UserCountries.active'      =>  1,
						'UserCountries.id'            =>  $row['User']['societe_pays'],
						'UserCountryLang.lang_id'   =>  1
					)
				));
				   $row['User']['societe_pays'] = $country['UserCountryLang']['name'];
			  }


			  $line = array(
				  'id'    => $row['User']['id'],
				  'lastname'     => $row['User']['lastname'],
				  'firstname'    => $row['User']['firstname'],
				  'societe'       => $row['User']['societe'],
				  'societe_adress'       => $row['User']['societe_adress'],
				  'societe_adress2'       => $row['User']['societe_adress2'],
				  'societe_cp'       => $row['User']['societe_cp'],
				  'societe_ville'       => $row['User']['societe_ville'],
				  'societe_pays'       => $row['User']['societe_pays'],
				  'siret'       => $row['User']['siret'],
				  'vat_num'       => $row['User']['vat_num'],
				  'date_invoice'       => $row['InvoiceAgent']['date_add'],
				  'num_invoice'       => $row['InvoiceAgent']['order_id'],
				  'total_HT'       => str_replace('.',',',$row['InvoiceAgent']['amount']),
				  'VAT_TX'       => str_replace('.',',',$row['InvoiceAgent']['vat_tx']),
				  'TOTAL_VAT'       => str_replace('.',',',$row['InvoiceAgent']['vat']),
				  'TOTAL_TTC'       => str_replace('.',',',$row['InvoiceAgent']['amount_total']),
				  'status_VAT'       => $status_vat,
				  'status'       => $etat,

            );

            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
			if($row['User']['firstname'])
           fputcsv($fp, array_values($line), ';', '"');
		 }
		 fclose($fp);
		$this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
	}

	public function vatnum(){

		if($this->request->is('post')){
            $requestData = $this->request->data;
			$desc = '';
			if(!empty($requestData["Agents"]["choice"])){
				switch ($requestData["Agents"]["choice"]) {
						case 0:
							$choice = 'Societe en france';
							$this->Session->setFlash(__('Merci de cocher une réponse concernant votre société en France.'), 'flash_warning');
							$this->redirect(array('controller' => 'agents', 'action' => 'vatnum'));
							break;
						case 1:
							$choice = 'Societe hors france';
							$this->Session->setFlash(__('Merci de cocher une réponse concernant votre société hors France.'), 'flash_warning');
							$this->redirect(array('controller' => 'agents', 'action' => 'vatnum'));
							break;
						case 2:
							$choice = 'Societe en france, demande deja faite';
							break;
						case 3:
							$choice = 'Societe en france, demande pas faite';
							$desc = $requestData["Agents"]["desc"];
							break;
						case 4:
							$choice = 'Societe en france, rappel Nicolas';
							break;
						case 5:
							$choice = 'Societe hors france, demande deja faite';
							break;
						case 6:
							$choice = 'Societe hors france, demande pas faite';
							$desc = $requestData["Agents"]["desc2"];
							break;
						case 7:
							$choice = 'Societe hors france, rappel Nicolas';
							break;
						case 8:
							$choice = 'Societe en france, demande faite et relancé';
							break;
						case 9:
							$choice = 'Societe hors france, demande faite et relancé';
							break;
						case 10:
							$choice = 'Societe en france, demande faite non relancé';
							break;
						case 11:
							$choice = 'Societe hors france, demande faite non relancé';
							break;
						case 12:
							$choice = 'Societe en france, demande faite non transmis';
							break;
						case 13:
							$choice = 'Societe hors france, demande faite non transmis';
							break;
						case 14:
							$choice = 'Societe en france, demande faite et transmis';
							break;
						case 15:
							$choice = 'Societe hors france, demande faite et transmis';
							break;
						case 16:
							$choice = 'Societe en france, demande faite et preuve transmis';
							break;
						case 17:
							$choice = 'Societe hors france, demande faite et preuve transmis';
							break;
				}
				$this->User->id = $this->Auth->user('id');
				$test = $this->User->saveField('vat_num_status_reason', $choice);
				if($desc)$this->User->saveField('vat_num_status_reason_desc', $desc);

				if($test){
					$this->Session->setFlash(__('Votre réponse a été enregistré.'),'flash_success');
					$this->redirect(array('controller' => 'agents', 'action' => 'profil'));
				}else{
					$this->Session->setFlash(__('Merci de resaisir une réponse suite à un problème technique.'), 'flash_warning');
				}

			}else{
				$this->Session->setFlash(__('Merci de cocher une réponse concernant votre société.'), 'flash_warning');
				$this->redirect(array('controller' => 'agents', 'action' => 'vatnum'));
			}

		}else{
			$this->Session->setFlash(__('Vous devez absolument répondre aux questions ci-contre afin de passer à une autre étape.'), 'flash_warning');
		}

	}

	public function admin_vat(){
		ini_set("memory_limit",-1);
		$conditions = array(
								'User.vat_num !=' =>NULL,
					'User.role' => 'agent',
					'User.deleted' => 0,
					'User.active' => 1
					);


		if(isset($this->request->data['Agents']['status'])){
			if($this->request->data['Agents']['status'] != 'empty'){
				if($this->request->data['Agents']['status'] == 'null'){
					$conditions = array_merge($conditions, array('User.vat_num_status ' => NULL));
				}else{
					$conditions = array_merge($conditions, array('User.vat_num_status' => $this->request->data['Agents']['status']));
				}
			}

		}

		$agents = $this->User->find('all',array(

				   'fields' => array('User.id', 'User.pseudo', 'User.vat_num','User.email',  'User.vat_num_status', 'User.vat_num_status_reason', 'User.vat_num_proof', 'User.vat_num_status_reason_desc', 'User.vat_num_status_reason_obs','User.firstname', 'User.lastname','User.country_id','User.societe_pays', 'Country.name', 'CountrySociety.name'),
					'conditions' => $conditions,
					'order' => 'User.id desc',
					'joins' => array(
								array(
									'table' => 'user_country_langs',
									'alias' => 'Country',
									'type'  => 'left',
									'conditions' => array(
										'Country.user_countries_id = User.country_id',
										'Country.lang_id = 1'
									)
								),
								array(
									'table' => 'user_country_langs',
									'alias' => 'CountrySociety',
									'type'  => 'left',
									'conditions' => array(
										'CountrySociety.user_countries_id = User.societe_pays',
										'CountrySociety.lang_id = 1'
									)
								)
							)
					)
				);

        $this->set(compact('agents'));
    }

	  public function admin_delete_vat_reason($id){
        //On récupère les infos de l'user
        $user = $this->User->find('first', array(
            'fields'        => array('User.role', 'User.agent_number'),
            'conditions'    => array('User.id' => $id, 'User.role' => 'agent'),
            'recursive'     => -1
        ));

        //Si l'agent existe
        if(!empty($user)){
            //On supprime l'agent
			$this->User->id = $id;
            if($this->User->saveField('vat_num_status_reason', NULL)){
               $this->User->saveField('vat_num_status_reason_desc', NULL);
                $this->Session->setFlash(__('Les infos de l\'expert ont été éffacé'));
            }
            else
                $this->Session->setFlash(__('Erreur lors de la demande.'));

            $this->redirect(array('controller' => 'agents', 'action' => 'vat', 'admin' => true), false);
        }else
            $this->redirect(array('controller' => 'agents', 'action' => 'vat', 'admin' => true), false);
    }

	public function admin_export_vat(){
		ini_set("memory_limit",-1);
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/tva_experts.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		$label = 'all_tva';

        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

		$dbb_r = new DATABASE_CONFIG();
		$dbb_s = $dbb_r->default;


		$line = array();
				$line['id'] = '';
				$line['prenom'] = '';
				$line['nom'] = '';
				$line['email'] = '';
				$line['pseudo'] = '';
				$line['country'] = '';
				$line['vat_num']= '';
				$line['vat_num_status']= '';
		$line['vat_num_status_reason']= '';
		$line['vat_num_status_reason_desc']= '';
		$line['vat_num_status_reason_obs']= '';
		$line['vat_num_preuve']= '';
		fputcsv($fp, array_keys($line), ';', '"');

		$conditions = array(
								'User.vat_num !=' =>NULL,
					'User.role' => 'agent',
					'User.deleted' => 0,
					'User.active' => 1
					);

		$agents = $this->User->find('all',array(

				   'fields' => array('User.id', 'User.pseudo','User.email', 'User.vat_num', 'User.vat_num_status', 'User.vat_num_status_reason', 'User.vat_num_status_reason_desc', 'User.vat_num_status_reason_obs','User.firstname', 'User.lastname','User.country_id','User.societe_pays', 'Country.name', 'Country.name', 'User.vat_num_proof','CountrySociety.name'),
					'conditions' => $conditions,
					'order' => 'User.id desc',
					'joins' => array(
								array(
									'table' => 'user_country_langs',
									'alias' => 'Country',
									'type'  => 'left',
									'conditions' => array(
										'Country.user_countries_id = User.country_id',
										'Country.lang_id = 1'
									)
								),
								array(
									'table' => 'user_country_langs',
									'alias' => 'CountrySociety',
									'type'  => 'left',
									'conditions' => array(
										'CountrySociety.user_countries_id = User.societe_pays',
										'CountrySociety.lang_id = 1'
									)
								)
							)
					)
				);

		foreach($agents as $rowagent){
			$reason = '';
			switch($rowagent['User']['vat_num_status_reason']){
	 case 'Societe en france, demande deja faite':
        $reason ="Ma societe est basée en France, Ma demande est deja effectuee aupres de mon SIE - Service des Impots des entreprises de ma region et j'attends mon numero de TVA intracommunautaire valide.";
        break;
    case 'Societe en france, demande pas faite':
        $reason ="Ma societe est basee en France, Non je n'ai pas encore fait ma demande";
        break;
	case 'Societe en france, rappel Nicolas':
        $reason ="Ma societe est basee en France, Demander un rappel telephonique de la part de Nicolas";
        break;
	case 'Societe en france, demande faite et relancé':
        $reason ="Ma demande est bien realisee aupres de mon centre des impots et j'ai relance celui-ci";
        break;
	case 'Societe en france, demande faite non relancé':
        $reason ="Ma societe est basee en France, Ma demande est bien realisee aupres de mon centre des impots et mais je n'ai pas encore relance celui-ci";
        break;
	case 'Societe en france, demande faite non transmis':
        $reason ="Ma societe est basee en France, Mon numero est desormais valide par mon centre des impots mais je dois vous envoyer le document attestant par Email";
        break;
	case 'Societe en france, demande faite et transmis':
        $reason ="Ma societe est basee en France, Mon numero est desormais valide par mon centre des impots et je vous ai deja envoye le document attestant par Email";
        break;
	case 'Societe en france, demande faite et preuve transmis':
        $reason ="Ma societe est basee en France, Mon numero de TVA intracommunautaire est bien valide, je vous en ai fournis la preuve par document";
        break;
	case 'Societe hors france, demande deja faite':
        $reason ="Ma societe n'est pas basee en France, Ma demande est deja effectuee aupres de mon Service des Impots des entreprises de ma region et j'attends mon numero de TVA intracommunautaire valide.";
        break;
	case 'Societe hors france, demande pas faite':
        $reason ="Ma societe n'est pas basee en France, Non je n\'ai pas encore fait ma demande";
        break;
	case 'Societe hors france, rappel Nicolas':
        $reason ="Ma societe n'est pas basee en France, Demander un rappel telephonique de la part de Nicolas";
        break;
	case 'Societe hors france, demande faite et relancé':
        $reason ="Ma societe n'est pas basee en France, Ma demande est bien realisee aupres de mon centre des impots et j'ai relance celui-ci";
        break;
	case 'Societe hors france, demande faite non relancé':
        $reason ="Ma demande est bien realisee aupres de mon centre des impots et mais je n'ai pas encore relance celui-ci";
        break;
	case 'Societe hors france, demande faite non transmis':
        $reason ="Ma societe n'est pas basee en France, Mon numero est desormais valide par mon centre des impots mais je dois vous envoyer le document attestant par Email";
        break;
	case 'Societe hors france, demande faite et transmis':
        $reason ="Ma societe n'est pas basee en France, Mon numero est desormais valide par mon centre des impots et je vous ai deja envoye le document attestant par Email";
        break;
	case 'Societe hors france, demande faite et preuve transmis':
        $reason ="Ma societe n'est pas basee en France, Mon numero de TVA intracommunautaire est bien valide, je vous en ai fournis la preuve par document";
        break;
}

			$proof = 'Non';
			if($rowagent['User']['vat_num_proof'])$proof = 'Oui';

				$country = $rowagent['CountrySociety']['name'];
				if(!$country)$country =$rowagent['Country']['name'];

					$line['id'] = $rowagent['User']['id'];
					$line['prenom'] = $rowagent['User']['firstname'];
					$line['nom'] = $rowagent['User']['lastname'];
					$line['email'] = $rowagent['User']['email'];
					$line['pseudo'] = $rowagent['User']['pseudo'];
					$line['country'] = $country;
					$line['vat_num'] = $rowagent['User']['vat_num'];
					$line['vat_num_status'] = $rowagent['User']['vat_num_status'];
				$line['vat_num_status_reason'] = $reason;//utf8_encode($reason);
				$line['vat_num_status_reason_desc'] = $rowagent['User']['vat_num_status_reason_desc'];
			$line['vat_num_status_reason_obs'] = $rowagent['User']['vat_num_status_reason_obs'];
			$line['vat_num_preuve'] = $proof;
					fputcsv($fp, array_values($line), ';', '"');
		}

        fclose($fp);

        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
    }

	public function admin_stripe_balance(){
        $this->loadModel('InvoiceAgent');

        //On récupère les datas pour la vue
		    $users = $this->_adminIndex('Agents',false,null,array('User.active' => 1), array('User.lastname' => 'asc'));

        //clean users
        $clean_users = array();
        foreach($users as $user){

          if($user['User']['active']){
            //get invoice

            $invoice_agent = $this->InvoiceAgent->find('first', array(
                    'conditions'    => array("InvoiceAgent.user_id" => $user['User']['id'], "InvoiceAgent.status =" =>0, "InvoiceAgent.payment_mode" =>'stripe'),
                    'recursive'     => -1,
                    'order'     => 'InvoiceAgent.date_add desc'
                  ));
            if($invoice_agent){
               $user['InvoiceAgent'] = $invoice_agent['InvoiceAgent'];
            }
            array_push($clean_users, $user);
          }else{
            if($user['User']['stripe_balance'] != 0 )
              array_push($clean_users, $user);
          }

        }

        $this->set(array('users' => $clean_users));
    }

	public function admin_stripe_sold_depos($agent_id){
		$this->layout = false;
        $this->autoRender = false;

		if(!$agent_id){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur technique'));
			$this->render('/Agents/admin_order_pop');
			return false;
		}

		$agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $agent_id),
					'recursive' => -1
				));


		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

		$stripe_balance = 0;
		if($agent['User']['stripe_account']){
			try {
				//$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
				$balance = \Stripe\Balance::retrieve(
				  ["stripe_account" => $agent['User']['stripe_account']]
				);
				if($balance->available && is_array($balance->available)){
					$available = $balance->available[0];
					$stripe_balance = $available->amount /100;
				}
			 } catch (\Stripe\Error\Base $e) {

			}
		}

		$stripe_diff = 0;
		if($agent['User']['stripe_base'] < 20)$stripe_diff = 20;
		if($agent['User']['stripe_base'] >=20 && $agent['User']['stripe_base'] < 50)$stripe_diff = 50 - $agent['User']['stripe_base'];
		if($agent['User']['stripe_base'] >=50 && $agent['User']['stripe_base'] < 100)$stripe_diff = 100 - $agent['User']['stripe_base'];
		if($agent['User']['stripe_base'] >=100 && $agent['User']['stripe_base'] < 250)$stripe_diff = 250 - $agent['User']['stripe_base'];
		if($agent['User']['stripe_base'] >=250 && $agent['User']['stripe_base'] < 600)$stripe_diff = 600 - $agent['User']['stripe_base'];

        $this->set(array('title' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'], 'stripe_account' => $agent['User']['stripe_account'],'stripe_balance' => $stripe_balance,'stripe_diff' => $stripe_diff, 'agent_id' => $agent['User']['id']));
        $this->render('/Agents/admin_stripe_sold_depos');
	}

	public function admin_stripe_sold_depos_action(){

		 if($this->request->is('ajax')){
			 if(!isset($this->request->data['Agent']['agent_id']) || empty($this->request->data['Agent']['agent_id']) || !is_numeric($this->request->data['Agent']['agent_id'])){
				 $this->jsonRender(array('msg' => 'Erreur lors du traitement de ce virement'));
			 }else{

				 if(!isset($this->request->data['Agent']['montant']) || empty($this->request->data['Agent']['montant']) || !is_numeric($this->request->data['Agent']['montant'])){
					$this->jsonRender(array('msg' => 'Montant non renseigné'));
				 }else{

					 $this->loadModel('WorkingCapital');

					 $amount_total = str_replace(',','.',$this->request->data['Agent']['montant']);


						 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $this->request->data['Agent']['agent_id']),
								'recursive' => -1
							));


							App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

						 $pp = number_format($amount_total,2,'.','') * 100;
					 	$msg = '';
					 	try {

											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => $agent['User']['stripe_account'],
											]);

							$capital = array(
								'user_id'              => $agent['User']['id'],
								'type'              => 'transfert',
								'amount'              => number_format($amount_total,2,'.',''),
								'date_transfert'              => date('Y-m-d H:i:s')
							);

							$this->WorkingCapital->create();
							$this->WorkingCapital->save($capital);


								$stripe_balance = 0;
									try {
										//$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
										$balance = \Stripe\Balance::retrieve(
										  ["stripe_account" => $agent['User']['stripe_account']]
										);
										if($balance->available && is_array($balance->available)){
											$available = $balance->available[0];
											$stripe_balance = $available->amount /100;
										}
									 } catch (\Stripe\Error\Base $e) {

									}

									$this->User->id = $agent['User']['id'];
								$new_base = number_format($agent['User']['stripe_base']+$amount_total,2,'.','');
								$this->User->saveField('stripe_base', $new_base);
								$this->User->saveField('stripe_balance', $stripe_balance);
								$msg = 'Montant viré sur le compte connect.';

								}
								   catch (Exception $e) {
									 //var_dump($e->getMessage());
										$msg = $e->getMessage();
									}

						 $this->jsonRender(array('msg' => $msg, 'action' => ''));

				 }
			 }

		 }
	}

	public function admin_stripe_sold_refund($agent_id){
		$this->layout = false;
        $this->autoRender = false;

		if(!$agent_id){
			$this->set(array('title' => 'Erreur', 'msg_error' => 'Erreur technique'));
			$this->render('/Agents/admin_order_pop');
			return false;
		}

		$agent = $this->User->find('first',array(
					'conditions' => array('User.id' => $agent_id),
					'recursive' => -1
				));


		App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

		$stripe_balance = 0;
		if($agent['User']['stripe_account']){
			try {
				//$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
				$balance = \Stripe\Balance::retrieve(
				  ["stripe_account" => $agent['User']['stripe_account']]
				);
				if($balance->available && is_array($balance->available)){
					$available = $balance->available[0];
					$stripe_balance = $available->amount /100;
				}
			 } catch (\Stripe\Error\Base $e) {

			}
		}


        $this->set(array('title' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'], 'stripe_account' => $agent['User']['stripe_account'],'stripe_balance' => $stripe_balance, 'agent_id' => $agent['User']['id']));
        $this->render('/Agents/admin_stripe_sold_refund');
	}

	public function admin_stripe_sold_refund_action(){

		 if($this->request->is('ajax')){
			 if(!isset($this->request->data['Agent']['agent_id']) || empty($this->request->data['Agent']['agent_id']) || !is_numeric($this->request->data['Agent']['agent_id'])){
				 $this->jsonRender(array('msg' => 'Erreur lors du traitement de ce virement'));
			 }else{

				 if(!isset($this->request->data['Agent']['montant']) || empty($this->request->data['Agent']['montant']) || !is_numeric($this->request->data['Agent']['montant'])){
					$this->jsonRender(array('msg' => 'Montant non renseigné'));
				 }else{

					 $this->loadModel('WorkingCapital');

					 $amount_total = str_replace(',','.',$this->request->data['Agent']['montant']);


						 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $this->request->data['Agent']['agent_id']),
								'recursive' => -1
							));


							App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

						 $pp = number_format($amount_total,2,'.','') * 100;
					 	$msg = '';
					 	try {
										$account = \Stripe\Account::retrieve();
										$transfer =  \Stripe\Transfer::create(
										  [
											"amount" => $pp,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent['User']['stripe_account']]
										);

							$capital = array(
								'user_id'              => $agent['User']['id'],
								'type'              => 'refund',
								'amount'              => number_format($amount_total,2,'.',''),
								'date_transfert'              => date('Y-m-d H:i:s')
							);
							$this->WorkingCapital->create();
							$this->WorkingCapital->save($capital);

								$stripe_balance = 0;
									try {
										//$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
										$balance = \Stripe\Balance::retrieve(
										  ["stripe_account" => $agent['User']['stripe_account']]
										);
										if($balance->available && is_array($balance->available)){
											$available = $balance->available[0];
											$stripe_balance = $available->amount /100;
										}
									 } catch (\Stripe\Error\Base $e) {

									}

									$this->User->id = $agent['User']['id'];
								$new_base = number_format($agent['User']['stripe_base']-$amount_total,2,'.','');
								$this->User->saveField('stripe_base', $new_base);
								$this->User->saveField('stripe_balance', $stripe_balance);
								$msg = 'Montant retiré sur le compte connect.';

								}
								   catch (Exception $e) {
									 //var_dump($e->getMessage());
										$msg = $e->getMessage();
									}

						 $this->jsonRender(array('msg' => $msg, 'action' => ''));

				 }
			 }

		 }
	}

	public function admin_voucher_create(){



            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

				if(!empty($requestData['Agents']['agent_number'])){

					 $agent = $this->User->find('first',array(
								'conditions' => array('User.agent_number' => $requestData['Agents']['agent_number']),
								'recursive' => -1
							));

					if(!$agent){
						$this->Session->setFlash(__('Aucun expert trouvé !'), 'flash_success');
							$this->redirect(array('controller' => 'agents', 'action' => 'voucher_create', 'admin' => true), false);
					}

					$this->loadModel('InvoiceAgent');
					$invoice = $this->InvoiceAgent->find('all',array(
								'conditions' => array('InvoiceAgent.user_id' => $agent['User']['id'],'InvoiceAgent.status !=' => 10 ),
								'recursive' => -1
							));

					if(!$agent){
						$this->Session->setFlash(__('Aucun facture trouvé pour cet expert !'), 'flash_success');
							$this->redirect(array('controller' => 'agents', 'action' => 'voucher_create', 'admin' => true), false);
					}

					$select_invoice = array();

					foreach($invoice as $invoi){
						$select_invoice[$invoi['InvoiceAgent']['id']] = $agent['User']['pseudo'].' ('.$agent['User']['firstname'].' '.$agent['User']['lastname'].') '. CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$invoi['InvoiceAgent']['date_max']),'%m/%y'). ' - '.$invoi['InvoiceAgent']['amount_total'].'€';
					}

					$this->set('select_invoice', $select_invoice);

				}else{
					$this->loadModel('InvoiceVoucherAgent');

					//On vérifie les champs du formulaire
					$requestData['Agents'] = Tools::checkFormField($requestData['Agents'],
						array('invoice_id'),
						array('invoice_id')
					);
					if($requestData['Agents'] === false){
						$this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
						return;
					}

					$this->loadModel('InvoiceAgent');
					$invoice = $this->InvoiceAgent->find('first',array(
								'conditions' => array('InvoiceAgent.id' => $requestData['Agents']['invoice_id'] ),
								'recursive' => -1
							));

					$data['InvoiceVoucherAgent']['user_id']= $invoice['InvoiceAgent']['user_id'];
					$data['InvoiceVoucherAgent']['invoice_id']= $invoice['InvoiceAgent']['id'];
					$data['InvoiceVoucherAgent']['amount']= $invoice['InvoiceAgent']['amount_total'];
					$data['InvoiceVoucherAgent']['date_add']= date('Y-m-d H:i:s');
					$data['InvoiceVoucherAgent']['status']= 1;

						$this->InvoiceVoucherAgent->create();
						if($this->InvoiceVoucherAgent->save($data)){

							$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
							$this->InvoiceAgent->saveField('status', 10);

							$this->Session->setFlash(__('L\'avoir a été enregistré'), 'flash_success');
							$this->redirect(array('controller' => 'agents', 'action' => 'voucher_list', 'admin' => true), false);
						}else{
							$this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
							$this->redirect(array('controller' => 'agents', 'action' => 'voucher_create', 'admin' => true), false);
						}

				}

            }

        }
		public function admin_voucher_list(){
            $this->loadModel('InvoiceVoucherAgent');
            $this->Paginator->settings = array(
				 'fields' => array('InvoiceVoucherAgent.*','User.pseudo','User.id','InvoiceAgent.*'),
                'order' => array('InvoiceVoucherAgent.date_add' => 'desc'),
				 'joins' => array(
					array('table' => 'invoice_agents',
						'alias' => 'InvoiceAgent',
						'type' => 'left',
						'conditions' => array(
							'InvoiceAgent.id = InvoiceVoucherAgent.invoice_id',
						)
					),
					 array('table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array(
							'User.id = InvoiceVoucherAgent.user_id',
						)
					)
				),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $invoices = $this->Paginator->paginate($this->InvoiceVoucherAgent);

            $this->set(compact('invoices'));
    }

	public function admin_exportstripebasecsv(){
        set_time_limit ( 0 );


        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		$listing_utcdec = Configure::read('Site.utcDec');

		$conditions = array('stripe_base >' => 0);


		$allComDatas = $this->User->find('all',array(
					'fields'        => array('User.*'),
					'conditions' => $conditions,
					'recursive' => -1
				));
		 //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'agents', 'action' => 'stripe_balance', 'admin' => true), false);
            else
                $this->redirect($source);
        }

        //Si date
        $label = 'all_export';


		$fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");


		 foreach($allComDatas as $indice => $row){


			  $line = array(
				  'id'    => $row['User']['id'],
				  'lastname'     => $row['User']['lastname'],
				  'firstname'    => $row['User']['firstname'],
				  'pseudo'       => $row['User']['pseudo'],
				  'active'       => $row['User']['active'],
				  'stripe_account'       => $row['User']['stripe_account'],
				  'stripe_base'       => $row['User']['stripe_base'],

            );

            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
			if($row['User']['id'])
           fputcsv($fp, array_values($line), ';', '"');
		 }
		 fclose($fp);
		$this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
	}

	public function admin_saveAgentFactured(){
		if($this->request->is('ajax')){

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database'], $dbb_s['port']);

			App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');

			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);

			$requestData = $this->request->data;

            if(!isset($requestData['id']))
                $this->jsonRender('Erreur');



			$id = $requestData['id'];
			$status = $requestData['status'];
			$txt = '';
			if(isset($requestData['txt'])){
				$txt = $requestData['txt'];
				$id = str_replace('text_agent_factured_','',$id);
			}

			$this->loadModel('UserCreditHistory');
			$this->loadModel('UserCreditLastHistory');
			$com = $this->UserCreditHistory->find('first', array(
				'fields'        => array('UserCreditHistory.*', 'Agent.stripe_account','Agent.order_cat', 'Agent.mail_price'),
				'conditions'    => array('UserCreditHistory.user_credit_history' => $id),
				'recursive'     => 1
			));

			if(!$com)
                $this->jsonRender('Communication manquante');

			$comlast = $this->UserCreditLastHistory->find('first', array(
				'conditions'    => array('UserCreditLastHistory.sessionid' => $com['UserCreditHistory']['sessionid'],'UserCreditLastHistory.media' => $com['UserCreditHistory']['media'],'UserCreditLastHistory.agent_id' => $com['UserCreditHistory']['agent_id']),
				'recursive'     => 1
			));

			if(!$comlast)
                $this->jsonRender('Communication erreur');


			$this->loadModel('UserPay');
			$pay = $this->UserPay->find('first', array(
				'conditions'    => array('UserPay.id_user_credit_history' => $id),
				'recursive'     => 1
			));

			if($txt){
				$this->UserCreditHistory->id = $com['UserCreditHistory']['user_credit_history'];
				$this->UserCreditHistory->saveField('text_factured', addslashes($txt));
				$this->UserCreditLastHistory->id = $comlast['UserCreditLastHistory']['user_credit_last_history'];
				$this->UserCreditLastHistory->saveField('text_factured', addslashes($txt));
				$this->jsonRender('Texte enregistre');
			}else{

				if(!$status){
					//REFUND
					$this->UserCreditHistory->id = $com['UserCreditHistory']['user_credit_history'];
					$this->UserCreditHistory->saveField('ca', 0);
					$this->UserCreditHistory->saveField('is_factured', 0);
					$this->UserCreditLastHistory->id = $comlast['UserCreditLastHistory']['user_credit_last_history'];
					$this->UserCreditLastHistory->saveField('is_factured', 0);

					if($pay){
						if($com['Agent']['stripe_account'] && $pay['UserPay']['ca'] > 0){
							try {
								$account = \Stripe\Account::retrieve();
								\Stripe\Transfer::create(
										  [
											"amount" => $pay['UserPay']['ca'] * 100,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $com['Agent']['stripe_account']]
										);
								$mysqli->query("UPDATE user_pay set price = '0',ca = '0' where id_user_pay = '{$pay['UserPay']['id_user_pay']}'");
								/*$this->UserPay->id_user_pay = $pay['UserPay']['id_user_pay'];
								$this->UserPay->saveField('ca', 0);
								$this->UserPay->saveField('price', 0);*/
								$this->jsonRender('Communication non facture et refund effectue');
							 } catch (\Stripe\Error\Base $e) {
								$this->jsonRender('Erreur refund Stripe');
							}
						}else{
							$mysqli->query("UPDATE user_pay set price = '0',ca = '0' where id_user_pay = '{$pay['UserPay']['id_user_pay']}'");
							/*$this->UserPay->id_user_pay = $pay['UserPay']['id_user_pay'];
							$this->UserPay->saveField('ca', 0);
							$this->UserPay->saveField('price', 0);*/
							$this->jsonRender('Communication non facture');
						}
					}else{
						$this->jsonRender('Communication non facture');
					}

				}else{
					//PAY
					$com['UserCreditHistory']['ca'] = $this->calcCAComm($com['UserCreditHistory']['user_credit_history'], true);

					$this->UserCreditHistory->id = $com['UserCreditHistory']['user_credit_history'];
					$this->UserCreditHistory->saveField('is_factured', 1);
					$this->UserCreditLastHistory->id = $comlast['UserCreditLastHistory']['user_credit_last_history'];
					$this->UserCreditLastHistory->saveField('is_factured', 1);

					if($pay){

						$list_cost = array();
						$this->loadModel('Cost');
						$costs = $this->Cost->find('all', array(
							'order' => array('Cost.id' => 'asc'),
							'recursive'     => 1
						));
						foreach($costs as $cost){
							$list_cost[$cost['Cost']['id']] = $cost['Cost']['cost'] / 60;
						}

						$order_cat = $com['Agent']['order_cat'];
						$mail_price = $com['Agent']['mail_price'];

						$remuneration_time = 0;
						if($order_cat)
							$remuneration_time = $list_cost[$order_cat];

						$price = 0;
						switch ($com['UserCreditHistory']['media']) {
							case 'phone':
								$price = $com['UserCreditHistory']['seconds'] * $remuneration_time;
								break;
							case 'chat':
								$price = $com['UserCreditHistory']['seconds'] * $remuneration_time;
								break;
							case 'email':
								$price = $mail_price;
								break;
						}

						$this->loadModel('Currency');
						$currency = $this->Currency->find('first', array(
							'conditions'    => array('Currency.label' => $com['UserCreditHistory']['ca_currency']),
							'recursive'     => 1
						));

						$ca_paid = $com['UserCreditHistory']['ca'] * $currency['Currency']['amount'];
						$ca_paid = number_format($ca_paid,2,'.','');
						$price = number_format($price,4,'.','');
						$mysqli->query("UPDATE user_pay set price = '".$price."', ca = '".$ca_paid."' where id_user_pay = '{$pay['UserPay']['id_user_pay']}'");

						/*$this->UserPay->id_user_pay = $pay['UserPay']['id_user_pay'];
						$this->UserPay->saveField('ca', $ca_paid);
						$this->UserPay->saveField('price', $price);*/


						if($com['Agent']['stripe_account'] && $ca_paid > 0){
							 try {
											$transfer = \Stripe\Transfer::create([
											  "amount" => $ca_paid * 100,
											  "currency" => "eur",
											  "destination" => $com['Agent']['stripe_account'],
											]);
								 $this->jsonRender('Communication facture et paiement effectue');
									}
								   catch (Exception $e) {
									$this->jsonRender('Erreur paiement stripe');
								   }

						}else{
							$this->jsonRender('Communication facture');
						}

						$this->jsonRender('stripe pay expter');
					}else{
						$this->jsonRender('Communication facture, non calcule');
					}
				}
			}
		}else{
			$this->jsonRender('Erreur');
		}

	}
   public function visio(){
		  $id_lang = $this->Session->read('Config.id_lang');

		  $user = $this->Session->read('Auth.User');
        if (!isset($user['id']) && $user['role'] !== 'agent')
            throw new Exception("Erreur de sécurité !", 1);

         $idAgent = $user['id'];



       $this->set(compact('agent'));
    }

    /**
     * fonction ajax pour actualiser html avec le status consulter par
     */
    public function updatemodeconsult(){
        if($this->request->is('ajax')){
            $html = '';
            $requestData = $this->request->data;
            $this->loadModel('User');
            $this->loadModel('CountryLangPhone');
            App::uses('FrontblockHelper', 'View/Helper');
            $fbH = new FrontblockHelper(new View());

            $CountryLang = $this->CountryLangPhone->find('first', array(
                'conditions'        => array(
                    'CountryLangPhone.country_id = '.$this->Session->read('Config.id_country'),
                    'CountryLangPhone.lang_id = '.$this->Session->read('Config.id_lang')
                ),
                'recursive'         => -1
            ));

            $CountryLangPhone = $CountryLang['CountryLangPhone'];
            $thirdNumberRule = $fbH->getThirdNumberIfExists($CountryLangPhone);
            $dateNow = date('Y-m-d H:i:s');
            $rows = $this->User->find('first', array(
                'fields' => array('User.*',
                    '(SELECT TIMESTAMPDIFF(SECOND, date_add, "'.$dateNow.'") FROM user_state_history WHERE user_id = User.id ORDER BY date_add DESC LIMIT 1) AS second_from_last_status'
                ),
                'conditions'        => array('agent_number' => $this->request->data['agent_number']),
                'recursive'         => -1
            ));
            $User = $rows['User'];
            $user = $this->Session->read('Auth.User');

            if($User['agent_status'] == 'busy'){
                $agent_busy_mode = $fbH->agentModeBusy($User['id']);
            }
            if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1){
                if($User['agent_status'] == 'busy'){
                    if($agent_busy_mode == 'phone')
                        $css_phone = ' t-'.$User['agent_status'];
                    else
                        $css_phone = ' disabled';
                }else{
                    $css_phone = ' t-'.$User['agent_status'];
                }

            }else{
                $css_phone = ' disabled';
            }
            if (isset($User['consult_chat']) && (int)$User['consult_chat'] == 1 && $fbH->agentActif($User['date_last_activity'])){
                if($User['agent_status'] == 'busy'){
                    if($agent_busy_mode == 'tchat')
                        $css_tchat = 'c-'.$User['agent_status'];
                    else
                        $css_tchat = ' disabled';
                }else{
                    $css_tchat = 'c-'.$User['agent_status'];
                }

            }else{
                $css_tchat = ' disabled';
            }


            $headerHtml='';
            $footerhTml='';
            if (isset($User['consult_email']) && (int)$User['consult_email'] == 1){
                if($User['agent_status'] == 'busy'){
                    $css_email = 'm-available';
                }else{
                    $css_email = 'm-'.$User['agent_status'];
                }
            }else{
                $css_email = ' disabled';
            }

            $csstooltip = 'tooltip';



              // end status
            $is_agent_busy = $User['agent_status'] == 'busy';
            //$agent_busy_since = '00:00:00';// TODO: where do we get this info from in db?
            $agent_busy_since_fmt = 'H:mm:ss';
            $set_title_status = '';
            $set_class_status = '';
            if ($User['agent_status'] == 'available') {
                $set_title_status = 'Disponible';
                $set_class_status = 'available';
            } elseif ($User['agent_status'] == 'busy') {
                $set_title_status = 'En consultation';
                $set_class_status = 'consultation';
            } elseif ($User['agent_status'] == 'unavailable') {
                if ($fbH->getPlanningDispo($User['id'])) {
                    $set_title_status = $fbH->getPlanningDispo($User['id']);
                } else {
                    $set_title_status = __('Indisponible');
                }
                $set_class_status = 'retour';
            }


            $headerHtml .='
            <span class="avcb-title">'. __('Consulter par').'</span>';
            $footerhTml .=' <div class="avc-btns_footer on-mobile footer_dispo">
                 <div class="bloc_info">
                    <span class="avcb-title"><b>'.__('Consulter par').'</b></span>
                    <br>
                    <span class="code_expert_bottom">'.__('Code Expert').'<span style="padding-left: 5px;">'.$User['agent_number'].'</span></span>
                </div>';

            $html .='<span class="avcb-actions"><span class="desktop-only av-tel '.$css_phone.'">';

            if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1):
                $html .='<div data-toggle="' . $csstooltip . '" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">' . __('Agents par téléphone') . '</span><p>Tel</p><span class="ae_phone_param" style="display:none">' . $User['id'] . '</span></div>';
            else:
                $html .='<div data-toggle="' . $csstooltip . '" data-placement="top" title="Tel" class="aicon"><p>Tel</p></div>';
            endif;
                $html .='</span><span class="desktop-only av-chat '.$css_chat.'">';

            if (isset($User['consult_chat']) && (int)$User['consult_chat'] == 1 && $fbH->agentActif($User['date_last_activity'])):
                $html .='<div  data-toggle="' . $csstooltip . '" data-placement="top" title="Tchat" class="nx_chatboxinterne aicon"><span class="linklink">' . __('Agents par tchat - ') . $User['id'] .'</span><p>Tchat</p></div>';
            else:
                $html .='<div data-toggle="' . $csstooltip . '" data-placement="top" title="Tchat" class="aicon"><p>Tchat</p></div>';
             endif;
            $html .='</span><span class="desktop-only av-email '.$css_email.'">';

            if (isset($User['consult_email']) && (int)$User['consult_email'] == 1):
                $html .='<div data-toggle="' . $csstooltip . '" data-placement="top" title="Email" class="nx_emailboxinterne aicon"><span class="linklink">' .  __('Agents par email - ') . $User['id'] . '</span><p>Email</p></div>';
        else:
            $html .='<div data-toggle="' . $csstooltip . '" data-placement="top" title="Email" class="aicon"><p>Email</p></div>';

        endif;
            $html .='</span>';

            if (!empty($debugging_ui) || $set_class_status != 'available'):
                $html .='<span>';
                        $lien = $fbH->Html->url(
                            array(
                                'language'      => $this->Session->read('Config.language'),
                                'controller'    => 'alerts',
                                'action'        => 'setnew',
                                $User['id']
                            )
                        );


                $html .='<span title="'. __('Recevoir une alerte sms/email').' " class="on-desktop aebutton nxtooltip icon-link btn av-btn-big-circle mobile-only"><i class="fa fa-volume-up"></i></span>
                    <span class="icon_url nx_openlightbox action-box-alerte" style="display:none">'. $lien .'</span>
                </span>';
            endif;

            $lien2 = $fbH->Html->url(
                array(
                    'language'      => $this->Session->read('Config.language'),
                    'controller'    => 'accounts',
                    'action'        => 'add_favorite',
                    $User['id']
                )
            );

            $html .='<span class="btn av-btn-big-circle nx_openlightboxinterne linkinterne desktop-only hide" data-toggle="'.$toggle_tooltip_class.'" data-placement="top" title="'.__('Favoris').'; "><span class="linklink">';
            $html .=$lien2;
            $html .='</span>
                    <i class="fa fa-heart-o"></i>
                </span>
            </span>';

         if (!empty($agent_busy_since)):
            $html .='<span class="avcb-actions-info"> '.__('En consultation depuis ').'  <span data-timer-inc="'.$agent_busy_since_fmt.'">'. $agent_busy_since.'</span></span>';
         endif;
        if (!empty($debugging_ui) || $set_class_status != 'available'):
            $html .='<span class="avcb-info">';
                $lien = $fbH->Html->url(
                    array(
                        'language'      => $this->Session->read('Config.language'),
                        'controller'    => 'alerts',
                'action'        => 'setnew',
                $User['id']
            )
        );
            $html .='<span>
                            <span title="'.__('Recevoir une alerte sms/email').'" class="on-desktop aebutton nxtooltip icon-link"><i class="fa fa-exclamation-circle"></i>'. __('Recevoir une alerte SMS/Email').'</span>
                            <span class="icon_url nx_openlightbox action-box-alerte" style="display:none">'.$lien.'</span>
                        </span>
                    </span>';
         endif;
                    $html .='</div>';

            //status agent
            $set_title_status = '';
            $set_class_status = '';
            if ($User['agent_status'] == 'available'){
                $set_title_status = 'Disponible';
                $set_class_status = 'available';
            }elseif ($User['agent_status'] == 'busy'){
                $set_title_status = 'En consultation';
                $set_class_status = 'busy';
            }elseif ($User['agent_status'] == 'unavailable'){
                if($fbH->getPlanningDispo($User['id'])){
                    $set_title_status = $fbH->getPlanningDispo($User['id']);
                }else{
                    $set_title_status = __('Indisponible');
                }

                $set_class_status = 'unavailable';
            }

            //bar mobile
            $mobile_bar = $footerhTml.$html;

            $this->jsonRender(array('html' => $headerHtml.$html, 'set_title_status' => $set_title_status, 'set_class_status' => $set_class_status , 'mobile_bar' => $mobile_bar));
        }
    }

    public function admin_message_absent($page = 1){
        $this->loadModel('AgentMessage');
        //Les conditions de base
        $conditions = array();

        if($this->params->data['Agent']['sessionid']){
            $conditions = array_merge($conditions, array(
                'AgentMessage.agent_id' => $this->params->data['Agent']['sessionid'],
            ));
        }

        //On récupère les infos du dernier achat pour les clients
        $this->Paginator->settings = array(
            'fields' => array('AgentMessage.*','Agent.id','Agent.pseudo','Agent.agent_number', 'Agent.firstname', 'Agent.id'),
            'conditions' => $conditions,
            'order' => 'AgentMessage.date_add DESC',
            'paramType' => 'querystring',
            'joins' => array(
                array('table' => 'users',
                    'alias' => 'Agent',
                    'type' => 'inner',
                    'conditions' => array('AgentMessage.agent_id = Agent.id')
                ),
            ),
            'limit' => 15
        );

        $LastMessage = $this->Paginator->paginate($this->AgentMessage);

        $this->set(compact('LastMessage'));

    }

    public function admin_delete_message($id){

        if(empty($id) || !is_numeric($id) ){
            $this->Session->setFlash(__('Le message est introuvable'), 'flash_error');
            $this->redirect(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true),false);
        }
        $this->loadModel('AgentMessage');
        //On supprime l'avis
        if($this->AgentMessage->deleteAll(array('AgentMessage.id' => $id), false))
            $this->Session->setFlash(__('Le message a été supprimé.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors de la suppression.'), 'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true),false);
    }

    public function admin_message_status_vu ($id){

        $this->loadModel('AgentMessage');

        if($this->AgentMessage->updateAll(array('status' => '"Vu"'),array('id' => $id)))
            $this->Session->setFlash(__('Modification enregistrée.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors du changement status.'),'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true),false);

    }

    public function admin_message_status_lu ($id){

        $this->loadModel('AgentMessage');

        if($this->AgentMessage->updateAll(array('status' => '"Marquer non lu"'),array('id' => $id)))
            $this->Session->setFlash(__('Modification enregistrée.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors du changement status.'),'flash_warning');

        $this->redirect(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true),false);

    }
	
	public function admin_export_vu(){
        set_time_limit ( 0 );
        ini_set("memory_limit",-1);

        //Charge model
        $this->loadModel('Voucher');
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';

        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

        // query get non lu record

        $conditions = array();
        $conditions = array_merge($conditions, array('Survey.is_view =' =>0 ));

        $this->loadModel('Survey');

        $dataRecord = $this->Survey->find('all', array(
            'fields' => array('Survey.*','Agent.*'),
            'conditions' => $conditions,
            'order' => array('Survey.id' => 'desc'),
            'paramType' => 'querystring',
            'joins'      => array(
                array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type'  => 'inner',
                    'conditions' => array(
                        'Agent.id = Survey.user_id'
                    )
                )
            )
        ));

        //Si pas de données
        if(empty($dataRecord)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'agents', 'action' => 'survey_list', 'admin' => true), false);
            else
                $this->redirect($source);
        }


        $label = 'non_lu_record';

        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

        foreach($dataRecord as $indice => $row){
            $date_envoi = CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Survey']['date_add']),'%d/%m/%y %Hh%M');

            $line = array(
                'agent'            		=> $row['Agent']['pseudo'].'-'.$row['Agent']['firstname'].' '.$row['Agent']['lastname'],
                'email'        => $row['Survey']['email'],
                'date_envoi'          =>$date_envoi,
                'statut'          		=> 'Non lu',
                'Traité'        		=>$row['Survey']['status'] > 0 ?  'Oui' :  'Non',
                'compte_valide'         		=> 	$row['Survey']['is_valid'] ? 'Oui' : 'Non',
            );

            if($indice == 0)
                fputcsv($fp, array_keys($line), ';', '"');

            fputcsv($fp, array_values($line), ';', '"');
        }
        fclose($fp);

        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));

        return $this->response;
    }
    
    
    public function certif_account(){}
    public function payment_requests(){}
    public function my_private_content(){}

    
    
    public function my_training_videos(){
	App::uses('FormHelper', 'View/Helper');
    }
    
    
    public function rates(){
	    App::uses('Inflector', 'Utility');
	    //echo Inflector::slug("formation vidéo");
	   // App::uses('CakeTime', 'Utility');
	   // echo CakeTime::format("2011-08-22 11:53:00", '%B %e, %Y %H:%M %p');

	    //exit;
            $this->loadModel('Currency');
	    $currencies= $this->Currency->find('all');
	    $this->set('currencies', $currencies);
	

    }
    
     public function effacer(){
	$number =1203.99;


//	App::uses('CakeNumber', 'Utility');
	echo CakeNumber::currency($number, "$", array(
	    'wholePosition'    => 'after',
	    'thousands' => ' ' ,  
	    'decimals'         => ',',
));
	exit;
    }
	public function promo_codes(){
		
	}
	public function social_networks(){
		
	}
	public function oeuvres_caritatives(){
		
	}

	public function my_sales(){
		
	}

	public function qr_code(){
		
	}

	public function my_billing(){
		
	}
	public function dashboard(){
		
	}
}


<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class HoroscopesController extends AppController {
        public $components = array('Paginator');
        public $uses = array('Horoscope','HoroscopeSign');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'horoscopes')));

        public function beforeFilter() {


            parent::beforeFilter();
        }
		
		private function geolocDetectHoroscopes()
		{
			$domain_id = $this->Session->read('Config.id_domain');
			if (in_array($domain_id, array(19,29,22,13,11)))return false;
			
			
			$ip = '';
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
	
			if(!$ip)$ip = $this->request->clientIp(true);
			
			require 'Component/geoip.inc';
			$gi = geoip_open("/voynce/app/Controller/Component/GeoIP.dat", GEOIP_STANDARD);
			$country_code = geoip_country_code_by_addr($gi, $ip);//$this->freegeoip_get($ip);
			geoip_close($gi);
	
			if (!$country_code)return false;
	
			$this->loadModel('Geoloc');
			$res = $this->Geoloc->find("first", array(
				'conditions' => array('country_code' => $country_code)
			));
			if (empty($res)){
				$this->Geoloc->create();
				$this->Geoloc->save(array(
					'country_code' => $country_code,
					'domain_id'    => 0,
					'lang_id'      => 0
				));
				return false;
			}
	
			$domain     = $res['Domain']['domain'];
			$lang_code  = $res['Lang']['language_code'];
			if (empty($domain) || empty($lang_code))return false;
	
			$url = 'http://'.$domain;//.'/'.$lang_code;
			$list_domain_actif = array('FR','CH','BE','LU','CA');
			if(in_array($country_code,$list_domain_actif))
				$this->redirect($url.'/horoscopes/index');
			else
				$this->redirect($url);
		}

		
		
        private function getSignes()
        {
            $signs = $this->HoroscopeSign->find('list', array(
                'fields' => array('link_rewrite', 'name'),
                'conditions' => array('lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));

            //Si il manque des traductions pour la langue en cours
            if(count($signs) < 12){
                //Les signes qui ont une traduction
                $valid_sign = array_keys($signs);
                //On va chercher la première traduction pour les signes manquants
                for($i = 1; $i <= 12; $i++){
                    //Si nous n'avons pas de traduction pour ce signe
                    if(!in_array($i, $valid_sign)){
                        //On va chercher la première traduction
                        $name = $this->HoroscopeSign->find('first', array(
                            'fields' => 'name',
                            'conditions' => array('sign_id' => $i),
                            'recursive' => -1
                        ));
                        //Si on a trouvé une traducton
                        if(!empty($name))
                            $signs[$i] = $name['HoroscopeSign']['name'];
                    }
                }
            }

            $this->set(compact('signs'));
        }
        public function index(){
			
			
			if(substr_count($_SERVER['REQUEST_URI'], '/horoscopes/' )){
				$domain_id = $this->Session->read('Config.id_domain');
				header("Status: 301 Moved Permanently", false, 301);
				switch ($domain_id) {
					case 19:
						header("Location: /fre/horoscope-du-jour");
						break;
					case 29:
						header("Location: /frc/horoscope-du-jour");
						break;
					case 22:
						header("Location: /frl/horoscope-du-jour");
						break;
				   case 13:
						header("Location: /frs/horoscope-du-jour");
						break;
				   case 11:
						header("Location: /frb/horoscope-du-jour");
						break;
				}
				exit();
			}
			
			
			if($this->request->is('post')){
			
            	$requestData = $this->request->data;
				if(isset($requestData['Horoscopes']['email']) && $requestData['Horoscopes']['email']){
					$requestData['HoroscopeSubscribe'] = Tools::checkFormField($requestData['Horoscopes'],
						array('email', 'firstname'),
						array('email')
					);
					if($requestData['Horoscopes'] === false){
						$this->Session->setFlash(__('Veuillez remplir un email.'),'flash_error');
						return;
					}
					$requestData['HoroscopeSubscribe']['sign_id'] = 0;
					if($requestData['HoroscopeSubscribe']['email']){
					$this->loadModel('HoroscopeSubscribe');
					 $this->HoroscopeSubscribe->create();
						if($this->HoroscopeSubscribe->save($requestData)){
							$this->Session->setFlash(__('Vous allez adorer découvrir chaque jour ce que les astres vous réservent !
							(Votre inscription est validée, merci !)'), 'flash_success');
						}else
							$this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre abonnement'),'flash_warning');
					}
				}else{
					$this->Session->setFlash(__('Merci de renseigner votre email.'),'flash_warning');
				}
				
			}
			
			$domain_id = $this->Session->read('Config.id_domain');
			
			$this->geolocDetectHoroscopes();
            $this->getSignes();
			
			$month = array(
				'01' => 'janvier',
				'02' => 'février',
				'03' => 'mars',
				'04' => 'avril',
				'05' => 'mai',
				'06' => 'juin',
				'07' => 'juillet',
				'08' => 'aout',
				'09' => 'septembre',
				'10' => 'octobre',
				'11' => 'novembre',
				'12' => 'décembre'
			);
			
			$day = date("d").' '.$month[date("m")].' '.date("Y");
			
			switch ($domain_id) {
				case 19:
					$this->site_vars['meta_title']       = __('Horoscope du jour gratuit et complet par Spiriteo France');
					$this->site_vars['meta_keywords']    = __('Horoscope');
					$this->site_vars['meta_description'] = __('Découvrez vite votre horoscope du jour gratuit et complet fourni par les experts astrologues de Spiriteo, le #1 de l\'astrologie, go !');
					break;
				case 29:
					$this->site_vars['meta_title']       = __('Horoscope du jour gratuit et complet par Spiriteo Canada');
					$this->site_vars['meta_keywords']    = __('Horoscope');
					$this->site_vars['meta_description'] = __('Découvrez vite votre horoscope du jour gratuit et complet fourni par les experts astrologues de Spiriteo, le #1 de l\'astrologie, go !');
					break;
				case 22:
					$this->site_vars['meta_title']       = __('Horoscope du jour gratuit et complet par Spiriteo Luxembourg');
					$this->site_vars['meta_keywords']    = __('Horoscope');
					$this->site_vars['meta_description'] = __('Découvrez vite votre horoscope du jour gratuit et complet fourni par les experts astrologues de Spiriteo, le #1 de l\'astrologie, go !');
					break;
			   case 13:
					$this->site_vars['meta_title']       = __('Horoscope du jour gratuit et complet par Spiriteo Suisse');
					$this->site_vars['meta_keywords']    = __('Horoscope');
					$this->site_vars['meta_description'] = __('Découvrez vite votre horoscope du jour gratuit et complet fourni par les experts astrologues de Spiriteo, le #1 de l\'astrologie, go !');
					break;
			   case 11:
					$this->site_vars['meta_title']       = __('Horoscope du jour gratuit et complet par Spiriteo Belgique');
					$this->site_vars['meta_keywords']    = __('Horoscope');
					$this->site_vars['meta_description'] = __('Découvrez vite votre horoscope du jour gratuit et complet fourni par les experts astrologues de Spiriteo, le #1 de l\'astrologie, go !');
					break;
			}
			
        }

        public function admin_list(){
            $this->admin_create();

            $this->render('admin_create');
        }

        public function admin_edit($id, $sign){
            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            if($this->request->is('post')){
                $requestData = $this->request->data;

                //Check le formulaire
                $requestData['Horoscope'] = Tools::checkFormField($requestData['Horoscope'], array('date_publication', 'sign_id'), array('date_publication', 'sign_id'));
                if($requestData['Horoscope'] === false){
                    $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                    $this->redirect(array('controller' => 'horoscopes', 'action' => 'edit', 'admin' => true, 'id' => $id, 'sign' => $sign), false);
                }

                //On supprime les lanques qui n'ont pas été renseigné
                foreach($requestData['HoroscopeLang'] as $key => $lang){
                    if(empty($lang['content']))
                        unset($requestData['HoroscopeLang'][$key]);
                }

                //S'il n'y aucune langue
                if(empty($requestData['HoroscopeLang'])){
                    $this->Session->setFlash(__('Veuillez renseigner au moins une langue'), 'flash_warning');
                    $this->redirect(array('controller' => 'horoscopes', 'action' => 'edit', 'admin' => true, 'id' => $id, 'sign' => $sign), false);
                }

                //Pour les langues restantes, on vérifie
                foreach($requestData['HoroscopeLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'content'), array('lang_id', 'content'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur pour une des langues'), 'flash_warning');
                        $this->redirect(array('controller' => 'horoscopes', 'action' => 'edit', 'admin' => true, 'id' => $id, 'sign' => $sign), false);
                    }

                    //Modification des url pour les images
                    $requestData['HoroscopeLang'][$key]['content'] = Tools::clearUrlImage($requestData['HoroscopeLang'][$key]['content']);
                    if($requestData['HoroscopeLang'][$key]['content'] === false){
                        $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                        $this->redirect(array('controller' => 'horoscopes', 'action' => 'edit', 'admin' => true, 'id' => $id, 'sign' => $sign), false);
                    }
                }

                //On check la date de publication
                if(!$this->check_date($requestData['Horoscope']['date_publication'])){
                    $this->set(compact('langs'));
                    return;
                }

                //Restructuration des données
                $date = new DateTime($requestData['Horoscope']['date_publication']);
                $requestData['Horoscope']['date_publication'] = $date->format('Y-m-d H:i:s');

                //On save la date de publication
                $this->Horoscope->id = $id;
                if($this->Horoscope->saveField('date_publication', $requestData['Horoscope']['date_publication'])){
                    //On supprime toutes les langues
                    $this->Horoscope->HoroscopeLang->deleteAll(array('horoscope_id' => $id, 'sign_id' => $sign), false);

                    //On save les nouvelles langues
                    $saveData = array();
                    foreach($requestData['HoroscopeLang'] as $lang){
                        $lang['sign_id'] = $requestData['Horoscope']['sign_id'];
                        $lang['horoscope_id'] = $this->Horoscope->id;
                        $saveData[] = $lang;
                    }

                    //Sauvegarde multiple
                    if($this->Horoscope->HoroscopeLang->saveMany($saveData))
                        $this->Session->setFlash(__('Votre horoscope a été modifié.'), 'flash_success');
                    else
                        $this->Session->setFlash(__('Votre horoscope a été modifié, mais un problème est survenue au niveau des langues.'), 'flash_warning');
                }else
                    $this->Session->setFlash(__('L\'horoscope n\'a pu être modifié.'), 'flash_warning');

                $this->redirect(array('controller' => 'horoscopes', 'action' => 'list', 'admin' => true), false);
            }

            //On récupère les infos de l'horoscope et les langues
            $tmp_horoscope = $this->Horoscope->find('first', array(
                'fields' => array(),
                'conditions' => array('Horoscope.id' => $id),
                'recursive' => -1
            ));

            $horoscopeLangs = $this->Horoscope->HoroscopeLang->find('all', array(
                'fields'        => array('lang_id', 'content'),
                'conditions'    => array('horoscope_id' => $id, 'sign_id' => $sign),
                'recursive'     => -1
            ));

            //Si aucune donnée
            if(empty($tmp_horoscope)){
                $this->Session->setFlash(__('Horoscope introuvable'), 'flash_warning');
                $this->redirect(array('controller' => 'horoscopes', 'action' => 'list', 'admin' => true), false);
            }

            $langDatas = array();
            //Pour chaque langue
            foreach($horoscopeLangs as $row)
                $langDatas[$row['HoroscopeLang']['lang_id']]['content'] = $row['HoroscopeLang']['content'];

            //Les infos de l'horoscope
            $horoscope['id'] = $tmp_horoscope['Horoscope']['id'];
            $horoscope['date_publication'] = $tmp_horoscope['Horoscope']['date_publication'];
            $horoscope['sign_id'] = $sign;
            $horoscope['name'] = $this->HoroscopeSign->field('name', array('sign_id' => $sign, 'lang_id' => $this->Session->read('Config.id_lang')));

            $this->set(compact('langs', 'langDatas', 'horoscope'));
        }

        public function admin_create(){
            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            //Les signes
            $sign_options = $this->HoroscopeSign->find('list', array(
                'fields' => array('sign_id', 'name'),
                'conditions' => array('lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));

            if($this->request->is('post')){
                $requestData = $this->request->data;

                //Check le formulaire
                $requestData['Horoscope'] = Tools::checkFormField($requestData['Horoscope'], array('date_publication', 'sign_id'), array('date_publication', 'sign_id'));
                if($requestData['Horoscope'] === false){
                    $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                    $this->redirect(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true), false);
                }

                //On supprime les lanques qui n'ont pas été renseigné
                foreach($requestData['HoroscopeLang'] as $key => $lang){
                    if(empty($lang['content']))
                        unset($requestData['HoroscopeLang'][$key]);
                }

                //S'il n'y aucune langue
                if(empty($requestData['HoroscopeLang'])){
                    $this->Session->setFlash(__('Veuillez renseigner au moins une langue'), 'flash_warning');
                    $this->redirect(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true), false);
                }

                //Pour les langues restantes, on vérifie
                foreach($requestData['HoroscopeLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'content'), array('lang_id', 'content'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur pour une des langues'), 'flash_warning');
                        $this->redirect(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true), false);
                    }

                    //Modification des url pour les images
                    $requestData['HoroscopeLang'][$key]['content'] = Tools::clearUrlImage($requestData['HoroscopeLang'][$key]['content']);
                    if($requestData['HoroscopeLang'][$key]['content'] === false){
                        $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                        $this->redirect(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true), false);
                    }
                }

                //On check la date de publication
                if(!$this->check_date($requestData['Horoscope']['date_publication'])){
                    $this->set(compact('langs', 'sign_options'));
                    return;
                }

                //Restructuration des données
                $date = new DateTime($requestData['Horoscope']['date_publication']);
                $requestData['Horoscope']['date_publication'] = $date->format('Y-m-d H:i:s');

                //Le signe a t-il déjà un horoscope pour la date de publication
                if($this->Horoscope->hasHoroscope($requestData['Horoscope']['date_publication'],$requestData['Horoscope']['sign_id'])){
                    $this->Session->setFlash(__('Un horoscope est déjà prévu pour ce signe à cette date'), 'flash_warning');
                    $this->set(compact('langs', 'sign_options'));
                    return;
                }

                //On save l'horoscope
                $this->Horoscope->create();
                if($this->Horoscope->getHoroscopeId($requestData['Horoscope']['date_publication']) || $this->Horoscope->save($requestData['Horoscope'])){
                    //On save les langues
                    $saveData = array();
                    foreach($requestData['HoroscopeLang'] as $lang){
                        $lang['sign_id'] = $requestData['Horoscope']['sign_id'];
                        $lang['horoscope_id'] = $this->Horoscope->id;
                        $saveData[] = $lang;
                    }
                    //Sauvegarde multiple
                    if($this->Horoscope->HoroscopeLang->saveMany($saveData))
                        $this->Session->setFlash(__('Votre horoscope a été enregistré.'), 'flash_success');
                    else
                        $this->Session->setFlash(__('Votre horoscope a été enregistré, mais un problème est survenue au niveau des langues.'), 'flash_warning');
                }else
                    $this->Session->setFlash(__('L\'horoscope n\'a pu être sauvegardé.'), 'flash_warning');

                $this->redirect(array('controller' => 'horoscopes', 'action' => 'create', 'admin' => true), false);
            }

            $this->Paginator->settings = array(
                'fields' => array('Horoscope.*','HoroscopeLang.sign_id', 'HoroscopeSign.name', 'HoroscopeSign.sign_id'),
                'conditions' => array(),
                'joins' => array(
                    array(
                        'table' => 'horoscope_langs',
                        'alias' => 'HoroscopeLang',
                        'type'  => 'left',
                        'conditions' => array('HoroscopeLang.horoscope_id = Horoscope.id')
                    ),
                    array(
                        'table' => 'horoscope_signs',
                        'alias' => 'HoroscopeSign',
                        'type'  => 'left',
                        'conditions' => array(
                            'HoroscopeSign.sign_id = HoroscopeLang.sign_id',
                            'HoroscopeSign.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
                'paramType' => 'querystring',
                'limit' => 24
            );

            $tmp_horoscopes = $this->Paginator->paginate($this->Horoscope);

            $horoscopes = array();
            $antiDoublons = array();
            foreach($tmp_horoscopes as $horoscope){
                //Doublon
                if(in_array($horoscope['Horoscope']['id'].'-'.$horoscope['HoroscopeSign']['sign_id'], $antiDoublons))
                    continue;
                $antiDoublons[] = $horoscope['Horoscope']['id'].'-'.$horoscope['HoroscopeSign']['sign_id'];

                $horoscopes[] = array(
                    'id'                => $horoscope['Horoscope']['id'],
                    'date_publication'  => $horoscope['Horoscope']['date_publication'],
                    'name'              => $horoscope['HoroscopeSign']['name'],
                    'id_sign'           => $horoscope['HoroscopeSign']['sign_id']
                );
            }

            $this->set(compact('horoscopes','langs', 'sign_options'));
        }

        public function display($idSign = '', $idHoroscope = false){//$idSign
            $this->getSignes();
			
			if (empty($idSign)){
				$link = $this->getHoroscopesLink();
				 $linkr = $this->HoroscopeSign->find('first', array(
					'fields' => array('sign_id'),
					'conditions' => array('lang_id' => $this->Session->read('Config.id_lang'), 'link_rewrite' => $link),
					'recursive' => -1
				));
				$idSign = $linkr['HoroscopeSign']['sign_id'];
        	}
			
			if($this->request->is('post')){
			
            	$requestData = $this->request->data;
				if(isset($requestData['Horoscopes']['email']) && $requestData['Horoscopes']['email']){
					$requestData['HoroscopeSubscribe'] = Tools::checkFormField($requestData['Horoscopes'],
						array('email', 'firstname'),
						array('email')
					);
					if($requestData['Horoscopes'] === false){
						$this->Session->setFlash(__('Veuillez remplir un email.'),'flash_error');
						return;
					}
					$requestData['HoroscopeSubscribe']['sign_id'] = $idSign;
					if($requestData['HoroscopeSubscribe']['email']){
					$this->loadModel('HoroscopeSubscribe');
					 $this->HoroscopeSubscribe->create();
						if($this->HoroscopeSubscribe->save($requestData)){
							$this->Session->setFlash(__('Vous allez adorer découvrir chaque jour ce que les astres vous réservent !
							(Votre inscription est validée, merci !)'), 'flash_success');
						}else
							$this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre abonnement'),'flash_warning');
					}
				}else{
					$this->Session->setFlash(__('Merci de renseigner votre email.'),'flash_warning');
				}
				
			}
			
            /* On récupère les dates du signe */
            $info_dates = $this->HoroscopeSign->find('first', array(
                'fields' => array('info_dates'),
                'conditions' => array('lang_id' => $this->Session->read('Config.id_lang'), 'sign_id' => $idSign),
                'recursive' => -1
            ));
			
            $this->set('horoscope_sign_dates', isset($info_dates['HoroscopeSign']['info_dates'])?$info_dates['HoroscopeSign']['info_dates']:'');

            //Si pas d'id, redirection page d'accueil
            if(empty($idSign) || !is_numeric($idSign))
                $this->redirect(array('controller' => 'home', 'action' => 'index'));

            $horoscopes = $idHoroscope;
			
            if($idHoroscope === false){
                //La date d'aujourd'hui
				//if(Configure::read('debug')){//PATCH
					//$today = '2015-08-13 00:00:00';
				//}else{
					$today = date('Y-m-d 00:00:00');
				//}

                //On récupère les horoscopes d'aujourd'hui
                $horoscopes = $this->Horoscope->find('list', array(
                    'fields' => array('id'),
                    'conditions' => array('date_publication' => $today),
                    'recursive' => -1
                ));
                $this->set('horoscope_date',$today);
            }else{
                $tmp = $this->Horoscope->find('first', array(
                    'fields' => array('date_publication'),
                    'conditions' => array('id' => $idHoroscope),
                    'recursive' => -1
                ));
                $this->set('horoscope_date',$tmp['Horoscope']['date_publication']);
            }


            //Si pas d'horoscope aujourd'hui
            if(empty($horoscopes)){
                $this->Session->setFlash(__('Nous sommes désolés, mais votre horoscope n\'est pas disponible.'), 'flash_warning');
                $this->redirect(array('controller' => 'horoscopes', 'action' => 'index'));
            }

            //On récupère le contenu de l'horoscope pour le signe et la langue sélectionné
            $horoscope = $this->Horoscope->HoroscopeLang->find('first', array(
                'fields' => array('HoroscopeLang.content', 'HoroscopeLang.sign_id', 'HoroscopeSign.*'),
                'conditions' => array('HoroscopeLang.horoscope_id' => $horoscopes, 'HoroscopeLang.sign_id' => $idSign, 'HoroscopeLang.lang_id' => $this->Session->read('Config.id_lang')),
                'joins' => array(
                    array(
                        'table' => 'horoscope_signs',
                        'alias' => 'HoroscopeSign',
                        'type'  => 'left',
                        'conditions' => array(
                            'HoroscopeSign.sign_id = '.$idSign,
                            'HoroscopeSign.lang_id = HoroscopeLang.lang_id'
                        )
                    )
                ),
                'recursive' => -1
            ));
            //Si pas d'horoscope aujourd'hui
            if(empty($horoscope)){
                $this->Session->setFlash(__('Nous sommes désolés, mais votre horoscope n\'est pas disponible.'), 'flash_warning');
                $this->redirect(array('controller' => 'horoscopes', 'action' => 'index'));
            }


            /* Metas */
           // $this->site_vars['meta_title']          = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'];
           // $this->site_vars['meta_keywords']       = $horoscope['HoroscopeSign']['name'];
           // $this->site_vars['meta_description']    = substr(strip_tags(str_replace("</h2>",". ",$horoscope['HoroscopeLang']['content'])), 0, 155);
			
			$month = array(
				'01' => 'janvier',
				'02' => 'février',
				'03' => 'mars',
				'04' => 'avril',
				'05' => 'mai',
				'06' => 'juin',
				'07' => 'juillet',
				'08' => 'aout',
				'09' => 'septembre',
				'10' => 'octobre',
				'11' => 'novembre',
				'12' => 'décembre'
			);
			
			$day = date("d").' '.$month[date("m")].' '.date("Y");
			
			$domain_id = $this->Session->read('Config.id_domain');
			/*$this->site_vars['meta_title']          = $horoscope['HoroscopeSign']['name'].__(' – Votre horoscope du jour gratuit et complet').$domain_name;
            $this->site_vars['meta_keywords']       = $horoscope['HoroscopeSign']['name'];
            $this->site_vars['meta_description']    = $day.__(' - Découvrez les tendances complètes de votre journée : Amour, Travail, Argent... '.$horoscope['HoroscopeSign']['name'].' - Votre horoscope du jour gratuit et complet, rédigé par notre astrologue expert. Go !');*/
			switch ($domain_id) {
				case 19:
					$this->site_vars['meta_title']       = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit complet | Spiriteo France');
					$this->site_vars['meta_keywords']    = __('Horoscope ').$horoscope['HoroscopeSign']['name'];
					$this->site_vars['meta_description'] = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit et complet : découvrez les tendances complètes de votre journée par nos astrologues experts. Go !');
					break;
				case 29:
					$this->site_vars['meta_title']       = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit complet | Spiriteo  Canada');
					$this->site_vars['meta_keywords']    = __('Horoscope ').$horoscope['HoroscopeSign']['name'];
					$this->site_vars['meta_description'] = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit et complet : découvrez les tendances complètes de votre journée par nos astrologues experts. Go !');
					break;
				case 22:
					$this->site_vars['meta_title']       = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit complet | Spiriteo  Luxembourg');
					$this->site_vars['meta_keywords']    = __('Horoscope ').$horoscope['HoroscopeSign']['name'];
					$this->site_vars['meta_description'] = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit et complet : découvrez les tendances complètes de votre journée par nos astrologues experts. Go !');
					break;
			   case 13:
					$this->site_vars['meta_title']       = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit complet | Spiriteo  Suisse');
					$this->site_vars['meta_keywords']    = __('Horoscope ').$horoscope['HoroscopeSign']['name'];
					$this->site_vars['meta_description'] = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit et complet : découvrez les tendances complètes de votre journée par nos astrologues experts. Go !');
					break;
			   case 11:
					$this->site_vars['meta_title']       = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit complet | Spiriteo  Belgique');
					$this->site_vars['meta_keywords']    = __('Horoscope ').$horoscope['HoroscopeSign']['name'];
					$this->site_vars['meta_description'] = __('Horoscope du jour ').$horoscope['HoroscopeSign']['name'].__(' gratuit et complet : découvrez les tendances complètes de votre journée par nos astrologues experts. Go !');
					break;
			}
			

			$isMobile = $this->request->isMobile();	
            $this->set(compact('horoscope','isMobile'));
        }

        private function check_date($date){
            //Les dates dans le bon format
            if(preg_match('/\d{2}-\d{2}-\d{4}/', $date) === 0 || preg_match('/\d{2}-\d{2}-\d{4}/', $date) === false){
                $this->Session->setFlash(__('La date est incorrecte. Respectez le format suivant : JJ-MM-AAA'),'flash_warning');
                return false;
            }
            return true;
        }
		
		public function admin_signs()
		{
			 $signes = $this->HoroscopeSign->find('all', array(
                'conditions' => array('lang_id' => 1),
                'recursive' => -1
            ));
			 $this->set(compact('signes'));
		}
		public function admin_signs_edit($id)
		{
			if($this->request->is('post')){
			
            	$requestData = $this->request->data;
				$signe = $this->HoroscopeSign->find('first', array(
					'conditions' => array('sign_id' => $id, 'lang_id' => 1),
					'recursive' => -1
				));
				
				$langs_fr = array(1,8,10,11,12);
				
				$signe['HoroscopeSign']['name'] = "'".$requestData['Horoscopes']['name']."'";
				$signe['HoroscopeSign']['info_dates'] = "'".$requestData['Horoscopes']['info_dates']."'";
				$signe['HoroscopeSign']['def1'] = "'".$requestData['Horoscopes']['def1']."'";
				$signe['HoroscopeSign']['def1_color'] = "'".$requestData['Horoscopes']['def1_color']."'";
				$signe['HoroscopeSign']['def2'] = "'".$requestData['Horoscopes']['def2']."'";
				$signe['HoroscopeSign']['def2_color'] = "'".$requestData['Horoscopes']['def2_color']."'";
				$signe['HoroscopeSign']['def3'] = "'".$requestData['Horoscopes']['def3']."'";
				$signe['HoroscopeSign']['def3_color'] = "'".$requestData['Horoscopes']['def3_color']."'";
				$signe['HoroscopeSign']['def4'] = "'".$requestData['Horoscopes']['def4']."'";
				$signe['HoroscopeSign']['def4_color'] = "'".$requestData['Horoscopes']['def4_color']."'";
				$signe['HoroscopeSign']['link_rewrite'] = "'".$signe['HoroscopeSign']['link_rewrite']."'";
				$signe['HoroscopeSign']['def1_img'] = "'".$signe['HoroscopeSign']['def1_img']."'";
				$signe['HoroscopeSign']['def2_img'] = "'".$signe['HoroscopeSign']['def2_img']."'";
				$signe['HoroscopeSign']['def3_img'] = "'".$signe['HoroscopeSign']['def3_img']."'";
				$signe['HoroscopeSign']['def4_img'] = "'".$signe['HoroscopeSign']['def4_img']."'";
				$signe['HoroscopeSign']['pub'] = "'".$signe['HoroscopeSign']['pub']."'";
				$signe['HoroscopeSign']['pub_mobile'] = "'".$signe['HoroscopeSign']['pub_mobile']."'";
				$signe['HoroscopeSign']['pub_sidebar_top'] = "'".$signe['HoroscopeSign']['pub_sidebar_top']."'";
				$signe['HoroscopeSign']['pub_sidebar_bottom'] = "'".$signe['HoroscopeSign']['pub_sidebar_bottom']."'";
				
				$signe['HoroscopeSign']['pub_link'] = "'".$requestData['Horoscopes']['pub_link']."'";
				$signe['HoroscopeSign']['pub_mobile_link'] = "'".$requestData['Horoscopes']['pub_mobile_link']."'";
				$signe['HoroscopeSign']['pub_sidebar_top_link'] = "'".$requestData['Horoscopes']['pub_sidebar_top_link']."'";
				$signe['HoroscopeSign']['pub_sidebar_bottom_link'] = "'".$requestData['Horoscopes']['pub_sidebar_bottom_link']."'";
				
				if($this->isUploadedFile($requestData['Horoscopes']['def1_img'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['def1_img']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Def1.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['def1_img']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['def1_img'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['def2_img'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['def2_img']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Def2.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['def2_img']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['def2_img'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['def3_img'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['def3_img']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Def3.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['def3_img']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['def3_img'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['def4_img'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['def4_img']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Def4.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['def4_img']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['def4_img'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['pub'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['pub']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                        /*if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Pub.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['pub']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['pub'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['pub_mobile'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['pub_mobile']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                        /*if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Pubmobile.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['pub_mobile']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['pub_mobile'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['pub_sidebar_top'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['pub_sidebar_top']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Pubsidebartop.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['pub_sidebar_top']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['pub_sidebar_top'] = "'".$filename."'";
                }
				if($this->isUploadedFile($requestData['Horoscopes']['pub_sidebar_bottom'])){

                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($requestData['Horoscopes']['pub_sidebar_bottom']['tmp_name']);
                        //Est-ce un fichier image autorisé ??
                       /* if(!in_array($dataSlide['mime'], array('image/png','image/jpeg', 'image/pjpeg'))){
                            continue;
                        }*/

                        $filename = $id.'-Pubsidebarbottom.jpg';
                        if(move_uploaded_file($requestData['Horoscopes']['pub_sidebar_bottom']['tmp_name'], Configure::read('Site.pathHoroscope').DS.$filename))
						 $signe['HoroscopeSign']['pub_sidebar_bottom'] = "'".$filename."'";
                }
				
				$pub_link = $signe['HoroscopeSign']['pub_link'];
				$pub_mobile_link = $signe['HoroscopeSign']['pub_mobile_link'];
				$pub_sidebar_top_link = $signe['HoroscopeSign']['pub_sidebar_top_link'];
				$pub_sidebar_bottom_link = $signe['HoroscopeSign']['pub_sidebar_bottom_link'];
				
				foreach($langs_fr as $idlang){
					$signe['HoroscopeSign']['lang_id']=$idlang;
					
					//patch url
					switch ($idlang) {
						case 8://canada
							$signe['HoroscopeSign']['pub_link'] = str_replace('fr.','ca.',$pub_link);
							$signe['HoroscopeSign']['pub_mobile_link'] = str_replace('fr.','ca.',$pub_mobile_link);
							$signe['HoroscopeSign']['pub_sidebar_top_link'] = str_replace('fr','ca.',$pub_sidebar_top_link);
							$signe['HoroscopeSign']['pub_sidebar_bottom_link'] = str_replace('fr.','ca.',$pub_sidebar_bottom_link);
							break;
						case 10://suisse
							$signe['HoroscopeSign']['pub_link'] = str_replace('fr.','ch.',$pub_link);
							$signe['HoroscopeSign']['pub_mobile_link'] = str_replace('fr.','ch.',$pub_mobile_link);
							$signe['HoroscopeSign']['pub_sidebar_top_link'] = str_replace('fr.','ch.',$pub_sidebar_top_link);
							$signe['HoroscopeSign']['pub_sidebar_bottom_link'] = str_replace('fr.','ch.',$pub_sidebar_bottom_link);
							break;
						case 11://belgique
							$signe['HoroscopeSign']['pub_link'] = str_replace('fr.','be.',$pub_link);
							$signe['HoroscopeSign']['pub_mobile_link'] = str_replace('fr.','be.',$pub_mobile_link);
							$signe['HoroscopeSign']['pub_sidebar_top_link'] = str_replace('fr.','be.',$pub_sidebar_top_link);
							$signe['HoroscopeSign']['pub_sidebar_bottom_link'] = str_replace('fr.','be.',$pub_sidebar_bottom_link);
							break;
						case 12://luxembourg
							$signe['HoroscopeSign']['pub_link'] = str_replace('fr.','lu.',$pub_link);
							$signe['HoroscopeSign']['pub_mobile_link'] = str_replace('fr.','lu.',$pub_mobile_link);
							$signe['HoroscopeSign']['pub_sidebar_top_link'] = str_replace('fr.','lu.',$pub_sidebar_top_link);
							$signe['HoroscopeSign']['pub_sidebar_bottom_link'] = str_replace('fr.','lu.',$pub_sidebar_bottom_link);
							break;
					}
					
					$this->HoroscopeSign->updateAll($signe['HoroscopeSign'], array('sign_id' => $id, 'lang_id' => $idlang));
				}
				$this->redirect(array('controller' => 'horoscopes', 'action' => 'signs', 'admin' => true), false);
			}
			
			
			 $signe = $this->HoroscopeSign->find('first', array(
                'conditions' => array('sign_id' => $id, 'lang_id' => 1),
                'recursive' => -1
            ));
			 $this->set(compact('signe'));
		}
		public function admin_signs_subscribe()
		{
			$this->loadModel('HoroscopeSubscribe');
			$this->loadModel('User');
			
			 $this->Paginator->settings = array(
				'fields' => array('HoroscopeSubscribe.*','Sign.name'),//, 'User.date_add'
				'recursive' => 1,
				'order' => 'HoroscopeSubscribe.date_add DESC',
				'paramType' => 'querystring',
				'joins' => array(
								array('table' => 'horoscope_signs',
									  'alias' => 'Sign',
									  'type' => 'left',
									  'conditions' => array('Sign.sign_id = HoroscopeSubscribe.sign_id','Sign.lang_id = 1')
								),
					 			/* array('table' => 'users',
									  'alias' => 'User',
									  'type' => 'left',
									  'conditions' => array('User.email = HoroscopeSubscribe.email')
								),*/
				),
				 'group' => 'HoroscopeSubscribe.id',
					'limit' => 25
				);
			
			
			$subs = $this->Paginator->paginate($this->HoroscopeSubscribe);
			
			foreach($subs as &$sub){
				
				$usser = $this->User->find('first', array(
					'fields' => array('User.date_add'),
					'conditions' => array('User.email'=>$sub['HoroscopeSubscribe']['email']),
					'paramType' => 'querystring',
					'recursive' => -1
				));	
				
				if($usser && $usser['User']['date_add']){
					$sub['User']['date_add'] = $usser['User']['date_add'];
				}
				
			}
			

			$this->set(compact('subs'));
		}
		public function widgetbottomlisting(){
		
		if($this->request->is('ajax')){
			$nbview = 1;
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
							$set_title = __('En consultation').'  <span class="depuis_widget">'.__('depuis').$fbH->secondsToHis($agent[0]['second_from_last_status']).'</span>';
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
                                            ).'" data-toggle="tooltip" data-placement="top" title="'.__('Recevoir une alerte sms/email').'" class="aebutton nx_openinlightbox nxtooltip"><p>Alert</p></a></li>
									<li class="alert-message"><a href="'.$fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $agent['User']['id']
                                                )
                                            ).'" class="alerte-a aebutton nx_openinlightbox nxtooltip">'.__('Recevoir une<br />alerte sms/email').'</a></li>';
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
	public function admin_exportcsv()
    {
		$this->autoRender = false;
        $filename = Configure::read('Site.pathExport').'/all_horoscopes.csv';
        $this->_fp = fopen($filename, 'w+');
        fwrite($this->_fp, "\xEF\xBB\xBF");

        $fields = array('id','email','firstname','signe','date souscription','inscrit agents','date inscription agents');
		
		fputcsv($this->_fp, $fields, ';' ,'"');
		
		$this->loadModel('HoroscopeSubscribe');
		$this->loadModel('User');
		$subscribes = $this->HoroscopeSubscribe->find('all', array(
                'fields' => array('HoroscopeSubscribe.*','Sign.name'),
				'order' => 'HoroscopeSubscribe.date_add ASC',
                'paramType' => 'querystring',
				 'joins' => array(
				array('table' => 'horoscope_signs',
                      'alias' => 'Sign',
                      'type' => 'left',
                      'conditions' => array('Sign.sign_id = HoroscopeSubscribe.sign_id','Sign.lang_id = 1')
                ),
            ),
                'recursive' => -1
            ));	
		foreach($subscribes as $subscribe){
			$row = array();
			$row['id'] = $subscribe['HoroscopeSubscribe']['id'];
			$row['email'] = $subscribe['HoroscopeSubscribe']['email'];
			$row['firstname'] = $subscribe['HoroscopeSubscribe']['firstname'];
			$row['signe'] = $subscribe['Sign']['name'];
			$row['date souscription'] = $subscribe['HoroscopeSubscribe']['date_add'];
			
			$user = $this->User->find('first', array(
                'fields' => array('User.date_add'),
				'conditions' => array('User.email'=>$subscribe['HoroscopeSubscribe']['email']),
                'paramType' => 'querystring',
                'recursive' => -1
            ));	
			
			if($user){
				$row['inscrit agents'] = 'oui';
				$row['date inscription agents'] = $user['User']['date_add'];
			}else{
				$row['inscrit agents'] = 'non';
				$row['date inscription agents'] = '';
			}
			fputcsv($this->_fp, $row, ';' ,'"');
		}
		fclose($this->_fp);
        $this->response->file($filename, array('download' => true, 'name' => basename($filename)));
		
	}
}
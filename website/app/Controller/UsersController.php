<?php
App::uses('AppController', 'Controller');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class UsersController extends AppController {

    public function beforeFilter()
    {
        if(strcmp($this->params['action'], 'logout') != 0){
            parent::beforeFilter();
            $this->Auth->allow('login','confirmation','newPasswd','passwdForget','subscribe_agent','confirmation_agent', 'modalPhotoAgent','test', 'login_agent', 'subscribe_agent_ajax','restore','login_cart','subscribe_cart','survey_agent', 'gads_subscribe');
        }
    }
	
	private function geolocDetectUser()
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
		$gi = geoip_open(APP."/Controller/Component/GeoIP.dat", GEOIP_STANDARD);
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

        $url = 'https://'.$domain;//.'/'.$lang_code;
		$list_domain_actif = array('FR','CH','BE','LU','CA');
		if(in_array($country_code,$list_domain_actif))
        	$this->redirect($url.'/users/subscribe');
		else
			$this->redirect($url);
    }


    public function subscribe_agent_ajax(){
        if($this->request->is('ajax')){
            $this->loadModel('Country');
            //Test des champs (sauf photo et audio) avant validation du formulaire
            $requestData = $this->request->data;
			
			//empty confirmation
			//if(!isset($requestData['User']['email2']) )$requestData['User']['email2'] = $requestData['User']['email_subscribe'];
			if(!isset($requestData['User']['passwd2']) )$requestData['User']['passwd2'] = $requestData['User']['passwd_subscribe'];

            //Erreur à 0
            $errors = 0;
            $listError = '<p class="txt-bold">'.__('Liste des erreurs : ').'</p><ul style="list-style-type: initial;">';

            //On vérifie l'email
            if(!filter_var($requestData['User']['email_subscribe'], FILTER_VALIDATE_EMAIL)){
                $listError.= '<li>'. __('Email invalide') .'</li>';
                $errors++;
            }
            // Les emails sont différents
            if($requestData['User']['email_subscribe'] != $requestData['User']['email2']){
                $listError.= '<li>'. __('Vos emails sont différents') .'</li>';
                $errors++;
            }
            // Si mot de passe moins 8 caractères
            if(strlen($requestData['User']['passwd_subscribe'])<8){
                $listError.= '<li>'. __('La taille minimum de votre mot de passe doit être de 8 caractères.') .'</li>';
                $errors++;
            }
            //Si mot de passe différents
            if(strcmp($requestData['User']['passwd_subscribe'], $requestData['User']['passwd2']) != 0){
                $listError.= '<li>'. __('Les mots de passe sont différents') .'</li>';
                $errors++;
            }
            //Test email unique
            if(!$this->User->singleEmail($requestData['User']['email_subscribe'], 'agent')){
                $listError.= '<li>'. __('Cet email est déjà enregistré') .'</li>';
                $errors++;
            }
            //Test sur indicatif téléphone

            $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_phone']);
            if($flag_tel === -1 || !$flag_tel){
                $listError.= '<li>'. __('L\'indicatif téléphonique n\'est pas valide') .'</li>';
                $errors++;
            }
            //On assemble le phone_number
            if(!empty($requestData['User']['phone_number']))
                $requestData['User']['phone_number'] = Tools::implodePhoneNumber($requestData['User']['indicatif_phone'], $requestData['User']['phone_number']);
            //Test numéro unique
            if(!$this->User->isUniquePhoneNumber($requestData['User']['phone_number'], 'agent')){
                $listError.= '<li>'. __('Ce numéro de téléphone est déjà enregistré') .'</li>';
                $errors++;
            }
            //Si pseudo vide ou non alphanumérique
            if(!isset($requestData['User']['pseudo']) || empty($requestData['User']['pseudo']) || !ctype_alnum(str_replace('-', '', str_replace(' ', '', $requestData['User']['pseudo'])))){
                $listError.= '<li>'. __('Veuillez saisir votre pseudo uniquement avec des caractères alphanumériques et sans espace ( avec tiret )') .'</li>';
                $errors++;
            }
            //On vérifie le numero de téléphone
			$requestData['User']['phone_number'] = str_replace(' ','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('.','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('-','',$requestData['User']['phone_number']);
            $requestData['User']['phone_number'] = $this->phoneNumberValid($requestData['User']['phone_number'], 3);
            if($requestData['User']['phone_number'] === false){
                $listError.= '<li>'. __('Numéro de téléphone invalide') .'</li>';
                $errors++;
            }

            if (empty($requestData['User']['phone_operator'])){
                $listError.= '<li>'.__('Veuillez préciser l\'opérateur téléphonique de votre numéro de téléphone').'</li>';
                $errors++;
            }

            //On teste les options experts
            if(empty($requestData['User']['langs']) || empty($requestData['User']['countries']) || empty($requestData['User']['categories']) || empty($requestData['User']['consult'])){
                $listError.= '<li>'. __('Veuillez remplir toutes vos options experts') .'</li>';
                $errors++;
            }
            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['texte']) || empty($requestData['User']['texte']) || ctype_space($requestData['User']['texte'])){
                $listError.= '<li>'. __('Votre présentation est vide') .'</li>';
                $errors++;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['careers']) || empty($requestData['User']['careers']) || ctype_space($requestData['User']['careers'])){
                $listError.= '<li>'. __('Votre parcours professionnel est vide') .'</li>';
                $errors++;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['profile']) || empty($requestData['User']['profile']) || ctype_space($requestData['User']['profile'])){
                $listError.= '<li>'. __('Votre profile est vide') .'</li>';
                $errors++;
            }
            //On ferme la liste
            $listError.= '</ul>';

            if($errors != 0){
                $this->layout = '';
                $this->set(array('title' => __('Erreur avec le formulaire'), 'content' => $listError, 'button' => __('Fermer')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('content' => $response->body(), 'return' => false));
            }else
                $this->jsonRender(array('return' => true));
        }
    }

    //Inscription agent
    public function subscribe_agent(){
        /* On récupère la liste des pays disponibles et les langues */
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');
		$this->loadModel('SocietyType');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('select_langs', $this->Lang->getLang(true));
        $this->set('select_countries_sites', $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('category_langs', $this->CategoryLang->getCategories($this->Session->read('Config.id_lang')));
		$this->set('select_society_types', $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang')));
		$dbb_r = new DATABASE_CONFIG();
		$dbb_route = $dbb_r->default;
		$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
		$result_routing_page = $mysqli_conf_route->query("SELECT name from country_langs where country_id = '{$this->Session->read('Config.id_country')}' AND id_lang = '{$this->Session->read('Config.id_lang')}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
		$coutryname = $row_routing_page['name'];
		
		$result_routing_page = $mysqli_conf_route->query("SELECT user_countries_id from user_country_langs where name = '{$coutryname}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
		
		$this->set('selected_countries', $row_routing_page['user_countries_id']);


        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(142, false, $tmp, $page);
        $this->set('page_block', $page_content);

        $this->site_vars['meta_title'] = $page['PageLang']['meta_title'];
        $this->site_vars['meta_description'] = $page['PageLang']['meta_description'];
        $this->site_vars['meta_keywords'] = $page['PageLang']['meta_keywords'];
        


        if ($this->request->is('post')){
			
            $requestData = $this->request->data;
			
			//empty confirmation
			//if(!isset($requestData['User']['email2']) )$requestData['User']['email2'] = $requestData['User']['email_subscribe'];
			if(!isset($requestData['User']['passwd2']) )$requestData['User']['passwd2'] = $requestData['User']['passwd_subscribe'];

            //On teste les options experts
            if(empty($requestData['User']['langs']) || empty($requestData['User']['countries']) || empty($requestData['User']['categories']) || empty($requestData['User']['consult'])){
                $this->Session->setFlash(__('Veuillez remplir toutes vos options experts'), 'flash_warning');
                return;
            }

            //On vérifie les champs du formulaire
            $champForm = array('email_subscribe','email2','passwd_subscribe','passwd2','pseudo','firstname','lastname','sexe','birthdate','country_id', 'indicatif_phone',
                'photo','lang_id','crop','phone_number','indicatif_mobile','phone_mobile','audio','texte','consult','categories','countries','langs','siret','societe_statut','vat_num','city','postalcode','address', 'careers', 'profile','phone_operator');
            $champsRequired = array('email_subscribe','email2','passwd_subscribe','passwd2','pseudo','firstname','lastname','sexe','birthdate','country_id',
                                    'photo','lang_id','crop','phone_number', 'indicatif_phone', 'siret', 'city', 'postalcode', 'address','phone_operator');
            $requestData['User'] = Tools::checkFormField($requestData['User'], $champForm, $champsRequired, array('birthdate'));
            if($requestData['User'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire. N\'oubliez pas les champs obligatoires.'),'flash_warning');
                $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
            }

            //On charge les validateurs Agent
            $this->User->validate = $this->User->agent_validate;

            //On assemble le numéro de téléphone, s'il est renseigné
            if(!empty($requestData['User']['phone_number'])){
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_phone']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
                }
                $requestData['User']['phone_number'] = Tools::implodePhoneNumber($requestData['User']['indicatif_phone'], $requestData['User']['phone_number']);
            }

            if(!empty($requestData['User']['phone_mobile'])){
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_mobile']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique du deuxième numéro de téléphone n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
                }
                $requestData['User']['phone_mobile'] = Tools::implodePhoneNumber($requestData['User']['indicatif_mobile'], $requestData['User']['phone_mobile']);
            }

            //On vérifie l'email et le mot de passe
            if(!$this->requestSubscribe($requestData, 'agent')) return;

            //On vérifie le numero de téléphone
			$requestData['User']['phone_number'] = str_replace(' ','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('.','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('-','',$requestData['User']['phone_number']);
            $requestData['User']['phone_number'] = $this->checkPhoneNumber($requestData['User']['phone_number'], 3);
            if($requestData['User']['phone_number'] === false)
                return;

            //On vérifie le 2eme numero de téléphone
            if (!empty($requestData['User']['phone_mobile'])){
				$requestData['User']['phone_mobile'] = str_replace(' ','',$requestData['User']['phone_mobile']);
				$requestData['User']['phone_mobile'] = str_replace('.','',$requestData['User']['phone_mobile']);
				$requestData['User']['phone_mobile'] = str_replace('-','',$requestData['User']['phone_mobile']);
                $requestData['User']['phone_mobile'] = $this->checkPhoneNumber($requestData['User']['phone_mobile'], 3);
                if($requestData['User']['phone_mobile'] === false)
                    return;
            }

            //On teste si les fichiers ont été uploade et qu'ils soient correct
            if(!$this->isUploadedFile($requestData['User']['photo'])){
                $this->Session->setFlash(__('Erreur dans le chargement de votre photo de présentation. Veuillez réessayer.'),'flash_error');
                return;
            }

            if ($requestData['User']['audio']['size'] != 0 && !$this->isUploadedFile($requestData['User']['audio'])){
                $this->Session->setFlash(__('Erreur dans le chargement de votre présentation audio. Veuillez réessayer.'),'flash_error');
                return;
            }
            //Type des fichiers
            if(!Tools::formatFile($this->allowed_mime_types,$requestData['User']['photo']['type'], 'Image')
                || ($requestData['User']['audio']['size'] != 0
                    && !Tools::formatFile($this->allowed_mime_types,$requestData['User']['audio']['type'], 'Audio')
                )){
                $this->Session->setFlash(__('Un des fichers est dans un format incorrect.'),'flash_warning');
                return;
            }
            //Fichier audio trop volumineux
            //error == 1 signifie que la taille du fichier est plus grande que celle de la conf php.    Voir php.ini
            if(($requestData['User']['audio']['size'] > Configure::read('Site.maxSizeAudio') || $requestData['User']['audio']['error'] == 1)){
                $this->Session->setFlash(__('Votre fichier audio est trop volumineux.'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['texte']) || empty($requestData['User']['texte']) || ctype_space($requestData['User']['texte'])){
                $this->Session->setFlash(__('Votre présentation est vide'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['careers']) || empty($requestData['User']['careers']) || ctype_space($requestData['User']['careers'])){
                $this->Session->setFlash(__('Votre parcours professionnel est vide'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['profile']) || empty($requestData['User']['profile']) || ctype_space($requestData['User']['profile'])){
                $this->Session->setFlash(__('Votre profil est vide'),'flash_warning');
                return;
            }

            //Initialise les paramètres de l'utilisateur
            $requestData = $this->initSubscribe($requestData);
            $requestData['User']['role'] = 'agent';
			$requestData['User']['order_cat'] = 1;
			$requestData['User']['date_new'] = date('Y-m-d H:i:s');
            $requestData['User']['agent_status'] = 'unavailable';
            $requestData['User']['has_photo'] = 1;
            $requestData['User']['agent_number'] = null;
            if($this->isUploadedFile($requestData['User']['audio']))
                $requestData['User']['has_audio'] = 1;

            //On transforme les données du champ consult
            //0 : Email     1 : Téléphone       2 : Chat
            foreach ($requestData['User']['consult'] as $value){
                if($value == 0)
                    $requestData['User']['consult_email'] = 1;
                elseif ($value == 1)
                    $requestData['User']['consult_phone'] = 1;
                else
                    $requestData['User']['consult_chat'] = 1;
            }

            //On transforme le tableau des langues parlées et des pays en String
            $requestData['User']['langs'] = implode(',',$requestData['User']['langs']);
			$requestData['User']['langs'] = str_replace('1','1,8,10,11,12',$requestData['User']['langs']);
            $requestData['User']['countries'] = implode(',',$requestData['User']['countries']);

            $this->User->create();
            $requestData['User']['lang_id'] = $this->Session->read('Config.id_lang');
            $requestData['User']['domain_id'] = $this->Session->read('Config.id_domain');


            if ($this->User->save($requestData)){
                //On charge le model pour save les catégories, statut et présentation
                $this->loadModel('CategoryUser');
                $this->loadModel('UserStateHistory');
                $this->loadModel('UserPresentLang');

                //On récupère l'id de l'user
                $idUser = $this->User->id;

                //CATGEORY (Univers) : On transforme les données de categories
                foreach ($requestData['User']['categories'] as $value){
                    $dataCategories[] = array('CategoryUser' => array('user_id' => $idUser, 'category_id' => $value));
                }
                $this->CategoryUser->saveMany($dataCategories);

                //UserStateHistory : on save le statut
                $dataUserState = array('UserStateHistory' => array('user_id' => $idUser, 'state' => 'unavailable'));
                $this->UserStateHistory->save($dataUserState);

                //UserPresentLang : on save la présentation
				$langid = $requestData['User']['lang_id'];
				if($requestData['User']['lang_id'] == 8 || $requestData['User']['lang_id'] == 10 || $requestData['User']['lang_id'] == 11 || $requestData['User']['lang_id'] == 12) $langid = 1;
                $dataUserPresent = array('UserPresentLang' => array('texte' => $requestData['User']['texte'], 'user_id' => $idUser, 'lang_id' => $langid, 'date_upd' => date('Y-m-d H:i:s')));//$requestData['User']['lang_id']
                $this->UserPresentLang->save($dataUserPresent);

                //On sauvegarde les fichiers (photo et présentation audio)
                $this->saveFile($requestData['User'], 'photo', false, true, $idUser);
                //On vérifie qu'il a bien posté une présentation audio
                if($this->isUploadedFile($requestData['User']['audio']))
                    $this->saveFile($requestData['User'], 'audio', false, true, $idUser);


                //Paramètre pour le mail de confirmation
                $paramEmail = array(
                    'email' => $requestData['User']['email'],
                    'urlConfirmation' => $this->linkGenerator('users','confirmation_agent',array(
                            'an' => 'null',
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                );

                //$this->sendEmail($requestData['User']['email'],'Validation de votre inscription sur '.Configure::read('Site.name'),'subscribe',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(151, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
                    'PARAM_EMAIL' =>    $requestData['User']['email'],
                    'PARAM_URLCONFIRMATION' => $this->linkGenerator('users','confirmation_agent',array(
                            'an' => 'null',
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                ));
				
				/* keep IP */
				$this->loadModel('UserIp');
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$check_ip = $this->UserIp->find('first',array(
					'conditions'    => array(
						'IP' => $ip_user,
						'user_id' => $this->User->id,
					),
					'recursive' => -1
				));
				if(count($check_ip)){
					$check_ip['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					 $this->UserIp->save($check_ip);
				}else{
					$this->UserIp->create();
					$requestDataIp = array();
					$requestDataIp['UserIp']['user_id'] = $this->User->id;
					$requestDataIp['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					$requestDataIp['UserIp']['IP'] = $ip_user;
           			$ret = $this->UserIp->save($requestDataIp);	
				}

                $this->set('inscription', true);
            }else{
                //On récupère les erreurs de validation
                $errors = $this->User->validationErrors;
                $keys = array_keys($errors);
                //On affiche le premier message d'erreur
                $this->Session->setFlash(__($errors[$keys[0]][0]), 'flash_warning');
                //On vide le tableau d'erreur
                $this->User->validationErrors = array();
            }
        }
    }
	
	public function subscribe_agent_merci()
    {
			
	}
	
	 public function subscribe_merci()
    {
		 /* On récupère la liste des pays disponibles et les langues */
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('select_langs', $this->Lang->getLang(true));
        $this->set('select_countries_sites', $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('category_langs', $this->CategoryLang->getCategories($this->Session->read('Config.id_lang')));


        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(142, false, $tmp, $page);
        $this->set('page_block', $page_content);
		
		return;	
	}
	
	public function subscribe_merci_parrainage()
    {
		 /* On récupère la liste des pays disponibles et les langues */
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('select_langs', $this->Lang->getLang(true));
        $this->set('select_countries_sites', $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('category_langs', $this->CategoryLang->getCategories($this->Session->read('Config.id_lang')));


        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(142, false, $tmp, $page);
		$user = $this->Session->read('Auth.User');
		$firstname = $user['firstname'];
		
		
        $this->set('page_block', $page_content);
		$this->set('firstname', $firstname);
		return;	
	}
	
	public function subscribe_cart()
    {
		if ($this->request->is('post')) {
			if(isset($this->request->data['email']) && $this->request->data['pass']){
				$this->request->data['User'] = array();
				$this->request->data['User']['isAjax'] = 1;
				$this->request->data['User']['country_id'] = $this->request->data['country'];
				$this->request->data['User']['email_subscribe'] = $this->request->data['email'];
				$this->request->data['User']['passwd_subscribe'] = $this->request->data['pass'];
				$this->request->data['User']['firstname'] = $this->request->data['firstname'];
				$this->request->data['User']['source_ins'] = '';
				$this->request->data['User']['email2'] = '';
				$this->request->data['User']['sponsor_id'] = '';
				$this->request->data['User']['sponsor_user_id'] = '';
				$this->request->data['User']['sponsor_email'] = '';
				$this->request->data['User']['cgu'] = 1;
				$this->request->data['User']['optin'] = $this->request->data['subscribe'];;
				
				$user_id = $this->subscribe();
				 if($user_id){
					 $id_cart = $this->Session->read('User.id_cart');
					 $this->loadModel('Cart');
					 $this->Cart->id = $id_cart;
					 $this->Cart->saveField('user_id',  $user_id);
					 $this->loadModel('CartLoose');
					 $cartLoose = $this->CartLoose->find('first', array(
										'conditions' => array(
											'CartLoose.id_cart' => $id_cart,
										),
										array('recursive' => -1)
					));

					if($cartLoose){
						 $this->CartLoose->id = $cartLoose['CartLoose']['id'];
						$this->CartLoose->saveField('id_user',  $user_id);
					}
					$this->jsonRender(array('return' => true));
				 }else{
					$this->jsonRender(array('return' => false, 'msg' => __('Echec lors de la création de votre compte'))); 
				 }
				
			}else{
				$this->jsonRender(array('return' => false, 'msg' => __('Merci de renseigner un email et un mot de passe.')));
			}
		 }else{
			 $this->jsonRender(array('return' => false, 'msg' => __('Inscription impossible.')));
		 }
	}
	
    public function subscribe()
    {
		if($this->Auth->user('id'))
		$this->redirect(array('controller' => 'accounts', 'action' =>'profil', 'tab' => 'profil'));
		
		//set data
		if ($this->request->is('post')){
			$requestData = $this->request->data;
				$is_sponsorship = 0;
				if(substr_count($requestData['User']['source_ins'],'parrainage'))$is_sponsorship = 1;
				$this->set(array('country' => $requestData['User']['country_id'] , 'email2' => $requestData['User']['email2'], 'email' => $requestData['User']['email_subscribe'], 'firstname' => $requestData['User']['firstname'],'is_sponsorship' => $is_sponsorship,'source_ins' => $requestData['User']['source_ins'],'sponsor_id' => $requestData['User']['sponsor_id'],'sponsor_user_id' => $requestData['User']['sponsor_user_id'],'sponsor_email' => $requestData['User']['sponsor_email']));

		}else{
				$this->set(array('country' => '' , 'email2' => '', 'email' => '', 'firstname' => '', 'is_sponsorship' => '','source_ins'=> '','sponsor_id' => '','sponsor_user_id' => '','sponsor_email' => ''));
		}
		
		$this->geolocDetectUser();
		
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
		
		
		//recup txt marketing
		$this->loadModel('Subscribe');
		 $txt = $this->Subscribe->find('first',array(
            'fields' => array('SubscribeLang.*'),
            'conditions' => array('Subscribe.active' => 1, 'Subscribe.domain' => $this->Session->read('Config.id_domain')),
            'joins' => array(
                array('table' => 'subscribe_langs',
                    'alias' => 'SubscribeLang',
                    'type' => 'left',
                    'conditions' => array(
                        'SubscribeLang.subscribe_id = Subscribe.id',
                    )
                )
            ),
            'recursive' => -1,
        ));
		
		$this->set('txt', $txt);
		
		
        if ($this->request->is('post')){
            $requestData = $this->request->data;
			
			//empty confirmation
			if(!isset($requestData['User']['email2']) )$requestData['User']['email2'] = $requestData['User']['email_subscribe'];
			if(!isset($requestData['User']['passwd2']) )$requestData['User']['passwd2'] = $requestData['User']['passwd_subscribe'];
			
            //Vérification des champs requis
            $champForm = array('firstname','email_subscribe','email2','passwd_subscribe','passwd2','country_id','cgu','optin', 'phone_number', 'indicatif_phone', 'source_ins', 'sponsor_id', 'sponsor_user_id');
            $champRequired = array('firstname','email_subscribe','email2','passwd_subscribe','passwd2','country_id','cgu');

            /* On nettoie le tableau */
            $tmp = array();
            foreach ($champForm AS $field)
                $tmp[$field] = isset($requestData['User'][$field])?$requestData['User'][$field]:'';
            $requestData['User'] = $tmp;

            /* OLD : JR
            $requestData['User'] = Tools::checkFormField($requestData['User'], $champForm, $champRequired);
            if($requestData['User'] === false){
                $this->Session->setFlash(__('Erreur dans le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'users', 'action' =>'subscribe'));
            }
            */

            /* On charge les validateurs Client */
            $this->User->validate = $this->User->customer_validate;
			
			//verifie le login
			if(substr_count($requestData['User']['firstname'],'@')){
				 if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    $this->jsonRender(array('return' => false, 'msg' => __('Pseudo incorrect.')));
                else{
                    $this->Session->setFlash(__('Pseudo incorrect'),'flash_error');
					return;
                }
			}
			

            //On assemble le numéro de téléphone, s'il est renseigné
            if(!empty($requestData['User']['phone_number'])){
                $this->loadModel('Country');
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_phone']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'users', 'action' =>'subscribe'));
                }
                $requestData['User']['phone_number'] = Tools::implodePhoneNumber($requestData['User']['indicatif_phone'], $requestData['User']['phone_number']);
            }
			
			
			//cpie donnee manquante landing
			if(substr_count($requestData['User']['source_ins'],'landing')){
				$requestData['User']['email2'] = $requestData['User']['email_subscribe'];
				$requestData['User']['passwd2'] = $requestData['User']['passwd_subscribe'];
			}
			 if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax']){
				 $requestData['User']['isAjax'] = $this->request->data['User']['isAjax'];
				 $requestData['User']['email2'] = $requestData['User']['email_subscribe'];
			 }

            //Vérification sur les informations de l'utilisateur
            if(!$this->requestSubscribe($requestData, 'client')){
				if($is_sponsorship){
					$this->Session->write('previousPagePostData', $this->request->data);
					$this->redirect($this->referer());
				}else{
					if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
						$this->jsonRender(array('return' => false, 'msg' => __('Informations incorrect.')));
					else{
						return;
					}
				}
				
			} 
			
			//recheck email
			$string = preg_match('/^[.\w-]+@([\w-]+\.)+[a-zA-Z]{2,6}$/', $requestData['User']['email_subscribe']);
			if(!$string){
					if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
						$this->jsonRender(array('return' => false, 'msg' => __('Email invalide.')));
					else{
						return;
					}
			} 

            //On vérifie le numero de téléphone, s'il y en a un
            if(!empty($requestData['User']['phone_number'])){
                $requestData['User']['phone_number'] = $this->phoneNumberValid($requestData['User']['phone_number'], 3);
                if($requestData['User']['phone_number'] === false)
                    return;
            }

            //Initialise les paramètres de l'utilisateur$
            $requestData = $this->initSubscribe($requestData);
            $requestData['User']['credit'] = 0;
			
            //On lui affecte un code personnel
            $this->loadModel('PersonalCode');
            $codes = $this->PersonalCode->find('all',array(
                'conditions' => array('PersonalCode.used' => 0),
                'limit' => 50,
                'order' => 'rand()'
            ));

            $requestData['User']['personal_code'] = $codes[rand(0,count($codes)-1)]['PersonalCode']['combinaisons'];
            //Le code n'est plus disponible
            $this->PersonalCode->updateAll(array('PersonalCode.used' => 1), array('PersonalCode.combinaisons' => $requestData['User']['personal_code']));
			
			//check if come back
			$comeBack = $this->User->find('first', array(
						'conditions'    => array('User.email' => 'delete_'.$requestData['User']['email_subscribe']),
						'recursive'     => -1
					));
			if($comeBack)$requestData['User']['is_come_back'] = 1;

            $this->User->create();

            $requestData['User']['lang_id'] = $this->Session->read('Config.id_lang');
            $requestData['User']['domain_id'] = $this->Session->read('Config.id_domain');

            /* On valide le compte */
            $requestData['User']['valid'] = 1;
			$requestData['User']['active'] = 1;
			$requestData['User']['emailConfirm'] = 1;
			
			if(!$requestData['User']['source']){
				$requestData['User']['source'] = $this->getCustomerSource();
			}
			
			/* on check si source landing  */
			if(substr_count($requestData['User']['source_ins'],'landing')){
				$requestData['User']['source']	+= ' '.$requestData['User']['source_ins'];
			}
			
			/* on check si source sponsorship */
			$is_sponsorship = false;
			if(substr_count($requestData['User']['source_ins'],'parrainage')){
				$requestData['User']['source']	= $requestData['User']['source_ins'].' '.$requestData['User']['source'];
				$is_sponsorship = true;
			}
			
			/*$cleanrequestData = array();
			$cleanrequestData = $requestData['User'];
			$requestData = array();
			$requestData['User'] = $cleanrequestData;*/
			
            if ($this->User->save($requestData)){
                //Paramètre pour le mail de confirmation
                $paramEmail = array(
                    'email' => $requestData['User']['email'],
                    'urlConfirmation' => $this->linkGenerator('users','confirmation',array(
                            'pc' => $requestData['User']['personal_code'],
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                );

                //$this->sendEmail($requestData['User']['email'],'Validation de votre inscription sur '.Configure::read('Site.name'),'subscribe',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(181, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
                    'PARAM_EMAIL' => $requestData['User']['email'],
                    'PARAM_URLCONFIRMATION' => $this->linkGenerator('users','confirmation',array(
                            'pc' => $requestData['User']['personal_code'],
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                ));

                $this->set('inscription', true);
				if(substr_count($requestData['User']['source_ins'],'landing')){
					$_SESSION['inscription_user_id'] = $this->User->id;
					$_SESSION['inscription_user_email'] = $requestData['User']['email'];
					$_SESSION['inscription_user_source'] = $requestData['User']['source_ins'];
				}
				
				if($is_sponsorship){
					$this->loadModel('Sponsorship');
					$this->loadModel('SponsorshipRule');
					$this->loadModel('UserIp');
					$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
					
					$dx = new DateTime(date('Y-m-d H:i:s'));	
					$dx->modify('- 30 days');
					$date_min = $dx->format('Y-m-d H:i:s');
					
					$check_ip = $this->UserIp->find('first',array(
						'conditions'    => array(
							'IP' => $ip_user,
							'user_id !=' => $this->User->id,
							'date_conn >=' => $date_min
						),
						'recursive' => -1
					));
					
					//valid l inscription sponsorship
						if($requestData['User']['sponsor_id']){
							if(!$check_ip){
								$this->Sponsorship->updateAll(array('status'=>2,'id_customer'=>$this->User->id, 'IP'=>"'".$ip_user."'"), array('Sponsorship.id' => $requestData['User']['sponsor_id']));
							}else{
								$is_sponsorship = false;
							}
							
						}else{
							if($requestData['User']['sponsor_user_id']){
								$conditions = array(
										'User.id' => $requestData['User']['sponsor_user_id'],
								);
								$userr = $this->User->find('first',array('conditions' => $conditions));
								$conditions = array(
								'SponsorshipRule.type_user' => $userr['User']['role'],
								);
								$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
								
								$this->Sponsorship->create();
								$saveData = array();
								$saveData['Sponsorship'] = array();
								$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
								$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
								$saveData['Sponsorship']['type_user'] = $userr['User']['role'];
								$saveData['Sponsorship']['user_id'] = $requestData['User']['sponsor_user_id'];
								$saveData['Sponsorship']['source'] = 'partage';
								$saveData['Sponsorship']['email'] = $requestData['User']['email'];
								$saveData['Sponsorship']['status'] = 0;
								$saveData['Sponsorship']['hash'] = '';
								$saveData['Sponsorship']['id_customer'] = 0;
								$this->Sponsorship->save($saveData);
								
								if(!$check_ip){
									$this->Sponsorship->updateAll(array('status'=>2,'id_customer'=>$this->User->id, 'IP'=>"'".$ip_user."'"), array('Sponsorship.id' => $this->Sponsorship->id));
								}else{
									$is_sponsorship = false;
								}
								
								
							}
						}
						if(!$check_ip){
							//SPONSORSHIP KDO
							$this->loadModel('Voucher');
							$this->Voucher->create();
							$requestVoucher = array();
							$dt = new DateTime(date('Y-m-d H:i:s'));
							$dt->modify('+ 2 day');

							if($requestData['User']['personal_code']){
								$characts = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	
								$characts .= '1234567890'; 
								$code = ''; 

								for($i=0;$i < 8;$i++) 
								{ 
									$code .= $characts[ rand() % strlen($characts) ]; 
								} 


								$requestVoucher["code"] = $code; 
								if($userr['User']['role'] == 'client')
									$requestVoucher["title"] = 'BONUS PARRAINAGE CLIENT'; 
								else
									$requestVoucher["title"] = 'BONUS PARRAINAGE EXPERT'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["population"] = $requestData['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["country_ids"] = 'all'; 

								$this->Voucher->save($requestVoucher);

								/*$this->sendCmsTemplateByMail(333, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
								  'CODE' 		=>   $code,
								  'USER_FIRSTNAME' => 	$requestData['User']['firstname'],
									'EMAIL_CLIENT' => 	$requestData['User']['email'],
											),true);*/
							}
						}
					
						if($check_ip){
							$this->Sponsorship->updateAll(array('status'=>5, 'IP'=>"'".$ip_user."'"), array('Sponsorship.id' => $requestData['User']['sponsor_id']));
							$this->User->updateAll(array('subscribe_mail'=>0), array('User.id' => $this->User->id));
						}
					
				}

				
				/* keep IP */
				$this->loadModel('UserIp');
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$check_ip = $this->UserIp->find('first',array(
					'conditions'    => array(
						'IP' => $ip_user,
						'user_id' => $this->User->id,
					),
					'recursive' => -1
				));
				if(count($check_ip)){
					$check_ip['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					 $this->UserIp->save($check_ip);
				}else{
					$this->UserIp->create();
					$requestDataIp = array();
					$requestDataIp['UserIp']['user_id'] = $this->User->id;
					$requestDataIp['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					$requestDataIp['UserIp']['IP'] = $ip_user;
           			$ret = $this->UserIp->save($requestDataIp);	
				}
				$this->set('is_sponsorship', $is_sponsorship);
				/* auto login */
				$this->login_subscribe($requestData['User']['email_subscribe'],$requestData['User']['passwd_subscribe']);
				if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
					return $this->User->id;
                   // $this->jsonRender(array('return' => true));
				
			}else{
				if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    $this->jsonRender(array('return' => false));
			}
			

            //Pour éviter l'affichage des données dans le HeaderUserBlock
            unset($this->request->data['User']['email']);
            unset($this->request->data['User']['passwd']);
			
			
			
        }
		
		$domain_id = $this->Session->read('Config.id_domain');
		
		switch ($domain_id) {
				case 19:
					$this->site_vars['meta_title']       = __('Inscription agents en ligne sur Spiriteo France');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Inscrivez-vous sur Spiriteo France et accédez aux tarifs les moins chers du web sur la agents en ligne de qualité 24/7 ► Classé n°1 depuis 2015 !');
					break;
				case 29:
					$this->site_vars['meta_title']       = __('Inscription agents en ligne sur Spiriteo Canada');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Inscrivez-vous sur Spiriteo Canada et accédez aux tarifs les moins chers du web sur la agents en ligne de qualité 24/7 ► Classé n°1 depuis 2015 !');
					break;
				case 22:
					$this->site_vars['meta_title']       = __('Inscription agents en ligne sur Spiriteo Luxemboug');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Inscrivez-vous sur Spiriteo Luxembourg et accédez aux tarifs les moins chers du web sur la agents en ligne de qualité 24/7 ► Classé n°1 depuis 2015 !');
					break;
			   case 13:
					$this->site_vars['meta_title']       = __('Inscription agents en ligne sur Spiriteo Suisse');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Inscrivez-vous sur Spiriteo Suisse et accédez aux tarifs les moins chers du web sur la agents en ligne de qualité 24/7 ► Classé n°1 depuis 2015 !');
					break;
			   case 11:
					$this->site_vars['meta_title']       = __('Inscription agents en ligne sur Spiriteo Belgique');
					$this->site_vars['meta_keywords']    = '';
					$this->site_vars['meta_description'] = __('Inscrivez-vous sur Spiriteo Belgique et accédez aux tarifs les moins chers du web sur la agents en ligne de qualité 24/7 ► Classé n°1 depuis 2015 !');
					break;
			}
		
    }

    //Confirmation de l'email d'un client
    public function confirmation(){
        //Vérification du lien
        $customer = $this->requestConfirmation('Account',$this->request->query);
        
        //Si customer = false, alors redirection sur la page d'accueil
        if(!$customer)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        $this->User->id = $customer['User']['id'];
        if(!$this->User->saveField('emailConfirm', 1) || !$this->User->saveField('active', 1)){
            $this->set(array('confirmation' => false, 'role' => 'client'));
        }else{
            //Connexion du client
            if($this->Auth->login($customer['User'])){
                /* On update la date de connexion */
                $this->User->id = $customer['User']['id'];
                $this->User->saveField('date_lastconnexion', date('Y-m-d H:i:s'));
                $this->redirect(array('controller' => 'accounts', 'action' => 'index'));
            }
        }
    }

    //Valider inscription agent
    public function confirmation_agent(){
        //Vérification du lien
        $customer = $this->requestConfirmation('Agent',$this->request->query);
        
        //Si customer = false, alors redirection sur la page d'accueil
        if(!$customer)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        $this->User->id = $customer['User']['id'];
        //Si la confirmation de l'email échoue
        if(!($this->User->saveField('emailConfirm', 1)))
            $confirmation = false;
        else{
            //Si l'agent a juste changé son adresse mail
            if($customer['User']['active'] == 1 && $customer['User']['valid'] == 1 && !empty($customer['User']['date_lastconnexion'])){
                //Connexion de l'agent
                if($this->Auth->login($customer['User'])){
                    /* On update la date de connexion */
                    $this->User->id = $customer['User']['id'];
                    $this->User->saveField('date_lastconnexion', date('Y-m-d H:i:s'));
                    $this->redirect(array('controller' => 'agents', 'action' => 'index'));
                }

            }else  //sinon c'est la 1er confirmation après l'inscription
                $confirmation = true;
        }

        $this->set(array('role' => 'agent', 'confirmation' => $confirmation));
        $this->render('confirmation');
    }

    /* Nouveau mot de passe */
    public function newPasswd(){

        if($this->request->is('post')){

            //Vérification des champs du formulaire
            $this->request->data['User'] = Tools::checkFormField($this->request->data['User'],array('passwd','passwd2','forgotten_password'), array('passwd','passwd2','forgotten_password'));
            if($this->request->data['User'] === false){
                //$this->Session->setFlash(__('Erreur dans le formulaire..'),'flash_error');
                $this->redirect(array('controller' => 'users', 'action' => 'newpasswd'));
            }

            $user = $this->User->find('first',array(
                'fields' => array('User.id','User.forgotten_password'),
                'conditions' => array('User.forgotten_password' => $this->request->data['User']['forgotten_password'], 'User.deleted' => 0),
                'recursive' => -1,
            ));

            //Si pas d'user alors redirection accueil
            if(empty($user))
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            else{
                //Mot de passe moins de huit caractères ou différent
                if(strlen($this->request->data['User']['passwd'])<8 || $this->request->data['User']['passwd']!=$this->request->data['User']['passwd2']){
                    $this->Session->setFlash(__('Votre mot de passe doit contenir au minimum huit caractères ou vos mots de passe ne sont pas identiques.'),'flash_warning');
                    $this->set('reInitPass', false);
                    return;
                }

                $this->User->id = $user['User']['id'];
                //Utilisation unique du token
                $this->request->data['User']['forgotten_password'] = null;
                //Hash le mot de passe
                $this->request->data['User']['passwd'] = $this->hashMDP($this->request->data['User']['passwd']);
				$this->request->data['User']['id'] = $this->User->id;
                if(!$this->User->save($this->request->data)){
                    $this->Session->setFlash(__('Erreur dans la réinitialisation de votre mot de passe'),'flash_error');
                    $this->redirect(array('controller' => 'home', 'action' => 'index'));
                }else
                    $this->set('reInitPass', true);
            }
            //Pour éviter l'affichage des données dans le HeaderUserBlock
            unset($this->request->data['User']['passwd']);
			$this->Session->setFlash(__('Votre mot de passe a été réinitialisé. '),'flash_success');
             $this->redirect(array('controller' => 'users', 'action' => 'login'));
            return;
        }

        //Utilisateur redirigé sur la page d'accueil si manque les paramètres
        if(empty($this->request->query)
            || !isset($this->request->query['key']))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        $user = $this->User->find('first',array(
            'fields' => 'User.forgotten_password',
            'conditions' => array('User.forgotten_password' => $this->request->query['key'], 'User.deleted' => 0),
            'recursive' => -1,
        ));

        //Redirection accueil ou on stoke le token dans le formulaire
        if(empty($user))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
        else{
            $this->request->data = $user;
		}
		
		
    }

    //Mot de passe oublié
    public function passwdForget($compte = 'client'){
        if(isset($this->params->query['compte']) && !empty($this->params->query['compte']))
            $compte = $this->params->query['compte'];

        if($this->request->is('post')){
            //Vérification des champs du formulaire
            $this->request->data['User'] = Tools::checkFormField($this->request->data['User'], array('email', 'compte'), array('email', 'compte'));
            if($this->request->data['User'] === false){
                $this->Session->setFlash(__('Erreur dans le formulaire...'),'flash_error');
                $this->redirect(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => $compte)));
            }

            //Le type de compte
            $compte = $this->request->data['User']['compte'];

            //Vérification sur l'adresse mail
            if(!filter_var($this->request->data['User']['email'], FILTER_VALIDATE_EMAIL)){
                $this->Session->setFlash(__('Email invalide.'),'flash_error');
                $this->redirect(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => $compte)));
            }

            $user = $this->User->find('first',array(
                'fields' => array('User.id','User.last_passwd_gen', 'role', 'active', 'valid'),
                'conditions' => array('User.email' => $this->request->data['User']['email'], 'User.deleted' => 0, 'User.role' => $this->request->data['User']['compte'],
                    'OR' => array(
                        array('User.active' => 1),
                        array('User.valid' => 1)
                    )
                ),
                'recursive' => -1
            ));

            //Si l'email existe et correspond à un compte actif
            if(!empty($user)
                && (($user['User']['role'] == 'client' && $user['User']['active'] == 1)
                    || ($user['User']['role'] == 'agent' && $user['User']['valid'] == 1))){
                // Token pour le lien
                $dateNow = date('Y-m-d H:i:s');
                $token = Security::hash($this->request->data['User']['email'].$dateNow,null,true);

                //Si l'utilisateur a déjà généré un mot de passe
                if(!empty($user['User']['last_passwd_gen'])){
                    // Date de la dernière génération
                    $lastPassGen = new DateTime($user['User']['last_passwd_gen']);
                    $lastPassGen = $lastPassGen->getTimestamp();

                    // Date actuelle
                    $passGen = new DateTime($dateNow);
                    $passGen = $passGen->getTimestamp();

                    // si moins de 30min depuis la dernière génération
                    if(($passGen-$lastPassGen) < Configure::read('Site.timeMinPass')){
                        $this->set('timePass',false);
                        return;
                    }
                }

                // On sauvegarde le token et sa date de génération
                $user['User']['last_passwd_gen'] = date('Y-m-d H:i:s');
                $user['User']['forgotten_password'] = $token;

                //Paramètres email
                $paramEmail = array(
                    'email' => $this->request->data['User']['email'],
                    'urlReinitialisation' => $this->linkGenerator('users','newpasswd',array('key' => $token))
                );
                //$this->sendEmail($this->request->data['User']['email'],'Réinitialisation de votre mot de passe','reinit_pass',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(180, $this->Session->read('Config.id_lang'), $this->request->data['User']['email'], array(
                    'EMAIL_ADDRESS' => $paramEmail['email'],
                    'LIEN_PWD_REINIT' => $paramEmail['urlReinitialisation']
                ));

                $this->User->id = $user['User']['id'];
                $this->User->save($user);

                $this->set('emailValid',true);
                $this->set(compact('compte'));
            }else{
                $this->Session->setFlash(__('Identifiant inconnu'),'flash_warning');
                $this->set('emailValid',false);
                $this->set(compact('compte'));
            }
            //Pour éviter l'affichage des données dans le HeaderUserBlock
            unset($this->request->data['User']['email']);
        }

        $this->set(compact('compte'));
    }
	
	public function login_cart()
    {
    
		 if ($this->request->is('post')) {
			if(isset($this->request->data['email_con']) && $this->request->data['passwd_con']){
				$this->request->data['User'] = array();
				$this->request->data['User']['isAjax'] = 1;
				$this->request->data['User']['compte'] = 'client';
				$this->request->data['User']['email'] = $this->request->data['email_con'];
				$this->request->data['User']['passwd'] = $this->request->data['passwd_con'];
				$this->login();
				 if($this->Auth->loggedIn() && $this->Auth->user('role') == 'client'){
					 $user = $this->Session->read('Auth.User');
					
					 $id_cart = $this->Session->read('User.id_cart');
					 $this->loadModel('Cart');
					 $this->Cart->id = $id_cart;
					 $this->Cart->saveField('user_id',  $user['id']);
					 $this->loadModel('CartLoose');
					 $cartLoose = $this->CartLoose->find('first', array(
										'conditions' => array(
											'CartLoose.id_cart' => $id_cart,
										),
										array('recursive' => -1)
					));

					if($cartLoose){
						 $this->CartLoose->id = $cartLoose['CartLoose']['id'];
						$this->CartLoose->saveField('id_user',  $user['id']);
					}
					 
					 $this->User->id = $user['id'];
					$this->jsonRender(array('return' => true, 'is_restricted'=> $this->User->field('payment_blocked'))); 
				 }else{
					$this->jsonRender(array('return' => false, 'msg' => __('Echec de connexion à votre compte'))); 
				 }
				
			}else{
				$this->jsonRender(array('return' => false, 'msg' => __('Merci de renseigner un email et un mot de passe.')));
			}
		 }else{
			 $this->jsonRender(array('return' => false, 'msg' => __('Authentification impossible.')));
		 }
	}

    public function login()
    {
        
	//var_dump($this->request->data);exit;
	
        if ($this->request->is('post')) {
			if(isset($this->request->data['User']['compte_con']) && $this->request->data['User']['email_con']){
				$this->request->data['User']['compte'] = $this->request->data['User']['compte_con'];	
				$this->request->data['User']['email'] = $this->request->data['User']['email_con'];	
				$this->request->data['User']['passwd'] = $this->request->data['User']['passwd_con'];
			}

            //De quelle connexion il s'agit (admin, voyant, client) ?
            if(isset($this->request->data['User']['compte']) && in_array($this->request->data['User']['compte'],$this->nx_roles)){
                //On modifie les conditions d'authentification
                $this->Auth->authenticate['Form']['scope'] = array_merge(array('User.role = \''.$this->request->data['User']['compte'].'\''),$this->Auth->authenticate['Form']['scope']);
			}else{
                if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    $this->jsonRender(array('return' => false, 'msg' => __('Authentification impossible. Veuillez actualiser la page.')));
                else{
                    $this->Session->setFlash(__('Authentification impossible. Veuillez actualiser la page.'),'flash_error');
                    return;
                }
            }
           
            //On détruit toute session
            $this->Session->delete('Auth.User');

            sleep(1);

	   
            if ($this->Auth->login()){
                /* On récupère les datas */
                $this->User->id = $this->Auth->user('id');
                $datas = $this->User->find('first', array(
                    'conditions' => array('id' => $this->Auth->user('id')),
                    'recursive' => -1
                ));

                //On update la date de connexion
                $this->User->saveField('date_lastconnexion', date('Y-m-d H:i:s'));
				

                /* Cookie de connexion */
                    if (isset($datas['User'])){// && isset($this->request->data['User']['rememberme']) && $this->request->data['User']['rememberme']  == 1){
                        $cookieDatas['email'] = $datas['User']['email'];
                        $cookieDatas['passwd'] = $datas['User']['passwd'];
                        $this->Cookie->write('user_remember', $cookieDatas, true, "2 months");
                    }
					
                /* keep IP */
                $this->loadModel('UserIp');
                $ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
                $check_ip = $this->UserIp->find('first',array(
                  'conditions'    => array(
                    'IP' => $ip_user,
                    'user_id' => $this->Auth->user('id'),
                  ),
                  'recursive' => -1
                ));
                if(count($check_ip)){
                  $check_ip['UserIp']['date_conn'] = date('Y-m-d H:i:s');
                   $this->UserIp->save($check_ip);
                }else{
                  $this->UserIp->create();
                  $requestDataIp = array();
                  $requestDataIp['UserIp']['user_id'] = $this->Auth->user('id');
                  $requestDataIp['UserIp']['date_conn'] = date('Y-m-d H:i:s');
                  $requestDataIp['UserIp']['IP'] = $ip_user;
                        $ret = $this->UserIp->save($requestDataIp);	
                }
                //Retour ajax
                if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax']){
                  return '';
                    //$this->jsonRender(array('return' => true));
				}else{
					/* Redirection selon le statut du compte */
					switch ($datas['User']['role']){
						case 'admin':
							//----------------------A SUPPRIMER -----------------------------------------
							$this->Session->write('Config.id_lang', 1);
							$this->Session->write('Config.language', 'fre');
							$this->Session->write('Config.id_country', 1);
							$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true), false);
							break;
						case 'agent':

							$this->loadModel('UserConnexion');
							if($datas['User']['agent_status'] != 'unavailable'){
								$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => session_id(),
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'status'       		 => 'login',
									'who'       		 => $this->User->id,
									'mail'            	=> $datas['User']['consult_email'],
									'tchat'      		=> $datas['User']['consult_chat'],
									'phone'    			=> $datas['User']['consult_phone']
								);
							}else{
								$connexion = array(
									'user_id'          	=> $this->User->id,
									'session_id'        => session_id(),
									'date_connexion'    => date('Y-m-d H:i:s'),
									'date_lastactivity' => date('Y-m-d H:i:s'),
									'status'       		 => 'login',
									'who'       		 => $this->User->id,
									'mail'            	=> $datas['User']['consult_email'],
									'tchat'      		=> $datas['User']['consult_chat'],
									'phone'    			=> $datas['User']['consult_phone']
								);
							}

							$this->UserConnexion->create();
							$this->UserConnexion->save($connexion);

							$this->redirect(array('controller' => 'agents', 'action' => 'profil'));

							break;
						case 'client':
							if($datas['User']['valid'] == 0) $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
							//if($this->request->isMobile()){
							//	$this->redirect(array('controller' => 'home', 'action' => 'index'));
							//}else{
								//$this->redirect(array('controller' => 'accounts', 'action' => 'index'));	
							//}
							break;
					}
					
					if(!substr_count($this->referer(),'users/login')){
						$this->redirect($this->referer());
					}else{
						$this->redirect(array('controller' => 'home', 'action' => 'index'));
					}
					
				}
            } else {
		
                //Retour ajax
               // if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                //    $this->jsonRender(array('return' => false, 'msg' => __('Identifiants incorrects')));

		$compte = $this->User->find('first', array(
                    'conditions' => array('email' => $this->request->data['User']['email'],  'role' => $this->request->data['User']['compte']),
                    'recursive' => -1
                ));//'active !=' => 1,
		
		
		
		if($compte && $compte['User']['deleted'] && $compte['User']['role'] == 'client'){
					if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    	$this->jsonRender(array('return' => false, 'msg' => __('Vous avez demandé la désactivation de votre compte client')));
					else{
						$this->Session->setFlash(__('Vous avez demandé la désactivation de votre compte client'), 'flash_info', array('link' => Router::url(array('controller' => 'users', 'action' => 'restore', '?' => array('compte' => $compte['User']['id'])), true), 'messageLink' => __('<span class="hover_action_flash">Si vous souhaitez vous le réactiver cliquez ici</span>.')));
					}
					
						
				}else{
				   
				    
					if($compte){
						
					  
					    
			if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    	$this->jsonRender(array('return' => false, 'msg' => __('Identifiants incorrects.')));
						else{
							$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => 'client')), true), 'messageLink' => __('Mot de passe oublié ?')));
							if($this->request->data['User']['compte'] === 'admin'){
								$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'admins', 'action' => 'passwdforget'), true), 'messageLink' => __('Mot de passe oublié ?')));
								$this->redirect(array('controller' => 'admins', 'action' => 'login'), false);
							}elseif($this->request->data['User']['compte'] === 'agent'){
								$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => 'agent')), true), 'messageLink' => __('Mot de passe oublié ?')));
								$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
							}
							
						}
						
					}else{
						
						if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
                    	$this->jsonRender(array('return' => false, 'msg' => __('Votre compte n\'est pas activé')));
						else{
							 $this->Session->setFlash(__('Votre compte n\'est pas activé'), 'flash_info');
							if($this->request->data['User']['compte'] === 'agent')
								$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
							elseif($this->request->data['User']['compte'] === 'admin')
								$this->redirect(array('controller' => 'admins', 'action' => 'login'), false);
							
						}

						
						/*

						if($compte['User']['deleted'] && $compte['User']['role'] == 'client'){
							$this->Session->setFlash(__('Vous avez demandé la désactivation de votre compte client'), 'flash_info', array('link' => Router::url(array('controller' => 'users', 'action' => 'restore', '?' => array('compte' => $compte['User']['id'])), true), 'messageLink' => __('<span class="hover_action_flash">Si vous souhaitez vous le réactiver cliquez ici</span>.')));
						}else{
							$this->Session->setFlash(__('Votre compte n\'est pas activé'), 'flash_info');
							if($this->request->data['User']['compte'] === 'agent')
								$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
							elseif($this->request->data['User']['compte'] === 'admin')
								$this->redirect(array('controller' => 'admins', 'action' => 'login'), false);
						}*/
					}
				}
            }
        }
		
		if($this->Auth->loggedIn()){
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		}

        /* On récupère la liste des pays disponibles */
        $this->loadModel('UserCountry');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
		
		/* Metas */
        $this->site_vars['meta_title']       = __('Accès espace client - Spiriteo, agents en ligne privée');
        $this->site_vars['meta_keywords']    = '';
        $this->site_vars['meta_description'] = __('Page d\'accès à votre espace personnel sur Spiriteo. Retrouvez votre tableau de bord avec votre historique de consultation. Connectez-vous maintenant.');
		
    }
	
	public function login_subscribe($email,$pass)
    {
        
        if ($email && $pass) {
				$this->request->data['User']['compte'] = 'client';	
				$this->request->data['User']['email'] = $email;	
				$this->request->data['User']['passwd'] = $pass;
			
                $this->Auth->authenticate['Form']['scope'] = array_merge($this->Auth->authenticate['Form']['scope'], array('User.role = \'client\''));

            //On détruit toute session
            $this->Session->delete('Auth.User');

            sleep(1);

            if ($this->Auth->login()){
                /* On récupère les datas */
                $this->User->id = $this->Auth->user('id');
                $datas = $this->User->find('first', array(
                    'conditions' => array('id' => $this->Auth->user('id')),
                    'recursive' => -1
                ));

                //On update la date de connexion
                $this->User->saveField('date_lastconnexion', date('Y-m-d H:i:s'));
				

					
				/* keep IP */
				$this->loadModel('UserIp');
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$check_ip = $this->UserIp->find('first',array(
					'conditions'    => array(
						'IP' => $ip_user,
						'user_id' => $this->Auth->user('id'),
					),
					'recursive' => -1
				));
				if(count($check_ip)){
					$check_ip['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					 $this->UserIp->save($check_ip);
				}else{
					$this->UserIp->create();
					$requestDataIp = array();
					$requestDataIp['UserIp']['user_id'] = $this->Auth->user('id');
					$requestDataIp['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					$requestDataIp['UserIp']['IP'] = $ip_user;
           			$ret = $this->UserIp->save($requestDataIp);	
				}

                /* Redirection selon le statut du compte */
              /* switch ($datas['User']['role']){
                    case 'client':
                        if($datas['User']['valid'] == 0) $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
						if($this->request->isMobile()){
                        	$this->redirect(array('controller' => 'home', 'action' => 'index'));
						}else{
							$this->redirect(array('controller' => 'accounts', 'action' => 'index'));	
						}
						break;
                }*/

               // $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }
        }
    }

	
	public function restore(){
		$query = $this->request->query;
		
		if($query['compte'] && is_numeric($query['compte'])){
			
                $datas = $this->User->find('first', array(
                    'conditions' => array('id' => $query['compte']),
                    'recursive' => -1
                ));
				if($datas['User']['id']){
                	$this->User->id = $datas['User']['id'];
                	$this->User->saveField('deleted', 0);
					$this->Session->setFlash(__('Votre compte vient d\'être réactivé'), 'flash_success');
				}else{
					$this->Session->setFlash(__('Votre compte n\'a pas été restauré'), 'flash_warning');
				}
			
			$this->redirect(array('controller' => 'users', 'action' => 'login'), false);
		}
	}

    public function login_agent(){}
    
    public function logout()
    {
        $message = __('Vous êtes déconnecté.');
        $template = 'flash_success';
        //Déconnexion avec un message spécifique
        if(isset($this->request->query['message']) && isset($this->request->query['template'])){
            $message = $this->request->query['message'];
            $template = $this->request->query['template'];
        }
       
        $this->Session->setFlash($message, $template);
       // $this->Cookie->delete('user_remember');
		setcookie("CakeCookie[user_remember]", "", time()-3600);
		
        //Fermeture des chats ouverts
        $this->closeChat();

        $this->destroySessionAndCookie();
        $this->Auth->logout();


        if(isset($this->request->query['adminLogout']))
            $this->redirect(array('controller' => 'admins', 'action' => 'login'));
        /*$this->redirect(array(
            'controller' => $this->Auth->logoutRedirect['controller'],
            'action' => $this->Auth->logoutRedirect['action'],
            'language' => $this->Session->read('Config.language')
        ));*/
		$this->redirect('/');
    }

    //Effectue les tests primaires lors d'une inscription user,agent
    protected function requestSubscribe($datas, $role){
        $valid = true;
		
		if(isset($datas['User']['isAjax']) && $datas['User']['isAjax']){
			//Vérification sur l'adresse mail
			if(!filter_var($datas['User']['email_subscribe'], FILTER_VALIDATE_EMAIL)){
				$this->jsonRender(array('return' => false, 'msg' => __('Email invalide.')));
				$valid = false;
				return;
			}elseif (empty($datas['User']['email2'])){
			// La confirmation d'email n'est pas présente
				$this->jsonRender(array('return' => false, 'msg' => __('Veuillez confirmer votre adresse e-mail en la re-saisissant')));
				$valid = false;
				return;
				
			// Les emails sont différents
			}elseif($datas['User']['email_subscribe']!=$datas['User']['email2']){
				$this->jsonRender(array('return' => false, 'msg' => __('Vos emails sont différents')));
				$valid = false;
				return;

			}
			// Si mot de passe moins 8 caractères
			elseif(strlen($datas['User']['passwd_subscribe'])<8){
				$this->jsonRender(array('return' => false, 'msg' => __('La taille minimum de votre mot de passe doit être de 8 caractères.')));
				$valid = false;
				return;

			}
			// La confirmation de mot de passe n'a pas été saisie
			elseif(empty($datas['User']['passwd2'])){
				$this->jsonRender(array('return' => false, 'msg' => __('Veuillez re-saisir votre mot de passe dans le champ de confirmation')));
				$valid = false;
				return;

			}
			//Si mot de passe différents
			elseif(strcmp($datas['User']['passwd_subscribe'], $datas['User']['passwd2']) != 0){
				$this->jsonRender(array('return' => false, 'msg' => __('Les mots de passe sont différents.')));
				$valid = false;
				return;

			}
			//Test email unique
			elseif(!$this->User->singleEmail($datas['User']['email_subscribe'], $role)){
				$this->jsonRender(array('return' => false, 'msg' => __('Cet email est déjà enregistré.')));
				$valid = false;
				return;

			}
			//Test numéro unique
			elseif(!empty($datas['User']['phone_number']) && !$this->User->isUniquePhoneNumber($datas['User']['phone_number'], $role)){
				$this->jsonRender(array('return' => false, 'msg' => __('Ce numéro de téléphone est déjà enregistré.')));
				$valid = false;
				return;

			}elseif (empty($datas['User']['firstname'])){
				$this->jsonRender(array('return' => false, 'msg' => __('Veuillez rentrer votre prénom ou pseudo')));
				$valid = false;
				return;

			}elseif ($role == 'client' && (int)$datas['User']['cgu'] != 1){
				$this->jsonRender(array('return' => false, 'msg' => __('Veuillez accepter les CGV pour créer votre compte')));
				$valid = false;
				return;
			}
			
		}else{
		
			//Vérification sur l'adresse mail
			if(!filter_var($datas['User']['email_subscribe'], FILTER_VALIDATE_EMAIL)){
				$this->Session->setFlash(__('Email invalide.'),'flash_error');
				$valid = false;
			}elseif (empty($datas['User']['email2'])){
			// La confirmation d'email n'est pas présente
				$this->Session->setFlash(__('Veuillez confirmer votre adresse e-mail en la re-saisissant'),'flash_warning');
				$valid = false;
			// Les emails sont différents
			}elseif($datas['User']['email_subscribe']!=$datas['User']['email2']){
				$this->Session->setFlash(__('Vos emails sont différents'),'flash_warning');
				$valid = false;
			}
			// Si mot de passe moins 8 caractères
			elseif(strlen($datas['User']['passwd_subscribe'])<8){
				$this->Session->setFlash(__('La taille minimum de votre mot de passe doit être de 8 caractères.'),'flash_warning');
				$valid = false;
			}
			// La confirmation de mot de passe n'a pas été saisie
			elseif(empty($datas['User']['passwd2'])){
				$this->Session->setFlash(__('Veuillez re-saisir votre mot de passe dans le champ de confirmation'),'flash_warning');
				$valid = false;
			}
			//Si mot de passe différents
			elseif(strcmp($datas['User']['passwd_subscribe'], $datas['User']['passwd2']) != 0){
				$this->Session->setFlash(__('Les mots de passe sont différents.'),'flash_warning');
				$valid = false;
			}
			//Test email unique
			elseif(!$this->User->singleEmail($datas['User']['email_subscribe'], $role)){
				$this->Session->setFlash(__('Cet email est déjà enregistré.'),'flash_warning');
				$valid = false;
			}
			//Test numéro unique
			elseif(!empty($datas['User']['phone_number']) && !$this->User->isUniquePhoneNumber($datas['User']['phone_number'], $role)){
				$this->Session->setFlash(__('Ce numéro de téléphone est déjà enregistré.'), 'flash_warning');
				$valid = false;
			}elseif (empty($datas['User']['firstname'])){
				$this->Session->setFlash(__('Veuillez rentrer votre prénom ou pseudo'), 'flash_warning');
				$valid = false;
			}elseif ($role == 'client' && (int)$datas['User']['cgu'] != 1){
				$this->Session->setFlash(__('Veuillez accepter les CGV pour créer votre compte'), 'flash_warning');
				$valid = false;
			}

		}
		
        // ?????
        if(!$valid){
            unset($datas['User']['passwd_subscribe']);
            unset($datas['User']['passwd2']);
        }

        return $valid;
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

    private function closeChat(){
        if($this->Auth->user('role') !== 'admin'){
            $this->loadModel('Chat');
            $chat = $this->Chat->find('first',array(
                'fields' => array('Chat.id', 'Chat.session_id', 'Chat.consult_date_start', 'Chat.from_id', 'Chat.to_id', 'Agent.pseudo', 'User.credit'),
                'conditions' => array(
                    ($this->Auth->user('role') === 'client' ?'Chat.from_id':'Chat.to_id') => $this->Auth->user('id'),
                    'Chat.date_start !='    => null,
                    'Chat.date_end'         => null
                ),
                'recursive' => 0
            ));
            if($this->Chat->closeChat($chat, $this->Auth->user('id'), $this->Auth->user('role')) !== false){
                //L'agent est disponible
                $this->User->id = $chat['Chat']['to_id'];
                $this->User->saveField('agent_status', 'available');
                $this->loadModel('UserStateHistory');
                $this->UserStateHistory->create();
                $this->UserStateHistory->save(array(
                    'user_id'   => $chat['Chat']['to_id'],
                    'state'     => 'available'
                ));

                //Si déconnection de l'agent
                if($this->Auth->user('role') === 'agent'){
                    //Le code de l'agent
                    $agent_number = $this->User->field('agent_number');
                    //On alerte les clients qui l'ont demandé si le statut est available
                    App::import('Controller', 'Alerts');
                    $alerts = new AlertsController();
                    $alerts->alertUsersForUserAvailability($agent_number, 'phone');
                }
            }
        }
    }
    
    //Effectue les tests primaires lors d'une confirmation d'adresse mail d'un agent ou client
    protected function requestConfirmation($model,$query){
        $varModel = array(
            'Agent' => array('queryField' => 'an', 'modelField' => 'agent_number'),
            'Account' => array('queryField' => 'pc', 'modelField' => 'personal_code')
        );
    
        //Utilisateur redirigé sur la page d'accueil si manque les paramètres
        if(empty($query)
            || !isset($query[$varModel[$model]['queryField']])
            || !isset($query['mc']))
            return false;

        //Le ou les customer(s) qui n'ont pas confirmé leurs adresses mails
        $customers = $this->User->find('all',array(
            'conditions' => array(
                'User.'.$varModel[$model]['modelField'] => (strcmp($query[$varModel[$model]['queryField']], 'null') == 0 ?null:$query[$varModel[$model]['queryField']]),
                'User.deleted' => 0,
                'User.emailConfirm' => 0
            ),
            'recursive' => -1,
        ));

        //Si pas de customer trouvé, redirection page d'accueil
        if(empty($customers))
            return false;

        $customer = array();
        //Pour chaque customer
        foreach($customers as $custo){
            //Si l'email reçu correspond avec l'email du $custo
            if(Security::hash($custo['User']['email'].(empty($custo['User']['date_lastconnexion'])?'null':$custo['User']['date_lastconnexion']),null,true) === $query['mc']){
                $customer = $custo;
                break;
            }
        }

        //Si pas de customer trouvé, redirection sur la page d'accueil
        if(empty($customer))
            return false;
            
        return $customer;
    }

    //Initialise les paramètres primaires lors d'une inscription user,agent
    protected function initSubscribe($datas){
        $datas['User']['email'] = $datas['User']['email_subscribe'];
        $datas['User']['passwd'] = $datas['User']['passwd_subscribe'];
        $datas['User']['active'] = 0;
        $datas['User']['valid'] = 0;
        $datas['User']['deleted'] = 0;
        $datas['User']['emailConfirm'] = 0;

        return $datas;
    }

    //La modal pour la photo de l'agent
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
	
	public function getCustomerSource(){
		
		if(!empty($this->Cookie->read('customer_s'))){
			if($this->Cookie->read('customer_s'))
				return $this->Cookie->read('customer_s');
			else
				return 'Direct';
		}
			
		else
			return 'Direct';
	}
	
	 //Questionnaire agent
    public function survey_agent(){
		
		
        /* On récupère la liste des pays disponibles et les langues */
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('select_langs', $this->Lang->getLang(true));
        $this->set('select_countries_sites', $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang')));
        $this->set('category_langs', $this->CategoryLang->getCategories($this->Session->read('Config.id_lang')));
		
		$dbb_r = new DATABASE_CONFIG();
		$dbb_route = $dbb_r->default;
		$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
		$result_routing_page = $mysqli_conf_route->query("SELECT name from country_langs where country_id = '{$this->Session->read('Config.id_country')}' AND id_lang = '{$this->Session->read('Config.id_lang')}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
		$coutryname = $row_routing_page['name'];
		
		$result_routing_page = $mysqli_conf_route->query("SELECT user_countries_id from user_country_langs where name = '{$coutryname}'");
		$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
		
		$this->set('selected_countries', $row_routing_page['user_countries_id']);


        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(142, false, $tmp, $page);
        $this->set('page_block', $page_content);

        $this->site_vars['meta_title'] = $page['PageLang']['meta_title'];
        $this->site_vars['meta_description'] = $page['PageLang']['meta_description'];
        $this->site_vars['meta_keywords'] = $page['PageLang']['meta_keywords'];
        


        if ($this->request->is('post')){
			
            $requestData = $this->request->data;
			
			//empty confirmation
			//if(!isset($requestData['User']['email2']) )$requestData['User']['email2'] = $requestData['User']['email_subscribe'];
			if(!isset($requestData['User']['passwd2']) )$requestData['User']['passwd2'] = $requestData['User']['passwd_subscribe'];

            //On teste les options experts
            if(empty($requestData['User']['langs']) || empty($requestData['User']['countries']) || empty($requestData['User']['categories']) || empty($requestData['User']['consult'])){
                $this->Session->setFlash(__('Veuillez remplir toutes vos options experts'), 'flash_warning');
                return;
            }

            //On vérifie les champs du formulaire
            $champForm = array('email_subscribe','email2','passwd_subscribe','passwd2','pseudo','firstname','lastname','sexe','birthdate','country_id', 'indicatif_phone',
                'photo','lang_id','crop','phone_number','indicatif_phone2','phone_number2','audio','texte','consult','categories','countries','langs','siret','societe_statut','vat_num','city','postalcode','address', 'careers', 'profile','phone_operator');
            $champsRequired = array('email_subscribe','email2','passwd_subscribe','passwd2','pseudo','firstname','lastname','sexe','birthdate','country_id',
                                    'photo','lang_id','crop','phone_number', 'indicatif_phone', 'siret', 'city', 'postalcode', 'address','phone_operator');
            $requestData['User'] = Tools::checkFormField($requestData['User'], $champForm, $champsRequired, array('birthdate'));
            if($requestData['User'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire. N\'oubliez pas les champs obligatoires.'),'flash_warning');
                $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
            }

            //On charge les validateurs Agent
            $this->User->validate = $this->User->agent_validate;

            //On assemble le numéro de téléphone, s'il est renseigné
            if(!empty($requestData['User']['phone_number'])){
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_phone']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
                }
                $requestData['User']['phone_number'] = Tools::implodePhoneNumber($requestData['User']['indicatif_phone'], $requestData['User']['phone_number']);
            }

            if(!empty($requestData['User']['phone_number2'])){
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['User']['indicatif_phone2']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique du deuxième numéro de téléphone n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'users', 'action' =>'subscribe_agent'));
                }
                $requestData['User']['phone_number2'] = Tools::implodePhoneNumber($requestData['User']['indicatif_phone2'], $requestData['User']['phone_number2']);
            }

            //On vérifie l'email et le mot de passe
            if(!$this->requestSubscribe($requestData, 'agent')) return;

            //On vérifie le numero de téléphone
			$requestData['User']['phone_number'] = str_replace(' ','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('.','',$requestData['User']['phone_number']);
			$requestData['User']['phone_number'] = str_replace('-','',$requestData['User']['phone_number']);
            $requestData['User']['phone_number'] = $this->checkPhoneNumber($requestData['User']['phone_number'], 3);
            if($requestData['User']['phone_number'] === false)
                return;

            //On vérifie le 2eme numero de téléphone
            if (!empty($requestData['User']['phone_mobile'])){
				$requestData['User']['phone_mobile'] = str_replace(' ','',$requestData['User']['phone_mobile']);
				$requestData['User']['phone_mobile'] = str_replace('.','',$requestData['User']['phone_mobile']);
				$requestData['User']['phone_mobile'] = str_replace('-','',$requestData['User']['phone_mobile']);
                $requestData['User']['phone_mobile'] = $this->checkPhoneNumber($requestData['User']['phone_mobile'], 3);
                if($requestData['User']['phone_mobile'] === false)
                    return;
            }

            //On teste si les fichiers ont été uploade et qu'ils soient correct
            if(!$this->isUploadedFile($requestData['User']['photo'])){
                $this->Session->setFlash(__('Erreur dans le chargement de votre photo de présentation. Veuillez réessayer.'),'flash_error');
                return;
            }

            if ($requestData['User']['audio']['size'] != 0 && !$this->isUploadedFile($requestData['User']['audio'])){
                $this->Session->setFlash(__('Erreur dans le chargement de votre présentation audio. Veuillez réessayer.'),'flash_error');
                return;
            }
            //Type des fichiers
            if(!Tools::formatFile($this->allowed_mime_types,$requestData['User']['photo']['type'], 'Image')
                || ($requestData['User']['audio']['size'] != 0
                    && !Tools::formatFile($this->allowed_mime_types,$requestData['User']['audio']['type'], 'Audio')
                )){
                $this->Session->setFlash(__('Un des fichers est dans un format incorrect.'),'flash_warning');
                return;
            }
            //Fichier audio trop volumineux
            //error == 1 signifie que la taille du fichier est plus grande que celle de la conf php.    Voir php.ini
            if(($requestData['User']['audio']['size'] > Configure::read('Site.maxSizeAudio') || $requestData['User']['audio']['error'] == 1)){
                $this->Session->setFlash(__('Votre fichier audio est trop volumineux.'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['texte']) || empty($requestData['User']['texte']) || ctype_space($requestData['User']['texte'])){
                $this->Session->setFlash(__('Votre présentation est vide'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['careers']) || empty($requestData['User']['careers']) || ctype_space($requestData['User']['careers'])){
                $this->Session->setFlash(__('Votre parcours professionnel est vide'),'flash_warning');
                return;
            }

            //Si présentation empty ou que des espaces blancs
            if(!isset($requestData['User']['profile']) || empty($requestData['User']['profile']) || ctype_space($requestData['User']['profile'])){
                $this->Session->setFlash(__('Votre profil est vide'),'flash_warning');
                return;
            }

            //Initialise les paramètres de l'utilisateur
            $requestData = $this->initSubscribe($requestData);
            $requestData['User']['role'] = 'agent';
			$requestData['User']['date_new'] = date('Y-m-d H:i:s');
            $requestData['User']['agent_status'] = 'unavailable';
            $requestData['User']['has_photo'] = 1;
            $requestData['User']['agent_number'] = null;
            if($this->isUploadedFile($requestData['User']['audio']))
                $requestData['User']['has_audio'] = 1;

            //On transforme les données du champ consult
            //0 : Email     1 : Téléphone       2 : Chat
            foreach ($requestData['User']['consult'] as $value){
                if($value == 0)
                    $requestData['User']['consult_email'] = 1;
                elseif ($value == 1)
                    $requestData['User']['consult_phone'] = 1;
                else
                    $requestData['User']['consult_chat'] = 1;
            }

            //On transforme le tableau des langues parlées et des pays en String
            $requestData['User']['langs'] = implode(',',$requestData['User']['langs']);
			$requestData['User']['langs'] = str_replace('1','1,8,10,11,12',$requestData['User']['langs']);
            $requestData['User']['countries'] = implode(',',$requestData['User']['countries']);

            $this->User->create();
            $requestData['User']['lang_id'] = $this->Session->read('Config.id_lang');
            $requestData['User']['domain_id'] = $this->Session->read('Config.id_domain');


            if ($this->User->save($requestData)){
                //On charge le model pour save les catégories, statut et présentation
                $this->loadModel('CategoryUser');
                $this->loadModel('UserStateHistory');
                $this->loadModel('UserPresentLang');

                //On récupère l'id de l'user
                $idUser = $this->User->id;

                //CATGEORY (Univers) : On transforme les données de categories
                foreach ($requestData['User']['categories'] as $value){
                    $dataCategories[] = array('CategoryUser' => array('user_id' => $idUser, 'category_id' => $value));
                }
                $this->CategoryUser->saveMany($dataCategories);

                //UserStateHistory : on save le statut
                $dataUserState = array('UserStateHistory' => array('user_id' => $idUser, 'state' => 'unavailable'));
                $this->UserStateHistory->save($dataUserState);

                //UserPresentLang : on save la présentation
				$langid = $requestData['User']['lang_id'];
				if($requestData['User']['lang_id'] == 8 || $requestData['User']['lang_id'] == 10 || $requestData['User']['lang_id'] == 11 || $requestData['User']['lang_id'] == 12) $langid = 1;
                $dataUserPresent = array('UserPresentLang' => array('texte' => $requestData['User']['texte'], 'user_id' => $idUser, 'lang_id' => $langid, 'date_upd' => date('Y-m-d H:i:s')));//$requestData['User']['lang_id']
                $this->UserPresentLang->save($dataUserPresent);

                //On sauvegarde les fichiers (photo et présentation audio)
                $this->saveFile($requestData['User'], 'photo', false, true, $idUser);
                //On vérifie qu'il a bien posté une présentation audio
                if($this->isUploadedFile($requestData['User']['audio']))
                    $this->saveFile($requestData['User'], 'audio', false, true, $idUser);


                //Paramètre pour le mail de confirmation
                $paramEmail = array(
                    'email' => $requestData['User']['email'],
                    'urlConfirmation' => $this->linkGenerator('users','confirmation_agent',array(
                            'an' => 'null',
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                );

                //$this->sendEmail($requestData['User']['email'],'Validation de votre inscription sur '.Configure::read('Site.name'),'subscribe',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(151, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
                    'PARAM_EMAIL' =>    $requestData['User']['email'],
                    'PARAM_URLCONFIRMATION' => $this->linkGenerator('users','confirmation_agent',array(
                            'an' => 'null',
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                ));
				
				/* keep IP */
				$this->loadModel('UserIp');
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$check_ip = $this->UserIp->find('first',array(
					'conditions'    => array(
						'IP' => $ip_user,
						'user_id' => $this->User->id,
					),
					'recursive' => -1
				));
				if(count($check_ip)){
					$check_ip['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					 $this->UserIp->save($check_ip);
				}else{
					$this->UserIp->create();
					$requestDataIp = array();
					$requestDataIp['UserIp']['user_id'] = $this->User->id;
					$requestDataIp['UserIp']['date_conn'] = date('Y-m-d H:i:s');
					$requestDataIp['UserIp']['IP'] = $ip_user;
           			$ret = $this->UserIp->save($requestDataIp);	
				}

                $this->set('inscription', true);
            }else{
                //On récupère les erreurs de validation
                $errors = $this->User->validationErrors;
                $keys = array_keys($errors);
                //On affiche le premier message d'erreur
                $this->Session->setFlash(__($errors[$keys[0]][0]), 'flash_warning');
                //On vide le tableau d'erreur
                $this->User->validationErrors = array();
            }
        }
    }
	
	public function gads_subscribe(){
		
		$jsonData = file_get_contents("php://input");
		$data = json_decode($jsonData, true);
		
		$country_id = '';
		$firstname = '';
		$email = '';
		$lang_id = '';
		$domain_id = '';
		
		if($data){
			$gads_lead = $data['lead_id'];
			
			$country_id = 1;
			$lang_id = 1;
			$domain_id = 19;
			
			if($gads_lead == 'lead_id1'){
				$country_id = 1;
				$lang_id = 1;
				$domain_id = 19;
			}
			
			$form_data = $data['user_column_data'];
			foreach($form_data as $fdata){
				if($fdata['column_name'] == 'Full Name')
					$firstname = $fdata['string_value'];
				if($fdata['column_name'] == 'User Email')
					$email = $fdata['string_value'];
			}
			$requestData = array();
			$requestData['User'] = array();
			$requestData['User']['firstname'] = $firstname;
			$requestData['User']['email_subscribe'] = $email;
			$requestData['User']['email2'] = $email;
			$requestData['User']['passwd_subscribe'] = $email;
			$requestData['User']['passwd2'] = $email;
			$requestData['User']['country_id'] = $country_id;
			$requestData['User']['cgu'] = 1;
			
			$requestData = $this->initSubscribe($requestData);
            $requestData['User']['credit'] = 0;
			
            //On lui affecte un code personnel
            $this->loadModel('PersonalCode');
            $codes = $this->PersonalCode->find('all',array(
                'conditions' => array('PersonalCode.used' => 0),
                'limit' => 50,
                'order' => 'rand()'
            ));

            $requestData['User']['personal_code'] = $codes[rand(0,count($codes)-1)]['PersonalCode']['combinaisons'];
            //Le code n'est plus disponible
            $this->PersonalCode->updateAll(array('PersonalCode.used' => 1), array('PersonalCode.combinaisons' => $requestData['User']['personal_code']));
			
		    $this->User->create();

            $requestData['User']['lang_id'] = $lang_id;
            $requestData['User']['domain_id'] = $domain_id;

            /* On valide le compte */
            $requestData['User']['valid'] = 1;
			$requestData['User']['active'] = 1;
			$requestData['User']['emailConfirm'] = 1;
			
			$requestData['User']['source'] = 'Google Ads Form';
			
			 if ($this->User->save($requestData)){
                //Paramètre pour le mail de confirmation
                $paramEmail = array(
                    'email' => $requestData['User']['email'],
                    'urlConfirmation' => $this->linkGenerator('users','confirmation',array(
                            'pc' => $requestData['User']['personal_code'],
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                );

                //$this->sendEmail($requestData['User']['email'],'Validation de votre inscription sur '.Configure::read('Site.name'),'subscribe',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(181, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
                    'PARAM_EMAIL' => $requestData['User']['email'],
                    'PARAM_URLCONFIRMATION' => $this->linkGenerator('users','confirmation',array(
                            'pc' => $requestData['User']['personal_code'],
                            'mc' => Security::hash($requestData['User']['email'].'null',null,true)
                        ))
                ));
			 }
			
		}
		
		exit;
	}
	
}
<?php
App::uses('ExtranetController', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');

class AccountsController extends ExtranetController {
    protected $myRole = 'client';
    //On charge le model User pour tout le controller
    public $uses = array('User');
    public $components = array('Paginator');
    public $helpers = array('Paginator','Time');

    public function beforeRender()
    {
        parent::beforeRender();
    }

    public function beforeFilter(){
	
	
	
        parent::beforeFilter();
        $this->Auth->deny('index');
        $this->Auth->allow('add_favorite','remove_favorite', 'new_mail', 'updatemodeconsult','redir_cart_buy','profilremove','cart','buycreditpaiement','start','new_visio');

        $user = $this->Auth->user();

	///var_dump($user);exit;
	
	
        if (!empty($user) && $user['role'] === 'admin' && strpos($this->params['action'], 'admin') === 0)
            return true;
	
	/*
        if (!empty($user) && $user['role'] !== $this->myRole && !in_array($this->params['action'], array('add_favorite','remove_favorite', 'new_mail','new_visio')))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	
	*/
    }
    



    public function index(){
        $customer = $this->User->find('first', array(
            'conditions'    => array('User.id' => $this->Auth->user('id')),
            'recursive'     => -1
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
            'conditions'    => array('UserCreditHistory.user_id' => $customer['User']['id']),
            'order'         => 'UserCreditHistory.user_credit_history desc',
            'recursive'     => -1
        ));

        //S'il y a une communication
        if(!empty($lastCom)){
            //On récupère l'agent
            $agent = $this->User->getAgent($lastCom['UserCreditHistory']['agent_id']);

            $this->set(compact('agent'));
        }

        //Les experts favoris
        $this->loadModel('Favorite');
        $agents = $this->Favorite->find('all', array(
            'fields'        => array(),
            'conditions'    => array('Favorite.user_id' => $this->Auth->user('id')),
            'limit'         => 5,
            'recursive'     => 0
        ));

		//On récupère les photos des agents favoris et calcule l'inactivité de l'agent
        foreach($agents as $indice => $val){
            $photo = $this->mediaAgentExist($val['Agent']['agent_number'],'Image');
            //Pas de photo, photo par défaut
            if($photo === false)
                $agents[$indice]['Agent']['photo'] = '/'.Configure::read('Site.defaultImage');
            else
                $agents[$indice]['Agent']['photo'] = '/'.$photo;
        }

        //Les experts sur lesquels on peut mettre un avis
        $agentsFavorite = $this->getAgentFavorite();

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
		$this->set('cond_cu', $cu_a_valide);

		$this->loadModel('GiftOrder');
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.beneficiary_id' => $this->Auth->user('id'),'GiftOrder.date_validity >=' => date('Y-m-d H:i:s'),'GiftOrder.valid' => 2),
					'recursive' => -1,
				));

		$this->set(compact('customer', 'lastCom', 'agents', 'agentsFavorite', 'gift_order'));
    }

    //Modifie le compte (mail, passwd) du client
    public function editAccountCompte(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formualaire
            $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('email', 'passwd','passwd2'), array('email'));
            if($requestData['Account'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'accounts', 'action' =>'profil', 'tab' => 'profil'));
            }
				//Vérification sur l'adresse mail
				if(!filter_var($requestData['Account']['email'], FILTER_VALIDATE_EMAIL)){
					$this->Session->setFlash(__('Email invalide.'),'flash_error');
					$this->redirect(array('controller' => 'accounts', 'action' =>'profil', 'tab' => 'profil'));
				}

				$this->_editCompte('Account', $requestData);
        }else

            $this->redirect(array('controller' => 'accounts', 'action' =>'profil'));
    }

    //Modifie les infos d'un client
    public function editAccountInfos(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formualaire
            $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('firstname', 'lastname','birthdate'));
            if($requestData['Account'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
            }

			if(substr_count($requestData['Account']['firstname'],'@')){
				$this->Session->setFlash(__('Pseudo incorrect'),'flash_error');
				$this->redirect(array('controller' => 'accounts', 'action' =>'profil'));
			}else{

				//On sauvegarde les données
				$this->User->id = $this->Auth->user('id');
				//On active le compte
				$requestData['Account']['id'] = $this->User->id;
				$requestData['Account']['valid'] = 1;
				$requestData['Account']['date_upd'] = date('Y-m-d H:i:s');
				if($this->User->save($requestData['Account'])){
					if($this->Auth->user('valid') == 1){
						CakeSession::write(array('Auth.User.firstname' => $requestData['Account']['firstname']));
						$this->Session->setFlash(__('Vos données ont été mises à jour.'), 'flash_success');
					}else{
						CakeSession::write(array('Auth.User.valid' => 1, 'Auth.User.firstname' => $requestData['Account']['firstname']));
						$this->Session->setFlash(__('Vos données ont été mises à jour. Votre compte est activé.'), 'flash_success');
					}
				}else
					$this->Session->setFlash(__('Erreur lors de la sauvegarde de vos données'), 'flash_error');
			}
        }
        $this->redirect(array('controller' => 'accounts', 'action' =>'index'));
    }

    //Modifie les détails d'un client
    public function editAccountDetails(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On vérifie les champs du formualaire
            $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('address', 'postalcode','city', 'optin', 'save_bank_card', 'phone_number', 'country_id', 'indicatif_phone'), array('country_id'));
            if($requestData['Account'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'accounts', 'action' => 'profil', 'tab' => 'details'));
            }

            //On vérifie le numero de téléphone, s'il y a en un
            if(!empty($requestData['Account']['phone_number'])){
                $this->loadModel('Country');
                //Indicatif invalide
                $flag_tel = $this->Country->allowedIndicatif($requestData['Account']['indicatif_phone']);
                if($flag_tel === -1 || !$flag_tel){
                    $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                    $this->redirect(array('controller' => 'accounts', 'action' => 'profil', 'tab' => 'details'));
                }
                //$requestData['Account']['phone_number'] = $requestData['Account']['indicatif_phone'].$requestData['Account']['phone_number'];
                //On assemble pour l'indicatif et le numéro de tel
                $requestData['Account']['phone_number'] = Tools::implodePhoneNumber($requestData['Account']['indicatif_phone'], $requestData['Account']['phone_number']);
                $requestData['Account']['phone_number'] = $this->phoneNumberValid($requestData['Account']['phone_number'], 3);
                if($requestData['Account']['phone_number'] === false)
                    $this->redirect(array('controller' => 'accounts', 'action' => 'profil', 'tab' => 'details'));
            }

            //On sauvegarde les données
            $this->User->id = $this->Auth->user('id');
            $saveData = $requestData['Account'];
			$saveData['id'] = $this->User->id;
            $saveData['date_upd'] = date('Y-m-d H:i:s');
            if($this->User->save($saveData))
                $this->Session->setFlash(__('Vos données ont été mises à jour.'), 'flash_success');
            else{
                $this->Session->setFlash(__('Erreur lors de la sauvegarde de vos données'), 'flash_error');
                $this->redirect(array('controller' => 'accounts', 'action' => 'profil', 'tab' => 'details'));
            }
        }
        if($this->Auth->user('valid') == 1)
            $this->redirect(array('controller' => 'accounts', 'action' =>'index'));
        else
            $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
    }

    //Affiche le profil client
    public function profil($tab='infos')
    {
	App::uses('LanguageHelper', 'View/Helper');
        //Pour l'onglet
        if(isset($this->request['named']['tab']))
            $tab = $this->request['named']['tab'];

        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception("Erreur de sécurité !", 1);

        /* On charge le client */
        $userData = $this->User->find('first',array(
            'conditions' => array('id' => (int)$user['id']),
            'recursive' => -1
        ));
        //On change la clé du tableau
        $userData['Account'] = $userData['User'];
        unset($userData['User']);

        /* On vérifie que l'utilisateur est bien dans son role */
        /*
	if ($userData['Account']['role'] != $this->myRole)
            throw new Exception("Erreur de sécurité", 1);
	*/
	
        $this->loadModel('UserCountry');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));

        //Comtpe client non valid
        if(!$this->User->accountValid($this->Auth->user('id'))){
            $this->layout = 'user_novalid';
            $this->set('valid',false);
        }

        //On récupère l'indicatif tel et le numéro de tel sans l'indicatif
        $this->loadModel('Country');
        $userPhone = $this->Country->getIndicatifOfPhone($userData['Account']['phone_number']);
        $userData['Account']['indicatif_phone'] = $userPhone['indicatif'];
        $userData['Account']['phone_number'] = $userPhone['phone_number'];



        /* On envoie les données pour les valeurs par défaut du formulaire */
        unset($userData['Account']['passwd']);
        unset($userData['Account']['id']);


		$this->loadModel('GiftOrder');
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.beneficiary_id' => $this->Auth->user('id'),'GiftOrder.date_validity >=' => date('Y-m-d H:i:s'),'GiftOrder.valid' => 2),
					'recursive' => -1,
				));
		 $limit = $this->User->field('limit_credit', array('User.id' => $this->Auth->user('id')));
        $total_amount_hebdo = $this->getUserCreditHebdoEncours();
        //$total_buyable = (($limit - $total_credits_hebdo)>0)?($limit - $total_credits_hebdo):0;
        $total_buyable = (($limit - $total_amount_hebdo)>0)?($limit - $total_amount_hebdo):0;
		$total_buyable = number_format($total_buyable,2,'.','');
		$total_currency = $this->Session->read('Config.devise');
		$total_credits_hebdo="";
        $this->set(compact('userData', 'tab', 'gift_order','limit', 'total_credits_hebdo','total_amount_hebdo', 'total_buyable','total_currency'));
        $this->request->data = $userData;

    }

    //Permet de rédiger un avis sur un voyant
    public function review($expert = 0){

        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception("Erreur de sécurité !", 1);

        /* On charge le client */
        $this->User->id = (int)$user['id'];
        $userData = $this->User->read();

        /* On vérifie que l'utilisateur est bien dans son role */
        /*
	if ($userData['User']['role'] != $this->myRole)
            $this->redirec(array('controller' => 'home', 'action' => 'index'));

	*/
	
        //Lorsqu'un client post un avis-------------------------------------------------------------------
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //Pour le retour sur la fiche agent
            if(isset($requestData['Account']['url']))
                $url = $requestData['Account']['url'];
            else
                $url = array('controller' => 'accounts', 'action' => 'review');

            //Vérification des champs du formulaire

            $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('agent_number', 'content', 'rate'), array('agent_number','content'));
            if($requestData['Account'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_warning');

                $this->redirect($url);
            }

            // check max length avix <1000
            if(strlen($requestData['Account']['content'])>1000 ) {
                $this->Session->setFlash(__('Longueur avis dépassés 1000 caractères.'),'flash_warning');

                $this->redirect($url);
            }

            //S'il y a bien une note et qu'elle est négative
            if(isset($requestData['Account']['rate']) && $requestData['Account']['rate'] <= 0){
                $this->Session->setFlash(__('Veuillez choisir une note pour l\'expert.'),'flash_warning');
                $this->redirect($url);
            }
            //Si un petit malin, met une note supérieur au max
            if($requestData['Account']['rate'] > 5)
                $requestData['Account']['rate'] = 5;

            $requestData['Account']['lang_id'] = $this->Session->read('Config.id_lang');
            $requestData['Account']['user_id'] = $this->Auth->user('id');
            $requestData['Account']['date_add'] = date('Y-m-d H:i:s');
            $requestData['Account']['status'] = -1;
            //On récupère l'id de l'agent
            $idAgent = $this->User->field('id',array(
                'agent_number' => $requestData['Account']['agent_number'],
                'deleted' => 0,
                'active' => 1
            ));

            //Si l'agent n'a pas été trouvé
            if(!$idAgent){
                $this->Session->setFlash(__('Erreur lors de la sauvegarde de votre avis.'),'flash_warning');
                $this->redirect($url);
            }

            $requestData['Account']['agent_id'] = $idAgent;

            $requestData['Review'] = $requestData['Account'];
            unset($requestData['Account']);

			//calcul pourcentage
			$requestData['Review']['pourcent'] = number_format($requestData['Review']['rate'] * 100 / 5,2);

            //On charge le model review
            $this->loadModel('Review');

            $this->Review->create();
            if($this->Review->save($requestData)){
                $this->Session->setFlash(__('Merci. Votre avis est enregistré.'),'flash_success');
                $this->redirect($url);
            }
            else {
                $this->Session->setFlash(__('Erreur rencontrée lors de la sauvegarde de votre avis.'),'flash_error');
                $this->redirect($url);
            }
        }

        //Quand il arrive sur la page de rédaction d'un avis--------------------------------------------------------------------------------------------------------
        $voyants = $this->getAgentFavorite(true);

        //S'il y a des voyants
        if(!empty($voyants)){
            //On assoicie le pseudo avec le numero d'agent
            foreach ($voyants as $number => $pseudo){
                $voyants[$number] = $pseudo.' - '.$number;
            }
        }

        $this->set('voyants', $voyants);
        $this->set(compact('expert'));
    }

    //L'historique des communications du client
    public function history(){
        $this->_history('client');

        //On met à jour le crédit
        $this->User->id = $this->Auth->user('id');
        $this->Session->write('Auth.User.credit', $this->User->field('credit'));
    }

    public function chat_history(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

            //On va chercher les messages du chat
            $this->loadModel('ChatMessage');
            $messages = $this->ChatMessage->find('all', array(
                'fields' => array('ChatMessage.*', 'User.pseudo'),
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
            $this->set(array('title' => __('Chat'), 'content' => $response->body(), 'button' => __('Fermer')));
            $response = $this->render('/Elements/modal');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        //On récupére toutes les communications
        $this->loadModel('Chat');
        $this->Paginator->settings = array(
            'fields' => array('Chat.id', 'Chat.to_id', 'Chat.consult_date_start', 'User.*'),
            'conditions' => array(
                'Chat.from_id' => $this->Auth->user('id'),
                'Chat.date_end !=' => null,
                'Chat.consult_date_start !=' => null,
                'Chat.consult_date_end !=' => null
            ),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Chat.to_id')
                )
            ),
            'order' => 'Chat.consult_date_start desc',
            'recursive' => -1,
            'limit' => 15
        );

        $chats = $this->Paginator->paginate($this->Chat);

        $this->set(compact('chats'));
    }

    public function archive_chat(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['param']) || !is_numeric($requestData['param']))
                $this->jsonRender(array('return' => false));

            $param=$requestData['param'];

            //On va chercher les messages du chat
            $this->loadModel('Chat');
            $messages = $this->Chat->find('first', array(
                'fields' => array('Chat.*'),
                'conditions' => array('Chat.id' => $param),
                'recursive' => -1
            ));

            //Si aucun messages
            if(empty($messages)){
                $this->jsonRender(array('return' => false,'msg'=>__('L\'historique de la conversation est introuvable.')));
            }

            $this->Chat->updateAll(array('Chat.archive' => 1), array('Chat.id' => $param));
            $this->jsonRender(array('return' => true));

        }
    }

    //Permet de définir la limite de conso
    public function limits(){
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //On check le formulaire
            if(!isset($requestData['Account']['limit_credit'])){
                $this->Session->setFlash(__('Erreur dans le formulaire.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'limits'));
            }

            //Si la valeur est egale à 0
            if(empty($requestData['Account']['limit_credit']) || $requestData['Account']['limit_credit'] < 0)
                $requestData['Account']['limit_credit'] = null;

            //On save la limite
            $this->User->id = $this->Auth->user('id');
            if($this->User->saveField('limit_credit', $requestData['Account']['limit_credit'])){
                $this->Session->setFlash(__('Votre limite a été mise à jour.'), 'flash_success');
                $this->redirect(array('controller' => 'accounts', 'action' => 'limits'));
            }
            else{
                $this->Session->setFlash(__('La mise à jour de la limite a échoué.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'limits'));
            };
        }

        $limit = $this->User->field('limit_credit', array('User.id' => $this->Auth->user('id')));
        $total_amount_hebdo = $this->getUserCreditHebdoEncours();
        //$total_buyable = (($limit - $total_credits_hebdo)>0)?($limit - $total_credits_hebdo):0;
        $total_buyable = (($limit - $total_amount_hebdo)>0)?($limit - $total_amount_hebdo):0;
		$total_buyable = number_format($total_buyable,2,'.','');
		$total_currency = $this->Session->read('Config.devise');
        $this->set(compact('limit', 'total_credits_hebdo','total_amount_hebdo', 'total_buyable','total_currency'));
    }

    //Permet d'ajouter un favoris en AJAX
    public function add_favorite($idAgent){
        if($this->request->is('ajax')){
            $this->layout = '';
            //Utilisateur client connecté ??
            if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
                $content = $this->render('/Elements/login_modal');
                $this->set(array('title' => __('Accès client'), 'content' => $content, 'button' => __('Annuler')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('html' => $response->body()));
            }

            //Un agent valide ??
            $agent = $this->User->getAgent($idAgent);
            if($agent === false){
                $content = __('Cet expert n\'existe pas.');
                $this->set(array('title' => __('Expert introuvable'), 'content' => $content, 'button' => __('Ok')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('html' => $response->body()));
            }

            //Agent déjà dans les favoris
            $this->loadModel('Favorite');
            $row = $this->Favorite->find('first', array(
                'conditions'    => array('user_id' => $this->Auth->user('id'), 'agent_id' => $idAgent),
                'recursive'     => -1
            ));

            if(!empty($row)){
                $content = __('Cet expert est déjà enregistré dans vos favoris.');
                $this->set(array('title' => __('Expert favoris'), 'content' => $content, 'button' => __('Ok')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('html' => $response->body()));
            }

            //On ajoute l'agent aux favoris de l'utilisateur
            $this->Favorite->create();
            if($this->Favorite->save(array(
                'user_id' => $this->Auth->user('id'),
                'agent_id' => $idAgent
            )))
                $content = __('L\'expert a été ajouté(e) à vos Experts favoris.');
            else
                $content = __('Erreur lors de l\'enregistrement de l\'expert dans vos favoris.');

            $this->set(array('title' => __('Expert favoris'), 'content' => $content, 'button' => __('Ok')));
            $response = $this->render('/Elements/modal');
            $this->jsonRender(array('html' => $response->body()));
        }
    }

	public function remove_favorite($idAgent){
        if($this->request->is('ajax')){
            $this->layout = '';
            //Utilisateur client connecté ??
            if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
                $content = $this->render('/Elements/login_modal');
                $this->set(array('title' => __('Accès client'), 'content' => $content, 'button' => __('Annuler')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('html' => $response->body()));
            }

            //Un agent valide ??
            $agent = $this->User->getAgent($idAgent);
            if($agent === false){
                $content = __('Cet expert n\'existe pas.');
                $this->set(array('title' => __('Expert introuvable'), 'content' => $content, 'button' => __('Ok')));
                $response = $this->render('/Elements/modal');
                $this->jsonRender(array('html' => $response->body()));
            }

            //Agent déjà dans les favoris
           /* $this->loadModel('Favorite');
            $row = $this->Favorite->find('first', array(
                'conditions'    => array('user_id' => $this->Auth->user('id'), 'agent_id' => $idAgent),
                'recursive'     => -1
            ));*/


            /*if($this->Favorite->remove(array(
                'user_id' => $this->Auth->user('id'),
                'agent_id' => $idAgent
            )))*/

			$dbb_r = new DATABASE_CONFIG();
			$dbb_s = $dbb_r->default;
			$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database']);
			if($mysqli_s->query("DELETE from favorites WHERE user_id = '{$this->Auth->user('id')}' and agent_id = '{$idAgent}'"))
                $content = __('L\'expert a été supprimé(e) de vos Experts favoris.');
            else
                $content = __('Erreur lors de l\'enregistrement de l\'expert dans vos favoris.');

            $this->set(array('title' => __('Expert favoris'), 'content' => $content, 'button' => __('Ok')));
            $response = $this->render('/Elements/modal');
            $this->jsonRender(array('html' => $response->body()));
			$mysqli_s->close();
        }
    }


    //Permet à l'utilisateur d'afficher ses experts préférés et d'en ajouter
    public function favorites(){
        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception("Erreur de sécurité !", 1);

        /* On vérifie que l'utilisateur est bien dans son role */
	/*
        if ($user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	*/
	
        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        //Suppression d'un agent des favoris
        if(isset($this->params['agent_number'])){
            $this->deleteFavorite($this->params['agent_number']);
            $this->redirect(array('controller' => 'accounts', 'action' => 'favorites'));
        }

        //On charge les models
        $this->loadModel('Favorite');
        $this->loadModel('UserCreditLastHistory');

        //Lors d'un ajout d'un voyant aux favoris-----------------------------------------
        if($this->request->is('post')){
            $requestData = $this->request->data;

            //Vérification des champs du formulaire
            $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('agent_id'), array('agent_id'));
            if($requestData['Account'] === false){
                $this->Session->setFlash(__('Veuillez remplir correctement le formulaire.'),'flash_error');
                $this->redirect(array('controller' => 'accounts', 'action' => 'favorites'));
            }

            $requestData['Favorite'] = $requestData['Account'];
            unset($requestData['Account']);

            //On initialise les données
            $requestData['Favorite']['user_id'] = $this->Auth->user('id');
            $this->Favorite->create();
            if($this->Favorite->save($requestData))
                $this->Session->setFlash(__('L\'expert a été ajouté à vos favoris.'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'ajout de l\'expert dans vos favoris.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'favorites'));
        }



        //Les agents active et favoris de l'user
        $this->Paginator->settings = array(
            'conditions' => array('user_id' => $this->Auth->user('id'), 'Agent.active' => 1,'Agent.deleted' => 0),
            'order' => array('Favorite.date_add' => 'desc'),
            'limit' => 10
        );

        $agentsFavoris = $this->Paginator->paginate($this->Favorite);

        //On récupère les photos des agents favoris et calcule l'inactivité de l'agent
        foreach($agentsFavoris as $indice => $val){
            $photo = $this->mediaAgentExist($val['Agent']['agent_number'],'Image');
            //Pas de photo, photo par défaut
            if($photo === false)
                $agentsFavoris[$indice]['Agent']['photo'] = '/'.Configure::read('Site.defaultImage');
            else
                $agentsFavoris[$indice]['Agent']['photo'] = '/'.$photo;
        }

        //On récupère les agents avec qui l'user a eu affaire dernièrement.
        $agents = $this->UserCreditLastHistory->find('list',array(
            'fields' => array('agent_id'),
            'conditions' => array('users_id' => $this->Auth->user('id'))
        ));

        //On enlève les doublons
        $agents = array_unique($agents);

        //On récupère les pseudos et codes agents pour chaque voyant de la liste
        $voyants = $this->User->find('list',array(
            'fields' => array('agent_number', 'pseudo','id'),
            'conditions' => array('id' => $agents, 'deleted' => 0, 'active' => 1, 'role' => 'agent'),
            'recursive' => -1
        ));

        //S'il y a des voyants
        if(!empty($voyants)){
            //On associe le pseudo avec le numero d'agent
            foreach ($voyants as $id => $val){
                $key = array_keys($val)[0];
                $voyants[$id] = $val[$key].' - '.$key;
            }

            //On récupère TOUT les voyants favoris car $agentsFavoris ne contient pas forcément tout les voyants favoris à cause du Paginator
            $agentsFavorisID = $this->Favorite->find('all',array(
                'fields' => array('Agent.id'),
                'conditions' => array('user_id' => $this->Auth->user('id'))
            ));

            //On supprime les voyants qui sont déjà favoris
            foreach ($agentsFavorisID as $val){
                if(isset($voyants[$val['Agent']['id']]))
                    unset($voyants[$val['Agent']['id']]);
            }
        }

        $this->set(compact('agentsFavoris','voyants'));
    }

    private function deleteFavorite($agent_number){
        //On charge le model
        $this->loadModel('Favorite');
        //On récupère l'id du voyant
        $agent_id = $this->User->field('id',array('agent_number' => $agent_number, 'role' => 'agent'));

        //Agent introuvable
        if(!$agent_id)
            $this->Session->setFlash(__('Impossible de trouver l\'expert.'), 'flash_warning');
        elseif($this->Favorite->deleteAll(array('user_id' => $this->Auth->user('id'), 'agent_id' => $agent_id),false))
            $this->Session->setFlash(__('L\'expert a été supprimé de vos favoris.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur dans la suppression de l\'expert de vos favoris.'),'flash_warning');
    }

	  public function cart(){
		 $pack = '';
		  $promo = '';
		  $promo_title = '';
		  $is_promo_total = '';

		  $this->loadModel('Voucher');
		  $this->loadModel('GiftOrder');


		  if($this->request->is('post') || $this->Session->read('cart_product')){
			$requestData = $this->request->data;

			if(!$this->request->is('post') && $this->Session->read('cart_product')){
			   $requestData = array();
			   $requestData['Account'] = array();
			   $requestData['Account']['produit'] = $this->Session->read('cart_product');
			   $requestData['Account']['voucher'] = $this->Session->read('cart_voucher');
		    }

			$requestData['Account'] = Tools::checkFormField($requestData['Account'], array('produit','voucher'), array('produit'));
			if($requestData['Account'] === false){
				$this->Session->setFlash(__('Erreur lors de la sélection du pack'), 'flash_warning');
				$this->redirect(array('controller' => 'products', 'action' => 'tarif'));
			}/*else{
				$this->Session->write('cart_product', $requestData['Account']['produit']);
				$this->Session->write('cart_voucher', $requestData['Account']['voucher']);
			}*/


			 /* On créé le panier si nécessaire, ou on récupère */
			$this->loadModel('Cart');
			if ($this->Session->check('User.id_cart')){
				$id_cart = $this->Session->read('User.id_cart');
				$this->Cart->id = $id_cart;
			}else{
				$this->Cart->create();
			}

				$voucher_code = isset($requestData['Account']['voucher'])?$requestData['Account']['voucher']:false;
				$this->Cart->save(array(
					'user_id'       =>      $this->Auth->user('id'),
					'product_id'    =>      $requestData['Account']['produit'],
					'lang_id'       =>      $this->Session->read('Config.id_lang'),
					'country_id'    =>      $this->Session->read('Config.id_country'),
					'voucher_code'  =>      $voucher_code
				));
				$id_cart = $this->Cart->id;
				if ($this->Cart->id){
					$this->Session->write('User.id_cart', $this->Cart->id);
					$this->Session->write('User.save_id_cart_for_validation', $this->Cart->id);
				}

				//save cart loose
				$this->loadModel('CartLoose');

				$cartLoose = $this->CartLoose->find('all', array(
										'conditions' => array(
											'CartLoose.id_cart' => $id_cart,
										),
										array('recursive' => -1)
				));

				if(!$cartLoose){
					$this->CartLoose->create();
					$this->CartLoose->save(array(
						'id_cart'    =>      $id_cart,
						'id_user'    =>      $this->Auth->user('id'),
						'date_cart'  =>      date('Y-m-d H:i:s'),
					));
				}




			//On récupère le pack produit
			$this->loadModel('Product');
			$pack = $this->Product->find('first',array(
					'fields' => array('Product.*', 'ProductLang.*'),
					'conditions' => array(
						'Product.id' => $requestData['Account']['produit'],
					),
					'joins' => array(
						array('table' => 'product_langs',
							  'alias' => 'ProductLang',
							  'type' => 'inner',
							  'conditions' => array(
								  'ProductLang.lang_id = '.$this->Session->read('Config.id_lang'),
								  'ProductLang.product_id = Product.id'
							  )
						)
					),
					'recursive' => -1,
				));

			 //check si promo public
			 if($requestData['Account']['voucher']){
					 $voucher_public = $this->Voucher->find('first',array(
								'conditions' => array(
									'Voucher.active' => 1,
									'Voucher.public' => 1,
								),
						 		'order' => array('id DESC'),
								'recursive' => -1,
							));
				  if($voucher_public ){//je repush la promo public
					  $promo = $voucher_public['Voucher']["code"];
					  $promo_title = $voucher_public['Voucher']["title"];
					  $label = '';
						if($this->Session->read('Config.id_country') == 1 && $voucher_public['Voucher']['label_fr']) $label = $voucher_public['Voucher']['label_fr'];
						if($this->Session->read('Config.id_country') == 3 && $voucher_public['Voucher']['label_ch']) $label = $voucher_public['Voucher']['label_ch'];
						if($this->Session->read('Config.id_country') == 4 && $voucher_public['Voucher']['label_be']) $label = $voucher_public['Voucher']['label_be'];
						if($this->Session->read('Config.id_country') == 5 && $voucher_public['Voucher']['label_lu']) $label = $voucher_public['Voucher']['label_lu'];
						if($this->Session->read('Config.id_country') == 13 && $voucher_public['Voucher']['label_ca']) $label = $voucher_public['Voucher']['label_ca'];
						$pack['Product']['promo_credit'] = (int)$voucher_public['Voucher']['credit'];
						$pack['Product']['promo_label'] = $label;
						$pack['Product']['promo_amount'] = (int)voucher_public['Voucher']['amount'];
						$pack['Product']['promo_percent'] = (int)$voucher_public['Voucher']['percent'];
				  }
			 }

			$vouchers = '';
			 $is_gift_voucher = false;
			if($requestData['Account']['voucher']){

					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.code' => $requestData['Account']['voucher'],
						),
						'recursive' => -1,
					));
					if(!$vouchers){
						$gift = $this->GiftOrder->find('first',array(
							'conditions' => array(
								'GiftOrder.valid' => 1,
								'GiftOrder.date_validity >=' => date('Y-m-d H:i:s'),
								'GiftOrder.code' => $requestData['Account']['voucher'],
							),
							'recursive' => -1,
						));
						if(!$gift){
							$gift = $this->GiftOrder->find('first',array(
							'conditions' => array(
								'GiftOrder.valid' => 2,
								'GiftOrder.date_validity >=' => date('Y-m-d H:i:s'),
								'GiftOrder.code' => $requestData['Account']['voucher'],
							),
							'recursive' => -1,
							));
						}
						if($gift)$is_gift_voucher = true;
					}
			}else{

				if($this->Session->read('promo_landing')){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.nobuyer' => 0,
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
			}
			if(!$is_gift_voucher) {
				// if(isset($requestData['Account']['voucher']) && !empty($requestData['Account']['voucher']) && !$vouchers) {
				//	$this->Session->setFlash('Le bon de réduction que vous avez indiqué n\'est pas valide.');
				//}

				foreach($vouchers as $voucher){
					//$promo = '';
					//$promo_title = '';
					$rightToUse_once = false;
					$prod_promo = array();
					$produit_promo_select = '';

					//Le client peut-il l'utiliser ??
					
					if($this->Auth->user('id'))
					 $rightToUse = $this->Voucher->rightToUse($voucher['Voucher']["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $pack['Product']['id']);
						 else
					$rightToUse = $this->Voucher->rightToUsePublic($voucher['Voucher']["code"], $pack['Product']['id']);

					if($this->Auth->user('id') && $voucher['Voucher']["nobuyer"]){
						$this->loadModel('Order');
						$order_account = $this->Order->find('first',array(
								'conditions' => array(
									'Order.user_id' => $this->Auth->user('id'),
									'Order.valid' => 1,
								),
								'recursive' => -1,
							));
						if($order_account)$rightToUse=false;
					}
					if(!$this->Auth->user('id') && $voucher['Voucher']["nobuyer"]){
						//$rightToUse=false;
					}

					$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
					$is_coupon_buy_only = $voucher['Voucher']['buy_only'];

					if($requestData['Account']['voucher']) {
						  //kill promo session
						//$this->Session->write('promo_client', '');
						//check IP
						$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
						$list_block_ip = explode(',',$voucher['Voucher']['ips']);
						if(in_array($ip_user,$list_block_ip)){
							$rightToUse = false;
							$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client.'), 'flash_warning');

						}

						if ((!$rightToUse || $coupon == false)  && !$is_coupon_buy_only && $voucher['Voucher']["code"] != $voucher_public['Voucher']["code"]){
							$this->Session->setFlash('Le bon de réduction que vous avez indiqué n\'est pas valide.');
						}
					}

					 if($rightToUse){
						 $rightToUse_once = true;

							$label = '';
							if($this->Session->read('Config.id_country') == 1 && $voucher['Voucher']['label_fr']) $label = $voucher['Voucher']['label_fr'];
							if($this->Session->read('Config.id_country') == 3 && $voucher['Voucher']['label_ch']) $label = $voucher['Voucher']['label_ch'];
							if($this->Session->read('Config.id_country') == 4 && $voucher['Voucher']['label_be']) $label = $voucher['Voucher']['label_be'];
							if($this->Session->read('Config.id_country') == 5 && $voucher['Voucher']['label_lu']) $label = $voucher['Voucher']['label_lu'];
							if($this->Session->read('Config.id_country') == 13 && $voucher['Voucher']['label_ca']) $label = $voucher['Voucher']['label_ca'];
							$pack['Product']['promo_credit'] = (int)$voucher['Voucher']['credit'];
							$pack['Product']['promo_label'] = $label;
							$pack['Product']['promo_amount'] = (int)$voucher['Voucher']['amount'];
							$pack['Product']['promo_percent'] = (int)$voucher['Voucher']['percent'];
							$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
							if(!$produit_promo_select){
								$produit_promo_select = $pack['Product']['id'];
							}

							$promo = $voucher['Voucher']["code"];
							$promo_title = $voucher['Voucher']["title"];
							$this->Session->write('promo_client', $promo);
						 	$this->Session->write('cart_product', $requestData['Account']['produit']);
							$this->Session->write('cart_voucher', $requestData['Account']['voucher']);
					}else{
						 $pack['Product']['promo_credit'] = '';
						 $pack['Product']['promo_label'] = '';
						 $pack['Product']['promo_amount'] = '';
						 $pack['Product']['promo_percent'] = '';
						 $promo = '';

					 }

				}
			}else{
				if(!$gift) {
					$this->Session->setFlash(__('Le carte cadeau que vous avez indiqué n\'est pas valide.'));
				}else{

					$pack['Product']['promo_credit'] = 0;
					$pack['Product']['promo_label'] = 'E-carte Cadeau Spiriteo';
					if($gift['GiftOrder']['valid'] == 2){
					$pack['Product']['promo_amount'] = (int)$gift['GiftOrder']['sold'];
					}else{
						$pack['Product']['promo_amount'] = (int)$gift['GiftOrder']['amount'];
					}

					$pack['Product']['promo_percent'] = 0;
					//$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
					//if(!$produit_promo_select){
						$produit_promo_select = $pack['Product']['id'];
					//}

					$promo = $gift['GiftOrder']["code"];
					$promo_title = 'E-carte '.$gift['GiftOrder']["amount"];
					//$this->Session->write('promo_client', $promo);
				}
			}
			$is_promo_total = 0;

        }
		$this->set(compact('pack','promo','promo_title','is_promo_total','is_gift_voucher'));

    }

    public function buycredits(){
        $user = $this->Session->read('Auth.User');
        if (!isset($user['id']))
            throw new Exception(__('Erreur de sécurité !'), 1);

        /* On vérifie que l'utilisateur est bien dans son role */
	/*
        if ($user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

	*/
        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();


        //On récupère les packs produits
        $this->loadModel('Product');
        $this->Paginator->settings = array(
            'fields' => array('Product.*', 'ProductLang.*'),
            'conditions' => array('Product.active' => 1,'Product.credits >' => 0, 'Product.country_id' => $this->Session->read('Config.id_country')),
            'joins' => array(
                array('table' => 'product_langs',
                      'alias' => 'ProductLang',
                      'type' => 'inner',
                      'conditions' => array(
                          'ProductLang.lang_id = '.$this->Session->read('Config.id_lang'),
                          'ProductLang.product_id = Product.id'
                      )
                )
            ),
            'recursive' => -1,
            'limit' => 15
        );

        $packs = $this->Paginator->paginate($this->Product);

		//check si promo
			$this->loadModel('Voucher');
			if($this->Session->read('promo_landing') || $this->Session->read('promo_client')){
				$code_promo = '';
				if($this->Session->read('promo_landing'))$code_promo = $this->Session->read('promo_landing');
				if($this->Session->read('promo_client'))$code_promo = $this->Session->read('promo_client');
				$vouchers = $this->Voucher->find('all',array(
					'conditions' => array(
						'Voucher.active' => 1,
						'Voucher.code' => $code_promo,
					),
					'recursive' => -1,
				));
			}else{
				//check si promo public
				$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 1,
							'Voucher.validity_end >=' => date('Y-m-d H:i:s'),
						),
						'recursive' => -1,
					));

				//check si promo pour account
				if(!$vouchers && $user['id']){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 0,
							'Voucher.customer' => 1,
							'Voucher.show' => 1,
							'Voucher.validity_end >=' => date('Y-m-d H:i:s'),
						),
						'recursive' => -1,
					));
				}

				//check si promo pour buyer
				$this->loadModel('Order');
				$order_account = $this->Order->find('first',array(
						'conditions' => array(
							'Order.user_id' => $user['id'],
							'Order.valid' => 1,
						),
						'recursive' => -1,
					));
				if(!$vouchers && $user['id'] && $order_account){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 0,
							'Voucher.buyer' => 1,
						),
						'recursive' => -1,
					));
				}

				//check promo du client
				if(!$vouchers && $this->Auth->user('personal_code')){
					$vouchers = $this->Voucher->find('all', array(
						'fields'        => array(),
						'conditions'    => array('Voucher.population like' => '%'.$this->Auth->user('personal_code').'%', 'Voucher.active'=>1,'Voucher.buy_only' => 0,'Voucher.show' => 1),
						'limit'         => -1,
						'order'			=> array('Voucher.validity_end DESC'),
						'recursive'     => 0
					));
				}
			}

			foreach($vouchers as $voucher){
				$promo = '';
				$promo_title = '';
				$rightToUse_once = false;
				$prod_promo = array();
				$produit_promo_select = '';

				 foreach($packs as $produit){

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

					}
					 array_push($prod_promo, $produit);
				 }
				if($promo)$packs = $prod_promo;
			}
			$is_promo_total = 0;


			$this->loadModel('Slideprice');
			$slideprice = $this->Slideprice->find('first',array(
				'fields' => array('Slideprice.*','SlidepriceLang.*'),
                'conditions' => array(
                    'Slideprice.active' => 1,
					'Slideprice.domain' => $this->Session->read('Config.id_domain'),
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
					'Slidepricemobile.domain' => $this->Session->read('Config.id_domain'),
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

		$product_preselect = 0;
		if($this->Session->read('product_preselect'))$product_preselect = $this->Session->read('product_preselect');

        $this->set(compact('packs','promo','promo_title','is_promo_total','slideprice','slidepricemobile','product_preselect'));

        if($this->request->is('post')){
            //$this->_buyCreditUser($this->params['pack'],$user['id']);
            $this->buyCreditPaiement();
        }
    }


    public function payments(){
        $user = $this->Session->read('Auth.User');
	
	/*
        if (!isset($user['id']) && $user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

	 */

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('UserCredit');
        //Tout les achats du client
        $this->Paginator->settings = array(
            'fields' => array('Product.country_id','Product.id','UserCredit.credits','Order.total',  'UserCredit.payment_mode','UserCredit.date_upd', 'Product.tarif', 'UserCredit.product_name','Order.valid', 'ProductLang.name','Country.devise'),
            'conditions' => array('users_id' => $user['id']),
            'joins' => array(
                array('table' => 'product_langs',
                      'alias' => 'ProductLang',
                      'type' => 'left',
                      'conditions' => array(
                          'ProductLang.lang_id = '.$this->Session->read('Config.id_lang'),
                          'ProductLang.product_id = UserCredit.product_id'
                      )
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type' => 'left',
                    'conditions' => array('Product.id = UserCredit.product_id')
                ),
				 array(
						'table' => 'orders',
						'alias' => 'Order',
						'type' => 'left',
						'conditions' => array('Order.id = UserCredit.order_id')
					),
                array(
                    'table' => 'countries',
                    'alias' => 'Country',
                    'type' => 'left',
                    'conditions' => array('Country.id = Product.country_id')
                )
            ),
            'order' => 'UserCredit.date_upd desc',
            'recursive' => -1,
            'limit' => 15
        );

		$payments = $this->Paginator->paginate($this->UserCredit);

        //On récupère les devise pour chaque pays
        $this->loadModel('Country');
        $devises = $this->Country->find('list', array(
            'fields'        => array('Country.id', 'Country.devise'),
            'recursive'     => -1
        ));

		//on check si carte cadeau sur delai credit affiché
		$this->loadModel('GiftOrder');
		$min_date = '';
		$max_date = '';
		foreach($payments as $payment){
			if(!$max_date)$max_date = $payment['UserCredit']['date_upd'];
			$min_date = $payment['UserCredit']['date_upd'];
		}

		$params = $this->request->params;

		if(!$params["page"]){
			$max_date = date('Y-m-d H:i:s');
		}
		if(!$min_date){
			$min_date = date('2018-12-01 00:00:00');
			$max_date = date('Y-m-d H:i:s');
		}
		$gift_order = $this->GiftOrder->find('all', array(
					'conditions' => array('GiftOrder.user_id' => $user['id'],'GiftOrder.date_add >=' => $min_date ,'GiftOrder.date_add <=' => $max_date,'GiftOrder.valid >' => 0),
					'recursive' => -1,
					'order' => 'GiftOrder.date_add desc',
				));
        $this->set(compact('payments','devises','gift_order'));
    }

	public function loyalty(){
        $user = $this->Session->read('Auth.User');
	
	/*
        if (!isset($user['id']) && $user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	 */

        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('LoyaltyCredit');
        //Tout les achats du client
        $this->Paginator->settings = array(
            'fields' => array('LoyaltyCredit.date_add'),
            'conditions' => array('user_id' => $user['id'], 'LoyaltyCredit.valid' => 1),
            'order' => 'LoyaltyCredit.date_add desc',
            'recursive' => -1,
            'limit' => 15
        );

        $loyalty = $this->Paginator->paginate($this->LoyaltyCredit);


		$loyalty_credit = $this->LoyaltyCredit->find('all',array(
                'conditions' => array('LoyaltyCredit.user_id' => $user['id'], 'LoyaltyCredit.valid' => 0),
				'order' => array('id'=> 'desc'),
                'recursive' => -1
            ));

		$current_pourcent = 0;
		$this->loadModel('LoyaltyUserBuy');
		$loyalty_user = $this->LoyaltyUserBuy->find('first', array(
					'conditions' => array('LoyaltyUserBuy.user_id' => $user['id']),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));
			if($loyalty_user['LoyaltyUserBuy']['pourcent_current']) $current_pourcent = $loyalty_user['LoyaltyUserBuy']['pourcent_current'];

		$this->set(compact('loyalty','loyalty_credit','current_pourcent'));

    }


    public function mails($idMail = 0){
        $user = $this->Session->read('Auth.User');
	/*
        if (!isset($user['id']) && $user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	*/
	
        $this->loadModel('Message');

        //Formulaire pour un nouveau message
        if($this->request->is('post') && !isset($this->request->data['isAjax'])){
            $this->answerMail();
        }

        //L'id de l'user
        $id = $user['id'];

        //Messages privés ??
       /* if(isset($this->params->query['private']) && $this->params->query['private']){
            $mails = $this->Message->getDiscussion($id, false, false, true);
            $conditions = $this->Message->getConditions($id, false, false, true);
        }
        else{
            $mails = $this->Message->getDiscussion($id);
            $conditions = $this->Message->getConditions($id);
        }

        $this->Paginator->settings = array(
            'conditions'    => $conditions,
            'paramType'     => 'querystring',
            'limit'         => Configure::read('Site.limitMessagePage')
        );

        $this->Paginator->paginate($this->Message);

        //On crée les différentes pages
        $pages = array_chunk($mails, Configure::read('Site.limitMessagePage'));

        $page = 0;
        if(isset($this->params->query['page']))
            $page = $this->params->query['page']-1;

        if(isset($pages[$page]))
            $mails = $pages[$page];
        else
            $mails = array();
		*/
		$this->paginatorParams();

		$firstConditions = array(
                'Message.deleted' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $id),
                    array('Message.to_id' => $id, 'Message.etat !=' => 2)
                )
            );

		if(isset($this->params->query['private']) && $this->params->query['private'])
                $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
            else{
                //Les discussions privés
                $firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 0));
            }

		$this->Paginator->settings = array(
            'fields' => array('Message.*','To.*','LastMessage.*','FirstMessage.*', 'To.pseudo as pseudo', 'To.agent_number as agent_number',
							 '((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder'

							 ),
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
                    'alias' => 'To',
                    'type'  => 'left',
                    'conditions' => array(
						'To.id = Message.to_id'
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


        $dataNoRead['mailConsult'] = ($this->Message->hasNoReadMail($user['id']) > 0 ?true:false);
        $dataNoRead['mailPrivate'] = ($this->Message->hasNoReadMail($user['id'], true) > 0 ?true:false);

        $this->set(compact('mails', 'id', 'dataNoRead', 'idMail'));
    }

    //Pour récupérer le formulaire de réponse
    public function answerForm(){
        $this->_answerForm($this->Auth->user('id'), 'accounts');
    }

    public function new_mail(){
		$id_lang = $this->Session->read('Config.id_lang');
		$noaccept_butpost = false;

        //Requete ajax
        if($this->request->is('ajax')){
            //Utilisateur non connecté
            if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
                $this->layout = '';

				if(isset($this->params['id']) && $this->Auth->user('role') !== 'agent'){
					$agent = $this->User->getAgent($this->params['id']);
					$response = $this->render('/Home/media_phone');
					$content = $response->body();
				}else{
					if($this->Session->read('type_modal') == 'ins'){
						$content = $this->render('/Elements/ins_modal');
					}else{
						$content = $this->render('/Elements/login_modal');
					}
				}

				$intro = $this->getCmsPage(308, $id_lang);
				$this->loadModel('UserCountry');
				$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
				if(isset($this->params['id']) && $this->Auth->user('role') !== 'agent'){
					$this->set(array('title' =>__('Consultation par email<br />avec<span>').' '.$agent['User']['pseudo'].'</span>', 'content' => $content, 'User' => $agent['User']));
					$response = $this->render('/Elements/modal_consult');
				}else{
					$this->set(array('title' =>__('Accès client'), 'content' => $intro["PageLang"]['content'].$content));
					$response = $this->render('/Elements/modal');
				}

                $this->jsonRender(array('html' => $response->body(), 'return' => false));
            }
            $this->jsonRender(array('return' => true));
        }
        //-------------------------------------Envoi d'un mail--------------------------------------------------
        if($this->request->is('post')){
            //Les datas
			$requestData = $this->request->data;
			$this->Session->write('Message_in_live', $requestData['Message']['content']);

			//Avons-nous deux photos ??
			$n_image = 1;
			$attachment = array();
			$attachment2 = array();
			foreach($requestData['Message']['attachment'] as $file){
				if($n_image == 1)
					$attachment = $file;
				if($n_image == 2)
					$attachment2 = $file;
				$n_image ++;
			}
			$requestData['Message']['attachment'] = $attachment;
			$requestData['Message']['attachment2'] = $attachment2;

            //Les champs du formulaire
            $toId = $requestData['Message']['to_id'];
            $requestData['Message'] = Tools::checkFormField($requestData['Message'], array('to_id', 'content', 'attachment', 'attachment2'), array('to_id', 'content'));
            if($requestData['Message'] === false){
                $this->Session->setFlash(__('Erreur avec le formulaire'),'flash_error');

                $this->redirect(array('controller' => 'accounts', 'action' => 'new_mail', 'id' => $toId),false);
            }

            //Check sur l'agent
            $agent = $this->User->find('first',array(
                'fields' => array('creditMail', 'agent_status', 'pseudo', 'agent_number', 'id', 'email', 'lang_id','consults_nb'),
                'conditions' => array('User.id' => $requestData['Message']['to_id'], 'User.role' => 'agent', 'User.consult_email' => 1, 'User.active' => 1, 'User.deleted' => 0,
                    'OR' => array(
                        array('User.agent_status' => 'available'),
                        array(
                            'User.agent_status' => 'busy',
                            'User.consult_email' => 1
                        )
                    )
                    /*'User.agent_status' => 'available'*/
                ),
                'recursive' => -1
            ));

            //Si pas d'agent, envoie de l'email impossible
            if(empty($agent)){
                $this->Session->setFlash(__('L\'expert est indisponible ou il n\'accepte pas de consultation par mail à cet instant.'),'flash_warning');
				$noaccept_butpost = true;
				//return;
                //$this->redirect(array('controller' => 'accounts', 'action' => 'new_mail', 'id' => $toId),false);
            }else{

				$attachment = false;
				$attachment2 = false;
				//Avons-nous un fichier ??
				if(count($requestData['Message']['attachment'])){
				if($this->isUploadedFile($requestData['Message']['attachment'])){
					//Pièce jointe
					$attachment = true;

					//Est-ce un fichier image autorisé ??
					if(!Tools::formatFile($this->allowed_mime_types, $requestData['Message']['attachment']['type'],'Image')){
						$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
						$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
						return;
					}
				}
				//S'il y a eu une erreur dans l'upload du fichier
				elseif($requestData['Message']['attachment']['error'] != 4){
					$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
					$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
					return;
				}
				}
				if(count($requestData['Message']['attachment2'])){
				 if( $this->isUploadedFile($requestData['Message']['attachment2'])){
					//Pièce jointe
					$attachment2 = true;

					//Est-ce un fichier image autorisé ??
					if(!Tools::formatFile($this->allowed_mime_types, $requestData['Message']['attachment2']['type'],'Image')){
						$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
						$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
						return;
					}
				}
				//S'il y a eu une erreur dans l'upload du fichier
				elseif($requestData['Message']['attachment2']['error'] != 4){
					$this->Session->setFlash(__('Erreur dans le chargement de votre deuxième fichier.'),'flash_warning');
					$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
					return;
				}
				}

				//Le nombre de crédit pour une question pour cet agent
				$creditMail = (empty($agent['User']['creditMail']) ?Configure::read('Site.creditPourUnMail'):$agent['User']['creditMail']);
				//le crédit du client
				$creditUser = $this->User->field('credit', array('id' => $this->Auth->user('id')));
				//Si le client n'a pas assez de crédit
				if($creditUser < $creditMail){
					$this->Session->setFlash(__('Vous n\'avez pas assez de crédit. Il vous faut').' '.$creditMail.' '.__('crédits.'),'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'new_mail', 'id' => $requestData['Message']['to_id']), false);
				}

				$this->loadModel('Message');

				$etat = 0;
				//on filtre le contenu
				$this->loadModel('FiltreMessage');

				$filtres = $this->FiltreMessage->find("all", array(
						'conditions' => array(
						)
				));
				foreach($filtres as $filtre){
					if(substr_count(strtolower($requestData['Message']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
						$etat = 2;
				}


				//On envoie le mail
				$this->Message->create();
				if($this->Message->save(array(
					'from_id'       => $this->Auth->user('id'),
					'to_id'         => $requestData['Message']['to_id'],
					'content'       => $this->remove_emoji($requestData['Message']['content']),
					'credit'        => $creditMail,
					'total_credit'  => $creditMail,
					'etat'          => $etat
				))){
					if($etat == 2){
							App::import('Controller', 'Extranet');
							$extractrl = new ExtranetController();
							//Les datas pour l'email
							$datasEmail = array(
								'content' => __('Un Message client requiert check terme interdit.') ,
								'PARAM_URLSITE' => 'https://fr.spiriteo.com'
							);
							//Envoie de l'email
							$extractrl->sendEmail('contact@talkappdev.com',__('Message client terme interdit'),'default',$datasEmail);
						}
					//Mise à jour du crédit
					$newCredit = $this->updateCredit($this->Auth->user('id'), (isset($creditMail) ?$creditMail:Configure::read('Site.creditPourUnMail')));
					if($newCredit !== false)
						CakeSession::write(array('Auth.User.credit' => $newCredit));
					else{
						//Problème au niveau du crédit, on supprime le message
						//$this->Message->delete($this->Message->id, false);
						//$this->Session->setFlash(__('Erreur lors de la mise à jour de votre crédit. Le mail n\'a pas été envoyé.'),'flash_error');
						$this->redirect(array('controller' => 'accounts', 'action' => 'new_mail', 'id' => $requestData['Message']['to_id']));
					}

					//Save dans l'historique
					$this->loadModel('UserCreditLastHistory');
					$this->loadModel('UserCreditHistory');
					$saveData = array(
						'users_id'              => $this->Auth->user('id'),
						'agent_id'              => $requestData['Message']['to_id'],
						'agent_pseudo'          => $agent['User']['pseudo'],
						'media'                 => 'email',
						'credits'               => $creditMail,
						'user_credits_before'   => $creditUser,
						'user_credits_after'    => $newCredit,
						'date_start'            => date('Y-m-d H:i:s'),
						'date_end'              => date('Y-m-d H:i:s'),
						'sessionid'             => $this->Message->id
					);
					$this->UserCreditLastHistory->create();
					$this->UserCreditLastHistory->save($saveData);
					//Save dans l'historique (archive)
					$saveData['user_id'] = $saveData['users_id'];
					unset($saveData['users_id']);

					$saveData['is_new'] = 0;
					$lastComCheck = $this->UserCreditHistory->find('first', array(
						'conditions'    => array('UserCreditHistory.user_id' => $saveData['user_id']),
						'recursive'     => -1
					));
					if(!$lastComCheck && !$this->User->field('is_come_back', array('id' => $this->Auth->user('id'))))$saveData['is_new'] = 1;
					$saveData['type_pay'] = 'pre';
					$saveData['domain_id'] = $this->Session->read('Config.id_domain');

					$this->UserCreditHistory->create();
					$this->UserCreditHistory->save($saveData);
					$this->calcCAComm($this->UserCreditHistory->id);

					//Sponsorship
					$lastHistoryID = $this->UserCreditLastHistory->id;
					App::import('Model', 'Sponsorship');
					$Sponsorship = new Sponsorship();
					$Sponsorship->Benefit($lastHistoryID);

					//cumul comm
					$consults_nb = $agent['User']['consults_nb'] + 1;
					$this->User->id = $agent['User']['id'];
					$this->User->saveField('consults_nb', $consults_nb);

					//update costAgent
					/*$this->loadModel('CostAgent');
					$cost_agent = $this->CostAgent->find('first', array(
								'conditions' => array('CostAgent.id_agent' => $saveData['agent_id']),
								'recursive' => -1
							));
					$cost_min_total = $creditMail / 60;
					if($cost_agent['CostAgent']['nb_minutes']) {
						$cost_min_total = $cost_min_total + $cost_agent['CostAgent']['nb_minutes'];
						$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
						$this->CostAgent->save($cost_agent);
						//$this->CostAgent->updateAll($cost_agent['CostAgent'],array('id' => $cost_agent['CostAgent']['id']));
					}else{
						$cost_agent = array();
						$cost_agent['CostAgent'] = array();
						$cost_agent['CostAgent']['id_agent'] = $saveData['agent_id'];
						$cost_agent['CostAgent']['id_cost'] = 1;
						$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
						$this->CostAgent->create();
						$this->CostAgent->save($cost_agent);
						$cost_agent = $this->CostAgent->find('first', array(
								'conditions' => array('CostAgent.id_agent' => $saveData['agent_id']),
								'recursive' => -1
							));
					}

					$id_cost = 0;
					$palier = $cost_min_total;
					$this->loadModel('Cost');
					if($cost_agent['CostAgent']['id_cost'] < 5)
					$costs = $this->Cost->find('all', array(
							'conditions' =>  array('id <' => 5),
							'order' => array('id'=> 'asc'),
							'recursive' => -1
						));
					if($cost_agent['CostAgent']['id_cost'] >= 5)
					$costs = $this->Cost->find('all', array(
							'conditions' =>  array('id >=' => 5),
							'order' => array('id'=> 'asc'),
							'recursive' => -1
						));
					if(!empty($costs)){
										foreach($costs as $bobo){
											foreach($bobo as $b){
												if($palier >= $b['level'])
													$id_cost = $b['id'];
											}
										}
									}
					$id_cost = $id_cost +1;
					$cost_agent['CostAgent']['id_cost'] = $id_cost;
					$this->CostAgent->save($cost_agent);
					*/

					//Save la pièce jointe
					//Si y a erreur
					if($attachment && !Tools::saveAttachment($requestData['Message']['attachment'], Configure::read('Site.pathAttachment'), $agent['User']['agent_number'], $this->Message->id))
						$this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
					elseif($attachment){
						//On save le nom de la pièce jointe
						$this->Message->saveField('attachment', $agent['User']['agent_number'].'-'. $this->Message->id .'.jpg');
						$this->Session->setFlash(__('Votre message a été envoyé.'),'flash_success');
					}else
						$this->Session->setFlash(__('Votre message a été envoyé.'),'flash_success');

					if($attachment2 && !Tools::saveAttachment($requestData['Message']['attachment2'], Configure::read('Site.pathAttachment'), $agent['User']['agent_number'].'-2', $this->Message->id))
						$this->Session->setFlash(__('Votre message a été envoyé. Cependant la deuxième pièce jointe n\'a pu être envoyé.'), 'flash_warning');
					elseif($attachment2){
						//On save le nom de la pièce jointe
						$this->Message->saveField('attachment2', $agent['User']['agent_number'].'-2'.'-'. $this->Message->id .'.jpg');
					}

           $title_mail = __('Vous avez une consultation Email payante en attente !');

					//Envoi de l'email
					//$this->sendEmail($agent['User']['email'],'Nouveau message','new_mail',array('param' => array('name' => $agent['User']['pseudo'])));
					if($etat == 0){
						$this->sendCmsTemplateByMail(179, $agent['User']['lang_id'], $agent['User']['email'], array(
							'PSEUDO_NAME_DEST' => $agent['User']['pseudo'],'MAIL_SUBJECT' => $title_mail
						));
					}
					$this->Session->write('Message_in_live', '');
					$this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
				}else{
					$this->Session->setFlash(__('Erreur durant l\'envoi du mail. Vous n\'avez pas été décrédité.'),'flash_error');
					$this->redirect(array('controller' => 'accounts', 'action' => 'new_mail', 'id' => $requestData['Message']['to_id']));
				}
			}
        }

        //-----------------------------------------------------------------Nouveau message-------------------------------------------------
        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(210, false, $tmp, $page);
        $this->set('page_content', $page_content);

        if(isset($this->params['id']))
            $idAgent = $this->params['id'];
        elseif(isset($this->params['named']['id']))
            $idAgent = $this->params['named']['id'];

        //Si pas d'id redirection home
        if(empty($idAgent))
            $this->redirect(array('controller' => 'home', 'action' => 'index'),true,301);

        //Si utilisateur différent de client
        if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
            $this->Session->setFlash(__('Veuillez-vous connecter avec un compte client.'), 'flash_warning');
            $this->redirect(array('controller' => 'users', 'action' => 'login'),true,301);
        }

        //Si l'agent existe et accepte les consultations par mail
        $agent = $this->User->find('first',array(
                'fields' => array('agent_status', 'pseudo','agent_number', 'creditMail','id','mail_infos_v'),
                'conditions' => array('User.id' => $idAgent, 'User.role' => 'agent', 'User.consult_email' => 1, 'User.active' => 1, 'User.deleted' => 0,
                    'OR' => array(
                        array('User.agent_status' => 'available'),
                        array(
                            'User.agent_status' => 'busy',
                            'User.consult_email' => 1
                        )
                    )
                    /*'User.agent_status' => 'available'*/
                ),
                'recursive' => -1
            ));


        //Si aucun agent trouvé
        if(empty($agent) && !$noaccept_butpost){
            $this->Session->setFlash(__('L\'expert est indisponible ou il n\'accepte pas les consultations par mail.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }
        //Nombre de crédit pour une question
        $creditMail = (empty($agent['User']['creditMail']) ?Configure::read('Site.creditPourUnMail'):$agent['User']['creditMail']);
        if($this->Auth->user('credit') < $creditMail){
            $this->Session->setFlash(__('Il vous faut un minimum de 15 minutes de crédits afin d\'effectuer une consultation par Email.'),'flash_warning');
			//$this->Session->write('not_enought_for_mail', true);
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
       // }else{
			//$this->Session->write('not_enought_for_mail', false);
		}
		$message_in_live = $this->Session->read('Message_in_live');
        $this->set(compact('agent', 'creditMail','message_in_live'));
    }

    public function closeMessage(){
        if($this->request->is('ajax')){
            //Check l'id du mail
            if(!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
                $this->jsonRender(array('return' => false));
            $idMail = $this->request->data['id_mail'];
            $this->loadModel('Message');
            //On récupère la conversation
            $tmp_conversation = $this->Message->find('first',array(
                'conditions' => array('Message.id' => $idMail)
            ));

            //Si pas de conversation ou si la conversation ne lui est pas destiné
          /*  if(empty($tmp_conversation) || $tmp_conversation['Message']['from_id'] != $this->Auth->user('id'))
                $this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => 'accounts', 'action' => 'mails'),true)));*/

            //On clôture la discussion
            $this->Message->id = $idMail;
            $this->Message->updateAll(array('Message.archive' => 1), array('Message.parent_id' => $idMail));
            if($this->Message->saveField('archive', 1))
                $this->jsonRender(array('return' => true));
            else
                $this->jsonRender(array('return' => false));
        }
    }

	public function restoreMessage(){
        if($this->request->is('ajax')){
            //Check l'id du mail
            if(!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
                $this->jsonRender(array('return' => false));
            $idMail = $this->request->data['id_mail'];
            $this->loadModel('Message');
            //On récupère la conversation
            $tmp_conversation = $this->Message->find('first',array(
                'conditions' => array('Message.id' => $idMail)
            ));

            //Si pas de conversation ou si la conversation ne lui est pas destiné
          /* if(empty($tmp_conversation) || $tmp_conversation['Message']['from_id'] != $this->Auth->user('id'))
                $this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => 'accounts', 'action' => 'mails'),true)));*/

            //On clôture la discussion
            $this->Message->id = $idMail;
            $this->Message->updateAll(array('Message.archive' => 0), array('Message.parent_id' => $idMail));
			//$this->Message->saveField('etat', 0);
            if($this->Message->saveField('archive', 0))
                $this->jsonRender(array('return' => true));
            else
                $this->jsonRender(array('return' => false));
        }
    }


	public function deleteMessage(){
        if($this->request->is('ajax')){
            //Check l'id du mail
            if(!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
                $this->jsonRender(array('return' => false));
            $idMail = $this->request->data['id_mail'];
            $this->loadModel('Message');
            //On récupère la conversation
            $tmp_conversation = $this->Message->find('first',array(
                'conditions' => array('Message.id' => $idMail)
            ));

            //Si pas de conversation ou si la conversation ne lui est pas destiné
            if(empty($tmp_conversation))// || $tmp_conversation['Message']['from_id'] != $this->Auth->user('id')
                $this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => 'accounts', 'action' => 'mails'),true)));

            //On clôture la discussion
            $this->Message->id = $idMail;
            $this->Message->updateAll(array('Message.archive' => 2), array('Message.parent_id' => $idMail));
            if($this->Message->saveField('archive', 2))
                $this->jsonRender(array('return' => true));
            else
                $this->jsonRender(array('return' => false));
        }
    }


    public function getMails(){
      /* $this->_getMails('accounts');*/

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
						/*if($requestData['archive'] === 'private'){
							$firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 1));
						}else{
							$firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 1));
						}*/
						$firstConditions = array_merge($firstConditions, array( 'Message.archive' => 1));
                        $param = 'archive';

                        break;
                    default :
                        //Par défaut les messages de consultations
                         $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
                        $param = 'message';
                }


		$this->Paginator->settings = array(
            'fields' => array('Message.*','LastMessage.*','FirstMessage.*','To.*','To.agent_number as agent_number', 'To.pseudo as pseudo','((CASE
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
                    'alias' => 'To',
                    'type'  => 'left',
                    'conditions' => array(
						'To.id = Message.to_id'
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

			$mmail['LastMessage']['content'] = htmlentities($mmail['LastMessage']['content']);//utf8_encode(strip_tags($mmail['LastMessage']['content']));
		}
		//Message privé ou pas
                  /*      if(isset($requestData['archive'])){
                            $typeArchive = ($requestData['archive'] === 'private' ?0:1);
                            foreach($mails as $indice => $mail){
                                if($mail['Message']['private'] == $typeArchive || $mail['Message']['private'] == 2)
                                    unset($mails[$indice]);
                            }
                        }	*/



                $this->layout = '';

                $this->set(compact('mails', 'id'));
                if(isset($requestData['onlyBlockMail']))
                    $this->set('onlyBlockMail', true);
                $this->set(array('controller' => 'accounts'));
                $response = $this->render('/Elements/mails');

                $this->jsonRender(array('return' => true, 'html' => $response->body(), 'param' => $param));
            }

            $this->jsonRender(array('return' => false));
        }


    }

    public function update_mail(){
        $this->_update_mail();
    }

    private function answerMail(){
        //Les datas
        $requestData = $this->request->data;
		$this->loadModel('User');


		//Avons-nous deux photos ??

		$n_image = 1;
		$attachment = array();
		$attachment2 = array();
		foreach($requestData['Account']['attachment'] as $file){
			if($n_image == 1)
				$attachment = $file;
			if($n_image == 2)
				$attachment2 = $file;
			$n_image ++;
		}
		$requestData['Account']['attachment'] = $attachment;
		$requestData['Account']['attachment2'] = $attachment2;

        //Les champs du formulaire
        $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('mail_id', 'content', 'attachment', 'attachment2'), array('mail_id', 'content'));
        if($requestData['Account'] === false){
            $this->Session->setFlash(__('Erreur avec le formulaire de réponse'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

        $infoMessage = $this->Message->find('first',array(
            'fields' => array('Message.to_id', 'Message.total_credit', 'Message.private', 'Message.archive' => 0, 'Agent.pseudo', 'Agent.email', 'Agent.consults_nb','Agent.id',
                'Agent.lang_id','User.lang_id','Agent.consult_email','Agent.agent_status', 'Agent.creditMail', 'Agent.agent_number', 'User.role', 'User.id'),
            'conditions' => array('Message.id' => $requestData['Account']['mail_id'], 'Message.deleted' => 0, 'Message.parent_id' => null, 'Message.from_id' => $this->Auth->user('id')),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type' => 'left',
                    'conditions' => array(
                        'Agent.id = Message.to_id',
                        'Agent.role = "agent"',
                        'Agent.active = "1"',
                        'Agent.deleted = "0"'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array(
                        'User.id = Message.to_id',
                        'User.deleted = 0'
                    )
                )
            ),
            'recursive' => -1
        ));

		if(!isset($infoMessage['Message']['private'])){
			 $infoMessage = $this->Message->find('first',array(
            'fields' => array('Message.to_id','Message.from_id', 'Message.total_credit', 'Message.private', 'Message.archive' => 0, 'Agent.pseudo', 'Agent.email', 'Agent.consults_nb','Agent.id',
                'Agent.lang_id','User.lang_id','Agent.consult_email','Agent.agent_status', 'Agent.creditMail', 'Agent.agent_number', 'User.role', 'User.id'),
            'conditions' => array('Message.id' => $requestData['Account']['mail_id'], 'Message.deleted' => 0, 'Message.parent_id' => null, 'Message.to_id' => $this->Auth->user('id')),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type' => 'left',
                    'conditions' => array(
                        'Agent.id = Message.from_id',
                        'Agent.role = "agent"',
                        'Agent.active = "1"',
                        'Agent.deleted = "0"'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array(
                        'User.id = Message.from_id',
                        'User.deleted = 0'
                    )
                )
            ),
            'recursive' => -1
        ));

		}

        //Check sur le message-------------------------------------------------------------------------
        //Si pas de message, pas d'agent ou pas d'admin
        if(empty($infoMessage['Message']['to_id']) || (empty($infoMessage['Agent']['agent_number']) && empty($infoMessage['User']['id']) )){
           $this->Session->setFlash(__('Vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }
        /*//Si pas de message
        if(empty($infoMessage['Message']['to_id'])){
            $this->Session->setFlash(__('Vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

        //Check sur l'agent-------------------------------------------------------------------------
        //Si pas d'agent
        if(empty($infoMessage['Agent']['agent_number'])){
            $this->Session->setFlash(__('L\'expert demandé n\'existe pas ou il n\'est plus actif ou vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }
        //Check sur l'admin-------------------------------------------------------------------------
        //Si pas d'admin
        elseif(empty($infoMessage['Agent']['agent_number'])){
            $this->Session->setFlash(__('L\'expert demandé n\'existe pas ou il n\'est plus actif ou vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }*/


        //Si l'agent ne prend pas de consultation par email
		if(!empty($infoMessage['Agent']['agent_number']) && $infoMessage['Message']['private'] == 0 && $infoMessage['Agent']['agent_status'] != 'available'){
            $this->Session->setFlash(__('L\'expert n\'accepte pas/plus de consultation par mail.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

        if(!empty($infoMessage['Agent']['agent_number']) && $infoMessage['Agent']['consult_email'] == 0 && $infoMessage['Message']['private'] == 0 ){
            $this->Session->setFlash(__('L\'expert n\'accepte pas/plus de consultation par mail.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

        $attachment = false;
		$attachment2 = false;


        if($this->isUploadedFile($requestData['Account']['attachment'])){
            //Est-ce un fichier image autorisé ??
            if(!Tools::formatFile($this->allowed_mime_types, $requestData['Account']['attachment']['type'],'Image')){
                $this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
            }
            $attachment = true;
        }
        //S'il y a eu une erreur dans l'upload du fichier
        elseif(isset($requestData['Account']['attachment']['error']) && $requestData['Account']['attachment']['error'] != 4){
            $this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }
		if($this->isUploadedFile($requestData['Account']['attachment2'])){
            //Est-ce un fichier image autorisé ??
            if(!Tools::formatFile($this->allowed_mime_types, $requestData['Account']['attachment2']['type'],'Image')){
                $this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
            }
            $attachment2 = true;
        }
        //S'il y a eu une erreur dans l'upload du fichier
        elseif(isset($requestData['Account']['attachment2']['error']) && $requestData['Account']['attachment2']['error'] != 4){
            $this->Session->setFlash(__('Erreur dans le chargement de votre deuxième fichier.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

        $creditMail = 0;
        //Si ce n'est pas un message privée
        if($infoMessage['Message']['private'] == 0){
            //Check sur le crédit du client--------------------------------------------------------------
            $creditUser = $this->User->field('credit', array('id' => $this->Auth->user('id')));
            $creditMail = (empty($infoMessage['Agent']['creditMail']) ?Configure::read('Site.creditPourUnMail'):$infoMessage['Agent']['creditMail']);
            //Pas assez de crédit
            if($creditUser < $creditMail){
                $this->Session->setFlash(__('Vous n\'avez pas assez de crédit. Il vous faut').' '.$creditMail.' '.__('crédits.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
            }
        }

		$etat = 0;
		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
					'conditions' => array(
					)
		));
		foreach($filtres as $filtre){
			if(substr_count(strtolower($requestData['Account']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
				$etat = 2;
		}


        //On save (envoie) le mail
        $this->Message->create();

		if($infoMessage['Message']['to_id'] == $this->Auth->user('id'))$infoMessage['Message']['to_id'] = $infoMessage['Message']['from_id'];//pour les relances
        if($this->Message->save(array(
            'parent_id' => $requestData['Account']['mail_id'],
            'from_id' => $this->Auth->user('id'),
            'to_id' => $infoMessage['Message']['to_id'],
            'content' => $this->remove_emoji($requestData['Account']['content']),
            'credit' => $creditMail,
            'private'   => $infoMessage['Message']['private'],
            'etat' => $etat,
            'deleted' => 0,
			'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
        ))){
			if($etat == 2){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						//Les datas pour l'email
						$datasEmail = array(
							'content' => 'Un Mail requiert check terme interdit.' ,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com'
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Mail client terme interdit','default',$datasEmail);
					}
            //L'id du message qui vient d'être crée
            $newId = $this->Message->id;
            //Si ce n'est pas un message privée
            if($infoMessage['Message']['private'] == 0){
                //Mise à jour du crédit
                $newCredit = $this->updateCredit($this->Auth->user('id'), (isset($creditMail) ?$creditMail:Configure::read('Site.creditPourUnMail')));
                if($newCredit !== false)
                    CakeSession::write(array('Auth.User.credit' => $newCredit));
                else{
                    //Problème au niveau du crédit, on supprime le message
                    //$this->Message->delete($newId, false);
                    //$this->Session->setFlash(__('Erreur lors de la mise à jour de votre crédit. Le mail n\'a pas été envoyé.'),'flash_error');
                    $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
                }
                //On save le total de crédit pour cette discussion
                $total_credit = (int)$infoMessage['Message']['total_credit'] + $creditMail;
                $this->Message->id = $requestData['Account']['mail_id'];
                $this->Message->saveField('total_credit', $total_credit);


                //Save dans l'historique
                $this->loadModel('UserCreditLastHistory');
                $this->loadModel('UserCreditHistory');
                $saveData = array(
                    'users_id'              => $this->Auth->user('id'),
                    'agent_id'              => $infoMessage['Message']['to_id'],
                    'agent_pseudo'          => $infoMessage['Agent']['pseudo'],
                    'media'                 => 'email',
                    'credits'               => $creditMail,
                    'user_credits_before'   => $creditUser,
                    'user_credits_after'    => $newCredit,
                    'date_start'            => date('Y-m-d H:i:s'),
                    'date_end'              => date('Y-m-d H:i:s'),
					'sessionid'             => $newId
                );
                $this->UserCreditLastHistory->create();
                $this->UserCreditLastHistory->save($saveData);
                //Save dans l'historique (archive)
                $saveData['user_id'] = $saveData['users_id'];
                unset($saveData['users_id']);

				$saveData['is_new'] = 0;
				/*$lastComCheck = $this->UserCreditHistory->find('first', array(
					'conditions'    => array('UserCreditHistory.user_id' => $saveData['user_id']),
					'recursive'     => -1
				));
				if(!$lastComCheck)$saveData['is_new'] = 1;*/
				$saveData['type_pay'] = 'pre';
				$saveData['domain_id'] = $this->Session->read('Config.id_domain');

                $this->UserCreditHistory->create();
                $this->UserCreditHistory->save($saveData);
				$this->calcCAComm($this->UserCreditHistory->id);

				//Sponsorship
				$lastHistoryID = $this->UserCreditLastHistory->id;
				App::import('Model', 'Sponsorship');
				$Sponsorship = new Sponsorship();
				$Sponsorship->Benefit($lastHistoryID);

				//cumul comm
				$consults_nb = $infoMessage['Agent']['consults_nb'] + 1;
				$this->User->id = $infoMessage['Agent']['id'];
				$this->User->saveField('consults_nb', $consults_nb);

				//update costAgent
				/*$this->loadModel('CostAgent');
				$cost_agent = $this->CostAgent->find('first', array(
							'conditions' => array('CostAgent.id_agent' => $saveData['agent_id']),
							'recursive' => -1
						));
				$cost_min_total = $creditMail / 60;
				if($cost_agent['CostAgent']['nb_minutes']) {
					$cost_min_total = $cost_min_total + $cost_agent['CostAgent']['nb_minutes'];
					$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
					$this->CostAgent->save($cost_agent);
					//$this->CostAgent->updateAll($cost_agent['CostAgent'],array('id' => $cost_agent['CostAgent']['id']));
				}else{
					$cost_agent = array();
					$cost_agent['CostAgent'] = array();
					$cost_agent['CostAgent']['id_agent'] = $saveData['agent_id'];
					$cost_agent['CostAgent']['id_cost'] = 1;
					$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
					$this->CostAgent->create();
					$this->CostAgent->save($cost_agent);
					$cost_agent = $this->CostAgent->find('first', array(
							'conditions' => array('CostAgent.id_agent' => $saveData['agent_id']),
							'recursive' => -1
						));
				}

				$id_cost = 0;
				$palier = $cost_min_total;
				$this->loadModel('Cost');
				if($cost_agent['CostAgent']['id_cost'] < 5)
				$costs = $this->Cost->find('all', array(
						'conditions' =>  array('id <' => 5),
						'order' => array('id'=> 'asc'),
						'recursive' => -1
					));
				if($cost_agent['CostAgent']['id_cost'] >= 5)
				$costs = $this->Cost->find('all', array(
						'conditions' =>  array('id >=' => 5),
						'order' => array('id'=> 'asc'),
						'recursive' => -1
					));
				if(!empty($costs)){
									foreach($costs as $bobo){
										foreach($bobo as $b){
											if($palier >= $b['level'])
												$id_cost = $b['id'];
										}
									}
								}
				$id_cost = $id_cost +1;
				$cost_agent['CostAgent']['id_cost'] = $id_cost;
				$this->CostAgent->save($cost_agent);
				*/

            }

			if($infoMessage['Message']['private'] == 1 && $infoMessage['Message']['to_id'] == Configure::read('Admin.id')){
				$bodymail = 'Consulter : <a href="https://fr.spiriteo.com/admin/admins/mails">https://fr.spiriteo.com/admin/admins/mails</a>';
					$this->sendEmail(
						'contact@talkappdev.com',
						'Vous avez reçu un nouveau message dans la boite " Contact " de Spiriteo',
						'default',array('content' => $bodymail)
					);
			}

            //Save la pièce jointe
            //Si y a erreur
            if($attachment && !Tools::saveAttachment($requestData['Account']['attachment'], Configure::read('Site.pathAttachment'), $infoMessage['Agent']['agent_number'], $newId)){
                $this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
            }
            elseif($attachment){
                //On save le nom de la pièce jointe
                $this->Message->id = $newId;
                $this->Message->saveField('attachment', $infoMessage['Agent']['agent_number'].'-'. $newId .'.jpg');
            }
			if($attachment2 && !Tools::saveAttachment($requestData['Account']['attachment2'], Configure::read('Site.pathAttachment'), $infoMessage['Agent']['agent_number'].'-2', $newId)){
                $this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
            }
            elseif($attachment2){
                //On save le nom de la pièce jointe
                $this->Message->id = $newId;
                $this->Message->saveField('attachment2', $infoMessage['Agent']['agent_number'].'-2'.'-'. $newId .'.jpg');
            }

          $title_mail = __('Vous avez une réponse à une consultation Email payante en attente !');
          if($infoMessage['Message']['private'] == 1) $title_mail = __('Vous avez une réponse à un message privé en attente !');

            //est-ce un msg pour un expert ??
            if(isset($infoMessage['User']) && $infoMessage['User']['role'] === 'agent' && $etat == 0)
                //Envoi de l'email

                //$this->sendEmail($infoMessage['Agent']['email'],'Nouveau message','new_mail',array('param' => array('name' => $infoMessage['Agent']['pseudo'])));
                $this->sendCmsTemplateByMail(177, $infoMessage['User']['lang_id'], $infoMessage['Agent']['email'], array(
                    'PSEUDO_NAME_DEST' => $infoMessage['Agent']['pseudo'], 'MAIL_SUBJECT' => $title_mail
                ));

			if($infoMessage['Message']['private'] == 1){
				//check last message
				$last_message_private = $this->Message->find('first', array(
						'conditions' => array('Message.private' => 1, 'Message.from_id' => $infoMessage['Message']['to_id'],'Message.to_id' => $this->Auth->user('id'), 'Message.id !=' =>$newId),
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


			}

			else
				$this->Session->setFlash(__('Votre message a été envoyé.'),'flash_success');

        }else
            $this->Session->setFlash(__('Erreur durant l\'envoi du mail. Vous n\'avez pas été décrédité.'),'flash_error');

		if($infoMessage['Message']['private'] == 1)
        $this->redirect(array('controller' => 'accounts', 'action' => 'mails', '?' => array('private' => true)));
		else
		$this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
    }

    //Permet d'afficher tout les messages d'une conversation
    public function readMail(){
        $this->_readMail($this->Auth->user('id'), 'accounts');
    }

    public function downloadAttachment($name){
        return $this->_downloadAttachment($name);
    }

    public function buycreditpaiement()
    {

        $res = $this->verifCart2();

        /* Si erreur dans le panier, on redirige */
        if (!$res){
            $this->clearSessionCart();
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
            return false;
        }
		    $this->set('cart', $res);

		//load data inscription
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

		//check si client auth
		$is_connected = false;
    $is_restricted = false;
		if ($this->Auth->login()){
			$user = $this->Session->read('Auth.User');
			$role = $this->Auth->user('role');
      if($user['id'] && $role = "client"){
				$is_connected = true;
         $this->User->id = $user['id'];
		    $is_restricted = $this->User->field('payment_blocked');
			}
		}
		$this->set('is_connected', $is_connected);

    //check if payment restricted ( only card payment )
    $this->set('is_restricted', $is_restricted);

     $this->render('buycreditpaiement');
    }

	 public function start()
    {

		 //si connecté je le renvoi sur etape 1 panier
		//check si client auth
		$is_connected = false;
		if ($this->Auth->login()){
			$user = $this->Session->read('Auth.User');
			$role = $this->Auth->user('role');
            if($user['id'] && $role = "client"){
				$is_connected = true;
				 $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
			}
		}


		 //force offre start
		 $this->request->data = array(
		 	"Account" => array(
				"produit"=> "1",
				"voucher"=>"OFFRERSTART"
			)
		 );

        $res = $this->verifCart2();

        /* Si erreur dans le panier, on redirige */
        if (!$res){
            $this->clearSessionCart();
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
            return false;
        }
		$this->set('cart', $res);

		//load data inscription
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


		$this->set('is_connected', $is_connected);

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

		  //recup product start
		$this->loadModel('Product');
         $product = $this->Product->find('first',array(
                'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
                'conditions' => array(
                    'Product.active' => 1,
					'Product.credits' => 600,
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

		$this->set('product', $product);
		$this->site_vars['meta_title']          = __('meta title');
		  $this->site_vars['meta_description']    = __('meta desc');
        $this->site_vars['meta_keywords']       = '';
		//$this->site_vars['robots']    = 'noindex';
        $this->render('start');
    }

    public function buycreditsconfirmation()
    {

    }
    private function verifCart()
    {
        $coupon = false;
        $requestData = $this->request->data;

        $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('produit', 'voucher', 'cgu'), array('cgu'));
        if($requestData['Account'] === false){
            $this->Session->setFlash(__('Erreur au niveau du formulaire'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

		//Avons-nous un coupon ??
		$is_coupon_buy_only = 0;
        if(!empty($requestData['Account']['voucher'])){
            $this->loadModel('Voucher');
            //Le client peut-il l'utiliser ??

            $rightToUse = $this->Voucher->rightToUse($requestData['Account']['voucher'], $this->Auth->user('personal_code'), $this->Auth->user('id'), $requestData['Account']['produit']);

			if($rightToUse){
                //on récupère le coupon
                $voucher = $this->Voucher->find('first', array(
                    'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips'),
                    'conditions'    => array('Voucher.code' => $requestData['Account']['voucher']),
                    'recursive'     =>-1
                ));

                $coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
            }

			//check IP
			$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
			$list_block_ip = explode(',',$voucher['Voucher']['ips']);
			if(in_array($ip_user,$list_block_ip)){
				$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
                return false;
			}


			$is_coupon_buy_only = $voucher['Voucher']['buy_only'];


            if ((!$rightToUse || $coupon == false)  && !$is_coupon_buy_only){
                $this->Session->setFlash(__('Le bon de réduction que vous avez indiqué n\'est pas valide.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
                return false;
            }


        }

		//je supprime le produit si coupon de redutcion
		if($is_coupon_buy_only){
			$requestData['Account']['produit'] = array();
		}


        //Si pas de produit sélectionné
        if(empty($requestData['Account']['produit']) && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Veuillez sélectionner un produit'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

        //Le nombre de crédits du produit
        $this->loadModel('Product');
        $productDatas = $this->Product->find('all', array(
                                'conditions' => array(
                                    'Product.id' => $requestData['Account']['produit'],
                                    'Product.active' => 1
                                ),
                                array('recursive' => -1)
        ));

        $credits = isset($productDatas['0']['Product']['credits'])?(int)$productDatas['0']['Product']['credits']:false;
        $credits_save = $credits;
        $reduction_amount = 0;
        $reduction_mode = false;

        //Si le produit est indisponible
        if(!$credits && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Ce produit n\'est plus disponible ou n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

        //Le user a-t-il une limite ?
        $user_limit_hebdo = $this->User->field('limit_credit', array('User.id' => $this->Auth->user('id')));
        if ($user_limit_hebdo !== 0){
            if (!$this->userCanBuy($user_limit_hebdo, $credits)){
                //echo $this->render('limite_atteinte');
				$this->Session->setFlash(__('Vous avez atteint la limite de crédits que vous vous êtes fixés.'), 'flash_warning');
            	$this->redirect(array('controller' => 'accounts', 'action' => 'limits'));
                return false;
            }
        }

        $total_price = (float)$productDatas['0']['Product']['tarif'];


        /* On ne garde que la langue courante dans le tableau Produits si existante */
        $found = 0;
        foreach ($productDatas['0']['ProductLang'] AS $k => $pLang){
            if ($pLang['lang_id'] == $this->Session->read('Config.id_lang')){
                $productDatas['0']['ProductLang'] = array($pLang);
                $found = true;
            }
        }

        if (!$found && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Le produit demandé n\'existe pas ou plus.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
            return false;
        }

        /*
        $cart = array(
            'product' => $productDatas['0'],
            'credits_without_voucher' => $credits_save,
            'credits_with_voucher' => $credits,
            'voucher'  => isset($voucher['Voucher'])?$voucher['Voucher']:false,
            'cgu'     => (isset($this->request->data['Account']['cgu']) && (int)$this->request->data['Account']['cgu'] == 1)?true:false,
            'cart_reference' => $this->getUniqReference(),
            'reduction_mode'   => $reduction_mode,
            'reduction_amount' => $reduction_amount,
            'user'  => array(
                'id'        => $this->Auth->user('id'),
                'email'     => $this->Auth->user('email'),
                'firstname' => $this->Auth->user('firstname'),
                'lastname'  => $this->Auth->user('lastname')
            ),
            'total_price'   => $total_price,
            'total_price_before_reduc' => (float)$productDatas['0']['Product']['tarif']
        );*/

        /* On créé le panier si nécessaire, ou on récupère */
        $this->loadModel('Cart');
        if ($this->Session->check('User.id_cart')){
            $id_cart = $this->Session->read('User.id_cart');
            $this->Cart->id = $id_cart;
        }else{
            $this->Cart->create();
        }

        $voucher_code = isset($voucher['Voucher']['code'])?$voucher['Voucher']['code']:false;
        $this->Cart->save(array(
            'user_id'       =>      $this->Auth->user('id'),
            'product_id'    =>      $productDatas['0']['Product']['id'],
            'lang_id'       =>      $this->Session->read('Config.id_lang'),
            'country_id'    =>      $this->Session->read('Config.id_country'),
            'voucher_code'  =>      $voucher_code
        ));
        $id_cart = $this->Cart->id;
        if ($this->Cart->id){
            $this->Session->write('User.id_cart', $this->Cart->id);
            $this->Session->write('User.save_id_cart_for_validation', $this->Cart->id);
        }

		//save cart loose
		$this->loadModel('CartLoose');

		$cartLoose = $this->CartLoose->find('all', array(
                                'conditions' => array(
                                    'CartLoose.id_cart' => $id_cart,
                                ),
                                array('recursive' => -1)
        ));

		if(!$cartLoose){
			$this->CartLoose->create();
			$this->CartLoose->save(array(
				'id_cart'    =>      $id_cart,
				'id_user'    =>      $this->Auth->user('id'),
				'date_cart'  =>      date('Y-m-d H:i:s'),
			));
		}
        $cart = $this->Cart->getDatas($id_cart);

        if ($cart['total_price'] < 0 && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Le bon de réduction que vous avez indiqué n\'est pas valide pour ce produit.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
            return false;
        }

        /* Calcul du token de sécurité */
            $cart['token'] = $this->getCartTokenFromCartDatas($cart);





        return $cart;

    }
	private function verifCart2()
    {
        $coupon = false;
        $requestData = $this->request->data;



        $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('produit', 'voucher'), array('produit'));
        if($requestData['Account'] === false){
            $this->Session->setFlash(__('Erreur au niveau du formulaire'), 'flash_warning');
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
        }

		$this->loadModel('Product');
		if($requestData['Account']['produit']){
			$productDatas = $this->Product->find('all', array(
									'conditions' => array(
										'Product.id' => $requestData['Account']['produit'],
										'Product.active' => 1
									),
									array('recursive' => -1)
			));
		}else{
			$productDatas = '';
		}


		$voucher_code = false;
		//Avons-nous un coupon ??
		$is_coupon_buy_only = 0;
        if(!empty($requestData['Account']['voucher'])){

			$this->loadModel('GiftOrder');
			$gift = $this->GiftOrder->find('first',array(
							'conditions' => array(
								'GiftOrder.valid' => 1,
								'GiftOrder.date_validity >=' => date('Y-m-d H:i:s'),
								'GiftOrder.code' => $requestData['Account']['voucher'],
							),
							'recursive' => -1,
						));

			if(!$gift){

				$this->loadModel('Voucher');
				//Le client peut-il l'utiliser ??
				if($this->Auth->user('id'))
					 $rightToUse = $this->Voucher->rightToUse($requestData['Account']['voucher'], $this->Auth->user('personal_code'), $this->Auth->user('id'), $requestData['Account']['produit']);
						 else
					$rightToUse = $this->Voucher->rightToUsePublic($requestData['Account']['voucher'], $requestData['Account']['produit']);


				if($rightToUse){
					//on récupère le coupon
					$voucher = $this->Voucher->find('first', array(
						'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips'),
						'conditions'    => array('Voucher.code' => $requestData['Account']['voucher']),
						'recursive'     =>-1
					));

					$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
				}


				//check IP
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$list_block_ip = explode(',',$voucher['Voucher']['ips']);
				if(in_array($ip_user,$list_block_ip)){
					$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client.'), 'flash_warning');
					$this->redirect(array('controller' => 'products', 'action' => 'tarif'));
					return false;
				}


				$is_coupon_buy_only = $voucher['Voucher']['buy_only'];


				if ((!$rightToUse || $coupon == false)  && !$is_coupon_buy_only){
					$this->Session->setFlash(__('Le bon de réduction que vous avez indiqué n\'est pas valide.'), 'flash_warning');
					$this->redirect(array('controller' => 'products', 'action' => 'tarif'));
					return false;
				}
				$voucher_code = isset($voucher['Voucher']['code'])?$voucher['Voucher']['code']:false;
			}else{
				$rightToUse = true;
				$voucher_code = isset($gift['GiftOrder']['code'])?$gift['GiftOrder']['code']:false;
				$price = isset($productDatas['0']['Product']['tarif'])?(int)$productDatas['0']['Product']['tarif']:false;
				if($gift['GiftOrder']['amount'] >= $price)$is_coupon_buy_only = 1;
			}

        }

		//je supprime le produit si coupon de redutcion
		if($is_coupon_buy_only){
			$requestData['Account']['produit'] = array();
		}


        //Si pas de produit sélectionné
        if(empty($requestData['Account']['produit']) && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Veuillez sélectionner un produit'), 'flash_warning');
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
        }

        //Le nombre de crédits du produit
        $this->loadModel('Product');
        $productDatas = $this->Product->find('all', array(
                                'conditions' => array(
                                    'Product.id' => $requestData['Account']['produit'],
                                    'Product.active' => 1
                                ),
                                array('recursive' => -1)
        ));

        $credits = isset($productDatas['0']['Product']['credits'])?(int)$productDatas['0']['Product']['credits']:false;
        $credits_save = $credits;
        $reduction_amount = 0;
        $reduction_mode = false;

        //Si le produit est indisponible
        if(!$credits && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Ce produit n\'est plus disponible ou n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
        }

        //Le user a-t-il une limite ?
        $user_limit_hebdo = $this->User->field('limit_credit', array('User.id' => $this->Auth->user('id')));
        if ($user_limit_hebdo !== 0){
            if (!$this->userCanBuy($user_limit_hebdo, $productDatas['0']['Product']['tarif'])){
               // echo $this->render('limite_atteinte');
				$this->Session->setFlash(__('Vous avez atteint la limite de crédits que vous vous êtes fixés.'), 'flash_warning');
            	$this->redirect(array('controller' => 'accounts', 'action' => 'limits'));
                return false;
            }
        }

        $total_price = (float)$productDatas['0']['Product']['tarif'];


        /* On ne garde que la langue courante dans le tableau Produits si existante */
        $found = 0;
        foreach ($productDatas['0']['ProductLang'] AS $k => $pLang){
            if ($pLang['lang_id'] == $this->Session->read('Config.id_lang')){
                $productDatas['0']['ProductLang'] = array($pLang);
                $found = true;
            }
        }

        if (!$found && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Le produit demandé n\'existe pas ou plus.'), 'flash_warning');
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
            return false;
        }


        /* On créé le panier si nécessaire, ou on récupère */
        $this->loadModel('Cart');
        if ($this->Session->check('User.id_cart')){
            $id_cart = $this->Session->read('User.id_cart');
            $this->Cart->id = $id_cart;
        }else{
            $this->Cart->create();
        }


        $this->Cart->save(array(
            'user_id'       =>      $this->Auth->user('id'),
            'product_id'    =>      $productDatas['0']['Product']['id'],
            'lang_id'       =>      $this->Session->read('Config.id_lang'),
            'country_id'    =>      $this->Session->read('Config.id_country'),
            'voucher_code'  =>      $voucher_code
        ));
        $id_cart = $this->Cart->id;
        if ($this->Cart->id){
            $this->Session->write('User.id_cart', $this->Cart->id);
            $this->Session->write('User.save_id_cart_for_validation', $this->Cart->id);
        }

		//save cart loose
		$this->loadModel('CartLoose');

		$cartLoose = $this->CartLoose->find('all', array(
                                'conditions' => array(
                                    'CartLoose.id_cart' => $id_cart,
                                ),
                                array('recursive' => -1)
        ));

		if(!$cartLoose){
			$this->CartLoose->create();
			$this->CartLoose->save(array(
				'id_cart'    =>      $id_cart,
				'id_user'    =>      $this->Auth->user('id'),
				'date_cart'  =>      date('Y-m-d H:i:s'),
			));
		}
        $cart = $this->Cart->getDatas($id_cart);

        if ($cart['total_price'] < 0 && !$is_coupon_buy_only){
            $this->Session->setFlash(__('Le bon de réduction que vous avez indiqué n\'est pas valide pour ce produit.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'cart'));
            return false;
        }

        /* Calcul du token de sécurité */
        $cart['token'] = $this->getCartTokenFromCartDatas($cart);
		if($is_coupon_buy_only)$cart['voucher']['buy_only'] = 1;

        return $cart;

    }
    private function _buyCreditUser(){
        $this->verifCart();

        //On update le crédit de l'user
        $newCredit = $this->updateCredit($this->Auth->user('id'),$credits,false);

        if($newCredit === false)
            $this->Session->setFlash(__('Erreur dans la mise à jour de votre crédit.'),'flash_warning');
        else{
            //Date d'aujourd'hui
            $dateNow = date('Y-m-d H:i:s');
            //On save l'achat des crédits
            $this->loadModel('UserCredit');
            $this->UserCredit->create();
            $this->UserCredit->save(array(
                'credits'   => $credits,
                'product_id' => $requestData['Account']['produit'],
                'date_upd'  => $dateNow,
                'users_id'  => $this->Auth->user('id')
            ));
            //On save l'historique coupon, s'il y a eu un coupon
            if(isset($coupon) && $coupon){
                $this->loadModel('VoucherHistory');
                $this->VoucherHistory->create();
                $this->VoucherHistory->save(array(
                    'user_id'           => $this->Auth->user('id'),
                    'code'              => $voucher['Voucher']['code'],
                    'transaction_id'    => 0,   // !!!!!!!!!!!!!! Model UserCredit n'a pas d'ID, lorsqu'il y aura l'api bancaire faut changer le Model UserCredit !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    'credit'            => $voucher['Voucher']['credit'],
                    'date_add'          => $dateNow
                ));
            }

            //On met à jour la session
            CakeSession::write(array('Auth.User.credit' => $newCredit));
            CakeSession::write(array('Auth.User.credit_recharge' => $credits));
            $this->Session->setFlash(__('Votre compte a été crédité de').' '.$credits.' '.__('crédits'),'flash_success');
        }

        $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits_confirmation'));
    }

    private function userCanBuy($limit_credit_by_week=0, $want_credits=0)
    {
        if ($limit_credit_by_week == 0)return true;
        $credits_week_bought = $this->getUserCreditHebdoEncours();
        $parms = array(
            '##PARM_LIMITE##'           =>  $limit_credit_by_week,
            '##PARM_CREDITS_WEEK##'     =>  $credits_week_bought,
            '##PARM_CREDITS_BUYABLE##'     =>  ($limit_credit_by_week-$credits_week_bought)>0?($limit_credit_by_week-$credits_week_bought):0
        );
        $this->set(compact('parms'));

        return (($want_credits + $credits_week_bought) <= ($limit_credit_by_week));
    }

    //Retourne les experts favoris sur lesquels l'user peut mettre un avis
    private function getAgentFavorite($pseudo = false){
        //On récupère les voyants avec qui le client a eu affaire, ainsi que la date de son dernier contact
        $this->loadModel('UserCreditLastHistory');
        $arrayIdAgent = $this->UserCreditLastHistory->find('list',array(
            'fields' => array('agent_id','date_start'),
            'conditions' => array('users_id' => $this->Auth->user('id')),
            'recursive' => -1,
            'order' => 'date_start'
        ));

        //Avis illimité ??
        if(!Configure::read('Site.unlimitedReview')){
            $this->loadModel('Review');
            //On récupère la date du dernier avis de l'user pour chaque voyant avec qui l'user a eu affaire
            $idAgentReview = $this->Review->find('list',array(
                'fields' => array('agent_id','date_add'),
                'conditions' => array('agent_id' => array_keys($arrayIdAgent),'user_id' => $this->Auth->user('id'),'status !=' => 0),
                'recursive' => -1,
                'order' => 'date_add'
            ));

            //Pour chaque date des avis
            foreach ($idAgentReview as $idAgent => $dateReview){
                //Si la date de l'avis est plus grande que la date de son dernier contact avec le voyant
                if($dateReview > $arrayIdAgent[$idAgent])
                    //Alors on retire le voyant de la liste
                    unset($arrayIdAgent[$idAgent]);
            }
        }

        //On garde que les id des agents (on perd les dates)
        $arrayIdAgent = array_keys($arrayIdAgent);

        //On récupère les pseudos et codes agents pour chaque voyant de la liste
        $voyants = $this->User->find('list',array(
            'fields' => ($pseudo ?array('agent_number', 'pseudo'):array('agent_number')),   //Doit-on retourner le pseudo ??
            'conditions' => array('id' => $arrayIdAgent, 'deleted' => 0, 'active' => 1),
            'recursive' => -1
        ));

        return $voyants;
    }

    //-------------------------------------------------------------------------------------------ADMIN-----------------------------------------------------------------------------------------------------------------

    public function admin_index(){
        //On récupère les datas pour la vue
		if($this->Session->check('DateClient')){
            $conditions = array(
                'User.date_add >=' => CakeTime::format($this->Session->read('DateClient.start'), '%Y-%m-%d 00:00:00'),
                'User.date_add <=' => CakeTime::format($this->Session->read('DateClient.end'), '%Y-%m-%d 23:59:59')
            );
        }

        $users = $this->_adminIndex('Accounts',
            true,
            false,$conditions);
        $this->set(array('users' => $users));
    }

	public function admin_export_new_customer(){


        $this->loadModel('UserCreditHistory');
		$conditions = array('UserCreditHistory.is_new'=>1);
		//$conditions = array('(SELECT COMM.date_start from user_credit_history COMM where COMM.user_id = UserCreditHistory.user_id order by COMM.date_start asc LIMIT 1) = `UserCreditHistory`.`date_start`');

		if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserCreditHistory.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserCreditHistory.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }

		//On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('UserCreditHistory.*','User.*', 'Agent.*',
								 ),
                'conditions' => $conditions,
                'order' => 'UserCreditHistory.date_start desc',
                /*'joins' => array(
					array('table' => 'users',
						'alias' => 'User',
						'type' => 'left',
						'conditions' => array(
							'User.id = UserCreditHistory.user_id',
						)
					)
				),*/
				'paramType' => 'querystring',
                'limit' => 25
            );

			$lastCom = $this->Paginator->paginate($this->UserCreditHistory);

        $this->set(compact('lastCom'));
	}


    public function admin_edit($id){
        //On récupère les données de l'user
		 $this->loadModel('UserLevel');
		//verifie si le code est pas pris par autre client
		$post_data = $this->request->data['Account'];
		$check = $this->User->find('first',array(
            'fields' => (array('personal_code')),
            'conditions' => array('id !=' => $id, 'deleted' => 0, 'active' => 1, 'personal_code' => $post_data['personal_code']),
            'recursive' => -1
        ));
		if($check['User']['personal_code']){
			$this->Session->setFlash(__('Le code client est deja attribué.'), 'flash_warning');
		}else{

        $user = $this->_adminEdit($id,
            array('firstname','email','country_id','lastname','passwd','passwd2','address','postalcode','city', 'phone_number', 'personal_code','credit','credit_old','subscribe_mail'),
            array('firstname','email','country_id')
            ,'Account');
		}
		$user_co = $this->Session->read('Auth.User');
		$level = $this->UserLevel->find('first', array(
						'conditions' => array('UserLevel.user_id' => $user_co['id']),
						'recursive' => -1
					));
		$level = $level['UserLevel']['level'];



        $this->set(compact('user','level'));
    }
/*
    public function admin_view($id){
        $user = $this->User->find('first',array(
            'fields' => array('User.*','UserCountryLang.name','UserCountryLang.name'),
            'conditions' => array('User.id' => $id, 'User.role' => 'client'),
            'joins' => array(
                array('table' => 'user_country_langs',
                    'alias' => 'UserCountryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'UserCountryLang.user_countries_id = User.country_id',
                        'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                    )
                )
            ),
            'recursive' => -1,
        ));

        if(!empty($user)){
            $this->loadModel('UserCredit');
            $this->loadModel('UserCreditHistory');
            $this->loadModel('AdminNote');
			$this->loadModel('UserIp');
			$this->loadModel('LoyaltyCredit');
			$this->loadModel('Sponsorship');
			$this->loadModel('GiftOrder');

            //On récupère la note de l'admin
            $user['User']['admin_note'] = $this->AdminNote->field('note', array('user_id' => $id));

            //L'historique des achats
            $historiqueCredit = $this->UserCredit->find('all',array(
                'fields' => array('UserCredit.credits','UserCredit.product_name','UserCredit.payment_mode', 'UserCredit.date_upd', 'Orders.voucher_code','Orders.cart_id', 'Orders.voucher_name', 'Orders.voucher_code', 'Orders.valid', 'Orders.payment_mode','Orders.id_com', 'Hipay.transaction','Paypal.payment_transactionid','Stripe.id', 'Orders.total', 'Orders.currency','Sepa.charge_id'),
                'conditions' => array('UserCredit.users_id' => $id),
                'order' => 'UserCredit.date_upd DESC',
				 'joins' => array(
						array('table' => 'orders',
							'alias' => 'Orders',
							'type' => 'left',
							'conditions' => array(
								'Orders.id = UserCredit.order_id'
							)
						),
					 	array('table' => 'order_hipaytransactions',
							'alias' => 'Hipay',
							'type' => 'left',
							'conditions' => array(
								'Hipay.cart_id = Orders.cart_id'
							)
						),
					 	array('table' => 'order_paypaltransactions',
							'alias' => 'Paypal',
							'type' => 'left',
							'conditions' => array(
								'Paypal.cart_id = Orders.cart_id',
								'Paypal.payment_status = \'completed\''
							)
						),
					 array('table' => 'order_stripetransactions',
							'alias' => 'Stripe',
							'type' => 'left',
							'conditions' => array(
								'Stripe.cart_id = Orders.cart_id',
							)
						),
					 array('table' => 'order_sepatransactions',
							'alias' => 'Sepa',
							'type' => 'left',
							'conditions' => array(
								'Sepa.cart_id = Orders.cart_id',
							)
						)
					),
                'recursive' => -1,
                'limit' => Configure::read('Site.limitStatistique')
            ));

			foreach($historiqueCredit as &$histo){
				if(!$histo['Orders']['voucher_name'] && $histo['Orders']['voucher_code']){
					$gift = $this->GiftOrder->find('first',array(
						'fields' => array('GiftOrder.*','Gift.name','Gift.amount'),
						'conditions' => array('GiftOrder.code' => $histo['Orders']['voucher_code']),
						'joins' => array(
						array('table' => 'gifts',
							'alias' => 'Gift',
							'type' => 'left',
							'conditions' => array(
								'Gift.id = GiftOrder.gift_id'
							)
						),
					),
						'recursive' => -1,
					));
					if($gift)
						$histo['Orders']['voucher_name'] = $gift['Gift']['name'].' '.$gift['Gift']['amount'];
				}
			}

            //L'historique des communicatioins
            $historiqueCom = $this->UserCreditHistory->find('all',array(
                'conditions' => array('UserCreditHistory.user_id' => $id),
                'order' => 'date_start DESC',
                'recursive' => -1,
                'limit' => Configure::read('Site.limitStatistique')
            ));

			//L'historique des gains
            $loyaltyCom = $this->LoyaltyCredit->find('all',array(
                'conditions' => array('LoyaltyCredit.user_id' => $id, 'LoyaltyCredit.valid' => 1),
                'order' => 'date_add DESC',
                'recursive' => -1,
                'limit' => Configure::read('Site.limitStatistique')
            ));

            //Nom des champs en format humain
            $nameField = array('fullname' => 'Nom complet', 'email' => 'Adresse mail', 'birthdate' => 'Date de naissance', 'address' => 'Adresse', 'country_id' => 'Pays de résidence', 'emailConfirm' => 'Email',
                               'valid' => 'Compte', 'date_add' => 'Inscription', 'date_upd' => 'Dernière modification', 'date_lastconnexion' => 'Dernière connexion', 'personal_code' => 'Code personnel',
                                'limit_credit' => 'Limite de rechargement', 'phone_number' => 'Numéro de téléphone', 'credit' => 'Crédit', 'credit_old' => 'Crédit périmé','source' => 'Source');

            //On formate la sortie des données
            foreach($user['User'] as $key => $val){
                switch ($key){
                    case 'country_id' :
                        $user['User'][$key] = $user['UserCountryLang']['name'];
                        break;
                    case 'address' :
                        $user['User'][$key] = $user['User']['address'].' '.$user['User']['postalcode'].' '.$user['User']['city'];
                        break;
                    case 'birthdate' :
                    case 'date_add' :
                    case 'date_upd' :
                        if(empty($val)){
                            $user['User'][$key] = __('N/D');
                            continue;
                        }
                        $user['User'][$key] = CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$val), '%d %B %Y %H:%M');
                        break;
                    case 'phone_number' :
                    case 'limit_credit' :
                        if(empty($val))
                            $user['User'][$key] = __('N/D');
                        break;
                }
            }
            //Données supplémentaires
            $user['User']['fullname'] = $user['User']['firstname'].' '.$user['User']['lastname'];
            unset($user['UserCountryLang']);

			//L'historique des Ip
            $userIp = $this->UserIp->find('all',array(
                'conditions' => array('UserIp.user_id' => $id),
                'order' => 'date_conn DESC',
                'recursive' => -1
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


			$sponsorships = $this->Sponsorship->find('all',array(
                'fields' => array('Sponsorship.*','Filleul.*',),
                'conditions' => array('Sponsorship.user_id' => $user['User']['id'], 'Sponsorship.is_recup'=>1),
                'recursive' => -1,
                'joins' => array(
                    array('table' => 'users',
                          'alias' => 'Filleul',
                          'type' => 'left',
                          'conditions' => array(
                              'Filleul.id = Sponsorship.id_customer',
                          )
                    )
                ),
				'order' => 'Sponsorship.date_add desc',
				'limit' => Configure::read('Site.limitStatistique')
            ));
        }

        $this->set(compact('user', 'nameField', 'historiqueCredit', 'historiqueCom', 'userIp','userNotIp', 'loyaltyCom','sponsorships'));
    }
*/
    public function admin_credit(){
        $this->loadModel('UserCredit');
        //Les conditions de base
        $conditions = array('User.role' => 'client', 'User.deleted' => 0);

        //Avons-nous un filtre sur la date ??
        if($this->Session->check('Date')){
            $conditions = array_merge($conditions, array(
                'UserCredit.date_upd >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                'UserCredit.date_upd <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
            ));
        }

		if($this->params->data['UserCredit']){
				$conditions_order = array(
					'Orders.id = UserCredit.order_id',
                    'Orders.IP' => $this->params->data['UserCredit']['IP']
                );
			}else{
				$conditions_order = array(
					'Orders.id = UserCredit.order_id',
                );
		}
/*
        //Les dernieres dates des achats des clients
        $lastDate = $this->UserCredit->find('list',array(
            'fields' => array('UserCredit.users_id', 'UserCredit.date_upd'),
            'conditions' => $conditions,
            'order' => 'UserCredit.date_upd',
			'joins' => array(
						array('table' => 'orders',
							'alias' => 'Orders',
							'type' => 'left',
							'conditions' => array(
								'Orders.id = UserCredit.order_id'
							)
						)
					),
            'recursive' => 1
        ));

        //On récupère les id user
        $idUsers = array_keys($lastDate);*/

        //On récupère les infos du dernier achat pour les clients
        $this->Paginator->settings = array(
            'fields' => array('UserCredit.*','Orders.*', 'User.firstname', 'User.lastname'),
           /* 'conditions' => array('UserCredit.users_id' => $idUsers, 'UserCredit.date_upd' => $lastDate),*/
			'conditions' => $conditions,
            'order' => 'UserCredit.date_upd DESC',
            'paramType' => 'querystring',
			'joins' => array(
						array('table' => 'orders',
							'alias' => 'Orders',
							'type' => 'inner',
							'conditions' => $conditions_order
						)
					),
            'limit' => 15
        );

        $lastCredit = $this->Paginator->paginate($this->UserCredit);

        $this->set(compact('lastCredit'));
    }

    public function admin_credit_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.firstname'),
            'conditions' => array('User.id' => $id, 'User.role' => 'client'),
            'recursive' => -1
        ));

        if(!empty($user)){
            $this->loadModel('UserCredit');

            //Les conditions de base
            $conditions = array('UserCredit.users_id' => $id);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserCredit.date_upd >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserCredit.date_upd <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }
			if($this->params->data['UserCredit']){
				$conditions_order = array(
					'Orders.id = UserCredit.order_id',
                    'Orders.IP' => $this->params->data['UserCredit']['IP']
                );
			}else{
				$conditions_order = array(
					'Orders.id = UserCredit.order_id',
                );
			}
            //Tout les achats du client
            $this->Paginator->settings = array(
                'fields' => array('UserCredit.credits','UserCredit.product_name', 'UserCredit.payment_mode',  'UserCredit.date_upd', 'Product.tarif', 'Product.country_id', 'ProductLang.name', 'Orders.voucher_code', 'Orders.voucher_name','Orders.voucher_code','Orders.valid', 'Orders.cookie_id','Orders.IP','Orders.id_com', 'Hipay.transaction','Paypal.payment_transactionid','Stripe.id', 'Orders.total', 'Orders.currency','Sepa.charge_id'),
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'table' => 'product_langs',
                        'alias' => 'ProductLang',
                        'type' => 'left',
                        'conditions' => array(
                            'ProductLang.lang_id = '.$this->Session->read('Config.id_lang'),
                            'ProductLang.product_id = UserCredit.product_id'
                        )
                    ),
						array('table' => 'orders',
							'alias' => 'Orders',
							'type' => 'inner',
							'conditions' => $conditions_order
						),
					 	array('table' => 'order_hipaytransactions',
							'alias' => 'Hipay',
							'type' => 'left',
							'conditions' => array(
								'Hipay.cart_id = Orders.cart_id'
							)
						),
					 	array('table' => 'order_paypaltransactions',
							'alias' => 'Paypal',
							'type' => 'left',
							'conditions' => array(
								'Paypal.cart_id = Orders.cart_id',
								'Paypal.payment_status = \'completed\''
							)
						),
					 array('table' => 'order_stripetransactions',
							'alias' => 'Stripe',
							'type' => 'left',
							'conditions' => array(
								'Stripe.cart_id = Orders.cart_id',
							)
						),
					 array('table' => 'order_sepatransactions',
							'alias' => 'Sepa',
							'type' => 'left',
							'conditions' => array(
								'Sepa.cart_id = Orders.cart_id',
							)
						)
                ),
                'order' => 'UserCredit.date_upd desc',
                'paramType' => 'querystring',
                'limit' => 25
            );


            $allCredits = $this->Paginator->paginate($this->UserCredit);

			$this->loadModel('GiftOrder');
			foreach($allCredits as &$histo){
				if(!$histo['Orders']['voucher_name'] && $histo['Orders']['voucher_code']){
					$gift = $this->GiftOrder->find('first',array(
						'fields' => array('GiftOrder.*','Gift.name','Gift.amount'),
						'conditions' => array('GiftOrder.code' => $histo['Orders']['voucher_code']),
						'joins' => array(
						array('table' => 'gifts',
							'alias' => 'Gift',
							'type' => 'left',
							'conditions' => array(
								'Gift.id = GiftOrder.gift_id'
							)
						),
					),
						'recursive' => -1,
					));
					if($gift)
						$histo['Orders']['voucher_name'] = $gift['Gift']['name'].' '.$gift['Gift']['amount'];
				}
			}

            //On récupère les devise pour chaque pays
            $this->loadModel('Country');
            $devises = $this->Country->find('list', array(
                'fields'        => array('Country.id', 'Country.devise'),
                'recursive'     => -1
            ));

            $this->set(compact('user','allCredits', 'devises'));
        }else{
            $this->Session->setFlash(__('Aucun client trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'credit', 'admin' => true), false);
        }
    }

    public function admin_com(){
        //Voir méthode dans extranet
        $this->_adminCom('user_id');
    }

    public function admin_com_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.firstname'),
            'conditions' => array('User.id' => $id, 'User.role' => 'client'),
            'recursive' => -1
        ));

        if(!empty($user)){
            //Charge model
            $this->loadModel('UserCreditHistory');

            //Les conditions de base
            $conditions = array('UserCreditHistory.user_id' => $id);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserCreditHistory.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserCreditHistory.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }

            //Avons-nous un filtre sur les medias ??
            if($this->Session->check('Media'))
                $conditions = array_merge($conditions, array('UserCreditHistory.media' => $this->Session->read('Media.value')));

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('UserCreditHistory.agent_pseudo','UserCreditHistory.sessionid', 'UserCreditHistory.agent_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.media', 'UserCreditHistory.credits', 'UserCreditHistory.seconds', 'UserCreditHistory.user_credits_before', 'UserCreditHistory.date_start', 'UserCreditHistory.user_credit_history'),
                'conditions' => $conditions,
                'order' => 'UserCreditHistory.date_start desc',
                'paramType' => 'querystring',
                'limit' => 25
            );

            $allComs = $this->Paginator->paginate($this->UserCreditHistory);

            $this->set(compact('user','allComs'));
        }else{
            $this->Session->setFlash(__('Aucun client trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'com', 'admin' => true), false);
        }
    }

	public function admin_comlosttchat(){


		$this->loadModel('Chat');
		$this->loadModel('ChatMessage');

        //Les conditions de base
        $conditions = array('Chat.consult_date_start' => NULL, 'Chat.etat' => 1, 'Chat.closed_by !=' => 'client_timeout','(SELECT COUNT(*) FROM chat_messages WHERE chat_messages.chat_id = Chat.id and chat_messages.user_id = Chat.to_id) = 0');

       //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'Chat.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'Chat.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }


       //On récupère l'historique entier
       $this->Paginator->settings = array(
                'fields' => array('Chat.*', 'User.*', 'Agent.*'),
                'conditions' => $conditions,
                'order' => 'Chat.date_start desc',
                'paramType' => 'querystring',
                'limit' => 25
            );

       $allComs = $this->Paginator->paginate($this->Chat);
       $this->set(compact('allComs'));


     }

	public function admin_comlostcall(){


        $this->loadModel('Callinfo');
		$this->Callinfo->useTable = 'call_infos';

        //Les conditions de base
            $conditions = array(
				'Callinfo.agent !=' => '',

				'OR' => array(
					'Callinfo.accepted' => 'no',
					'Callinfo.reason' => array('NOANSWER','BUSY','CHANUNAVAIL','CANCEL'),
					'Callinfo.agent1 !=' => '',
					'Callinfo.agent2 !=' => '',
					'Callinfo.agent3 !=' => '',
					'Callinfo.agent4 !=' => '',
					'Callinfo.agent5 !=' => '',
				));

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
				$date_start = new DateTime($this->Session->read('Date.start'));
				$date_end = new DateTime($this->Session->read('Date.end').' 23:59:59');

                $conditions = array_merge($conditions, array(
                    'Callinfo.timestamp >=' =>$date_start->getTimestamp(),
                    'Callinfo.timestamp <=' => $date_end->getTimestamp()
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('Callinfo.timestamp','Callinfo.status','Callinfo.reason','Callinfo.date_send','Callinfo.callerid','Callinfo.customer',  'Agent.id','Agent.pseudo','Callinfo.time_getstatut','Callinfo.time_stop','Callinfo.time_start','Callinfo.agent','Callinfo.agent1','Callinfo.agent2','Callinfo.agent3','Callinfo.agent4','Callinfo.agent5','Callinfo.sessionid'),//, 'User.id', 'User.firstname', 'User.lastname'
                'conditions' => $conditions,
				'joins' => array(

		   		array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type'  => 'left',
                    'conditions' => array(
                        'Agent.agent_number = Callinfo.agent',
                    )
                )
            ),
                'order' => 'Callinfo.timestamp desc',
                'paramType' => 'querystring',
                'limit' => 25
            );
			/*
			 array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array(
                        'User.personal_code = Callinfo.customer',
                    )
                ),
			*/

            $allComs = $this->Paginator->paginate($this->Callinfo);

			foreach($allComs as &$comm){
				if($comm['Callinfo']['customer']){
					$client_sql = $this->User->find('first', array(
						'conditions' => array('User.personal_code' => $comm['Callinfo']['customer']),
						'recursive' => -1
					));

					if($client_sql['User']['firstname']){
						$comm['User'] = $client_sql['User'];
					}
				}
				if($comm['Callinfo']['agent1']){

				$customer = $this->User->find('first', array(
						'conditions' => array('User.agent_number' => $comm['Callinfo']['agent1']),
						'recursive' => -1
					));
				$comm['Agent1'] = $customer['User'] ;
			}
			if($comm['Callinfo']['agent2']){

				$customer = $this->User->find('first', array(
						'conditions' => array('User.agent_number' => $comm['Callinfo']['agent2']),
						'recursive' => -1
					));
				$comm['Agent2'] = $customer['User'] ;
			}
			if($comm['Callinfo']['agent3']){

				$customer = $this->User->find('first', array(
						'conditions' => array('User.agent_number' => $comm['Callinfo']['agent3']),
						'recursive' => -1
					));
				$comm['Agent3'] = $customer['User'] ;
			}
			if($comm['Callinfo']['agent4']){

				$customer = $this->User->find('first', array(
						'conditions' => array('User.agent_number' => $comm['Callinfo']['agent4']),
						'recursive' => -1
					));
				$comm['Agent4'] = $customer['User'] ;
			}
			if($comm['Callinfo']['agent5']){

				$customer = $this->User->find('first', array(
						'conditions' => array('User.agent_number' => $comm['Callinfo']['agent5']),
						'recursive' => -1
					));
				$comm['Agent5'] = $customer['User'] ;
			}
			}
		//var_dump($allComs);exit;
            $this->set(compact('user','allComs'));


     }


	public function admin_loyalty_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.firstname'),
            'conditions' => array('User.id' => $id, 'User.role' => 'client', 'User.deleted' => 0),
            'recursive' => -1
        ));

        if(!empty($user)){
            //Charge model
            $this->loadModel('LoyaltyCredit');

            //Les conditions de base
            $conditions = array('LoyaltyCredit.user_id' => $id, 'LoyaltyCredit.valid' => 1);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'LoyaltyCredit.date_add >=' => CakeTime::format($this->Session->read('Date.add'), '%Y-%m-%d 00:00:00'),
                    'LoyaltyCredit.date_add <=' => CakeTime::format($this->Session->read('Date.add'), '%Y-%m-%d 23:59:59')
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('LoyaltyCredit.date_add'),
                'conditions' => $conditions,
                'order' => 'LoyaltyCredit.date_add desc',
                'paramType' => 'querystring',
                'limit' => 25
            );

            $allLoyaltys = $this->Paginator->paginate($this->LoyaltyCredit);

            $this->set(compact('user','allLoyaltys'));
        }else{
            $this->Session->setFlash(__('Aucun client trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'com', 'admin' => true), false);
        }
    }

	public function admin_sponsorship_view($id){
        $user = $this->User->find('first', array(
            'fields' => array('User.id', 'User.firstname'),
            'conditions' => array('User.id' => $id, 'User.role' => 'client', 'User.deleted' => 0),
            'recursive' => -1
        ));

        if(!empty($user)){
            //Charge model
            $this->loadModel('Sponsorship');

            //Les conditions de base
            $conditions = array('Sponsorship.user_id' => $id, 'Sponsorship.is_recup' => 1);

            //Avons-nous un filtre sur la date ??
            if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'Sponsorship.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'Sponsorship.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }

            //On récupère l'historique entier
            $this->Paginator->settings = array(
                'fields' => array('Sponsorship.*','Filleul.*'),
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

            $allSponsorships = $this->Paginator->paginate($this->Sponsorship);

            $this->set(compact('user','allSponsorships'));
        }else{
            $this->Session->setFlash(__('Aucun client trouvé'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'com', 'admin' => true), false);
        }
    }


    //Export des données de communication
    public function admin_export_com(){
        return $this->_adminExportCom('accounts');
    }
	//Export des données de communication
    public function admin_export_com_tranche(){
        return $this->_adminExportComTranche('accounts');
    }

	public function admin_export_com_new(){
        set_time_limit ( 0 );

        //Charge model
        $this->loadModel('UserCreditHistory');
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

        $conditions = array('UserCreditHistory.is_new'=>1);
        //Filtre par date ??
        if($this->Session->check('Date')){
            $conditions = array_merge($conditions, array(
                'UserCreditHistory.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                'UserCreditHistory.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
            ));
        }

        //Les données à sortir
        $allComDatas = $this->UserCreditHistory->find('all', array(
            'fields'        => array('UserCreditHistory.*', 'User.id', 'User.credit','User.source', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id',
                'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number',
            'Agent.lastname AS agent_lastname', 'Agent.email'),
            'conditions'    => $conditions,
            'order'         => 'UserCreditHistory.date_start DESC'
        ));

        //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'accounts', 'action' => 'export_new_customer', 'admin' => true), false);
            else
                $this->redirect($source);
        }

        //Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_export';


        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

		$dbb_r = new DATABASE_CONFIG();
		$dbb_r = $dbb_r->default;
		$mysqli = new mysqli($dbb_r['host'], $dbb_r['login'], $dbb_r['password'], $dbb_r['database']);

        foreach($allComDatas as $indice => $row){


			$code_iso = '';
			$resultcc = $mysqli->query("SELECT domain from domains where id = '{$row['UserCreditHistory']['domain_id']}'");
			$rowcc = $resultcc->fetch_array(MYSQLI_ASSOC);
			$code_iso = $rowcc['domain'];

			switch ($code_iso) {
				case "www.talkappdev.com":
					$code_iso = 'France';
					break;
				case "www.spiriteo.ca":
					$code_iso = 'Canada';
					break;
				case "www.spiriteo.be":
					$code_iso = 'Belgique';
					break;
				case "www.spiriteo.ch":
					$code_iso = 'Suisse';
					break;
				case "www.spiriteo.lu":
					$code_iso = 'Luxembourg';
					break;
				case "fr.spiriteo.com":
					$code_iso = 'France';
					break;
				case "ca.spiriteo.com":
					$code_iso = 'Canada';
					break;
				case "be.spiriteo.com":
					$code_iso = 'Belgique';
					break;
				case "ch.spiriteo.com":
					$code_iso = 'Suisse';
					break;
				case "lu.spiriteo.com":
					$code_iso = 'Luxembourg';
					break;
			}

			$called = '';


			$timing = explode(' ',$row['UserCreditHistory']['date_start']);
			$heures = intval(($row['UserCreditHistory']['seconds']) / 60 / 60);
			$minutes = intval(($row['UserCreditHistory']['seconds'] % 3600) / 60);
			$secondes =intval((($row['UserCreditHistory']['seconds'] % 3600) % 60));


			$result = $mysqli->query("SELECT price from user_pay where id_user_credit_history = '{$row['UserCreditHistory']['user_credit_history']}'");
			$row2 = $result->fetch_array(MYSQLI_ASSOC);
			$price = $row2['price'];

			$result = $mysqli->query("SELECT * from call_infos where sessionid = '{$row['UserCreditHistory']['sessionid']}'");
			$row3 = $result->fetch_array(MYSQLI_ASSOC);
			$caller = $row3['callerid'];
			$caller_line = $row3['line'];
			/*
			switch ($row['UserCreditHistory']['called_number']) {
				case 901801885:
					$called = 'Suisse audiotel';
					$code_iso = 'Suisse';
					break;
				case 41225183456:
					$called = 'Suisse prepaye';
					$code_iso = 'Suisse';
					break;
				case 90755456:
					$called = 'Belgique audiotel';
					$code_iso = 'Belgique';
					break;
				case 3235553456:
					$called = 'Belgique prepaye';
					$code_iso = 'Belgique';
					break;
				case 90128222:
					$called = 'Luxembourg audiotel';
					$code_iso = 'Luxembourg';
					break;
				case 27864456:
					$called = 'Luxembourg prepaye';
					$code_iso = 'Luxembourg';
					break;
				case 4466:
					$called = 'Canada audiotel mobile';
					$code_iso = 'Canada';
					break;
				case 19007884466:
					$called = 'Canada audiotel fixe';
					$code_iso = 'Canada';
					break;
				case 18442514456:
					$called = 'Canada prepaye';
					$code_iso = 'Canada';
					break;
				case 33970736456:
					$called = 'France prepaye';
					$code_iso = 'France';
					break;
			}
*/

			switch ($row3['line']) {
							case 'CH-0901801885':
							case 'CH-+41225183456':
								$called = 'Suisse audiotel';
								break;
							case 'BE-090755456':
							case 'BE-+3235553456':
								$called = 'Belgique audiotel';
								break;
							case 'BE-090755456 mob.':
								$called = 'Belgique mob. audiotel';
								break;
							case 'LU-+35227864456':
							case 'LU-90128222':
								$called = 'Luxembourg audiotel';
								break;
							case 'CA-+18442514456':
							case 'CA-19007884466':
								$called = 'Canada audiotel';
								break;
							case 'CA-#4466 Bell':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Rogers/Fido':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Telus':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Videotron':
							case 'AT-431230460013':
								$called = 'Canada mob. audiotel';
								break;
						}


			//if($row['User']['personal_code'] < 999990 && $row['User']['personal_code'] > 999999){

			if($caller == 'UNKNOWN')$caller = '';
			$date_1_appel_old = '';	$nb_appels = '';$nb_appels_today = ''; $date_1_appel = '';

			if(!substr_count($row['User']['firstname'] , 'AUDIOTEL')){
				$nb_appels = $this->UserCreditHistory->find('count', array(
					'conditions' => array('user_id' => $row['UserCreditHistory']['user_id']),
					'recursive' => -1
				));
				$nb_appels_today = $this->UserCreditHistory->find('count', array(
					'conditions' => array('user_id' => $row['UserCreditHistory']['user_id'],  'DATE(date_start) >=' => $timing[0].' 00:00:00',  'DATE(date_start) <=' => $timing[0].' 23:59:59'),
					'recursive' => -1
				));

				$date_1_appel = $this->UserCreditHistory->find('first', array(
						'conditions' => array('user_id' => $row['UserCreditHistory']['user_id']),
						'recursive' => -1,
						'order' => 'date_start asc'
					));
			}else{

				if($caller){
					$nb_appels = $this->UserCreditHistory->find('count', array(
						'fields' => 'DISTINCT CallInfos.sessionid',
						'conditions' => array('user_id' => $row['UserCreditHistory']['user_id']),
						'recursive' => -1,
						'joins' => array(
							array('table' => 'call_infos',
								'alias' => 'CallInfos',
								'type' => 'INNER',
								'conditions' => array(
									'CallInfos.sessionid = UserCreditHistory.sessionid',
									'CallInfos.callerid = '.$caller
								)
							)
						)
					));

					$nb_appels += $this->UserCreditHistory->find('count', array(
						'fields' => 'DISTINCT CallInfos.sessionid',
						'conditions' => array('user_id' => 286),
						'recursive' => -1,
						'joins' => array(
							array('table' => 'call_infos',
								'alias' => 'CallInfos',
								'type' => 'INNER',
								'conditions' => array(
									'CallInfos.sessionid = UserCreditHistory.sessionid',
									'CallInfos.callerid = '.$caller
								)
							)
						)
					));

					$nb_appels_today = $this->UserCreditHistory->find('count', array(
						'fields' => 'DISTINCT CallInfos.sessionid',
						'conditions' => array('user_id' => $row['UserCreditHistory']['user_id'],  'DATE(date_start) >=' => $timing[0].' 00:00:00',  'DATE(date_start) <=' => $timing[0].' 23:59:59'),
						'recursive' => -1,
						'joins' => array(
							array('table' => 'call_infos',
								'alias' => 'CallInfos',
								'type' => 'INNER',
								'conditions' => array(
									'UserCreditHistory.sessionid = CallInfos.sessionid',
									'CallInfos.callerid = '.$caller

								)
							)
						),
					));
					$date_1_appel = $this->UserCreditHistory->find('first', array(
						'conditions' => array('user_id' => $row['UserCreditHistory']['user_id']),
						'recursive' => -1,
						'order' => 'date_start asc',
						'joins' => array(
							array('table' => 'call_infos',
								'alias' => 'CallInfos',
								'type' => 'INNER',
								'conditions' => array(
									'CallInfos.sessionid = UserCreditHistory.sessionid',
									'CallInfos.callerid = '.$caller
								)
							)
						)
					));
					$date_1_appel_old = $this->UserCreditHistory->find('first', array(
						'conditions' => array('user_id' => 286),
						'recursive' => -1,
						'order' => 'date_start asc',
						'joins' => array(
							array('table' => 'call_infos',
								'alias' => 'CallInfos',
								'type' => 'INNER',
								'conditions' => array(
									'CallInfos.sessionid = UserCreditHistory.sessionid',
									'CallInfos.callerid = '.$caller
								)
							)
						)
					));
				}else{
					$nb_appels = '';
					$nb_appels_today = '';
					$date_1_appel = '';
				}
			}

			if(substr_count($row['User']['firstname'] , 'AUDIOTEL')){
				$row['User']['firstname'] =  'AT'.substr($caller, -6);
				$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4)*15);
				if($date_1_appel_old['UserCreditHistory']['date_start'])
				$row['User']['date_add'] = $date_1_appel_old['UserCreditHistory']['date_start'];
				else
				$row['User']['date_add'] = $date_1_appel['UserCreditHistory']['date_start'];
			}

			$premier_appel = '';
			//var_dump($timing[0].' '.$timing[1] .' == '. $date_1_appel);exit;
			if($date_1_appel_old['UserCreditHistory']['date_start']){
				if($timing[0].' '.$timing[1] == $date_1_appel_old['UserCreditHistory']['date_start']) $premier_appel = 'X';
			}else{
				if($timing[0].' '.$timing[1] == $date_1_appel['UserCreditHistory']['date_start']) $premier_appel = 'X';
			}

			if(!$called){
				/*switch ($code_iso) {
					case 'be':
						$called = 'Belgique';
						break;
					case 'ch':
						$called = 'Suisse';
						break;
					case 'fr':
						$called = 'France';
						break;
					case 'lu':
						$called = 'Luxembourg';
						break;
					case 'ca':
						$called = 'Canada';
						break;
				}	*/
				$called = $code_iso. ' prepaye';
			}

            $line = array(

              //  'agent_number'      => $row['Agent']['agent_number'],
              //  'agent_pseudo'      => $row['Agent']['pseudo'],
              //  'agent_firstname'   => $row['Agent']['agent_firstname'],
              //  'agent_lastname'    => $row['Agent']['agent_lastname'],
               // 'agent_email'       => $row['Agent']['email'],
               // 'user_code'         => $row['User']['personal_code'],
                'user_firstname'    => $row['User']['firstname'],
                'user_lastname'     => $row['User']['lastname'],
				'user_source'     => $row['User']['source'],
				'user_domain'       => $code_iso,
				'user_date_add'     => Tools::dateUser('Europe/Paris', $row['User']['date_add']),
				'1er appel'		=> $premier_appel,
				//'X appel/inscription'=> $nb_appels,
				//'X appel/journée'   => $nb_appels_today,
               // 'user_credit_now'       => $row['User']['credit'],
                'media'             => $row['UserCreditHistory']['media'],
              //  'credits'           => ($row['User']['personal_code']!=999999)?$row['UserCreditHistory']['credits']:'',
               // 'seconds'           => $row['UserCreditHistory']['seconds'],
				//'minutes'           => $heures . ' h '.$minutes. ' min '. $secondes .' sec',
               // 'called_number'     => $row['UserCreditHistory']['called_number'],
				'called'            => $called,
               // 'phone_agent'       => $row['Agent']['phone_number'],
				//'caller'            => $caller,
				//'caller_line'       => $caller_line,
				//'sessionid'         => $row['UserCreditHistory']['sessionid'],
              //  'phone_client'      => ($row['UserCreditHistory']['phone_number']=='0000000000')?'MASQUE':$row['UserCreditHistory']['phone_number'],
			    'time_start'        => $timing[1],
                'date_start'        => Tools::dateUser('Europe/Paris', $row['UserCreditHistory']['date_start']),
               // 'date_end'          => Tools::dateUser('Europe/Paris', $row['UserCreditHistory']['date_end']),
				//'remuneration'      => $price
            );

            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
           fputcsv($fp, array_values($line), ';', '"');
        }
        fclose($fp);
		$mysqli->close();
        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
    }

    //Export des données d'achat de crédit
    public function admin_export_credit(){
		set_time_limit ( 0 );
		ini_set("memory_limit",-1);
        //Charge model
        $this->loadModel('UserCredit');
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

        $conditions = array();
        //Filtre par date ??
        if($this->Session->check('Date')){
            $conditions = array_merge($conditions, array(
                'UserCredit.date_upd >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                'UserCredit.date_upd <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
            ));
        }

        //Les données de toute le monde ou celles d'un user
        if(isset($this->params->query['user'])){
            $idUser = $this->params->query['user'];
            $conditions = array_merge($conditions, array('UserCredit.users_id' => $idUser));
        }

        //Les données à sortir
        $allCreditDatas = $this->UserCredit->find('all', array(
            'fields'        => array('UserCredit.*', 'User.*','Product.*','Orders.*'),
            'conditions'    => $conditions,
			'joins' => array(
						array('table' => 'orders',
							'alias' => 'Orders',
							'type' => 'left',
							'conditions' => array(
								'Orders.id = UserCredit.order_id'
							)
						)
					),
            'order' => 'UserCredit.date_upd DESC'
        ));


        //Si pas de données
        if(empty($allCreditDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'accounts', 'action' => 'credit', 'admin' => true), false);
            else
                $this->redirect($source);
        }


        //Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_export';

        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

        foreach($allCreditDatas as $indice => $row){

			$curr = str_replace('€','Euro',$row['Orders']['currency']);
			$curr = str_replace('$','Dollar',$curr);

			$origine = '';
			switch ($row['User']['domain_id']) {
				case 11:
					$origine = 'Belgique';
					break;
				case 13:
					$origine = 'Suisse';
					break;
				case 19:
					$origine = 'France';
					break;
				case 22:
					$origine = 'Luxembourg';
					break;
				case 29:
					$origine = 'Canada';
					break;
			}

			$statut = '';
			$payment_type = '';

			if($row['UserCredit']['payment_mode']== 'hipay'){
									if($row['Orders']['valid']== '0') $statut =  'refusé';
									if($row['Orders']['valid']== '1') $statut =  'accepté';
									if($row['Orders']['valid']== '2') $statut =  'impayé';
									if($row['Orders']['valid']== '3') $statut =  'remboursé';
									if($row['Orders']['valid']== '4') $statut =  '';
									$payment_type = 'Carte';
								}

								if($row['UserCredit']['payment_mode']== 'paypal'){
									if($row['Orders']['valid']== '0') $statut =  'refusé';
									if($row['Orders']['valid']== '1') $statut =  'accepté';
									if($row['Orders']['valid']== '2') $statut =  'en attente';
									if($row['Orders']['valid']== '3') $statut =  'impayé';
									if($row['Orders']['valid']== '4') $statut =  'remboursé';
									$payment_type = 'Carte';
								}
								if($row['UserCredit']['payment_mode']== 'bankwire'){
									if($row['Orders']['valid']== '0') $statut =  'refusé';
									if($row['Orders']['valid']== '1') $statut =  'accepté';
									if($row['Orders']['valid']== '2') $statut =  'en attente';
									if($row['Orders']['valid']== '3') $statut =  '';
									if($row['Orders']['valid']== '4') $statut =  '';
									$payment_type = 'Banque';
								}
			if($row['UserCredit']['payment_mode']== 'stripe'){
									if($row['Orders']['valid']== '0') $statut =  'refusé';
									if($row['Orders']['valid']== '1') $statut =  'accepté';
									if($row['Orders']['valid']== '2') $statut =  'impayé';
									if($row['Orders']['valid']== '3') $statut =  'remboursé';
									if($row['Orders']['valid']== '4') $statut =  '';
								$payment_type = 'Carte';




								}
			$payment_ref = '';
			if($row['UserCredit']['payment_mode']== 'stripe'){
				$this->loadModel('OrderStripetransaction');
				$stripe_data = $this->OrderStripetransaction->find('first', array(
            'conditions'    => array('OrderStripetransaction.order_id' => $row['UserCredit']['order_id']),
        						));

				if($stripe_data && substr_count($stripe_data['OrderStripetransaction']['payment_method'],'apple_pay') )$payment_type = 'Apple Pay';
				if($stripe_data && substr_count($stripe_data['OrderStripetransaction']['payment_method'],'google_pay') )$payment_type = 'Google Pay';
				if($stripe_data && substr_count($stripe_data['OrderStripetransaction']['payment_method'],'microsoft_pay') )$payment_type = 'Microsoft Pay';
				if($stripe_data && substr_count($stripe_data['OrderStripetransaction']['payment_method'],'samsung_pay') )$payment_type = 'Samsung Pay';
				$payment_ref = $stripe_data['OrderStripetransaction']['id'];
			}
			if($row['UserCredit']['payment_mode']== 'hipay'){
				$this->loadModel('OrderHipaytransaction');
				$stripe_data = $this->OrderHipaytransaction->find('first', array(
            'conditions'    => array('OrderHipaytransaction.order_id' => $row['UserCredit']['order_id']),
        						));
				$payment_ref = $stripe_data['OrderHipaytransaction']['transaction'];
			}

			if($row['UserCredit']['payment_mode']== 'paypal'){
				$this->loadModel('OrderPaypaltransaction');
				$stripe_data = $this->OrderPaypaltransaction->find('first', array(
            'conditions'    => array('OrderPaypaltransaction.order_id' => $row['UserCredit']['order_id']),
        						));
				$payment_ref = $stripe_data['OrderPaypaltransaction']['payment_transactionid'];
			}

            $line = array(
                'date_achat'            => $row['UserCredit']['date_upd'],
                'credit_achat'          => $row['UserCredit']['credits'],
				'product_name'          => $row['UserCredit']['product_name'],
				'payment_mode'          => $row['UserCredit']['payment_mode'],
				'payment_type'			=> $payment_type,
				'payment_ref'			=> $payment_ref,
				'total'          		=> number_format ($row['Orders']['total'],2),
				'currency'          	=> $curr,
				'pays_inscription_client'        => $origine,
				'voucher_name'          => $row['Orders']['voucher_name'],
				'voucher_code'          => $row['Orders']['voucher_code'],
                'user_id'               => $row['User']['id'],
                'user_firstname'        => $row['User']['firstname'],
                'user_lastname'         => $row['User']['lastname'],
                'user_email'            => $row['User']['email'],
                'user_sexe'             => $row['User']['sexe'],
                'user_personal_code'    => $row['User']['personal_code'],
				'cookie_id'    			=> $row['Orders']['cookie_id'],
				'IP'    				=> $row['Orders']['IP'],
                'user_credit'           => $row['User']['credit'],
                'product_id'            => $row['Product']['id'],
                'product_country_id'    => $row['Product']['country_id'],
                'product_credits'       => $row['Product']['credits'],
                'product_tarif'         => $row['Product']['tarif'],
                'product_active'        => $row['Product']['active'],
				'statut'        		=> $statut,
            );

            if($indice == 0)
                fputcsv($fp, array_keys($line), ';', '"');

            fputcsv($fp, array_values($line), ';', '"');
        }
        fclose($fp);

        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
    }

    public function admin_relance_mail_confirm($id){
        if(isset($this->request->query['view']))
            $this->relanceMailConfirm($id,'accounts','view');
        else
            $this->relanceMailConfirm($id,'accounts');
    }

    public function admin_confirm_mail($id){
        if(isset($this->request->query['view']))
            $this->confirmMail($id,'accounts','view');
        else
            $this->confirmMail($id,'accounts');
    }

    public function admin_deactivate_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');

        //Si c'est bien un agent ou un client
        if($role === 'client')
            $this->changeCompte($id, 'accounts', array('success' => 'Le compte a été désactivé.','warning' => 'La désactivation du compte a échoué.'), false);
        else{
            $this->Session->setFlash(__('Cet utilisateur n\'existe pas ou n\'est pas un client.'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
        }
    }

    public function admin_activate_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');

        //Si c'est bien un client
        if($role === 'client')
            $this->changeCompte($id, 'accounts', array(
                'success' => 'Le compte a été activé. Email envoyé',
                'warning' => 'L\'activation du compte a échoué.',
                'email'   => 'Votre compte a été activé.'
            ));
        else
            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
    }

    public function admin_delete_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');

        //Si c'est bien un client
        if($role === 'client'){
            //On supprime le client
            if($this->User->delete_user($id))
                $this->Session->setFlash(__('Le compte a été supprimé.'));
            else
                $this->Session->setFlash(__('Erreur lors de la suppression.'));

            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
        }else
            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
    }

	public function admin_restore_user($id){
        $this->User->id = $id;
        //On récupère le role de l'user
        $role = $this->User->field('role');

        //Si c'est bien un client
        if($role === 'client'){
            //On supprime le client
            if($this->User->restore_user($id))
                $this->Session->setFlash(__('Le compte a été re-activé.'));
            else
                $this->Session->setFlash(__('Erreur lors de la reactivation.'));

            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
        }else
            $this->redirect(array('controller' => 'accounts', 'action' => 'index', 'admin' => true), false);
    }

    public function admin_note($id){
        //On sauve la note
        $this->_adminNote($id,'Account');
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
					$check_ip['UserIp']['note'] = $this->request->data['Account']['note'];
					$this->UserIp->save($check_ip);
			 }
             $this->Session->setFlash(__('Note sauvegardé.'),'flash_success');
         }
        $this->redirect(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $id),false);
    }

	public function cart_buy(){
		if($this->request->is('post')){

			$data = $this->request->data;
			$id_cart = $data['id_cart'];

			$this->loadModel('CartLoose');

			$cartLoose = $this->CartLoose->find('first', array(
                                'conditions' => array(
                                    'CartLoose.id_cart' => $id_cart,
                                ),
                                array('recursive' => -1)
        ));

		if($cartLoose){
			$this->CartLoose->id = $cartLoose['CartLoose']['id'];
			$this->CartLoose->save(array(
				'status'     =>      0,
			));
		}


		}
		exit;
	}

	public function redir_cart_buy()
	{
		if($this->request->is('ajax')){
			$this->Session->write('product_preselect', 0);
			//$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
			if($this->request->data['id_product']){
				$url = '/accounts/buycredits';
				$this->Session->write('product_preselect', $this->request->data['id_product']);
				$this->jsonRender(array('redir_url' => $url));
			}else{
				$this->jsonRender(array('redir_url' => ''));
			}
		}
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
		$url = Router::url(array('controller' => 'accounts', 'action' => 'profilremove-'.$hash),true);

		$is_send = $this->sendCmsTemplatePublic(361, (int)$client['User']['lang_id'], $client['User']['email'], array(
									'CLIENT' =>$client['User']['firstname'],
									'URL_REMOVE' =>$url,
								));

        $this->Session->setFlash('Un email de confirmation vous a été envoyé.', 'flash_success');

        $this->redirect(array(
            'controller' => 'accounts',
            'action' => 'profil',
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
				'conditions'    => array('User.email' => $hash, 'User.role' => 'client'),
				'recursive'     => -1
			));
			$this->User->id = $user['User']['id'];
			$this->User->saveField('email', 'delete_'.$hash);
			$this->User->saveField('deleted', '1');
			$this->User->saveField('active', '0');
			$this->User->saveField('date_upd', date('Y-m-d H:i:s'));

			$message = __('Votre compte est desormais supprimé.');
			$template = 'flash_success';

			$this->Session->setFlash($message, $template);
			$this->Cookie->delete('user_remember');
			$this->Auth->logout();
			//$this->destroySessionAndCookie();

		}

        $this->redirect(array(
            'controller' => 'home',
            'action' => 'index',
        ));
    }

	public function admin_account_deleted(){

		$this->loadModel('User');

		$this->Paginator->settings = array(
                'order' => array('User.date_upd' => 'desc'),
				'conditions' => array('User.email like ' => '%delete_%','role'=>'client'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $accounts = $this->Paginator->paginate($this->User);

            $this->set(compact('accounts'));
	}

	public function admin_account_subscribe()
		{
			$this->loadModel('UserSubscribe');

			 $this->Paginator->settings = array(
				'fields' => array('UserSubscribe.*','User.date_add','User.firstname','User.id'),
				'recursive' => 1,
				'order' => 'UserSubscribe.date_add DESC',
				'paramType' => 'querystring',
				 'joins' => array(

					 			array('table' => 'users',
									  'alias' => 'User',
									  'type' => 'left',
									  'conditions' => array('User.email = UserSubscribe.email')
								),
				),
				 'group' => 'UserSubscribe.id',
					'limit' => 50
				);




			$subs = $this->Paginator->paginate($this->UserSubscribe);

			$this->set(compact('subs'));
		}
	public function admin_account_loyalty()
    {
        $this->loadModel('User');
        $this->loadModel('LoyaltyUserBuy');
		
		$conditions = array('LoyaltyUserBuy.pourcent_current >'=>0);
		if($this->request->is('post') && isset($this->request->data['Account']['email'])){
		$conditions = array_merge($conditions, array(
						array('User.email LIKE' => '%'.$this->request->data['Account']['email'].'%'),
						));

		 }

        $model = new LoyaltyUserBuy();
        $ds = $model->getDataSource();
        $sub = $ds->buildStatement(array(
            'fields' => array('L2.user_id', 'MAX(L2.date_add) AS maxDate'),
            'table' => 'loyalty_user_buys',
            'alias' => 'L2',
            'group' => 'L2.user_id'
        ), $model);


        $this->Paginator->settings = array(
            'fields' => array('User.id', 'User.firstname', 'User.email', 'LoyaltyUserBuy.pourcent_current'),
            'conditions' => $conditions,
            'recursive' => -1,
            'paramType' => 'querystring',
            'joins' => array(
                array(
                    'table' => 'loyalty_user_buys',
                    'alias' => 'LoyaltyUserBuy',
                    'type' => 'inner',
                    'conditions' => array('User.id = LoyaltyUserBuy.user_id')
                ),
                array(
                    'table' => "($sub)",
                    'alias' => 'L',
                    'type' => 'inner',
                    'conditions' => array(
                        'LoyaltyUserBuy.user_id = L.user_id',
                        'LoyaltyUserBuy.date_add = L.maxDate'
                    )
                ),
            ),
            'limit' => 50
        );

        $accounts = $this->Paginator->paginate($this->User);

        $this->set(compact('accounts'));
    }
	public function admin_exportsubscribecsv()
    {
		$this->autoRender = false;
        $filename = Configure::read('Site.pathExport').'/all_subscribes.csv';
        $this->_fp = fopen($filename, 'w+');
        fwrite($this->_fp, "\xEF\xBB\xBF");

        $fields = array('id','email','date souscription','inscrit agents','date inscription agents');

		fputcsv($this->_fp, $fields, ';' ,'"');

		$this->loadModel('UserSubscribe');
		$this->loadModel('User');
		$subscribes = $this->UserSubscribe->find('all', array(
                'fields' => array('UserSubscribe.*'),
				'order' => 'UserSubscribe.date_add ASC',
                'paramType' => 'querystring',
				 'joins' => array(

            ),
                'recursive' => -1
            ));
		foreach($subscribes as $subscribe){
			$row = array();
			$row['id'] = $subscribe['UserSubscribe']['id'];
			$row['email'] = $subscribe['UserSubscribe']['email'];
			$row['date souscription'] = $subscribe['UserSubscribe']['date_add'];

			$user = $this->User->find('first', array(
                'fields' => array('User.date_add'),
				'conditions' => array('User.email'=>$subscribe['UserSubscribe']['email']),
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

	//Liste les RDV expert
    public function appointments(){

        $user = $this->Session->read('Auth.User');
	
	/*
        if ($user['role'] != $this->myRole)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	*/
	
		//Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        $this->loadModel('CustomerAppointment');
		$this->loadModel('User');
		$this->loadModel('Domain');
		$this->loadModel('Lang');
		$this->loadModel('Message');

		if($this->request->is('post')){
            $requestData = $this->request->data;
			$appoint_id = $requestData["accounts"]["appoint_id"];
			$appoint_resp = $requestData["accounts"]["content"];
			$appoint_choice = $requestData["accounts"]["ChoiceRDV"];



			if($appoint_id && $appoint_choice){
				$user_id = $this->CustomerAppointment->field('user_id', array('CustomerAppointment.id' => $appoint_id));
				$user_lang_id = $this->User->field('lang_id', array('User.id' => $user_id));
				$user_email = $this->User->field('email', array('User.id' => $user_id));
				$user_firstname = $this->User->field('firstname', array('User.id' => $user_id));
				$user_domain_id = $this->User->field('domain_id', array('User.id' => $user_id));

				$agent_id = $this->CustomerAppointment->field('agent_id', array('CustomerAppointment.id' => $appoint_id));
				$agent_pseudo = $this->User->field('pseudo', array('User.id' => $agent_id));
				$agent_email = $this->User->field('email', array('User.id' => $agent_id));
				$agent_number = $this->User->field('agent_number', array('User.id' => $agent_id));
				$country_agent = $this->User->field('country_id', array('id' => $agent_id));

				$conditions = array(
						'Domain.id' => $user_domain_id
					);
				$domain = $this->Domain->find('first',array('conditions' => $conditions));
				if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';
				$conditions = array(
						'Lang.id_lang' => $user_lang_id
				);
				$lang = $this->Lang->find('first',array('conditions' => $conditions));

				$url_expert = 'https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/agents/'.strtolower($agent_pseudo).'-'.$agent_number;

				$dateAppoint = $this->CustomerAppointment->field('A', array('CustomerAppointment.id' => $appoint_id)).'-'.$this->CustomerAppointment->field('M', array('CustomerAppointment.id' => $appoint_id)).'-'.
                        $this->CustomerAppointment->field('J', array('CustomerAppointment.id' => $appoint_id)).' '.
                        str_pad($this->CustomerAppointment->field('H', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT).':'.
                        str_pad($this->CustomerAppointment->field('Min', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT).':00';


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

						//$utc_dec = Configure::read('Site.utc_dec');
						$dx = new DateTime($dateAppoint);
						$dx->modify($utc_dec.' hour');
						$dateAppoint = $dx->format('Y-m-d H:i:s');
						$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
					}else{
						$dx = new DateTime($dateAppoint);
						$dateAppoint = $dx->format('Y-m-d H:i:s');
						$rdv = CakeTime::format($dateAppoint,'%d %B &agrave; %Hh%M');
					}
				$this->CustomerAppointment->id = $appoint_id;
				switch ($appoint_choice) {

					case 3:
						$this->sendCmsTemplateByMail(358, $user_lang_id, $agent_email, array(
							'PARAM_CLIENT' => $user_firstname,
							'PARAM_PSEUDO' => $agent_pseudo,
							'PARAM_RENDEZVOUS' => $rdv,
							'PAGE_EXPERT' => $url_expert,
						));
						//$this->CustomerAppointment->delete($appoint_id, false);
						$this->CustomerAppointment->saveField('valid', -2);
						 $this->Session->setFlash(__('Votre réponse a été transmise a l\'expert.'),'flash_success');
						break;
				}
			}

			$this->redirect(array('controller' => 'accounts', 'action' => 'appointments', 'admin' => false),false);
		}



        //Date d'aujourd'hui explosé
        $dateNow = CakeTime::format('now', '%d-%m-%Y');
        $dateNow = Tools::explodeDate($dateNow);
        $dateEnd = CakeTime::format(strtotime('+'.(Configure::read('Site.limitPlanning')-1).' days'), '%d-%m-%Y');
        $dateEnd = Tools::explodeDate($dateEnd);

        $this->Paginator->settings = array(
            'fields' => array('CustomerAppointment.*', 'Agent.pseudo', 'Agent.id', 'Agent.agent_number'),
            'conditions' => $this->CustomerAppointment->getConditionsClient($user['id'], $dateNow, $dateEnd),
            'joins' => array(
                array('table' => 'users',
                      'alias' => 'Agent',
                      'type' => 'left',
                      'conditions' => array('Agent.id = CustomerAppointment.agent_id')
                )
            ),
            //'order' => array('CustomerAppointment.A' => 'ASC', 'CustomerAppointment.M' => 'ASC', 'CustomerAppointment.J' => 'ASC', 'CustomerAppointment.H' => 'ASC', 'CustomerAppointment.Min' => 'ASC'),
            'order' => "date_format(CONCAT(A,'-',M,'-',J,' ',H,':',Min,':00'),'%Y-%m-%d %H:%i:%s') ASC",
            'limit' => 15,
            'recursive' => -1
        );

        $appointments = $this->Paginator->paginate($this->CustomerAppointment);
        $appointments = $this->CustomerAppointment->restructureAppointmentClientV2($appointments);


		$country_client = $this->User->field('country_id', array('id' => $user['id']));
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
	public function mails_deprecated()
	{
		 //Les datas
        $requestData = $this->request->data;
		$this->loadModel('User');
        //Les champs du formulaire
        $requestData['Account'] = Tools::checkFormField($requestData['Account'], array('mail_id','content'), array('mail_id','content'));
        if($requestData['Account'] === false){
            $this->Session->setFlash(__('Erreur avec le formulaire de réponse'),'flash_error');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }
		$this->loadModel('Message');
		 $infoMessage = $this->Message->find('first',array(
            'fields' => array('Message.*', 'Agent.pseudo', 'Agent.email', 'Agent.consults_nb','Agent.id',
                'Agent.lang_id','User.lang_id','Agent.consult_email', 'Agent.creditMail', 'Agent.agent_number', 'User.role', 'User.id'),
            'conditions' => array('Message.id' => $requestData['Account']['mail_id'], 'Message.deleted' => 0, 'Message.parent_id' => null, 'Message.from_id' => $this->Auth->user('id')),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type' => 'left',
                    'conditions' => array(
                        'Agent.id = Message.to_id',
                        'Agent.role = "agent"',
                        'Agent.active = "1"',
                        'Agent.deleted = "0"'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array(
                        'User.id = Message.from_id',
                        'User.deleted = 0'
                    )
                )
            ),
            'recursive' => -1
        ));

		if(empty($infoMessage)){
           $this->Session->setFlash(__('Erreur lors de la création du nouvelle email, merci de recommencer.'),'flash_warning');
           $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

		 //Check sur l'agent-------------------------------------------------------------------------
        //Si pas d'agent
        if(empty($infoMessage['Agent']['agent_number'])){
            $this->Session->setFlash(__('L\'expert demandé n\'existe pas ou il n\'est plus actif ou vous ne pouvez pas répondre à ce message.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

		//Si l'agent ne prend pas de consultation par email
        if($infoMessage['Agent']['consult_email'] == 0){
            $this->Session->setFlash(__('L\'expert n\'accepte pas/plus de consultation par mail.'),'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
        }

		 $creditMail = 0;
         //Check sur le crédit du client--------------------------------------------------------------
         $creditUser = $this->User->field('credit', array('id' => $this->Auth->user('id')));
         $creditMail = (empty($infoMessage['Agent']['creditMail']) ?Configure::read('Site.creditPourUnMail'):$infoMessage['Agent']['creditMail']);
         //Pas assez de crédit
         if($creditUser < $creditMail){
                $this->Session->setFlash(__('Vous n\'avez pas assez de crédit. Il vous faut').' '.$creditMail.' '.__('crédits.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
         }

		$etat = 0;
		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
					'conditions' => array(
					)
		));
		foreach($filtres as $filtre){
			if(substr_count(strtolower($requestData['Account']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
				$etat = 2;
		}

		$this->Message->create();
		 if($this->Message->save(array(
            'parent_id' => NULL,
            'from_id' => $this->Auth->user('id'),
            'to_id' => $infoMessage['Message']['to_id'],
            'content' => $this->remove_emoji($requestData['Account']['content']),
			'attachment' => $infoMessage['Message']['attachment'],
			'attachment2' => $infoMessage['Message']['attachment2'],
            'credit' => $creditMail,
            'private'   => $infoMessage['Message']['private'],
            'etat' => $etat,
            'deleted' => 0,
			'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
        ))){

			 if($etat == 2){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						//Les datas pour l'email
						$datasEmail = array(
							'content' => 'Un Mail requiert check terme interdit.' ,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com'
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Mail client terme interdit','default',$datasEmail);
					}

            //L'id du message qui vient d'être crée
            $newId = $this->Message->id;
            //Si ce n'est pas un message privée
            if($infoMessage['Message']['private'] == 0){
                //Mise à jour du crédit
                $newCredit = $this->updateCredit($this->Auth->user('id'), (isset($creditMail) ?$creditMail:Configure::read('Site.creditPourUnMail')));
                if($newCredit !== false)
                    CakeSession::write(array('Auth.User.credit' => $newCredit));
                else{
                    //Problème au niveau du crédit, on supprime le message
                    $this->Message->delete($newId, false);
                    $this->Session->setFlash(__('Erreur lors de la mise à jour de votre crédit. Le mail n\'a pas été envoyé.'),'flash_error');
                    $this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
                }

				$this->Message->id = $infoMessage['Message']['id'];
				$this->Message->saveField('archive', 1);


                //Save dans l'historique
                $this->loadModel('UserCreditLastHistory');
                $this->loadModel('UserCreditHistory');
                $saveData = array(
                    'users_id'              => $this->Auth->user('id'),
                    'agent_id'              => $infoMessage['Message']['to_id'],
                    'agent_pseudo'          => $infoMessage['Agent']['pseudo'],
                    'media'                 => 'email',
                    'credits'               => $creditMail,
                    'user_credits_before'   => $creditUser,
                    'user_credits_after'    => $newCredit,
                    'date_start'            => date('Y-m-d H:i:s'),
                    'date_end'              => date('Y-m-d H:i:s'),
					'sessionid'             => $newId
                );
                $this->UserCreditLastHistory->create();
                $this->UserCreditLastHistory->save($saveData);
                //Save dans l'historique (archive)
                $saveData['user_id'] = $saveData['users_id'];
                unset($saveData['users_id']);

				$saveData['is_new'] = 0;
				$saveData['type_pay'] = 'pre';
				$saveData['domain_id'] = $this->Session->read('Config.id_domain');

                $this->UserCreditHistory->create();
                $this->UserCreditHistory->save($saveData);
				$this->calcCAComm($this->UserCreditHistory->id);

				//Sponsorship
				$lastHistoryID = $this->UserCreditLastHistory->id;
				App::import('Model', 'Sponsorship');
				$Sponsorship = new Sponsorship();
				$Sponsorship->Benefit($lastHistoryID);

				//cumul comm
				$consults_nb = $infoMessage['Agent']['consults_nb'] + 1;
				$this->User->id = $infoMessage['Agent']['id'];
				$this->User->saveField('consults_nb', $consults_nb);

            }


            //est-ce un msg pour un expert ??
            if(isset($infoMessage['User']) && $infoMessage['User']['role'] === 'agent' && $etat == 0)
                $this->sendCmsTemplateByMail(177, $infoMessage['User']['lang_id'], $infoMessage['Agent']['email'], array(
                    'PSEUDO_NAME_DEST' => $infoMessage['Agent']['pseudo']
                ));

				$this->Session->setFlash(__('Votre message a été envoyé.'),'flash_success');

        }else
            $this->Session->setFlash(__('Erreur durant l\'envoi du mail. Vous n\'avez pas été décrédité.'),'flash_error');

		$this->redirect(array('controller' => 'accounts', 'action' => 'mails'));
	}
   public function new_visio(){
		$id_lang = $this->Session->read('Config.id_lang');
		 $user = $this->Session->read('Auth.User');

       /* App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());
        $page_content = $fbH->getPageBlocTexte(210, false, $tmp, $page);
        $this->set('page_content', $page_content);*/

        if(isset($this->params['id']))
            $idAgent = $this->params['id'];
        elseif(isset($this->params['named']['id']))
            $idAgent = $this->params['named']['id'];

        //Si pas d'id redirection home
        if(empty($idAgent))
            $this->redirect(array('controller' => 'home', 'action' => 'index'),true,301);

        //Si utilisateur différent de client
        if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
            $this->Session->setFlash(__('Veuillez-vous connecter avec un compte client.'), 'flash_warning');
            $this->redirect(array('controller' => 'users', 'action' => 'login'),true,301);
        }

        //Si l'agent existe et accepte les consultations par visio
        $agent = $this->User->find('first',array(
                'fields' => array('agent_status', 'pseudo','agent_number', 'creditMail','id','mail_infos_v'),
                'conditions' => array('User.id' => $idAgent, 'User.role' => 'agent', 'User.consult_email' => 1, 'User.active' => 1, 'User.deleted' => 0,
                    'OR' => array(
                        array('User.agent_status' => 'available'),
                        array(
                            'User.agent_status' => 'busy',
                            'User.consult_email' => 1
                        )
                    )
                    /*'User.agent_status' => 'available'*/
                ),
                'recursive' => -1
            ));


        //Si aucun agent trouvé
        if(empty($agent) && !$noaccept_butpost){
            $this->Session->setFlash(__('L\'expert est indisponible ou il n\'accepte pas les consultations par visio.'),'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

       $this->set(compact('user'));
    }
	
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
				'conditions'        => array('id' => $this->request->data['id_agent']),
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
										
										if (isset($User['consult_email']) && (int)$User['consult_email'] == 1){
											if($User['agent_status'] == 'busy'){
												$css_email = 'm-available';
											}else{
												$css_email = 'm-'.$User['agent_status'];
											}
										}else{
											$css_email = ' disabled';
										}
			
			
			$html .= '<span class="dis-call-rel">'.$this->request->data['id_agent'].'</span><div class="col-md-4 col-sm-4 col-xs-4">';
				$html .= '<div class="medium-icon text-center">';
					$html .= '<div class="tel '.$css_phone;
					
					$html .= '">';
					$csstooltip = 'tooltip';
					if($this->request->isMobile())$csstooltip = '';
					if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1):
						/*$lien = $fbH->Html->url(
												array(
													'controller' => 'home',
                                                    'action' => 'media_phone'
												)
											);*/
						$lien = 'agents par téléphone';
						$html .= '<div data-toggle="'.$csstooltip.'" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tel</p><span class="ae_phone_param" style="display:none">'.$User['id'].'</span></div>';

					else:
						$html .= '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon"><p>Tel</p></div>';
						$link_num_tel = '';
					endif;
					
					$html .= '</div>';
				$html .= '</div>';
				/*$html .= '<div class="phone-number text-center hidden-xs">';
			
				if (empty($user)): 
					if ($thirdNumberRule):
						$html .= '<h4>'.__('Par téléphone surtaxé').'</h4>';
						if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1):
							$lien = $fbH->Html->url(
														array(
															'controller' => 'home',
															'action' => 'media_phone'
														)
													);
							$linktel = '<div  data-toggle="'.$csstooltip.'" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span>'.$thirdNumberRule['numero'].'<span class="ae_phone_param" style="display:none">'.$User['id'].'</span></div>';	
			
						else:
							$linktel = '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon">'.$thirdNumberRule['numero'].'</div>';
							
						endif;
                           $html .= '<div class="p-num flag-'.$this->Session->read('Config.id_country').'">'.$linktel.' <span>'.$thirdNumberRule['pricemin'].''.$this->Session->read('Config.devise').' /min </span></div>';
						endif;
						if(isset($CountryLangPhone['surtaxed_phone_number']) && !empty($CountryLangPhone['surtaxed_phone_number'])) :
											$html .=  '<h4>'.__('Par téléphone surtaxé').'</h4>';
											if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1):
										$lien = $fbH->Html->url(
														array(
															'controller' => 'home',
															'action' => 'media_phone'
														)
													);
												$linktel = '<div  data-toggle="'.$csstooltip.'" data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span>'.$CountryLangPhone['surtaxed_phone_number'].'<span class="ae_phone_param" style="display:none">'.$User['id'].'</span></div>';	
			
												else:
												$linktel = '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon">'.$CountryLangPhone['surtaxed_phone_number'].'</div>';

												endif;
                                			$html .= '<div class="p-num flag-'.$this->Session->read('Config.id_country').'">'.$linktel.' <span>'.$CountryLangPhone['surtaxed_minute_cost'].''.$this->Session->read('Config.devise').' /min </span></div>';
										endif;
									endif;
									if(isset($CountryLangPhone['prepayed_phone_number']) && !empty($CountryLangPhone['prepayed_phone_number'])):
										$html .= '<h4>'.__('Paiement par carte bancaire').'</h4>';
										if (isset($User['consult_phone']) && (int)$User['consult_phone'] == 1):
											$lien = $fbH->Html->url(
														array(
															'controller' => 'home',
															'action' => 'media_phone'
														)
													);
											$linktel = '<div data-toggle="tooltip"  data-placement="top" title="Tel" class="nx_phoneboxinterne aicon"><span class="linklink">'.$lien.'</span>'.$CountryLangPhone['prepayed_phone_number'].'<span class="ae_phone_param" style="display:none">'.$User['id'].'</span></div>';	
			
											
												else:
											$linktel = '<div data-toggle="tooltip" data-placement="top" title="Tel" class="aicon">'.$CountryLangPhone['prepayed_phone_number'].'</div>';

												endif;
										
                                		$html .= '<div class="p-num flag-'.$this->Session->read('Config.id_country').'">'.$linktel.' <span>'.$CountryLangPhone['prepayed_minute_cost'].''.$this->Session->read('Config.devise').' /min </span></div>';
									endif;
                                $html .= '<p class="text-center acheter mb0">';
								
									if(!empty($user)){
										$html .=  '<a href="'.$fbH->getProductsLink().'" class="underline pink" title="Acheter des minutes">Acheter des minutes</a>';	
									
									}else{
										$html .= '<span class="underline pink a" data-toggle="modal" data-target="#connection">Acheter des minutes</span>';	
									}
                                $html .= '</p>';
                            $html .= '</div>';*/

						$html .= '</div>';

						

						$html .='<div class="col-md-4 col-sm-4 col-xs-4">';
                            $html .='<div class="medium-icon text-center">';
                                $html .='<div class="chat '.$css_tchat;
								 
								$html .='">';
                                 if (isset($User['consult_chat']) && (int)$User['consult_chat'] == 1 && $fbH->agentActif($User['date_last_activity'])):
			
								/* $lien = $fbH->Html->url(
												array(
													'controller' => 'chats',
                                                    'action' => 'create_session',
                                                    'id' => $User['id']
												)
											);*/
									$lien = 'agents par tchat - '.$User['id'];
											$html .=  '<div data-toggle="tooltip" data-placement="top" title="Chat" class="nx_chatboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Tchat</p></div>'; 
                                                    
                                
                                                        else:
                                                          $html .=  '<div data-toggle="tooltip" data-placement="top" title="Tchat" class="aicon"><p>Tchat</p></div>';
                                                        endif;
                                $html .='</div>';
                            $html .='</div><!--medium-icon-->';
    
                           /* $html .='<div class="chat-status text-center hidden-xs">';
                                $html .='<h4>Consultez '.$User['pseudo'].' par tchat</h4>';
                                $html .='<p>'.$fbH->getPageBlocTextebyLang(317,$this->Session->read('Config.id_lang')).'</p>';
                                $html .='<p class="text-center acheter mb0">';
								if(!empty($user)){
									$html .=  '<a href="'.$fbH->getProductsLink().'" class="underline pink" title="Acheter des minutes">Acheter des minutes</a>';	
									
								}else{
										$html .= '<span class="underline pink a" data-toggle="modal" data-target="#connection">Acheter des minutes</span>';	
									}
                                $html .='</p>';
                            $html .='</div>';*/

						$html .='</div>';
						$html .= '<div class="col-md-4 col-sm-4 col-xs-4">';
							$html .= '<div class="medium-icon text-center">';
                                $html .= '<div class="mail '.$css_email;
							 $html .='">';
                                     if (isset($User['consult_email']) && (int)$User['consult_email'] == 1):
                                                       /* $lien = $fbH->Html->url(
												array(
													'controller' => 'accounts',
													'action' => 'new_mail',
													'id' => $User['id']
												)
											);*/
											$lien = 'agents par mail - '.$User['id'];
											$html .= '<div data-toggle="'.$csstooltip.'" data-placement="top" title="Email" class="nx_emailboxinterne aicon"><span class="linklink">'.$lien.'</span><p>Email</p></div>';
                                                        
                                                            else:
                                                                 $html .= '<div data-toggle="tooltip" data-placement="top" title="Email" class="aicon"><p>Email</p></div>';
                                                            endif;
                                
                                $html .='</div>';
                            $html .='</div><!--medium-icon-->';
                           /* $html .='<div class="mail-address text-center hidden-xs">';
                                $html .='<h4>Consultez '.$User['pseudo'].' par e-mail</h4>';
                                $html .='<p>'.$fbH->getPageBlocTextebyLang(316,$this->Session->read('Config.id_lang')).'</p>';
                                $html .='<p class="text-center acheter mb0">';
								if(!empty($user)){
									$html .=  '<a href="'.$fbH->getProductsLink().'" class="underline pink" title="Acheter des minutes">Acheter des minutes</a>';	
									
								}else{
										$html .=  '<span class="underline pink a" data-toggle="modal" data-target="#connection">Acheter des crédits</span>';
									}
								
                                $html .='</p>';
                            $html .='</div>';*/
								
						$html .='</div>';
								/*if(isset($User['consult_chat']) && isset($User['consult_email']) && isset($User['consult_phone']) && ( (int)$User['consult_chat'] == 0 || ((int)$User['consult_chat'] == 1 && !$fbH->agentActif($User['date_last_activity'])) )&& (int)$User['consult_phone'] == 0 && (int)$User['consult_email'] == 1 && $User['agent_status'] == 'available'){
								$html .='<div class="alerte_mobile  visible-xs"><a href="';
                                            $html .= $fbH->Html->url(
                                                array(
                                                    'language'      => $this->Session->read('Config.language'),
                                                    'controller'    => 'alerts',
                                                    'action'        => 'setnew',
                                                    $User['id']
                                                )
											);
									$html .='" class="alerte-link aebutton nx_openinlightbox nxtooltip">Recevoir une alerte</a></div>';
								}*/
			
			//status agent
			$set_title_status = '';
			$set_class_status = '';
			if ($User['agent_status'] == 'available'){
				$set_title_status = 'Disponible';
				$set_class_status = 'available';
			}elseif ($User['agent_status'] == 'busy'){
				$set_title_status = 'En consultation';
				$set_class_status = 'consultation';
			}elseif ($User['agent_status'] == 'unavailable'){
				if($fbH->getPlanningDispo($User['id'])){
					$set_title_status = $fbH->getPlanningDispo($User['id']);	
				}else{
					$set_title_status = __('Indisponible');
				}

				$set_class_status = 'retour';
			}
			
			//bar mobile
        	$mobile_bar = $fbH->getAccountBarInfo($User,$rows[0]['second_from_last_status']);
			
			$this->jsonRender(array('html' => $html, 'set_title_status' => $set_title_status, 'set_class_status' => $set_class_status , 'mobile_bar' => $mobile_bar));
		}
	}
	
	
	 //L'historique des communications du client
    public function promo_codes(){  }	
    public function masterclass(){   }
    public function photos_od(){    }
    public function videos_od(){    }
    public function pdf_od(){    }
    public function subscription(){    }
    public function payment_request(){    }
    public function affiliate_payment(){    }
    public function docs_pdf(){    }
    public function loyalty_bonus(){    }
    
    public function payment_details(){   
	 $this->loadModel('UserCountry');
	 $this->set('select_countries', $this->UserCountry->getCountriesList($this->Session->read('Config.id_lang')));
    }
    
    
    public function certif_account(){    }
    public function photos_videos_od(){    }
    public function my_masterclass(){    }
    public function my_videos(){    }
    public function my_private_messages(){    }
    public function paid_consultations_email(){    }
  
    
}

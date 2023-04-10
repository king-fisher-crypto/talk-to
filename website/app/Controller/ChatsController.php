<?php
    App::import('Controller', 'Extranet');
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');
	App::import('Vendor', 'Noox/Api');

    class ChatsController extends AppController {
        public $uses = array('User', 'Chat');
        private $status = array('Connecting','Connecting_mobile', 'Online', 'Disconnecting');
        private $cause = array('agent', 'client', 'client_credit', 'agent_timeout', 'client_timeout');
        private $messagesChat = array();

        public function beforeFilter() {

            $this->Auth->allow('index', 'create_session', 'do_session', 'uploadphoto', 'removephoto');

            $this->messagesChat = array(
                'agent'     => array(
                    'client'        => array(
                        'status'    => __('Déconnexion du client. Vous pouvez fermer le chat'),
                        'info'      => __('Déconnexion du client. Vous pouvez fermer le chat')
                    ),
                    'client_credit'   => array(
                        'status'    => __('Votre client n\'a plus de crédit. Il a été déconnecté du chat.'),
                        'info'      => __('Votre client n\'a plus de crédit. Il a été déconnecté du chat.')
                    ),
                    'client_timeout'  => array(
                        'status'    => __('Votre client n\'est plus présent sur le site. Il a été déconnecté du chat.'),
                        'info'      => __('Déconnexion du client. Vous pouvez fermer le chat')
                    ),
                    'accueil'       => __('Engagez la discussion avec votre client (Le décompte client commencera dès que vous aurez envoyé la première réponse)'),
					'mobile'       => __('Connexion client depuis un Mobile, la consultation peut être altérée en fonction<br />de la qualité de son réseau 3G, 4G ou Wifi…')
                ),
                'client'    => array(
                    'agent'         => array(
                        'status'    => __('Le chat est interrompu. Vous pouvez relancer une consultation.'),
                        'info'      => __('Le chat est interrompu. Vous pouvez relancer une consultation.')
                    ),
                    'client_credit'   => array(
                        'status'    => __('Vous n\'avez plus assez de crédits. Vous avez été déconnecté du chat.'),
                        'info'      => __('Vous n\'avez plus assez de crédits. Vous avez été déconnecté du chat.')
                    ),
                    'agent_timeout' => array(
                        'status'    => __('L\'expert s\'est déconnecté du site. Vous pouvez fermer le site.'),
                        'info'      => __('Le chat est interrompu. Vous pouvez relancer une consultation.')
                    ),
                    'accueil'       => __('<span class="blink_text">La connexion avec l\'expert est en cours. Veuillez patienter...</span>'),
					'mobile'       => __('Assurez-vous de la qualité de votre réseau, 4G ou Wifi conseillés pour consulter dans les meilleures conditions.')
                )
            );

            parent::beforeFilter();
        }

        public function index(){

        }

        public function hasSession(){
            if($this->request->is('ajax')){
				$dateNow = date('Y-m-d H:i:s');
                if($this->Auth->user('role') === 'agent'){
                    $agent = $this->User->find('first', array(
                        'fields' => array('agent_status', 'consult_chat', 'date_last_activity'),
                        'conditions' => array('id' => $this->Auth->user('id')),
                        'recursive' => -1
                    ));
                    //si l'agent est unavailable ou non consultable par chat
                    if($agent['User']['consult_chat'] == 0)
                        $this->jsonRender(array('return' => false, 'agent' => true));
					
					if(Tools::diffInSec($agent['User']['date_last_activity'], $dateNow) >= Configure::read('Chat.maxTimeCloseChat')){
						$this->jsonRender(array('return' => false, 'agent' => true));
					}

                    //MAJ de la date d'activité
                    $this->User->id = $this->Auth->user('id');
                    $this->User->saveField('date_last_activity', date('Y-m-d H:i:s'));
					
					if($agent['User']['agent_status'] == 'unavailable' || $agent['User']['consult_chat'] == 0)
                        $this->jsonRender(array('return' => false, 'agent' => true));
                }
                //Utilisateur est un client ou agent
                if($this->Auth->user('role') === 'client' || $this->Auth->user('role') === 'agent'){
                    //A t-il une session en cours ?
                    if($this->Chat->isInChat($this->Auth->user('id'),$this->Auth->user('role'))){
                        //Session du chat
                        $session = $this->Chat->field('session_id', $this->basicConditions());
                        $idChat = $this->Chat->field('id', $this->basicConditions());
                        //Les derniers id
                        $lastIds = $this->getLastIds($idChat);
						
                    $this->jsonRender(array(
                            'return' => true,
                            'session' => $session,
                            'url' => Router::url(array('controller' => 'chats', 'action' => 'getTemplateChat')),
                            'otherUrl'  => $this->getUrl(),
                            'lastIdMsg'  => $lastIds['msg'],
                            'lastIdEvent'    => $lastIds['event'],
							'agent' => ($this->Auth->user('role') === 'agent' ?true:false)
                        ));
                    }
                }

                $this->jsonRender(array('return' => false, 'agent' => ($this->Auth->user('role') === 'agent' ?true:false)));
            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function start_session(){
            if($this->request->is('ajax')){
                switch ($this->Auth->user('role')){
                    case 'client':
                        //La session
                        $chat = $this->Chat->find('first',array(
                            'fields' => array('Chat.session_id', 'Agent.agent_number', 'Agent.pseudo'),
                            'conditions' => $this->basicConditions(),
                            'recursive' => 0
                        ));
                        //La photo
                        $extra = new ExtranetController;
                        $photo = $extra->mediaAgentExist($chat['Agent']['agent_number'], 'Image');
                        if($photo === false)
                            $photo = Configure::read('Site.defaultImage');
                        $this->jsonRender(array('return' => true, 'data' => array(
                            'session'       => $chat['Chat']['session_id'],
                            'pseudo'        => $chat['Agent']['pseudo'],
                            'msg'           => $this->messagesChat['client']['accueil'],
                            'photo'         => $photo,
                            'urlUpdate'     => Router::url(array('controller' => 'chats', 'action' => 'getLastDate')),
                            'maxDisplay'    => Configure::read('Chat.maxDisplay')
                        )));
                        break;
                    case 'agent':
                        //La session
                        $chat = $this->Chat->find('first', array(
                            'fields' => array('Chat.session_id', 'User.firstname'),
                            'conditions' => $this->basicConditions(),
                            'recursive' => 0
                        ));
                        //Si la session existe bien
                        if(!empty($chat) ){//&& !$this->request->isMobile()
                            $this->jsonRender(array('return' => true, 'data' => array(
                                'agent'         => true,
                                'session'       => $chat['Chat']['session_id'],
                                'pseudo'        => $chat['User']['firstname'],
                                'msg'           => $this->messagesChat['agent']['accueil'],
                                'urlUpdate'     => Router::url(array('controller' => 'chats', 'action' => 'getLastDate')),
                                'maxDisplay'    => Configure::read('Chat.maxDisplay')
                            )));
                        }
                        break;
                }
            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function create_session(){
			
			$this->layout = '';
            if($this->request->is('ajax')){
                //Utilisateur non connecté ??
                if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
					$intro = $this->getCmsPage(309, $id_lang);
					
					if(isset($this->params['id'])){
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
					$this->loadModel('UserCountry');
					$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
					if(isset($this->params['id'])){
						$this->set(array('title' =>__('Consultation par tchat<br />avec<span>').' '.$agent['User']['pseudo'].'</span>', 'content' => $content, 'User' => $agent['User']));
						$response = $this->render('/Elements/modal_consult');
					}else{
						 $this->set(array('title' => __('Accès client'), 'content' => $intro["PageLang"]['content'].$content, 'button' => __('Annuler')));
						$response = $this->render('/Elements/modal');
					}
                    $this->jsonRender(array('return' => false, 'typeError' => 'login', 'value' => $response->body()));
                }else{
                    //Si l'utilisateur est un client
                    if($this->Auth->user('role') === 'client'){
                        //Si l'id de l'agent n'existe pas
                        if(!isset($this->params['id']))
                            $this->jsonRender(array('return' => false, 'typeError' => 'missParam', 'value' => Router::url(array('controller' => 'home', 'action' => 'index'), true)));
                        //L'id de l'agent
                        $idAgent = $this->params['id'];
                        //On récupère l'agent
                        $agent = $this->User->find('first',array(
                            'fields' => array('id','pseudo', 'agent_number', 'agent_status','has_photo'),
                            'conditions' => array('id' => $idAgent, 'active' => 1, 'deleted' => 0, 'consult_chat' => 1, 'agent_status' => 'available', 'role' => 'agent'),
                            'recursive' => -1
                        ));
                        //Si pas d'agent
                        if(empty($agent))
                            $this->jsonRender(array('return' => false, 'typeError' => 'noAgent', 'value' => Router::url(array('controller' => 'home', 'action' => 'index'),true)));
                        $customer = $this->Auth->user();
                        //Assez de crédit ??
                        if($customer['credit'] < Configure::read('Chat.creditMinPourChat'))
                            $this->returnViewModal('not_enough_credit', 'noCredit', $idAgent, $agent);
                        //Est-il déjà sur un chat ??
                        if($this->Chat->isInChat($customer['id'],$customer['role']))
                            $this->returnViewModal('already_chat', 'chat', $idAgent, $agent);

                        //Tout est OK, popup transition tchat
						$tchat_txt = $this->getCmsPage(450, $id_lang);
						
						//clean folder picture
						
						$folder = new Folder(Configure::read('Site.pathChatLiveAdmin').DS.$this->Auth->user('id'),true,0755);
						if(is_dir($folder->path)){
							$files = array_diff(scandir($folder->path), array('.','..'));
							foreach ($files as $file) {
								unlink($folder->path.DS.$file);
							}
							rmdir($folder->path);
						}
						
						$this->set(array('title' =>__('Consultation par tchat<br />avec<span>').' '.$agent['User']['pseudo'].'</span>', 'content' => $tchat_txt["PageLang"]['content'], 'User' => $agent['User']));
						$response = $this->render('/Home/media_chat');
						
						$this->jsonRender(array('return' => false, 'html' => $response->body(), 'typeError' => 'chatPopup'));
                        
                    }else
                        $this->jsonRender(array('return' => false, 'typeError' => 'noCustomer', 'value' => __('Uniquement disponible pour les comptes clients.')));
                }
            }else{
				$this->redirect(array('controller' => 'home', 'action' => 'index'),true,301);
			}
                
        }
		
				
		public function do_session(){
			
			
            if($this->request->is('ajax')){
                //Utilisateur non connecté ??
                if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
					var_dump($this->Auth->user('role'));exit;
                    $this->layout = '';
					$intro = $this->getCmsPage(309, $id_lang);
					
					if(isset($this->params['id'])){
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
					$this->loadModel('UserCountry');
					$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
					if(isset($this->params['id'])){
						$this->set(array('title' =>__('Consultation par tchat<br />avec<span>').' '.$agent['User']['pseudo'].'</span>', 'content' => $content, 'User' => $agent['User']));
						$response = $this->render('/Elements/modal_consult');
					}else{
						 $this->set(array('title' => __('Accès client'), 'content' => $intro["PageLang"]['content'].$content, 'button' => __('Annuler')));
						$response = $this->render('/Elements/modal');
					}
                    $this->jsonRender(array('return' => false, 'typeError' => 'login', 'value' => $response->body()));
                }else{
                    //Si l'utilisateur est un client
                    if($this->Auth->user('role') === 'client'){
						
						//verif si client en consult
						 $this->loadModel('UserCreditLastHistory');
						 $in_com = $this->UserCreditLastHistory->find('first', array(
							'fields'        => array('UserCreditLastHistory.*'),
							'conditions'    => array('UserCreditLastHistory.users_id' => $this->Auth->user('id'), 'UserCreditLastHistory.date_end' => NULL),
							'recursive'     => 1
						 ));
						 if($in_com)
							 $this->jsonRender(array('return' => false, 'typeError' => 'missParam', 'value' => Router::url(array('controller' => 'home', 'action' => 'index'), true)));
						
                        //Si l'id de l'agent n'existe pas
                        if(!isset($this->params['id']))
                            $this->jsonRender(array('return' => false, 'typeError' => 'missParam', 'value' => Router::url(array('controller' => 'home', 'action' => 'index'), true)));
                        //L'id de l'agent
                        $idAgent = $this->params['id'];
                        //On récupère l'agent
                        $agent = $this->User->find('first',array(
                            'fields' => array('pseudo', 'agent_number'),
                            'conditions' => array('id' => $idAgent, 'active' => 1, 'deleted' => 0, 'consult_chat' => 1, 'agent_status' => 'available', 'role' => 'agent'),
                            'recursive' => -1
                        ));
                        //Si pas d'agent
                        if(empty($agent))
                            $this->jsonRender(array('return' => false, 'typeError' => 'noAgent', 'value' => Router::url(array('controller' => 'home', 'action' => 'index'),true)));
                        $customer = $this->Auth->user();
                        //Assez de crédit ??
                        if($customer['credit'] < Configure::read('Chat.creditMinPourChat'))
                            $this->returnViewModal('not_enough_credit', 'noCredit', $idAgent, $agent);
                        //Est-il déjà sur un chat ??
                        if($this->Chat->isInChat($customer['id'],$customer['role']))
                            $this->returnViewModal('already_chat', 'chat', $idAgent, $agent);

                        //Tout est OK, on crée une session de chat
                        $session = uniqid($customer['personal_code']);
                        $this->Chat->create();
						
						$source = 'desktop';
						$statut = 'Connecting';
						if($this->request->isMobile()){
							$source = 'mobile';	
							$statut = 'Connecting_mobile';
						}
						
                        if($this->Chat->save(array(
                            'from_id'       => $customer['id'],
                            'to_id'         => $idAgent,
                            'session_id'    => $session,
                            'date_start'    => date('Y-m-d H:i:s'),
							'source'    => $source
                        ))){
                            //L'agent est occupé
                            $this->agentStatus($idAgent, 'busy');
                            //On met à jour les dates d'activité du chat
                            $this->updateChatDateActivity($customer['id']);
                            $this->updateChatDateActivity($idAgent);

                            //On save le 1er status, connexion en cours
                            $this->Chat->ChatEvent->save(array(
                                'user_id'   => $this->Auth->user('id'),
                                'chat_id'   => $this->Chat->id,
                                'status'    => $statut,
                                'writting'  => 0,
                                'send'      => 0,
                                'date_add'  => date('Y-m-d H:i:s')
                            ));
                            //On renvoie l'id de la session
                            $this->jsonRender(array(
                                'return'            => true,
                                'session'           => $session,
                                'url'               => Router::url(array('controller' => 'chats', 'action' => 'getTemplateChat')),
                                'otherUrl'          => $this->getUrl(),
                                'lastIdMsg'         => 0,
                                'lastIdEvent'       => $this->Chat->ChatEvent->id
                            ));
                        }else
                            $this->jsonRender(array('return' => false, 'typeError' => 'create', 'value' => __('Erreur lors de la création de votre chat. Veuillez réessayer.')));
                    }else
                        $this->jsonRender(array('return' => false, 'typeError' => 'noCustomer', 'value' => __('Uniquement disponible pour les comptes clients.')));
                }
            }else{
				$this->redirect(array('controller' => 'home', 'action' => 'index'),true,301);
			}
                
        }

        public function viewModal(){
            if($this->request->is('ajax')){

                $nameView = $this->request->data['param']['view'];
                //l'agent
                $agent = $this->request->data['param']['agent'];

                $this->autoRender = false;
                $this->layout = '';
                //Selon la vue demnadé
                switch ($nameView){
                    case 'not_enough_credit' :
                    case 'already_chat' :
                        $this->set(array('pseudo' => $agent['User']['pseudo'], 'consult' => 'chat', 'minCredit' => Configure::read('Chat.creditMinPourChat')));
                        $response = $this->render('/Elements/'.$nameView);
                        $this->set(array('title' => __('Consultation par chat avec').' '.$agent['User']['pseudo'], 'content' => $response->body(), 'button' => 'Ok'));
                        $response = $this->render('/Elements/modal');
                        $this->jsonRender(array('html' => $response->body()));
                        break;
                }
            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        private function returnViewModal($view, $typeError, $id, $agent){
            $this->jsonRender(array(
                'return' => false,
                'typeError' => $typeError,
                'value' => Router::url(array('controller' => 'chats', 'action' => 'viewModal'),true),
                'param' => array(
                    'id' => $id,
                    'view' => $view,
                    'agent' => $agent
                )
            ));
        }

        public function getTemplateChat(){
            if($this->request->is('ajax')){
				
				$this->layout = '';
				
				/*generate note popup*/
				$html_note = '';
				$phone_note_title = '';
				$phone_note_text = '';
				$phone_note_birthday_day = '';
				$phone_note_birthday_month = '';
				$phone_note_birthday_year = '';
				$phone_note_sexe = '';
				$phone_note_call = '';
				$phone_note_tchat = '';
				$phone_id_client = '';
						
				if( $this->Auth->user('role') == 'agent' ){//&& !$this->request->isMobile()
							
							$chat = $this->Chat->find('first', array(
								'fields' => array('Chat.session_id','Chat.id','Chat.from_id', 'User.firstname', 'User.id', 'Agent.id'),
								'conditions' => $this->basicConditions(),
								'recursive' => 0
							));
							
							
							
							 $response_note = $this->render('/Elements/template_phone_notes');	
							 $html_note = $response_note->body();
							 $phone_note_title = $chat['User']['firstname'];
							 $phone_note_title = 'Notes sur le client : '.$phone_note_title;
							 
							 $this->loadModel('Notes');
					 		
							 $note = $this->Notes->find('first',array(
									'fields' => array('Notes.*'),
									'conditions' => array('id_agent' => $chat['Agent']['id'] , 'id_client' => $chat['User']['id']),
									'recursive' => 0
								));
	
							 if(isset($note['Notes']) && $note['Notes']['note']){
								$phone_note_text = $note['Notes']['note'];
							 }else{
								$phone_note_text = ''; 
							 }
							if(isset($note['Notes']) && $note['Notes']['birthday'] && $note['Notes']['birthday'] != '0000-00-00 00:00:00'){
							 $date_naissance = explode(' ',$note['Notes']['birthday']);
						 $date_naissance = explode('-',$date_naissance[0] );
						 $birthday = $date_naissance[2].'-'.$date_naissance[1].'-'.$date_naissance[0];
								 $phone_note_birthday_day = $date_naissance[2];
								 $phone_note_birthday_month = $date_naissance[1];
								 $phone_note_birthday_year = $date_naissance[0];
							 }
							 $phone_note_sexe = $note['Notes']['sexe'];
							 $phone_note_call = '';
							 $phone_note_tchat = $chat['Chat']['id'];
						$phone_id_client = $chat['Chat']['from_id'];
							 
				}
				if(($this->Auth->user('role') == 'agent') || $this->Auth->user('role') == 'client'){//!$this->request->isMobile() && 
                	$response = $this->render('/Elements/template_chat');
                	$this->jsonRender(array('html' => $response->body(),'html_note' => $html_note,'phone_note_title' => $phone_note_title, 'phone_note_text' => $phone_note_text, 'phone_note_call' => $phone_note_call,'phone_note_tchat' => $phone_note_tchat, 'phone_note_agent' => $this->Auth->user('id'),'phone_note_birthday_day' => $phone_note_birthday_day,'phone_note_birthday_month' => $phone_note_birthday_month,'phone_note_birthday_year' => $phone_note_birthday_year,'phone_note_sexe' => $phone_note_sexe,'phone_id_client' => $phone_id_client));
				}
			}else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }


        public function hasUpdate(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;
				$idChat = $this->Chat->getChatOpen($this->Auth->user('id'));
				
				if(is_numeric($idChat) && $this->Auth->user('role') == 'client'){
					//On send alert tel agent
					
					$chat = $this->Chat->find('first', array(
						'conditions' => array('Chat.id'=>$idChat),
						'recursive' => 0
					));
					$this->loadModel('User');
					$agent = $this->User->find('first', array(
						'conditions' => array('User.id'=>$chat['Chat']['to_id']),
						'recursive' => 0
					));
					

					$this->loadModel('ChatEvent');
					$chat_event = $this->ChatEvent->firstMessage($idChat,$chat['Chat']['to_id']);
					
					if(!$chat_event && $chat['Chat']['date_start'] && $agent['User']['alert_phone'] && $chat['Chat']['consult_date_start'] == null ){
						$consultSecond = $this->timeChat($chat['Chat']['date_start'], date('Y-m-d H:i:s'));
						if($consultSecond > 5 && $chat['Chat']['alert'] == 0){
							$this->Chat->id = $idChat;
							$this->Chat->saveField('alert',1);
						 $api = new Api();
						 $resultat = $api->alertAgent($agent['User']['agent_number'], 1);
							
						}
					}
				}
                //Si les paramètres requis sont là
                if(isset($requestData['lastIdEvent']) && isset($requestData['lastIdMsg'])){
                    //L'id du chat en cours pour l'utilisateur
                    $idChat = $this->Chat->getChatOpen($this->Auth->user('id'));

                    //Si pas de chat en cours
                    if(empty($idChat)){
                        //La cause de fermeture du dernier chat ferme
                        $cause = $this->closedBy($this->Auth->user('id'), $this->Auth->user('role'));
                        //Si client
                        if($this->Auth->user('role') === 'client'){
                            //On met à jour le crédit, côté interface
                            $current_credit = $this->User->field('credit', array('id' => $this->Auth->user('id')));
                            $this->Session->write('Auth.User.credit', $current_credit);
                        }

                        $this->jsonRender(array(
                            'return'        => false,
                            'hasSession'    => false,
                            'agent'         => ($this->Auth->user('role') === 'agent' ?true:false),
                            'status'        => $this->messagesChat[$this->Auth->user('role')][$cause]['status'],
                            'info'          => $this->messagesChat[$this->Auth->user('role')][$cause]['info']
                        ));
                    }
                    //On met à jour la date du chat de l'user
                    $this->updateChatDateActivity($this->Auth->user('id'));
					
					
					
                    //On va chercher les derniers id
                    $lastIds = $this->getLastIds($idChat);
                    //Si id event en BDD est plus grande que celui du JS
                    if($lastIds['event'] > $requestData['lastIdEvent'])
                        $this->jsonRender($this->getLastData($idChat, $requestData['lastIdMsg']));

                    $this->jsonRender(array('return' => false, 'hasSession' => true,'agent'  => ($this->Auth->user('role') === 'agent' ?true:false)));
                }else
                    $this->jsonRender(array('return' => false,'agent'         => ($this->Auth->user('role') === 'agent' ?true:false), 'msg' => __('Impossible d\'actualiser votre chat. Veuillez réactualiser la page.')));
            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        private function getLastData($idChat, $idMsg){
            //Le dernier evenement pour la session
            $lastEvent = $this->Chat->ChatEvent->getLastDate($idChat);
            //Nom du contact
            $name = $this->User->field(($this->Auth->user('role') === 'client' ?'pseudo':'firstname'), array('id' => $lastEvent['Chat'][($this->Auth->user('role') === 'client' ?'to_id':'from_id')]));

            //On récupère les derniers messages
            $lastMsgs = $this->Chat->ChatMessage->find('all', array(
                'fields' => array('ChatMessage.id', 'ChatMessage.user_id', 'ChatMessage.content', 'ChatMessage.date_add', 'Chat.session_id'),
                'conditions' => array(
                    'ChatMessage.chat_id' => $idChat,
                    'ChatMessage.id >' => $idMsg
                ),
                'recursive' => 0
            ));
            //formatage des données
            $data['session'] = $lastEvent['Chat']['session_id'];
            if($lastEvent['ChatEvent']['user_id'] != $this->Auth->user('id')){
                $data['Event']['status']['value'] = $lastEvent['ChatEvent']['status'];
                $data['Event']['status']['msg'] = ($lastEvent['ChatEvent']['status'] === 'Online'
                    ?__('En ligne')
                    :($lastEvent['ChatEvent']['status'] === 'Disconnecting'
                        ?''//__(($this->Auth->user('role') === 'client' ?'Déconnexion de l\'expert.':'Déconnexion du client.').' Vous pouvez fermer le chat.')
                        :''
                    )
                );
                $data['Event']['writting']['value'] = $lastEvent['ChatEvent']['writting'];
                $data['Event']['writting']['msg'] = ($lastEvent['ChatEvent']['writting'] == 1
                    ?$name.' '.__('est en train d\'écrire...')
                    :''
                );
            }

            //Id du dernier message
            $lastIdMsg = end($lastMsgs)['ChatMessage']['id'];
            //Si id vide
            if(empty($lastIdMsg))
                $lastIdMsg = $idMsg;

            $data['Message'] = array();
            //Pour chaque message
            foreach($lastMsgs as $key => $message){
                if($lastEvent['Chat']['session_id'] === $message['Chat']['session_id']){
                    $data['Message'][] = array(
                        'content'   => nl2br(h($message['ChatMessage']['content'])),
                        'name'      => ($message['ChatMessage']['user_id'] == $this->Auth->user('id') ?__('Moi'):$name),
                        'time'      => CakeTime::format($message['ChatMessage']['date_add'], '%H:%M')//Tools::dateUser($this->Session->read('Config.timezone_user'),
                    );
                    unset($lastMsgs[$key]);
                }
            }
            //on envoie les données
            return array('return' => true, 'data' => $data, 'lastIdEvent' => $lastEvent['ChatEvent']['id'], 'lastIdMsg' => $lastIdMsg);
        }

        public function setStatus(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //On récupère l'id pour la session reçue
                $idChat = $this->Chat->field('id', $this->basicConditions($requestData['session']));
                //Si le chat existe
                if($idChat !== false){
                    //On save le status
                    $this->Chat->ChatEvent->create();
                    $this->Chat->ChatEvent->save(array(
                        'user_id'   => $this->Auth->user('id'),
                        'status'    => $requestData['status'],
                        'writting'  => $requestData['writting'],
                        'chat_id'   => $idChat,
                        'date_add'  => date('Y-m-d H:i:s')
                    ));

                    //Est-ce les premières lettres tapées après le 1er message d'un client
                    if(Configure::read('Chat.consultStartAnswer') === false && $this->Auth->user('role') === 'agent'){
                        //Id du client
                        $idCustomer = $this->Chat->field('from_id', $this->basicConditions($requestData['session']));
                        //Le client a t-il déjà posté un message et est-ce que l'agent tape sur son clavier
                        if($this->Chat->ChatEvent->firstMessage($idChat, $idCustomer) && $this->Chat->consultBegin($idChat) === false && $requestData['writting'] == 1){
                            //La consultation commence
                            $this->Chat->id = $idChat;
                            $this->Chat->saveField('consult_date_start', date('Y-m-d H:i:s'));
                        }
                    }
                    $this->jsonRender(array('return' =>  true));
                }
                $this->jsonRender(array('return' => false));

            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function saveMessage(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //On récupère l'id pour la session reçue
                $idChat = $this->Chat->field('id', $this->basicConditions($requestData['session']));
                //Si le chat existe
                if($idChat !== false){
                    $dateNow = date('Y-m-d H:i:s');
                    //On save un status
                    $this->Chat->ChatEvent->create();
                    $this->Chat->ChatEvent->save(array(
                        'user_id'   => $this->Auth->user('id'),
                        'chat_id'   => $idChat,
                        'status'    => $requestData['status'],
                        'writting'  => 0,
                        'send'      => 1,
                        'date_add'  => $dateNow
                    ));
					
					//check email in live
					$tab_msg = explode(' ',$requestData['msg']);
					foreach($tab_msg as &$msg){
						if(substr_count($msg,'@') || substr_count($msg,'gmail.com') || substr_count($msg,'outlook.com') || substr_count($msg,'gmx.fr') || substr_count($msg,'wanadoo.fr') || substr_count($msg,'orange.fr') || substr_count($msg,'free.fr') || substr_count($msg,'yahoo.com') || substr_count($msg,'hotmail.fr') || substr_count($msg,'hotmail.com') || substr_count($msg,'aol.com') || substr_count($msg,'aol.fr') || substr_count($msg,'yahoo.fr') || substr_count($msg,'live.com') || substr_count($msg,'live.fr') || substr_count($msg,'http') || substr_count($msg,'www')){
							$msg = '******';
						}
					}
					$requestData['msg'] = implode(' ',$tab_msg);
						
                    //On save le message
                    $this->Chat->ChatMessage->create();
                    if($this->Chat->ChatMessage->save(array(
                        'user_id'   => $this->Auth->user('id'),
                        'chat_id'   => $idChat,
                        'content'   => $requestData['msg']
                    ))){
                        //Est-ce le premier message après le 1er message d'un client
                        if(Configure::read('Chat.consultStartAnswer') && $this->Auth->user('role') === 'agent'){
                            //Id du client
                            $idCustomer = $this->Chat->field('from_id', $this->basicConditions($requestData['session']));
                            //Le client a t-il déjà posté un message
                            if($this->Chat->ChatEvent->firstMessage($idChat, $idCustomer) && $this->Chat->consultBegin($idChat) === false){
                                //La consultation commence
                                $this->Chat->id = $idChat;
                                $this->Chat->saveField('consult_date_start', date('Y-m-d H:i:s'));
                            }
                        }

                        //On renvoie les données du message
                        $message = array(
                            'content'   => nl2br(h($requestData['msg'])),
                            'name'      => __('Moi'),
							'class'		=> 'txt_grey',
                            'time'      => CakeTime::format($this->Chat->ChatMessage->field('date_add'), '%H:%M')//Tools::dateUser($this->Session->read('Config.timezone_user'),
                        );

                        $this->jsonRender(array('return' => true, 'message' => $message));
                    }else
                        $this->jsonRender(array('return' => false, 'typeError' => 'save', 'value' => __('Erreur dans l\'envoi de votre message.')));
                }
                $this->jsonRender(array('return' => false));

            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function stop_session(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //Si fermeture
                if(isset($requestData['session'])){
                    $chat = $this->Chat->find('first',array(
                        'fields' => array('Chat.id', 'Chat.session_id', 'Chat.consult_date_start', 'Chat.from_id', 'Chat.to_id', 'Agent.pseudo', 'User.credit'),
                        'conditions' => $this->basicConditions($requestData['session']),
                        'recursive' => 0
                    ));
                    //Si le chat existe
                    if(!empty($chat)){
                        $return = $this->Chat->closeChat($chat, $this->Auth->user('id'), $this->Auth->user('role'));
                        if($return !== false){
                            //MAJ du credit pour l'interface
                            if($this->Auth->user('role') === 'client' && $return['return'] !== false){
                                $current_credit = $this->User->field('credit', array('User.id' => $this->Auth->user('id')));
                                $this->Session->write('Auth.User.credit', $current_credit);
                            }
                            if($this->Auth->user('role') === 'agent')
                                $return = array_merge($return, array('agent' => true));

                            //L'agent est disponible
                            $this->agentStatus($chat['Chat']['to_id'], 'available');
							
                            $this->jsonRender($return);
                        }
                    }
                    $this->jsonRender(array('return' => false));
                }else //Message de fermeture
                    $this->jsonRender(array('msg' => __('Voulez-vous vraiment fermer le chat ?')));

            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function hasCredit(){
            if($this->request->is('ajax')){
                //On récupère la session en cours
                $chat = $this->Chat->find('first', array(
                    'fields' => array('Chat.*', 'Agent.pseudo', 'User.credit'),
                    'conditions' => array('date_end' => null, 'consult_date_start !=' => null, 'OR' => array(
                        'to_id' => $this->Auth->user('id'),
                        'from_id' => $this->Auth->user('id')
                    )),
                    'recursive' => 0
                ));
				
				

                //Si pas de chat avec une consultation en cours
                if(empty($chat)){
                    $this->jsonRender(array('return' => true));
                }else{
					
					
					$lastmessageClient = $this->Chat->ChatMessage->find('first', array(
                        'fields' => array('date_add'),
                        'conditions' => array('chat_id' => $chat['Chat']['id'], 'user_id' =>$chat['Chat']['from_id']),
                        'order' => 'id desc',
                        'recursive' => -1
                    ));
				
					$lastmessageAgent = $this->Chat->ChatMessage->find('first', array(
							'fields' => array('date_add'),
							'conditions' => array('chat_id' => $chat['Chat']['id'], 'user_id' =>$chat['Chat']['to_id']),
							'order' => 'id desc',
							'recursive' => -1
						));
					
					
                    //Durée de la consult
                    $consultSecond = $this->timeChat($chat['Chat']['consult_date_start'], 'now');
                    //Coût du chat
                    $cost = ceil($consultSecond/(int)Configure::read('Site.secondePourUnCredit'));
                    //si le coût du chat est plus grand que le nombre de crédit, alors fermeture du chat (à l'heure d'aujourd'hui, un crédit = 1sec)
                    if($cost >= ($chat['User']['credit'] - 2)){
                        //On ferme le chat
                        $result = $this->Chat->closeChat($chat, $chat['Chat']['from_id'], 'client_credit');

                        if($result !== false){
                            //Si fermeture ok
                            if($result['return']){
                                //On met le crédit à 0, il reste les 2sec de marge au-dessus
                                $this->User->id = $chat['Chat']['from_id'];
                                $this->User->saveField('credit', 0);

                                //L'agent est disponible
                                $this->agentStatus($chat['Chat']['to_id'], 'available');

                                $this->jsonRender(array('return' => false));
                            }else
                                $this->jsonRender($result);
                        }

                        $this->jsonRender(array('return' => false, 'value' => __('Echec lors de la fermeture du chat.')));
                    }else{
                        $creditRestant = (int)$chat['User']['credit'] - $cost;  //Crédit restant virtuellement
                        //Temps restant
                        $sec = $creditRestant * Configure::read('Site.secondePourUnCredit');
                        //Selon le nombre de seconde qu'il reste
                        if($sec <= 60){
                            $msg['client'] = __('Il vous reste').' '.$sec.'sec de crédit';
                            $msg['agent'] = __('Il reste').' '.$sec.'sec de crédit à votre client';
                        }else if($sec <= 120){
                            $msg['client'] = __('Il vous reste moins de 2min').' ('.$this->secInString($sec).')';
                            $msg['agent'] = __('Il reste moins de 2min de crédit à votre client').' ('.$this->secInString($sec).')';
                        }else if($sec <= 180){
                            $msg['client'] = __('Il vous reste moins de 3min').' ('.$this->secInString($sec).')';
                            $msg['agent'] = __('Il reste moins de 3min de crédit à votre client').' ('.$this->secInString($sec).')';
                        }else if($sec <= 240){
                            $msg['client'] = __('Il vous reste moins de 4min').' ('.$this->secInString($sec).')';
                            $msg['agent'] = __('Il reste moins de 4min de crédit à votre client').' ('.$this->secInString($sec).')';
                        }else if($sec <= 300){
                            $msg['client'] = __('Il vous reste moins de 5min').' ('.$this->secInString($sec).')';
                            $msg['agent'] = __('Il reste moins de 5min de crédit à votre client').' ('.$this->secInString($sec).')';
                        }else if($sec <= 360){
                            $msg['client'] = __('Il vous reste moins de 6min').' ('.$this->secInString($sec).')';
                            $msg['agent'] = __('Il reste moins de 6min de crédit à votre client').' ('.$this->secInString($sec).')';
                        }else{
                            $msg['client'] = __('Temps restant :').' '.$this->secInString($sec);
                            $msg['agent'] = __('Crédit client restant :').' '.$this->secInString($sec);
                        }
						
						$dateNow = date('Y-m-d H:i:s');
						$alert_time['agent'] = ' ';
						$alert_time['client'] = ' ';
						
						 if(!empty($chat['User']['chat_last_activity']) && Tools::diffInSec($chat['User']['chat_last_activity'], $dateNow) >= Configure::read('Chat.maxTimeCloseChat')){
							$alert_time['agent'] = __('Le client semble avoir perdue sa connexion, le tchat va se couper automatiquement.');
						}

						//Agent deco
						if(!empty($chat['User']['chat_last_activity']) && Tools::diffInSec($chatData['Agent']['chat_last_activity'], $dateNow) >= Configure::read('Chat.maxTimeCloseChat')){
							$alert_time['client'] = __('L\'expert semble avoir perdue sa connexion, le tchat va se couper automatiquement.');
						}


						//Client inactif
						if($lastmessageClient && Tools::diffInSec($lastmessageClient['ChatMessage']['date_add'], $dateNow) >= (Configure::read('Chat.maxDelayInactif') - 15 )){
							$alert_time['client'] =__('Inactivité détectée de votre côté, veuillez écrire et valider un message avant que le tchat ne se coupe automatiquement.');
						}

						//Agent inactif
						if($lastmessageAgent && Tools::diffInSec($lastmessageAgent['ChatMessage']['date_add'], $dateNow) >= (Configure::read('Chat.maxDelayInactif') - 15 )){
							$alert_time['agent'] = __('Inactivité détectée de votre côté, veuillez écrire et valider un message avant que le tchat ne se coupe automatiquement.');
						}
						

                        //Le temps écoulé depuis le début de la conversation
                        $dateStart = $chat['Chat']['consult_date_start'];
                        $dateNow = date('Y-m-d H:i:s');
                        $msg['time_value'] = __('Temps de consultation : ').$this->secInString(Tools::diffInSec($dateStart, $dateNow));
						
						//check if customer picture
						$picture = '';
						$folder = new Folder(Configure::read('Site.pathChatLiveAdmin').DS.$chat['Chat']['from_id'],true,0755);
						if(is_dir($folder->path)){
							$files = array_diff(scandir($folder->path), array('.','..'));
							foreach ($files as $file) {
								$picture .= '<a href="'.DS.Configure::read('Site.pathChatLive').DS.$chat['Chat']['from_id'].DS.$file.'" class="chat_picture"><span class="icon_photo"></span></a>';
							}
						}

                        $this->jsonRender(array('return' => true, 'value' => $sec, 'msg' => $msg, 'alert_time' => $alert_time, 'session' => $chat['Chat']['session_id'], 'picture' => $picture));
                    }
                }
            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        public function getLastMessage(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //Le chat
                $chat = $this->Chat->find('first',array(
                    'fields' => array('Chat.id', 'Chat.from_id', 'Chat.to_id', 'User.firstname', 'Agent.pseudo'),
                    'conditions' => $this->basicConditions($requestData['session']),
                    'recursive' => 0
                ));
                //Si le chat existe
                if(!empty($chat)){
                    //On récupére les messages
                    $messages = $this->Chat->ChatMessage->find('all', array(
                        'fields' => array('id', 'user_id', 'content', 'date_add'),
                        'conditions' => array('chat_id' => $chat['Chat']['id']),
                        'order' => 'id desc',
                        'limit' => 25,
                        'recursive' => -1
                    ));
                    //On récupère le dernier status
                    $status = $this->Chat->ChatEvent->find('first', array(
                        'fields' => array('status'),
                        'conditions' => array('chat_id' => $chat['Chat']['id']),
                        'order' => 'id desc',
                        'recursive' => -1
                    ));
                    switch ($status['ChatEvent']['status']){
                        case 'Connecting' :
                            $status = $this->messagesChat[$this->Auth->user('role')]['accueil'];
                            break;
						case 'Connecting_mobile' :
                            $status = $this->messagesChat[$this->Auth->user('role')]['mobile'];
                            break;
                        case 'Online' :
                            $status = __('En ligne');
                            break;
                    }
                    //S'il y a des messages
                    if(!empty($messages)){
                        $lastMessages = array();
                        //Le nom de l'autre contact
                        $name = ($this->Auth->user('role') === 'client' ?$chat['Agent']['pseudo']:$chat['User']['firstname']);
                        foreach($messages as $message){
                            $lastMessages[] = array(
                                'content'   => nl2br(h($message['ChatMessage']['content'])),
                                'name'      => ($message['ChatMessage']['user_id'] == $this->Auth->user('id') ?__('Moi'):$name),
                                'time'      => CakeTime::format($message['ChatMessage']['date_add'], '%H:%M')//Tools::dateUser($this->Session->read('Config.timezone_user')
                            );
                        }
                        $this->jsonRender(array('return' => true, 'messages' => $lastMessages, 'event' => $status, 'lastIdMsg' => $messages[0]['ChatMessage']['id']));
                    }
                    $this->jsonRender(array('return' => true, 'event' => $status));
                }
                $this->jsonRender(array('return' => false));

            }else
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
        }

        private function timeChat($start, $end){
            if(empty($start) || empty($end))
                return false;
            $tmstmpStart = new DateTime($start);
            $tmstmpStart = $tmstmpStart->getTimestamp();
            $tmstmpEnd = new DateTime($end);
            $tmstmpEnd = $tmstmpEnd->getTimestamp();
            return ($tmstmpEnd - $tmstmpStart);
        }

        private function closedBy($idUser, $role){
            if(empty($idUser) || !is_numeric($idUser) || empty($role))
                return false;

            $chat = $this->Chat->find('first', array(
                'fields'        => 'closed_by',
                'conditions'    => array('date_start !=' => null, 'date_end !=' => null, ($role === 'client' ?'from_id':'to_id') => $idUser),
                'order'         => 'date_end DESC',
                'recursive'     => -1
            ));

            //Si pas de chat
            if(empty($chat))
                return false;
            else
                return $chat['Chat']['closed_by'];
        }

        private function basicConditions($session = array()){
            if(empty($session))
                return array(
                    ($this->Auth->user('role') === 'client' ?'Chat.from_id':'Chat.to_id') => $this->Auth->user('id'),
                    'Chat.date_start !=' => null,
                    'Chat.date_end'      => null
                );
            else
                return array(
                    'Chat.session_id'       => $session,
                    'Chat.date_start !='    => null,
                    'Chat.date_end'         => null,
                    ($this->Auth->user('role') === 'client' ?'Chat.from_id':'Chat.to_id') => $this->Auth->user('id')
                );
        }

        private function getUrl(){
            return array(
                'urlStartSession'   => Router::url(array('controller' => 'chats', 'action' => 'start_session')),
                'urlSetStatus'      => Router::url(array('controller' => 'chats', 'action' => 'setStatus')),
                'urlUpdate'         => Router::url(array('controller' => 'chats', 'action' => 'hasUpdate')),
                'urlPostMessage'    => Router::url(array('controller' => 'chats', 'action' => 'saveMessage')),
                'urlStopSession'    => Router::url(array('controller' => 'chats', 'action' => 'stop_session')),
                'urlHasCredit'      => Router::url(array('controller' => 'chats', 'action' => 'hasCredit')),
                'urlGetMessage'     => Router::url(array('controller' => 'chats', 'action' => 'getLastMessage'))
            );
        }

        private function secInString($sec){
            if(empty($sec))
                return '';
            else{
                $h = (int)($sec/3600);
                $sec -= (3600 * $h);
                $m = (int)($sec/60);
                $sec -= (60 * $m);

                return str_pad($h,2,'0',STR_PAD_LEFT).'h'.str_pad($m,2,'0',STR_PAD_LEFT).'min'.str_pad($sec,2,'0',STR_PAD_LEFT).'sec';
            }
        }

        private function agentStatus($idAgent, $status){
            //Modification du status
            $this->User->id = $idAgent;
            $this->User->saveField('agent_status', $status);
            $this->loadModel('UserStateHistory');
            $this->UserStateHistory->create();
            $this->UserStateHistory->save(array(
                'user_id'   => $idAgent,
                'state'     => $status
            ));

            //Si status = "available"
            if(strcmp($status, 'available') == 0){
                //Le code de l'agent
                $agent_number = $this->User->field('agent_number');
                //On alerte les clients qui l'ont demandé si le statut est available
                App::import('Controller', 'Alerts');
                $alerts = new AlertsController();
                $alerts->alertUsersForUserAvailability($agent_number);
            }
        }

        private function updateChatDateActivity($idUser){
            //Pas d'id
            if(empty($idUser) || !is_numeric($idUser))
                return false;

            //MAJ de la date d'activité
            $this->User->id = $idUser;
            $this->User->saveField('chat_last_activity', date('Y-m-d H:i:s'));
            return true;
        }

        //Retourne les derniers id event et msg
        private function getLastIds($idChat){
            $lastIds = array('msg' => 0, 'event' => 0);
            //Si pas d'id
            if(empty($idChat))
                return $lastIds;

            //On retire les données User et Agent
            $this->Chat->unbindModel(
                array('belongsTo' => array('User', 'Agent'))
            );
            //On modifie les associations des Event et Message
            $this->Chat->bindModel(
                array('hasMany' => array(
                    'ChatEvent' => array(
                        'className' => 'ChatEvent',
                        'foreignKey' => 'chat_id',
                        'fields' => 'id',
                        'order' => 'ChatEvent.id DESC',
                        'limit' => 1
                    ),
                    'ChatMessage' => array(
                        'className' => 'ChatMessage',
                        'foreignKey' => 'chat_id',
                        'fields' => 'id',
                        'order' => 'ChatMessage.id DESC',
                        'limit' => 1
                    )
                ))

            );

            $datas = $this->Chat->find('first', array(
                'fields'        => array('Chat.id'),
                'conditions'    => array('Chat.id' => $idChat)
            ));

            //Si pas de données
            if(empty($datas))
                return $lastIds;

            if(!empty($datas['ChatEvent']))
                $lastIds['event'] = $datas['ChatEvent'][0]['id'];
            if(!empty($datas['ChatMessage']))
                $lastIds['msg'] = $datas['ChatMessage'][0]['id'];

            return $lastIds;
        }
		
		public function uploadphoto(){
			 if($this->request->is('ajax') && $this->Auth->user('id')){
				$requestData = $this->request->data; 
				 $folder = new Folder(Configure::read('Site.pathChatLiveAdmin').DS.$this->Auth->user('id'),true,0755);
				 if(is_dir($folder->path)){
					$link = $folder->path;
					if($_FILES['file']){
						$file = new File($_FILES['file']['tmp_name']);
						$file->copy($link.DS.$_FILES['file']['name']);
					 }
				 }
				 $this->jsonRender(array('return' => true));
			 }
			
			exit;
		}
		
		public function removephoto(){
			 if($this->request->is('ajax') && $this->Auth->user('id')){
				$requestData = $this->request->data; 
				 $folder = new Folder(Configure::read('Site.pathChatLiveAdmin').DS.$this->Auth->user('id'),true,0755);
				 if(is_dir($folder->path)){
					$link = $folder->path;
					if($requestData['filename']){
						unlink($link.DS.$requestData['filename']);
					 }
				 }
				 $this->jsonRender(array('return' => true));
			 }
			
			exit;
		}
    }
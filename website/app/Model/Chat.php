    <?php
    App::uses('AppModel', 'Model');
App::uses('CakeSession', 'Model/Datasource');
    /**
     * Chat Model
     *
     * @property User $User
     * @property ChatEvent $ChatEvent
     * @property ChatMessage $ChatMessage
     */
    class Chat extends AppModel {

        /**
         * Validation rules
         *
         * @var array
         */
        public $validate = array(
            'from_id' => array(
                'numeric' => array(
                    'rule' => array('numeric'),
                    //'message' => 'Your custom message here',
                    'allowEmpty' => false,
                    //'required' => false,
                    //'last' => false, // Stop validation after this rule
                    //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
            ),
            'to_id' => array(
                'numeric' => array(
                    'rule' => array('numeric'),
                    //'message' => 'Your custom message here',
                    'allowEmpty' => false,
                    //'required' => false,
                    //'last' => false, // Stop validation after this rule
                    //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
            )
        );

        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'User' => array(
                'className' => 'User',
                'foreignKey' => 'from_id',
                'conditions' => '',
                'fields' => array('role', 'firstname', 'credit', 'chat_last_activity'),
                'order' => ''
            ),
            'Agent' => array(
                'className' => 'User',
                'foreignKey' => 'to_id',
                'conditions' => '',
                'fields' => array('role', 'pseudo', 'agent_number', 'chat_last_activity'),
                'order' => ''
            )
        );

        /**
         * hasMany associations
         *
         * @var array
         */
        public $hasMany = array(
            'ChatEvent' => array(
                'className' => 'ChatEvent',
                'foreignKey' => 'chat_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            ),
            'ChatMessage' => array(
                'className' => 'ChatMessage',
                'foreignKey' => 'chat_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        /**
         *  Indique si l'user à un chat en cours ou pas
         *
         * @param $idUser   int     L'id de l'user
         * @param $role     string  Le role de l'user
         * @return bool
         */
        public function isInChat($idUser, $role){
            if(empty($idUser) || empty($role))
                return false;

            switch ($role){
                case 'agent' :
                    if($this->find('count',array(
                            'conditions' => array('to_id' => $idUser, 'date_start !=' => null, 'date_end' => null),
                            'recursive' => -1
                        )) > 0)
                        return true;
                    break;
                case 'client' :
                    if($this->find('count',array(
                        'conditions' => array('from_id' => $idUser, 'date_start !=' => null, 'date_end' => null),
                        'recursive' => -1
                    )) > 0)
                        return true;
                    break;
                default :
                    return false;
            }
            return false;
        }

        /**
         * Indique si la consultation a commencé pour un chat donné
         *
         * @param $id_chat  int     L'id du chat
         * @return bool|int
         */
        public function consultBegin($id_chat){
            if(empty($id_chat) || !is_numeric($id_chat))
                return -1;

            $chat = $this->find('first', array(
                'conditions'    => array('Chat.id' => $id_chat, 'Chat.consult_date_start !=' => null),
                'recursive'     => -1
            ));

            if(empty($chat))
                return false;
            else
                return true;
        }

        /**
         * Indique les chats en cours en général ou de l'utilisateur
         *
         * @param int $idUser
         * @return array|string
         */
        public function getChatOpen($idUser = 0){
            //Si pas d'utilisateur fourni, alors toutes les sessions ouvertes
            if($idUser === 0){
                $ids = $this->find('list',array(
                    'conditions' => array('date_start !=' => null, 'date_end' => null),
                    'recursive' => -1
                ));
            }else{
                $ids = $this->field('id', array(
                    'date_start !=' => null,
                    'date_end' => null,
                    'OR' => array(
                        array('from_id' => $idUser),
                        array('to_id' => $idUser)
                    )
                ));
            }

            return $ids;
        }

        public function closeChat($chat, $idUser, $causeCloseChat){
            if(empty($chat))
                return false;
			
			App::import('Controller', 'App');
            $app = new AppController;
			//App::import('Controller', 'Extranet');
            //$extractrl = new ExtranetController();
						
            $dateNow = date('Y-m-d H:i:s');
            //Le status de déconnection
            $this->ChatEvent->create();
            $this->ChatEvent->save(array(
                'user_id'   => $idUser,
                'chat_id'   => $chat['Chat']['id'],
                'status'    => 'Disconnecting',
                'writting'  => 0,
                'send'      => 0,
                'date_add'  => $dateNow
            ));
            //On ferme la session de chat
            $this->id = $chat['Chat']['id'];
            if($this->saveField('date_end', $dateNow)){
                //On enregistre la cause
                $this->saveField('closed_by', $causeCloseChat);
				
				//on sauve l etat du contenu
				if($causeCloseChat != 'client_timeout' && $causeCloseChat != 'agent_timeout'){
					App::import('Model', 'FiltreMessage');
					$filtremessage = new FiltreMessage();
					$etat = 1;
					$filtres = $filtremessage->find("all", array(
						'conditions' => array(
						)
					));
					$lastMsgs = $this->ChatMessage->find('all', array(
						'fields' => array('ChatMessage.id', 'ChatMessage.user_id', 'ChatMessage.content', 'ChatMessage.date_add'),
						'conditions' => array(
							'ChatMessage.chat_id' => $chat['Chat']['id'],
						),
						'recursive' => -1
					));
					foreach($filtres as $filtre){
						foreach($lastMsgs as $msg){
							if(substr_count(strtolower($msg['ChatMessage']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
								$etat = 0;
						}
					}
					//if(!$etat){
						//Les datas pour l'email
						//$datasEmail = array(
						//	'content' => 'Le Tchat ID '.$chat['Chat']['id'].' requiert check terme interdit.' ,
						//	'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						//);
						//Envoie de l'email
						//$extractrl->sendEmail('contact@talkappdev.com','Tchat terme interdit','default',$datasEmail);
					//}
					$this->saveField('etat', $etat);
				}
                //S'il a vraiment eu une consultation
                if(!empty($chat['Chat']['consult_date_start'])){
                    //Durée de la consult
                    $tmstmpStart = new DateTime($chat['Chat']['consult_date_start']);
                    $tmstmpStart = $tmstmpStart->getTimestamp();
                    $tmstmpEnd = new DateTime($dateNow);
                    $tmstmpEnd = $tmstmpEnd->getTimestamp();
                    $consultSecond = ($tmstmpEnd - $tmstmpStart);
                    //Nombre de crédits utilisés pour la consult
                    $credits = ceil($consultSecond/(int)Configure::read('Site.secondePourUnCredit'));
                    //On met à jour le crédit du customer
                    
                    $newCredit = $app->updateCredit($chat['Chat']['from_id'],$credits);
                    //MAJ du crédit raté
                    if($newCredit === false){
                        //On met le crédit à 0
                        $this->User->id = $chat['Chat']['from_id'];
                        $this->User->saveField('credit', 0);
                        $newCredit = 0;
                    }
                    //On met à jour la session chat
                    $this->updateAll(array('credit' => $credits, 'consult_date_end' => $this->value($dateNow)),array('Chat.id' => $chat['Chat']['id']));

                    //On save dans l'historique
                    App::import('Model', 'UserCreditLastHistory');
                    App::import('Model', 'UserCreditHistory');
                    $userCreditLastHistory = new UserCreditLastHistory();
                    $userCreditHistory = new UserCreditHistory();

                    $saveData = array(
                        'users_id'              => $chat['Chat']['from_id'],
                        'agent_id'              => $chat['Chat']['to_id'],
                        'agent_pseudo'          => $chat['Agent']['pseudo'],
                        'media'                 => 'chat',
                        'credits'               => $credits,
                        'seconds'               => $consultSecond,
                        'user_credits_before'   => $chat['User']['credit'],
                        'user_credits_after'    => $newCredit,
                        'date_start'            => $chat['Chat']['consult_date_start'],
                        'date_end'              => $dateNow,
						'sessionid'             => $chat['Chat']['id']
                    );
					
					
					if(!$userCreditLastHistory->duplicateLine($chat)){
						$userCreditLastHistory->create();
						$consult = $userCreditLastHistory->save($saveData);
	
						$saveData['user_id'] = $saveData['users_id'];
						unset($saveData['users_id']);
	
						//Save dans l'historique (archive)
						$saveData['is_new'] = 0;
						$lastComCheck = $userCreditHistory->find('first', array(
							'conditions'    => array('UserCreditHistory.user_id' => $saveData['user_id']),
							'recursive'     => -1
						));
						if(!$lastComCheck && !$this->User->field('is_come_back', array('id' => $chat['Chat']['from_id'])))$saveData['is_new'] = 1;
						$dbb_patch = new DATABASE_CONFIG();
						$dbb_connect = $dbb_patch->default;
						$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
						
						$saveData['type_pay'] = 'pre';
						$resultuser = $mysqli_connect->query("SELECT * from users WHERE id = '".$saveData['user_id']."'");
						$rowuser = $resultuser->fetch_array(MYSQLI_ASSOC);
						
						$saveData['domain_id'] = $rowuser['domain_id'];//CakeSession::read('Config.id_domain');
						
						$userCreditHistory->create();
						$userCreditHistory->save($saveData);
						$app->calcCAComm($userCreditHistory->id);
						
						//on save photos
						$folder_old = new Folder(Configure::read('Site.pathChatLiveAdmin').DS.$chat['Chat']['from_id'],true,0755);
						$folder = new Folder(Configure::read('Site.pathChatArchiveAdmin').DS.$chat['Chat']['id'],true,0755);
						if(is_dir($folder_old->path) && is_dir($folder->path)){
							$files = array_diff(scandir($folder_old->path), array('.','..'));
							foreach ($files as $file) {
								copy($folder_old->path.DS.$file, $folder->path.DS.$file);
							}
						}
					
						//On save l'historiques de messages
						/*App::import('Model', 'ChatHistory');
						$chatHistory = new ChatHistory();
						$chatHistory->saveHistoric($chat['Chat']['id']);*/
						
						//on sauvegarde data pour bonus agent
						
						
						//if($causeCloseChat != 'client_timeout' && $causeCloseChat != 'agent_timeout'){
						
							//App::import('Model', 'BonusAgent');
							//$BonusAgent = new BonusAgent();
							/*$bonus_agent = $BonusAgent->find('first', array(
										'conditions' => array('BonusAgent.id_agent' => $chat['Chat']['to_id'], 'annee' => date('Y'), 'mois' => date('m'), 'active' => 1),
										'order' => array('id'=> 'desc'),
										'recursive' => -1
									));*/
						$listing_utcdec = Configure::read('Site.utcDec');
						
							$date_bonus = new DateTime($chat['Chat']['consult_date_start'] );
						$date_bonus->modify('+'.$listing_utcdec[$date_bonus->format('md')].' hour');
							$annee_bonus = $date_bonus->format('Y');
							$mois_bonus = $date_bonus->format('m');
							$result = $mysqli_connect->query("SELECT * from bonus_agents WHERE id_agent = '".$chat['Chat']['to_id']."' and annee = '".$annee_bonus."' and mois = '".$mois_bonus."' and active = '1' order by id DESC");
							$row = $result->fetch_array(MYSQLI_ASSOC);
							
							$bonus_min_total = $consultSecond;
							/*if($bonus_agent['BonusAgent']['min_total']) {
								$bonus_min_total = $bonus_min_total + $bonus_agent['BonusAgent']['min_total'];
								$bonus_agent['BonusAgent']['active'] = 0;
								$bonus_agent['BonusAgent']['date_add'] = "'".$bonus_agent['BonusAgent']['date_add']."'";
								$bonus_agent['BonusAgent']['IP'] = "'".$bonus_agent['BonusAgent']['IP']."'";
								$BonusAgent->updateAll($bonus_agent['BonusAgent'],array('id' => $bonus_agent['BonusAgent']['id']));
							}*/
						    $ancienne_ligne = 0;
							if($row['min_total']) {
								$bonus_min_total = $bonus_min_total + $row['min_total'];
								$ancienne_ligne = $row['id'];
							}
							
							$id_bonus = 0;
							$palier = floor($bonus_min_total / 60);
							//App::import('Model', 'Bonus');
							//$BonusModel = new Bonus();
							/*$bonus = $BonusModel->find('all', array(
									'order' => array('id'=> 'asc'),
									'recursive' => -1
								));*/
							$r_bonus = $mysqli_connect->query("SELECT * from bonuses order by id ASC");
							while($b = $r_bonus->fetch_array(MYSQLI_ASSOC)){
								if($palier >= $b['bearing'])
									$id_bonus = $b['id'];
							}
							/*if(!empty($bonus)){
								foreach($bonus as $bobo){
									foreach($bobo as $b){
										if($palier >= $b['bearing'])
											$id_bonus = $b['id'];	
									}
								}
							}*/
							
							$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
							$bonusAgent = array();
							$bonusAgent['BonusAgent'] = array();
							$bonusAgent['BonusAgent']['id_agent'] = $chat['Chat']['to_id'];
							$bonusAgent['BonusAgent']['id_com'] = $consult["UserCreditLastHistory"]["user_credit_last_history"];
							$bonusAgent['BonusAgent']['id_bonus'] = $id_bonus;
							$bonusAgent['BonusAgent']['date_add'] = date('Y-m-d H:i:s');
							$bonusAgent['BonusAgent']['min_tchat'] = $consultSecond;
							$bonusAgent['BonusAgent']['min_tel'] = 0;
							$bonusAgent['BonusAgent']['IP'] = $ip_user;
							$bonusAgent['BonusAgent']['annee'] = $annee_bonus;
							$bonusAgent['BonusAgent']['mois'] = $mois_bonus;
							$bonusAgent['BonusAgent']['active'] = 1;
							$bonusAgent['BonusAgent']['min_total'] = $bonus_min_total;
									
							//$BonusAgent->create();
							//$BonusAgent->save($bonusAgent);
						    $result3 = $mysqli_connect->query("SELECT * from bonus_agents WHERE id_agent = '".$chat['Chat']['to_id']."' and date_add = '".date('Y-m-d H:i:s')."' ");
							$row3 = $result3->fetch_array(MYSQLI_ASSOC);
							if(!$row3['id']){
								$mysqli_connect->query("INSERT INTO bonus_agents (id_agent, id_bonus, id_com, min_tchat, min_tel, min_total, annee, mois, date_add, IP, active) VALUES ('".$chat['Chat']['to_id']."','".$id_bonus."','".$consult["UserCreditLastHistory"]["user_credit_last_history"]."','".$consultSecond."','0','".$bonus_min_total."','".$annee_bonus."','".$mois_bonus."','".date('Y-m-d H:i:s')."','".$ip_user."','1')");
							    $mysqli_connect->query("UPDATE bonus_agents SET active = 0 where id = '".$ancienne_ligne."'");
							}
						//}
						
						//update CostAgent
						$result_cost = $mysqli_connect->query("SELECT * from cost_agents WHERE id_agent = '".$chat['Chat']['to_id']."'");
						$row_cost = $result_cost->fetch_array(MYSQLI_ASSOC);
						if(!$row_cost){
							$mysqli_connect->query("insert into cost_agents(id_agent,id_cost,nb_minutes) values('".$chat['Chat']['to_id']."',1,0)");
							$result_cost = $mysqli_connect->query("SELECT * from cost_agents WHERE id_agent = '".$chat['Chat']['to_id']."'");
							$row_cost = $result_cost->fetch_array(MYSQLI_ASSOC);
						}
						$minutes = $row_cost['nb_minutes'] + ($consultSecond / 60);
						$mysqli_connect->query("UPDATE cost_agents SET nb_minutes = '{$minutes}' where id_agent = '".$chat['Chat']['to_id']."'");
						if($row_cost['id_cost'] <= 4)
							$result_cost2 = $mysqli_connect->query("SELECT id from costs where level <= '{$minutes}' and id < 4 order by id DESC");
						else
							$result_cost2 = $mysqli_connect->query("SELECT id from costs where level <= '{$minutes}' and id >= 5 and id < 9 order by id DESC");
							
						if(!empty($result_cost2)){
							$row_cost2 = $result_cost2->fetch_array(MYSQLI_ASSOC);
							if(isset($row_cost2[0])){
								$row_cost2['id'] = $row_cost2[0]['id'];
							}
							if($row_cost2['id']<4)
							$row_cost2['id'] = $row_cost2['id'] +1;

							$mysqli_connect->query("UPDATE cost_agents SET id_cost = '{$row_cost2['id']}' where id_agent = '".$chat['Chat']['to_id']."'");
						}
						
						//sponsorship
						App::import('Model', 'Sponsorship');
						$Sponsorship = new Sponsorship();
						$Sponsorship->Benefit($consult['UserCreditLastHistory']['user_credit_last_history']);
						
						$consults_nb = $this->User->field('consults_nb', array('id' => $chat['Chat']['to_id']));
						$consults_nb = $consults_nb + 1;
						$this->User->id = $chat['Chat']['to_id'];
                        $this->User->saveField('consults_nb', $consults_nb);
						
						
						//send email
						$consult_id = $consult["UserCreditLastHistory"]["user_credit_last_history"];
						
						$email = $this->User->field('email', array('id' => $chat['Chat']['from_id']));
						$prenom = $this->User->field('firstname', array('id' => $chat['Chat']['from_id']));
						$lang_id = $this->User->field('lang_id', array('id' => $chat['Chat']['from_id']));
						
						if($causeCloseChat != 'client_timeout' && $causeCloseChat != 'agent_timeout' && $consultSecond > 300 && !in_array($chat['Chat']['from_id'],Configure::read('Review.no_send_email'))){
							$app->sendCmsTemplateByMail(192, $lang_id, $email, array(
										'SITE_NAME'    => Configure::read('Site.name'),
										'AGENT_PSEUDO' => $chat['Agent']['pseudo'],
										'PRENOM' => $prenom,
										'COM_DUREE'    => (int)60,
										'DATE_COM'    => CakeTime::format($chat['Chat']['consult_date_start'], '%d-%m-%Y'),
										'REVIEW_LINK'  => Router::url(array('controller' => 'reviews', 'action' => 'reviews_post?u='.$chat['Chat']['from_id'].'&a='.$chat['Chat']['to_id'].'&c='.$consult_id),false)
							));
						}
					}
					
                }
                return array('return' => true, 'session' => $chat['Chat']['session_id'], 'info' => __('Déconnexion du chat'));
            }
            return array('return' => false, 'typeError' => 'update', 'value' => __('Echec lors de la fermeture du chat.'));
        }
    }

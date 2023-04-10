<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');
	App::import('Vendor', 'Noox/Tools');
    App::import('Controller', 'Alerts');

    class CronController extends AppController {
        public $uses = array('CountryLangPhone');

        public function beforeFilter()
        {
            $this->Auth->allow();
            parent::beforeFilter();
        }

        public $autoRender = false;
		
        public function horoscope_rotate()
        {
			exit;
            /* On récupère la dernière date de publication de l'horoscope sur le site */
            $this->loadModel('Horoscope');
            $last = $this->Horoscope->find("first",array(
                'order' => 'date_publication DESC',
                'limit' => 1
            ));


            if (empty($last)){
                $last_date_unix = strtotime(date("Y-m-d",time() - 86400)." 00:00:00");
            }else{
                $last_date_unix = strtotime(date("Y-m-d",strtotime($last['Horoscope']['date_publication']))." 00:00:00");
            }

            /* On genère 7 jours d'horoscope */
            for ($i=1; $i<8; $i++){
                $curr_date = date("Y-m-d H:i:s", $last_date_unix+ (86400 * $i));
                echo 'genere horoscope du '.$curr_date."\n";

                /* On récupère un horoscope dans le stock */
                    $horoscope = $this->getRandHoroscope();


                /* On prépare le tableau HoroscopeLang */
                    $horoscopeLang = array();
                    foreach ($horoscope AS $row){
                        if (isset($row['Horoscope_stock']['sign_id'])){
                            $horoscopeLang[] = array(
                                'sign_id' => $row['Horoscope_stock']['sign_id'],
                                'lang_id' => 1,
                                'content' => $row['Horoscope_stock']['texte']
                            );
							$horoscopeLang[] = array(
                                'sign_id' => $row['Horoscope_stock']['sign_id'],
                                'lang_id' => 8,
                                'content' => $row['Horoscope_stock']['texte']
                            );
							$horoscopeLang[] = array(
                                'sign_id' => $row['Horoscope_stock']['sign_id'],
                                'lang_id' => 10,
                                'content' => $row['Horoscope_stock']['texte']
                            );
							$horoscopeLang[] = array(
                                'sign_id' => $row['Horoscope_stock']['sign_id'],
                                'lang_id' => 11,
                                'content' => $row['Horoscope_stock']['texte']
                            );
							$horoscopeLang[] = array(
                                'sign_id' => $row['Horoscope_stock']['sign_id'],
                                'lang_id' => 12,
                                'content' => $row['Horoscope_stock']['texte']
                            );
                        }
                    }



                $this->Horoscope->create();
                $add = array(
                    'Horoscope' => array(
                        'date_publication' => $curr_date
                    ),
                    'HoroscopeLang' => $horoscopeLang
                );

                $this->Horoscope->saveAll($add);
            }
        }
        private function getRandHoroscope()
        {
            $this->loadModel('Horoscope_stock');

            /* On récupère une date d'horoscope dans le stock */
            $row = $this->Horoscope_stock->find("first", array(
                'fields' => array('date','id'),
                'order' => 'isnull(last_used_date) desc, last_used_date asc, rand()',
                'limit'  => 1
            ));
            if (empty($row) || !isset($row['Horoscope_stock']))return false;
            $date = $row['Horoscope_stock']['date'];

            /* On update la date d'utilisation de cet horoscope dans le stock */
            $this->Horoscope_stock->updateAll(array(
                'last_used_date'    =>  "'".date("Y-m-d")."'"
            ), array(
                'date' => $date
            ));

            /* On récupère l'horoscope complet de la date trouvée */
            $horoscope = $this->Horoscope_stock->find("all", array(
                'conditions' => array(
                    'date' => $date
                )
            ));

            /* On récupère les signes */
            $this->loadModel('Horoscope_signs');
            $rows = $this->Horoscope_signs->find("all", array(
                'fields' => array('sign_id','link_rewrite'),
                'conditions' => array(
                    'lang_id' => 1
                )
            ));
            $signes = array();
            foreach ($rows AS $row)
                $signes[$row['Horoscope_signs']['link_rewrite']] = $row['Horoscope_signs']['sign_id'];

            /* On ajoute les id signes dans l'horoscope issu du stock */
            foreach ($horoscope AS $k => $v){
                if (isset($signes[$v['Horoscope_stock']['signe']]))
                    $horoscope[$k]['Horoscope_stock']['sign_id'] = $signes[$v['Horoscope_stock']['signe']];
                $horoscope[$k]['Horoscope_stock']['texte'] = str_replace(array('<h1>','</h1>'), array('<h2>','</h2>'), $v['Horoscope_stock']['texte']);
            }

            return $horoscope;
        }
        public function clearCreditLastHistory(){
			exit;
            $this->loadModel('UserCreditLastHistory');

            //L'historique de 15 jours
            $startBegin = date('Y-m-d 00:00:00', strtotime('-'. configure::read('Cron.clearCreditHistory') .' days'));
            $startEnd = date('Y-m-d 23:59:59', strtotime('-'. configure::read('Cron.clearCreditHistory') .' days'));

            //L'historique de 15 jours
            $rows = $this->UserCreditLastHistory->find('list', array(
                'fields'    => array('UserCreditLastHistory.user_credit_last_history'),
                'conditions' => array('UserCreditLastHistory.date_start >=' => $startBegin, 'UserCreditLastHistory.date_start <=' => $startEnd, 'UserCreditLastHistory.date_end !=' => null),
                'recursive' => -1
            ));

            //On le supprime
            $this->UserCreditLastHistory->deleteAll(array('UserCreditLastHistory.user_credit_last_history' => $rows), false);
        }

        public function saveMessageHistory(){
			$this->loadModel('Message');
            $this->loadModel('MessageHistory');

            $dateNow = date('Y-m-d 23:59:59', strtotime('-'. configure::read('Cron.saveMessageHistory') .' days'));
            $yesterday = date('Y-m-d 00:00:00', strtotime('-'. configure::read('Cron.saveMessageHistory') .' days'));

            //Tout les messages admin read et < delai configuré
            $messages = $this->Message->find('all', array(
                'conditions' => array('Message.date_add <' => $yesterday, 'Message.to_id ' => 1, 'Message.admin_read_flag ' => 1),
                'recursive' => -1
            ));

            if(!empty($messages)){
                $saveData['MessageHistory'] = array();
                foreach($messages as $key => $message){

                    //$saveData['MessageHistory'][] = $messages[$key]['Message'];
					$this->MessageHistory->create();
                	if($this->MessageHistory->save($messages[$key]['Message'])){
						$this->Message->delete($messages[$key]['Message']['id'], false);
					}
					
					$messages_child = $this->Message->find('all', array(
						'conditions' => array('Message.parent_id ' => $messages[$key]['Message']['id']),
						'recursive' => -1
					));
					if(!empty($messages_child)){
						foreach($messages_child as $keychild => $messagechild){
							//$saveData['MessageHistory'][] = $messages_child[$key]['Message'];
							$this->MessageHistory->create();
							if($this->MessageHistory->save($messages_child[$keychild]['Message'])){
								$this->Message->delete($messages_child[$keychild]['Message']['id'], false);
							}
						}
					}
                }
                //$this->MessageHistory->create();
                //$this->MessageHistory->saveMany($saveData['MessageHistory']);
            }
			
			//Tout les messages invité read et < delai configuré
            $messages = $this->Message->find('all', array(
                'conditions' => array('Message.date_add <' => $yesterday, 'Message.to_id ' => 2, 'Message.admin_read_flag ' => 1),
                'recursive' => -1
            ));

            if(!empty($messages)){
                $saveData['MessageHistory'] = array();
                foreach($messages as $key => $message){

					$this->MessageHistory->create();
                	if($this->MessageHistory->save($messages[$key]['Message'])){
						$this->Message->delete($messages[$key]['Message']['id'], false);
					}
					
					$messages_child = $this->Message->find('all', array(
						'conditions' => array('Message.parent_id ' => $messages[$key]['Message']['id']),
						'recursive' => -1
					));
					if(!empty($messages_child)){
						foreach($messages_child as $keychild => $messagechild){
							//$saveData['MessageHistory'][] = $messages_child[$key]['Message'];
							$this->MessageHistory->create();
							if($this->MessageHistory->save($messages_child[$keychild]['Message'])){
								$this->Message->delete($messages_child[$keychild]['Message']['id'], false);
							}
						}
					}
                }
            }
        }

        public function clearAppointments(){
            $this->loadModel('CustomerAppointment');
            $this->loadModel('User');

            $dateStart = date('d-m-Y', strtotime('- 360 days'));
            $dateEnd = date('d-m-Y', strtotime('-'. configure::read('Cron.clearAppointment') .' days'));

            $dateStart = Tools::explodeDate($dateStart);
            $dateEnd = Tools::explodeDate($dateEnd);

            //Les id des agents
            $idAgents = $this->User->find('list', array(
                'fields'        => array('id'),
                'conditions'    => array('User.role' => 'agent'),
                'recursive'     => -1
            ));

            //Les conditions pour les rdvs
            $conditions = $this->CustomerAppointment->getConditions($idAgents, $dateStart, $dateEnd);

            $this->CustomerAppointment->deleteAll($conditions, false);
        }

        public function sendAppointments(){

			exit;
            $this->loadModel('User');
            $this->loadModel('CustomerAppointment');
            $this->loadModel('Lang');

            //Les agents
            $idAgents = $this->User->find('list', array(
                'fields'        => array('email', 'id'),
                'conditions'    => array('User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1, 'User.valid' => 1),
                'recursive'     => -1
            ));

            $dateStart = date('d-m-Y');
            $dateEnd = date('d-m-Y', strtotime('+'. Configure::read('Site.limitPlanning') .' days'));

            $dateStart = Tools::explodeDate($dateStart);
            $dateEnd = Tools::explodeDate($dateEnd);

            //Les conditions pour les rdvs
            $conditions = $this->CustomerAppointment->getConditions($idAgents, $dateStart, $dateEnd);

            //On récupère les rdv
            $appointments = $this->CustomerAppointment->find('all', array(
                'conditions'    => $conditions,
                'order' => array('CustomerAppointment.A ASC', 'CustomerAppointment.M ASC', 'CustomerAppointment.J ASC', 'CustomerAppointment.H ASC', 'CustomerAppointment.Min ASC'),
                'recursive'     => 0
            ));


            //On inverse le tableau
            $emailAgents = array_flip($idAgents);

            $mailData = array();
			
            if(!empty($appointments)){
                foreach($appointments as $appointment){
                    $this->User->id = $appointment['Agent']['id'];
                    $user_lang_id = $this->User->field('lang_id');
                    $this->Lang->id = $user_lang_id;
                    $locale = $this->Lang->field('lc_time');
                    if (empty($locale))$locale = 'fr_FR.utf8';
                    setlocale(LC_ALL, $locale);

                    $dateAppoint = $appointment['CustomerAppointment']['A'].'-'.$appointment['CustomerAppointment']['M'].'-'.
                        $appointment['CustomerAppointment']['J'].' '.
                        str_pad($appointment['CustomerAppointment']['H'], 2, '0', STR_PAD_LEFT).':'.
                        str_pad($appointment['CustomerAppointment']['Min'], 2, '0', STR_PAD_LEFT).':00';
					
					
					//check si on envoi today
					$send = false;
					if(($appointment['CustomerAppointment']['M'] != date('m') || $appointment['CustomerAppointment']['J'] != date('d') || $appointment['CustomerAppointment']['A'] != date('Y')) && date('H') == '06'){
						$send = true;
					}
					if(($appointment['CustomerAppointment']['M'] == date('m') && $appointment['CustomerAppointment']['J'] == date('d') && $appointment['CustomerAppointment']['A'] == date('Y')) && ($appointment['CustomerAppointment']['H'] - 2 ) == (date('H'))){
						$send = true;
					}
					if($send){
						if(isset($mailData[$appointment['CustomerAppointment']['agent_id']]))
							$mailData[$appointment['CustomerAppointment']['agent_id']]['appointments'][] = array('date' => CakeTime::format($dateAppoint, '%d %B %Hh%M'));
						else{
							$mailData[$appointment['CustomerAppointment']['agent_id']]['pseudo'] = $appointment['Agent']['pseudo'];
							$mailData[$appointment['CustomerAppointment']['agent_id']]['user_id'] = $appointment['CustomerAppointment']['agent_id'];
							$mailData[$appointment['CustomerAppointment']['agent_id']]['appointments'] = array();
							$mailData[$appointment['CustomerAppointment']['agent_id']]['appointments'][] = array('date' => CakeTime::format($dateAppoint, '%d %B %Hh%M'));
						}
					}
                }
            }

            //On envoie l'email pour chaque agent qui a des rdvs
            foreach($mailData as $id => $row){
                $rdv = "<table>";
                foreach($row['appointments'] as $row2)
                    $rdv.= "<tr><td>".$row2['date']."</td></tr>";
                $rdv.= '</table>';

                /* langue agent */
                    $this->User->id = $row['user_id'];
                    $this->recursive = -1;
                    $id_lang = (int)$this->User->field('User.lang_id');

                //$this->sendEmail($emailAgents[$id], 'Vos rendez-vous', 'agent_appointments', array('param' => $row));
                $this->sendCmsTemplateByMail(152, $id_lang, $emailAgents[$id], array(
                    'PARAM_PSEUDO'      =>  $row['pseudo'],
                    'PARAM_RENDEZVOUS'  =>  $rdv
                ));
            }
        }

        public function clearPhoneAlerts(){
            $this->loadModel('Alert');
            $this->loadModel('AlertHistory');

            //Date d'hier
            $startBegin = date('Y-m-d 00:00:00', strtotime('-1 days'));
            $startEnd = date('Y-m-d 23:59:59', strtotime('-1 days'));

            //On récupère les id des alertesz pour lesquelles au moins une alerte a été généré hier
            $alertsHistory = $this->AlertHistory->find('list', array(
                'fields'        => array('AlertHistory.alerts_id', 'AlertHistory.alerts_id'),
                'conditions'    => array('AlertHistory.date_add >=' => $startBegin, 'AlertHistory.date_add <=' => $startEnd)
            ));

            //On efface le numéro pour les alertes en questions
            $this->Alert->updateAll(array('Alert.phone_number' => null), array('Alert.id' => $alertsHistory));
        }

        public function clearAlerts(){
			return;
            $this->loadModel('Alert');

            $startDate = date('Y-m-d 00:00:00', strtotime('-'.Configure::read('Site.alerts.days').' days'));
            //On récupère les alertes qu'il faut supprimer
            $idAlerts = $this->Alert->find('list', array(
                'conditions'    => array('Alert.date_add >' => $startDate),
                'recursive'     => -1
            ));

            //On supprime l'historique d envoi des emails
            $this->Alert->AlertHistory->deleteAll(array('AlertHistory.alerts_id' => $idAlerts, 'AlertHistory.alert_type' => 'email'), false);

            //On supprime les alerts qui ont une date inférieure
            //$this->Alert->deleteAll(array('Alert.date_add <' => $startDate), false);
        }
		
		public function sendAlerts(){
			
			$alert_type = array(0 => 'email', 1 => 'sms');
			
			$this->loadModel('Alert');
			$this->loadModel('User');
			$this->loadModel('AlertHistory');
			
			$conditions = array(
				'Alert.send'       => 2,
            );
			
			
			
			//Init le tableau des alertes
        $alertCustomer = array(
            'Agent'     => array(),
            'Customer'  => array()
        );
          $alerts = $this->Alert->find("all", array(
			  'fields'     => array('Domain.*,Lang.*,Alert.*, Agent.pseudo, Agent.agent_number, Agent.consult_chat, Agent.consult_email, Agent.consult_phone',
                                          '(SELECT count(*) FROM alert_histories WHERE alerts_id = Alert.id AND alert_type = "sms" AND DATE(date_add) = DATE(NOW())) AS alerts_today_sms',
                                          '(SELECT count(*) FROM alert_histories WHERE alerts_id = Alert.id AND alert_type = "email" AND DATE(date_add) = DATE(NOW())) AS alerts_today_email',
                                          '(SELECT date_add FROM alert_histories WHERE alerts_id = Alert.id AND DATE(date_add) = DATE(NOW()) ORDER BY date_add DESC LIMIT 1) AS last_alert_date'
                    ),
                    'conditions' => $conditions,
			  'joins'      => array(
                        array(
                            'table' => 'users',
                            'alias' => 'Agent',
                            'type'  => 'LEFT',
                            'conditions' => array(
                                'Agent.id = Alert.agent_id'
                            )
                        ),
                        array(
                            'table' => 'langs',
                            'alias' => 'Lang',
                            'type'  => 'LEFT',
                            'conditions' => array(
                                'Lang.id_lang = Alert.lang_id'
                            )
                        ),
					  array(
                            'table' => 'domains',
                            'alias' => 'Domain',
                            'type'  => 'LEFT',
                            'conditions' => array(
                                'Domain.id = 19'
                            )
                        )
                    ),
                    'group'      => array('Alert.id'),
                    'recursive' => -1
                )
            );
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			 $dateMin = date('Y-m-d H:i:s', time() - Configure::read('Site.alerts.delay_between_alerts_second'));
		foreach ($alerts as $alert){
			
			$history_count = 0;
			
			
			 $agent = $this->User->find("first", array(
                    'conditions' => array(
                        'id' => $alert['Alert']['agent_id'],
                        'role'         => 'agent',
                        'active'       => 1,
                        'deleted'      => 0),
                    'recursive' => -1
                )
            );
			if(array_key_exists('User',$agent)){
			$alertCustomer['Agent'] = array(
                'id'            => $agent['User']['id'],
                'pseudo'        => $agent['User']['pseudo'],
                'agent_number'  => $agent['User']['agent_number'],
                'consult_email' => $agent['User']['consult_email'],
                'consult_phone' => $agent['User']['consult_phone'],
                'consult_chat' => $agent['User']['consult_chat'],
            );
			
							
			$history_c =	$mysqli->query("select * from alert_histories where alerts_id = ".$alert['Alert']['id']);	
			$history_count = $history_c->fetch_array(MYSQLI_ASSOC);
            if(
                ($alert['0']['last_alert_date'] < $dateMin)
                &&
                ($agent['User']['consult_'.$alert['Alert']['media']] == 1)
				&&
				!$history_count
            ){
                //Si l'email n'est pas renseigné, mais que le customer est un client
                if(trim($alert['Alert']['email']) == '')
                    $alert['Alert']['email'] = '_'.$alert['Alert']['users_id'];

                //SMS
                if($alert['0']['alerts_today_sms'] < $alert['Alert']['alert_by_day']){
                    //Si l'email est déjà renseigné dans le tableau
                    if(isset($alertCustomer['Customer'][$alert['Alert']['email']])){
                        $alertCustomer['Customer'][$alert['Alert']['email']]['Alert']['media'][] = array(
                            'id'    => $alert['Alert']['id'],
                            'name'  => $alert['Alert']['media'],
                            'type'  => $alert_type[1]
                        );
                    }else   //Sinon on ajoute le customer dans le tableau
                        $alertCustomer['Customer'][$alert['Alert']['email']] = array(
                            'Domain'    => $alert['Domain'],
                            'Lang'      => $alert['Lang'],
                            'Alert'     => array(
                                'phone_number'  => $alert['Alert']['phone_number'],
                                'media'         => array(
                                    0 => array(
                                        'id'        => $alert['Alert']['id'],
                                        'name'      => $alert['Alert']['media'],
                                        'type'      => $alert_type[1]
                                    )
                                )
                            )
                        );
                }

                //Email
                if($alert['0']['alerts_today_email'] < $alert['Alert']['alert_by_day']){
                    //Si l'email est déjà renseigné dans le tableau
                    if(isset($alertCustomer['Customer'][$alert['Alert']['email']])){
                        $alertCustomer['Customer'][$alert['Alert']['email']]['Alert']['media'][] = array(
                            'id'    => $alert['Alert']['id'],
                            'name'  => $alert['Alert']['media'],
                            'type'  => $alert_type[0]
                        );
                    }else   //Sinon on ajoute le customer dans le tableau
                        $alertCustomer['Customer'][$alert['Alert']['email']] = array(
                            'Domain'    => $alert['Domain'],
                            'Lang'      => $alert['Lang'],
                            'Alert'     => array(
                                'phone_number'  => $alert['Alert']['phone_number'],
                                'media'         => array(
                                    0 => array(
                                        'id'        => $alert['Alert']['id'],
                                        'name'      => $alert['Alert']['media'],
                                        'type'      => $alert_type[0]
                                    )
                                )
                            )
                        );

					}
				}

			}
			
			App::import('Controller', 'Alerts');
            $alertsctrl = new AlertsController();
			
		   $alertsctrl->sendSmsAlertForAgentAvailable($alertCustomer,true);
           $alertsctrl->sendEmailAlertForAgentAvailable($alertCustomer,true);
		   
		}
			$mysqli->close();
        }

        public function closeChat(){
            $this->loadModel('Chat');
			$this->loadModel('ChatMessage');
            $this->loadModel('User');
            $this->loadModel('UserStateHistory');
			$this->loadModel('BonusAgent');
			$this->loadModel('CostAgent');

            $dateNow = date('Y-m-d H:i:s');

            //Liste des chats en cours
            $chatsOpen = $this->Chat->getChatOpen();

            //Pour chaque chat ouvert, on récupère la derniere activité de chaque membre. (chat_last_activity)
            foreach($chatsOpen as $chat){
                //Pour savoir qui est inactif
                $userInactif = false;
                $agentInactif = false;

                $chatData = $this->Chat->find('first', array(
                    'fields'        => array('Chat.*','User.id', 'User.chat_last_activity', 'User.date_last_activity', 'User.credit', 'Agent.id', 'Agent.chat_last_activity','Agent.date_last_activity', 'Agent.pseudo', 'Agent.agent_number'),
                    'conditions'    => array('Chat.id' => $chat),
                    'recursive'     => 0
                ));
				
				$lastmessageClient = $this->Chat->ChatMessage->find('first', array(
                        'fields' => array('date_add'),
                        'conditions' => array('chat_id' => $chatData['Chat']['id'], 'user_id' =>$chatData['Chat']['from_id']),
                        'order' => 'id desc',
                        'recursive' => -1
                    ));
				
				$lastmessageAgent = $this->Chat->ChatMessage->find('first', array(
                        'fields' => array('date_add'),
                        'conditions' => array('chat_id' => $chatData['Chat']['id'], 'user_id' =>$chatData['Chat']['to_id']),
                        'order' => 'id desc',
                        'recursive' => -1
                    ));
				
				$cause = '';
                //Client deco
                if(Tools::diffInSec($chatData['User']['date_last_activity'], $dateNow) >= 180){//Configure::read('Chat.maxTimeCloseChat')
					//$userInactif = true;
					$cause = 'client_timeout';
				}
                    
                //Agent deco
                if(Tools::diffInSec($chatData['Agent']['date_last_activity'], $dateNow) >= 180){
					$agentInactif = true;
					$cause = 'agent_timeout';
				}
                
				$userinactivity = false;
				if(($lastmessageClient && Tools::diffInSec($lastmessageClient['ChatMessage']['date_add'], $dateNow) >= Configure::read('Chat.maxDelayInactif')) ){
					$userinactivity = true;
				}
				$agentinactivity = false;
				if(($lastmessageAgent && Tools::diffInSec($lastmessageAgent['ChatMessage']['date_add'], $dateNow) >= Configure::read('Chat.maxDelayInactif'))){
					$agentinactivity = true;
				}
				
				//Client inactif
                if($lastmessageClient && $userinactivity && Tools::diffInSec($lastmessageClient['ChatMessage']['date_add'], $dateNow) >= Configure::read('Chat.maxDelayInactif')){
					$userInactif = true;
					$cause = 'client_inactivity';
				}
                    
                //Agent inactif
                if($lastmessageAgent && $agentinactivity && Tools::diffInSec($lastmessageAgent['ChatMessage']['date_add'], $dateNow) >= Configure::read('Chat.maxDelayInactif')){
					$agentInactif = true;
					$cause = 'agent_inactivity';
				}
                    

				//Si un des users n'a plus son navigateur ouvert, on ferme le chat
                if($userInactif || $agentInactif){
                    //On ferme le chat
                    if($this->Chat->closeChat($chatData, ($userInactif ?$chatData['User']['id']:$chatData['Agent']['id']), $cause) !== false){
                        //L'agent est disponible
                        $this->User->id = $chatData['Agent']['id'];
                        $this->User->saveField('agent_status', 'available');
                        $this->UserStateHistory->create();
                        $this->UserStateHistory->save(array(
                            'user_id'   => $chatData['Agent']['id'],
                            'state'     => 'available'
                        ));

                        //On alerte les clients qui l'ont demandé si le statut est available
                        App::import('Controller', 'Alerts');
                        $alerts = new AlertsController();
                        $alerts->alertUsersForUserAvailability($chatData['Agent']['agent_number'], 'phone');
						
						
                    }
                }
            }
        }
		
		public function autoConnectAgent(){
			exit;
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			$dateNow = date('Y-m-d H:i:s');
			
			$need_agent = 3;
			
			//check nb agent busy
			$result = $mysqli->query("SELECT count(id) as nb from users where agent_status= 'busy'");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			if($row['nb'])$need_agent = $need_agent - $row['nb'];
			if($need_agent < 0)$need_agent = 0;
			$index_agent = 0;
			$result = $mysqli->query("SELECT * from user_connected order by last_connexion ASC");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				
				$result_co = $mysqli->query("SELECT agent_status, nb_consult_ajoute from users where id= ".$row['agent_id']);
				if($result_co){
					$row_co = $result_co->fetch_array(MYSQLI_ASSOC);

					$result_time = $mysqli->query("SELECT TIMESTAMPDIFF(SECOND, date_add, '{$dateNow}') as duration FROM user_state_history WHERE user_id = {$row['agent_id']} ORDER BY date_add DESC LIMIT 1");
					$row_time = $result_time->fetch_array(MYSQLI_ASSOC);

					$nbconsult = $row_co['nb_consult_ajoute']  + 1;

					//sleep(rand(1, 10));
					$dateNew = date('Y-m-d H:i:s');
					if($row_co['agent_status'] == 'busy' && ($row_time['duration'] >= $row['call_during'] || $row_time['duration'] == NULL )){
						$mysqli->query("UPDATE users set agent_status = 'unavailable'  where id= ".$row['agent_id']);//, active = '1'
						$mysqli->query("UPDATE user_connected set last_connexion = NOW()  where agent_id= ".$row['agent_id']);
						$mysqli->query("INSERT INTO `user_state_history` (`user_id`, `state`, `date_add`) VALUES ('{$row['agent_id']}', 'unavailable', '{$dateNew}')");
					}
					if($row_co['agent_status'] != 'busy' && ($row_time['duration'] >= $row['time_laps'] || $row_time['duration'] == NULL || $need_agent > 0) && $index_agent < $need_agent){
						$mysqli->query("UPDATE users set agent_status = 'busy'  where id= ".$row['agent_id']);//, active = '1'
						$mysqli->query("INSERT INTO `user_state_history` (`user_id`, `state`, `date_add`) VALUES ('{$row['agent_id']}', 'busy', '{$dateNew}')");
						$mysqli->query("UPDATE users set nb_consult_ajoute = '{$nbconsult}'  where id= ".$row['agent_id']);
						$need_agent --;
						$index_agent ++;
					}
				}
				
			}
			$mysqli->close();
		}
		
		public function autoSortExpert(){
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			$dateNow = date('Y-m-d H:i:s');
					
			$tchat_second = Configure::read('Site.chat_dec');		
			
			//mettre tous le monde a zero
			$mysqli->query("UPDATE users set list_pos = '9999' where role = 'agent'");
			$n_rand = 0;	
			$n_index = 1;
			
			//mettre tous ceux qui ont le phone + tchat + email dispo en classé
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email = 1 and consult_phone = 1 AND (consult_chat = 1  AND    ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60)");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$n_rand = $n_rand + $row['nb'];

			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable' and consult_email = 1 and consult_phone = 1 and (consult_chat = 1  AND ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60) order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;
			}
			
			//mettre tous ceux qui ont le phone + tchat  dispo en classé
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and consult_phone = 1 AND (consult_chat = 1  AND    ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60)");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$n_rand = $n_rand + $row['nb'];

			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and consult_phone = 1 and (consult_chat = 1  AND ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60) order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;
			}
			
			//mettre tous ceux qui ont le phone ou tchat + dispo en classé	
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email = 1 and (consult_phone = 1 OR (consult_chat = 1  AND    ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60))");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable' and consult_email = 1 and (consult_phone = 1 OR (consult_chat = 1  AND ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60)) order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;	
			}
			
			//mettre tous ceux qui ont le phone ou tchat + dispo en classé	
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and (consult_phone = 1 OR (consult_chat = 1  AND    ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60))");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and (consult_phone = 1 OR (consult_chat = 1  AND ((UNIX_TIMESTAMP(now())-".$tchat_second.") - (unix_timestamp(date_last_activity))) <= 60)) order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;	
			}
			
			
			
			//mettre tous ceux qui ont le phone  dispo en classé	
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and consult_phone = 1  AND consult_chat <  1");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and consult_phone = 1  AND consult_chat <  1 order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;	
			}
			
			//mettre tous ceux qui ont tchat + dispo en classé 
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_email < 1 and consult_phone < 1 and consult_chat = 1");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable'  and consult_email < 1 and consult_phone < 1 and consult_chat = 1  order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and  id= ".$row['id']);
				}
				$n_index = $n_rand;
			}
			
			//mettre juste email + dispo en classé
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status != 'unavailable' and consult_phone < 1 and consult_chat < 1 and consult_email = 1");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status != 'unavailable'  and consult_phone < 1 and consult_chat < 1 and consult_email = 1 order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and  id= ".$row['id']);
				}
				$n_index = $n_rand;
			}
			
			//mettre indispo en classé
			$result = $mysqli->query("SELECT count(*) as nb from users where role = 'agent' and agent_status = 'unavailable'");		
			$row = $result->fetch_array(MYSQLI_ASSOC);	
			$n_rand = $n_rand + $row['nb'];
			if($row['nb']){
				$list_v=array();
				for($x=$n_index;$x<=$n_rand;$x++){
					array_push($list_v,$x);	
				}
				$result = $mysqli->query("SELECT * from users where role = 'agent' and agent_status = 'unavailable'  order by id");
				while($row = $result->fetch_array(MYSQLI_ASSOC)){
					$key = mt_rand(0, count($list_v) - 1);
					$pos = $list_v[$key];
					unset($list_v[$key]);
					$list_v=array_values($list_v);
					$mysqli->query("UPDATE users set list_pos = '{$pos}' where list_pos = '9999' and id= ".$row['id']);
				}
				$n_index = $n_rand;
			}
			$mysqli->close();
		}
		
		public function orderExpert(){

			set_time_limit ( 0 );
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

			App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();
			App::import('Controller', 'Extranet');
        	$extractrl = new ExtranetController();
			
			
			require(APP.'Lib/stripe/init.php');
			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
			
			$listing_utcdec = Configure::read('Site.utcDec');
			
			$list_experts = array();
			$cut = explode('-',date('Y-m-d') );
			$mois_comp = $cut[1];
			/*if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/
			
			$date = date('Y-m-d 00:00:00', strtotime('-1 day'));
			$date2 = date('Y-m-d 23:59:59', strtotime('-1 day'));
			

			$dx = new DateTime($date);
			$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
			$dx2 = new DateTime($date2);
			$dx2->modify('- '.$listing_utcdec[$dx2->format('md')].' hour');
			
			$datemin = $dx->format('Y-m-d H:i:s');
			$datemax = $dx2->format('Y-m-d H:i:s');
			
			$list_cost = array();
			
			$result = $mysqli->query("SELECT * from costs order by id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$list_cost[$row['id']] = $row['cost'] / 60;
			}
			
			//check comm missing
			$result = $mysqli->query("SELECT * from user_credit_last_histories where date_start >= '{$datemin}' and date_start <= '{$datemax}' order by user_credit_last_history ");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$result2 = $mysqli->query("SELECT * from user_credit_history where media = '{$row['media']}' and sessionid='{$row['sessionid']}' and date_start='{$row['date_start']}' ");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if(!$row2){
					$type_pay = 'pre';
					$domainid = '';
					if($row['users_id'] == 286 || $row['users_id'] == 3630 || $row['users_id'] == 3631 || $row['users_id'] == 3632 || $row['users_id'] == 3633 || $row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 ){
						$type_pay = 'aud';
						switch ($row['users_id']) {
							case '286':
								$domainid = '19';
								break;
							case '3630':
								$domainid = '13';
								break;
							case '3631':
								$domainid = '11';
								break;
							case '3632':
								$domainid = '11';
								break;
							case '3633':
								$domainid = '22';
								break;
							case '3634':
								$domainid = '29';
								break;
							case '3635':
								$domainid = '29';
								break;
							case '3636':
								$domainid = '29';
								break;
							case '3637':
								$domainid = '29';
								break;
							case '3638':
								$domainid = '29';
								break;
						}
					}else{
						$domainid = $row['domain_id'];
					}


					 $mysqli->query("INSERT INTO user_credit_history (user_id, agent_id, agent_pseudo, media, phone_number, called_number, sessionid, credits, seconds, user_credits_before, user_credits_after, date_start , date_end,type_pay, domain_id ) VALUES ('".$row['users_id']."','".$row['agent_id']."','".$row['agent_pseudo']."','".$row['media']."','".$row['phone_number']."','".$row['called_number']."','".$row['sessionid']."','".$row['credits']."','".$row['seconds']."','".$row['user_credits_before']."','".$row['user_credits_after']."','".$row['date_start']."','".$row['date_end']."','".$type_pay."','".$domainid."') ");
				}
			}
			
			$html_email = '';
			$result = $mysqli->query("SELECT C.agent_id,C.user_id, C.user_credit_history,C.media,C.sessionid,C.is_factured, C.seconds,C.credits,C.ca,C.ca_euros,C.ca_currency, C.ca_ids,C.is_mobile, C.expert_number, U.order_cat, U.mail_price, U.stripe_account, C.type_pay, C.domain_id,U2.payment_opposed,U2.parent_account_opposed from user_credit_history C, users U, users U2 WHERE C.agent_id = U.id and C.user_id = U2.id and C.date_start >= '{$datemin}' and C.date_start <= '{$datemax}'");

			
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$agent_id = $row['agent_id'];
				$customer_id = $row['user_id'];
				if(!in_array($agent_id,$list_experts))
					array_push($list_experts,$agent_id);
				$user_credit_history = $row['user_credit_history'];
				$seconds = $row['seconds'];
				$order_cat = $row['order_cat'];
				$mail_price = $row['mail_price'];
				$media = $row['media'];
				$is_factured = $row['is_factured'];
				$is_payment_oppose = false;
				if($row['payment_opposed'])$is_payment_oppose = true;
				if($row['parent_account_opposed'])$is_payment_oppose = true;
				
				if($order_cat){
					$remuneration_time = $list_cost[$order_cat];
					if($row['is_mobile']){
						$rem_surcost = 0.10 / 60;
						$result_costphone = $mysqli->query("SELECT * from cost_phones order by id");
						while($row_costphone = $result_costphone->fetch_array(MYSQLI_ASSOC)){
							if(substr($row['expert_number'],0,strlen($row_costphone['indicatif'])) == $row_costphone['indicatif'])
								$rem_surcost = $row_costphone['cost'] / 60;
						}
						
						$remuneration_time = $remuneration_time - $rem_surcost;
					}


					$price = 0;
					if($is_factured){
						switch ($media) {
							case 'phone':
								$price = $seconds * $remuneration_time;
								break;
							case 'chat':
								$price = $seconds * $remuneration_time;
								break;
							case 'email':
								$price = $mail_price;
								break;
						}
					}
          
          //calculate CA if empty
          if($is_factured && !$row['ca']){
            if($row['type_pay'] == 'pre'){
              $result3 = $mysqli->query("SELECT * from orders where user_id = '".$row['user_id']."'  and valid = 1 and product_price > 0 order by id desc limit 1");
              $row3 = $result3->fetch_array(MYSQLI_ASSOC);
              if($row3['product_credits']){
                $pricing = $row3['total'] / $row3['product_credits'];
                $ca = $pricing * $row['credits']; 
                $ca_currency = $row3['currency'];
                $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = '{$user_credit_history}'");
                $row['ca'] = $ca;
                $row['ca_currency'] = $ca_currency;
              }
            }
            if($row['type_pay'] == 'aud'){
					     $ca_currency = '€';
              $the_country_id = 1;
              switch ($row['domain_id']) {
                  case '19':
                    $the_country_id = 1;
                    $ca_currency = '€';
                    break;
                  case '13':
                    $the_country_id = 3;
                    $ca_currency = 'CHF';
                    break;
                  case '11':
                    $the_country_id = 4;
                    $ca_currency = '€';
                    break;
                  case '22':
                    $the_country_id = 5;
                    $ca_currency = '€';
                    break;
                  case '29':
                    $the_country_id = 13;
                    $ca_currency = '$';
                    break;
                }
              $this->loadModel('CountryLangPhone');
              $phoneCountry = $this->CountryLangPhone->find('first', array(
                'fields'        => array('CountryLangPhone.surtaxed_minute_cost'),
                'conditions'    => array('CountryLangPhone.country_id' => $the_country_id, 'CountryLangPhone.lang_id' => 1),
                'recursive'     => 1
              ));
              $ca = 0;
              if($phoneCountry){
                $cost_second = $phoneCountry['CountryLangPhone']['surtaxed_minute_cost'] / 60;
                $ca = $row['seconds'] * $cost_second;
                $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = '{$user_credit_history}'");
                $row['ca'] = $ca;
                $row['ca_currency'] = $ca_currency;
              }
            }
          }
          
					//payment opposé   
					/*if($is_payment_oppose){
						$mysqli->query("UPDATE users set active = '0' where id = '{$customer_id}'");
						if($row['media'] == 'phone'){
							$price = 0;
							$mysqli->query("UPDATE user_credit_history set is_factured = '0', text_factured = 'client en défaut de paiement' where user_credit_history = '{$user_credit_history}'");
							$mysqli->query("UPDATE user_credit_last_histories set is_factured = '0', text_factured = 'client en défaut de paiement' where media = '{$row['media']}' and sessionid = '{$row['sessionid']}'");
							$datasEmail = array(
												'content' => 'La communication '.$row['media'].' session ID : '.$row['sessionid'].' est passe en non facture puisque le client est en paiement opposé https://fr.spiriteo.com/admin/accounts/view-'.$customer_id.' ',
												'PARAM_URLSITE' => 'https://fr.spiriteo.com'
									);
							$extractrl->sendEmail('contact@talkappdev.com','Communication client oppose','default',$datasEmail);
						}
					}*/
					
					//calculate CA pay expert
					$result_cu = $mysqli->query("SELECT * from currencies WHERE label = '{$row['ca_currency']}'");
					$row_cu = $result_cu->fetch_array(MYSQLI_ASSOC);
					if(!$row_cu['amount']) $row_cu['amount'] = 1;
          
					$ca_paid = $row['ca'] * $row_cu['amount'];
					
					$ca_old = 0;
					if($row['ca_euros'] > 0.1){
						$ca_old = $ca_paid;
						$ca_paid = $row['ca_euros'];
					}

					$result2 = $mysqli->query("SELECT * from user_pay WHERE id_user_credit_history = '{$user_credit_history}'");
					$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					if(!$row2){
						$mysqli->query("INSERT INTO user_pay(id_user_credit_history, order_cat_index, mail_price_index, date_pay, price, ca, ca_old, ca_currency,currency, tx_change) VALUES ('{$user_credit_history}','{$order_cat}','{$mail_price}',NOW(),'{$price}','{$ca_paid}','{$ca_old}','{$row['ca']}','{$row['ca_currency']}','{$row_cu['amount']}')");
						
						//transfert compte connecté
						
						if($row['stripe_account']){
							
							//recup infos charge client
							$source_transaction = '';
							$transfer_group = '';
							
							if($row['ca_ids']){
								$list_cas = unserialize($row['ca_ids']);//   explode('_',$row['ca_ids']);
                $last_charge = "";
                if(!is_array($list_cas)){
                  $list_cas = explode('_',$row['ca_ids']);
                  foreach($list_cas as $ca){
                    $last_charge = $ca;
                  }
                }else{
                  foreach($list_cas as $ca){
                    $last_charge = $ca['id'];
                  }
                }
								
								if($last_charge){
									
									$result3 = $mysqli->query("SELECT id_user_credit from user_credit_prices WHERE id = '{$last_charge}'");
									$row3 = $result3->fetch_array(MYSQLI_ASSOC);
									
									$result4 = $mysqli->query("SELECT order_id from user_credits WHERE id = '{$row3['id_user_credit']}'");
									$row4 = $result4->fetch_array(MYSQLI_ASSOC);
									
									$result5 = $mysqli->query("SELECT payment_mode from orders WHERE id = '{$row4['order_id']}' and date_add > '2019-07-01 12:00:00' ");
									$row5 = $result5->fetch_array(MYSQLI_ASSOC);
									
									//if($row4["payment_mode"] == 'sepa'){
									//	$result5 = $mysqli->query("SELECT id, cart_id from order_sepatransactions WHERE order_id = '{$row3['order_id']}'");
									//	$row5 = $result5->fetch_array(MYSQLI_ASSOC);
										
									//}
									
									if($row5["payment_mode"] == 'stripe'){
										$result6 = $mysqli->query("SELECT id, cart_id from order_stripetransactions WHERE order_id = '{$row4['order_id']}'");
										$row6 = $result6->fetch_array(MYSQLI_ASSOC);
										if(substr_count($row6['id'],'ch_' )){
											$source_transaction = $row6['id'];
											$transfer_group = $row6['cart_id'];
										}else{
											if(substr_count($row6['id'],'pi_' )){
												$paymentIntent =  \Stripe\PaymentIntent::retrieve($row6['id']);
	
												$charges = $paymentIntent->charges->data;
												$charge = $charges[0];
												$source_transaction = $charge->id;
												$transfer_group = $row6['cart_id'];
											}
										}
									}
								}
								
							}
							if($ca_paid > 0){
								$pp = number_format($ca_paid,2,'.','') * 100;

								try {
									if($source_transaction && $transfer_group){
										$transfer = \Stripe\Transfer::create([
										  "amount" => $pp,
										  "currency" => "eur",
										  "source_transaction" => $source_transaction,
										  "destination" => $row['stripe_account'],
										  "transfer_group" => $transfer_group,
										]);	
									}else{
										$transfer = \Stripe\Transfer::create([
										  "amount" => $pp,
										  "currency" => "eur",
										  "destination" => $row['stripe_account'],
										]);
									}
								}
							   catch (Exception $e) {
								// var_dump($e->getMessage());
								   try {
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => $row['stripe_account'],
											]);
									}
								   catch (Exception $e) {
									$datasEmail = array(
												'content' => $e->getMessage(). ' UserCreditHistory =>'.$user_credit_history,
												'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
									);
									$extractrl->sendEmail('system@web-sigle.fr','BUG transfert stripe','default',$datasEmail);
								   }
								
							   }
							}
						}
					}
					
				}
			}
			//paiement des primes palier experts
			$annee = $dx->format('Y');
			$mois = $dx->format('m');
			foreach($list_experts as $id_agent){
				$result1 = $mysqli->query("SELECT * from bonus_agents WHERE annee >= '{$annee}' and mois >= {$mois} and id_agent= '{$id_agent}' and paid = 1 order by id desc limit 1");
				$row1 = $result1->fetch_array(MYSQLI_ASSOC);
				
				if($row1){
					$palier = $row1['id_bonus'];
				}else{
					$palier = 0;
				}
				
				$result2 = $mysqli->query("SELECT * from bonus_agents WHERE date_add >= '{$datemin}' and date_add <= '{$datemax}' and id_agent= '{$id_agent}' and id_bonus > '{$palier}' order by id asc limit 1");
				while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
					
						//double verif
						$result0 = $mysqli->query("SELECT * from bonus_agents WHERE annee = {$row2['annee']} and mois = {$row2['mois']} and id_agent= '{$id_agent}' and paid = 1 and id_bonus = '{$row2['id_bonus']}'");
						$row0 = $result0->fetch_array(MYSQLI_ASSOC);
						
						if(!$row0){
							//paiement du nouveau palier
							$result3 = $mysqli->query("SELECT * from bonuses WHERE id= '{$palier}'");
							$row3 = $result3->fetch_array(MYSQLI_ASSOC);
							if($row3){
								$old = $row3['amount'];
							}else{
								$old = 0;
							}

							$result4 = $mysqli->query("SELECT * from bonuses WHERE id= '{$row2['id_bonus']}'");
							$row4 = $result4->fetch_array(MYSQLI_ASSOC);
							$new = $row4['amount'];

							$diff = $new - $old;
							if($palier > 1 && !$old)$diff = 0;
							
							
							$result10 = $mysqli->query("SELECT stripe_account from users WHERE id= '{$id_agent}'");
							$row10 = $result10->fetch_array(MYSQLI_ASSOC);
							if($diff > 0){
							$test = $mysqli->query("UPDATE bonus_agents SET paid = 1, paid_amount = '{$diff}' WHERE id = '{$row2['id']}'");
								if($row10['stripe_account'] && $test){
										$pp = number_format($diff,2,'.','') * 100;
										try {

												$transfer = \Stripe\Transfer::create([
												  "amount" => $pp,
												  "currency" => "eur",
												  "destination" => $row10['stripe_account'],
												]);
										}
									   catch (Exception $e) {
										// var_dump($e->getMessage());
										$datasEmail = array(
													'content' => $e->getMessage(). ' Bonus ID  =>'.$row2['id_bonus']. ' Pour Agent ID '.$id_agent,
													'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
										);
										$extractrl->sendEmail('system@web-sigle.fr','BUG bonus stripe','default',$datasEmail);
									   }
								}
							}
						}
				}
			}
			
			
			
			//sponsorship update
			
			$date = date('Y-m-01 00:00:00', strtotime('-1 day'));
			$date2 = date('Y-m-31 23:59:59', strtotime('-1 day'));
			$dx = new DateTime($date);
			$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
			$dx2 = new DateTime($date2);
			$dx2->modify('- '.$listing_utcdec[$dx2->format('md')].' hour');
			
			$datemin = $dx->format('Y-m-d H:i:s');
			$datemax = $dx2->format('Y-m-d H:i:s');
			
			
			$result = $mysqli->query("SELECT * from sponsorship_rules WHERE type_user = 'agent'");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$palier_declenchement = $row['palier_declenche'];
			$palier_type = $row['palier_declenche_type'];
			
			$result = $mysqli->query("SELECT sum(P.price) as mt, C.agent_id from user_credit_history C, user_pay P WHERE P.date_pay >= '{$datemin}' and P.date_pay <= '{$datemax}' and C.user_credit_history = P.id_user_credit_history group by C.agent_id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				if($row['mt'] >= $palier_declenchement){
					$result2 = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$row['agent_id']}' and is_recup = 0 and date_add >= '{$datemin}' and date_add <= '{$datemax}' and type_user = 'agent' and status = 3");
					while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
						$mysqli->query("UPDATE `sponsorships` SET `is_recup` = '1' , `date_recup` = NOW() WHERE `id` = '{$row2['id']}'");
					}
				}
			
			}
			
			//update mode de paiement
			$result = $mysqli->query("SELECT mode_paiement,id from users WHERE role = 'agent' and mode_paiement != '' order by id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$result2 = $mysqli->query("SELECT mode from user_pay_mode WHERE user_id = '".$row['id']."' ORDER BY ID DESC LIMIT 1");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if($row['mode_paiement'] != $row2['mode']){
					$mysqli->query("INSERT INTO user_pay_mode(user_id, mode, date_add) VALUES ('{$row['id']}','{$row['mode_paiement']}',NOW())");
				}
			}
			
			//check status des virement exterieurs pending
			$this->loadModel('InvoiceAgent');
			$invoice_pending = $this->InvoiceAgent->find('all', array(
							'conditions'    => array("InvoiceAgent.status" => 7, "InvoiceAgent.payment_id !=" =>NULL),
							'recursive'     => -1
						));
			foreach($invoice_pending as $invoice){
				$result_invoice = $mysqli->query("SELECT payment_id from invoice_agents WHERE id = '{$invoice['InvoiceAgent']['id']}'");
				$row_invoice = $result_invoice->fetch_array(MYSQLI_ASSOC);
				$result_user = $mysqli->query("SELECT stripe_account from users WHERE id = '{$invoice['InvoiceAgent']['user_id']}'");
				$row_user = $result_user->fetch_array(MYSQLI_ASSOC);
				if($row_invoice['payment_id']){
					try {

						$payout = \Stripe\Payout::retrieve($row_invoice['payment_id'],["stripe_account" => $row_user['stripe_account']]);
						
						if($payout){
							if($payout->status == 'in_transit'){
								$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
								$this->InvoiceAgent->saveField('status', 9);
							}
							if($payout->status == 'failed'){
								$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
								$this->InvoiceAgent->saveField('status', 8);
							}
							if($payout->status == 'paid'){
								$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
								$this->InvoiceAgent->saveField('status', 1);
							}
							
						}
						
						
					}
					 catch (Exception $e) {
					}
				}
			}

			//check status des virement exterieurs transit
			$this->loadModel('InvoiceAgent');
			$invoice_pending = $this->InvoiceAgent->find('all', array(
							'conditions'    => array("InvoiceAgent.status" => 9, "InvoiceAgent.payment_id !=" =>NULL),
							'recursive'     => -1
						));
			foreach($invoice_pending as $invoice){
				$result_invoice = $mysqli->query("SELECT payment_id from invoice_agents WHERE id = '{$invoice['InvoiceAgent']['id']}'");
				$row_invoice = $result_invoice->fetch_array(MYSQLI_ASSOC);
				$result_user = $mysqli->query("SELECT stripe_account from users WHERE id = '{$invoice['InvoiceAgent']['user_id']}'");
				$row_user = $result_user->fetch_array(MYSQLI_ASSOC);
				if($row_invoice['payment_id']){
					try {

						$payout = \Stripe\Payout::retrieve($row_invoice['payment_id'],["stripe_account" => $row_user['stripe_account']]);
						
						if($payout){
							
							if($payout->status == 'failed'){
								$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
								$this->InvoiceAgent->saveField('status', 8);
							}
							if($payout->status == 'paid'){
								$this->InvoiceAgent->id = $invoice['InvoiceAgent']['id'];
								$this->InvoiceAgent->saveField('status', 1);
							}
							
						}
						
						
					}
					 catch (Exception $e) {
										
					}
				}
			}
			
			$mysqli->close();			
		}
		public function removeRecords(){
			$this->loadModel('Record');
			
			$path = Configure::read('Site.pathRecordCron');
			$path_new = str_replace('/records','/records_archive',Configure::read('Site.pathRecordCron'));
			//$files = glob($path.'/*.wav');
			//$files2 = glob($path_new.'/*.wav');
			//var_dump(count($files));
			//var_dump(count($files2));exit;
						
			if ($handle = opendir($path)) {
			
				while (false !== ($file = readdir($handle))) { 
					$filelastmodified = filemtime($path .'/'. $file);
					//24 hours in a day * 3600 seconds per hour * 30 days
					if((time() - $filelastmodified) > 24*3600*30)
					{
						if($file != '.' && $file != '..'){
							//deplace en archive
							$move = rename($path .'/'. $file, $path_new .'/'. $file);
							if($move){
								$record = $this->Record->find('first',array(
									'conditions' => array('filename' => $file),
									'recursive' => -1
								));
								if($record){
									$this->Record->id = $record['Record']['id'];
									$this->Record->saveField('archive', 1);
								}
							}
						}
					}
				}
			
				closedir($handle); 
			}

			if ($handle = opendir($path_new)) {
			
				while (false !== ($file = readdir($handle))) { 
					$filelastmodified = filemtime($path_new . '/'. $file);
					//24 hours in a day * 3600 seconds per hour * 90 days
					if((time() - $filelastmodified) > 24*3600*90)
					{
						if($file != '.' && $file != '..'){
					   		$delete = unlink($path_new .'/'. $file);
						
							if($delete){
								$record = $this->Record->find('first',array(
									'conditions' => array('filename' => $file),
									'recursive' => -1
								));
								if($record){
									$this->Record->id = $record['Record']['id'];
									$this->Record->saveField('deleted', 1);
								}
							}
						}
					}
			
				}
			
				closedir($handle); 
			}
		}
		public function alertSMSComTchat(){
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
			$this->loadModel('User');
			//On charge l'API
			App::import('Vendor', 'Noox/Api');
			//On charge le model
			$this->loadModel('SmsHistory');
			$api = new Api();
			
			$this->loadModel('Chat');
			$chats =  $this->Chat->find('all', array(
				'conditions'    => array(
					'Chat.date_end' => NULL,
					'Chat.consult_date_start' => NULL
				),
				'recursive'     => -1,
	
			));
			
			foreach($chats as $chat){
				$datetime1 = new DateTime($chat['Chat']['date_start']);
				$datetime2 = new DateTime(date('Y-m-d H:i:s'));	
				$interval = $datetime1->diff($datetime2);
				$diff_seconde = $interval->format('%S');
				$diff_minute = $interval->format('%I');
				$diff_seconde = ($diff_minute * 60) + $diff_seconde;
				$history = $this->SmsHistory->find('first', array(
						'fields' => array('SmsHistory.id'),
						'conditions' => array('SmsHistory.id_tchat' => $chat['Chat']['id']),
						'recursive' => -1
					));
					
				
				$result_count = $mysqli->query("SELECT * from chat_events where send = '1' and chat_id = '{$chat['Chat']['id']}' and user_id = '{$chat['Chat']['to_id']}' ");

				if($diff_seconde > 20 && !count($history) && !$result_count->num_rows){
					
					$agent = $this->User->find('first', array(
						'fields' => array('User.phone_number', 'User.phone_mobile', 'User.subscribe_mail', 'User.alert_sms'),
						'conditions' => array('User.id' => $chat['Chat']['to_id'], 'User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1),
						'recursive' => -1
					));
					
					$txt = 'Bonjour un Tchat client Spiriteo s\'est declenche et attend votre retour depuis 60 secondes, merci de votre reponse le plus rapidement possible, Cordialement.';
					if(count($agent)){
						$numero = $agent['User']['phone_mobile'];
		
						if($numero && !$agent['User']['alert_sms']){// && $chat['Chat']['to_id'] == 332
							$txtLength = strlen($txt);
							$result = 0;
							$result = $api->sendSms($numero, base64_encode($txt));	
							$history = array(
								'id_agent'          => $chat['Chat']['to_id'],
								'id_client'         => '',
								'id_tchat'         => $chat['Chat']['id'],
								'id_message'         => '',
								'email'             => '',
								'phone_number'      => $numero,
								'content_length'    => $txtLength,
								'content'    		=> $txt,
								'send'              => ($result > 0)?1:0,
								'date_add'          => date('Y-m-d H:i:s'),
								'type'				=> 'CONSULT TCHAT',
								'cost'				=> $result
							);
			
							//On save dans l'historique
							$this->SmsHistory->create();
							$this->SmsHistory->save($history);
						}
					}
				}
			}
			$mysqli->close();
		}
		public function alertSMSComMail(){
			//exit;
			$this->loadModel('User');
			//On charge l'API
			App::import('Vendor', 'Noox/Api');
			//On charge le model
			App::import('Controller', 'Extranet');
			$extractrl = new ExtranetController();
			$this->loadModel('SmsHistory');
			$this->loadModel('UserPenality');
			$this->loadModel('Penality');
			

			App::import('Controller', 'Paymentstripe');
			$paymentctrl = new PaymentstripeController();

			require(APP.'Lib/stripe/init.php');
			\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
			
			//minuit -> 8h on ne tiens pas compte
			$tranche_min = 0;
			$tranche_max = 8;
			$delay_email = 6;
			
			/*debug*/
			/*$date = '2020-05-11 13:39:13';
			$timezone = 'Europe/Paris';
			$mail_date = CakeTime::format(Tools::dateZoneUser($timezone,$date),'%Y-%m-%d %H:%M:%S');
			$datehour = new DateTime($mail_date);
			$heure_mail = $datehour->format('H');
			var_dump($mail_date);
			//add hour night
			$night = 0;
			if($heure_mail >= $tranche_min && $heure_mail <= $tranche_max){
				$night = 1;
			}
			
			$hour_work = $delay_email;
			$date_work = new DateTime($mail_date);
			$add_hour = 0;
			while($hour_work > 0){
				$hour = $date_work->format('H');
				$night_work = 0;
				var_dump($hour);
				if($hour >= $tranche_min && $hour < $tranche_max){
					$night_work = 1;
					$add_hour ++;
					var_dump('ADD');
				}
				if(!$night_work){
					$hour_work --;
				}
				$date_work->modify('+1 hour');
				var_dump('work '.$hour_work);
			}
			//if($night)$add_hour --;
			$add_hour += $delay_email;		
			var_dump('final add :'.$add_hour);
			$dx = new DateTime($mail_date);
						$dx->modify($add_hour.' hour');
						$date_test = $dx->format('Y-m-d H:i:s');
			var_dump($date_test);
			exit;*/
			/*end debug*/
			
			
			$this->loadModel('Message');
			$this->loadModel('User');
			
			
			//check email terme interdit
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 4 hour');
			$date_min = $dx->format('Y-m-d H:i:s');
			$messages =  $this->Message->find('all', array(
				'conditions'    => array(
					'Message.private' => 0,
					'Message.deleted' => 0,
					'Message.archive' => 0,
					'Message.etat' => 2,
					//'Message.parent_id' => NULL,
				    'Message.date_add >=' => $date_min
				),
				'recursive'     => -1,
	
			));
			foreach($messages as $message){
				
				$admin_1 = 317;
				$admin_2 = 314;
				$admin_3 = 332;
				
				$check_date  = date('Y-m-d H:i:s');
				$mail_date  = $message['Message']['date_add'];
				
				$datetime1 = new DateTime($mail_date);
				$date_comp = $datetime1->format('YmdHis');
				
				$datetime2 = new DateTime($check_date);	
				$nb_hour = -60;
				$datetime2->modify($nb_hour.' minute');
				$date_sms_1 = $datetime2->format('YmdHis');
				$num_1 = $this->User->field('phone_number',array('id' => $admin_1));
				$datetime2 = new DateTime($check_date);	
				$nb_hour = -75;
				$datetime2->modify($nb_hour.' minute');
				$date_sms_2 = $datetime2->format('YmdHis');
				$num_2 = $this->User->field('phone_number',array('id' => $admin_2));
				$datetime2 = new DateTime($check_date);	
				$nb_hour = -180;
				$datetime2->modify($nb_hour.' minute');
				$date_sms_3 = $datetime2->format('YmdHis');
				$num_3 = $this->User->field('phone_number',array('id' => $admin_3));
				
				$history_1 = $this->SmsHistory->find('first', array(
							'fields' => array('SmsHistory.id'),
							'conditions' => array('SmsHistory.id_message' => $message['Message']['id'],'SmsHistory.id_agent' => $admin_1),
							'recursive' => -1
					));
				$history_2 = $this->SmsHistory->find('first', array(
							'fields' => array('SmsHistory.id'),
							'conditions' => array('SmsHistory.id_message' => $message['Message']['id'],'SmsHistory.id_agent' => $admin_2),
							'recursive' => -1
					));
				$history_3 = $this->SmsHistory->find('first', array(
							'fields' => array('SmsHistory.id'),
							'conditions' => array('SmsHistory.id_message' => $message['Message']['id'],'SmsHistory.id_agent' => $admin_3),
							'recursive' => -1
					));
				
				$txt = 'URGENT ! Consultation Email contenant un terme interdit à checker puis valider, merci. https://fr.spiriteo.com/admin/admins/watchmails';

				
					if($date_sms_1 >= $date_comp  && !count($history_1)){

						if($num_1){
							$txtLength = strlen($txt);
							$result = 0;
							$api = new Api();
							$result = $api->sendSms($num_1, base64_encode($txt));	
							$history = array(
									'id_agent'          => $admin_1,
									'id_client'         => '',
									'id_tchat'         => '',
									'id_message'         => $message['Message']['id'],
									'email'             => '',
									'phone_number'      => $num_1,
									'content_length'    => $txtLength,
									'content'    		=> $txt,
									'send'              => ($result > 0)?1:0,
									'date_add'          => date('Y-m-d H:i:s'),
									'type'				=> 'CONSULT EMAIL',
									'cost'				=> $result
								);

								//On save dans l'historique
							$this->SmsHistory->create();
							$this->SmsHistory->save($history);
						}
					}
				if($date_sms_2 >= $date_comp  && !count($history_2)){

						if($num_2){
							$txtLength = strlen($txt);
							$result = 0;
							$api = new Api();
							$result = $api->sendSms($num_2, base64_encode($txt));	
							$history = array(
									'id_agent'          => $admin_2,
									'id_client'         => '',
									'id_tchat'         => '',
									'id_message'         => $message['Message']['id'],
									'email'             => '',
									'phone_number'      => $num_2,
									'content_length'    => $txtLength,
									'content'    		=> $txt,
									'send'              => ($result > 0)?1:0,
									'date_add'          => date('Y-m-d H:i:s'),
									'type'				=> 'CONSULT EMAIL',
									'cost'				=> $result
								);

								//On save dans l'historique
							$this->SmsHistory->create();
							$this->SmsHistory->save($history);
						}
					}
				if($date_sms_3 >= $date_comp  && !count($history_3)){

						if($num_3){
							$txtLength = strlen($txt);
							$result = 0;
							$api = new Api();
							$result = $api->sendSms($num_3, base64_encode($txt));	
							$history = array(
									'id_agent'          => $admin_3,
									'id_client'         => '',
									'id_tchat'         => '',
									'id_message'         => $message['Message']['id'],
									'email'             => '',
									'phone_number'      => $num_3,
									'content_length'    => $txtLength,
									'content'    		=> $txt,
									'send'              => ($result > 0)?1:0,
									'date_add'          => date('Y-m-d H:i:s'),
									'type'				=> 'CONSULT EMAIL',
									'cost'				=> $result
								);

								//On save dans l'historique
							$this->SmsHistory->create();
							$this->SmsHistory->save($history);
						}
					}
				
			}
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 24 hour');
			$date_min = $dx->format('Y-m-d H:i:s');
			$messages =  $this->Message->find('all', array(
				'conditions'    => array(
					'Message.private' => 0,
					//'Message.deleted' => 0,
					//'Message.archive' => 0,
					'Message.etat !=' => 3,
					//'Message.id' => 725310,
				    'Message.date_add >=' => $date_min
				),
				'recursive'     => -1,
	
			));
			
			foreach($messages as $message){
				
				if($message['Message']['parent_id'])$old_id = $message['Message']['parent_id']; else  $old_id = $message['Message']['id'];
				
				$messages_check =  $this->Message->find('first', array(
						'conditions'    => array(
						'Message.private' => 0,
						'Message.parent_id' => $old_id,
						//'Message.from_id' => $message['Message']['to_id'],
						//'Message.to_id' => $message['Message']['from_id'],
					),
					'recursive'     => -1,
					'order'         => 'Message.date_add desc',

				));
				
				//check si il s agit du dernier message de ce fil de discussion
				$discussion_check =  $this->Message->find('first', array(
						'conditions'    => array(
						'Message.private' => 0,
						'Message.parent_id' => $old_id,
						'Message.from_id' => $message['Message']['from_id'],
						'Message.date_add >' => $message['Message']['date_add'],
					),
					'recursive'     => -1,
				));
				
				$is_respond = true;
				
				
				if($messages_check){
					
					$to_check =  $this->User->find('first', array(
							'conditions'    => array(
							'User.id' => $messages_check['Message']['from_id'],
						),
						'recursive'     => -1,
					));
					
					if($to_check['User']['role'] != 'agent'){
						$is_respond = false;
					}
					
				}else{
					$is_respond = false;
				}
				
				if($discussion_check)$is_respond = true;
				
				
				if(!$is_respond){
					
					$agent = $this->User->find('first', array(
							'fields' => array('User.phone_number', 'User.phone_mobile', 'User.alert_sms', 'User.alert_mail','User.pseudo', 'User.agent_number', 'User.lang_id', 'User.email','User.country_id','User.domain_id','User.id','User.stripe_account'),
							'conditions' => array('User.id' => $message['Message']['to_id'], 'User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1),
							'recursive' => -1
						));
					if($agent){
						$this->loadModel('UserCountry');
						 $cc_infos = $this->UserCountry->find('first',array(
							'fields' => array('CountryLang.country_id'),
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

						if($cc_infos['CountryLang']['country_id'] && $agent['User']['domain_id'] == 19){
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
								'conditions' => array('Domain.id' => $agent['User']['domain_id']),
								'recursive' => -1
							));


							$this->loadModel('Country');
							$countryInfo = $this->Country->find('first', array(
								'fields' => array('timezone', 'devise', 'devise_iso'),
								'conditions' => array('Country.id' => $domainInfo['Domain']['country_id']),
								'recursive' => -1
							));
						}


						if($countryInfo['Country']['timezone'])
							$timezone = $countryInfo['Country']['timezone'];
						else
							$timezone = 'Europe/Paris';

						$client = $this->User->find('first', array(
								'fields' => array('User.firstname'),
								'conditions' => array('User.id' => $message['Message']['from_id']),
								'recursive' => -1
							));


						if($timezone){
							$mail_date = CakeTime::format(Tools::dateZoneUser($timezone,$message['Message']['date_add']),'%Y-%m-%d %H:%M:%S');
							$check_date = CakeTime::format(Tools::dateZoneUser($timezone,date('Y-m-d H:i:s')),'%Y-%m-%d %H:%M:%S');
						}else{
							$mail_date  = $message['Message']['date_add'];
							$check_date  = date('Y-m-d H:i:s');
						}
						//$check_date = '2019-04-22 13:52:51';//PATCH
						
						if($timezone == 'Europe/Paris'){
							$time_mail = $delay_email;
						}else{
							$time_mail = $delay_email;
							if($timezone == 'America/Toronto'){
								$dateTimezoneUser1 = new DateTimeZone('Europe/Paris');
								$dateTimeUser = new DateTime($mail_date);
								$off = $dateTimezoneUser1->getOffset($dateTimeUser);
								$time_plus = 	($off / 60 ) / 60  ;
								if($time_plus < 2)$time_mail = 7;//rester sur 6 heure dec
							}
						}

						$datehour = new DateTime($check_date);
						$heure_check = $datehour->format('H');

						$night_send = 0;
						if($heure_check >= $tranche_min && $heure_check <= $tranche_max){
							$night_send = 1;
						}

						$datehour = new DateTime($mail_date);
						$heure_mail = $datehour->format('H');
						$datehour->modify('+ '.$time_mail.' hour');
						$heure_check = $datehour->format('H');

						$night = 0;
						if($heure_mail >= $tranche_min && $heure_mail <= $tranche_max){
							$night = 1;
						}

						//calculate add hour for email with night not working
						$hour_work = $time_mail;
						$date_work = new DateTime($mail_date);
						$add_hour = 0;
						while($hour_work > 0){
							$hour = $date_work->format('H');
							$night_work = 0;
							if($hour >= $tranche_min && $hour < $tranche_max){
								$night_work = 1;
								$add_hour ++;
							}
							if(!$night_work){
								$hour_work --;
							}
							$date_work->modify('+1 hour');
						}
						$add_hour += $time_mail;	

						$datetime1 = new DateTime($mail_date);
						$date_pour_mail = $datetime1->format('d-m-Y H').'h'.$datetime1->format('i').'min'.$datetime1->format('s').'s';

						$mail_date = $datetime1->format('Y-m-d H:i:s');
						$datetime1->modify('+ '.$add_hour.' hour');
						$mail_date_penality = $datetime1->format('Y-m-d H:i:s');

						$datetime1 = new DateTime($mail_date);
						$datetime2 = new DateTime($check_date);	
						$date_comp = $datetime1->format('YmdHis');

						$datetime3 = new DateTime($mail_date_penality);
						$date_comp_penality = $datetime3->format('YmdHis');

						$interval = $datetime1->diff($datetime2);
						$diff_heure = $interval->format('%H');
						$diff_jour  = $interval->format('%D');
						$diff_minute  = $interval->format('%I');
						$diff_second  = $datetime2->getTimestamp() - $datetime1->getTimestamp();
						$dx = new DateTime($check_date);
						$nb_hour = -2;
						$dx->modify($nb_hour.' hour');
						$date_sms = $dx->format('YmdHis');
						$dx = new DateTime($check_date);
						$nb_hour = -3;
						$dx->modify($nb_hour.' hour');
						$date_agent = $dx->format('YmdHis');

						//calcul date mail perdu
						$dx = new DateTime($check_date);
						$heure_check = $dx->format('H');
						$night = 0;

						if($heure_check >= $tranche_min && $heure_check <= $tranche_max){
							$night = 1;
						}
						
						

						$dx = new DateTime($check_date);
						$date_admin = $dx->format('YmdHis');

						$history = $this->SmsHistory->find('first', array(
								'fields' => array('SmsHistory.id'),
								'conditions' => array('SmsHistory.id_message' => $message['Message']['id']),
								'recursive' => -1
						));

						$penalty_id = 0;
						$penalty_cost = 0;
						$penalty = $this->Penality->find('all', array(
								'conditions' => array('Penality.type' => 'message'),
								'recursive' => -1
						));
						foreach($penalty as $penalti){
							if($diff_second >= $penalti['Penality']['delay_min'] && $diff_second < $penalti['Penality']['delay_max']){
								$penalty_id = $penalti['Penality']['id'];
								$penalty_cost = $penalti['Penality']['cost'];
							}
						}
						$user_penalty = $this->UserPenality->find('first', array(
								'fields' => array('UserPenality.id'),
								'conditions' => array('UserPenality.message_id' => $message['Message']['id']),
								'recursive' => -1
						));
						
						
						 //load comm
                		$this->loadModel('UserCreditHistory');
						$condition_ca = array(
										'UserCreditHistory.media' => 'email',
										'UserCreditHistory.sessionid' => $message['Message']['id'],
								);
                		$com_ca = $this->UserCreditHistory->find('first',array('conditions' => $condition_ca));
						if(!$com_ca['UserCreditHistory']['is_factured'])$penalty_id = 0;

						if($date_sms >= $date_comp  && !count($history)  && !$night_send ){
							$txt = 'Bonjour vous avez recu une consultation EMAIL Spiriteo il y a 2h. Nous vous remercions de repondre a votre client(e) dans le delai imparti.';
							if(count($agent) && $agent['User']['email']){
								$numero = $agent['User']['phone_mobile'];

								if($numero && $agent['User']['alert_sms']){// && $message['Message']['to_id'] == 317
									$txtLength = strlen($txt);
									$result = 0;
									$api = new Api();
									$result = $api->sendSms($numero, base64_encode($txt));	
									$history = array(
										'id_agent'          => $message['Message']['to_id'],
										'id_client'         => '',
										'id_tchat'         => '',
										'id_message'         => $message['Message']['id'],
										'email'             => '',
										'phone_number'      => $numero,
										'content_length'    => $txtLength,
										'content'    		=> $txt,
										'send'              => ($result > 0)?1:0,
										'date_add'          => date('Y-m-d H:i:s'),
										'type'				=> 'CONSULT EMAIL',
										'cost'				=> $result
									);

									//On save dans l'historique
									$this->SmsHistory->create();
									$this->SmsHistory->save($history);
								}
							}
						}

						if($date_agent >= $date_comp  && intval($diff_minute) == 0 && count($agent) && $agent['User']['email'] && ($diff_heure == 2 || $diff_heure == 3 || $diff_heure == 4 || $diff_heure == 5) && $agent['User']['alert_mail']  && !$night_send ){
							$datasEmail = array(
									'content' => '',
									'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
								);
								//Envoie de l'email
								$is_send = $this->sendCmsTemplateByMail(315, (int)$agent['User']['lang_id'], $agent['User']['email'], array(
									  'PARAM_URLSITE' 			=>    'https://fr.spiriteo.com',
									 'PARAM_PSEUDO' 			=>   $agent['User']['pseudo'],
									'PARAM_CLIENT' 			=>   $client['User']['firstname'],
									'DATE_HEURE_CONSULTATION_PERDUE' 	=>   $date_pour_mail
								),true);
						}
					
						//var_dump($date_admin);
						//var_dump($date_comp_penality);
						//var_dump($night_send);exit;
						if($date_admin >= $date_comp_penality  && $penalty_id && !$user_penalty && !$night_send){
							//var_dump('penality');exit;
							//save penalty
							$penaltyData = array();
							$penaltyData['penalities_id'] = $penalty_id;
							$penaltyData['user_id'] = $agent['User']['id'];
							$penaltyData['message_id'] = $message['Message']['id'];
							$penaltyData['date_com'] = $message['Message']['date_add'];
							$penaltyData['date_add'] = date('Y-m-d H:i:s');
							$penaltyData['delay'] = $diff_second;
							$penaltyData['penality_cost'] = $penalty_cost;

							$this->UserPenality->create();
							if($this->UserPenality->save($penaltyData)){

								$txt = 'L\'Expert '.$agent['User']['pseudo'].' ('.$agent['User']['agent_number'].') a recu une consultation EMAIL à '.CakeTime::format(Tools::dateZoneUser('Europe/Paris',$message['Message']['date_add']),'%Y-%m-%d %H:%M:%S').' de '.$client['User']['firstname'].' non répondu';
								$datasEmail = array(
										'content' => $txt,
										'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
								);

								$this->Message->id = $message['Message']['id'];
								$this->Message->saveField('etat', 3);


								//refund client
								$client = $this->User->find('first', array(
									'fields' => array('User.id', 'User.lang_id', 'User.country_id', 'User.email', 'User.firstname'),
									'conditions' => array('User.id' => $message['Message']['from_id']),
									'recursive' => -1
								));
								$this->loadModel('Order');
								//cree order
								$this->Order->create();
								$uniqReference = $this->getUniqReference();
								$datas = array(
									'cart_id'           => '',
									'reference'         => $uniqReference,
									'user_id'           => $client['User']['id'],
									'lang_id'           => $client['User']['lang_id'],
									'country_id'        => $client['User']['country_id'],
									'product_id'        => '',
									'product_name'      => 'Remboursement',
									'product_credits'   => $message['Message']['credit'],
									'product_price'     => NULL,
									'voucher_code'      => '',
									'voucher_name'      => '',
									'voucher_mode'      => '',
									'voucher_credits'   => '',
									'voucher_amount'    => '',
									'voucher_percent'    => '',
									'payment_mode'      => 'refund',
									'currency'          => '€',
									'total'             => NULL,
									'valid'             => 1,
									'IP'                => '',
									'label'        		=> 'Remboursement consultation Email '.$agent['User']['pseudo'],
									'type_com'         => 4,
									'id_com'           => $message['Message']['id'],
									'commentaire'         => 'Remboursement consultation Email (ID : '.$message['Message']['id'].') '.$agent['User']['pseudo'].' cause délai dépassé.',
									'is_new'			=> 0
								);

								$this->Order->saveAll($datas);

								$this->loadModel('UserCredit');
									$this->UserCredit->create();
									$this->UserCredit->save(array(
										'credits'    => $message['Message']['credit'],
										'product_id' => '',
										'product_name' => 'Remboursement consultation Email '.$agent['User']['pseudo'],
										'order_id'   => $this->Order->id,
										'payment_mode' => 'refund',
										'date_upd'   => date('Y-m-d H:i:s'),
										'users_id'   => $client['User']['id']
									));

								//load comm
                $this->loadModel('UserCreditPrice');
                $this->loadModel('Order');
								$condition_ca = array(
										'UserCreditHistory.media' => 'email',
										'UserCreditHistory.sessionid' => $message['Message']['id'],
								);
                $com_ca = $this->UserCreditHistory->find('first',array('conditions' => $condition_ca));
                
                $list_cas = unserialize($com_ca['UserCreditHistory']['ca_ids']);
                $last_charge = "";
                if(!is_array($list_cas)){
                  $list_cas = explode('_',$com_ca['UserCreditHistory']['ca_ids']);
                  foreach($list_cas as $ca){
                    $last_charge = $ca;
                  }
                }else{
                  foreach($list_cas as $ca){
                    $last_charge = $ca['id'];
                  }
                }
                
                
                
                $userp = 0;
				$userpe = 0;
                $devise = '';
                if($last_charge){
                  $condition_price = array(
										'UserCreditPrice.id' => $last_charge,
                  );
                  $userprice = $this->UserCreditPrice->find('first',array('conditions' => $condition_price));
                  $userp = $userprice['UserCreditPrice']['price'];
                  $devise = $userprice['UserCreditPrice']['devise'];
                }else{
                  $condition_order = array(
										'Order.user_id' => $client['User']['id'],
                    'Order.valid' => 1,
										'Order.date_add <' => $message['Message']['date_add'],
                    
                  );
                  $order_client = $this->Order->find('first',array('conditions' => $condition_order, 'order'=>'Order.id DESC'));
                  $userp = $order_client['Order']['total'] / ($order_client['Order']['voucher_credits'] + $order_client['Order']['product_credits']);
				  $userpe = $order_client['Order']['total_euros'] / ($order_client['Order']['voucher_credits'] + $order_client['Order']['product_credits']);
                  $devise = $order_client['Order']['currency'];
                }
                
                //add user credit price
                $this->UserCreditPrice->create();
                $this->UserCreditPrice->save(array(
                    'id_user_credit'    => $this->UserCredit->id,
                    'user_id' => $client['User']['id'],
                    'price' => $userp,
					'price_euros' => $userpe,
                    'devise' => $devise,
                    'seconds'   => $message['Message']['credit'],
                    'seconds_left'   => $message['Message']['credit'],
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd'   => date('Y-m-d H:i:s'),
                ));

								//crediter client
								$this->User->id = $client['User']['id'];
								$credits = $this->User->field('credit') + $message['Message']['credit'];
								$newCredit = $this->User->saveField('credit', $credits);

								//delete CA de la comm
								$this->UserCreditHistory->id = $com_ca['UserCreditHistory']['user_credit_history'];
								$this->UserCreditHistory->saveField('ca', 0);
								

								//Email refund client
								$comm = '<table style="width: 100%; padding: 15px; background-color: #f2f2f2;">
	<tbody>
	<tr>
	<td><span style="color: #000080;">Remboursement consultation Email '.$agent['User']['pseudo'].' cause délai dépassé.</span></td>
	</tr>
	</tbody>
	</table>';
								$this->sendCmsTemplatePublic(431, (int)$client['User']['lang_id'], $client['User']['email'], array(
										'NOMBRE_CREDITS' =>$message['Message']['credit'],
										'NOMBRE_MIN' =>'15 min.',
										'EMAIL_COMPTE_CLIENT' =>$client['User']['email'],
										'COMMENTAIRE' =>$comm,
										'PSEUDO' =>$client['User']['firstname'],
										'PARAM_AGENT_PSEUDO' =>$agent['User']['pseudo'],
										'DATE_MAIL' => CakeTime::format(Tools::dateZoneUser('Europe/Paris',$message['Message']['date_add']),'%Y-%m-%d à %H:%M:%S')
									));
								
								//refund stripe
								if($agent['User']['stripe_account']){
									try {
										$account = \Stripe\Account::retrieve();
										\Stripe\Transfer::create(
										  [
											"amount" => 1200,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent['User']['stripe_account']]
										);

									 } catch (\Stripe\Error\Base $e) {
										$datasEmail2 = array(
										'content' => $e->getMessage(). ' Message.id =>'.$message['Message']['id'],
										'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
										);
										$extractrl->sendEmail('system@web-sigle.fr','BUG refund stripe','default',$datasEmail2);
									}
								}

								//Envoie de l'email
								$extractrl->sendEmail('contact@talkappdev.com','Consult Email non repondu','default',$datasEmail);
							}
						}
					}
				}
			}
		}
		public function deleteComDuplicate(){
			ini_set("memory_limit",-1);
			set_time_limit ( 0 );
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 180 minutes');
			$date_max = $dx->format('Y-m-d H:i:s');
			
			$list_duplicate = array();
			$result = $mysqli->query("SELECT * from user_credit_last_histories where date_start >= '{$date_max}' and sessionid != '' order by user_credit_last_history");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){	
				$result2 = $mysqli->query("SELECT * from user_credit_last_histories WHERE date_start >= '{$date_max}' and sessionid = '{$row['sessionid']}' and user_credit_last_history != '{$row['user_credit_last_history']}' and media = '{$row['media']}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					if($row2['user_credit_last_history'] && !in_array($row['sessionid'],$list_duplicate)){
						$mysqli->query("delete from user_credit_last_histories WHERE user_credit_last_history = '{$row2['user_credit_last_history']}'");
						array_push($list_duplicate,$row['sessionid']); 
					}
			}
			
			$list_duplicate = array();
			$result = $mysqli->query("SELECT * from user_credit_history where date_start >= '{$date_max}' and sessionid != '' order by user_credit_history");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){	
				$result2 = $mysqli->query("SELECT * from user_credit_history WHERE date_start >= '{$date_max}' and sessionid = '{$row['sessionid']}' and user_credit_history != '{$row['user_credit_history']}' and media = '{$row['media']}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					if($row2['user_credit_history'] && !in_array($row['sessionid'],$list_duplicate)){
						$mysqli->query("delete from user_credit_history WHERE user_credit_history = '{$row2['user_credit_history']}'");
						array_push($list_duplicate,$row['sessionid']); 
					}
			}
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 2 days');
			$date_max = $dx->format('Y-m-d H:i:s');
			
			
			$list_duplicate = array();
			$result = $mysqli->query("SELECT * from user_pay where date_pay >= '{$date_max}' and id_user_credit_history != '' order by id_user_credit_history");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){	
				$result2 = $mysqli->query("SELECT * from user_pay WHERE date_pay >= '{$date_max}' and id_user_credit_history = '{$row['id_user_credit_history']}' and id_user_pay != '{$row['id_user_pay']}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					if($row2['id_user_pay'] && !in_array($row['id_user_credit_history'],$list_duplicate)){
						$mysqli->query("delete from user_pay WHERE id_user_pay = '{$row2['id_user_pay']}'");
						array_push($list_duplicate,$row['id_user_credit_history']); 
					}
			}
			$mysqli->close();
		}
		
		public function crmSend(){
			//exit;
			$this->loadModel('Crm');
			$this->loadModel('User');
			$this->loadModel('Lang');
			$this->loadModel('Page');
			$this->loadModel('PageLang');
			$this->loadModel('Order');
			$this->loadModel('CrmStat');
			$this->loadModel('CartLoose');
			$this->loadModel('AgentView');
			$this->loadModel('UserCreditHistory');
			$this->loadModel('Domain');
			$this->loadModel('Voucher');
			$this->loadModel('Review');
			$this->loadModel('LoyaltyCredit');
			$this->loadModel('CategoryUser');
			
			
			$conditions = array(
				'Crm.active' => 1,
			);
			
			$crms = $this->Crm->find('all',array('conditions' => $conditions));
			$listSend = array();
			
			foreach($crms as $crm){
				$dt = new DateTime(date('Y-m-d H:i:s'));
				$dx = new DateTime(date('Y-m-d H:i:s'));
				
				switch ($crm['Crm']['timing']) {
					case "0.005":
						$dt->modify('- 30 minutes');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 1 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;		
					case "0.01":
						$dt->modify('- 1 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 2 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;	
					case "0.02":
						$dt->modify('- 2 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 3 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.03":
						$dt->modify('- 3 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 4 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.04":
						$dt->modify('- 4 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 5 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.05":
						$dt->modify('- 5 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 6 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.06":
						$dt->modify('- 6 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 12 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.08":
						$dt->modify('- 8 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 10 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.10":
						$dt->modify('- 10 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 12 hour');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "0.12":
						$dt->modify('- 12 hour');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 1 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "1":
						$dt->modify('- 1 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 2 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "2":
						$dt->modify('- 2 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 3 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "3":
						$dt->modify('- 3 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 4 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "4":
						$dt->modify('- 4 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 5 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "5":
						$dt->modify('- 5 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 6 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "6":
						$dt->modify('- 6 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 7 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "7":
						$dt->modify('- 7 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 14 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "14":
						$dt->modify('- 14 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 15 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "21":
						$dt->modify('- 21 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 22 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "30":
						$dt->modify('- 30 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 31 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "45":
						$dt->modify('- 45 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 46 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "60":
						$dt->modify('- 60 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 61 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "90":
						$dt->modify('- 90 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 91 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "120":
						$dt->modify('- 120 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 121 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "150":
						$dt->modify('- 150 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 151 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "180":
						$dt->modify('- 180 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 181 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "210":
						$dt->modify('- 210 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 211 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "240":
						$dt->modify('- 240 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 241 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "270":
						$dt->modify('- 270 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 271 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "300":
						$dt->modify('- 300 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 301 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "330":
						$dt->modify('- 330 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 331 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "360":
						$dt->modify('- 360 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 361 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "540":
						$dt->modify('- 540 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 541 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
					case "720":
						$dt->modify('- 720 day');
						$delai = $dt->format('Y-m-d H:i:s');
						$dx->modify('- 721 day');
						$delai_max = $dx->format('Y-m-d H:i:s');
						break;
				}
				
				//check si envoi now
				if(!$crm['Crm']['h_start'] || ($crm['Crm']['h_start'] == date('H'))){
					switch ($crm['Crm']['type']) {
						case "NEVER"://N'ayant jamais acheter sur le site

							$conditions = array(
								'User.active' => 1,
								'User.deleted' => 0,
								'User.valid' => 1,
								'User.role' => 'client',
								'User.date_add <=' =>$delai,
								'User.date_add >' =>$delai_max
							);
							$users = $this->User->find('all',array('conditions' => $conditions));

							foreach($users as $user){
								//check si pas d'achat
								$conditions = array(
									'Order.user_id' => $user['User']['id'],
									'Order.valid' => 1,
								);

								$orders = $this->Order->find('first',array('conditions' => $conditions));	
								if(!$orders){
									//check dernier envoi
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => $crm['Crm']['id'],
									);

									$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions));
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 35,
									);

									$crmstats2 = $this->CrmStat->find('first',array('conditions' => $conditions));
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 39,
									);

									$crmstats3 = $this->CrmStat->find('first',array('conditions' => $conditions));

									if(!$crmstats && !$crmstats2 && !$crmstats3){
										//ajout du client ds tableau envoi
										$objSend = new StdClass();
										$objSend->id_user = $user['User']['id'];
										$objSend->id_cms = $crm['Crm']['id_cms'];
										$objSend->id_mail = $crm['Crm']['id_mail'];
										$objSend->id_crm = $crm['Crm']['id'];
										$objSend->tracker = $crm['Crm']['tracker'];
										$objSend->data = '';
										array_push($listSend, $objSend);
									}
								}
							}
						break;
						case "SINCE"://Inscrit mais n ayant pas acheter depuis

							$conditions = array(
									'Order.valid' => 1,
									'Order.date_add >' =>$delai_max,
									'Order.date_add <=' =>$delai,
							);
							
							$orders = $this->Order->find('all',array('fields' => array('Order.user_id','Order.date_add','Order.id'), 'conditions' => $conditions, 'order' => array('Order.date_add' => 'DESC')));	
							$unique_order = array();
							foreach($orders as $order){
								if(!isset($unique_order[$order["Order"]['user_id']])){
									$ordertest = $this->Order->find('first',array('fields' => array('Order.id'), 'conditions' => array('Order.valid' => 1,
									'Order.date_add >' =>$order["Order"]['date_add'],
									'Order.user_id' =>$order["Order"]['user_id'])));
									if(!$ordertest)
									$unique_order[$order["Order"]['user_id']] = $order;
								}
									
							}
							foreach($unique_order as $order){
								//check dernier envoi
								$conditions3 = array(
												'CrmStat.id_user' => $order['Order']['user_id'],
												'CrmStat.id_crm' => $crm['Crm']['id'],

								);
								$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions3));	
								if(!$crmstats){
												//ajout du client ds tableau envoi
												$objSend = new StdClass();
												$objSend->id_user = $order['Order']['user_id'];
												$objSend->id_cms = $crm['Crm']['id_cms'];
												$objSend->id_mail = $crm['Crm']['id_mail'];
												$objSend->id_crm = $crm['Crm']['id'];
												$objSend->tracker = $crm['Crm']['tracker'];
												$objSend->data = $order['Order']['id'];
												array_push($listSend, $objSend);
								}
							}
						break;
						case "CART"://Panier abandonné
						
							$conditions = array(
								'CartLoose.status' => -1,
								'CartLoose.id_user >' => 0,
								'CartLoose.date_cart <=' =>$delai,
								'CartLoose.date_cart >' =>$delai_max
							);

							$carts = $this->CartLoose->find('all',array('conditions' => $conditions));

							foreach($carts as $cart){
								$dx = new DateTime(date('Y-m-d H:i:s'));
								$dx->modify('- 60 day');
								$delai_comp = $dx->format('Y-m-d H:i:s');
								
								$ordertest = $this->Order->find('first',array('fields' => array('Order.id'), 'conditions' => array('Order.valid' => 1,
									'Order.date_add >' =>$delai_comp,
									'Order.user_id' =>$cart['CartLoose']['id_user'])));
								if(!$ordertest){
									//check dernier envoi
									$conditions = array(
											'CrmStat.id_user' => $cart['CartLoose']['id_user'],
											'CrmStat.id_crm' => $crm['Crm']['id'],
											'CrmStat.data' => $cart['CartLoose']['id'],
									);

									$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions));	
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 3,
									);

									$crmstats2 = $this->CrmStat->find('first',array('conditions' => $conditions));
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 4,
									);

									$crmstats3 = $this->CrmStat->find('first',array('conditions' => $conditions));
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 1,
									);

									$crmstats4 = $this->CrmStat->find('first',array('conditions' => $conditions));
									
									$conditions = array(
										'CrmStat.id_user' => $user['User']['id'],
										'CrmStat.id_crm' => 2,
									);

									$crmstats5 = $this->CrmStat->find('first',array('conditions' => $conditions));
									

									if(!$crmstats && !$crmstats2 && !$crmstats3 && !$crmstats4 && !$crmstats5){
										//ajout du client ds tableau envoi
										$objSend = new StdClass();
										$objSend->id_user = $cart['CartLoose']['id_user'];
										$objSend->id_cms = $crm['Crm']['id_cms'];
										$objSend->id_mail = $crm['Crm']['id_mail'];
										$objSend->id_crm = $crm['Crm']['id'];
										$objSend->tracker = $crm['Crm']['tracker'];
										$objSend->data = $cart['CartLoose']['id'];
										array_push($listSend, $objSend);
									}
								}
							}
						break;
						case "BUY"://Achat non finalisé

							$conditions = array(
								'CartLoose.status' => 0,
								'CartLoose.id_user >' => 0,
								'CartLoose.date_cart <=' =>$delai,
								'CartLoose.date_cart >' =>$delai_max
							);

							$carts = $this->CartLoose->find('all',array('conditions' => $conditions));

							foreach($carts as $cart){
								$dx = new DateTime(date('Y-m-d H:i:s'));
								$dx->modify('- 60 day');
								$delai_comp = $dx->format('Y-m-d H:i:s');
								$ordertest = $this->Order->find('first',array('fields' => array('Order.id'), 'conditions' => array('Order.valid' => 1,
									'Order.date_add >' =>$delai_comp,
									'Order.user_id' =>$cart['CartLoose']['id_user'])));
								if(!$ordertest){
								
									//check dernier envoi
									$conditions = array(
											'CrmStat.id_user' => $cart['CartLoose']['id_user'],
											'CrmStat.id_crm' => $crm['Crm']['id'],
											'CrmStat.data' => $cart['CartLoose']['id'],
									);

									$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions));	

									if(!$crmstats){
										//ajout du client ds tableau envoi
										$objSend = new StdClass();
										$objSend->id_user = $cart['CartLoose']['id_user'];
										$objSend->id_cms = $crm['Crm']['id_cms'];
										$objSend->id_mail = $crm['Crm']['id_mail'];
										$objSend->id_crm = $crm['Crm']['id'];
										$objSend->tracker = $crm['Crm']['tracker'];
										$objSend->data = $cart['CartLoose']['id'];
										array_push($listSend, $objSend);
									}
								}
							}
						break;
						case "VISIT"://Visite profil Expert

							$conditions = array(
								'AgentView.date_view <=' =>$delai,
								'AgentView.date_view >' =>$delai_max,
								'AgentView.user_id >' =>0
							);
							$views = $this->AgentView->find('all',array('conditions' => $conditions));

							foreach($views as $view){
								//check si comm avec ce voyant
								$conditions = array(
										'UserCreditHistory.user_id' => $view['AgentView']['user_id'],
										'UserCreditHistory.agent_id' => $view['AgentView']['agent_id'],
								);
								$coms = $this->UserCreditHistory->find('first',array('conditions' => $conditions , 'order' => 'UserCreditHistory.user_credit_history DESC'));
								if(!$coms){

									//check si agent actif
									$agent_actif = $this->User->find('first',array('conditions' => array('id' => $view['AgentView']['user_id'], 'active' => 1)));
									if($agent_actif){

										//check dernier envoi
										$conditions = array(
											'CrmStat.id_user' => $view['AgentView']['user_id'],
											'CrmStat.id_crm' => $crm['Crm']['id'],
										);
										$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions));	

										if($agent_actif['User']['id'] && !$crmstats){
											//ajout du client ds tableau envoi
											$objSend = new StdClass();
											$objSend->id_user = $view['AgentView']['user_id'];
											$objSend->id_cms = $crm['Crm']['id_cms'];
											$objSend->id_mail = $crm['Crm']['id_mail'];
											$objSend->id_crm = $crm['Crm']['id'];
											$objSend->tracker = $crm['Crm']['tracker'];
											$objSend->data = $view['AgentView']['agent_id'];
											array_push($listSend, $objSend);
										}
									}
								}
							}
						break;
						case "LOYAL"://N'ayant jamais acheter sur le site
						
							$conditions = array(
								'LoyaltyCredit.date_add <=' =>$delai,
							    'LoyaltyCredit.date_add >' =>$delai_max,
								'LoyaltyCredit.valid' => 0,
							);

							$loyals = $this->LoyaltyCredit->find('all',array('conditions' => $conditions));	
							if($loyals){
								foreach($loyals as $loyal){	
									//check dernier envoi
									$conditions = array(
										'CrmStat.id_user' => $loyal['LoyaltyCredit']['user_id'],
										'CrmStat.id_crm' => $crm['Crm']['id'],
										'CrmStat.data' => $loyal['LoyaltyCredit']['id'],
									);

									$crmstats = $this->CrmStat->find('first',array('conditions' => $conditions));	

									if(!$crmstats){
										//ajout du client ds tableau envoi
										$objSend = new StdClass();
										$objSend->id_user = $loyal['LoyaltyCredit']['user_id'];
										$objSend->id_cms = $crm['Crm']['id_cms'];
										$objSend->id_mail = $crm['Crm']['id_mail'];
										$objSend->id_crm = $crm['Crm']['id'];
										$objSend->tracker = $crm['Crm']['tracker'];
										$objSend->data = $loyal['LoyaltyCredit']['id'];
										array_push($listSend, $objSend);
									}
								}
							}
												
						break;
					}
				}
			}
			
			$sendClient = array();
			
			foreach($listSend as $send){
				
				if(!in_array($send->id_user,$sendClient ) ){
					$conditions = array(
						'User.id' => $send->id_user,
					);

					$user = $this->User->find('first',array('conditions' => $conditions));
					$user_prenom = $user['User']['firstname'];

					if(is_array($user['User']) && $user['User']['active'] && $user['User']['subscribe_mail']){
						$conditions = array(
							'Domain.id' => $user['User']['domain_id'],
						);

						$domain = $this->Domain->find('first',array('conditions' => $conditions));
						if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';

						$conditions = array(
							'Lang.id_lang' => $user['User']['lang_id'],
						);

						$lang = $this->Lang->find('first',array('conditions' => $conditions));

						$conditions = array(
							'Page.id' => $send->id_mail,
						);

						$mail = $this->Page->PageLang->find('first',array(
						'fields'     => 'PageLang.*, Page.id, Page.active, Page.page_category_id',
						'conditions' => $conditions));

						$mail_text = $mail['PageLang']['content'];

						$characts = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	
						$characts .= '1234567890'; 
						$code = ''; 

						for($i=0;$i < 8;$i++) 
						{ 
							$code .= $characts[ rand() % strlen($characts) ]; 
						}
						
						switch ($send->id_crm) {
							case 1:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 1 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_1H_5MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 2:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 1 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_3H_5MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 3:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_1J_10MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;	
							case 4:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 1 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_2J_10MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 5:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_7J_10MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 6:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'ISA_14J_10MIN'; 
								$requestVoucher["label_fr"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_be"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ch"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_lu"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["label_ca"] = 'Cadeau de bienvenue : 10mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 19:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'RAC_14J_5MIN'; 
								$requestVoucher["label_fr"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 20:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'RAC_45J_5MIN'; 
								$requestVoucher["label_fr"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 21:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'RAC_60J_5MIN'; 
								$requestVoucher["label_fr"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 22:
								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'RAC_90J_5MIN'; 
								$requestVoucher["label_fr"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Promo fidélité : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 35:
								$code_label = 'Promo fidélité : 10mn offertes';
								$code_title = 'PA_RAC_1J_10MIN';
								$conditions = array(
										'Order.user_id' => $user['User']['id'],
										'Order.valid' => 1,
									);

								$orders = $this->Order->find('first',array('conditions' => $conditions));	
								if(!$orders){
									$code_label = 'Cadeau de bienvenue : 10 mn offertes';
									$code_title = 'PA_ISA_1J_10MIN';
								}

								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = $code_title; 
								$requestVoucher["label_fr"] = $code_label; 
								$requestVoucher["label_be"] = $code_label; 
								$requestVoucher["label_ch"] = $code_label; 
								$requestVoucher["label_lu"] = $code_label; 
								$requestVoucher["label_ca"] = $code_label;
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
							case 39:
								$code_label = 'Promo fidélité : 10mn offertes';
								$code_title = 'PA_RAC_2J_10MIN';
								$conditions = array(
										'Order.user_id' => $user['User']['id'],
										'Order.valid' => 1,
									);

								$orders = $this->Order->find('first',array('conditions' => $conditions));	
								if(!$orders){
									$code_label = 'Cadeau de bienvenue : 10 mn offertes';
									$code_title = 'PA_ISA_2J_10MIN';
								}

								$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = $code_title; 
								$requestVoucher["label_fr"] = $code_label; 
								$requestVoucher["label_be"] = $code_label; 
								$requestVoucher["label_ch"] = $code_label; 
								$requestVoucher["label_lu"] = $code_label; 
								$requestVoucher["label_ca"] = $code_label;
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '600'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
								break;
						}
						
						//save send
						$agent_nom1 ='' ;
						$agent_photo1 	='' ;
						$agent_pourcent1 ='' ;
						$agent_url1 	='' ;
						$agent1_spe1		= '';
						$agent1_spe2		= '';
						$agent_nom2 ='' ;
						$agent_photo2 	='' ;
						$agent_pourcent2 ='' ;
						$agent_url2 	='' ;
						$agent2_spe1		= '';
						$agent2_spe2		= '';
						$agent_nom3 ='' ;
						$agent_photo3 	='' ;
						$agent_pourcent3 ='' ;
						$agent_url3 	='' ;
						$agent3_spe1		= '';
						$agent3_spe2		= '';

						$this->CrmStat->create();
						$requestCrmStat = array();
						$requestCrmStat['CrmStat']['id_crm'] = $send->id_crm;
						$requestCrmStat['CrmStat']['id_user'] = $send->id_user;
						$requestCrmStat['CrmStat']['data'] = $send->data;
						$requestCrmStat['CrmStat']['email'] = '';
						$requestCrmStat['CrmStat']['date'] = date('Y-m-d H:i:s');
						$this->CrmStat->save($requestCrmStat);
						
						$url = 'https://'.$domain['Domain']['domain'].'/crm/login?utm_campaign='.$send->tracker.'&utm_medium=email&utm_source=website&m='.$user['User']['email'].'&i='.$this->CrmStat->id;
						$url_home = 'https://'.$domain['Domain']['domain'].'/';
						$url_review = 'https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/avis-clients';
						$url_param = '?utm_campaign='.$send->tracker.'&utm_medium=email&utm_source=website';
						$url_pixel_view = '<img src="https://'.$domain['Domain']['domain'].'/crm/track?i='.$this->CrmStat->id.'" />';

						$agent_pseudo = 'spiriteo';
						if($send->id_crm == 36){//recupere pseudo agent visite
							if($send->data){
							$conditions = array(
									'User.id' =>$send->data,
								);
								$userdata = $this->User->find('first',array('fields' => array('User.pseudo'),'conditions' => $conditions));
								$agent_pseudo = $userdata['User']['pseudo'];
							}
						}

						$url_redir = '';
						$url_redir2 = 'https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/avis-clients';

						switch ($send->id_crm) {
							case 36:
								$url_redir = 'https://'.$domain['Domain']['domain'].'/';
								break;
							default:
								$url_redir = 'https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/tunnel-1-choisissez-le-nombre-de-minutes-a-acheter';
								break;

						}
						
						if($send->id_crm == 19){//load 3 dernier agent actif
							$conditions = array(
									'User.role' =>'agent',
									'User.active' =>'1',
									'User.deleted' =>'0',
							);
							$users_find = $this->User->find('all',array('conditions' => $conditions, 'order' => 'User.date_add DESC', 'limit' => 3));
							$n = 1;
							foreach($users_find as $user_find){

								$url_find ='https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/agents-en-ligne/'.strtolower(str_replace(' ','-',$user_find['User']['pseudo'])).'-'.$user_find['User']['agent_number'];

								$photo_find = 'https://'.$domain['Domain']['domain'].'/media/photo/'.substr($user_find['User']['agent_number'],0,1).'/'.substr($user_find['User']['agent_number'],1,1).'/'.$user_find['User']['agent_number'].'_listing.jpg';

								$conditions = array(
										'Review.agent_id' => $user_find['User']['id'],
										'Review.status' => 1,
										'Review.parent_id' => NULL
								);
								$review_find = $this->Review->find('first',array('field' => 'AVG(pourcent) as star','conditions' => $conditions));
								$avg = '';
								if(isset($review_find[0]['star']))
									$avg = number_format($review_find[0]['star'],1);
								else
									$avg = 100;

								if($avg && $avg > 0) $avg .= '%';

								$categoryLangs = $this->CategoryUser->find('all',array(
									'fields' => array('CategoryLang.category_id', 'CategoryLang.name', 'CategoryLang.link_rewrite'),
									'conditions' => array('CategoryUser.user_id' => $user_find['User']['id']),
									'joins' => array(
										array(
											'table' => 'category_langs',
											'alias' => 'CategoryLang',
											'type'  => 'left',
											'conditions' => array(
												'CategoryLang.category_id = CategoryUser.category_id',
												'CategoryLang.lang_id = 1'
											)
										)
									),
									'limit' => 2,
									'recursive' => -1
								));

								if($n == 1){
									$agent_nom1 		= $user_find['User']['pseudo'];
									$agent_photo1 		= $photo_find;
									$agent_pourcent1 	= $avg;
									$agent_url1 		= $url_find;
									$c_n = 0;
									foreach($categoryLangs as $key => $category){
										$c_n ++;
										if(!empty($category['CategoryLang']['name'])){
											if($c_n == 1)
												$agent1_spe1		= $category['CategoryLang']['name'];
											if($c_n == 2)
												$agent1_spe2		= $category['CategoryLang']['name'];
										}
									}

								}
								if($n == 2){
									$agent_nom2 		= $user_find['User']['pseudo'];
									$agent_photo2 		= $photo_find;
									$agent_pourcent2 	= $avg;
									$agent_url2 		= $url_find;
									$c_n = 0;
									foreach($categoryLangs as $key => $category){
										$c_n ++;
										if(!empty($category['CategoryLang']['name'])){
											if($c_n == 1)
												$agent2_spe1		= $category['CategoryLang']['name'];
											if($c_n == 2)
												$agent2_spe2		= $category['CategoryLang']['name'];
										}
									}
								}
								if($n == 3){
									$agent_nom3 		= $user_find['User']['pseudo'];
									$agent_photo3 		= $photo_find;
									$agent_pourcent3 	= $avg;
									$agent_url3 		= $url_find;
									$c_n = 0;
									foreach($categoryLangs as $key => $category){
										$c_n ++;
										if(!empty($category['CategoryLang']['name'])){
											if($c_n == 1)
												$agent3_spe1		= $category['CategoryLang']['name'];
											if($c_n == 2)
												$agent3_spe2		= $category['CategoryLang']['name'];
										}
									}
								}
								$n++;
							}
						}
						
						$is_send = $this->sendCmsTemplateByMail($send->id_mail, (int)$user['User']['lang_id'], $user['User']['email'], array(
							  'PIXEL' 					=>   $url_pixel_view,
							  'PARAM_URLSITE' 			=>   'https://'.$domain['Domain']['domain'].'/',
							  'URL'						=>   $url_redir,
							  'URL2'					=>   $url_redir2,
							  'URL_PARAM' 			    =>   $url_param,
							  'URL_CONNEXION' 			=>   $url,
							  'URL_CONNEXION_HOME' 		=>   $url_home,
							  'URL_CONNEXION_AVIS' 		=>   $url_review,
							  'PSEUDO' 					=>   $agent_pseudo,
							  'PRENOM'					=>   $user_prenom,
							  'CART_USER_FIRSTNAME' 	=>   $user['User']['firstname'],
							  'EMAIL_CLIENT'            =>   $user['User']['email'], 
							  'AGENT_NOM1' 				=>   $agent_nom1,
							  'AGENT_PHOTO1' 			=>   $agent_photo1,
							  'AGENT_POURCENT1' 		=>   $agent_pourcent1,
							  'AGENT1_SPE1' 		    =>   $agent1_spe1,
						      'AGENT1_SPE2' 		    =>   $agent1_spe2,
							  'AGENT_URL1' 				=>   $agent_url1,
							  'AGENT_NOM2' 				=>   $agent_nom2,
							  'AGENT_PHOTO2' 			=>   $agent_photo2,
							  'AGENT_POURCENT2' 		=>   $agent_pourcent2,
							  'AGENT2_SPE1' 		    =>   $agent2_spe1,
						      'AGENT2_SPE2' 		    =>   $agent2_spe2,
							  'AGENT_URL2' 				=>   $agent_url2,
							  'AGENT_NOM3' 				=>   $agent_nom3,
							  'AGENT_PHOTO3' 			=>   $agent_photo3,
							  'AGENT_POURCENT3' 		=>   $agent_pourcent3,
						      'AGENT3_SPE1' 		    =>   $agent3_spe1,
						      'AGENT3_SPE2' 		    =>   $agent3_spe2,
							  'AGENT_URL3' 				=>   $agent_url3,
						),true);

						if($is_send){
							$this->CrmStat->saveField('email', $user['User']['email']);
						}
						
						
					}
					
					array_push($sendClient,$send->id_user);
				}
			}
			
			exit;
		}
		
		public function clearBonus(){
			$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
			//archiver les vouchers perimé ou utilisé
			$this->loadModel('Voucher');
			$conditions = array(
				'Voucher.active' => 1,
			);
			
			$vouchers = $this->Voucher->find('all',array('conditions' => $conditions));
			
			foreach($vouchers as $voucher){
				
				$datevoucher = str_replace('-','',$voucher['Voucher']['validity_end']);
				$datevoucher = str_replace(':','',$datevoucher);
				$datevoucher = str_replace(' ','',$datevoucher);
				
				if($datevoucher < date('YmdHis')){
					$mysqli->query("update vouchers set active = 2 WHERE code = '{$voucher['Voucher']['code']}' and validity_end = '".$voucher['Voucher']['validity_end']."'");
				}
			}
			$mysqli->close();
		}
		
		public function checkCallNoResponse(){

			$mysqli_connect = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			App::import('Vendor', 'Noox/Api');
			
			//boucle sur call infos et check si dans les derniers 30 mins, on a 2 appels vers agent snon repondu
			$this->loadModel('CallInfo');
			//$this->Callinfo->useTable = 'call_infos';
			$this->loadModel('User');
			$this->loadModel('Chat');
			$this->loadModel('ChatMessage');
			$this->loadModel('UserPenality');
			$this->loadModel('Penality');
			
			App::import('Controller', 'Extranet');
            $extractrl = new ExtranetController();
			
			$dt = new DateTime(date('Y-m-d H:i:s'));
			$dt->modify('- 30 minutes');
			$date_min = $dt->format('Y-m-d H:i:s');
			$tab_d = explode(' ',$date_min);
			$tab_dd = explode('-', $tab_d[0]);
			$tab_ddd = explode(':', $tab_d[1]);
						
			$time = mktime($tab_ddd[0],$tab_ddd[1],$tab_ddd[2],$tab_dd[1],$tab_dd[2],$tab_dd[0]);
			
			$conditions = array(
				'CallInfo.timestamp >' => $time,
				'CallInfo.alert' => 0,
				'OR' => array(
					'CallInfo.accepted' => 'no',
					'CallInfo.reason' => array('NOANSWER','BUSY','CHANUNAVAIL','CANCEL')
				)
			);
			$calls = $this->CallInfo->find('all',array('conditions' => $conditions));
			foreach($calls as $call){
				if($call['CallInfo']['agent']){
					$conditions_test = array(
						'CallInfo.timestamp >' => $time,
						'CallInfo.agent' => $call['CallInfo']['agent'],
						'OR' => array(
							'CallInfo.accepted' => 'no',
							'CallInfo.reason' => array('NOANSWER','BUSY','CHANUNAVAIL','CANCEL')
						)
					);

					$call_check = $this->CallInfo->find('all',array('conditions' => $conditions_test));
					$datas = $this->User->find('first', array(
							'conditions' => array('agent_number' => $call['CallInfo']['agent']),
							'recursive' => -1
						));
					
					if(count($call_check) >= 1){
						$status = '';
						if($datas['User']['agent_status'] == 'available')$status = 'Dispo'; else $status = 'Non dispo';
						if($datas['User']['agent_status'] == 'busy')$status = 'Occupé ( en ligne )';
						$html = 'L\'appel '.$call['CallInfo']['sessionid']. ' vers agent : '.$datas['User']['pseudo'].'('.$call['CallInfo']['agent'].') a échoué au moins une fois.';
						$html .= '<br />Agent '.$status;
						
						$consult_phone = '';
						switch ($datas['User']['consult_phone']) {
							case 0:
								$consult_phone = 'non actif';
								break;
							case 1:
								$consult_phone = 'actif';
								break;
							case 2:
							case "-1":
								$consult_phone = 'bloqué';
								break;
						}
						$consult_chat = '';
						switch ($datas['User']['consult_chat']) {
							case 0:
								$consult_chat = 'non actif';
								break;
							case 1:
								$consult_chat = 'actif';
								break;
							case 2:
								$consult_chat = 'bloqué';
								break;
						}
						$consult_email = '';
						switch ($datas['User']['consult_email']) {
							case 0:
								$consult_email = 'non actif';
								break;
							case 1:
								$consult_email = 'actif';
								break;
							case 2:
								$consult_email = 'bloqué';
								break;
						}
						if($status == 'Dispo'){
							$html .= '<br />Mode Tel -> actif';
							$html .= '<br />Mode Chat -> '.$consult_chat;
							$html .= '<br />Mode Email -> '.$consult_email;
						}
						$html .= '<br />Appels manqués :<br />';
						foreach($call_check as $cc){
							$html .= 'Sessionid : '.$cc['CallInfo']['sessionid'].' à '.date('d/m/Y H:i:s', $cc['CallInfo']['timestamp']).'<br />';
						}
						
						
						//Les datas pour l'email
						$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Appel perdu URGENT','default',$datasEmail);
						
						$mysqli_connect->query("UPDATE call_infos SET alert = '1' WHERE callinfo_id = '{$call['CallInfo']['callinfo_id']}'");

					}
					
					if(count($call_check) >= 2 && $datas['User']['agent_status'] != 'busy'){
						//descative le mode tel de lexpert
						
						if($datas['User']['id']){
							$this->User->id = $datas['User']['id'];
							if($this->User->saveField('consult_phone', 0)){


								$this->sendCmsTemplateByMail(331, 1, $datas['User']['email'], array(
									'AGENT_PSEUDO' => $datas['User']['pseudo'],
									'MODE_DESACTIVE' => 'téléphone',
								));
								$this->sendCmsTemplateByMail(332, 1, 'contact@talkappdev.com', array(
									'AGENT_PSEUDO' => $datas['User']['pseudo'],
									'MODE_DESACTIVE' => 'téléphone',
								));


								$this->loadModel('UserConnexion');
								$consult_email = $this->User->field('consult_email', array('id' => $datas['User']['id']));
								$consult_chat = $this->User->field('consult_chat', array('id' => $datas['User']['id']));
								$agent_status = $this->User->field('agent_status', array('id' => $datas['User']['id']));
								$consult_phone = 0;

								if(!$consult_email && !$consult_chat && $agent_status != 'unavailable'){
									//plus de mode je passe indispo
									$this->User->saveField('agent_status', 'unavailable');
									$agent_status = 'unavailable';
								}

								$connexion = array(
										'user_id'          	=> $datas['User']['id'],
										'session_id'        => '',
										'date_connexion'    => date('Y-m-d H:i:s'),
										'date_lastactivity' => date('Y-m-d H:i:s'),
										'status'			=> $agent_status,
										'who'				=> '1',
										'mail'            	=> $consult_email,
										'tchat'      		=> $consult_chat,
										'phone'    			=> $consult_phone
									);
								$this->UserConnexion->create();
								$this->UserConnexion->save($connexion);

								$api = new Api();
								$result = $api->deconnectAgent($call['CallInfo']['agent']);
							}
						}
					}
				}
				
			}		
			
			//envoi mail alert agent
			 $conditions = array(
				 'CallInfo.status' => 0,
				'CallInfo.date_send' => NULL,
				'OR' => array(
					'CallInfo.accepted' => 'no',
					'CallInfo.reason' => array('NOANSWER','BUSY','CHANUNAVAIL','CANCEL')
				)
			);
			
			$calls = $this->CallInfo->find('all',array('conditions' => $conditions));
			
			foreach($calls as $call){
				$agent = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['CallInfo']['agent']),
					'recursive' => -1
				));
				if($call['CallInfo']['customer']){
					$client_sql = $this->User->find('first', array(
					'conditions' => array('User.personal_code' => $call['CallInfo']['customer']),
					'recursive' => -1
					));

					if($client_sql['User']['firstname']){
						$client = $client_sql['User']['firstname'];
					}else{
						$client = '';
					}
				}else{
					$client = 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
				}

				if($agent['User']['agent_status'] != 'busy'){
					
					$diff_second = $call['CallInfo']['time_stop'] - $call['CallInfo']['time_setstatut'];
					$penalty_id = 0;
					$penalty_cost = 0;
					$penalty = $this->Penality->find('all', array(
							'conditions' => array('Penality.type' => 'phone'),
							'recursive' => -1
					));
					foreach($penalty as $penalti){
						if($diff_second >= $penalti['Penality']['delay_min'] && $diff_second < $penalti['Penality']['delay_max']){
							$penalty_id = $penalti['Penality']['id'];
							$penalty_cost = $penalti['Penality']['cost'];
						}
					}
					
					$user_penalty = $this->UserPenality->find('first', array(
							'fields' => array('UserPenality.id'),
							'conditions' => array('UserPenality.callinfo_id' => $call['CallInfo']['callinfo_id']),
							'recursive' => -1
					));
					
					if($penalty_id && !$user_penalty){
						//save penalty
							$penaltyData = array();
							$penaltyData['penalities_id'] = $penalty_id;
							$penaltyData['user_id'] = $agent['User']['id'];
							$penaltyData['callinfo_id'] = $call['CallInfo']['callinfo_id'];
							$penaltyData['date_com'] = date('Y-m-d H:i:s',$call['CallInfo']['timestamp']);
							$penaltyData['delay'] = $diff_second;

							$penaltyData['penality_cost'] = $penalty_cost;
							$this->UserPenality->create();
						
						if($this->UserPenality->save($penaltyData)){
							date_default_timezone_set('Europe/Paris');
							$this->sendCmsTemplateByMail(319, 1, $agent['User']['email'], array(
									'PSEUDO_NAME_DEST' => $agent['User']['pseudo'],
									'PARAM_PSEUDO' => $agent['User']['pseudo'],
									'PARAM_CLIENT' => $client,
									'DATE_HEURE_CONSULTATION_PERDUE' => date('d-m-Y H',$call['CallInfo']['timestamp']).'h'.date('i',$call['CallInfo']['timestamp']).'min'.date('s',$call['CallInfo']['timestamp']).'s'
								));

							$mysqli_connect->query("UPDATE call_infos set date_send = NOW() WHERE callinfo_id = '{$call['CallInfo']['callinfo_id']}'");
							date_default_timezone_set('UTC');
						}
					}
				}
			}
			
			
			/* tchat cut */
			
			$conditions = array(
				'Chat.date_start >' => $date_min,
				'Chat.status' => 0,
				'Chat.date_send' => NULL,
				'Chat.consult_date_start' => NULL,
				'Chat.etat' => 1,
				'Chat.closed_by !=' => 'client_timeout',
				'Chat.date_end !=' => NULL,
			);
			
			$calls = $this->Chat->find('all',array('conditions' => $conditions));
			
			foreach($calls as $call){
				
				$date_end = new DateTime($call['Chat']['date_end']);
				$stamp_end =  $date_end->getTimestamp();
				$date_start = new DateTime($call['Chat']['date_start']);
				$stamp_start =  $date_start->getTimestamp();
				$diff = $stamp_end - $stamp_start;
				if($diff > 25){

					$lastmessageAgent = $this->Chat->ChatMessage->find('first', array(
							'fields' => array('date_add'),
							'conditions' => array('chat_id' => $call['Chat']['id'], 'user_id' =>$call['Chat']['to_id']),
							'order' => 'id desc',
							'recursive' => -1
						));

					if(!$lastmessageAgent){
						$agent = $this->User->find('first', array(
							'conditions' => array('User.id' => $call['Chat']['to_id']),
							'recursive' => -1
						));

						$client = $this->User->find('first', array(
							'conditions' => array('User.id' => $call['Chat']['from_id']),
							'recursive' => -1
						));


						$conditions_test = array(
							'Chat.date_start >' => $date_min,
							'Chat.to_id' => $agent['User']['id'],
							'Chat.status' => 0,
							'Chat.consult_date_start' => NULL,
							'Chat.etat' => 1,
							'Chat.closed_by !=' => 'client_timeout',
							'Chat.date_end !=' => NULL,
						);

						$call_check = $this->Chat->find('all',array('conditions' => $conditions_test));


						if(count($call_check) >= 2 && $this->User->field('consult_chat', array('id' => $agent['User']['id']))){
							//descative le mode tchat de lexpert

							if($agent['User']['id']){
								$this->User->id = $agent['User']['id'];
								$this->User->saveField('consult_chat', 0);


								$this->loadModel('UserConnexion');
								$consult_email = $this->User->field('consult_email', array('id' => $agent['User']['id']));
								$consult_chat = 0;
								$status = $this->User->field('agent_status', array('id' => $agent['User']['id']));
								$consult_phone = $this->User->field('consult_phone', array('id' => $agent['User']['id']));
								if(!$consult_email && !$consult_phone && $status != 'unavailable'){
									//plus de mode je passe indispo
									$this->User->saveField('agent_status', 'unavailable');
									$status = 'unavailable';
								}

								$connexion = array(
										'user_id'          	=> $agent['User']['id'],
										'session_id'        => '',
										'date_connexion'    => date('Y-m-d H:i:s'),
										'date_lastactivity' => date('Y-m-d H:i:s'),
										'status'			=> $status,
										'who'				=> '1',
										'mail'            	=> $consult_email,
										'tchat'      		=> $consult_chat,
										'phone'    			=> $consult_phone
									);
								$this->UserConnexion->create();
								$this->UserConnexion->save($connexion);

							}
						}
					}
				}
			}
			
			/* TCHAT CHECK*/
			$conditions = array(
				'Chat.date_start >' => $date_min,
				'Chat.status' => 0,
				'Chat.date_send' => NULL,
				'Chat.consult_date_start' => NULL,
				'Chat.etat' => 1,
				'Chat.closed_by !=' => 'client_timeout',
				'Chat.date_end !=' => NULL,
			);
			
			$calls = $this->Chat->find('all',array('conditions' => $conditions));
			
			foreach($calls as $call){
				$lastmessageAgent = $this->Chat->ChatMessage->find('first', array(
                        'fields' => array('date_add'),
                        'conditions' => array('chat_id' => $call['Chat']['id'], 'user_id' =>$call['Chat']['to_id']),
                        'order' => 'id desc',
                        'recursive' => -1
                    ));
				
				if(!$lastmessageAgent){
					$agent = $this->User->find('first', array(
						'conditions' => array('User.id' => $call['Chat']['to_id']),
						'recursive' => -1
					));

					$client = $this->User->find('first', array(
						'conditions' => array('User.id' => $call['Chat']['from_id']),
						'recursive' => -1
					));
					
					
					
					$this->sendCmsTemplateByMail(318, 1, $agent['User']['email'], array(
								'PSEUDO_NAME_DEST' => $agent['User']['pseudo'],
								'PARAM_PSEUDO' => $agent['User']['pseudo'],
								'PARAM_CLIENT' => $client['User']['firstname'],
								'DATE_HEURE_CONSULTATION_PERDUE' => CakeTime::format(Tools::dateUser('Europe/Paris',$call['Chat']['date_start']),'%d-%m-%Y %Hh%Mmin%Ss')
							));
					$mysqli_connect->query("UPDATE chats set date_send = NOW() WHERE id = '{$call['Chat']['id']}'");
					
					$date_start = new DateTime($call['Chat']['date_start']);
					$date_end = new DateTime($call['Chat']['date_end']);
					$diff_second = $date_end->getTimestamp() - $date_start->getTimestamp();
					$penalty_id = 0;
					$penalty_cost = 0;
					$penalty = $this->Penality->find('all', array(
							'conditions' => array('Penality.type' => 'tchat'),
							'recursive' => -1
					));
					foreach($penalty as $penalti){
						if($diff_second >= $penalti['Penality']['delay_min'] && $diff_second < $penalti['Penality']['delay_max']){
							$penalty_id = $penalti['Penality']['id'];
							$penalty_cost = $penalti['Penality']['cost'];
						}
					}
					
					$user_penalty = $this->UserPenality->find('first', array(
							'fields' => array('UserPenality.id'),
							'conditions' => array('UserPenality.tchat_id' => $call['Chat']['id']),
							'recursive' => -1
					));
					if($penalty_id && !$user_penalty){
						//save penalty
							$penaltyData = array();
							$penaltyData['penalities_id'] = $penalty_id;
							$penaltyData['user_id'] = $call['Chat']['to_id'];
							$penaltyData['tchat_id'] = $call['Chat']['id'];
							$penaltyData['date_com'] = $call['Chat']['date_start'];
							$penaltyData['delay'] = $diff_second;
							$penaltyData['penality_cost'] = $penalty_cost;

						$this->UserPenality->create();
						$this->UserPenality->save($penaltyData);
					}
					
					$conditions_test = array(
						'Chat.date_start >' => $date_min,
						'Chat.to_id' => $agent['User']['id'],
						'Chat.status' => 0,
						'Chat.consult_date_start' => NULL,
						'Chat.etat' => 1,
						'Chat.closed_by !=' => 'client_timeout',
						'Chat.date_end !=' => NULL,
					);

					$call_check = $this->Chat->find('all',array('conditions' => $conditions_test));
					
					
					if(count($call_check) >= 1){
						$status = '';
						if($agent['User']['agent_status'] == 'available')$status = 'Dispo'; else $status = 'Non dispo';
						
						$html = 'Le chat '.$call['Chat']['id']. ' vers agent : '.$agent['User']['pseudo'].'('.$agent['User']['agent_number'].') a échoué au moins une fois.';
						$html .= '<br />Agent '.$status;
						
						$consult_phone = '';
						switch ($agent['User']['consult_phone']) {
							case 0:
								$consult_phone = 'non actif';
								break;
							case 1:
								$consult_phone = 'actif';
								break;
							case 2:
							case "-1":
								$consult_phone = 'bloqué';
								break;
						}
						$consult_chat = '';
						switch ($agent['User']['consult_chat']) {
							case 0:
								$consult_chat = 'non actif';
								break;
							case 1:
								$consult_chat = 'actif';
								break;
							case 2:
								$consult_chat = 'bloqué';
								break;
						}
						$consult_email = '';
						switch ($agent['User']['consult_email']) {
							case 0:
								$consult_email = 'non actif';
								break;
							case 1:
								$consult_email = 'actif';
								break;
							case 2:
								$consult_email = 'bloqué';
								break;
						}
						if($status == 'Dispo'){
							$html .= '<br />Mode Tel -> '.$consult_phone;
							$html .= '<br />Mode Chat -> actif';
							$html .= '<br />Mode Email -> '.$consult_email;
						}
						$html .= '<br />Chats manqués :<br />';
						foreach($call_check as $cc){
							$html .= 'Chat ID : '.$cc['Chat']['id'].' à '.$cc['Chat']['date_start'].'<br />';
						}
						
						
						//Les datas pour l'email
						$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Chat perdu URGENT','default',$datasEmail);
						//$extractrl->sendEmail('system@web-sigle.fr','Chat perdu URGENT','default',$datasEmail);
						

					}
					if(count($call_check) >= 2){
						//descative le mode tel de lexpert
						
						$date_end = new DateTime($call['Chat']['date_end']);
						$stamp_end =  $date_end->getTimestamp();
						$date_start = new DateTime($call['Chat']['date_start']);
						$stamp_start =  $date_start->getTimestamp();
						$diff = $stamp_end - $stamp_start;
						if($diff > 25){
							if($agent['User']['id']){
								$this->User->id = $agent['User']['id'];
								$this->User->saveField('consult_chat', 0);
								$this->sendCmsTemplateByMail(331, 1, $agent['User']['email'], array(
									'AGENT_PSEUDO' => $agent['User']['pseudo'],
									'MODE_DESACTIVE' => 'chat',
								));
								$this->sendCmsTemplateByMail(332, 1, 'contact@talkappdev.com', array(
									'AGENT_PSEUDO' => $agent['User']['pseudo'],
									'MODE_DESACTIVE' => 'chat',
								));
							}
						}
					}
					
				}
				
			}
			
			
			//check agent just tchat inactif
			$agents = $this->User->find('all', array(
					'conditions' => array('User.role' => 'agent', 'User.active' => 1,'User.valid' => 1,'User.deleted' => 0, 'User.consult_email <=' => 0,'User.consult_phone <=' => 0,'User.consult_chat' => 1,'User.agent_status' => 'available','User.date_last_activity <' => date("Y-m-d H:i:s",(time() - Configure::read('Chat.maxTimeInactif')))),
					'recursive' => -1
				));
			foreach($agents as $agent){
				$this->User->id = $agent['User']['id'];

				
				if($this->User->saveField('agent_status', 'unavailable')){
				
					$this->sendCmsTemplateByMail(335, 1, $agent['User']['email'], array(
										'AGENT_PSEUDO' => $agent['User']['pseudo'],
									));
					/*$this->sendCmsTemplateByMail(335, 1, 'contact@talkappdev.com', array(
									'AGENT_PSEUDO' => $agent['User']['pseudo'],
								));*/
					
					
					$this->loadModel('UserConnexion');
					$consult_email = $this->User->field('consult_email', array('id' => $agent['User']['id']));
					$consult_chat = $this->User->field('consult_chat', array('id' => $agent['User']['id']));
					$status = $this->User->field('agent_status', array('id' => $agent['User']['id']));
					$consult_phone = $this->User->field('consult_phone', array('id' => $agent['User']['id']));
								
					$connexion = array(
										'user_id'          	=> $agent['User']['id'],
										'session_id'        => '',
										'date_connexion'    => date('Y-m-d H:i:s'),
										'date_lastactivity' => date('Y-m-d H:i:s'),
										'status'			=> $status,
										'who'				=> '1',
										'mail'            	=> $consult_email,
										'tchat'      		=> $consult_chat,
										'phone'    			=> $consult_phone
					);
					$this->UserConnexion->create();
					$this->UserConnexion->save($connexion);
					
				}
			}
			$mysqli_connect->close();
		}

		public function sendRelance(){
			
			$this->loadModel('Relance');
			$this->loadModel('User');
			$conditions = array(
				'Relance.date_relance' => date('Y-m-d 00:00:00'),
			);
			
			$relances = $this->Relance->find('all',array('conditions' => $conditions));
			$list_agent = array();
			foreach($relances as $relance){
				
				array_push($list_agent, $relance['Relance']['agent_id']);
			}
			$list_agent = array_unique($list_agent);
			
			foreach($list_agent as $agent_id){
				
				 $conditions = array(
							'User.id' => $agent_id,
						);
				 $user_mail = $this->User->find('first',array('conditions' => $conditions));
				 $test = $this->sendCmsTemplateByMail(312, $user_mail['User']['lang_id'], $user_mail['User']['email'], array(
							'PARAM_PSEUDO' => $user_mail['User']['pseudo'],

						));
			}
		}
		
		public function checkCostAgent(){
			$this->loadModel('CostAgent');
			$this->loadModel('User');
			
			App::import('Controller', 'Extranet');
            $extractrl = new ExtranetController();
			
			
			$conditions = array(
			);
			
			$costs = $this->CostAgent->find('all',array('conditions' => $conditions));
			
			foreach($costs as $cost){
				
				$conditions_test = array(
						'User.id' => $cost['CostAgent']['id_agent'],
					);
				$user = $this->User->find('first',array('conditions' => $conditions_test));
				$url = 'https://fr.spiriteo.com/admin/agents/view-'.$user['User']['id'];
				
				if($user['User']['order_cat'] && $user['User']['order_cat'] != $cost['CostAgent']['id_cost'] && $cost['CostAgent']['id_cost'] > $user['User']['order_cat']){
					
						$html = 'L\'agent '.$user['User']['pseudo']. ' a changé de catégorie rémunération => '.$user['User']['order_cat'].' to '.$cost['CostAgent']['id_cost']. ' a partir du'.date('d/m/Y H:i:s').'<br />';
					
						$html .= 'Vous n\'avez pas d\'action à mener, ce changement est automatique. Vous pouvez suivre cette modification sur la compte expert via le lien suivant : <br />';
						$html .= '<a target="_blank" href="'.$url.'">'.$url.'</a>';
						//Les datas pour l'email
						$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Agent remuneration','default',$datasEmail);
						$extractrl->sendEmail('system@web-sigle.fr','Agent remuneration','default',$datasEmail);
						$this->User->id = $user['User']['id'];
						$this->User->saveField('order_cat', $cost['CostAgent']['id_cost']);
				}
			}
			
		}
		
		public function checkNextCostAgent(){
			exit;
			$this->loadModel('Cost');
			$this->loadModel('CostAgent');
			$this->loadModel('User');
			
			App::import('Controller', 'Extranet');
            $extractrl = new ExtranetController();
			
			
			
			$conditions = array(
			);
			
			$costs = $this->CostAgent->find('all',array('conditions' => $conditions));
			
			foreach($costs as $cost){
				
				$conditions_test = array(
						'User.id' => $cost['CostAgent']['id_agent'],
					);
				$user = $this->User->find('first',array('conditions' => $conditions_test));
				
				$nb_minutes = $cost['CostAgent']['nb_minutes'] + 1;
				if($cost['CostAgent']['id_cost']<=4)
					$costmax = 4;
				else
					$costmax = 8;
				
				$conditions_test = array(
						'Cost.level <' => $nb_minutes,
						'Cost.id' => $cost['CostAgent']['id_cost'],
						'Cost.id <=' => $costmax,
					);
				$cost_test = $this->Cost->find('first',array('conditions' => $conditions_test));
				
				if(count($cost_test)){
					
					if($user['User']['order_cat'] != $cost_test['Cost']['id']){
						$conditions_test = array(
								'User.id' => $cost['CostAgent']['id_agent'],
							);
						$user = $this->User->find('first',array('conditions' => $conditions_test));
					
						$html = 'L\'agent '.$user['User']['pseudo']. ' va changer de catégorie rémunération.';
						//Les datas pour l'email
						$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Agent remuneration','default',$datasEmail);
						//$extractrl->sendEmail('system@web-sigle.fr','Agent remuneration','default',$datasEmail);
					}

				}
			}
			
		}
		
		public function sendQueueRelanceAgent(){
			return '';
		}
		public function alertCustomerCredit(){
			exit;
			$this->loadModel('User');
			$this->loadModel('UserCreditHistory');
		
			$seuil_credit = 100;
			$delai_delete = 365;//nb jours
			$delai_delete2 = 365;//nb jours
			$delai_alert = 358;//nb jours
			$delai_warning = 335;//nb jours
		    $delai_warning1 = 305;//nb jours
		
		
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai_alert.' days');
			$delai = $dx->format('Y-m-d H:i:s');
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai_warning.' days');
			$delai_warning = $dx->format('Y-m-d H:i:s');
		
		    $dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai_warning1.' days');
			$delai_warning1 = $dx->format('Y-m-d H:i:s');
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai_delete.' days');
			$delai_delete = $dx->format('Y-m-d H:i:s');
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai_delete2.' days');
			$date_mail_expire = $dx->format('d/m/Y H:i:s');
			
			$conditions = array(
								'User.credit >' => 0,
								'User.credit <=' => $seuil_credit,
			);
			$users = $this->User->find('all',array('conditions' => $conditions));
			
			foreach($users as $client){
				
				$conditions_search = array(
					'UserCreditHistory.date_start >=' => $delai_warning1,
				);
				$comms = $this->UserCreditHistory->find('all',array('conditions' => $conditions));
				
				if(!$comms){
					
						//Envoie de l'email
						$client['User']['email'] = 'contact@talkappdev.com';
						$is_send = $this->sendCmsTemplateByMail(395, (int)$client['User']['lang_id'], $client['User']['email'], array(
							  'PARAM_URLSITE' 			=>    'https://fr.spiriteo.com',
							 'PARAM_PSEUDO' 			=>   $agent['User']['pseudo'],
							'PARAM_CREDITS' =>$client['User']['credit'],
							'DATE_HEURE_FIN_CREDITS' => $date_mail_expire,
							'CLIENT' =>$client['User']['firstname']
						),true);
				}
				
				$conditions_search = array(
					'UserCreditHistory.date_start >=' => $delai_warning,
				);
				$comms = $this->UserCreditHistory->find('all',array('conditions' => $conditions));
				
				if(!$comms){
					
						//Envoie de l'email
						$client['User']['email'] = 'contact@talkappdev.com';
						$is_send = $this->sendCmsTemplateByMail(320, (int)$client['User']['lang_id'], $client['User']['email'], array(
							  'PARAM_URLSITE' 			=>    'https://fr.spiriteo.com',
							 'PARAM_PSEUDO' 			=>   $agent['User']['pseudo'],
							'PARAM_CREDITS' =>$client['User']['credit'],
							'DATE_HEURE_FIN_CREDITS' => $date_mail_expire,
							'CLIENT' =>$client['User']['firstname']
						),true);
				}
				
				$conditions_search = array(
					'UserCreditHistory.date_start >=' => $delai,
				);
				$comms = $this->UserCreditHistory->find('all',array('conditions' => $conditions));
				
				if(!$comms){
					
						//Envoie de l'email
						$client['User']['email'] = 'contact@talkappdev.com';
						$is_send = $this->sendCmsTemplateByMail(321, (int)$client['User']['lang_id'], $client['User']['email'], array(
							  'PARAM_URLSITE' 			=>    'https://fr.spiriteo.com',
							 'PARAM_PSEUDO' 			=>   $agent['User']['pseudo'],
							'PARAM_CREDITS' =>$client['User']['credit'],
							'DATE_HEURE_FIN_CREDITS' => $date_mail_expire,
							'CLIENT' =>$client['User']['firstname']
						),true);
				}
				
				$conditions_search = array(
					'UserCreditHistory.date_start >=' => $delai_delete,
				);
				$comms = $this->UserCreditHistory->find('all',array('conditions' => $conditions));
				
				if(!$comms){
					 $this->User->id = $client['User']['id'];
                     $this->User->saveField('credit_old', $client['User']['credit']);
					$this->User->saveField('credit', 0);
				}
			}
		}
		
		public function relanceReviews(){
			$this->loadModel('User');
			$this->loadModel('UserCreditHistory');
			$this->loadModel('Review');

			$delai = 21;//nb jours


			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- '.$delai.' days');
			$delai = $dx->format('Y-m-d');
			$delai_min = $delai.' 00:00:00';
			$delai_max = $delai.' 23:59:59';

			$conditions_search = array(
				'UserCreditHistory.date_start >=' => $delai_min,
				'UserCreditHistory.date_start <=' => $delai_max,
				'UserCreditHistory.user_id !=' => 286,
				'UserCreditHistory.user_id !=' => 3630,
				'UserCreditHistory.user_id !=' => 3631,
				'UserCreditHistory.user_id !=' => 3632,
				'UserCreditHistory.user_id !=' => 3633,
				'UserCreditHistory.user_id !=' => 3634,
				'UserCreditHistory.user_id !=' => 3635,
				'UserCreditHistory.user_id !=' => 3636,
				'UserCreditHistory.user_id !=' => 3637,
				'UserCreditHistory.user_id !=' => 3638,
				'UserCreditHistory.user_id !=' => 29837
			);

			$list_comm = array();
			$list_bad_user = array(286,3630,3631,3632,3633,3634,3635,3636,3637,3638,29837);

			
			
			$comms = $this->UserCreditHistory->find('all',array('conditions' => $conditions_search));

			foreach($comms as $comm){
				$ids = $comm['UserCreditHistory']['user_id'].'_'.$comm['UserCreditHistory']['agent_id'];

				//check si pas de reviews deposé entre tps
				/*$conditions = array(
					'Review.agent_id' => $comm['UserCreditHistory']['agent_id'],
					'Review.user_id' => $comm['UserCreditHistory']['user_id'],
					'Review.date_add >=' => $delai_min,
					'Review.status' => 1,
					'Review.parent_id' => NULL
				);
				$review_find = $this->Review->find('first',array('conditions' => $conditions));*/

				if(!in_array($ids,$list_comm) && !in_array($comm['UserCreditHistory']['user_id'],$list_bad_user)){//&& !$review_find
					array_push($list_comm,$ids.'#'.$comm['UserCreditHistory']['date_start']);
				}
			}

			foreach($list_comm as $comm){

				$exp = explode('#',$comm);
				$exp2 = explode('_',$exp[0]);
				$user_id = $exp2[0];
				$agent_id = $exp2[1];
				$date_com = $exp[1];

				$dx = new DateTime($date_com);
				$date_mail = $dx->format('d/m/Y H:i:s');

				$conditions = array(
					'User.id' => $user_id,
				);
				$client = $this->User->find('first',array('conditions' => $conditions));

				$conditions = array(
					'User.id' => $agent_id,
				);
				$agent = $this->User->find('first',array('conditions' => $conditions));

				//Envoie de l'email
				$is_send = $this->sendCmsTemplateByMail(323, (int)$client['User']['lang_id'], $client['User']['email'], array(
								  'PARAM_URLSITE' 			=>    'https://fr.spiriteo.com',
								 'PARAM_PSEUDO' 			=>   $agent['User']['pseudo'],
								'AGENT_PSEUDO' =>$agent['User']['pseudo'],
								'DATE_COM' => $date_mail,
								'REVIEW_LINK' => 'accounts/review'
							),true);
			}
		}
		
	public function generatePhotoFBAgent(){
		
		set_time_limit ( 0 );
		ini_set("memory_limit",-1);
		
		$this->loadModel('User');
		$this->loadModel('Review');
		$this->loadModel('CategoryUser');
		
		$dir_webroot = str_replace('Controller','webroot',dirname(__FILE__)); 

		$radius = 100;
		$radius_min = 50;
		
		$font_title = './Roboto-Black.ttf';
		$font_txt = './Roboto-Regular.ttf';
		$font_bold = './Roboto-Medium.ttf';
		
		$image_bg = imagecreatefromjpeg($dir_webroot.'/bg-fb.jpg');
		$image_stars = imagecreatefromjpeg($dir_webroot.'/fb-star.jpg');
		$image_point = imagecreatefromjpeg($dir_webroot.'/fb-point.jpg');
		
		$image_logo_min = imagecreatefromjpeg($dir_webroot.'/top_min.jpg');
		$image_stars_min = imagecreatefromjpeg($dir_webroot.'/stars_bg.jpg');
		
		$conditions = array(
				'User.role' => 'agent',
				'User.valid' => '1',
				'User.active' => '1',
		);
		
		$agents = $this->User->find('all',array('conditions' => $conditions));
		
		foreach($agents as $agent){
			
			//recup category de l agent
			$id_lang = 1;
			$categoryLangs = $this->CategoryUser->find('all',array(
				'fields' => array('CategoryLang.category_id', 'CategoryLang.name', 'CategoryLang.link_rewrite'),
				'conditions' => array('CategoryUser.user_id' => $agent['User']['id']),
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
		
			$agent_number = $agent['User']['agent_number'];
			$folder = $dir_webroot.'/'.Configure::read('Site.pathPhoto').DS.$agent_number[0].DS.$agent_number[1].DS;
			$img_fb_name = $agent_number.'_fb.jpg';
			$img_min_fb_name = $agent_number.'_fb_min.jpg';
			
			/*if(count($categoryLangs) < 6){
				$hauteur = 246;
			}
			if(count($categoryLangs) == 6){
				$hauteur = 266;
			}
			if(count($categoryLangs) > 6){
				$hauteur = 276;
			}*/
			$hauteur = 315;
			$img_final = imagecreatetruecolor(600, $hauteur);
			$img_final_min = imagecreatetruecolor(200, 200);
			$white = imagecolorallocate($img_final, 255, 255, 255);
			$white = imagecolorallocate($img_final_min, 255, 255, 255);
			$aubergine = imagecolorallocate($img_final, 147, 60, 143);//933c8f
			$gris = imagecolorallocate($img_final, 118, 119, 136);//767788
			$dark = imagecolorallocate($img_final, 66, 66, 76);//767788
			$noir = imagecolorallocate($img_final, 0, 0, 0);//767788
			$noir = imagecolorallocate($img_final_min, 0, 0, 0);//767788
			imagefill($img_final, 0, 0, $white);
			imagefill($img_final_min, 0, 0, $white);

			$image_agent = imagecreatefromjpeg($folder.$agent_number.'.jpg');
			$image_agent2 = imagecreatetruecolor(150, 150);
			imagecopyresampled($image_agent2, $image_agent, 0, 0, 0, 0, 150, 150, 190, 190);
			$radius = 77;
			$image_agent = $this->imageRadius($image_agent2, $radius);
			$image_agent_min = imagecreatefromjpeg($folder.$agent_number.'_listing.jpg');
			$image_agent_min = $this->imageRadius($image_agent_min, $radius_min);

			imagecopymerge($img_final, $image_bg, 0, 0, 0, 0, 600, 315, 100);
			imagecopymerge($img_final_min, $image_logo_min, 0, 0, 0, 0, 200, 53, 100);
			imagecopymerge($img_final, $image_agent, 30, 120, 0, 0, 150, 150, 100);
			imagecopymerge($img_final_min, $image_agent_min, 5, 70, 0, 0, 95, 95, 100);
			imagecopymerge($img_final, $image_stars, 196, 275, 0, 0, 113, 26, 100);//66-17
			imagecopymerge($img_final_min, $image_stars_min, 110, 130, 0, 0, 78, 20, 100);//66-17
			/*imagecopymerge($img_final, $image_mode, 479, 79, 0, 0, 123, 197, 100);*/
			
			$conditions_r = array(
				'Review.agent_id' => $agent['User']['id'],
				'Review.status' => '1',
				'Review.parent_id' => NULL,
			);

			$reviews = $this->Review->find('all',array('fields' => array('AVG(Review.pourcent) as pourcent'), 'conditions' => $conditions_r));
			
			$pourcent = number_format($reviews[0][0]['pourcent'],1);
			if(intval($pourcent)){
			$string = $pourcent.'% Avis positifs';
			imagettftext($img_final, 13, 0, 200, 270, $white, $font_txt, $string);
			$string = $pourcent.'%';
			imagettftext($img_final_min, 11, 0, 130, 100, $noir, $font_title, $string);
			$string = 'Avis positifs';
			imagettftext($img_final_min, 11, 0, 110, 120, $noir, $font_title, $string);
			}
			$string = strtoupper($agent['User']['pseudo']);
			imagettftext($img_final, 17, 0, 200, 135, $white, $font_title, $string);
			imagettftext($img_final_min, 13, 0, 10, 185, $dark, $font_title, $string);
			$y_text = 165;
			$y_picto = 157;
			$limit_cat = 4;
			$n_cat = 1;
			foreach($categoryLangs as $category){
				imagecopymerge($img_final, $image_point, 200, $y_picto , 0, 0, 5, 5, 100);
				$string = $category['CategoryLang']['name'];
				imagettftext($img_final, 13, 0, 210, $y_text , $white, $font_bold, utf8_encode($string));
				
				$y_picto = $y_picto + 25;
				$y_text = $y_text + 25;
				$n_cat ++;
				if($n_cat > 4)break;
			}
			imagejpeg($img_final,$folder.$img_fb_name, 100);
			imagejpeg($img_final_min,$folder.$img_min_fb_name, 100);
			imagedestroy($img_final);
			imagedestroy($img_final_min);
			imagedestroy($image_agent);
			imagedestroy($image_agent_min);
		}
		imagedestroy($image_bg);
		imagedestroy($image_logo_min);
		//imagedestroy($image_mode);
		imagedestroy($image_stars);
		imagedestroy($image_stars_min);
		imagedestroy($image_point);
	}
		
	public function imageRadius($img, $radius){
		$radius = 2*$radius;
		$rectangle_h = $radius;
		$rectangle_w = $radius;
		$img_w = ImageSX ($img);
		$img_h = ImageSY ($img);
		$rectangle = imagecreatetruecolor($rectangle_w, $rectangle_h);
		$red = imagecolorallocate($rectangle, 255, 0, 0);
		$black = imagecolorallocate($rectangle, 0, 0, 0);
		imagefilledrectangle($rectangle, 0, 0, $radius, $radius, $red);
		imagefilledellipse($rectangle, ($radius-1)/2, ($radius-1)/2, $radius, $radius, $black);
		imagecolortransparent($rectangle, $black);
		imagecopymerge($img, $rectangle, 0, 0, 0, 0, $rectangle_w/2, $rectangle_h/2, 100);//des_x des_y src_x src_y w, h, opacity
		imagecopymerge($img, $rectangle, $img_w-$rectangle_w/2, 0, ($rectangle_w-1)/2, 0, $rectangle_w/2, $rectangle_h/2, 100);
		imagecopymerge($img, $rectangle, 0, $img_h-$rectangle_h/2, 0, $rectangle_h/2, $rectangle_w/2, $rectangle_h/2, 100);
		imagecopymerge($img, $rectangle, $img_w-$rectangle_w/2, $img_h-$rectangle_h/2, $rectangle_w/2, $rectangle_h/2, $rectangle_w/2, $rectangle_h/2, 100);
		imagecolortransparent($img, $red);
		return $img;
	}
		
	public function unlockSponsorship(){
		$this->loadModel('User');
		$this->loadModel('Sponsorship');
		
		$sponsorships = $this->Sponsorship->find('all', array(
							'conditions'    => array('Sponsorship.type_user' => 'client', 'Sponsorship.is_recup' => 0, 'Sponsorship.is_alert' => 0,'Sponsorship.status' => 3),
							'recursive'     => -1
						));
		foreach($sponsorships as $sponsor){
			$this->Sponsorship->updateAll(array('is_alert'=>1), array('Sponsorship.id' => $sponsor['Sponsorship']['id']));
			$condition_user = array(
								'User.id' =>$sponsor['Sponsorship']['user_id'],
								);
			$user = $this->User->find('first',array('conditions' => $condition_user));
			
			$this->sendCmsTemplateByMail(334, (int)$user['User']['lang_id'], $user['User']['email'], array(
							  'USER_FIRSTNAME' 	=>   $user['User']['firstname'],
						),true);
		}
	}
		
	public function sendSponsorship(){
		
		$this->loadModel('User');
		$this->loadModel('Domain');
		$this->loadModel('Sponsorship');	
        $this->loadModel('SponsorshipRule');   
		
			
		$conditions = array(
								'User.id' => 317,
		);
		$agent = $this->User->find('first',array('conditions' => $conditions));
			
		$conditions = array(
								'SponsorshipRule.type_user' => 'agent',
		);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));

		$list_email = array();
		if(is_array($list_email)){
			foreach($list_email as $email){
				if($email){
					//verifier si email pas present
					$conditions = array(
								'User.email' => $email
					);
					$is_client = $this->User->find('first',array('conditions' => $conditions));
					$conditions = array(
								'Sponsorship.email' => $email
					);
					$is_sponsor_send = $this->Sponsorship->find('first',array('conditions' => $conditions));
						
					if($is_client){
							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 10;
							$saveData['Sponsorship']['hash'] = '';

							$this->Sponsorship->save($saveData);
					}
						
						
					if(!$is_client && !$is_sponsor_send){
						$hash =  $this->crypter($email);
							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 0;
							$saveData['Sponsorship']['hash'] = $hash;

							$this->Sponsorship->save($saveData);

							$url = 'https://fr.spiriteo.com/sponsorship/parrainage-'.$hash;
						
							$conditions = array(
								'Domain.id' => $agent['User']['domain_id'],
							);

							$domain = 'www.talkappdev.com';
							
							$url_pixel_view = '<img src="https://'.$domain.'/sponsorship/track?i='.$this->Sponsorship->id.'" />';

							$is_send = $this->sendCmsTemplatePublic(327, (int)$agent['User']['lang_id'], $email, array(
									'AGENT' =>$agent['User']['pseudo'],
									'URL' =>$url,
									'PIXEL' =>$url_pixel_view,
									'PSEUDO_EXPERT_PARRAIN' => $agent['User']['pseudo']
								));
					}
				}
				sleep(10);
			}
		}
	}
		
	protected function crypter($maChaineACrypter){
		$maCleDeCryptage = md5('ssponsor');
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

	
	public function base64url_encode($data) { 
	  return rtrim(strtr(base64_encode($data), '+_', '-|'), '='); 
	} 

	public function notEnoughtExpert(){
		
		$send_mail = 0;
		$send_sms = 0;
		$ratio = 3;
		$debug = 0;
		
		//8h30 a 23h ratio a 3 par sms
		//23h a minuit ratio 2 par sms
		//minuit a 8h ratio 2 par mail
		date_default_timezone_set('Europe/Paris');
		$heure = date('G');
		$minute = date('i');
		if($heure >= 19 && $heure < 24){
			$debug = 1;
		}
		
		if($heure >= 23 && $heure < 24){
			$ratio = 2;
		}
		if($heure >= 0 && $heure <= 8){
			$ratio = 2;
		}
		if($heure >= 0 && $heure <= 3){
			$debug = 1;
		}
		/*if($heure == 8 && $minute <= 30){
			$ratio = 2;
		}*/
		
		if($heure >= 0 && $heure < 8){
			$send_mail = 1;
			$send_sms = 0;
		}
		if($heure >= 8 && $heure < 24){
			$send_mail = 1;
			$send_sms = 1;
		}
		
		date_default_timezone_set('UTC');
		
		$this->loadModel('User');
		$this->loadModel('Chat');
		$this->loadModel('UserCreditLastHistory');
		//$this->loadModel('UserConnexion');
		$dt = new DateTime(date('Y-m-d H:i:s'));
		$dt->modify('- 1 minutes');
		$date_min = $dt->format('Y-m-d H:i:s');
		
		$contact_expert = array();
		
		//recup nb expert dispo en tchat ou tel
		$agents_connected = $this->User->find("all", array(
					'fields'     => array('User.id','User.consult_chat','User.consult_phone'),
					 'conditions' => array('role'=>'agent','active'=>1,'valid'=>1,'deleted'=>0,'agent_status'=>'available',										   'OR'=> array('consult_phone' => 1,'consult_chat = 1 and date_last_activity >= \''.$date_min.'\'')
									),
					'recursive' => -1,
					'group' => array('User.id')
                )
            );
		
		$fakeagent = array(318,339,340,341,342,343,344,345,346,347,348,380,384,390,403,423,442,443,464,469,484);
		$agents_busy = $this->User->find("all", array(
					'fields'     => array('User.id','User.consult_chat','User.consult_phone'),
					 'conditions' => array('role'=>'agent','active'=>1,'valid'=>1,'deleted'=>0,'agent_status'=>'busy'),												
					'recursive' => -1
                )
            );
		foreach($agents_busy as $k=>$i){
			if(in_array($i['User']['id'],$fakeagent))unset($agents_busy[$k]);
		}
		
		//multiplié avec le ratio
		$nb_expert_be_connected = count($agents_busy) * $ratio;
		if($nb_expert_be_connected > 21)$nb_expert_be_connected = 21;
		
		if((count($agents_connected)) < $nb_expert_be_connected){// + count($agents_busy) 
			
			App::import('Vendor', 'Noox/Api');
			$this->loadModel('SmsHistory');
			$this->loadModel('Message');
			
			
			$content = 'Ratio : '.$ratio.'<br />';
			$content .= 'Nombre d expert dispo : '.count($agents_connected).'<br />';
			$content .= 'Nombre d expert occupés : '.count($agents_busy).'<br />';
			
			$nb_tchat = 0;
			$nb_tel  = 0;
			$nb_combi =0;
			foreach($agents_connected as $agent){
				if($agent['User']['consult_chat'] && $agent['User']['consult_phone']){
					$nb_combi ++;
				}else{
					if($agent['User']['consult_chat']){
						$nb_tchat ++;
					}else{
						$nb_tel ++;
					}
				}
			}
			$content .= 'Nombre expert dispo par Tel et tchat additionné : '.$nb_combi.'<br />';
			$content .= 'Nombre expert dispo par Tel uniquement : '.$nb_tel.'<br />';
			$content .= 'Nombre expert dispo par Chat uniquement : '.$nb_tchat.'<br />';
			
			$agents_nconnected = $this->User->find("all", array(
					'fields'     => array('User.id','User.agent_number','User.email','User.pseudo','User.phone_number', 'User.phone_mobile','User.lang_id','User.alert_sms','User.alert_mail','User.alert_night'),
					 'conditions' => array('role'=>'agent','active'=>1,'valid'=>1,'deleted'=>0,'agent_status'=>'unavailable'),
					'recursive' => -1,
					'order' => array('User.pseudo ASC'),
                )
            );
			$agents_justmail = $this->User->find("all", array(
					'fields'     => array('User.id','User.agent_number','User.email','User.pseudo','User.phone_number', 'User.phone_mobile','User.lang_id', 'User.alert_sms','User.alert_mail','User.alert_night'),
					 'conditions' => array('role'=>'agent','active'=>1,'valid'=>1,'deleted'=>0,'agent_status'=>'available','consult_chat'=>'0','consult_email'=>'1','consult_phone'=>'0'),
					'recursive' => -1,
					'order' => array('User.pseudo ASC'),
                )
            );
			$agents_nconnected = array_merge($agents_nconnected, $agents_justmail);
				foreach($agents_nconnected as $k=>$i){
				if(in_array($i['User']['id'],$fakeagent))unset($agents_nconnected[$k]);
			}
			
			//filtre
			$content .= '<br />Agents a avertir :<br /><br />';
			foreach($agents_nconnected as $agent){
				if($agent['User']['pseudo'])
				$content .= $agent['User']['pseudo']. '('.$agent['User']['agent_number'].')'.'<br />';
			}
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 30 minutes');
			$date_comp = $dx->format('Y-m-d H:i:s');
			
			if($send_sms){
				$txt = "Alerte : Nous vous invitons a vous connecter des a present sur Spiriteo pour beneficier des demandes de consultation en hausse actuellement.";
				
				$num_done = array();
				
				foreach($agents_nconnected as $agent){
					//if($agent['User']['agent_number'] == 3356 || $agent['User']['agent_number'] == 5029 || $agent['User']['agent_number'] == 6150){
					if($agent['User']['alert_sms']){
						$numero = $agent['User']['phone_mobile'];
						
						$check = $this->SmsHistory->find('first', array(
							 'fields'=>array('SmsHistory.id'),
							'conditions' => array('SmsHistory.id_agent' => $agent['User']['id'],'SmsHistory.date_add >=' => $date_comp),
							'recursive' => -1
						));
						
						
						if($numero && !$check && !in_array($numero,$num_done)){
							array_push($num_done,$numero);
							$txtLength = strlen($txt);
							$api = new Api();
							$result = $api->sendSms($numero, base64_encode($txt));	

							$history = array(
									'id_agent'          => $agent['User']['id'],
									'id_client'         => '',
									'id_tchat'         => '',
									'id_message'         => '',
									'email'             => 'SMS',
									'phone_number'      => $numero,
									'content_length'    => $txtLength,
									'content'    		=> $txt,
									'send'              => ($result > 0)?1:0,
									'date_add'          => date('Y-m-d H:i:s'),
									'type'				=> 'ALERTE EXPERT',
									'cost'				=> $result
							);

							//On save dans l'historique
							$this->SmsHistory->create();
							$this->SmsHistory->save($history);
						}
					}
					//}
				}
				
			}
			if($send_mail){
				$txt_mail = 'Bonjour cher Expert,

En raison d\'une hausse actuelle des demandes de consultations sur cette tranche horaire, nous vous invitons à vous connecter et vous rendre disponible dès à présent sur Spiriteo afin de bénéficier de cette forte demande.  

Cordialement,
L\'équipe Spiriteo
';
				$mail_done = array();
				foreach($agents_nconnected as $agent){
					//if($agent['User']['agent_number'] == 3356 || $agent['User']['agent_number'] == 5029 || $agent['User']['agent_number'] == 6150){
					if($heure >= 0 && $heure < 8){
						$sendmail = $agent['User']['alert_night'];
					}else{
						$sendmail = $agent['User']['alert_mail'];
					}
					
					
					if($sendmail && !in_array($agent['User']['email'],$mail_done)){	
						array_push($mail_done,$agent['User']['email']);
						$check = $this->Message->find('first', array(
							 'fields'=>array('Message.id'),
							'conditions' => array('Message.to_id' => $agent['User']['id'],'Message.from_id' => 1,'Message.date_add >=' => $date_comp,'Message.deleted' => 1),
							'recursive' => -1
						));
						
						if(!$check){
							$this->Message->create();
							if($this->Message->save(array(
										'from_id' => 1,
										'to_id' => $agent['User']['id'],
										'content' => nl2br($txt_mail),
										'private' => 1,
										'deleted' => 1,
										'etat' => 1,
										'archive' => 1,
										'admin_read_flag' => 1
									))){
								$this->User->id = $agent['User']['id'];
								$client = $this->User->read();
								$this->sendCmsTemplateByMail(384, $agent['User']['lang_id'], $agent['User']['email'], array(
									));
							}
						}
					}
				}
			}
			
			
			//$this->sendEmail('system@web-sigle.fr', 'Alerte Experts manquants', 'default', array('content' => $content, 'PARAM_URLSITE'=>'https://fr.spiriteo.com'));
			$this->sendEmail('degrefinance@gmail.com', 'Alerte Experts manquants', 'default', array('content' => $content, 'PARAM_URLSITE'=>'https://fr.spiriteo.com'));
			$this->sendEmail('cathyproche@gmail.com', 'Alerte Experts manquants', 'default', array('content' => $content, 'PARAM_URLSITE'=>'https://fr.spiriteo.com'));
		}else{
			
			
			/*if($debug){
				$this->sendEmail('system@web-sigle.fr', 'Alerte Experts manquants', 'default', array('content' =>'ratio : '.$ratio. '<br />expert dispo :' .count($agents_connected) .' <br >Expert busy :'.count($agents_busy).' <br >soit '. $nb_expert_be_connected.' expert souhaité dispo ', 'PARAM_URLSITE'=>'https://fr.spiriteo.com')); 
				$this->sendEmail('cathyproche@gmail.com', 'Alerte Experts manquants', 'default', array('content' => 'ratio : '.$ratio. '<br />expert dispo :' .count($agents_connected) .' <br >Expert busy :'.count($agents_busy).' <br >soit '. $nb_expert_be_connected.' expert souhaité dispo ', 'PARAM_URLSITE'=>'https://fr.spiriteo.com')); 
			}*/
		}
		
		//manque expert tchat
		$agents_tchat = $this->User->find("count", array(
					'fields'     => array('User.id'),
					 'conditions' => array('role'=>'agent','active'=>1,'valid'=>1,'deleted'=>0,'agent_status'=>'available','consult_chat'=>'1'),
					'recursive' => -1,
                )
            );
		if(!$agents_tchat && $send_sms){
			$this->sendEmail('system@web-sigle.fr', 'Alerte Pas Experts dispo Tchat', 'default', array('content' =>'Aucun expert dispo en tchat le '.date('d/m/Y H:i:s'), 'PARAM_URLSITE'=>'https://fr.spiriteo.com')); 
			$this->sendEmail('cathyproche@gmail.com', 'Alerte Pas Experts dispo Tchat', 'default', array('content' =>'Aucun expert dispo en tchat le '.date('d/m/Y H:i:s'), 'PARAM_URLSITE'=>'https://fr.spiriteo.com')); 
			$this->sendEmail('degrefinance@gmail.com', 'Alerte Pas Experts dispo Tchat', 'default', array('content' =>'Aucun expert dispo en tchat le '.date('d/m/Y H:i:s'), 'PARAM_URLSITE'=>'https://fr.spiriteo.com')); 
		}
	}
		
	public function getExports(){
		ini_set("memory_limit",-1);
		set_time_limit ( 0 );
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
		
		$listing_utcdec = Configure::read('Site.utcDec');
		
		/*$utc_dec = 1;
		$cut = explode('-',date('Y-m-d') );
		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
			$utc_dec = 2;*/
		
		
		$dx = new DateTime(date('Y-m-d 00:00:00'));
		$dx->modify('- 1 days');
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$period_jour = $dx->format('Y-m-d H:i:s');
		
		$dx2 = new DateTime(date('Y-m-d 23:59:59'));
		$dx2->modify('- 1 days');
		$dx2->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$period_jour2 = $dx2->format('Y-m-d H:i:s');
		
		
		$list_period = array();
	
		//periode du jour
		$dx = new DateTime($period_jour);
		$period_jour_begin = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime($period_jour2);
		$period_jour_end = $dx->format('Y-m-d H:i:s');
		array_push($list_period,$period_jour_begin. '_'.$period_jour_end);

		//periode semaine
		if(date('w',strtotime($period_jour)) == 1){
			$period_semaine_begin = date('Y-m-d  00:00:00', strtotime($period_jour));
			$dx = new DateTime($period_semaine_begin);
			$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
			$period_semaine_begin = $dx->format('Y-m-d H:i:s');
		}else{
			$period_semaine_begin = date('Y-m-d  00:00:00', strtotime('last monday', strtotime($period_jour)));
			$dx = new DateTime($period_semaine_begin);
			$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
			$period_semaine_begin = $dx->format('Y-m-d H:i:s');
		}

		$period_semaine_end = date('Y-m-d 23:59:59', strtotime('next sunday', strtotime($period_semaine_begin)));
		$dx = new DateTime($period_semaine_end);
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$period_semaine_end = $dx->format('Y-m-d H:i:s');
		array_push($list_period,$period_semaine_begin. '_'.$period_semaine_end);

		//periode mois
		$dx = new DateTime($period_jour2);
		$period_mois_begin = $dx->format('Y-m-01 00:00:00');
		$dx = new DateTime($period_mois_begin);
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$period_mois_begin = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime($period_jour2);
		$dx->modify('last day of this month');
		$period_mois_end = $dx->format('Y-m-d 23:59:59');
		$dx = new DateTime($period_mois_end);
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$period_mois_end = $dx->format('Y-m-d H:i:s');
		array_push($list_period,$period_mois_begin. '_'.$period_mois_end);
		

		//TRAITEMENT STATS AGENT DASHBOARD
		$result = $mysqli->query("SELECT * from users WHERE role = 'agent' and active = 1 and valid = 1 and deleted = 0 order by id");
		while($row = $result->fetch_array(MYSQLI_ASSOC)){

			foreach($list_period as $period){

				$dd = explode('_',$period);
				$min =$dd[0];
				$max =$dd[1];

				$dx1 = new DateTime($min);
				$dx2 = new DateTime($max);
				$jdelai=$dx1->diff($dx2)->days; 
				if(!$jdelai)$jdelai = 1;
				

				$note='';$presence='';$decroche='';$transformation='';$tmc='';$tmc_global='';$email='';$tchat='';$tel='';

				//check si agent travailler cette periode 
				//$result_check = $mysqli->query("SELECT user_credit_last_history from user_credit_last_histories WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");
				//$row_check = $result_check->fetch_array(MYSQLI_ASSOC);	
				//if($row_check['user_credit_last_history']){

					//note
					$result_s = $mysqli->query("SELECT count(*) as nb_review, avg(pourcent) as total_review from reviews WHERE agent_id = '".$row['id']."' and date_add <= '".$max."' and parent_id is NULL and status = 1");
					$row_s = $result_s->fetch_array(MYSQLI_ASSOC);	

					$n_reviens = $row_s['nb_review'];
					$total_review = $row_s['total_review'];
					if($n_reviens){
						$note = number_format($total_review,1);
					}else{
						$note = 0;	
					}

					//presence
					$result_s = $mysqli->query("SELECT * from user_state_history WHERE user_id = '".$row['id']."' and date_add >= '".$min."' and date_add <= '".$max."' order by date_add");
				
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
							if(!empty($tran) && !empty($tran->end))
							$tt->end = $tran->end;//date('Y-m-d H:i:s');
						else
							$tt->end = date('Y-m-d H:i:s');
							array_push($tranches_co,$tt);				
					}

					$tranches = array();
					foreach($tranches_co as $tran){
				
						$result_s = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and date_connexion >= '".$tran->begin."' and date_connexion <= '".$tran->end."' order by id");

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


							/*if($row_s['status'] == 'available'){
								$tranche_begin = $row_s['date_connexion'];
								$tranche_end = '';
							}
							if($row_s['status'] == 'unavailable'){
								if(!$tranche_begin)$tranche_begin = $min;
								$tranche_end = $row_s['date_connexion'];

								$tt = new stdClass();
								$tt->begin = $tranche_begin;
								$tt->end = $tranche_end;
								array_push($tranches,$tt);				
								$tranche_begin = '';
							}*/
							
							$last_tranche_end = $row_s['date_connexion'];
						}
						/*if(!$tranche_begin && !$tranche_end){
							$tt = new stdClass();
							$tt->begin = $min;
							$tt->end = $max;
							array_push($tranches,$tt);				
						}
						if($tranche_begin && !$tranche_end){
							$tranche_end = $max;
							$tt = new stdClass();
							$tt->begin = $tranche_begin;
							$tt->end = $tranche_end;
							array_push($tranches,$tt);				
						}*/
					}
				
					if($in_live && $tranche_begin && !$tranche_end){
						$tt = new stdClass();
						$tt->begin = $tranche_begin;
						$tt->end = $last_tranche_end;
						array_push($tranches,$tt);				
						$tranche_begin = '';
						$tranche_end = '';
					}
				
					$connexion_max = $jdelai * 24 * 60 * 60; //mettre un delta heures a enlever
					$connexion_min = 0;


					foreach($tranches as $periode){

						/*$result_ss = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and (tchat = 1 OR phone = 1) and date_connexion >= '".$periode->begin."' and date_connexion <= '".$periode->end."'");

						while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){*/
							$connexion_min += strtotime($periode->end) - strtotime($periode->begin);
						//}
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
		
				$presence = number_format($connexion_min * 100 / $connexion_max,1);

				if($connexion_min)
				$presence_time = $dd;
				else
					$presence_time = '';

					//decroche
					$agent_number = $row['agent_number'];
					if($agent_number){
						$result_s = $mysqli->query("SELECT * from call_infos WHERE agent = '".$agent_number."' and timestamp >= '".strtotime($min)."' and timestamp <= '".strtotime($max)."'");
						$nb_call_ok = 0;
						$nb_call_nok = 0;
						while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
							if($row_s['accepted'] == 'yes')$nb_call_ok ++;
							if($row_s['accepted'] == 'no' || $row_s['reason'] == 'NOANSWER')$nb_call_nok ++;
						}

						$result_s = $mysqli->query("SELECT * from chats WHERE to_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");
						while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
							if($row_s['consult_date_start'])$nb_call_ok ++;
							if(!$row_s['consult_date_start'])$nb_call_nok ++;
						}
						if(($nb_call_ok + $nb_call_nok) > 0)
						$decroche = number_format($nb_call_ok * 100 / ($nb_call_ok + $nb_call_nok),1);
					}

					//transformation
					/*$result_s = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and (tchat = 1 OR phone = 1) and date_connexion >= '".$periode->begin."' and date_connexion <= '".$periode->end."'");*/

					$nb_consult = 0;
					$nb_visite = 0;
					//while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
					foreach($tranches as $periode){	
						$result_ss = $mysqli->query("SELECT * from agent_views WHERE agent_id = '".$row['id']."' and date_view >= '".$periode->begin."' and date_view < '".$periode->end."'");

						while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){
							$nb_visite ++;
						}

						$result_ss = $mysqli->query("SELECT * from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$periode->begin."' and date_start < '".$periode->end."'");

						while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){
							$nb_consult ++;
						}
					}


					if($nb_visite){	
						$transformation = $nb_consult.'_'.$nb_visite;
					}


					//tmc
					$result_s = $mysqli->query("SELECT AVG(seconds) as duree from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."' and media != 'email'");
					$row_s = $result_s->fetch_array(MYSQLI_ASSOC);
					$duree = $row_s['duree'];
					$tmc = $row_s['duree'];//gmdate("i,s", $row_s['duree']);

					//tmc_global
					$result_s = $mysqli->query("SELECT AVG(seconds) as duree from user_credit_history WHERE date_start >= '".$min."' and date_start <= '".$max."' and media != 'email'");
					$row_s = $result_s->fetch_array(MYSQLI_ASSOC);
					$tmc_global = $row_s['duree'];


					//proportion
					$result_s = $mysqli->query("SELECT media from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");

					$count_total = 0;
					$count_total_mail = 0;
					$count_total_tchat = 0;
					$count_total_phone = 0;

					while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
						if($row_s['media'] == 'email') $count_total_mail ++;
						if($row_s['media'] == 'phone') $count_total_phone ++;
						if($row_s['media'] == 'chat') $count_total_tchat ++;
						$count_total ++;
					}

					if($count_total){
						$email = number_format($count_total_mail * 100 / $count_total,1);
						$tchat = number_format($count_total_tchat * 100 / $count_total,1);
						$tel = number_format($count_total_phone * 100 / $count_total,1);
					}

					//SAVE
					$result_save = $mysqli->query("SELECT id from agent_stats WHERE user_id = '".$row['id']."' and date_min = '".$min."' and date_max = '".$max."'");
					$row_save = $result_save->fetch_array(MYSQLI_ASSOC);
					if($row_save['id']){
						$mysqli->query("UPDATE agent_stats set note = '{$note}' ,presence = '{$presence}',presence_time = '{$presence_time}',decroche = '{$decroche}',transformation = '{$transformation}',tmc = '{$tmc}',tmc_global = '{$tmc_global}',email = '{$email}',tchat = '{$tchat}',tel = '{$tel}' where id = '{$row_save['id']}'");
					}else{
						$mysqli->query("INSERT INTO agent_stats(user_id, date_min,date_max,note,presence,presence_time,decroche,transformation,tmc,tmc_global,email,tchat,tel) VALUES ('{$row['id']}','{$min}','{$max}','{$note}','{$presence}','{$presence_time}','{$decroche}','{$transformation}','{$tmc}','{$tmc_global}','{$email}','{$tchat}','{$tel}')");
					}
				//}
			}
		}
		$mysqli->close();
	}
	public function sendManualSponsorship(){
		$id_agent = 14549;
		$list_email = array(

);
		
		$this->loadModel('User');
		$this->loadModel('Domain');
		$this->loadModel('Sponsorship');
		$this->loadModel('SponsorshipRule');
		
		$conditions = array(
								'User.id' => $id_agent,
		);
		$agent = $this->User->find('first',array('conditions' => $conditions));
			
		$conditions = array(
					'SponsorshipRule.type_user' => 'agent',
		);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
		$is_send = true;
		if(is_array($list_email)){
				foreach($list_email as $email){
					if($email){
						//verifier si email pas present
						$conditions = array(
									'User.email' => $email
						);
						$is_client = $this->User->find('first',array('conditions' => $conditions));
						$conditions = array(
									'Sponsorship.email' => $email
						);
						$is_sponsor_send = $this->Sponsorship->find('first',array('conditions' => $conditions));
						
						if($is_client){
							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 10;
							$saveData['Sponsorship']['hash'] = '';

							$this->Sponsorship->save($saveData);
						}
						
						
						if(!$is_client && !$is_sponsor_send){
							$hash =  $this->cron_crypter($email);

							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 0;
							$saveData['Sponsorship']['hash'] = $hash;

							$this->Sponsorship->save($saveData);

							$url = 'https://fr.spiriteo.com/sponsorship/parrainage-'.$hash;
							
							$conditions = array(
								'Domain.id' => $agent['User']['domain_id'],
							);

							$domain = $this->Domain->find('first',array('conditions' => $conditions));
							if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'fr.spiriteo.com';
							
							$url_pixel_view = '<img src="https://'.$domain['Domain']['domain'].'/sponsorship/track?i='.$this->Sponsorship->id.'" />';

							$is_send = $this->sendCmsTemplatePublic(327, (int)$agent['User']['lang_id'], $email, array(
									'AGENT' =>$agent['User']['pseudo'],
									'URL' =>$url,
									'PIXEL' =>$url_pixel_view,
									'PSEUDO_EXPERT_PARRAIN' => $agent['User']['pseudo']
								));
						}
					}
					sleep(60);
				}
			}
			
			
            if($is_send){
                echo 'done';
            }
			echo 'end';
	}	
	protected function cron_crypter($maChaineACrypter){
		$maCleDeCryptage = md5('ssponsor');
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
	
	public function sendHoroscope(){
		
		$startBegin = date('Y-m-d 00:00:00', strtotime('-1 days'));
        
		$list_sign_list = array(
			0 => 34,
			1 => 35,
			2 => 36,
			3 => 37,
			4 => 38,
			5 => 39,
			6 => 40,
			7 => 41,
			8 => 42,
			9 => 43,
			10 => 44,
			11 => 46,
			12 => 47
		);
		
		//get email template
		$uri_base = str_replace('Console/','/',$this->webroot).'View/Layouts/Emails/html/';
		$tpl = file_get_contents($uri_base.'default.ctp');
		
		$tpl = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>##TITLE##</title>
	<style>
		a{
			color:#7f6faa;
		}
	</style>
</head>
<body>
	<div align="center">
		<table style="width:600px;" width="600" cellspacing="0" cellpadding="0" border="0">
 			<tbody>
				<tr>
  					<td>
						<a href="https://fr.spiriteo.com"><img src="'.Configure::read('Email.logo').'" alt="Spiriteo" width="598" border="0" hspace="0" vspace="0" /></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div align="center">
		<table style="width:600px;" width="600" cellspacing="0" cellpadding="0" border="0">
 			<tbody>
				<tr>
  					<td>
						##CONTENT##
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	##FOOTER##
</body>
</html>';
		
		
		//update contact
		$this->loadModel('HoroscopeSubscribe');
		
		$users = $this->HoroscopeSubscribe->find('all', array(
					'fields' => array('HoroscopeSubscribe.*'),
					'conditions' => array('HoroscopeSubscribe.date_add >'=>$startBegin),
					'paramType' => 'querystring',
					'recursive' => -1
				));	

		if(is_array($users)){
			foreach($users as $user){
				
				$curl = curl_init();
				
				$jsonData = array(
					'emails' => array($user['HoroscopeSubscribe']['email']),
				);
				$jsonDataEncoded = json_encode($jsonData);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists/48/contacts/remove",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
									 'Content-Length: ' . strlen($jsonDataEncoded),
									'api-key: ' . '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'
									  ),
				 CURLOPT_POSTFIELDS => $jsonDataEncoded
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				$curl = curl_init();
				
				$attr = new stdClass();
				$attr->PRENOM = $user['HoroscopeSubscribe']['firstname'];
				
				$jsonData = array(
					'listIds' => array($list_sign_list[$user['HoroscopeSubscribe']['sign_id']]),
					'email' => $user['HoroscopeSubscribe']['email'],
					'attributes' => $attr,
					'updateEnabled' => true
				);
				$jsonDataEncoded = json_encode($jsonData);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
									 'Content-Length: ' . strlen($jsonDataEncoded),
									'api-key: ' . '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'
									  ),
				 CURLOPT_POSTFIELDS => $jsonDataEncoded
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				/*if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  echo $response;
				}*/
			}
		}

		//recup mail content
		$page = $this->getCmsPageMail(392, 1);
		$title = $page['PageLang']['meta_title'];
		$commonFooter = $this->getCmsPage(197, 1);
		$commonFooter = $commonFooter['PageLang']['content'].'<br /><br /><p style="text-align:center"><a href="[UNSUBSCRIBE]">cliquez ici pour vous désinscrire.</a></p>';
		
		$body = str_replace('##FOOTER##',$commonFooter,$tpl);
		$body = str_replace('##TITLE##',$page['PageLang']['meta_title'],$body);
		$body = str_replace('##CONTENT##',$page['PageLang']['content'],$body);
		$body = str_replace('##PARAM_URLSITE##','https://fr.spiriteo.com/',$body);
		
		$month = array(
			"01" => "janvier",
			"02" => "février",
			"03" => "mars",
			"04" => "avril",
			"05" => "mai",
			"06" => "juin",
			"07" => "juillet",
			"08" => "aout",
			"09" => "septembre",
			"10" => "octobre",
			"11" => "novembre",
			"12" => "décembre"
			);
		
		$body = str_replace('##DATE_HORO##',date('d').' '.$month[ date('m') ].' '.date('Y'),$body);
		
		
		//boucler toute les lists
		$list_sign_list = array(
			34 => 'Index',
			35 => 'Bélier',
			36 => 'Taureau',
			37 => 'Gémeaux',
			38 => 'Cancer',
			39 => 'Lion',
			40 => 'Vierge',
			41 => 'Balance',
			42 => 'Scorpion',
			43 => 'Sagittaire',
			44 => 'Capricorne',
			46 => 'Verseau',
			47 => 'Poisson'
		);
		
		$list_sign_url = array(
			34 => 'https://fr.spiriteo.com/fre/horoscope-du-jour',
			35 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/belier',
			36 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/taureau',
			37 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/gemeaux',
			38 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/cancer',
			39 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/lion',
			40 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/vierge',
			41 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/balance',
			42 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/scorpion',
			43 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/sagittaire',
			44 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/capricorne',
			46 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/verseau',
			47 => 'https://fr.spiriteo.com/fre/horoscope-du-jour/poisson'
		);
		$body_source = $body;
		foreach($list_sign_list as $id_list => $sign_label){
			
			/*$curl = curl_init();
			curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists/".$id_list."/contacts",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET",
				  CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
									'api-key: ' . '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'
									  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			$tab = json_decode($response);
			$list_recipient = array();
			if(is_object($tab) && is_array($tab->contacts)){
				foreach($tab->contacts as $t){
					array_push($list_recipient,$t->id);
				}
			}*/
			$list_recipient = array();
			array_push($list_recipient,$id_list);
			if(count($list_recipient)>0){
				$body = str_replace('##URL_HORO##',$list_sign_url[$id_list],$body_source);

				$curl = curl_init();
				
				$sender = new stdClass();
				$sender->name = 'Spiriteo';
			    $sender->email = 'contact@talkappdev.com';
			
			    $recipients = new stdClass();
				$recipients->listIds = $list_recipient;
				
				$jsonData = array(
					'recipients' => $recipients,
					'sender' => $sender,
					'name'  => 'Horoscope '.$sign_label.' '.date('d/m/Y'),
					'htmlContent' => $body,
					'subject' => $title,
					'type' => 'classic'
				);
				$jsonDataEncoded = json_encode($jsonData);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.sendinblue.com/v3/emailCampaigns",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
									 'Content-Length: ' . strlen($jsonDataEncoded),
									'api-key: ' . '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'
									  ),
				 CURLOPT_POSTFIELDS => $jsonDataEncoded
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
				$campaign_create = json_decode($response);
				if(is_object($campaign_create) && !empty($campaign_create->id))
					$campaign_id = $campaign_create->id;
				if($campaign_id){
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => "https://api.sendinblue.com/v3/emailCampaigns/".$campaign_id."/sendNow",
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
														'api-key: ' . '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'
														  ),
					));

					$response = curl_exec($curl);
					$err = curl_error($curl);

					curl_close($curl);

				}
				
			}
		}
		
	}	
	
	public function sendHoroscopeDominical(){

		$curl = curl_init();
				
				$sender = new stdClass();
				$sender->name = 'Spiriteo';
			    $sender->email = 'contact@talkappdev.com';
			
			    $recipients = new stdClass();
				$recipients->listIds = array(48);
				
				$jsonData = array(
					'recipients' => $recipients,
					'sender' => $sender,
					'name'  => 'Horoscope dimanche '.date('d/m/Y'),
					'subject' => 'Votre Horoscope personnalisé de la semaine !',
					'type' => 'classic',
					'templateId' => 182
				);
				$jsonDataEncoded = json_encode($jsonData);
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.sendinblue.com/v3/emailCampaigns",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
									 'Content-Length: ' . strlen($jsonDataEncoded),
									'api-key: ' . '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'
									  ),
				 CURLOPT_POSTFIELDS => $jsonDataEncoded
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
				$campaign_create = json_decode($response);
				$campaign_id = $campaign_create->id;
				if($campaign_id){
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => "https://api.sendinblue.com/v3/emailCampaigns/".$campaign_id."/sendNow",
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
														'api-key: ' . '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'
														  ),
					));

					$response = curl_exec($curl);
					$err = curl_error($curl);

					curl_close($curl);

				}	
	}
	public function simultaneousCommunication(){

		App::import('Controller', 'Extranet');
        $extractrl = new ExtranetController();
		
		$this->loadModel('Callinfo');
		$this->Callinfo->useTable = 'call_infos';
		
		 $conditions = array(
					/*'Callinfo.accepted' => 'yes',*/
					'Callinfo.time_start !=' => NULL,
					'Callinfo.time_end' => NULL,
					'Callinfo.time_stop' => NULL,
				);
			
		$live_phone = $this->Callinfo->find('all', array(
				'conditions'    => $conditions,
				'order'         => 'Callinfo.time_start asc',
				'recursive'     => -1
		));

		$this->loadModel('Chat');
		
		//Les conditions de base
		/*$conditions = array('Chat.consult_date_start !=' => NULL, 'Chat.etat' => 1, 'Chat.consult_date_end' => NULL, 'Chat.date_end' => NULL);
			
		$live_chat = $this->Chat->find('all', array(
				'conditions'    => $conditions,
				'order'         => 'Chat.consult_date_start asc',
				'recursive'     => -1
		));*/
		
		$this->loadModel('User');

		foreach($live_phone as $phone){
			$agent = $this->User->find('first',array(
					'fields'     => array('User.id','User.agent_status','User.agent_status','User.pseudo','User.agent_number'),
					'conditions' => array('User.agent_number' => $phone['Callinfo']['agent']),
					'recursive' => -1
			));
			if($agent['User']['agent_status'] != 'busy'){
				$html = 'L\'Agent ' .$agent['User']['pseudo'].' ('.$agent['User']['agent_number'].') est disponible alors qu\'il est en communication';
				$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
				);
				//Envoie de l'email
				//$extractrl->sendEmail('contact@talkappdev.com','URGENT - Expert dispo en comm Tél','default',$datasEmail);
				//$extractrl->sendEmail('system@web-sigle.fr','URGENT - Expert dispo en comm Tél','default',$datasEmail);
				
				$this->User->id = $agent['User']['id'];
				$this->User->saveField('agent_status', 'busy');
			}
		}
		/*foreach($live_chat as $chat){
			$agent = $this->User->find('first',array(
					'fields'     => array('User.id','User.agent_status','User.pseudo','User.agent_number'),
					'conditions' => array('User.id' => $chat['Chat']['to_id']),
					'recursive' => -1
			));
			if($agent['User']['agent_status'] != 'busy'){
				$html = 'L\'Agent ' .$agent['User']['pseudo'].' ('.$agent['User']['agent_number'].') est disponible alors qu\'il est en communication';
				$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
				);
				//Envoie de l'email
				//$extractrl->sendEmail('contact@talkappdev.com','URGENT - Expert dispo en comm CHat','default',$datasEmail);
				//$extractrl->sendEmail('system@web-sigle.fr','URGENT - Expert dispo en comm Chat','default',$datasEmail);
				//$this->User->id = $agent['User']['id'];
				//$this->User->saveField('agent_status', 'busy');
			}
		}*/
		exit;
	}
		
	public function sendGift(){
		$this->loadModel('User');
		$this->loadModel('GiftOrder');
		$this->loadModel('Domain');
		$gift_orders = $this->GiftOrder->find('all', array(
					'conditions' => array('GiftOrder.valid' => 1, 'GiftOrder.is_send' => 0,'GiftOrder.send_who' => 1),
					'recursive' => -1,
				));
		foreach($gift_orders as $gift_order){
			$user_order = $this->User->find('first', array(
					'conditions' => array('User.id' => $gift_order['GiftOrder']['user_id']),
					'recursive' => -1,
				));
			$conditions = array(
								'Domain.id' => $user_order['User']['domain_id'],
							);
			

			$domain = $this->Domain->find('first',array('conditions' => $conditions));
			if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';
			
			$url = 'https://'.$domain['Domain']['domain'].'/gifts/show-'.$gift_order['GiftOrder']['hash_beneficiary'];

				 $this->mail_vars = array(
					'beneficiary'       =>    $gift_order['GiftOrder']['beneficiary_firstname'].' '.$gift_order['GiftOrder']['beneficiary_lastname'],
					'customer'       =>    $user_order['User']['firstname'],
					'url_carte_cadeau'       =>    $url,
				);

				if($this->sendCmsTemplateByMail(414, $user_order['User']['lang_id'], $gift_order['GiftOrder']['beneficiary_email'])){
					$this->GiftOrder->id = $giftorder_id;
					$this->GiftOrder->saveField('is_send', 1);
				}
					 
		}
	}
		
	public function userInvoice(){
		
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
		
		$dd = new DateTime(date('Y-m-d'));
		$dd->modify('-1 day');

		$date = $dd->format('Y-m-01');
		$datecomp = $dd->format('Ym01');
		$period = $dd->format('Y-m');
		$mois_comp = $dd->format('m');
		$datemin = $date.' 00:00:00';
		$dd->modify('last day of this month');
		$datemax = $dd->format('Y-m-d').' 23:59:59';

		$session_date_min_public =  $datemin; 
		$session_date_max_public =  $datemax; 
		
		$listing_utcdec = Configure::read('Site.utcDec');

		/*$utc_dec = 1;
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
			$utc_dec = 2;*/

		$dmin = new DateTime($datemin);
		$dmax = new DateTime($datemax);
		if($datecomp >= '20190228'){
			$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
			$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
		}

		$session_date_min =  $dmin->format('Y-m-d H:i:s'); 
		$session_date_max =  $dmax->format('Y-m-d H:i:s'); 

		//if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';
		
		$result_agent = $mysqli->query("SELECT id from users WHERE role = 'agent' order by id");
		while($row_agent = $result_agent->fetch_array(MYSQLI_ASSOC)){

			$result_comm = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$row_agent['id']}' and C.date_start >= '{$session_date_min}' and C.date_start <= '{$session_date_max}' and P.id_user_credit_history = C.user_credit_history");
			$total = 0;
			$total_comm = 0;
			$total_penality = 0;
			$total_bonus = 0;

			while($row_comm = $result_comm->fetch_array(MYSQLI_ASSOC)){
				if($row_comm['is_factured']){
					$total += $row_comm['price'];
					$total_comm += $row_comm['price'];
				}
			}

			$tabdate = explode(' ',$session_date_min_public);
			$tabdatec = explode('-',$tabdate[0]);
			$annee_min = $tabdatec[0];
			$mois_min = $tabdatec[1];
			$tabdate = explode(' ',$session_date_max_public);
			$tabdatec = explode('-',$tabdate[0]);
			$annee_max = $tabdatec[0];
			$mois_max = $tabdatec[1];


			$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$row_agent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id DESC");
			while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){

				$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
				$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
				$total += $rowbonus['amount'];
				$total_bonus += $rowbonus['amount'];
			}


			$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$row_agent['id']}' and is_recup = 1 and status <= 4");
			while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){

				$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$session_date_min}' and date_start <= '{$session_date_max}'");
				$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
				$mt = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];
				$total += $mt;
				$total_bonus += $mt;
			}


			$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$row_agent['id']}' and date_ecriture >= '".$session_date_min."' and date_ecriture <= '".$session_date_max."' order by id ASC");

			while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){
				$total += $rowfacturation['amount'];
			}


			$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$row_agent['id']}' and date_com >= '".$session_date_min."' and date_com <= '".$session_date_max."' and is_factured = 1 order by id ASC");

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

			if($total > 0){
				$mysqli->query("INSERT INTO user_invoices(user_id, date_min, date_max, period, total_comm,total_bonus,total_penality,total_amount) VALUES ('{$row_agent['id']}','{$session_date_min}','{$session_date_max}','{$period}','{$total_comm}','{$total_bonus}','{$total_penality}','{$total}')");
			}
		}
	}
		
	public function checkSEPA(){

		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
		
		App::import('Controller', 'Payment');
        $paymentctrl = new PaymentController();
		
		App::import('Controller', 'Paymentstripe');
		$paymentstripe = new PaymentstripeController();
		
		require(APP.'Lib/stripe/init.php');
		\Stripe\Stripe::setApiKey($paymentstripe->_stripe_confs[$paymentstripe->_stripe_mode]['private_key']);
		
		$this->loadModel('Order');
		$this->loadModel('StripeCustomer');
		
		$startBegin = date('Y-m-d 00:00:00', strtotime('-14 days'));
		
		$ordersepas = $this->Order->find('all',array(
							'conditions' => array('Order.valid' => 0, 'Order.payment_mode' => 'sepa', 'Order.date_add >=' => $startBegin),
							'recursive' => -1
							));

		$this->loadModel('OrderSepatransaction');
		foreach($ordersepas as $order){
			
			//get source id
			$source_id = '';
			

			$ordersepa = $this->OrderSepatransaction->find('first',array(
								'conditions' => array('OrderSepatransaction.order_id' => $order['Order']['id']),
								'recursive' => -1
								));

			if($ordersepa['OrderSepatransaction']['id']){

				$source = '';

				try {
					$source = \Stripe\Source::retrieve($ordersepa['OrderSepatransaction']['id']);

				 } catch (\Stripe\Error\Base $e) {
					//getsion error
				}

				if(is_object($source)){


					$amount_receive = $source->receiver->amount_received;

					if($source->status == 'chargeable' && $amount_receive >= ($order['Order']['total'] * 100)){
						$amount = $order['Order']['total'] * 100;
						try {
							$charge = \Stripe\Charge::create([
									  'amount' => $amount,
									  'currency' => 'eur',
									  'source' => $ordersepa['OrderSepatransaction']['id'],
									  'transfer_group' => $order['Order']['cart_id']
									]);

						 } catch (\Stripe\Error\Base $e) {
							//getsion error
						}


						if($charge->status == 'succeeded'){

							//update 
							$mysqli->query("UPDATE order_sepatransactions set amount_received = '{$amount_receive}', amount_charged = '{$amount}', charge_id = '{$charge->id}'  where order_id = '{$order['Order']['id']}'");
							$paymentctrl->order_confirm($order['Order']['id']);
							$mysqli->query("UPDATE orders set date_upd = NOW()  where id = '{$order['Order']['id']}'");
						}
					}
				}
			}
		}
		//supprime vieille source
		$ordersepas = $this->Order->find('all',array(
							'conditions' => array('Order.valid' => 0, 'Order.payment_mode' => 'sepa', 'Order.date_add <' => $startBegin),
							'recursive' => -1
							));
		foreach($ordersepas as $order){
			//get source id
			$source_id = '';
			

			$ordersepa = $this->OrderSepatransaction->find('first',array(
								'conditions' => array('OrderSepatransaction.order_id' => $order['Order']['id']),
								'recursive' => -1
								));
			$customer_stripe = $this->StripeCustomer->find('first',array(
				'conditions' => array('StripeCustomer.user_id' => $order['Order']['user_id']),
				'recursive' => -1
				));
			

			if($ordersepa['OrderSepatransaction']['id'] && $customer_stripe && $customer_stripe['StripeCustomer']['customer_id']){

				$source = '';

				try {
					$source = \Stripe\Source::retrieve($ordersepa['OrderSepatransaction']['id']);

				 } catch (\Stripe\Error\Base $e) {
					//getsion error
				}

				if(is_object($source)){
					try {
						\Stripe\Customer::deleteSource(
						  $customer_stripe['StripeCustomer']['customer_id'],
						  $source->id
						);


						

					 } catch (\Stripe\Error\Base $e) {
						//getsion error
						//var_dump($e);exit;
					}
					
				}
				
				
			}
			$paymentctrl->cron_delete($order['Order']['id']);
		}
		
	}
		
	public function generateInvoiceAccount(){
		$this->loadModel('UserCreditLastHistory');
		$this->loadModel('UserCreditHistory');
		$this->loadModel('InvoiceAccount');
		$this->loadModel('InvoiceVat');
		$this->loadModel('User');
		
		$listing_utcdec = Configure::read('Site.utcDec');
		
		/*$utc_dec = 1;
		$cut = explode('-',date('Y-m-d', strtotime('-1 day')) );
		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/
			
		$date = date('Y-m-d 00:00:00', strtotime('-1 day'));//'2019-07-01 00:00:00';//
		$date2 = date('Y-m-d 23:59:59', strtotime('-1 day'));//'2019-08-26 23:59:59';//
		$dx = new DateTime($date);
		$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
		$dx2 = new DateTime($date2);
		$dx2->modify('- '.$listing_utcdec[$dx2->format('md')].' hour');
			
		$datemin = $dx->format('Y-m-d H:i:s');
		$datemax = $dx2->format('Y-m-d H:i:s');

		$rows = $this->UserCreditLastHistory->find('all', array(
                'fields'    => array('UserCreditLastHistory.user_credit_last_history','UserCreditLastHistory.media','UserCreditLastHistory.sessionid','UserCreditLastHistory.users_id','UserCreditLastHistory.agent_id','UserCreditLastHistory.date_start','UserCreditLastHistory.agent_pseudo'),
                'conditions' => array('UserCreditLastHistory.date_start >=' => $datemin, 'UserCreditLastHistory.date_start <=' => $datemax, 'UserCreditLastHistory.date_end !=' => null),
                'recursive' => -1
         ));

		foreach($rows as $row){
			
			$comm = $this->UserCreditHistory->find('first', array(
                'conditions' => array('UserCreditHistory.sessionid' => $row['UserCreditLastHistory']['sessionid'], 'UserCreditHistory.media' => $row['UserCreditLastHistory']['media'], 'UserCreditHistory.date_start' => $row['UserCreditLastHistory']['date_start'],'UserCreditHistory.user_id' => $row['UserCreditLastHistory']['users_id'],'UserCreditHistory.agent_id' => $row['UserCreditLastHistory']['agent_id']),
                'recursive' => -1
         	));
			if($comm){
				
				$check = $this->InvoiceAccount->find('first', array(
					'conditions' => array('InvoiceAccount.user_credit_last_history' => $row['UserCreditLastHistory']['user_credit_last_history']),
					'recursive' => -1
				));
				if(!$check){
					$order = $this->InvoiceAccount->find('first', array(
						'conditions' => array('InvoiceAccount.agent_id' => $comm['UserCreditHistory']['agent_id']),
						'order' => 'InvoiceAccount.id DESC',
						'recursive' => -1
					));
					$agent = $this->User->find('first', array(
						'conditions' => array('User.id' => $row['UserCreditLastHistory']['agent_id']),
						'recursive' => -1
					));
					if(isset($agent['User']['invoice_vat_id'])){
						$vat_data = $this->InvoiceVat->find('first', array(
							'conditions' => array('InvoiceVat.id' => $agent['User']['invoice_vat_id']),
							'recursive' => -1
						));
					}else{
						$vat_data = $this->InvoiceVat->find('first', array(
							'conditions' => array('InvoiceVat.country_id' => $agent['User']['country_id'], 'InvoiceVat.society_type_id' => $agent['User']['society_type_id']),
							'recursive' => -1
						));
					}
					
					if($order)
						$order_id = $order['InvoiceAccount']['order_id'] + 1;
					else
						$order_id = 1;
					
					if($vat_data){
						$taux = 1 + ($vat_data['InvoiceVat']['rate'] / 100);
						$amount = $comm['UserCreditHistory']['ca']  / $taux;
						$vat_amount = $comm['UserCreditHistory']['ca'] - $amount;
						$total_amount = $comm['UserCreditHistory']['ca'];
						$vat_id = $vat_data['InvoiceVat']['id'];
						$vat_tx = $vat_data['InvoiceVat']['rate'];
						
					}else{
						$amount = $comm['UserCreditHistory']['ca'];
						$vat_id = 0;
						$vat_amount = 0;
						$total_amount = $amount;	
						$vat_tx = 0;
					}
					
					
					
					$product = 'Communication par ';
					switch ($row['UserCreditLastHistory']['media']) {
						case 'phone':
							$product .= 'téléphone';
							break;
						case 'chat':
							$product .= 'tchat';
							break;
						case 'email':
							$product .= 'email';
							break;
					}
					$product .= ' avec '.$row['UserCreditLastHistory']['agent_pseudo'];
					$ddate = new DateTime($row['UserCreditLastHistory']['date_start']);
					$ddate->modify('+'.$listing_utcdec[$ddate->format('md')].' hour');
					$date_com =  $ddate->format('d-m-Y à H:i:s');
					$product .= ' le '.$date_com;
					if($amount > 0){
						$this->InvoiceAccount->create();
						$saveData = array();
						$saveData['InvoiceAccount'] = array();
						$saveData['InvoiceAccount']['user_credit_last_history'] = $row['UserCreditLastHistory']['user_credit_last_history'];
						$saveData['InvoiceAccount']['agent_id'] = $comm['UserCreditHistory']['agent_id'];
						$saveData['InvoiceAccount']['user_id'] = $comm['UserCreditHistory']['user_id'];
						$saveData['InvoiceAccount']['date_add'] = date('Y-m-d H:i:s');
						$saveData['InvoiceAccount']['order_id'] = $order_id;
						$saveData['InvoiceAccount']['product'] = $product;
						$saveData['InvoiceAccount']['amount'] = $amount;
						$saveData['InvoiceAccount']['vat_id'] = $vat_id;
						$saveData['InvoiceAccount']['vat_tx'] = $vat_tx;
						$saveData['InvoiceAccount']['vat_amount'] = $vat_amount;
						$saveData['InvoiceAccount']['total_amount'] = $total_amount;
						$saveData['InvoiceAccount']['currency'] = $comm['UserCreditHistory']['ca_currency'];

						$this->InvoiceAccount->save($saveData);
					}
				}
			}
			
		}
	}
		
	public function generateInvoiceAgent(){
    

		$this->loadModel('UserCreditHistory');
		$this->loadModel('InvoiceAgent');
		$this->loadModel('InvoiceAgentDetail');
		$this->loadModel('InvoiceNum');
		$this->loadModel('UserPay');
		$this->loadModel('User');
		$this->loadModel('UserPenality');
		$this->loadModel('UserOrder');
		$this->loadModel('BonusAgent');
		$this->loadModel('Sponsorship');
		$this->loadModel('Currency');
    
    App::import('Controller', 'Extranet');
    $extractrl = new ExtranetController();
    
    
    App::import('Controller', 'Paymentstripe');
		$paymentctrl = new PaymentstripeController();
    
    require(APP.'Lib/stripe/init.php');

		\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
    
		$listing_utcdec = Configure::read('Site.utcDec');
    
		
		//$cut = explode('-',date('Y-m-d', strtotime('-1 month')) );
		/*$utc_dec = 1;
		$mois_comp = $cut[1];
		if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/
		
		$date = date('Y-m-d 00:00:00', strtotime('-1 month'));
		$dx = new DateTime($date);
		$date = $dx->format('Y-m-01 00:00:00');
		$date_perdiode_min = $dx->format('01-m-Y');
		$dx = new DateTime($date);
		$dx->modify('last day of this month');
		$date2 = $dx->format('Y-m-d 23:59:59');
		$date_perdiode_max = $dx->format('d-m-Y');
		$dx = new DateTime($date);
		$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
		$dx2 = new DateTime($date2);
		$dx2->modify('- '.$listing_utcdec[$dx2->format('md')].' hour');
			
		$sql_datemin = $dx->format('Y-m-d H:i:s');
		$sql_datemax = $dx2->format('Y-m-d H:i:s');
		$datecheck = $dx2->format('Y-m-15 H:i:s');
        $listing_agents = array();

		//load expert qui ont travaillé
		$agents = $this->User->find("all", array(
           // 'fields'     => array('User.id','User.stripe_account','User.vat_num_status','User.vat_num_proof','User.vat_num','User.active','User.pseudo','User.iban','User.rib','User.country_id','User.societe_pays','User.bank_country','User.stripe_base'),
            'conditions' => array(
                'User.role'   =>  'agent',
				//'User.id' => 68614
            ),
			'recursive' => -1
        ));
		foreach($agents as $agent){
			
			$comm = null;
			$comm_this_month = null;
      
      //check if stripe country auth
      $is_available_stripe = true;
      $countrie_stripe = Configure::read('Stripe.countries');
      if($agent['User']['societe_pays'] && !in_array($agent['User']['societe_pays'],$countrie_stripe))$is_available_stripe = false;
			if(!$agent['User']['societe_pays'] && !in_array($agent['User']['country_id'],$countrie_stripe))$is_available_stripe = false;
      
      if($agent['User']['bank_country'] && !$is_available_stripe && strtolower($agent['User']['bank_country']) == 'france')$is_available_stripe = true;
      if($agent['User']['bank_country'] && strtolower($agent['User']['bank_country']) == 'maroc')$is_available_stripe = false;
			
     
			$datemin = $sql_datemin;
			$datemax = $sql_datemax;
			$date_perdiodemin = $date_perdiode_min;
			
			//recalcul la date min de l expert 
			$comm = $this->UserCreditHistory->find('first', array(
                'conditions' => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_start >=' => '2019-06-30 22:00:00','UserCreditHistory.date_start <=' => $datemax,'UserCreditHistory.is_sold' => 0,'UserCreditHistory.is_factured' => 1),
                'recursive' => -1,
				'order' => 'UserCreditHistory.date_start asc',
         	));
			
			$comm_this_month = $this->UserCreditHistory->find('first', array(
                'conditions' => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_start >=' => $datemin,'UserCreditHistory.date_start <=' => $datemax,'UserCreditHistory.is_sold' => 0,'UserCreditHistory.is_factured' => 1),
                'recursive' => -1
         	));
			
			$do_fact = 1;
			if(!$agent['User']['active'] && !$comm_this_month)$do_fact = 0;
			
			if($agent['User']['id'] == 317)$do_fact = 0;
			if($agent['User']['id'] == 332)$do_fact = 0;
			if($agent['User']['id'] == 17414)$do_fact = 0;
			if($agent['User']['id'] == 34482)$do_fact = 0;
			if($agent['User']['id'] == 70347)$do_fact = 0;
			if($agent['User']['id'] == 71825)$do_fact = 0;
			
			if(!$agent['User']['iban'] && !$agent['User']['rib'])$do_fact = 0;

			if($do_fact && $comm){//agent work
				
				if(!$comm_this_month && $comm['UserCreditHistory']['user_credit_history']){
					$datemin = $comm['UserCreditHistory']['date_start'];
					$dx = new DateTime($datemin);
					$date_perdiodemin = $dx->format('d-m-Y');
				}
					
				
				if($comm_this_month && $comm['UserCreditHistory']['user_credit_history'] < $comm_this_month['UserCreditHistory']['user_credit_history']){
					$datemin = $comm['UserCreditHistory']['date_start'];
					$dx = new DateTime($datemin);
					$date_perdiodemin = $dx->format('d-m-Y');
				}
					
				
				//var_dump($agent['User']['pseudo']. ' '.$agent['User']['id']);
				//var_dump($datemin . ' -> '.$datemax);exit;
				
				$ca_euro 	= 0;
				$ca_dollar 	= 0;
				$ca_chf 	= 0;
        		$ca_pay 	= 0;
				$bonus 		= 0;
				$sponsor 	= 0;
				$order_line = 0;
				$penality 	= 0;
				$agent_paid	= 0;
				$fees		= 0;
				
				//get order amount for agent
				$lines = $this->UserCreditHistory->find('all',array(
					'fields' => array('UserCreditHistory.user_credit_history','UserPay.price','UserPay.ca','UserCreditHistory.ca','UserCreditHistory.ca_currency','UserCreditHistory.sessionid','UserCreditHistory.media'),
					'conditions' => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_start >=' => $datemin,'UserCreditHistory.date_start <=' => $datemax, 'UserCreditHistory.is_factured' => 1),
					'joins' => array(
						array(
							'table' => 'user_pay',
							'alias' => 'UserPay',
							'type'  => 'left',
							'conditions' => array(
								'UserPay.id_user_credit_history = UserCreditHistory.user_credit_history'
							)
						)
					),
					'recursive' => -1
				));
				$this->loadModel('Message');
				foreach($lines as $line){
					$agent_paid += number_format($line['UserPay']['price'],2,'.','');
					
					//double check email refund
					$comm_ok = true;
					if($line['UserCreditHistory']['media'] == 'email'){
						$message = $this->Message->find('first', array(
										'fields' => array('Message.etat'),
										'conditions' => array('Message.id' => $line['UserCreditHistory']['sessionid']),
										'recursive' => -1
									));
						if($message['Message']['etat'] == 3 )$comm_ok = false;
					}
					if($comm_ok){
						switch ($line['UserCreditHistory']['ca_currency']) {
							case '€':
								$ca_euro += $line['UserCreditHistory']['ca'];
								break;
							case '$':
								$ca_dollar += $line['UserCreditHistory']['ca'];
								break;
							case 'CHF':
								$ca_chf += $line['UserCreditHistory']['ca'];
								break;
							default:
								$ca_euro += $line['UserCreditHistory']['ca'];
								break;
						}
            
            $ca_pay += $line['UserPay']['ca'];
					}
				}
				
				$lines_penalty = $this->UserPenality->find('all', array(
							'fields' => array('UserPenality.id','UserPenality.penality_cost'),
							'conditions' => array('UserPenality.user_id' => $agent['User']['id'], 'UserPenality.date_add >=' => $datemin,'UserPenality.date_add <=' => $datemax, 'UserPenality.is_factured' => 1,'UserPenality.message_id >' => 0),
							'recursive' => -1
					));

				foreach($lines_penalty as $line){
					$penality += 12;
				}
				
				//loop user_order
				$lines_modifs = $this->UserOrder->find('all', array(
							'fields' => array('UserOrder.amount','UserOrder.label'),
							'conditions' => array('UserOrder.user_id' => $agent['User']['id'], 'UserOrder.date_ecriture >=' => $datemin,'UserOrder.date_ecriture <=' => $datemax ),
							'recursive' => -1
					));
				foreach($lines_modifs as $line){
					$order_line += $line['UserOrder']['amount'];
				}

				//loop bonus
				$lines_bonus = $this->BonusAgent->find('all', array(
							'fields' => array('BonusAgent.paid_amount'),
							'conditions' => array('BonusAgent.id_agent' => $agent['User']['id'], 'BonusAgent.date_add >=' => $datemin, 'BonusAgent.paid' => 1),
							'recursive' => -1
					));//'BonusAgent.date_add <=' => $datemax,
				foreach($lines_bonus as $line){
					$bonus += $line['BonusAgent']['paid_amount'];
				}
				
				//loop sponsorship
				$lines_sponsor = $this->Sponsorship->find('all', array(
							'fields' => array('Sponsorship.id_customer','Sponsorship.bonus'),
							'conditions' => array('Sponsorship.user_id' => $agent['User']['id'], 'Sponsorship.is_recup' => 1,'Sponsorship.status <=' => 4),
							'recursive' => -1
					));
								
				foreach($lines_sponsor as $line){
					$lines_s = $this->UserCreditHistory->find('all',array(
						'fields' => array('UserCreditHistory.credits'),
						'conditions' => array('UserCreditHistory.user_id' => $line['Sponsorship']['id_customer'], 'UserCreditHistory.date_start >=' => $datemin,'UserCreditHistory.date_start <=' => $datemax, 'UserCreditHistory.is_factured' => 1),
						'recursive' => -1
					));
					$credits = 0;
					foreach($lines_s as $ss){
						$credits += $ss['UserCreditHistory']['credits'];
					}
					
					$sponsor += number_format($line['Sponsorship']['bonus'] / 60 * $credits,2,'.','');
				}
				
				$agent_info = $this->User->find("first", array(
					'conditions' => array(
						'User.id'      =>  $agent['User']['id'],
					)
				));
				
				//paiement du parrainage
				if($sponsor > 0 && $agent_info['User']['stripe_account']){
					try {
							$account = \Stripe\Account::retrieve();
							\Stripe\Transfer::create(
										  [
											"amount" => $sponsor * 100,
											"currency" => "eur",
											"destination" => $agent_info['User']['stripe_account']
										  ]
										);

							 } catch (\Stripe\Error\Base $e) {
								
							}
				}
				
				//calcul montant factures expert
				$agent_order_amount = $agent_paid + $order_line - $penality + $bonus + $sponsor;
				
				
				
				//look si expert a communiqué > 15 jrs
				$lines_check_first_com = $this->UserCreditHistory->find('first',array(
						'fields' => array('UserCreditHistory.credits'),
						'conditions' => array('UserCreditHistory.agent_id' => $agent['User']['id'], 'UserCreditHistory.date_start <=' => $datecheck,'UserCreditHistory.is_factured' => 1),
						'recursive' => -1
					));
				
				/*if($agent_order_amount < 20  && $lines_check_first_com){
					$saveData = array();
					$saveData['UserOrder']['user_id'] = $agent['User']['id'];
					$saveData['UserOrder']['date_ecriture'] = $datemax;
					$saveData['UserOrder']['type'] = 2;
					$saveData['UserOrder']['amount'] = -$agent_order_amount;
					$saveData['UserOrder']['label'] = 'Pénalité.';
					$saveData['UserOrder']['commentaire'] = 'Pénalité du solde car facture mensuelle de '.$agent_order_amount . ' euros';
					$this->UserOrder->create();
                	if($this->UserOrder->save($saveData) && $agent_info['User']['stripe_account']){
						try {
							$account = \Stripe\Account::retrieve();
							\Stripe\Transfer::create(
										  [
											"amount" => $agent_order_amount * 100,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent_info['User']['stripe_account']]
										);

							 } catch (\Stripe\Error\Base $e) {
								
							}
					}
					$penality += $agent_order_amount;
					$agent_order_amount = 0;
				}*/
				
				//generation des penalités et save modifs facture
				/*if($agent_order_amount > 20 && $agent_order_amount < 50 && $lines_check_first_com){
					//Penality 10 euros
					$saveData = array();
					$saveData['UserOrder']['user_id'] = $agent['User']['id'];
					$saveData['UserOrder']['date_ecriture'] = $datemax;
					$saveData['UserOrder']['type'] = 2;
					$saveData['UserOrder']['amount'] = -10.00;
					$saveData['UserOrder']['label'] = 'Pénalité de 10 euros.';
					$saveData['UserOrder']['commentaire'] = 'Pénalité de 10 euros car facture mensuelle de '.$agent_order_amount . ' euros';
					$this->UserOrder->create();
                	if($this->UserOrder->save($saveData) && $agent_info['User']['stripe_account']){
						try {
							$account = \Stripe\Account::retrieve();
							\Stripe\Transfer::create(
										  [
											"amount" => 1000,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent_info['User']['stripe_account']]
										);

							 } catch (\Stripe\Error\Base $e) {
								
							}
					}
					$agent_order_amount += -10;
					$penality += 10;
				}*/
				
				
				
				$ca = $ca_pay;
				$vat_tx = 0;
				$vat = 0;
				$is_fact = 1;
				$mt_bank_transfert = 0;
				$agent_paid_total = $agent_paid;
        
				//TVA regularisation
				if($agent['User']['stripe_account'] && $is_available_stripe){
					if(!$agent['User']['vat_num'] || ($agent['User']['vat_num'] && $agent['User']['vat_num_status'] == 'invalide' && !$agent['User']['vat_num_proof'] )){
						if(!$agent['User']['vat_num_proof']){ // exclue cas specifique avec preuve
							if($agent['User']['country_id'] != 3 && $agent['User']['country_id'] != 66){//exclue suisse
								if(!$agent['User']['active']){
									$vat_tx = 23;
									$fees = $ca - $agent_order_amount;
									$vat = $fees * $vat_tx / 100;
									$vat = number_format($vat,2,'.','');
									$agent_paid_total = $agent_paid_total - $vat;
								}else{
									$is_fact = 0;
								}
							}
						}
					}
				}else{
					//expert hors UE
					if($agent_paid < 30){
						$is_fact = false;
					}else{
						
							if(strtolower($agent['User']['bank_country']) != 'france' && strtolower($agent['User']['bank_country']) != 'fr'){
								//fees bank transfert 17 euros
								$saveData = array();
								$saveData['UserOrder']['user_id'] = $agent['User']['id'];
								$saveData['UserOrder']['date_ecriture'] = $datemax;
								$saveData['UserOrder']['type'] = 2;
								$saveData['UserOrder']['amount'] = -17.5;
								$saveData['UserOrder']['label'] = 'Frais de virement hors Europe';
								$saveData['UserOrder']['commentaire'] = 'Frais de virement hors Europe';
								$this->UserOrder->create();
								$this->UserOrder->save($saveData);
								$mt_bank_transfert  = 17.5;//not penality
								$agent_order_amount = $agent_order_amount - 17.5;
							}
						}
				}
				
				//if paid to expert == 0
				$check_total = $agent_paid_total + $order_line + $bonus + $sponsor - $penality - $mt_bank_transfert;
				if($check_total <= 0)$is_fact = 0;
				
        		if($is_fact){
					
					$this->loadModel('UserCountry');
					$society_name = '';
					$society_address = '';
					$society_postalcode = '';
					$society_city = '';
					$society_country = '';
					$society_num = '';
					$vat_num = '';
					$vat_status = '';
					$payment_mode = '';
					
					if($agent['User']['societe']){
						$society_name = ($agent['User']['societe'])."\n".$agent['User']['lastname'].' '.$agent['User']['firstname'];	
					}else{
						$society_name = $agent['User']['lastname'].' '.$agent['User']['firstname'];	
					}
	
					if($agent['User']['societe'] && $agent['User']['societe_adress']){
						$cc_infos = '';
						if($agent['User']['societe_pays'])
						 $cc_infos = $this->UserCountry->find('first',array(
							'fields' => array('CountryLang.name'),
							'conditions' => array('UserCountry.id' => $agent['User']['societe_pays']),
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

						
						$society_address = $agent['User']['societe_adress'].' '.$agent['User']['societe_adress2'];
						$society_postalcode = $agent['User']['societe_cp'];
						$society_city = $agent['User']['societe_ville'];

						if($cc_infos)
							$society_country = $cc_infos['CountryLang']['name'];

					}else{
						
						$cc_infos = '';
						if($agent['User']['societe_pays'])
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

						$society_address = $agent['User']['address'];
						$society_postalcode = $agent['User']['postalcode'];
						$society_city = $agent['User']['city'];
						if($cc_infos)
							$society_country = $cc_infos['CountryLang']['name'];
					}
	
	
					if($agent['User']['siret'] && !$agent['User']['belgium_save_num'] && !$agent['User']['belgium_society_num'] && !$agent['User']['canada_id_hst'] && !$agent['User']['spain_cif'] && !$agent['User']['luxembourg_autorisation'] && !$agent['User']['luxembourg_commerce_registrar'] && !$agent['User']['marocco_ice'] && !$agent['User']['marocco_if'] && !$agent['User']['portugal_nif'] && !$agent['User']['senegal_ninea'] && !$agent['User']['senegal_rccm'] && !$agent['User']['tunisia_rc'] )
					$society_num .= 'SIRET : '.$agent['User']['siret']."\n";
				if($agent['User']['belgium_save_num'])
					$society_num .= utf8_decode('recording N° : ').$agent['User']['belgium_save_num']."\n";
				if($agent['User']['belgium_society_num'])
					$society_num .= utf8_decode('Society N° : ').$agent['User']['belgium_society_num']."\n";
				if($agent['User']['canada_id_hst'])
					$society_num .= 'HST ID : '.$agent['User']['canada_id_hst']."\n";
				if($agent['User']['spain_cif'])
					$society_num .= 'CIF (NIF) : '.$agent['User']['spain_cif']."\n";
				if($agent['User']['luxembourg_autorisation'])
					$society_num .= utf8_decode('Authorization n° : ').$agent['User']['luxembourg_autorisation']."\n";
				if($agent['User']['luxembourg_commerce_registrar'])
					$society_num .= utf8_decode('The commercial register n° : ').$agent['User']['luxembourg_commerce_registrar']."\n";
				if($agent['User']['marocco_ice'])
					$society_num .= 'I.C.E : '.$agent['User']['marocco_ice']."\n";
				if($agent['User']['marocco_if'])
					$society_num .= 'I.F : '.$agent['User']['marocco_if']."\n";
				if($agent['User']['portugal_nif'])
					$society_num .= 'NIF / NIPC : '.$agent['User']['portugal_nif']."\n";
				if($agent['User']['senegal_ninea'])
					$society_num .= 'NINEA : '.$agent['User']['senegal_ninea']."\n";
				if($agent['User']['senegal_rccm'])
					$society_num .= 'RCCM : '.$agent['User']['senegal_rccm']."\n";
				if($agent['User']['tunisia_rc'])
					$society_num .= 'R.C : '.$agent['User']['tunisia_rc']."\n";
				if($agent['User']['vat_num'])
					$society_num .= 'VAT : '.$agent['User']['vat_num']."\n";
					
					
					$vat_num = $agent['User']['vat_num'];
					$vat_status = $agent['User']['vat_num_status'];
					$payment_mode = 'stripe';
					if($mt_bank_transfert > 0)$payment_mode = 'bankwire';
					
					//get last index order_id
					$num = $this->InvoiceNum->find('first', array(
						'conditions' => array('InvoiceNum.society_id' => 2),
						'recursive' => -1
					));
					//save facture expert
					$saveData = array();
					$saveData['InvoiceAgent']['order_id'] = $num['InvoiceNum']['num'] + 1;
					$saveData['InvoiceAgent']['user_id'] = $agent['User']['id'];
					$saveData['InvoiceAgent']['society_name'] = $society_name;
					$saveData['InvoiceAgent']['society_address'] = $society_address;
					$saveData['InvoiceAgent']['society_postalcode'] = $society_postalcode;
					$saveData['InvoiceAgent']['society_city'] = $society_city;
					$saveData['InvoiceAgent']['society_country'] = $society_country;
					$saveData['InvoiceAgent']['society_num'] = $society_num;
					$saveData['InvoiceAgent']['vat_num'] = $vat_num;
					$saveData['InvoiceAgent']['vat_status'] = $vat_status;
					$saveData['InvoiceAgent']['payment_mode'] = $payment_mode;
					$saveData['InvoiceAgent']['date_add'] = date('Y-m-d H:i:s');
					$saveData['InvoiceAgent']['date_min'] = $datemin;
					$saveData['InvoiceAgent']['date_max'] = $datemax;
					$saveData['InvoiceAgent']['ca'] = $ca;
					$saveData['InvoiceAgent']['paid'] = $agent_paid;
					$saveData['InvoiceAgent']['paid_total'] = $agent_paid_total + $order_line + $bonus + $sponsor - $penality - $mt_bank_transfert;
					$saveData['InvoiceAgent']['other'] = $order_line;
					$saveData['InvoiceAgent']['penality'] = $penality;
					$saveData['InvoiceAgent']['bonus'] = $bonus;
					$saveData['InvoiceAgent']['sponsor'] = $sponsor;
					$saveData['InvoiceAgent']['amount'] = number_format($ca - $agent_order_amount,2,'.','');
					$saveData['InvoiceAgent']['vat_tx'] = $vat_tx;
					$saveData['InvoiceAgent']['vat'] = $vat;
					$saveData['InvoiceAgent']['amount_total'] = number_format($ca - $agent_order_amount + $vat,2,'.','');
					$saveData['InvoiceAgent']['currency'] = '€';
					$this->InvoiceAgent->create();
					if($this->InvoiceAgent->save($saveData)){
						$saveData = array();
						$saveData['InvoiceAgentDetail']['invoice_id'] = $this->InvoiceAgent->id;
						$saveData['InvoiceAgentDetail']['type'] = 'fees';
						$saveData['InvoiceAgentDetail']['amount'] = number_format($ca - $agent_order_amount + $vat ,2,'.','');
						$saveData['InvoiceAgentDetail']['label'] = 'Frais de service Glassgen pour la periode '.$date_perdiodemin . ' au '.$date_perdiode_max;
						$this->InvoiceAgentDetail->create();
						$this->InvoiceAgentDetail->save($saveData);
						
						$this->InvoiceNum->id = $num['InvoiceNum']['id'];
						$this->InvoiceNum->saveField('num', $num['InvoiceNum']['num'] + 1);
						
						//Retrieve Plateforme Fees
						$fees = number_format($ca - $agent_order_amount + $vat ,2,'.','');
            $fees_bug = number_format($ca_pay - $agent_order_amount + $vat ,2,'.','');
						$expert_gain = $agent_paid_total + $order_line + $bonus + $sponsor - $penality - $mt_bank_transfert;
						if($fees_bug > 0 && $agent_info['User']['stripe_account']){

							try {
								$account = \Stripe\Account::retrieve();
								
								$stripe_balance = 0;
									$balance = \Stripe\Balance::retrieve(
										  ["stripe_account" => $agent_info['User']['stripe_account']]
										);
								if($balance->available && is_array($balance->available)){
									$available = $balance->available[0];
									$stripe_balance += $available->amount /100;
								}
								if($balance->pending && is_array($balance->pending)){
									$available = $balance->pending[0];
									$stripe_balance += $available->amount /100;
								}
								if(!$agent_info['User']['stripe_base'])$agent_info['User']['stripe_base'] = 0;
								$diff = number_format($stripe_balance - $expert_gain - $agent['User']['stripe_base'],2,'.','');
								$pp = $diff * 100 ; //$fees  * 100;
								if($pp>1)
								\Stripe\Transfer::create(
											  [
												"amount" => $pp,
												"currency" => "eur",
												"destination" => $account->id
											  ],
											  ["stripe_account" => $agent_info['User']['stripe_account']]
											);

							} catch (\Stripe\Error\Base $e) {
								$datasEmail = array(
											'content' => $e->getMessage(). ' Fees expert =>'.$agent_info['User']['id'],
											'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
								);
								$extractrl->sendEmail('system@web-sigle.fr','BUG fees expert','default',$datasEmail);
							}
						}
            if(!$is_available_stripe && $agent_info['User']['stripe_account']){
              //refund bankwire sold 
              try {
								$account = \Stripe\Account::retrieve();
								
								$pp = $expert_gain * 100;
								if($pp>1)
								\Stripe\Transfer::create(
											  [
												"amount" => $pp,
												"currency" => "eur",
												"destination" => $account->id
											  ],
											  ["stripe_account" => $agent_info['User']['stripe_account']]
											);

							} catch (\Stripe\Error\Base $e) {
								$datasEmail = array(
											'content' => $e->getMessage(). ' Sold expert =>'.$agent_info['User']['id'],
											'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
								);
								$extractrl->sendEmail('system@web-sigle.fr','BUG sold bankwire expert','default',$datasEmail);
							}
            }
					}
				}
			}
		}
    //$this->getStripeBalance();
	}
		
	public function getStripeBalance(){
		ini_set("memory_limit",-1);
			set_time_limit ( 0 );
		$this->loadModel('User');
		$this->loadModel('InvoiceAgent');
		
		App::import('Controller', 'Paymentstripe');
		$paymentctrl = new PaymentstripeController();

		require(APP.'Lib/stripe/init.php');
				
		\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
		
		$agents = $this->User->find("all", array(
					'conditions' => array(
						'User.role'      =>  'agent',
						'User.stripe_account !='      =>  '',
					)
				));
		foreach($agents as $agent){
				$stripe_balance = 0;
				$stripe_available = 0;
			try {
				$balance = \Stripe\Balance::retrieve(
						  ["stripe_account" => $agent['User']['stripe_account']]
						);
				if($balance->available && is_array($balance->available)){
					$available = $balance->available[0];
					$stripe_balance += $available->amount /100;
					$stripe_available += $available->amount /100;
				}
				if($balance->pending && is_array($balance->pending)){
					$available = $balance->pending[0];
					$stripe_balance += $available->amount /100;
				}
				
				$account = \Stripe\Account::retrieve($agent['User']['stripe_account']);
				$stripe_status = '';
				if(!$account->payouts_enabled){
					$stripe_status = implode(' ,',$account->requirements->currently_due);
					
				}

				$this->User->id = $agent['User']['id'];
				$this->User->saveField('stripe_balance', $stripe_balance);
				$this->User->saveField('stripe_available', $stripe_available);
				$this->User->saveField('stripe_payout_status', $stripe_status);
				
				//update Bank Account
				$is_same = true;
				$bank = $account->external_accounts;
				if($bank && is_object($bank)){
					if($bank->data){
					$last4 = $bank->data[0]->last4;
						if($last4){
							$iban = str_replace(' ','',$agent['User']['iban']);
							$iban_last = substr($iban,-4,4);
							if($last4 != $iban_last)$is_same = false;
						}
					}else{
						$is_same = false;
					}
				}else{
					$is_same = false;
				}
				if(!$is_same){
					$cpt_country = '';
					switch (strtolower($agent['User']['bank_country'])) {
								case 'allemagne':
									$cpt_country = 'DE';//Allemagne
									break;
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
					if(!$cpt_country){
						switch ($agent['User']['country_id']) {
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
					}
					if($cpt_country){
					$data = \Stripe\Account::update(
									  $agent['User']['stripe_account'],
									  [
										  'external_account' => [
											'object' => 'bank_account',
											'country' => $cpt_country,
											'currency' => 'EUR',
											'account_number' => $agent['User']['iban'],
										],
									  ]
									);
					}
				}
				
			 } catch (\Stripe\Error\Base $e) {

			}
		}
		
		
		//check facture a payer
		 $invoice_agents = $this->InvoiceAgent->find('all',array(
								'conditions' => array('InvoiceAgent.status' => 5),
								'recursive' => -1
							));
		foreach($invoice_agents as $invoice_agent){
			 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));
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
			 if($stripe_balance >= $invoice_agent['InvoiceAgent']['paid_total_valid']){
				 $this->InvoiceAgent->id =  $invoice_agent['InvoiceAgent']['id'];
				 if(!$invoice_agent['InvoiceAgent']['is_valid_2'])
				 	$this->InvoiceAgent->saveField('status', 3);
				 else
					 $this->InvoiceAgent->saveField('status', 6);
				 
				  $url = Router::url(array('controller' => 'agents', 'action' => 'order-'.$invoice_agent['InvoiceAgent']['id'], 'admin' => true),true);

				 $admin_emails = Configure::read('Site.emailsAdmins');
							 if(is_array($admin_emails)){
								 foreach($admin_emails as $email){
									$is_send = $this->sendCmsTemplatePublic(447, 1, $email, array(
											'AGENT' => $agent['User']['pseudo'].' : '.$agent['User']['firstname'].' '.$agent['User']['lastname'],
											'URL' =>$url,
											'AMOUNT_TOTAL' => $invoice_agent['InvoiceAgent']['paid_total_valid'],
										));
								 }
							 }
			 }
		}
		
		 $invoice_agents = $this->InvoiceAgent->find('all',array(
								'conditions' => array('InvoiceAgent.status' => 7),
								'recursive' => -1
							));
		foreach($invoice_agents as $invoice_agent){
			
			$this->InvoiceAgent->id =  $invoice_agent['InvoiceAgent']['id'];
			
		 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));
			try {
								$payout = \Stripe\Payout::retrieve($invoice_agent['InvoiceAgent']['payment_id'],
									  ["stripe_account" => $agent['User']['stripe_account']]
									);
								if($payout->status == 'in_transit'){
									 $this->InvoiceAgent->saveField('status', 9);
								}
								if($payout->status == 'paid'){
									 $this->InvoiceAgent->saveField('status', 1);
								}
							
							} catch (\Stripe\Error\Base $e) {

							}
			 
		}
		
		$invoice_agents = $this->InvoiceAgent->find('all',array(
								'conditions' => array('InvoiceAgent.status' => 9),
								'recursive' => -1
							));
		foreach($invoice_agents as $invoice_agent){
			
			$this->InvoiceAgent->id =  $invoice_agent['InvoiceAgent']['id'];
			
		 $agent = $this->User->find('first',array(
								'conditions' => array('User.id' => $invoice_agent['InvoiceAgent']['user_id']),
								'recursive' => -1
							));
			try {
								$payout = \Stripe\Payout::retrieve($invoice_agent['InvoiceAgent']['payment_id'],
									  ["stripe_account" => $agent['User']['stripe_account']]
									);
								if($payout->status == 'paid'){
									 $this->InvoiceAgent->saveField('status', 1);
								}
							
							} catch (\Stripe\Error\Base $e) {

							}
			 
		}
	}
		
	public function addRecords(){
		//Le chemin des enregistrements audio
        $paths = glob(Configure::read('Site.pathRecordCron').'/*.wav');
		
		$now = time();
		if(!empty($paths)){
			$this->loadModel('UserCreditLastHistory');
			$this->loadModel('Record');
			$nb = 0;
			$nb2 = 0;;
            foreach($paths as $file){
				if ($now - filemtime($file) < 24*3600*1)
				{ 
					//Le nom du fichier
                	$filename = basename($file);
					//On retire l'extension du fichier
					$filecut = substr($filename,0,(strripos($filename,'.') - strlen($filename)));
					//On explose les données
					$tmp = explode('-',$filecut);
					
					
					if($tmp[3]){
						$sessionid = $tmp[2].'-'.$tmp[3];

						$lastCom = $this->UserCreditLastHistory->find('first',array(
							'conditions' => array('sessionid' => $sessionid),
							'recursive' => -1
						));
						
						$check = $this->Record->find('first',array(
							'conditions' => array('sessionid' => $sessionid),
							'recursive' => -1
						));

						if($lastCom && !$check){
							$data = array();
							$data['Record'] = array();
							$data['Record']['agent_id'] = $lastCom["UserCreditLastHistory"]["agent_id"];
							$data['Record']['user_id'] = $lastCom["UserCreditLastHistory"]["users_id"];
							$data['Record']['time'] = $tmp[1];
							$data['Record']['sessionid'] = $sessionid;
							$data['Record']['date_add'] = date("Y-m-d H:i:s");
							$data['Record']['filename'] = $filename;
							$data['Record']['archive'] = 0;
							$data['Record']['deleted'] = 0;

							//On save
							$this->Record->create();
							$this->Record->save($data['Record']);
						}
					}
				 
				}
			}
			
			/*$path_new = str_replace('/records','/records_archive',Configure::read('Site.pathRecordCron'));
			 $paths = glob($path_new.'/*.wav');
		
		$now = time();
		if(!empty($paths)){
			$this->loadModel('UserCreditLastHistory');
			$this->loadModel('Record');
			$nb = 0;
			$nb2 = 0;;
            foreach($paths as $file){
					//Le nom du fichier
                	$filename = basename($file);
					//On retire l'extension du fichier
					$filecut = substr($filename,0,(strripos($filename,'.') - strlen($filename)));
					//On explose les données
					$tmp = explode('-',$filecut);
					
					
					if($tmp[3]){
						$sessionid = $tmp[2].'-'.$tmp[3];

						$lastCom = $this->UserCreditLastHistory->find('first',array(
							'conditions' => array('sessionid' => $sessionid),
							'recursive' => -1
						));
						
						$check = $this->Record->find('first',array(
							'conditions' => array('sessionid' => $sessionid),
							'recursive' => -1
						));

						if($lastCom && !$check){
							$data = array();
							$data['Record'] = array();
							$data['Record']['agent_id'] = $lastCom["UserCreditLastHistory"]["agent_id"];
							$data['Record']['user_id'] = $lastCom["UserCreditLastHistory"]["users_id"];
							$data['Record']['time'] = $tmp[1];
							$data['Record']['sessionid'] = $sessionid;
							$data['Record']['date_add'] = date("Y-m-d H:i:s");
							$data['Record']['filename'] = $filename;
							$data['Record']['archive'] = 1;
							$data['Record']['deleted'] = 0;

							//On save
							$this->Record->create();
							$this->Record->save($data['Record']);
						}
					}
				 
				}
			}*/
			exit;
		}
	}
		
	public function checkVatStatus(){
		set_time_limit ( 0 );
		$this->loadModel('User');
		
		$agents = $this->User->find("all", array(
					'fields' => array('id','vat_num','vat_num_status'),
					'conditions' => array(
						'User.role'      =>  'agent',
						'User.vat_num !='  =>  '',
						//'User.vat_num_status !='  =>  'valide',
						'User.active' => 1,
						'User.deleted' => 0,
					),
					'recursive' => -1
				));

		foreach($agents as $agent){
			if($agent['User']['vat_num_status'] != 'valide'){
				$result = '';
				$tva = str_replace(' ','',$agent['User']['vat_num']);
				$iso_code = substr($tva,0,2);
				$vat = substr($tva,2);
				$url = 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html?locale=FR&memberStateCode='.
				strtoupper($iso_code).'&number='.$vat.'&traderName=';
				ini_set('default_socket_timeout', 3);
				for ($i = 0; $i < 3; $i++) {
					if ($line = @file_get_contents($url)) {
						if (strstr($line, 'TVA valide')) {
							ini_restore('default_socket_timeout');
							$result = 'valide';
						}
						if (strstr($line, 'TVA invalide')) {
							ini_restore('default_socket_timeout');
							$result = 'invalide';
						}
						if (strstr($line, 'demandes trop nombreuses')) {
							ini_restore('default_socket_timeout');
							$result = 'Numero non verifie';
						}
						if( !$result ) $result = 'invalide';
					}
				}
				ini_restore('default_socket_timeout');
				$this->User->id = $agent['User']['id'];
				$this->User->saveField('vat_num_status', $result);
				//var_dump($agent['User']['id']. ' -> '.$result);
			}
		}
		
	}
		
	public function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {

		foreach($messageParts as $part) {
			$flattenedParts[$prefix.$index] = $part;
			if(isset($part->parts)) {
				if($part->type == 2) {
					$flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);
				}
				elseif($fullPrefix) {
					$flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');
				}
				else {
					$flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix);
				}
				unset($flattenedParts[$prefix.$index]->parts);
			}
			$index++;
		}

		return $flattenedParts;

	}
	
	public function getPart($connection, $messageNumber, $partNumber, $encoding) {
	
	$data = imap_fetchbody($connection, $messageNumber, $partNumber);
	switch($encoding) {
		case 0: return $data; // 7BIT
		case 1: return $data; // 8BIT
		case 2: return $data; // BINARY
		case 3: return base64_decode($data); // BASE64
		case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
		case 5: return $data; // OTHER
	}
	
	
}

	public function getFilenameFromPart($part) {

		$filename = '';

		if($part->ifdparameters) {
			foreach($part->dparameters as $object) {
				if(strtolower($object->attribute) == 'filename') {
					$filename = $object->value;
				}
			}
		}

		if(!$filename && $part->ifparameters) {
			foreach($part->parameters as $object) {
				if(strtolower($object->attribute) == 'name') {
					$filename = $object->value;
				}
			}
		}

		return $filename;

	}
		
	public function decoder($body){
		$body = quoted_printable_decode($body);//Convertit une chaîne quoted-printable en chaîne 8 bits
		$body = imap_utf8($body);//Convertit une chaîne UTF-8 en ISO-8859-1 //Convertit du texte au format MIME en UTF8
		//$body = quoted_printable_decode($body);//Convertit une chaîne quoted-printable en chaîne 8 bits
		//$body = utf8_encode(($body));//Convertit une chaîne UTF-8 en ISO-8859-1 //Convertit du texte au format MIME en UTF8
		$body = stripslashes($body);//Supprime les anti-slash d'une chaîne
		$body = trim($body);//Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
		$body = html_entity_decode($body);//Convertit toutes les entités HTML en caractères normaux
		$body = str_replace('<br>','',$body);
		return $body;
	}
		
	public function supportReadMail(){
		error_reporting(E_ALL & ~E_NOTICE);
		
		$this->loadModel('Support');
		$this->loadModel('SupportMessage');
		$this->loadModel('SupportService');
		$this->loadModel('SupportAdmin');
    $this->loadModel('SupportMessageAttachment');
		
		$listing_utcdec = Configure::read('Site.utcDec');
		
		$services = $this->SupportService->find('all', array(
				'fields' => array('SupportService.*'),
                'conditions' => array('SupportService.status' => 1),
				'group' => '`SupportService`.`mail`',
                'recursive' => -1
            ));
		
		foreach($services as $service){

			$box_mail = $service['SupportService']['mail'];
			$box_password = 'spiriteo2020support';
			
			
			/*$box_mail = 'serviceclient@talkappdev.com';
			$box_password = 'Spi2020SC';*/
			
			
			$mbox = '';
			if($box_mail){
				try {
					//echo $box_mail;
					$mbox = @imap_open("{SSL0.OVH.NET:143/novalidate-cert}INBOX", $box_mail, $box_password , 0, 0, array('DISABLE_AUTHENTICATOR' => 'GSSAPI'));
					 //or die("Connexion impossible : " . imap_last_error());
					//imap_errors();
					//imap_alerts();
					//echo 'ok';
				}
				 catch (Exception $e) {
								// var_dump($e->getMessage());
								$datasEmail = array(
											'content' => $e->getMessage(). ' boxmail =>'.$box_mail,
											'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
								);
								$extractrl->sendEmail('system@web-sigle.fr','BUG support email','default',$datasEmail);
				}
			}
			if($mbox){
				$MC = imap_check($mbox);

				$result = imap_search( $mbox, "UNSEEN");//ALL

			if($result && is_array($result))
			 foreach($result as $msgno){
				 $overview = imap_fetch_overview($mbox,$msgno,0);
				// $body = imap_fetchbody($mbox,$msgno,1);
				 $headers = imap_headerinfo($mbox, $msgno);
				  $header = imap_header($mbox, $msgno);
				$from = $header->from;
				foreach ($from as $id => $object) {
					if(!empty($object->personal))
						$fromname = $object->personal;
					else
						$fromname = $object->mailbox;
					$fromaddress = $object->mailbox . "@" . $object->host;
				}
				 $flattenedParts = array();
				 $structure = imap_fetchstructure($mbox, $msgno);
				 if($structure && !empty($structure->parts))
				 $flattenedParts = $this->flattenParts($structure->parts);
				 $attachment = array();
				 //var_dump($flattenedParts);exit;
				 $message = '';
				 foreach($flattenedParts as $partNumber => $part) {

					switch($part->type) {

						case 0:
							// the HTML or plain text part of the email
							if($part->subtype == 'PLAIN')
							$message = $this->getPart($mbox, $msgno, $partNumber ,$part->encoding );
							// now do something with the message, e.g. render it
						break;

						case 1:
							// multi-part headers, can ignore
							
						break;
						case 2:
							// attached message headers, can ignore
							
						break;

						case 3: // application
						case 4: // audio
						case 5: // image
						case 6: // video
						case 7: // other
							 $filename = $this->getFilenameFromPart($part);
								if($filename) {
									// it's an attachment
									$attach = $this->getPart($mbox, $msgno, $partNumber, $part->encoding);
									// now do something with the attachment, e.g. save it somewhere
									array_push($attachment,array(
									'filename' => ($filename),
									'extension' => (strtolower($part->subtype)),
									'attachment' => $attach));
								}
								else {
												// don't know what it is
									
								}
						break;

					}
					//var_dump(base64_decode(imap_fetchbody($mbox,$msgno,1)));exit;
					 
				 }
				 
				 
				 //clean
				 $message_decode = $this->decoder($message);
				 
				 if(!substr_count($message_decode,'??'))
					 $message = $message_decode;
         
         if(empty($message) && $structure->subtype=='HTML'){
           $message = $this->getPart($mbox, $msgno, 1 ,$structure->encoding );
         }
				 
				 if(empty($message)){
					 $message = $this->getPart($mbox, $msgno, 1 ,4 );
				 }
				 if(empty($message)){
					 $message = $this->getPart($mbox, $msgno, 2 ,4 );
				 }

				 if(substr_count($message,'<div style="border:none;border-top:solid #E1E1E1 1.0pt;padding:3.0pt 0cm 0cm 0cm">')){
					 $text = explode('<div style="border:none;border-top:solid #E1E1E1 1.0pt;padding:3.0pt 0cm 0cm 0cm">',$message);
				 	$message = $text[0].'</div>';
				 }
				 if(substr_count($message,' <div style="mso-element:para-border-div;border:none;border-top:solid #E1E1E1 1.0pt;padding:3.0pt 0cm 0cm 0cm">')){
					 $text = explode(' <div style="mso-element:para-border-div;border:none;border-top:solid #E1E1E1 1.0pt;padding:3.0pt 0cm 0cm 0cm">',$message);
				 	$message = $text[0].'</div>';
				 }
         
          if(substr_count($message,'-------- Message original --------')){
           $text = explode('-------- Message original --------',$message);
				 	$message = $text[0];
         }
				 
				$message_encoded = utf8_encode($message);
				 if(!substr_count($message_encoded,'©'))
				 	$message = $message_encoded;
         
				 
				 if(!empty($headers->subject))
				 	$title = $headers->subject;
					
				 $mail_date = $headers->date;
				 $ref = null;
				 $cut_title = explode('[#',$title);
				 if(count($cut_title)>0 && !empty($cut_title[1])){
					 $cut_ref = explode(']',$cut_title[1]);
					 $ref = $cut_ref[0];
				 }
				 
				 if(!$ref){
					 $title = $this->decoder($headers->subject);
					 $cut_title = explode('[#',$title);
					 if(count($cut_title)>0 && !empty($cut_title[1])){
						 $cut_ref = explode(']',$cut_title[1]);
						 $ref = $cut_ref[0];
					 }
				 }
				  if(!$ref){
					 $subject = imap_mime_header_decode($headers->subject);
					 $title = $subject[0]->text;
					 $cut_title = explode('[#',$title);
					 if(count($cut_title)>0 && !empty($cut_title[1])){
						 $cut_ref = explode(']',$cut_title[1]);
						 $ref = $cut_ref[0];
					 }
				 }

				 if(!$ref) $title = 'nouveau message';
				 
				 if(!empty($ref) && is_numeric($ref)){
						
						
						
						$support = $this->Support->find('first', array(
							'conditions' => array('Support.id' => $ref),
							'recursive' => -1
						));
						
						if($support){
							
							if($support['Support']['status'] < 2){
							
								$messages = $this->SupportMessage->find('all',array(
								'conditions' => array('support_id' => $support['Support']['id']),
									'recursive' => -1,
									'order' => 'SupportMessage.date_add asc',
								));

								//update status to read OK
								foreach($messages as $mes){
									if(!$mes['SupportMessage']['etat'] && $mes['SupportMessage']['from_id'] != $support['Support']['from_id']){
										$this->SupportMessage->id = $mes['SupportMessage']['id'];
										$this->SupportMessage->saveField('etat', 1);
									}
								}

								$message_date = new DateTime($mail_date);
								$message_date->modify('-'.$listing_utcdec[$message_date->format('md')].' hour');
								$m_date = $message_date->format('Y-m-d H:i:s');

								//check si message deja traité
								$do_message = $this->SupportMessage->find('all',array(
								'conditions' => array('support_id' => $support['Support']['id'], 'from_id' => $support['Support']['from_id'],'date_message' => $m_date),
									'recursive' => -1,
								));

								if(!$do_message){
									$this->Support->id = $support['Support']['id'];
									$this->Support->saveField('status', 0);
									$this->Support->saveField('date_upd', date('Y-m-d H:is'));

									//SAVE SUPPORT MESSAGE
					$hasAttachment = false;
					if(count($attachment))$hasAttachment = true;

									$this->SupportMessage->create();
									if($this->SupportMessage->save(array(
											'support_id'   => $support['Support']['id'],
											'from_id'   => $support['Support']['from_id'],
											'guest_id'  => $support['Support']['guest_id'],
											'to_id'     => Configure::read('Admin.id'),
											'content'   => $message,
											'date_message'   => $m_date,
											'date_add'   => date('Y-m-d H:i:s'),
											'etat'      => 0,
						'hasAttachment' => $hasAttachment,
											'IP'		=> $m_date
									))){
										//Attachment $attachment

										$n_attach = 0;
					  $maxSelfSupportMessageAttachment = 2;
										 foreach($attachment as $file){
						$fileKey = ($key === 0) ? '' : '-' . ($key + 1);
											 $destPath = Configure::read('Site.pathSupportAdmin').'/'.$support['Support']['id'][0].'/'.$support['Support']['id'][1];

											 if(!is_dir($destPath))
												 mkdir($destPath);

											 $filename = $this->SupportMessage->id.'-'.$n_attach.'.'.$file['extension'];
						 //Tools::saveSupportAttachment($file['attachment'], Configure::read('Site.pathSupportAdmin'), $support['Support']['id'], $this->SupportMessage->id, $fileKey);
						 file_put_contents($destPath.'/'.$filename, $file['attachment']);
						//add new attachment
						$this->SupportMessageAttachment->create();
						//build filename
						$this->SupportMessageAttachment->save(array(
						  'support_message_id' => $this->SupportMessage->id,
						  'name' => $filename
						));
						//old attachment save
						if ($n_attach < $maxSelfSupportMessageAttachment) {
						  $this->SupportMessage->saveField('attachment' . $fileKey, $fileName);
						}
											/* file_put_contents($destPath.'/'.$filename, $file['attachment']);
											 if($n_attach < 1)
												$this->SupportMessage->saveField('attachment', $filename);
											 if($n_attach > 0)
												 $this->SupportMessage->saveField('attachment2', $filename);*/
											 $n_attach ++;

										 }

										//Send Email Admin
										$admins = $this->SupportAdmin->find('all',array(
											'fields' => array('User.email'),
										'conditions' => array('service_id' => $support['Support']['service_id'],'level' => 1),
											'joins' => array(
														array('table' => 'users',
															  'alias' => 'User',
															  'type' => 'left',
															  'conditions' => array('User.id = SupportAdmin.user_id')
														)
													),
											'recursive' => -1,
										));
										$service = $this->SupportService->find('first',array(
											'conditions' => array('id' => $support['Support']['service_id']),
											'recursive' => -1,
										));
										$title = _('Nouveau message Support / ').$service['SupportService']['name'].' - '.ucfirst($service['SupportService']['who']);

										foreach($admins as $admin){
											$this->sendCmsTemplateByMail(452, 1, $admin['User']['email'], array(
												'URL_TICKET' => 'https://fr.spiriteo.com/admin/support/fil/'.$support['Support']['id'],
												'SUBJECTSUPPORT' => $title,	
											));
										}


									}
								}
							}else{
								if($fromaddress)
					 			$this->sendCmsTemplateByMail(463, 1, $fromaddress, array());
							}
						}else{
							if($fromaddress)
					 			$this->sendCmsTemplateByMail(463, 1, $fromaddress, array());
						}
				}else{
					 if($fromaddress)
					 $this->sendCmsTemplateByMail(454, 1, $fromaddress, array());
				 }
				 
				 
			 }

			imap_close($mbox);
		}
		}
	}
		
	public function calcExportCom(){

		set_time_limit ( 0 );
		ini_set("memory_limit",-1);


		//Charge model
        $this->loadModel('UserCreditHistory');
		$this->loadModel('UserPenality');
		$this->loadModel('User');
		$this->loadModel('UserPay');
		$this->loadModel('Cost');
		$this->loadModel('CostAgent');
		$this->loadModel('BonusAgent');
		$this->loadModel('CallInfo');
		$this->loadModel('ExportCom');
		$this->loadModel('CostPhone');


		$listing_utcdec = Configure::read('Site.utcDec');

		$dstart = date('Y-m-d 00:00:00');
		$dend = date('Y-m-d 23:59:59');
		
		$dx = new DateTime($dstart);
		$dx->modify('- 1 days');
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$delai = $dx->format('Y-m-d H:i:s');

		$dx2 = new DateTime($dend);
		$dx2->modify('- 1 days');
		$dx2->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$delai_max = $dx2->format('Y-m-d H:i:s');

		$conditions = array('UserCreditHistory.is_factured' => 1);
		$conditions_refund = array();

		$conditions = array_merge($conditions, array(
                'UserCreditHistory.date_start >=' => $delai,
                'UserCreditHistory.date_start <' => $delai_max,

            ));
		$conditions_refund = array_merge($conditions_refund, array(
                'UserPenality.date_add >=' => $delai,
                'UserPenality.date_add <' => $delai_max
            ));

		//Les données à sortir
		$new_list = array();
        $allComDatas = $this->UserCreditHistory->find('all', array(
            'fields'        => array('UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured','UserCreditHistory.ca','UserCreditHistory.ca_currency','UserCreditHistory.is_mobile','UserCreditHistory.expert_number',
			'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email', 'Agent.order_cat', 'Agent.phone_number2', 'Agent.phone_mobile', 'Agent.phone_operator', 'Agent.phone_operator2', 'Agent.phone_operator3'),
            'conditions'    => $conditions,
            'order'         => 'UserCreditHistory.date_start ASC'
        ));




		$conditions_refund = array_merge($conditions_refund, array('UserPenality.is_factured'=>1,'UserPenality.message_id >'=>0));

		$allRefundDatas = $this->UserPenality->find('all', array(
				'fields'        => array('UserPenality.date_add','UserPenality.is_factured','UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured','UserCreditHistory.ca','UserCreditHistory.ca_currency','UserCreditHistory.is_mobile','UserCreditHistory.expert_number',
				'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email', 'Agent.order_cat', 'Agent.phone_number2', 'Agent.phone_mobile', 'Agent.phone_operator', 'Agent.phone_operator2', 'Agent.phone_operator3'),
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
				'order'         => 'UserPenality.date_add ASC'
			));

		if(count($allRefundDatas)){

				foreach($allComDatas as $todo){
					$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
				}

				foreach($allRefundDatas as &$refund){
					if($refund['UserPenality']['is_factured']){
						$refund['UserCreditHistory']['date_start'] = $refund['UserPenality']['date_add'];
						if(isset($refund['UserPay']))
							$refund['UserPay']['price'] = $refund['UserPay']['price']  * -1;
						else
							$refund['UserPay']['price'] = 0;
						$refund['UserCreditHistory']['credits'] = $refund['UserCreditHistory']['credits'] * -1;
						$refund['UserCreditHistory']['ca'] = $refund['UserCreditHistory']['ca'] * -1;
						$new_list[$refund['UserCreditHistory']['date_start']] = $refund;
					}
				}
				krsort($new_list);
				$allComDatas = array();
				foreach($new_list as $cc){
					array_push($allComDatas,$cc);
				}
		}


		foreach($allComDatas as $indice => $row){

			$userPay = $this->UserPay->find('first', array(
					'conditions'    => array('UserPay.id_user_credit_history' => $row['UserCreditHistory']['user_credit_history']),

				));

			$cost = $this->Cost->find('first', array(
					'fields'        => array('Cost.cost'),
					'conditions'    => array('Cost.id' => $row['Agent']['order_cat']),

				));

			$cost_agent = $this->CostAgent->find('first', array(
					'fields'        => array('CostAgent.nb_minutes'),
					'conditions'    => array('CostAgent.id_agent' => $row['Agent']['id']),

				));

			$bonus_agent = $this->BonusAgent->find('first', array(
					'fields'        => array('BonusAgent.min_total'),
					'conditions'    => array( 'BonusAgent.id_agent' => $row['Agent']['id'],'BonusAgent.date_add >=' => $row['UserCreditHistory']['date_start'], 'BonusAgent.active' => 1),
					'order'			=> 'BonusAgent.id'
				));
			
			$cost_phones = $this->CostPhone->find('all', array(

				));

			//recup data call info
			if($row['UserCreditHistory']['sessionid']){
				 $callinfo = $this->CallInfo->find('first', array(
					'fields'        => array('CallInfo.callerid','CallInfo.line'),
					'conditions'    => array('CallInfo.sessionid' => $row['UserCreditHistory']['sessionid']),

				));
			}else{
				$callinfo = array();
			}

			$code_iso = '';
			switch ($row['UserCreditHistory']['domain_id']) {
				case 11:
					$code_iso = 'Belgique';
					break;
				case 13:
					$code_iso = 'Suisse';
					break;
				case 19:
					$code_iso = 'France';
					break;
				case 22:
					$code_iso = 'Luxembourg';
					break;
				case 29:
					$code_iso = 'Canada';
					break;
			}
			$timing = explode(' ',$row['UserCreditHistory']['date_start']);
			$heures = intval(($row['UserCreditHistory']['seconds']) / 60 / 60);
			$minutes = intval(($row['UserCreditHistory']['seconds'] % 3600) / 60);
			$secondes =intval((($row['UserCreditHistory']['seconds'] % 3600) % 60));
			$price = $userPay['UserPay']['price'];
			if($cost){
				$cost = $cost['Cost']['cost'];
				
				if($row['UserCreditHistory']['is_mobile']){
						foreach($cost_phones as $cost_phone){
							if(substr($row['UserCreditHistory']['expert_number'],0,strlen($cost_phone['CostPhone']['indicatif'])) == $cost_phone['CostPhone']['indicatif'])
								$cost = $cost -$cost_phone['CostPhone']['cost'];
						}
				}
			}else
				$cost = 0;
			if($callinfo){
				$caller = $callinfo['CallInfo']['callerid'];
				$caller_line = $callinfo['CallInfo']['line'];
			}else{
				$caller = '';
				$caller_line = '';
			}


			$called = '';
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

			switch ($caller_line) {
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
						'conditions' => array('phone_number' => $caller),
						'recursive' => -1
					));
					$nb_appels_today = $this->UserCreditHistory->find('count', array(
					'conditions' => array('phone_number' => $caller,  'DATE(date_start) >=' => $timing[0].' 00:00:00',  'DATE(date_start) <=' => $timing[0].' 23:59:59'),
					'recursive' => -1
				));

				$date_1_appel = $this->UserCreditHistory->find('first', array(
						'conditions' => array('phone_number' => $caller),
						'recursive' => -1,
						'order' => 'date_start asc'
					));


				}else{
					$nb_appels = '';
					$nb_appels_today = '';
					$date_1_appel = '';
				}
			}


			$is_audiotel = 0;
			if(substr_count($row['User']['firstname'] , 'AUDIOTEL')){
				if(is_numeric($caller)){
					$row['User']['firstname'] =  'AT'.substr($caller, -6);
					$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4)*15);
				}else{
					$row['User']['firstname'] =  'AT'.substr($caller, -6);
					$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4));
				}
				if($date_1_appel)
					$row['User']['date_add'] = $date_1_appel['UserCreditHistory']['date_start'];
				else
					$row['User']['date_add'] = $row['UserCreditHistory']['date_start'];
				$is_audiotel = 1;
			}

			$premier_appel = '';

			if($row['UserCreditHistory']['is_new'])$premier_appel = 'X';

			if(!$called){
				$called = $code_iso;
			}
			$date_premiere_consult = '';
			$delai_avant_consult = '';
			$concordance = '';


			if($premier_appel == 'X' && !$is_audiotel){


				$date_premiere_consult = $date_1_appel['UserCreditHistory']['date_start'] ;
				$dateins = $row['User']['date_add'];

				$date1 = new DateTime($dateins);
				$date2= new DateTime($date_1_appel['UserCreditHistory']['date_start']);
				$date=$date2->diff($date1);
				//$diffInSeconds = $date2->getTimestamp() - $date1->getTimestamp();
				$minutes = $date->days * 24 * 60;
				$minutes += $date->h * 60;
				$minutes += $date->i;
				$delai_avant_consult = $minutes;//$date->format('%I');//%a jours, %h heures, %i minutes et %s secondes

				if($row['UserCreditHistory']['phone_number'] && is_numeric($row['UserCreditHistory']['phone_number']) &&  $row['UserCreditHistory']['phone_number'] != 'UNKNOWN' &&  $row['UserCreditHistory']['phone_number'] != '43123001999'){
					$concordance_data = $this->UserCreditHistory->find('first', array(
							'conditions' => array('phone_number' => $row['UserCreditHistory']['phone_number'], 'date_start <' => $date_1_appel['UserCreditHistory']['date_start'], 'type_pay' => 'aud'),
							'recursive' => -1,
							'order' => 'date_start asc'
						));

					if($concordance_data && $concordance_data['UserCreditHistory']['date_start'] != $date_1_appel['UserCreditHistory']['date_start']){
						$concordance = utf8_decode('Concordance client Prépayé audiotel');
					}
					//$concordance = Tools::dateUser('Europe/Paris',$concordance_data['UserCreditHistory']['date_start']);
				}
			}

			//patch comm non facturé
			if(!$row['UserCreditHistory']['is_factured'] && $row['UserCreditHistory']['media'] == 'email'){
				$price = 0;
				$row['UserCreditHistory']['credits'] = $row['UserCreditHistory']['credits'] * -1;
			}

			$ca_euro = 0;
			$ca_dollar = 0;
			$ca_chf = 0;
			switch ($row['UserCreditHistory']['ca_currency']) {
				case '€':
					$ca_euro = $row['UserCreditHistory']['ca'];
				break;
				case '$':
					$ca_dollar = $row['UserCreditHistory']['ca'];
				break;
				case 'CHF':
					$ca_chf = $row['UserCreditHistory']['ca'];
				break;
			}
			
			$ca_euro =  $userPay['UserPay']['ca'];
			
			if($row['UserCreditHistory']['credits'] < 0 && $price > 0){
				$price = $price * -1;
				$ca_euro = 0;
				$ca_dollar = 0;
				$ca_chf = 0;
			}

			$min_total = 0;
			if($bonus_agent)$min_total = $bonus_agent['BonusAgent']['min_total'];
			
			$phone_operator = '';
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_number'])$phone_operator = $row['Agent']['phone_operator'];
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_number2'])$phone_operator = $row['Agent']['phone_operator2'];
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_mobile'])$phone_operator = $row['Agent']['phone_operator3'];

			$line = array(
				'user_credit_history_id'=> $row['UserCreditHistory']['user_credit_history'],
				'agent_id'=> $row['Agent']['id'],
				'user_id'=> $row['User']['id'],
                'agent_number'      => $row['Agent']['agent_number'],
                'agent_pseudo'      => $row['Agent']['pseudo'],
                'agent_firstname'   => $row['Agent']['agent_firstname'],
                'agent_lastname'    => $row['Agent']['agent_lastname'],
                'agent_email'       => $row['Agent']['email'],
                'user_code'         => $row['User']['personal_code'],
                'user_firstname'    => $row['User']['firstname'],
                'user_lastname'     => $row['User']['lastname'],
				'user_domain'       => $code_iso,
				'user_date_add'     => $row['User']['date_add'],
				'first_call'		=> $premier_appel,
				'concordance'	=> $concordance,
				'date_first_consult'		=> $date_premiere_consult,
				'delay_before_first_consult	'		=> $delai_avant_consult,
				'nb_calls'=> $nb_appels,
				'nb_calls_day'   => $nb_appels_today,
                'user_credit_now'       => $row['User']['credit'],
                'media'             => $row['UserCreditHistory']['media'],
                'credits'           => ($row['User']['personal_code']!=999999)?$row['UserCreditHistory']['credits']:'',
                'seconds'           => $row['UserCreditHistory']['seconds'],
				'minutes'           => $heures . ' h '.$minutes. ' min '. $secondes .' sec',
                'called_number'     => $row['UserCreditHistory']['called_number'],
				'called'            => $called,
                'phone_agent'       => $row['UserCreditHistory']['expert_number'],
				'phone_operator'    => $phone_operator,
				'caller'            => $caller,
				'caller_line'       => $caller_line,
				'sessionid'         => $row['UserCreditHistory']['sessionid'],
			    'time_start'        => $timing[1],
                'date_start'        => $row['UserCreditHistory']['date_start'],
                'date_end'          => $row['UserCreditHistory']['date_end'],
				'tx_minute'     => $cost,
				'tx_second'    => $cost /60,
				'price'      => $price,
				'ca_euro'      		=> $ca_euro,
				'ca_chf'      		=> $ca_chf,
				'ca_dollar'      	=> $ca_dollar,
				'total_seconds_month'  => $min_total,
				'total_seconds'       => $cost_agent['CostAgent']['nb_minutes'] * 60,

            );

			/*$check_data = $this->ExportCom->find('first', array(
							'conditions' => array('user_credit_history_id' => $row['UserCreditHistory']['user_credit_history']),
							'recursive' => -1,
						));
			if(!$check_data){*/
				$this->ExportCom->create();
				$this->ExportCom->save($line);
			//}

        }
	}
		
	public function calcExportComUpdate(){

		set_time_limit ( 0 );
		ini_set("memory_limit",-1);


		//Charge model
        $this->loadModel('UserCreditHistory');
		$this->loadModel('UserPenality');
		$this->loadModel('User');
		$this->loadModel('UserPay');
		$this->loadModel('Cost');
		$this->loadModel('CostAgent');
		$this->loadModel('BonusAgent');
		$this->loadModel('CallInfo');
		$this->loadModel('ExportCom');
		$this->loadModel('CostPhone');


		$listing_utcdec = Configure::read('Site.utcDec');

		$dstart = date('Y-m-d 00:00:00');
		$dend = date('Y-m-d 23:59:59');

		$dx = new DateTime($dstart);
		$dx->modify('- 1000 days');
		$dx->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$delai = $dx->format('Y-m-d H:i:s');

		$dx2 = new DateTime($dend);
		$dx2->modify('- 1 days');
		$dx2->modify('-'.$listing_utcdec[$dx->format('md')].' hour');
		$delai_max = $dx2->format('Y-m-d H:i:s');

		$conditions = array('UserCreditHistory.is_factured' => 1);
		$conditions_refund = array();

		$conditions = array_merge($conditions, array(
               // 'UserCreditHistory.date_start >=' => $delai,
               // 'UserCreditHistory.date_start <' => $delai_max,
				'UserCreditHistory.user_credit_history' => 531796,

            ));
		$conditions_refund = array_merge($conditions_refund, array(
                'UserPenality.date_add >=' => $delai,
                'UserPenality.date_add <' => $delai_max
            ));


		//Les données à sortir
		$new_list = array();
        $allComDatas = $this->UserCreditHistory->find('all', array(
            'fields'        => array('UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured','UserCreditHistory.ca','UserCreditHistory.ca_currency','UserCreditHistory.is_mobile','UserCreditHistory.expert_number',
			'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email', 'Agent.order_cat', 'Agent.phone_number2', 'Agent.phone_mobile', 'Agent.phone_operator', 'Agent.phone_operator2', 'Agent.phone_operator3'),
            'conditions'    => $conditions,
            'order'         => 'UserCreditHistory.date_start ASC'
        ));

		



		/*$conditions_refund = array_merge($conditions_refund, array('UserPenality.is_factured'=>1,'UserPenality.message_id >'=>0));

		$allRefundDatas = $this->UserPenality->find('all', array(
				'fields'        => array('UserPenality.date_add','UserPenality.is_factured','UserCreditHistory.date_start','UserCreditHistory.date_end','UserCreditHistory.seconds','UserCreditHistory.user_credit_history','UserCreditHistory.sessionid','UserCreditHistory.credits','UserCreditHistory.phone_number','UserCreditHistory.media','UserCreditHistory.user_id','UserCreditHistory.called_number','UserCreditHistory.domain_id','UserCreditHistory.is_new','UserCreditHistory.is_factured','UserCreditHistory.ca','UserCreditHistory.ca_currency','UserCreditHistory.is_mobile','UserCreditHistory.expert_number',
				'User.id', 'User.credit', 'User.firstname', 'User.lastname','User.country_id', 'Agent.id', 'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number','Agent.lastname AS agent_lastname', 'Agent.email', 'Agent.order_cat', 'Agent.phone_number2', 'Agent.phone_mobile', 'Agent.phone_operator', 'Agent.phone_operator2', 'Agent.phone_operator3'),
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
				'order'         => 'UserPenality.date_add ASC'
			));

		if(count($allRefundDatas)){

				foreach($allComDatas as $todo){
					$new_list[$todo['UserCreditHistory']['date_start']] = $todo;
				}

				foreach($allRefundDatas as &$refund){
					if($refund['UserPenality']['is_factured']){
						$refund['UserCreditHistory']['date_start'] = $refund['UserPenality']['date_add'];
						if(isset($refund['UserPay']))
							$refund['UserPay']['price'] = $refund['UserPay']['price']  * -1;
						else
							$refund['UserPay']['price'] = 0;
						$refund['UserCreditHistory']['credits'] = $refund['UserCreditHistory']['credits'] * -1;
						$new_list[$refund['UserCreditHistory']['date_start']] = $refund;
					}
				}
				krsort($new_list);
				$allComDatas = array();
				foreach($new_list as $cc){
					array_push($allComDatas,$cc);
				}
		}*/
		
		// $mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

		foreach($allComDatas as $indice => $row){
			
			$checkExport = $this->ExportCom->find('first', array(
					'conditions'    => array('user_credit_history_id' => $row['UserCreditHistory']['user_credit_history']),

				));
			

			
			if(!$checkExport){

			$userPay = $this->UserPay->find('first', array(
					'conditions'    => array('UserPay.id_user_credit_history' => $row['UserCreditHistory']['user_credit_history']),

				));

			$cost = $this->Cost->find('first', array(
					'fields'        => array('Cost.cost'),
					'conditions'    => array('Cost.id' => $row['Agent']['order_cat']),

				));

			$cost_agent = $this->CostAgent->find('first', array(
					'fields'        => array('CostAgent.nb_minutes'),
					'conditions'    => array('CostAgent.id_agent' => $row['Agent']['id']),

				));

			$bonus_agent = $this->BonusAgent->find('first', array(
					'fields'        => array('BonusAgent.min_total'),
					'conditions'    => array( 'BonusAgent.id_agent' => $row['Agent']['id'],'BonusAgent.date_add >=' => $row['UserCreditHistory']['date_start'], 'BonusAgent.active' => 1),
					'order'			=> 'BonusAgent.id'
				));
			
			$cost_phones = $this->CostPhone->find('all', array(

				));

			//recup data call info
			if($row['UserCreditHistory']['sessionid']){
				 $callinfo = $this->CallInfo->find('first', array(
					'fields'        => array('CallInfo.callerid','CallInfo.line'),
					'conditions'    => array('CallInfo.sessionid' => $row['UserCreditHistory']['sessionid']),

				));
			}else{
				$callinfo = array();
			}

			$code_iso = '';
			switch ($row['UserCreditHistory']['domain_id']) {
				case 11:
					$code_iso = 'Belgique';
					break;
				case 13:
					$code_iso = 'Suisse';
					break;
				case 19:
					$code_iso = 'France';
					break;
				case 22:
					$code_iso = 'Luxembourg';
					break;
				case 29:
					$code_iso = 'Canada';
					break;
			}
			$timing = explode(' ',$row['UserCreditHistory']['date_start']);
			$heures = intval(($row['UserCreditHistory']['seconds']) / 60 / 60);
			$minutes = intval(($row['UserCreditHistory']['seconds'] % 3600) / 60);
			$secondes =intval((($row['UserCreditHistory']['seconds'] % 3600) % 60));
			$price = $userPay['UserPay']['price'];
			if($cost){
				$cost = $cost['Cost']['cost'];
				
				if($row['UserCreditHistory']['is_mobile']){
						foreach($cost_phones as $cost_phone){
							if(substr($row['UserCreditHistory']['expert_number'],0,strlen($cost_phone['CostPhone']['indicatif'])) == $cost_phone['CostPhone']['indicatif'])
								$cost = $cost -$cost_phone['CostPhone']['cost'];
						}
				}
			}else
				$cost = 0;
			if($callinfo){
				$caller = $callinfo['CallInfo']['callerid'];
				$caller_line = $callinfo['CallInfo']['line'];
			}else{
				$caller = '';
				$caller_line = '';
			}


			$called = '';
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
        case 9910:
					$called = 'Canada audiotel mobile';
					$code_iso = 'Canada';
					break;
				case 19007884466:
        case 19005289010:
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

			switch ($caller_line) {
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
              case 'CA-19005289010':
								$called = 'Canada audiotel';
								break;
							case 'CA-#4466 Bell':
              case 'CA-#9010 Bell':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Rogers/Fido':
              case 'CA-#9010 Rogers/Fido':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Telus':
              case 'CA-#9010 Telus':
								$called = 'Canada mob. audiotel';
								break;
							case 'CA-#4466 Videotron':
              case 'CA-#9010 Videotron':
							case 'AT-431230460013':
								$called = 'Canada mob. audiotel';
								break;
			}

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
						'conditions' => array('phone_number' => $caller),
						'recursive' => -1
					));
					$nb_appels_today = $this->UserCreditHistory->find('count', array(
					'conditions' => array('phone_number' => $caller,  'DATE(date_start) >=' => $timing[0].' 00:00:00',  'DATE(date_start) <=' => $timing[0].' 23:59:59'),
					'recursive' => -1
				));

				$date_1_appel = $this->UserCreditHistory->find('first', array(
						'conditions' => array('phone_number' => $caller),
						'recursive' => -1,
						'order' => 'date_start asc'
					));


				}else{
					$nb_appels = '';
					$nb_appels_today = '';
					$date_1_appel = '';
				}
			}


			$is_audiotel = 0;
			if(substr_count($row['User']['firstname'] , 'AUDIOTEL')){
				if(is_numeric($caller)){
					$row['User']['firstname'] =  'AT'.substr($caller, -6);
					$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4)*15);
				}else{
					$row['User']['firstname'] =  'AT'.substr($caller, -6);
					$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4));
				}
				if($date_1_appel)
					$row['User']['date_add'] = $date_1_appel['UserCreditHistory']['date_start'];
				else
					$row['User']['date_add'] = $row['UserCreditHistory']['date_start'];
				$is_audiotel = 1;
			}

			$premier_appel = '';

			if($row['UserCreditHistory']['is_new'])$premier_appel = 'X';

			if(!$called){
				$called = $code_iso.' prepaye';
			}
			$date_premiere_consult = '';
			$delai_avant_consult = '';
			$concordance = '';


			if($premier_appel == 'X' && !$is_audiotel){


				$date_premiere_consult = $date_1_appel['UserCreditHistory']['date_start'] ;
				$dateins = $row['User']['date_add'];

				$date1 = new DateTime($dateins);
				$date2= new DateTime($date_1_appel['UserCreditHistory']['date_start']);
				$date=$date2->diff($date1);
				//$diffInSeconds = $date2->getTimestamp() - $date1->getTimestamp();
				$minutes = $date->days * 24 * 60;
				$minutes += $date->h * 60;
				$minutes += $date->i;
				$delai_avant_consult = $minutes;//$date->format('%I');//%a jours, %h heures, %i minutes et %s secondes

				if($row['UserCreditHistory']['phone_number'] && is_numeric($row['UserCreditHistory']['phone_number']) &&  $row['UserCreditHistory']['phone_number'] != 'UNKNOWN' &&  $row['UserCreditHistory']['phone_number'] != '43123001999'){
					$concordance_data = $this->UserCreditHistory->find('first', array(
							'conditions' => array('phone_number' => $row['UserCreditHistory']['phone_number'], 'date_start <' => $date_1_appel['UserCreditHistory']['date_start'], 'type_pay' => 'aud'),
							'recursive' => -1,
							'order' => 'date_start asc'
						));

					if($concordance_data && $concordance_data['UserCreditHistory']['date_start'] != $date_1_appel['UserCreditHistory']['date_start']){
						$concordance = utf8_decode('Concordance client Prépayé audiotel');
					}
					//$concordance = Tools::dateUser('Europe/Paris',$concordance_data['UserCreditHistory']['date_start']);
				}
			}

			//patch comm non facturé
			if(!$row['UserCreditHistory']['is_factured'] && $row['UserCreditHistory']['media'] == 'email'){
				$price = 0;
				$row['UserCreditHistory']['credits'] = $row['UserCreditHistory']['credits'] * -1;
			}

			$ca_euro = 0;
			$ca_dollar = 0;
			$ca_chf = 0;
			switch ($row['UserCreditHistory']['ca_currency']) {
				case '€':
					$ca_euro = $row['UserCreditHistory']['ca'];
				break;
				case '$':
					$ca_dollar = $row['UserCreditHistory']['ca'];
				break;
				case 'CHF':
					$ca_chf = $row['UserCreditHistory']['ca'];
				break;
			}
			if($row['UserCreditHistory']['credits'] < 0 && $price > 0){
				$price = $price * -1;
				$ca_euro = 0;
				$ca_dollar = 0;
				$ca_chf = 0;
			}

			$min_total = 0;
			if($bonus_agent)$min_total = $bonus_agent['BonusAgent']['min_total'];
			
			$phone_operator = '';
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_number'])$phone_operator = $row['Agent']['phone_operator'];
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_number2'])$phone_operator = $row['Agent']['phone_operator2'];
			if($row['UserCreditHistory']['expert_number'] == $row['Agent']['phone_mobile'])$phone_operator = $row['Agent']['phone_operator3'];

			$line = array(
				'user_credit_history_id'=> $row['UserCreditHistory']['user_credit_history'],
				'agent_id'=> $row['Agent']['id'],
				'user_id'=> $row['User']['id'],
                'agent_number'      => $row['Agent']['agent_number'],
                'agent_pseudo'      => $row['Agent']['pseudo'],
                'agent_firstname'   => $row['Agent']['agent_firstname'],
                'agent_lastname'    => $row['Agent']['agent_lastname'],
                'agent_email'       => $row['Agent']['email'],
                'user_code'         => $row['User']['personal_code'],
                'user_firstname'    => $row['User']['firstname'],
                'user_lastname'     => $row['User']['lastname'],
				'user_domain'       => $code_iso,
				'user_date_add'     => $row['User']['date_add'],
				'first_call'		=> $premier_appel,
				'concordance'	=> $concordance,
				'date_first_consult'		=> $date_premiere_consult,
				'delay_before_first_consult	'		=> $delai_avant_consult,
				'nb_calls'=> $nb_appels,
				'nb_calls_day'   => $nb_appels_today,
                'user_credit_now'       => $row['User']['credit'],
                'media'             => $row['UserCreditHistory']['media'],
                'credits'           => ($row['User']['personal_code']!=999999)?$row['UserCreditHistory']['credits']:'',
                'seconds'           => $row['UserCreditHistory']['seconds'],
				'minutes'           => $heures . ' h '.$minutes. ' min '. $secondes .' sec',
                'called_number'     => $row['UserCreditHistory']['called_number'],
				'called'            => $called,
                'phone_agent'       => $row['UserCreditHistory']['expert_number'],
				'phone_operator'    => $phone_operator,
				'caller'            => $caller,
				'caller_line'       => $caller_line,
				'sessionid'         => $row['UserCreditHistory']['sessionid'],
			    'time_start'        => $timing[1],
                'date_start'        => $row['UserCreditHistory']['date_start'],
                'date_end'          => $row['UserCreditHistory']['date_end'],
				'tx_minute'     => $cost,
				'tx_second'    => $cost /60,
				'price'      => $price,
				'ca_euro'      		=> $ca_euro,
				'ca_chf'      		=> $ca_chf,
				'ca_dollar'      	=> $ca_dollar,
				'total_seconds_month'  => $min_total,
				'total_seconds'       => $cost_agent['CostAgent']['nb_minutes'] * 60,

            );

			/*$check_data = $this->ExportCom->find('first', array(
							'conditions' => array('user_credit_history_id' => $row['UserCreditHistory']['user_credit_history']),
							'recursive' => -1,
						));
			if(!$check_data){*/
				$this->ExportCom->create();
				$this->ExportCom->save($line);
				var_dump('OK');
			//}
				
			}

        }
	}
		
		
      
  public function updateCurrencies(){
   
    $mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
    
    try {
       $url = 'http://api.currencylayer.com/live?access_key=026cf8f759a075e7582378525c0a63e2&source=CAD&currencies=EUR&format=1';
		$ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      $result = curl_exec($ch);
      curl_close($ch);
        $tab = json_decode($result);
    
		if($tab->quotes->CADEUR)
    $mysqli->query("update currencies set amount = '".$tab->quotes->CADEUR."' where code = 'CAD'");	

						 } catch (\Stripe\Error\Base $e) {
							var_dump($e);
						}
     try {
       $url = 'http://api.currencylayer.com/live?access_key=026cf8f759a075e7582378525c0a63e2&source=CHF&currencies=EUR&format=1';
							$ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      $result = curl_exec($ch);
      curl_close($ch);
        $tab = json_decode($result);
      if($tab->quotes->CHFEUR)
      $mysqli->query("update currencies set amount = '".$tab->quotes->CHFEUR."' where code = 'CHF'");	

						 } catch (\Stripe\Error\Base $e) {
							var_dump($e);
						}
  }
  
  public function alertWrongCustomer(){
    $this->loadModel('Order');
    $this->loadModel('User');
    $this->loadModel('CallInfo');
    
    App::import('Controller', 'Extranet');
    $extractrl = new ExtranetController();
    
    $conditions = array('Order.valid' => 3, 'Order.payment_mode' => 'paypal');
    $allPaypalOpposed = $this->Order->find('all', array(
				'fields'        => array('Order.user_id'),
				'conditions'    => $conditions,
				'order'         => 'Order.date_add ASC'
			));
     $conditions = array('Order.valid' => 2, 'Order.payment_mode' => 'stripe');
    $allStripeOpposed = $this->Order->find('all', array(
				'fields'        => array('Order.user_id'),
				'conditions'    => $conditions,
				'order'         => 'Order.date_add ASC'
			));
    
    
    $customers = array();
    foreach($allPaypalOpposed as $order){
      array_push($customers, $order['Order']['user_id']);
    }
    foreach($allStripeOpposed as $order){
      array_push($customers, $order['Order']['user_id']);
    }
    
    $customerOpposed = array_unique($customers);
    
    sort($customerOpposed);
    
    foreach($customerOpposed as $userid){
       $this->User->id = $userid;
		   $user_number = $this->User->field('personal_code');
       if(!$this->User->field('payment_opposed')){
        $this->User->saveField('payment_opposed', 1);
        $this->User->saveField('payment_blocked', 1);
		$this->User->saveField('active', 0);
        $this->User->saveField('date_blocked', date('Y-m-d H:i:s'));
       }
       $get_number = $this->CallInfo->find('first',array('conditions' => array('customer'=> $user_number, 'accepted' => 'yes', 'time_start !=' => NULL, 'callerid !=' => 'UNKNOWN')));
		   $customer_number = array();
      if($get_number && $get_number['CallInfo']['callerid']  && $get_number['CallInfo']['callerid'] != 'UNKNOWN' && $get_number['CallInfo']['callerid'] != 43123001999){
        if(!$this->User->field('phone_number'))
        $this->User->saveField('phone_number', $get_number['CallInfo']['callerid']);
         $other_account = $this->CallInfo->find('all',array('conditions' => array('customer != '=> $user_number, 'accepted' => 'yes', 'time_start !=' => NULL, 'callerid' => $get_number['CallInfo']['callerid'])));
        
         foreach($other_account as $calldata ){
             array_push($customer_number, $calldata['CallInfo']['customer']);
         }
        $customer_number = array_unique($customer_number);
        
        foreach($customer_number as $num ){
          $bad_customer = $this->User->find('first',array('conditions' => array('personal_code'=> $num, 'active' => 1, 'parent_account_opposed' => NULL)));
          if($bad_customer){
            $this->User->id = $bad_customer['User']['id'];
            $phone_account = $this->CallInfo->find('first',array('conditions' => array('customer'=> $num, 'accepted' => 'yes', 'time_start !=' => NULL)));
            $this->User->saveField('phone_number', $phone_account['CallInfo']['callerid']);
		        $this->User->saveField('parent_account_opposed', $userid);
            $this->User->saveField('payment_blocked', 1);
			$this->User->saveField('active', 0);
            $this->User->saveField('date_blocked', date('Y-m-d H:i:s'));
            $html = 'Le client <a href="https://fr.spiriteo.com/admin/accounts/view-'.$bad_customer['User']['id']. '">'.$bad_customer['User']['id']. '</a> avec pour mail '.$bad_customer['User']['email'].' utilise le meme telephone que le compte ID <a href="https://fr.spiriteo.com/admin/accounts/view-'.$userid. '">'.$userid. '</a> lié a un paiement oppose, son compté vient d\'etre desactivé.';
            //Les datas pour l'email
						$datasEmail = array(
							'content' => $html,
							'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
						);
						//Envoie de l'email
						$extractrl->sendEmail('contact@talkappdev.com','Compte client a desactiver','default',$datasEmail);
            
          }
        }
        
      }
    }
  }
      
      /**
         * get last added client
         * @todo  add template
         */
  public function updateClientList(){

          //update contact
          $this->loadModel('User');
          $startBegin = date('Y-m-d 00:00:00', strtotime('-10 days'));
          $users = $this->User->find('all', array(
              'fields' => array('User.*'),
              'conditions' => array('User.date_add >'=>$startBegin),
              'paramType' => 'querystring',
              'recursive' => -1
          ));

            if(is_array($users)){
                foreach($users as $user){
                    $curl = curl_init();
                    
                    $attr = new stdClass();
                    $attr->PRENOM = $user['User']['firstname'];

                    $jsonData = array(
                        'listIds' => array(14),
                        'email' => $user['User']['email'],
                        'attributes' => $attr,
                        'updateEnabled' => true
                    );
                    $jsonDataEncoded = json_encode($jsonData);
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
                            'Content-Length: ' . strlen($jsonDataEncoded),
                            'api-key: ' . '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'
                        ),
                        CURLOPT_POSTFIELDS => $jsonDataEncoded
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);


                }
            }
        }
		
	public function SaleReconciliation(){
		exit;
		set_time_limit ( 0 );
		ini_set("memory_limit",-1);
		
		$listing_utcdec = Configure::read('Site.utcDec');
		$date_cron = date('Y-m-d H:i:s');//'2020-11-01 12:00:00';//
		$dx = new DateTime($date_cron);
		$date_add = $dx->format('Y-m-01 00:00:00');
		$date_pay = $dx->format('Y-m-01 22:00:00');
		$date_end = $dx->format('Y-m-28 12:00:00');
		
		$date = date('Y-m-d 00:00:00', strtotime('-1 month'));
		//$date = '2020-10-01 12:00:00';
		$dx = new DateTime($date);
		$date = $dx->format('Y-m-01 00:00:00');
		
		$date_perdiode_min = $dx->format('01-m-Y');
		$dx = new DateTime($date);
		$dx->modify('last day of this month');
		$date2 = $dx->format('Y-m-d 23:59:59');
		$date_perdiode_max = $dx->format('d-m-Y');
		$dx = new DateTime($date);
		$dx->modify('- '.$listing_utcdec[$dx->format('md')].' hour');
		$dx2 = new DateTime($date2);
		$dx2->modify('- '.$listing_utcdec[$dx2->format('md')].' hour');

		$sql_datemin = $dx->format('Y-m-d H:i:s');
		$sql_datemax = $dx2->format('Y-m-d H:i:s');
		$date_rec = $dx2->format('Y-m-15 22:00:00');
		
	
		$this->loadModel('InvoiceAgent');
		$this->loadModel('InvoiceVoucherAgent');
		$this->loadModel('UserPay');
		$this->loadModel('UserCreditPrice');
		$this->loadModel('Currency');
		$this->loadModel('SaleReconciliation');
		$this->loadModel('UserCreditHistory');
		$this->loadModel('WorkingCapital');
		
		$sales = $this->SaleReconciliation->find('first',array(
					'conditions' => array('SaleReconciliation.status' => 1),
					'order' => 'SaleReconciliation.id DESC',
				));
		$dd_sales_datemin = new DateTime($sales['SaleReconciliation']['date_add']);
		$dd_sales_datemin->modify('+1 month');
		$datemin_sales = $dd_sales_datemin->format('Y-m-01 00:00:00');
		$dd_sales_datemin = new DateTime($datemin_sales);
		$dd_sales_datemin->modify('- '.$listing_utcdec[$dd_sales_datemin->format('md')].' hour');
		$sales_datemin = $dd_sales_datemin->format('Y-m-d H:i:s');
		
		$currencies = $this->Currency->find('all',array(
						'recursive' => -1
					));
		
		
		//remplir total prepaid -> invoice_prepaid
		//remplir total prepaid -> invoice_premium
		$invoice_premium = 0;
		$invoice_prepaid = 0;
		$premium = 0;
		
		$lines_invoice = $this->InvoiceAgent->find('all',array(
					'conditions' => array('InvoiceAgent.date_add >=' => $date_add,'InvoiceAgent.date_add <=' => $date_end),
				));
		foreach($lines_invoice as $line_invoice){
		
			$lines = $this->UserCreditHistory->find('all',array(
						'fields' => array('UserPay.price', 'UserPay.ca','UserCreditHistory.type_pay','UserCreditHistory.agent_id'),
						'conditions' => array('UserCreditHistory.agent_id' => $line_invoice['InvoiceAgent']['user_id'],'UserCreditHistory.date_start >=' => $line_invoice['InvoiceAgent']['date_min'],'UserCreditHistory.date_start <=' => $line_invoice['InvoiceAgent']['date_max'], 'UserCreditHistory.is_factured' => 1),
						'joins'      => array(
							array(
								'table' => 'user_pay',
								'alias' => 'UserPay',
								'type'  => 'LEFT',
								'conditions' => array(
									'UserPay.id_user_credit_history = UserCreditHistory.user_credit_history'
								)
							)
						),
						'recursive' => -1
					));
			foreach($lines as $line){

				$tot = $line['UserPay']['ca'];
				$tot_premium = $line['UserPay']['ca'] - $line['UserPay']['price'];
				if($line['UserCreditHistory']['type_pay'] == 'aud')$invoice_premium += $tot; else $invoice_prepaid += $tot;
				if($line['UserCreditHistory']['type_pay'] == 'aud')$premium += $tot_premium;
			}
		}
		
		//remplir total facture -> invoice_agent
		$invoice_agent = 0;
		$lines = $this->InvoiceAgent->find('all',array(
					'fields' => array('InvoiceAgent.amount'),
					'conditions' => array('InvoiceAgent.date_add >=' => $date_add,'InvoiceAgent.date_add <=' => $date_end, 'InvoiceAgent.status >=' => 0),
					'recursive' => -1
				));
		foreach($lines as $line){
			$invoice_agent += $line['InvoiceAgent']['amount'];
		}
		//remplir total tva facture -> vat_invoice_agent
		$vat_invoice_agent = 0;
		$lines = $this->InvoiceAgent->find('all',array(
					'fields' => array('InvoiceAgent.vat'),
					'conditions' => array('InvoiceAgent.date_add >=' => $date_add,'InvoiceAgent.date_add <=' => $date_end, 'InvoiceAgent.status >' => 0),
					'recursive' => -1
				));
		foreach($lines as $line){
			$vat_invoice_agent += $line['InvoiceAgent']['vat'];
			
		}
		
		//remplir total virement -> bankwire_agent
		$bankwire_agent = 0;
		$lines = $this->InvoiceAgent->find('all',array(
					'fields' => array('InvoiceAgent.paid_total'),
					'conditions' => array('InvoiceAgent.date_add >=' => $date_add,'InvoiceAgent.date_add <=' => $date_end, 'InvoiceAgent.status' => 1, 'InvoiceAgent.paid_total_valid' => 0),
					'recursive' => -1
				));
		foreach($lines as $line){
			$bankwire_agent += $line['InvoiceAgent']['paid_total'];
		}
		
		//remplir total credit note -> credit_note
		$credit_note = 0;
		$lines = $this->InvoiceVoucherAgent->find('all',array(
					'fields' => array('InvoiceVoucherAgent.amount'),
					'conditions' => array('InvoiceVoucherAgent.date_add >=' => $sql_datemin,'InvoiceVoucherAgent.date_add <' => $sql_datemax, 'InvoiceVoucherAgent.status' => 1),
					'recursive' => -1
				));
		foreach($lines as $line){
			$credit_note += $line['InvoiceVoucherAgent']['amount'];
		}
		
		//remplir fond roulement -> working_capital
		$working_capital = 0;
		$lines = $this->WorkingCapital->find('all',array(
					'fields' => array('WorkingCapital.amount','WorkingCapital.type'),
					'conditions' => array('WorkingCapital.date_transfert >=' => $sql_datemin,'WorkingCapital.date_transfert <' => $sql_datemax),
					'recursive' => -1
				));
		foreach($lines as $line){
			if($line['WorkingCapital']['type'] == 'transfert')
				$working_capital += $line['WorkingCapital']['amount'];
			else
				$working_capital -= $line['WorkingCapital']['amount'];
		}
		
		//remplir total transfert du 1 du mois vers connect -> owed_agent
		$owed_agent = 0;
		$lines = $this->UserPay->find('all',array(
					'fields' => array('UserPay.ca'),
					'conditions' => array('UserPay.date_pay >=' => $date_add,'UserPay.date_pay <=' => $date_pay),
					'recursive' => -1
				));
		foreach($lines as $line){
			$owed_agent += $line['UserPay']['ca'];
			
		}
		
		//remplir credit non utilisé -> unused_credit
		$unused_credit = 0;
		$lines = $this->UserCreditPrice->find('all',array(
					'fields' => array('UserCreditPrice.price','UserCreditPrice.devise','UserCreditPrice.seconds_left'),
					'conditions' => array('UserCreditPrice.date_add >=' => $sales_datemin,'UserCreditPrice.date_add <' => $sql_datemax, 'UserCreditPrice.status' => 0, 'UserCreditPrice.seconds_left >' => 0),
					'recursive' => -1
				));
		foreach($lines as $line){
			
			$amount = 0;
				foreach($currencies as $currency){
					switch ($line['UserCreditPrice']['devise']) {
						case '€':
							$amount = ($line['UserCreditPrice']['price'] * $line['UserCreditPrice']['seconds_left']) * $currency['Currency']['amount'];
							break;
						case '$':
							$amount = ($line['UserCreditPrice']['price'] * $line['UserCreditPrice']['seconds_left']) * $currency['Currency']['amount'];
							break;
						case 'CHF':
							$amount = ($line['UserCreditPrice']['price'] * $line['UserCreditPrice']['seconds_left']) * $currency['Currency']['amount'];
							break;
					}
				}
			
			$unused_credit += $amount;
			
		}
		
		
		$invoiceData = array();
		$invoiceData['SaleReconciliation'] = array();
		$invoiceData['SaleReconciliation']['date_reconciliation'] = $date_rec;
		$invoiceData['SaleReconciliation']['status'] = 0;
		$invoiceData['SaleReconciliation']['invoice_prepaid'] = $invoice_prepaid;
		$invoiceData['SaleReconciliation']['invoice_premium'] = $invoice_premium;
		$invoiceData['SaleReconciliation']['invoice_agent'] = $invoice_agent;
		$invoiceData['SaleReconciliation']['vat_invoice_agent'] = $vat_invoice_agent;
		$invoiceData['SaleReconciliation']['credit_note'] = $credit_note;
		$invoiceData['SaleReconciliation']['working_capital'] = $working_capital;
		$invoiceData['SaleReconciliation']['owed_agent'] = $owed_agent;
		$invoiceData['SaleReconciliation']['error_agent'] = 0;
		$invoiceData['SaleReconciliation']['bankwire_agent'] = $bankwire_agent;
		$invoiceData['SaleReconciliation']['stripe'] = 0;
		$invoiceData['SaleReconciliation']['paypal'] = 0;
		$invoiceData['SaleReconciliation']['unused_credit'] = $unused_credit;
		$invoiceData['SaleReconciliation']['currency_diff'] = 0;
		$invoiceData['SaleReconciliation']['premium_number'] = $premium;	
		$this->SaleReconciliation->create();
        $this->SaleReconciliation->save($invoiceData);
	}
		
	public function updateClientTarotList(){

          //update contact
          $this->loadModel('User');
		  $this->loadModel('CardEmail');
          $startBegin = date('Y-m-d H:i:s', strtotime('-1 hour'));
          $users = $this->CardEmail->find('all', array(
              'fields' => array('CardEmail.*'),
              'conditions' => array('CardEmail.date_add >='=>$startBegin),
              'paramType' => 'querystring',
              'recursive' => -1
          ));

            if(is_array($users)){
                foreach($users as $user){
					
					 $user_ins = $this->User->find('first', array(
						  'conditions' => array('User.email'=>$user['CardEmail']['email']),
						  'paramType' => 'querystring',
						  'recursive' => -1
					  ));
					if(!$user_ins){
					
						$curl = curl_init();

						$attr = new stdClass();

						$jsonData = array(
							'listIds' => array(64),
							'email' => $user['CardEmail']['email'],
							'attributes' => $attr,
							'updateEnabled' => true
						);
						$jsonDataEncoded = json_encode($jsonData);
						curl_setopt_array($curl, array(
							CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => "",
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 30,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => "POST",
							CURLOPT_HTTPHEADER =>array('Content-Type:application/json; charset=utf-8',
								'Content-Length: ' . strlen($jsonDataEncoded),
								'api-key: ' . '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'
							),
							CURLOPT_POSTFIELDS => $jsonDataEncoded
						));

						$response = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);
					}

                }
            }
        }
		
				
		public function updateOrderConversion(){
			
			$this->loadModel('Order');
			$this->loadModel('UserCredit');
			$this->loadModel('UserCreditPrice');
			$this->loadModel('OrderStripetransaction');
			$this->loadModel('OrderSepatransaction');
			
			App::import('Controller', 'Paymentstripe');
		    $paymentctrl = new PaymentstripeController();

				
			require_once(APP.'Lib/stripe7/init.php');
			
			$stripe = new \Stripe\StripeClient(
						  $paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']
						);
			
			$dx = new DateTime(date('Y-m-d H:i:s'));
			$dx->modify('- 1 hour');
			$datemin = $dx->format('Y-m-d H:i:s');
			
			$orders = $this->Order->find('all', array(
										'conditions' => array('Order.payment_mode' => 'stripe', 'Order.currency !=' => '€', 'Order.valid' => '1', 'Order.total_euros <' => '1', 'Order.date_add >=' => $datemin),
										'recursive' => -1
									));			
			foreach($orders as $order){
				$stripe_transac = $this->OrderStripetransaction->find('first',array(
							'conditions' => array('OrderStripetransaction.cart_id' => $order['Order']['cart_id']),
							'recursive' => -1
							));
				if(!$stripe_transac){
					$stripe_transac = $this->OrderSepatransaction->find('first',array(
							'conditions' => array('OrderSepatransaction.cart_id' => $order['Order']['cart_id']),
							'recursive' => -1
							));
				}
				if($stripe_transac['OrderStripetransaction']['id']){
					
				 try {
						if(!substr_count($stripe_transac['OrderStripetransaction']['id'],'ch_' )){
							$pi = $stripe->paymentIntents->retrieve(
							  $stripe_transac['OrderStripetransaction']['id'],
							  []
							);
						}else{
							$pi = 1;
						}
						
					 	if($pi){
							if(substr_count($stripe_transac['OrderStripetransaction']['id'],'ch_' )){
								$charge = $stripe->charges->retrieve(
											  $stripe_transac['OrderStripetransaction']['id'],
											  []
											);
							}else{
								$charge = $pi->charges->data[0];
							}
							if($charge){

								$balance = $stripe->balanceTransactions->all(['source' => $charge->id]);
								$euros = $balance->data[0]->amount;
								$euros = $euros/100;
								$this->Order->id = $order['Order']['id'];
								$this->Order->saveField('total_euros', $euros);
								
								
								$user_credit = $this->UserCredit->find('first',array(
									'conditions' => array('UserCredit.order_id' => $order['Order']['id']),
									'recursive' => -1
								));
								
								$user_credit_price = $this->UserCreditPrice->find('first',array(
									'conditions' => array('UserCreditPrice.id_user_credit' => $user_credit['UserCredit']['id']),
									'recursive' => -1
								));
								
								$credits = $user_credit['UserCredit']['credits'];
								$price = ($euros / $credits);
								$this->UserCreditPrice->id = $user_credit_price['UserCreditPrice']['id'];
								$this->UserCreditPrice->saveField('price_euros', $price);
							}
						}
					}
					 catch (Exception $e) {
									
					}
				}
			}
			
			
		}
}
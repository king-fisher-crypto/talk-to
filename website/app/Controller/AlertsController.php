<?php
App::uses('AppController', 'Controller');


class AlertsController extends AppController {
    protected $alert_type = array('email', 'sms');

    public function beforeFilter()
    {
        $this->Auth->allow('setnew', 'stop_alert');
        if ($this->request->is('ajax')){
            $this->layout = 'ajax';
            $this->set('isAjax',1);
        }

        parent::beforeFilter();
    }
    public function alertUsersForUserAvailability($agent_number=0, $media=false)
    {
		
		/* On récupère les infos de l'agent */
            $this->loadModel('User');
            $agent = $this->User->find("first", array(
                    'conditions' => array(
                        'agent_number' => $agent_number,
                        'role'         => 'agent',
                        'active'       => 1,
                        'deleted'      => 0),
                    'recursive' => -1
                )
            );

        /* On récupère l'id de l'agent et on vérifie que le statut soit bien à "available" */
        if (!isset($agent['User']) || !isset($agent['User']['agent_status']) || $agent['User']['agent_status'] !== 'available') return false;
		
		  /* On récupère les utilisateurs ayant demandé une alerte */
            $conditions = array(
                'Alert.agent_id'     => $agent['User']['id'],
                'Alert.date_add >= ' => date("Y-m-d H:i:s", time() - (86400 * (int)Configure::read('Site.alerts.days'))),
                'Alert.alert_by_day >' => 0
            );

        /* Filtrons-nous par média ? */
            if ($media){
                $conditions['Alert.media'] = $media;
            }

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
                        )
                    ),
                    'group'      => array('Alert.id')
                )
            );


        //Init le tableau des alertes
        $alertCustomer = array(
            'Agent'     => array(
                'id'            => $agent['User']['id'],
                'pseudo'        => $agent['User']['pseudo'],
                'agent_number'  => $agent['User']['agent_number'],
                'consult_email' => $agent['User']['consult_email'],
                'consult_phone' => $agent['User']['consult_phone'],
                'consult_chat' => $agent['User']['consult_chat'],
            ),
            'Customer'  => array()
        );
        /* On envoie les alertes aux utilisateurs eligibles
            1/ L'utilisateur n'a pas dépassé son nombre d'alertes max par jour
            2/ La dernière alerte recue a été envoyée il ya + de x secondes (config)
            3/ L'expert accepte la consultation pour le média en question
        */

        //Date minimum pour envoyer une nouvelle alerte
        $dateMin = date('Y-m-d H:i:s', time() - Configure::read('Site.alerts.delay_between_alerts_second'));

		$this->loadModel('AlertHistory');
		$dbb_patch = new DATABASE_CONFIG();
		$dbb_connect = $dbb_patch->default;
		$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
		foreach ($alerts as $alert){
			
			$history_count = 0;
			/*  $conditions = array(
               'AlertHistory.alerts_id'     => $alert['Alert']['id'],
            );
			var_dump($conditions);
			$history_count = $this->AlertHistory->find('first', $conditions);*/
							
			$history_c =	$mysqli_connect->query("select * from alert_histories where alerts_id = ".$alert['Alert']['id']);	
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
                            'type'  => $this->alert_type[1]
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
                                        'type'      => $this->alert_type[1]
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
                            'type'  => $this->alert_type[0]
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
                                        'type'      => $this->alert_type[0]
                                    )
                                )
                            )
                        );

                }
            }
        }
		//Envoie des emails et sms
       $this->sendSmsAlertForAgentAvailable($alertCustomer);
       $this->sendEmailAlertForAgentAvailable($alertCustomer);
    }

    public function sendEmailAlertForAgentAvailable($datas=array(), $issend = false)
    {
		
		
		
        if (empty($datas['Customer']))return false;

        //Pour chaque customer
        foreach($datas['Customer'] as $email => $data){
            $id_lang = (int)$data['Lang']['id_lang'];

            //On garde uniquement les alertes par mail
            foreach($data['Alert']['media'] as $key => $val){
                if($val['type'] != $this->alert_type[0])
                    unset($data['Alert']['media'][$key]);
            }

			if (substr($email, 0, 1) != '_' && !empty($data['Alert']['media']) && $this->Alert->alertEmailToday($datas['Agent']['id'], $email)){
                /* On envoie le mail */
                /*
                $this->sendEmail(
                    $email,
                    'Votre agent '.$datas['Agent']['pseudo'].' est disponible !',
                    'alert_available',
                    array('customer' => $data, 'agent' => $datas['Agent'], 'nameMedia' => $this->consult_medias)
                );
                */

                $agent_medias = '<div>';
                /*
                foreach($data['Alert']['media'] as $media){
                    $agent_medias.= "<div style=\"padding-left:20px; clear:both\">".'- '.__($this->consult_medias[$media['name']]).'<div>';
                }
                */
                foreach ($this->consult_medias AS $media => $name){

                    if (isset($datas['Agent']['consult_'.$media]) && (int)$datas['Agent']['consult_'.$media] == 1){
                        $agent_medias.= "<div style=\"padding-left:20px; clear:both; display:block\">".'- '.__($this->consult_medias[$media]).'</div>';
                    }
                }
                $agent_medias.= '</div>';



                /* On recherche l'id de l'alerte email */
                $email_alert_id = 0;
                foreach ($data['Alert']['media'] AS $med){
                    if ($med['name'] == 'email'){
                        $email_alert_id = (int)$med['id'];
                        break;
                    }
                }
				
				if($issend){
					$this->loadModel('User');
					$client =  $this->User->find('first', array(
						'fields' => array('User.firstname'),
						'conditions' => array('User.email' => $email, 'User.role' => 'client'),
						'recursive' => -1
					));
					//if(!$client)
						$prenom = 'Bonjour';
					//else
					//	$prenom = $client['User']['firstname'];
					
					$agent =  $this->User->find('first', array(
						'fields' => array('User.id','User.reviews_avg','User.reviews_nb'),
						'conditions' => array('User.agent_number' => $datas['Agent']['agent_number'], 'User.role' => 'agent'),
						'recursive' => -1
					));
										
								
					if($agent['User']['reviews_nb'] > 0 && $agent['User']['reviews_avg'] > 0){
						$agent_pourcent1 	= 'Ø '.number_format($agent['User']['reviews_avg'],1,'.','').' sur '.$agent['User']['reviews_nb'].' avis';
					}else{
						$agent_pourcent1 	= '';
					}
					
					$this->loadModel('CategoryUser');		
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
											'CategoryLang.lang_id = 1'
										)
									)
								),
								'limit' => 2,
								'recursive' => -1
							));
																
					
					$agent1_spe1 = '';
					$agent1_spe2 = '';
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
					
					$photo_find = 'https://'.$data['Domain']['domain'].'/media/photo/'.substr($datas['Agent']['agent_number'],0,1).'/'.substr($datas['Agent']['agent_number'],1,1).'/'.$datas['Agent']['agent_number'].'_listing.jpg';
					$this->sendCmsTemplateByMail(153, $id_lang, $email, array(
						'PARAM_AGENT_PSEUDO' => $datas['Agent']['pseudo'].' ('.$datas['Agent']['agent_number'].')',
						'PARAM_LINK_STOP'    => 'http://'.$data['Domain']['domain'].Router::url(array(
														'controller' => 'alerts',
														'action' => 'stop_alert',
														'language'=> $data['Lang']['language_code'],
														'id' => $email_alert_id,
														'admin' => false)),
						'PARAM_AGENT_MEDIAS' => $agent_medias,
						'PARAM_LINK_AGENT'   => 'http://'.$data['Domain']['domain'].Router::url(
								array(
									'language'      => $data['Lang']['language_code'],
									'controller'    => 'agents',
									'action'        => 'display',
									'link_rewrite'  => strtolower($datas['Agent']['pseudo']),
									'agent_number'  => $datas['Agent']['agent_number'],
									'admin'         => false
								),
								array(
									'title'         => $datas['Agent']['pseudo']
								)
							),
						'PRENOM' => $prenom,
						'AGENT_NOM1'=> $datas['Agent']['pseudo'],
						'AGENT_PHOTO1'=> $photo_find,
						'AGENT1_SPE1'=> $agent1_spe1,
						'AGENT1_SPE2'=> $agent1_spe2,
						'AGENT_POURCENT1'=> $agent_pourcent1,
						
					));
					//Création de l'historique d'alertes
					$history = $this->createHistory($data['Alert'], $this->alert_type[0]);

					$this->Alert->AlertHistory->saveMany($history);
					foreach ($this->consult_medias AS $media => $name){
						$this->Alert->updateAll(array(
									'Alert.send'      => 1,
								), array(
									'Alert.agent_id'    => $datas['Agent']['id'],
									'Alert.media'       => $media,
									'Alert.send'       => 2,
								));
					}
				
					
				}else{
					foreach ($this->consult_medias AS $media => $name){
						$this->Alert->updateAll(array(
									'Alert.send'      => 2,
								), array(
									'Alert.agent_id'    => $datas['Agent']['id'],
									'Alert.media'       => $media,
									'Alert.send'       => 0,
								));
					}
				}
            }
        }
    }
    
    public function sendSmsAlertForAgentAvailable($datas = array(), $issend = false){
        if (empty($datas['Customer'])) return false;

        //On charge l'API
        App::import('Vendor', 'Noox/Api');
        //On charge le model
        $this->loadModel('SmsHistory');

        //Pour chaque customer
        foreach($datas['Customer'] as $email => $data){
            //On garde uniquement les alertes par sms
            foreach($data['Alert']['media'] as $key => $val){
                if($val['type'] != $this->alert_type[1])
                    unset($data['Alert']['media'][$key]);
            }

            //Si un numero est renseigné, qu'il y a des medias et que l'envoi d'sms est possible
            if(!empty($data['Alert']['phone_number']) && !empty($data['Alert']['media']) && $this->Alert->alertSmsToday($datas['Agent']['id'], $data['Alert']['phone_number'])){
                /* On force la langue pour l'envoi du mail */
                Configure::write('Config.language', $data['Lang']['language_code']);
                $_SESSION['Config']['language'] = $data['Lang']['language_code'];

                //Le corps du sms
                $txt = __('Bonjour votre Expert %s (%s) est a nouveau disponible sur www.spiriteo.com ', array($datas['Agent']['pseudo'], $datas['Agent']['agent_number']));

                //$txt = $data['Lang']['language_code'].__('L\'agent').' "'.$datas['Agent']['pseudo'].'" '.__('est disponible.');
                //Pour chaque media
                /*
                foreach($data['Alert']['media'] as $media){
                    $txt.= "\n".'- '.__('Par '.$this->consult_medias[$media['name']]);
                }
                */
                foreach ($this->consult_medias AS $media => $name){
                    if (isset($datas['Agent']['consult_'.$media]) && (int)$datas['Agent']['consult_'.$media] == 1){
                        $txt.= "\n".'- '.__('Par '.$this->consult_medias[$media]);
                    }
                }
                //Longueur du message
                $txtLength = strlen($txt);
				$txt_save = $txt;
                //Encodage en base64
                //$txt = base64_encode($txt);
				
				if($issend){
					//Envoi du sms
					$api = new Api();
					$result = $api->sendSms($data['Alert']['phone_number'], base64_encode($txt));

					//On init la variable historique
					$history = array(
						'id_agent'          => $datas['Agent']['id'],
						'id_client'         => '',
						'id_tchat'           => '',
						'id_message'         => '',
						'email'             => (substr($email, 0, 1) == '_')?'':$email,
						'phone_number'      => $data['Alert']['phone_number'],
						'content_length'    => $txtLength,
						'content'    		=> $txt_save,
						'send'              => ($result > 0)?1:0,
						'date_add'          => date('Y-m-d H:i:s'),
						'type'				=> 'DISPO EXPERT',
						'cost'				=> $result
					);

					//On save dans l'historique
				
					$this->SmsHistory->create();
					$this->SmsHistory->save($history);

					//Création de l'historique d'alertes
					$history = $this->createHistory($data['Alert'], $this->alert_type[1]);

					$this->Alert->AlertHistory->saveMany($history);

					foreach ($this->consult_medias AS $media => $name){
						$this->Alert->updateAll(array(
									'Alert.send'      => 1,
								), array(
									'Alert.agent_id'    => $datas['Agent']['id'],
									'Alert.media'       => $media,
									'Alert.send'       => 2,
								));
					}
				
				}else{
				
					foreach ($this->consult_medias AS $media => $name){
						$this->Alert->updateAll(array(
									'Alert.send'      => 2,
								), array(
									'Alert.agent_id'    => $datas['Agent']['id'],
									'Alert.media'       => $media,
									'Alert.send'       => 0,
								));
					}
				
				}
            }
        }
    }

    private function createHistory($datas, $type){
        if(empty($datas) || empty($type))
            return array();

        $dateNow = date('Y-m-d H:i:s');

        //Pour chaque alerte.
        $history = array();
        foreach($datas['media'] as $val){
            /* On mets à jour l'alerte */
            $history[] = array(
                'alerts_id'     =>  $val['id'],
                'date_add'      =>  $dateNow,
                'alert_type'    =>  $type
            );
        }

        return $history;
    }

    public function stop_alert($id){
        if(empty($id) || !is_numeric($id))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        //On récupère l'email de cette alerte
        $customerEmail = $this->Alert->find('first', array(
            'fields'        => array('Alert.email'),
            'conditions'    => array('Alert.id' => $id),
            'recursive'     => -1
        ));

        //Si pas d'email
        if(empty($customerEmail) || empty($customerEmail['Alert']['email']))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));

        //On récupère les id des alertes pour cet email
        $idAlerts = $this->Alert->find('list', array(
            'conditions'    => array('Alert.email' => $customerEmail['Alert']['email']),
            'recursive'     => -1
        ));

        //On supprime dans l'historique
       // $this->Alert->AlertHistory->deleteAll(array('AlertHistory.alerts_id' => $idAlerts), false);

        //On supprime toutes les alertes qui ont cet email
        if($this->Alert->deleteAll(array('Alert.email' => $customerEmail['Alert']['email']), false))
            $this->Session->setFlash(__('Vos alertes ont été supprimées'), 'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors de la suppression de vos alertes'), 'flash_warning');

        $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function setnew($agent_id=0)
    {
        /* On récupère l'agent id lorsque le formulaire est posté */
            if (!empty($this->request->data)){
                if (isset($this->request->data['Alert']['agent_id'])){
                    $agent_id = (int)$this->request->data['Alert']['agent_id'];
                }
            }

        /* On redirige si on a pas de agent_id */
            if (!$agent_id){
                $this->redirect(array('controller' => 'home'));
            }

        //Utilisateur non connecté ou compte non client
        if(!$this->Auth->loggedIn() || $this->Auth->user('role') !== 'client'){
            $title_page = __('Alerte impossible');
            $this->set(array('auth' => false));

            if($this->request->is('ajax')){
                $this->layout = '';
                $content = $this->render('/Elements/login_modal');
                $content = $content->body();
                $logo = $this->render('/Elements/logo_modal');
                $logo = $logo->body();

                $this->jsonRender(array('title' => $logo.__('Accès client'), 'content' => $content, 'button' => __('Annuler')));
            }
        }

        /* On récupère les données de l'agent */
            $this->loadModel('User');
            if (!$agent = $this->User->getAgent($agent_id))
                $this->redirect(array('controller' => 'home'));
            $this->set('agent', $agent);

        //On récupère les alerts déjà programmés
        $customerAlerts = $this->Alert->find('all', array(
            'fields' => array('media', 'alert_by_day', 'phone_number', 'email'),
            'conditions' => array('Alert.agent_id' => $agent['User']['id'], 'Alert.users_id' => $this->Auth->user('id'),'Alert.date_add >= ' => date("Y-m-d H:i:s", time() - (86400 * (int)Configure::read('Site.alerts.days')))),
            'recursive' => -1
        ));

        //Pour chaque alertes
        $alertDatas = array();
        foreach($customerAlerts as $alert)
            $alertDatas[$alert['Alert']['media']] = $alert['Alert']['alert_by_day'];

        $this->set(compact('alertDatas', 'customerAlerts'));

        /* Titre de la page */
            $title_page = __('Pour être alerté de la disponibilité de').' '.$agent['User']['pseudo'];
            $this->set('title_page', $title_page);
            $this->set('agent_pseudo', $agent['User']['pseudo']);

        /* Environnement AJAX (light box par exemple) */
            if ($this->request->is('ajax')){
                $logo = $this->render('/Elements/logo_modal');
                $logo = $logo->body();

                $view = new View($this, false);
                $view->set('consult_medias', $this->consult_medias);
                $view->set('isAjax', $this->request->is('ajax'));

                $datas = array(
                    'title'     => $logo.$title_page,
                    'content'   => $view->render(),
                    'button'    => __('Annuler')
                );
                $this->jsonRender($datas);
            }

        /* Post action */
            if (!empty($this->request->data)){
                $errors = 0;
				$this->request->data['Alert']['email2'] = $this->request->data['Alert']['email'];
                /* On vérifie les deux adresses mail */
                    if(strcmp($this->request->data['Alert']['email'], $this->request->data['Alert']['email2']) !== 0){
                        $this->Session->setFlash(__('Il y a une incohérence entre votre adresse e-mail et sa confirmation.'),'flash_warning');
                        $errors++;
                    }

                //Avons-nous un téléphone et est-il correct ?
                if(!empty($this->request->data['Alert']['phone_number'])){
                    $tmp_phone = $this->request->data['Alert']['phone_number'];
                    $this->loadModel('Country');
                    //Indicatif invalide
                    $flag_tel = $this->Country->allowedIndicatif($this->request->data['Alert']['indicatif_phone']);
                    if($flag_tel === -1 || !$flag_tel){
                        $this->Session->setFlash(__('L\'indicatif téléphonique n\'est pas valide.'),'flash_error');
                        $errors++;
                    }
                    //On assemble l'indicatif et le numéro de tel
                    $this->request->data['Alert']['phone_number'] = Tools::implodePhoneNumber($this->request->data['Alert']['indicatif_phone'], $this->request->data['Alert']['phone_number']);
                    $this->request->data['Alert']['phone_number'] = $this->phoneNumberValid($this->request->data['Alert']['phone_number'], 3);
                    if($this->request->data['Alert']['phone_number'] === false)
                        $errors++;
                }

                /* avons-nous une valeur pour chaque media ? */
                 /*   foreach ($this->consult_medias AS $media => $text){
                        if (!isset($this->request->data['Alert']['media_'.$media])){
                            $this->Session->setFlash(__('Erreur inconnue'),'flash_warning');
                            $errors++;
                        }
                    }*/
				
				if(is_array($this->request->data['Alert']['consult']) && count($this->request->data['Alert']['consult'])){
					//good
					if(in_array('0',$this->request->data['Alert']['consult']))$this->request->data['Alert']['media_phone'] = 1;
					if(in_array('1',$this->request->data['Alert']['consult']))$this->request->data['Alert']['media_chat'] = 1;
					if(in_array('2',$this->request->data['Alert']['consult']))$this->request->data['Alert']['media_email'] = 1;
				}else{
					$this->Session->setFlash(__('Merci de saisir au moins un type de consultation'),'flash_warning');
                    $errors++;
					return;
				}
				

                /* sélection des alertes */
                    $alerts = array();
                    foreach ($this->consult_medias AS $media => $text){
                        if ((int)$this->request->data['Alert']['media_'.$media] > 0)
                            $alerts[$media] = (int)$this->request->data['Alert']['media_'.$media];
                        else
                            $alerts[$media] = 0;
                    }

                if (($errors === 0 ) &&  (
                        (empty($this->request->data['Alert']['phone_number']))
                        &&
                        (empty($this->request->data['Alert']['email']) || empty($this->request->data['Alert']['email2']))
                    )

                ){
                    /* Cas où l'utilisateur veut supprimer ses alertes */


                    //On récupère les id des alertes pour cet email
                    $idAlerts = $this->Alert->find('list', array(
                        'conditions'    => array('Alert.users_id' => $this->Auth->user('id'), 'Alert.agent_id' => $agent_id),
                        'recursive'     => -1
                    ));

                    //On supprime dans l'historique
                     //   $this->Alert->AlertHistory->deleteAll(array('AlertHistory.alerts_id' => $idAlerts), false);

                    //On supprime toutes les alertes qui ont cet email
                      //  $this->Alert->deleteAll(array('Alert.users_id' => $this->Auth->user('id'), 'Alert.agent_id' => $agent_id), false);

                    $this->Session->setFlash(__('Votre(vos) alerte(s) a(ont) été désactivée(s) !'),'flash_success');
                    $this->set('success_alert',1);
                }else{
                    /* On prépare le tableau des données à ajouter */
                    $datas = array(
                        'agent_id'      => $agent_id,
                        'email'         => $this->request->data['Alert']['email'],
                        'user_id'       => $this->Auth->user('id'),
                        'phone_number'  => $this->request->data['Alert']['phone_number']
                    );


                    /* Cas où adresse e-mail vide + nbre d'alerts par jour vide + alerte SMS demandée */
                        if (
                            empty($this->request->data['Alert']['email']) &&
                            empty($this->request->data['Alert']['email2']) &&
                            !empty($this->request->data['Alert']['phone_number']) &&
                            !empty($this->request->data['Alert']['indicatif_phone'])
                        ){
                            foreach ($alerts AS $k => $v)
                                $alerts[$k] = 1;
                        }



                    /* On créé les alertes ou MAJ des alertes */
					$mail_media = '';
					$dbb_patch = new DATABASE_CONFIG();
					$dbb_connect = $dbb_patch->default;
					$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
                    foreach ($alerts AS $media => $byday){
						
						$history_count = 0 ;
						$alert_test = $this->Alert->find('first', array(
							'conditions' => array(
								'Alert.agent_id'    => $datas['agent_id'],
								'Alert.users_id'    => $datas['user_id'],
								'Alert.media'       => $media,
								'Alert.date_add >= ' => date("Y-m-d H:i:s", time() - (86400 * (int)Configure::read('Site.alerts.days')))
							),

							'recursive' => -1
						));
						if($alert_test){
							$history_c =	$mysqli_connect->query("select * from alert_histories where alerts_id = ".$alert_test['Alert']['id']);	
							$history_count = $history_c->fetch_array(MYSQLI_ASSOC);
						}
                        if($this->Alert->exist($datas, $media) && !$history_count){
                            $this->Alert->updateAll(array(
                                'domain_id'     => $this->Session->read('Config.id_domain'),
                                'lang_id'       => $this->Session->read('Config.id_lang'),
                                'phone_number'  => (empty($this->request->data['Alert']['phone_number']) ?null:$this->request->data['Alert']['phone_number']),
                                'alert_by_day'  => $byday,
                                'email'         => "'".$datas['email']."'",
                                'users_id'      => $datas['user_id'],
								
                                'date_add'      => $this->Alert->value(date('Y-m-d H:i:s'))
                            ), array(
                                'Alert.agent_id'    => $datas['agent_id'],
                                'Alert.users_id'    => $datas['user_id'],
                                'Alert.media'       => $media,
                            ));
                        }else{
                            $mode = 'create';
                            $this->Alert->create();
                            $datasTmp                 = array_merge($datas, array(
                                'domain_id'     =>    $this->Session->read('Config.id_domain'),
                                'lang_id'       =>    $this->Session->read('Config.id_lang'),
                                'users_id'      =>    $datas['user_id'],
                                'media'         =>    $media,
                                'alert_by_day'  =>    $byday
                            ));

                            $this->Alert->save($datasTmp);
							
                        }
						if($byday){
							$comm = $media;
							$comm = str_replace('phone','telephone',$comm);
							$comm = str_replace('chat','tchat',$comm);
							$mail_media .= '-'.ucfirst($comm).'<br />';
						}
						
                    }
					
					/* on mail le voyant */
					$this->sendCmsTemplateByMail(242, $this->Session->read('Config.id_lang'), $agent['User']['email'], array(
						'PARAM_PSEUDO' => $agent['User']['pseudo'],
						'PARAM_AGENT_MEDIAS' => $mail_media,
					));

                    /* tout s'est bien passé, on informe l'internaute */
                    $this->Session->setFlash(__('Votre(vos) alerte(s) ont été créée(s) avec succès !'),'flash_success');
                    $this->set('success_alert',1);

                }

                //On remet le numéro sans l'indicatif
                if(isset($tmp_phone))
                    $this->request->data['Alert']['phone_number'] = $tmp_phone;
            }

    }

    private function validPhoneNumber($phone){
        //Si il est vide ou si ce n'est pas numeric
        if(empty($phone) || !is_numeric($phone))
            return false;
        //si le téléphone commence par 0 ou + ou si trop petit
        if($phone[0] === '0' || $phone[0] === '+' || strlen($phone) < 10)
            return false;

        return true;
    }

}

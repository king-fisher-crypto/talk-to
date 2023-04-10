<?php
App::uses('AppController', 'Controller');

class CustomerController extends AppController {
    public $uses = array('User','UserCreditLastHistory');

    public function __api_getcredit($parms=false)
    {
		
		$sessionid = isset($parms['sessionid']) ?$parms['sessionid']:false;
		$timestamp = isset($parms['timestamp']) ?$parms['timestamp']:false;
		$cust_personal_code = isset($parms['cust_personal_code']) ?(int)$parms['cust_personal_code']:false;
        if ($sessionid){
		/*	$this->loadModel('CallInfo');
			$resultat = $this->CallInfo->find('first',array(
                                                    'conditions' => array(
                                                        'CallInfo.sessionid'     => $sessionid
                                                    )
                                
                                ));	
			if(isset($resultat['CallInfo']['call_info_id'])){
				$dbb_patch = new DATABASE_CONFIG();
				$dbb_connect = $dbb_patch->default;
				$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
				$mysqli_connect->query("UPDATE call_infos SET time_getcredit = '{$timestamp}' WHERE call_info_id = '{$resultat['CallInfo']['call_info_id']}'");
			}*/
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE call_infos SET time_getcredit = '{$timestamp}', customer = '{$cust_personal_code}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}

        
        if (!$cust_personal_code)
               return array('response_code' => 14, 'response' => false);   
		
		
		$result = $this->User->find('first',array(
                                                    'fields'     =>  array('credit','id'),
                                                    'conditions' => array(
                                                        'User.personal_code'     => (int)$parms['cust_personal_code'],
                                                        'User.role'              => 'client',
                                                        'User.active'            => 1,
                                                        'User.deleted'            => 0
                                                    )
                                
                                ));
		
		
		if (empty($result))
            return array('response_code' => 14, 'response' => false);
        elseif (isset($result['User']['credit'])){
		
			if ($parms['cust_personal_code'] != 999999){

				 $consult = $this->UserCreditLastHistory->find('first',array(
					'conditions' => array(
						'date_end' => null,
						'users_id' => $result['User']['id'],
					),
					'recursive' => -1
				));
				//Customer consultation
				if(!empty($consult))
					return array('response_code' => 14, 'response' => false);
			}
		
        
            //Temps en sec de communication restant
            $secCom = (int)$result['User']['credit'] * (int)Configure::read('Site.secondePourUnCredit');
            return array('response_code' => 0, 'response' => (int)$secCom);
        }
        else return array('response_code' => 999, 'response' => false);
    }
	
	public function __api_callinfo($parms=false){

		/*$line = (isset($parms['line'])?trim($parms['line']):false);
        if (!$line){
            return array('response_code' => 13, 'response' => false);
        }*/
		
		$callerid = (isset($parms['callerid'])?trim($parms['callerid']):false);
       /* if (!$callerid){
            return array('response_code' => 25, 'response' => false);
        }*/
		
		$sessionid = (isset($parms['sessionid'])?trim($parms['sessionid']):false);
       /* if (!$sessionid){
            return array('response_code' => 29, 'response' => false);
        }*/
		$called_number = (isset($parms['called_number'])?trim($parms['called_number']):false);
		
		$mob_info = (isset($parms['mob_info'])?$parms['mob_info']:false);
		$timestamp = (isset($parms['timestamp'])?trim($parms['timestamp']):false);


		$this->loadModel('CallInfo');

        //On sauvegarde les datas
        //Les données
        $info = array(
            'line'              => $mob_info,
            'callerid'          => $callerid,
			'called_number'     => $called_number,
			'mob_info'          => $mob_info,
			'timestamp'         => $timestamp,
            'sessionid'         => $sessionid
        );
        $this->CallInfo->create();

        //Save réussi
        if($this->CallInfo->save($info)){
            return array('response_code' => 0, 'response' => true);
        }else{  //Echec du save
            return array('response_code' => 999, 'response' => true);
        }
		
	}
	public function __api_callstop($parms=false){
		//$parms = $_GET;
		$sessionid = (isset($parms['sessionid'])?trim($parms['sessionid']):false);
        if (!$sessionid){
            return array('response_code' => 29, 'response' => false);
        }

		$timestamp = (isset($parms['timestamp'])?trim($parms['timestamp']):false);
		$hungupby = (isset($parms['hungupby'])?trim($parms['hungupby']):false);
		$usagetime = (isset($parms['usagetime'])?trim($parms['usagetime']):false);
		$usagecost = (isset($parms['usagecost'])?trim($parms['usagecost']):false);
		$commtime = (isset($parms['commtime'])?trim($parms['commtime']):false);
		$commcost = (isset($parms['commcost'])?trim($parms['commcost']):false);
		$routetime = (isset($parms['routetime'])?trim($parms['routetime']):false);
		$routecost = (isset($parms['routecost'])?trim($parms['routecost']):false);
		$revenue = (isset($parms['revenue'])?trim($parms['revenue']):false);
		$sms = (isset($parms['sms'])?trim($parms['sms']):false);
		$customercli = (isset($parms['customercli'])?trim($parms['customercli']):false);

		if ($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE call_infos SET time_stop = '{$timestamp}', hungupby = '{$hungupby}', usagetime = '{$usagetime}', usagecost = '{$usagecost}', commtime = '{$commtime}', commcost = '{$commcost}', routetime = '{$routetime}', routecost = '{$routecost}', revenue = '{$revenue}', sms = '{$sms}', customercli = '{$customercli}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}	
		
		//force arret de l agent
		$this->loadModel('CallInfo');
		$call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.agent'),
						'conditions' => array('sessionid' => $sessionid),
						'recursive' => 0
		 ));
	 	$this->loadModel('User');	 
 		$agent = $this->User->find('first', array(
			'fields' => array('User.id','User.agent_status', 'User.consults_nb'),
            'conditions'    => array('User.agent_number' => $call['CallInfo']['agent'], 'User.role' => 'agent', 'User.deleted' => 0),
            'recursive'     => -1
        ));
		
		$dt = new DateTime(date('Y-m-d H:i:s'));
		$dt->modify('- 120 minutes');
		$delai = $dt->format('Y-m-d H:i:s');
		$delay2 = $dt->getTimestamp();
		
		/*$this->loadModel('UserCreditLastHistory');
        $consult = $this->UserCreditLastHistory->find('first',array(
            'conditions' => array(
                'date_end' => null,
                'agent_id' => $agent['User']['id']
            ),
            'recursive' => -1
        ));*/
		
		$this->loadModel('Chat');
        $chat = $this->Chat->find('first',array(
            'conditions' => array(
                'date_end' => null,
				'date_start >' => $delai,
                'to_id' => $agent['User']['id']
            ),
            'recursive' => -1
        ));
		
		$calllive = $this->CallInfo->find('first',array(
						'conditions' => array('sessionid !=' => $sessionid, 'time_stop' => null, 'agent' => $call['CallInfo']['agent'],'time_getstatut >' => $delay2),
						'recursive' => 0
		 ));
		
		//look si tchat ou call pour liberer agent
		
      	if($agent['User']['id'] && $agent['User']['agent_status'] == 'busy' && empty($chat) && empty($calllive) ){
			$this->User->id = $agent['User']['id'];
		  	if($this->User->saveField('agent_status', 'available')){
					//On ajoute le changement dans la table historique
					$this->loadModel('UserStateHistory');
					$this->UserStateHistory->create();
					$this->UserStateHistory->save(array(
						'user_id' => $agent['User']['id'],
						'state' => 'available'
					));
			}
		}
		
		//on ferme dernier call bugué
		$this->loadModel('UserCreditLastHistory');
		$conditions = array('agent_id' => $agent['User']['id'],'sessionid' => $sessionid, 'date_end' => null);
		$lastCom = $this->UserCreditLastHistory->find('first',array(
						'conditions' => array($conditions),
						'order' => 'user_credit_last_history desc',
						'recursive' => -1
		));
		if(!empty($lastCom)){
			//La date de début en secondes
			$timestampStart = new DateTime($lastCom['UserCreditLastHistory']['date_start']);
			$timestampStart = $timestampStart->getTimestamp();
			//La date de fin
			$date_end = date('Y-m-d H:i:s',$timestamp);

			//Durée de la communication
			$comSecond = $timestamp - $timestampStart;
			//Nombre de crédits utilisés pour la communication
			$credits = ceil($comSecond/(int)Configure::read('Site.secondePourUnCredit'));
						
			//On retire l'id du model LastHistory
			$lastHistoryID = $lastCom['UserCreditLastHistory']['user_credit_last_history'];
			unset($lastCom['UserCreditLastHistory']['user_credit_last_history']);
			$lastCom['UserCreditLastHistory']['user_id'] = $lastCom['UserCreditLastHistory']['users_id'];
			unset($lastCom['UserCreditLastHistory']['users_id']);
			$updateCom = array(
							'seconds'   => $comSecond,
							'credits'   => $credits,
							'user_credits_after' => $credits,
							'date_end'  => $date_end
			);

			//On assemble les infos
			$updateCom = array_merge($lastCom['UserCreditLastHistory'],$updateCom);
			//On met à jour le model UserCreditLastHistory
			$lastCom = $updateCom;
			$lastCom['date_end'] = $date_end;
			$lastCom['date_start'] = $updateCom['date_start'];
			$lastCom['users_id'] = $updateCom['user_id'];
			$lastCom['phone_number'] = $updateCom['phone_number'];
			unset($lastCom['user_id']);
			$lastCom['agent_pseudo'] = $updateCom['agent_pseudo'];
			$lastCom = $this->UserCreditLastHistory->value($lastCom);
			$this->UserCreditLastHistory->updateAll($lastCom,array('user_credit_last_history' => $lastHistoryID));
						
		}
		
		//cumul comm
		$consults_nb = $agent['User']['consults_nb'] + 1;
		$this->User->id = $agent['User']['id'];
		$this->User->saveField('consults_nb', $consults_nb);

		//Save réussi
        return array('response_code' => 0, 'response' => true);
	}


    public function __api_startconsult($parms=false){
		
        //On vérifie les paramètres
        $cust_personal_code = (isset($parms['cust_personal_code']) ?(int)$parms['cust_personal_code']:false);
        $agent_number = (isset($parms['agent_number']) ?(int)$parms['agent_number']:false);
		
	
        if(!isset($parms['phone_number']) || !isset($parms['timestamp']))
            return array('response_code' => 13, 'response' => false);


        $called_number = (isset($parms['called_number'])?trim($parms['called_number']):false);
        if (!$called_number){
            return array('response_code' => 25, 'response' => false);
        }
		
		$sessionid = (isset($parms['sessionid'])?trim($parms['sessionid']):false);
        if (!$sessionid){
            return array('response_code' => 29, 'response' => false);
        }

        //Customer introuvable
        if(!$cust_personal_code)
            return array('response_code' => 14, 'response' => false);
        //Agent introuvable
        if(!$agent_number)
            return array('response_code' => 15, 'response' => false);
		$timestamp = (isset($parms['timestamp'])?trim($parms['timestamp']):false);	
		if ($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE call_infos SET time_start = '{$timestamp}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}	
		
		//on recup info call info
		 $this->loadModel('CallInfo');
		 $call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.called_number', 'CallInfo.line', 'CallInfo.callerid'),
						'conditions' => array('sessionid' => $sessionid),
						'recursive' => 0
		 ));
		 $customer_personal_code = $cust_personal_code;
		 if(count($call) && $cust_personal_code == 999999){
			 switch ($call['CallInfo']['line']) {
							case 'CH-0901801885':
							case 'CH-+41225183456':
								$customer_personal_code = 999998;
								break;
							case 'BE-090755456':
							case 'BE-+3235553456':
								$customer_personal_code = 999997;
								break;
							case 'BE-090755456 mob.':
								$customer_personal_code = 999996;
								break;
							case 'LU-+35227864456':
							case 'LU-90128222':
								$customer_personal_code = 999995;
								break;
							case 'CA-+18442514456':
							case 'CA-19007884466':
             case 'CA-19005289010':
								$customer_personal_code = 999994;
								break;
							case 'CA-#4466 Bell':
              case 'CA-#9010 Bell':
								$customer_personal_code = 999993;
								break;
							case 'CA-#4466 Rogers/Fido':
              case 'CA-#9010 Rogers/Fido':
								$customer_personal_code = 999990;
								break;
							case 'CA-#4466 Telus':
              case 'CA-#9010 Telus':
								$customer_personal_code = 999992;
								break;
							case 'CA-#4466 Videotron':
              case 'CA-#9010 Videotron':
							case 'AT-431230460013':
								$customer_personal_code = 999991;
								break;
							default:
								$customer_personal_code = 999999;
								break;
						}
		 }
		
        //On récupère les infos nécessaires pour enregistrer une consultation
        $this->loadModel('User');
        //Le client
        $customer = $this->User->find('first',array(
            'fields'        => array('id','credit'),
            'conditions'    => array(
                'personal_code' => $customer_personal_code,
                'role'          => 'client',
                'active'        => 1,
                'deleted'       => 0
            ),
            'recursive'     => -1
        ));
        if ((int)$customer['User']['credit']<10 && $cust_personal_code != 999999){
            return array('response_code' => 28, 'response' => false);
        }

        //L'agent
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

        //Customer introuvable
        if(empty($customer))
            return array('response_code' => 14, 'response' => false);
        //Agent introuvable
        if(empty($agent))
            return array('response_code' => 15, 'response' => false);

        if (empty($parms['timestamp'])){
            return array('response_code' => 26, 'response' => false);
        }

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
		
		
	
		if ($cust_personal_code != 999999){
			
             $consult = $this->UserCreditLastHistory->find('first',array(
				'conditions' => array(
					'date_end' => null,
					'users_id' => $customer['User']['id'],
				),
				'recursive' => -1
			));
			//Customer consultation
			if(!empty($consult))
				return array('response_code' => 19, 'response' => false);
			
			$this->loadModel('Chat');
			$consult = $this->Chat->find('first',array(
				'conditions' => array(
					'consult_date_start !=' => null,
					'consult_date_end' => null,
					'from_id' => $customer['User']['id'],
				),
				'recursive' => -1
			));
			//Customer consultation
			if(!empty($consult))
				return array('response_code' => 19, 'response' => false);
        }	
			
        $parms['timestamp'] = trim($parms['timestamp']);
        $date_start = date('Y-m-d H:i:s',$parms['timestamp']);
		
		//patch pour recup vrai num client
		if($call['CallInfo']['callerid'])$parms['phone_number'] = $call['CallInfo']['callerid'];
		
        //On sauvegarde les datas
        //Les données
        $startCom = array(
            'users_id'              => $customer['User']['id'],
            'user_credits_before'   => $customer['User']['credit'],
            'agent_id'              => $agent['User']['id'],
            'agent_pseudo'          => $agent['User']['pseudo'],
            'media'                 => 'phone',
            'called_number'         => $called_number,
			'sessionid'         	=> $sessionid,
            'phone_number'          => $parms['phone_number'],
            'date_start'            => $date_start
        );
        $this->UserCreditLastHistory->create();
        //Save réussi
        if($this->UserCreditLastHistory->save($startCom)){
            return array('response_code' => 0, 'response' => true);
        }else{  //Echec du save
            return array('response_code' => 999, 'response' => true);
        }
    }

    public function __api_endconsult($parms=false){
        //On vérifie les paramètres
        $cust_personal_code = (isset($parms['cust_personal_code']) ?$parms['cust_personal_code']:false);
        $timestamp = (isset($parms['timestamp']) ?$parms['timestamp']:false);
		$sessionid = (isset($parms['sessionid']) ?$parms['sessionid']:false);

		
        //Customer introuvable
        if(!$cust_personal_code)
            return array('response_code' => 14, 'response' => false);
        //Timestamp invalide
        if(!$timestamp)
            return array('response_code' => 13, 'response' => false);
        if (!$sessionid){
            return array('response_code' => 26, 'response' => false);
        }
		if($sessionid){
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE call_infos SET time_end = '{$timestamp}' WHERE sessionid = '{$sessionid}'");
			$mysqli_connect->close();
		}
        
		//on recup info call info
		 $this->loadModel('CallInfo');
		 $call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.called_number', 'CallInfo.line'),
						'conditions' => array('sessionid' => $sessionid),
						'recursive' => 0
		 ));
		 $customer_personal_code = $cust_personal_code;
		 if(count($call) && $cust_personal_code == 999999){
			switch ($call['CallInfo']['line']) {
							case 'CH-0901801885':
							case 'CH-+41225183456':
								$customer_personal_code = 999998;
								break;
							case 'BE-090755456':
							case 'BE-+3235553456':
								$customer_personal_code = 999997;
								break;
							case 'BE-090755456 mob.':
								$customer_personal_code = 999996;
								break;
							case 'LU-+35227864456':
							case 'LU-90128222':
								$customer_personal_code = 999995;
								break;
							case 'CA-+18442514456':
							case 'CA-19007884466':
              case 'CA-19005289010':
								$customer_personal_code = 999994;
								break;
							case 'CA-#4466 Bell':
              case 'CA-#9010 Bell':
								$customer_personal_code = 999993;
								break;
							case 'CA-#4466 Rogers/Fido':
              case 'CA-#9010 Rogers/Fido':
								$customer_personal_code = 999990;
								break;
							case 'CA-#4466 Telus':
              case 'CA-#9010 Telus':
								$customer_personal_code = 999992;
								break;
							case 'CA-#4466 Videotron':
              case 'CA-#9010 Videotron':
							case 'AT-431230460013':
								$customer_personal_code = 999991;
								break;
							default:
								$customer_personal_code = 999999;
								break;
						}
		 }
		
		//Les models
        $this->loadModel('User');
        $this->loadModel('UserCreditLastHistory');
        $customer = $this->User->find('first',array(
            'fields' => array('id', 'domain_id', 'is_come_back'),
            'conditions'    => array(
                'personal_code' => $customer_personal_code,
                'role'          => 'client',
                //'active'        => 1,
                'deleted'       => 0
            ),
            'recursive' => -1
        ));
        if (empty($customer)){
            return array('response_code' => 14, 'response' => false);
        }

        //La dernière communication 
        //if ($cust_personal_code !== 999999){
            $conditions = array('users_id' => $customer['User']['id'],'sessionid' => $sessionid, 'date_end' => null);
            $lastCom = $this->UserCreditLastHistory->find('first',array(
                'conditions' => array($conditions),
                'order' => 'user_credit_last_history desc',
                'recursive' => -1
            ));
      //  }else{

       // }

        if(empty($lastCom))
            return array('response_code' => 21, 'response' => false);



        if (strtotime($lastCom['UserCreditLastHistory']['date_start']) > (int)$timestamp){
            return array('response_code' => 27, 'response' => false);
        }

        //La date de début en secondes
        $timestampStart = new DateTime($lastCom['UserCreditLastHistory']['date_start']);
        $timestampStart = $timestampStart->getTimestamp();
        //La date de fin
        $date_end = date('Y-m-d H:i:s',$timestamp);

        //Durée de la communication
        $comSecond = $timestamp - $timestampStart;
        //Nombre de crédits utilisés pour la communication
        $credits = ceil($comSecond/(int)Configure::read('Site.secondePourUnCredit'));

        if ($parms['cust_personal_code'] < 999990){
            //On met à jour le crédit du customer
            $creditsAfter = $this->updateCredit($customer['User']['id'],$credits, true, true);
            //Si problème avec la mise à jour du crédit
            //if($creditsAfter === false)
                //return array('response_code' => 17, 'response' => false);
            if ($creditsAfter === false)
                $creditsAfter = 0;
        }
        if ($parms['cust_personal_code'] >= 999990) $creditsAfter = 0;

        //On retire l'id du model LastHistory
        $lastHistoryID = $lastCom['UserCreditLastHistory']['user_credit_last_history'];
        unset($lastCom['UserCreditLastHistory']['user_credit_last_history']);
        $lastCom['UserCreditLastHistory']['user_id'] = $lastCom['UserCreditLastHistory']['users_id'];
        unset($lastCom['UserCreditLastHistory']['users_id']);
        $updateCom = array(
            'seconds'   => $comSecond,
            'credits'   => $credits,
            'user_credits_after' => $creditsAfter,
            'date_end'  => $date_end
        );

        //On assemble les infos
        $updateCom = array_merge($lastCom['UserCreditLastHistory'],$updateCom);
        //On met à jour le model UserCreditLastHistory
        $lastCom = $updateCom;
        $lastCom['date_end'] = $date_end;
        $lastCom['date_start'] = $updateCom['date_start'];
        $lastCom['users_id'] = $updateCom['user_id'];
        $lastCom['phone_number'] = $updateCom['phone_number'];
        unset($lastCom['user_id']);
        $lastCom['agent_pseudo'] = $updateCom['agent_pseudo'];
        $lastCom = $this->UserCreditLastHistory->value($lastCom);
        $this->UserCreditLastHistory->updateAll($lastCom,array('user_credit_last_history' => $lastHistoryID));

        //Envoie de l'email
        if ($comSecond > 60 && $parms['cust_personal_code'] <= 999990)
            $this->emailEndConsult($customer['User']['id'], $updateCom['agent_id'], $comSecond, $updateCom['agent_pseudo'], $lastHistoryID, $updateCom['date_start']);
		
		
		//on sauvegarde data pour bonus agent
		$listing_utcdec = Configure::read('Site.utcDec');
		
		$date_bonus = new DateTime($updateCom['date_start']);
		$date_bonus->modify('+'.$listing_utcdec[$date_bonus->format('md')].' hour');
     	$annee_bonus = $date_bonus->format('Y');
		$mois_bonus = $date_bonus->format('m');
		
		$this->loadModel('BonusAgent');
		$bonus_agent = $this->BonusAgent->find('first', array(
					'conditions' => array('BonusAgent.id_agent' => $updateCom['agent_id'], 'annee' => $annee_bonus, 'mois' => $mois_bonus, 'active' => 1),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));
		$bonus_min_total = $comSecond;
		if($bonus_agent['BonusAgent']['min_total']) {
			$bonus_min_total = $bonus_min_total + $bonus_agent['BonusAgent']['min_total'];
			$bonus_agent['BonusAgent']['active'] = 0;
			$bonus_agent['BonusAgent']['date_add'] = "'".$bonus_agent['BonusAgent']['date_add']."'";
			$bonus_agent['BonusAgent']['IP'] = "'".$bonus_agent['BonusAgent']['IP']."'";
			$this->BonusAgent->updateAll($bonus_agent['BonusAgent'],array('id' => $bonus_agent['BonusAgent']['id']));
		}
		
		$id_bonus = 0;
		$palier = floor($bonus_min_total / 60);
		$this->loadModel('Bonus');
		$bonus = $this->Bonus->find('all', array(
                'order' => array('id'=> 'asc'),
                'recursive' => -1
            ));
		if(!empty($bonus)){
							foreach($bonus as $bobo){
								foreach($bobo as $b){
									if($palier >= $b['bearing'])
										$id_bonus = $b['id'];	
								}
							}
						}
		$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
		$bonusAgent = array();
		$bonusAgent['BonusAgent'] = array();
		$bonusAgent['BonusAgent']['id_agent'] = $updateCom['agent_id'];
		$bonusAgent['BonusAgent']['id_com'] = $lastHistoryID;
		$bonusAgent['BonusAgent']['id_bonus'] = $id_bonus;
		$bonusAgent['BonusAgent']['date_add'] = date('Y-m-d H:i:s');
		$bonusAgent['BonusAgent']['min_tel'] = $comSecond;
		$bonusAgent['BonusAgent']['min_tchat'] = 0;
		$bonusAgent['BonusAgent']['IP'] = $ip_user;
		$bonusAgent['BonusAgent']['annee'] = $annee_bonus;
		$bonusAgent['BonusAgent']['mois'] = $mois_bonus;
		$bonusAgent['BonusAgent']['active'] = 1;
		$bonusAgent['BonusAgent']['min_total'] = $bonus_min_total;
				
		$this->BonusAgent->create();
		$this->BonusAgent->save($bonusAgent);
		
		
		//update costAgent
		$this->loadModel('CostAgent');
		$cost_agent = $this->CostAgent->find('first', array(
					'conditions' => array('CostAgent.id_agent' => $updateCom['agent_id']),
					'recursive' => -1
				));
		$cost_min_total = $comSecond / 60;
		if($cost_agent['CostAgent']['nb_minutes']) {
			$cost_min_total = $cost_min_total + $cost_agent['CostAgent']['nb_minutes'];
			$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
			$this->CostAgent->save($cost_agent);
			//$this->CostAgent->updateAll($cost_agent['CostAgent'],array('id' => $cost_agent['CostAgent']['id']));
		}else{
			$cost_agent = array();
			$cost_agent['CostAgent'] = array();
			$cost_agent['CostAgent']['id_agent'] = $updateCom['agent_id'];
			$cost_agent['CostAgent']['id_cost'] = 1;
			$cost_agent['CostAgent']['nb_minutes'] = $cost_min_total;
			$this->CostAgent->create();
			$this->CostAgent->save($cost_agent);
			$cost_agent = $this->CostAgent->find('first', array(
					'conditions' => array('CostAgent.id_agent' => $updateCom['agent_id']),
					'recursive' => -1
				));
		}
		
		$id_cost = 0;
		$palier = $cost_min_total;
		$this->loadModel('Cost');
		if($cost_agent['CostAgent']['id_cost'] < 4)
		$costs = $this->Cost->find('all', array(
				'conditions' =>  array('id <' => 4),
                'order' => array('id'=> 'asc'),
                'recursive' => -1
            ));
		if($cost_agent['CostAgent']['id_cost'] >= 5 && $cost_agent['CostAgent']['id_cost'] < 9)
		$costs = $this->Cost->find('all', array(
				'conditions' =>  array('id >=' => 5,'id <' => 9),
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
						
			$id_cost = $id_cost +1;
			$cost_agent['CostAgent']['id_cost'] = $id_cost;
			$this->CostAgent->save($cost_agent);
		}
		//$this->CostAgent->updateAll($cost_agent['CostAgent'],array('id' => $cost_agent['CostAgent']['id']));
		
		//Sponsorship
		App::import('Model', 'Sponsorship');
		$Sponsorship = new Sponsorship();
		$Sponsorship->Benefit($lastHistoryID);
		
		//On sauvegarde la com
		$this->loadModel('UserCreditHistory');
		
		$agent = $this->User->find('first',array(
            'fields' => array('id', 'phone_api_use', 'phone_mobile'),
            'conditions'    => array(
                'id' => $updateCom['agent_id'],
            ),
            'recursive' => -1
        ));
		if($agent && $agent['User']['phone_api_use'] == $agent['User']['phone_mobile'])$updateCom['is_mobile'] = 1;
		$updateCom['expert_number'] = $agent['User']['phone_api_use'];
		
		$type_pay = 'pre';
		$domainid = '';
		$updateCom['is_new'] = 0;
		if($updateCom['user_id'] == 286 || $updateCom['user_id'] == 3630 || $updateCom['user_id'] == 3631 || $updateCom['user_id'] == 3632 || $updateCom['user_id'] == 3633 || $updateCom['user_id'] == 3634 || $updateCom['user_id'] == 3635 || $updateCom['user_id'] == 3636 || $updateCom['user_id'] == 3637 || $updateCom['user_id'] == 3638 ){
			$lastComCheck = $this->UserCreditHistory->find('first', array(
					'conditions'    => array('UserCreditHistory.phone_number' => $updateCom['phone_number']),
					'recursive'     => -1
				));
			$type_pay = 'aud';
			switch ($updateCom['user_id']) {
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
			$lastComCheck = $this->UserCreditHistory->find('first', array(
					'conditions'    => array('UserCreditHistory.user_id' => $updateCom['user_id']),
					'recursive'     => -1
				));
			$domainid = $customer['User']['domain_id'];
		}
		
		
		
		if(!$lastComCheck && !$customer['User']['is_come_back'])$updateCom['is_new'] = 1;
		$updateCom['type_pay'] = $type_pay;
		$updateCom['domain_id'] = $domainid;
        
        $this->UserCreditHistory->create();
        if($this->UserCreditHistory->save($updateCom)){
			$this->calcCAComm($this->UserCreditHistory->id);
            return array('response_code' => 0, 'response' => true);
		}else
            return array('response_code' => 17, 'response' => false);
    }
    public function __api_setnewsmsalert($parms=false){
        //On vérifie les paramètres
        $cust_personal_code = (isset($parms['cust_personal_code']) ?(int)$parms['cust_personal_code']:false);
        $agent_number = (isset($parms['agent_number']) ?(int)$parms['agent_number']:false);
        $cust_mobilephone_number = (isset($parms['cust_mobilephone_number']) ?$parms['cust_mobilephone_number']:false);


        //Customer introuvable
        if(!$cust_personal_code)
            return array('response_code' => 22, 'response' => false);

        if (!$agent_number)
            return array('response_code' => 23, 'response' => false);

        if (empty($cust_mobilephone_number))
            return array('response_code' => 24, 'response' => false);

        /* On vérifie que le code client existe */
            $user_id = 0;
            $this->loadModel('User');
            $rows = $this->User->find("first", array(
                'fields' => array('id','domain_id','lang_id'),
                'recursive' => -1,
                'conditions' => array(
                    'role' => 'client',
                    'personal_code' => $cust_personal_code,
                    'deleted' => 0,
                    'active'  => 1,
                    'valid'   => 1
                )
            ));
            $user_id = isset($rows['User']['id'])?(int)$rows['User']['id']:false;
            $customer = isset($rows['User'])?$rows['User']:false;
            if (!$user_id) return array('response_code' => 14, 'response' => false);

        /* On verifie que le code agent existe */
            $agent_id = 0;
            $this->loadModel('User');
            $rows = $this->User->find("first", array(
                'fields' => array('id'),
                'recursive' => -1,
                'conditions' => array(
                    'role' => 'agent',
                    'agent_number' => $agent_number,
                    'deleted' => 0,
                    'active'  => 1,
                    'valid'   => 1
                )
            ));
            $agent_id = isset($rows['User']['id'])?(int)$rows['User']['id']:false;
            if (!$agent_id) return array('response_code' => 15, 'response' => false);

        /* tout est ok, on créé l'alerte */
            $this->loadModel('Alert');
            foreach ($this->consult_medias AS $media => $text){
                $row = $this->Alert->find("first", array(
                    'fields'     => array('id'),
                    'recursive'  => -1,
                    'conditions' => array(
                        'users_id' => $user_id,
                        'agent_id' => $agent_id,
                        'media'    => $media
                    )
                ));
                if (isset($row['Alert']['id'])){
                    /* On a déjà une alerte pour ce media */
                        $this->Alert->id = $row['Alert']['id'];
                        $this->Alert->save(array(
                            'phone_number' => $cust_mobilephone_number
                        ));
                    /* On supprime l'historique du jour, pour être sûr que le client recevra cette alerte
                    même s'il en a par ailleurs déjà recu ce jour */
                        $this->loadModel('AlertHistory');
                        $this->AlertHistory->deleteAll(array(
                            'alerts_id' => $row['Alert']['id']
                        ), false, false);
                }else{
                    $this->Alert->create();
                    $this->Alert->save(array(
                        'users_id' => $user_id,
                        'agent_id' => $agent_id,
                        'media'    => $media,
                        'phone_number' => $cust_mobilephone_number,
                        'domain_id'=> $customer['domain_id'],
                        'lang_id'  => $customer['lang_id'],
                        'alert_by_day' => 1
                    ));
                }
            }

        return array('response_code' => 0, 'response' => true);
    }
    public function testmathieu()
    {
       
        $this->autoRender = false;
        //$this->emailEndConsult(258, 1600, 'Luloo');
		//$this->emailEndConsult(494,332, 125, 'Ari', 1960, '2016-01-29 17:30:00');
		//var_dump('oktestmathieu');
    }
    private function emailEndConsult($idUser, $agent_id, $secondCom, $pseudoAgent, $consult_id, $consult_date){
        //Email de l'user
        $email = $this->User->field('email', array('id' => $idUser));
		$prenom = $this->User->field('firstname', array('id' => $idUser));
        $lang_id = $this->User->field('lang_id', array('id' => $idUser));
        $this->loadModel('Lang');
        $langCode = $this->Lang->field('language_code', array('id_lang' => $lang_id));
        //Paramètre pour le mail de confirmation
        $paramEmail = array(
            'pseudo' => $pseudoAgent,
            'timeCom' => (int)($secondCom/60),
            'linkReview' => Configure::read('Site.baseUrlFull')
        //.'/'.Router::url(array('controller' => 'accounts', 'action' => 'review', 'admin' => false, 'language' => $langCode),false)
        );
		if($consult_id && !in_array($idUser,Configure::read('Review.no_send_email'))){
			$this->sendCmsTemplateByMail(192, $lang_id, $email, array(
				'SITE_NAME'    => Configure::read('Site.name'),
				'AGENT_PSEUDO' => $paramEmail['pseudo'],
				'PRENOM' => $prenom,
				'COM_DUREE'    => $paramEmail['timeCom'],
				'DATE_COM'    => CakeTime::format($consult_date, '%d-%m-%Y'),
				'REVIEW_LINK'  => Router::url(array('controller' => 'reviews', 'action' => 'reviews_post?u='.$idUser.'&a='.$agent_id.'&c='.$consult_id),false)
			));
		}
        //$this->sendEmail($email,'Communication terminée','end_consult',array('param' => $paramEmail));
    }
}

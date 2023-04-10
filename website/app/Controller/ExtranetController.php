<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::import('Vendor', 'Noox/Api');

class ExtranetController extends AppController {
    protected $myRole = '';


    public function beforeFilter() {
        parent::beforeFilter();
    }

    /**
     * Modifie le compte d'un user. (client ou agent)
     *
     * @param string $model Le nom du model
     * @param array $requestData Les datas du comtpe
     *
     * @return void
     */
    public function _editCompte($model, $requestData){
        $modifCompte = false;
        $modif = null;
        //Variable pour l'action selon le $model
        $varAction = array(
            'Account' => array('actionConfirmation' => 'confirmation', 'controller' => 'accounts', 'tab' => 'profil', 'role' => 'client'),
            'Agent' => array('actionConfirmation' => 'confirmation_agent', 'controller' => 'agents', 'role' => 'agent')
        );

        //On vérifie le role de l'user
        $lien = array();
        if(strcmp($this->Auth->user('role'),'agent') == 0)
            $lien = array('key' => 'an', 'value' => 'agent_number');
        elseif (strcmp($this->Auth->user('role'),'client') == 0)
            $lien = array('key' => 'pc', 'value' => 'personal_code');

        //Modifie l'email
        $modif = $this->_editEmail($requestData[$model]['email'], $varAction[$model]['role']);
        //S'il y a une erreur pendant la modification de l'email
        if((int)$modif == -1){
            if(isset($varAction[$model]['tab']))
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil', 'tab' => $varAction[$model]['tab']));
            else
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil'));
        }
        elseif($modif){ //Sinon modification good
            $modifEmail = true;
            $modifCompte = true;
        }

        //Modifie le mot de passe
        $modif = $this->_editPasswd($requestData[$model]['passwd'],$requestData[$model]['passwd2']);
        //S'il y a une erreur pendant la modification

		if(!$requestData[$model]['passwd']){
			$this->Session->setFlash(__('Vous n\'avez saisi aucun nouveau mot de passe.'),'flash_error');
		}else{

        if((int)$modif == -1){
			$this->Session->setFlash(__('La modification de votre mot de passe est incomplète ou incorrecte. Veuillez recommencer.'),'flash_error');

            if(isset($varAction[$model]['tab']))
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil', 'tab' => $varAction[$model]['tab']));
            else
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil'));
        }
        elseif($modif) //Sinon modification good
            $modifCompte = true;
	}
        //S'il y a bien eu une modification
        if($modifCompte){
            //On charge le model User
            $this->loadModel('User');
            //Enregistre la date de modification des données
            $this->User->id = $this->Auth->user('id');
            $this->User->saveField('date_upd', date('Y-m-d H:i:s'));

            //Si l'email a été modifié
            if(isset($modifEmail)){
                //Date de la dernière connexion
                $lastconnexion = $this->User->field('date_lastconnexion');
                //Paramètre pour le mail de confirmation
                $paramEmail = array(
                    'email' => $requestData[$model]['email'],
                    'urlConfirmation' => $this->linkGenerator('users',$varAction[$model]['actionConfirmation'],array(
                            $lien['key'] => $this->Auth->user($lien['value']),
                            'mc' => Security::hash($requestData[$model]['email'].$lastconnexion,null,true)
                        ))
                );


                //$this->sendEmail($requestData[$model]['email'],'Validation de la modification de votre compte','edit_mail',array('param' => $paramEmail));
                $this->sendCmsTemplateByMail(194, $this->User->field('lang_id'), $requestData[$model]['email'], array(
                    'CONFIRM_LINK'  =>  $paramEmail['urlConfirmation'],
                    'CONFIRM_EMAIL' =>  $paramEmail['email']
                ));

                //L'user ne peut plus avoir accès au site
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('emailConfirm', 0);

                $message = __('Votre compte a été modifié. Vous allez recevoir un mail pour la confirmation de votre compte.');
            }else
                $message = __('Votre compte a été modifié. Veuillez vous reconnecter');

            //Déconnexion de l'user car son compte a été modifié
			$this->Session->setFlash('Votre nouveau mot de passe ou email est bien pris en compte.', 'flash_success');
			$this->redirect(array('controller' => $varAction[$model]['controller'], 'action' =>'profil'));
          /*  $this->redirect(array('controller' => 'users', 'action' => 'logout',
                '?' => array(
                    'message' => $message,
                    'template' => 'flash_success'
                )
            ));*/
        }else{
            if(isset($varAction[$model]['tab']))
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil', 'tab' => $varAction[$model]['tab']));
            else
                $this->redirect(array('controller' => $varAction[$model]['controller'], 'action' => 'profil'));
        }
    }

    /**
     * Permet de changer de mot de passe d'un user
     *
     * @param string $passwd Mot de passe
     * @param string $passwd2 Confirmation du mot de passe
     *
     * @return bool | int
     * true : passwd modifier     false : pas de modification passwd   -1 : erreur dans la modification
     */
    public function _editPasswd($passwd, $passwd2){
        //Changement de mot de passe
        if(!empty($passwd)){
            //Si mot de passe confirmer
            if(!empty($passwd2)){
                //On charge le model User
                $this->loadModel('User');
                // Si mot de passe moins 8 caractères ou différent
                if(strlen($passwd) < 8){
                    $this->Session->setFlash(__('Votre mot de passe doit faire 8 caractères au minimum.'),'flash_warning');
                    return -1;
                }
                elseif(strcmp($passwd, $passwd2) != 0){
                    $this->Session->setFlash(__('Les mots de passe sont différents.'),'flash_warning');
                    return -1;
                }
                //Hash le mot de passe
                $passwd = $this->hashMDP($passwd);

                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('passwd', $passwd);
                return true;
            }else{
                $this->Session->setFlash(__('Veuillez confirmer votre nouveau mot de passe'),'flash_warning');
                return -1;
            }
        }
        return false;
    }

    /**
     * Permet de changer d'email d'un user
     *
     * @param string $email L'email de l'user
     * @param string $role  Le role de l'user
     *
     * @return bool | int
     * true : email modifier     false : pas de modification email   -1 : erreur dans la modification
     */
    public function _editEmail($email, $role){
        //Changement d'adresse mail
        if(strcmp($email,$this->Auth->user('email')) != 0){
            //On charge le model User
            $this->loadModel('User');
            //Si email unique
            if($this->User->singleEmail($email, $role)){
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('email', $email);
                return true;
            }else{
                $this->Session->setFlash(__('L\'email renseigné est déjà enregistré'),'flash_warning');
                return -1;
            }
        }
        return false;
    }


    public function _history($role){
        //Utilisateur non connecté
        if(!$this->Auth->loggedIn())
            throw new Exception("Erreur de sécurité !", 1);

	
        /* On vérifie que l'utilisateur est bien dans son role */
	
	/*
        if ($this->Auth->user('role') !== $role)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
	
	*/
	
	
        //Pour que le paginator fonctionne avec la réécriture du lien
        $this->paginatorParams();

        //On charge le model
        $this->loadModel('UserCreditLastHistory');

        //Les conditions de base
        $conditions = array(($role === 'client' ?'users_id':'agent_id') => $this->Auth->user('id'));
		$limit = 15;//($role === 'client' ?10:15)
		//Avons-nous un filtre sur la date ??
		$is_date_filtre = 0;
        if($this->Session->check('Date')){

			$listing_utcdec = Configure::read('Site.utcDec');

			$dmax = new DateTime($this->Session->read('Date.end').' 23:59:59');
			$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
			$session_date_max =  $dmax->format('Y-m-d H:i:s');

			$dmin = new DateTime($this->Session->read('Date.start'). '00:00:00');
			$cut = explode('-',$this->Session->read('Date.start') );
				$datecomp = $cut[2].$cut[1].$cut[0];
			//if($datecomp >= '20190301')
			$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
			//	else
			//$dmin->modify('-0 hour');
			$session_date_min =  $dmin->format('Y-m-d H:i:s');

			//if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';

                $conditions = array_merge($conditions, array(
                    'UserCreditLastHistory.date_start >=' => $session_date_min,
                    'UserCreditLastHistory.date_start <=' => $session_date_max
               ));
			//$limit = 999999;
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

        //Si en mode client
        if($role === 'client'){
            //On récupère les photos des agents
            foreach($historiqueComs as $indice => $val){
                $photo = $this->mediaAgentExist($val['Agent']['agent_number'],'Image');
                if($photo === false)
                    $historiqueComs[$indice]['Agent']['photo'] = '/'.Configure::read('Site.defaultImage');
                else
                    $historiqueComs[$indice]['Agent']['photo'] = '/'.$photo;
            }
        }
		if($role === 'agent'){
			$dbb_r = new DATABASE_CONFIG();
				$dbb_s = $dbb_r->default;
				$mysqli_s = new mysqli($dbb_s['host'], $dbb_s['login'], $dbb_s['password'], $dbb_s['database']);
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

					//check si email perdu
					/*$result3 = $mysqli_s->query("SELECT * from user_penalities WHERE message_id = '{$row['UserCreditLastHistory']['sessionid']}'");
					$row3 = $result3->fetch_array(MYSQLI_ASSOC);
					if($row3['id']){
						$row['UserCreditLastHistory']['price'] = 0;
					}*/
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
        $this->set(compact('historiqueComs', 'date_fact', 'total_bonus'));
    }

    /**
     * Permet l'affichage des clients ou agents en mode admin sur les actions index
     *
     * @param string $controller
     * @return mixed
     */
    public function _adminIndex($controller, $withPaginate=true, $fields=false, $cond_supp = array(), $orderby = array('User.date_add' => 'desc')){

        //Conditions par défaut
        $conditions = array();
        if(strcmp($controller,'Accounts') == 0)
            $conditions = array('User.role' => 'client');
        elseif(strcmp($controller,'Agents') == 0)
            $conditions = array('User.role' => 'agent','User.deleted' => 0);
		if(count($cond_supp)>0)
		$conditions = array_merge($cond_supp,$conditions);

        //On rajoute les conditions pour les comptes avec des emails non confirmés
        if(isset($this->params->query['email'])){
            $conditions = array_merge($conditions, array('User.emailConfirm' => 0));
            /*if(strcmp($controller,'Accounts') == 0)
                $conditions = array_merge($conditions,array('User.active' => 0));
            elseif(strcmp($controller,'Agents') == 0)
                $conditions = array_merge(
                $conditions,
                array('OR' => array(
                    array('User.valid' => 0),
                    array('User.valid' => 1, 'User.active' => 0, 'User.date_lastconnexion !=' => 'NULL')
                ))
            );*/
        }
        //Ou les conditions pour les comptes non validés
        elseif (isset($this->params->query['compte'])){
            /*$conditions = array_merge($conditions, array('User.date_lastconnexion' => null,
                'OR' => array(
                    array('User.active' => 0),
                    array('User.valid' => 0)
                )
            ));*/
            //Client
            if(strcmp($controller,'Accounts') == 0)
                $conditions = array_merge($conditions, array('User.firstname' => null,
                                                             'OR' => array(
                                                                 array('User.active' => 0),
                                                                 array('User.valid' => 0)
                                                             )
                ));
            //Agent
            elseif(strcmp($controller,'Agents') == 0)
                $conditions = array_merge($conditions, array('User.date_lastconnexion' => null,
                                                             'OR' => array(
                                                                 array('User.active' => 0),
                                                                 array('User.valid' => 0)
                                                             )
                ));
        }
        //Recherche

        if($this->request->is('post')){
            if($controller === 'Agents'){
				if(isset($this->request->data['Agent']['active']) && is_numeric($this->request->data['Agent']['active']))
                    $conditions = array_merge($conditions, array('User.active' => $this->request->data['Agent']['active']));
                if(isset($this->request->data['Agent']['agent_number']) && !empty($this->request->data['Agent']['agent_number']))
                    $conditions = array_merge($conditions, array('User.agent_number LIKE' => '%'.$this->request->data['Agent']['agent_number'].'%'));
                if(isset($this->request->data['Agent']['email']) && !empty($this->request->data['Agent']['email']))
                    $conditions = array_merge($conditions, array('User.email LIKE' => '%'.$this->request->data['Agent']['email'].'%'));
				if(isset($this->request->data['Account']['status']) && !empty($this->request->data['Account']['status']) && $this->request->data['Account']['status'])
                    $conditions = array_merge($conditions, array('User.status' => $this->request->data['Account']['status']));
				if(isset($this->request->data['Agent']['adr_ip']) && !empty($this->request->data['Agent']['adr_ip']))
                    $conditions = array_merge($conditions, array('UserIp.IP LIKE' => '%'.$this->request->data['Agent']['adr_ip'].'%'));
                elseif(isset($this->request->data['Agent']['pseudo']) && !empty($this->request->data['Agent']['pseudo'])){
					$conditions = array_merge($conditions, array(
                        'OR' => array(
								array('User.pseudo LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%'),
								array('User.firstname LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%'),
								array('User.lastname LIKE' => '%'.$this->request->data['Agent']['pseudo'].'%')
							)
						));
					}
            }elseif($controller === 'Accounts'){
                if(isset($this->request->data['Account']['personal_code']) && !empty($this->request->data['Account']['personal_code']))
                    $conditions = array_merge($conditions, array('User.personal_code LIKE' => '%'.$this->request->data['Account']['personal_code'].'%'));
                if(isset($this->request->data['Account']['email']) && !empty($this->request->data['Account']['email']))
                    $conditions = array_merge($conditions, array('User.email LIKE' => '%'.$this->request->data['Account']['email'].'%'));
				if(isset($this->request->data['Account']['adr_ip']) && !empty($this->request->data['Account']['adr_ip']))
                    $conditions = array_merge($conditions, array('UserIp.IP LIKE' => '%'.$this->request->data['Account']['adr_ip'].'%'));
                elseif(isset($this->request->data['Account']['fullname']) && !empty($this->request->data['Account']['fullname']))
                    $conditions = array_merge($conditions, array(
                        'OR' => array(
                            array('User.firstname LIKE' => '%'.$this->request->data['Account']['fullname'].'%'),
                            array('User.lastname LIKE' => '%'.$this->request->data['Account']['fullname'].'%')
                        )
                    ));
            }

        }
		$limit = 10;
		if($this->request->is('post'))$limit = '-1';
        if ($withPaginate){
            $this->Paginator->settings = array(
                'fields' => array('UserCountryLang.name', 'User.*', 'UserIp.IP'),
                'conditions' => $conditions,
                'order' => $orderby,
				'paramType' => 'querystring',
                'joins' => array(
                    array('table' => 'user_country_langs',
                        'alias' => 'UserCountryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'UserCountryLang.user_countries_id = User.country_id',
                            'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    ),
					array('table' => 'user_ips',
                        'alias' => 'UserIp',
                        'type' => 'left',
                        'conditions' => array(
                            'UserIp.user_id = User.id',
                        )
					)
                ),
				'group' => 'User.id',
                'limit' => $limit ,
				'maxLimit' => $limit
            );

            $users = $this->Paginator->paginate($this->User);
        }else{
            $options = array(
                'fields' => array_merge($fields,array('UserCountryLang.name')),
                'conditions' => $conditions,
                'order' => $orderby,
                'recursive' => -1,
                'joins' => array(
                    array('table' => 'user_country_langs',
                        'alias' => 'UserCountryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'UserCountryLang.user_countries_id = User.country_id',
                            'UserCountryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                )
            );

            $users = $this->User->find("all", $options);

        }
        foreach($users as $i => $user){
            $user['User']['country_id'] = $user['UserCountryLang']['name'];
            unset($user['UserCountryLang']);
            $users[$i] = $user;
        }

        return $users;
    }

    public function admin_exportcsv()
    {
		set_time_limit ( 0 );
		ini_set("memory_limit",-1);
		$dbb_patch = new DATABASE_CONFIG();
		$dbb_connect = $dbb_patch->default;

		$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);

        $this->autoRender = false;
        $filename = Configure::read('Site.pathExport').'/all_'.$this->request->params['controller'].'.csv';
        $this->_fp = fopen($filename, 'w+');
        fwrite($this->_fp, "\xEF\xBB\xBF");

        $fields = array('id','status','firstname','lastname','pseudo','email','birthdate','role','address','postalcode','city','sexe','country_id','optin','personal_code','last_passwd_gen',
  'date_add','date_lastconnexion','date_last_activity','credit','siret','society_type_id','societe_statut','societe','vat_num','vat_num_status','vat_num_proof','vat_num_status_reason','vat_num_status_reason_desc','invoice_vat_id','save_bank_card','stripe_base','stripe_balance','stripe_available','iban','rib','mode_paiement','consult_chat','consult_email','consult_phone','phone_number','phone_number2','phone_mobile','limit_credit','emailConfirm','careers','source','active');
        if ($this->request->params['controller'] == 'accounts'){
            foreach ($fields AS $k => $v){
                if (in_array($v, array('consult_chat','consult_phone','consult_email','siret','society_type_id','societe_statut','societe','vat_num','vat_num_status','vat_num_proof','vat_num_status_reason','vat_num_status_reason_desc','invoice_vat_id','save_bank_card','iban','rib','mode_paiement','date_last_activity','stripe_base','stripe_balance','stripe_available')))
                    unset($fields[$k]);
            }
        }

        if($this->Session->check('Date')){
            $conditions = array(
                'User.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                'User.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
            );
        }

        $users = $this->_adminIndex(
            ucfirst($this->request->params['controller']),
            false,
            $fields,$conditions);



		//$fields['time_connexion'] = '';

        foreach ($users AS $k => $row){

            if ($k == 0){
				$row['User']['date_1_appel'] = '';
				$row['User']['time_connexion'] = '';
				$row['User']['nb_phone'] = '';
				$row['User']['during_phone'] = '';
				$row['User']['nb_chat'] = '';
				$row['User']['during_chat'] = '';
				$row['User']['nb_email'] = '';
				$row['User']['during_email'] = '';
				$row['User']['user_ip'] = '';
				$row['User']['date_last_order'] = '';
				$row['User']['deja achete'] = '';
				$row['User']['deja consomme'] = '';

				$row['User'] = $this->replaceKeys("societe_statut", "societe_statut_autre", $row['User']);
				$row['User'] = $this->replaceKeys("society_type_id", "societe_statut", $row['User']);
				$row['User'] = $this->replaceKeys("invoice_vat_id", "tva_type", $row['User']);
				$row['User'] = $this->replaceKeys("save_bank_card", "tva_taux", $row['User']);


				if ($this->request->params['controller'] == 'agents'){
					$row['User']['specialite_astrologue'] = '';
					$row['User']['specialite_cartomancien'] = '';
					$row['User']['specialite_voyant'] = '';
					$row['User']['specialite_numerologue'] = '';
					$row['User']['specialite_tarologue'] = '';
					$row['User']['specialite_magnetiseur'] = '';
					$row['User']['specialite_coaching'] = '';
					$row['User']['specialite_reves'] = '';
					$row['User']['specialite_medium'] = '';
				}

                fputcsv($this->_fp, array_keys($row['User']), ';' ,'"');
            }

			switch ($row['User']["status"]) {
				case 0:
					$row['User']["status"] = '--';
					break;
				case 1:
					$row['User']["status"] = 'Envoi questionnaire';
					break;
				case 2:
					$row['User']["status"] = 'Questionnaire reçu';
					break;
				case 3:
					$row['User']["status"] = 'Experts refusés';
					break;
				case 4:
					$row['User']["status"] = 'Expert relancé';
					break;
				case 5:
					$row['User']["status"] = 'Experts sans réponse';
					break;
				case 6:
					$row['User']["status"] = 'Refus de l\'expert';
					break;
				case 7:
					$row['User']["status"] = 'Dossier incomplet Standby';
					break;
				case 8:
					$row['User']["status"] = 'Divers';
					break;
				case 9:
					$row['User']["status"] = 'Expert en ligne';
					break;
				case 10:
					$row['User']["status"] = 'Expert en pause';
					break;
				case 11:
					$row['User']["status"] = 'Expert à controler';
					break;
				case 12:
					$row['User']["status"] = 'Attente entretien téléphone';
					break;
				case 13:
					$row['User']["status"] = 'Envoi contrat';
					break;
				case 14:
					$row['User']["status"] = 'Portage salarial';
					break;
				case 15:
					$row['User']["status"] = 'Cessation partenariat';
					break;
				case 16:
					$row['User']["status"] = 'A faire patienter';
					break;
				case 17:
					$row['User']["status"] = 'En ligne/portage';
					break;
				case 18:
					$row['User']["status"] = 'Radié/détournement clients';
					break;
				case 19:
					$row['User']["status"] = 'Contrôle PI & déclaration';
					break;
				case 20:
					$row['User']["status"] = 'Ouverture en cours';
					break;
			}

			$row['User']['date_1_appel'] = '-';
			$row['User']['time_connexion'] = '0';
			$row['User']['nb_phone'] = '0';
			$row['User']['during_phone'] = '0';
			$row['User']['nb_chat'] = '0';
			$row['User']['during_chat'] = '0';
			$row['User']['nb_email'] = '0';
			$row['User']['during_email'] = '0';
			$connexion_second  = 0;

			$row['User']['user_ip'] = '-';
			$row['User']['date_last_order'] = '-';
			$row['User']['deja achete'] = 'non';
			$row['User']['deja consomme'] = 'non';

			if ($this->request->params['controller'] == 'agents'){
				$row['User']['specialite_astrologue'] = 'non';
				$row['User']['specialite_cartomancien'] = 'non';
				$row['User']['specialite_voyant'] = 'non';
				$row['User']['specialite_numerologue'] = 'non';
				$row['User']['specialite_tarologue'] = 'non';
				$row['User']['specialite_magnetiseur'] = 'non';
				$row['User']['specialite_coaching'] = 'non';
				$row['User']['specialite_reves'] = 'non';
				$row['User']['specialite_medium'] = 'non';
				$this->loadModel('CategoryUser');
				$all_cats = $this->CategoryUser->find('all',array(
					'conditions' => array('CategoryUser.user_id' => $row['User']['id']),
					'recursive' => -1,
				));
				foreach($all_cats as $catuser){
					if($catuser['CategoryUser']['category_id'] == 2)$row['User']['specialite_astrologue'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 3)$row['User']['specialite_cartomancien'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 5)$row['User']['specialite_voyant'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 6)$row['User']['specialite_numerologue'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 7)$row['User']['specialite_tarologue'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 20)$row['User']['specialite_magnetiseur'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 25)$row['User']['specialite_coaching'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 26)$row['User']['specialite_reves'] = 'oui';
					if($catuser['CategoryUser']['category_id'] == 27)$row['User']['specialite_medium'] = 'oui';
				}

			}





			$my_result = $mysqli_connect->query("SELECT TIME_TO_SEC(TIMEDIFF(date_connexion,date_lastactivity)) as time,date_connexion,date_lastactivity from user_connexion where user_id = '{$row['User']['id']}'");
			while($row_connec = $my_result->fetch_array(MYSQLI_ASSOC)){

				//var_dump( $row_connec['date_connexion'].' -> '.$row_connec['date_lastactivity'] .' : '.$row_connec['time']);
				$connexion_second  += $row_connec['time'];
			}
			if($connexion_second){
				 $dtF = new DateTime("@0");
				 $dtT = new DateTime("@$connexion_second");
				$row['User']['time_connexion'] =  $dtF->diff($dtT)->format('%a jours, %h heures, %i minutes and %s secondes');
			}
			 $this->loadModel('UserCreditHistory');

			 $champid = 'agent_id';
			 if($row['User']['role'] == 'client')
			 $champid = 'user_id';

			$row['User']['nb_phone'] = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'phone'),
                'recursive' => -1,
            ));
			$list_phone = $this->UserCreditHistory->find('all',array(
                'fields' => array('UserCreditHistory.seconds'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'phone'),
                'recursive' => -1,
            ));
			foreach($list_phone as $lst){
				$row['User']['during_phone'] += $lst['UserCreditHistory']['seconds'];
			}

			$row['User']['nb_email'] = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'email'),
                'recursive' => -1,
            ));
			$list_email = $this->UserCreditHistory->find('all',array(
                'fields' => array('UserCreditHistory.credits'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'email'),
                'recursive' => -1,
            ));
			$row['User']['during_email'] = 0;
			foreach($list_email as $lst){
				$row['User']['during_email'] += $lst['UserCreditHistory']['credits'];
			}
			$row['User']['nb_chat'] = $this->UserCreditHistory->find('count',array(
                'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'chat'),
                'recursive' => -1,
            ));
			$list_chat = $this->UserCreditHistory->find('all',array(
                'fields' => array('UserCreditHistory.seconds'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'chat'),
                'recursive' => -1,
            ));
			foreach($list_chat as $lst){
				$row['User']['during_chat'] += $lst['UserCreditHistory']['seconds'];
			}

			$list_email = $this->UserCreditHistory->find('all',array(
                'fields' => array('UserCreditHistory.credits'),
                'conditions' => array('UserCreditHistory.'.$champid => $row['User']['id'], 'media' => 'email'),
                'recursive' => -1,
            ));

			$this->loadModel('SocietyType');
			$list_types = $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang'));

			$date1appel = $this->UserCreditHistory->find('first', array(
						'conditions' => array('user_id' => $row['User']['id']),
						'recursive' => -1,
						'order' => 'date_start asc'
					));
			$row['User']['date_1_appel'] = $date1appel['UserCreditHistory']['date_start'];
			$this->loadModel('UserIp');
			$user_ips = $this->UserIp->find('all', array(
						'fields' => array('IP'),
						'conditions' => array('user_id' => $row['User']['id']),
						'recursive' => -1,
					));

			foreach($user_ips as $userip){
				$row['User']['user_ip'] .= str_replace('.','\.',$userip['UserIp']['IP']).'|';
			}

			$this->loadModel('Order');
			$user_last_order = $this->Order->find('first', array(
						'fields' => array('date_add'),
						'conditions' => array('user_id' => $row['User']['id'], 'valid'=>1),
						'order' => array('date_add DESC'),
						'recursive' => -1,
					));
			$row['User']['date_last_order'] = $user_last_order['Order']['date_add'];

			if(!$user_last_order['Order']['date_add']){
				$row['User']['deja achete'] =  'non';
			}else{
				$row['User']['deja achete'] =  'oui';
			}

			if(!$date1appel['UserCreditHistory']['date_start']){
				$row['User']['deja consomme'] =  'non';
			}else{
				$row['User']['deja consomme'] =  'oui';
			}

			$row['User']['society_type_id'] =  $list_types[$row['User']['society_type_id']];

			$this->loadModel('InvoiceVat');
			$vat = $this->InvoiceVat->find('first', array(
					'fields'     => array('InvoiceVat.*','Country.name','Society.name'),
						'conditions' => array('InvoiceVat.id' => $row['User']['invoice_vat_id']),
						 'joins' => array(
									array('table' => 'user_country_langs',
										'alias' => 'Country',
										'type' => 'left',
										'conditions' => array(
											'Country.user_countries_id = InvoiceVat.country_id',
											'Country.lang_id = 1',
										)
									),
									 array('table' => 'society_types',
										'alias' => 'Society',
										'type' => 'left',
										'conditions' => array(
											'Society.id = InvoiceVat.society_type_id',
										)
									)
							 ),
						'order' => array('InvoiceVat.id DESC'),
						'recursive' => -1,
					));

			if($vat){
				$row['User']['invoice_vat_id'] =  $vat['Country']['name'].' '.$vat['Society']['name'];
				$row['User']['save_bank_card'] =  $vat['InvoiceVat']['rate'];
			}else{
				$row['User']['invoice_vat_id'] =  '';
				$row['User']['save_bank_card'] =  '';
			}

            fputcsv($this->_fp, $row['User'], ';' ,'"');

        }
		$mysqli_connect->close();
        fclose($this->_fp);
        $this->response->file($filename, array('download' => true, 'name' => basename($filename)));
    }

    public function admin_exportcsvaudiotel()
    {
		set_time_limit ( 0 );
		$dbb_patch = new DATABASE_CONFIG();
		$dbb_connect = $dbb_patch->default;

		$mysqli = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);



		//Les données à sortir
		$this->loadModel('UserCreditHistory');
		$conditions = array();

		//On récupère les datas pour la vue
		if($this->Session->check('Date')){
            $conditions = array(
                'UserCreditHistory.date_start >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                'UserCreditHistory.date_start <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
            );
        }



        $allComDatas = $this->UserCreditHistory->find('all', array(
            'fields'        => array('UserCreditHistory.*', 'User.id', 'User.credit', 'User.firstname', 'User.lastname', 'Agent.id',
                'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number',
            'Agent.lastname AS agent_lastname', 'Agent.email'),
            'conditions'    => $conditions,
            'order'         => 'UserCreditHistory.date_start DESC'
        ));


        $this->autoRender = false;
        $filename = Configure::read('Site.pathExport').'/all_'.$this->request->params['controller'].'.csv';
        $this->_fp = fopen($filename, 'w+');
        fwrite($this->_fp, "\xEF\xBB\xBF");

		$clientAudiotel = array();
		foreach($allComDatas as $indice => $row){

			if($row['UserCreditHistory']['user_id'] == '286' || $row['UserCreditHistory']['user_id'] == '3630' || $row['UserCreditHistory']['user_id'] == '3631' || $row['UserCreditHistory']['user_id'] == '3632' || $row['UserCreditHistory']['user_id'] == '3633' || $row['UserCreditHistory']['user_id'] == '3634' || $row['UserCreditHistory']['user_id'] == '3635' || $row['UserCreditHistory']['user_id'] == '3636' || $row['UserCreditHistory']['user_id'] == '3637' || $row['UserCreditHistory']['user_id'] == '3638'){

				$result = $mysqli->query("SELECT * from call_infos where sessionid = '{$row['UserCreditHistory']['sessionid']}'");
				$row3 = $result->fetch_array(MYSQLI_ASSOC);
				if($row3['callerid']){
					$caller = $row3['callerid'];
					$caller_line = $row3['line'];
					$id = 'AT'.substr($caller, -6);
					$id_public = 'AUDIO'.(substr($row3['callerid'], -4)*15);

					$called = '';
					switch ($row['UserCreditHistory']['called_number']) {
						case 901801885:
							$called = 'suisse audiotel';
							break;
						case 41225183456:
							$called = 'suisse prepaye';
							break;
						case 90755456:
							$called = 'Belgique audiotel';
							break;
						case 3235553456:
						case 3225885252:
							$called = 'Belgique prepaye';
							break;
						case 90128222:
							$called = 'Luxembourg audiotel';
							break;
						case 27864456:
							$called = 'Luxembourg prepaye';
							break;
						case 4466:
            case 9010:
							$called = 'Canada audiotel mobile';
							break;
						case 19007884466:
            case 19005289010:
							$called = 'Canada audiotel fixe';
							break;
						case 18442514456:
							$called = 'Canada prepaye';
							break;
						case 33970736456:
							$called = 'France prepaye';
							break;
					}



					if(!is_array($clientAudiotel[$id])){
						$clientAudiotel[$id] = array();
						$clientAudiotel[$id]['id_public'] = 0;
						$clientAudiotel[$id]['nb_appel'] = 0;
						$clientAudiotel[$id]['duree_appel'] = 0;
						$clientAudiotel[$id]['date_1_appel'] = '';
						$clientAudiotel[$id]['num'] = '';
						$clientAudiotel[$id]['line'] = '';
						$clientAudiotel[$id]['called'] = '';
					}

					if(!$clientAudiotel[$id]['date_1_appel'])$clientAudiotel[$id]['date_1_appel'] = $row['UserCreditHistory']['date_start'];
					if(!$clientAudiotel[$id]['num'])$clientAudiotel[$id]['num'] = $caller;
					if(!$clientAudiotel[$id]['line'])$clientAudiotel[$id]['line'] = $caller_line;
					if(!$clientAudiotel[$id]['called'])$clientAudiotel[$id]['called'] = $called;
					$clientAudiotel[$id]['nb_appel'] ++;
					$clientAudiotel[$id]['duree_appel'] += $row['UserCreditHistory']['seconds'];
					$clientAudiotel[$id]['id_public'] = $id_public;
				}
			}
		}
		$exportAudiotel = array();
			$exportAudiotel['code'] = 'code';
			$exportAudiotel['code_public'] = 'code public';
			$exportAudiotel['num'] = 'num';
			$exportAudiotel['line'] = 'line';
			$exportAudiotel['called'] = 'called';
			$exportAudiotel['date_1_appel'] = 'date_1_appel';
			$exportAudiotel['nb_appel'] = 'nb_appel';
			$exportAudiotel['duree_appel'] = 'duree_appel';
		fputcsv($this->_fp, $exportAudiotel, ';' ,'"');

		foreach($clientAudiotel as $id => $client){
			$exportAudiotel = array();
			$exportAudiotel['code'] = $id;
			$exportAudiotel['code_public'] = $client['id_public'];
			$exportAudiotel['num'] = $client['num'];
			$exportAudiotel['line'] = $client['line'];
			$exportAudiotel['called'] = $client['called'];
			$exportAudiotel['date_1_appel'] = $client['date_1_appel'];
			$exportAudiotel['nb_appel'] = $client['nb_appel'];
			$exportAudiotel['duree_appel'] = $client['duree_appel'];
			 fputcsv($this->_fp, $exportAudiotel, ';' ,'"');
		}
		$mysqli->close();
        fclose($this->_fp);
        $this->response->file($filename, array('download' => true, 'name' => basename($filename)));
    }


    public function _adminNote($id, $model){
        $this->User->id = $id;
        $role = $this->User->field('role');

        if($role !== false && in_array($role, array('client', 'agent'))){
            if($this->request->is('post')){
                $this->loadModel('AdminNote');
                //On supprime la note actuelle
                $this->AdminNote->deleteAll(array('user_id' => $id),false);
                //On save la nouvelle note
                $this->AdminNote->save(array('user_id' => $id, 'note' => $this->request->data[$model]['note']));
                $this->Session->setFlash(__('Commentaire sauvegardé.'),'flash_success');
            }
        }
        $this->redirect(array('controller' => ($model === 'Account' ?'accounts':'agents'), 'action' => 'view', 'admin' => true, 'id' => $id),false);
    }

    /**
     * Active/Désactive l'enregistrement téléphonique d'un agent
     *
     * @param $id   int L'id de l'agent
     * @param $etat int L'état voulu (1 = activation, 0 = désactivation)
     */
    public function _adminChangeRecord($id, $etat, $redirect = true){
        //On récupère les données de l'agent
        $agent = $this->User->find('first',array(
            'fields' => array('agent_number'),
            'conditions' => array('role' => 'agent', 'active' => 1, 'deleted' => 0, 'id' => $id),
            'recursive' => -1
        ));

        if(empty($agent)){
            $this->Session->setFlash(__('Aucun agent trouvé.'),'flash_warning');
			if($redirect)
            $this->redirect(array('controller' => 'agents', 'action' => 'index', 'admin' => true),false);
        }else{
            $api = new Api();
            if($etat === 1)
                $result = $api->startRecordingAgent($agent['User']['agent_number']);
            else
                $result = $api->stopRecordingAgent($agent['User']['agent_number']);

            //Si tout est ok
            if(isset($result['response_code']) && $result['response_code'] == 0){
                //Enregistre la demande
                $this->User->id = $id;
                $this->User->saveField('record', $etat);
                $this->Session->setFlash(__(($etat === 1 ?'Enregistrement activé.':'Enregistrement désactivé.')),'flash_success');
            }else{
                $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message'])
                        ?$result['response_message']
                        :($etat === 1 ?'Echec lors de l\'activation de l\'enregistrement.':'Echec lors de la désactivation de l\'enregistrement.')
                    )),'flash_warning');
            }
			if($redirect)
            $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
        }
    }

    public function _adminCom($groupField){
        $this->loadModel('UserCreditHistory');

		/*
		//Création de la sous-requête
        $db = $this->UserCreditHistory->getDataSource();

        //Initialisation des paramètres
        $subQuery = $db->buildStatement(
            array(
                'fields'     => array('MAX(UserCreditHistory2.user_credit_history)'),
                'table'      => $db->fullTableName($this->UserCreditHistory),
                'alias'      => 'UserCreditHistory2',
                'limit'      => null,
                'offset'     => null,
                'joins'      => array(),
                'conditions' => array(),
                'order'      => null,
                'group'      => 'UserCreditHistory2.'.$groupField
            ),
            $this->UserCreditHistory
        );
		$subQuery_tot = $db->buildStatement(
            array(
                'fields'     => array('MAX(UserCreditHistory2.user_credit_history)'),
                'table'      => $db->fullTableName($this->UserCreditHistory),
                'alias'      => 'UserCreditHistory2',
                'limit'      => null,
                'offset'     => null,
                'joins'      => array(),
                'conditions' => array(),
                'order'      => null,
                'group'      => 'UserCreditHistory2.user_credit_history'
            ),
            $this->UserCreditHistory
        );*/
        //On complète la sous-requete avec le champ de la requete principale
        $subQuery = '1=1 ';//'UserCreditHistory.user_credit_history IN (' . $subQuery . ')';
		$subQuery_tot = '1=1 ';//'UserCreditHistory.user_credit_history IN (' . $subQuery_tot . ')';
        //Avons-nous une recharche par date ??
        if($this->Session->check('Date')){
            $subQuery.= ' AND UserCreditHistory.date_start >= "'.CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00').'"';
            $subQuery.= ' AND UserCreditHistory.date_start <= "'.CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59').'"';
			$subQuery_tot.= ' AND UserCreditHistory.date_start >= "'.CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00').'"';
            $subQuery_tot.= ' AND UserCreditHistory.date_start <= "'.CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59').'"';
        }else{
			$subQuery.= ' AND UserCreditHistory.date_start >= "'.date('Y-m-d 00:00:00').'"';
            $subQuery.= ' AND UserCreditHistory.date_start <= "'.date('Y-m-d 23:59:59').'"';
			$subQuery_tot.= ' AND UserCreditHistory.date_start >= "'.date('Y-m-d 00:00:00').'"';
            $subQuery_tot.= ' AND UserCreditHistory.date_start <= "'.date('Y-m-d 23:59:59').'"';
		}
        //Avons-nous une recharche par media ??
        if($this->Session->check('Media')){
            $subQuery.= ' AND UserCreditHistory.media = "'.$this->Session->read('Media.value').'"';
			$subQuery_tot.= ' AND UserCreditHistory.media = "'.$this->Session->read('Media.value').'"';
        }

        //$subQueryExpression = $db->expression($subQuery);
        //Retourne un object avec l'expression complète en sql de la sous-requete
        $conditions[] = $subQuery;//$subQueryExpression;

        $this->Paginator->settings = array(
            'fields' => array('UserCreditHistory.*', 'User.firstname', 'User.lastname'),
            'conditions' => $conditions,
            'order' => 'UserCreditHistory.date_start desc',
            'paramType' => 'querystring',
            'limit' => 15
        );

        $lastCom = $this->Paginator->paginate($this->UserCreditHistory);


		if(!$this->Session->check('Media') || $this->Session->check('Media') == 'phone'){
			 if(!$this->Session->check('Media')){
				 $subQuery2= $subQuery_tot.' AND UserCreditHistory.media = "phone"';
				// $subQueryExpression2 = $db->expression($subQuery2);
				 $condition2 = array();
				 $conditions2[] = $subQuery2;//$subQueryExpression2;
			 }
			$nbComPhone = $this->UserCreditHistory->find('count',array(
					'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
					'conditions' => $conditions2,
				 'paramType' => 'querystring',
					'recursive' => -1,
				));
				$nbMinComPhone = $this->UserCreditHistory->find('all',array(
					'fields' => array('SUM(UserCreditHistory.seconds) as total'),
					'conditions' => $conditions2,
					 'paramType' => 'querystring',
					'recursive' => -1,
				));
				$nbMinComPhone = $nbMinComPhone[0][0]['total'];
		}
		if(!$this->Session->check('Media') || $this->Session->check('Media') == 'email'){
			 if(!$this->Session->check('Media')){
				$subQuery3= $subQuery_tot.' AND UserCreditHistory.media = "email"';
				// $subQueryExpression3 = $db->expression($subQuery3);
				 $condition3 = array();
				 $conditions3[] = $subQuery3;//$subQueryExpression3;
			 }
				$nbComMail = $this->UserCreditHistory->find('count',array(
					'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
					'conditions' => $conditions3,
					 'paramType' => 'querystring',
					'recursive' => -1,
				));
				$nbMinComMail = 0;
		}
		if(!$this->Session->check('Media') || $this->Session->check('Media') == 'chat'){
			 if(!$this->Session->check('Media')){
				$subQuery4= $subQuery_tot.' AND UserCreditHistory.media = "chat"';
				 //$subQueryExpression4 = $db->expression($subQuery4);
				 $condition4 = array();
				 $conditions4[] = $subQuery4;//$subQueryExpression4;
			 }
				$nbComTchat = $this->UserCreditHistory->find('count',array(
					'fields' => array('UserCreditHistory.user_credit_history','UserCreditHistory.user_id', 'UserCreditHistory.phone_number', 'UserCreditHistory.seconds', 'UserCreditHistory.date_start', 'UserCreditHistory.media','UserCreditHistory.is_factured','UserCreditHistory.text_factured','UserCreditHistory.called_number', 'User.firstname', 'User.lastname'),
					'conditions' => $conditions4,
					 'paramType' => 'querystring',
					'recursive' => -1,
				));

				$nbMinComTchat = $this->UserCreditHistory->find('all',array(
					'fields' => array('SUM(UserCreditHistory.seconds) as total'),
					'conditions' => $conditions4,
					 'paramType' => 'querystring',
					'recursive' => -1,
				));
				$nbMinComTchat = $nbMinComTchat[0][0]['total'];
		}

        $this->set(compact('lastCom','nbComPhone', 'nbComTchat', 'nbComMail','nbMinComPhone', 'nbMinComTchat', 'nbMinComMail'));
    }

    public function _adminExportCom($controller){
		set_time_limit ( 0 );
		ini_set("memory_limit",-1);

		 //Charge model
        $this->loadModel('ExportCom');

		 //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

		 $conditions = array();

		 if($this->Session->check('Date')){

			$listing_utcdec = Configure::read('Site.utcDec');

			$delai =CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$delai_max =CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$dx1 = new DateTime($delai);
			$dx2 = new DateTime($delai_max);
			$dx1->modify('-'.$listing_utcdec[$dx1->format('md')].' hour');
			$dx2->modify('-'.$listing_utcdec[$dx2->format('md')].' hour');
			$delai = $dx1->format('Y-m-d H:i:s');
			$delai_max = $dx2->format('Y-m-d H:i:s');

            $conditions = array_merge($conditions, array(
                'ExportCom.date_start >=' => $delai,
                'ExportCom.date_start <=' => $delai_max
            ));
        }

		//Filtre media ??
        if($this->Session->check('Media'))
            $conditions = array_merge($conditions, array('ExportCom.media' => $this->Session->read('Media.value')));

        //Les données de toute le monde ou celles d'un user
		if(isset($this->params->query['user'])){
            $idUser = $this->params->query['user'];
            //On récupère le role de l'user
            $role = $this->User->field('role', array('User.id' => $this->params->query['user']));

            if($role === 'client'){
                $conditions = array_merge($conditions, array('ExportCom.user_id' => $idUser));
			}elseif($role === 'agent'){
                $conditions = array_merge($conditions, array('ExportCom.agent_id' => $idUser));
			}
        }

		$new_list = array();
        $allComDatas = $this->ExportCom->find('all', array(
            'conditions'    => $conditions,
            'order'         => 'ExportCom.date_start DESC'
        ));

		 //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => $controller, 'action' => 'com', 'admin' => true), false);
            else
                $this->redirect($source);
        }

		//Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_export';

        //Si media
        if($this->Session->check('Media'))
            $label.= '_by_'.$this->Session->read('Media.value');


        $fp = fopen($filename, 'w+');
        #fputs($fp, "\xEF\xBB\xBF");

		foreach($allComDatas as $indice => $row){
			$line = array(
                'agent_number'      => $row['ExportCom']['agent_number'],
                'agent_pseudo'      => utf8_decode($row['ExportCom']['agent_pseudo']),
                'agent_firstname'   => utf8_decode($row['ExportCom']['agent_firstname']),
                'agent_lastname'    => utf8_decode($row['ExportCom']['agent_lastname']),
                'agent_email'       => $row['ExportCom']['email'],
                'user_code'         => $row['ExportCom']['user_code'],
                'user_firstname'    => utf8_decode($row['ExportCom']['user_firstname']),
                'user_lastname'     => utf8_decode($row['ExportCom']['user_lastname']),
				'user_domain'       => $row['ExportCom']['user_domain'],
				'user_date_add'     => Tools::dateUser('Europe/Paris', $row['ExportCom']['user_date_add']),
				'1er appel'		=> $row['ExportCom']['first_call'],
				utf8_decode('Concordance client Prépayé audiotel')	=> $row['ExportCom']['concordance'],
				'date 1ere consult'		=> $row['ExportCom']['date_first_consult'],
				utf8_decode('délai 1ere consult depuis inscription ( minutes )')		=> $row['ExportCom']['delay_before_first_consult'],
				'X appel/inscription'=> $row['ExportCom']['nb_calls'],
				utf8_decode('X appel/journée')   => $row['ExportCom']['nb_calls_day'],
                'user_credit_now'       => $row['ExportCom']['user_credit_now'],
                'media'             => $row['ExportCom']['media'],
                'credits'           => $row['ExportCom']['credits'],
                'seconds'           => $row['ExportCom']['seconds'],
				'minutes'           => $row['ExportCom']['minutes'],
                'called_number'     => $row['ExportCom']['called_number'],
				'called'            => $row['ExportCom']['called'],
                'phone_agent'       => $row['ExportCom']['phone_agent'],
				'operator'       	=> $row['ExportCom']['phone_operator'],
				'caller'            => $row['ExportCom']['caller'],
				'caller_line'       => $row['ExportCom']['caller_line'],
				'sessionid'         => $row['ExportCom']['sessionid'],
			    'time_start'        => $row['ExportCom']['time_start'],
                'date_start'        => Tools::dateUser('Europe/Paris', $row['ExportCom']['date_start']),
                'date_end'          => Tools::dateUser('Europe/Paris', $row['ExportCom']['date_end']),
				'taux / minute'     => $row['ExportCom']['tx_minute'],
				'taux / seconde'    => $row['ExportCom']['tx_second'],
				'remuneration'      => $row['ExportCom']['price'],
				'ca_euro'      		=> $row['ExportCom']['ca_euro'],
				'ca_chf'      		=> $row['ExportCom']['ca_chf'],
				'ca_dollar'      	=> $row['ExportCom']['ca_dollar'],
				'total_secondes_mois'  => $row['ExportCom']['total_seconds_month'],
				'total_secondes'       => $row['ExportCom']['total_seconds'],

            );

			if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
           fputcsv($fp, array_values($line), ';', '"');
		}

		fclose($fp);
        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;

    }
    public function _adminExportComTranche($controller){

		set_time_limit ( 0 );
		ini_set("memory_limit",-1);
        //Charge model
        $this->loadModel('UserCreditHistory');
		$this->loadModel('Order');
		$this->loadModel('CallInfo');
        //Le nom du fichier temporaire
        $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);

        $conditions = array();
		$conditions2 = array();


        //Filtre par date ??
        if($this->Session->check('Date')){

			/*$utc_dec = 1;//Configure::read('Site.utc_dec');
			$cut = explode('-',$this->Session->read('Date.start') );
			$mois_comp = $cut[1];
			if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
				$utc_dec = 2;*/
			$listing_utcdec = Configure::read('Site.utcDec');

			$delai = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$delai_max =CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
			$dx1 = new DateTime($delai);
			$dx2 = new DateTime($delai_max);
			$dx1->modify('-'.$listing_utcdec[$dx1->format('md')].' hour');
			$dx2->modify('-'.$listing_utcdec[$dx2->format('md')].' hour');
			$delai = $dx1->format('Y-m-d H:i:s');
			$delai_max = $dx2->format('Y-m-d H:i:s');


            $conditions = array_merge($conditions, array(
                'UserCreditHistory.date_start >=' => $delai,
                'UserCreditHistory.date_start <=' => $delai_max
            ));
			$conditions2 = array_merge($conditions2, array(
                'Order.date_add >=' => $delai,
                'Order.date_add <=' => $delai_max
            ));
			$date_min = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$date_max = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
        }else{
			$date_min = '2015-06-09 00:00:01';
			$date_max = date('Y-m-d H:i:s');
		}

        //Filtre media ??
        if($this->Session->check('Media'))
            $conditions = array_merge($conditions, array('UserCreditHistory.media' => $this->Session->read('Media.value')));

        //Les données de toute le monde ou celles d'un user
        if(isset($this->params->query['user'])){
            $idUser = $this->params->query['user'];
            //On récupère le role de l'user
            $role = $this->User->field('role', array('User.id' => $this->params->query['user']));

            if($role === 'client')
                $conditions = array_merge($conditions, array('UserCreditHistory.user_id' => $idUser));
            elseif($role === 'agent')
                $conditions = array_merge($conditions, array('UserCreditHistory.agent_id' => $idUser));
        }

		$type_export = 'par_date';
		 if($this->Session->check('type_export'))
            $type_export = $this->Session->read('type_export.value');

		if($type_export == 'par_date'){

			function date_range($first, $last, $step = '+1 hour', $output_format = 'Y-m-d-H' ) {

				$dates = array();
				$current = strtotime($first);
				$last = strtotime($last);

				while( $current <= $last ) {

					$dates[] = date($output_format, $current);
					$current = strtotime($step, $current);
				}

				return $dates;
			}

			$calendar = date_range($date_min,$date_max);
		}else{
			$calendar = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
		}
			//creation data vierge
		$export_data = array();
		foreach($calendar as $date){

				$obj = new StdClass();
				$obj->TT_TEL_PREPAYE_France = 0;
				$obj->TT_TEL_PREPAYE_Belgique = 0;
				$obj->TT_TEL_AUDIOTEL_Belgique = 0;
				$obj->TT_TEL_PREPAYE_Suisse = 0;
				$obj->TT_TEL_AUDIOTEL_Suisse = 0;
				$obj->TT_TEL_PREPAYE_Luxembourg = 0;
				$obj->TT_TEL_AUDIOTEL_Luxembourg = 0;
				$obj->TT_TEL_PREPAYE_Canada = 0;
				$obj->TT_TEL_AUDIOTEL_Canada = 0;
				$obj->TT_Email_France = 0;
				$obj->TT_Email_Belgique = 0;
				$obj->TT_Email_Suisse = 0;
				$obj->TT_Email_Luxembourg = 0;
				$obj->TT_Email_Canada = 0;
				$obj->TT_Tchat_France = 0;
				$obj->TT_Tchat_Belgique = 0;
				$obj->TT_Tchat_Suisse = 0;
				$obj->TT_Tchat_Luxembourg = 0;
				$obj->TT_Tchat_Canada = 0;
				$obj->TT_TEL = 0;
				$obj->TT_Email = 0;
				$obj->TT_Tchat = 0;
				$obj->TT_Consult = 0;
				$obj->ANCIENS_CLIENTS_France = 0;
				$obj->ANCIENS_CLIENTS_France_unique = 0;
				$obj->NVX_CLIENTS_France = 0;
				$obj->NVX_CLIENTS_France_phone_prepaye = 0;
				$obj->NVX_CLIENTS_France_email = 0;
				$obj->NVX_CLIENTS_France_tchat = 0;
				$obj->ANCIENS_CLIENTS_Belgique = 0;
				$obj->ANCIENS_CLIENTS_Belgique_unique = 0;
				$obj->NVX_CLIENTS_Belgique = 0;
				$obj->NVX_CLIENTS_Belgique_phone = 0;
				$obj->NVX_CLIENTS_Belgique_phone_prepaye = 0;
				$obj->NVX_CLIENTS_Belgique_phone_audiotel = 0;
				$obj->NVX_CLIENTS_Belgique_email = 0;
				$obj->NVX_CLIENTS_Belgique_tchat = 0;
				$obj->ANCIENS_CLIENTS_Suisse = 0;
				$obj->ANCIENS_CLIENTS_Suisse_unique = 0;
				$obj->NVX_CLIENTS_Suisse = 0;
				$obj->NVX_CLIENTS_Suisse_phone = 0;
				$obj->NVX_CLIENTS_Suisse_phone_prepaye = 0;
				$obj->NVX_CLIENTS_Suisse_phone_audiotel = 0;
				$obj->NVX_CLIENTS_Suisse_email = 0;
				$obj->NVX_CLIENTS_Suisse_tchat = 0;
				$obj->ANCIENS_CLIENTS_Luxembourg = 0;
				$obj->ANCIENS_CLIENTS_Luxembourg_unique = 0;
				$obj->NVX_CLIENTS_Luxembourg = 0;
			    $obj->NVX_CLIENTS_Luxembourg_phone = 0;
				$obj->NVX_CLIENTS_Luxembourg_phone_prepaye = 0;
				$obj->NVX_CLIENTS_Luxembourg_phone_audiotel = 0;
			    $obj->NVX_CLIENTS_Luxembourg_email = 0;
			    $obj->NVX_CLIENTS_Luxembourg_tchat = 0;
				$obj->ANCIENS_CLIENTS_Canada = 0;
				$obj->ANCIENS_CLIENTS_Canada_unique = 0;
				$obj->NVX_CLIENTS_Canada = 0;
				$obj->NVX_CLIENTS_Canada_phone = 0;
				$obj->NVX_CLIENTS_Canada_phone_prepaye = 0;
				$obj->NVX_CLIENTS_Canada_phone_audiotel = 0;
				$obj->NVX_CLIENTS_Canada_email = 0;
				$obj->NVX_CLIENTS_Canada_tchat = 0;
				$obj->NBRE_TT_CLIENTS_TOUS_PAYS = 0;
				$obj->NBRE_TT_CLIENTS_TOUS_PAYS_phone = 0;
				$obj->NBRE_TT_CLIENTS_TOUS_PAYS_email = 0;
				$obj->NBRE_TT_CLIENTS_TOUS_PAYS_tchat = 0;
				$obj->TT_ANCIENS_CLIENTS_TOUS_PAYS = 0;
				$obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique = 0;
				$obj->TT_NVX_CLIENTS_TOUS_PAYS = 0;
			    $obj->TT_NVX_CLIENTS_TOUS_PAYS_phone = 0;
				$obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye = 0;
				$obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel = 0;
			    $obj->TT_NVX_CLIENTS_TOUS_PAYS_email = 0;
			    $obj->TT_NVX_CLIENTS_TOUS_PAYS_tchat = 0;
				$obj->Total_secondes_depensees = 0;
				$obj->Total_credits_achetes = 0;
				$obj->Total_transac_tous_pays = 0;
				$obj->Total_credits_achetes_france = 0;
				$obj->Total_transac_france = 0;
				$obj->Total_credits_achetes_belgique = 0;
				$obj->Total_transac_belgique = 0;
				$obj->Total_credits_achetes_suisse = 0;
				$obj->Total_transac_suisse = 0;
				$obj->Total_credits_achetes_luxembourg = 0;
				$obj->Total_transac_luxembourg = 0;
				$obj->Total_credits_achetes_canada = 0;
				$obj->Total_transac_canada = 0;
				$export_data[$date] = $obj;
		}


        //Les données à sortir
        $allComDatas = $this->UserCreditHistory->find('all', array(
            'fields'        => array('UserCreditHistory.*', 'User.*', 'Agent.id',
                'User.personal_code','User.domain_id','User.date_add', 'Agent.agent_number', 'Agent.pseudo', 'Agent.firstname AS agent_firstname', 'Agent.phone_number',
            'Agent.lastname AS agent_lastname', 'Agent.email'),
            'conditions'    => $conditions,
			/*'joins' => array(
				array(
                    'table' => 'call_infos',
                    'alias' => 'CallInfos',
                    'type'  => 'left',
                    'conditions' => array(
                        'CallInfos.sessionid = UserCreditHistory.sessionid',
                    )
                ),
            ),*/
            'order'         => 'UserCreditHistory.date_start DESC'
        ));
		$conditions2 = array_merge($conditions2, array(
                'Order.valid =' => 1,
            ));
		$allOrderDatas = $this->Order->find('all', array(
            'fields'        => array('Order.*'),
            'conditions'    => $conditions2,
            'order'         => 'Order.date_add DESC'
        ));

        //Si pas de données
        if(empty($allComDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => $controller, 'action' => 'com', 'admin' => true), false);
            else
                $this->redirect($source);
        }

        //Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_export';

        //Si media
        if($this->Session->check('Media'))
            $label.= '_by_'.$this->Session->read('Media.value');


        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");

		//$dbb_r = new DATABASE_CONFIG();
		//$dbb_r = $dbb_r->default;
		//$mysqli = new mysqli($dbb_r['host'], $dbb_r['login'], $dbb_r['password'], $dbb_r['database']);

       $export_unique_client = array();
		$export_unique_client_total = array();
        foreach($allComDatas as $indice => $row){

			//recup data call info
			if($row['UserCreditHistory']['sessionid']){
				 $callinfo = $this->CallInfo->find('first', array(
					'fields'        => array('CallInfo.callerid','CallInfo.line'),
					'conditions'    => array('CallInfo.sessionid' => $row['UserCreditHistory']['sessionid']),

				));
			}else{
				$callinfo = array();
			}

			//recup le pays
			$code_iso = '';
			$called = '';
			if($row['UserCreditHistory']['media'] != 'phone'){
				switch ($row['User']['domain_id']) {
					case 19:
						$code_iso = 'France';
						break;
					case 11:
						$code_iso = 'Belgique';
						break;
					case 13:
						$code_iso = 'Suisse';
						break;
					case 22:
						$code_iso = 'Luxembourg';
						break;
					case 29:
						$code_iso = 'Canada';
						break;
					default :
						$code_iso = 'France';
						break;
				}

				$date_1_appel = $this->UserCreditHistory->find('first', array(
							'conditions' => array('user_id' => $row['UserCreditHistory']['user_id']),
							'recursive' => -1,
							'order' => 'date_start asc',
						));
				$date_1_appel_old = '';

			}else{
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
          case 9010:
						$called = 'Canada audiotel';
						$code_iso = 'Canada';
						break;
					case 19007884466:
          case 19005289010:
						$called = 'Canada audiotel';
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
					default :
						$called = 'France prepaye';
						$code_iso = 'France';
						break;
				}

				switch ($row['UserCreditHistory']['domain_id']) {
					case 19:
						$code_iso = 'France';
						break;
					case 11:
						$code_iso = 'Belgique';
						break;
					case 13:
						$code_iso = 'Suisse';
						break;
					case 22:
						$code_iso = 'Luxembourg';
						break;
					case 29:
						$code_iso = 'Canada';
						break;
					default :
						$code_iso = 'France';
						break;
				}

				switch ($row['UserCreditHistory']['type_pay']) {
					case 'aud':
						$called = $code_iso.' audiotel';
						break;
					case 'pre':
						$called = $code_iso.' prepaye';
						break;
				}

				$caller = $callinfo['CallInfo']['callerid'];
				$caller_line = $callinfo['CallInfo']['line'];


				if($caller == 'UNKNOWN')$caller = '';
				$date_1_appel_old = '';	$nb_appels = '';$nb_appels_today = ''; $date_1_appel = '';

				if(!substr_count($row['User']['firstname'] , 'AUDIOTEL')){
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

						$date_1_appel = $this->UserCreditHistory->find('first', array(
							'conditions' => array('phone_number' => $caller),
							'recursive' => -1,
							'order' => 'date_start asc'
						));


					}else{
						$nb_appels = '';
						$date_1_appel = '';
					}
				}
				if(substr_count($row['User']['firstname'] , 'AUDIOTEL')){
					$row['User']['firstname'] =  'AT'.substr($caller, -6);
					$row['User']['lastname'] = 'AUDIO'.(substr($caller, -4)*15);
					$row['User']['date_add'] = $date_1_appel['UserCreditHistory']['date_start'];
				}
			}


			$premier_appel = '';
			$timing = explode(' ',$row['UserCreditHistory']['date_start']);
			$heures = intval(($row['UserCreditHistory']['seconds']) / 60 / 60);
			$minutes = intval(($row['UserCreditHistory']['seconds'] % 3600) / 60);
			$secondes = intval((($row['UserCreditHistory']['seconds'] % 3600) % 60));

			if($date_1_appel_old['UserCreditHistory']['date_start']){
				if($timing[0].' '.$timing[1] == $date_1_appel_old['UserCreditHistory']['date_start']) $premier_appel = 'X';
			}else{
				if($timing[0].' '.$timing[1] == $date_1_appel['UserCreditHistory']['date_start']) $premier_appel = 'X';
			}

			if(!$row['UserCreditHistory']['is_new'])$premier_appel = '';
			if(!$row['UserCreditHistory']['is_factured'])$premier_appel = '';

			//var_dump($timing[0].' '.$timing[1] .' == '. $date_1_appel['UserCreditHistory']['date_start']. ' ==> '.$premier_appel);

			//calcul
			$tab_index = '';

			$datecalc = new DateTime($row['UserCreditHistory']['date_start']);
			$datecalc->modify('+'.$utc_dec.' hour');
			$dddcacl = $datecalc->format('Y-m-d H:i:s');


			$cut_date = explode(' ',$dddcacl);
			$cut_date1 = explode('-',$cut_date[0]);
			$cut_date2 = explode(':',$cut_date[1]);
			if($type_export == 'par_date'){
				$tab_index = $cut_date[0].'-'.$cut_date2[0];
			}else{
				$tab_index = $cut_date2[0];
			}
			if($tab_index){

				if(!is_array($export_unique_client[$tab_index]))$export_unique_client[$tab_index] = array();
				$ok_client = false;
				if(!in_array($row['UserCreditHistory']['user_id'],$export_unique_client[$tab_index])){$ok_client = true;array_push( $export_unique_client[$tab_index], $row['UserCreditHistory']['user_id']);}

				$ok_client_total = false;
				if(!in_array($row['UserCreditHistory']['user_id'],$export_unique_client_total)){$ok_client_total = true;array_push( $export_unique_client_total, $row['UserCreditHistory']['user_id']);}

				$obj = $export_data[$tab_index];
				if($code_iso == 'France' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'France prepaye')
					$obj->TT_TEL_PREPAYE_France ++;
				if($code_iso == 'Belgique' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Belgique prepaye')
					$obj->TT_TEL_PREPAYE_Belgique  ++;
				if($code_iso == 'Belgique' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Belgique audiotel')
					$obj->TT_TEL_AUDIOTEL_Belgique  ++;
				if($code_iso == 'Suisse' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Suisse prepaye')
					$obj->TT_TEL_PREPAYE_Suisse  ++;
				if($code_iso == 'Suisse' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Suisse audiotel')
					$obj->TT_TEL_AUDIOTEL_Suisse  ++;
				if($code_iso == 'Luxembourg' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Luxembourg prepaye')
					$obj->TT_TEL_PREPAYE_Luxembourg  ++;
				if($code_iso == 'Luxembourg' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Luxembourg audiotel')
					$obj->TT_TEL_AUDIOTEL_Luxembourg  ++;
				if($code_iso == 'Canada' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Canada prepaye')
					$obj->TT_TEL_PREPAYE_Canada  ++;
				if($code_iso == 'Canada' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Canada audiotel')
					$obj->TT_TEL_AUDIOTEL_Canada  ++;
				if($code_iso == 'France' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email_France  ++;
				if($code_iso == 'Belgique' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email_Belgique  ++;
				if($code_iso == 'Suisse' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email_Suisse  ++;
				if($code_iso == 'Luxembourg' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email_Luxembourg  ++;
				if($code_iso == 'Canada' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email_Canada  ++;
				if($code_iso == 'France' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat_France  ++;
				if($code_iso == 'Belgique' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat_Belgique  ++;
				if($code_iso == 'Suisse' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat_Suisse  ++;
				if($code_iso == 'Luxembourg' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat_Luxembourg  ++;
				if($code_iso == 'Canada' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat_Canada  ++;
				if($row['UserCreditHistory']['media'] == 'phone')
					$obj->TT_TEL  ++;
				if($row['UserCreditHistory']['media'] == 'email')
					$obj->TT_Email  ++;
				if($row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_Tchat  ++;

					$obj->TT_Consult  ++;
				if($code_iso == 'France' && $premier_appel == '' && $ok_client)
					$obj->ANCIENS_CLIENTS_France  ++;
				if($code_iso == 'France' && $premier_appel == '' && $ok_client_total)
					$obj->ANCIENS_CLIENTS_France_unique  ++;
				if($code_iso == 'France' && $premier_appel == 'X')
					$obj->NVX_CLIENTS_France  ++;
				if($code_iso == 'France' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'France prepaye')
					$obj->NVX_CLIENTS_France_phone_prepaye  ++;
				if($code_iso == 'France' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->NVX_CLIENTS_France_email  ++;
				if($code_iso == 'France' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->NVX_CLIENTS_France_tchat  ++;
				if($code_iso == 'Belgique' && $premier_appel == '' && $ok_client)
					$obj->ANCIENS_CLIENTS_Belgique  ++;
				if($code_iso == 'Belgique' && $premier_appel == '' && $ok_client_total)
					$obj->ANCIENS_CLIENTS_Belgique_unique  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X')
					$obj->NVX_CLIENTS_Belgique  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone')
					$obj->NVX_CLIENTS_Belgique_phone  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Belgique prepaye')
					$obj->NVX_CLIENTS_Belgique_phone_prepaye  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Belgique audiotel')
					$obj->NVX_CLIENTS_Belgique_phone_audiotel  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->NVX_CLIENTS_Belgique_email  ++;
				if($code_iso == 'Belgique' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->NVX_CLIENTS_Belgique_tchat  ++;
				if($code_iso == 'Suisse' && $premier_appel == '' && $ok_client)
					$obj->ANCIENS_CLIENTS_Suisse  ++;
				if($code_iso == 'Suisse' && $premier_appel == '' && $ok_client_total)
					$obj->ANCIENS_CLIENTS_Suisse_unique  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X')
					$obj->NVX_CLIENTS_Suisse  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone')
					$obj->NVX_CLIENTS_Suisse_phone  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Suisse prepaye')
					$obj->NVX_CLIENTS_Suisse_phone_prepaye  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Suisse audiotel')
					$obj->NVX_CLIENTS_Suisse_phone_audiotel  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->NVX_CLIENTS_Suisse_email  ++;
				if($code_iso == 'Suisse' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->NVX_CLIENTS_Suisse_tchat  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == '' && $ok_client)
					$obj->ANCIENS_CLIENTS_Luxembourg  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == '' && $ok_client_total)
					$obj->ANCIENS_CLIENTS_Luxembourg_unique  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X')
					$obj->NVX_CLIENTS_Luxembourg  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone')
					$obj->NVX_CLIENTS_Luxembourg_phone  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Luxembourg prepaye')
					$obj->NVX_CLIENTS_Luxembourg_phone_prepaye  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Luxembourg audiotel')
					$obj->NVX_CLIENTS_Luxembourg_phone_audiotel  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->NVX_CLIENTS_Luxembourg_email  ++;
				if($code_iso == 'Luxembourg' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->NVX_CLIENTS_Luxembourg_tchat  ++;
				if($code_iso == 'Canada' && $premier_appel == '' && $ok_client)
					$obj->ANCIENS_CLIENTS_Canada  ++;
				if($code_iso == 'Canada' && $premier_appel == '' && $ok_client_total)
					$obj->ANCIENS_CLIENTS_Canada_unique  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X')
					$obj->NVX_CLIENTS_Canada  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone')
					$obj->NVX_CLIENTS_Canada_phone  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Canada prepaye')
					$obj->NVX_CLIENTS_Canada_phone_prepaye  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && $called == 'Canada audiotel')
					$obj->NVX_CLIENTS_Canada_phone_audiotel  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->NVX_CLIENTS_Canada_email  ++;
				if($code_iso == 'Canada' && $premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->NVX_CLIENTS_Canada_tchat  ++;
				if($premier_appel == '' && $ok_client)
					$obj->TT_ANCIENS_CLIENTS_TOUS_PAYS  ++;
				if($premier_appel == '' && $ok_client_total)
					$obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique  ++;
				if($premier_appel == 'X')
					$obj->TT_NVX_CLIENTS_TOUS_PAYS  ++;
				if($premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone')
					$obj->TT_NVX_CLIENTS_TOUS_PAYS_phone  ++;
				if($premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && substr_count($called,'prepaye'))
					$obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye  ++;
				if($premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'phone' && substr_count($called,'audiotel'))
					$obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel  ++;
				if($premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'email')
					$obj->TT_NVX_CLIENTS_TOUS_PAYS_email  ++;
				if($premier_appel == 'X' && $row['UserCreditHistory']['media'] == 'chat')
					$obj->TT_NVX_CLIENTS_TOUS_PAYS_tchat  ++;
				//if( $ok_client && $ok_client_total)
					$obj->NBRE_TT_CLIENTS_TOUS_PAYS   = $obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique + $obj->TT_NVX_CLIENTS_TOUS_PAYS;

					$obj->Total_secondes_depensees  += $row['UserCreditHistory']['credits'];



				$export_data[$tab_index] = $obj;
			}

		}


		foreach($allOrderDatas as $indice => $row){
			$tab_index = '';

			$datecalc = new DateTime($row['Order']['date_add']);
			$datecalc->modify('+'.$listing_utcdec[$datecalc->format('md')].' hour');
			$dddcacl = $datecalc->format('Y-m-d H:i:s');


			$cut_date = explode(' ',$dddcacl);
			$cut_date1 = explode('-',$cut_date[0]);
			$cut_date2 = explode(':',$cut_date[1]);
			$code_iso = '';
			switch ($row['Order']['country_id']) {
					case 1:
						$code_iso = 'France';
						break;
					case 4:
						$code_iso = 'Belgique';
						break;
					case 3:
						$code_iso = 'Suisse';
						break;
					case 5:
						$code_iso = 'Luxembourg';
						break;
					case 13:
						$code_iso = 'Canada';
						break;
					default :
						$code_iso = 'France';
						break;
				}


			if($type_export == 'par_date'){
				$tab_index = $cut_date[0].'-'.$cut_date2[0];
			}else{
				$tab_index = $cut_date2[0];
			}
			if($tab_index){

				$obj = $export_data[$tab_index];
				$obj->Total_credits_achetes  += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				$obj->Total_transac_tous_pays ++;
				if($code_iso == 'France')
				$obj->Total_credits_achetes_france += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				if($code_iso == 'France')
				$obj->Total_transac_france ++;
				if($code_iso == 'Belgique')
				$obj->Total_credits_achetes_belgique += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				if($code_iso == 'Belgique')
				$obj->Total_transac_belgique ++;
				if($code_iso == 'Suisse')
				$obj->Total_credits_achetes_suisse += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				if($code_iso == 'Suisse')
				$obj->Total_transac_suisse ++;
				if($code_iso == 'Luxembourg')
				$obj->Total_credits_achetes_luxembourg += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				if($code_iso == 'Luxembourg')
				$obj->Total_transac_luxembourg ++;
				if($code_iso == 'Canada')
				$obj->Total_credits_achetes_canada += $row['Order']['product_credits'] + $row['Order']['voucher_credits'];
				if($code_iso == 'Canada')
				$obj->Total_transac_canada ++;

				$export_data[$tab_index] = $obj;
			}
		}
		$indice = 0;
		$ancien_date = '';

		$total = new StdClass();
		$total->TT_TEL_PREPAYE_France =0;
		$total->TT_TEL_PREPAYE_Belgique =0;
		$total->TT_TEL_AUDIOTEL_Belgique =0;
		$total->TT_TEL_PREPAYE_Suisse =0;
		$total->TT_TEL_AUDIOTEL_Suisse =0;
		$total->TT_TEL_PREPAYE_Luxembourg =0;
		$total->TT_TEL_AUDIOTEL_Luxembourg =0;
		$total->TT_TEL_PREPAYE_Canada =0;
		$total->TT_TEL_AUDIOTEL_Canada =0;
		$total->TT_Email_France =0;
		$total->TT_Email_Belgique =0;
		$total->TT_Email_Suisse =0;
		$total->TT_Email_Luxembourg =0;
		$total->TT_Email_Canada =0;
		$total->TT_Tchat_France =0;
		$total->TT_Tchat_Belgique  =0;
		$total->TT_Tchat_Suisse =0;
		$total->TT_Tchat_Luxembourg =0;
		$total->TT_Tchat_Canada =0;
		$total->TT_TEL =0;
		$total->TT_Email =0;
		$total->TT_Tchat  =0;
		$total->TT_Consult  =0;
		$total->ANCIENS_CLIENTS_France  =0;
		$total->ANCIENS_CLIENTS_France_unique  =0;
		$total->NVX_CLIENTS_France =0;
		$total->NVX_CLIENTS_France_phone_prepaye =0;
		$total->NVX_CLIENTS_France_email =0;
		$total->NVX_CLIENTS_France_tchat =0;
		$total->ANCIENS_CLIENTS_Belgique  =0;
		$total->ANCIENS_CLIENTS_Belgique_unique  =0;
		$total->NVX_CLIENTS_Belgique  =0;
		$total->NVX_CLIENTS_Belgique_phone =0;
		$total->NVX_CLIENTS_Belgique_phone_prepaye =0;
		$total->NVX_CLIENTS_Belgique_phone_audiotel =0;
		$total->NVX_CLIENTS_Belgique_email =0;
		$total->NVX_CLIENTS_Belgique_tchat =0;
		$total->ANCIENS_CLIENTS_Suisse  =0;
		$total->ANCIENS_CLIENTS_Suisse_unique  =0;
		$total->NVX_CLIENTS_Suisse =0;
		$total->NVX_CLIENTS_Suisse_phone =0;
		$total->NVX_CLIENTS_Suisse_phone_prepaye =0;
		$total->NVX_CLIENTS_Suisse_phone_audiotel =0;
		$total->NVX_CLIENTS_Suisse_email =0;
		$total->NVX_CLIENTS_Suisse_tchat =0;
		$total->ANCIENS_CLIENTS_Luxembourg  =0;
		$total->ANCIENS_CLIENTS_Luxembourg_unique  =0;
		$total->NVX_CLIENTS_Luxembourg  =0;
		$total->NVX_CLIENTS_Luxembourg_phone  =0;
		$total->NVX_CLIENTS_Luxembourg_phone_prepaye  =0;
		$total->NVX_CLIENTS_Luxembourg_phone_audiotel  =0;
		$total->NVX_CLIENTS_Luxembourg_email  =0;
		$total->NVX_CLIENTS_Luxembourg_tchat  =0;
		$total->ANCIENS_CLIENTS_Canada  =0;
		$total->ANCIENS_CLIENTS_Canada_unique  =0;
		$total->NVX_CLIENTS_Canada  =0;
		$total->NVX_CLIENTS_Canada_phone =0;
		$total->NVX_CLIENTS_Canada_phone_prepaye =0;
		$total->NVX_CLIENTS_Canada_phone_audiotel =0;
		$total->NVX_CLIENTS_Canada_email =0;
		$total->NVX_CLIENTS_Canada_tchat =0;
		$total->NBRE_TT_CLIENTS_TOUS_PAYS  =0;
		$total->TT_ANCIENS_CLIENTS_TOUS_PAYS  =0;
		$total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique  =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS  =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_email =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_tchat =0;
		$total->Total_secondes_depensees  =0;
		$total->Total_credits_achetes = 0;
		$total->Total_transac_tous_pays = 0;
				$total->Total_credits_achetes_france = 0;
				$total->Total_transac_france = 0;
				$total->Total_credits_achetes_belgique = 0;
				$total->Total_transac_belgique = 0;
				$total->Total_credits_achetes_suisse = 0;
				$total->Total_transac_suisse = 0;
				$total->Total_credits_achetes_luxembourg = 0;
				$total->Total_transac_luxembourg = 0;
				$total->Total_credits_achetes_canada = 0;
				$total->Total_transac_canada = 0;
		foreach($export_data as $dd => $data_obj){



			if(substr_count($dd,'-')){
				$cut = explode('-',$dd);
				$date = $cut[0].'-'.$cut[1].'-'.$cut[2];
				$heure = $cut[3];

			}else{
				$dd_min = explode(' ',$date_min);
				$dd_max = explode(' ',$date_max);
				$date = $dd_min[0].' - '.$dd_max[0];
				$heure = $dd;
			}
			$heure_supp = $heure + 1;
			if($heure_supp>24)$heure_supp = 0;
			$tranche_heure = $heure . ' a '.str_pad($heure_supp,2,0,STR_PAD_LEFT);

			if($date != $ancien_date && $ancien_date != ''){
			  $line_total = array(

                'DATE'      => $ancien_date,
                'HEURE'      => 'TOTAL',
                'TT TEL PREPAYE France'   => $total->TT_TEL_PREPAYE_France,
				'TT TEL PREPAYE Belgique'   => $total->TT_TEL_PREPAYE_Belgique,
				'TT TEL AUDIOTEL Belgique'   => $total->TT_TEL_AUDIOTEL_Belgique,
				'TT TEL PREPAYE Suisse'   => $total->TT_TEL_PREPAYE_Suisse,
				'TT TEL AUDIOTEL Suisse'   => $total->TT_TEL_AUDIOTEL_Suisse,
				'TT TEL PREPAYE Luxembourg'   => $total->TT_TEL_PREPAYE_Luxembourg,
				'TT TEL AUDIOTEL Luxembourg'   => $total->TT_TEL_AUDIOTEL_Luxembourg,
				'TT TEL PREPAYE Canada'   => $total->TT_TEL_PREPAYE_Canada,
				'TT TEL AUDIOTEL Canada'   => $total->TT_TEL_AUDIOTEL_Canada,
				'TT Email France'   => $total->TT_Email_France ,
				'TT Email Belgique'   => $total->TT_Email_Belgique,
				'TT Email Suisse'   => $total->TT_Email_Suisse ,
				'TT Email Luxembourg'   => $total->TT_Email_Luxembourg,
				'TT Email Canada'   => $total->TT_Email_Canada,
				'TT Tchat France'   => $total->TT_Tchat_France,
				'TT Tchat Belgique'   => $total->TT_Tchat_Belgique ,
				'TT Tchat Suisse'   => $total->TT_Tchat_Suisse ,
				'TT Tchat Luxembourg'   => $total->TT_Tchat_Luxembourg ,
				'TT Tchat Canada'   => $total->TT_Tchat_Canada ,
				'TT tous pays et tous types Téléphone'   => $total->TT_TEL,
				'TT tous pays Email'   => $total->TT_Email,
				'TT tous pays Tchat'   => $total->TT_Tchat ,
				'TOTAL TOUS TYPES CONSULTS'   => $total->TT_Consult ,
				'ANCIENS CLIENTS France'   => $total->ANCIENS_CLIENTS_France_unique ,
				'NVX CLIENTS France'   => $total->NVX_CLIENTS_France,
				'NVX CLIENTS France Phone prepaye'   => $total->NVX_CLIENTS_France_phone_prepaye,
				'NVX CLIENTS France Email'   => $total->NVX_CLIENTS_France_email,
				'NVX CLIENTS France tchat'   => $total->NVX_CLIENTS_France_tchat,
				'ANCIENS CLIENTS Belgique'   => $total->ANCIENS_CLIENTS_Belgique_unique ,
				'NVX CLIENTS Belgique'   => $total->NVX_CLIENTS_Belgique ,
				'NVX CLIENTS Belgique Phone'   => $total->NVX_CLIENTS_Belgique_phone,
				'NVX CLIENTS Belgique Phone prepaye'   => $total->NVX_CLIENTS_Belgique_phone_prepaye,
				'NVX CLIENTS Belgique Phone audiotel'   => $total->NVX_CLIENTS_Belgique_phone_audiotel,
				'NVX CLIENTS Belgique Email'   => $total->NVX_CLIENTS_Belgique_email,
				'NVX CLIENTS Belgique tchat'   => $total->NVX_CLIENTS_Belgique_tchat,
				'ANCIENS CLIENTS Suisse'   => $total->ANCIENS_CLIENTS_Suisse_unique ,
				'NVX CLIENTS Suisse'   => $total->NVX_CLIENTS_Suisse,
				'NVX CLIENTS Suisse Phone'   => $total->NVX_CLIENTS_Suisse_phone,
				'NVX CLIENTS Suisse Phone prepaye'   => $total->NVX_CLIENTS_Suisse_phone_prepaye,
				'NVX CLIENTS Suisse Phone audiotel'   => $total->NVX_CLIENTS_Suisse_phone_audiotel,
				'NVX CLIENTS Suisse Email'   => $total->NVX_CLIENTS_Suisse_email,
				'NVX CLIENTS Suisse tchat'   => $total->NVX_CLIENTS_Suisse_tchat,
				'ANCIENS CLIENTS Luxembourg'   => $total->ANCIENS_CLIENTS_Luxembourg_unique ,
				'NVX CLIENTS Luxembourg'   => $total->NVX_CLIENTS_Luxembourg ,
				'NVX CLIENTS Luxembourg Phone'   => $total->NVX_CLIENTS_Luxembourg_phone,
				'NVX CLIENTS Luxembourg Phone prepaye'   => $total->NVX_CLIENTS_Luxembourg_phone_prepaye,
				'NVX CLIENTS Luxembourg Phone audiotel'   => $total->NVX_CLIENTS_Luxembourg_phone_audiotel,
				'NVX CLIENTS Luxembourg Email'   => $total->NVX_CLIENTS_Luxembourg_email,
				'NVX CLIENTS Luxembourg tchat'   => $total->NVX_CLIENTS_Luxembourg_tchat,
				'ANCIENS CLIENTS Canada'   => $total->ANCIENS_CLIENTS_Canada_unique ,
				'NVX CLIENTS Canada'   => $total->NVX_CLIENTS_Canada ,
				'NVX CLIENTS Canada Phone'   => $total->NVX_CLIENTS_Canada_phone,
				'NVX CLIENTS Canada Phone prepaye'   => $total->NVX_CLIENTS_Canada_phone_prepaye,
				'NVX CLIENTS Canada Phone audiotel'   => $total->NVX_CLIENTS_Canada_phone_audiotel,
				'NVX CLIENTS Canada Email'   => $total->NVX_CLIENTS_Canada_email,
				'NVX CLIENTS Canada tchat'   => $total->NVX_CLIENTS_Canada_tchat,
				'NBRE TT CLIENTS TOUS PAYS'   => $total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique + $total->TT_NVX_CLIENTS_TOUS_PAYS,//$total->NBRE_TT_CLIENTS_TOUS_PAYS ,
				'TT ANCIENS CLIENTS TOUS PAYS'   => $total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique ,
				'TT NVX CLIENTS TOUS PAYS'   => $total->TT_NVX_CLIENTS_TOUS_PAYS ,
				'TT NVX CLIENTS TOUS PAYS Phone'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone,
				'TT NVX CLIENTS TOUS PAYS Phone prepaye'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye,
				'TT NVX CLIENTS TOUS PAYS Phone audiotel'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel,
				'TT NVX CLIENTS TOUS PAYS Email'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_email,
				'TT NVX CLIENTS TOUS PAYS tchat'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_tchat,
				'Total Credits ou secondes depensees'   => $total->Total_secondes_depensees ,
				'Total Credits ou achetes'   => $total->Total_credits_achetes ,
				'Total Transaction'   =>   $total->Total_transac_tous_pays ,
				'Total Credits ou achetes France'   => $total->Total_credits_achetes_france ,
				'Total Transaction France'   => $total->Total_transac_france,
				'Total Credits ou achetes Belgique'   => $total->Total_credits_achetes_belgique ,
				'Total Transaction Belgique'   => $total->Total_transac_belgique ,
				'Total Credits ou achetes Suisse'   => $total->Total_credits_achetes_suisse ,
				'Total Transaction Suisse'   => $total->Total_transac_suisse ,
				'Total Credits ou achetes Luxembourg'   => $total->Total_credits_achetes_luxembourg ,
				'Total Transaction Luxembourg'   => $total->Total_transac_luxembourg ,
				'Total Credits ou achetes Canada'   => $total->Total_credits_achetes_canada ,
				'Total Transaction Canada'   => $total->Total_transac_canada
            );
			  fputcsv($fp, array_values($line_total), ';', '"');
			  $total->TT_TEL_PREPAYE_France =0;
		$total->TT_TEL_PREPAYE_Belgique =0;
		$total->TT_TEL_AUDIOTEL_Belgique =0;
		$total->TT_TEL_PREPAYE_Suisse =0;
		$total->TT_TEL_AUDIOTEL_Suisse =0;
		$total->TT_TEL_PREPAYE_Luxembourg =0;
		$total->TT_TEL_AUDIOTEL_Luxembourg =0;
		$total->TT_TEL_PREPAYE_Canada =0;
		$total->TT_TEL_AUDIOTEL_Canada =0;
		$total->TT_Email_France =0;
		$total->TT_Email_Belgique =0;
		$total->TT_Email_Suisse =0;
		$total->TT_Email_Luxembourg =0;
		$total->TT_Email_Canada =0;
		$total->TT_Tchat_France =0;
		$total->TT_Tchat_Belgique  =0;
		$total->TT_Tchat_Suisse =0;
		$total->TT_Tchat_Luxembourg =0;
		$total->TT_Tchat_Canada =0;
		$total->TT_TEL =0;
		$total->TT_Email =0;
		$total->TT_Tchat  =0;
		$total->TT_Consult  =0;
		$total->ANCIENS_CLIENTS_France =0;
				$total->ANCIENS_CLIENTS_France_unique =0;
		$total->NVX_CLIENTS_France =0;
		$total->NVX_CLIENTS_France_phone_prepaye =0;
		$total->NVX_CLIENTS_France_email =0;
		$total->NVX_CLIENTS_France_tchat =0;
		$total->ANCIENS_CLIENTS_Belgique  =0;
				$total->ANCIENS_CLIENTS_Belgique_unique  =0;
		$total->NVX_CLIENTS_Belgique  =0;
		$total->NVX_CLIENTS_Belgique_phone =0;
		$total->NVX_CLIENTS_Belgique_phone_prepaye =0;
		$total->NVX_CLIENTS_Belgique_phone_audiotel =0;
		$total->NVX_CLIENTS_Belgique_email =0;
		$total->NVX_CLIENTS_Belgique_tchat =0;
		$total->ANCIENS_CLIENTS_Suisse  =0;
				$total->ANCIENS_CLIENTS_Suisse_unique  =0;
		$total->NVX_CLIENTS_Suisse =0;
		$total->NVX_CLIENTS_Suisse_phone =0;
		$total->NVX_CLIENTS_Suisse_phone_prepaye =0;
		$total->NVX_CLIENTS_Suisse_phone_audiotel =0;
		$total->NVX_CLIENTS_Suisse_email =0;
		$total->NVX_CLIENTS_Suisse_tchat =0;
		$total->ANCIENS_CLIENTS_Luxembourg  =0;
				$total->ANCIENS_CLIENTS_Luxembourg_unique  =0;
		$total->NVX_CLIENTS_Luxembourg  =0;
		$total->NVX_CLIENTS_Luxembourg_phone =0;
		$total->NVX_CLIENTS_Luxembourg_phone_prepaye =0;
		$total->NVX_CLIENTS_Luxembourg_phone_audiotel =0;
		$total->NVX_CLIENTS_Luxembourg_email =0;
		$total->NVX_CLIENTS_Luxembourg_tchat =0;
		$total->ANCIENS_CLIENTS_Canada  =0;
				$total->ANCIENS_CLIENTS_Canada_unique  =0;
		$total->NVX_CLIENTS_Canada  =0;
		$total->NVX_CLIENTS_Canada_phone =0;
		$total->NVX_CLIENTS_Canada_phone_prepaye =0;
		$total->NVX_CLIENTS_Canada_phone_audiotel =0;
		$total->NVX_CLIENTS_Canada_email =0;
		$total->NVX_CLIENTS_Canada_tchat =0;
		$total->NBRE_TT_CLIENTS_TOUS_PAYS  =0;
		$total->TT_ANCIENS_CLIENTS_TOUS_PAYS  =0;
				$total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique  =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS  =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_email =0;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_tchat =0;
		$total->Total_secondes_depensees  =0;
		$total->Total_credits_achetes  =0;
		$total->Total_transac_tous_pays = 0;
				$total->Total_credits_achetes_france = 0;
				$total->Total_transac_france = 0;
				$total->Total_credits_achetes_belgique = 0;
				$total->Total_transac_belgique = 0;
				$total->Total_credits_achetes_suisse = 0;
				$total->Total_transac_suisse = 0;
				$total->Total_credits_achetes_luxembourg = 0;
				$total->Total_transac_luxembourg = 0;
				$total->Total_credits_achetes_canada = 0;
				$total->Total_transac_canada = 0;
			  $line_separation = array(
                'DATE'      => '',
                'HEURE'      => '',
                'TT TEL PREPAYE France'   => '',
				'TT TEL PREPAYE Belgique'   => '',
				'TT TEL AUDIOTEL Belgique'   => '',
				'TT TEL PREPAYE Suisse'   => '',
				'TT TEL AUDIOTEL Suisse'   => '',
				'TT TEL PREPAYE Luxembourg'   => '',
				'TT TEL AUDIOTEL Luxembourg'   => '',
				'TT TEL PREPAYE Canada'   => '',
				'TT TEL AUDIOTEL Canada'   => '',
				'TT Email France'   => '',
				'TT Email Belgique'   => '',
				'TT Email Suisse'   => '',
				'TT Email Luxembourg'   => '',
				'TT Email Canada'   => '',
				'TT Tchat France'   => '',
				'TT Tchat Belgique'   => '',
				'TT Tchat Suisse'   => '',
				'TT Tchat Luxembourg'   => '',
				'TT Tchat Canada'   => '',
				'TT tous pays et tous types Téléphone'   => '',
				'TT tous pays Email'   => '',
				'TT tous pays Tchat'   => '',
				'TOTAL TOUS TYPES CONSULTS'   => '',
				'ANCIENS CLIENTS France'   => '',
				'NVX CLIENTS France'   => '',
				'NVX CLIENTS France Phone prepaye'   => '',
				'NVX CLIENTS France Email'   => '',
				'NVX CLIENTS France tchat'   => '',
				'ANCIENS CLIENTS Belgique'   => '',
				'NVX CLIENTS Belgique'   => '',
				'NVX CLIENTS Belgique Phone'   => '',
				'NVX CLIENTS Belgique Phone prepaye'   => '',
				'NVX CLIENTS Belgique Phone audiotel'   => '',
				'NVX CLIENTS Belgique Email'   => '',
				'NVX CLIENTS Belgique tchat'   => '',
				'ANCIENS CLIENTS Suisse'   => '',
				'NVX CLIENTS Suisse'   => '',
				'NVX CLIENTS Suisse Phone'   => '',
				'NVX CLIENTS Suisse Phone prepaye'   => '',
				'NVX CLIENTS Suisse Phone audiotel'   => '',
				'NVX CLIENTS Suisse Email'   => '',
				'NVX CLIENTS Suisse tchat'   => '',
				'ANCIENS CLIENTS Luxembourg'   => '',
				'NVX CLIENTS Luxembourg'   => '',
				'NVX CLIENTS Luxembourg Phone'   => '',
				'NVX CLIENTS Luxembourg Phone prepaye'   => '',
				'NVX CLIENTS Luxembourg Phone audiotel'   => '',
				'NVX CLIENTS Luxembourg Email'   => '',
				'NVX CLIENTS Luxembourg tchat'   => '',
				'ANCIENS CLIENTS Canada'   => '',
				'NVX CLIENTS Canada'   => '',
				'NVX CLIENTS Canada Phone'   => '',
				'NVX CLIENTS Canada Phone prepaye'   => '',
				'NVX CLIENTS Canada Phone audiotel'   => '',
				'NVX CLIENTS Canada Email'   => '',
				'NVX CLIENTS Canada tchat'   => '',
				'NBRE TT CLIENTS TOUS PAYS'   => '',
				'TT ANCIENS CLIENTS TOUS PAYS'   => '',
				'TT NVX CLIENTS TOUS PAYS'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone prepaye'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone audiotel'   => '',
				'TT NVX CLIENTS TOUS PAYS Email'   => '',
				'TT NVX CLIENTS TOUS PAYS tchat'   => '',
				'Total Credits ou secondes depensees'   => '',
			  'Total Credits ou secondes achetees'   => '',
				  'Total Transaction'   =>   '' ,
				'Total Credits ou achetes France'   => '' ,
				'Total Transaction France'   => '',
				'Total Credits ou achetes Belgique'   => '' ,
				'Total Transaction Belgique'   => '' ,
				'Total Credits ou achetes Suisse'   => '' ,
				'Total Transaction Suisse'   => '' ,
				'Total Credits ou achetes Luxembourg'   => '' ,
				'Total Transaction Luxembourg'   => '' ,
				'Total Credits ou achetes Canada'   => '' ,
				'Total Transaction Canada'   => ''
			  );

			  	fputcsv($fp, array_values($line_separation), ';', '"');

		  }




			 $line = array(

                'DATE'      => $date,
                'HEURE'      => $tranche_heure,
                'TT TEL PREPAYE France'   => $data_obj->TT_TEL_PREPAYE_France,
				'TT TEL PREPAYE Belgique'   => $data_obj->TT_TEL_PREPAYE_Belgique,
				'TT TEL AUDIOTEL Belgique'   => $data_obj->TT_TEL_AUDIOTEL_Belgique,
				'TT TEL PREPAYE Suisse'   => $data_obj->TT_TEL_PREPAYE_Suisse,
				'TT TEL AUDIOTEL Suisse'   => $data_obj->TT_TEL_AUDIOTEL_Suisse,
				'TT TEL PREPAYE Luxembourg'   => $data_obj->TT_TEL_PREPAYE_Luxembourg,
				'TT TEL AUDIOTEL Luxembourg'   => $data_obj->TT_TEL_AUDIOTEL_Luxembourg,
				'TT TEL PREPAYE Canada'   => $data_obj->TT_TEL_PREPAYE_Canada,
				'TT TEL AUDIOTEL Canada'   => $data_obj->TT_TEL_AUDIOTEL_Canada,
				'TT Email France'   => $data_obj->TT_Email_France ,
				'TT Email Belgique'   => $data_obj->TT_Email_Belgique,
				'TT Email Suisse'   => $data_obj->TT_Email_Suisse ,
				'TT Email Luxembourg'   => $data_obj->TT_Email_Luxembourg,
				'TT Email Canada'   => $data_obj->TT_Email_Canada,
				'TT Tchat France'   => $data_obj->TT_Tchat_France,
				'TT Tchat Belgique'   => $data_obj->TT_Tchat_Belgique ,
				'TT Tchat Suisse'   => $data_obj->TT_Tchat_Suisse ,
				'TT Tchat Luxembourg'   => $data_obj->TT_Tchat_Luxembourg ,
				'TT Tchat Canada'   => $data_obj->TT_Tchat_Canada ,
				'TT tous pays et tous types Téléphone'   => $data_obj->TT_TEL,
				'TT tous pays Email'   => $data_obj->TT_Email,
				'TT tous pays Tchat'   => $data_obj->TT_Tchat ,
				'TOTAL TOUS TYPES CONSULTS'   => $data_obj->TT_Consult ,
				'ANCIENS CLIENTS France'   => $data_obj->ANCIENS_CLIENTS_France ,
				'NVX CLIENTS France'   => $data_obj->NVX_CLIENTS_France,
				'NVX CLIENTS France Phone prepaye'   => $data_obj->NVX_CLIENTS_France_phone_prepaye,
				'NVX CLIENTS France Email'   => $data_obj->NVX_CLIENTS_France_email,
				'NVX CLIENTS France Tchat'   => $data_obj->NVX_CLIENTS_France_tchat,
				'ANCIENS CLIENTS Belgique'   => $data_obj->ANCIENS_CLIENTS_Belgique ,
				'NVX CLIENTS Belgique'   => $data_obj->NVX_CLIENTS_Belgique ,
				'NVX CLIENTS Belgique Phone'   => $data_obj->NVX_CLIENTS_Belgique_phone,
				'NVX CLIENTS Belgique Phone prepaye'   => $data_obj->NVX_CLIENTS_Belgique_phone_prepaye,
				'NVX CLIENTS Belgique Phone audiotel'   => $data_obj->NVX_CLIENTS_Belgique_phone_audiotel,
				'NVX CLIENTS Belgique Email'   => $data_obj->NVX_CLIENTS_Belgique_email,
				'NVX CLIENTS Belgique Tchat'   => $data_obj->NVX_CLIENTS_Belgique_tchat,
				'ANCIENS CLIENTS Suisse'   => $data_obj->ANCIENS_CLIENTS_Suisse ,
				'NVX CLIENTS Suisse'   => $data_obj->NVX_CLIENTS_Suisse,
				'NVX CLIENTS Suisse Phone'   => $data_obj->NVX_CLIENTS_Suisse_phone,
				'NVX CLIENTS Suisse Phone prepaye'   => $data_obj->NVX_CLIENTS_Suisse_phone_prepaye,
				'NVX CLIENTS Suisse Phone audiotel'   => $data_obj->NVX_CLIENTS_Suisse_phone_audiotel,
				'NVX CLIENTS Suisse Email'   => $data_obj->NVX_CLIENTS_Suisse_email,
				'NVX CLIENTS Suisse Tchat'   => $data_obj->NVX_CLIENTS_Suisse_tchat,
				'ANCIENS CLIENTS Luxembourg'   => $data_obj->ANCIENS_CLIENTS_Luxembourg ,
				'NVX CLIENTS Luxembourg'   => $data_obj->NVX_CLIENTS_Luxembourg ,
				'NVX CLIENTS Luxembourg Phone'   => $data_obj->NVX_CLIENTS_Luxembourg_phone,
				'NVX CLIENTS Luxembourg Phone prepaye'   => $data_obj->NVX_CLIENTS_Luxembourg_phone_prepaye,
				'NVX CLIENTS Luxembourg Phone audiotel'   => $data_obj->NVX_CLIENTS_Luxembourg_phone_audiotel,
				'NVX CLIENTS Luxembourg Email'   => $data_obj->NVX_CLIENTS_Luxembourg_email,
				'NVX CLIENTS Luxembourg Tchat'   => $data_obj->NVX_CLIENTS_Luxembourg_tchat,
				'ANCIENS CLIENTS Canada'   => $data_obj->ANCIENS_CLIENTS_Canada ,
				'NVX CLIENTS Canada'   => $data_obj->NVX_CLIENTS_Canada ,
				'NVX CLIENTS Canada Phone'   => $data_obj->NVX_CLIENTS_Canada_phone,
				'NVX CLIENTS Canada Phone prepaye'   => $data_obj->NVX_CLIENTS_Canada_phone_prepaye,
				'NVX CLIENTS Canada Phone audiotel'   => $data_obj->NVX_CLIENTS_Canada_phone_audiotel,
				'NVX CLIENTS Canada Email'   => $data_obj->NVX_CLIENTS_Canada_email,
				'NVX CLIENTS Canada Tchat'   => $data_obj->NVX_CLIENTS_Canada_tchat,
				'NBRE TT CLIENTS TOUS PAYS'   => $data_obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique + $data_obj->TT_NVX_CLIENTS_TOUS_PAYS, //$data_obj->NBRE_TT_CLIENTS_TOUS_PAYS ,
				'TT ANCIENS CLIENTS TOUS PAYS'   => $data_obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique ,
				'TT NVX CLIENTS TOUS PAYS'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS ,
				'TT NVX CLIENTS TOUS PAYS Phone'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone,
				'TT NVX CLIENTS TOUS PAYS Phone prepaye'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye,
				'TT NVX CLIENTS TOUS PAYS Phone audiotel'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel,
				'TT NVX CLIENTS TOUS PAYS Email'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS_email,
				'TT NVX CLIENTS TOUS PAYS Tchat'   => $data_obj->TT_NVX_CLIENTS_TOUS_PAYS_tchat,
				'Total Credits ou secondes depensees'   => $data_obj->Total_secondes_depensees ,
				'Total Credits ou achetes'   => $data_obj->Total_credits_achetes,
				 'Total Transaction'   =>   $data_obj->Total_transac_tous_pays ,
				'Total Credits ou achetes France'   => $data_obj->Total_credits_achetes_france ,
				'Total Transaction France'   => $data_obj->Total_transac_france,
				'Total Credits ou achetes Belgique'   => $data_obj->Total_credits_achetes_belgique ,
				'Total Transaction Belgique'   => $data_obj->Total_transac_belgique ,
				'Total Credits ou achetes Suisse'   => $data_obj->Total_credits_achetes_suisse ,
				'Total Transaction Suisse'   => $data_obj->Total_transac_suisse ,
				'Total Credits ou achetes Luxembourg'   => $data_obj->Total_credits_achetes_luxembourg ,
				'Total Transaction Luxembourg'   => $data_obj->Total_transac_luxembourg ,
				'Total Credits ou achetes Canada'   => $data_obj->Total_credits_achetes_canada ,
				'Total Transaction Canada'   => $data_obj->Total_transac_canada
            );

			$total->TT_TEL_PREPAYE_France +=$data_obj->TT_TEL_PREPAYE_France;
		$total->TT_TEL_PREPAYE_Belgique +=$data_obj->TT_TEL_PREPAYE_Belgique;
		$total->TT_TEL_AUDIOTEL_Belgique +=$data_obj->TT_TEL_AUDIOTEL_Belgique;
		$total->TT_TEL_PREPAYE_Suisse +=$data_obj->TT_TEL_PREPAYE_Suisse;
		$total->TT_TEL_AUDIOTEL_Suisse +=$data_obj->TT_TEL_AUDIOTEL_Suisse;
		$total->TT_TEL_PREPAYE_Luxembourg +=$data_obj->TT_TEL_PREPAYE_Luxembourg;
		$total->TT_TEL_AUDIOTEL_Luxembourg +=$data_obj->TT_TEL_AUDIOTEL_Luxembourg;
		$total->TT_TEL_PREPAYE_Canada +=$data_obj->TT_TEL_PREPAYE_Canada;
		$total->TT_TEL_AUDIOTEL_Canada +=$data_obj->TT_TEL_AUDIOTEL_Canada;
		$total->TT_Email_France +=$data_obj->TT_Email_France;
		$total->TT_Email_Belgique +=$data_obj->TT_Email_Belgique;
		$total->TT_Email_Suisse +=$data_obj->TT_Email_Suisse ;
		$total->TT_Email_Luxembourg +=$data_obj->TT_Email_Luxembourg;
		$total->TT_Email_Canada +=$data_obj->TT_Email_Canada;
		$total->TT_Tchat_France +=$data_obj->TT_Tchat_France;
		$total->TT_Tchat_Belgique  +=$data_obj->TT_Tchat_Belgique;
		$total->TT_Tchat_Suisse +=$data_obj->TT_Tchat_Suisse;
		$total->TT_Tchat_Luxembourg +=$data_obj->TT_Tchat_Luxembourg;
		$total->TT_Tchat_Canada +=$data_obj->TT_Tchat_Canada;
		$total->TT_TEL +=$data_obj->TT_TEL;
		$total->TT_Email +=$data_obj->TT_Email;
		$total->TT_Tchat  +=$data_obj->TT_Tchat ;
		$total->TT_Consult  +=$data_obj->TT_Consult;
		$total->ANCIENS_CLIENTS_France  +=$data_obj->ANCIENS_CLIENTS_France;
		$total->ANCIENS_CLIENTS_France_unique  +=$data_obj->ANCIENS_CLIENTS_France_unique;
		$total->NVX_CLIENTS_France +=$data_obj->NVX_CLIENTS_France;
		$total->NVX_CLIENTS_France_phone_prepaye +=$data_obj->NVX_CLIENTS_France_phone_prepaye;
		$total->NVX_CLIENTS_France_email +=$data_obj->NVX_CLIENTS_France_email;
		$total->NVX_CLIENTS_France_tchat +=$data_obj->NVX_CLIENTS_France_tchat;
		$total->ANCIENS_CLIENTS_Belgique  +=$data_obj->ANCIENS_CLIENTS_Belgique;
		$total->ANCIENS_CLIENTS_Belgique_unique  +=$data_obj->ANCIENS_CLIENTS_Belgique_unique;
		$total->NVX_CLIENTS_Belgique  +=$data_obj->NVX_CLIENTS_Belgique;
		$total->NVX_CLIENTS_Belgique_phone +=$data_obj->NVX_CLIENTS_Belgique_phone;
		$total->NVX_CLIENTS_Belgique_phone_prepaye +=$data_obj->NVX_CLIENTS_Belgique_phone_prepaye;
		$total->NVX_CLIENTS_Belgique_phone_audiotel +=$data_obj->NVX_CLIENTS_Belgique_phone_audiotel;
		$total->NVX_CLIENTS_Belgique_email +=$data_obj->NVX_CLIENTS_Belgique_email;
		$total->NVX_CLIENTS_Belgique_tchat +=$data_obj->NVX_CLIENTS_Belgique_tchat;
		$total->ANCIENS_CLIENTS_Suisse  +=$data_obj->ANCIENS_CLIENTS_Suisse;
		$total->ANCIENS_CLIENTS_Suisse_unique  +=$data_obj->ANCIENS_CLIENTS_Suisse_unique;
		$total->NVX_CLIENTS_Suisse +=$data_obj->NVX_CLIENTS_Suisse;
		$total->NVX_CLIENTS_Suisse_phone +=$data_obj->NVX_CLIENTS_Suisse_phone;
		$total->NVX_CLIENTS_Suisse_phone_prepaye +=$data_obj->NVX_CLIENTS_Suisse_phone_prepaye;
		$total->NVX_CLIENTS_Suisse_phone_audiotel +=$data_obj->NVX_CLIENTS_Suisse_phone_audiotel;
		$total->NVX_CLIENTS_Suisse_email +=$data_obj->NVX_CLIENTS_Suisse_email;
		$total->NVX_CLIENTS_Suisse_tchat +=$data_obj->NVX_CLIENTS_Suisse_tchat;
		$total->ANCIENS_CLIENTS_Luxembourg  +=$data_obj->ANCIENS_CLIENTS_Luxembourg;
		$total->ANCIENS_CLIENTS_Luxembourg_unique  +=$data_obj->ANCIENS_CLIENTS_Luxembourg_unique;
		$total->NVX_CLIENTS_Luxembourg  +=$data_obj->NVX_CLIENTS_Luxembourg;
		$total->NVX_CLIENTS_Luxembourg_phone +=$data_obj->NVX_CLIENTS_Luxembourg_phone;
		$total->NVX_CLIENTS_Luxembourg_phone_prepaye +=$data_obj->NVX_CLIENTS_Luxembourg_phone_prepaye;
		$total->NVX_CLIENTS_Luxembourg_phone_audiotel +=$data_obj->NVX_CLIENTS_Luxembourg_phone_audiotel;
		$total->NVX_CLIENTS_Luxembourg_email +=$data_obj->NVX_CLIENTS_Luxembourg_email;
		$total->NVX_CLIENTS_Luxembourg_tchat +=$data_obj->NVX_CLIENTS_Luxembourg_tchat;
		$total->ANCIENS_CLIENTS_Canada  +=$data_obj->ANCIENS_CLIENTS_Canada;
		$total->ANCIENS_CLIENTS_Canada_unique  +=$data_obj->ANCIENS_CLIENTS_Canada_unique;
		$total->NVX_CLIENTS_Canada  +=$data_obj->NVX_CLIENTS_Canada;
		$total->NVX_CLIENTS_Canada_phone +=$data_obj->NVX_CLIENTS_Canada_phone;
		$total->NVX_CLIENTS_Canada_phone_prepaye +=$data_obj->NVX_CLIENTS_Canada_phone_prepaye;
		$total->NVX_CLIENTS_Canada_phone_audiotel +=$data_obj->NVX_CLIENTS_Canada_phone_audiotel;
		$total->NVX_CLIENTS_Canada_email +=$data_obj->NVX_CLIENTS_Canada_email;
		$total->NVX_CLIENTS_Canada_tchat +=$data_obj->NVX_CLIENTS_Canada_tchat;
		$total->NBRE_TT_CLIENTS_TOUS_PAYS  +=$data_obj->NBRE_TT_CLIENTS_TOUS_PAYS;
		$total->NBRE_TT_CLIENTS_TOUS_PAYS_unique  +=$data_obj->NBRE_TT_CLIENTS_TOUS_PAYS_unique;
		$total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique  +=$data_obj->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique;
		$total->TT_NVX_CLIENTS_TOUS_PAYS  +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_email +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS_email;
		$total->TT_NVX_CLIENTS_TOUS_PAYS_tchat +=$data_obj->TT_NVX_CLIENTS_TOUS_PAYS_tchat;
		$total->Total_secondes_depensees  +=$data_obj->Total_secondes_depensees;
		$total->Total_credits_achetes  +=$data_obj->Total_credits_achetes;
		$total->Total_transac_tous_pays +=$data_obj->Total_transac_tous_pays;
				$total->Total_credits_achetes_france +=$data_obj->Total_credits_achetes_france;
				$total->Total_transac_france +=$data_obj->Total_transac_france;
				$total->Total_credits_achetes_belgique +=$data_obj->Total_credits_achetes_belgique;
				$total->Total_transac_belgique +=$data_obj->Total_transac_belgique;
				$total->Total_credits_achetes_suisse +=$data_obj->Total_credits_achetes_suisse;
				$total->Total_transac_suisse +=$data_obj->Total_transac_suisse;
				$total->Total_credits_achetes_luxembourg +=$data_obj->Total_credits_achetes_luxembourg;
				$total->Total_transac_luxembourg +=$data_obj->Total_transac_luxembourg;
				$total->Total_credits_achetes_canada +=$data_obj->Total_credits_achetes_canada;
				$total->Total_transac_canada +=$data_obj->Total_transac_canada;
            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
				$indice ++;
            }
           fputcsv($fp, array_values($line), ';', '"');

		  	$ancien_date = $date;
        }


			  $line_total = array(

                'DATE'      => $date,
                'HEURE'      => 'TOTAL',
                'TT TEL PREPAYE France'   => $total->TT_TEL_PREPAYE_France,
				'TT TEL PREPAYE Belgique'   => $total->TT_TEL_PREPAYE_Belgique,
				'TT TEL AUDIOTEL Belgique'   => $total->TT_TEL_AUDIOTEL_Belgique,
				'TT TEL PREPAYE Suisse'   => $total->TT_TEL_PREPAYE_Suisse,
				'TT TEL AUDIOTEL Suisse'   => $total->TT_TEL_AUDIOTEL_Suisse,
				'TT TEL PREPAYE Luxembourg'   => $total->TT_TEL_PREPAYE_Luxembourg,
				'TT TEL AUDIOTEL Luxembourg'   => $total->TT_TEL_AUDIOTEL_Luxembourg,
				'TT TEL PREPAYE Canada'   => $total->TT_TEL_PREPAYE_Canada,
				'TT TEL AUDIOTEL Canada'   => $total->TT_TEL_AUDIOTEL_Canada,
				'TT Email France'   => $total->TT_Email_France ,
				'TT Email Belgique'   => $total->TT_Email_Belgique,
				'TT Email Suisse'   => $total->TT_Email_Suisse ,
				'TT Email Luxembourg'   => $total->TT_Email_Luxembourg,
				'TT Email Canada'   => $total->TT_Email_Canada,
				'TT Tchat France'   => $total->TT_Tchat_France,
				'TT Tchat Belgique'   => $total->TT_Tchat_Belgique ,
				'TT Tchat Suisse'   => $total->TT_Tchat_Suisse ,
				'TT Tchat Luxembourg'   => $total->TT_Tchat_Luxembourg ,
				'TT Tchat Canada'   => $total->TT_Tchat_Canada ,
				'TT tous pays et tous types Téléphone'   => $total->TT_TEL,
				'TT tous pays Email'   => $total->TT_Email,
				'TT tous pays Tchat'   => $total->TT_Tchat ,
				'TOTAL TOUS TYPES CONSULTS'   => $total->TT_Consult ,
				'ANCIENS CLIENTS France'   => $total->ANCIENS_CLIENTS_France_unique ,
				'NVX CLIENTS France'   => $total->NVX_CLIENTS_France,
				'NVX CLIENTS France Phone prepaye'   => $total->NVX_CLIENTS_France_phone_prepaye,
				'NVX CLIENTS France Email'   => $total->NVX_CLIENTS_France_email,
				'NVX CLIENTS France Tchat'   => $total->NVX_CLIENTS_France_tchat,
				'ANCIENS CLIENTS Belgique'   => $total->ANCIENS_CLIENTS_Belgique_unique ,
				'NVX CLIENTS Belgique'   => $total->NVX_CLIENTS_Belgique ,
				'NVX CLIENTS Belgique Phone'   => $total->NVX_CLIENTS_Belgique_phone,
				'NVX CLIENTS Belgique Phone prepaye'   => $total->NVX_CLIENTS_Belgique_phone_prepaye,
				'NVX CLIENTS Belgique Phone audiotel'   => $total->NVX_CLIENTS_Belgique_phone_audiotel,
				'NVX CLIENTS Belgique Email'   => $total->NVX_CLIENTS_Belgique_email,
				'NVX CLIENTS Belgique Tchat'   => $total->NVX_CLIENTS_Belgique_tchat,
				'ANCIENS CLIENTS Suisse'   => $total->ANCIENS_CLIENTS_Suisse_unique ,
				'NVX CLIENTS Suisse'   => $total->NVX_CLIENTS_Suisse,
				'NVX CLIENTS Suisse Phone'   => $total->NVX_CLIENTS_Suisse_phone,
				'NVX CLIENTS Suisse Phone prepaye'   => $total->NVX_CLIENTS_Suisse_phone_prepaye,
				'NVX CLIENTS Suisse Phone audiotel'   => $total->NVX_CLIENTS_Suisse_phone_audiotel,
				'NVX CLIENTS Suisse Email'   => $total->NVX_CLIENTS_Suisse_email,
				'NVX CLIENTS Suisse Tchat'   => $total->NVX_CLIENTS_Suisse_tchat,
				'ANCIENS CLIENTS Luxembourg'   => $total->ANCIENS_CLIENTS_Luxembourg_unique ,
				'NVX CLIENTS Luxembourg'   => $total->NVX_CLIENTS_Luxembourg ,
				'NVX CLIENTS Luxembourg Phone'   => $total->NVX_CLIENTS_Luxembourg_phone,
				'NVX CLIENTS Luxembourg Phone prepaye'   => $total->NVX_CLIENTS_Luxembourg_phone_prepaye,
				'NVX CLIENTS Luxembourg Phone audiotel'   => $total->NVX_CLIENTS_Luxembourg_phone_audiotel,
				'NVX CLIENTS Luxembourg Email'   => $total->NVX_CLIENTS_Luxembourg_email,
				'NVX CLIENTS Luxembourg Tchat'   => $total->NVX_CLIENTS_Luxembourg_tchat,
				'ANCIENS CLIENTS Canada'   => $total->ANCIENS_CLIENTS_Canada_unique ,
				'NVX CLIENTS Canada'   => $total->NVX_CLIENTS_Canada ,
				'NVX CLIENTS Canada Phone'   => $total->NVX_CLIENTS_Canada_phone,
				'NVX CLIENTS Canada Phone prepaye'   => $total->NVX_CLIENTS_Canada_phone_prepaye,
				'NVX CLIENTS Canada Phone audiotel'   => $total->NVX_CLIENTS_Canada_phone_audiotel,
				'NVX CLIENTS Canada Email'   => $total->NVX_CLIENTS_Canada_email,
				'NVX CLIENTS Canada Tchat'   => $total->NVX_CLIENTS_Canada_tchat,
				'NBRE TT CLIENTS TOUS PAYS'   => $total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique + $total->TT_NVX_CLIENTS_TOUS_PAYS,//$total->NBRE_TT_CLIENTS_TOUS_PAYS ,
				'TT ANCIENS CLIENTS TOUS PAYS'   => $total->TT_ANCIENS_CLIENTS_TOUS_PAYS_unique ,
				'TT NVX CLIENTS TOUS PAYS'   => $total->TT_NVX_CLIENTS_TOUS_PAYS ,
				'TT NVX CLIENTS TOUS PAYS Phone'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone,
				'TT NVX CLIENTS TOUS PAYS Phone prepaye'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone_prepaye,
				'TT NVX CLIENTS TOUS PAYS Phone audiotel'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_phone_audiotel,
				'TT NVX CLIENTS TOUS PAYS Email'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_email,
				'TT NVX CLIENTS TOUS PAYS Tchat'   => $total->TT_NVX_CLIENTS_TOUS_PAYS_tchat,
				'Total Credits ou secondes depensees'   => $total->Total_secondes_depensees ,
				  'Total Credits ou achetes'   => $total->Total_credits_achetes,
				   'Total Transaction'   =>   $total->Total_transac_tous_pays ,
				'Total Credits ou achetes France'   => $total->Total_credits_achetes_france ,
				'Total Transaction France'   => $total->Total_transac_france,
				'Total Credits ou achetes Belgique'   => $total->Total_credits_achetes_belgique ,
				'Total Transaction Belgique'   => $total->Total_transac_belgique ,
				'Total Credits ou achetes Suisse'   => $total->Total_credits_achetes_suisse ,
				'Total Transaction Suisse'   => $total->Total_transac_suisse ,
				'Total Credits ou achetes Luxembourg'   => $total->Total_credits_achetes_luxembourg ,
				'Total Transaction Luxembourg'   => $total->Total_transac_luxembourg ,
				'Total Credits ou achetes Canada'   => $total->Total_credits_achetes_canada ,
				'Total Transaction Canada'   => $total->Total_transac_canada
            );
			  fputcsv($fp, array_values($line_total), ';', '"');
			  $line_separation = array(
                'DATE'      => '',
                'HEURE'      => '',
                'TT TEL PREPAYE France'   => '',
				'TT TEL PREPAYE Belgique'   => '',
				'TT TEL AUDIOTEL Belgique'   => '',
				'TT TEL PREPAYE Suisse'   => '',
				'TT TEL AUDIOTEL Suisse'   => '',
				'TT TEL PREPAYE Luxembourg'   => '',
				'TT TEL AUDIOTEL Luxembourg'   => '',
				'TT TEL PREPAYE Canada'   => '',
				'TT TEL AUDIOTEL Canada'   => '',
				'TT Email France'   => '',
				'TT Email Belgique'   => '',
				'TT Email Suisse'   => '',
				'TT Email Luxembourg'   => '',
				'TT Email Canada'   => '',
				'TT Tchat France'   => '',
				'TT Tchat Belgique'   => '',
				'TT Tchat Suisse'   => '',
				'TT Tchat Luxembourg'   => '',
				'TT Tchat Canada'   => '',
				'TT tous pays et tous types Téléphone'   => '',
				'TT tous pays Email'   => '',
				'TT tous pays Tchat'   => '',
				'TOTAL TOUS TYPES CONSULTS'   => '',
				'ANCIENS CLIENTS France'   => '',
				'NVX CLIENTS France'   => '',
				'NVX CLIENTS France Phone prepaye'   => '',
				'NVX CLIENTS France Email'   => '',
				'NVX CLIENTS France Tchat'   => '',
				'ANCIENS CLIENTS Belgique'   => '',
				'NVX CLIENTS Belgique'   => '',
				'NVX CLIENTS Belgique Phone'   => '',
				'NVX CLIENTS Belgique Phone prepaye'   => '',
				'NVX CLIENTS Belgique Phone audiotel'   => '',
				'NVX CLIENTS Belgique Email'   => '',
				'NVX CLIENTS Belgique Tchat'   => '',
				'ANCIENS CLIENTS Suisse'   => '',
				'NVX CLIENTS Suisse'   => '',
				'NVX CLIENTS Suisse Phone'   => '',
				'NVX CLIENTS Suisse Phone prepaye'   => '',
				'NVX CLIENTS Suisse Phone audiotel'   => '',
				'NVX CLIENTS Suisse Email'   => '',
				'NVX CLIENTS Suisse Tchat'   => '',
				'ANCIENS CLIENTS Luxembourg'   => '',
				'NVX CLIENTS Luxembourg'   => '',
				'NVX CLIENTS Luxembourg Phone'   => '',
				'NVX CLIENTS Luxembourg Phone prepaye'   => '',
				'NVX CLIENTS Luxembourg Phone audiotel'   => '',
				'NVX CLIENTS Luxembourg Email'   => '',
				'NVX CLIENTS Luxembourg Tchat'   => '',
				'ANCIENS CLIENTS Canada'   => '',
				'NVX CLIENTS Canada'   => '',
				'NVX CLIENTS Canada Phone'   => '',
				'NVX CLIENTS Canada Phone prepaye'   => '',
				'NVX CLIENTS Canada Phone audiotel'   => '',
				'NVX CLIENTS Canada Email'   => '',
				'NVX CLIENTS Canada Tchat'   => '',
				'NBRE TT CLIENTS TOUS PAYS'   => '',
				'TT ANCIENS CLIENTS TOUS PAYS'   => '',
				'TT NVX CLIENTS TOUS PAYS'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone prepaye'   => '',
				'TT NVX CLIENTS TOUS PAYS Phone audiotel'   => '',
				'TT NVX CLIENTS TOUS PAYS Email'   => '',
				'TT NVX CLIENTS TOUS PAYS Tchat'   => '',
				'Total Credits ou secondes depensees'   => '',
			  'Total Credits ou secondes achetees'   => '',
			  'Total Transaction'   =>   '',
				'Total Credits ou achetes France'   => '',
				'Total Transaction France'   => '',
				'Total Credits ou achetes Belgique'   => '',
				'Total Transaction Belgique'   => '',
				'Total Credits ou achetes Suisse'   => '',
				'Total Transaction Suisse'   => '',
				'Total Credits ou achetes Luxembourg'   => '',
				'Total Transaction Luxembourg'   => '',
				'Total Credits ou achetes Canada'   => '',
				'Total Transaction Canada'   => '' );

			  	fputcsv($fp, array_values($line_separation), ';', '"');

        fclose($fp);
		//$mysqli->close();
        $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
    }
    /**
     * Permet l'édition d'un user par un admin
     *
     * @param $id           integer L'id de l'user
     * @param $fieldsForm   array Les champs qui doivent être présents dans le formulaire
     * @param $fields       array Les champs requis du formulaire
     * @param $model        string Le nom du model
     *
     * @return mixed Retourne les datas de l'user
     */
    public function _adminEdit($id, $fieldsForm, $fields, $model){

        $template = array(
            'Account' => array(
                'controller' => 'accounts',
                'msgFlashSuccess' => 'Le client a été modifié.',
                'msgFlashError' => 'Erreur dans la modification du client.',
                'role' => 'client'
            ),
            'Agent' => array(
                'controller' => 'agents',
                'msgFlashSuccess' => 'L\'agent a été modifié.',
                'msgFlashError' => 'Erreur dans la modification de l\'agent.',
                'role' => 'agent'
            )
        );

        if($this->request->is('post') || $this->request->is('put')){
            //On vérifie les champs du formulaire
            $this->request->data[$model] = Tools::checkFormField($this->request->data[$model], $fieldsForm, $fields);
            if($this->request->data[$model] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
            }

            //Vérification sur l'adresse mail
            if(!filter_var($this->request->data[$model]['email'], FILTER_VALIDATE_EMAIL)){
                $this->Session->setFlash(__('Email invalide.'),'flash_error');
                $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
            }

            //Test email unique
            if($this->request->data[$model]['email'] !== $this->User->field('email', array('id' => $id))
                &&
                !$this->User->singleEmail($this->request->data[$model]['email'], $template[$model]['role'])
            ){
                $this->Session->setFlash(__('Cet email est déjà enregistré'),'flash_warning');
                $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
            }

            //S'il n'y a pas de modification du mot de passe
            if(empty($this->request->data[$model]['passwd']))
                unset($this->request->data[$model]['passwd']);
            else{
                if(strlen($this->request->data[$model]['passwd']) < 8){
                    $this->Session->setFlash(__('Le mot de passe doit faire 8 caractères au minimum.'),'flash_warning');
                    $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                }elseif(strcmp($this->request->data[$model]['passwd'],$this->request->data[$model]['passwd2']) != 0){
                    $this->Session->setFlash(__('Les mots de passe sont différents.'),'flash_warning');
                    $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                }
                else
                    $this->request->data[$model]['passwd'] = $this->hashMDP($this->request->data[$model]['passwd']);
            }

            //Si modification d'un client
            if($model === 'Account'){
                //Num téléphone
                if(isset($this->request->data[$model]['phone_number']) && !empty($this->request->data[$model]['phone_number'])){
                    //On vérifie le numero de téléphone
                    $this->request->data[$model]['phone_number'] = $this->phoneNumberValid($this->request->data[$model]['phone_number'], 10);
                    if($this->request->data[$model]['phone_number'] === false)
                        $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                }
				
				//S'il y a plus de credit
                if(!isset($this->request->data[$model]['credit']) || !$this->request->data[$model]['credit']){
                    //Valeur négative ou égale à 0
                    $this->request->data[$model]['credit'] = 0;
                }
            }

            //Si modification d'un agent
            if($model === 'Agent'){
				$this->request->data[$model]['langs'] = str_replace('1','1,8,10,11,12',$this->request->data[$model]['langs']);
				$this->request->data[$model]['langs'] = implode(',',$this->request->data[$model]['langs']);
				$this->request->data[$model]['countries'] = implode(',',$this->request->data[$model]['countries']);

				$this->request->data[$model]['vat_num'] = $this->request->data[$model]['vat_num_spirit'];
				unlink($this->request->data[$model]['vat_num_spirit']);

				$dataCategories = array();
				$this->loadModel('CategoryUser');
				foreach ($this->request->data['Agent']['categories'] as $value){
					$dataCategories[] = array('CategoryUser' => array('user_id' => $id, 'category_id' => $value));
            	}
				$this->CategoryUser->deleteAll(array('user_id' => $id), false);
                $this->CategoryUser->saveMany($dataCategories);
                unset($this->request->data['Agent']['categories']);


                //Si l'un des champs n'est pas alphanumerique
                if(isset($this->request->data[$model]['pseudo']) && !ctype_alnum(str_replace('-', '', str_replace(' ', '',$this->request->data[$model]['pseudo'])))){
                    $this->Session->setFlash(__('Le champ "Pseudo" n\'accepte que les caractères alphanumériques.'),'flash_warning');
                    $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                }

                //S'il y a le numéro de téléphone
                if(isset($this->request->data[$model]['phone_number'])){
                    $this->request->data[$model]['phone_number'] = $this->checkPhoneNumber($this->request->data[$model]['phone_number'],9, $id);
                    if(!$this->request->data[$model]['phone_number'])
                        $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);

                    //Vérifie si le numéro à changer
                    $samePhone = $this->User->phoneNumberCmp($id, $this->request->data[$model]['phone_number']);
                    //Si le numéro est différent, on le met à jour sur l'api
                    if($samePhone !== 0 && $samePhone !== false){
                        $user = $this->User->find('first', array(
                            'fields'        => array('active', 'valid'),
                            'conditions'    => array('User.id' => $id),
                            'recursive'     => -1
                        ));

                        //Si l'user est active
                        if(!empty($user) && $user['User']['active'] == 1 && $user['User']['valid'] == 1){
                            //On update l'agent au niveau de l'api
                            $api = new Api();
                            $result = $api->updateAgent($this->User->field('agent_number', array('id' => $id)), $this->request->data[$model]['phone_number']);

                            //S'il y a eu une erreur
                            if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0)){
                                $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Echec de la mise à jour de l\'agent.')),'flash_warning');
                                $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                            }
                        }
                    }
                }

                //S'il y a le champ creditMail
                if(isset($this->request->data[$model]['creditMail'])){
                    //Valeur négative ou égale à 0
                    if($this->request->data[$model]['creditMail'] < 0)
                        $this->request->data[$model]['creditMail'] = null;
                    elseif($this->request->data[$model]['creditMail'] === '0'){
                        $this->Session->setFlash(__('Le nombre de crédits pour un mail ne peut être égal à zéro.'),'flash_warning');
                        $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'edit', 'admin' => true, 'id' => $id),false);
                    }
                }

				//PATCH photo agent
				$checkModif = array('photo' => false);

				$media = array(
							'photo' => array(
								'format' => 'Image',
								'formatError' => 'Le fichier image n\'est pas dans un bon format.',
								'uploadError' => 'Erreur dans le chargement de votre photo.',
								'sizeError' => 'Le fichier image est trop volumineux',
								'path' => 'Site.pathPhotoValidation',
								'extension' => 'jpg'
							)
				);
				//On ajoute le code de l'agent dans les datas

				$this->request->data[$model]['agent_number'] = $this->User->field('agent_number', array('id' => $id));

				//pour chaque media on check si tout est bien
				foreach ($media as $format => $file){
					//Si le fichier a été téléchargé correctement

					if($this->isUploadedFile($this->request->data[$model][$format])){
						if(!Tools::formatFile($this->allowed_mime_types,$this->request->data[$model][$format]['type'], $file['format'])){
							$this->Session->setFlash(__($file['formatError']),'flash_warning');
							$this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
						}
						$checkModif[$format] = true;
					}elseif($this->request->data[$model][$format]['size'] > 0){    //Si un fichier a bien été téléchargé (partiellement ou complètemenent) mais que isUploadedFile retourne false alors erreur
						$this->Session->setFlash(__($file['uploadError']),'flash_error');
						$this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
					}
					elseif($this->request->data[$model][$format]['error'] == 1){   //Taille du fichier plus grande que celle de la conf php.   Voir php.ini
						$this->Session->setFlash(__($file['sizeError']),'flash_warning');
						$this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
					}elseif($this->request->data[$model][$format]['error'] != 4 && $this->request->data[$model][$format]['error'] != 0) {  //Sinon autre erreur
						$this->Session->setFlash(__($file['uploadError']),'flash_error');
						$this->redirect(array('controller' => 'agents', 'action' => 'profil', 'tab' => 'media'));
					}
				}


				//On save les modifications
				$validation_media = false; $inscription_media = false;
				if(!$this->request->data[$model]['agent_number']) $inscription_media = true;
				foreach ($checkModif as $format => $val){
					if($val){
						//On supprime un éventuel fichier en attente
						$file = new File(Configure::read($media[$format]['path']).'/'.$this->request->data[$model]['agent_number'][0].'/'.$this->request->data[$model]['agent_number'][1].'/'.$this->request->data[$model]['agent_number'].'.'.$media[$format]['extension']);
						if($file->exists()){
							$file->delete();
							//La photo listing
							if(strcmp($format, 'Image') == 0){
								$file = new File(Configure::read($media[$format]['path']).'/'.$this->request->data[$model]['agent_number'][0].'/'.$this->request->data[$model]['agent_number'][1].'/'.$this->request->data[$model]['agent_number'].'_listing.'.$media[$format]['extension']);
								if($file->exists()) $file->delete();
							}
						}
						//On save le fichier
						$this->saveFile($this->request->data[$model], $format,$validation_media, $inscription_media,$id);
						//$this->Session->setFlash('La modification de votre présentation est en attente de validation', 'flash_success');
					}
				}

				//save presentation
				$this->loadModel('UserPresentLang');
				//Si oui
				if($this->request->data[$model]['lang_id'] == 8 || $this->request->data[$model]['lang_id'] == 9 || $this->request->data[$model]['lang_id'] == 10 || $this->request->data[$model]['lang_id'] == 11 || $this->request->data[$model]['lang_id'] == 12)
				$this->request->data[$model]['lang_id'] = 1;

				if($this->UserPresentLang->hasPresentation($id,$this->request->data[$model]['lang_id'])){
					//On unbind les associations
					$this->UserPresentLang->unbindModel(array('hasOne' => array('User','Lang')));
					//On update le champ texte
					$this->UserPresentLang->updateAll(
						array(
							'texte' => $this->UserPresentLang->value(htmlentities($this->request->data[$model]['texte'])),
							'date_upd' => $this->UserPresentLang->value(date('Y-m-d H:i:s'))
						),
						array(
							'user_id' => $id,
							'lang_id' => $this->request->data[$model]['lang_id']
						));
				}


				//refresh pseudo
				$dbb_patch = new DATABASE_CONFIG();
				$dbb_connect = $dbb_patch->default;
				$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
				$agent_id = $id;
				$agent_pseudo = addslashes($this->request->data[$model]['pseudo']);
				$mysqli_connect->query("update agent_pseudos set pseudo = '{$agent_pseudo}' WHERE user_id = '{$agent_id}'");
				$mysqli_connect->close();

            }

			//$this->request->data[$model]['date_upd'] = date('Y-m-d H:i:s');
			//$this->request->data[$model]['update'] = true;
			$this->request->data[$model]['id'] = $id;
            $this->User->id = $id;




            if($this->User->save($this->request->data[$model]))
                $this->Session->setFlash(__($template[$model]['msgFlashSuccess']),'flash_success');
            else
                $this->Session->setFlash(__($template[$model]['msgFlashError']),'flash_error');
        }

        $user = $this->User->find('first',array(
            'conditions' => array('id' => $id, 'role' => $template[$model]['role']),
            'recursive' => -1
        ));

        //Si aucun user retourné, redirection sur listing user
        if(empty($user)){
            $this->redirect(array('controller' => $template[$model]['controller'], 'action' => 'index', 'admin' => true),false);
        }
        //Pour remplir le formulaire automatiquement
        $this->request->data[$model] = $user['User'];
        //On unset le password
        unset($this->request->data[$model]['passwd']);
		if($model == 'Agent')$this->request->data[$model]['vat_num_spirit'] = $this->request->data[$model]['vat_num'];
        //On récupère la liste des pays
        $this->loadModel('UserCountry');
        $this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));

        return $user;
    }

    /**
     * Permet de générer les données pour l'onglet Infos
     *
     * @param array $countries Liste des pays
     * @param array $langs  Liste des langues
     * @param array $countries_agent  Liste des pays correspondant aux domaines
     * @return mixed
     */
    public function _ongletInfos($countries,$langs, $countries_agent){
        $data['donnees'] = $this->User->find('first',array(
            'fields' => array(
                'id','firstname', 'lastname', 'pseudo', 'email', 'birthdate', 'address', 'consult_chat', 'sexe', 'consult_chat',
                'postalcode', 'city', 'country_id', 'countries', 'langs', 'siret','society_type_id', 'societe_statut','vat_num','rib', 'bank_name','bank_address', 'bank_country', 'iban', 'swift','societe','paypal','mode_paiement', 'consult_email', 'consult_phone', 'phone_number', 'phone_number2','phone_mobile','phone_operator','societe_adress','societe_adress2','societe_cp','societe_ville','societe_pays','mail_infos','belgium_save_num','belgium_society_num','canada_id_hst','spain_cif','luxembourg_autorisation','luxembourg_commerce_registrar','marocco_ice','marocco_if','portugal_nif','senegal_ninea','senegal_rccm','tunisia_rc','invoice_vat_id','country_id'),
            'conditions' => array('User.id' => $this->Auth->user('id')),
            'recursive' => -1
        ));



        //Mise en forme des données pour l'affichage du tableau profil
        $data['userDatas'] = $this->initAgentDatas('User',$data['donnees'],
            $countries[$data['donnees']['User']['country_id']],
            $this->idCountryInFlag($data['donnees']['User']['countries'],$countries_agent),
            $this->idLangInFlag($data['donnees']['User']['langs'],$langs));

        return $data;
    }

    /**
     * Permet de générer les données pour l'onglet Modifier profil
     *
     * @param array $countries Liste des pays
     * @param array $donnees    Les données de l'agent
     */
    public function _ongletDatas($countries,$donnees){
        $idEdit = $this->UserValidation->enAttente($this->Auth->user('id'));
        //S'il y a des modifications en attente
        if($idEdit !== false){
            $dataValidations = $this->UserValidation->find('first', array(
                'conditions' => array('id' => $idEdit),
                'recursive' => -1
            ));

            //Données en attente
            $userDataValidations = $this->initAgentDatas('UserValidation',$dataValidations,$countries[$dataValidations['UserValidation']['country_id']]);
            $this->set('userDataValidations', $userDataValidations);


            $this->request->data['Agent'] = $dataValidations['UserValidation'];
            //Unset ce qui doit être unset
            unset($dataValidations['UserValidation']);
            unset($this->request->data['User']['admin_id']);
            unset($this->request->data['User']['etat']);
            unset($this->request->data['User']['users_id']);

            $this->set('modification', true);
        }else{  //Pas de modification en attente
            $this->request->data['Agent'] = $donnees['User'];
            $this->set('modification', false);
        }

        //On récupère l'indicatif tel et le numéro de tel sans l'indicatif
        $agentPhone = $this->Country->getIndicatifOfPhone($this->request->data['Agent']['phone_number']);
        $this->request->data['Agent']['indicatif_phone'] = $agentPhone['indicatif'];
        $this->request->data['Agent']['phone_number'] = $agentPhone['phone_number'];


        //On récupère l'indicatif tel et le numéro de tel 2 sans l'indicatif
        $agentPhone = $this->Country->getIndicatifOfPhone($this->request->data['Agent']['phone_number2']);
        $this->request->data['Agent']['indicatif_phone2'] = $agentPhone['indicatif'];
        $this->request->data['Agent']['phone_number2'] = $agentPhone['phone_number'];
		
		//On récupère l'indicatif tel et le numéro de tel 2 sans l'indicatif
        $agentPhone = $this->Country->getIndicatifOfPhone($this->request->data['Agent']['phone_mobile']);
        $this->request->data['Agent']['indicatif_mobile'] = $agentPhone['indicatif'];
        $this->request->data['Agent']['phone_mobile'] = $agentPhone['phone_number'];
    }

    /**
     * Permet de générer les options pour l'onglet Options
     *
     * @param array $donnees Les données de l'agent
     * @return mixed
     */
    public function _ongletOptions($donnees){
        //On récupère les univers
        $data['univers'] = $this->CategoryUser->find('all', array(
            'fields' => array('category_id'),
            'conditions' => array('user_id' => $this->Auth->user('id')),
            'recursive' => -1
        ));

        foreach($data['univers'] as $k => $value){
            $data['univers'][$k] = $data['univers'][$k]['CategoryUser']['category_id'];
        };

        //Init le champ consult

        $data['consult'] = array();
        //0 : Email     1 : Téléphone       2 : Chat
        $checkConsult = array('consult_email', 'consult_phone', 'consult_chat');
        foreach ($checkConsult as $k => $val){
            if($donnees['User'][$val] == 1)
                array_push($data['consult'],$k);
        }

        //On transforme les strings de "countries" et "langs" an array(int)
        $this->request->data['Agent']['countries'] = explode(',', $donnees['User']['countries']);
        $this->request->data['Agent']['langs'] = explode(',', $donnees['User']['langs']);

        return $data;
    }

    protected function _answerMail($model, $controller){
        //Voir si on peut rassembler Account et Agent.
    }

    /**
     * Permet de récupérer le formulaire pour répondre à un mail
     *
     * @param $idUser       int     L'id de l'user
     * @param $controller   string  Le nom du controller
     */
    protected function _answerForm($idUser, $controller){
        if($this->request->is('ajax')){



            //Check l'id du mail
            if(!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
                $this->jsonRender(array('return' => false));
            $idMail = $this->request->data['id_mail'];
            $this->loadModel('Message');
            //On récupère la conversation
            $conversation = $this->Message->find('first',array(
                'conditions' => array('Message.id' => $idMail, 'Message.archive' => 0),
                'recursive' => -1
            ));


			//check si voyant toujours dispo
			$this->loadModel('User');
			$agent = $this->User->find('first', array(
				'conditions'    => array('User.id' => $conversation['Message']['to_id']),
				'recursive'     => -1
			));
			
			$sender = $this->User->find('first', array(
				'conditions'    => array('User.id' => $conversation['Message']['from_id']),
				'recursive'     => -1
			));

			if(!$agent['User']['active']){
				return '';
			}
			
			$canActive = true;
			if(!$sender['User']['active']){
				$canActive = false;
			}

            /* On check si le destinataire du message (en dehors du client) est un admin */
                $ids = array($conversation['Message']['from_id'], $conversation['Message']['to_id']);
                foreach ($ids AS $k => $v)
                    if ($v == $this->Session->read('Auth.User.id'))
                        unset($ids[$k]);
                $other_user_than_me = $ids[array_keys($ids)[0]];
                $this->User->id = $other_user_than_me;
                $other_user_than_me =  $this->User->read();
                $showHourAnswerAlert = ($other_user_than_me['User']['role'] == 'admin')?0:1;
                $otherThanMeIsAdmin = ($other_user_than_me['User']['role'] == 'admin')?1:0;
                $this->set(compact('showHourAnswerAlert','otherThanMeIsAdmin'));

            //Si pas de conversation ou si la conversation ne lui est pas destiné
            if(empty($conversation) || ($conversation['Message']['from_id'] != $idUser && $conversation['Message']['to_id'] != $idUser))
                $this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => $controller, 'action' => 'mails'),true)));

            //Si c'est l'agent qui répond
            if(strcmp($controller, 'agents') == 0)
                $idAgent = $conversation['Message']['from_id'];
            else
                $idAgent = $conversation['Message']['to_id'];

            //Le nombre de crédit pour recevoir une réponse ou envoyer une question
            $creditMail = $this->User->field('creditMail', array('id' => $idAgent));
            $creditMail = (empty($creditMail) ?Configure::read('Site.creditPourUnMail'):$creditMail);

            $canPost = true;
			$canAnswer = true;
			$isDeprecated = false;

            if ($controller !== 'agents' && $conversation['Message']['private'] != 1){
                /* client */
                $credit_now = $this->Session->read('Auth.User.credit');
                $canPost = ($credit_now >= $creditMail)?true:false;
				if($agent['User']['agent_status'] == 'unavailable' || !$agent['User']['consult_email']){
					$canAnswer = false;
				}
            }
			$canReply = true;

			if ($controller !== 'agents' && $conversation['Message']['private'] == 1){
				$envoyer = $this->User->find('first', array(
					'conditions'    => array('User.id' => $conversation['Message']['from_id']),
					'recursive'     => -1
				));

				$role_envoyer  =$envoyer['User']['role'];
				if($role_envoyer == 'client'){
					$messages = $this->Message->find('all',array(
						'conditions' => array('Message.to_id' => $conversation['Message']['to_id'],'Message.from_id' => $conversation['Message']['from_id'],'Message.private' => 1, 'Message.date_add >' => date('Y-m-d 00:00:00')),
					));
					if(count($messages)>= 2)
					$canReply = false;
				}

            }
			if(is_array($conversation['LastMessage'])){
				if($conversation['LastMessage']['etat'] == 3){
					$isDeprecated = true;
				}
			}else{
				if($conversation['Message']['etat'] == 3){
					$isDeprecated = true;
				}
			}



			if ($controller == 'agents' && $conversation['Message']['private'] == 1){

					$dx = new DateTime(date('Y-m-d H:i:s'));
					$dx->modify('- 30 day');
					$delai = $dx->format('Y-m-d H:i:s');
					$comp = $dx->format('Ym-d');
					if($comp <= '20190317'){
						$messages = $this->Message->find('all',array(
							'conditions' => array('Message.to_id' => $conversation['Message']['from_id'],'Message.from_id' => $conversation['Message']['to_id'],'Message.private' => 1, 'Message.date_add >' => date('Y-m-d 00:00:00')),
						));
					}else{
						$messages = $this->Message->find('all',array(
							'conditions' => array('Message.to_id' => $conversation['Message']['from_id'],'Message.from_id' => $conversation['Message']['to_id'],'Message.private' => 1, 'Message.date_add >' => $delai),
						));
					}

					if(count($messages)>= 1)
						$canReply = false;

            }
            $this->layout = '';
            $this->set(array('canpost' => $canPost,'canreply' => $canReply, 'canAnswer' => $canAnswer,'isDeprecated' => $isDeprecated, 'model' => ($controller === 'agents' ?'Agent':'Account'), 'idMail' => $idMail, 'creditMail' => $creditMail, 'private' => $conversation['Message']['private'], 'to_id' => $conversation['Message']['to_id'], 'mail_content' => $conversation['Message']['content'],'canActive' => $canActive));
            $response = $this->render('/Elements/answer_mail');

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }
    }

    /**
     * Récupère les messages pour une conversation donnée
     *
     * @param $idUser       int     L'id de l'user pour qui on récupère la conversation
     * @param $controller   string  Le nom du controller en question
     */
    protected function _readMail($idUser, $controller){
        if($this->request->is('ajax')){
            //Check l'id du mail
            if(!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
                $this->jsonRender(array('return' => false));
            $idMail = $this->request->data['id_mail'];
            $this->loadModel('Message');
            //On récupère la conversation
            $tmp_conversation = $this->Message->find('threaded',array(
                'conditions' => array(
                    'OR' => array(
                        array('Message.id' => $idMail),//, 'Message.etat != ' => 2
                        array('Message.parent_id' => $idMail)
                    )
                )
            ));

            //Si pas de conversation ou si la conversation ne lui est pas destiné
            if(empty($tmp_conversation) || ($tmp_conversation[0]['Message']['from_id'] != $idUser && $tmp_conversation[0]['Message']['to_id'] != $idUser))
                $this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => $controller, 'action' => 'mails'),true)));

            //Restructurer les messages
            //Le nom expediteur et destinataire
            $from = $this->displayName($tmp_conversation[0]['From']['role'], $tmp_conversation[0]['Message']['from_id'], $idUser, $tmp_conversation[0]['From']['pseudo'], $tmp_conversation[0]['From']['firstname']);
            $to = $this->displayName($tmp_conversation[0]['To']['role'], $tmp_conversation[0]['Message']['to_id'], $idUser, $tmp_conversation[0]['To']['pseudo'], $tmp_conversation[0]['To']['firstname']);

            //Le 1er message le plus ancien
            $conversation[0] = array(
                'from_id' => $tmp_conversation[0]['Message']['from_id'],
                'to_id' => $tmp_conversation[0]['Message']['to_id'],
                'content' => $tmp_conversation[0]['Message']['content'],
                'date' => $tmp_conversation[0]['Message']['date_add'],
                'attachment' => $tmp_conversation[0]['Message']['attachment'],
				'attachment2' => $tmp_conversation[0]['Message']['attachment2'],
                'from' => $from,
                'to'  => $to,
				'etat'  => $tmp_conversation[0]['Message']['etat']
            );

			$sender_email = $tmp_conversation[0]['Message']['from_id'];
			$recever_email = '';

            //Les autres messages
            foreach($tmp_conversation[0]['children'] as $mail){
                //Le nom expediteur et destinataire
                $from = $this->displayName($mail['From']['role'], $mail['Message']['from_id'], $idUser, $mail['From']['pseudo'], $mail['From']['firstname']);
                $to = $this->displayName($mail['To']['role'], $mail['Message']['to_id'], $idUser, $mail['To']['pseudo'], $mail['To']['firstname']);
				$look = 1;
				if($idUser == $mail['Message']['to_id'] && $mail['Message']['etat'] == 2) $look = 0 ;

				if($look)
                array_push($conversation, array(
                    'from_id' => $mail['Message']['from_id'],
                    'to_id' => $mail['Message']['to_id'],
                    'content' => $mail['Message']['content'],
                    'date' => $mail['Message']['date_add'],
                    'attachment' => $mail['Message']['attachment'],
					'attachment2' => $mail['Message']['attachment2'],
                    'from' => $from,
                    'to'  => $to,
					'etat'  => $mail['Message']['etat']
                ));
				$recever_email = $mail['Message']['from_id'];
            }

			if($recever_email == $sender_email || !$recever_email)$is_in_live = true; else $is_in_live = false;

            $this->layout = '';
            $this->set(compact('conversation', 'controller', 'is_in_live'));
            $response = $this->render('/Elements/read_mail');

            //Indiquer que le message est lu s'il est le destinataire
            if(end($conversation)['to_id'] == $idUser){
                $this->Message->updateAll(array('Message.etat' => 1),array(
					'Message.etat' => 0,
                    'OR' =>  array(
                        array('Message.id' => $idMail),
                        array('Message.parent_id' => $idMail)
                    )
                ));
            }

            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }
    }

    /**
     * Permet d'obtenir les discussion d'un onglet
     *
     * @param $controller
     */
    public function _getMails($controller){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(isset($requestData['isAjax']) && $requestData['isAjax']){
                $this->loadModel('Message');
                //Selon le type d'email demandé
                $id = $this->Auth->user('id');

                switch($requestData['param']){
                    case 'message' :
                        //Uniquement les messages de consultations
                        $mails = $this->Message->getDiscussion($id);
                        $param = 'message';
                        break;
                    case 'private' :
                        //Uniquement les messages privés
                        $mails = $this->Message->getDiscussion($id, false, false, true);
                        $param = 'private';
                        break;
                    case 'archive' :
                        //Uniquement les messages archivés
                        $mails = $this->Message->getDiscussion($id, false, true, false);
                        $param = 'archive';
                        //Message privé ou pas
                        if(isset($requestData['archive'])){
                            $typeArchive = ($requestData['archive'] === 'private' ?0:1);
                            foreach($mails as $indice => $mail){
                                if($mail['Message']['private'] == $typeArchive || $mail['Message']['private'] == 2)
                                    unset($mails[$indice]);
                            }
                        }
                        break;
                    default :
                        //Par défaut les messages de consultations
                        $mails = $this->Message->getDiscussion($id);
                        $param = 'message';
                }

                //On crée les différentes pages
                $pages = array_chunk($mails, Configure::read('Site.limitMessagePage'));

                $page = 0;
                if(isset($this->request['page']))
                    $page = $this->request['page']-1;

                if(isset($pages[$page]))
                    $mails = $pages[$page];
                else
                    $mails = array();

                $this->layout = '';

                $this->set(compact('mails', 'id'));
                if(isset($requestData['onlyBlockMail']))
                    $this->set('onlyBlockMail', true);
                $this->set(array('controller' => $controller));
                $response = $this->render('/Elements/mails');

                $this->jsonRender(array('return' => true, 'html' => $response->body(), 'param' => $param));
            }

            $this->jsonRender(array('return' => false));
        }
    }

    protected function _update_mail(){
        if($this->request->is('ajax')){
            $requestData = $this->request->data;

            if(!isset($requestData['user']) || empty($requestData['user']))
                $this->jsonRender(array('return' => false));

            $this->loadModel('Message');
            //Des messages de consultation non lu ??
            $data['mailConsult'] = ($this->Message->hasNoReadMail($requestData['user']) > 0 ?true:false);
            //Des messages privés non lu ??
            $data['mailPrivate'] = ($this->Message->hasNoReadMail($requestData['user'], true) > 0 ?true:false);

            $this->jsonRender(array('return' => true, 'data' => $data));
        }
    }


    /**
     * Permet de télécharger les pièces jointes
     *
     * @param string    $name   Le nom de la pièce jointe à téléchargé.
     * @return mixed
     */
    protected function _downloadAttachment($name){
        //Si pas de nom, redirection mails
        if(empty($name)){
            $this->Session->setFlash(__('Le fichier est introuvable.'), 'flash_warning');
            $this->redirect(array('action' => 'mails'));
        }

        //Est-ce que le fichier existe ??
        $filename = Configure::read('Site.pathAttachment').'/'.$name[0].'/'.$name[1].'/'.$name;
        if(file_exists($filename)){
            //Charge le model
            $this->loadModel('Message');
            //Est-il autorisé à lire cette pièce jointe ??
			$name = str_replace('-2-','-',$name);
            $infoFile = explode('-', $name);
            $idMail = explode('.', $infoFile[1]);
            if($infoFile[0] === $this->Auth->user('agent_number') || $this->Message->myDiscussion($idMail[0], $this->Auth->user('id'))){
                $this->response->file($filename, array('download' => true, 'name' => __('Pièce jointe').'.jpg'));
                return $this->response;
            }
        }
        $this->Session->setFlash(__('Le fichier n\'existe pas.'), 'flash_warning');
        $this->redirect(array('action' => 'mails'));
    }

    /**
     * Permet d'afficher le nom de l'expéditeur/destinataire pour les messages internes
     *
     * @param $role         string  Le role du destinataire ou expediteur
     * @param $id           int     L'id du destinataire ou expediteur
     * @param $idUser       int     L'id de l'expediteur ou destinataire
     * @param $pseudo       string  Le pseudo si c'est un agent
     * @param $firstname    string  Le nom si c'est un client
     * @return string
     */
    public function displayName($role, $id, $idUser, $pseudo, $firstname){
        switch ($role){
            case 'admin' :
                return ($id == $idUser ?'Moi':Configure::read('Site.name'));
                break;
            case 'agent' :
                return ($id == $idUser ?'Moi':$pseudo);
                break;
            case 'client' :
                return ($id == $idUser ?'Moi':$firstname);
                break;
            default :
                return '';
                break;
        }
    }

    /**
     * Permet de générer les datas pour l'onglet Media
     *
     * @param array $data Les données de l'agent
     */
    public function _ongletPresentations($data){
        //On regarde si l'agent a bien une photo
        $this->set('namePhoto', $this->mediaAgentExist($data['User']['agent_number'],'Image'));
        //Une photo en attente de validation
        $namePhotoValidation = $this->mediaAgentExist($data['User']['agent_number'],'Image', true);
        if($namePhotoValidation != false)
            $this->set('namePhotoValidation', $namePhotoValidation);

        //On regarde si l'agent a une presentation audio
        $nameAudio = $this->mediaAgentExist($data['User']['agent_number'],'Audio');
        if($nameAudio != false)
            $this->set('nameAudio', $nameAudio);
        //Une présentation en attente de validation
        $nameAudioValidation = $this->mediaAgentExist($data['User']['agent_number'],'Audio', true);
        if($nameAudioValidation != false)
            $this->set('nameAudioValidation', $nameAudioValidation);

        //On regarde si l'agent a une presentation video
        $nameVideo = $this->mediaAgentExist($data['User']['agent_number'],'Video');
        if($nameVideo != false)
            $this->set('nameVideo', $nameVideo);
        //Une présentation en attente de validation
        $nameVideoValidation = $this->mediaAgentExist($data['User']['agent_number'],'Video', true);
        if($nameVideoValidation != false)
            $this->set('nameVideoValidation', $nameVideoValidation);
    }

    //Permet de lister les données de l'agent (actuelle et en attente)
    protected function initAgentDatas($model,$datas, $pays, $countries = '', $langues = ''){
        if(empty($datas)) return array();
        $userDatas = array();

        $valeurSpe = array('Date de naissance','Sexe','Pays de résidence');

        $templateDatas = array(
            'Nom' => 'lastname',
            'Prenom' => 'firstname',
            'Pseudo' => 'pseudo',
            'Email' => 'email',
            'Numéro de téléphone' => 'phone_number',
            'Opérateur téléphonique' => 'phone_operator',
            'Numéro de téléphone secondaire' => 'phone_number2',
			'Numéro de téléphone mobile' => 'phone_mobile',
            'Date de naissance' => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$datas[$model]['birthdate']), '%d %B %Y'),
            'Sexe' => ($datas[$model]['sexe'] == 1?'Homme':'Femme'),
            'Adresse' => 'address',
            'Code Postal' => 'postalcode',
            'Ville' => 'city',
            'Pays de résidence' => $pays,
            'Siret' => 'siret',
			'Statut' => 'society_type_id',
			'Statut autre' => 'societe_statut',
			'N° TVA Intracommunautaire' => 'vat_num'
        );

        //Si le model est User
        if(strcmp($model,'User') == 0){
            array_push($valeurSpe, 'Visible sur le site','Langue parlées','Consultation par téléphone', 'Consultation par mail','Consultation par chat');
            $templateDatas += array(
                'Visible sur le site' => $countries,
                'Langue parlées' => $langues,
                'Consultation par mail' => ($datas['User']['consult_email'] == 0
                        ?array('badge' => 'warning','val' => 'Non', 'glyphicon' => 'remove')
                        :array('badge' => 'success','val' => 'Oui', 'glyphicon' => 'ok')
                    ),
                'Consultation par téléphone' => ($datas['User']['consult_phone'] == 0
                        ?array('badge' => 'warning','val' => 'Non', 'glyphicon' => 'remove')
                        :array('badge' => 'success','val' => 'Oui', 'glyphicon' => 'ok')
                    ),
                'Consultation par chat' => ($datas['User']['consult_chat'] == 0
                        ?array('badge' => 'warning','val' => 'Non', 'glyphicon' => 'remove')
                        :array('badge' => 'success','val' => 'Oui', 'glyphicon' => 'ok')
                    )
            );
        }
        foreach ($templateDatas as $key => $value ){
            if(in_array($key,$valeurSpe)){
                $userDatas[$key] = $value;
            }else{
               // if(isset($datas[$model][$value]))
                    $userDatas[$key] = (isset($datas[$model][$value]))?$datas[$model][$value]:'';

            }
        }

        return $userDatas;
    }

    //Renvoye un drapeau avec les noms à la place des id pour les langues
    protected function idLangInFlag($data, $list, $delim = ','){
        if(empty($data) || empty($list)) return '';

        $arrayData = explode($delim, $data);

        $flags = '';
        foreach ($arrayData as $value){
            if(isset($list[$value]) ){
				if(key($list[$value]) != 'frc' && key($list[$value]) != 'frb' && key($list[$value]) != 'frs' && key($list[$value]) != 'frl'){
                	$flags.= '<i class="lang_flags lang_'.key($list[$value]).' img-data-agent nxtooltip" data-toggle="tooltip" title="'. __($list[$value][key($list[$value])]) .'"></i>';
                	$flags.= '<span style="display: none;">'. __($list[$value][key($list[$value])]) .'</span>';
				}
            }
        }

        return $flags;
    }

    //Renvoye un drapeau avec les noms à la place des id pour les pays
    protected function idCountryInFlag($data, $list, $delim = ','){
        if(empty($data) || empty($list)) return '';
        $arrayData = explode($delim, $data);
        $flags = '';
        foreach ($arrayData as $value){
            if(isset($list[$value])){
                $flags.= '<i class="country_flags country_'.$value.' img-data-agent nxtooltip" data-toggle="tooltip" title="'. $list[$value] .'"></i>';
                $flags.= '<span style="display: none;">'. $list[$value] .'</span>';
            }
        }
        return $flags;
    }

    //Vérifie que l'agent dispose du média entré en paramètre
    public function mediaAgentExist($agent_number,$format, $validation = false){
        $path = array('Image' => 'Site.pathPhoto', 'Audio' => 'Site.pathPresentation', 'Video' => 'Site.pathPresentationVideo');
        $pathValidation = array('Image' => 'Site.pathPhotoValidation', 'Audio' => 'Site.pathPresentationValidation', 'Video' => 'Site.pathPresentationVideoValidation');

        switch ($format){
            case 'Image' :
                $file = new File(Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'_listing.jpg');
                if($file->exists())
                    return Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'_listing.jpg';
                break;
            case 'Audio' :
                $file = new File(Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp3');
                if($file->exists())
                    return Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp3';
                break;
            case 'Video' :
                $file = new File(Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp4');
                if($file->exists())
                    return Configure::read(($validation?$pathValidation[$format]:$path[$format])).'/'.$agent_number[0].'/'.$agent_number[1].'/'.$agent_number.'.mp4';
                break;
        }
        return false;
    }

    /**
     * Permet de refuser un media (photo,audio)
     *
     * @param int       $id         L'id de l'agent
     * @param string    $url        Le chemin de l'action
     * @param string    $titleModal Le titre de la modal
     * @param array     $files      Les fichiers à supprimer
     * @param array     $form       Les données pour le formulaire
     * @param array     $email      Les données pour l'email
     */
    protected function refuseMedia($id,$url,$titleModal,$files,$form,$email){
        //On récupère la vue
        $this->getViewModal('/Elements/admin_refuse',$form,$titleModal);

        //On refuse l'entite et on envoie l'email
        if($this->request->is('post') && !empty($this->request->data)){
            //Le code agent
            $this->User->id = $id;
            $agent_number = $this->User->field('agent_number');
            //On supprime les medias
            if(Tools::deleteFile($files)){
                //L'adresse de l'agent
                $emailAgent = $this->User->field('email');
                //Les datas pour l'email
                $datasEmail = array(
                    'content' => $email['content'],
                    'motif' => $this->request->data[$form['model']]['motif'],
                    'emailAdmin' => $this->Auth->user('email')
                );
                //Envoie de l'email
                $this->sendEmail($emailAgent,$email['subject'],'admin_refuse',array('data' => $datasEmail));
                $this->Session->setFlash(__('Le fichier a été refusé. L\'email a été envoyé.'),'flash_success');
            }else
                $this->Session->setFlash(__('Erreur dans le refus du fichier.'),'flash_warning');

            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1)
                $this->redirect($url);
            else
                // Retour JSON
                $this->jsonRender(array('url' => $url));
        }
    }

	 /**
     * Permet de refuser un media (photo,audio)
     *
     * @param int       $id         L'id de l'agent
     * @param string    $url        Le chemin de l'action
     * @param string    $titleModal Le titre de la modal
     * @param array     $files      Les fichiers à supprimer
     * @param array     $form       Les données pour le formulaire
     * @param array     $email      Les données pour l'email
     */
    protected function deleteMedia($id,$url,$titleModal,$files,$form,$email){
        //On récupère la vue
        $this->getViewModal('/Elements/admin_refuse',$form,$titleModal);

        //On refuse l'entite et on envoie l'email
        if($this->request->is('post') && !empty($this->request->data)){
            //Le code agent
            $this->User->id = $id;
            $agent_number = $this->User->field('agent_number');
            //On supprime les medias
            if(Tools::deleteFile($files)){
                //L'adresse de l'agent
                $emailAgent = $this->User->field('email');
                //Les datas pour l'email
                $datasEmail = array(
                    'content' => $email['content'],
                    'motif' => $this->request->data[$form['model']]['motif'],
                    'emailAdmin' => $this->Auth->user('email')
                );
                //Envoie de l'email
                $this->sendEmail($emailAgent,$email['subject'],'admin_refuse',array('data' => $datasEmail));
                $this->Session->setFlash(__('Le fichier a été effacé. L\'email a été envoyé.'),'flash_success');
            }else
                $this->Session->setFlash(__('Erreur dans la suppression du fichier.'),'flash_warning');

            if (!isset($this->request->data['isAjax']) || (int)$this->request->data['isAjax'] != 1)
                $this->redirect($url);
            else
                // Retour JSON
                $this->jsonRender(array('url' => $url));
        }
    }


    /**
     * Permet d'accepter un media (photo,audio) pour un agent.
     *
     * @param string    $format Le nom du format (Image|Audio)
     * @param int       $id     L'id de l'agent
     * @param string    $url    Le chemin de l'action
     */
    protected function acceptMedia($format,$id,$url){
        //Le code de l'agent
        $agent_number = $this->User->field('agent_number');

        $template = array(
            'Image' => array(
                'configValidation'  => 'Site.pathPhotoValidationAdmin',
                'config'            => 'Site.pathPhotoAdmin',
                'files'             => array(
                    array('old' => $agent_number.'.jpg', 'new' => $agent_number.'.jpg'),
                    array('old' => $agent_number.'_listing.jpg', 'new' => $agent_number.'_listing.jpg')
                ),
                'emailContent'      => 'Votre nouvelle photo a été validée',
                'emailSubject'      => 'Photo validée',
                'field'             => 'has_photo',
                'cms_id'            => 193
            ),
            'Audio' => array(
                'configValidation'  => 'Site.pathPresentationValidationAdmin',
                'config'            => 'Site.pathPresentationAdmin',
                'files'             => array(array('old' => $agent_number.'.mp3','new' => $agent_number.'.mp3')),
                'emailContent'      => 'Votre nouvelle présentation audio a été validée',
                'emailSubject'      => 'Présentation audio validée',
                'field'             => 'has_audio',
                'cms_id'            => 195
            ),
            'Video' => array(
                'configValidation'  => 'Site.pathPresentationVideoValidationAdmin',
                'config'            => 'Site.pathPresentationVideoAdmin',
                'files'             => array(array('old' => $agent_number.'.mp4','new' => $agent_number.'.mp4')),
                'emailContent'      => 'Votre nouvelle présentation video a été validée',
                'emailSubject'      => 'Présentation video validée',
                'field'             => 'has_video',
                'cms_id'            => 195
            )
        );

        $oldPath = Configure::read($template[$format]['configValidation']).'/'.$agent_number[0].'/'.$agent_number[1];
        $newPath = Configure::read($template[$format]['config']).'/'.$agent_number[0].'/'.$agent_number[1];

        //On vérifie que le fichier existe bien
        if(!file_exists($oldPath.'/'.$agent_number.'.'.($format === 'Image'?'jpg':($format === 'Audio' ? 'mp3' : 'mp4')))){
            $this->Session->setFlash(__('Le fichier en attente n\'existe pas.'),'flash_warning');
            $this->redirect($url, false);
        }

        //En déplace les fichiers
        if(Tools::moveFile($oldPath, $newPath, $template[$format]['files'])){
            $this->User->id = $id;
            //On indique que l'agent a bien ce media
            $this->User->saveField($template[$format]['field'], 1);
            //L'email de l'agent
            $emailAgent = $this->User->field('email');
            //Les datas pour l'email
            $datasEmail = array('content' => $template[$format]['emailContent']);
            //Envoie de l'email
            //$this->sendEmail($emailAgent,$template[$format]['emailSubject'],'admin_accept',array('data' => $datasEmail));
            $this->sendCmsTemplateByMail($template[$format]['cms_id'], $this->User->field('lang_id'), $emailAgent, array(
                'AGENT_PSEUDO' => $this->User->field('pseudo')
            ));
            $this->Session->setFlash(__('Le fichier a été accepté. L\'email a été envoyé.'),'flash_success');
        }else
            $this->Session->setFlash(__('Erreur dans le déplacement du fichier.'),'flash_warning');

        $this->redirect($url);
    }


    /**
     * Permet d'activer ou désactiver un compte
     *
     * @param $id           int     L'id de l'user
     * @param $controller   string  Le nom du controller
     * @param $msg          array   Les différents messages pour les flash
     * @param $activation   bool    En mode activation ou désactivation de compte
     */
    protected function changeCompte($id, $controller, $msg, $activation = true){
        $this->User->id = $id;

        //Activation ou désactivation du compte
        if($this->User->changeEtatUser($id, $activation)){
            if($controller === 'agents'){
                //On récupère le code agent
                $agent_number = $this->User->field('agent_number', array(
                        'id' => $id,
                        'deleted' => 0)
                );

                //Agent inexistant
                if($agent_number === false){
                    $this->Session->setFlash(__('Agent inexistant'),'flash_warning');
                    $this->redirect(array('controller' => 'agents', 'action' => 'view', 'id' => $id, 'admin' => true),false);
                }
                //Activation de l'agent
                if($activation){
                    //S'il n'a pas de code agent (uniquement lors de la première activation)
                    if(empty($agent_number)){
                        //Save l'agent number, on le crée au niveau de l'api et déplace les médias
                        $this->firstActivate($id);
                    }else{
                        //On réactive l'agent au niveau de l'api
                        $api = new Api();
                        $result = $api->activateAgent($agent_number);

                        //S'il y a eu une erreur
                        if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0)){
                            //Il faut annuler les modifications
                            //On est dans une réactivation de compte donc il faut re désactiver le compte
                            $this->User->changeEtatUser($id, false);
                            $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Réactivation de l\'agent impossible.')),'flash_warning');
                            $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
                        }
                    }
                }else{  //Désactivation de l'agent
                    //On désactive l'agent au niveau de l'api
                    $api = new Api();
                    $result = $api->deactivateAgent($agent_number);

                    //S'il y a eu une erreur
                    if(!isset($result['response_code']) || (isset($result['response_code']) && $result['response_code'] != 0)){
                        //Il faut annuler les modifications
                        //On est dans une désactivation de compte donc il faut re activer le compte
                        $this->User->changeEtatUser($id);
                        $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Désactivation de l\'agent impossible.')),'flash_warning');
                        $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
                    }
                }
            }

            //Si on doit send un email ou pas
            if(isset($msg['email'])){

                if($controller === 'agents'){
                    if ($activation){
                        $user_infos = $this->User->find('first', array(
                                'conditions' => array(
                                    'id' => $id,
                                    'deleted' => 0
                                ),
                                'recursive' => -1,
                                'fields' => array('pseudo','email','lang_id','domain_id')
                            )
                        );

                        /* On récupère la langue */
                            $this->loadModel('Domain');
                            $this->Domain->id = (int)$user_infos['User']['domain_id'];
                            $domain = $this->Domain->field('domain');


                        /* activation compte agent */
                            $this->sendCmsTemplateByMail(204, (int)$user_infos['User']['lang_id'], $this->User->field('email'), array(
                                'AGENT_PSEUDO' => $user_infos['User']['pseudo'],
                                'AGENT_EMAIL' =>  $user_infos['User']['email'],
                                'URL_EXTRANET_AGENT' =>  'https://'.$domain.'/users/login_agent'
                            ));
                    }

                }else{
                    //L'email de l'agent
                    $emailUser = $this->User->field('email');
                    //Les datas pour l'email
                    $datasEmail = array('content' => $msg['email']);
                    //Envoie de l'email
                    $this->sendEmail($emailUser,'Compte activé','admin_accept',array('data' => $datasEmail));
                }


            }

            $this->Session->setFlash(__($msg['success']),'flash_success');
        }else
            $this->Session->setFlash(__($msg['warning']),'flash_warning');

        $this->redirect(array('controller' => $controller, 'action' => 'view', 'admin' => true, 'id' => $id),false);
    }

    /**
     * Permet de relancer un mail de confirmation
     *
     * @param $id
     * @param $controller
     * @param string $action
     */
    protected function relanceMailConfirm($id, $controller, $action = 'index'){
        //Role de l'user
        $roleUser = $this->User->field('role',array(
            'deleted' => 0,
            'role' => array('client','agent'),
            'id' => $id
        ));

        //Langue de l'user
        $lang_id = $this->User->field('lang_id',array(
            'deleted' => 0,
            'role' => array('client','agent'),
            'id' => $id
        ));

        if($action === 'index')
            $url = array('controller' => $controller, 'action' => 'index', 'admin' => true, '?' => 'email');
        else
            $url = array('controller' => $controller, 'action' => $action, 'admin' => true, 'id' => $id);


        //Si y a un role
        if($roleUser !== false){
            //template
            $template = array(
                'agent' => array(
                    'actionConfirmation' => 'confirmation_agent',
                    'keyLink' => 'an',
                    'fieldCode' => 'agent_number'
                ),
                'client' => array(
                    'actionConfirmation' => 'confirmation',
                    'keyLink' => 'pc',
                    'fieldCode' => 'personal_code'
                )
            );

            //Les infos de l'user
            $user = $this->User->find('first',array(
                'fields' => array('email',$template[$roleUser]['fieldCode'],'date_lastconnexion','domain_id'),
                'conditions' => array('id' => $id),
                'recursive' => -1
            ));

            //Paramètre pour le mail de confirmation
            $paramEmail = array(
                'email' => $user['User']['email'],
                'urlConfirmation' => $this->linkGenerator('users',$template[$roleUser]['actionConfirmation'],array(
                        $template[$roleUser]['keyLink'] => (empty($user['User'][$template[$roleUser]['fieldCode']]) ?'null':$user['User'][$template[$roleUser]['fieldCode']]),
                        'mc' => Security::hash($user['User']['email'].(empty($user['User']['date_lastconnexion']) ?'null':$user['User']['date_lastconnexion']),null,true)
                    ))
            );

			//patch lang
			$dbb_r = new DATABASE_CONFIG();
			$dbb_route = $dbb_r->default;
			$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
			$domain_id = $user['User']['domain_id'];
			$result_do = $mysqli_conf_route->query("SELECT domain from domains where id = '{$domain_id}'");
			$row_do = $result_do->fetch_array(MYSQLI_ASSOC);
			if($row_do['domain']){
				$paramEmail['urlConfirmation'] = str_replace('fr.spiriteo.com',$row_do['domain'],$paramEmail['urlConfirmation']);
			}


            if ($roleUser == 'client'){
                $this->sendCmsTemplateByMail(202, $lang_id, $user['User']['email'], array(
                    'CONFIRM_EMAIL_LINK' => $paramEmail['urlConfirmation'],
                    'EMAIL_ADDRESS'      => $paramEmail['email']
                ));
            }else{
                $this->sendCmsTemplateByMail(203, $lang_id, $user['User']['email'], array(
                    'CONFIRM_EMAIL_LINK' => $paramEmail['urlConfirmation'],
                    'EMAIL_ADDRESS'      => $paramEmail['email']
                ));
                //$this->sendEmail($user['User']['email'],'Confirmez votre adresse mail','mail_confirm',array('param' => $paramEmail));
            }
			$mysqli_conf_route->close();
            $this->Session->setFlash(__('Le mail a été envoyé.'),'flash_success');
            $this->redirect($url, false);
        }else
            $this->redirect($url, false);
    }

    /**
     * Permet de confirmer un mail. Confirmation forcée
     *
     * @param int       $id         L'id de l'user
     * @param string    $controller Le nom du controller, pour la redirection
     * @param string    $action     Le nom de l'action pour la redirection
     */
    protected function confirmMail($id, $controller, $action='index'){
        //Role de l'user
        $roleUser = $this->User->field('role',array(
            'deleted' => 0,
            'role' => array('agent','client'),
            'id' => $id
        ));

        if($action === 'index')
            $url = array('controller' => $controller, 'action' => 'index', 'admin' => true, '?' => 'email');
        else
            $url = array('controller' => $controller, 'action' => $action, 'admin' => true, 'id' => $id);

        //Si y a un role
        if($roleUser !== false){
           // $this->Session->setFlash(__('Echec de la confirmation de l\'email.'),'flash_warning');
            $this->User->id = $id;
            //On confirme l'email, si c'est bien un agent ou un client
            if(in_array($roleUser, array('client', 'agent'))){
                //Si erreur dans la confirmation de l'email
                if(!$this->User->saveField('emailConfirm', 1))
                    $this->redirect($url, false);
            }

            $this->Session->setFlash(__('L\'adresse mail a été confirmé.'),'flash_success');
            $this->redirect($url, false);
        }else
            $this->redirect($url, false);
    }

    /**
     * Lors de la première activation d'un agent, permet de le créer au niveau de l'api, et de déplacer les fichiers média
     *
     * @param $id   int L'id de l'user
     */
    private function firstActivate($id){
        //On affecte un numéro à l'agent
        $this->loadModel('AgentNumber');
        $this->loadModel('AgentPseudo');
        //On récupère quelques codes
        $codes = $this->AgentNumber->find('all',array(
            'conditions' => array('AgentNumber.used' => 0),
            'limit' => 50,
            'order' => 'rand()'
        ));
        //On sélectionne un code au hasard
        $agent_number = $codes[rand(0,count($codes)-1)]['AgentNumber']['combinaisons'];

        //On récupère le numero de tel pour l'api et son speudo
        $phone_number = $this->User->field('phone_number');
        $pseudo = $this->User->field('pseudo');

        //On crée l'agent au niveau de l'api
        $api = new Api();
        $result = $api->createAgent($agent_number, $phone_number);

        //Si tout est OK
        if(isset($result['response_code']) && $result['response_code'] == 0){
            //Le code n'est plus disponible
            $this->AgentNumber->updateAll(array('AgentNumber.used' => 1), array('AgentNumber.combinaisons' => $agent_number));
            //On save le code agent
            $this->User->saveField('agent_number', $agent_number);

			//On déplace les fichiers photos
			Tools::moveFile(Configure::read('Site.pathInscriptionMediaUpload').'/'.$id,Configure::read('Site.pathPhotoAdmin').'/'.$agent_number[0].'/'.$agent_number[1],array(
				array('old' => $id.'.jpg', 'new' => $agent_number.'.jpg'),
				array('old' => $id.'_listing.jpg', 'new' => $agent_number.'_listing.jpg')
			));
			//On déplace les fichiers présentations
			Tools::moveFile(Configure::read('Site.pathInscriptionMediaUpload').'/'.$id,Configure::read('Site.pathPresentationAdmin').'/'.$agent_number[0].'/'.$agent_number[1],array(
				array('old' => $id.'.mp3', 'new' => $agent_number.'.mp3')
			));


            //On save le pseudo dans la table AgentPseudo
            $this->AgentPseudo->create();
            $this->AgentPseudo->save(array('user_id' => $id, 'pseudo' => $pseudo));
			$this->_adminChangeRecord($id, 1, false);
        }else{  //Erreur
            //Il faut annuler les modifications
            //On est dans une activation de compte donc il faut re désactiver le compte
            $this->User->changeEtatUser($id, false);
            $this->Session->setFlash(__('Erreur API : '.(isset($result['response_message']) ?$result['response_message']:'Création de l\'expert impossible au niveau de l\'api')),'flash_warning');
            $this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $id),false);
        }


    }
    /* Récupère l'encours de la semaine courante de crédit rechargé : pour les limites de conso */
    protected function getUserCreditHebdoEncours()
    {
        $this->loadModel('UserCredit');
		$this->loadModel('Order');
        $now = time();
        $dateBeginCurrentWeek = mktime(0, 0, 0, date('m', $now), date('d', $now)-date('N', $now)+1, date('Y', $now));
       // $query = "SELECT SUM(credits) AS total_credits_hebdo FROM user_credits WHERE users_id = ".(int)$this->Auth->user('id')." AND date_upd >= '".date("Y-m-d H:i:s",$dateBeginCurrentWeek)."'";

        //$rows = $this->UserCredit->query($query);
		$query = "SELECT SUM(total) AS total_credits_hebdo FROM orders WHERE user_id = ".(int)$this->Auth->user('id')." AND date_add >= '".date("Y-m-d H:i:s",$dateBeginCurrentWeek)."'";

        $rows = $this->Order->query($query);
        return isset($rows['0']['0']['total_credits_hebdo'])?number_format($rows['0']['0']['total_credits_hebdo'],2,'.',''):0;
    }

	protected function replaceKeys($oldKey, $newKey, array $input){
		$return = array();
		foreach ($input as $key => $value) {
			if ($key===$oldKey)
				$key = $newKey;

			if (is_array($value))
				$value = replaceKeys( $oldKey, $newKey, $value);

			$return[$key] = $value;
		}
		return $return;
	}
}

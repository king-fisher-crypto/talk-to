<?php
    App::uses('AppController', 'Controller');

    class PhonesController extends AppController {
        public $uses = array('CountryLangPhone');

        public $components = array('Paginator');
        public $helpers = array('Paginator');

        public function beforeFilter()
        {
            parent::beforeFilter();
        }

        public function admin_create(){
            $this->loadModel('Lang');
            $this->loadModel('Country');
            $select_langs = $this->Lang->getLang();
            $select_countries = $this->Country->getCountriesForSelect($this->Session->read('Config.id_lang'));

            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On check le formulaire
                $requestData['Phone'] = Tools::checkFormField($requestData['Phone'],
                    array('country_id', 'lang_id', 'surtaxed_phone_number', 'surtaxed_minute_cost', 'prepayed_phone_number', 'prepayed_minute_cost', 'prepayed_second_credit', 'mention_legale_num1','mention_legale_num2'),
                    array('country_id', 'lang_id')
                );
                if($requestData['Phone'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    $this->redirect(array('controller' => 'phones', 'action' => 'create' , 'admin' => true),false);
                }

                //A t-il déjà une conf ?
                $phone = $this->CountryLangPhone->find('first',array(
                    'conditions' => array('country_id' => $requestData['Phone']['country_id'], 'lang_id' => $requestData['Phone']['lang_id']),
                    'recursive' => -1
                ));

                if(empty($phone)){
                    $this->CountryLangPhone->create();
                    if($this->CountryLangPhone->save($requestData['Phone'])){
                        $this->Session->setFlash(__('Nouvelle configuration ajoutée.'),'flash_success');
                        $this->redirect(array('controller' => 'phones', 'action' => 'list', 'admin' => true),false);
                    }else{
                        $this->Session->setFlash(__('Echec de l\'ajout.'),'flash_warning');
                        $this->set(compact('select_langs', 'select_countries'));
                        return;
                    }
                }else{
                    $this->Session->setFlash(__('Une configuration pour ce pays et cette langue existe déjà. Vous pouvez la modifier.'),'flash_warning');
                    $this->set(compact('select_langs', 'select_countries'));
                    return;
                }
            }

            $this->set(compact('select_langs', 'select_countries'));
        }

        public function admin_list(){
            //Les paramètres pour le paginator
            $this->Paginator->settings = array(
                'fields' => array('CountryLangPhone.*', 'CountryLang.name', 'Lang.name'),
                'joins' => array(
                    array(
                        'table' => 'country_langs',
                        'alias' => 'CountryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'CountryLang.country_id = CountryLangPhone.country_id',
                            'CountryLang.id_lang = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order'  => 'CountryLang.name ASC, Lang.name ASC',
                'paramType' => 'querystring',
                'limit' => 20
            );

            $phones = $this->Paginator->paginate($this->CountryLangPhone);
            $this->set(compact('phones'));
        }

        public function admin_edit($country, $lang){
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On check le formulaire
                $requestData['Phone'] = Tools::checkFormField($requestData['Phone'],
                    array('country_id', 'lang_id', 'surtaxed_phone_number', 'surtaxed_minute_cost', 'prepayed_phone_number', 'prepayed_minute_cost',
                        'prepayed_second_credit', 'mention_legale_num1', 'mention_legale_num2', 'mention_legale_num3', 'third_phone_number', 'third_minute_cost'),
                    array('country_id', 'lang_id')
                );
                if($requestData['Phone'] === false){
                    $this->Session->setFlash(__('Erreur avec le formulaire.'),'flash_error');
                    $this->redirect(array('controller' => 'phones', 'action' =>'edit', 'country' => $country, 'lang' => $lang),false);
                }
                $requestData = $this->request->data;
                //On supprime la conf actuelle
                $this->CountryLangPhone->deleteAll(array('CountryLangPhone.country_id' => $country, 'CountryLangPhone.lang_id' => $lang),false);
                //On save la nouvelle
                if($this->CountryLangPhone->save($requestData['Phone'])){
                    $this->Session->setFlash(__('Modification enregistrée.'),'flash_success');
                    $this->redirect(array('controller' => 'phones', 'action' => 'list', 'admin' => true),false);
                }else{
                    $this->Session->setFlash(__('Echec de la modification.'),'flash_warning');
                    $this->redirect(array('controller' => 'phones', 'action' =>'edit', 'country' => $country, 'lang' => $lang),false);
                }
            }

            if(empty($country) || empty($lang))
                $this->redirect(array('controller' => 'phones', 'action' => 'list', 'admin' => true), false);

            $phone = $this->CountryLangPhone->find('first',array(
                'conditions' => array('country_id' => $country, 'lang_id' => $lang),
                'recursive' => -1
            ));

            $this->set(compact('phone', 'country', 'lang'));
        }
		
		public function hasSession(){

			 if($this->request->is('ajax')){
				 
				 $is_payment_oppose = false;
				 
				 $agent = $this->User->find('first', array(
                        'fields' => array('agent_number'),
                        'conditions' => array('id' => $this->Auth->user('id')),
                        'recursive' => -1
                    ));
				 
				 if($agent['User']['agent_number']){
					 $this->loadModel('CallInfo');
					 $call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.called_number', 'CallInfo.customer', 'CallInfo.time_start', 'CallInfo.timestamp', 'CallInfo.callinfo_id', 'CallInfo.line', 'CallInfo.callerid'),
						'conditions' => array('agent' => $agent['User']['agent_number'] , 'time_start IS NOT NULL' , 'time_stop' => NULL),
						'recursive' => 0
					));

					 if(count($call)){
						 
						 $phone_com_title = 'Nom client : ';
						 $user_credit = 0;
						 $anonymous = 0;
						 if($call['CallInfo']['callerid'] == '43123001999' || $call['CallInfo']['callerid'] == 'anonymous' || $call['CallInfo']['callerid'] == 'anonyme' || $call['CallInfo']['callerid'] == 'UNKNOW'){
							 if(!$call['CallInfo']['customer'])
							 $anonymous = 1;
						 }
						 if((substr($call['CallInfo']['callerid'], -4)*15) == 0 && !$call['CallInfo']['customer'])$anonymous = 1;
						 $is_audiotel = 0;
						 if(!$anonymous){
							 if(!$call['CallInfo']['customer']){
								  $call['CallInfo']['customer'] = '999999';
								  $phone_com_title .= 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
								  $is_audiotel = 1 ;
							 }else{
									$customer = $this->User->find('first', array(
										'fields' => array('firstname', 'lastname', 'credit','payment_opposed','parent_account_opposed'),
										'conditions' => array('personal_code' => $call['CallInfo']['customer']),
										'recursive' => -1
									)); 
								 $phone_com_title .= $customer['User']['firstname'];
								 $user_credit = $customer['User']['credit'];
								 
								 if($customer['User']['payment_opposed'])$is_payment_oppose = true;
								 if($customer['User']['parent_account_opposed'])$is_payment_oppose = true;
							 }
						 }else{
							 $phone_com_title .= 'non identifié';
						 }
						 
						 $timing = 0;
						 $duree = 0;
						switch ($call['CallInfo']['called_number']) {
							case 901801885:
								$phone_com_time = __('Origine : client Suisse audiotel (60 min)');
								$duree = 3600;
								break;
							case 41225183456:
								$phone_com_time = __('Origine : client Suisse prépayé');
								$duree = $user_credit;
								break;
							case 90755456:
								$phone_com_time = __('Origine : client Belgique audiotel (10 min)');
								$duree = 600;
								break;
							case 3235553456:
								$phone_com_time = __('Origine : client Belgique prépayé');
								$duree = $user_credit;
								break;
							case 90128222:
								$phone_com_time = __('Origine : client Luxembourg audiotel (30 min)');
								$duree = 1800;
								break;
							case 27864456:
								$phone_com_time = __('Origine : client Luxembourg prépayé');
								$duree = $user_credit;
								break;
							case 4466:
								$phone_com_time = __('Origine : client Canada audiotel (60 min)');
								$duree = 3600;
								break;
							case 19007884466:
								$phone_com_time = __('Origine : client Canada audiotel (16 min)');
								$duree = 960;
								break;
							case 18442514456:
								$phone_com_time = __('Origine : client Canada prépayé');
								$duree = $user_credit;
								break;
							case 33970736456:
								$phone_com_time = __('Origine : client France prépayé');
								$duree = $user_credit;
								break;
							default:
								$phone_com_time = __('Origine : client France');
								$duree = $user_credit;
								break;
						}
						
						switch ($call['CallInfo']['line']) {
							case 'CH-0901801885':
							case 'CH-+41225183456':
								$phone_com_time = __('Origine : client Suisse audiotel (60 min)');
								$duree = 3600;
								break;
							case 'BE-090755456':
							case 'BE-+3235553456':
								$phone_com_time = __('Origine : client Belgique audiotel (10 min)');
								$duree = 600;
								break;
							case 'BE-090755456 mob.':
								$phone_com_time = __('Origine : client Belgique audiotel (10 min)');
								$duree = 600;
								break;
							case 'LU-+35227864456':
							case 'LU-90128222':
								$phone_com_time = __('Origine : client Luxembourg audiotel (30 min)');
								$duree = 1800;
								break;
							case 'CA-+18442514456':
							case 'CA-19007884466':
							case 'CA-19005289010':
								$phone_com_time = __('Origine : client Canada audiotel (60 min)');
								$duree = 3600;
								break;
							case 'CA-#4466 Bell':
							case 'CA-#9010 Bell':
								$phone_com_time = __('Origine : client Canada audiotel (16 min)');
								$duree = 960;
								break;
							case 'CA-#4466 Rogers/Fido':
							case 'CA-#9010 Rogers/Fido':
								$phone_com_time = __('Origine : client Canada audiotel (60 min)');
								$duree = 3600;
								break;
							case 'CA-#4466 Telus':
							case 'CA-#9010 Telus':
								$phone_com_time = __('Origine : client Canada audiotel (30 min)');
								$duree = 1920;
								break;
							case 'CA-#4466 Videotron':
							case 'CA-#9010 Videotron':
								$phone_com_time = __('Origine : client Canada audiotel (30 min)');
								$duree = 1920;
								break;
							case 'AT-431230460013':
							//case 'AT-431230460012':
								$phone_com_time = __('Origine : client audiotel');
								$duree = 3600;
								break;
						}
						
						$phone_com_time .= '<br />';
						$phone_com_time .= __('Temps restant en communication : ');
						
						if($is_audiotel){
							$first_date = $call['CallInfo']['timestamp'];
						}else{
							$first_date = $call['CallInfo']['time_start'];	
						}
						
						$second_date = time();
						$difference = $second_date - $first_date;
						
						$tps_restant = $duree - $difference;
						
						//$phone_com_time .= ''.$duree. ' -> '.$difference;
						
						
						if($tps_restant > 0){
							$hours = intval((floor($tps_restant/3600)));
							$phone_com_time .= intval((floor($tps_restant/3600))).' h '.intval(floor(($tps_restant - ($hours*3600)) / 60)). ' min '.intval(floor($tps_restant % 60)). ' sec' ;
						}else{
							$phone_com_time .= '0 sec.';
						}
						
						$contentbox = $this->getCmsPage(230);//230 prod
						$box_content = $contentbox['PageLang']['content'];
						 
						if($is_payment_oppose){
							$box_content =  __('<p style="font-size:13px;color:#ff0000"><b style="color:#ff0000">ATTENTION :</b> <b>Veuillez indiquer à ce client que vous ne pouvez réaliser cette consultation en raison d\'une opposition de paiement de sa part sur de précédents achats et que vous ne serez pas rémunéré(es) tant que le solde ne sera pas effectué, une fois cette annonce faite au client nous vous remercions de raccrocher et de nous en faire part via le support.</b></p><p><b>Les consultations de ce client seront rémunérées uniquement une fois le solde effectué et cette fenêtre n\'apparaitra plus sur votre écran lorsque tel sera le cas.</b></p>');
						}
						
						$phone_com_time = '<p class="phoneboxtext">'.$phone_com_time.'</p>';
						$phone_com_time .= '<br />'.$box_content;
						$this->layout = '';
						$response = $this->render('/Elements/template_phone');
						$this->jsonRender(array('html' => $response->body(),'phone_com_title' => $phone_com_title, 'phone_com_time' => $phone_com_time));
					 }else{
						$this->jsonRender(array('html' => '','phone_com_title' => '', 'phone_com_time' => ''));
					 }
				 }else{
						$this->jsonRender(array('html' => '','phone_com_title' => '', 'phone_com_time' => ''));
				}
			 }
			
        }
		
		public function hasClientNotes(){

			 if($this->request->is('ajax')){// && !$this->request->isMobile()
				 
				 $agent = $this->User->find('first', array(
                        'fields' => array('agent_number','id'),
                        'conditions' => array('id' => $this->Auth->user('id')),
                        'recursive' => -1
                    ));
				 
				 if($agent['User']['agent_number']){
					 
					 
					 $this->loadModel('CallInfo');
					 $call = $this->CallInfo->find('first',array(
						'fields' => array('CallInfo.callinfo_id','CallInfo.called_number', 'CallInfo.customer', 'CallInfo.time_start', 'CallInfo.timestamp', 'CallInfo.callinfo_id', 'CallInfo.line', 'CallInfo.callerid'),
						'conditions' => array('agent' => $agent['User']['agent_number'] , 'time_start IS NOT NULL' , 'time_stop' => NULL),
						'recursive' => 0
					));
					
					if(!count($call)){
						$this->loadModel('Chat');
						$call = $this->Chat->find('first',array(
									'fields' => array('Chat.*'),
									'conditions' => array('Chat.to_id' => $agent['User']['id'], 'date_start IS NOT NULL' , 'date_end' => NULL),
									'recursive' => 0
						)); 	
					}
					

					 if(count($call)){

					    $anonymous = 0;
						if(is_array($call['CallInfo']) && !$call['CallInfo']['customer']){
							if($call['CallInfo']['callerid'] == '43123001999' || $call['CallInfo']['callerid'] == 'anonymous' || $call['CallInfo']['callerid'] == 'anonyme' || $call['CallInfo']['callerid'] == 'UNKNOW'){
								$anonymous = 1;
							}
							 if((substr($call['CallInfo']['callerid'], -4)*15) == 0 )$anonymous = 1;
						} 
						
						if(!$anonymous){
							 $is_audiotel = false;
							 $id_client = 0;
	
							 
							if(is_array($call['CallInfo'])){
								if(!$call['CallInfo']['customer']){
									  $id_client =  $call['CallInfo']['callerid'];//'286';
									  $phone_note_title = 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
									  $is_audiotel = true;
								 }else{
										$customer = $this->User->find('first', array(
										'fields' => array('id','firstname', 'lastname'),
										'conditions' => array('personal_code' => $call['CallInfo']['customer']),
										'recursive' => -1
									)); 
									 $phone_note_title = $customer['User']['firstname'];
									 $id_client = $customer['User']['id'];
								 }
							 }else{
									$customer = $this->User->find('first', array(
											'fields' => array('id','firstname', 'lastname'),
											'conditions' => array('id' => $call['Chat']['from_id']),
											'recursive' => -1
										)); 
										 $phone_note_title = $customer['User']['firstname'];
										 $id_client = $customer['User']['id'];	
								}
							 
							 $this->loadModel('Notes');
								
							 if(!$is_audiotel){
								 $note = $this->Notes->find('first',array(
									'fields' => array('Notes.*'),
									'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => $id_client),
									'recursive' => 0
								));
							 }else{
								$note = $this->Notes->find('first',array(
									'fields' => array('Notes.*'),
									'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => $id_client),
									'recursive' => 0
								));
								 if(!$note){
									 //old '286'
									 $note = $this->Notes->find('first',array(
										'fields' => array('Notes.*'),
										'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => '286', 'client' => $phone_note_title),
										'recursive' => 0
									));
								 }
							 }
	
							 if(isset($note['Notes']) && $note['Notes']['note']){
								 $phone_note_text = $note['Notes']['note'];
							 }else{
								$phone_note_text = ''; 
							 }
							 
							 //affichage
							 $birthday = '';
							 if(isset($note['Notes']) && $note['Notes']['birthday'] && $note['Notes']['birthday'] != '0000-00-00 00:00:00'){
							 $date_naissance = explode(' ',$note['Notes']['birthday']);
						     $date_naissance = explode('-',$date_naissance[0] );
						     $birthday = $date_naissance[2].'-'.$date_naissance[1].'-'.$date_naissance[0];
								 $birthday_day = $date_naissance[2];
								 $birthday_month = $date_naissance[1];
								 $birthday_year = $date_naissance[0];
							 }
							 $chat_id = 0;
							 $note_sex = '';
							 if(isset($call['Chat']['id']))$chat_id = $call['Chat']['id'];
							 if(isset($note['Notes']['sexe']))$note_sex = $note['Notes']['sexe'];
							 
							 $this->layout = '';
							 $response = $this->render('/Elements/template_phone_notes');
							 $this->jsonRender(array('html' => $response->body(),'phone_note_title' => 'Notes sur le client : '.$phone_note_title, 'phone_note_text' => $phone_note_text, 'phone_note_call' => $call['CallInfo']['callinfo_id'],'phone_note_tchat' => $chat_id, 'phone_note_agent' => $this->Auth->user('id'),'phone_note_birthday'=>$birthday,'phone_note_birthday_day'=>$birthday_day,'phone_note_birthday_month'=>$birthday_month,'phone_note_birthday_year'=>$birthday_year,'phone_note_sexe'=>$note_sex,'phone_id_client' => $id_client));
						 }else{
							 $this->layout = '';
							 $response = $this->render('/Elements/template_phone_notes');
							 $this->jsonRender(array('html' => $response->body(),'phone_note_title' => __('Notes sur le client : non identifié'), 'phone_note_text' => __('Notes impossibles.'), 'phone_note_call' => 0,'phone_note_tchat' => 0, 'phone_note_agent' => $this->Auth->user('id')));
						 }
					 }else{
						$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '','phone_note_tchat' => '', 'phone_note_agent' => $this->Auth->user('id')));  
					 }
				 }else{
					$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '','phone_note_tchat' => '', 'phone_note_agent' => $this->Auth->user('id'))); 
				 }
			 }else{
				$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '','phone_note_tchat' => '', 'phone_note_agent' => $this->Auth->user('id')));
			}
			
        }
		
		public function showClientNotes(){

			 if($this->request->is('ajax')){
				 $requestData = $this->request->data;
				 $this->loadModel('Notes');
				 $note = $this->Notes->find('first',array(
								'fields' => array('Notes.*'),
								'conditions' => array('id' => $requestData['note']),
								'recursive' => 0
							));
				 
				 $agent = $this->User->find('first', array(
                        'fields' => array('agent_number'),
                        'conditions' => array('id' => $note['Notes']['id_agent']),
                        'recursive' => -1
                    ));
					
				$client = $this->User->find('first', array(
                        'fields' => array('firstname'),
                        'conditions' => array('id' => $note['Notes']['id_client']),
                        'recursive' => -1
                    ));	
				 
				 if($agent['User']['agent_number']){
					 
					 
					 $this->loadModel('CallInfo');
					 $call = $this->CallInfo->find('first',array(
							'fields' => array('CallInfo.callinfo_id','CallInfo.called_number', 'CallInfo.customer', 'CallInfo.time_start', 'CallInfo.timestamp', 'CallInfo.callinfo_id', 'CallInfo.line', 'CallInfo.callerid'),
							'conditions' => array('callinfo_id' => $note['Notes']['callinfo_id']),
							'recursive' => 0
						));
					if(!count($call)){
						$this->loadModel('Chat');
						$call = $this->Chat->find('first',array(
									'fields' => array('Chat.*'),
									'conditions' => array('Chat.id' => $note['Notes']['tchat_id']),
									'recursive' => 0
						)); 	
					}
					 if(!count($call)){
						//sagit juste d un email
						 $call = array();
						 $call['Chat']['from_id'] = $note['Notes']['id_client'];
					 }

					 if(count($call)){
						 $is_audiotel = false;
						 $id_client = 0;
						 if(is_array($call['CallInfo'])){
							if(!$call['CallInfo']['customer']){
								  $id_client =  $note['Notes']['id_client'];
								  $phone_note_title = 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
								  $is_audiotel = true;
							 }else{
								 $phone_note_title = $client['User']['firstname'];
								 $id_client = $client['User']['id'];
							 }
						 }else{
								$customer = $this->User->find('first', array(
										'fields' => array('id','firstname', 'lastname'),
										'conditions' => array('id' => $call['Chat']['from_id']),
										'recursive' => -1
									)); 
									 $phone_note_title = $customer['User']['firstname'];
									 $id_client = $customer['User']['id'];	
							}

						 if($note['Notes']['note']){
							 $phone_note_text = $note['Notes']['note'];
						 }else{
							$phone_note_text = ''; 
						 }
						 
						 //affichage
						 $birthday = '';
						 if($note['Notes']['birthday'] != '0000-00-00 00:00:00'){
						 $date_naissance = explode(' ',$note['Notes']['birthday']);
						 $date_naissance = explode('-',$date_naissance[0] );
						 $birthday = $date_naissance[2].'-'.$date_naissance[1].'-'.$date_naissance[0];
							 $birthday_day = $date_naissance[2];
							 $birthday_month = $date_naissance[1];
							 $birthday_year = $date_naissance[0];
							 
						 }
						 $this->layout = '';
						 $response = $this->render('/Elements/template_phone_notes');
						 $this->jsonRender(array('html' => $response->body(),'phone_note_title' => __('Notes sur le client : ').$phone_note_title, 'phone_note_text' => $phone_note_text, 'phone_note_call' => $call['CallInfo']['callinfo_id'],'phone_note_tchat' => $note['Notes']['tchat_id'], 'phone_note_agent' => $this->Auth->user('id'),'id_client'=>$note['Notes']['id_client'],'phone_note_birthday'=>$birthday,'phone_note_birthday_day'=>$birthday_day,'phone_note_birthday_month'=>$birthday_month,'phone_note_birthday_year'=>$birthday_year,'phone_note_sexe'=>$note['Notes']['sexe']));
					 }else{
						$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '', 'phone_note_agent' => $this->Auth->user('id')));  
					 }
				 }else{
					$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '', 'phone_note_agent' => $this->Auth->user('id'))); 
				 }
			 }else{
				$this->jsonRender(array('html' => '','phone_note_title' => '', 'phone_note_text' => '', 'phone_note_call' => '', 'phone_note_agent' => $this->Auth->user('id')));
			}
			
        }
		
		public function addClientNotes(){

			 if($this->request->is('ajax')){
				 $requestData = $this->request->data;
				 $agent = $this->User->find('first', array(
                        'fields' => array('agent_number'),
                        'conditions' => array('id' => $this->Auth->user('id')),
                        'recursive' => -1
                    ));
				 
				 if($agent['User']['agent_number']){
					 $call = array();
					 if(isset($requestData['call']) && $requestData['call']){
						 $this->loadModel('CallInfo');
							 $call = $this->CallInfo->find('first',array(
								'fields' => array('CallInfo.callinfo_id','CallInfo.called_number', 'CallInfo.customer', 'CallInfo.time_start', 'CallInfo.timestamp', 'CallInfo.callinfo_id', 'CallInfo.line', 'CallInfo.callerid'),
								'conditions' => array('callinfo_id' => $requestData['call']),
								'recursive' => 0
							));
						/* }else{
							$call = $this->CallInfo->find('first',array(
								'fields' => array('CallInfo.callinfo_id','CallInfo.called_number', 'CallInfo.customer', 'CallInfo.time_start', 'CallInfo.timestamp', 'CallInfo.callinfo_id', 'CallInfo.line', 'CallInfo.callerid'),
								'conditions' => array('agent' => $agent['User']['agent_number'] , 'time_start IS NOT NULL' , 'time_stop' => NULL),
								'recursive' => 0
							)); 
						 }*/
					 }else{
						 if(isset($requestData['tchat']) && $requestData['tchat']){
							 $this->loadModel('Chat');
							 $call = $this->Chat->find('first',array(
									'fields' => array('Chat.*'),
									'conditions' => array('Chat.id' => $requestData['tchat']),
									'recursive' => 0
								)); 
						 }
					 }
					 
					 if(count($call)){
						 
						$is_audiotel = false;
						$id_client = 0;
						if(is_array($call['CallInfo'])){
							if(!$call['CallInfo']['customer']){
								  $id_client =  $call['CallInfo']['callerid'];
								  $phone_note_title = 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
								  $is_audiotel = true;
							 }else{
									$customer = $this->User->find('first', array(
									'fields' => array('id','firstname', 'lastname'),
									'conditions' => array('personal_code' => $call['CallInfo']['customer']),
									'recursive' => -1
								)); 
								 $phone_note_title = $customer['User']['firstname'];
								 $id_client = $customer['User']['id'];
							 }
						}else{
							$customer = $this->User->find('first', array(
									'fields' => array('id','firstname', 'lastname'),
									'conditions' => array('id' => $call['Chat']['from_id']),
									'recursive' => -1
								)); 
								 $phone_note_title = $customer['User']['firstname'];
								 $id_client = $customer['User']['id'];	
						}
						 
						 $this->loadModel('Notes');
						 if(!$is_audiotel){
							 $note = $this->Notes->find('first',array(
								'fields' => array('Notes.*'),
								'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => $id_client),
								'recursive' => 0
							));
							// if($note["Notes"]['client'])
							 $phone_note_title = $note["Notes"]['client'];
						 }else{
							 $note = $this->Notes->find('first',array(
								'fields' => array('Notes.*'),
								'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => '286', 'client' => $phone_note_title),
								'recursive' => 0
							));	
							 
							 if(!$note){
								$note = $this->Notes->find('first',array(
									'fields' => array('Notes.*'),
									'conditions' => array('id_agent' => $this->Auth->user('id') , 'id_client' => $id_client),
									'recursive' => 0
								));	
							 }
							 if($note["Notes"]['client'] != $phone_note_title)$note = null;
						 }
						 
					 	 if(!$note){
							 
							$birthday = ''; 
							if($requestData['birthday']){
								$date_naissance = explode('-',$requestData['birthday']); 
								$birthday = $date_naissance[2].'-'.$date_naissance[1].'-'.$date_naissance[0].' 00:00:00';
							}
							
							 
							 
						 	$info = array(
								'id_agent'   => $this->Auth->user('id'),
								'id_client'     => $id_client,
								'client'   => $phone_note_title,
								'callinfo_id'     => $call['CallInfo']['callinfo_id'],
								'tchat_id'     => $requestData['tchat'],
								'note'   => addslashes($requestData['note']),
								'birthday'   => $birthday,
								'sexe'   => $requestData['sex'],
								'date_crea'      => date('Y-m-d H:i:s'),
								'date_upd'      => date('Y-m-d H:i:s')
							);
							$this->Notes->create();
							$this->Notes->save($info);
						 }else{
							  $requestData['note'] = addslashes(utf8_decode($requestData['note']));
							 $birthday = ''; 
							if($requestData['birthday']){
								$date_naissance = explode('-',$requestData['birthday']); 
								$birthday = $date_naissance[2].'-'.$date_naissance[1].'-'.$date_naissance[0].' 00:00:00';
							}
							 
							 
							 $dbb_patch = new DATABASE_CONFIG();
							$dbb_connect = $dbb_patch->default;
							$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
							$req =  "UPDATE notes SET note = '{$requestData['note']}', date_upd = NOW(),callinfo_id = '{$call['CallInfo']['callinfo_id']}',tchat_id = '{$requestData['tchat']}',sexe = '{$requestData['sexe']}',birthday = '{$birthday}'  WHERE id = '{$note["Notes"]['id']}' ";
							 
							 /*
							 and id_client = '{$id_client}'";
							 if($phone_note_title)
							 $req .= " and client = '{$phone_note_title}'
							 */

							$mysqli_connect->query($req);
						 }
					 }
				 }
			 }
			exit;
        }
		
		
    }
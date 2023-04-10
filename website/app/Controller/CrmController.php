<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class CrmController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'Crm');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'block')));

        public function beforeFilter() {

            parent::beforeFilter();
        }
		
		
		public function admin_cart() {
			
			
			$this->loadModel('CartLoose');
			$conditions = array();
			
			if($this->request->is('post')){
				/* if(isset($this->request->data['Vouchers']['vouchers_title']) && !empty($this->request->data['Vouchers']['vouchers_title']))
				 	$conditions = array_merge($conditions, array('Voucher.title LIKE' => '%'.$this->request->data['Vouchers']['vouchers_title'].'%'));
				 if(isset($this->request->data['Vouchers']['vouchers_code']) && !empty($this->request->data['Vouchers']['vouchers_code']))
				 	$conditions = array_merge($conditions, array('Voucher.code LIKE' => '%'.$this->request->data['Vouchers']['vouchers_code'].'%'));*/
			}
			
			
            //Les coupons
            $this->Paginator->settings = array(
				'fields' => 'CartLoose.*, Cart.voucher_code, User.firstname, Product.name, User.id',
				'conditions' => $conditions,
                'order' => array('CartLoose.date_cart' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 15,
				 'joins' => array(
					array(
						'table' => 'carts',
						'alias' => 'Cart',
						'type' => 'inner',
						'conditions' => array(
							'Cart.id = CartLoose.id_cart',
						)
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => array(
							'User.id = CartLoose.id_user',
						)
					),
					array(
							'table' => 'product_langs',
							'alias' => 'Product',
							'type' => 'inner',
							'conditions' => array(
								'Product.product_id = Cart.product_id',
								'Product.lang_id = 1',
							)
						),
				),
            );

            $carts = $this->Paginator->paginate($this->CartLoose);

            $this->set(compact('carts'));
			
			
		}
		
		public function admin_agent_view() {
			
			$this->loadModel('AgentView');
			$conditions = array();
			
			if($this->request->is('post')){
				/* if(isset($this->request->data['Vouchers']['vouchers_title']) && !empty($this->request->data['Vouchers']['vouchers_title']))
				 	$conditions = array_merge($conditions, array('Voucher.title LIKE' => '%'.$this->request->data['Vouchers']['vouchers_title'].'%'));
				 if(isset($this->request->data['Vouchers']['vouchers_code']) && !empty($this->request->data['Vouchers']['vouchers_code']))
				 	$conditions = array_merge($conditions, array('Voucher.code LIKE' => '%'.$this->request->data['Vouchers']['vouchers_code'].'%'));*/
			}
			
			
            //Les coupons
            $this->Paginator->settings = array(
				'fields' => 'AgentView.*, Agent.pseudo, Agent.id, User.firstname, User.id',
				'conditions' => $conditions,
                'order' => array('AgentView.date_view' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 15,
				 'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'Agent',
						'type' => 'inner',
						'conditions' => array(
							'Agent.id = AgentView.agent_id',
						)
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => array(
							'User.id = AgentView.user_id',
						)
					),
					
				),
            );

            $views = $this->Paginator->paginate($this->AgentView);

            $this->set(compact('views'));
			
			
		}
		
		
		public function admin_sends_test() {
			
			$this->loadModel('CrmStat');
			$this->loadModel('Crm');
			
			$conditions = array();
			
			if($this->request->is('post')){
				 if(isset($this->request->data['Crm']['crm_tracker']) && !empty($this->request->data['Crm']['crm_tracker']))
				 	$conditions = array_merge($conditions, array('Crm.tracker LIKE' => '%'.$this->request->data['Crm']['crm_tracker'].'%'));
				
				 if(isset($this->request->data['Crm']['crm_client']) && !empty($this->request->data['Crm']['crm_client']))
				 	$conditions = array_merge($conditions, array('User.firstname LIKE' => '%'.$this->request->data['Crm']['crm_client'].'%'));
				
				if(isset($this->request->data['Crm']['crm_code']) && !empty($this->request->data['Crm']['crm_code']))
				 	$conditions = array_merge($conditions, array('User.personal_code' => $this->request->data['Crm']['crm_code']));
			}
			
			
            $this->Paginator->settings = array(
				'fields' => 'CrmStat.*, Crm.*, User.id, User.firstname, User.lastname, User.personal_code,User.date_add', 
				'conditions' => $conditions,
                'order' => array('CrmStat.id' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 15,
				 'joins' => array(
					array(
						'table' => 'crms',
						'alias' => 'Crm',
						'type' => 'inner',
						'conditions' => array(
							'Crm.id = CrmStat.id_crm',
						)
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'inner',
						'conditions' => array(
							'User.id = CrmStat.id_user',
						)
					),
					
				),
            );

            $crms = $this->Paginator->paginate($this->CrmStat);
			
			$this->loadModel('Order');
			$this->loadModel('UserCreditLastHistory');
			
			foreach($crms as &$crm){
				$conditions = array(
								'Order.user_id' => $crm['User']['id'],
								'Order.valid' => 1,
							);

				$orders = $this->Order->find('first',array('conditions' => $conditions,'order' => array('Order.date_add' => 'desc')));
				
				$crm['Order'] = $orders['Order'];
				
				$conditions = array(
								'UserCreditLastHistory.users_id' => $crm['User']['id'],
							);

				$coms = $this->UserCreditLastHistory->find('first',array('conditions' => $conditions,'order' => array('UserCreditLastHistory.date_start' => 'desc')));
				
				$crm['Com'] = $coms['UserCreditLastHistory'];
			}
			
			
            $this->set(compact('crms'));
			
			
		}


		
		public function admin_create() {
			if($this->request->is('post')){

                $requestData = $this->validForm('create');

                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur, dans cette action on s'en fiche du msg
                    if(isset($requestData[0])){
                        $requestData = $requestData[1];
                    }
                }
                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;

                //On save les donnée

                $this->Crm->create();
                if($this->Crm->save($requestData['Crm'])){
                    $this->Session->setFlash(__('Le crm a été crée.'), 'flash_success');
                    $this->redirect(array('controller' => 'crm', 'action' => 'edit', 'admin' => true, 'id' => $this->Crm->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'crm', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection edition
                $this->redirect(array('controller' => 'crm', 'action' => 'list', 'admin' => true), false);
            }
			
			$this->loadModel('Page');
			
			$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.page_category_id' => 12
			);
			
			$mail = $this->Page->PageLang->find('all',array(
                            'conditions' => $conditions));
			
			$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.page_category_id' => 9
			);
			
			$cms = $this->Page->PageLang->find('all',array(
                            'conditions' => $conditions));
			
			$this->set(compact('cms','mail'));
			
		}
		
		private function validForm($mode, $id = 0){
            //Le template pour les modes
            $template['create'] = array(
                'fieldForm' => array('active','type','timing','id_cms','tracker','id_mail'),
               'requiredForm' => array()
            );
            $template['edit'] = array(
                'fieldForm' => array('active','type','timing','id_cms','tracker','id_mail'),
                'requiredForm' => array()
            );
            //Les données du formulaire
            $requestData = $this->request->data;
            //Check le formulaire
            $requestData['Crm'] = Tools::checkFormField($requestData['Crm'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['Crm'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'crm', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'crm', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }


            return $requestData;
        }
		
		public function admin_edit($id){
            if($this->request->is('post')){
                $requestData = $this->validForm('edit', $id);

                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'crm', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }

                $updateData = $requestData['Crm'];
                $updateData = $this->Crm->value($updateData);
                if($this->Crm->updateAll($updateData, array('Crm.id' => $id))){
                   
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour du crm'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour du crm'), 'flash_warning');

                $this->redirect(array('controller' => 'crm', 'action' => 'list', 'admin' => true), false);
            }


            //On récupère toutes les infos du crm
            $crm = $this->Crm->find('all',array(
                'conditions' => array('Crm.id' => $id),
                'recursive' => -1
            ));

            ///Les infos du crm
            $crmDatas = $crm[0]['Crm'];
			
			$this->loadModel('Page');
			
			$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.page_category_id' => 12
			);
			
			$mail = $this->Page->PageLang->find('all',array(
                            'conditions' => $conditions));
			
			$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.page_category_id' => 9
			);
			
			$cms = $this->Page->PageLang->find('all',array(
                            'conditions' => $conditions));


            $this->set(compact('crmDatas', 'cms', 'mail'));
        }


		
		public function admin_index() {
			 $this->Paginator->settings = array(
                'order' => array('Crm.id' => 'desc'),
                'paramType' => 'querystring',
				
                'limit' => 25
            );
			$this->loadModel('Page');
			$crm = $this->Paginator->paginate($this->Crm);
			foreach($crm as &$cr){
					$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.id' => $cr['Crm']['id_cms']
			);
			
			$cms = $this->Page->PageLang->find('first',array(
                            'conditions' => $conditions));
				$cr['Crm']['page'] = $cms['PageLang']['name'];
			}

            

            $this->set(compact('crm'));
		}
		
		public function admin_list(){
            $this->Paginator->settings = array(
                'order' => array('Crm.id' => 'desc'),
                'paramType' => 'querystring',
				
                'limit' => 25
            );
			 $crm = $this->Paginator->paginate($this->Crm);
			$this->loadModel('Page');
			foreach($crm as &$cr){
				
					$conditions = array(
				'PageLang.name LIKE' => '%CRM%',
				'Page.active'           => 1,
				'PageLang.lang_id'      => 1,
				'Page.id' => $cr['Crm']['id_cms']
			);
			
			$cms = $this->Page->PageLang->find('first',array(
                            'conditions' => $conditions));
				$cr['Crm']['page'] = $cms['PageLang']['name'];
			}
			

           

            $this->set(compact('crm'));
        }
		
		public function admin_activate($id){
            //on active le slide
            $this->Crm->id = $id;
            if($this->Crm->saveField('active', 1))
                $this->Session->setFlash(__('Le crm a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du crm.'),'flash_warning');

            $this->redirect(array('controller' => 'crm', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            $this->Crm->id = $id;
            if($this->Crm->saveField('active', 0))
                $this->Session->setFlash(__('Le crm a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du crm.'),'flash_warning');

            $this->redirect(array('controller' => 'crm', 'action' => 'list', 'admin' => true), false);
        }
		
		public function admin_deactivate_all(){
            $conditions = array(
					'Crm.active' => 1,
				);

				$crms = $this->Crm->find('all',array('conditions' => $conditions));
				$listSend = array();

				foreach($crms as $crm){
					 $this->Crm->id = $crm['Crm']['id'];
					if($this->Crm->saveField('active', 0))
						$this->Session->setFlash(__('Les crm ont été désactivé'),'flash_success');
					else
						$this->Session->setFlash(__('Erreur lors de la désactivation des crm.'),'flash_warning');
				}
            $this->redirect(array('controller' => 'crm', 'action' => 'list', 'admin' => true), false);
        }
		
		public function login(){
			 if ($this->request->is('post')) {
				if(isset($this->request->data['User']['compte_con']) && $this->request->data['User']['email_con']){
					$this->request->data['User']['compte'] = $this->request->data['User']['compte_con'];	
					$this->request->data['User']['email'] = $this->request->data['User']['email_con'];	
					$this->request->data['User']['passwd'] = $this->request->data['User']['passwd_con'];
				}

				//De quelle connexion il s'agit (admin, voyant, client) ?
				if(isset($this->request->data['User']['compte']) && in_array($this->request->data['User']['compte'],$this->nx_roles))
					//On modifie les conditions d'authentification
					$this->Auth->authenticate['Form']['scope'] = array_merge($this->Auth->authenticate['Form']['scope'], array('User.role = \''.$this->request->data['User']['compte'].'\''));
				else{
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
						if (isset($datas['User']) && isset($this->request->data['User']['rememberme']) && $this->request->data['User']['rememberme']  == 1){
							$cookieDatas['email'] = $datas['User']['email'];
							$cookieDatas['passwd'] = $datas['User']['passwd'];
							$this->Cookie->write('user_remember', $cookieDatas, true, "12 months");
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
					if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
						$this->jsonRender(array('return' => true));

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
									'mail'            	=> 0,
									'tchat'      		=> 0,
									'phone'    			=> 0
								);
							}
							$this->UserConnexion->create();
							$this->UserConnexion->save($connexion);


							$this->redirect(array('controller' => 'agents', 'action' => 'profil'));

							break;
						case 'client':
							if($datas['User']['valid'] == 0) $this->redirect(array('controller' => 'accounts', 'action' => 'profil'));
							if($this->request->isMobile()){
								$this->redirect(array('controller' => 'home', 'action' => 'index'));
							}else{
								$this->redirect(array('controller' => 'accounts', 'action' => 'index'));	
							}
							break;
					}

					$this->redirect(array('controller' => 'home', 'action' => 'index'));
				} else {
					//Retour ajax
					if(isset($this->request->data['User']['isAjax']) && $this->request->data['User']['isAjax'])
						$this->jsonRender(array('return' => false, 'msg' => __('Identifiants incorrects')));

					$compte = $this->User->find('first', array(
						'conditions' => array('email' => $this->request->data['User']['email'], 'active !=' => 1, 'deleted' => 0, 'role' => $this->request->data['User']['compte']),
						'recursive' => -1
					));
					if(empty($compte)){
						$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => 'client')), true), 'messageLink' => __('Mot de passe oublié ?')));
						if($this->request->data['User']['compte'] === 'admin'){
							$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'admins', 'action' => 'passwdforget'), true), 'messageLink' => __('Mot de passe oublié ?')));
							$this->redirect(array('controller' => 'admins', 'action' => 'login'), false);
						}elseif($this->request->data['User']['compte'] === 'agent'){
							$this->Session->setFlash(__('Identifiants incorrects.'), 'flash_warning', array('link' => Router::url(array('controller' => 'users', 'action' => 'passwdforget', '?' => array('compte' => 'agent')), true), 'messageLink' => __('Mot de passe oublié ?')));
							$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
						}
					}else{
						$this->Session->setFlash(__('Votre compte n\'est pas activé'), 'flash_info');
						if($this->request->data['User']['compte'] === 'agent')
							$this->redirect(array('controller' => 'users', 'action' => 'login_agent'));
						elseif($this->request->data['User']['compte'] === 'admin')
							$this->redirect(array('controller' => 'admins', 'action' => 'login'), false);
					}
				}
			}

			/* On récupère la liste des pays disponibles */
			$this->loadModel('UserCountry');
			$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
			
			$this->loadModel('Crm');
			
			$query = $this->request->query;
			
			$conditions = array(
				'Crm.tracker' => $query["utm_campaign"]
			);
			
			$crm = $this->Crm->find('first',array(
                            'conditions' => $conditions));
				
			$id_cms = $crm["Crm"]["id_cms"];
			$id_crm = $crm["Crm"]["id"];
			
			$this->loadModel('CrmStat');
			$query = $this->request->query;
			if($query["i"]){
				$this->CrmStat->id = $query["i"];
				$this->CrmStat->saveField('click', 1);
			}
			
			if($this->Auth->user('id')){
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			}
			
            $this->set(compact('id_cms'));
			
		}
		
		public function track(){
			$this->loadModel('CrmStat');
			$query = $this->request->query;
			$this->CrmStat->id = $query["i"];
			$this->CrmStat->saveField('view', 1);
			
			//$path = '/media/img/pixel.jpg';
			//header('Content-Type: image/jpeg');
			//readfile($path);
			
			// Create an image, 1x1 pixel in size
			  $im=imagecreate(1,1);

			  // Set the background colour
			  $white=imagecolorallocate($im,255,255,255);

			  // Allocate the background colour
			  imagesetpixel($im,1,1,$white);

			  // Set the image type
			  header("content-type:image/jpg");

			  // Create a JPEG file from the image
			  imagejpeg($im);

			  // Free memory associated with the image
			  imagedestroy($im);
			exit;
		}
		
		public function unsubscribe() {
			$this->loadModel('User');
			$query = $this->request->query;
			
			$conditions = array(
							'User.email' => $query["m"],
						);

			$users = $this->User->find('all',array('conditions' => $conditions));
						
			foreach($users as $user){
			
				$this->User->id = $user['User']["id"];
				$this->User->saveField('subscribe_mail', 0);
			}
			$this->Session->setFlash(__('Votre désinscription a bien été enregistrée'), 'flash_success');
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		}
		
		public function admin_bilan() {
		
			$list_code = array(
				'ISA_1H_5MIN' => 1,
				'ISA_3H_5MIN' => 2,
				'ISA_1J_10MIN' => 3,
				'ISA_2J_10MIN' => 4,
				'ISA_7J_10MIN' => 5,
				'PA_ISA_1J_10MIN' => 35,
				'PA_ISA_2J_10MIN' => 39,
				'RAC_14J_5MIN' => 19,
				'RAC_45J_5MIN' => 20,
				'RAC_60J_5MIN' => 21,
				'RAC_90J_5MIN' => 22,
				'PA_RAC_1J_10MIN' => 35,
				'PA_RAC_2J_10MIN' => 39,
			);
			
			$this->loadModel('Order');
			$this->loadModel('CrmStat');
			
			$utc_dec = Configure::read('Site.utc_dec');
			
			if($this->Session->check('Date')){
				$dmin = new DateTime(CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'));
				$dmin->modify('-'.$utc_dec.' hour');
				$dmin_date =  $dmin->format('Y-m-d H:i:s');
				
				$dmax = new DateTime(CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59'));
				$dmax->modify('-'.$utc_dec.' hour');
				$dmax_date =  $dmax->format('Y-m-d H:i:s');
				
			}
			
			$bilan = array();
			$total_ca = 0;
			$total_ca_new = 0;
			$total_ca_old = 0;
			$total_old_ca = 0;
			$total_old_ca_new = 0;
			$total_old_ca_old = 0;
			$total_nb = 0;
			$total_nb_new = 0;
			$total_nb_old = 0;
			$total_old_nb = 0;
			$total_old_nb_new = 0;
			$total_old_nb_old = 0;
			foreach($list_code as $code => $id_crm){
				$period = '';
				$conditions = array('voucher_name' => $code, 'valid'=>1);
				
				$nb_progress = '';
				$ca_progress = '';
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'Order.date_add >=' => $dmin_date,
						'Order.date_add <=' => $dmax_date
					));
					$period = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'). ' au '. CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
				}
				
				$nb = $this->Order->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));
				$total_nb += $nb;
				if(substr_count($code,'ISA')){
					$total_nb_new += $nb;
				}else{
					$total_nb_old += $nb;
				}
				
				$total_orders = $this->Order->find('all',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));
				$subtotal = 0;
				foreach($total_orders as $t){
					$subtotal += $t['Order']['total'];
					$total_ca += $t['Order']['total'];
					if(substr_count($code,'ISA')){
						$total_ca_new += $t['Order']['total'];
					}else{
						$total_ca_old += $t['Order']['total'];
					}
				}
				
				
				
				if($this->Session->check('Date')){
					
					$firstDate = new \DateTime($dmin_date);
					$endDate = new \DateTime($dmax_date);
					$firstDate->modify( 'first day of -1 month' );
					$endDate->modify( 'last day of -1 month' );
					
					 $conditions_old = array_merge($conditions, array(
						'Order.date_add >=' => $firstDate->format('Y-m-d H:i:s'),
						'Order.date_add <=' => $endDate->format('Y-m-d H:i:s')
					));
					$nb_old = $this->Order->find('count',array(
						'conditions' => $conditions_old,
						'recursive' => -1,
					));
					
					$total_old_nb += $nb_old;
					if(substr_count($code,'ISA')){
						$total_old_nb_new += $nb_old;
					}else{
						$total_old_nb_old += $nb_old;
					}
					
					if($nb_old){
						$nb_progress = ($nb - $nb_old) / $nb_old * 100;
					}
					
					$total_orders = $this->Order->find('all',array(
						'conditions' => $conditions_old,
						'recursive' => -1,
					));
					$subtotal_old = 0;
					foreach($total_orders as $t){
						$subtotal_old += $t['Order']['total'];
						$total_old_ca += $t['Order']['total'];
						if(substr_count($code,'ISA')){
							$total_old_ca_new += $t['Order']['total'];
						}else{
							$total_old_ca_old += $t['Order']['total'];
						}
					}
					if($subtotal_old){
						$ca_progress = ($subtotal - $subtotal_old) / $subtotal_old * 100;
					}
				}
				

				$conditions = array('id_crm' => $id_crm);
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'CrmStat.date >=' => $dmin_date,
						'CrmStat.date <=' => $dmax_date
					));
				}
				
				$nb_send = $this->CrmStat->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));
				
				$conditions = array('id_crm' => $id_crm, 'view'=>1);
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'CrmStat.date >=' => $dmin_date,
						'CrmStat.date <=' => $dmax_date
					));
				}
				
				$nb_view = $this->CrmStat->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));

				
				if(substr_count($code,'ISA'))$type = 'NOUVEAU'; else $type = 'ANCIEN';
				if(substr_count($code,'10MIN'))$promo = '10 MIN'; else $promo = '5 MIN';
				
				if($id_crm == 35 || $id_crm == 39){
					$nb_send = number_format($nb_send / 2,0,'.','');
					$nb_view = number_format($nb_view / 2,0,'.','');
				}
				
				if($nb_send > 0){
					$tx_view = $nb_view * 100 / $nb_send;
					$tx_convert = $nb * 100 / $nb_view;
				}else{
					$tx_view = 0;
					$tx_convert = 0;
				}
				
				
				$line = array();
				$line['date'] = $period;
				$line['type'] = $type;
				$line['code'] = $code;
				$line['promo'] = $promo;
				$line['nb'] = $nb;
				$line['send'] = $nb_send;
				$line['view'] = $nb_view;
				$line['tx_view'] = number_format($tx_view,1);
				$line['tx_convert'] = number_format($tx_convert,1);
				$line['nb_progress'] = number_format($nb_progress,1);
				$line['ca'] = number_format($subtotal,2,'.','');
				$line['ca_progress'] = number_format($ca_progress,1);
				array_push($bilan,$line);
			}
			
			if($total_old_nb_new){
				
				$nb_progress = ($total_nb_new - $total_old_nb_new) / $total_old_nb_new * 100;
				$ca_progress = ($total_ca_new - $total_old_ca_new) / $total_old_ca_new * 100;
				
				$line = array();
				$line['date'] = $period;
				$line['type'] = 'NOUVEAU';
				$line['code'] = 'TOTAL';
				$line['promo'] = '';
				$line['nb'] = $total_nb_new;
				$line['send'] = '';
				$line['view'] = '';
				$line['tx_view'] = '';
				$line['tx_convert'] = '';
				$line['nb_progress'] = number_format($nb_progress,1);
				$line['ca'] = number_format($total_ca_new,2,'.','');
				$line['ca_progress'] = number_format($ca_progress,1);
				array_push($bilan,$line);
			}
			if($total_old_nb_old){
				
				$nb_progress = ($total_nb_old - $total_old_nb_old) / $total_old_nb_old * 100;
				$ca_progress = ($total_ca_old - $total_old_ca_old) / $total_old_ca_old * 100;
				
				$line = array();
				$line['date'] = $period;
				$line['type'] = 'ANCIEN';
				$line['code'] = 'TOTAL';
				$line['promo'] = '';
				$line['nb'] = $total_nb_old;
				$line['send'] = '';
				$line['view'] = '';
				$line['tx_view'] = '';
				$line['tx_convert'] = '';
				$line['nb_progress'] = number_format($nb_progress,1);
				$line['ca'] = number_format($total_ca_old,2,'.','');
				$line['ca_progress'] = number_format($ca_progress,1);
				array_push($bilan,$line);
			}
			
			if($total_nb){
				
				$nb_progress = ($total_nb - $total_old_nb) / $total_old_nb * 100;
				$ca_progress = ($total_ca - $total_old_ca) / $total_old_ca * 100;
				
				$line = array();
				$line['date'] = $period;
				$line['type'] = '';
				$line['code'] = 'TOTAL';
				$line['promo'] = '';
				$line['nb'] = $total_nb;
				$line['send'] = '';
				$line['view'] = '';
				$line['tx_view'] = '';
				$line['tx_convert'] = '';
				$line['nb_progress'] = number_format($nb_progress,1);
				$line['ca'] = number_format($total_ca,2,'.','');
				$line['ca_progress'] = number_format($ca_progress,1);
				array_push($bilan,$line);
			}
			
			$this->set(compact('bilan'));
		}
		
		public function admin_export_bilan() {
			
			
				
			header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=export.csv");
		$fp = fopen('php://output', 'w');
		
		$legend = array(
			utf8_decode('Date'),
			utf8_decode('Client'),
			utf8_decode('Code'),
			utf8_decode('Promo'),
			utf8_decode('NB'),
			utf8_decode('Envois'),
			utf8_decode('Ouvert.'),
			utf8_decode('Tx Ouvert.'),
			utf8_decode('Tx Convert.')
		);
		fputcsv($fp, $legend,";");
			
			$list_code = array(
				'ISA_1H_5MIN' => 1,
				'ISA_3H_5MIN' => 2,
				'ISA_1J_10MIN' => 3,
				'ISA_2J_10MIN' => 4,
				'ISA_7J_10MIN' => 5,
				'PA_ISA_1J_10MIN' => 35,
				'PA_ISA_2J_10MIN' => 39,
				'RAC_14J_5MIN' => 19,
				'RAC_45J_5MIN' => 20,
				'RAC_60J_5MIN' => 21,
				'RAC_90J_5MIN' => 22,
				'PA_RAC_1J_10MIN' => 35,
				'PA_RAC_2J_10MIN' => 39,
			);
			
			$this->loadModel('Order');
			$this->loadModel('CrmStat');
			
			$bilan = array();
			
			$utc_dec = Configure::read('Site.utc_dec');
			
			if($this->Session->check('Date')){
				$dmin = new DateTime(CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'));
				$dmin->modify('-'.$utc_dec.' hour');
				$dmin_date =  $dmin->format('Y-m-d H:i:s');
				
				$dmax = new DateTime(CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59'));
				$dmax->modify('-'.$utc_dec.' hour');
				$dmax_date =  $dmax->format('Y-m-d H:i:s');
				
			}
			
			
			foreach($list_code as $code => $id_crm){
				$period = '';
				$conditions = array('voucher_name' => $code);
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'Order.date_add >=' => $dmin_date,
						'Order.date_add <=' => $dmax_date
					));
					$period = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'). ' au '. CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');
				}
				
				$nb = $this->Order->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));

				$conditions = array('id_crm' => $id_crm);
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'CrmStat.date >=' => $dmin_date,
						'CrmStat.date <=' => $dmax_date
					));
				}
				
				$nb_send = $this->CrmStat->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));
				
				$conditions = array('id_crm' => $id_crm, 'view'=>1);
				
				if($this->Session->check('Date')){
					 $conditions = array_merge($conditions, array(
						'CrmStat.date >=' => $dmin_date,
						'CrmStat.date <=' => $dmax_date
					));
				}
				
				$nb_view = $this->CrmStat->find('count',array(
					'conditions' => $conditions,
					'recursive' => -1,
				));

				
				if(substr_count($code,'ISA'))$type = 'NOUVEAU'; else $type = 'ANCIEN';
				if(substr_count($code,'10MIN'))$promo = '10 MIN'; else $promo = '5 MIN';
				
				if($id_crm == 35 || $id_crm == 39){
					$nb_send = number_format($nb_send / 2,0,'.','');
					$nb_view = number_format($nb_view / 2,0,'.','');
				}
				
				if($nb_send > 0){
					$tx_view = $nb_view * 100 / $nb_send;
					$tx_convert = $nb * 100 / $nb_view;
				}else{
					$tx_view = 0;
					$tx_convert = 0;
				}
				
				
				$line = array();
				$line['date'] = $period;
				$line['type'] = $type;
				$line['code'] = $code;
				$line['promo'] = $promo;
				$line['nb'] = $nb;
				$line['send'] = $nb_send;
				$line['view'] = $nb_view;
				$line['tx_view'] = number_format($tx_view,1);
				$line['tx_convert'] = number_format($tx_convert,1);
				
				fputcsv($fp, $line,";");
			}
			fclose($fp);
		exit;	
		}
    }
<?php
App::uses('AppController', 'Controller');


class SupportController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Support','SupportMessage', 'SupportMessageAttachment', 'SupportService', 'SupportAdmin', 'Guest', 'UserLevel','SupportClassification','SupportClassificationMessage');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'support')));

	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow('list_destinataire', 'submit_message');
    }

	public function admin_service() {
		$this->Paginator->settings = array(
				'fields' => array('SupportService.*'),
                'order' => array('SupportService.id' => 'asc'),
                'recursive' => -1,
				'limit' => -1,
				'maxLimit' => -1,
            );

            $services = $this->Paginator->paginate($this->SupportService);

            $this->set(compact('services'));
	}
	
	public function admin_treatment() {
		
	}


	public function admin_service_create() {

		if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Support'] = Tools::checkFormField($requestData['Support'],
                    array('name', 'mail', 'description','who'),
                    array('name','mail')
                );
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

				$save = array();
				$save['name'] = $requestData['Support']['name'];
				$save['mail'] = $requestData['Support']['mail'];
				$save['description'] = $requestData['Support']['description'];
				$save['who'] = $requestData['Support']['who'];
				$save['status'] = 1;
				$this->SupportService->create();
				if($this->SupportService->save($save)){
                        $this->Session->setFlash(__('Le service a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'Support', 'action' => 'service', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');

            }


	}

	public function admin_service_edit($service_id) {

			if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Support'] = Tools::checkFormField($requestData['Support'],
                    array('name', 'mail', 'description','id','who'),
                    array('name','mail')
                );
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

				$save = array();
				$save['name'] = "'".$requestData['Support']['name']."'";
				$save['mail'] = "'".$requestData['Support']['mail']."'";
				$save['who'] = "'".$requestData['Support']['who']."'";
				$save['description'] = "'".addslashes($requestData['Support']['description'])."'";
				$save['status'] = 1;

				if($this->SupportService->updateAll($save,array('SupportService.id' => $requestData['Support']['id']))){
                        $this->Session->setFlash(__('Le service a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'Support', 'action' => 'service', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');

            }



            $service = $this->SupportService->find('first', array(
				'fields' => array('SupportService.*'),
                'conditions' => array('SupportService.id' => $service_id),
                'recursive' => -1
            ));


            if(empty(service)){
                $this->Session->setFlash(__('introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'support', 'action' => 'service', 'admin' => true), false);
            }

            //On insère les données
            $this->set(array('edit' => true, 'service' => $service));
            $this->render('admin_service_edit');
	}

	public function admin_user() {
		$this->Paginator->settings = array(
				'fields' => array('SupportAdmin.*','User.*','SupportService.*'),
                'order' => array('SupportAdmin.id' => 'asc'),
                'paramType' => 'querystring',
				"group" => array('SupportAdmin.user_id', 'SupportService.name', 'SupportAdmin.level'),
				'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array(
                            'User.id = SupportAdmin.user_id',
                        )
                    ),
					array(
                        'table' => 'support_services',
                        'alias' => 'SupportService',
                        'type' => 'left',
                        'conditions' => array(
                            'SupportService.id = SupportAdmin.service_id',
                        )
                    )
                ),
                'limit' => 25
            );

            $admins = $this->Paginator->paginate($this->SupportAdmin);

            $this->set(compact('admins'));
	}

	public function admin_user_create() {

		if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Support'] = Tools::checkFormField($requestData['Support'],
                    array('user_id', 'service', 'level','is_control'),
                    array('user_id', 'service', 'level')
                );
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }


				$all_services = $this->SupportService->find('all',array(
					 'conditions' => array('name' => $requestData['Support']['service']),
				 	'paramType' => 'querystring',
				));
				foreach($all_services as $serv){
					$save = array();
					$save['user_id'] = $requestData['Support']['user_id'];
					$save['service_id'] = $serv['SupportService']['id'];
					$save['level'] = $requestData['Support']['level'];
					$save['is_control'] = $requestData['Support']['is_control'];
						$this->SupportAdmin->create();
						$test = $this->SupportAdmin->save($save);
				}
				if($test){
					$this->Session->setFlash(__('Le matching a été enregistré'), 'flash_success');
					$this->redirect(array('controller' => 'support', 'action' => 'user', 'admin' => true), false);
				}else
					$this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');

            }

		$users = $this->User->find('all',array(
                'conditions' => array('role' => 'admin'),
                'recursive' => -1,
            ));

		$services = $this->SupportService->find('all',array(
				'fields' => array('distinct(name)'),
				'group' => 'name',
			 'paramType' => 'querystring',
            ));

		$this->set(array('admins' => $users,'services' => $services));
	}

	public function admin_user_delete($id) {

		$admin = $this->SupportAdmin->find('first',array(
					 'conditions' => array('id' => $id),
				));
		$service = $this->SupportService->find('first',array(
					 'conditions' => array('id' => $admin['SupportAdmin']['service_id']),
				));

		$all_services = $this->SupportService->find('all',array(
					 'conditions' => array('name' => $service['SupportService']['name']),
				 	'paramType' => 'querystring',
				));
		foreach($all_services as $serv){

			$all_admins = $this->SupportAdmin->find('all',array(
					 'conditions' => array('user_id' => $admin['SupportAdmin']['user_id'],'service_id' => $serv['SupportService']['id'],'level' => $admin['SupportAdmin']['level']),
				 	'paramType' => 'querystring',
				));
			foreach($all_admins as $ad){
				$this->SupportAdmin->id= $ad['SupportAdmin']['id'];
				$test = $this->SupportAdmin->delete();
			}
		   }

		if($test){
            $this->Session->setFlash(__('Le matching a été supprimé'), 'flash_success');
             $this->redirect(array('controller' => 'support', 'action' => 'user', 'admin' => true), false);
        }else
           $this->Session->setFlash(__('Erreur lors de la suppréssion'),'flash_warning');

	}

	public function submit_message(){
		$this->layout = '';

		if($this->request->is('post')){
            $requestData = $this->request->data;

			//captcha
			if(!$requestData['g-recaptcha-response']){
				$this->Session->setFlash(__('Veuillez valider le Captcha.'),'flash_error');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
			}

			$is_connected = false;
			if($this->Auth->loggedIn() && in_array($this->Auth->user('role'), array('agent', 'client'))){
				$is_connected = true;
			}

			/**
			 * Unneeded block code though
			 *
				//Avons-nous deux photos ??
				$n_image = 1;
				$attachment = array();
				$attachment2 = array();
				foreach($requestData['Support']['attachment'] as $file){
					if($n_image == 1)
						$attachment = $file;
					if($n_image == 2)
						$attachment2 = $file;
					$n_image ++;
				}
				$requestData['Support']['attachment'] = $attachment;
				$requestData['Support']['attachment2'] = $attachment2;
			*/

            //On vérifie les champs du formulaire
			if($is_connected){
				$requestData['Support'] = Tools::checkFormField($requestData['Support'], array('service','title','message', 'attachment'), array('message'));
			}else{
				$requestData['Support'] = Tools::checkFormField($requestData['Support'], array('service','title','message', 'nom', 'prenom', 'email', 'attachment'), array('message', 'nom', 'prenom', 'email'));
			}

            if($requestData['Support'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }

            //On vérifie l'email
            if(!$is_connected && !filter_var($requestData['Support']['email'], FILTER_VALIDATE_EMAIL)){
                $this->Session->setFlash(__('Votre email est invalide.'),'flash_warning');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }

            //On crée le profil de l'invité
			if(!$is_connected){
            	$this->Guest->create();
				$this->Guest->save(array(
					'lastname' => $requestData['Support']['nom'],
					'firstname' => $requestData['Support']['prenom'],
					'domain_id' => $this->Session->read('Config.id_domain'),
					'lang_id' => $this->Session->read('Config.id_lang'),
					'email' => $requestData['Support']['email'],
					'ip' => $this->request->clientIp()
            	));
				$guest_id = $this->Guest->id;
			}

			//script upload
			$hasAttachment = false;
			if (!empty($requestData['Support']['attachment'])) {
				foreach ($requestData['Support']['attachment'] as $key=> $file) {
					if (!empty($file['name'])) {//make sure that it has valid name
						if ($this->isUploadedFile($file)) {
							$requestData['Support']['attachment'][$key]['success'] = true;//everything went well!!!
							$hasAttachment = true;//enough to know if has attachment
							/*//Est-ce un fichier image autorisé ??
							if(!Tools::formatFile($this->allowed_mime_types, $requestData['Message']['attachment']['type'],'Image')){
								$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
								$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
								return;
							}*/
						} elseif ($file['error'] != 4) {
							$requestData['Support']['attachment'][$key]['success'] = false;
							$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'), 'flash_warning');
							return;
						}
					}
				}
			}
			/**
			 * $attachment = false;
				$attachment2 = false;
				//Avons-nous un fichier ??

				if(count($requestData['Support']['attachment']) > 1){
					if($this->isUploadedFile($requestData['Support']['attachment'])){
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
					elseif($requestData['Support']['attachment']['error'] != 4){
						$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
						return;
					}
				}
				if(count($requestData['Support']['attachment2']) > 1){
					 if( $this->isUploadedFile($requestData['Support']['attachment2'])){
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
						return;
					}
				}*/

				if($is_connected){
					$guest_id = 0;
					$from_id   = $this->Auth->user('id');
					$to_id = Configure::read('Admin.id');
				}else{
					$guest_id = $guest_id;
					$from_id   = Configure::read('Guest.id');
					$to_id = Configure::read('Admin.id');
				}

			//CREER UN NOUVEAU SUPPORT
			$this->Support->create();
			$this->Support->save(
				array(
                    'service_id'   => $requestData['Support']['service'],
                    'from_id'  => $from_id,
                    'guest_id'     => $guest_id,
					'title'   => $requestData['Support']['title'],
                    'date_add'   => date('Y-m-d H:i:s'),
					'date_upd'   => date('Y-m-d H:i:s'),
                    'status'   => 0,
		   		)
			);

            $this->SupportMessage->create();
            $messageAttributes = array(
				'support_id'   => $this->Support->id,
				'from_id'   => $from_id ,
				'guest_id'  => $guest_id,
				'to_id'     => $to_id,
				'content'   => $requestData['Support']['message'],
				'date_add'   => date('Y-m-d H:i:s'),
				'date_message'   => date('Y-m-d H:i:s'),
				'etat'      => 0,
				'hasAttachment' => $hasAttachment,
				'IP' => getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
			);

			if($this->Support->id && $this->SupportMessage->save($messageAttributes)){
				//keep temporary old saving
				$maxSelfSupportMessageAttachment = 2;

				//always first check
				if (!empty($requestData['Support']['attachment'])) {
					foreach ($requestData['Support']['attachment'] as $key => $file) {
						if (!empty($file['name'])) {//make sure that it has valid name
							$isValidatedFile = $file['success'];
							$fileKey = ($key === 0) ? '' : '-' . ($key + 1);
							//upload single file
							if ($isValidatedFile
								&& !Tools::saveSupportAttachment($file, Configure::read('Site.pathSupportAdmin'), $this->Support->id, $this->SupportMessage->id, $fileKey)) {
								$this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
							} elseif ($isValidatedFile) {
								//On save le nom de la pièce jointe
								$path_parts = pathinfo($file["name"]);
								$extension = $path_parts['extension'];
								//add new attachment
								$this->SupportMessageAttachment->create();
								//build filename
								$fileName = $this->Support->id . '-' . $this->SupportMessage->id;
								$fileName .= $fileKey . '.' . $extension;
								$this->SupportMessageAttachment->save(array(
									'support_message_id' => $this->SupportMessage->id,
									'name' => $fileName
								));
								//old attachment save
								if ($key < $maxSelfSupportMessageAttachment) {
									$this->SupportMessage->saveField('attachment' . $fileKey, $fileName);
								}
							}
						}
					}
				}
				$this->SendAdminMail($this->Support->id,$requestData['Support']['service']);
				$this->Session->setFlash(__('Votre message est envoyé au service administrateur Spiriteo qui vous répondra dans un délai maximum est de 24h.'), 'flash_success');
				$this->redirect(array('controller' => 'contacts', 'action' => 'send'));
			} else{
                $this->Session->setFlash(__('Une erreur est survenue. Veuillez réessayer'),'flash_warning');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }
        }
        $this->redirect(array('controller' => 'home', 'action' => 'index'));
	}

	public function SendAdminMail($support_id = 0,$service_id, $level = 1, $is_new = 1){
		//find all user connected to this service and send CMS mail
		$admins = $this->SupportAdmin->find('all',array(
									'fields' => array('User.email'),
								'conditions' => array('service_id' => $service_id,'level' => $level),
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
							'conditions' => array('id' => $service_id),
							'recursive' => -1,
						));
		$domain = Router::url('/', true);
		if($is_new)
			$title = _('Nouveau message Support / ').$service['SupportService']['name'].' - '.ucfirst($service['SupportService']['who']);
		else
			$title = _('Un(e) admin vous attribue un nouveaux message / ').$service['SupportService']['name'].' - '.ucfirst($service['SupportService']['who']);
		foreach($admins as $admin){
			if($support_id){
				$this->sendCmsTemplateByMail(452, 1, $admin['User']['email'], array(
						'URL_TICKET' => $domain.'admin/support/fil/'.$support_id ,
						'SUBJECTSUPPORT' => $title,
				));
			}else{
				$this->sendCmsTemplateByMail(452, 1, $admin['User']['email'], array(
						'URL_TICKET' => $domain.'admin/support/message/',
						'SUBJECTSUPPORT' => $title,
				));
			}

		}

	}

	public function admin_message(){

		//get all auth acces services and make conditions for request IN array()
		$user_co = $this->Session->read('Auth.User');
		$service_list = $this->SupportAdmin->find('all',array(
								'conditions' => array('user_id' => $user_co['id']),
									'recursive' => -1,
								));
		$services = array(0);

		foreach($service_list as $serv){
			array_push($services,$serv['SupportAdmin']['service_id']);
		}

		$conditions = array('Support.service_id IN' => $services);

		if(isset($this->request->data['Support']) && is_numeric($this->request->data['Support']['status'])){
			 $conditions = array_merge($conditions, array(
                    'Support.status' => $this->request->data['Support']['status'],
               ));
		}
		$email = '';
		if(isset($this->request->data['Support']) && !empty($this->request->data['Support']['email'])){
			 $conditions = array_merge($conditions, array(
				 'OR' => array(
                    array('User.email' => $this->request->data['Support']['email']),
                    array('Guest.email' => $this->request->data['Support']['email'])
                )

               ));
			$email = $this->request->data['Support']['email'];
		}
		$name = '';
		if(isset($this->request->data['Support']) && !empty($this->request->data['Support']['name'])){
			 $conditions = array_merge($conditions, array(
				 	 'OR' => array(
                    array('User.firstname LIKE' => '%'.$this->request->data['Support']['name'].'%'),
					array('User.lastname LIKE' => '%'.$this->request->data['Support']['name'].'%'),
					array('Guest.firstname LIKE' => '%'.$this->request->data['Support']['name'].'%'),
					array('Guest.lastname LIKE' => '%'.$this->request->data['Support']['name'].'%'),
                    array('User.pseudo LIKE' => '%'.$this->request->data['Support']['name'].'%')
                )

               ));
			$name = $this->request->data['Support']['name'];
		}
		
		$classifications = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => NULL),
					'recursive' => -1,
					'order' => 'SupportClassification.num asc',
				));
		
		$list_classification = array('0'=>'Choisir');
		foreach($classifications as $classification){
			
			$classifications_child = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => $classification['SupportClassification']['id']),
					'recursive' => -1,
					'order' => 'SupportClassification.num asc',
				));
			$classifications_child = $this->order_classif($classifications_child);
			foreach($classifications_child as $child){
				$list_classification[$child['SupportClassification']['id']] = $child['SupportClassification']['num'].' '.$classification['SupportClassification']['name'].' - '.$child['SupportClassification']['name'];
			}
		}
		$classif = '';
		$classif_join = '';
		if(isset($this->request->data['Support']) && !empty($this->request->data['Support']['classification'])){
			
			$classif_join = array(
                        'table' => 'support_classification_messages',
                        'alias' => 'SupportClassificationMessage',
                        'type' => 'right',
                        'conditions' => array(
                            'SupportClassificationMessage.classification_id = '.$this->request->data['Support']['classification'],
							'SupportClassificationMessage.message_id = Support.id',
                        )
                    );
			
			$classif = $this->request->data['Support']['classification'];
		}
		


		$this->Paginator->settings = array(
				'fields' => array('Support.*','User.*','SupportService.*'),
                'order' => array('Support.date_upd' => 'desc'),
				'conditions' => $conditions,
                'paramType' => 'querystring',
				'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array(
                            'User.id = Support.from_id',
                        )
                    ),
					 array(
                        'table' => 'guests',
                        'alias' => 'Guest',
                        'type' => 'left',
                        'conditions' => array(
                            'Guest.id = Support.guest_id',
                        )
                    ),
					array(
                        'table' => 'support_services',
                        'alias' => 'SupportService',
                        'type' => 'left',
                        'conditions' => array(
                            'SupportService.id = Support.service_id',
                        )
                    ),
					$classif_join
                ),
                'limit' => 25
            );

            $messages = $this->Paginator->paginate($this->Support);

			//get first message
			foreach($messages as &$message){
				$message['Support']['classified'] = '';
				
				$classifies = $this->SupportClassificationMessage->find('first', array(
					'conditions' => array('SupportClassificationMessage.message_id' => $message['Support']['id']),
					'recursive' => -1,
				));
				if($classifies)$message['Support']['classified'] = 'Classifié';
				
				$mes = $this->SupportMessage->find('first',array(
								'conditions' => array('support_id' => $message['Support']['id'],'deleted' => 0),
									'recursive' => -1,
									'order' => 'id'
								));

				$message['Support']['message'] = $mes['SupportMessage']['content'];
				$message['Support']['hasAttachment'] = (bool) $mes['SupportMessage']['hasAttachment'];

				if($message['User']['id'] == 2){
					$guest = $this->Guest->find('first',array(
								'conditions' => array('id' => $message['Support']['guest_id']),
									'recursive' => -1,
								));
					$message['User'] = $guest['Guest'];
				}

				if($message['Support']['owner_id']){
					$owner = $this->User->find('first',array(
								'conditions' => array('id' => $message['Support']['owner_id']),
									'recursive' => -1,
								));
					$message['Support']['owner'] = $owner['User']['firstname'];
				}
			}


            $this->set(compact('messages','email','name','list_classification','classif'));

	}

  /**
	 * @param $support_id
	 * @return string
	 */
	protected function getSupportMessageDirectory($support_id)
	{
		return (strlen($support_id) > 1) ? $support_id[0] . '/' . $support_id[1] : $support_id;
	}

	/**
	 * allow to download any file type
	 * Main difference here is that it takes attachment id instead.
	 * @param $id
	 * @return CakeResponse|null
	 */
	public function admin_downloadFile($id)
	{
		//Si pas de nom, redirection mails
		if(empty($id)){
			$this->Session->setFlash(__('Le fichier est introuvable.'), 'flash_warning');
			$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
		}

		$attachmentModel = $this->SupportMessageAttachment->find('first',array(
			'conditions' => array('SupportMessageAttachment.id' => $id)
		));

		$supportMessageAttachment = $attachmentModel['SupportMessageAttachment'];
		$supportMessage = $attachmentModel['SupportMessage'];

		//find correct directory
		$folder = $this->getSupportMessageDirectory($supportMessage['support_id']);
		//file name
		$filename = Configure::read('Site.pathSupport') . '/'.$folder.'/' . $supportMessageAttachment['name'];

		if(file_exists($filename)){
			//Charge le model
			//Est-il autorisé à lire cette pièce jointe ??
			$path_parts = pathinfo($filename);
			$extension = $path_parts['extension'];

			$this->response->file($filename, array('download' => true, 'name' => __('Pièce jointe').'.'.$extension));
			return $this->response;
		}
		$this->Session->setFlash(__('Le fichier n\'existe pas.'), 'flash_warning');
		$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
	}

  //TODO: remove admin_downloadAttachment & admin_downloadAttachment2 in future only one common function for every download in the system.
	public function admin_downloadAttachment($id){
         //Si pas de nom, redirection mails
        if(empty($id)){
            $this->Session->setFlash(__('Le fichier est introuvable.'), 'flash_warning');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

		$message = $this->SupportMessage->find('first',array(
                'conditions' => array('id' => $id),
                'recursive' => -1,
            ));

        //Est-ce que le fichier existe ??

		if(strlen($message['SupportMessage']['support_id']) > 1){
			$folder = $message['SupportMessage']['support_id'][0].'/'.$message['SupportMessage']['support_id'][1];
		}else{
			$folder = $message['SupportMessage']['support_id'];
		}

        $filename = Configure::read('Site.pathSupport').'/'.$folder.'/'.$message['SupportMessage']['attachment'];

        if(file_exists($filename)){
            //Charge le model
            //Est-il autorisé à lire cette pièce jointe ??
			$path_parts = pathinfo($filename);
			$extension = $path_parts['extension'];

           $this->response->file($filename, array('download' => true, 'name' => __('Pièce jointe').'.'.$extension));
           return $this->response;
        }
        $this->Session->setFlash(__('Le fichier n\'existe pas.'), 'flash_warning');
        $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
    }

	public function admin_downloadAttachment2($id){
         //Si pas de nom, redirection mails
        if(empty($id)){
            $this->Session->setFlash(__('Le fichier est introuvable.'), 'flash_warning');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

		$message = $this->SupportMessage->find('first',array(
                'conditions' => array('id' => $id),
                'recursive' => -1,
            ));

        //Est-ce que le fichier existe ??

		if(strlen($message['SupportMessage']['support_id']) > 1){
			$folder = $message['SupportMessage']['support_id'][0].'/'.$message['SupportMessage']['support_id'][1];
		}else{
			$folder = $message['SupportMessage']['support_id'];
		}

        $filename = Configure::read('Site.pathSupport').'/'.$folder.'/'.$message['SupportMessage']['attachment2'];

        if(file_exists($filename)){
            //Charge le model
            //Est-il autorisé à lire cette pièce jointe ??
			$path_parts = pathinfo($filename);
			$extension = $path_parts['extension'];

           $this->response->file($filename, array('download' => true, 'name' => __('Pièce jointe').'.'.$extension));
           return $this->response;
        }
        $this->Session->setFlash(__('Le fichier n\'existe pas.'), 'flash_warning');
        $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
    }

	public function admin_fil($support_id = null){

        $user_co = $this->Session->read('Auth.User');
		
		if($this->request->is('post')){
			$requestData = $this->request->data;
			if($requestData['Support']['support_id'])
			$support_id = $requestData['Support']['support_id'];
			if(!$support_id && $requestData['Support']['support_id'])
			$support_id = $requestData['Support']['id'];
		}
		
		if (!$support_id)
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);

		$support = $this->Support->find('first',array(
                'conditions' => array('id' => $support_id),
                'recursive' => -1,
            ));

		if (!$support)
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);


		$service = $this->SupportService->find('first',array(
			'conditions' => array('id' => $support['Support']['service_id']),
                'recursive' => -1,
            ));
		
		
		//check moderator status
		$moderator = $this->SupportAdmin->find('first',array(
							'conditions' => array('service_id' => $support['Support']['service_id'],'level' => $support['Support']['level'],'user_id' => $user_co['id']),
							'recursive' => -1,
						));
		
		if($this->request->is('post')){
            $requestData = $this->request->data;

			//save comm
			if(!empty($requestData['Support']['comm'])){
				$this->Support->id = $requestData['Support']['id'];
				$this->Support->saveField('comm', $requestData['Support']['comm']);
				$this->Session->setFlash(__('Votre commentaire est enregistré'), 'flash_success');
				$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$requestData['Support']['id'], 'admin' => true), false);
			}elseif(!empty($requestData['Support']['classifs'])){
				
				if($this->SupportClassificationMessage->deleteAll(array('SupportClassificationMessage.message_id'=>$requestData['Support']['id']), false)){
					
					foreach($requestData['Support']['classifs'] as $classif_id){
						$this->SupportClassificationMessage->create();

				        $this->SupportClassificationMessage->save(array(
						'message_id'   => $requestData['Support']['id'],
						'classification_id'   => $classif_id,
						));
					}
					
					$this->Session->setFlash(__('Votre changement de classification est enregistrée'), 'flash_success');
				}else{
					$this->Session->setFlash(__('Votre changement de classification a échoué'), 'flash_success');
				}
				
				$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$requestData['Support']['id'], 'admin' => true), false);
			}elseif(!empty($requestData['Support']['service'])){
				//save service
				$this->Support->id = $requestData['Support']['id'];
				$this->Support->saveField('service_id', $requestData['Support']['service']);
				$this->SendAdminMail($requestData['Support']['id'],$requestData['Support']['service'],1,0);
				$this->Session->setFlash(__('Votre changement de service est enregistrée'), 'flash_success');
				$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$requestData['Support']['id'], 'admin' => true), false);
			}elseif(!empty($requestData['Support']['level'])){
				//save level
				$thesupport = $this->Support->find('first',array(
					'conditions' => array('id' => $requestData['Support']['id']),
					'recursive' => -1,
				));
				$this->Support->id = $requestData['Support']['id'];
				$this->Support->saveField('level', $requestData['Support']['level']);
				$this->SendAdminMail($requestData['Support']['id'],$thesupport['Support']['service_id'],$requestData['Support']['level'],0);
				$this->Session->setFlash(__('Votre changement de level est enregistrée'), 'flash_success');
				$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$requestData['Support']['id'], 'admin' => true), false);
			}else{
				//send message
				$requestData['Support'] = Tools::checkFormField($requestData['Support'], array('support_id','content', 'attachment','support_moderate'), array('support_id','content'));
				
				if($requestData['Support'] === false){
					$this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
					$this->redirect(array('controller' => 'support', 'action' =>'admin_message'));
				}


				$attachment = false;
				$attachment2 = false;
				//Avons-nous un fichier ??

        		$hasAttachment = false;
				if (!empty($requestData['Support']['attachment'])) {
					foreach ($requestData['Support']['attachment'] as $key=> $file) {
						if (!empty($file['name'])) {//make sure that it has valid name
							if ($this->isUploadedFile($file)) {
								$requestData['Support']['attachment'][$key]['success'] = true;//everything went well!!!
								$hasAttachment = true;//enough to know if has attachment
								/*//Est-ce un fichier image autorisé ??
								if(!Tools::formatFile($this->allowed_mime_types, $requestData['Message']['attachment']['type'],'Image')){
									$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
									$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
									return;
								}*/
							} elseif ($file['error'] != 4) {
								$requestData['Support']['attachment'][$key]['success'] = false;
								$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'), 'flash_warning');
								return;
							}
						}
					}
				}

				$this->SupportMessage->create();

				if($support['Support']['id'] && $this->SupportMessage->save(array(
						'support_id'   => $support['Support']['id'],
						'from_id'   => $user_co['id'],
						'guest_id'  => $support['Support']['guest_id'],
						'to_id'     => $support['Support']['from_id'],
						'content'   => $requestData['Support']['content'],
						'date_add'   => date('Y-m-d H:i:s'),
						'date_message'   => date('Y-m-d H:i:s'),
						'etat'      => 0,
						'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
				))){

				$maxSelfSupportMessageAttachment = 2;
				$mail_attachments = array();
				//always first check
				if (!empty($requestData['Support']['attachment'])) {
					foreach ($requestData['Support']['attachment'] as $key => $file) {
						if (!empty($file['name'])) {//make sure that it has valid name
							$isValidatedFile = $file['success'];
							$fileKey = ($key === 0) ? '' : '-' . ($key + 1);
							//upload single file
							if ($isValidatedFile
								&& !Tools::saveSupportAttachment($file, Configure::read('Site.pathSupportAdmin'), $support['Support']['id'], $this->SupportMessage->id, $fileKey)) {
								$this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
							} elseif ($isValidatedFile) {
								//On save le nom de la pièce jointe
								$path_parts = pathinfo($file["name"]);
								$extension = $path_parts['extension'];
								//add new attachment
								$this->SupportMessageAttachment->create();
								//build filename
								$fileName = $support['Support']['id'] . '-' . $this->SupportMessage->id;
								$fileName .= $fileKey . '.' . $extension;
								$this->SupportMessageAttachment->save(array(
									'support_message_id' => $this->SupportMessage->id,
									'name' => $fileName
								));
                $mail_attachments[] = Configure::read('Site.pathSupportAdmin').'/'.$support['Support']['id'][0].'/'.$support['Support']['id'][1].'/'.$support['Support']['id'].'-'. $this->SupportMessage->id .'.'.$extension;
								//old attachment save
								if ($key < $maxSelfSupportMessageAttachment) {
									$this->SupportMessage->saveField('attachment' . $fileKey, $fileName);
								}
							}
						}
					}
				}


					//send Email with attachment to_id

					$title = '[#'.$support['Support']['id'].'] '.$support['Support']['title'];

					if($support['Support']['guest_id']){
						$user = $this->Guest->find('first',array(
							'conditions' => array('id' => $support['Support']['guest_id']),
							'recursive' => -1,
						));
						$email = $user['Guest']['email'];
					}else{
						$user = $this->User->find('first',array(
							'conditions' => array('id' => $support['Support']['from_id']),
							'recursive' => -1,
						));
						$email = $user['User']['email'];

					}

					

					if($requestData['Support']['support_moderate']){
						$this->Support->id = $support['Support']['id'];
						$this->Support->saveField('owner_id', $user_co['id']);
						//$this->Support->saveField('status', 2);
						$this->Support->saveField('moderate_response', $this->SupportMessage->id);
						$this->Support->saveField('date_upd', date('Y-m-d H:is'));
						$this->Session->setFlash(__('Votre message est en cours de moderation.'), 'flash_warning');

						//send email
						$title = 'Un message support a moderer';
						$admin_email = 'contact@talkappdev.com';
						$this->sendCmsTemplateByMail(452, 1, $admin_email, array(
								'URL_TICKET' => $domain.'admin/support/fil/'.$support['Support']['id'],
								'SUBJECTSUPPORT' => $title,
						));

					}else{
						$this->Support->id = $support['Support']['id'];
						$this->Support->saveField('status', 1);
						$this->Support->saveField('owner_id', $user_co['id']);
						$this->Support->saveField('date_upd', date('Y-m-d H:is'));

						$content = $requestData['Support']['content'];
						$datasEmail = array('admin' => true, 'content' => $content);

						$service = $this->SupportService->find('first',array(
								'conditions' => array('id' => $support['Support']['service_id']),
								'recursive' => -1,
							));

						$reply = $service['SupportService']['mail'];
						$sender = $service['SupportService']['mail'];

						$this->sendEmailWithAttachment($email,$title,'support',array('param' => $datasEmail),'default',$sender,$reply, $mail_attachments);
						$this->Session->setFlash(__('Votre message a été envoyé.'), 'flash_warning');
					}


					$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$support['Support']['id'], 'admin' => true), false);
				}
			}
		}

		

		$services = $this->SupportService->find('all',array(
                'recursive' => -1,
			'fields' => array('distinct(name)','id'),
			'conditions' => array('name !=' => $service['SupportService']['name']),
				'group' => 'name',
			 'paramType' => 'querystring',
            ));
		array_push($services,$service);
		//check if administarteur for service
		foreach($services as $k_serv => $serv){
			$the_servive = $this->SupportAdmin->find('first',array(
			'conditions' => array('service_id' => $serv['SupportService']['id'],'level' => 1),
                'recursive' => -1,
            ));
			if(!$the_servive)unset($services[$k_serv]);
		}
		$the_servive = $this->SupportAdmin->find('all',array(
                'recursive' => -1,
				'conditions' => array('service_id' => $support['Support']['service_id']),
				'group' => 'level'
            ));
		$levels = array();
		foreach($the_servive as $ser){
			array_push($levels, $ser['SupportAdmin']['level']);
		}

		if($support['Support']['guest_id']){
			$guest = $this->Guest->find('first',array(
			'conditions' => array('id' => $support['Support']['guest_id']),
                'recursive' => -1,
            ));
			$user = array();
			$user['User'] = $guest['Guest'];
			$user['User']['role'] = 'guest';
		}else{
			$user = $this->User->find('first',array(
			'conditions' => array('id' => $support['Support']['from_id']),
                'recursive' => -1,
            ));
		}
		
		$list_classification = array();
		
		$classifications = $this->SupportClassification->find('all', array(
					'recursive' => -1,
					'conditions' => array('SupportClassification.parent_id' => NULL),
					'order' => 'SupportClassification.num asc',
				));
		
		
		foreach($classifications as $classification){
			
			$classifications_child = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => $classification['SupportClassification']['id']),
					'recursive' => -1,
					'order' => 'SupportClassification.num asc',
				));
			
			$classifications_child = $this->order_classif($classifications_child);
			
			foreach($classifications_child as $child){
				$list_classification[] = array($child['SupportClassification']['id'] => $child['SupportClassification']['num'].' '.$classification['SupportClassification']['name'].' - '.$child['SupportClassification']['name']);
			}
		}
		
		$classification_message = $this->SupportClassificationMessage->find('all', array(
					'fields' => array('SupportClassification.*','SupportClassificationMessage.*'),
					'recursive' => -1,
					'conditions' => array('message_id' => $support['Support']['id']),
					'order' => 'SupportClassification.name asc',
					'joins' => array(
							array('table' => 'support_classifications',
								'alias' => 'SupportClassification',
								'type' => 'inner',
								'conditions' => array('SupportClassification.id = SupportClassificationMessage.classification_id')
							)
						),
				));

		$messages = $this->SupportMessage->find('all',array(
			'conditions' => array('support_id' => $support['Support']['id']),
            //    'recursive' => -1,
				'order' => 'SupportMessage.date_add asc',
            ));

		//update status to read OK
		foreach($messages as $mes){
			if(!$mes['SupportMessage']['etat'] && $mes['SupportMessage']['to_id'] <= 1){
				$this->SupportMessage->id = $mes['SupportMessage']['id'];
				$this->SupportMessage->saveField('etat', 1);
			}

		}

		$is_live = false;

		if($support['Support']['user_live'] && $support['Support']['user_live'] != $user_co['id']){
			$this->Session->setFlash(__('Ce ticket est en cours de traitement, votre acces est en lecture seule.'), 'flash_success');
			$is_live = true;
		}else{
			$this->Support->id = $support['Support']['id'];
			$this->Support->saveField('user_live', $user_co['id']);
		}

		// check si l'utilisateur sur une autre tickets
        $checkUserLiveSupport = $this->Support->find('first',array(
            'conditions' => array('user_live' =>  $user_co['id'],'id != ' =>  $support_id),
            'recursive' => -1,
        ));

		if(!empty($checkUserLiveSupport)){
		    $is_live=true;
            $this->Session->setFlash(__('Vous travailler deja sur un autre ticket.'), 'flash_error');
        }

        $moderator_test = $this->SupportAdmin->find('first',array(
							'conditions' => array('service_id' => $support['Support']['service_id'],'level >=' => $support['Support']['level'],'user_id' => $user_co['id']),
							'recursive' => -1,
						));
		if($support['Support']['moderate_response'] && $moderator_test['SupportAdmin']['is_control'])$is_live = true;

		if(!$moderator_test){
			$this->Session->setFlash(__('Vous avez accès en lecture seule.'), 'flash_error');
		 	$is_live = true;
		}

        $adminSupportLevel = $this->SupportAdmin->find('first',array(
            'conditions' => array('service_id' => $support['Support']['service_id']),
        ));

        // get level user connected
        $userConnectedSupportLevel= $this->SupportAdmin->find('first',array(
            'conditions' => array('user_id' =>$user_co['id']),
			'order' => array('level DESC'),
        ));
		
		$live_person = '';
		
		if($support['Support']['user_live']){
				$userLive= $this->User->find('first',array(
				'conditions' => array('id' =>$support['Support']['user_live']),
			));
			$live_person = $userLive['User']['firstname'];
		}
		
		$is_control = $moderator['SupportAdmin']['is_control'];

		$this->set(compact('support','service','services','user','messages','levels','is_live','adminSupportLevel','userConnectedSupportLevel','live_person','list_classification','classification_message', 'is_control'));

	}

	public function admin_debloque_support($support_id){
        if(empty($support_id)){
            $this->Session->setFlash(__('Support introuvable ! .'),'flash_error');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

        $support = $this->Support->find('first',array(
            'conditions' => array('id' => $support_id),
            'recursive' => -1,
        ));

        if (!$support)
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);

        $user_co = $this->Session->read('Auth.User');

        if($this->Support->updateAll(array('user_live'=>$user_co['id']),array('Support.id' => $support_id))){
            $this->Session->setFlash(__('Votre changement de service est enregistrée'), 'flash_success');
            $this->redirect(array('controller' => 'support', 'action' => 'fil/'.$support_id, 'admin' => true), false);
        }else{
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

    }

	public function admin_fil_moderate($support_id = null){

		if($this->request->is('post')){
            $requestData = $this->request->data;

			//send message
			$requestData['Support'] = Tools::checkFormField($requestData['Support'], array('support_id','content'), array('support_id','content'));

			if($requestData['Support'] === false){
					$this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
					$this->redirect(array('controller' => 'support', 'action' =>'admin_message'));
			}

			$support = $this->Support->find('first',array(
					'conditions' => array('id' => $requestData['Support']['support_id']),
					'recursive' => -1,
			));

			if (!$support)
				$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);

			$supportMessage = $this->SupportMessage->find('first',array(
					'conditions' => array('id' => $support['Support']['moderate_response']),
					'recursive' => -1,
			));

			if (!$supportMessage){
				$this->Session->setFlash(__('Aucun message a moderer'),'flash_error');
				$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
			}



			$this->SupportMessage->id = $supportMessage['SupportMessage']['id'];
			$this->SupportMessage->saveField('content', $requestData['Support']['content']);

			//send Email with attachment to_id

			$title = '[#'.$support['Support']['id'].'] '.$support['Support']['title'];

			if($support['Support']['guest_id']){
						$user = $this->Guest->find('first',array(
							'conditions' => array('id' => $support['Support']['guest_id']),
							'recursive' => -1,
						));
						$email = $user['Guest']['email'];
			}else{
						$user = $this->User->find('first',array(
							'conditions' => array('id' => $support['Support']['from_id']),
							'recursive' => -1,
						));
						$email = $user['User']['email'];

			}

			$user_co = $this->Session->read('Auth.User');


			$this->Support->id = $support['Support']['id'];
			$this->Support->saveField('status', 1);
			$this->Support->saveField('moderate_response', NULL);
			$this->Support->saveField('date_upd', date('Y-m-d H:is'));

			$content = $requestData['Support']['content'];
			$datasEmail = array('admin' => true, 'content' => $content);

			$mail_attachments = array();

			/*if($supportMessage['SupportMessage']['attachment']){
				$path_parts = pathinfo($supportMessage['SupportMessage']['attachment']);
				$extension = $path_parts['extension'];
				$mail_attachments[] = Configure::read('Site.pathSupportAdmin').'/'.$support['Support']['id'][0].'/'.$support['Support']['id'][1].'/'.$support['Support']['id'].'-'. $supportMessage['SupportMessage']['id'] .'.'.$extension;
			}
			if($supportMessage['SupportMessage']['attachment2']){
				$path_parts = pathinfo($supportMessage['SupportMessage']['attachment2']);
				$extension = $path_parts['extension'];
				$mail_attachments[] = Configure::read('Site.pathSupportAdmin').'/'.$support['Support']['id'][0].'/'.$support['Support']['id'][1].'/'.$support['Support']['id'].'-'. $supportMessage['SupportMessage']['id'].'-2' .'.'.$extension;
			}*/

      if($supportMessage['SupportMessage']['hasAttachment']	){
        foreach($supportMessage['SupportMessageAttachment'] as $attachmentModel){
           $attachment = $attachmentModel['name'];
				   $attachmentExtension = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
           $mail_attachments[] = Configure::read('Site.pathSupportAdmin').'/'.$support['Support']['id'][0].'/'.$support['Support']['id'][1].'/'.$attachment;
        }
      }



			$service = $this->SupportService->find('first',array(
								'conditions' => array('id' => $support['Support']['service_id']),
								'recursive' => -1,
			));

			$reply = $service['SupportService']['mail'];
			$sender = $service['SupportService']['mail'];

			$this->sendEmailWithAttachment($email,$title,'support',array('param' => $datasEmail),'default',$sender,$reply, $mail_attachments);
			$this->Session->setFlash(__('Votre message a été envoyé.'), 'flash_warning');


			$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$support['Support']['id'], 'admin' => true), false);
		}
		$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
	}

	public function admin_fil_delete($support_id){
	    if(empty($support_id)){
            $this->Session->setFlash(__('Message introuvable ! .'),'flash_error');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

        $support = $this->Support->find('first',array(
            'conditions' => array('id' =>$support_id),
            'recursive' => -1,
        ));

        if (!$support)
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);

        $supportMessage = $this->SupportMessage->find('first',array(
            'conditions' => array('id' => $support['Support']['moderate_response']),
            'recursive' => -1,
        ));

        if (!$supportMessage){
            $this->Session->setFlash(__('Aucun message a moderer'),'flash_error');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }

        if($this->Support->updateAll(array('moderate_response'=>null),array('Support.id' => $support_id)) && $this->SupportMessage->updateAll(array('deleted'=>1),array('SupportMessage.id' => $supportMessage['SupportMessage']['id']))){
            $this->Session->setFlash(__('Message a modérer supprimé'), 'flash_success');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }
        else{
            $this->Session->setFlash(__('Erreur lors de la suppréssion'),'flash_warning');
            $this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
        }
    }

	public function admin_list_destinataire() {

		$this->layout = false;
        $this->autoRender = false;
		 $array = array();
		 if($this->request->is('ajax')){
			if($this->request->query['term']){

				$conditions = array('User.deleted' => 0);

				$conditions = array_merge($conditions, array(
						 'OR' => array(
						array('User.firstname LIKE' => '%'.$this->request->query['term'].'%'),
						array('User.lastname LIKE' => '%'.$this->request->query['term'].'%'),
						array('User.pseudo LIKE' => '%'.$this->request->query['term'].'%'),
						array('User.email LIKE' => '%'.$this->request->query['term'].'%')
					)

				   ));

				$users = $this->User->find('all', array(
						'fields' => array('User.id', 'User.firstname', 'User.lastname', 'User.pseudo', 'User.role', 'User.email'),
						'conditions' => $conditions,
						'recursive' => -1
					));


				foreach($users  as $user){
					if($user['User']['role'] == 'agent')
					$array[] = array('value' => $user['User']['id'], 'label' => $user['User']['firstname'].' '.$user['User']['lastname'].' ('.$user['User']['pseudo'].') - '.$user['User']['email']);
					if($user['User']['role'] == 'client')
					$array[] = array('value' => $user['User']['id'], 'label' => $user['User']['firstname'].' '.$user['User']['lastname'].' - '.$user['User']['email']);
				}


			}
		 }
		$this->jsonRender($array);

	}

	public function admin_write() {

		if($this->request->is('post')){
        	$requestData = $this->request->data;

			//Avons-nous deux photos ??
			/*	$n_image = 1;
				$attachment = array();
				$attachment2 = array();
				foreach($requestData['Support']['attachment'] as $file){
					if($n_image == 1)
						$attachment = $file;
					if($n_image == 2)
						$attachment2 = $file;
					$n_image ++;
				}
				$requestData['Support']['attachment'] = $attachment;
				$requestData['Support']['attachment2'] = $attachment2;*/

			$requestData['Support'] = Tools::checkFormField($requestData['Support'], array('who_id','content','title', 'attachment','guestmail','guestname','guestfirstname'), array('content','title'));

			if($requestData['Support'] === false){
					$this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
					$this->redirect(array('controller' => 'support', 'action' =>'admin_write'));
			}

			if(!$requestData['Support']['who_id'] && $requestData['Support']['guestmail']) {
					if(!$requestData['Support']['guestname'])$requestData['Support']['guestname'] = '.';
					if(!$requestData['Support']['guestfirstname'])$requestData['Support']['guestfirstname'] = '.';
					$this->Guest->create();
					$this->Guest->save(array(
					'lastname' => $requestData['Support']['guestname'],
					'firstname' => $requestData['Support']['guestfirstname'],
					'domain_id' => $this->Session->read('Config.id_domain'),
					'lang_id' => 1,
					'email' => $requestData['Support']['guestmail'],
					'ip' => ''
					));
					$guest_id = $this->Guest->id;
					$to_id = $this->Guest->id;
					$email = $requestData['Support']['guestmail'];
					$from_id = Configure::read('Guest.id');
				}else{
					$guest_id = 0;
					$to_id = $requestData['Support']['who_id'];
					$from_id = $requestData['Support']['who_id'];
					$user = $this->User->find('first',array(
							'conditions' => array('id' => $requestData['Support']['who_id']),
							'recursive' => -1,
						));
						$email = $user['User']['email'];
				}


			$this->Support->create();
			$this->Support->save(array(
                    'service_id'   => 1,
                    'from_id'  => $from_id,
					'guest_id'  => $guest_id,
					'title'   => $requestData['Support']['title'],
                    'date_add'   => date('Y-m-d H:i:s'),
					'date_upd'   => date('Y-m-d H:i:s'),
                    'status'   => 0,
               ));

			if (!$this->Support->id)
					$this->redirect(array('controller' => 'support', 'action' => 'write', 'admin' => true), false);

              if($this->Support->id){

				  $attachment = false;
				 $attachment2 = false;
					//Avons-nous un fichier ??

/*
					if(count($requestData['Support']['attachment']) > 1){
						if($this->isUploadedFile($requestData['Support']['attachment'])){
							//Pièce jointe
							$attachment = true;
						}
						//S'il y a eu une erreur dans l'upload du fichier
						elseif($requestData['Support']['attachment']['error'] != 4){
							$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'),'flash_warning');
							return;
						}
					}
					if(count($requestData['Support']['attachment2']) > 1){
						 if( $this->isUploadedFile($requestData['Support']['attachment2'])){
							//Pièce jointe
							$attachment2 = true;
						}
						//S'il y a eu une erreur dans l'upload du fichier
						elseif($requestData['Message']['attachment2']['error'] != 4){
							$this->Session->setFlash(__('Erreur dans le chargement de votre deuxième fichier.'),'flash_warning');
							return;
						}
					}
*/
                $hasAttachment = false;
			if (!empty($requestData['Support']['attachment'])) {
				foreach ($requestData['Support']['attachment'] as $key=> $file) {
					if (!empty($file['name'])) {//make sure that it has valid name
						if ($this->isUploadedFile($file)) {
							$requestData['Support']['attachment'][$key]['success'] = true;//everything went well!!!
							$hasAttachment = true;//enough to know if has attachment
							/*//Est-ce un fichier image autorisé ??
							if(!Tools::formatFile($this->allowed_mime_types, $requestData['Message']['attachment']['type'],'Image')){
								$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
								$this->set(array('agent' => $agent, 'creditMail' => $agent['User']['creditMail']));
								return;
							}*/
						} elseif ($file['error'] != 4) {
							$requestData['Support']['attachment'][$key]['success'] = false;
							$this->Session->setFlash(__('Erreur dans le chargement de votre fichier.'), 'flash_warning');
							return;
						}
					}
				}
			}

				$user_co = $this->Session->read('Auth.User');

				$this->SupportMessage->create();
				if( $this->SupportMessage->save(array(
						'support_id'   => $this->Support->id,
						'from_id'   => $user_co['id'],
						'to_id'     => $to_id,
						'guest_id'     => $guest_id,
						'content'   => $requestData['Support']['content'],
						'date_add'   => date('Y-m-d H:i:s'),
						'date_message'   => date('Y-m-d H:i:s'),
						'etat'      => 0,
						'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
				))){
					//Save la pièce jointe
					$mail_attachments = array();
					
          //keep temporary old saving
				$maxSelfSupportMessageAttachment = 2;

				//always first check
				if (!empty($requestData['Support']['attachment'])) {
					foreach ($requestData['Support']['attachment'] as $key => $file) {
						if (!empty($file['name'])) {//make sure that it has valid name
							$isValidatedFile = $file['success'];
							$fileKey = ($key === 0) ? '' : '-' . ($key + 1);
							//upload single file
							if ($isValidatedFile
								&& !Tools::saveSupportAttachment($file, Configure::read('Site.pathSupportAdmin'), $this->Support->id, $this->SupportMessage->id, $fileKey)) {
								$this->Session->setFlash(__('Votre message a été envoyé. Cependant la pièce jointe n\'a pu être envoyé.'), 'flash_warning');
							} elseif ($isValidatedFile) {
								//On save le nom de la pièce jointe
								$path_parts = pathinfo($file["name"]);
								$extension = $path_parts['extension'];
								//add new attachment
								$this->SupportMessageAttachment->create();
								//build filename
								$fileName = $this->Support->id . '-' . $this->SupportMessage->id;
								$fileName .= $fileKey . '.' . $extension;
								$this->SupportMessageAttachment->save(array(
									'support_message_id' => $this->SupportMessage->id,
									'name' => $fileName
								));
                $mail_attachments[] = Configure::read('Site.pathSupportAdmin').'/'.$this->Support->id[0].'/'.$this->Support->id[1].'/'.$this->Support->id.'-'. $this->SupportMessage->id .'.'.$extension;
								//old attachment save
								if ($key < $maxSelfSupportMessageAttachment) {
									$this->SupportMessage->saveField('attachment' . $fileKey, $fileName);
								}
							}
						}
					}
				}


					//send EMail with attachment to_id

					$title = '[#'.$this->Support->id.'] '.$requestData['Support']['title'];

					$support = $this->Support->find('first',array(
						'conditions' => array('id' => $this->Support->id),
						'recursive' => -1,
					));


					$user_co = $this->Session->read('Auth.User');
					//check moderator status
					$moderator = $this->SupportAdmin->find('first',array(
							'conditions' => array('service_id' => $support['Support']['service_id'],'level' => $support['Support']['level'],'user_id' => $user_co['id']),
							'recursive' => -1,
						));

					if($moderator['SupportAdmin']['is_control']){
						$this->Support->id = $support['Support']['id'];
						$this->Support->saveField('owner_id', $user_co['id']);
						$this->Support->saveField('moderate_response', $this->SupportMessage->id);
						$this->Support->saveField('date_upd', date('Y-m-d H:is'));
						$this->Session->setFlash(__('Votre message est en cours de moderation.'), 'flash_warning');

						//send email
						$title = 'Un message support a moderer';
						$admin_email = 'contact@talkappdev.com';
						$this->sendCmsTemplateByMail(452, 1, $admin_email, array(
								'URL_TICKET' => $domain.'admin/support/fil/'.$support['Support']['id'],
								'SUBJECTSUPPORT' => $title,
						));

					}else{
						$this->Support->id = $support['Support']['id'];
						$this->Support->saveField('status', 1);
						$this->Support->saveField('owner_id', $user_co['id']);
						$this->Support->saveField('date_upd', date('Y-m-d H:is'));

						$content = $requestData['Support']['content'];
						$datasEmail = array('admin' => true, 'content' => $content);

						$service = $this->SupportService->find('first',array(
								'conditions' => array('id' => $support['Support']['service_id']),
								'recursive' => -1,
							));

						$reply = $service['SupportService']['mail'];
						$sender = $service['SupportService']['mail'];

						$this->sendEmailWithAttachment($email,$title,'support',array('param' => $datasEmail),'default',$sender,$reply, $mail_attachments);
						$this->Session->setFlash(__('Votre message a été envoyé.'), 'flash_warning');
					}

				}
			  }

            }

	}
	public function admin_close($support_id = null){
		if($support_id){
			$this->Support->id = $support_id;
			$this->Support->saveField('status', 2);
		}
		$this->redirect(array('controller' => 'support', 'action' => 'message', 'admin' => true), false);
	}
	
	public function admin_close_fil($support_id = null){
		if($support_id){
			$this->Support->id = $support_id;
			$this->Support->saveField('status', 2);
		}
		$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$support_id , 'admin' => true), false);
	}
	
	public function admin_unclose_fil($support_id = null){
		if($support_id){
			$this->Support->id = $support_id;
			$this->Support->saveField('status', 1);
		}
		$this->redirect(array('controller' => 'support', 'action' => 'fil/'.$support_id , 'admin' => true), false);
	}
	
	public function admin_classification() {
		
		/*$classifications = array();
		
		 $classification_parent = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => NULL),
			  		'oder' => 'num asc',
					'recursive' => -1
				));
		foreach($classification_parent as $classif){
			$classification_child = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => $classif['SupportClassification']['id']),
			  		'oder' => 'num asc',
					'recursive' => -1
				));
			$classif['SupportClassification']['index'] = $classif['SupportClassification']['num'];
			array_push($classifications,$classif);
			
			foreach($classification_child as $classif_child){
				$classif_child['SupportClassification']['index'] = $classif['SupportClassification']['num'].'.'.$classif_child['SupportClassification']['num'];
				array_push($classifications,$classif_child);
			}
		}


       $this->set(compact('classifications'));*/
		
		$this->Paginator->settings = array(
				'fields' => array('SupportClassification.*','SupportClassificationParent.*'),
				'conditions' => array('SupportClassification.parent_id !=' => NULL),
				'order' => array('SupportClassification.num' => 'asc'),
                'paramType' => 'querystring',
				'joins' => array(
                   
					array(
                        'table' => 'support_classifications',
                        'alias' => 'SupportClassificationParent',
                        'type' => 'left',
                        'conditions' => array(
                            'SupportClassificationParent.id = SupportClassification.parent_id',
                        )
                    )
                ),
                'limit' => 999
            );

            $classifications = $this->Paginator->paginate($this->SupportClassification);
		
		$classifications = $this->order_classif($classifications);

		$this->set(compact('classifications'));
	}
	
	public function admin_classification_parent() {
		
		$this->Paginator->settings = array(
				'conditions' => array('SupportClassification.parent_id' => NULL),
				'order' => array('SupportClassification.num' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $classifications = $this->Paginator->paginate($this->SupportClassification);

		$this->set(compact('classifications'));
	}
	
	public function admin_classification_parent_create(){
		
		
            if($this->request->is('post')){
                $requestData = $this->request->data;
				
                //On vérifie les champs du formulaire
				$requestData['Support'] = Tools::checkFormField($requestData['Support'],
						array('name','num'),
						array('name','num')
					);
				
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$classificationData = array();
				$classificationData['SupportClassification'] = array();
				$classificationData['SupportClassification']['num'] = $requestData['Support']['num'];
				$classificationData['SupportClassification']['name'] = $requestData['Support']['name'];
				
				
				$this->SupportClassification->create();
                if($this->SupportClassification->save($classificationData)){
					
									
                        $this->Session->setFlash(__('La classification a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'support', 'action' => 'classification_parent', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }
		
     }
	
	public function admin_classification_create(){
		
		
            if($this->request->is('post')){
                $requestData = $this->request->data;
				
                //On vérifie les champs du formulaire
				$requestData['Support'] = Tools::checkFormField($requestData['Support'],
						array('name','description', 'solution_link','num','parent_id'),
						array('name','num')
					);
				
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$classificationData = array();
				$classificationData['SupportClassification'] = array();
				if($requestData['Support']['parent_id'])
				$classificationData['SupportClassification']['parent_id'] = $requestData['Support']['parent_id'];
				$classificationData['SupportClassification']['num'] = $requestData['Support']['num'];
				$classificationData['SupportClassification']['name'] = $requestData['Support']['name'];
				$classificationData['SupportClassification']['description'] = $requestData['Support']['description'];
				$classificationData['SupportClassification']['solution_link'] = $requestData['Support']['solution_link'];
				
				
				$this->SupportClassification->create();
                if($this->SupportClassification->save($classificationData)){
					
									
                        $this->Session->setFlash(__('La classification a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }
		  $classification_parent = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => NULL),
			  		'oder' => 'num asc',
					'recursive' => -1
				));
		$list_classification_parent = array(0 => 'Choisir');
		  foreach($classification_parent as $classification){
			  $list_classification_parent[$classification['SupportClassification']['id']] = $classification['SupportClassification']['num'].' '.$classification['SupportClassification']['name'];
		  }
				$this->set(array('list_classification_parent' => $list_classification_parent));
		
     }
	
	public function admin_classification_parent_edit($id){
		
		
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Support'] = Tools::checkFormField($requestData['Support'],
                    array('name','num'),
                    array('name','num')
                );
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$classificationData = array();
				$classificationData['SupportClassification'] = array();
				$classificationData['SupportClassification']['num'] = $requestData['Support']['num'];
				$classificationData['SupportClassification']['name'] = $requestData['Support']['name'];
				
				
				$this->SupportClassification->id = $id;
                if($this->SupportClassification->save($classificationData)){
					
									
                        $this->Session->setFlash(__('La classification a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'support', 'action' => 'classification_parent', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }else{
				$classification = $this->SupportClassification->find('first', array(
					'conditions' => array('SupportClassification.id' => $id),
					'recursive' => -1
				));

				if(empty($classification)){
					$this->Session->setFlash(__('Classification introuvable.'),'flash_warning');
					$this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
				}
				
			  
				$this->set(array('edit' => true, 'classification' => $classification));
				$this->render('admin_classification_parent_edit');
			}
     }
	
	public function admin_classification_edit($id){
		
		
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Support'] = Tools::checkFormField($requestData['Support'],
                    array('name','description', 'solution_link','parent_id','num'),
                    array('name','num')
                );
                if($requestData['Support'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$classificationData = array();
				$classificationData['SupportClassification'] = array();
				if($requestData['Support']['parent_id'])
				$classificationData['SupportClassification']['parent_id'] = $requestData['Support']['parent_id'];
				$classificationData['SupportClassification']['num'] = $requestData['Support']['num'];
				$classificationData['SupportClassification']['name'] = $requestData['Support']['name'];
				$classificationData['SupportClassification']['description'] = $requestData['Support']['description'];
				$classificationData['SupportClassification']['solution_link'] = $requestData['Support']['solution_link'];
				
				
				$this->SupportClassification->id = $id;
                if($this->SupportClassification->save($classificationData)){
					
									
                        $this->Session->setFlash(__('La classification a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }else{
				$classification = $this->SupportClassification->find('first', array(
					'conditions' => array('SupportClassification.id' => $id),
					'recursive' => -1
				));

				if(empty($classification)){
					$this->Session->setFlash(__('Classification introuvable.'),'flash_warning');
					$this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
				}
				
				 $classification_parent = $this->SupportClassification->find('all', array(
					'conditions' => array('SupportClassification.parent_id' => NULL),
			  		'oder' => 'num asc',
					'recursive' => -1
				));
				$list_classification_parent = array(0 => 'Choisir');
				  foreach($classification_parent as $classif){
					  $list_classification_parent[$classif['SupportClassification']['id']] = $classif['SupportClassification']['num'].' '.$classif['SupportClassification']['name'];
				  }
			  
				$this->set(array('edit' => true, 'classification' => $classification,'list_classification_parent' => $list_classification_parent));
				$this->render('admin_classification_edit');
			}
     }
	
	public function admin_classification_delete($id){
		$classification = $this->SupportClassification->find('first', array(
					'conditions' => array('SupportClassification.id' => $id),
					'recursive' => -1
				));

				if(empty($classification)){
					$this->Session->setFlash(__('Classification introuvable.'),'flash_warning');
					$this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
				}
		if($this->SupportClassification->deleteAll(array('SupportClassification.id'=>$id), false)){
			$this->Session->setFlash(__('La classification a été supprimé'), 'flash_success');
        $this->redirect(array('controller' => 'support', 'action' => 'classification', 'admin' => true), false);
         }else
              $this->Session->setFlash(__('Erreur lors de la suppression'),'flash_warning');
		
		
	}
	
	protected function order_classif($classifs)
	{
		
		$order_classifs = array();
		foreach($classifs as $classif){
			$index = '';
			$cut_index = explode('.',$classif['SupportClassification']['num']);
			
			foreach($cut_index as $cut){
				if(strlen($cut) < 2)$cut = '0'.$cut;
				$index .= $cut.'.';
			}
			
			$order_classifs[ $index ] = $classif;
		}
		ksort($order_classifs);
		return $order_classifs;
	}

}

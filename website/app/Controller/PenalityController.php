<?php
App::uses('AppController', 'Controller');


class PenalityController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Penality','UserPenality');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'penality')));
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Penality'] = Tools::checkFormField($requestData['Penality'],
                    array('type', 'delay_min', 'delay_max','active','cost'),
                    array('type')
                );
                if($requestData['Penality'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

                    $this->Penality->create();
                    if($this->Penality->save($requestData)){
                        $this->Session->setFlash(__('La regle a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement de la regle'),'flash_warning');
                
            }
     }
	
	public function admin_activate($id){
            //on active le slide
            $this->Penality->id = $id;
            if($this->Penality->saveField('active', 1))
                $this->Session->setFlash(__('La regle a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation.'),'flash_warning');

            $this->redirect(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive le slide
            $this->Penality->id = $id;
            if($this->Penality->saveField('active', 0))
                $this->Session->setFlash(__('La regle a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation.'),'flash_warning');

            $this->redirect(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true), false);
        }
	
	
	public function admin_edit($id){
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Penality'] = Tools::checkFormField($requestData['Penality'],
                    array('type', 'delay_min', 'delay_max','active','cost'),
                    array('type')
                );
                if($requestData['Penality'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				$requestData['Penality']['type'] = "'".$requestData['Penality']['type']."'";
                //Si la modif a réussi
                    if($this->Penality->updateAll(
                        $requestData['Penality'],
                        array('Penality.id' => $id))
                    ){
                        $this->Session->setFlash(__('La regle a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification de la regle'),'flash_warning');
                }else{

            $rule = $this->Penality->find('first', array(
                'conditions' => array('Penality.id' => $id),
                'recursive' => -1
            ));


            if(empty($rule)){
                $this->Session->setFlash(__('Regle introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'penality', 'action' => 'penalities', 'admin' => true), false);
            }
            //On insère les données
            $this->request->data = $rule;
            $this->set(array('edit' => true, 'penality' => $rule));
            $this->render('admin_edit');
				}
        }


	
	public function admin_penalities(){
		$this->Paginator->settings = array(
				'order' => array('Penality.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $penalities = $this->Paginator->paginate($this->Penality);

		$this->set(compact('penalities'));
	}
	
	public function admin_comlostcall(){
		$parms = $this->params;
		$page = 1;
		if($parms["page"])$page = $parms["page"];
		
		$nbpage = 25;
		$limit = 1;
		if($page > 1) $limit = $page * $nbpage;
		
		
		$conditions = array('UserPenality.callinfo_id !=' => NULL);
		
		  if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_com >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_com <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }
		
		
		  $this->Paginator->settings = array(
            'fields' => array('UserPenality.id','UserPenality.date_com', 'Callinfo.callinfo_id','Callinfo.customer', 'Callinfo.timestamp', 'Callinfo.callerid','Callinfo.date_send', 'UserPenality.is_view', 'UserPenality.reason', 'UserPenality.delay','Agent.id','Agent.pseudo'),
            'conditions' => $conditions,
			 'joins' => array(
                array(
                    'table' => 'call_infos',
                    'alias' => 'Callinfo',
                    'type'  => 'left',
                    'conditions' => array('Callinfo.callinfo_id = UserPenality.callinfo_id')
                ),
				  array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type'  => 'left',
                    'conditions' => array('Agent.id = UserPenality.user_id')
                ),
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
            'limit' => $nbpage,
			'page' => $page
        );

        $allComs = $this->Paginator->paginate($this->UserPenality);
		
		foreach($allComs as &$comm){
				if($comm['Callinfo']['customer']){
					$client_sql = $this->User->find('first', array(
						'fields' => array('User.firstname','User.id'),
						'conditions' => array('User.personal_code' => $comm['Callinfo']['customer']),
						'recursive' => -1
					));

					if($client_sql['User']['firstname']){
						$comm['User'] = $client_sql['User'];
					}
				}
			}
		
            $this->set(compact('allComs'));
	}
	
	public function admin_comlosttchat(){
		
		$parms = $this->params;
		$page = 1;
		if($parms["page"])$page = $parms["page"];
		
		$nbpage = 25;
		$limit = 1;
		if($page > 1) $limit = $page * $nbpage;
		
		$conditions = array('UserPenality.tchat_id !=' => NULL);
		
		  if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_com >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_com <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }
		
		
		 $this->Paginator->settings = array(
            'fields' => array('UserPenality.id','UserPenality.is_view', 'Chat.id','Chat.from_id', 'Chat.date_start', 'Chat.status','Chat.date_send', 'User.firstname', 'User.id', 'UserPenality.reason', 'UserPenality.delay','Agent.id','Agent.pseudo'),
           'conditions' => $conditions,
			 'joins' => array(
                array(
                    'table' => 'chats',
                    'alias' => 'Chat',
                    'type'  => 'left',
                    'conditions' => array('Chat.id = UserPenality.tchat_id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Chat.from_id')
                ),
				  array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type'  => 'left',
                    'conditions' => array('Agent.id = UserPenality.user_id')
                ),
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
            'limit' => $nbpage,
			'page' => $page
        );

        $allComs = $this->Paginator->paginate($this->UserPenality);
		
            $this->set(compact('allComs'));
	}
	
	public function admin_comlostmessage(){
		
		$parms = $this->params;
		$page = 1;
		if($parms["page"])$page = $parms["page"];
		
		$nbpage = 25;
		$limit = 1;
		if($page > 1) $limit = $page * $nbpage;
		
		$conditions = array('UserPenality.message_id !=' => NULL);
		
		  if($this->Session->check('Date')){
                $conditions = array_merge($conditions, array(
                    'UserPenality.date_com >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'UserPenality.date_com <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
            }
		
		
		 $this->Paginator->settings = array(
            'fields' => array('UserPenality.id','UserPenality.is_view', 'Message.id','Message.from_id', 'Message.date_add',  'User.firstname', 'User.id', 'UserPenality.reason', 'UserPenality.delay','Agent.id','Agent.pseudo'),
           'conditions' => $conditions,
			 'joins' => array(
                array(
                    'table' => 'messages',
                    'alias' => 'Message',
                    'type'  => 'left',
                    'conditions' => array('Message.id = UserPenality.message_id')
                ),
				 array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array('User.id = Message.from_id')
                ),
				  array(
                    'table' => 'users',
                    'alias' => 'Agent',
                    'type'  => 'left',
                    'conditions' => array('Agent.id = UserPenality.user_id')
                ),
            ),
            'order' => 'UserPenality.date_com desc',
            'recursive' => -1,
            'limit' => $nbpage,
			'page' => $page
        );

        $allComs = $this->Paginator->paginate($this->UserPenality);
		
            $this->set(compact('allComs'));
	}
	
}
<?php
App::uses('AppController', 'Controller');


class UserlevelController extends AppController {
	
	  public $components = array('Paginator');
        public $uses = array('User','Userlevel');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'userlevel')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }

	public function admin_create() {
		
		if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Userlevel'] = Tools::checkFormField($requestData['Userlevel'],
                    array('user_id', 'level'),
                    array('user_id', 'level')
                );
                if($requestData['Userlevel'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }


                    $this->Userlevel->create();
                    if($this->Userlevel->save($requestData)){
                        $this->Session->setFlash(__('Le level a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'Userlevel', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
		
		$this->loadModel('User');
		$users = $this->User->find('all',array(
                'conditions' => array('role' => 'admin'),
                'recursive' => -1,
            ));
		$this->loadModel('Userlevelacl');
		$Userlevelacl = $this->Userlevelacl->find('all',array(
                'recursive' => -1,
            ));
		$levels = array();
		foreach($Userlevelacl as $lev){
			if(!in_array($lev['Userlevelacl']['level'],$levels)){
				array_push($levels,$lev['Userlevelacl']['level']);
			}
		}
		$this->set(array('admins' => $users,'levels' => $levels));
	}
	
	public function admin_edit($id) {
		    if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Userlevel'] = Tools::checkFormField($requestData['Userlevel'],
                    array('user_id', 'level'),
                    array('user_id', 'level')
                );
                if($requestData['Userlevel'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				$requestData['Userlevel']['level'] = "'".$requestData['Userlevel']['level']."'";
                //Si la modif a réussi
                    if($this->Userlevel->updateAll(
                        $requestData['Userlevel'],
                        array('Userlevel.id' => $id))
                    ){
                        $this->Session->setFlash(__('Le level a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'userlevel', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification du level'),'flash_warning');
                }else{

            $level = $this->Userlevel->find('first', array(
				'fields' => array('Userlevel.*','User.*'),
                'conditions' => array('Userlevel.id' => $id),
				'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array(
                            'User.id = Userlevel.user_id',
                        )
                    )
                ),
                'recursive' => -1
            ));


            if(empty($level)){
                $this->Session->setFlash(__('introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'userlevel', 'action' => 'list', 'admin' => true), false);
            }
				
			$this->loadModel('Userlevelacl');
			$Userlevelacl = $this->Userlevelacl->find('all',array(
					'recursive' => -1,
				));
			$levels = array();
			foreach($Userlevelacl as $lev){
				if(!in_array($lev['Userlevelacl']['level'],$levels)){
					array_push($levels,$lev['Userlevelacl']['level']);
				}
			}	

            //On insère les données
            $this->request->data = $level;
            $this->set(array('edit' => true, 'level' => $level, 'levels' => $levels));
            $this->render('admin_edit');
		}

	}
	
	public function admin_list() {
		$this->Paginator->settings = array(
				'fields' => array('Userlevel.*','User.*'),
                'order' => array('Userlevel.id' => 'asc'),
                'paramType' => 'querystring',
				'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array(
                            'User.id = Userlevel.user_id',
                        )
                    )
                ),
                'limit' => 25
            );

            $levels = $this->Paginator->paginate($this->Userlevel);

            $this->set(compact('levels'));
	}

}
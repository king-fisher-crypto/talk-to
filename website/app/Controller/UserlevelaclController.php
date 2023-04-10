<?php
App::uses('AppController', 'Controller');

class UserlevelaclController extends AppController {
	
	 public $components = array('Paginator');
        public $uses = array('User','Userlevel', 'Userlevelacl');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'userlevelacl')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }

	public function admin_create() {
		
		if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Userlevelacl'] = Tools::checkFormField($requestData['Userlevelacl'],
                    array('level'),
                    array('level')
                );
                if($requestData['Userlevelacl'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
					
					$levels = $this->Userlevelacl->find('all', array(
						'fields' => array('Userlevelacl.*'),
						'conditions' => array('Userlevelacl.level' => 'admin'),
						'recursive' => -1
					));
					
			foreach($levels as $lev){
				$save = array();
				$save['level'] = $requestData['Userlevelacl']['level'];
				$save['menu'] = $lev['Userlevelacl']['menu'];
				$save['auth'] = 0;
				$this->Userlevelacl->create();
				$this->Userlevelacl->save($save);
			}
                    //$this->Userlevelacl->create();
                    //if($this->Userlevelacl->save($requestData)){
                        $this->Session->setFlash(__('Le level a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'Userlevelacl', 'action' => 'list', 'admin' => true), false);
                   // }else
                   //     $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
		
		
	}

	
	public function admin_edit($level) {
		if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
              /* $requestData['Userlevelacl'] = Tools::checkFormField($requestData['Userlevelacl'],
                    array('level'),
                    array('level')
                );
                if($requestData['Userlevelacl'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }*/
			$leveladmin = $requestData['Userlevelacl']['level'];
				$requestData['Userlevelacl']['level'] = "'".$requestData['Userlevelacl']['level']."'";
				foreach($requestData['Userlevelacl'] as $key => $data){
					if($key != 'level'){
						$levelacl = $this->Userlevelacl->find('first', array(
							'conditions' => array('level' => $leveladmin,'menu' => $key),
							'recursive' => -1
						));
						$save = array();
						$save['level'] = $requestData['Userlevelacl']['level'];
						$save['menu'] = "'".$key."'";
						$save['auth'] = $data;
						
						$this->Userlevelacl->updateAll($save,array('Userlevelacl.id' => $levelacl['Userlevelacl']['id']));
					}
				}
			
                //Si la modif a réussi
                   // if($this->Userlevelacl->updateAll(
                    //    $requestData['Userlevelacl'],
                    //    array('Userlevelacl.id' => $id))
                   // ){
                        $this->Session->setFlash(__('Le level a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'userlevelacl', 'action' => 'list', 'admin' => true), false);
                    //}else
                      //  $this->Session->setFlash(__('Erreur lors de la modification du level'),'flash_warning');
                }else{

            $levels = $this->Userlevelacl->find('all', array(
				'fields' => array('Userlevelacl.*'),
                'conditions' => array('Userlevelacl.level' => $level),
                'recursive' => -1
            ));


            if(empty($levels)){
                $this->Session->setFlash(__('introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'userlevel', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $levels;
            $this->set(array('edit' => true, 'levels' => $levels));
            $this->render('admin_edit');
		}
	}
	
	public function admin_list() {
		$this->Paginator->settings = array(
				'fields' => array('Userlevelacl.*'),
                'order' => array('Userlevelacl.id' => 'asc'),
                'recursive' => -1,
				'limit' => 1000
            );

            $levels = $this->Paginator->paginate($this->Userlevelacl);

            $this->set(compact('levels'));
	}

}
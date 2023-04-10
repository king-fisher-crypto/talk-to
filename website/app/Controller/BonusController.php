<?php
App::uses('AppController', 'Controller');


class BonusController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Bonus');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'bonus')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Bonus'] = Tools::checkFormField($requestData['Bonus'],
                    array('name', 'bearing', 'amount'),
                    array('name', 'bearing', 'amount')
                );
                if($requestData['bonus'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }


                    $this->Bonus->create();
                    if($this->Bonus->save($requestData)){
                        $this->Session->setFlash(__('Le bonus a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'bonus', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement du bonus'),'flash_warning');
                
            }



        }

	
	  public function admin_list(){
            
            $this->Paginator->settings = array(
                'order' => array('Bonus.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $bonus = $this->Paginator->paginate($this->Bonus);

            $this->set(compact('bonus'));
        }
		
		public function admin_edit($id){
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Bonus'] = Tools::checkFormField($requestData['Bonus'],
                    array('name', 'bearing', 'amount'),
                    array('name', 'bearing', 'amount')
                );
                if($requestData['Bonus'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$requestData['Bonus']['name'] = "'".$requestData['Bonus']['name']."'";
				


                //Si la modif a réussi
                    if($this->Bonus->updateAll(
                        $requestData['Bonus'],
                        array('Bonus.id' => $id))
                    ){
                        $this->Session->setFlash(__('Le bonus a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'bonus', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification du bonus'),'flash_warning');
                }else{

            $bonus = $this->Bonus->find('first', array(
                'conditions' => array('Bonus.id' => $id),
                'recursive' => -1
            ));


            if(empty($bonus)){
                $this->Session->setFlash(__('Bonus introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'bonus', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $bonus;
            $this->set(array('edit' => true, 'bonus' => $bonus));
            $this->render('admin_edit');
				}
        }


}

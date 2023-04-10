<?php
App::uses('AppController', 'Controller');


class CostController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Cost', 'CostPhone');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'cost')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Cost'] = Tools::checkFormField($requestData['Cost'],
                    array('name', 'level', 'cost'),
                    array('name', 'level', 'cost')
                );
                if($requestData['cost'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }


                    $this->Cost->create();
                    if($this->Cost->save($requestData)){
                        $this->Session->setFlash(__('Le cout a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'cost', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement du cout'),'flash_warning');
                
            }



        }

	
	  public function admin_list(){
            
            $this->Paginator->settings = array(
                'order' => array('Cost.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $costs = $this->Paginator->paginate($this->Cost);

            $this->set(compact('costs'));
        }
	
	public function admin_list_phone(){
            
            $this->Paginator->settings = array(
                'order' => array('CostPhone.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $costs = $this->Paginator->paginate($this->CostPhone);

            $this->set(compact('costs'));
        }
		
		public function admin_edit($id){
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Cost'] = Tools::checkFormField($requestData['Cost'],
                    array('name', 'level', 'cost'),
                    array('name', 'level', 'cost')
                );
                if($requestData['Cost'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$requestData['Cost']['name'] = "'".$requestData['Cost']['name']."'";
				


                //Si la modif a réussi
                    if($this->Cost->updateAll(
                        $requestData['Cost'],
                        array('Cost.id' => $id))
                    ){
                        $this->Session->setFlash(__('Le cout a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'cost', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification du cout'),'flash_warning');
                }else{

            $cost = $this->Cost->find('first', array(
                'conditions' => array('Cost.id' => $id),
                'recursive' => -1
            ));


            if(empty($cost)){
                $this->Session->setFlash(__('Cout introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'cost', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $cost;
            $this->set(array('edit' => true, 'cost' => $cost));
            $this->render('admin_edit');
				}
        }


}

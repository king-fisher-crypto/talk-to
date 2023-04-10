<?php
App::uses('AppController', 'Controller');


class RedirectController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('Redirect');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'redirect')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Redirect'] = Tools::checkFormField($requestData['Redirect'],
                    array('type','domain_id', 'old', 'new'),
                    array('type','domain_id', 'old', 'new')
                );
                if($requestData['redirect'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }


                    $this->Redirect->create();
                    if($this->Redirect->save($requestData)){
                        $this->Session->setFlash(__('La redirection a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'redirect', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }



        }

	
	  public function admin_list(){
            
            $this->Paginator->settings = array(
                'order' => array('Redirect.id' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $redirects = $this->Paginator->paginate($this->Redirect);

            $this->set(compact('redirects'));
        }
		
		public function admin_edit($id){
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Redirect'] = Tools::checkFormField($requestData['Redirect'],
                    array('type','domain_id', 'old', 'new'),
                    array('type','domain_id', 'old', 'new')
                );
                if($requestData['Redirect'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$requestData['Redirect']['type'] = "'".$requestData['Redirect']['type']."'";
				$requestData['Redirect']['domain_id'] = "'".$requestData['Redirect']['domain_id']."'";
				$requestData['Redirect']['old'] = "'".$requestData['Redirect']['old']."'";
				$requestData['Redirect']['new'] = "'".$requestData['Redirect']['new']."'";

                //Si la modif a réussi
                    if($this->Redirect->updateAll(
                        $requestData['Redirect'],
                        array('Redirect.id' => $id))
                    ){
                        $this->Session->setFlash(__('La redirection a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'redirect', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification'),'flash_warning');
                }else{

            $redirect = $this->Redirect->find('first', array(
                'conditions' => array('Redirect.id' => $id),
                'recursive' => -1
            ));


            if(empty($redirect)){
                $this->Session->setFlash(__('Redirection introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'redirect', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $redirect;
            $this->set(array('edit' => true, 'redirect' => $redirect));
            $this->render('admin_edit');
				}
        }


}

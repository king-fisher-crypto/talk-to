<?php
App::uses('AppController', 'Controller');


class SalesController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('SaleReconciliation');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'sales')));
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow();
    }
	
	
	 public function admin_index(){
		 
		 	$conditions = array();
			
			$this->Paginator->settings = array(
				'fields' => array('SaleReconciliation.*'),
				'conditions' => $conditions,
				'order' => 'SaleReconciliation.id DESC',
				'paramType' => 'querystring',
				'limit' => 15
			);

			$lastOrder = $this->Paginator->paginate($this->SaleReconciliation);

			$this->set(compact('lastOrder'));
        }
	
	
	public function admin_edit($id){
		
		
            if($this->request->is('post')){
                $requestData = $this->request->data;
                //On vérifie les champs du formulaire
                $requestData['SaleReconciliation'] = Tools::checkFormField($requestData['SaleReconciliation'],
                    array('id', 'invoice_agent', 'vat_invoice_agent', 'credit_note', 'owed_agent', 'error_agent', 'bankwire_agent', 'stripe', 'paypal', 'unused_credit', 'currency_diff', 'premium_number'),
                    array('id')
                );
                if($requestData['SaleReconciliation'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				
				$this->SaleReconciliation->id = $id;
				$this->SaleReconciliation->save($requestData['SaleReconciliation']);
				
               $this->Session->setFlash(__('La réconcialiation a été enregistré'), 'flash_success');
               $this->redirect(array('controller' => 'sales', 'action' => 'index', 'admin' => true), false);
                    

          }     
				
		$order = $this->SaleReconciliation->find('first', array(
						'conditions' => array('id'=>$id),
					));
		
		$this->set('order', $order);
        
	}
	
	public function admin_validate($id){
		
            if($id){
                
				$this->SaleReconciliation->id = $id;
				$this->SaleReconciliation->saveField('status', 1);
				$this->SaleReconciliation->saveField('date_valid', date('Y-m-d H:i:s'));
               $this->Session->setFlash(__('La réconcialiation a été validé'), 'flash_success');
               

          }     
        $this->redirect(array('controller' => 'sales', 'action' => 'index', 'admin' => true), false);
	}
	
}
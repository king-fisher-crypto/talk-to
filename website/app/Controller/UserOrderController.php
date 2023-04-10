<?php
App::uses('AppController', 'Controller');


class UserOrderController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','UserOrder');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'refund')));
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
				
				App::import('Controller', 'Paymentstripe');
				$paymentctrl = new PaymentstripeController();

				require(APP.'Lib/stripe/init.php');
				
				\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
				
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['UserOrder'] = Tools::checkFormField($requestData['UserOrder'],
                    array('user_id', 'type', 'amount','label','type_com', 'id_com', 'commentaire'),//,'date_add'
                    array('user_id', 'type', 'amount', 'label')
                );
                if($requestData['UserOrder'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$condition = array('User.id' => $requestData['UserOrder']['user_id']);
				
				$agent = $this->User->find('first', array(
							'conditions'    => $condition,
							'recursive'     => -1
						));
				
				$saveData = array();
				$saveData['UserOrder']['user_id'] = $requestData['UserOrder']['user_id'];
				$saveData['UserOrder']['date_ecriture'] = date('Y-m-d H:i:s');;//$requestData['UserOrder']['date_add'];
				$saveData['UserOrder']['type'] = $requestData['UserOrder']['type'];
				$saveData['UserOrder']['amount'] = str_replace(',','.',$requestData['UserOrder']['amount']);
				$saveData['UserOrder']['label'] = $requestData['UserOrder']['label'];
				$saveData['UserOrder']['id_com'] = $requestData['UserOrder']['id_com'];
				$saveData['UserOrder']['type_com'] = $requestData['UserOrder']['type_com'];
				$saveData['UserOrder']['commentaire'] = $requestData['UserOrder']['commentaire'];
				
				//stripe request
				if($agent['User']['stripe_account']){
					if($requestData['UserOrder']['amount'] > 0){
						try {
							\Stripe\Transfer::create(
										  [
											"amount" => $saveData['UserOrder']['amount'] * 100,
											"currency" => "eur",
											"destination" => $agent['User']['stripe_account']
										  ]
										);

									 } catch (\Stripe\Error\Base $e) {
										 $this->Session->setFlash($e->getMessage(),'flash_warning');
										$this->redirect(array('controller' => 'user_order', 'action' => 'index', 'admin' => true), false);
							}
					}else{
						try {
							$account = \Stripe\Account::retrieve();
							\Stripe\Transfer::create(
										  [
											"amount" => $saveData['UserOrder']['amount'] * -100,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent['User']['stripe_account']]
										);

									 } catch (\Stripe\Error\Base $e) {
										 $this->Session->setFlash($e->getMessage(),'flash_warning');
										$this->redirect(array('controller' => 'user_order', 'action' => 'index', 'admin' => true), false);
							}
					}
				}
				
				
                $this->UserOrder->create();
                if($this->UserOrder->save($saveData)){
                   $this->Session->setFlash(__('L\'écriture a été enregistré'), 'flash_success');
                   $this->redirect(array('controller' => 'user_order', 'action' => 'index', 'admin' => true), false);
                }else
                  $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
		
		$agents = $this->User->find('all', array(
							'conditions'    => array('role'=>'agent', 'deleted'=>0),
							'order' => array('pseudo'),
							'recursive'     => -1
						));
		$list_agents = array();
		foreach($agents as $agent){
			
			$list_agents[$agent['User']['id']] = $agent['User']['pseudo']. '('.$agent['User']['agent_number'].')';
			
		}
		
		$this->set(compact('list_agents'));
     }
	


	
	public function admin_index(){
		$this->Paginator->settings = array(
				'fields' => array('UserOrder.*','Agent.*'),
                'order' => array('UserOrder.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins'      => array(
							array(
								'table' => 'users',
								'alias' => 'Agent',
								'type'  => 'inner',
								'conditions' => array(
									'Agent.id = UserOrder.user_id'
								)
							)
						),
                'limit' => 25
            );

            $refunds = $this->Paginator->paginate($this->UserOrder);

            $this->set(compact('refunds'));
	}
	public function admin_delete($id=0)
    {
        $this->autoRender = false;
        $this->loadModel('UserOrder');
        $row = $this->UserOrder->findById($id);
		

        if (empty($row)){
            $this->Session->setFlash(__('Erreur, la ligne n\'a pu être trouvée'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }

        if ($row['UserOrder']['is_sold'] == 1){
            $this->Session->setFlash(__('Erreur, l\'action n\'est pas possible sur une ligne soldée'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }
		$condition = array('User.id' => $row['UserOrder']['user_id']);
				
		$agent = $this->User->find('first', array(
							'conditions'    => $condition,
							'recursive'     => -1
						));
		if (!$agent){
            $this->Session->setFlash(__('Erreur, l\'agent n\'a pas été trouvé'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }
		
		App::import('Controller', 'Paymentstripe');
		$paymentctrl = new PaymentstripeController();

		require(APP.'Lib/stripe/init.php');
				
		\Stripe\Stripe::setApiKey($paymentctrl->_stripe_confs[$paymentctrl->_stripe_mode]['private_key']);
		
		//stripe request
		if($agent['User']['stripe_account']){
					if($row['UserOrder']['amount'] > 0){
						try {
							$account = \Stripe\Account::retrieve();
							\Stripe\Transfer::create(
										  [
											"amount" => $row['UserOrder']['amount'] * 100,
											"currency" => "eur",
											"destination" => $account->id
										  ],
										  ["stripe_account" => $agent['User']['stripe_account']]
										);

									 } catch (\Stripe\Error\Base $e) {
										 $this->Session->setFlash($e->getMessage(),'flash_warning');
										$this->redirect(array('controller' => 'user_order', 'action' => 'index', 'admin' => true), false);
							}
						
					}else{
						try {
							\Stripe\Transfer::create(
										  [
											"amount" => $row['UserOrder']['amount'] * -100,
											"currency" => "eur",
											"destination" => $agent['User']['stripe_account']
										  ]
										);

									 } catch (\Stripe\Error\Base $e) {
										 $this->Session->setFlash($e->getMessage(),'flash_warning');
										$this->redirect(array('controller' => 'user_order', 'action' => 'index', 'admin' => true), false);
							}
					}
		}

        $this->UserOrder->id = $id;
        $this->UserOrder->delete();

        $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
    }
	
	public function admin_sold($id=0)
    {
        $this->autoRender = false;
        $this->loadModel('UserOrder');
        $row = $this->UserOrder->findById($id);

        if (empty($row)){
            $this->Session->setFlash(__('Erreur, la ligne n\'a pu être trouvée'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }

       $this->UserOrder->updateAll(array('is_sold'=>1), array('UserOrder.id' => $id));

        $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
    }
}
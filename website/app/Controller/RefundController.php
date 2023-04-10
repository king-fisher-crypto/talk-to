<?php
App::uses('AppController', 'Controller');


class RefundController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Order');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'refund')));
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow();
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Refund'] = Tools::checkFormField($requestData['Refund'],
                    array('client', 'price', 'credits','label','type_com', 'id_com', 'commentaire'),
                    array('client', 'credits', 'label')
                );
                if($requestData['Refund'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				if(is_numeric($requestData['Refund']['client'])){
					$condition = array('User.personal_code' => $requestData['Refund']['client']);
				}else{
					$condition = array('User.role'=>'client','User.email' => $requestData['Refund']['client']);
				}
				
				$user = $this->User->find('first', array(
							'conditions'    => $condition,
							'recursive'     => -1
						));
				 if(!$user){
                    $this->Session->setFlash(__('Client non trouvé.'),'flash_error');
                    return;
                }
				//cree order
				$requestData['Refund']['price'] = str_replace(',','.',$requestData['Refund']['price']);
				 $this->Order->create();
				$uniqReference = $this->getUniqReference();
				$datas = array(
					'cart_id'           => '',
					'reference'         => $uniqReference,
					'user_id'           => $user['User']['id'],
					'lang_id'           => $user['User']['lang_id'],
					'country_id'        => $user['User']['country_id'],
					'product_id'        => '',
					'product_name'      => 'Remboursement',
					'product_credits'   => $requestData['Refund']['credits'],
					'product_price'     => $requestData['Refund']['price'],
					'voucher_code'      => '',
					'voucher_name'      => '',
					'voucher_mode'      => '',
					'voucher_credits'   => '',
					'voucher_amount'    => '',
					'voucher_percent'    => '',
					'payment_mode'      => 'refund',
					'currency'          => '€',
					'total'             => $requestData['Refund']['price'],
					'valid'             => 1,
					'IP'                => '',
					'label'        		=> $requestData['Refund']['label'],
					'type_com'         => $requestData['Refund']['type_com'],
					'id_com'         => $requestData['Refund']['id_com'],
					'commentaire'         => $requestData['Refund']['commentaire'],
					'is_new'			=> 0
				);

        		$this->Order->saveAll($datas);
				
				$this->loadModel('UserCredit');
					$this->UserCredit->create();
					$this->UserCredit->save(array(
						'credits'    => $requestData['Refund']['credits'],
						'product_id' => '',
						'product_name' => $requestData['Refund']['label'],
						'order_id'   => $this->Order->id,
						'payment_mode' => 'refund',
						'date_upd'   => date('Y-m-d H:i:s'),
						'users_id'   => $user['User']['id']
					));
				
				//crediter client
				$this->User->id = $user['User']['id'];
				$credits = $this->User->field('credit') + $requestData['Refund']['credits'];
				$newCredit = $this->User->saveField('credit', $credits);
				
				if($requestData['Refund']['commentaire']){
					$comm = '<table style="width: 100%; padding: 15px; background-color: #f2f2f2;">
<tbody>
<tr>
<td><span style="color: #000080;">'.nl2br($requestData['Refund']['commentaire']).'</span></td>
</tr>
</tbody>
</table>';
				}else{
					$comm = '';
				}
				
				$is_send = $this->sendCmsTemplatePublic(336, (int)$user['User']['lang_id'], $user['User']['email'], array(
									'NOMBRE_CREDITS' =>$requestData['Refund']['credits'],
									'EMAIL_COMPTE_CLIENT' =>$user['User']['email'],
									'COMMENTAIRE' =>$comm,
								));
				
				
                    if($newCredit){
                        $this->Session->setFlash(__('Le remboursement a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'refund', 'action' => 'index', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
     }
	


	
	public function admin_index(){
		$this->Paginator->settings = array(
				'fields' => array('Order.*','Client.*'),
				'conditions'    => array('payment_mode'=>'refund'),
                'order' => array('Order.id' => 'desc'),
                'paramType' => 'querystring',
			'joins'      => array(
							array(
								'table' => 'users',
								'alias' => 'Client',
								'type'  => 'inner',
								'conditions' => array(
									'Client.id = Order.user_id'
								)
							)
						),
                'limit' => 25
            );

            $refunds = $this->Paginator->paginate($this->Order);

            $this->set(compact('refunds'));
	}
	
}
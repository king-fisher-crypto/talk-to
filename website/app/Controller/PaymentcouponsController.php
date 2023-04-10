<?php
App::uses('PaymentController', 'Controller');

class PaymentcouponsController extends PaymentController {
    protected $payment_mode = 'coupon';
    protected $payment_name = '';
    protected $cart_datas = array();
    protected $_render_admin_index = '/Payment/Coupon/admin_index';

    protected $cms_ids = array(
        'attente_paiement'          =>  220,  /* Propre à chaque mode de paiement !!!! */
        'confirmation_paiement'     =>  221
    );

    public function beforeFilter()
    {
        $this->payment_name = __('Coupons');
        parent::beforeFilter();

        if (isset($this->request->params['admin'])){
            return true;
        }

        /* On créé la commande */
        $order_id = $this->convertCartToOrder($this->Session->read('User.id_cart'));
		$this->loadModel('Order');
		$this->Order->id = $order_id;
        $this->Order->saveField('valid', 1);
		
		$row = $this->Order->findById($order_id);
		$dateNow = date('Y-m-d H:i:s');
		
		
		//On update le crédit de l'user
        $credits = (int)$row['Order']['product_credits'] + (int)$row['Order']['voucher_credits'];
        if ($credits <= 0){
            $errorMsg = __('Impossible de créditer un nombre nul ou négatif de crédits ('.$credits.')');
            return false;
        }
        $newCredit = $this->updateCredit((int)$row['Order']['user_id'], $credits, false);
		
		
        if($newCredit === false){
            $errorMsg = __('Erreur dans la mise à jour du crédit.');
            return false;
        }else{
            //On save l'achat des crédits
            $this->loadModel('UserCredit');
            $this->UserCredit->create();
            $this->UserCredit->save(array(
                'credits'    => $credits,
                'product_id' => $row['Order']['product_id'],
                'product_name' => $row['Order']['product_name'],
                'order_id'   => $row['Order']['id'],
                'payment_mode' => $row['Order']['payment_mode'],
                'date_upd'   => $dateNow,
                'users_id'   => $row['Order']['user_id']
            ));
            //On save l'historique coupon, s'il y a eu un coupon
            if(!empty($row['Order']['voucher_code'])){
                $this->loadModel('VoucherHistory');
                $this->VoucherHistory->create();
                $this->VoucherHistory->save(array(
                    'user_id'           => $row['Order']['user_id'],
                    'code'              => $row['Order']['voucher_code'],
                    'transaction_id'    => 0,   // !!!!!!!!!!!!!! Model UserCredit n'a pas d'ID, lorsqu'il y aura l'api bancaire faut changer le Model UserCredit !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    'credit'            => $row['Order']['voucher_credits'],
                    'percent'           => $row['Order']['voucher_percent'],
                    'date_add'          => $dateNow
                ));
            }

        }
		
		
    }
    protected function onConfirmPayment($order=false)
    {
        parent::onConfirmPayment($order);
    }
    public function index()
    {
		
		/* On envoi le mail d'attente de paiement */
        $this->sendCmsTemplateByMail($this->cms_ids['confirmation_paiement']);

        parent::index();
    }
	
	public function admin_index()
    {
		
		
		$condition = array();
		
		$condition['Order.payment_mode'] = $this->payment_mode;
		
		if(isset($this->request->data['Payment'])){
			if($this->request->data['Payment']['adr_ip'])
				$condition['Order.IP'] = $this->request->data['Payment']['adr_ip'];	
			if($this->request->data['Payment']['client'])
				$condition['User.firstname'] = $this->request->data['Payment']['client'];	
			if($this->request->data['Payment']['email'])
				$condition['User.email'] = $this->request->data['Payment']['email'];
			if($this->request->data['Payment']['numero'])
				$condition['Order.reference'] = $this->request->data['Payment']['numero'];		
		}
		
		
		
        $this->loadModel('Order');
        $this->Paginator->settings = array(
            'fields' => array('Order.*','User.*'),
            'conditions' => $condition,
           
            'order'        => 'Order.date_add DESC',
            'paramType' => 'querystring',
            'limit' => 15
        );

        $rows = $this->Paginator->paginate($this->UserCreditHistory);

        $this->set(array(
            'page_title' => $this->payment_name,
            'orders' => $rows
        ));
        $this->render($this->_render_admin_index);
    }
	
	public function admin_removegift($order_id=0)
    {
		if($order_id){
			$this->loadModel('Order');
			$this->Order->id = $order_id;
			$this->Order->delete();
			//$this->Order->saveField('valid', 0); 
		}
		$this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
	}
	
	public function admin_validgift($order_id=0)
    {
		if($order_id){
			$this->loadModel('Order');
			$this->loadModel('Product');
			$this->loadModel('User');
			$this->loadModel('GiftOrder');
			 $order = $this->Order->find('first', array(
				'conditions'    => array('Order.id' => $order_id),
				'recursive'     => -1
			));
			
			$product = $this->Product->find('first',array(
					'fields' => array('ProductLang.*', 'Product.*'),
					'conditions' => array(
						'Product.tarif <=' => $order['Order']['voucher_amount'],
					),
					'joins' => array(
						array(
							'table' => 'product_langs',
							'alias' => 'ProductLang',
							//'type' => 'left',
							'conditions' => array(
								'ProductLang.product_id = Product.id',
								'ProductLang.lang_id = '.$this->Session->read('Config.id_lang')
							)
						)
					),
					'recursive' => -1,
					'limit' => 1,
					'order' => 'Product.tarif DESC',
				));
			
			$this->Order->id = $order_id;
			$this->Order->saveField('product_id', $product['Product']['id']); 
			$this->Order->saveField('product_name', $product['ProductLang']['name']); 
			$this->Order->saveField('product_credits', $product['Product']['credits']);
			$this->Order->saveField('product_price', $product['Product']['tarif']);
			
			 $gift_order = $this->GiftOrder->find('first', array(
				'conditions'    => array('GiftOrder.code' => $order['Order']['voucher_code']),
				'recursive'     => -1
			));
			$this->GiftOrder->id = $gift_order['GiftOrder']['id'];
			$this->GiftOrder->saveField('beneficiary_id', $order['Order']['user_id']); 
			
			//update customer account
			$client = $this->User->find('first', array(
						'conditions'    => array('User.id' => $order['Order']['user_id']),
						'recursive'     => -1
			));
			$this->User->id = $client['User']['id'];
			$this->User->saveField('credit', $product['Product']['credits']); 
			
			$this->loadModel('UserCredit');
            $this->UserCredit->create();
            $this->UserCredit->save(array(
                'credits'    => $product['Product']['credits'],
                'product_id' => $product['Product']['id'],
                'product_name' => $product['ProductLang']['name'],
                'order_id'   => $order['Order']['id'],
                'payment_mode' => 'coupon',
                'date_upd'   => date('Y-m-d H:i:s'),
                'users_id'   => $order['Order']['user_id']
            ));
			
			$this->addUserCreditPrice($this->UserCredit->id);
			
		}
		$this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
	}

}
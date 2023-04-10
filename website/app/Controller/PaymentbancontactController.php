<?php
App::uses('PaymentController', 'Controller');

/*
DOC https://stripe.com/docs/testing
Carte test present ds doc
//contact tech : Youssef Ben Othman <youssef@stripe.com>
*/
class PaymentbancontactController extends PaymentController {
    protected $_stripe_mode = 'prod'; /* Dev ou prod */
    protected $_stripe_confs = array(
        'dev'  =>  array(
            'public_key'         => 'pk_test_njXT5xxKaXrVtmsZo69P2AvT00r5M2LREQ',
            'private_key'        => 'sk_test_JFhyexc86xNJjf5rCxnGm7ks00Id6GSvbw'
        ),
        'prod' => array(
            'public_key'         => 'pk_live_RrrnetDh9ZPo67X1NdoenBol00q4Qxda8D',
            'private_key'        => 'sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs'
        )
    );

    protected $payment_mode = 'stripe';
    protected $payment_name = '';
    protected $cart_datas = array();
	protected $_render_index = '/Payment/Bancontact/index';
	
    protected $_render_admin_index = '/Payment/Stripe/admin_index';
    protected $_render_validation = '/Payment/Bancontact/validation';
	protected $_render_validation_gift = '/Gifts/validation';
    protected $_render_error = '/Payment/Bancontact/error';
	protected $_url_validation = '/Payment/Bancontact/validation?utm_novverride=1';
    protected $_url_error = '/Payment/Bancontact/error?utm_novverride=1';
	protected $_url_form = '/paymentstripe/submit';

    /* On ne tente pas de charger le panier pour les actions listées dans ce tableau (retour de banque par ex) */
    protected $_validation_actions = array('validation','error_return');


    protected $cms_ids = array(
        'confirmation_paiement'     =>  433
    );



    public function admin_index()
    {
				
		$condition = array();
		
		$condition['Order.payment_mode'] = $this->payment_mode;
		
		$condition['Order.valid'] = 0;
		if(isset($this->request->query['valid'])){
			unset($condition['Order.valid']);
			$condition['OR'] = array('Order.valid = 1', 'Order.valid =3');
		} //$condition['Order.valid'] = 1;
		if(isset($this->request->query['oppos'])) $condition['Order.valid'] = 2;
		
		//$condition['Order.valid'] = (isset($this->request->query['valid']))?1:0;

		if(isset($this->request->data['Payment'])){
			
			$this->Session->write('Payment_adr_ip', '');
			$this->Session->write('Payment_client', '');
			$this->Session->write('Payment_email', '');
			$this->Session->write('Payment_numero', '');
			
			if($this->request->data['Payment']['adr_ip']){
				$condition['Order.IP'] = $this->request->data['Payment']['adr_ip'];	
				$this->Session->write('Payment_adr_ip', $this->request->data['Payment']['adr_ip']);
			}
				
			if($this->request->data['Payment']['client']){
				$condition['User.firstname'] = $this->request->data['Payment']['client'];
				$this->Session->write('Payment_client', $this->request->data['Payment']['client']);
			}
					
			if($this->request->data['Payment']['email']){
				$condition['User.email'] = $this->request->data['Payment']['email'];
				$this->Session->write('Payment_email', $this->request->data['Payment']['email']);
			}
				
			if($this->request->data['Payment']['numero']){
				$condition['stripelogs.id'] = $this->request->data['Payment']['numero'];
				$this->Session->write('Payment_numero', $this->request->data['Payment']['numero']);
			}
						
		}else{
		
			if($this->Session->read('Payment_adr_ip')){
				$condition['Order.IP'] = $this->Session->read('Payment_adr_ip');
			}
			if($this->Session->read('Payment_client')){
				$condition['User.firstname'] = $this->Session->read('Payment_client');
			}
			if($this->Session->read('Payment_email')){
				$condition['User.email'] = $this->Session->read('Payment_email');
			}
			if($this->Session->read('Payment_numero')){
				$condition['stripelogs.id'] = $this->Session->read('Payment_numero');
			}
		}
		//var_dump($condition);
		
        $this->loadModel('Order');
        $this->Paginator->settings = array(
            'fields' => array('Order.*','User.*','stripelogs.*'),
            'conditions' => $condition,
            'joins'     => array(
                array(

                    'alias' => 'stripelogs',
                    'table' => 'order_stripetransactions',
                    'type'  => 'inner',
                    'conditions' => array('stripelogs.cart_id = Order.cart_id')
                )
            ),
            'order'        => 'Order.date_add DESC',
            'paramType' => 'querystring',
            'limit' => 15
        );

        $rows = $this->Paginator->paginate($this->Order);

        $this->set(array(
            'page_title' => $this->payment_name,
            'orders' => $rows
        ));
        $this->render($this->_render_admin_index);
    }

    public function beforeFilter()
    {
        $this->payment_name = __('Paiement Bancontact');
        $this->Auth->allow('confirmation','confirmationdev','admin_declarer_valide','admin_declarer_impaye','admin_declarer_rembourse','getHookPayment',
			 'getHookPaymentGift','confirmation_gift','validation_gift','remove_card','confirm_payment','index_gift');
		
        parent::beforeFilter();
    }

    public function index()
    {
		$this->set('public_key', $this->_stripe_confs[$this->_stripe_mode]['public_key'] );
		$this->set('form_url', $this->_url_form );
		
		$cart = $this->cart_datas;
		$this->set('price', $cart['total_price'] );
		$this->set('devise', $cart['product']['Country']['devise'] );
		
		$return_url =  Router::url('/', true).'paymentbancontact/confirm_payment';
		
		
		
		$this->set('bancontact_amount', $cart['total_price'] * 100 );
		$this->set('bancontact_currency', strtolower($cart['product']['Country']['devise_iso']) );
		$this->set('bancontact_desc', $cart['id_cart'] );
		$this->set('bancontact_firstname', $this->cart_datas['user']['firstname'].' '.$this->cart_datas['user']['lastname'] );
		$this->set('bancontact_return', $return_url );
		
		require '../Lib/stripe/init.php';
		\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
		
		//check si le client a deja un compte stripe
		$this->loadModel('StripeCustomer');
		$customer_stripe = $this->StripeCustomer->find('first',array(
            'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
            'recursive' => -1
			));
		
		
		try {
			$transfer_group = $cart['id_cart'];
			$desc = $cart["user"]["firstname"]. ' ('.$cart["user"]["email"]. ') - '.$cart['id_cart'];
			
			$intent = \Stripe\PaymentIntent::create([
			  'amount' => $cart['total_price'] * 100,
			  'currency' => strtolower($cart['product']['Country']['devise_iso']),
			  'payment_method_types' => ['bancontact'],
			  'description' => $desc,
			  'statement_descriptor_suffix' => 'Limited',
			  'transfer_group' => $transfer_group,
			]);
			$this->set('client_secret', $intent->client_secret );
		 } catch (\Stripe\Error\Base $e) {
				# Display error on client
				
			  }
		$this->payment_mode = 'bancontact';
		parent::index();
    }
	
	
	public function confirm_payment()
    {
		
		//load session source
		$is_source = false;
		
		$requestData = $this->request->query;
		
		$source_id = $requestData['payment_intent'];
		
		if(!$source_id){
			$this->error_return();
		}else{
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			
			$intent = '';
			
			try {
				$intent = \Stripe\PaymentIntent::retrieve($source_id);
			
			 } catch (\Stripe\Error\Base $e) {
				$this->error_return();
			}
			
			if(!is_object($intent))$this->error_return();
			
			if($requestData['redirect_status'] != 'succeeded'){
				$this->error_return();
			}else{
				$cart = $this->cart_datas;
				$price = $cart['total_price']*100;
				$devise = strtolower($cart["product"]["Country"]["devise_iso"]);
				
				$this->loadModel('StripeCustomer');
				$customer_stripe = $this->StripeCustomer->find('first',array(
							'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
							'recursive' => -1
							));
				if($customer_stripe){
					$customer = \Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id']);
				}else{
					$customer = \Stripe\Customer::create([
							  "email" => $cart["user"]["email"],
							]);
					$stripeData = array();
							$stripeData['StripeCustomer'] = array();
							$stripeData['StripeCustomer']['user_id'] = $this->Auth->user('id');
							$stripeData['StripeCustomer']['email'] = $cart["user"]["email"];
							$stripeData['StripeCustomer']['customer_id'] = $customer->id;
							$stripeData['StripeCustomer']['date_add'] = date('Y-m-d H:i:s');

							$this->StripeCustomer->create();
							$this->StripeCustomer->save($stripeData);
				}
				
				
				$this->saveLogOrder($intent);
				//redirect validation page
				
				//if($charge->status == 'succeeded'){
					$this->submit();
				//}else{
				//	$this->error_return();
				//}
				
			}
			
		}
	}
	
	public function go_payment()
    {
		
		//load session source
		$is_source = false;
		var_dump($this->request->data);
		if($this->request->data['source_id']){
			$source_id = $this->request->data['source_id'];
			$is_source = true;
		}
		
		if(!$source_id){
			$this->error_return();
		}else{
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			
			$source = '';
			
			try {
				$source = \Stripe\Source::retrieve($source_id);
			
			 } catch (\Stripe\Error\Base $e) {
				$this->error_return();
			}
			var_dump($source);exit;
			if(!is_object($source))$this->error_return();
			
			if($source->status != 'chargeable'){
				$this->error_return();
			}else{
				$cart = $this->cart_datas;
				$price = $cart['total_price']*100;
				$devise = strtolower($cart["product"]["Country"]["devise_iso"]);
				
				$this->loadModel('StripeCustomer');
				$customer_stripe = $this->StripeCustomer->find('first',array(
							'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
							'recursive' => -1
							));
				if($customer_stripe){
					$customer = \Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id']);
				}else{
					$customer = \Stripe\Customer::create([
							  "email" => $cart["user"]["email"],
							]);
					$stripeData = array();
							$stripeData['StripeCustomer'] = array();
							$stripeData['StripeCustomer']['user_id'] = $this->Auth->user('id');
							$stripeData['StripeCustomer']['email'] = $cart["user"]["email"];
							$stripeData['StripeCustomer']['customer_id'] = $customer->id;
							$stripeData['StripeCustomer']['date_add'] = date('Y-m-d H:i:s');

							$this->StripeCustomer->create();
							$this->StripeCustomer->save($stripeData);
				}
				
				
				//charge la source
				$desc = $cart["user"]["firstname"]. ' ('.$cart["user"]["email"]. ') - '.$cart['id_cart'];
				$charge = \Stripe\Charge::create([
				  'amount' => $price,
				  'currency' => $devise,
				  'customer' => $customer->id,
				  'source' => $source->id,
					'description' => $desc,
					'statement_descriptor_suffix' => 'Limited',
				]);
				$this->saveLogOrder($charge);
				//redirect validation page
				
				if($charge->status == 'succeeded'){
					$this->submit();
				}else{
					$this->error_return();
				}
				
			}
			
		}
	}
	
	public function submit()
    {
				$cart = $this->cart_datas;
				$id_order = $this->convertCartToOrder($cart['id_cart']);
				if (!$id_order){
					$this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
					return false;
				}
				
				$this->validateOrder($id_order);
				$this->displayValidatePage();
	}
	
	
	
    private function saveLogOrder($stripeLogs = 0)
    {
		$this->autoRender = false;
        if (!$stripeLogs)return false;
		
		
		$this->loadModel('OrderStripetransaction');
        $this->OrderStripetransaction->create();
		$cart = $this->cart_datas;
		
		$data = array();
		$data['cart_id'] = $cart['id_cart'] ;
		$data['order_id'] = $order_id ;
		$data['id'] = $stripeLogs->id;
		$data['payment_method'] = $stripeLogs->payment_method;
		$data['paid'] = $stripeLogs->paid;
		$data['captured'] = $stripeLogs->captured;
		$data['outcome'] = json_encode($stripeLogs->outcome);
		$data['created'] = $stripeLogs->created;
		$data['amount'] = $stripeLogs->amount;
		$data['currency'] = $stripeLogs->currency;
		$data['date_add'] = date('Y-m-d H:i:s');
		
        $this->OrderStripetransaction->saveAll($data);
    }
   
   
    private function validateOrder($order_id=0)
    {
		$this->autoRender = false;
        if (!$order_id)return false;
		$cart = $this->cart_datas;
        $this->loadModel('OrderStripetransaction');
		
		$dbb_r = new DATABASE_CONFIG();
		$dbb_route = $dbb_r->default;
		$mysqli_conf = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
		$mysqli_conf->query("UPDATE order_stripetransactions set order_id = '{$order_id}' where cart_id = '{$cart['id_cart']}'");
		
		/*
		
		$this->OrderStripetransaction->save(array(
                'order_id' => $order_id,
                'cart_id' => $cart['id_cart']
            ));*/

        $this->order_confirm($order_id);
    }

    public function validation_gift()
    {
        $this->autoRender = false;
		$gift_order = array();
		$giftorder_id = $this->Session->read('GiftOrderId');
		if($giftorder_id){
			$this->loadModel('GiftOrder');
			$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $giftorder_id),
					'recursive' => -1,
				));
		}

       $this->set('order',$gift_order);
		

        $this->render($this->_render_validation_gift);
    }
	
	public function validation()
    {
        $this->autoRender = false;

        $contenu = $this->getCmsPage(211);
		
        $this->set('contenu',$contenu);


        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $id_cart = $this->Session->read('User.save_id_cart_for_validation');
        $this->loadModel('Cart');
        $cart_datas = $this->Cart->getDatas($id_cart);
        $this->set('cart_datas',$cart_datas);

		$this->loadModel('Order');
		$order = $this->Order->find('first', array(
                            'conditions'    => array('Order.cart_id' => $id_cart),
                            'recursive'     => -1
                        ));
		if(count($order)){
			$this->Order->id = $order['Order']['id'];
			$this->Order->saveField('IP', getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR'));
		}
		
		$this->loadModel('Order');
		$this->loadModel('User');
		$user_id = $cart_datas['user']["id"];
		
		/* FLAG is new */
		$orders = $this->Order->find('first', array(
                'conditions' => array('Order.user_id' => $user_id, 'Order.valid' => 1, 'Order.cart_id !=' => $id_cart),
                'recursive' => -1
            ));
		$is_new = 1;
		if(!empty($orders)){
			$is_new = 0;
		}
		
		$this->set('is_new_customer_payment', $is_new);
		
		/* FLAG delay order */
		$client = $this->User->find('first', array(
                'conditions' => array('User.id' => $user_id),
                'recursive' => -1
            ));
		$start = strtotime($client['User']['date_add']);
		$end = strtotime(date('Y-m-d H:i:s'));
		$days_between = ceil(abs($end - $start) / 86400);
		$this->set('delay_payment', $days_between);

        $this->render($this->_render_validation);
		
        /* Important !! */
        $this->clearSessionCart();
        $this->Session->delete('User.save_id_cart_for_validation');
    }
	 private function displayValidatePage()
    {
        $this->autoRender = false;
        $contenu = $this->getCmsPage(434);
        $this->set('contenu',$contenu);


        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $id_cart = $this->Session->read('User.save_id_cart_for_validation');
        $this->loadModel('Cart');
        $cart_datas = $this->Cart->getDatas($id_cart);
		 
        $this->set('cart_datas',$cart_datas);

		 $this->loadModel('Order');
		$this->loadModel('User');
		$user_id = $cart_datas['user']["id"];
		 
		 /* FLAG is new */
		$orders = $this->Order->find('first', array(
                'conditions' => array('Order.user_id' => $user_id, 'Order.valid' => 1, 'Order.cart_id !=' => $id_cart),
                'recursive' => -1
            ));
		$is_new = 1;
		if(!empty($orders)){
			$is_new = 0;
		}
		
		$this->set('is_new_customer_payment', $is_new);
		
		/* FLAG delay order */
		$client = $this->User->find('first', array(
                'conditions' => array('User.id' => $user_id),
                'recursive' => -1
            ));
		$start = strtotime($client['User']['date_add']);
		$end = strtotime(date('Y-m-d H:i:s'));
		$days_between = ceil(abs($end - $start) / 86400);
		$this->set('delay_payment', $days_between);

        $this->render($this->_render_validation);

        /* Important !! */
        $this->clearSessionCart();
        $this->Session->delete('User.save_id_cart_for_validation');
		 
    }
	
	
	 private function displayValidatePageGift($id_order = 0)
    {
        $this->autoRender = false;
		 $gift_order = array();
		 if($id_order){
			$this->loadModel('GiftOrder');
			$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $id_order),
					'recursive' => -1,
				));
		}
		 
        $this->set('order',$gift_order);

        $this->render($this->_render_validation_gift);
    }
	
	
    public function error_return()
    {
        $this->autoRender = false;

        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $contenu = $this->getCmsPage(432);
        $this->set('contenu',$contenu);

        $this->render($this->_render_error);

        $this->clearSessionCart();
    }


	
	public function admin_declarer_impaye(){
		if($this->request->is('ajax')){
			
			$requestData = $this->request->data;
           
			if(!isset($requestData)|| !isset($this->request->data['id_order']))
                $this->jsonRender(array('return' => false));
			
			
			$this->loadModel('Order');
			$this->Order->id = $this->request->data['id_order'];
            $this->Order->saveField('valid',2);
			
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE order_stripetransactions SET date_upd = NOW() WHERE order_id = '{$this->request->data['id_order']}'");
			
			
			
			$this->jsonRender(array('return' => true));
		}
	}
	
	public function admin_declarer_valide(){
		if($this->request->is('ajax')){
			
			$requestData = $this->request->data;
           
			if(!isset($requestData)|| !isset($this->request->data['id_order']))
                $this->jsonRender(array('return' => false));
			
			
			$this->loadModel('Order');
			$this->Order->id = $this->request->data['id_order'];
            $this->Order->saveField('valid',1);
			
			
			$this->jsonRender(array('return' => true));
		}
	}
	
	public function admin_declarer_rembourse(){
		if($this->request->is('ajax')){
			
			$requestData = $this->request->data;
           
			if(!isset($requestData)|| !isset($this->request->data['id_order']))
                $this->jsonRender(array('return' => false));
			
			
			$this->loadModel('Order');
			$this->Order->id = $this->request->data['id_order'];
            $this->Order->saveField('valid',3);
			
			
			$this->jsonRender(array('return' => true));
		}
	}
	
	public function save_cart(){
		$this->autoRender = false;
		$this->layout = false;
			
			$requestData = $this->request->data;
			if(!isset($requestData)|| !isset($this->request->data['source']))
                $this->jsonRender(array('return' => false));
			
			if($requestData['source']){
				$cart = $this->cart_datas;
				$this->Session->write('Bancontact_source',$requestData['source']);
				$this->Session->write('Bancontact_cart',$cart['id_cart']);
				
				$this->jsonRender(array('return' => true));
			}
	}
	
	public function remove_card(){
		$this->autoRender = false;
		$this->layout = false;
			
			$requestData = $this->request->data;
			if(!isset($requestData)|| !isset($this->request->data['customer']))
                $this->jsonRender(array('return' => false));
			
			if($requestData['customer'] && $requestData['card']){
				require '../Lib/stripe/init.php';
				\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
				\Stripe\Customer::deleteSource(
				  $requestData['customer'],
				  $requestData['card']
				);

				
				$this->jsonRender(array('return' => true));
			}
	}

}
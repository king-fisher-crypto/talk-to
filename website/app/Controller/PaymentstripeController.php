<?php
App::uses('PaymentController', 'Controller');

/*
DOC https://stripe.com/docs/testing
Carte test present ds doc
//contact tech : Youssef Ben Othman <youssef@stripe.com>
*/
class PaymentstripeController extends PaymentController {
    protected $_stripe_mode = 'dev'; /* Dev ou prod */
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
    protected $_render_admin_index = '/Payment/Stripe/admin_index';
    protected $_render_validation = '/Payment/Stripe/validation';
	protected $_render_validation_gift = '/Gifts/validation';
    protected $_render_error = '/Payment/Stripe/error';
	protected $_url_validation = '/Payment/Stripe/validation?utm_novverride=1';
    protected $_url_error = '/Payment/Stripe/error?utm_novverride=1';
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
			
			if($this->request->data['Payment']['adr_ip'] && $this->request->data['Payment']['adr_ip'] != ' '){
				$condition['Order.IP'] = $this->request->data['Payment']['adr_ip'];	
				$this->Session->write('Payment_adr_ip', $this->request->data['Payment']['adr_ip']);
			}
				
			if($this->request->data['Payment']['client'] && $this->request->data['Payment']['client'] != ' '){
				$condition['User.firstname'] = $this->request->data['Payment']['client'];
				$this->Session->write('Payment_client', $this->request->data['Payment']['client']);
			}
					
			if($this->request->data['Payment']['email'] && $this->request->data['Payment']['email'] != ' '){
				$condition['User.email'] = $this->request->data['Payment']['email'];
				$this->Session->write('Payment_email', $this->request->data['Payment']['email']);
			}
				
			if($this->request->data['Payment']['numero'] && $this->request->data['Payment']['numero'] != ' '){
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
		$limit = 15;
		 if($this->Session->check('Date')){
			 if(isset($this->request->query['oppos'])){
					$condition = array_merge($condition, array(
                    'stripelogs.date_upd >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'stripelogs.date_upd <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
				}else{
					$condition = array_merge($condition, array(
                    'Order.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'Order.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
				}
                
			 $limit = 999999;
            }
		
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
            'limit' => $limit
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
        $this->payment_name = __('Paiement Carte Bleue Stripe');
        $this->Auth->allow('confirmation','confirmationdev','admin_declarer_valide','admin_declarer_impaye','admin_declarer_rembourse','admin_declarer_incident','getHookPayment',
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
		
		//check si le client a deja un compte stripe
		$this->loadModel('StripeCustomer');
		$customer_stripe = $this->StripeCustomer->find('first',array(
            'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
            'recursive' => -1
			));
		if($customer_stripe){
			$this->set('stripe_customer', $customer_stripe['StripeCustomer']['customer_id'] );
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			
			$default_source = \Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id'])->$default_source;
			
			
			$cards = \Stripe\PaymentMethod::all(['customer' => $customer_stripe['StripeCustomer']['customer_id'], 'type' => 'card']);//\Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id'])->sources->all(array("object" => "card"));
			$cartes = array();
			$nn = 1;
			foreach($cards->data as $card){
				$obj = new stdClass();
				if(!$default_source)$default_source = $card->id;
				$obj->id = $card->id;
				$obj->type = strtolower($card->card->brand);
				$obj->exp = $card->card->exp_month.'/'.$card->card->exp_year;
				$obj->num = $card->card->last4;
				
				if($obj->id == $default_source)
					$cartes[0] = $obj;
				else
					$cartes[$nn] = $obj;
				
				$nn++;

			}
			if($default_source)
				$this->set('stripe_default_card', $default_source );
			if(count($cartes))
				$this->set(compact('cartes'));
				
		}
		parent::index();

    }
	
	 public function index_gift()
    {
		$this->set('public_key', $this->_stripe_confs[$this->_stripe_mode]['public_key'] );
		$this->set('form_url', $this->_url_form );
		 
		 $giftorder_id = $this->Session->read('GiftOrderId');
		 $gift_order = array();
		if($giftorder_id){
			$this->loadModel('GiftOrder');
			$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $giftorder_id),
					'recursive' => -1,
				));
		}
		
		$this->set('price', $gift_order['GiftOrder'] ['amount'] );
		$this->set('devise', $gift_order['GiftOrder']['devise'] );
		
		//check si le client a deja un compte stripe
		$this->loadModel('StripeCustomer');
		$customer_stripe = $this->StripeCustomer->find('first',array(
            'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
            'recursive' => -1
			));
		if($customer_stripe){
			$this->set('stripe_customer', $customer_stripe['StripeCustomer']['customer_id'] );
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			
			$default_source = \Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id'])->$default_source;
			
			
			$cards = \Stripe\PaymentMethod::all(['customer' => $customer_stripe['StripeCustomer']['customer_id'], 'type' => 'card']);//\Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id'])->sources->all(array("object" => "card"));
			$cartes = array();
			$nn = 1;
			foreach($cards->data as $card){
				$obj = new stdClass();
				if(!$default_source)$default_source = $card->id;
				$obj->id = $card->id;
				$obj->type = strtolower($card->card->brand);
				$obj->exp = $card->card->exp_month.'/'.$card->card->exp_year;
				$obj->num = $card->card->last4;
				
				if($obj->id == $default_source)
					$cartes[0] = $obj;
				else
					$cartes[$nn] = $obj;
				
				$nn++;

			}
			$this->set('stripe_default_card', $default_source );
			$this->set(compact('cartes'));
		}
		parent::index();

    }
	
	public function confirm_payment()
    {
		
		$requestData = $this->request->data;
			if(!isset($requestData))
                $this->jsonRender(array('return' => false));
		
		$giftorder_id = $this->Session->read('GiftOrderId');
		if($giftorder_id){
			$this->loadModel('GiftOrder');
			$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $giftorder_id),
					'recursive' => -1,
				));
			$price = $gift_order['GiftOrder'] ['amount']*100 ;
		    $devise = $gift_order['GiftOrder']['devise'] ;
		}else{
			$cart = $this->cart_datas;
			$price = $cart['total_price']*100;
			$devise = strtolower($cart["product"]["Country"]["devise_iso"]);
		}
		
		
		require '../Lib/stripe/init.php';
		\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);

		if($this->request->data['payment_method_id']){
			
			
			//verifie doublon
			if($cart['id_cart']){
				$this->loadModel('OrderStripetransaction');
				$panier = $this->OrderStripetransaction->find('first',array(
							'conditions' => array('OrderStripetransaction.cart_id' => $cart['id_cart']),
							'recursive' => -1
							));
				if($panier){
					if(!$panier['OrderStripetransaction']['order_id']){
						$id_order = $this->convertCartToOrder($cart['id_cart']);
						if (!$id_order){
							$this->jsonRender(array('error' => array('message'=>__('Erreur interne, veuillez réessayer en actualisant cette page.'))));
						}
						$this->validateOrder($id_order);
						$this->jsonRender(array('error' => array('message'=>__('Le paiement est déjà validé, merci de retourner sur votre compte.'))));
					}else{
						$this->jsonRender(array('error' => array('message'=>__('Le paiement est déjà validé, merci de retourner sur votre compte.'))));
					}
				}
			}
			
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
			
			
			if($this->Session->read('GiftOrderId')){
				$desc = 'carte cadeau : '.$this->Session->read('GiftOrderId');
				$transfer_group = $this->Session->read('GiftOrderId');
				
			}else{
				$desc = $this->request->data['cardholdername']. ' ('.$cart["user"]["email"]. ') - '.$cart['id_cart'];
				$transfer_group = $cart['id_cart'];
			}
			
			$is_auth_confirm = false;
			
			if (isset($this->request->data['payment_method_id'])) {
				//si carte deja authentifié on valide
				$pm = \Stripe\PaymentMethod::retrieve($this->request->data['payment_method_id']);
				if($pm->metadata && isset($pm->metadata->auth_confirm) && $pm->metadata->auth_confirm){
					
					//verification si premier paiement > 30 euros
					if($price > 30){
						/*$this->loadModel('Order');
						$order = $this->Order->find('first',array(
							'conditions' => array('Order.user_id' => $this->Auth->user('id'), 'total >' => 30, 'valid' => 1, 'payment_mode' => 'stripe'),
							'recursive' => -1
							));
						if($order){
							$is_auth_confirm = true;
						}else{*/
							$is_auth_confirm = false;
						//}
					}else{
						$is_auth_confirm = true;
					}
				}
			}
			//check si save card
			$this->loadModel('User');
			$save_bank_card = $this->User->field('save_bank_card', array('id' => $this->Auth->user('id')));
			if($save_bank_card)$save_card = true; else $save_card = false;
			
			try {
				if (isset($this->request->data['payment_method_id'])) {
				  # Create the PaymentIntent
					
					$intent = \Stripe\PaymentIntent::create([
							'payment_method' => $this->request->data['payment_method_id'],
							'save_payment_method' => $save_card,
							'customer' => $customer->id,
							'amount' => $price,
							'currency' => $devise,
							'confirmation_method' => 'manual',
							'confirm' => true,
							'description' => $desc,
							'statement_descriptor_suffix' => 'Limited',
							'metadata' => ['auth_confirm' => $is_auth_confirm],
							'transfer_group' => $transfer_group,
              'payment_method_options' => ['card' => ['request_three_d_secure' => 'any']]
						]);
				 
				}
				
			  } catch (\Stripe\Error\Base $e) {
				# Display error on client
				$body = $e->getJsonBody();
  				$err  = $body['error'];
				$error_code = $err['decline_code'];
				if(!$error_code)$error_code = $err['code'];
				
				if($error_code){
					$message = $this->getMessageError($error_code);
				}else{
					$message = $e->getMessage();
				}
				$this->jsonRender(array('error' => array('message'=>$message)));
			  }

			if($is_auth_confirm && $intent->status != 'succeeded'){
				$intent->confirm();
			}
			
			
		}else{
			if($this->request->data['payment_intent_id']){
				$intent = \Stripe\PaymentIntent::retrieve($this->request->data['payment_intent_id']);
				
				try {
					$intent->confirm();
				} catch (\Stripe\Error\Base $e) {
					# Display error on client
					$body = $e->getJsonBody();
					$err  = $body['error'];
					$error_code = $err['decline_code'];
					if(!$error_code)$error_code = $err['code'];

					if($error_code){
						$message = $this->getMessageError($error_code);
					}else{
						$message = $e->getMessage();
					}
				$this->jsonRender(array('error' => array('message'=>$message)));
			  }
				$save_bank_card = $this->User->field('save_bank_card', array('id' => $this->Auth->user('id')));
				if($save_bank_card && $intent->payment_method){
				
					\Stripe\PaymentMethod::update(
					  $intent->payment_method,
					  [
						'metadata' => ['auth_confirm' => 1],
					  ]
					);
				}
			}else{
				$this->jsonRender(array('return' => false));
			}
		}
		

		if (($intent->status == 'requires_source_action' || $intent->status == 'requires_action' ) &&
			$intent->next_action->type == 'use_stripe_sdk') {
			# Tell the client to handle the action
			 $this->jsonRender(array('requires_action' => true,'payment_intent_client_secret' => $intent->client_secret));
		} else if ($intent->status == 'succeeded') {
			# The payment didn’t need any additional actions and completed!
			# Handle post-payment fulfillment
			 $this->saveLogOrder($intent);
			 $cart = $this->cart_datas;
			 $this->Session->write('StripeCart', $cart);
				$id_order = $this->convertCartToOrder($cart['id_cart']);
				if (!$id_order){
					$this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
					return false;
				}
				$this->validateOrder($id_order);
			 $this->jsonRender(array('success' => true));
		} else {
			# Invalid status
			http_response_code(500);
			$this->jsonRender(array('error' => array('message'=>'Invalid PaymentIntent status')));
		}
		
	
	}
	
	public function getMessageError($error_code){
		$message = '';
		switch ($error_code) {
						case 'approve_with_id':
							$message =  __('Le paiement est refusé');
							break;
						case 'call_issuer':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'card_not_supported':
							$message =  __('La carte ne prend pas en charge ce type d\'achat.');
							break;
						case 'card_velocity_exceeded':
							$message =  __('Le solde ou la limite de crédit disponible sur cette carte est dépassé.');
							break;
						case 'currency_not_supported':
							$message =  __('La carte ne prend pas en charge la devise spécifiée.');
							break;
						case 'card_declined':
						case 'do_not_honor':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'do_not_try_again':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'duplicate_transaction':
							$message =  __('Une transaction avec un montant et des informations de carte de crédit identiques a été soumise très récemment.');
							break;
						case 'expired_card':
							$message =  __('La carte a expiré.');
							break;
						case 'fraudulent':
							$message =  __('Le paiement a été refusé car Stripe le soupçonne d\'être frauduleux.');
							break;
						case 'generic_decline':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'incorrect_number':
							$message =  __('Le numéro de carte est incorrect.');
							break;
						case 'incorrect_cvc':
							$message =  __('Le numéro CVC est incorrect.');
							break;
						case 'incorrect_pin':
							$message =  __('Le code PIN entré est incorrect. Ce code de refus ne s\'applique qu\'aux paiements effectués avec un lecteur de carte.');
							break;
						case 'incorrect_zip':
							$message =  __('Le code postal est incorrect.');
							break;
						case 'insufficient_funds':
							$message =  __('La carte ne dispose pas de fonds suffisants pour effectuer l\'achat.');
							break;
						case 'invalid_account':
							$message =  __('La carte ou le compte auquel la carte est connectée est invalide.');
							break;
						case 'invalid_cvc':
							$message =  __('Le numéro CVC est incorrect.');
							break;
						case 'invalid_expiry_year':
							$message =  __('L\'année d\'expiration invalide.');
							break;
						case 'invalid_number':
							$message =  __('Le numéro de carte est incorrect.');
							break;
						case 'invalid_pin':
							$message =  __('Le code PIN entré est incorrect. Ce code de refus ne s\'applique qu\'aux paiements effectués avec un lecteur de carte.');
							break;
						case 'issuer_not_available':
							$message =  __('L\'émetteur de la carte n\'a pas pu être contacté et le paiement n\'a donc pas pu être autorisé.');
							break;
						case 'lost_card':
							$message =  __('Le paiement a été refusé.');// car la carte est perdue.
							break;
						case 'merchant_blacklist':
							$message =  __('Le paiement a été refusé.');// car il correspond à une valeur de la liste de blocage de l'utilisateur Stripe.
							break;
						case 'new_account_information_available':
							$message =  __('La carte ou le compte auquel la carte est connectée est invalide.');
							break;
						case 'no_action_taken':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'not_permitted':
							$message =  __('Le paiement n\'est pas autorisé.');
							break;
						case 'pickup_card':
							$message =  __('Cette carte ne peut pas être utilisée pour effectuer ce paiement.');//(il est possible qu’elle ait été perdue ou volée)
							break;
						case 'pin_try_exceeded':
							$message =  __('Le nombre autorisé d\'essais de code PIN a été dépassé.');
							break;
						case 'processing_error':
							$message =  __('Une erreur s\'est produite lors du traitement de la carte.');
							break;
						case 'reenter_transaction':
							$message =  __('Le paiement n\'a pas pu être traité par l\'émetteur pour une raison inconnue.');
							break;
						case 'restricted_card':
							$message =  __('Cette carte ne peut pas être utilisée pour effectuer ce paiement.');//(il est possible qu’elle ait été perdue ou volée)
							break;
						case 'revocation_of_all_authorizations':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'revocation_of_authorization':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'security_violation':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'service_not_allowed':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'stolen_card':
							$message =  __('Le paiement a été refusé.');// car la carte est volée.
							break;
						case 'stop_payment_order':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'testmode_decline':
							$message =  __('Un numéro de carte de test Stripe a été utilisé.');
							break;
						case 'transaction_not_allowed':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'try_again_later':
							$message =  __('La carte a été refusée pour une raison inconnue.');
							break;
						case 'withdrawal_count_limit_exceeded':
							$message =  __('Le solde ou la limite de crédit disponible sur cette carte est dépassé.');
							break;

					}
		return $message;
	}
	
	public function submit()
    {
		if($this->request->is('post')){
			$this->autoRender = false;
			
			if($this->Session->read('GiftOrderId')){
				$this->validateGift($this->Session->read('GiftOrderId'));
                $this->displayValidatePageGift();
			}else{
				
				//$cart = $this->Session->read('StripeCart');//$this->cart_datas;
				/*$id_order = $this->convertCartToOrder($cart['id_cart']);
				if (!$id_order){
					$this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
					return false;
				}
				
				$this->validateOrder($id_order);*/
				$this->displayValidatePage();
			}
			
		}
	}
	
	/*public function submit()
    {
		
		if($this->request->is('post')){
			require '../Lib/stripe/init.php';
            $requestData = $this->request->data;
			$cart = $this->cart_datas;
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			$price = $cart['total_price']*100;
			$devise = strtolower($cart["product"]["Country"]["devise_iso"]);
			
			if (!isset($requestData['stripeCard'])){
				try {
					if (!isset($requestData['stripeToken']))
					  throw new Exception("The Stripe Token was not generated correctly");
					
					
					$this->loadModel('StripeCustomer');
					$customer_stripe = $this->StripeCustomer->find('first',array(
						'conditions' => array('StripeCustomer.user_id' => $this->Auth->user('id')),
						'recursive' => -1
						));
					if($customer_stripe){
						$customer = \Stripe\Customer::retrieve($customer_stripe['StripeCustomer']['customer_id']);
						
						$carte = \Stripe\Customer::createSource(
						  $customer->id,
						  [
							'source' => $requestData['stripeToken'],
						  ]
						);
						
					}else{
						$customer = \Stripe\Customer::create([
						  "email" => $cart["user"]["email"],
						  "source" => $requestData['stripeToken'],
						]);

						$cards = \Stripe\Customer::retrieve($customer->id)->sources->all(array("object" => "card"));
						$carte = '';
						foreach($cards->data as $card){
							$carte = $card;
							break;
						}
					}
					
					if (!isset($carte ))
					  throw new Exception("Erreur technique lors de l\'enregistrement de votre paiement.");
					if(!$customer_stripe){
						$stripeData = array();
						$stripeData['StripeCustomer'] = array();
						$stripeData['StripeCustomer']['user_id'] = $this->Auth->user('id');
						$stripeData['StripeCustomer']['email'] = $cart["user"]["email"];
						$stripeData['StripeCustomer']['customer_id'] = $customer->id;
						$stripeData['StripeCustomer']['date_add'] = date('Y-m-d H:i:s');

						$this->StripeCustomer->create();
						$this->StripeCustomer->save($stripeData);
					}
					\Stripe\Customer::update(
					  $customer->id,
					  [
						'default_source' => $carte->id,
					  ]
					);
					
					$ret = \Stripe\Charge::create(array("amount" => $price,
												"currency" => $devise,
												"source" => $carte->id,
											    'customer' => $customer->id,
												"receipt_email" =>$cart["user"]["email"],
												"description" => $requestData['cardholdername']. ' ('.$cart["user"]["email"]. ') - '.$cart['id_cart']));


					  $id_order = $this->convertCartToOrder($cart['id_cart']);
						if (!$id_order){
							$this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
							$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
							return false;
						}
					 $this->validateOrder($id_order, json_decode($ret));
					$this->displayValidatePage();

				  }
				  catch (Exception $e) {
					 $this->Session->setFlash($e->getMessage(), 'flash_warning');
					$this->redirect($this->_url_error, false);
				  }
			}else{
				$customer_id = $requestData['stripeCustomer'];
				$card_id = $requestData['stripeCard'];
				try {
					if (!isset($card_id ))
					  throw new Exception("Erreur technique lors de votre paiement.");
					
					\Stripe\Customer::update(
					  $customer_id,
					  [
						'default_source' => $card_id,
					  ]
					);

					$ret = \Stripe\Charge::create(array("amount" => $price,
												"currency" => $devise,
												'customer' => $customer_id,
  												'source' => $card_id,
												"receipt_email" =>$cart["user"]["email"],
												"description" => $requestData['cardholdername']. ' ('.$cart["user"]["email"]. ') - '.$cart['id_cart']));


					  $id_order = $this->convertCartToOrder($cart['id_cart']);
						if (!$id_order){
							$this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
							$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
							return false;
						}
					 $this->validateOrder($id_order, json_decode($ret));
					$this->displayValidatePage();

				  }
				  catch (Exception $e) {
					 $this->Session->setFlash($e->getMessage(), 'flash_warning');
					$this->redirect($this->_url_error, false);
				  }
			}
		}

    }*/
	
	
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
        $cart_datas = $this->Session->read('StripeCart');//$this->Cart->getDatas($id_cart);
		 
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
		$this->Session->delete('StripeCart');
		 
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
	
	public function remove_card(){
		$this->autoRender = false;
		$this->layout = false;
			
			$requestData = $this->request->data;
			if(!isset($requestData)|| !isset($this->request->data['customer']))
                $this->jsonRender(array('return' => false));
			
			if($requestData['customer'] && $requestData['card']){
				require '../Lib/stripe/init.php';
				\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
				$payment_method = \Stripe\PaymentMethod::retrieve($requestData['card']);
				$payment_method->detach();
				
				
				/*$ret = \Stripe\Customer::deleteSource(
				  $requestData['customer'],
				  $requestData['card']
				);*/
				//var_dump($ret);
				$this->jsonRender(array('return' => true));
			}
	}
	
	public function admin_declarer_incident(){
		
		$params = $this->request->params;
		$id_order = $params['pass'][0];
		
		
		if(empty($id_order))
			 $this->redirect('/admin/paymentstripe?valid');
		
		
		$condition = array('Order.id'=>$id_order);
		
		$this->loadModel('Order');
		
		$order = $this->Order->find("first", array(
						'fields' => array('Order.*','stripe_logs.*','User.*'),
						'conditions' => $condition,
						'joins'     => array(
							array(

								'alias' => 'stripe_logs',
								'table' => 'order_stripetransactions',
								'type'  => 'inner',
								'conditions' => array('stripe_logs.cart_id = Order.cart_id')
							)
						),
						'group'         => 'Order.id',
						'order'        => 'Order.date_add DESC',
						'paramType' => 'querystring',
					)
				);
		
		$condition = array('Order.id >'=>$id_order,'Order.user_id'=>$order['Order']['user_id']);
		
		$order_next = $this->Order->find("first", array(
						'fields' => array('Order.*','stripe_logs.*','User.*'),
						'conditions' => $condition,
						'joins'     => array(
							array(

								'alias' => 'stripe_logs',
								'table' => 'order_stripetransactions',
								'type'  => 'inner',
								'conditions' => array('stripe_logs.cart_id = Order.cart_id')
							)
						),
						'group'         => 'Order.id',
						'order'        => 'Order.date_add asc',
						'paramType' => 'querystring',
					)
				);
		if($order_next){
			$condition = array('UserCreditLastHistory.users_id'=>$order['User']['id'], 'UserCreditLastHistory.date_start >=' => $order['Order']['date_add'], 'UserCreditLastHistory.date_start <' => $order_next['Order']['date_add']);
		}else{
			$condition = array('UserCreditLastHistory.users_id'=>$order['User']['id'], 'UserCreditLastHistory.date_start >=' => $order['Order']['date_add']);
		}
		
		
		$this->loadModel('UserCreditLastHistory');
		$comm = $this->UserCreditLastHistory->find("all", array(
						'fields' => array('UserCreditLastHistory.*'),
						'conditions' => $condition,
						'order'        => 'UserCreditLastHistory.date_start ASC',
						'paramType' => 'querystring',
					)
				);
		
		
		$condition = array('UserCreditLastHistory.users_id'=>$order['User']['id'], 'UserCreditLastHistory.date_start <' => $order['Order']['date_add']);
		$comm_old = $this->UserCreditLastHistory->find("all", array(
						'fields' => array('UserCreditLastHistory.*'),
						'conditions' => $condition,
						'order'        => 'UserCreditLastHistory.date_start DESC',
						'paramType' => 'querystring',
						'limit' => 15
					)
				);
		
        $this->set(array(
            'comm' => $comm,
			'comm_old' => $comm_old,
            'order' => $order
        ));
				

	}

}
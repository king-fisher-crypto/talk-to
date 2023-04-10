<?php
App::uses('PaymentController', 'Controller');

/*
DOC https://stripe.com/docs/stripe-js/elements/payment-request-button
//contact tech : Youssef Ben Othman <youssef@stripe.com>
pour tester 'amount_1950@gemelos.fr
*/
class PaymentsepaController extends PaymentController {
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

    protected $payment_mode = 'sepa';
    protected $payment_name = '';
    protected $cart_datas = array();
	protected $_render_index = '/Payment/Sepa/index';
	
    protected $_render_admin_index = '/Payment/Sepa/admin_index';
    protected $_render_validation = '/Payment/Sepa/validation';
	protected $_render_validation_gift = '/Gifts/validation';
    protected $_render_error = '/Payment/Sepa/error';
	protected $_url_validation = '/Payment/Sepa/validation?utm_novverride=1';
    protected $_url_error = '/Payment/Sepa/error?utm_novverride=1';
	protected $_url_form = '/paymentsepa/submit';

    /* On ne tente pas de charger le panier pour les actions listées dans ce tableau (retour de banque par ex) */
    protected $_validation_actions = array('validation','error_return');


    protected $cms_ids = array(
        'confirmation_paiement'     =>  436,
		'attente_paiement'          =>  435,  /* Propre à chaque mode de paiement !!!! */
    );




    public function beforeFilter()
    {
        $this->payment_name = __('Paiement Sepa');
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
		
		$this->set('holdname', $this->cart_datas['user']['firstname'].' '.$this->cart_datas['user']['lastname'] );
		$this->set('email', $this->cart_datas['user']['email']);
		
		$id_cart = $this->cart_datas['id_cart'];
			
			$this->loadModel('CartLoose');
			
			$cartLoose = $this->CartLoose->find('first', array(
                                'conditions' => array(
                                    'CartLoose.id_cart' => $id_cart,
                                ),
                                array('recursive' => -1)
        ));
		
		if($cartLoose){
			$this->CartLoose->id = $cartLoose['CartLoose']['id'];
			$this->CartLoose->save(array(
				'status'     =>      0,
			));
		}
		
		
		$this->payment_mode = 'sepa';
		parent::index();
    }
	
	public function submit()
    {
		if($this->request->is('post')){
			$requestData = $this->request->data;
			
			if($requestData['bankholdername'] === false || $requestData['email'] === false){
                $this->Session->setFlash(__('Veuillez remplir tous les champs.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            }
			
			$cart = $this->cart_datas;
			$devise = strtolower($cart["product"]["Country"]["devise_iso"]);
			if($devise != 'eur'){
                $this->Session->setFlash(__('Ce mode de paiement est disponible seulement en Euro.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            }
			
			//create source
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);
			
			$source = '';
			try {
				$source = \Stripe\Source::create([
							  "type" => "sepa_credit_transfer",
							  "currency" => "eur",
							  "owner" => [
								"name" => $requestData['bankholdername'],
								"email" => $requestData['email'],
							  ],
							]);
			
			 } catch (\Stripe\Error\Base $e) {
				$this->Session->setFlash(__('Votre demande n\'a pas été prise en compte. Merci de changer de mode de paiement.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
			}
			
			if(is_object($source) && isset($source->sepa_credit_transfer)){
				
				$cart = $this->cart_datas;
				$this->set('price', $cart['total_price'] );
				$this->set('devise', $cart['product']['Country']['devise'] );
				
				$this->set('bank', $source->sepa_credit_transfer->bank_name);
				$this->set('iban', $source->sepa_credit_transfer->iban);
				$this->set('bic', $source->sepa_credit_transfer->bic);
				
				$this->set('usr_email', $this->cart_datas['user']['email']);
				$this->set('cart_datas', $this->cart_datas);
				
				
				
				$id_order = $this->convertCartToOrder($cart['id_cart']);
				$this->saveLogOrder($source,$id_order);
				
				$firstname = $this->cart_datas['user']['firstname'];
				$lastname = $this->cart_datas['user']['lastname'];
				if(!$lastname) $lastname = ' ';
				
				/* On prépare les variables */
				$this->mail_vars = array(
					'cart_user_firstname'       =>   $firstname ,
					'cart_user_lastname'        =>    $lastname,
					'cart_total'        =>    $this->displayPrice($this->cart_datas['total_price']),
					'cart_order_ref'            =>    $this->cart_datas['cart_reference'],
					'sepa_bank'       =>   $source->sepa_credit_transfer->bank_name ,
					'sepa_iban'       =>   $source->sepa_credit_transfer->iban ,
					'sepa_bic'       =>   $source->sepa_credit_transfer->bic ,
				);

				$this->sendCmsTemplateByMail($this->cms_ids['attente_paiement']);
				
				 $contenu = $this->getCmsPage(437);
		
				$this->set('contenu',$contenu);
				
				
				$this->render($this->_render_validation);
				
			}else{
				$this->Session->setFlash(__('Erreur technique. Merci de changer de mode de paiement.'),'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
			}
			
		}else
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
    }
	
	 private function saveLogOrder($stripeLogs = 0,$id_order = 0)
    {
		$this->autoRender = false;
        if (!$stripeLogs)return false;
		
		
		$this->loadModel('OrderSepatransaction');
        $this->OrderSepatransaction->create();
		$cart = $this->cart_datas;
		
		$data = array();
		$data['cart_id'] = $cart['id_cart'] ;
		$data['order_id'] = $id_order ;
		$data['id'] = $stripeLogs->id;
		$data['payment_method'] = 'sepa_credit_transfer';
		$data['amount_received'] = $stripeLogs->receiver->amount_received;
		$data['amount_charged'] = $stripeLogs->receiver->amount_charged;
		$data['sepa_credit_transfer'] = json_encode($stripeLogs->sepa_credit_transfer);
		$data['owner'] = json_encode($stripeLogs->owner);
		$data['currency'] = $stripeLogs->currency;
		$data['date_add'] = date('Y-m-d H:i:s');
		
        $this->OrderSepatransaction->saveAll($data);
    }
	
	public function admin_index()
    {
		
		$query = $this->request->query;
		
		if (isset($query['valid'])){
            $order_valid = 1;
        }else{
            $order_valid = 0;
        }
		
		$condition = array();
		
		$condition['Order.payment_mode'] = $this->payment_mode;
		$condition['Order.valid'] = $order_valid;
		
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
            'fields' => array('Order.*','User.*','OrderSepatransaction.*'),
            'conditions' => $condition,
           'joins' => array(
                array(
                    'table' => 'order_sepatransactions',
                    'alias' => 'OrderSepatransaction',
                    'type'  => 'left',
                    'conditions' => array(
                        'OrderSepatransaction.order_id = Order.id',
                    )
                )
            ),
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
	
	public function admin_confirm($order_id=0)
    {
		
		//get source id
		$source_id = '';
		$this->loadModel('OrderSepatransaction');
		
		$ordersepa = $this->OrderSepatransaction->find('first',array(
							'conditions' => array('OrderSepatransaction.order_id' => $order_id),
							'recursive' => -1
							));
		
		if(!$ordersepa){
			$this->Session->setFlash('Virement SEPA introuvable', 'flash_warning');
        	$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
		}
    
		if($ordersepa['OrderSepatransaction']['id']){
		
			require '../Lib/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->_stripe_confs[$this->_stripe_mode]['private_key']);


			$source = '';
			
			try {
				$source = \Stripe\Source::retrieve($ordersepa['OrderSepatransaction']['id']);
			
			 } catch (\Stripe\Error\Base $e) {
				$this->Session->setFlash(__('Source de paiement non trouvé sur Stripe'), 'flash_warning');
        		$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
			}
			
			if(!is_object($source)){
				$this->Session->setFlash(__('Erreur technique'), 'flash_warning');
        		$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
			}
		
			$this->loadModel('Order');
		
			$order = $this->Order->find('first',array(
							'conditions' => array('Order.id' => $order_id),
							'recursive' => -1
							));
			
			$amount_receive = $source->receiver->amount_received;
			
			if($source->status != 'chargeable'){
				$this->Session->setFlash(__('Le transfert n\'est pas encore disponible.'), 'flash_warning');
        		$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
			}
			
			
			if($amount_receive < ($order['Order']['total'] * 100)){
				$this->Session->setFlash(__('Le montant transféré par le client est inférieur a l\'achat'), 'flash_warning');
        		$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
			}
			
			if($source->status == 'chargeable'){
				$amount = $order['Order']['total'] * 100;
				try {
					$charge = \Stripe\Charge::create([
							  'amount' => $amount,
							  'currency' => 'eur',
							  'source' => $ordersepa['OrderSepatransaction']['id'],
							  'transfer_group' => $order['Order']['cart_id']
							]);

				 } catch (\Stripe\Error\Base $e) {
					$this->Session->setFlash(__('Erreur impossible de charger le virement SEPA'), 'flash_warning');
					$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
				}
				
				
				if($charge->status == 'succeeded'){
					
					//update 
					$dbb_r = new DATABASE_CONFIG();
					$dbb_route = $dbb_r->default;
					$mysqli_conf = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
					$mysqli_conf->query("UPDATE order_sepatransactions set amount_received = '{$amount_receive}', amount_charged = '{$amount}', charge_id = '{$charge->id}'  where order_id = '{$order_id}'");
					
					$this->order_confirm($order_id);
					$this->Session->setFlash(__('Virement SEPA honoré, client crédité'), 'flash_success');
					$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
				}else{
					$this->Session->setFlash(__('Erreur durant la charge du virement SEPA'), 'flash_warning');
					$this->redirect(array('controller' => 'paymentsepa', 'action' => 'index', 'admin' => true), false);
				}
			}
			
		}
    }

}
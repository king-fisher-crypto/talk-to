<?php
App::uses('PaymentController', 'Controller');

class PaymentbankwireController extends PaymentController {
    protected $payment_mode = 'bankwire';
    protected $payment_name = '';
    protected $cart_datas = array();
    protected $_render_admin_index = '/Payment/Bankwire/admin_index';

    protected $cms_ids = array(
        'attente_paiement'          =>  172,  /* Propre à chaque mode de paiement !!!! */
        'confirmation_paiement'     =>  173
    );

    public function beforeFilter()
    {
        $this->payment_name = __('Virement bancaire');
        parent::beforeFilter();

        if (isset($this->request->params['admin'])){
            return true;
        }

        /* On créé la commande */
        $this->convertCartToOrder($this->Session->read('User.id_cart'));
    }
    protected function onConfirmPayment($order=false)
    {
        parent::onConfirmPayment($order);
    }
    public function index()
    {
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
		
		
		$firstname = $this->cart_datas['user']['firstname'];
		$lastname = $this->cart_datas['user']['lastname'];
		if(!$lastname) $lastname = ' ';
				
		/* On prépare les variables */
            $this->mail_vars = array(
				'cart_user_firstname'       =>   $firstname ,
            	'cart_user_lastname'        =>    $lastname,
				'cart_total'        =>    $this->displayPrice($this->cart_datas['total_price']),
				'cart_order_ref'            =>    $this->cart_datas['cart_reference'],
            );
		
        /* On envoi le mail d'attente de paiement */
        $this->sendCmsTemplateByMail($this->cms_ids['attente_paiement']);

        parent::index();
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

}
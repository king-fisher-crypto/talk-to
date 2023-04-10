<?php
App::uses('AppController', 'Controller');

class PaymentController extends AppController {
    public $uses = array('Order');
    public $components = array('Paginator');
    private $allowed_payment_modes = array('bankwire','hipay','paypal','coupon','stripe','bancontact','request','sepa');
    protected $payment_name = '';
    protected $payment_mode = '';
    protected $cart_datas = array();
    protected $mail_vars = array(
        'cart_total'            =>    '',
        'cart_user_firstname'   =>    '',
        'cart_user_lastname'    =>    '',
        'cart_user_mail'        =>    '',
        'cart_order_ref'        =>    '',
        'cart_order_product_credit' => '',
        'cart_order_voucher_credit' => '',
        'cart_order_voucher_code'   => '',
        'cart_order_total_credit'   =>    '',
        'cart_order_cond_credit_str'=>    ''
    );

    protected $cms_ids = array(
        'attente_paiement'          =>  172,
        'confirmation_paiement'     =>  173
    );

    protected $_validation_actions = array();

    protected $_render_admin_index = '/Payment/admin_index';


	public function cron_delete($id=0)
    {
        $this->autoRender = false;
        $this->loadModel('Order');
        $row = $this->Order->findById($id);

       		
		if($row && !$row['Order']['valid']){
			$this->Order->id = $id;
			$this->Order->delete();
		}

    }

    public function admin_delete($id=0)
    {
        $this->autoRender = false;
        $this->loadModel('Order');
        $row = $this->Order->findById($id);

        if (empty($row)){
            $this->Session->setFlash(__('Erreur, la commande n\'a pu être trouvée'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }

        if ($row['Order']['valid'] == 1){
            $this->Session->setFlash(__('Erreur, l\'action n\'est pas possible sur une commande validée'), 'flash_warning');
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }

        $this->Order->id = $id;
        $this->Order->delete();

        $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
    }
    public function order_confirm($order_id=0, &$errorMsg='', &$errorMsgTemplate='flash_warning')
    {
        if (!$order_id)return false;
        $this->loadModel('Order');
		$this->loadModel('Voucher');
		$this->loadModel('User');
		$this->loadModel('Loyalty');
		$this->loadModel('GiftOrder');
        $row = $this->Order->findById($order_id);
        if (empty($row)){
            $errorMsg = __('Erreur, la commande n\'a pu être trouvée');
            return false;
        }

        if ((int)$row['Order']['valid'] == 1){
            $errorMsg = __('Ce paiement a déjà été validé');
            return false;
        }

		//update programme fidélité
		$product_id = $row['Order']['product_id'];
		$id_cart = $row['Order']['cart_id'];
		
		$loyalty = $this->Loyalty->find('first', array(
                'conditions' => array('Loyalty.product_id' => $product_id),
                'recursive' => -1
            ));
		if(!empty($loyalty)){
			$rightToUse = true;
			if(!empty($row['Order']['voucher_code'])){
				 
				 //on récupère le coupon
                $voucher = $this->Voucher->find('first', array(
                    'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips'),
                    'conditions'    => array('Voucher.code' => $row['Order']['voucher_code']),
                    'recursive'     =>-1
                ));
				if($voucher['Voucher']['buy_only']) $rightToUse = false;	
			}
			
			if($rightToUse){
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$this->loadModel('LoyaltyUserBuy');	
				
				$current_pourcent = 0;
				$loyalty_user = $this->LoyaltyUserBuy->find('first', array(
					'conditions' => array('LoyaltyUserBuy.user_id' => $row['Order']['user_id']),
					'order' => array('id'=> 'desc'),
					'recursive' => -1
				));
				if($loyalty_user['LoyaltyUserBuy']['pourcent_current']) $current_pourcent = $loyalty_user['LoyaltyUserBuy']['pourcent_current'];
				$current_pourcent = $current_pourcent + $loyalty['Loyalty']['pourcent'];
				$is_loyalty_credit = false;
				if($current_pourcent >= 100){
					$current_pourcent = $current_pourcent - 100;
					$is_loyalty_credit = true;
				}
				$loyaltyData = array();
				$loyaltyData['LoyaltyUserBuy'] = array();
				$loyaltyData['LoyaltyUserBuy']['order_id'] = $row['Order']['id'];
				$loyaltyData['LoyaltyUserBuy']['loyalty_id'] = $loyalty['Loyalty']['id'];
				$loyaltyData['LoyaltyUserBuy']['IP'] = $ip_user;
				$loyaltyData['LoyaltyUserBuy']['date_add'] = date('Y-m-d H:i:s');
				$loyaltyData['LoyaltyUserBuy']['user_id'] = $row['Order']['user_id'];
				$loyaltyData['LoyaltyUserBuy']['pourcent'] = $loyalty['Loyalty']['pourcent'];
				$loyaltyData['LoyaltyUserBuy']['pourcent_current'] = $current_pourcent;
				
				$this->LoyaltyUserBuy->create();
				$this->LoyaltyUserBuy->save($loyaltyData);
				
				if($is_loyalty_credit){
					//$row['Order']['product_credits'] = $row['Order']['product_credits'] + 600;//ajout 10 minutes fidelité
					
					$this->loadModel('LoyaltyCredit');	
					$loyaltyCredit = array();
					$loyaltyCredit['LoyaltyCredit'] = array();
					$loyaltyCredit['LoyaltyCredit']['user_id'] = $row['Order']['user_id'];
					$loyaltyCredit['LoyaltyCredit']['name'] = $loyalty['Loyalty']['name'];
					$loyaltyCredit['LoyaltyCredit']['loyalty_id'] = $loyalty['Loyalty']['id'];
					$loyaltyCredit['LoyaltyCredit']['pourcent'] = $loyalty['Loyalty']['pourcent'];
					$loyaltyCredit['LoyaltyCredit']['IP'] = $ip_user;
					$loyaltyCredit['LoyaltyCredit']['date_add'] = date('Y-m-d H:i:s');
					$loyaltyCredit['LoyaltyCredit']['valid'] = 0;
					
					$this->LoyaltyCredit->create();
					$this->LoyaltyCredit->save($loyaltyCredit);
					
					 $customer = $this->User->find('first', array(
						'conditions'    => array('User.id' => $row['Order']['user_id']),
						'recursive'     => -1
					));
		
					$this->sendCmsTemplateByMail(241, $customer['User']['lang_id'], $customer['User']['email'], array());
				}
			}
		}
		
		
		
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
            //Date d'aujourd'hui
            $dateNow = date('Y-m-d H:i:s');
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
			
			$this->addUserCreditPrice($this->UserCredit->id);
			
			//carte cadeau
			$is_gift = false;
			if(!empty($row['Order']['voucher_code'])){
				$gift = $this->GiftOrder->find('first', array(
                    'conditions'    => array('GiftOrder.code' => $row['Order']['voucher_code'], 'GiftOrder.valid >=' => 1, 'GiftOrder.valid <=' => 2),
                    'recursive'     =>-1
                ));
				if($gift){
					$is_gift = true;
					$this->GiftOrder->id = $gift['GiftOrder']['id'];
					$this->GiftOrder->saveField('beneficiary_id', $row['Order']['user_id']); 
					$this->GiftOrder->saveField('date_use', date('Y-m-d H:i:s')); 
					$sold = (float)$row['Order']['product_price'] - (float)$gift['GiftOrder']['amount'];
					if($sold > 0){
						$this->GiftOrder->saveField('valid', 2); 
						$this->GiftOrder->saveField('sold', $sold);
					}else{
						$this->GiftOrder->saveField('sold', 0); 
						$this->GiftOrder->saveField('valid', 3); 
					}
					if($row['Order']['is_new']){
						$this->loadModel('User');
						$this->User->id = $row['Order']['user_id'];
						$this->User->saveField('source', 'carte cadeau'); 
					}
				}
			}
			
            //On save l'historique coupon, s'il y a eu un coupon
            if(!empty($row['Order']['voucher_code']) && !$is_gift){
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

            /* On valide la commande */
            $this->Order->id = $order_id;
            $this->Order->saveField('valid', 1);
			
			
			//save total_euros
			if($row['Order']['payment_mode'] == 'stripe' && $row['Order']['currency'] == '€'){
				$this->Order->saveField('total_euros', $row['Order']['total']);
			}
			
			if($row['Order']['payment_mode'] == 'sepa' && $row['Order']['currency'] == '€'){
				$this->Order->saveField('total_euros', $row['Order']['total']);
			}
			
			
			if($row['Order']['payment_mode'] == 'paypal' && $row['Order']['currency'] == '€'){
				$this->Order->saveField('total_euros', $row['Order']['total']);
			}
			
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
					'status'     =>      1,
				));
			}
		//on bloc IP si utilisation = 1 // 
		
		
		 $customer = $this->User->find('first', array(
            'conditions'    => array('User.id' => $row['Order']['user_id']),
            'recursive'     => -1
        ));
		
		if(!$is_gift){
			$voucher = $this->Voucher->find('first', array(
						'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips'),
						'conditions'    => array('Voucher.code' => $row['Order']['voucher_code']),
						'recursive'     =>-1
					));

			if($voucher)
				$rightToUse = $this->Voucher->rightToUse($voucher["Voucher"], $customer['User']['personal_code'], $row['Order']['user_id'], $row['Order']['product_id']);
			else
				$rightToUse = 0;
			if($voucher && !$rightToUse){
				$this->loadModel('UserIp');
				$user_ip = $this->UserIp->find('first', array(
					'conditions'    => array('UserIp.user_id' => $row['Order']['user_id']),
					'order'     => 'date_conn DESC',
					'recursive'     => -1
				));
				if(!isset($saveData['code']))$saveData['code'] = '';
				if(!isset($saveData['title']))$saveData['title'] = '';
				$saveData = $voucher['Voucher'];
				$saveData['ips'] .= $user_ip['UserIp']['IP'].',';
				$saveData['ips'] = "'".addslashes($saveData['ips'])."'";
				$saveData['code'] = "'".addslashes($saveData['code'])."'";
				$saveData['title'] = "'".addslashes($saveData['title'])."'";
				$this->Voucher->updateAll($saveData,array('code' => $row['Order']['voucher_code']));
			}
		}
            /* Hook confirmation commande */
            $this->onConfirmPayment($row);

            $errorMsg = 'Le paiement a été validé et le compte crédité';
            $errorMsgTemplate = 'flash_success';
        }

        return true;
    }
    public function admin_confirm($order_id=0)
    {
        $this->autoRender = false;

        if (!$this->order_confirm($order_id, $errorMsg, $errorMsgTemplate)){
            $this->Session->setFlash($errorMsg, $errorMsgTemplate);
            $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
        }else{
            $this->Session->setFlash($errorMsg, $errorMsgTemplate);

        }
        $this->redirect(array('controller' => $this->request->controller, 'action' => 'index', 'admin' => true), false);
    }
    protected function onConfirmPayment($order=false)
    {
        if (!$order)return false;

        if (!empty($order['Order']['voucher_code']))
            $condCreditStr = (int)$order['Order']['product_credits'].' (+ '.(int)$order['Order']['voucher_credits'].')';
        else $condCreditStr = (int)$order['Order']['product_credits'];
		
		$this->loadModel('Product');
		
		$product_id = $order['Order']['product_id'];
		
		$product = $this->Product->find('first',array(
					'fields' => array('ProductLang.name', 'ProductLang.description'),
					'conditions' => array(
						'Product.id' => $product_id,
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
				));
		
		

        /* On prépare les variables */
            $this->mail_vars = array(
                'cart_total'                =>    $this->displayPrice($order['Order']['total']),
                'cart_user_firstname'       =>    $order['User']['firstname'],
                'cart_user_lastname'        =>    $order['User']['lastname'],
                'cart_user_mail'            =>    $order['User']['email'],
                'cart_order_ref'            =>    $order['Order']['reference'],
                'cart_order_product_credit' =>    $order['Order']['product_credits'],
                'cart_order_voucher_credit' =>    $order['Order']['voucher_credits'],
                'cart_order_voucher_code'   =>    $order['Order']['voucher_code'],
                'cart_order_total_credit'   =>    (int)$order['Order']['product_credits']+(int)$order['Order']['voucher_credits'],
                'cart_order_cond_credit_str'=>    $condCreditStr,
				'cart_order_product_description' =>  $product['ProductLang']['description'],
            );


        /* Envoi du mail de confirmation de paiement */
            $this->sendCmsTemplateByMail($this->cms_ids['confirmation_paiement'], $order['Order']['lang_id'], $order['User']['email']);
    }
    public function admin_index()
    {
        $this->loadModel('Order');
        $this->Paginator->settings = array(

            'conditions' => array(
                'Order.payment_mode' => $this->payment_mode,
            ),
            'order'        => 'Order.date_add DESC',
            'paramType' => 'querystring',
            'limit' => 15
        );

        $valid = (isset($this->request->query['valid']))?1:0;
        $this->Paginator->settings['conditions']['Order.valid'] = $valid;

        $rows = $this->Paginator->paginate($this->UserCreditHistory);


        $this->set(array(
            'page_title' => $this->payment_name,
            'orders' => $rows
        ));
        $this->render($this->_render_admin_index);
    }


    public function beforeFilter()
    {
        parent::beforeFilter();

        if (in_array($this->request->params['action'], $this->_validation_actions)){
            /* Si on est dans une action de validation, on s'arrête là et on ne traite pas la validation panier */
            return true;
        }
		if($this->Session->read('StripeCart')){
			return true;
		}
        /* Metas */
        if (empty($this->payment_name))
            $this->payment_name = $this->payment_mode;
        $this->site_vars['meta_title']          = __('Réglement par').' '.$this->payment_name;

        if (isset($this->request->params['admin'])){
            return true;
        }
        /* On charge les données du panier */
            $this->getDatasFromCart();

    }
    public function beforeRender()
    {
        parent::beforeRender();
        /* On passe les données à la vue */
        $this->set('cart', $this->cart_datas);

    }
    public function index()
    {
		$this->loadModel('Order');
		$this->loadModel('User');
		$cart = $this->cart_datas;
		$user_id = $cart['user']["id"];
		
		/* FLAG is new */
		$orders = $this->Order->find('first', array(
                'conditions' => array('Order.user_id' => $user_id, 'Order.valid' => 1),
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
		$days_between = ceil(abs($end - $start) / 86400)-1;
		$this->set('delay_payment', $days_between);
		
        /* Chemin de vue personnalisé */
            $this->render('/Payment/'.ucfirst(strtolower($this->payment_mode)).'/index');
    }


    

    protected function convertCartToOrder($cart_id=0, $override_datas=array())
    {
        if (!$cart_id)return false;
        $cart = $this->Cart->getDatas($cart_id);

        /* Une commande de ce panier existe déjà ? On Sort */
            $tmp = $this->Order->findByCartId($cart_id);
            if (!empty($tmp))return false;

        $this->Order->create();
        $config = $this->Session->read('Config');

        $uniqReference = $this->getUniqReference();
        $this->cart_datas['cart_reference'] = $uniqReference;
		
		if ( isset($_SERVER["HTTP_CLIENT_IP"] )&& strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown") )
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown") )
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		{
			$ip = getenv("REMOTE_ADDR");
		}
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$ip = "unknown";
		}
		//getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
		
		$is_new_order = 0;
		$lastOrderCheck = $this->Order->find('first', array(
					'conditions'    => array('Order.user_id' => $cart['user']['id'], 'Order.valid' => 1),
					'recursive'     => -1
		));
		$this->loadModel('User');
		$client = $this->User->find('first', array(
					'conditions'    => array('User.id' => $cart['user']['id']),
					'recursive'     => -1
		));
		
		if(!$lastOrderCheck &&!$client['User']['is_come_back'])$is_new_order = 1;
		
		
		if($cart['voucher']["code"]){
			$this->loadModel('Voucher');
			$rightToUse = $this->Voucher->rightToUse($cart['voucher']["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $cart['product']['Product']['id']);
			if(!$rightToUse){
				$cart['voucher']['code'] = '';
				$cart['voucher']['title'] = '';
				$cart['reduction_mode'] = '';
				$cart['voucher']['credit'] = '';
				$cart['voucher']['amount'] = '';
				$cart['voucher']['percent'] = '';
			}
		}
		
		
        $datas = array(
            'cart_id'           => $cart_id,
            'reference'         => $uniqReference,
            'user_id'           => $cart['user']['id'],
            'lang_id'           => $cart['lang_id'],
            'country_id'        => $cart['product']['Country']['id'],
            'product_id'        => $cart['product']['Product']['id'],
			'product_name'      => $cart['product']['ProductLang']['0']['name'],
            'product_description'      => $cart['product']['ProductLang']['0']['description'],
            'product_credits'   => $cart['product']['Product']['credits'],
            'product_price'     => $cart['product']['Product']['tarif'],
            'voucher_code'      => isset($cart['voucher']['code'])?$cart['voucher']['code']:'',
            'voucher_name'      => isset($cart['voucher']['title'])?$cart['voucher']['title']:'',
            'voucher_mode'      => isset($cart['reduction_mode'])?$cart['reduction_mode']:'',
            'voucher_credits'   => isset($cart['voucher']['credit'])?$cart['voucher']['credit']:'',
            'voucher_amount'    => isset($cart['voucher']['amount'])?$cart['voucher']['amount']:'',
            'voucher_percent'    => isset($cart['voucher']['percent'])?$cart['voucher']['percent']:'',
            'payment_mode'      => $this->payment_mode,
            'currency'          => $config['devise'],
            'total'             => $cart['total_price'],
            'valid'             => 0,
			'IP'                => $ip,
			'cookie_id'         => session_id(),
			'is_new'			=> $is_new_order
        );

        if (!empty($override_datas) && is_array($override_datas)){
            $datas = array_merge($datas, $override_datas);
        }
        $this->Order->saveAll($datas);
        $this->_last_order_id = 0;

        if (!$this->Order->id){
            $this->Session->setFlash(__('Erreur de création de la commande'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

        $this->_last_order_id = $this->Order->id;

        /* Variables de mail */
        $totalCredit = (int)$cart['product']['Product']['credits']+
            (!empty($cart['voucher'])?(int)$cart['voucher']['credit']:0);
        if (!empty($cart['voucher']))
            $condCreditStr = (int)$cart['product']['Product']['credits'].' (+ '.(int)$cart['voucher']['credit'].')';
        else $condCreditStr = (int)$cart['product']['Product']['credits'];

        $this->mail_vars = array(
            'cart_total'                =>    $this->displayPrice($this->cart_datas['total_price']),
            'cart_user_firstname'       =>    $cart['user']['firstname'],
            'cart_user_lastname'        =>    $cart['user']['lastname'],
            'cart_user_mail'            =>    $cart['user']['email'],
            'cart_order_ref'            =>    $uniqReference,
            'cart_order_product_credit' =>    $cart['product']['Product']['credits'],
            'cart_order_voucher_credit' =>    (!empty($cart['voucher'])?(int)$cart['voucher']['credit']:0),
            'cart_order_voucher_code'   =>    (!empty($cart['voucher'])?(int)$cart['voucher']['code']:0),
            'cart_order_total_credit'   =>    $totalCredit,
            'cart_order_cond_credit_str'=>    $condCreditStr
        );
        unset($cart);
        $this->clearSessionCart();

        return $this->Order->id;
    }
    protected function getDatasFromCart()
    {
		
        if (empty($this->payment_mode)){
            $this->Session->setFlash(__('Mode de paiement non autorisé'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

        if (!in_array($this->payment_mode, $this->allowed_payment_modes)){
            $this->Session->setFlash(__('Mode de paiement inconnu'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }

        /*if (!$this->Session->check('User.id_cart')){
            $this->Session->setFlash(__('Erreur de panier.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }*/
        $id_cart = $this->Session->read('User.id_cart');
		$id_gift = $this->Session->read('GiftOrderId');
        if (!$id_cart && !$id_gift){
            $this->Session->setFlash(__('Votre tentative de paiement a échoué, veuillez essayer à nouveau.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
        }
		
		if($id_cart){
        /* On charge les données du panier */
            $this->loadModel('Cart');
            $cart = $this->Cart->getDatas($id_cart);
        /* Une commande existe-t-elle avec ce panier ? */
            $rows = $this->Order->find("count", array(
                'conditions' => array('Order.cart_id' => $id_cart)
            ));

            if ((int)$rows > 0){
                $this->Session->setFlash(__('Une commande a déjà été passée avec ce panier'), 'flash_warning');
                $this->clearSessionCart();
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
                return false;
            }
			/* Ok on a récupéré les infos */
            $this->cart_datas = $cart;
		}
		
		if($id_gift){
			$cart = array();
			$this->cart_datas = $cart;
		}
        
		
    }
    protected function displayPrice($price=0, $devise=0)
    {
        if (!$devise)$devise = $this->Session->read('Config.devise');

        switch ($devise){
            case '$':
                return $devise.number_format($price, 2, ',', ' ');
                break;
            default:
                return number_format($price, 2, ',', ' ').' '.$devise;
                break;
        }



    }
    public function getHookPayment(){}
	public function getHookPaymentGift(){}
    protected function getPaymentLocale()
    {
        $locale = $this->Session->check('Config.lc_time')?$this->Session->read('Config.lc_time'):false;
        if ($locale){ $tmp = explode(".", $locale); $locale = isset($tmp['0'])?$tmp['0']:$tmp;}else{
            $locale = 'en_GB';
        }
        return $locale;
    }
    protected function getPaymentLogoUrl()
    {
        $domain_id = $this->Session->check('Config.id_domain')?$this->Session->read('Config.id_domain'):false;
        if (isset($this->logos_ssl_hosting[$domain_id]))
            return $this->logos_ssl_hosting[$domain_id];

        return false;
        $url_site = Router::url('/', true);
        $path_logo = Configure::read('Site.pathLogo');
        $domain_id = $this->Session->check('Config.id_domain')?$this->Session->read('Config.id_domain'):false;
        if (!$path_logo || !$domain_id)return false;
        $logo_url = $url_site.$path_logo.'/'.$domain_id.'_logo.jpg';
        return $logo_url;
    }
	
	protected function validateGift($giftorder_id=0)
    {
		$this->loadModel('GiftOrder');
		$this->loadModel('User');
		$this->loadModel('Domain');
		$this->loadModel('Voucher');
		
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $giftorder_id),
					'recursive' => -1,
				));
		$user_order = $this->User->find('first', array(
					'conditions' => array('User.id' => $gift_order['GiftOrder']['user_id']),
					'recursive' => -1,
				));
		if(!$gift_order['GiftOrder']['valid']){
			$this->GiftOrder->id = $giftorder_id;
			$this->GiftOrder->saveField('valid', 1); 
			$this->Session->write('GiftOrderId', '');
			$this->GiftOrder->saveField('date_validity', date('Y-m-d H:i:s', strtotime(' + 365 day'))); 

			$characts = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	
			$characts .= '1234567890'; 
			$code = ''; 

			for($i=0;$i < 8;$i++) 
			{ 
				$code .= $characts[ rand() % strlen($characts) ]; 
			}
			$this->GiftOrder->saveField('code', $code); 

			$hash_buyer = rtrim(strtr(base64_encode('e-carte-buyer-'.$gift_order['GiftOrder']['id']), '+_', '-|'), '='); 
			$this->GiftOrder->saveField('hash_buyer', $hash_buyer);
			$hash_benef = rtrim(strtr(base64_encode('e-carte-benef-'.$gift_order['GiftOrder']['id']), '+_', '-|'), '='); 
			$this->GiftOrder->saveField('hash_beneficiary', $hash_benef);

			$conditions = array(
								'Domain.id' => $user_order['User']['domain_id'],
							);
			

			$domain = $this->Domain->find('first',array('conditions' => $conditions));
			if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';

			if($gift_order['GiftOrder']['send_who'] && ($gift_order['GiftOrder']['send_date'] == '0000-00-00 00:00:00' || $gift_order['GiftOrder']['send_date'] == date('Y-m-d 00:00:00') )){//envoi mail beneficiaire

				$url = 'https://'.$domain['Domain']['domain'].'/gifts/show-'.$hash_benef;

				 $this->mail_vars = array(
					'beneficiary'       =>    $gift_order['GiftOrder']['beneficiary_firstname'].' '.$gift_order['GiftOrder']['beneficiary_lastname'],
					'customer'       =>    $user_order['User']['firstname'],
					'url_carte_cadeau'       =>    $url,
				);

				if($this->sendCmsTemplateByMail(414, $user_order['User']['lang_id'], $gift_order['GiftOrder']['beneficiary_email']))
					$this->GiftOrder->saveField('is_send', 1); 

			}

			$url = 'https://'.$domain['Domain']['domain'].'/gifts/show-'.$hash_buyer;

			$this->mail_vars = array(
					'beneficiary'       =>    $gift_order['GiftOrder']['beneficiary_firstname'].' '.$gift_order['GiftOrder']['beneficiary_lastname'],
					'customer'       =>    $user_order['User']['firstname'],
					'url_carte_cadeau'       =>    $url,
				);

			$this->sendCmsTemplateByMail(413, $user_order['User']['lang_id'], $user_order['User']['email']);
			
			
			//offre 5 min
			/*$characts = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	
						$characts .= '1234567890'; 
						$code = ''; 

						for($i=0;$i < 8;$i++) 
						{ 
							$code .= $characts[ rand() % strlen($characts) ]; 
						}
			
			$this->Voucher->create();
								$requestVoucher = array();
								$dt = new DateTime(date('Y-m-d H:i:s'));
								$dt->modify('+ 2 day');
								$requestVoucher["code"] = $code; 
								$requestVoucher["title"] = 'CARTE_CADEAU_PROMO_5_MIN'; 
								$requestVoucher["label_fr"] = 'Carte cadeau : 5mn offertes'; 
								$requestVoucher["label_be"] = 'Carte cadeau : 5mn offertes'; 
								$requestVoucher["label_ch"] = 'Carte cadeau : 5mn offertes'; 
								$requestVoucher["label_lu"] = 'Carte cadeau : 5mn offertes'; 
								$requestVoucher["label_ca"] = 'Carte cadeau : 5mn offertes'; 
								$requestVoucher["validity_start"] = date('Y-m-d H:i:s'); 
								$requestVoucher["validity_end"] = $dt->format('Y-m-d H:i:s'); 
								$requestVoucher["credit"] = '300'; 
								$requestVoucher["number_use"] = '1'; 
								$requestVoucher["number_use_by_user"] = '1'; 
								$requestVoucher["active"] = '1'; 
								$requestVoucher["show"] = '1'; 
								$requestVoucher["population"] = $user_order['User']['personal_code']; 
								$requestVoucher["product_ids"] = 'all'; 
								$requestVoucher["buy_only"] = '0'; 
								$requestVoucher["country_ids"] = 'all';
								$this->Voucher->save($requestVoucher);
			$this->mail_vars = array(
					'customer'       =>    $user_order['User']['firstname'],
					'code'       =>    $code,
				);
			$this->sendCmsTemplateByMail(417, $user_order['User']['lang_id'], $user_order['User']['email']);*/
		}
		
	}
}
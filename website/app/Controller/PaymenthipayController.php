<?php
App::uses('PaymentController', 'Controller');

class PaymenthipayController extends PaymentController {
    protected $_hipay_mode = 'prod'; /* Dev ou prod */
    protected $_hipay_confs = array(
        'dev'  =>  array(
            'form_url'           => 'https://test-payment.hipay.com/index/form/',
            'user_account_id'    => 0,
            'website_id'         => 0,
            'private_key'        => ''
        ),
        'prod' => array(
            'form_url'           => 'https://payment.hipay.com/index/form/',
            'user_account_id'    => 0,
            'website_id'         => 0,
            'private_key'        => ''
        )
    );

    protected $payment_mode = 'hipay';
    protected $payment_name = '';
    protected $cart_datas = array();
    protected $_render_admin_index = '/Payment/Hipay/admin_index';
    protected $_render_validation = '/Payment/Hipay/validation';
	protected $_render_validation_gift = '/Gifts/validation';
    protected $_render_error = '/Payment/Hipay/error';
	protected $_url_validation = '/Payment/Hipay/validation?utm_novverride=1';
    protected $_url_error = '/Payment/Hipay/error?utm_novverride=1';

    /* On ne tente pas de charger le panier pour les actions listées dans ce tableau (retour de banque par ex) */
    protected $_validation_actions = array('validation','confirmation','confirmation_gift','confirmationdev','error_return');


    protected $cms_ids = array(
        'attente_paiement'          =>  172,  /* Propre à chaque mode de paiement !!!! */
        'confirmation_paiement'     =>  216
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
				$condition['hipaylogs.transaction'] = $this->request->data['Payment']['numero'];
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
				$condition['hipaylogs.transaction'] = $this->Session->read('Payment_numero');
			}
		}
		//var_dump($condition);
		$limit = 15;
		if($this->Session->check('Date')){
                $condition = array_merge($condition, array(
                    'Order.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'Order.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
                ));
			$limit = 999999;
            }
		
        $this->loadModel('Order');
        $this->Paginator->settings = array(
            'fields' => array('Order.*','User.*','hipaylogs.*'),
            'conditions' => $condition,
            'joins'     => array(
                array(

                    'alias' => 'hipaylogs',
                    'table' => 'order_hipaytransactions',
                    'type'  => 'inner',
                    'conditions' => array('hipaylogs.cart_id = Order.cart_id')
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
        $this->payment_name = __('Paiement Carte Bleue HiPay');
        $this->Auth->allow('confirmation','confirmationdev','admin_declarer_valide','admin_declarer_impaye','admin_declarer_rembourse','getHookPayment',
			 'getHookPaymentGift','confirmation_gift');
		
        parent::beforeFilter();
    }

    public function index()
    {

    }
    private function getHipayConf()
    {
        $this->loadModel('OrderHipayconf');
        if ($this->_hipay_mode !== 'prod')$this->_hipay_mode = 'dev';
        $fields_prefix = ($this->_hipay_mode=='prod')?'prod_':'dev_';

        $dyn_fields = array('account_id','website_id','private_key');
        $fields = array();
        foreach ($dyn_fields AS $f){
            $fields[] = $fields_prefix.$f.' AS '.$f;
        }

        $conditions = array(
            'currency' => $this->Session->read('Config.devise_iso')
        );

        $conf = $this->OrderHipayconf->find("first", array(
            'fields'     => array_merge(array('currency'), $fields),
            'conditions' => $conditions
        ));
        if (empty($conf))return false;
        $conf = isset($conf['OrderHipayconf'])?$conf['OrderHipayconf']:false;
        if (!$conf)return false;


        $this->_hipay_confs[$this->_hipay_mode]['user_account_id'] = $conf['account_id'];
        $this->_hipay_confs[$this->_hipay_mode]['website_id'] = $conf['website_id'];
        $this->_hipay_confs[$this->_hipay_mode]['private_key'] = !empty($conf['private_key'])?$conf['private_key']:false;

        if (!$this->_hipay_confs[$this->_hipay_mode]['private_key'])return false;
        return isset($this->_hipay_confs[$this->_hipay_mode])?$this->_hipay_confs[$this->_hipay_mode]:false;
    }
    private function getOrderXmlFromCart()
    {
        $conf = $this->getHipayConf();
        if (!$conf)return false;

        $locale = $this->getPaymentLocale();

        $url_site = Router::url('/', true);
        $logo_url = $this->getPaymentLogoUrl();

        $this->Order->id = $this->_last_order_id;
        $this->Order->recursive = -1;
        $order = $this->Order->read();

        $this->loadModel('Domain');
        $res = $this->Domain->find("first", array('fields' => array('iso'),'conditions' => array('id' => $this->Session->read('Config.id_domain'))));
        $iso = isset($res['Domain']['iso'])?$res['Domain']['iso']:false;
        if ($iso){
            $tmp = explode("_", $locale);
            $tmp['1'] = strtoupper($iso);
            $locale = implode("_", $tmp);
          
        }
		
		$product_name = $this->cart_datas['product']['ProductLang']['0']['name'];
		if(is_array($this->cart_datas['voucher'])){
			$product_name .= ' + '.$this->cart_datas['voucher']['title'];	
		}
$locale = 'en_GB';
        $token = $this->getTokenForHiPay($this->cart_datas['id_cart'], $this->cart_datas['user']['id']);
		if(!$this->cart_datas['product']['Country']['devise_iso'])$this->cart_datas['product']['Country']['devise_iso'] = 'EUR';
        $xml = "<?xml version='1.0' encoding='utf-8' ?>
<order>
    <userAccountId>".$conf['user_account_id']."</userAccountId>
    <currency>".$this->cart_datas['product']['Country']['devise_iso']."</currency>
    <locale>".$locale."</locale>
    <label>".$product_name."</label>
    <ageGroup>ALL</ageGroup>
    <categoryId>625</categoryId>
    <urlAcquital><![CDATA[".$url_site."paymenthipay/confirmation?token=".$token."]]></urlAcquital>
    <urlOk><![CDATA[".$url_site."paymenthipay/validation?utm_novverride=1]]></urlOk>
    <urlKo><![CDATA[".$url_site."paymenthipay/error_return?utm_novverride=1]]></urlKo>
    <urlCancel><![CDATA[".$url_site."accounts/buycredits?utm_novverride=1]]></urlCancel>
    <urlInstall><![CDATA[".$url_site."accounts/buycredits?utm_novverride=1]]></urlInstall>
    <urlLogo><![CDATA[".$logo_url."]]></urlLogo>
    <issuerAccountLogin><![CDATA[".$this->cart_datas['user']['email']."]]></issuerAccountLogin>
    <locale><![CDATA[".$locale."]]></locale>
    <reference><![CDATA[".$this->cart_datas['id_cart']."]]></reference>
    <!-- subscription description -->
    <items>
        <item id='1'>
            <name><![CDATA[".$this->cart_datas['product']['ProductLang']['0']['name']."]]></name>
            <categoryId>625</categoryId>
            <infos><![CDATA[".$this->cart_datas['product']['ProductLang']['0']['description']."]]></infos>
            <amount>".str_replace(",",".",(float)$this->cart_datas['total_price'])."</amount>
            <quantity>1</quantity>
            <reference>".$this->cart_datas['product']['Product']['id']."</reference>
        </item>
    </items>
</order>";
        $xml = trim($xml);
        return $xml;
    }
	private function getOrderXmlFromGift()
    {
        $conf = $this->getHipayConf();
        if (!$conf)return false;

        $locale = $this->getPaymentLocale();

        $url_site = Router::url('/', true);
        $logo_url = $this->getPaymentLogoUrl();
		$this->loadModel('GiftOrder');
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $this->Session->read('GiftOrderId')),
					'recursive' => -1,
				));
		$this->loadModel('Gift');
		$gift = $this->Gift->find('first', array(
					'conditions' => array('Gift.id' => $gift_order['GiftOrder']['gift_id']),
					'recursive' => -1,
				));

        $this->loadModel('Domain');
        $res = $this->Domain->find("first", array('fields' => array('iso'),'conditions' => array('id' => $this->Session->read('Config.id_domain'))));
        $iso = isset($res['Domain']['iso'])?$res['Domain']['iso']:false;
        if ($iso){
            $tmp = explode("_", $locale);
            $tmp['1'] = strtoupper($iso);
            $locale = implode("_", $tmp);
          
        }
		
		$product_name = $gift['Gift']['name'].' '.$gift['Gift']['amount'];

        $token = $this->getTokenForHiPay($gift_order['GiftOrder']['id'], $this->Auth->user('id'));
        $xml = "<?xml version='1.0' encoding='utf-8' ?>
<order>
    <userAccountId>".$conf['user_account_id']."</userAccountId>
    <currency>".$gift_order['GiftOrder']['devise']."</currency>
    <locale>".$locale."</locale>
    <label>".$product_name."</label>
    <ageGroup>ALL</ageGroup>
    <categoryId>625</categoryId>
    <urlAcquital><![CDATA[".$url_site."paymenthipay/confirmation_gift?token=".$token."]]></urlAcquital>
    <urlOk><![CDATA[".$url_site."paymenthipay/validation_gift?utm_novverride=1]]></urlOk>
    <urlKo><![CDATA[".$url_site."paymenthipay/error_return?utm_novverride=1]]></urlKo>
    <urlCancel><![CDATA[".$url_site."gifts/buy?utm_novverride=1]]></urlCancel>
    <urlInstall><![CDATA[".$url_site."gifts/buy?utm_novverride=1]]></urlInstall>
    <urlLogo><![CDATA[".$logo_url."]]></urlLogo>
    <issuerAccountLogin><![CDATA[".$this->Auth->user('email')."]]></issuerAccountLogin>
    <locale><![CDATA[".$locale."]]></locale>
    <reference><![CDATA[".$gift_order['GiftOrder']['id']."]]></reference>
    <!-- subscription description -->
    <items>
        <item id='1'>
            <name><![CDATA[".$gift['Gift']['name'].' '.$gift_order['GiftOrder']['amount']."]]></name>
            <categoryId>625</categoryId>
            <infos><![CDATA[]]></infos>
            <amount>".str_replace(",",".",(float)$gift_order['GiftOrder']['amount'])."</amount>
            <quantity>1</quantity>
            <reference>".$gift_order['GiftOrder']['gift_id']."</reference>
        </item>
    </items>
</order>";
        $xml = trim($xml);
        return $xml;
    }
    private function getTokenForHiPay($cart_id=false, $user_id=0)
    {
        if (!$cart_id || !$user_id){
            /* On retourne une valeur fausse */
            $token = time() * rand(111,999);
        }else{
            $token = Configure::read('Security.salt').$cart_id.$user_id;
        }

        return base64_encode($token);
    }
    public function getHookPayment()
    {
        $conf = $this->getHipayConf();
        if (!$conf)return false;
        $xml     = $this->getOrderXmlFromCart();
        $crypted = base64_encode($this->_hipay_encrypt(base64_encode($xml), $conf['private_key']));
        $sign    = base64_encode($this->_hipay_sign(base64_encode($xml), $conf['private_key']));
        $html = '<form action="'.$conf['form_url'].'" method="post" id="hipay_form" style="display:none">
            <input type="hidden" name="mode" value="MODE_C" />
            <input type="hidden" name="website_id" value="'.$conf['website_id'].'" />
            <input type="hidden" name="sign" value="'.$sign.'" />
            <input type="hidden" name="data" value="'.$crypted.'" />
            <input type="image" name="send" src="https://test-payment.hipay.com/images/bt_payment3.gif" />
        </form>';
		
		if($this->request->is('ajax')){
			$this->jsonRender(array('return' => true, 'html' => $html));
		}else{
			return $html;
		}
        
    }

	 public function getHookPaymentGift()
    {
		//save client
		$this->loadModel('GiftOrder');
		$this->GiftOrder->id = $this->Session->read('GiftOrderId');
		$this->GiftOrder->saveField('user_id', $this->Auth->user('id'));
		 
		
        $conf = $this->getHipayConf();
		  
        if (!$conf)return false;
        $xml     = $this->getOrderXmlFromGift();
		
        $crypted = base64_encode($this->_hipay_encrypt(base64_encode($xml), $conf['private_key']));
        $sign    = base64_encode($this->_hipay_sign(base64_encode($xml), $conf['private_key']));
        $html = '<form action="'.$conf['form_url'].'" method="post" id="hipay_form" style="display:none">
            <input type="hidden" name="mode" value="MODE_C" />
            <input type="hidden" name="website_id" value="'.$conf['website_id'].'" />
            <input type="hidden" name="sign" value="'.$sign.'" />
            <input type="hidden" name="data" value="'.$crypted.'" />
            <input type="image" name="send" src="https://test-payment.hipay.com/images/bt_payment3.gif" />
        </form>';
		$this->jsonRender(array('return' => true, 'html' => $html));
        
    }


    private function _hipay_encrypt($source, $privateKey)
    {
        $maxLength = 117;
        $output = "";
        while ($source){
            $slice = substr($source, 0, $maxLength);
            $source = substr($source, $maxLength);
            openssl_private_encrypt($slice, $encrypted, $privateKey);
            $output .= $encrypted;
        }
        return $output;
    }
    private function _hipay_sign($data, $privateKey)
    {
        $output = "";
        openssl_private_encrypt(sha1($data), $output, $privateKey);

        return $output;
    }
    public function confirmationdev()
    {
        $this->confirmation(file_get_contents(Configure::read('Site.pathLogHipay').date("Y")."/".date("m")."/".date("d")."/103921.xml"));

    }
    public function confirmation()
    {
        $this->autoRender = false;

		
        if (!isset($this->request->query['token'])) return;
        $token = $this->request->query['token'];
		
		if (!array_key_exists('xml', $this->request->data))  return;
        $xml = stripslashes($this->request->data['xml']);
		
        if (!$token || !$xml)return false;

        if ($this->analyzeNotificationXML($xml, $operation, $status, $date, $time, $transid, $amount, $currency, $cart_id, $data) === false){
            $filename = Configure::read('Site.pathLogHipay').date("Y")."/".date("m")."/".date("d")."/".date("His").".xml";
            if (!file_exists(dirname($filename))){
                mkdir(dirname($filename), 0755, true);
            }
            file_put_contents($filename, $xml);
            return false;
        }
		


        /* On charge le panier */
        $this->loadModel('Cart');
        $cart = $this->Cart->getDatas($cart_id);
        if (!$cart)return false;
      
        $validToken = $this->getTokenForHiPay($cart['id_cart'], $cart['user']['id']);
		
        if ($validToken !== $token)
            return false;


        $hiPay_logs = array(
            'cart_id'      => (string)$cart_id,
            'order_id'      => 0,
            'operation'     => (string)$operation,
            'status'        => (string)$status,
            'date'          => (string)$date,
            'transaction'   => (string)$transid,
            'amount'        => (string)$amount,
            'currency'      => (string)$currency
        );


        if (trim($operation) == 'authorization' && trim(strtolower($status)) == 'ok'){
            /* Authorisation OK */
                /* On créé la commande */
                $id_order = $this->convertCartToOrder((int)$cart_id);
                if ($id_order){
					$this->validateOrder($id_order, $hiPay_logs);
				}else{
					/*if($cart_id){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						$extractrl->sendEmail('system@web-sigle.fr','BUG URGENT - Paiement Ok panier non validé '.$cart_id,'default',$cart_id);
					}*/
				}
                    

        }else if (trim($operation) == 'capture' && trim(strtolower($status)) == 'ok'){
            // Capture OK
                /* On créé la commande */
                $id_order = $this->convertCartToOrder((int)$cart_id);
                 if ($id_order){
					$this->validateOrder($id_order, $hiPay_logs);
				}else{
					/*if($cart_id){
						App::import('Controller', 'Extranet');
						$extractrl = new ExtranetController();
						$extractrl->sendEmail('system@web-sigle.fr','BUG URGENT - Paiement Ok panier non validé '.$cart_id,'default',$cart_id);
					}*/
				}

        }else if (trim($operation) == 'capture' && trim(strtolower($status)) == 'nok'){
            /* On créé la commande */
            $id_order = $this->convertCartToOrder((int)$cart_id);
            if ($id_order){
                $this->Order->id = $id_order;
                $this->Order->saveField('valid', 0);

                $this->loadModel('OrderHipaytransaction');
                $this->OrderHipaytransaction->create();
                $hipayLogs['order_id'] = $id_order;
                $this->OrderHipaytransaction->saveAll($hipayLogs);
            }


        }else if (trim($operation) == 'authorization' && trim(strtolower($status)) == 'nok'){
            /* On créé la commande */
            $id_order = $this->convertCartToOrder((int)$cart_id);
            if ($id_order && $cart_id){
                $this->Order->id = $id_order;
                $this->Order->saveField('valid', 0);

                $this->loadModel('OrderHipaytransaction');
                $this->OrderHipaytransaction->create();
                $hipayLogs['order_id'] = $id_order;
                $this->OrderHipaytransaction->saveAll($hipayLogs);
            }



        }

        return true;
    }
	public function confirmation_gift()
    {
        $this->autoRender = false;
		
        if (!isset($this->request->query['token'])) return;
        $token = $this->request->query['token'];

		if (!array_key_exists('xml', $this->request->data))  return;
        $xml = stripslashes($this->request->data['xml']);
		
        if (!$token || !$xml)return false;

        if ($this->analyzeNotificationXML($xml, $operation, $status, $date, $time, $transid, $amount, $currency, $giftorder_id, $data) === false){
            $filename = Configure::read('Site.pathLogHipay').date("Y")."/".date("m")."/".date("d")."/".date("His").".xml";
            if (!file_exists(dirname($filename))){
                mkdir(dirname($filename), 0755, true);
            }
            file_put_contents($filename, $xml);
            return false;
        }
		
        /* On charge le panier */
        $this->loadModel('GiftOrder');
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $giftorder_id),
					'recursive' => -1,
				));
        if (!$gift_order)return false;
      
        $validToken = $this->getTokenForHiPay($giftorder_id, $gift_order['GiftOrder']['user_id']);

		if ($validToken !== $token)
            return false;


        $hiPay_logs = array(
            'cart_id'      => (string)'999999'.$giftorder_id,
            'order_id'      => (string)$giftorder_id,
            'operation'     => (string)$operation,
            'status'        => (string)$status,
            'date'          => (string)$date,
            'transaction'   => (string)$transid,
            'amount'        => (string)$amount,
            'currency'      => (string)$currency
        );

		if (trim($operation) == 'authorization' && trim(strtolower($status)) == 'ok'){
            /* Authorisation OK */
                /* On valide la commande */
            	    $this->loadModel('OrderHipaytransaction');
                $this->OrderHipaytransaction->create();
                $this->OrderHipaytransaction->saveAll($hiPay_logs);
                    $this->validateGift($giftorder_id);

        }else if (trim($operation) == 'capture' && trim(strtolower($status)) == 'ok'){
            // Capture OK
                /* On créé la commande */
			 $this->loadModel('OrderHipaytransaction');
                $this->OrderHipaytransaction->create();
                $this->OrderHipaytransaction->saveAll($hiPay_logs);
			$this->validateGift($giftorder_id);

        }else if (trim($operation) == 'capture' && trim(strtolower($status)) == 'nok'){
            /* On créé la commande */

                $this->loadModel('OrderHipaytransaction');
                $this->OrderHipaytransaction->create();
                $this->OrderHipaytransaction->saveAll(hiPay_logs);


        }else if (trim($operation) == 'authorization' && trim(strtolower($status)) == 'nok'){
            /* On créé la commande */

               // $this->Order->id = $giftorder_id;
                //$this->Order->saveField('valid', 0);

                $this->loadModel('OrderHipaytransaction');
               $this->OrderHipaytransaction->create();
                $this->OrderHipaytransaction->saveAll($hiPay_logs);

        }

        return true;
    }
    private function validateOrder($order_id=0, $hipayLogs=array())
    {
        if (!$order_id)return false;

        $this->loadModel('OrderHipaytransaction');
        $this->OrderHipaytransaction->create();
        $hipayLogs['order_id'] = $order_id;
        $this->OrderHipaytransaction->saveAll($hipayLogs);

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
    public function error_return()
    {
        $this->autoRender = false;

        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $contenu = $this->getCmsPage(212);
        $this->set('contenu',$contenu);

        $this->render($this->_render_error);

        $this->clearSessionCart();
    }


























    private function analyzeNotificationXML($xml, &$operation, &$status, &$date, &$time, &$transid, &$origAmount, &$origCurrency, &$idformerchant, &$merchantdatas)
    {
        $operation = '';
        $status = '';
        $date = '';
        $time = '';
        $transid = '';
        $origAmount = '';
        $origCurrency = '';
        $idformerchant = '';
        $merchantdatas = array();

        try {
            $obj = new SimpleXMLElement(trim($xml));
        } catch (Exception $e) {
            return false;
        }

        if (isset($obj->result[0]->operation))
            $operation=$obj->result[0]->operation;
        else return false;

        if (isset($obj->result[0]->status))
            $status=$obj->result[0]->status;
        else return false;

        if (isset($obj->result[0]->date))
            $date=$obj->result[0]->date;
        else return false;

        if (isset($obj->result[0]->time))
            $time=$obj->result[0]->time;
        else return false;

        if (isset($obj->result[0]->transid))
            $transid=$obj->result[0]->transid;
        else return false;

        if (isset($obj->result[0]->origAmount))
            $origAmount=$obj->result[0]->origAmount;
        else return false;

        if (isset($obj->result[0]->origCurrency))
            $origCurrency=$obj->result[0]->origCurrency;
        else return false;

        if (isset($obj->result[0]->idForMerchant))
            $idformerchant=$obj->result[0]->idForMerchant;
        else return false;

        if (isset($obj->result[0]->merchantDatas)) {
            $d = $obj->result[0]->merchantDatas->children();
            foreach($d as $xml2) {
                if (preg_match('#^_aKey_#i',$xml2->getName())) {
                    $indice = substr($xml2->getName(),6);
                    $xml2 = (array)$xml2;
                    $valeur = (string)$xml2[0];
                    $merchantdatas[$indice] = $valeur;
                }
            }
        }

        return true;
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
			$mysqli_connect->query("UPDATE order_hipaytransactions SET date_upd = NOW() WHERE order_id = '{$this->request->data['id_order']}'");
			
			
			
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

}
<?php
App::uses('PaymentController', 'Controller');

class PaymentpaypalController extends PaymentController {
    protected $payment_name = 'paypal';
    protected $payment_mode = 'paypal';
	
	protected $_url_return = 'paymentpaypal/submit?utm_novverride=1';
    protected $_url_cancel = 'paymentpaypal/cancel?utm_novverride=1';
	protected $_url_return_gift = 'paymentpaypal/submit_gift?utm_novverride=1';
    protected $_url_cancel_gift = 'paymentpaypal/cancel_gift?utm_novverride=1';
    protected $_render_return = 'paymentpaypal/submit';
    protected $_render_cancel = 'paymentpaypal/cancel';
    protected $_render_ipn    = 'paymentpaypal/ipn';

    protected $paypal_sandbox = true;

    protected $_render_admin_index = '/Payment/Paypal/admin_index';
    protected $_render_validation = '/Payment/Paypal/validation';
	protected $_render_validation_gift = '/Gifts/validation';
    protected $_render_error = '/Payment/Paypal/error';


    protected $_paypal_confs = array(
		'prod_old'    => array(
            'sandboxMode' => false,
            'nvpUsername' => 'esa.kkt_api1.gmail.com',
            'nvpPassword' => 'VG6QSJLERJ7NKT8M',
            'nvpSignature' => 'A-BDvHMqmfe7.jf23G.fqQCZNt0cAEz3KXb2.W1dOWA2mgFU4tGaEY6U'
        ),
        'prod'    => array(
            'sandboxMode' => false,
            'nvpUsername' => 'contact-2_api1.spiriteo.com',
            'nvpPassword' => '38MBY2LKVC458SLQ',
            'nvpSignature' => 'A9BiAtHlNpvdHOX3mPrOVBHDOETPAWnRnIKTzIgD3I3czVomNr3cYRz1'
        ),
        'sandbox' => array(
            'sandboxMode' => true,
            'nvpUsername' => 'surf-nico-facilitator_api1.gemelos.fr',
            'nvpPassword' => 'X3N7KCZX252CW2DS',
            'nvpSignature' => 'ADmCYjxeN-FRtmYiskW9PJ2jQxmPAL3dEwIKwDJbpstKkHMSg3Z39pbY'
        ),
    );

    /* Pages cms modèles de mails à envoyer */
    protected $cms_ids = array(
        'attente_paiement'          =>  172,  /* Propre à chaque mode de paiement !!!! */
        'confirmation_paiement'     =>  215
    );

    private function paypalApiConnect()
    {
        App::uses('Paypal', 'Paypal.Lib');
        $this->Paypal = new Paypal($this->paypal_sandbox?$this->_paypal_confs['sandbox']:$this->_paypal_confs['prod']);
    }
    private function getPaypalConf()
    {
        return $this->paypal_sandbox?$this->_paypal_confs['sandbox']:$this->_paypal_confs['prod'];
    }

    public function beforeFilter()
    {
        /* !!!!! On autorise IPN même si on n'est pas loggués */
        $this->Auth->allow('ipn','admin_declarer_valide','admin_declarer_impaye','admin_declarer_rembourse','admin_declarer_incident','getHookPayment','getHookPaymentGift');
        parent::beforeFilter();
    }
    protected function getDatasFromCart()
    {
        if (isset($this->request->params['action']) &&  $this->request->params['action'] == 'ipn'){
            /* Si ACTION IPN alors on ne récupère pas le panier (appel extérieur entre serveurs) */
                $this->autoRender = false;
                return true;
        }
        return parent::getDatasFromCart();
    }
    private function findPaypalTransactionByTxnId($transaction_id=0)
    {
        $this->loadModel('OrderPaypaltransaction');
        $rows = $this->OrderPaypaltransaction->find("first", array(
            'conditions' => array('payment_transactionid' => $transaction_id),
            'limit' => 1,
            'order' => 'order_id DESC'
        ));
        return isset($rows['OrderPaypaltransaction'])?$rows['OrderPaypaltransaction']:false;
    }
    private function tmpLog($msg="")
    {
        $this->loadModel('PaypalLog');
        $this->PaypalLog->create();
        $this->PaypalLog->save(array('msg' => $msg));
    }
    public function ipn()
    {


        $datas = $this->request->data;
        $transaction_id = (isset($datas['txn_id']) && !empty($datas['txn_id']))?trim($datas['txn_id']):false;
        if (!$transaction_id)die();
        $transaction = $this->findPaypalTransactionByTxnId($transaction_id);
        if (!$transaction)die();

/*
        ob_start();
        echo '<pre>';
        print_r($this->request->data);
        print_r($transaction);
        echo '</pre>';
        $tmp = ob_get_contents();
        ob_end_clean();

        $this->loadModel('Tmp');
        $this->Tmp->create();
        $this->Tmp->save(array(
            'text' => $tmp
        ));
*/

        $amount = (isset($datas['mc_gross']) && !empty($datas['mc_gross']))?floatval($datas['mc_gross']):false;



        /* On compare quelques valeurs par sécurité */
            if (floatval($transaction['payment_amount']) != $amount)die();
            $status = isset($datas['payment_status'])?$datas['payment_status']:false;
            if (!$status)die();


        /* On renvoit notre tableau POST à Paypal pour qu'il nous confirme que tout va bien */
            $params = 'cmd=_notify-validate';
            foreach ($_POST AS $key => $value)
                $params .= '&'.$key.'='.urlencode(stripslashes($value));
            $this->paypalApiConnect();
            $result = $this->Paypal->nx_verif_post_datas_integrity($params);
            if ($result !== 'VERIFIED')die();

        /* tout est secure à partir d'ici, les données sont intègres */

        /* On vérifie notre statut actuel de commande que l'on compare au statut Paypal recu */
            if (strtoupper($transaction['payment_status']) === strtoupper($status))die('Le statut n\'a pas ete modifie');

            switch (strtoupper($status)){
                case 'COMPLETED':
                    /* On log les données IPN Paypal */
                        $this->logOrderPaypalIpn((int)$transaction['order_id']);

                    /* On valide la commande */
                        $paypalLogs = $transaction;
                        $paypalLogs['payment_status'] = $status;
                        $paypalLogs['payment_ordertime'] = date("Y-m-d H:i:s",strtotime($datas['payment_date']));
                        $this->validateOrder((int)$transaction['order_id'], $paypalLogs);
                break;
            }


        header('HTTP/1.1 200 OK');


        /*
         * ob_start();
        echo '<pre>';
        print_r($_POST);
        print_r($this->request->data);
        print_r($transaction);
        echo '</pre>';
        $tmp = ob_get_contents();
        ob_end_clean();

        $this->loadModel('Tmp');
        $this->Tmp->create();
        $this->Tmp->save(array(
            'text' => $tmp
        ));
        die();
         */

        die();
    }
    private function logOrderPaypalIpn($order_id=0)
    {
        if (!$order_id)return false;
        $string = '';
        foreach ($this->request->data AS $k => $v)
            $string.= $k.': '.$v."\n";

        $this->loadModel('OrderPaypalipnLog');
        $this->OrderPaypalipnLog->create();
        $this->OrderPaypalipnLog->save(array('order_id' => $order_id, 'sandbox_context' => $this->paypal_sandbox?1:0, 'content' => $string));
    }

    public function admin_index()
    {
		ini_set("memory_limit",-1);
       /* if (isset($this->request->query['valid'])){
            $order_valid = 1;
        }elseif (isset($this->request->query['pending'])){
            $order_valid = 2;
        }else{
            $order_valid = 0;
        }*/
		
		$order_valid = 0;
		//if(isset($this->request->query['valid'])) $order_valid = 1;
		if(isset($this->request->query['pending'])) $order_valid = 2;
		if(isset($this->request->query['oppos'])) $order_valid = 3;
		
		
		$condition = array();
		
		
		$condition['Order.payment_mode'] = $this->payment_mode;
        $condition['Order.valid'] = $order_valid;
		
		if(isset($this->request->query['valid'])){
			unset($condition['Order.valid']);
			$condition['OR'] = array('Order.valid = 1', 'Order.valid =4');
		}
		
		if(isset($this->request->data['Payment'])){
			
			$this->Session->write('Payment_adr_ip', '');
			$this->Session->write('Payment_client', '');
			$this->Session->write('Payment_email', '');
			$this->Session->write('Payment_numero', '');
			
			if($this->request->data['Payment']['adr_ip'] && $this->request->data['Payment']['adr_ip'] != ' '){
				$condition['Order.IP'] = $this->request->data['Payment']['adr_ip'];	
				$this->Session->write('Payment_adr_ip',  $this->request->data['Payment']['adr_ip']);
			}
				
			if($this->request->data['Payment']['client'] && $this->request->data['Payment']['client'] != ' '){
				$condition['User.firstname'] = $this->request->data['Payment']['client'];
				$this->Session->write('Payment_client', $this->request->data['Payment']['client']);
			}
					
			if($this->request->data['Payment']['email'] && $this->request->data['Payment']['email'] != ' '){
				//$condition['paypal_logs.email'] = $this->request->data['Payment']['email'];
				$condition['OR'] = array('User.email' =>$this->request->data['Payment']['email'],
										'paypal_logs.email' =>$this->request->data['Payment']['email'],
				);
				//$condition['User.email'] = $this->request->data['Payment']['email'];
				$this->Session->write('Payment_email', $this->request->data['Payment']['email']);
			}
				
			if($this->request->data['Payment']['numero'] && $this->request->data['Payment']['numero'] != ' '){
				$condition['paypal_logs.payment_transactionid'] = $this->request->data['Payment']['numero'];
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
				$condition['paypal_logs.payment_transactionid'] = $this->Session->read('Payment_numero');
			}
		}
		$limit = 15;
		if($this->Session->check('Date')){
				if($order_valid == 3){
					$condition = array_merge($condition, array(
                    'paypal_logs.date_upd >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
                    'paypal_logs.date_upd <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
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
            'fields' => array('Order.*','paypal_logs.*','User.*'),
            'conditions' => $condition,
            'joins'     => array(
                array(

                    'alias' => 'paypal_logs',
                    'table' => 'order_paypaltransactions',
                    'type'  => 'inner',
                    'conditions' => array('paypal_logs.cart_id = Order.cart_id')
                )
            ),
            'group'         => 'Order.id',
            'order'        => 'Order.date_add DESC',
            'paramType' => 'querystring',
            'limit' => $limit
        );

        $rows = $this->Paginator->paginate($this->UserCreditHistory);

        $this->set(array(
            'page_title' => $this->payment_name,
            'orders' => $rows
        ));
        $this->render($this->_render_admin_index);
    }
    public function paypal_request($token=false, $payerID=false)
    {
		$this->autoRender = false;
        $error = false;
        if (empty($token) || !$token || empty($payerID) || !$payerID)return false;
        $session_token = $this->getPaypalDatasFromSession('token');
        $order = $this->getPaypalDatasFromSession('order');

        $session_id_cart = (int)$this->Session->read('User.id_cart');
        if (!$session_id_cart){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            return false;
        }

        if ($token !== $session_token){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            return false;
        }



        try {
            $infos = $this->Paypal->getExpressCheckoutDetails($token);
			//var_dump($infos);exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($error){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            return false;
        }

        /* On vérifie que l'id_cart de session correspond au CUSTOM renvoyé par PAYPAL */
        if ($session_id_cart !== (int)$infos['CUSTOM']){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            return false;
        }

        try {
            $result = $this->Paypal->doExpressCheckoutPayment($order, $token , $payerID);
        }catch (Exception $e){
            $error = $e->getMessage();
        }

        if ($error){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
            return false;
        }

        /* A partir de là, on confirme le paiement si nécessaire : */
            $paypalLogs = array(
                'cart_id'                   =>      $session_id_cart,
                'token'                     =>      $token,
                'correlationid'             =>      $result['CORRELATIONID'],
                'ack'                       =>      $infos['ACK'],
                'version'                   =>      $infos['VERSION'],
                'email'                     =>      $infos['EMAIL'],
                'payerid'                   =>      $infos['PAYERID'],
                'payerstatus'               =>      $infos['PAYERSTATUS'],
                'currencycode'              =>      $infos['CURRENCYCODE'],
                'amt'                       =>      (float)$infos['AMT'],
                'payment_transactionid'     =>      $result['PAYMENTINFO_0_TRANSACTIONID'],
                'payment_transactiontype'   =>      $result['PAYMENTINFO_0_TRANSACTIONTYPE'],
                'payment_type'              =>      $result['PAYMENTINFO_0_PAYMENTTYPE'],
                'payment_ordertime'         =>      date("Y-m-d H:i:s",strtotime($result['PAYMENTINFO_0_ORDERTIME'])),
                'payment_amount'            =>      (float)$result['PAYMENTINFO_0_AMT'],
                'payment_status'            =>      $result['PAYMENTINFO_0_PAYMENTSTATUS'],
                'payment_pendingreason'     =>      $result['PAYMENTINFO_0_PENDINGREASON'],
                'payment_ack'               =>      $result['PAYMENTINFO_0_ACK']
            );

        /* On convertit le panier en commande */
            $id_order = $this->convertCartToOrder($session_id_cart);
            if (!$id_order){
                $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
                $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
                return false;
            }
            $isCompleted = false;


            $status = isset($result['PAYMENTINFO_0_PAYMENTSTATUS'])?strtoupper(trim($result['PAYMENTINFO_0_PAYMENTSTATUS'])):false;

            if (strtoupper($result['ACK']) === 'SUCCESS' && in_array($status, array('PENDING','COMPLETED'))){
                /* si tout s'est bien passé */
                    if ($status == 'COMPLETED'){
                        /* Commande vérifiée, on crédite l'utilisateur */
                            $this->validateOrder($id_order, $paypalLogs);
                            $isCompleted = true;
                            $this->displayValidatePage();
                    }else{
                        /* La commande sera validée ultérieurement  via IPN - OU - est une erreur */
                            $isCompleted = false;
                            $this->Order->id = $id_order;
                            $this->Order->saveField('valid', ($status == 'PENDING')?2:0);
                            $this->loadModel('OrderPaypaltransaction');
                            $this->OrderPaypaltransaction->create();
                            $paypalLogs['order_id'] = $id_order;
                            $this->OrderPaypaltransaction->saveAll($paypalLogs);

                            if ($status == 'PENDING'){
                                $this->displayPendingPage();
                            }else{
                                $this->error_return();
                            }

                    }
            }
        return true;
    }
	public function paypal_request_gift($token=false, $payerID=false)
    {
		$this->autoRender = false;
        $error = false;
		
        if (empty($token) || !$token || empty($payerID) || !$payerID)return false;
        $session_token = $this->getPaypalDatasFromSession('token');
		$order = $this->getPaypalDatasFromSession('order');

        $session_id_giftorder = (int)$this->Session->read('GiftOrderId');
		
        if (!$session_id_giftorder){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
            return false;
        }

        if ($token !== $session_token){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
            return false;
        }



        try {
            $infos = $this->Paypal->getExpressCheckoutDetails($token);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($error){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
            return false;
        }

        /* On vérifie que l'id_cart de session correspond au CUSTOM renvoyé par PAYPAL */
        if ($session_id_giftorder !== (int)$infos['CUSTOM']){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
            return false;
        }

        try {
            $result = $this->Paypal->doExpressCheckoutPayment($order, $token , $payerID);
        }catch (Exception $e){
            $error = $e->getMessage();
        }

        if ($error){
            $this->Session->setFlash(__('Erreur interne, veuillez réessayer.'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
            return false;
        }


        /* A partir de là, on confirme le paiement si nécessaire : */
            $paypalLogs = array(
                'cart_id'                   =>      '999999'.$session_id_giftorder,
				'order_id'                   =>      $session_id_giftorder,
                'token'                     =>      $token,
                'correlationid'             =>      $result['CORRELATIONID'],
                'ack'                       =>      $infos['ACK'],
                'version'                   =>      $infos['VERSION'],
                'email'                     =>      $infos['EMAIL'],
                'payerid'                   =>      $infos['PAYERID'],
                'payerstatus'               =>      $infos['PAYERSTATUS'],
                'currencycode'              =>      $infos['CURRENCYCODE'],
                'amt'                       =>      (float)$infos['AMT'],
                'payment_transactionid'     =>      $result['PAYMENTINFO_0_TRANSACTIONID'],
                'payment_transactiontype'   =>      $result['PAYMENTINFO_0_TRANSACTIONTYPE'],
                'payment_type'              =>      $result['PAYMENTINFO_0_PAYMENTTYPE'],
                'payment_ordertime'         =>      date("Y-m-d H:i:s",strtotime($result['PAYMENTINFO_0_ORDERTIME'])),
                'payment_amount'            =>      (float)$result['PAYMENTINFO_0_AMT'],
                'payment_status'            =>      $result['PAYMENTINFO_0_PAYMENTSTATUS'],
                'payment_pendingreason'     =>      $result['PAYMENTINFO_0_PENDINGREASON'],
                'payment_ack'               =>      $result['PAYMENTINFO_0_ACK']
            );


            $status = isset($result['PAYMENTINFO_0_PAYMENTSTATUS'])?strtoupper(trim($result['PAYMENTINFO_0_PAYMENTSTATUS'])):false;
			$etat = strtoupper($result['ACK']);
		
            if ( in_array($etat, array('SUCCESS','SUCCESSWITHWARNING')) && in_array($status, array('PENDING','COMPLETED'))){
                /* si tout s'est bien passé */
				$this->loadModel('OrderPaypaltransaction');
                    if ($status == 'COMPLETED' || $status == 'PENDING' ){
                        /* Commande vérifiée, on crédite l'utilisateur */
                            
                            $isCompleted = true;
							$this->OrderPaypaltransaction->create();
                            $this->OrderPaypaltransaction->saveAll($paypalLogs);
						
							$this->validateGift((int)$infos['CUSTOM']);
                            $this->displayValidatePageGift((int)$infos['CUSTOM']);
						
                    }else{
                        /* La commande sera validée ultérieurement  via IPN - OU - est une erreur */
                            $isCompleted = false;
							$this->OrderPaypaltransaction->create();
                            $this->OrderPaypaltransaction->saveAll($paypalLogs);
                            if ($status == 'PENDING'){
                                $this->displayPendingPageGift((int)$infos['CUSTOM']);
                            }else{
                                $this->error_return();
                            }

                    }
            }
        return true;
    }
    private function error_return()
    {
        $this->autoRender = false;

        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $contenu = $this->getCmsPage(213);
        $this->set('contenu',$contenu);

        $this->render($this->_render_error);
        $this->clearSessionCart();
    }
    private function validateOrder($order_id=0, $paypalLogs=array())
    {
        if (!$order_id)return false;
        $this->loadModel('OrderPaypaltransaction');
        $this->OrderPaypaltransaction->create();
        $paypalLogs['order_id'] = $order_id;
        $this->OrderPaypaltransaction->saveAll($paypalLogs);
        $this->order_confirm($order_id, $error);

    }
    private function displayPendingPage()
    {
        $this->autoRender = false;
        $contenu = $this->getCmsPage(217);
        $this->set('contenu',$contenu);


        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $id_cart = $this->Session->read('User.save_id_cart_for_validation');
        $this->loadModel('Cart');
        $cart_datas = $this->Cart->getDatas($id_cart);
        $this->set('cart_datas',$cart_datas);



        $this->render($this->_render_validation);

        /* Important !! */
        $this->clearSessionCart();
        $this->Session->delete('User.save_id_cart_for_validation');
    }
	
    private function displayValidatePage()
    {
        $this->autoRender = false;
        $contenu = $this->getCmsPage(214);
        $this->set('contenu',$contenu);


        if (!$this->Session->check('User.save_id_cart_for_validation')){
            $this->redirect('/');
            return true;
        }

        $id_cart = $this->Session->read('User.save_id_cart_for_validation');
        $this->loadModel('Cart');
        $cart_datas = $this->Cart->getDatas($id_cart);
        $this->set('cart_datas',$cart_datas);



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
	private function displayPendingPageGift($id_order = 0)
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
    public function submit()
    {
        $this->paypalApiConnect();
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
		$days_between = ceil(abs($end - $start) / 86400);
		$this->set('delay_payment', $days_between);

        if (isset($this->request->query['token']) && isset($this->request->query['PayerID'])){
            /* Réception d'une requete de retour */
            $this->paypal_request($this->request->query['token'], $this->request->query['PayerID']);
        }else{
            $this->redirect(array('controller' => 'products', 'action' => 'tarif'));
        }
    }
	
	 public function submit_gift()
    {
        $this->paypalApiConnect();
				
        if (isset($this->request->query['token']) && isset($this->request->query['PayerID'])){
            /* Réception d'une requete de retour */
            $this->paypal_request_gift($this->request->query['token'], $this->request->query['PayerID']);
        }else{
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy'));
        }
    }

    public function formcheckout()
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
		
		
        $this->paypalApiConnect();
        $url_site = Router::url('/', true);
        $url = false;
        $error = false;

        $order = array(
            'description' => __('Votre achat sur %s', Configure::read('Site.name')),
            'currency' => $this->cart_datas['product']['Country']['devise_iso'],
            'return' => $url_site.$this->_url_return,
            'cancel' => $url_site.$this->_url_cancel,
            'custom' => $this->cart_datas['id_cart'],
            'shipping' => 0,
            'items' => array(
                0 => array(
                    'name' => $this->cart_datas['product']['ProductLang']['0']['name'],
                    'description' => $this->cart_datas['product']['ProductLang']['0']['description'],
                    'tax' => 0.00,
                    'subtotal' => $this->cart_datas['total_price'],
                    'qty' => 1
                )
            )
        );

        $parsed = array();
        try {
            $url = $this->Paypal->setExpressCheckout($order, array(
                'object' => $this,
                'function' => 'override_nvps'
            ), $parsed);

            /* Stockage du token */
                if (isset($parsed['TOKEN']) && isset($parsed['ACK']) && in_array($parsed['ACK'], array('Success', 'SuccessWithWarning')))  {
                    $this->putPaypalDatasToSession('token',$parsed['TOKEN']);
                    $this->putPaypalDatasToSession('order',$order);
                }else{
                    $this->putTokenToSession('token',false);
                    $this->putPaypalDatasToSession('order',false);
                }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($url && !$error){
            $this->redirect($url);
        }else{
            $this->Session->setFlash(__('Ce mode de paiement est actuellement indisponible, veuillez nous excuser'), 'flash_warning');
            $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
        }
    }
	 public function formcheckoutgift()
    {
		
		$giftorder_id = $this->Session->read('GiftOrderId');
		//save client
		$this->loadModel('GiftOrder');
		$this->GiftOrder->id = $this->Session->read('GiftOrderId');
		$this->GiftOrder->saveField('user_id', $this->Auth->user('id'));	
		
		$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $this->Session->read('GiftOrderId')),
					'recursive' => -1,
				));
		$this->loadModel('Gift');
		$gift = $this->Gift->find('first', array(
					'conditions' => array('Gift.id' => $gift_order['GiftOrder']['gift_id']),
					'recursive' => -1,
				));
		
        $this->paypalApiConnect();
        $url_site = Router::url('/', true);
        $url = false;
        $error = false;

        $order = array(
            'description' => __('Votre achat sur %s', Configure::read('Site.name')),
            'currency' => $gift_order['GiftOrder']['devise'],
            'return' => $url_site.$this->_url_return_gift,
            'cancel' => $url_site.$this->_url_cancel_gift,
            'custom' => $giftorder_id,
            'shipping' => 0,
            'items' => array(
                0 => array(
                    'name' => $gift['Gift']['name'].' '.$gift_order['GiftOrder']['amount'],
                    'description' => '',
                    'tax' => 0.00,
                    'subtotal' => $gift_order['GiftOrder']['amount'],
                    'qty' => 1
                )
            )
        );

        $parsed = array();
        try {
            $url = $this->Paypal->setExpressCheckout($order, array(
                'object' => $this,
                'function' => 'override_nvps'
            ), $parsed);

			/* Stockage du token */
                if (isset($parsed['TOKEN']) && isset($parsed['ACK']) && in_array($parsed['ACK'], array('Success', 'SuccessWithWarning')))  {
                    $this->putPaypalDatasToSession('token',$parsed['TOKEN']);
                    $this->putPaypalDatasToSession('order',$order);
                }else{
                    $this->putTokenToSession('token',false);
                    $this->putPaypalDatasToSession('order',false);
                }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

		 if ($url && !$error){
            $this->redirect($url);
        }else{
            $this->Session->setFlash(__('Ce mode de paiement est actuellement indisponible, veuillez nous excuser'), 'flash_warning');
            $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
        }
    }
    private function putPaypalDatasToSession($key=false, $value=false)
    {
        if (!$key)return false;
        return $this->Session->write('paypal.'.$key, $value);
    }
    private function getPaypalDatasFromSession($key=false)
    {
        if (!$key)return false;
        return $this->Session->read('paypal.'.$key);
    }
    public function override_nvps($nvp=array())
    {
        if (empty($nvp))return $nvp;
        $url_site = Router::url('/', true);
        $locale = $this->getPaymentLocale();
        if (!empty($locale) && $locale)
            $nvp['LOCALECODE'] = $locale;
        $nvp['PAYMENTREQUEST_0_NOTIFYURL'] = $url_site.$this->_render_ipn;



        $logo_url = $this->getPaymentLogoUrl();
        if ($logo_url)
            $nvp['LOGOIMG'] = $logo_url;

        return $nvp;
    }
    public function cancel()
    {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'accounts', 'action' => 'buycredits', 'admin' => false), false);
    }
	public function cancel_gift()
    {
        $this->autoRender = false;
        $this->redirect(array('controller' => 'gifts', 'action' => 'buy', 'admin' => false), false);
    }

    public function getHookPayment()
    {
		if($this->request->is('ajax')){
			$this->jsonRender(array('return' => true, 'url' => Router::url(array(
				'controller' => $this->request->controller,
				'action'     => 'formcheckout'
			))));
		}else{
			 return Router::url(array(
				'controller' => $this->request->controller,
				'action'     => 'formcheckout'
			));
		}
       
    }
	
	 public function getHookPaymentGift()
    {
		if($this->request->is('ajax')){
			$this->jsonRender(array('return' => true, 'url' => Router::url(array(
				'controller' => $this->request->controller,
				'action'     => 'formcheckoutgift'
			))));
		}else{
			 return Router::url(array(
				'controller' => $this->request->controller,
				'action'     => 'formcheckoutgift'
			));
		}
       
    }
	
	public function admin_declarer_impaye(){
		if($this->request->is('ajax')){
			
			$requestData = $this->request->data;
           
			if(!isset($requestData)|| !isset($this->request->data['id_order']))
                $this->jsonRender(array('return' => false));
			
			
			$this->loadModel('Order');
			$this->Order->id = $this->request->data['id_order'];
            $this->Order->saveField('valid',3);
			
			$dbb_patch = new DATABASE_CONFIG();
			$dbb_connect = $dbb_patch->default;
			$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
			$mysqli_connect->query("UPDATE order_paypaltransactions SET date_upd = NOW() WHERE order_id = '{$this->request->data['id_order']}'");
			
			
			
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
            $this->Order->saveField('valid',4);
			
			
			$this->jsonRender(array('return' => true));
		}
	}
	
	public function admin_declarer_incident(){
		
		$params = $this->request->params;
		$id_order = $params['pass'][0];
		
		
		if(empty($id_order))
			 $this->redirect('/admin/paymentpaypal?valid');
		
		
		$condition = array('Order.id'=>$id_order);
		
		$this->loadModel('Order');
		
		$order = $this->Order->find("first", array(
						'fields' => array('Order.*','paypal_logs.*','User.*'),
						'conditions' => $condition,
						'joins'     => array(
							array(

								'alias' => 'paypal_logs',
								'table' => 'order_paypaltransactions',
								'type'  => 'inner',
								'conditions' => array('paypal_logs.cart_id = Order.cart_id')
							)
						),
						'group'         => 'Order.id',
						'order'        => 'Order.date_add DESC',
						'paramType' => 'querystring',
					)
				);
		
		$condition = array('Order.id >'=>$id_order,'Order.user_id'=>$order['Order']['user_id']);
		
		$order_next = $this->Order->find("first", array(
						'fields' => array('Order.*','paypal_logs.*','User.*'),
						'conditions' => $condition,
						'joins'     => array(
							array(

								'alias' => 'paypal_logs',
								'table' => 'order_paypaltransactions',
								'type'  => 'inner',
								'conditions' => array('paypal_logs.cart_id = Order.cart_id')
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
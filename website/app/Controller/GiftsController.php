<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');
use setasign\Fpdi\Fpdi;
    class GiftsController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'Gift', 'GiftOrder');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'gifts')));

        public function beforeFilter() {
			 $this->Auth->allow('index','buy','show', 'pdf', 'postpdf');
            parent::beforeFilter();
        }

        public function admin_create(){

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('domain_select', 'count', 'half'));

            if($this->request->is('post')){

                $requestData = $this->validForm('create');

                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur, dans cette action on s'en fiche du msg
                    if(isset($requestData[0])){
                        $requestData = $requestData[1];
                    }
                }
                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;

                //On save les donnée
                $this->Gift->create();
                if($this->Gift->save($requestData['Gift'])){
                    $this->Session->setFlash(__('Le bon a été crée.'), 'flash_success');
                    $this->redirect(array('controller' => 'gifts', 'action' => 'edit', 'admin' => true, 'id' => $this->Gift->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'gifts', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection edition
                $this->redirect(array('controller' => 'gifts', 'action' => 'list', 'admin' => true), false);
            }
        }

        public function admin_list(){
            //Les slides
			
			 $this->Paginator->settings = array(
				'fields' => array('Gift.*'),
                'order' => array('Gift.id' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $gifts = $this->Paginator->paginate($this->Gift);

            $this->set(compact('gifts'));
        }

        public function admin_edit($id){
            if($this->request->is('post')){
				$req = $this->request->data;
                $requestData = $this->validForm('edit', $id);

                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'gifts', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }
				
				
                $updateData = $requestData['Gift'];
                $updateData = $this->Gift->value($updateData);
                if($this->Gift->updateAll($updateData, array('Gift.id' => $id))){
                   
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour du bon'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour du bon'), 'flash_warning');

                $this->redirect(array('controller' => 'gifts', 'action' => 'list', 'admin' => true), false);
            }

          
            //On récupère toutes les infos du slide
            $gift = $this->Gift->find('all',array(
                'fields' => array('Gift.*'),
                'conditions' => array('Gift.id' => $id),
                'recursive' => -1
            ));

            ///Les infos du slide
            $giftDatas = $gift[0]['Gift'];
            //On explose les id des domains
            $giftDatas['domain'] = explode(',', $giftDatas['domains']);

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('giftDatas', 'domain_select', 'count', 'half'));
        }

        public function admin_activate($id){
            //on active le slide
            $this->Gift->id = $id;
            if($this->Gift->saveField('active', 1))
                $this->Session->setFlash(__('Le bon a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du bon.'),'flash_warning');

            $this->redirect(array('controller' => 'gifts', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive le slide
            $this->Gift->id = $id;
            if($this->Gift->saveField('active', 0))
                $this->Session->setFlash(__('Le bon a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du bon.'),'flash_warning');

            $this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
        }

       
        private function validForm($mode, $id = 0){
            //Le template pour les modes
            $template['create'] = array(
                'fieldForm' => array('active', 'name', 'amount'),
                'requiredForm' => array('name', 'amount')
            );
            $template['edit'] = array(
                'fieldForm' => array('active', 'name', 'amount'),//, 'voucher_buyer', 'voucher_credit'
                'requiredForm' => array('id', 'name', 'amount')
            );
            //Les données du formulaire
            $requestData = $this->request->data;

            //Check le formulaire
            $requestData['Gift'] = Tools::checkFormField($requestData['Gift'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['Gift'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'gifts', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'gifts', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Si aucun domain n'a été checked
            if(empty($requestData['domain'])){
                $this->Session->setFlash(__('Sélectionner au minimum un domaine.'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'gifts', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Les domaines
            $requestData['Gift']['domains'] = implode(',', array_keys($requestData['domain']));

          
           
            return $requestData;
        }
		
		
		public function index(){
			$domain_id = $this->Session->read('Config.id_domain');
			if($this->request->is('post')){
            	$requestData = $this->request->data;
				
				if(!$requestData['g-recaptcha-response']){
					$this->Session->setFlash(__('Veuillez valider le Captcha.'),'flash_error');
					$this->redirect(array('controller' => 'gifts', 'action' =>'index'));
				}
				
				
				if(isset($requestData['Gift']['beneficiary_email']) && $requestData['Gift']['beneficiary_email']){
					$requestData['GiftOrder'] = Tools::checkFormField($requestData['Gift'],
						array('id', 'beneficiary_firstname', 'beneficiary_lastname', 'beneficiary_email', 'text', 'send_who', 'send_date'),
						array('id')
					);
					if($requestData['GiftOrder'] === false){
						$this->Session->setFlash(__('Veuillez remplir tous les champs.'),'flash_error');
					}else{
						$gift = $this->Gift->find('first', array(
							'conditions' => array('Gift.id' => $requestData['GiftOrder']['id']),
							'recursive' => -1,
						));
						 $devise = 'EUR';
						if($domain_id == 29)$devise = 'CAD';
						if($domain_id == 13)$devise = 'CHF';
						$requestData['GiftOrder']['user_id'] = $this->Auth->user('id');
						$requestData['GiftOrder']['gift_id'] = $requestData['GiftOrder']['id'];
						$requestData['GiftOrder']['domain_id'] = $domain_id;
						$requestData['GiftOrder']['devise'] = $devise;
						$requestData['GiftOrder']['amount'] = $gift['Gift']['amount'];
						$requestData['GiftOrder']['sold'] = $gift['Gift']['amount'];
						$dd = explode('/',$requestData['GiftOrder']['send_date']);
						$requestData['GiftOrder']['send_date'] = $dd[2].'-'.$dd[1].'-'.$dd[0].' 00:00:00';
						unset($requestData['GiftOrder']['id']);
						if($this->Session->read('GiftOrderId')){
							$requestData['GiftOrder']['devise'] = "'".$devise."'";
							$requestData['GiftOrder']['beneficiary_firstname'] = "'".addslashes($requestData['GiftOrder']['beneficiary_firstname'])."'";
							$requestData['GiftOrder']['beneficiary_lastname'] = "'".addslashes($requestData['GiftOrder']['beneficiary_lastname'])."'";
							$requestData['GiftOrder']['beneficiary_email'] = "'".addslashes($requestData['GiftOrder']['beneficiary_email'])."'";
							$requestData['GiftOrder']['text'] = "'".addslashes($requestData['GiftOrder']['text'])."'";
							$requestData['GiftOrder']['send_date'] = "'".addslashes($requestData['GiftOrder']['send_date'])."'";
							$this->GiftOrder->updateAll($requestData['GiftOrder'],array('id' => $this->Session->read('GiftOrderId'),'user_id' => $this->Auth->user('id')));
							$this->redirect(array('controller' => 'gifts', 'action' => 'buy'));
						}else{
							$this->GiftOrder->create();
							if($this->GiftOrder->save($requestData)){
								$this->Session->write('GiftOrderId', $this->GiftOrder->id);
								$this->redirect(array('controller' => 'gifts', 'action' => 'buy'));
							}else
								$this->Session->setFlash(__('Erreur dans les informations saisies'),'flash_warning');
						}
						 
					}
				}else{
					$this->Session->setFlash(__('Merci de renseigner votre email.'),'flash_warning');
				}
			}
			
			
			
			$gifts = $this->Gift->find('all', array(
                'conditions' => array('Gift.domains like' => '%'.$domain_id.'%', 'Gift.name'=>'e-carte'),
                'recursive' => -1,
				'order'=>'Gift.amount'
            ));
			
			$gift_selected = '';
			$form_data = array();
			if($this->Session->read('GiftOrderId')){
				$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $this->Session->read('GiftOrderId')),
					'recursive' => -1,
				));
				$gift_selected = $gift_order['GiftOrder']['gift_id'];
				if($gift_order['GiftOrder']['send_date'] && $gift_order['GiftOrder']['send_date'] != '0000-00-00 00:00:00'){
					$dd = explode(' ',$gift_order['GiftOrder']['send_date']);
					$dd1 = explode('-',$dd[0]);
					$gift_order['GiftOrder']['send_date_fr'] = $dd1[2].'/'.$dd1[1].'/'.$dd1[0];
				}
				$form_data = $gift_order['GiftOrder'];
			}
			
			$this->set(compact('gifts','gift_selected','form_data','domain_id'));
			
			$this->loadModel('Page');
			$this->loadModel('PageLang');
			
			$pagelang = $this->Page->PageLang->find('first',array(
                            'fields'     => 'PageLang.*, Page.*',
                            'conditions' => array('Page.id' => 409,'PageLang.lang_id' => $this->Session->read('Config.id_lang'))));
			
			$this->site_vars['meta_title']       = $pagelang['PageLang']['meta_title'];
			$this->site_vars['meta_keywords']    = $pagelang['PageLang']['meta_keywords'];
			$this->site_vars['meta_description'] = $pagelang['PageLang']['meta_description'];
			
			
        }
		
		public function buy(){
			//load data inscription
			/* On récupère la liste des pays disponibles */
			$this->loadModel('UserCountry');
			$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));

			$dbb_r = new DATABASE_CONFIG();
			$dbb_route = $dbb_r->default;
			$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
			$result_routing_page = $mysqli_conf_route->query("SELECT name from country_langs where country_id = '{$this->Session->read('Config.id_country')}' AND id_lang = '{$this->Session->read('Config.id_lang')}'");
			$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);
			$coutryname = $row_routing_page['name'];

			$result_routing_page = $mysqli_conf_route->query("SELECT user_countries_id from user_country_langs where name = '{$coutryname}'");
			$row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC);

			$this->set('selected_countries', $row_routing_page['user_countries_id']);

			//check si client auth
			$is_connected = false;
			if ($this->Auth->login()){
				$user = $this->Session->read('Auth.User');
				$role = $this->Auth->user('role');
				if($user['id'] && $role = "client"){
					$is_connected = true;
				}    
			}
			$this->set('is_connected', $is_connected);
			$this->set('giftorder_id', $this->Session->read('GiftOrderId'));
			
		}
		
		public function admin_order(){
			$this->loadModel('GiftOrder');
			 
			 $conditions = array();
			 
			//Avons-nous un filtre sur la date ??
			if($this->Session->check('Date')){
				$conditions = array_merge($conditions, array(
					'GiftOrder.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
					'GiftOrder.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
				));
			}

			
			if(isset($this->params->data['GiftOrder']) && !$this->params->data['GiftOrder']){
                $this->Session->delete('GiftOrderEmail');
				$this->Session->delete('GiftOrderEmailBenef');
				$this->Session->delete('GiftOrderNameBenef');
			 }else{
				$this->Session->write('GiftOrderEmail', $this->params->data['GiftOrder']['email']);
				$this->Session->write('GiftOrderEmailBenef', $this->params->data['GiftOrder']['email_banaf']);
				$this->Session->write('GiftOrderNameBenef', $this->params->data['GiftOrder']['nom_benef']);
			}
		
			if($this->Session->read('GiftOrderEmail')){
				$conditions = array_merge($conditions,array('User.email' => $this->Session->read('GiftOrderEmail')));
			}
			if($this->Session->read('GiftOrderEmailBenef')){
				$conditions = array_merge($conditions,array('GiftOrder.beneficiary_email' => $this->Session->read('GiftOrderEmailBenef')));
			}
			if($this->Session->read('GiftOrderNameBenef')){
				$conditions = array_merge($conditions,array('GiftOrder.beneficiary_lastname' => $this->Session->read('GiftOrderNameBenef')));
			}

			

			//On récupère les infos du dernier achat pour les clients
			$this->Paginator->settings = array(
				'fields' => array('GiftOrder.*','Gift.*', 'User.firstname', 'User.lastname', 'User.id', 'User.email'),
				'conditions' => $conditions,
				'order' => 'GiftOrder.date_add DESC',
				'paramType' => 'querystring',
				'joins' => array(
							array('table' => 'gifts',
								'alias' => 'Gift',
								'type' => 'inner',
								'conditions' => array('Gift.id = GiftOrder.gift_id')
							),
							array('table' => 'users',
								'alias' => 'User',
								'type' => 'inner',
								'conditions' => array('User.id = GiftOrder.user_id')
							),
						),
				'limit' => 15
			);

			$lastOrder = $this->Paginator->paginate($this->GiftOrder);

			$this->set(compact('lastOrder'));
		}
		
		public function show(){
			$params = $this->request->params;
			$requestData = '';
			
			$this->loadModel('User');
			$this->loadModel('GiftOrder');
			
			$hash = '';
			if(is_array($params))
				$hash = $params['hash'];

			if (empty($hash))
				$this->redirect(array('controller' => 'home', 'action' => 'index'));

			$hash = base64_decode(str_pad(strtr(urldecode($hash), '-|', '+_'), strlen($hash) % 4, '=', STR_PAD_RIGHT));
			
			//check type de page
			$gift_order_id = 0;
			$pagee = '';
			if(substr_count($hash,'e-carte-buyer-')){
				$gift_order_id = str_replace('e-carte-buyer-','',$hash);
				$pagee = 'buyer';
			}
			if(substr_count($hash,'e-carte-benef-')){
				$gift_order_id = str_replace('e-carte-benef-','',$hash);
				$pagee = 'benef';
			}

			if (empty($gift_order_id))
				$this->redirect(array('controller' => 'home', 'action' => 'index'));

			$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $gift_order_id),
					'recursive' => -1,
				));
			$this->GiftOrder->id = $gift_order_id;
			$this->GiftOrder->saveField('is_view', 1); 
			
			$user_order = $this->User->find('first', array(
					'conditions' => array('User.id' => $gift_order['GiftOrder']['user_id']),
					'recursive' => -1,
				));
			
			$this->set(compact('gift_order','user_order','pagee'));
			
		}
		
		public function postpdf(){
			$this->autoRender = false;
			$params = $this->request->data;
			
			if($params['value']){
				$this->loadModel('Gift');
				$gift = $this->Gift->find('first', array(
					'conditions' => array('Gift.id' => $params['value']),
					'recursive' => -1,
				));
				$this->Session->write('GiftPreviewPrice', $gift['Gift']['amount'].chr(128));
			}
			$this->Session->write('GiftPreviewBeneficiaire', $params['firstname']);
			$this->Session->write('GiftPreviewText', $params['text']);
			$this->jsonRender(array('return' => true));
		}
		
		public function pdf(){
			$this->autoRender = false;
			$params = $this->request->params;
			$requestData = '';
			
			$this->loadModel('User');
			$this->loadModel('GiftOrder');
			
			$hash = '';
			if(is_array($params))
				$hash = $params['hash'];

			if (empty($hash))
				$this->redirect(array('controller' => 'home', 'action' => 'index'));

			$hash = base64_decode(str_pad(strtr(urldecode($hash), '-|', '+_'), strlen($hash) % 4, '=', STR_PAD_RIGHT));
			
			$gift_order_id = 0;
			if(substr_count($hash,'e-carte-pdf-')){
				$gift_order_id = str_replace('e-carte-pdf-','',$hash);
			}
			

			if (!is_numeric($gift_order_id))
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			
			$client = '';
			$beneficiaire = '';
			$price = '';
			$message = '';
			$code = '';
			$validite = '';
			
			if($gift_order_id == 0){
				$client = 'Un ami';
				if($this->Session->read('GiftPreviewBeneficiaire'))
					$beneficiaire = $this->Session->read('GiftPreviewBeneficiaire');
				else
					$beneficiaire = '';
				if($this->Session->read('GiftPreviewPrice'))
					$price = $this->Session->read('GiftPreviewPrice');
				else
					$price = '20 '.chr(128);
				if($this->Session->read('GiftPreviewText'))
					$message = nl2br($this->Session->read('GiftPreviewText'));
				else
					$message = 'Exemple carte cadeau';
				
				
				$code = 'XXXX';
				$validite = date('d-m-Y', strtotime(' + 365 day'));
				
			}else{
				$gift_order = $this->GiftOrder->find('first', array(
					'conditions' => array('GiftOrder.id' => $gift_order_id),
					'recursive' => -1,
				));
				$user_order = $this->User->find('first', array(
					'conditions' => array('User.id' => $gift_order['GiftOrder']['user_id']),
					'recursive' => -1,
				));
				$currency = '';
				switch ($gift_order['GiftOrder']['devise']) {
					case 'EUR':
						$currency =  chr(128);
						break;
					case 'CHF':
						$currency =  "CHF";
						break;
					case 'CAD':
						$currency =  "$";
						break;
				}
				$client = $user_order['User']['firstname'];
				$beneficiaire = $gift_order['GiftOrder']['beneficiary_firstname'];
				$price = $gift_order['GiftOrder']['amount']. ' '.$currency;
				$message = nl2br($gift_order['GiftOrder']['text']);
				$code = $gift_order['GiftOrder']['code'];
				$validite = CakeTime::format($gift_order['GiftOrder']['date_validity'], '%d-%m-%Y');

			}
			
			//generer pdf

			require('../Lib/fpdf.php');
			require('../Lib/fpdi/autoload.php');
			
			// initiate FPDI
			$pdf = new Fpdi();
			
			
			// add a page
			$pdf->AddPage();
			$pdf->SetDisplayMode('real');
			// set the source file
			$pdf->setSourceFile('../webroot/media/pdf/carte-cadeau-spiriteo.pdf');
			// import page 1
			$tplIdx = $pdf->importPage(1);
			// use the imported page and place it at position 10,10 with a width of 100 mm
			$pdf->useTemplate($tplIdx, 5, 5, 200);

			// now write some text above the imported page
			$pdf->SetFont('Helvetica');
			$pdf->SetTextColor(127, 110, 170);
			$pdf->SetFontSize(12);
			$pdf->SetXY(50, 110);
			$pdf->Write(10, 'Bonjour '.$beneficiaire.',');
			$pdf->SetXY(50, 120);
			$pdf->Write(10, ucfirst($client).' vous offre cette e-carte cadeau Spiriteo');
			$pdf->SetXY(90, 130);
			$pdf->Write(10, 'd\'une valeur de ');
			$pdf->SetXY(95, 145);
			$pdf->SetFontSize(30);
			$pdf->Write(0, $price);
			$pdf->SetFontSize(12);
			$pdf->SetXY(42, 165);
			$InterLigne = 7; 
			$tab = explode('<br />',utf8_decode($message));
			$y = 165 - $InterLigne;
			foreach($tab as $txt){
				//$pdf->ln(10); 
				$pdf->MultiCell(126,$InterLigne,$txt,0,'J',0,15);
				$y += $InterLigne;
				$pdf->SetXY(42, $y);
			}
			
			//$pdf->Cell(126,45,$message,1,1,'C');
			$pdf->SetXY(115, 240);
			$pdf->Write(10, $code);
			$pdf->SetXY(115, 247);
			$pdf->Write(10, $validite);
			
			$pdf->Output('I','carte-cadeau-Spiriteo.pdf');
			
		}
	
		
	public function admin_export_order(){
		set_time_limit ( 0 );
		$this->autoRender = false;

		 $filename = Configure::read('Site.pathExport').'/export.csv';
        //On supprime l'ancien fichier export, s'il existe
        if(file_exists($filename))
            unlink($filename);
		
		//Si date
        if($this->Session->check('Date'))
            $label = 'export_'.CakeTime::format($this->Session->read('Date.start'), '%d-%m-%Y').'_'.CakeTime::format($this->Session->read('Date.end'), '%d-%m-%Y');
        else
            $label = 'all_export';


        $fp = fopen($filename, 'w+');
        fputs($fp, "\xEF\xBB\xBF");
		
		 $conditions = array();
			 
			//Avons-nous un filtre sur la date ??
			if($this->Session->check('Date')){
				$conditions = array_merge($conditions, array(
					'GiftOrder.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
					'GiftOrder.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
				));
			}
		
		
		$this->loadModel('GiftOrder');
		$this->loadModel('OrderPaypaltransaction');
		$this->loadModel('OrderHipaytransaction');
		
		$allDatas = $this->GiftOrder->find('all', array(
            'fields'        => array('GiftOrder.*','Gift.*', 'User.firstname', 'User.lastname', 'User.id', 'User.email'),
            'conditions'    => $conditions,
            'order'         => 'GiftOrder.date_add DESC',
			  'joins' => array(
							array('table' => 'gifts',
								'alias' => 'Gift',
								'type' => 'inner',
								'conditions' => array('Gift.id = GiftOrder.gift_id')
							),
							array('table' => 'users',
								'alias' => 'User',
								'type' => 'inner',
								'conditions' => array('User.id = GiftOrder.user_id')
							),
						),
        ));
		
		
		
		 //Si pas de données
        if(empty($allDatas)){
            $this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
            $source = $this->referer();
            if(empty($source))
                $this->redirect(array('controller' => 'gifts', 'action' => 'order', 'admin' => true), false);
            else
                $this->redirect($source);
        }
		
		
		$indice = 0;
		foreach($allDatas as $row){
			
			$paypal = $this->OrderPaypaltransaction->find('first', array(
					'conditions' => array('cart_id' => '999999'.$row['GiftOrder']['id']),
					'recursive' => -1
				));
			
			$hipay = $this->OrderHipaytransaction->find('first', array(
					'conditions' => array('cart_id' => '999999'.$row['GiftOrder']['id']),
					'recursive' => -1
				));
			
			$payment_type = '';
			$transaction = '';
			if($hipay){
				$payment_type = 'Hipay';
				$transaction = $hipay['OrderHipaytransaction']['transaction'];
			}
			
			if($paypal){
				$payment_type = 'Paypal';
				$transaction = $paypal['OrderPaypaltransaction']['payment_transactionid'];
			}
		
			$line = array(
                'id'      => $row['GiftOrder']['id'],
                'client_id'      => $row['GiftOrder']['user_id'],
                'client_firstname'      => $row['User']['firstname'],
              'client_lastname'      => $row['User']['lastname'],
              'client_email'      => $row['User']['email'],
                'beneficiary_id'         => $row['GiftOrder']['beneficiary_id'],
                'beneficiary_firstname'         => $row['GiftOrder']['beneficiary_firstname'],
				'beneficiary_lastname'         => $row['GiftOrder']['beneficiary_lastname'],
				'beneficiary_email'         => $row['GiftOrder']['beneficiary_email'],
				'date_add'     => Tools::dateUser('Europe/Paris', $row['GiftOrder']['date_add']),
                'send_date'     => Tools::dateUser('Europe/Paris',$row['GiftOrder']['send_date']),
				'date_validity'     => Tools::dateUser('Europe/Paris',$row['GiftOrder']['date_validity']),
				'date_use'     => Tools::dateUser('Europe/Paris',$row['GiftOrder']['date_use']),
				'code'         => $row['GiftOrder']['code'],
				'amount'         => $row['GiftOrder']['amount'],
				'devise'         => $row['GiftOrder']['devise'],
				'solde'         => $row['GiftOrder']['sold'],
				'payment_type'         => $payment_type ,
				'transaction'         => $transaction,
				
            );

            if($indice == 0){
                fputcsv($fp, array_keys($line), ';', '"');
            }
           fputcsv($fp, array_values($line), ';', '"');
			$indice ++;
		}
		
		 fclose($fp);
         $this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
        return $this->response;
	}
		
}
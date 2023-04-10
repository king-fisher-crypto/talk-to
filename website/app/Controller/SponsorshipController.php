<?php
App::uses('AppController', 'Controller');


class SponsorshipController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Sponsorship','SponsorshipRule');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'sponsorship')));
		public $hash_key = 'ssponsor';
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow('parrainage','deactivateSponsorship','activateSponsorship');
    }
	
	public function admin_create(){

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['SponsorshipRule'] = Tools::checkFormField($requestData['SponsorshipRule'],
                    array('type_user', 'palier', 'data'),
                    array('type_user', 'palier', 'data')
                );
                if($requestData['SponsorshipRule'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

				$requestData['SponsorshipRule']['date_add'] = date('Y-m-d H:i:s');
                    $this->SponsorshipRule->create();
                    if($this->SponsorshipRule->save($requestData)){
                        $this->Session->setFlash(__('La regle a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'sponsorship', 'action' => 'rules', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement de la regle'),'flash_warning');
                
            }
     }
	
	public function admin_edit($id){
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['SponsorshipRule'] = Tools::checkFormField($requestData['SponsorshipRule'],
                    array('type_user', 'palier', 'data'),
                    array('type_user', 'palier', 'data')
                );
                if($requestData['SponsorshipRule'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$requestData['SponsorshipRule']['type_user'] = "'".$requestData['SponsorshipRule']['type_user']."'";
				$requestData['SponsorshipRule']['palier'] = "'".$requestData['SponsorshipRule']['palier']."'";
				$requestData['SponsorshipRule']['data'] = "'".$requestData['SponsorshipRule']['data']."'";

                //Si la modif a réussi
                    if($this->SponsorshipRule->updateAll(
                        $requestData['SponsorshipRule'],
                        array('SponsorshipRule.id' => $id))
                    ){
                        $this->Session->setFlash(__('La regle a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'sponsorship', 'action' => 'rules', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification de la regle'),'flash_warning');
                }else{

            $rule = $this->SponsorshipRule->find('first', array(
                'conditions' => array('SponsorshipRule.id' => $id),
                'recursive' => -1
            ));


            if(empty($rule)){
                $this->Session->setFlash(__('Regle introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'sponsorship', 'action' => 'rules', 'admin' => true), false);
            }
            //On insère les données
            $this->request->data = $rule;
            $this->set(array('edit' => true, 'sponsorshiprule' => $rule));
            $this->render('admin_edit');
				}
        }


	
	public function admin_rules(){
		$this->Paginator->settings = array(
                'order' => array('SponsorshipRule.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $sponsorships = $this->Paginator->paginate($this->SponsorshipRule);

            $this->set(compact('sponsorships'));
	}
	
	public function admin_parrainage_view(){
		
		$parms = $this->params;
		
		$condition = array();
		
		if(isset($this->params->data['Sponsorship']) && !$this->params->data['Sponsorship']){
                $this->Session->delete('SponsorshipIp');
			$this->Session->delete('SponsorshipEmail');
			$this->Session->delete('SponsorshipEmailParrain');
			$this->Session->delete('SponsorshipPseudo');
			$this->Session->delete('SponsorshipFirstname');
         }else{
			if(isset($this->params->data['Sponsorship']) && $this->params->data['Sponsorship'])
                $this->Session->write('SponsorshipIp', $this->params->data['Sponsorship']['ip']);
			$this->Session->write('SponsorshipEmail', $this->params->data['Sponsorship']['email']);
			$this->Session->write('SponsorshipEmailParrain', $this->params->data['Sponsorship']['email_parrain']);
			$this->Session->write('SponsorshipPseudo', $this->params->data['Sponsorship']['pseudo']);
			$this->Session->write('SponsorshipFirstname', $this->params->data['Sponsorship']['firstname']);
        }
		
		if($this->Session->read('SponsorshipIp')){
			$condition = array_merge($condition,array('Sponsorship.IP' => $this->Session->read('SponsorshipIp')));
		}
		if($this->Session->read('SponsorshipEmail')){
			$condition = array_merge($condition,array('Sponsorship.email' => $this->Session->read('SponsorshipEmail')));
		}
		if($this->Session->read('SponsorshipEmailParrain')){
			$condition = array_merge($condition,array('Parrain.email' => $this->Session->read('SponsorshipEmailParrain')));
		}
		if($this->Session->read('SponsorshipPseudo')){
			$condition = array_merge($condition,array('Parrain.pseudo' => $this->Session->read('SponsorshipPseudo')));
		}
		if($this->Session->read('SponsorshipFirstname')){
			$condition = array_merge($condition,array('Customer.firstname' => $this->Session->read('SponsorshipFirstname')));
		}
		
		
		
		$this->Paginator->settings = array(
				 'fields'     => array('Customer.id','Customer.firstname','Customer.date_add','Customer.source','Parrain.id','Parrain.firstname','Parrain.pseudo','Parrain.email','Sponsorship.id','Sponsorship.date_add','Sponsorship.IP','Sponsorship.type_user','Sponsorship.email','Sponsorship.status','Sponsorship.bonus','Sponsorship.bonus_type','Sponsorship.is_block','Sponsorship.date_block','Sponsorship.id_customer','Sponsorship.source','(select sum(H.credits) from user_credit_history H where H.user_id = Sponsorship.id_customer and H.date_start>=Sponsorship.date_add) as total '),
				'conditions' => $condition,
                'order' => array('Sponsorship.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins'      => array(
                        array(
                            'table' => 'users',
                            'alias' => 'Parrain',
                            'type'  => 'left',
                            'conditions' => array(
                                'Parrain.id = Sponsorship.user_id'
                            )
                        ),
					 
					 array(
                            'table' => 'users',
                            'alias' => 'Customer',
                            'type'  => 'left',
                            'conditions' => array(
                                'Customer.id = Sponsorship.id_customer'
                            )
                        )
                    ),
                'limit' => 25
            );

            $sponsorships = $this->Paginator->paginate($this->Sponsorship);
		    $this->loadModel('Order');
			//recup achat filleul si status = 2;
			foreach($sponsorships as &$sponsor){
				$sponsor["Sponsorship"]["filleul_palier"] = 0;
				if($sponsor["Sponsorship"]["status"] >= 2 && $sponsor["Sponsorship"]["id_customer"]){
					$orders = $this->Order->find('all', array(
						'conditions'    => array('user_id' => $sponsor["Sponsorship"]["id_customer"], 'valid' => 1),
						'recursive'     => -1,
					));
					foreach($orders as $order){
						$sponsor["Sponsorship"]["filleul_palier"] += $order["Order"]["total"];	
					}
				}
			}
		
			/*$this->loadModel('User');
			$this->loadModel('UserIp');
			//parcourir pour trouver les duplicate compte pour cette pagination
			$tab_final = array();
			foreach($sponsorships as $sponsor){
				$tab_final[] = $sponsor;
				$status = $sponsor["Sponsorship"]["status"];
				if($status == 5 || $status == 7){//duplicate
					$new_line = array();
					$new_line["Customer"] = array();
					$new_line["Parrain"] = array();
					$new_line["Sponsorship"] = array();
					
					$last_ip = $this->UserIp->find('first', array(
						'conditions'    => array('IP' => $sponsor['Sponsorship']['IP'], 'user_id !=' => $sponsor['Sponsorship']['id_customer']),
						'recursive'     => -1,
						'order'			=> array('date_conn DESC')
					));
					if($last_ip){
						$customer_duplicate = $this->User->find('first', array(
							'conditions'    => array('User.id' => $last_ip['UserIp']['user_id']),
							'recursive'     => -1
						));
						$new_line["Customer"]["id"] = $customer_duplicate["User"]["id"];
						$new_line["Customer"]["firstname"] = $customer_duplicate["User"]["firstname"];
						$new_line["Customer"]["date_add"] = $customer_duplicate["User"]["date_add"];
						$new_line["Customer"]["source"] = '';

						$new_line["Parrain"]["id"] = $sponsor["Parrain"]["id"];
						$new_line["Parrain"]["firstname"] = $sponsor["Parrain"]["firstname"];
						$new_line["Parrain"]["pseudo"] = $sponsor["Parrain"]["pseudo"];

						$new_line["Sponsorship"]["id"] = $sponsor["Sponsorship"]["id"];
						$new_line["Sponsorship"]["date_add"] = $sponsor["Sponsorship"]["date_add"];
						$new_line["Sponsorship"]["IP"] = $sponsor["Sponsorship"]["IP"];
						$new_line["Sponsorship"]["type_user"] = $sponsor["Sponsorship"]["type_user"];
						$new_line["Sponsorship"]["email"] = $customer_duplicate["User"]["email"];
						$new_line["Sponsorship"]["status"] = $sponsor["Sponsorship"]["status"];
						$new_line["Sponsorship"]["bonus"] = '';
						$new_line["Sponsorship"]["bonus_type"] = '';
						$new_line["Sponsorship"]["is_block"] = '';
						$new_line["Sponsorship"]["date_block"] = '';
						$new_line["Sponsorship"]["id_customer"] = 0;
						$new_line["Sponsorship"]["source"] = '';

						$tab_final[] = $new_line;
					}
				}
				
			}
			$sponsorships = $tab_final;*/
            $this->set(compact('sponsorships'));
	}
	
	
	
	
	public function unlock(){
		$user = $this->Auth->user();
        if (empty($user) || (!empty($user) && $user['role'] !== 'client'))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		$this->loadModel('User');
		$sponsorships = $this->Sponsorship->find('all', array(
							'conditions'    => array('Sponsorship.user_id' => $user['id'], 'Sponsorship.is_recup' => 0, 'Sponsorship.status' => 3),
							'recursive'     => -1
						));
		$add_seconds = 0;
		foreach($sponsorships as $sponsor){
			
			switch($sponsor['Sponsorship']['bonus_type']){
				case 'min':
					$add_seconds = $add_seconds + ($sponsor['Sponsorship']['bonus'] * 60);
				case 'euros':
					$add_seconds = $add_seconds + (intval($sponsor['Sponsorship']['bonus'] * 600 / 19.5));
				break;
			}
			$this->Sponsorship->updateAll(array('status'=>4,'is_recup'=>1,'date_recup'=>'NOW()'), array('Sponsorship.id' => $sponsor['Sponsorship']['id']));
		}
		
		$this->User->id = $user['id'];
		$credits = $this->User->field('credit') + $add_seconds;
		$this->User->saveField('credit', $credits);

		$this->Session->setFlash(__('Votre récompense a été ajouté à vos crédits.'), 'flash_success');
		$this->redirect(array('controller' => 'sponsorship', 'action' => 'client'));
		
	}
	
	public function parrainage(){
	    
	    }
	public function parrainage_original(){
	    
	  
		$params = $this->request->params;
		$requestData = '';
		if ($this->request->is('post')){
			$requestData = $this->request->data;
		}
		if($this->Session->read('previousPagePostData')){
			$requestData = $this->Session->read('previousPagePostData');
		}
		
		if ($requestData){
			
				$is_sponsorship = 0;
				if(substr_count($requestData['User']['source_ins'],'parrainage'))$is_sponsorship = 1;
				$this->set(array('country' => $requestData['User']['country_id'] , 'email2' => $requestData['User']['email2'], 'email' => $requestData['User']['email_subscribe'], 'firstname' => $requestData['User']['firstname'],'is_sponsorship' => $is_sponsorship,'source_ins' => $requestData['User']['source_ins'],'sponsor_id' => $requestData['User']['sponsor_id'],'sponsor_user_id' => $requestData['User']['sponsor_user_id'],'sponsor_email' => $requestData['User']['sponsor_email']));

		}else{
				$this->set(array('country' => '' , 'email2' => '', 'email' => '', 'firstname' => '', 'is_sponsorship' => '','source_ins'=> '','sponsor_id' => '','sponsor_user_id' => '','sponsor_email' => ''));
		}
		
		$this->loadModel('User');
		$hash = '';
		if(is_array($params))
			$hash = $params['hash'];
		
		if (empty($hash))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		$hash = $this->decrypter($hash);
		$test = utf8_decode($hash);
		if(!$test || substr_count($test,'?'))
			$this->redirect(array('controller' => 'home', 'action' => 'index'));

		$type_user = '';
		$sponsor_id = 0;
		$sponsor_user_id = 0;
		$sponsor_email = '';
		
		if(is_numeric($hash)){
			$conditions = array(
								'User.id' => $hash
					);
			$parrain = $this->User->find('first',array('conditions' => $conditions));
			$type_user = $parrain['User']['role'];
			$sponsor_user_id = $parrain['User']['id'];
			
		}else{
			$conditions = array(
								'Sponsorship.email' => $hash
					);
			$sponsor = $this->Sponsorship->find('first',array('conditions' => $conditions));
			$type_user = $sponsor['Sponsorship']['type_user'];
			$sponsor_id = $sponsor['Sponsorship']['id'];
			$sponsor_user_id = $sponsor['Sponsorship']['user_id'];
			$sponsor_email = $sponsor['Sponsorship']['email'];
		}
		
		if (!$type_user)
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		$this->set(compact('type_user','sponsor_id','sponsor_user_id', 'sponsor_email'));
		
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
		if($type_user == 'client'){
			$this->site_vars['meta_title']          = __('Parrainage agents ► Je te recommande Spiriteo !');
			$this->site_vars['meta_keywords']       = '';
			$this->site_vars['meta_description']    = __('Deviens mon filleul et obtient 5 min de agents offerte sur Spiriteo avec les meilleurs voyants du web. Spiriteo, la agents privée de qualité - Par tél, chat ou mail 24h/24.');
			$this->request->params['action'] = 'parrainage_client';
		}else{
			$this->site_vars['meta_title']          = __('Retrouve-moi sur Spiriteo et obtient 5 mn de agents offerte !');
			$this->site_vars['meta_keywords']       = '';
			$this->site_vars['meta_description']    = __('Un professionnel de la agents de votre réseau vous invite à le retrouver sur Spiriteo, la agents privée de qualité par tél, chat, mail 24h/24.');
			$this->request->params['action'] = 'parrainage_agent';
		}
		
	}
	
	protected function crypter($maChaineACrypter){
		$maCleDeCryptage = md5($this->hash_key);
		$letter = -1;
		$newstr = '';
		$strlen = strlen($maChaineACrypter);
		for($i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineACrypter{$i}) + ord($maCleDeCryptage{$letter});
			if ( $neword > 255 ){
				$neword -= 256;
			}
			$newstr .= chr($neword);
		}
		$k = $this->base64url_encode($newstr);
		return $k;
	}

	public function decrypter($maChaineCrypter){
		$maCleDeCryptage = md5($this->hash_key);
		$letter = -1;
		$newstr = '';
		$maChaineCrypter = $this->base64url_decode($maChaineCrypter);
		$strlen = strlen($maChaineCrypter);
		for ( $i = 0; $i < $strlen; $i++ ){
			$letter++;
			if ( $letter > 31 ){
				$letter = 0;
			}
			$neword = ord($maChaineCrypter{$i}) - ord($maCleDeCryptage{$letter});
			if ( $neword < 1 ){
				$neword += 256;
			}
			$newstr .= chr($neword);
		}
		return $newstr;
	}
	
	public function base64url_encode($data) { 
	  return rtrim(strtr(base64_encode($data), '+_', '-|'), '='); 
	} 

	public function base64url_decode($data) { 
	  return base64_decode(str_pad(strtr(urldecode($data), '-|', '+_'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	} 
	
	public function track(){
			$query = $this->request->query;
			if($query["i"]){
				$this->Sponsorship->id = $query["i"];
				$this->Sponsorship->saveField('status', 1);
			}
			
			
			
			// Create an image, 1x1 pixel in size
			  $im=imagecreate(1,1);

			  // Set the background colour
			  $white=imagecolorallocate($im,255,255,255);

			  // Allocate the background colour
			  imagesetpixel($im,1,1,$white);

			  // Set the image type
			  header("content-type:image/jpg");

			  // Create a JPEG file from the image
			  imagejpeg($im);

			  // Free memory associated with the image
			  imagedestroy($im);
			exit;
		}
	
	public function deactivateSponsorship(){
		 if($this->request->is('ajax')){
			$this->loadModel('Sponsorship');
			 
			 if(!isset($this->request->data['id_sponsorship'])){
				return;
			 }else{
				 $id_sponsorship = $this->request->data['id_sponsorship'];
			 }
			 
			 
			 $this->Sponsorship->id = $id_sponsorship;
			 $this->Sponsorship->saveField('status', 6);
			 $this->Sponsorship->saveField('date_block', date('Y-m-d H:i:s'));
			 $this->jsonRender(array(
                'return'          => true,
            ));
			 
		}
		
	}
	
	public function activateSponsorship(){
		if($this->request->is('ajax')){
			$this->loadModel('Sponsorship');
			 
			 if(!isset($this->request->data['id_sponsorship'])){
				return;
			 }else{
				 $id_sponsorship = $this->request->data['id_sponsorship'];
			 }
			 
			 
			 $this->Sponsorship->id = $id_sponsorship;
			
			 $this->Sponsorship->saveField('status', 3);
			 $this->jsonRender(array(
                'return'          => true,
            ));
			 
		}
		
	}
	
	public function client(){
		$user = $this->Auth->user();
        if (empty($user) || (!empty($user) && $user['role'] !== 'client'))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		
		if($this->request->is('post')){
			$this->loadModel('User');
			$this->loadModel('Domain');
			
            $requestData = $this->request->data;
            //On check le formulaire
            if(!isset($requestData['Sponsorship']['email1'])){
                $this->Session->setFlash(__('Merci de renseigner un email.'), 'flash_warning');
            }
			
			$conditions = array(
								'User.id' => $this->Auth->user('id'),
			);
			$client = $this->User->find('first',array('conditions' => $conditions));
			
			$conditions = array(
								'SponsorshipRule.type_user' => 'client',
			);
			$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
			
            //On envoie les invites
			$list_email = array();
			if(isset($requestData['Sponsorship']['email1'])){
				array_push($list_email,$requestData['Sponsorship']['email1']);
			}
			if(isset($requestData['Sponsorship']['email2'])){
				array_push($list_email,$requestData['Sponsorship']['email2']);
			}
			if(isset($requestData['Sponsorship']['email3'])){
				array_push($list_email,$requestData['Sponsorship']['email3']);
			}
			$is_send = true;
			if(is_array($list_email)){
				foreach($list_email as $email){
					if($email){
						//verifier si email pas present
						$conditions = array(
									'User.email' => $email
						);
						$is_client = $this->User->find('first',array('conditions' => $conditions));
						$conditions = array(
									'Sponsorship.email' => $email
						);
						$is_sponsor_send = $this->Sponsorship->find('first',array('conditions' => $conditions));
						
						if($is_client){
							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'client';
							$saveData['Sponsorship']['user_id'] = $client['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 10;
							$saveData['Sponsorship']['hash'] = '';

							$this->Sponsorship->save($saveData);
						}
						
						if(!$is_client && !$is_sponsor_send){
							$hash =  $this->crypter($email);

							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'client';
							$saveData['Sponsorship']['user_id'] = $client['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 0;
							$saveData['Sponsorship']['hash'] = $hash;

							$this->Sponsorship->save($saveData);

							$url = Router::url(array('controller' => 'sponsorship', 'action' => 'parrainage-'.$hash),true);
							
							$conditions = array(
								'Domain.id' => $client['User']['domain_id'],
							);

							$domain = $this->Domain->find('first',array('conditions' => $conditions));
							if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'fr.spiriteo.com';
							
							$url_pixel_view = '<img src="https://'.$domain['Domain']['domain'].'/sponsorship/track?i='.$this->Sponsorship->id.'" />';

							$is_send = $this->sendCmsTemplatePublic(325, (int)$client['User']['lang_id'], $email, array(
									'CLIENT' =>$client['User']['firstname'],
									'URL' =>$url,
									'PIXEL' => $url_pixel_view,
									'EMAIL_PARRAIN' => $client['User']['email']
								));
						}
					}
				}
			}
			
			
            if($is_send){
                $this->Session->setFlash(__('Invitation(s) envoyée(s).'), 'flash_success');
            }
            else{
                $this->Session->setFlash(__('Echec lors de l\'envoi.'), 'flash_warning');
            }
        }
		
		
		$hash = $this->crypter($this->Auth->user('id'));
		$url_share = Router::url(array('controller' => 'sponsorship', 'action' => 'parrainage-'.$hash),true);
		
		$conditions = array(
								'SponsorshipRule.type_user' => 'client',
			);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
		$sponsor_gain = $rule['SponsorshipRule']['data'];
		$sponsor_palier = $rule['SponsorshipRule']['palier'];
		$this->set(compact('url_share','sponsor_gain', 'sponsor_palier'));
		
	}
	
	public function client_gain(){
		$user = $this->Auth->user();
        if (empty($user) || (!empty($user) && $user['role'] !== 'client'))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'client',
			'Sponsorship.bonus >' => 0,
			'Sponsorship.status' => 3,
			'Sponsorship.is_recup' => 0,
			);
		$btn_class = ' disabled';
		$is_bonus = $this->Sponsorship->find('first',array('conditions' => $conditions));
		if($is_bonus)
			$btn_class = ' enabled';
		
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'client',
			'Sponsorship.status >=' => 1,
			'Sponsorship.status <' => 5,
			);
		$accepts = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_accept = count($accepts);
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'client',
			'Sponsorship.status >=' => 2,
			'Sponsorship.status <' => 5,
			);
		$dones = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_done = count($dones);
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'client',
			'Sponsorship.status >=' => 3,
			'Sponsorship.status <' => 5,
			);
		$wins = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_win = count($wins);
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'client',
			'Sponsorship.status >=' => 3,
			'Sponsorship.status <' => 5,
			'Sponsorship.is_recup' => 0,
			);
		$wins = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_win_wait = 0;
		$nb_win_wait_type = '';
		foreach($wins as $win){
			$nb_win_wait += $win['Sponsorship']['bonus']; 
			$nb_win_wait_type = $win['Sponsorship']['bonus_type']; 
		}
		if($nb_win_wait)$nb_win_wait = $nb_win_wait. ' '.$nb_win_wait_type;
		$conditions = array(
								'SponsorshipRule.type_user' => 'client',
			);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
		$sponsor_gain = $rule['SponsorshipRule']['data'];
		$sponsor_palier = $rule['SponsorshipRule']['palier'];
		$this->set(compact('btn_class','nb_accept','nb_done','nb_win','nb_win_wait','sponsor_gain','sponsor_palier'));
	}
	
	public function agent(){
		$user = $this->Auth->user();
		/*
        if (empty($user) || (!empty($user) && $user['role'] !== 'agent'))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		*/
		
		if($this->request->is('post')){
			$this->loadModel('User');
			$this->loadModel('Domain');
			
            $requestData = $this->request->data;
            //On check le formulaire
            if(!isset($requestData['Sponsorship']['email1'])){
                $this->Session->setFlash(__('Merci de renseigner un email.'), 'flash_warning');
            }
			
			$conditions = array(
								'User.id' => $this->Auth->user('id'),
			);
			$agent = $this->User->find('first',array('conditions' => $conditions));
			
			$conditions = array(
								'SponsorshipRule.type_user' => 'agent',
			);
			$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
			
            //On envoie les invites
			$list_email = array();
			if(isset($requestData['Sponsorship']['email1'])){
				array_push($list_email,$requestData['Sponsorship']['email1']);
			}
			if(isset($requestData['Sponsorship']['email2'])){
				array_push($list_email,$requestData['Sponsorship']['email2']);
			}
			if(isset($requestData['Sponsorship']['email3'])){
				array_push($list_email,$requestData['Sponsorship']['email3']);
			}
			$is_send = true;
			if(is_array($list_email)){
				foreach($list_email as $email){
					if($email){
						//verifier si email pas present
						$conditions = array(
									'User.email' => $email
						);
						$is_client = $this->User->find('first',array('conditions' => $conditions));
						$conditions = array(
									'Sponsorship.email' => $email
						);
						$is_sponsor_send = $this->Sponsorship->find('first',array('conditions' => $conditions));
						
						if($is_client){
							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 10;
							$saveData['Sponsorship']['hash'] = '';

							$this->Sponsorship->save($saveData);
						}
						
						
						if(!$is_client && !$is_sponsor_send){
							$hash =  $this->crypter($email);

							$this->Sponsorship->create();
							$saveData = array();
							$saveData['Sponsorship'] = array();
							$saveData['Sponsorship']['date_add'] = date('Y-m-d H:i:s');
							$saveData['Sponsorship']['id_rules'] = $rule['SponsorshipRule']['id'];
							$saveData['Sponsorship']['type_user'] = 'agent';
							$saveData['Sponsorship']['user_id'] = $agent['User']['id'];
							$saveData['Sponsorship']['source'] = 'email';
							$saveData['Sponsorship']['email'] = $email;
							$saveData['Sponsorship']['status'] = 0;
							$saveData['Sponsorship']['hash'] = $hash;

							$this->Sponsorship->save($saveData);

							$url = Router::url(array('controller' => 'sponsorship', 'action' => 'parrainage-'.$hash),true);
							
							$conditions = array(
								'Domain.id' => $agent['User']['domain_id'],
							);

							$domain = $this->Domain->find('first',array('conditions' => $conditions));
							if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'fr.spiriteo.com';
							
							$url_pixel_view = '<img src="https://'.$domain['Domain']['domain'].'/sponsorship/track?i='.$this->Sponsorship->id.'" />';

							$is_send = $this->sendCmsTemplatePublic(327, (int)$agent['User']['lang_id'], $email, array(
									'AGENT' =>$agent['User']['pseudo'],
									'URL' =>$url,
									'PIXEL' =>$url_pixel_view,
									'PSEUDO_EXPERT_PARRAIN' => $agent['User']['pseudo']
								));
						}
					}
				}
			}
			
			
            if($is_send){
                $this->Session->setFlash(__('Invitation(s) envoyée(s).'), 'flash_success');
            }
            else{
                $this->Session->setFlash(__('Echec lors de l\'envoi.'), 'flash_warning');
            }
        }
		
		
		$hash = $this->crypter($this->Auth->user('id'));
		$url_share = Router::url(array('controller' => 'sponsorship', 'action' => 'parrainage-'.$hash),true);
		
		$conditions = array(
								'SponsorshipRule.type_user' => 'agent',
			);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
		$sponsor_gain = 10;// $rule['SponsorshipRule']['data'];
		$sponsor_palier = $rule['SponsorshipRule']['palier'];
		$this->set(compact('url_share','sponsor_gain', 'sponsor_palier'));
	}
	
	public function agent_gain(){
		$user = $this->Auth->user();
        if (empty($user) || (!empty($user) && $user['role'] !== 'agent'))
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
		
		$this->loadModel('UserCreditLastHistory');
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'agent',
			'Sponsorship.status >=' => 1,
			'Sponsorship.status <=' => 4,
			);
		$accepts = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_accept = count($accepts);
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'agent',
			'Sponsorship.status >=' => 2,
			'Sponsorship.status <=' => 4,
			);
		$dones = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_done = count($dones);
		$conditions = array(
			'Sponsorship.user_id' => $this->Auth->user('id'),
			'Sponsorship.type_user' => 'agent',
			'Sponsorship.status >=' => 3,
			'Sponsorship.status <=' => 4,
			/*'Sponsorship.is_recup' => 1,*/
			);
		$wins = $this->Sponsorship->find('all',array('conditions' => $conditions));
		$nb_win = 0;
		$nb_win_wait = 0;
		$nb_win_wait_type = '';
		
		foreach($wins as $win){
			$total = 0;
			switch($win['Sponsorship']['bonus_type']){
					 case 'euros':
						$lastComs = $this->UserCreditLastHistory->find('all', array(
							'conditions'    => array('UserCreditLastHistory.users_id' => $win['Sponsorship']['id_customer'], 
													 'UserCreditLastHistory.date_start >=' => date('Y-m-01 00:00:00'),
													 'UserCreditLastHistory.is_factured' => 1,
													 'UserCreditLastHistory.date_start >=' => $win['Sponsorship']['date_add']),
									
							'recursive'     => -1
						));
						foreach($lastComs as $comm){
							$total = $total + $comm['UserCreditLastHistory']['credits'];
						}
					break;
			}
			$nb_win_wait += $win['Sponsorship']['bonus']/60  * $total; 
			$nb_win_wait_type = $win['Sponsorship']['bonus_type']; 
		}
		if($nb_win_wait > 0)$nb_win = number_format($nb_win_wait,2,',',' ').' €';//.$nb_win_wait_type;
		$conditions = array(
								'SponsorshipRule.type_user' => 'agent',
			);
		$rule = $this->SponsorshipRule->find('first',array('conditions' => $conditions));
		$sponsor_gain = 10;//$rule['SponsorshipRule']['data'];
		$sponsor_palier = $rule['SponsorshipRule']['palier'];
		$this->set(compact('nb_accept','nb_done','nb_win','sponsor_gain'));
	}
}
<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class VouchersController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Voucher');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'vouchers')));

        public function beforeFilter() {

            parent::beforeFilter();
        }

        public function admin_create(){
            $this->set('products', $this->get_admin_products());
            $this->set('countries', $this->get_admin_countries());

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;


                //On vérifie les champs du formulaire
                $requestData['Voucher'] = Tools::checkFormField($requestData['Voucher'],
                    array('code', 'title','label_fr','label_be', 'label_ch', 'label_lu', 'label_ca', 'validity_start', 'validity_end', 'credit', 'amount','percent','number_use','number_use_by_user','active','population', 'product_ids', 'allproducts','allcountries','ips','public','show','customer','buyer','nobuyer',  'file'),//'buy_only',
                    array('code', 'title', 'validity_start', 'validity_end')
                );
                if($requestData['Voucher'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
                if ((int)$requestData['Voucher']['credit'] == 0 && (float)$requestData['Voucher']['amount'] == 0 && (int)$requestData['Voucher']['percent'] == 0){
                    $this->Session->setFlash(__('Veuillez saisir un nombre de crédit, un montant de réduction ou une remise en pourcentage'),'flash_error');
                    return;
                }

                $cntRemise = 0;
                if ((int)$requestData['Voucher']['credit'] > 0)$cntRemise++;
                if ((float)$requestData['Voucher']['amount'] > 0)$cntRemise++;
                if ((int)$requestData['Voucher']['percent'] > 0)$cntRemise++;

                if ($cntRemise > 1){
                    $this->Session->setFlash(__('Un bon de réduction ne peut avoir qu\'un seul mode de remise : crédits, montant ou pourcentage'),'flash_error');
                    return;
                }

                if ((int)$requestData['Voucher']['percent'] > 100){
                    $this->Session->setFlash(__('La remise en % ne peut dépasser 100%'),'flash_error');
                    return;
                }

                //Restructuration des données
                //$requestData['Voucher'] = $this->initData($requestData['Voucher']);
                /* On traite les produits */
                if (isset($requestData['Voucher']['allproducts']) && $requestData['Voucher']['allproducts'] == 'all'){
                    unset($requestData['Voucher']['allproducts']);
                    $requestData['Voucher']['product_ids'] = 'all';
                }else{
                    unset($requestData['Voucher']['allproducts']);
                    if (isset($requestData['product'])){
                        $requestData['Voucher']['product_ids'] = implode(",", array_keys($requestData['product']));
                    }
                }

                /* On traite les pays */
                if (isset($requestData['Voucher']['allcountries']) && $requestData['Voucher']['allcountries'] == 'all'){
                    unset($requestData['Voucher']['allcountries']);
                    $requestData['Voucher']['country_ids'] = 'all';
                }else{
                    unset($requestData['Voucher']['allcountries']);
                    if (isset($requestData['country'])){
                        $requestData['Voucher']['country_ids'] = implode(",", array_keys($requestData['country']));
                    }
                }
				
				/* On traite population par import csv*/

					if($this->isUploadedFile($requestData['Voucher']['file'])){
						//$csv = file_get_contents($requestData['Voucher']['file']['tmp_name']); 
						$file = fopen($requestData['Voucher']['file']['tmp_name'] , r );
						$dsatz = array();
							while (($result = fgetcsv($file, 10000, ";")) !== false)
							{
								$dsatz[] = $result;
							}
						if(count($dsatz < 2)){
							$line= preg_replace('/\r\n|\n\r|\n|\r/', '#', $dsatz[0][0]);
							$listcut = explode('#',$line);
							$dsatz = array();
							foreach($listcut as $cut){
								$dsatz[] = array($cut);
							}
						}
						
							$ListCodes = array();
							foreach ($dsatz as $key => $number) {

								$id = '';
								$codeclient = new StdClass();
									foreach ($number as $k => $content) {
										if($k == 0){
											array_push($ListCodes, $content);
										}
									}
							}
							$requestData['Voucher']['population'] = implode(',',$ListCodes);
							
					}
				unset($requestData['Voucher']['file']);
				$requestData['Voucher']['user_id'] = $this->Auth->user('id');

                //On check les données du coupon
                if($this->checkCoupon($requestData['Voucher'])){
                    //Restructure les données
                    $requestData['Voucher'] = $this->initData($requestData['Voucher']);
                    $this->Voucher->create();
                    if($this->Voucher->save($requestData)){
                        $this->Session->setFlash(__('Le coupon a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement du coupon'),'flash_warning');
                }else
                    return;
            }



        }
        private function get_admin_countries()
        {
            $this->loadModel('Country');
            $rows = $this->Country->find("all");

            $countries = array();
            foreach ($rows AS $country){
                $countries[$country['Country']['id']] = $country['CountryLang']['0']['name'];
            }
            return $countries;
        }
        private function get_admin_products()
        {
            /* Produits */
            $this->loadModel('Product');
            $rows = $this->Product->find("all", array('recursive' => 2, 'order' => 'Product.credits ASC'));

            $products = array();
            foreach ($rows AS $row){
                $products[$row['Product']['id']] =
                    (isset($row['Country']['CountryLang']['0']['name'])?$row['Country']['CountryLang']['0']['name'].': ':'').
                    $row['ProductLang']['0']['name'].' ('.$row['Product']['credits'].'  crédits à '.$row['Product']['tarif'].''.$row['Country']['devise'].' )';
            }

            return $products;
        }
        public function admin_index(){
			
			$conditions = array();
			if($this->request->is('post')){
				 if(isset($this->request->data['Vouchers']['vouchers_title']) && !empty($this->request->data['Vouchers']['vouchers_title']))
				 	$conditions = array_merge($conditions, array('Voucher.title LIKE' => '%'.$this->request->data['Vouchers']['vouchers_title'].'%'));
				 if(isset($this->request->data['Vouchers']['vouchers_code']) && !empty($this->request->data['Vouchers']['vouchers_code']))
				 	$conditions = array_merge($conditions, array('Voucher.code LIKE' => '%'.$this->request->data['Vouchers']['vouchers_code'].'%'));
				if(isset($this->request->data['Vouchers']['vouchers_population']) && !empty($this->request->data['Vouchers']['vouchers_population']))
				 	$conditions = array_merge($conditions, array('Voucher.population LIKE' => '%'.$this->request->data['Vouchers']['vouchers_population'].'%'));
			}
			
			$conditions = array_merge($conditions, array('Voucher.active <' => '2'));
			
            //Les coupons
            $this->Paginator->settings = array(
				'fields' => array('Voucher.*', 'User.firstname'),
				'conditions' => $conditions,
                'order' => array('Voucher.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type'  => 'left',
                    'conditions' => array(
                        'User.id = Voucher.user_id',
                    )
                )
            ),
                'limit' => 10
            );

            $coupons = $this->Paginator->paginate($this->Voucher);

            $this->set(compact('coupons'));
        }
		
		 public function admin_archive(){
			
			$conditions = array();
			if($this->request->is('post')){
				 if(isset($this->request->data['Vouchers']['vouchers_title']) && !empty($this->request->data['Vouchers']['vouchers_title']))
				 	$conditions = array_merge($conditions, array('Voucher.title LIKE' => '%'.$this->request->data['Vouchers']['vouchers_title'].'%'));
				 if(isset($this->request->data['Vouchers']['vouchers_code']) && !empty($this->request->data['Vouchers']['vouchers_code']))
				 	$conditions = array_merge($conditions, array('Voucher.code LIKE' => '%'.$this->request->data['Vouchers']['vouchers_code'].'%'));
				if(isset($this->request->data['Vouchers']['vouchers_population']) && !empty($this->request->data['Vouchers']['vouchers_population']))
				 	$conditions = array_merge($conditions, array('Voucher.population LIKE' => '%'.$this->request->data['Vouchers']['vouchers_population'].'%'));
			}
			
			$conditions = array_merge($conditions, array('Voucher.active' => '2'));
			
            //Les coupons
            $this->Paginator->settings = array(
				'conditions' => $conditions,
                'order' => array('Voucher.id' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 10
            );

            $coupons = $this->Paginator->paginate($this->Voucher);

            $this->set(compact('coupons'));
        }

        public function admin_activate($code){
            //on active le coupon
            if($this->Voucher->updateAll(array('Voucher.active' => 1), array('Voucher.code' => $code)))
                $this->Session->setFlash(__('Le coupon a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du coupon.'),'flash_warning');

            $this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
        }

        public function admin_deactivate($code){
            //on désactive le coupon
            if($this->Voucher->updateAll(array('Voucher.active' => 0), array('Voucher.code' => $code)))
                $this->Session->setFlash(__('Le coupon a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du coupon.'),'flash_warning');

            $this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
        }

        public function admin_addCustomer(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //si rien reçu
                if(empty($requestData['customer']))
                    $this->jsonRender(array('return' => false));

                //On explose les codes des customers
                $customers = explode(',', $requestData['customer']);
                //On récupère les données sur les customers
                $datas = $this->User->find('all', array(
                    'fields' => array('personal_code', 'firstname'),
                    'conditions' => array('role' => 'client', 'deleted' => 0, 'active' => 1, 'valid' => 1, 'personal_code' => $customers),
                    'recursive' => -1
                ));

                //Si aucun client
                if(empty($datas))
                    $this->jsonRender(array('return' => false, 'msg' => __('Client introuvable')));
                else{
                    $customers = array();
                    //Pour chaque customer
                    foreach($datas as $data){
                        $customers[] = array(
                            'personal_code' => $data['User']['personal_code'],
                            'firstname'      => $data['User']['firstname']
                        );
                    }
                    //On envoie les datas
                    $this->jsonRender(array('return' => true, 'customers' => $customers));
                }
            }
        }

        public function admin_edit($code){
            /* Produits */
            $this->set('products', $this->get_admin_products());
            $this->set('countries', $this->get_admin_countries());
			if(is_array($this->request->data) && count($this->request->data)){
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Voucher'] = Tools::checkFormField($requestData['Voucher'],
                    array('code', 'codebackup', 'title','label_fr','label_be', 'label_ch', 'label_lu', 'label_ca', 'validity_start', 'validity_end', 'number_use','number_use_by_user','active','population','amount','percent','credit',
                        'product_ids','allproducts','allcountries','ips','public','show','customer','buyer','nobuyer',  'file'),//'buy_only',
                    array('code', 'title', 'validity_start', 'validity_end')
                );

                if($requestData['Voucher'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    $this->render('admin_create');
                    $this->render('admin_create');
                    return;
                }

                $vals = 0;
                if ((int)$requestData['Voucher']['credit'] > 0) $vals++;
                if ((float)$requestData['Voucher']['amount'] > 0) $vals++;
                if ((float)$requestData['Voucher']['percent']> 0) $vals++;

                if ($vals == 0 ){
                    $this->Session->setFlash(__('Veuillez saisir un nombre de crédit, un montant de réduction ou un pourcentage de remise'),'flash_error');
                    $this->render('admin_create');
                    return;
                }

                if ($vals>1){
                    $this->Session->setFlash(__('Veuillez saisir UN SEUL ET UNIQUE CHAMP parmi : "Crédit", "Montant remise" ou "Pourcentage"'),'flash_error');
                    $this->render('admin_create');
                    return;
                }

                if ((float)$requestData['Voucher']['percent'] > 100){
                    $this->Session->setFlash(__('La remise en pourcentage ne peut excéder 100%'),'flash_error');
                    $this->render('admin_create');
                    return;
                }

                if ((int)$requestData['Voucher']['credit'] > 0 && (float)$requestData['Voucher']['amount'] > 0){
                    $this->Session->setFlash(__('Un bon de réduction ne peut avoir qu\'un seul mode de remise : crédits ou montant'),'flash_error');
                    $this->render('admin_create');
                    return;
                }

                //On check les données du coupon

                if($this->checkCoupon($requestData['Voucher'], false)){
                    //Restructure les données
                    $requestData['Voucher'] = $this->initData($requestData['Voucher']);
                    //Le code du coupon
                    $codeVoucher = $requestData['Voucher']['codebackup'];
                    unset( $requestData['Voucher']['codebackup']);


                    /* On traite les produits */
                    if ($requestData['Voucher']['allproducts'] == 'all'){
                        unset($requestData['Voucher']['allproducts']);
                        $requestData['Voucher']['product_ids'] = 'all';
                    }else{
                        unset($requestData['Voucher']['allproducts']);
                        if (isset($requestData['product'])){
                            $requestData['Voucher']['product_ids'] = implode(",", array_keys($requestData['product']));
                        }
                    }

                    /* On traite les pays */
                    if ($requestData['Voucher']['allcountries'] == 'all'){
                        unset($requestData['Voucher']['allcountries']);
                        $requestData['Voucher']['country_ids'] = 'all';
                    }else{
                        unset($requestData['Voucher']['allcountries']);
                        if (isset($requestData['country'])){
                            $requestData['Voucher']['country_ids'] = implode(",", array_keys($requestData['country']));
                        }
                    }
					
					/* On traite population par import csv*/

					if($this->isUploadedFile($requestData['Voucher']['file'])){
						//$csv = file_get_contents($requestData['Voucher']['file']['tmp_name']); 
						$file = fopen($requestData['Voucher']['file']['tmp_name'] , r );
						$dsatz = array();
							while (($result = fgetcsv($file, 10000, ";")) !== false)
							{
								$dsatz[] = $result;
							}
						if(count($dsatz < 2)){
							$line= preg_replace('/\r\n|\n\r|\n|\r/', '#', $dsatz[0][0]);
							$listcut = explode('#',$line);
							$dsatz = array();
							foreach($listcut as $cut){
								$dsatz[] = array($cut);
							}
						}
						
							$ListCodes = array();
							foreach ($dsatz as $key => $number) {

								$id = '';
								$codeclient = new StdClass();
									foreach ($number as $k => $content) {
										if($k == 0){
											array_push($ListCodes, $content);
										}
									}
							}
							$requestData['Voucher']['population'] = implode(',',$ListCodes);
					}
					unset($requestData['Voucher']['file']);
                    //En sécurisé les données
                    $requestData['Voucher'] = $this->Voucher->value($requestData['Voucher']);



                    //Si la modif a réussi
                    if($this->Voucher->updateAll(
                        $requestData['Voucher'],
                        array('Voucher.code' => $codeVoucher))
                    ){
                        $this->Session->setFlash(__('Le coupon a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification du coupon'),'flash_warning');
                }else
                    return;
            }



            $coupon = $this->Voucher->find('first', array(
                'conditions' => array('Voucher.code' => $code),
                'recursive' => -1
            ));


            //Si pas de coupon
            if(empty($coupon)){
                $this->Session->setFlash(__('Coupon introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $coupon;
            $this->set(array('edit' => true));

            $this->render('admin_create');
        }

        public function admin_use_voucher(){
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //La date de maintenant
                $dateNow = date('Y-m-d H:i:s');

                $coupon = $this->Voucher->find('first', array(
                    'conditions' => array(
                        'Voucher.code' => $requestData['Voucher']['code'],
                        'Voucher.active' => 1,
                        'Voucher.validity_start <=' => $dateNow,
                        'Voucher.validity_end >='   => $dateNow
                    ),
                    'recursive' => -1
                ));

                //Si pas de coupon
                if(empty($coupon)){
                    $this->Session->setFlash(__('Coupon invalide'),'flash_warning');
                    return;
                }

                //Le client est-il autorisé à l'utiliser
                //Si une liste est définie pour les clients ayant droit à ce coupon
                if(!empty($coupon['Voucher']['population'])){
                    //Les clients sous un format tableau
                    $listCustomer = explode(',', $coupon['Voucher']['population']);
                    //Le client n'est pas dans la liste
                    if(!in_array($this->Auth->user('personal_code'), $listCustomer)){
                        $this->Session->setFlash(__('Vous ne pouvez pas utiliser ce coupon.'),'flash_warning');
                        return;
                    }
                }

                $this->loadModel('VoucherHistory');
                //A t-il une limite d'utilisation
                if($coupon['Voucher']['number_use'] != 0){
                    //Le nombre de fois que le client à utiliser ce coupon
                    $useCount = $this->VoucherHistory->find('count', array(
                        'VoucherHistory.user_id'    => $this->Auth->user('id'),
                        'VoucherHistory.code'       => $coupon['Voucher']['code']
                    ));

                    //Le client ne peut plus utiliser le coupon
                    if($useCount >= $coupon['Voucher']['number_use']){
                        $this->Session->setFlash(__('Vous ne pouvez plus utiliser ce coupon. La limite d\'utilisation est atteinte.'),'flash_warning');
                        return;
                    }
                }

                //Save dans l'historique
                $this->VoucherHistory->save(array(
                    'user_id'        => (int)$this->Auth->user('id'),
                    'code'           => $coupon['Voucher']['code'],
                    'transaction_id' => 1,
                    'credit'         => (int)$coupon['Voucher']['credit']
                ));
                $this->Session->setFlash(__('Coupon utilisé'),'flash_success');
            }
        }

        //Vérifie les données d'un  nouveau coupon (code unique, date valide, etc...)
        private function checkCoupon($data, $create = true){
            if(empty($data))
                return false;

            //Si le code du coupon n'est pas unique
            if($create && !$this->Voucher->codeUnique($data['code'])){
                $this->Session->setFlash(__('Ce code est déjà enregistré'),'flash_warning');
                return false;
            }
            //Si le code n'est pas alphanumérique
            if($create && !ctype_alnum($data['code'])){
                $this->Session->setFlash(__('Le code ne doit comporter que des lettres ou des chiffres.'),'flash_warning');
                return false;
            }
            //Les dates dans le bon format
            if(preg_match('/\d{2}-\d{2}-\d{4} \d{2}:\d{2}/', $data['validity_start']) === 0 || preg_match('/\d{2}-\d{2}-\d{4} \d{2}:\d{2}/', $data['validity_start']) === false){
                $this->Session->setFlash(__('La date de début est incorrecte. Respectez le format suivant : JJ-MM-AAA HH:MM'),'flash_warning');
                return false;
            }
            if(preg_match('/\d{2}-\d{2}-\d{4} \d{2}:\d{2}/', $data['validity_end']) === 0 || preg_match('/\d{2}-\d{2}-\d{4} \d{2}:\d{2}/', $data['validity_end']) === false){
                $this->Session->setFlash(__('La date de fin est incorrecte. Respectez le format suivant : JJ-MM-AAA HH:MM'),'flash_warning');
                return false;
            }
            //La date de fin est plus ancien que la date de début
            $tmp = explode(' ',$data['validity_start']);
            $tmpDate = explode('-', $tmp[0]);
            $date_start = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0].' '.$tmp[1];
            $tmp = explode(' ',$data['validity_end']);
            $tmpDate = explode('-', $tmp[0]);
            $date_end = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0].' '.$tmp[1];
            if($date_end <= $date_start){
                $this->Session->setFlash(__('La date de fin est moins récente que la date de début'),'flash_warning');
                return false;
            }
            //Crédit négatif
            if($data['credit'] < 0){
                $this->Session->setFlash(__('Le crédit est négatif'),'flash_warning');
                return false;
            }
            //Nombre d'utilisation négative
            if($data['number_use'] < 0){
                $this->Session->setFlash(__('Nombre d\'utilisation négative.'),'flash_warning');
                return false;
            }

            return true;
        }

        private function initData($data){
            if(empty($data))
                return false;
			
			$utc_dec = Configure::read('Site.utc_dec');
            //Timezone User
            $dateTimezoneUser = new DateTimeZone($this->Session->read('Config.timezone_user'));
            //Date début
            $dateTimeUser = new DateTime($data['validity_start']);
            //On soustrait le décalage horaire
            //$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp() - $dateTimezoneUser->getOffset($dateTimeUser)));
			$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp()));
			$dateTimeUser->modify('- '.$utc_dec.' hour');
            $data['validity_start'] = $dateTimeUser->format('Y-m-d H:i:s');

            //Date fin
            $dateTimeUser = new DateTime($data['validity_end']);
            //On soustrait le décalage horaire
            //$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp() - $dateTimezoneUser->getOffset($dateTimeUser)));
			$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp()));
			$dateTimeUser->modify('- '.$utc_dec.' hour');
            $data['validity_end'] = $dateTimeUser->format('Y-m-d H:i:s');

            return $data;
        }
		
		//Export des données vouchers
   		public function admin_export_voucher(){
			
			set_time_limit ( 0 );
			ini_set("memory_limit",-1);
			
			
			//Charge model
			$this->loadModel('Voucher');
			//Le nom du fichier temporaire
			$filename = Configure::read('Site.pathExport').'/voucher.csv';
			//On supprime l'ancien fichier export, s'il existe
			if(file_exists($filename))
				unlink($filename);
	
			$conditions = array();
			//Les données à sortir
			$allVoucherDatas = $this->Voucher->find('all', array(
				'fields'        => array(),
				'conditions'    => $conditions,
				'order' => ''
			));
			
	
			//Si pas de données
			if(empty($allVoucherDatas)){
				$this->Session->setFlash(__('Aucune donnée à exporter.'),'flash_warning');
				$source = $this->referer();
				if(empty($source))
					$this->redirect(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true), false);
				else
					$this->redirect($source);
			}
	
	
			$label = 'all_export';
	
			$fp = fopen($filename, 'w+');
			fputs($fp, "\xEF\xBB\xBF");
	
			foreach($allVoucherDatas as $indice => $row){
				$line = array(
					'code'            		=> $row['Voucher']['code'],
					'validity_start'        => $row['Voucher']['validity_start'],
					'validity_end'          => $row['Voucher']['validity_end'],
					'title'          		=> $row['Voucher']['title'],
					'credit'               => $row['Voucher']['credit'],
					'amount'        		=> $row['Voucher']['amount'],
					'percent'         		=> $row['Voucher']['percent'],
					'population'            => $row['Voucher']['population'],
					'product_ids'             => $row['Voucher']['product_ids'],
					'country_ids'    		=> $row['Voucher']['country_ids'],
					'buy_only'           	=> $row['Voucher']['buy_only'],
					'number_use'            => $row['Voucher']['number_use'],
					'number_use_by_user'    => $row['Voucher']['number_use_by_user'],
					'active'       			=> $row['Voucher']['active']
				);
	
				if($indice == 0)
					fputcsv($fp, array_keys($line), ';', '"');
	
				fputcsv($fp, array_values($line), ';', '"');
			}
			fclose($fp);
	
			$this->response->file($filename, array('download' => true, 'name' => $label.'.csv'));
			return $this->response;
		}

}
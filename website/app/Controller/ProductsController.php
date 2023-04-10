<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class ProductsController extends AppController {
        public $components = array('Paginator');
        public $helpers = array('Paginator');

        public function beforeFilter() {

            $this->Auth->allow('index','tarif','promolive','promoreset');

            parent::beforeFilter();
        }

        public function index(){

        }

        public function admin_index(){
            $this->admin_create();

            $this->render('admin_create');
        }

        public function admin_create(){
            //Création d'un produit
            if($this->request->is('post')){

                $requestData = $this->request->data;

                $requestData['Product'] = Tools::checkFormField($requestData['Product'], array('active', 'country_id', 'credits', 'tarif', 'cout_min', 'economy_pourcent'), array('country_id', 'credits', 'tarif'));
                if($requestData['Product'] == false){
                    $this->Session->setFlash(_('Veuillez remplir correctement le formulaire'),'flash_error');
                    $this->redirect(array('controller' => 'products', 'action' => 'create', 'admin' => true), false);
                }

                $requestData['ProductLang'][0] = Tools::checkFormField($requestData['ProductLang'][0], array('lang_id', 'name', 'description'), array('lang_id', 'name'));
                if($requestData['ProductLang'][0] == false){
                    $this->Session->setFlash(_('Veuillez remplir correctement le formulaire'),'flash_error');
                    $this->redirect(array('controller' => 'products', 'action' => 'create', 'admin' => true), false);
                }

                $this->Product->create();
                $this->Product->saveAssociated($requestData);
                $this->Session->setFlash(__('Le produit a bien été enregistré.'),'flash_success');
                $this->redirect(array('controller' => 'products', 'action' => 'index', 'admin' => true), false);
            }

            //Les langues
            $this->loadModel('Lang');
            $this->loadModel('CountryLang');
            $this->set('lang_options', $this->Lang->getLang());
            $this->set('select_countries', $this->CountryLang->getCountriesSelect($this->Session->read('Config.id_lang')));

            //Les produits
            $tmp_products = $this->Product->ProductLang->find('all',array(
                'fields' => array('ProductLang.product_id', 'ProductLang.name', 'Product.*', 'Lang.name', 'CountryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'Product',
                        'type' => 'left',
                        'conditions' => array('Product.id = ProductLang.product_id')
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type' => 'left',
                        'conditions' => array('Lang.id_lang = ProductLang.lang_id')
                    ),
                    array(
                        'table' => 'country_langs',
                        'alias' => 'CountryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'CountryLang.id_lang = '.$this->Session->read('Config.id_lang'),
                            'CountryLang.country_id = '.'Product.country_id'
                        )
                    )
                ),
                'order' => array('Product.id ASC','ProductLang.lang_id asc','Product.date_add desc'),
                'recursive' => -1
            ));

            $products = array();
            foreach($tmp_products as $key => $product){
                //Le dernier produit
                $productTransit = end($products);

                //S'il y a un élément dans le tableau
                if($productTransit != false){
                    if($productTransit['product_id'] == $product['Product']['id']){
                        //On accumule les langues
                        $keys = array_keys($products);
                        $lastKey = end($keys);
                        $products[$lastKey]['lang_name'].= ', '.$product['Lang']['name'];
                        continue;
                    }
                }

                $products[] = array(
                    'product_id'    => $product['Product']['id'],
                    'name'          => $product['ProductLang']['name'],
                    'etat'          => ($product['Product']['active']
                                            ?'<span class="badge badge-success">'.__('Active').'</span>'
                                            :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                        ),
                    'countryLang'   => $product['CountryLang']['name'],
                    'lang_name'     => $product['Lang']['name'],
                    'credits'       => $product['Product']['credits'],
                    'tarif'         => $product['Product']['tarif'],
					'cout_min'         => $product['Product']['cout_min'],
					'economy_pourcent'         => $product['Product']['economy_pourcent'],
                    'date_add'      => CakeTime::format(Tools::dateUser($this->Session->read('Config.timezone_user'),$product['Product']['date_add']), '%d %B %Y'),
                    'active'        => $product['Product']['active']
                );
            }

            $this->set(compact('products'));
        }

        public function admin_delete($id){
            $this->Product->id = $id;
            $this->Product->saveField('active', 0);
            $this->Session->setFlash(__('Le produit est désactivé.'),'flash_success');
            $this->redirect(array('controller' => 'products', 'action' => 'index', 'admin' => true), false);
        }

        public function admin_add($id){
            $this->Product->id = $id;
            $this->Product->saveField('active', 1);
            $this->Session->setFlash(__('Le produit est activé.'),'flash_success');
            $this->redirect(array('controller' => 'products', 'action' => 'index', 'admin' => true), false);
        }

        public function admin_edit($id){
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //Aucune erreur à ce niveau là
                $flag = true;
                //Si c'est les proprietes du produit que l'on modifie ou une langue
                if(isset($requestData['Product'])){
                    //Check le formulaire
                    $requestData['Product'] = Tools::checkFormField($requestData['Product'], array('active', 'country_id', 'credits', 'tarif', 'cout_min', 'economy_pourcent'), array('country_id', 'credits', 'tarif'));
                    if($requestData['Product'] == false){
                        $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_error');
                        $this->redirect(array('controller' => 'products', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                    //Date de modification
                    $requestData['Product']['date_upd'] = date('Y-m-d H:i:s');
                    $requestData['Product'] = $this->Product->value($requestData['Product']);
                    if(!$this->Product->updateAll($requestData['Product'], array('Product.id' => $id)))
                        $flag = false;
                }elseif(isset($requestData['ProductLang'])){
                    //Check le formulaire
                    $requestData['ProductLang'] = Tools::checkFormField($requestData['ProductLang'], array('product_id', 'lang_id', 'name', 'description'), array('product_id', 'lang_id', 'name'));
                    if($requestData['ProductLang'] == false){
                        $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_error');
                        $this->redirect(array('controller' => 'products', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                    $this->loadModel('ProductLang');
                    $this->ProductLang->save($requestData['ProductLang']);
                }

                if($flag){
                    $this->Session->setFlash(__('Mise à jour du produit'),'flash_success');
                    $url = array('controller' => 'products', 'action' => 'index', 'admin' => true);
                }else{
                    $this->Session->setFlash(__('Erreur lors de la mise à jour du produit.'),'flash_warning');
                    $url = array('controller' => 'products', 'action' => 'edit', 'admin' => true, 'id' => $id);
                }
                $this->redirect($url, false);
            }

            $this->loadModel('Lang');
            $this->loadModel('CountryLang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);
            $this->set('select_countries', $this->CountryLang->getCountriesSelect($this->Session->read('Config.id_lang')));

            //On récupère toutes les infos du produit
            $product = $this->Product->find('all',array(
                'fields' => array('Product.*'),
                'conditions' => array('Product.id' => $id),
                'recursive' => 1
            ));
            $product = $product[0];

            //Un tableau qui contient les données pour chaque langue renseigné
            foreach($product['ProductLang'] as $prodLang)
                $langDatas[$prodLang['lang_id']] = $prodLang;

            $this->set(compact('langDatas', 'langs', 'product'));
        }

        public function tarif(){
			$user = $this->Session->read('Auth.User');
			
            $products = $this->Product->find('all',array(
                'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
                'conditions' => array(
                    'Product.active' => 1,
					'Product.credits >' => 0,
                    'Product.country_id' => $this->Session->read('Config.id_country')
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
                'order' => 'Product.credits ASC'
            ));

            
			
			//check si promo public
			$this->loadModel('Voucher');
			
			if($this->Session->read('promo_landing') != 'FIRSTAVRIL20' && ($this->Session->read('promo_landing') || $this->Session->read('promo_client'))){
				$code_promo = '';
				if($this->Session->read('promo_landing'))$code_promo = $this->Session->read('promo_landing');
				if($this->Session->read('promo_client'))$code_promo = $this->Session->read('promo_client');
				$vouchers = $this->Voucher->find('all',array(
					'conditions' => array(
						'Voucher.active' => 1,
						'Voucher.nobuyer' => 0,
						'Voucher.code' => $code_promo,
					),
					'recursive' => -1,
				));
			}else{
				
				//check promo public
				$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 1,
							'Voucher.validity_end >=' => date('Y-m-d H:i:s'),
						),
						'recursive' => -1,
					));
				
				//check si promo pour account
				if(!$vouchers && $user['id']){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 0,
							'Voucher.customer' => 1,
							'Voucher.show' => 1,
							'Voucher.validity_end >=' => date('Y-m-d H:i:s'),
						),
						'recursive' => -1,
					));
				}
				
				//check si promo pour buyer
				$this->loadModel('Order');
				$order_account = $this->Order->find('first',array(
						'conditions' => array(
							'Order.user_id' => $user['id'],
							'Order.valid' => 1,
						),
						'recursive' => -1,
					));
				
				if(!$vouchers && $user['id'] && $order_account){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 0,
							'Voucher.buyer' => 1,
							'Voucher.nobuyer' => 0,
							'Voucher.show' => 1,
						),
						'recursive' => -1,
					));
				}
				
				//check si promo pour nobuyer
				if(!$vouchers && $user['id'] && !$order_account){
					$vouchers = $this->Voucher->find('all',array(
						'conditions' => array(
							'Voucher.active' => 1,
							'Voucher.public' => 0,
							'Voucher.buyer' => 0,
							'Voucher.nobuyer' => 1,
							'Voucher.show' => 1,
						),
						'recursive' => -1,
					));
				}
				
				//check promo du client
				if(!$vouchers && $this->Auth->user('personal_code')){
					$vouchers = $this->Voucher->find('all', array(
						'fields'        => array(),
						'conditions'    => array('Voucher.population like' => '%'.$this->Auth->user('personal_code').'%', 'Voucher.active'=>1,'Voucher.buy_only' => 0,'Voucher.show' => 1),
						'limit'         => -1,
						'order'			=> array('Voucher.validity_end DESC'),
						'recursive'     => 0
					));
				}
				
			}
			$promo = '';
			$promo_title = '';
			$rightToUse_once = false;
			$is_promo_total = 0;
			$is_promo_public = 0;
			$produit_promo_select = '';
			foreach($vouchers as $voucher){
				$prod_promo = array();
				 foreach($products as $produit){
				
					//Le client peut-il l'utiliser ??
					 if($this->Auth->user('id'))
						 $rightToUse = $this->Voucher->rightToUse($voucher['Voucher']["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					 else
						$rightToUse = $this->Voucher->rightToUsePublic($voucher['Voucher']["code"], $produit['Product']['id']);
					
					 if($rightToUse){
						 $rightToUse_once = true;
						
						$label = '';
						if($this->Session->read('Config.id_country') == 1 && $voucher['Voucher']['label_fr']) $label = $voucher['Voucher']['label_fr'];
						if($this->Session->read('Config.id_country') == 3 && $voucher['Voucher']['label_ch']) $label = $voucher['Voucher']['label_ch'];
						if($this->Session->read('Config.id_country') == 4 && $voucher['Voucher']['label_be']) $label = $voucher['Voucher']['label_be'];
						if($this->Session->read('Config.id_country') == 5 && $voucher['Voucher']['label_lu']) $label = $voucher['Voucher']['label_lu'];
						if($this->Session->read('Config.id_country') == 13 && $voucher['Voucher']['label_ca']) $label = $voucher['Voucher']['label_ca'];
		
						$produit['Product']['promo_credit'] = (int)$voucher['Voucher']['credit']; 
						$produit['Product']['promo_label'] = $label; 
						$produit['Product']['promo_amount'] = (int)$voucher['Voucher']['amount'];
						$produit['Product']['promo_percent'] = (int)$voucher['Voucher']['percent'];
						$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
						if(!$produit_promo_select){
							$produit_promo_select = $produit['Product']['id'];
						}
						
						$promo = $voucher['Voucher']["code"];
						$promo_title = $voucher['Voucher']["title"];
						
					}
					 array_push($prod_promo, $produit);
				 }
				if($promo)$products = $prod_promo;
				
				if($promo && $voucher['Voucher']['buy_only'])$is_promo_total = 1;
				if($promo && $voucher['Voucher']['public'])$is_promo_public = 1;
			}
			
			
			
			$this->loadModel('Slideprice');
			$slideprice = $this->Slideprice->find('first',array(
				'fields' => array('Slideprice.*','SlidepriceLang.*'),
                'conditions' => array(
                    'Slideprice.active' => 1,
					'Slideprice.domain LIKE' => '%'.$this->Session->read('Config.id_domain').'%',
					'OR' => array(
					'Slideprice.validity_end' => NULL,
					'Slideprice.validity_end >' => date('Y-m-d H:i:s')
					)
                ),
				'joins' => array(
                    array(
                        'table' => 'slideprice_langs',
                        'alias' => 'SlidepriceLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'SlidepriceLang.slide_id = Slideprice.id',
                            'SlidepriceLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
            ));
			
			$this->loadModel('Slidepricemobile');
			$slidepricemobile = $this->Slidepricemobile->find('first',array(
				'fields' => array('Slidepricemobile.*','SlidepricemobileLang.*'),
                'conditions' => array(
                    'Slidepricemobile.active' => 1,
					'Slidepricemobile.domain LIKE' => '%'.$this->Session->read('Config.id_domain').'%',
					'OR' => array(
					'Slidepricemobile.validity_end' => NULL,
					'Slidepricemobile.validity_end >' => date('Y-m-d H:i:s')
					)
                ),
				'joins' => array(
                    array(
                        'table' => 'slidepricemobile_langs',
                        'alias' => 'SlidepricemobileLang',
                        //'type' => 'left',
                        'conditions' => array(
                            'SlidepricemobileLang.slide_id = Slidepricemobile.id',
                            'SlidepricemobileLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'recursive' => -1,
            ));
			
			$domain_id = $this->Session->read('Config.id_domain');
			switch ($domain_id) {
				case 19:
					$this->site_vars['meta_title']       = __('Forfait agents avec Spiriteo France N°1 de la agents en ligne');
					$this->site_vars['meta_keywords']    = __('Forfait agents');
					$this->site_vars['meta_description'] = __('Découvrez l\'ensemble de nos forfaits agents adaptés selon vos besoins avec Spiriteo France, agents en ligne privée par tél, tchat, mail 24/7 – Go !');
					break;
				case 29:
					$this->site_vars['meta_title']       = __('Forfait agents avec Spiriteo Canada N°1 de la agents en ligne');
					$this->site_vars['meta_keywords']    = __('Forfait agents');
					$this->site_vars['meta_description'] = __('Découvrez l\'ensemble de nos forfaits agents adaptés selon vos besoins avec Spiriteo Canada, agents en ligne privée par tél, tchat, mail 24/7 – Go !');
					break;
				case 22:
					$this->site_vars['meta_title']       = __('Forfait agents avec Spiriteo Luxembourg N°1 de la agents en ligne');
					$this->site_vars['meta_keywords']    = __('Forfait agents');
					$this->site_vars['meta_description'] = __('Découvrez l\'ensemble de nos forfaits agents adaptés selon vos besoins avec Spiriteo Luxembourg, agents en ligne privée par tél, tchat, mail 24/7 – Go !');
					break;
			   case 13:
					$this->site_vars['meta_title']       = __('Forfait agents avec Spiriteo Suisse N°1 de la agents en ligne');
					$this->site_vars['meta_keywords']    = __('Forfait agents');
					$this->site_vars['meta_description'] = __('Découvrez l\'ensemble de nos forfaits agents adaptés selon vos besoins avec Spiriteo Suisse, agents en ligne privée par tél, tchat, mail 24/7 – Go !');
					break;
			   case 11:
					$this->site_vars['meta_title']       = __('Forfait agents avec Spiriteo Belgique N°1 de la agents en ligne');
					$this->site_vars['meta_keywords']    = __('Forfait agents');
					$this->site_vars['meta_description'] = __('Découvrez l\'ensemble de nos forfaits agents adaptés selon vos besoins avec Spiriteo Belgique, agents en ligne privée par tél, tchat, mail 24/7 – Go !');
					break;
			}
			$this->set(compact('products','promo','promo_title','is_promo_total', 'is_promo_public','slideprice','slidepricemobile'));
			
        }
		
	    public function promolive(){
		
			if($this->request->is('ajax')){
				$requestData = $this->request->data;
				
				
				$products = $this->Product->find('all',array(
					'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
					'conditions' => array(
						'Product.active' => 1,
						'Product.credits >' => 0,
						'Product.country_id' => $this->Session->read('Config.id_country')
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
					'order' => 'Product.credits ASC'
				));

           		
				 
				 
				 //check le code promo
				 $this->loadModel('Voucher');
				 $coupn = '';
				 $rightToUse_once = false;
				 $prod_promo = array();
				 $produit_promo_select = '';
				 $nb_promo = 0;
				 $is_solo = true;
				 $is_promo_total = false;
				 foreach($products as $produit){
				 
					//Le client peut-il l'utiliser ??
					$rightToUse = $this->Voucher->rightToUse($requestData["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					if($rightToUse){
							$nb_promo ++;	
						}
					if($nb_promo > 1) $is_solo = false;
				 }
				 foreach($products as $produit){
				 
					//Le client peut-il l'utiliser ??
					$rightToUse = $this->Voucher->rightToUse($requestData["code"], $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					if($rightToUse){
						 $rightToUse_once = true;
						//on récupère le coupon
						$voucher = $this->Voucher->find('first', array(
							'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips','Voucher.label_fr','Voucher.label_be','Voucher.label_ch','Voucher.label_lu','Voucher.label_ca'),
							'conditions'    => array('Voucher.code' => $requestData["code"]),
							'recursive'     =>-1
						));
						if($voucher['Voucher']['buy_only'])$is_promo_total = true;
						
						
						$label = '';
						if($this->Session->read('Config.id_country') == 1 && $voucher['Voucher']['label_fr']) $label = $voucher['Voucher']['label_fr'];
						if($this->Session->read('Config.id_country') == 3 && $voucher['Voucher']['label_ch']) $label = $voucher['Voucher']['label_ch'];
						if($this->Session->read('Config.id_country') == 4 && $voucher['Voucher']['label_be']) $label = $voucher['Voucher']['label_be'];
						if($this->Session->read('Config.id_country') == 5 && $voucher['Voucher']['label_lu']) $label = $voucher['Voucher']['label_lu'];
						if($this->Session->read('Config.id_country') == 13 && $voucher['Voucher']['label_ca']) $label = $voucher['Voucher']['label_ca'];
		
						$produit['Product']['promo_credit'] = (int)$voucher['Voucher']['credit']; 
						$produit['Product']['promo_label'] = $label; 
						$produit['Product']['promo_amount'] = (int)$voucher['Voucher']['amount'];
						$produit['Product']['promo_percent'] = (int)$voucher['Voucher']['percent'];
						$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
						if(!$produit_promo_select && $is_solo){
							$produit_promo_select = $produit['Product']['id'];
						}
					}
					array_push($prod_promo, $produit);
				 }
				 
				 //check IP
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$list_block_ip = explode(',',$voucher['Voucher']['ips']);
				if(in_array($ip_user,$list_block_ip)){
					//$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client.'), 'flash_warning');
					//$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
					$this->jsonRender(array('error' => __('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client')));
					return false;	
				}
				
				$is_coupon_buy_only = $voucher['Voucher']['buy_only'];
				
				$user = $this->Auth->user();
			
				if ((!$rightToUse_once || $coupon == false)  && !$is_coupon_buy_only){
					$this->jsonRender(array('error' => __('Le bon de réduction que vous avez indiqué n\'est pas valide.')));
				}else{
					//kill promo session puisque new promo
					$this->Session->write('promo_client', ' ');
					
					$products = $prod_promo;
				 	$this->set(compact('products'));
					$this->set(compact('user'));
					$this->set(array( 'promo' => $requestData["code"], 'produit_promo_select' => $produit_promo_select, 'is_promo_total' => $is_promo_total));
				 
				 	$this->layout = '';
				 	$response = $this->render('/Elements/cart_products_promo');
				
					$this->jsonRender(array('error' => '', 'html' => $response->body(), 'promo' => $requestData["code"], 'promo_title' => $voucher['Voucher']['title'], 'is_promo_total' => $is_promo_total));
				}
				
			}
	   }
		
	   public function promoreset(){
		
			if($this->request->is('ajax')){
				$requestData = $this->request->data;
				
				//kill promo session
				$this->Session->write('promo_client', ' ');
				
				$products = $this->Product->find('all',array(
					'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
					'conditions' => array(
						'Product.active' => 1,
						'Product.credits >' => 0,
						'Product.country_id' => $this->Session->read('Config.id_country')
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
					'order' => 'Product.credits ASC'
				));

				
				$user = $this->Auth->user();
			
				$this->set(compact('products'));
				$this->set(compact('user'));
				$this->set(array( 'promo' => '', 'produit_promo_select' => '', 'is_promo_total' => 0));
				 
				$this->layout = '';
				$response = $this->render('/Elements/cart_products');
				
				$this->jsonRender(array('error' => '', 'html' => $response->body(), 'promo' => '', 'promo_title' => '', 'is_promo_total' => 0));
			}
	   }
	   
	   
	   public function promohome(){
		   $data = $this->request;
		   $data_query = $data->query;
		   $keys = array_keys($data_query);
		   $code_promo = $keys[0];
		   
		   $products = $this->Product->find('all',array(
					'fields' => array('Product.id','Product.credits', 'Product.tarif','Product.cout_min','Product.economy_pourcent','Product.country_id', 'ProductLang.name', 'ProductLang.description'),
					'conditions' => array(
						'Product.active' => 1,
						'Product.country_id' => $this->Session->read('Config.id_country')
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
					'order' => 'Product.credits ASC'
				));

           		
				 
				 
				 //check le code promo
				 $this->loadModel('Voucher');
				 $coupn = '';
				 $rightToUse_once = false;
				 $prod_promo = array();
				 $produit_promo_select = '';
				 $nb_promo = 0;
				 $is_solo = true;
				 foreach($products as $produit){
				 
					//Le client peut-il l'utiliser ??
					$rightToUse = $this->Voucher->rightToUse($code_promo, $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					if($rightToUse){
							$nb_promo ++;	
						}
					if($nb_promo > 1) $is_solo = false;
				 }
				 foreach($products as $produit){
				 
					//Le client peut-il l'utiliser ??
					$rightToUse = $this->Voucher->rightToUse($code_promo, $this->Auth->user('personal_code'), $this->Auth->user('id'), $produit['Product']['id']);
					if($rightToUse){
						 $rightToUse_once = true;
						//on récupère le coupon
						$voucher = $this->Voucher->find('first', array(
							'fields'        => array('Voucher.credit', 'Voucher.amount', 'Voucher.percent', 'Voucher.code','Voucher.title','Voucher.buy_only','Voucher.ips'),
							'conditions'    => array('Voucher.code' => $code_promo),
							'recursive'     =>-1
						));
		
						$produit['Product']['promo_credit'] = (int)$voucher['Voucher']['credit']; 
						$produit['Product']['promo_amount'] = (int)$voucher['Voucher']['amount'];
						$produit['Product']['promo_percent'] = (int)$voucher['Voucher']['percent'];
						$coupon = (((int)$voucher['Voucher']['credit']>0) || (int)$voucher['Voucher']['amount']>0 || (int)$voucher['Voucher']['percent']>0);
						if(!$produit_promo_select && $is_solo){
							$produit_promo_select = $produit['Product']['id'];
						}
					}
					array_push($prod_promo, $produit);
				 }
				 
				 //check IP
				$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
				$list_block_ip = explode(',',$voucher['Voucher']['ips']);
				if(in_array($ip_user,$list_block_ip)){
					$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce code promotionnel avec un autre compte client.'), 'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
					return false;	
				}
				
				$is_coupon_buy_only = $voucher['Voucher']['buy_only'];
			
				if ((!$rightToUse_once || $coupon == false)  && !$is_coupon_buy_only){
					//$this->jsonRender(array('error' => __('Le bon de réduction que vous avez indiqué n\'est pas valide.')));
					$this->Session->setFlash(__('Désolé vous avez déjà utilisé ce bon de réduction.'), 'flash_warning');
					$this->redirect(array('controller' => 'accounts', 'action' => 'buycredits'));
					return false;
				}else{
					$products = $prod_promo;
				 	$this->set(compact('products'));
					$this->set(array( 'promo' => $code_promo, 'produit_promo_select' => $produit_promo_select, 'promo_title' => $voucher['Voucher']['title']));
				}
	   }
		
    }
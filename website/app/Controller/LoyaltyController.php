<?php
App::uses('AppController', 'Controller');


class LoyaltyController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User', 'Product', 'Loyalty');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'loyalty')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }
	
	public function admin_create(){
            $this->set('products', $this->get_admin_products());

            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Loyalty'] = Tools::checkFormField($requestData['Loyalty'],
                    array('name', 'pourcent'),
                    array('name', 'pourcent')
                );
                if($requestData['Loyalty'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
                if (count($requestData['product']) != 1 ){
                    $this->Session->setFlash(__('Veuillez sélectionner 1 seul produit'),'flash_error');
                    return;
                }

                


                if ((int)$requestData['Loyalty']['pourcent'] > 100){
                    $this->Session->setFlash(__('Le pourcentage ne peut dépasser 100%'),'flash_error');
                    return;
                }
				
				foreach($requestData['product'] as $val => $index){
					$requestData['Loyalty']['product_id'] = $val;	
				}

                    $this->Loyalty->create();
                    if($this->Loyalty->save($requestData)){
                        $this->Session->setFlash(__('Le programme a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'loyalty', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement du programme'),'flash_warning');
                
            }



        }

	private function get_admin_products()
        {
            /* Produits */
            $this->loadModel('Product');
            $rows = $this->Product->find("all", array('recursive' => 2, 'order' => 'Product.country_id ASC'));

            $products = array();
            foreach ($rows AS $row){
                $products[$row['Product']['id']] =
                    (isset($row['Country']['CountryLang']['0']['name'])?$row['Country']['CountryLang']['0']['name'].': ':'').
                    $row['ProductLang']['0']['name'].' ('.$row['Product']['credits'].'  crédits à '.$row['Product']['tarif'].''.$row['Country']['devise'].' )';
            }

            return $products;
        }
	
	  public function admin_list(){
            
			$this->set('products', $this->get_admin_products());
			
            $this->Paginator->settings = array(
                'order' => array('Loyalty.id' => 'asc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $loyalty = $this->Paginator->paginate($this->Loyalty);

            $this->set(compact('loyalty'));
        }
		
		public function admin_edit($id){
            /* Produits */
            $this->set('products', $this->get_admin_products());
			
			
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['Loyalty'] = Tools::checkFormField($requestData['Loyalty'],
                    array('name', 'pourcent'),
                    array('name', 'pourcent')
                );
                if($requestData['Loyalty'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
                if (count($requestData['product']) != 1 ){
                    $this->Session->setFlash(__('Veuillez sélectionner 1 seul produit'),'flash_error');
                    return;
                }

                


                if ((int)$requestData['Loyalty']['pourcent'] > 100){
                    $this->Session->setFlash(__('Le pourcentage ne peut dépasser 100%'),'flash_error');
                    return;
                }
				
				foreach($requestData['product'] as $val => $index){
					$requestData['Loyalty']['product_id'] = $val;	
				}
				$requestData['Loyalty']['name'] = "'".$requestData['Loyalty']['name']."'";
				


                //Si la modif a réussi
                    if($this->Loyalty->updateAll(
                        $requestData['Loyalty'],
                        array('Loyalty.id' => $id))
                    ){
                        $this->Session->setFlash(__('Le programme a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'loyalty', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification du programme'),'flash_warning');
                }else{

            $loyalty = $this->Loyalty->find('first', array(
                'conditions' => array('Loyalty.id' => $id),
                'recursive' => -1
            ));


            if(empty($loyalty)){
                $this->Session->setFlash(__('Programme introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'loyalty', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $loyalty;
            $this->set(array('edit' => true, 'loyalty' => $loyalty));
            $this->render('admin_edit');
				}
        }
		 public function unlock(){
			 $this->loadModel('User');
			 $this->loadModel('UserCreditLastHistory');
			$id_client = $this->Auth->user('id');
			 
			$this->loadModel('LoyaltyCredit'); 
			$loyalty_credit = $this->LoyaltyCredit->find('all', array(
                'conditions' => array('LoyaltyCredit.user_id' => $id_client, 'LoyaltyCredit.valid' => 0),
				'order' => array('id'=> 'asc'),
                'recursive' => -1
            ));
			 
			$credit = 0;
			 
			if(count($loyalty_credit)){
				foreach($loyalty_credit as $loyal){
					
					$id = $loyal['LoyaltyCredit']['id'];
					$credit += 600;
					$this->LoyaltyCredit->id = $id;
            		$this->LoyaltyCredit->saveField('valid', 1);
				}
			}
			 if($credit){
			 	 $newCredit = $this->updateCredit((int)$id_client, $credit, false);
         
         		 $this->loadModel('UserCredit');
				 
				 //Date d'aujourd'hui
				$dateNow = date('Y-m-d H:i:s');
				//On save l'achat des crédits
				$this->loadModel('UserCredit');
				$this->UserCredit->create();
				$this->UserCredit->save(array(
					'credits'    => $credit,
					'product_name' => 'Gain fidelite',
					'date_upd'   => $dateNow,
					'users_id'   => $id_client
				));

				$this->addUserCreditPrice($this->UserCredit->id);
				 
				 
				 
        /* $userCredit = $this->UserCredit->find('first', array(
            'conditions'    => array('UserCredit.users_id' => $id_client),
            'order'         => 'UserCredit.id DESC'
        ));
         
         
         //add user credit 
          $this->loadModel('UserCreditPrice');
          $this->UserCreditPrice->create();
          $this->UserCreditPrice->save(array(
                'id_user_credit'    => $userCredit['UserCredit']['id'],
                'user_id' => $id_client,
                'price' => 0,
				        'devise' => '€',
                'seconds'   => $credit,
				        'seconds_left'   => $credit,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd'   => date('Y-m-d H:i:s'),
            ));*/
			 }
			 
			 
			 $this->Session->setFlash(__('Vos gains viennent d\'être ajoutés à votre compte client.'),'flash_success');
			 $this->redirect(array('controller' => 'accounts', 'action' => 'loyalty'), false);
        }

}

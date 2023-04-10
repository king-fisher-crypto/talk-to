<?php
App::uses('AppController', 'Controller');


class InvoicesController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','Agent','InvoiceAgent','InvoiceOther','InvoiceOtherDetail','InvoiceSociety','InvoiceNum','InvoiceCustomer');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'invoices')));
	
	public function beforeFilter() {
    	parent::beforeFilter();
		$this->Auth->allow();
    }
	
	public function admin_create_customer(){

            //Création d'un client
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['InvoiceCustomer'] = Tools::checkFormField($requestData['InvoiceCustomer'],
                    array('name', 'address', 'info', 'customer', 'mail', 'phone'),
                    array('name','address')
                );
                if($requestData['InvoiceCustomer'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

                    $this->InvoiceCustomer->create();
                    if($this->InvoiceCustomer->save($requestData)){
                        $this->Session->setFlash(__('Le client a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'invoices', 'action' => 'create', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
		
		
     }
	
	public function admin_create(){
		
		$nb_product = 36;
		//load data
		$societys = $this->InvoiceSociety->find('all', array(
						'conditions' => array(),
						'recursive' => -1,
						'order' => array('name ASC')
					));
		$select_society = array();
		foreach($societys as $society){
			$select_society[$society['InvoiceSociety']['id']] = $society['InvoiceSociety']['name'];
		}
		$this->set('select_society', $select_society);
		
		$customers = $this->InvoiceCustomer->find('all', array(
						'conditions' => array(),
						'recursive' => -1,
						'order' => array('name ASC')
					));
		$select_customer = array();
		foreach($customers as $customer){
			$select_customer[$customer['InvoiceCustomer']['id']] = $customer['InvoiceCustomer']['name'];
		}
		$this->set('select_customer', $select_customer);
		$this->set('nb_product', $nb_product);
		
		  //Création d'une facture
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['Invoices'] = Tools::checkFormField($requestData['Invoices'],
                    array('preview','society_id', 'customer_id', 'date_order','vat_tx','currency','deposit','remarque','conditions','mode','ProductName1','ProductPrice1','ProductQty1','ProductName2','ProductPrice2','ProductQty2','ProductName3','ProductPrice3','ProductQty3','ProductName4','ProductPrice4','ProductQty4','ProductName5','ProductPrice5','ProductQty5','ProductName6','ProductPrice6','ProductQty6','ProductName7','ProductPrice7','ProductQty7','ProductName8','ProductPrice8','ProductQty8','ProductName9','ProductPrice9','ProductQty9','ProductName10','ProductPrice10','ProductQty10','ProductName11','ProductPrice11','ProductQty11','ProductName12','ProductPrice12','ProductQty12','ProductName13','ProductPrice13','ProductQty13','ProductName14','ProductPrice14','ProductQty14','ProductName15','ProductPrice15','ProductQty15','ProductName16','ProductPrice16','ProductQty16','ProductName17','ProductPrice17','ProductQty17','ProductName18','ProductPrice18','ProductQty18','ProductName19','ProductPrice19','ProductQty19','ProductName20','ProductPrice20','ProductQty20','ProductName21','ProductPrice21','ProductQty21','ProductName22','ProductPrice22','ProductQty22','ProductName23','ProductPrice23','ProductQty23','ProductName24','ProductPrice24','ProductQty24','ProductName25','ProductPrice25','ProductQty25','ProductName26','ProductPrice26','ProductQty26','ProductName27','ProductPrice27','ProductQty27','ProductName28','ProductPrice28','ProductQty28','ProductName29','ProductPrice29','ProductQty29','ProductName30','ProductPrice30','ProductQty30','ProductName31','ProductPrice31','ProductQty31','ProductName32','ProductPrice32','ProductQty32','ProductName33','ProductPrice33','ProductQty33','ProductName34','ProductPrice34','ProductQty34','ProductName35','ProductPrice35','ProductQty35','ProductName36','ProductPrice36','ProductQty36'),
                    array('society_id', 'customer_id', 'date_order')
                );
                if($requestData['Invoices'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				if($requestData['Invoices']['preview']){
                    $this->InvoicePreview($requestData);
                    return;
                }
				
				//get last index order_id
					$num = $this->InvoiceNum->find('first', array(
						'conditions' => array('InvoiceNum.society_id' => $requestData['Invoices']['society_id']),
						'recursive' => -1
					));
				
				if(strlen($requestData['Invoices']['date_order']) == 10 && substr_count($requestData['Invoices']['date_order'],'-') )
					$dd = explode('-',$requestData['Invoices']['date_order']);
				else
					$dd = explode('-',date('d-m-Y'));
				
				//if(strlen($requestData['Invoices']['date_due']) == 10 && substr_count($requestData['Invoices']['date_due'],'-') )
				//	$dd2 = explode('-',$requestData['Invoices']['date_due']);
				//else
					$dd2 = explode('-',date('d-m-Y'));
				
				$invoiceData = array();
				$invoiceData['InvoiceOther'] = array();
				$invoiceData['InvoiceOther']['society_id'] = $requestData['Invoices']['society_id'];
				$invoiceData['InvoiceOther']['customer_id'] = $requestData['Invoices']['customer_id'];
				$invoiceData['InvoiceOther']['order_id'] = $num['InvoiceNum']['num'] + 1;
				$invoiceData['InvoiceOther']['date_order'] = $dd[2].'-'.$dd[1].'-'.$dd[0].' 12:00:00';
				$invoiceData['InvoiceOther']['date_due'] = $dd2[2].'-'.$dd2[1].'-'.$dd2[0].' 12:00:00';
				$invoiceData['InvoiceOther']['deposit'] = str_replace(',','.',$requestData['Invoices']['deposit']);
				$invoiceData['InvoiceOther']['vat_tx'] = $requestData['Invoices']['vat_tx'];
				$invoiceData['InvoiceOther']['currency'] = $requestData['Invoices']['currency'];
				$invoiceData['InvoiceOther']['mode'] = $requestData['Invoices']['mode'];
				$invoiceData['InvoiceOther']['remarque'] = $requestData['Invoices']['remarque'];
				$invoiceData['InvoiceOther']['conditions'] = $requestData['Invoices']['conditions'];
				$invoiceData['InvoiceOther']['date_add'] = date('Y-m-d H:i:s');
				
				$this->InvoiceOther->create();
                if($this->InvoiceOther->save($invoiceData)){
					$amount = 0;
					for($nn=1;$nn<=$nb_product;$nn++){
						if($requestData['Invoices']['ProductName'.$nn]){
							$saveData = array();
							$saveData['InvoiceOtherDetail']['invoice_id'] = $this->InvoiceOther->id;
							$saveData['InvoiceOtherDetail']['type'] = 'product';
							if(!$requestData['Invoices']['ProductPrice'.$nn] || !is_numeric($requestData['Invoices']['ProductPrice'.$nn]))$requestData['Invoices']['ProductPrice'.$nn] = 0;
							$saveData['InvoiceOtherDetail']['amount'] = number_format(str_replace(',','.',$requestData['Invoices']['ProductPrice'.$nn]) ,4,'.','');
							$saveData['InvoiceOtherDetail']['label'] = $requestData['Invoices']['ProductName'.$nn];
							if(!$requestData['Invoices']['ProductQty'.$nn]) $requestData['Invoices']['ProductQty'.$nn] = 1;
							$saveData['InvoiceOtherDetail']['qty'] = $requestData['Invoices']['ProductQty'.$nn];
							$this->InvoiceOtherDetail->create();
							$this->InvoiceOtherDetail->save($saveData);
							$amount += str_replace(',','.',$requestData['Invoices']['ProductPrice'.$nn] * $requestData['Invoices']['ProductQty'.$nn]);
						}
					}
					
					//save total
					$amount = number_format($amount,4,'.','');
					$vat_tx = $requestData['Invoices']['vat_tx'];
					$vat = $amount * $vat_tx / 100;
					$vat = number_format($vat,4,'.','');
					$this->InvoiceOther->id = $this->InvoiceOther->id;
					$this->InvoiceOther->saveField('vat', $vat);
					$this->InvoiceOther->saveField('amount', $amount);
					$this->InvoiceOther->saveField('amount_total', $amount + $vat);
					
					$this->InvoiceNum->id = $num['InvoiceNum']['id'];
					$this->InvoiceNum->saveField('num', $num['InvoiceNum']['num'] + 1);
					
                        $this->Session->setFlash(__('La facture a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'invoices', 'action' => 'index', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }
     }
	
	 public function admin_index(){
		 
		 	$conditions = array();
			
			$this->Paginator->settings = array(
				'fields' => array('InvoiceOther.*','InvoiceSociety.*', 'InvoiceCustomer.*'),
				'conditions' => $conditions,
				'order' => 'InvoiceOther.order_id DESC',
				'paramType' => 'querystring',
				'joins' => array(
							array('table' => 'invoice_societys',
								'alias' => 'InvoiceSociety',
								'type' => 'inner',
								'conditions' => array('InvoiceSociety.id = InvoiceOther.society_id')
							),
							array('table' => 'invoice_customers',
								'alias' => 'InvoiceCustomer',
								'type' => 'inner',
								'conditions' => array('InvoiceCustomer.id = InvoiceOther.customer_id')
							),
						),
				'limit' => 15
			);

			$lastOrder = $this->Paginator->paginate($this->InvoiceOther);

			$this->set(compact('lastOrder'));
        }
	
	
	public function admin_edit($id){
		
		$nb_product = 36;
		//load data
		$societys = $this->InvoiceSociety->find('all', array(
						'conditions' => array(),
						'recursive' => -1,
						'order' => array('name ASC')
					));
		$select_society = array();
		foreach($societys as $society){
			$select_society[$society['InvoiceSociety']['id']] = $society['InvoiceSociety']['name'];
		}
		$this->set('select_society', $select_society);
		
		$customers = $this->InvoiceCustomer->find('all', array(
						'conditions' => array(),
						'recursive' => -1,
						'order' => array('name ASC')
					));
		$select_customer = array();
		foreach($customers as $customer){
			$select_customer[$customer['InvoiceCustomer']['id']] = $customer['InvoiceCustomer']['name'];
		}
		$this->set('select_customer', $select_customer);
		$this->set('nb_product', $nb_product);
		
						
      	 //Création d'une facture
            if($this->request->is('post')){
                $requestData = $this->request->data;
                //On vérifie les champs du formulaire
                $requestData['Invoices'] = Tools::checkFormField($requestData['Invoices'],
                    array('preview','society_id', 'customer_id', 'date_order','vat_tx','currency','deposit','remarque','conditions','mode','ProductName1','ProductPrice1','ProductQty1','ProductName2','ProductPrice2','ProductQty2','ProductName3','ProductPrice3','ProductQty3','ProductName4','ProductPrice4','ProductQty4','ProductName5','ProductPrice5','ProductQty5','ProductName6','ProductPrice6','ProductQty6','ProductName7','ProductPrice7','ProductQty7','ProductName8','ProductPrice8','ProductQty8','ProductName9','ProductPrice9','ProductQty9','ProductName10','ProductPrice10','ProductQty10','ProductName11','ProductPrice11','ProductQty11','ProductName12','ProductPrice12','ProductQty12','ProductName13','ProductPrice13','ProductQty13','ProductName14','ProductPrice14','ProductQty14','ProductName15','ProductPrice15','ProductQty15','ProductName16','ProductPrice16','ProductQty16','ProductName17','ProductPrice17','ProductQty17','ProductName18','ProductPrice18','ProductQty18','ProductName19','ProductPrice19','ProductQty19','ProductName20','ProductPrice20','ProductQty20','ProductName21','ProductPrice21','ProductQty21','ProductName22','ProductPrice22','ProductQty22','ProductName23','ProductPrice23','ProductQty23','ProductName24','ProductPrice24','ProductQty24','ProductName25','ProductPrice25','ProductQty25','ProductName26','ProductPrice26','ProductQty26','ProductName27','ProductPrice27','ProductQty27','ProductName28','ProductPrice28','ProductQty28','ProductName29','ProductPrice29','ProductQty29','ProductName30','ProductPrice30','ProductQty30','ProductName31','ProductPrice31','ProductQty31','ProductName32','ProductPrice32','ProductQty32','ProductName33','ProductPrice33','ProductQty33','ProductName34','ProductPrice34','ProductQty34','ProductName35','ProductPrice35','ProductQty35','ProductName36','ProductPrice36','ProductQty36'),
                    array('society_id', 'customer_id', 'date_order')
                );
                if($requestData['Invoices'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				if($requestData['Invoices']['preview']){
                    $this->InvoicePreview($requestData);
                    return;
                }
				
				$dd = explode('-',$requestData['Invoices']['date_order']);
				//$dd2 = explode('-',$requestData['Invoices']['date_due']);
				
				$this->InvoiceOther->id = $id;
				$this->InvoiceOther->saveField('society_id', $requestData['Invoices']['society_id']);
				$this->InvoiceOther->saveField('customer_id', $requestData['Invoices']['customer_id']);
				$this->InvoiceOther->saveField('date_order', $dd[2].'-'.$dd[1].'-'.$dd[0].' 12:00:00');
				//$this->InvoiceOther->saveField('date_due', $dd2[2].'-'.$dd2[1].'-'.$dd2[0].' 12:00:00');
				$this->InvoiceOther->saveField('deposit', $requestData['Invoices']['deposit']);
				$this->InvoiceOther->saveField('vat_tx', $requestData['Invoices']['vat_tx']);
				$this->InvoiceOther->saveField('currency', $requestData['Invoices']['currency']);
				$this->InvoiceOther->saveField('mode', $requestData['Invoices']['mode']);
				$this->InvoiceOther->saveField('remarque', $requestData['Invoices']['remarque']);
				$this->InvoiceOther->saveField('conditions', $requestData['Invoices']['conditions']);
				$this->InvoiceOther->saveField('date_upd', date('Y-m-d H:i:s'));

                if($this->InvoiceOther->id){
					$this->InvoiceOtherDetail->deleteAll(array('InvoiceOtherDetail.invoice_id'=>$this->InvoiceOther->id), false);
					$amount = 0;
					for($nn=1;$nn<=$nb_product;$nn++){
						if($requestData['Invoices']['ProductName'.$nn]){
							$saveData = array();
							$saveData['InvoiceOtherDetail']['invoice_id'] = $this->InvoiceOther->id;
							$saveData['InvoiceOtherDetail']['type'] = 'product';
							if(!$requestData['Invoices']['ProductPrice'.$nn] || !is_numeric($requestData['Invoices']['ProductPrice'.$nn]))$requestData['Invoices']['ProductPrice'.$nn] = 0;
							$saveData['InvoiceOtherDetail']['amount'] = number_format(str_replace(',','.',$requestData['Invoices']['ProductPrice'.$nn]) ,4,'.','');
							$saveData['InvoiceOtherDetail']['label'] = $requestData['Invoices']['ProductName'.$nn];
							if(!$requestData['Invoices']['ProductQty'.$nn]) $requestData['Invoices']['ProductQty'.$nn] = 1;
							$saveData['InvoiceOtherDetail']['qty'] = $requestData['Invoices']['ProductQty'.$nn];
							$this->InvoiceOtherDetail->create();
							$this->InvoiceOtherDetail->save($saveData);
							$amount += str_replace(',','.',$requestData['Invoices']['ProductPrice'.$nn] * $requestData['Invoices']['ProductQty'.$nn]);
						}
					}
					
					//save total
					$amount = number_format($amount,4,'.','');
					$vat_tx = $requestData['Invoices']['vat_tx'];
					$vat = $amount * $vat_tx / 100;
					$vat = number_format($vat,4,'.','');
					$this->InvoiceOther->id = $this->InvoiceOther->id;
					$this->InvoiceOther->saveField('vat', $vat);
					$this->InvoiceOther->saveField('amount', $amount);
					$this->InvoiceOther->saveField('amount_total', $amount + $vat);

						$this->Session->setFlash(__('La facture a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'invoices', 'action' => 'index', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
               
            }else{

				$invoice = $this->InvoiceOther->find('first', array(
					'conditions' => array('InvoiceOther.id' => $id),
					'recursive' => -1
				));

				if(empty($invoice)){
					$this->Session->setFlash(__('Facture introuvable.'),'flash_warning');
					$this->redirect(array('controller' => 'invoices', 'action' => 'index', 'admin' => true), false);
				}

			   $invoice_details = $this->InvoiceOtherDetail->find('all', array(
					'conditions' => array('InvoiceOtherDetail.invoice_id' => $invoice['InvoiceOther']['id']),
					'recursive' => -1
				));

				$this->set(array('edit' => true, 'invoice' => $invoice,'details' => $invoice_details));
				$this->render('admin_edit');
			}
        }
	
	public function admin_remove($id){
		
		$this->InvoiceOtherDetail->deleteAll(array('InvoiceOtherDetail.invoice_id'=>$id), false);
		if($this->InvoiceOther->deleteAll(array('InvoiceOther.id'=>$id), false)){
			$this->Session->setFlash(__('La facture a été supprimé'), 'flash_success');
              $this->redirect(array('controller' => 'invoices', 'action' => 'index', 'admin' => true), false);
         }else
              $this->Session->setFlash(__('Erreur lors de la suppression'),'flash_warning');
	}
	
	public function admin_voucher($id){
		
		$this->InvoiceOther->id = $id;
		if($this->InvoiceOther->saveField('status', 2)){
			$this->Session->setFlash(__('L avoir a été créé'), 'flash_success');
              $this->redirect(array('controller' => 'invoices', 'action' => 'index', 'admin' => true), false);
         }else
              $this->Session->setFlash(__('Erreur lors de la creation'),'flash_warning');
	}
	
	public function InvoicePreview($requestData){
		$_SESSION['fact_other'] = $requestData;
		$this->redirect('/fact_other2_preview.php');
		exit;
	}
	
}
<?php
App::uses('AppController', 'Controller');


class VatController extends AppController {
	    public $components = array('Paginator');
        public $uses = array('User','UserCountry','UserCountryLang','InvoiceVat','SocietyType');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'vat')));
	
	
	public function beforeFilter() {
    	parent::beforeFilter();
    }
	
	public function admin_create(){
			
			$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
			$this->set('select_societies', $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang')));
		
            //Création d'un coupon
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formulaire
                $requestData['InvoiceVat'] = Tools::checkFormField($requestData['InvoiceVat'],
                    array('country_id', 'society_type_id', 'rate', 'description', 'show_vat_num', 'show_siret'),
                    array('country_id', 'society_type_id')
                );
                if($requestData['InvoiceVat'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }

				$requestData['InvoiceVat']['rate'] = str_replace(',','.',$requestData['InvoiceVat']['rate']);
                    $this->InvoiceVat->create();
                    if($this->InvoiceVat->save($requestData)){
                        $this->Session->setFlash(__('La TVA a été enregistré'), 'flash_success');
                        $this->redirect(array('controller' => 'vat', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de l\'enregistrement'),'flash_warning');
                
            }
		
			
        }

	
	  public function admin_list(){
            
            $this->Paginator->settings = array(
				 'fields' => array('InvoiceVat.*','Country.name','Society.name'),
                'order' => 'Country.name,Society.name,InvoiceVat.rate',
				 'joins' => array(
					array('table' => 'user_country_langs',
						'alias' => 'Country',
						'type' => 'left',
						'conditions' => array(
							'Country.user_countries_id = InvoiceVat.country_id',
							'Country.lang_id = 1',
						)
					),
					 array('table' => 'society_types',
						'alias' => 'Society',
						'type' => 'left',
						'conditions' => array(
							'Society.id = InvoiceVat.society_type_id',
						)
					)
				),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $vats = $this->Paginator->paginate($this->InvoiceVat);

            $this->set(compact('vats'));
        }
		
		public function admin_edit($id){
			
			$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
			$this->set('select_societies', $this->SocietyType->getTypeForSelect($this->Session->read('Config.id_lang')));
						
            if($this->request->is('post') || $this->request->is('put')){
				
                $this->set(array('edit' => true));
                $requestData = $this->request->data;

               //On vérifie les champs du formulaire
                $requestData['InvoiceVat'] = Tools::checkFormField($requestData['InvoiceVat'],
                    array('country_id', 'society_type_id', 'rate', 'description', 'show_vat_num', 'show_siret'),
                    array('country_id', 'society_type_id')
                );
                if($requestData['InvoiceVat'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    return;
                }
				
				$requestData['InvoiceVat']['description'] = "'".addslashes($requestData['InvoiceVat']['description'])."'";
				$requestData['InvoiceVat']['rate'] = str_replace(',','.',$requestData['InvoiceVat']['rate']);


                //Si la modif a réussi
                    if($this->InvoiceVat->updateAll(
                        $requestData['InvoiceVat'],
                        array('InvoiceVat.id' => $id))
                    ){
                        $this->Session->setFlash(__('La TVA a été modifié'), 'flash_success');
                        $this->redirect(array('controller' => 'vat', 'action' => 'list', 'admin' => true), false);
                    }else
                        $this->Session->setFlash(__('Erreur lors de la modification'),'flash_warning');
                }else{

            $vat = $this->InvoiceVat->find('first', array(
                'conditions' => array('InvoiceVat.id' => $id),
                'recursive' => -1
            ));


            if(empty($vat)){
                $this->Session->setFlash(__('TVA introuvable.'),'flash_warning');
                $this->redirect(array('controller' => 'vat', 'action' => 'list', 'admin' => true), false);
            }

            //On insère les données
            $this->request->data = $vat;
            $this->set(array('edit' => true, 'vat' => $vat));
            $this->render('admin_edit');
				}
        }


}

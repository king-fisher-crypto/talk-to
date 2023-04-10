<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');
App::import('Controller', 'Category');

class SubscribesController extends AppController {
	
 	public $components = array('Paginator');
    public $helpers = array('Paginator','Time');

    public function beforeFilter()
    {
        if ($this->request->is('ajax')){
            $this->layout = 'ajax';
            $this->set('isAjax',1);
        }
        
        parent::beforeFilter();

    }
	

    public function admin_create($post = true)
    {
		
		//Les domains pour les checkbox
		$this->loadModel('Domain');
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);
            $this->set(compact('domain_select', 'count', 'half'));
		
		
        if ($this->request->is('post') && $post) {
            $requestData = $this->request->data;
			
			$this->request->data['Subscribe']['domain'] = '';
			if(is_array($this->request->data['domain'])){
				foreach($this->request->data['domain'] as $kd => $vd){
					if($vd)
						$this->request->data['Subscribe']['domain'] = $kd;
				}
			}
            //Modification des url pour les images
           /* $this->request->data['SubscribeLang'][0]['content'] = Tools::clearUrlImage($this->request->data['SubscribeLang'][0]['content']);
            if($this->request->data['SubscribeLang'][0]['content'] === false){
                $this->Session->setFlash(__('Une erreur est survenue avec le contenu, votre contenu est sûrement vide.'),'flash_warning');
                $this->admin_create(false);
                return;
            }*/

            $this->Subscribe->create();
            $this->Subscribe->saveAssociated($this->request->data);

            //On regénère le footer
            //$this->regenCacheFooter();

            $this->Session->setFlash(__('La page a bien été enregistrée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
            $this->redirect(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        $this->set('lang_options', $this->Lang->find('list', array(
            'conditions' => array('active' => 1)
        )));

        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);


        $this->Paginator->settings = array(
            'fields' => array('SubscribeLang.*', 'Subscribe.*', 'Subscribe.active', 'Lang.name', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'subscribes',
                    'alias' => 'Subscribe',
                    'type' => 'left',
                    'conditions' => array('Subscribe.id = SubscribeLang.subscribe_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = SubscribeLang.lang_id')
                ),
               
            ),
            'recursive' => -1,
            'group' => 'Subscribe.id',
            'order' => 'Subscribe.id DESC',
            'paramType' => 'querystring',
            'limit' => 50
        );

        $tmp_pages = $this->Paginator->paginate($this->Subscribe->SubscribeLang);

        $pages = array();
        foreach ($tmp_pages AS $page){
            $pageTransit = end($pages);

            //S'il y a un élément d'enregistrer
            if($pageTransit != false){
                if($pageTransit['subscribe_id'] == $page['Subscribe']['id']){
                    $keys = array_keys($pages);
                    $lastKey = end($keys);
                    $pages[$lastKey]['lang_name'].= ', '.$page['Lang']['name'];
                    continue;
                }
            }


            $pages[] = array(
                'langs'         =>  str_replace(",",", ",isset($page['0']['langs'])?$page['0']['langs']:''),
                'subscribe_id'       =>  $page['SubscribeLang']['subscribe_id'],
				'domain' => $page['Subscribe']['domain'],
                'etat'          =>  ($page['Subscribe']['active']
                                        ?'<span class="badge badge-success">'.__('Active').'</span>'
                                        :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                    ),
                'lang_name'     =>  $page['Lang']['name'],
                'active'        =>  $page['Subscribe']['active'],
            );
        }

        $this->set(compact('pages', 'langs'));
    }

    public function admin_delete($id){
        $this->Subscribe->id = $id;
        if($this->Subscribe->saveField('active', 0)){
            //On regénère le footer
           // $this->regenCacheFooter();
            $this->Session->setFlash(__('La page est désactivée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la désactivation.'),'flash_warning');

        $this->redirect(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_true_delete($id){
        //On supprime les langues
        $this->Subscribe->SubscribeLang->deleteAll(array('SubscribeLang.subscribe_id' => $id), false);
        //On supprime la page
        if($this->Subscribe->deleteAll(array('Subscribe.id' => $id), false)){
            //On regénère le footer
           // $this->regenCacheFooter();
            $this->Session->setFlash(__('La page a été supprimée. Si votre suppression affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de la suppression.'),'flash_warning');

        $this->redirect(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_add($id){
        $this->Subscribe->id = $id;
        if($this->Subscribe->saveField('active', 1)){
            //On regénère le footer
            //$this->regenCacheFooter();
            $this->Session->setFlash(__('La page est activée. Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', array('link' => Router::url(array('controller' => 'menus', 'action' => 'menu', 'admin' => true, '?' => 'modif'), true), 'messageLink' => __('ICI')));
        }else
            $this->Session->setFlash(__('Erreur lors de l\'activation.'),'flash_warning');

        $this->redirect(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_edit($id, $post=true){
		
		//Les domains pour les checkbox
		$this->loadModel('Domain');
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('domain_select', 'count', 'half'));
		
        if($this->request->is('post') && $post){
            $requestData = $this->request->data;
			
			
				foreach($requestData['SubscribeLang'] as $key => $val){
					
					//Si le formulaire est vide
					if(!$requestData['SubscribeLang'][$key]['timing']){
						//On le supprime
						unset($requestData['SubscribeLang'][$key]);
						continue;
					}
				}
				

				//Si aucune page n'est renseigné
				if(empty($requestData['SubscribeLang'])){
					$this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
					$this->redirect(array('controller' => 'subscribes', 'action' => 'edit', 'admin' => true, 'id' => $id),false);
				}
					/* Cas des pages normales */
					$the_fields = array('lang_id','subscribe_id','timing','block1','block2','block3','intro1','intro2', 'intro3','intro4', 'intro5' );
						$the_required = array('lang_id','subscribe_id');

				foreach($requestData['SubscribeLang'] as $key => $val){
					//Vérification des champs requis

					$requestData['LandingLang'][$key]['block1'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block1']);
					$requestData['LandingLang'][$key]['block1'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block1'],'"/media','"'.Configure::read('Site.baseUrlFull').'/media');
					$requestData['LandingLang'][$key]['block1'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block1'],'"/theme','"'.Configure::read('Site.baseUrlFull').'/theme');
					
					$requestData['LandingLang'][$key]['block2'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block2']);
					$requestData['LandingLang'][$key]['block2'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block2'],'"/media','"'.Configure::read('Site.baseUrlFull').'/media');
					$requestData['LandingLang'][$key]['block2'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block2'],'"/theme','"'.Configure::read('Site.baseUrlFull').'/theme');
					
					$requestData['LandingLang'][$key]['block3'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block3']);
					$requestData['LandingLang'][$key]['block3'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block3'],'"/media','"'.Configure::read('Site.baseUrlFull').'/media');
					$requestData['LandingLang'][$key]['block3'] = Tools::clearUrlImage($requestData['LandingLang'][$key]['block3'],'"/theme','"'.Configure::read('Site.baseUrlFull').'/theme');
					
					
					if($requestData['LandingLang'][$key]['intro1'] === false){
						$this->Session->setFlash(__('Une erreur est survenue avec une des langues, votre contenu est sûrement vide.'),'flash_warning');
						$this->admin_edit($id, false);
						return;
					}
				}

			
				//L'etat de la page
				$this->Subscribe->id = $id;
				$this->Subscribe->save($requestData['Subscribe']);
				//On supprime toutes les langues
				$this->Subscribe->SubscribeLang->deleteAll(array('SubscribeLang.subscribe_id' => $id), false);
					
				$this->Subscribe->SubscribeLang->saveMany($requestData['SubscribeLang']);

				$this->Session->setFlash(__('La page a été modifiée.'), 'flash_success');

				$this->redirect(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true), false);
        }

        $this->loadModel('Lang');
        //L'id de la langue, le code, le nom de la langue
        $langs = $this->Lang->getLang(true);

        //On récupère toutes les infos des pages
        $pages = $this->Subscribe->find('all',array(
            'conditions' => array('Subscribe.id' => $id),
            'recursive' => 1
        ));

        //Un tableau qui contient les données pour chaque langue renseigné
        foreach($pages[0]['SubscribeLang'] as $pageLang){
            $langDatas[$pageLang['lang_id']] = $pageLang;
        }

        //Etat de la page
        $activePage = $pages[0]['Subscribe']['active'];
		
		$domainPage = $pages[0]['Subscribe']['domain'];

        //Variable qui stocke l'id de la page, pour un accès plus rapide
        $idPage = $pages[0]['Subscribe']['id'];


        $this->set(compact('langDatas', 'langs', 'idPage', 'activePage', 'domainPage'));
    }

    public function admin_list()
    {
        $this->admin_create();

        $this->render('admin_create');
    }

}
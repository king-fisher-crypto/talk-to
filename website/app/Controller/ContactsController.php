<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');

class ContactsController extends AppController {

    public $uses = array('Message', 'Guest', 'SupportService');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('answer','send');
    }


    public function index($link_rewrite="")
    {
        $guest = false;
        //Invité ??
        if(!$this->Auth->loggedIn() || $this->Auth->user('role') === 'admin')
            $guest = true;
		
		$idlang = $this->Session->read('Config.id_lang');
		$conditions = array(
            'Page.active'           => 1,
			'Page.id'           => 32,
            'PageLang.lang_id'      => $idlang
        );
		
		/*$this->Page->PageLang->bindModel(array(
            'belongsTo' => array(
                'PageCategory' => array(
                    'className' => 'PageCategory',
                    'foreignKey' => '',
                    'conditions' => 'Page.page_category_id = PageCategory.id',
                    'fields' => '',
                    'order' => ''
                )
            )
        ));*/

		App::import('Controller', 'Pages');
        $pp = new PagesController();
		$pp->loadModel('PageLang');				
		 $page = $pp->PageLang->find('first',array('conditions' => $conditions));
		/* Metas */
		
        $this->site_vars['meta_title']       = $page['PageLang']['meta_title'];
        $this->site_vars['meta_keywords']    = $page['PageLang']['meta_keywords'];
        $this->site_vars['meta_description'] = $page['PageLang']['meta_description'];
		
		
		$who = 'public';
		if($this->Auth->user('role') === 'agent')$who = 'agent';
		if($this->Auth->user('role') === 'client')$who = 'client';
		
		$services = $this->SupportService->find('all',array(
				'conditions' => array('SupportService.who' => $who),
                'recursive' => -1,
            ));
		$list_services	 = array();
		foreach($services as $service){
			$list_services[$service['SupportService']['id']] = $service['SupportService']['description'];
		}

        $this->set(compact('guest', 'list_services'));
    }
	
	public function send($link_rewrite="")
    {
        App::import('Vendor', 'Noox/ApiVonage');

        $apiVonage = new ApiVonage();



//		$idlang = $this->Session->read('Config.id_lang');
//		$conditions = array(
//            'Page.active'           => 1,
//			'Page.id'           => 453,
//            'PageLang.lang_id'      => $idlang
//        );
//
//
//		App::import('Controller', 'Pages');
//        $pp = new PagesController();
//		$pp->loadModel('PageLang');
//		$page = $pp->PageLang->find('first',array('conditions' => $conditions));
//		/* Metas */
//
//        $this->site_vars['meta_title']       = $page['PageLang']['meta_title'];
//        $this->site_vars['meta_keywords']    = $page['PageLang']['meta_keywords'];
//        $this->site_vars['meta_description'] = $page['PageLang']['meta_description'];

		
    }

    public function subscribe(){
        if($this->request->is('post')){
            $requestData = $this->request->data;
			
			//captcha
			if(!$requestData['g-recaptcha-response']){
				$this->Session->setFlash(__('Veuillez valider le Captcha.'),'flash_error');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
			}
			
			
            //On vérifie les champs du formualaire
            $requestData['Contact'] = Tools::checkFormField($requestData['Contact'], array('message', 'nom', 'prenom', 'email'), array('message', 'nom', 'prenom', 'email'));
            if($requestData['Contact'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }

            //On vérifie l'email
            if(!filter_var($requestData['Contact']['email'], FILTER_VALIDATE_EMAIL)){
                $this->Session->setFlash(__('Votre email est invalide.'),'flash_warning');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }

            //On crée le profil de l'invité
            $this->Guest->create();
            if($this->Guest->save(array(
                'lastname' => $requestData['Contact']['nom'],
                'firstname' => $requestData['Contact']['prenom'],
                'domain_id' => $this->Session->read('Config.id_domain'),
                'lang_id' => $this->Session->read('Config.id_lang'),
                'email' => $requestData['Contact']['email'],
                'ip' => $this->request->clientIp()
            ))){
                $this->Message->create();
                if($this->Message->save(array(
                    'from_id'   => Configure::read('Guest.id'),
                    'guest_id'  => $this->Guest->id,
                    'to_id'     => Configure::read('Admin.id'),
                    'content'   => $requestData['Contact']['message'],
                    'private'   => 1,
                    'etat'      => 0,
					'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
                ))){
				
				$bodymail = 'Consulter : <a href="https://fr.spiriteo.com/admin/admins/mails">http://www.talkappdev.com/admin/admins/mails</a>';
					$this->sendEmail(
						'contact@talkappdev.com',
						'Vous avez reçu un nouveau message dans la boite " Contact " de Spiriteo',
						'default',array('content' => $bodymail)
					);
				
				
                    $this->Session->setFlash(__('Votre message est envoyé au service administrateur Spiriteo qui vous répondra dans un délai maximum est de 24h.'), 'flash_success');
				}else
                    $this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre message.'), 'flash_warning');
            }else{
                $this->Session->setFlash(__('Une erreur est survenue. Veuillez réessayer'),'flash_warning');
                $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
            }
        }
        $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function discussion(){
        if($this->request->is('post')){
            //Utilisateur connecté et client ou agent uniquement
            if($this->Auth->loggedIn() && in_array($this->Auth->user('role'), array('agent', 'client'))){
                $requestData = $this->request->data;

                //On vérifie les champs du formualaire
                $requestData['Contact'] = Tools::checkFormField($requestData['Contact'], array('message'), array('message'));
                if($requestData['Contact'] === false){
                    $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_error');
                    $this->redirect(array('controller' => 'contacts', 'action' =>'index'));
                }

                //On crée la discussion
                $this->Message->create();
                if($this->Message->save(array(
                    'from_id'   => $this->Auth->user('id'),
                    'to_id'     => Configure::read('Admin.id'),
                    'content'   => $requestData['Contact']['message'],
                    'private'   => 1,
                    'etat'      => 0,
					'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
                ))){
					
				$bodymail = 'Consulter : <a href="https://fr.spiriteo.com/admin/admins/mails">http://www.talkappdev.com/admin/admins/mails</a>';
					$this->sendEmail(
						'contact@talkappdev.com',
						'Vous avez reçu un nouveau message dans la boite " Contact " de Spiriteo',
						'default',array('content' => $bodymail)
					);
					
					
                    $this->Session->setFlash(__('Votre message est envoyé au service administrateur Spiriteo qui vous répondra dans un délai maximum est de 24h.'), 'flash_success');
				}else
                    $this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre message.'), 'flash_warning');
            }
        }
        $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }

    public function answer(){
        if(isset($this->request->query['token']) && !empty($this->request->query['token'])){
            //On récupère le token
            $token = $this->request->query['token'];
            //On recherche le token dans les guest
            $guest = $this->Guest->find('first', array(
                'conditions'    => array('Guest.answer_token' => $token),
                'recursive'     => -1
            ));

            //Si pas de guest, redirection home
            if(empty($guest))
                $this->redirect(array('controller' => 'home', 'action' => 'index'));

            //Post une réponse
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On vérifie les champs du formualaire
                $requestData['Contact'] = Tools::checkFormField($requestData['Contact'], array('message', 'token'), array('message', 'token'));
                if($requestData['Contact'] === false){
                    $this->Session->setFlash(__('Une erreur est survenue.'),'flash_error');
                    $this->redirect(array('controller' => 'home', 'action' => 'index'));
                }

                //Token invalide
                if(strcmp($token, $requestData['Contact']['token']) != 0){
                    $this->Session->setFlash(__('Une erreur de sécurité est survenue.'),'flash_error');
                    $this->redirect(array('controller' => 'home', 'action' => 'index'));
                }

                //On ajoute l'id dans la liste des id du guest
                $listIp = explode(',', $guest['Guest']['ip']);
                //Si l'ip n'est pas déjà enregistré
                if(!in_array($this->request->clientIp(), $listIp)){
                    $this->Guest->id = $guest['Guest']['id'];
                    $this->Guest->saveField('ip', implode(',', array($guest['Guest']['ip'], $this->request->clientIp())));
                }

                //On va chercher la discussion
                $discussion = $this->Message->find('first', array(
                    'fields'        => array('Message.id'),
                    'conditions'    => array('Message.parent_id' => null, 'Message.guest_id' => $guest['Guest']['id'], 'Message.deleted' => 0, 'Message.archive' => 0),
                    'recursive'     => -1
                ));

                //Si pas de discussion, redirection home
                if(empty($discussion)){
                    $this->Session->setFlash(__('Votre discussion n\'existe plus.'),'flash_warning');
                    $this->redirect(array('controller' => 'home', 'action' => 'index'));
                }

                //On save le message
                $this->Message->create();
                if($this->Message->save(array(
                    'parent_id' => $discussion['Message']['id'],
                    'from_id'   => Configure::read('Guest.id'),
                    'guest_id'  => $guest['Guest']['id'],
                    'to_id'     => Configure::read('Admin.id'),
                    'content'   => $requestData['Contact']['message'],
                    'private'   => 1,
                    'etat'      => 0,
					'IP'		=> getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR')
                ))){
                    //On supprime le token
                    $this->Guest->id = $guest['Guest']['id'];
                    $this->Guest->saveField('answer_token', null);
					
					$bodymail = 'Consulter : <a href="https://fr.spiriteo.com/admin/admins/mails">http://www.talkappdev.com/admin/admins/mails</a>';
					$this->sendEmail(
						'contact@talkappdev.com',
						'Vous avez reçu un nouveau message dans la boite " Contact " de Spiriteo',
						'default',array('content' => $bodymail)
					);
					
					
                    $this->Session->setFlash(__('Votre message est envoyé au service administrateur Spiriteo qui vous répondra dans un délai maximum est de 24h.'), 'flash_success');
                }
                else
                    $this->Session->setFlash(__('Erreur lors de l\'enregistrement de votre message.'), 'flash_warning');

                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

            $this->set(compact('guest'));
        }else
            $this->redirect(array('controller' => 'home', 'action' => 'index'));
    }
}
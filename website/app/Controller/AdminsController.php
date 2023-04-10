<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');

class AdminsController extends AppController
{
	public $layout = 'admin_layout';
	protected $myRole = 'admin';
	public $uses = array('User');
	public $components = array('Paginator');
	public $helpers = array('Paginator');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->deny();
		$this->Auth->allow('login', 'passwdforget', 'newpasswd', 'modereRelanceAgent', 'refusRelanceAgent', 'deleteMessagePrivate', 'archiveMessagePrivate', 'livecom', 'getListingAgentsComm', 'getCommunicationData', 'save_declarer_incident', 'save_declarer_incident_email');
	}

	public function admin_downloadAttachment($name)
	{
		//Si pas de nom, redirection mails
		if (empty($name)) {
			$this->Session->setFlash(__('Le fichier est introuvable.'), 'flash_warning');
			$this->redirect(array('action' => 'mails'));
		}
		//Est-ce que le fichier existe ??
		$name = str_replace('_', '-', $name);

		$filename = Configure::read('Site.pathAttachment') . '/' . $name[0] . '/' . $name[1] . '/' . $name;

		if (file_exists($filename)) {
			//Charge le model
			$this->loadModel('Message');
			//Est-il autorisé à lire cette pièce jointe ??
			$name = str_replace('-2-', '-', $name);
			$infoFile = explode('-', $name);
			$idMail = explode('.', $infoFile[1]);
			$this->response->file($filename, array('download' => true, 'name' => __('Pièce jointe') . '.jpg'));
			return $this->response;
		}
		$this->Session->setFlash(__('Le fichier n\'existe pas.'), 'flash_warning');
		$this->redirect(array('action' => 'mails'));
	}

	public function admin_watchmails()
	{
		ini_set("memory_limit", -1);
		$this->loadModel('Message');
		$this->loadModel('AdminAction');
		$conditions = array('Message.private' => 0, 'Message.deleted' => 0);

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte'])
				$this->Session->write('MessageTexte', $this->params->data['Admin']['texte']);
			else
				$this->Session->write('MessageTexte', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
			if ($this->request->data['Admin']['content'])
				$this->Session->write('MessageContent', $this->params->data['Admin']['content']);
			else
				$this->Session->write('MessageContent', '');
			if ($this->request->data['Admin']['id'])
				$this->Session->write('MessageId', $this->params->data['Admin']['id']);
			else
				$this->Session->write('MessageId', '');
		}



		if ($this->Session->read('MessageTexte')) {
			$conditions = array('Message.private' => 0, 'Message.content LIKE ' => '%' . $this->Session->read('MessageTexte') . '%');
			$this->set('filtre_texte', $this->Session->read('MessageTexte'));
		}
		if ($this->Session->read('MessageClient')) {
			$conditions = array('Message.private' => 0);
			$conditions['OR'] = array('From.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%');
			$this->set('filtre_client', $this->Session->read('MessageClient'));
		}
		if ($this->Session->read('MessageContent') && isset($this->request->data['AdminMessageid'])) {
			$this->Message->updateAll(array('content' => "'" . addslashes($this->Session->read('MessageContent')) . "'", 'etat' => 0), array('Message.id' => $this->request->data['AdminMessageid']));

			$this->AdminAction->create();
			$this->AdminAction->save(array(
				'type' => 'mail',
				'user_id' => $this->Auth->user('id'),
				'type_id' => $this->request->data['AdminMessageid'],
				'date_action' => date('Y-m-d H:i:s'),
				'date_text' => $this->Message->field('date_add', array('id' => $this->request->data['AdminMessageid']))
			));
		}

		if ($this->Session->read('MessageId')) {
			$conditions = array('Message.private' => 0);
			$conditions['OR'] = array('Message.id' => $this->Session->read('MessageId'), 'Message.parent_id' => $this->Session->read('MessageId'));
			$this->set('filtre_id', $this->Session->read('MessageId'));
		}

		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		/* $this->Paginator->settings = array(
            'fields' => array('Message.id','Message.parent_id','Message.private','Message.content','Message.attachment','Message.attachment2','Message.etat','Message.archive','Message.deleted','From.firstname','From.id','From.pseudo','From.lastname','From.email','To.id','To.firstname','To.lastname','To.email','To.pseudo','To.role','From.role',
            'Message.date_add','Message.private', 'AdminAction.date_action', 'AdminAction.date_text', 'COUNT(AdminAction.id) > 0 as hasAdminActions', 'AdminAction.user_id'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'admin_actions',
                    'alias' => 'AdminAction',
                    'type' => 'left',
                    'conditions' => array('AdminAction.type_id = Message.id', 'AdminAction.type = "mail"')
                )
            ),
            'recursive' => 1,
            'order' => 'Message.date_add DESC',
            'paramType' => 'querystring',
            'limit' => 50,
            'group' => 'Message.id'
        );*/

		$this->Paginator->settings = array(
			'fields' => array(
				'Message.id', 'Message.parent_id', 'Message.private', 'Message.content', 'Message.attachment', 'Message.attachment2', 'Message.etat', 'Message.archive', 'Message.deleted', 'From.firstname', 'From.id', 'From.pseudo', 'From.lastname', 'From.email', 'To.id', 'To.firstname', 'To.lastname', 'To.email', 'To.pseudo', 'To.role', 'From.role',
				'Message.date_add', 'Message.private'
			),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Message.date_add DESC',
			'paramType' => 'querystring',
			'limit' => 50,
			'group' => 'Message.id'
		);


		$messages = $this->Paginator->paginate($this->Message);

		//check delai
		foreach ($messages as &$message) {
			if ($message['Message']['parent_id'] && $message['From']['pseudo']) {

				$last = $this->Message->find('first', array(
					'fields' => array('Message.date_add'),
					'conditions' => array('Message.parent_id' => $message['Message']['parent_id'], 'Message.from_id !=' => $message['From']['id']),
					'order' => 'Message.date_add DESC',
					'recursive' => -1
				));

				if (!$last) {
					$last = $this->Message->find('first', array(
						'fields' => array('Message.date_add'),
						'conditions' => array('Message.id' => $message['Message']['parent_id'], 'Message.from_id !=' => $message['From']['id']),
						'order' => 'Message.date_add DESC',
						'recursive' => -1
					));
				}

				if ($last) {

					$date = new DateTime($message['Message']['date_add']);
					$timestamp_rep = $date->getTimestamp();
					$date_last = new DateTime($last['Message']['date_add']);
					$timestamp_last = $date_last->getTimestamp();

					if ($timestamp_rep > $timestamp_last)
						$message['Message']['delay'] = gmdate("H:i:s", $timestamp_rep - $timestamp_last);
					else
						$message['Message']['delay'] = gmdate("H:i:s", $timestamp_last - $timestamp_rep);
				}
			}

			if ($message[0]['hasAdminActions']) {
				$date = new DateTime($message['AdminAction']['date_action']);
				$timestamp_action = $date->getTimestamp();
				$date_last = new DateTime($message['AdminAction']['date_text']);
				$timestamp_message = $date_last->getTimestamp();

				$message['Message']['validation_time'] = 'Débloqué par ' . $this->User->field('firstname', array('id' => $message['AdminAction']['user_id'])) . ' le ' . CakeTime::format($message['AdminAction']['date_action'], '%Y-%m-%d %H:%M:%S'); //gmdate("H:i:s", $timestamp_action - $timestamp_message);
			}
		}


		$this->set(compact('messages', 'filtres'));
	}
	public function admin_watchmessages()
	{
		ini_set("memory_limit", -1);
		$this->loadModel('Message');
		$this->loadModel('AdminAction');
		$conditions = array('Message.private' => 1, 'Message.from_id !=' => 1, 'Message.deleted' => 0);

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte'])
				$this->Session->write('MessageTexte', $this->params->data['Admin']['texte']);
			else
				$this->Session->write('MessageTexte', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
			if ($this->request->data['Admin']['content'])
				$this->Session->write('MessageContent', $this->params->data['Admin']['content']);
			else
				$this->Session->write('MessageContent', '');
		}

		if ($this->Session->read('MessageTexte')) {
			$conditions = array('Message.private' => 1, 'Message.content LIKE ' => '%' . $this->Session->read('MessageTexte') . '%');
			$this->set('filtre_texte', $this->Session->read('MessageTexte'));
		}
		if ($this->Session->read('MessageClient')) {
			$conditions = array('Message.private' => 1, 'Message.from_id !=' => 1);
			$conditions['OR'] = array('From.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%');
			$this->set('filtre_client', $this->Session->read('MessageClient'));
		}

		if ($this->Session->read('MessageContent') && isset($this->request->data['AdminMessageid'])) {
			$this->Message->updateAll(array('content' => "'" . addslashes($this->Session->read('MessageContent')) . "'", 'etat' => 0), array('Message.id' => $this->request->data['AdminMessageid']));

			$this->AdminAction->create();
			$this->AdminAction->save(array(
				'type' => 'message',
				'user_id' => $this->Auth->user('id'),
				'type_id' => $this->request->data['AdminMessageid'],
				'date_action' => date('Y-m-d H:i:s'),
				'date_text' => $this->Message->field('date_add', array('id' => $this->request->data['AdminMessageid']))
			));
		}

		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		/* $this->Paginator->settings = array(
            'fields' => array('Message.id','Message.parent_id','Message.private','Message.content','Message.attachment','Message.attachment2','Message.etat','From.firstname','From.id','From.pseudo','From.lastname','From.email','To.id','To.firstname','To.lastname','To.email','To.pseudo','To.role','From.role',
            'Message.date_add','Message.private','Message.archive', 'AdminAction.date_action', 'AdminAction.date_text', 'COUNT(AdminAction.id) > 0 as hasAdminActions', 'AdminAction.user_id'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'admin_actions',
                    'alias' => 'AdminAction',
                    'type' => 'left',
                    'conditions' => array('AdminAction.type_id = Message.id', 'AdminAction.type = "message"')
                )
            ),
            'recursive' => 1,
            'order' => 'Message.date_add DESC',
            'paramType' => 'querystring',
            'limit' => 50,
            'group' => 'Message.id'
        );*/

		$this->Paginator->settings = array(
			'fields' => array(
				'Message.id', 'Message.parent_id', 'Message.private', 'Message.content', 'Message.attachment', 'Message.attachment2', 'Message.etat', 'From.firstname', 'From.id', 'From.pseudo', 'From.lastname', 'From.email', 'To.id', 'To.firstname', 'To.lastname', 'To.email', 'To.pseudo', 'To.role', 'From.role',
				'Message.date_add', 'Message.private', 'Message.archive'
			),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Message.date_add DESC',
			'paramType' => 'querystring',
			'limit' => 50,
			'group' => 'Message.id'
		);


		$messages = $this->Paginator->paginate($this->Message);

		foreach ($messages as &$message) {
			if ($message[0]['hasAdminActions']) {
				$date = new DateTime($message['AdminAction']['date_action']);
				$timestamp_action = $date->getTimestamp();
				$date_last = new DateTime($message['AdminAction']['date_text']);
				$timestamp_message = $date_last->getTimestamp();

				$message['Message']['validation_time'] = 'Débloqué par ' . $this->User->field('firstname', array('id' => $message['AdminAction']['user_id'])) . ' le ' . CakeTime::format($message['AdminAction']['date_action'], '%Y-%m-%d %H:%M:%S'); //gmdate("H:i:s", $timestamp_action - $timestamp_message);
			}
		}

		$this->set(compact('messages', 'filtres'));
	}
	public function admin_deletemessageattachment()
	{
		$this->loadModel('Message');
		$id = empty($_GET['id']) ? 0 : (int) $_GET['id'];
		$num = empty($_GET['num']) ? 0 : (int) $_GET['num'];
		if (!$id || !in_array($num, [1, 2], true)) {
			$this->Session->setFlash(__('Paramètres incorrectes !'), 'flash_warning');
			$this->redirect(array('controller' => 'Admins', 'action' => 'watchmessages', 'admin' => true), false);
			return;
		}

		$message = $this->Message->find('first', array(
			'fields' => array('Message.id', 'Message.attachment', 'Message.attachment2'),
			'conditions' => array('Message.id' => $id),
			'recursive' => 0
		));

		if (empty($message)) {
			$this->Session->setFlash(__('Le message n\'a pas été trouvé !'), 'flash_warning');
			$this->redirect(array('controller' => 'Admins', 'action' => 'watchmessages', 'admin' => true), false);
			return;
		}

		$key = $num === 1 ? 'attachment' : 'attachment2';
		$filename = !$message['Message'][$key] ? '' : Configure::read('Site.pathAttachment') . '/' . $message['Message'][$key][0] . '/' . $message['Message'][$key][1] . '/' . $message['Message'][$key];
		if (!$filename || !file_exists($filename)) {
			$this->Session->setFlash(__('La pièce jointe n\'existe pas (ou plus) !') . $filename, 'flash_warning');
			$this->redirect(array('controller' => 'Admins', 'action' => 'watchmessages', 'admin' => true), false);
			return;
		}

		$this->Message->id = $id;
		if (!$this->Message->saveField($key, '')) {
			$this->Session->setFlash(__('La pièce jointe n\'a pas pu être supprimée !'), 'flash_warning');
			$this->redirect(array('controller' => 'Admins', 'action' => 'watchmessages', 'admin' => true), false);
			return;
		}
		@unlink($filename);
		$this->Session->setFlash(__('Piece jointe supprimée !'), 'flash_success');
		$this->redirect(array('controller' => 'Admins', 'action' => 'watchmessages', 'admin' => true), false);
	}

	public function admin_filtersmessage()
	{
		$this->loadModel('FiltreMessage');

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['terme']) {
				$filtre = array(
					'terme'              => $this->request->data['Admin']['terme']
				);

				$this->FiltreMessage->create();
				$this->FiltreMessage->save($filtre);
			}
		}

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte'])
				$conditions = array('FiltreMessage.terme LIKE ' => '%' . $this->request->data['Admin']['texte'] . '%');
		}


		$this->Paginator->settings = array(
			'fields' => array('FiltreMessage.id', 'FiltreMessage.terme'),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'FiltreMessage.terme ASC',
			'paramType' => 'querystring',
			'limit' => 50
		);


		$filtres = $this->Paginator->paginate($this->FiltreMessage);

		$this->set(compact('filtres'));
	}


	public function admin_delete_filtre($id)
	{
		$this->loadModel('FiltreMessage');
		$this->FiltreMessage->delete($id, false);
		/*$dbb_patch = new DATABASE_CONFIG();
		$dbb_connect = $dbb_patch->default;
		$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
		$mysqli_connect->query("DELETE from filtre_messages WHERE id = '{$id}'");*/

		$this->redirect(array('controller' => 'admins', 'action' => 'filtersmessage', 'admin' => true), false);
	}

	public function admin_tchat_accept_group()
	{
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			if ($this->request->data['liste']) {
				foreach ($this->request->data['liste'] as $tchat_id) {
					$this->admin_accept_chat_indesirable($tchat_id, false);
				}
			}
		}
		$this->Session->setFlash('Votre action groupée a été réalisé.', 'flash_success');
		$this->jsonRender(array(
			'return'          => true,
		));
	}

	public function admin_accept_chat_indesirable($id, $redir = true)
	{
		$this->loadModel('Chat');
		$this->loadModel('AdminAction');
		$this->Chat->updateAll(array('etat' => 1), array('Chat.id' => $id));

		$this->AdminAction->create();
		$this->AdminAction->save(array(
			'type' => 'chat',
			'user_id' => $this->Auth->user('id'),
			'type_id' => $id,
			'date_action' => date('Y-m-d H:i:s'),
			'date_text' => $this->Chat->field('date_start', array('id' => $id))
		));


		if ($redir)
			$this->redirect(array('controller' => 'admins', 'action' => 'watchtchat', 'admin' => true), false);
	}

	public function admin_watchtchat()
	{
		ini_set("memory_limit", -1);
		$this->loadModel('FiltreMessage');
		$this->loadModel('AdminAction');
		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		//$filtres = array();

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte']) {
				$this->Session->write('ChatTexte', $this->params->data['Admin']['texte']);
			} else {
				$this->Session->write('ChatTexte', '');
			}
			if ($this->request->data['Admin']['client']) {
				$this->Session->write('ChatClient', $this->params->data['Admin']['client']);
			} else {
				$this->Session->write('ChatClient', '');
			}
			if (isset($this->request->data['Admin']['repondu'])) {
				$this->Session->write('ChatRep', $this->params->data['Admin']['repondu']);
			} else {
				$this->Session->write('ChatRep', '');
			}
			if (isset($this->request->data['Admin']['etat']) && is_numeric($this->request->data['Admin']['etat'])) {

				if ($this->request->data['Admin']['etat']) {
					$this->Session->write('chat_etat', 0);
				} else {
					$this->Session->write('chat_etat', 1);
				}
			} else {

				$this->Session->write('chat_etat', '');
			}
		}
		$conditions = array();

		if ($this->Session->read('ChatTexte')) {
			$conditions = array_merge($conditions, array(
				'ChatMessage.content LIKE ' => '%' . $this->Session->read('ChatTexte') . '%'
			));
			$this->set('filtre_texte', $this->Session->read('ChatTexte'));
		}
		if ($this->Session->read('ChatClient')) {

			$conditions['OR'] = array('User.firstname LIKE' => '%' . $this->Session->read('ChatClient') . '%', 'User.lastname LIKE' => '%' . $this->Session->read('ChatClient') . '%', 'User.pseudo LIKE' => '%' . $this->Session->read('ChatClient') . '%', 'Agent.firstname LIKE' => '%' . $this->Session->read('ChatClient') . '%', 'Agent.lastname LIKE' => '%' . $this->Session->read('ChatClient') . '%', 'Agent.pseudo LIKE' => '%' . $this->Session->read('ChatClient') . '%');
			$this->set('filtre_client', $this->Session->read('ChatClient'));
		}
		if (is_numeric($this->Session->read('ChatRep'))) {
			if ($this->Session->read('ChatRep')) {
				$conditions = array_merge($conditions, array(
					'Chat.consult_date_start !=' => 'NULL'
				));
			} else {
				$conditions = array_merge($conditions, array(
					'Chat.consult_date_start' => NULL
				));
			}
			$this->set('filtre_rep', $this->Session->read('ChatRep'));
		}


		if (is_numeric($this->Session->read('chat_etat'))) {
			$conditions = array_merge($conditions, array(
				'Chat.etat ' => $this->Session->read('chat_etat')
			));
			/*	$filtres = $this->FiltreMessage->find("all", array(

				));	*/
			$this->set('filtre_etat', $this->Session->read('chat_etat'));
		}
		$this->loadModel('Chat');
		/* $this->Paginator->settings = array(
            'fields' => array('distinct(Chat.id)','Chat.date_start','Chat.consult_date_start','Chat.date_end','Chat.etat','Chat.source','Chat.closed_by','Chat.alert','User.firstname','User.id','User.pseudo','User.lastname','User.email','Agent.id','Agent.firstname','Agent.lastname','Agent.email','Agent.pseudo','Agent.role','User.role', 'AdminAction.date_action', 'AdminAction.date_text', 'COUNT(AdminAction.id) > 0 as hasAdminActions', 'AdminAction.user_id'),
            'conditions' => $conditions,
            'recursive' => 1,
            'order' => 'Chat.date_start DESC',
			'group' => 'Chat.id',
            'paramType' => 'querystring',
			'joins' => array(
				array('table' => 'chat_messages',
                      'alias' => 'ChatMessage',
                      'type' => 'left',
                      'conditions' => array('ChatMessage.chat_id = Chat.id')
                ),
				array(
                    'table' => 'admin_actions',
                    'alias' => 'AdminAction',
                    'type' => 'left',
                    'conditions' => array('AdminAction.type_id = Chat.id', 'AdminAction.type = "chat"')
                )
            ),
            'limit' => 25
        );*/

		$this->Paginator->settings = array(
			'fields' => array('distinct(Chat.id)', 'Chat.date_start', 'Chat.consult_date_start', 'Chat.date_end', 'Chat.etat', 'Chat.source', 'Chat.closed_by', 'Chat.alert', 'User.firstname', 'User.id', 'User.pseudo', 'User.lastname', 'User.email', 'Agent.id', 'Agent.firstname', 'Agent.lastname', 'Agent.email', 'Agent.pseudo', 'Agent.role', 'User.role'),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Chat.date_start DESC',
			'group' => 'Chat.id',
			'paramType' => 'querystring',
			'joins' => array(
				array(
					'table' => 'chat_messages',
					'alias' => 'ChatMessage',
					'type' => 'left',
					'conditions' => array('ChatMessage.chat_id = Chat.id')
				)
			),
			'limit' => 25
		);


		$chats = $this->Paginator->paginate($this->Chat);

		foreach ($chats as &$message) {
			if ($message[0]['hasAdminActions']) {
				$date = new DateTime($message['AdminAction']['date_action']);
				$timestamp_action = $date->getTimestamp();
				$date_last = new DateTime($message['AdminAction']['date_text']);
				$timestamp_message = $date_last->getTimestamp();

				$message['Chat']['validation_time'] = 'Débloqué par ' . $this->User->field('firstname', array('id' => $message['AdminAction']['user_id'])) . ' le ' . CakeTime::format($message['AdminAction']['date_action'], '%Y-%m-%d %H:%M:%S'); //gmdate("H:i:s", $timestamp_action - $timestamp_message);
			}
		}


		$this->set(compact('chats', 'filtres'));
	}

	public function admin_callinfosview()
	{
		$this->loadModel('Callinfo');
		$this->Callinfo->useTable = 'call_infos';

		$conditions = array();
		if (isset($this->request->data['Admin'])) {

			if (isset($this->request->data['Admin']['requestfailed'])) {
				$this->doneRequestFailed($this->request->data['Admin']['requestfailed']);
			} else {

				if ($this->request->data['Admin']['callerid']) {
					$conditions = array(
						'OR' => array(
							'Callinfo.callerid' => $this->request->data['Admin']['callerid'],
							'Callinfo.mob_info LIKE' => '%' . $this->request->data['Admin']['callerid'] . '%',
							'Callinfo.called_number' => $this->request->data['Admin']['callerid']
						)
					);
				}
				if ($this->request->data['Admin']['sessionid'])
					$conditions = array('Callinfo.sessionid' => $this->request->data['Admin']['sessionid']);
			}
		}


		$this->Paginator->settings = array(
			'fields' => array('Callinfo.*', 'Agent.*'),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Callinfo.timestamp DESC',
			'paramType' => 'querystring',
			'limit' => 50,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Agent',
					'type' => 'left',
					'conditions' => array(
						'Agent.agent_number = Callinfo.agent',
					)
				),

			)
		);


		$callinfos = $this->Paginator->paginate($this->Callinfo);

		foreach ($callinfos as &$call) {

			if ($call['Callinfo']['customer']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.personal_code' => $call['Callinfo']['customer']),
					'recursive' => -1
				));
				$call['Client'] = $customer['User'];
			}
			if ($call['Callinfo']['agent1']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['Callinfo']['agent1']),
					'recursive' => -1
				));
				$call['Agent1'] = $customer['User'];
			}
			if ($call['Callinfo']['agent2']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['Callinfo']['agent2']),
					'recursive' => -1
				));
				$call['Agent2'] = $customer['User'];
			}
			if ($call['Callinfo']['agent3']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['Callinfo']['agent3']),
					'recursive' => -1
				));
				$call['Agent3'] = $customer['User'];
			}
			if ($call['Callinfo']['agent4']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['Callinfo']['agent4']),
					'recursive' => -1
				));
				$call['Agent4'] = $customer['User'];
			}
			if ($call['Callinfo']['agent5']) {

				$customer = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $call['Callinfo']['agent5']),
					'recursive' => -1
				));
				$call['Agent5'] = $customer['User'];
			}
		}

		$this->set(compact('callinfos'));
	}


	public function admin_avis()
	{
	}

	public function admin_agent_connected()
	{
	}

	public function admin_alert_send()
	{

		$this->loadModel('AlertHistory');
		$this->Paginator->settings = array(
			'fields' => array('AlertHistory.*', 'User.*', 'Agent.*', 'Alert.*'),
			'conditions' => array(),
			'recursive' => 1,
			'order' => 'AlertHistory.date_add DESC',
			'group' => array('AlertHistory.date_add'),
			'paramType' => 'querystring',
			'joins' => array(
				array(
					'table' => 'alerts',
					'alias' => 'Alert',
					'type' => 'left',
					'conditions' => array('Alert.id = AlertHistory.alerts_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = Alert.users_id')
				),
				array(
					'table' => 'users',
					'alias' => 'Agent',
					'type' => 'left',
					'conditions' => array('Agent.id = Alert.agent_id')
				)
			),
			'limit' => 50
		);


		$list_alerte = $this->Paginator->paginate($this->AlertHistory);


		$this->set(compact('list_alerte'));
	}


	public function admin_sms_send()
	{
		if ($this->request->is('post')) {
			$requestData = $this->request->data;
			$this->loadModel('User');
			//On charge l'API
			App::import('Vendor', 'Noox/Api');
			//On charge le model
			$this->loadModel('SmsHistory');

			$api = new Api();

			$agent = $this->User->find('first', array(
				'fields' => array('User.phone_number', 'User.phone_mobile'),
				'conditions' => array('User.id' => $requestData['expert'], 'User.role' => 'agent', 'User.deleted' => 0),
				'recursive' => -1
			));

			$txt = $requestData['SMSHistory'];

			if (count($agent)) {
				$numero = $agent['User']['phone_mobile'];
				if ($numero) {
					$txtLength = strlen($txt);
					$result = 0;
					$result = $api->sendSms($numero, base64_encode($txt));
					$history = array(
						'id_agent'          => $requestData['expert'],
						'id_client'         => '',
						'id_tchat'         => '',
						'id_message'         => '',
						'email'             => 'SMS',
						'phone_number'      => $numero,
						'content_length'    => $txtLength,
						'content'    		=> $txt,
						'send'              => ($result > 0) ? 1 : 0,
						'date_add'          => date('Y-m-d H:i:s'),
						'type'				=> 'ADMIN CONTACT',
						'cost'				=> $result
					);

					//On save dans l'historique
					$this->SmsHistory->create();
					$this->SmsHistory->save($history);
					$this->Session->setFlash(__('SMS envoyé.'), 'flash_success');
				}
			}
		}
		$this->loadModel('SmsHistory');
		$this->loadModel('Chat');
		$this->loadModel('User');
		$this->Paginator->settings = array(
			'fields' => array('SmsHistory.*', 'User.*', 'Agent.*'),
			'conditions' => array(), //'OR'=>array('SmsHistory.id_tchat !=' => ' NULL','SmsHistory.id_message !=' => ' NULL','SmsHistory.email' => 'SMS')
			'recursive' => 1,
			'order' => 'SmsHistory.date_add DESC',
			'paramType' => 'querystring',
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = SmsHistory.id_client')
				),
				array(
					'table' => 'users',
					'alias' => 'Agent',
					'type' => 'left',
					'conditions' => array('Agent.id = SmsHistory.id_agent')
				)
			),
			'limit' => 25
		);


		$list_sms = $this->Paginator->paginate($this->SmsHistory);

		foreach ($list_sms as &$sms) {
			if ($sms["SmsHistory"]['id_tchat']) {
				$chat = $this->Chat->find('first', array(
					'conditions' => array('Chat.id' => $sms["SmsHistory"]['id_tchat']),
					'recursive' => -1
				));
				$user = $this->User->find('first', array(
					'conditions' => array('User.id' => $chat['Chat']['from_id']),
					'recursive' => -1
				));
				if ($chat['Chat']['consult_date_start']) {
					$sms["SmsHistory"]['respond'] = 'Oui';
				} else {
					$sms["SmsHistory"]['respond'] = 'Non';
				}
				$sms["SmsHistory"]['id_client'] = $chat['Chat']['from_id'];
				$sms["SmsHistory"]['client'] = $user['User']['firstname'];
			}
		}


		$this->set(compact('list_sms'));
	}


	public function admin_index()
	{
		/* Stats */
		$this->loadModel('UserLevel');

		$user_co = $this->Session->read('Auth.User');

		$level = $this->UserLevel->find('first', array(
			'conditions' => array('UserLevel.user_id' => $user_co['id']),
			'recursive' => -1
		));
		$level = $level['UserLevel']['level'];

		$this->loadModel('UserCreditHistory');
		$this->loadModel('Order');
		$this->loadModel('User');

		$this->set(compact('level'));
	}
	public function admin_delete($id = 0)
	{
		$this->layout = false;
		$this->autoRender = false;
		$this->User->id = $id;
		$this->User->saveField('active', 0);
		$this->User->saveField('deleted', 1);
		$this->redirect(array(
			'controller' => 'admins',
			'action' => 'list',
			'admin' => true
		), false);
	}
	public function admin_enable($id = 0)
	{
		$this->layout = false;
		$this->autoRender = false;
		$this->User->id = $id;
		$this->User->saveField('active', 1);
		$this->redirect(array(
			'controller' => 'admins',
			'action' => 'list',
			'admin' => true
		), false);
	}
	public function admin_disable($id = 0)
	{
		$this->layout = false;
		$this->autoRender = false;
		$this->User->id = $id;
		$this->User->saveField('active', 0);
		$this->redirect(array(
			'controller' => 'admins',
			'action' => 'list',
			'admin' => true
		), false);
	}
	public function admin_list()
	{
		$users = $this->User->find("all", array(
			'conditions' => array(
				'role' => 'admin',
				'deleted' => '!=1'
			)
		));
		$this->set(compact('users'));
	}

	public function login()
	{
		$this->layout = 'login_admin';
	}

	public function admin_subscribe()
	{
		if ($this->request->is('post')) {
			$requestData = $this->request->data;

			//Vérification des champs du formualire
			$champRequired = array('email', 'passwd', 'passwd2', 'country_id', 'firstname', 'lastname');
			$requestData['User'] = Tools::checkFormField($requestData['User'], $champRequired, $champRequired);
			if ($requestData['User'] === false) {
				$this->Session->setFlash(__('Erreur dans le formulaire.'), 'flash_error');
				$this->redirect(array('controller' => 'admins', 'action' => 'subscribe', 'admin' => true), false);
			}

			//Check email et mot de passe
			//Vérification sur l'adresse mail
			if (!filter_var($requestData['User']['email'], FILTER_VALIDATE_EMAIL)) {
				$this->Session->setFlash(__('Email invalide.'), 'flash_error');
				$this->redirect(array('controller' => 'admins', 'action' => 'subscribe', 'admin' => true), false);
			}
			if (strlen($requestData['User']['passwd']) < 8) {
				$this->Session->setFlash(__('8 caractères au minimum pour le mot de passe.'), 'flash_success');
				return;
			} elseif ($requestData['User']['passwd'] !== $requestData['User']['passwd2']) {
				$this->Session->setFlash(__('Les mots de passe sont différents.'), 'flash_success');
				return;
			}
			//Test mail unique pour les admins
			if ($this->User->singleEmail($requestData['User']['email'], 'admin')) {
				//Init
				$requestData['User']['valid'] = 1;
				$requestData['User']['active'] = 1;
				$requestData['User']['emailConfirm'] = 1;
				$requestData['User']['deleted'] = 0;
				$requestData['User']['credit'] = 0;
				$requestData['User']['role'] = 'admin';
				$requestData['User']['lang_id'] = 1;

				//On save
				$this->User->create();
				if ($this->User->save($requestData)) {
					$this->Session->setFlash(__('Nouvel utilisateur crée. Email envoyé.'), 'flash_success');
					//Paramètre pour le mail de confirmation
					$paramEmail = array(
						'email' => $requestData['User']['email'],
						'passwd' => $requestData['User']['passwd2']
					);

					//$this->sendEmail($requestData['User']['email'],'Compte administrateur crée','subscribe_admin',array('param' => $paramEmail));
					$this->sendCmsTemplateByMail(182, $this->Session->read('Config.id_lang'), $requestData['User']['email'], array(
						'ACCOUNT_EMAIL' => $requestData['User']['email'],
						'ACCOUNT_PWD'   => $requestData['User']['passwd2']
					));
				} else {
					$this->Session->setFlash(__('Erreur lors de la création de l\'admin. Email non envoyé.'), 'flash_warning');
				}
				$this->redirect(array('controller' => 'admins', 'action' => 'index', 'admin' => true), false);
			} else {
				$this->Session->flash(__('Un administrateur a déjà cet email. Veuillez réessayer.'), 'flash_warning');
				return;
			}
		}

		//Les pays
		$this->loadModel('UserCountry');
		$this->set('select_countries', $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang')));
	}

	public function newpasswd()
	{
		if ($this->request->is('post')) {

			//Vérification des champs du formulaire
			$this->request->data['User'] = Tools::checkFormField($this->request->data['User'], array('passwd', 'passwd2', 'forgotten_password'), array('passwd', 'passwd2', 'forgotten_password'));
			if ($this->request->data['User'] === false) {
				$this->Session->setFlash(__('Erreur dans le formulaire.'), 'flash_error');
				$this->redirect(array('controller' => 'admins', 'action' => 'newpasswd', 'admin' => false), false);
			}

			$user = $this->User->find('first', array(
				'fields' => array('User.id', 'User.forgotten_password'),
				'conditions' => array('User.forgotten_password' => $this->request->data['User']['forgotten_password'], 'User.deleted' => 0),
				'recursive' => -1,
			));

			//Si pas d'user alors redirection accueil
			if (empty($user))
				$this->redirect(array('controller' => 'home', 'action' => 'index'));
			else {
				//Mot de passe moins de huit caractères ou différent
				if (strlen($this->request->data['User']['passwd']) < 8) {
					$this->Session->setFlash(__('8 caractères au minimum pour le mot de passe.'), 'flash_warning');
					return;
				} elseif ($this->request->data['User']['passwd'] !== $this->request->data['User']['passwd2']) {
					$this->Session->setFlash(__('Les mots de passe sont différents.'), 'flash_warning');
					return;
				}

				$this->User->id = $user['User']['id'];
				//Utilisation unique du token
				$this->request->data['User']['forgotten_password'] = null;
				//Hash le mot de passe
				$this->request->data['User']['passwd'] = $this->hashMDP($this->request->data['User']['passwd']);

				if (!$this->User->save($this->request->data))
					$this->Session->setFlash(__('Erreur dans la réinitialisation de votre mot de passe'), 'flash_error');
				else
					$this->Session->setFlash(__('Votre mot de passe a été réinitialisé.'), 'flash_success');
				$this->redirect(array('controller' => 'admins', 'action' => 'login', 'admin' => false), false);
			}
		}

		//Utilisateur redirigé sur la page d'accueil si manque les paramètres
		if (
			empty($this->request->query)
			|| !isset($this->request->query['key'])
		)
			$this->redirect(array('controller' => 'home', 'action' => 'index'));

		if (isset($this->request->query['key'])) {
			$this->layout = 'login_admin';
		}

		$user = $this->User->find('first', array(
			'fields' => 'User.forgotten_password',
			'conditions' => array('User.forgotten_password' => $this->request->query['key'], 'User.deleted' => 0),
			'recursive' => -1,
		));

		//Redirection accueil ou on stoke le token dans le formulaire
		if (empty($user))
			$this->redirect(array('controller' => 'home', 'action' => 'index'));
		else
			$this->request->data = $user;
	}

	public function passwdforget()
	{
		$this->layout = 'login_admin';
		if ($this->request->is('post')) {

			//Vérification des champs du formulaire
			$this->request->data['User'] = Tools::checkFormField($this->request->data['User'], array('email'), array('email'));
			if ($this->request->data['User'] === false) {
				$this->Session->setFlash(__('Erreur dans le formulaire.'), 'flash_error');
				$this->redirect(array('controller' => 'admins', 'action' => 'passwdforget', 'admin' => false), false);
			}

			//Vérification sur l'adresse mail
			if (!filter_var($this->request->data['User']['email'], FILTER_VALIDATE_EMAIL)) {
				$this->Session->setFlash(__('Email invalide.'), 'flash_error');
				$this->redirect(array('controller' => 'admins', 'action' => 'passwdforget', 'admin' => false), false);
			}

			sleep(1);
			if ($this->User->isUniqueEmail($this->request->data['User']['email'])) {
				$user = $this->User->find('first', array(
					'fields' => array('last_passwd_gen', 'id'),
					'conditions' => array('email' => $this->request->data['User']['email'], 'deleted' => 0),
					'recursive' => -1
				));

				// Token pour le lien
				$dateNow = date('Y-m-d H:i:s');
				$token = Security::hash($this->request->data['User']['email'] . $dateNow, null, true);

				//Si l'utilisateur a déjà généré un mot de passe
				if (!empty($user['User']['last_passwd_gen'])) {
					// Date de la dernière génération
					$lastPassGen = new DateTime($user['User']['last_passwd_gen']);
					$lastPassGen = $lastPassGen->getTimestamp();

					// Date actuelle
					$passGen = new DateTime($dateNow);
					$passGen = $passGen->getTimestamp();

					// si moins de 30min depuis la dernière génération
					if (($passGen - $lastPassGen) < Configure::read('Site.timeMinPass')) {
						$this->set('timePass', false);
						$this->Session->setFlash(__('Un délai de 30min est requis entre chaque génération de mot de passe.'), 'flash_warning');
						return;
					}
				}

				// On sauvegarde le token et sa date de génération
				$user['User']['last_passwd_gen'] = date('Y-m-d H:i:s');
				$user['User']['forgotten_password'] = $token;

				//Paramètres email
				$paramEmail = array(
					'email' => $this->request->data['User']['email'],
					'urlReinitialisation' => $this->linkGenerator('admins', 'newpasswd', array('key' => $token))
				);
				//$this->sendEmail($this->request->data['User']['email'],'Réinitialisation de votre mot de passe','reinit_pass',array('param' => $paramEmail));
				$this->sendCmsTemplateByMail(183, $this->Session->read('Config.id_lang'), $this->request->data['User']['email'], array(
					'EMAIL_ADDRESS' => $this->request->data['User']['email'],
					'LIEN_PWD_REINIT' => $this->linkGenerator('admins', 'newpasswd', array('key' => $token))
				));

				$this->User->id = $user['User']['id'];
				$this->User->save($user);

				$this->Session->setFlash(__('Un mail pour réinitialiser votre mot de passe a été envoyé.'), 'flash_success');
				$this->set('emailValid', true);
			} else {

				$this->Session->setFlash(__('Aucun compte trouvé pour cet email.'), 'flash_warning');
				$this->redirect(array('controller' => 'admins', 'action' => 'passwdforget', 'admin' => false), false);
			}
		}
	}

	public function admin_mails()
	{
		$this->loadModel('Message');

		$ip = '';
		$mail = '';
		$nom = '';

		if (isset($this->request->data['Admin'])) {

			if ($this->request->data['Admin']['adr_ip']) {
				$ip = $this->request->data['Admin']['adr_ip'];
				$this->Session->write('MessagerieIP', $this->params->data['Admin']['adr_ip']);
			} else {
				$this->Session->write('MessagerieIP', '');
			}
			if ($this->request->data['Admin']['email']) {
				$email = $this->request->data['Admin']['email'];
				$this->Session->write('MessagerieEmail', $this->params->data['Admin']['email']);
			} else {
				$this->Session->write('MessagerieEmail', '');
			}
			if ($this->request->data['Admin']['Nom']) {
				$nom = $this->request->data['Admin']['Nom'];
				$this->Session->write('MessagerieNom', $this->params->data['Admin']['Nom']);
			} else {
				$this->Session->write('MessagerieNom', '');
			}
		}


		if ($this->request->is('post') && !$ip && !$email && !$nom)
			$this->answerMail();


		/* $mails = $this->Message->getDiscussion($this->Auth->user('id'), true, false, true, $ip, $email, $nom);
        $conditions = $this->Message->getConditions($this->Auth->user('id'), true, false, true);



        $this->Paginator->settings = array(
            'conditions'    => $conditions,
            'paramType'     => 'querystring',
            'limit'         => Configure::read('Site.limitMessagePage')
        );

        $this->Paginator->paginate($this->Message);

        //On crée les différentes pages
        $pages = array_chunk($mails, Configure::read('Site.limitMessagePage'));

        $page = 0;
        if(isset($this->params->query['page']))
            $page = $this->params->query['page']-1;

        if(isset($pages[$page]))
            $mails = $pages[$page];
        else
            $mails = array();*/


		$this->paginatorParams();

		$firstConditions = array(
			'Message.deleted' => 0,
			'Message.parent_id' => null,
			'OR' => array(
				array('Message.from_id' => array(1, 191, 198, 204, 323, 495)),
				array('Message.to_id' => Configure::read('Admin.id')),
				array('Message.to_id' => array(1, 191, 198, 204, 323, 495))
			)
		);


		if ($this->Session->read('MessagerieIP')) {
			$firstConditions['Message.IP'] = $this->Session->read('MessagerieIP');
			$firstConditions['Message.admin_read_flag'] = 1;
		}
		if ($this->Session->read('MessagerieEmail')) {
			$firstConditions['OR'] = array(
				array('Guest.email' => $this->Session->read('MessagerieEmail')),
				array('From.email' => $this->Session->read('MessagerieEmail')),
				array('To.email' => $this->Session->read('MessagerieEmail'))
			);
			$firstConditions['Message.admin_read_flag'] = 1;
		}
		if ($this->Session->read('MessagerieNom')) {
			$firstConditions['OR'] = array(
				array('Guest.firstname' => $this->Session->read('MessagerieNom')),
				array('From.firstname' => $this->Session->read('MessagerieNom')),
				array('To.firstname' => $this->Session->read('MessagerieNom'))
			);
			$firstConditions['Message.admin_read_flag'] = 1;
		}

		$this->Paginator->settings = array(
			'fields' => array('Message.id', 'Message.date_add', 'Message.from_id', 'Message.to_id', 'Message.content', 'Message.etat', 'LastMessage.id', 'LastMessage.date_add', 'LastMessage.from_id', 'LastMessage.to_id', 'LastMessage.content', 'LastMessage.etat', 'LastMessage.IP', 'FirstMessage.IP', 'Message.IP', 'LastMessage.admin_read_flag', 'FirstMessage.admin_read_flag', 'Message.admin_read_flag', 'FirstMessage.id', 'FirstMessage.date_add', 'FirstMessage.from_id', 'FirstMessage.to_id', 'FirstMessage.content', 'FirstMessage.etat', 'From.firstname as pseudo', '((CASE
            WHEN Message.date_add <= LastMessage.date_add
               THEN LastMessage.date_add
               ELSE Message.date_add
       END)) as dateorder', 'Guest.firstname', 'Guest.lastname', 'Guest.email', 'Guest.ip', 'From.firstname', 'From.lastname', 'From.email'),
			'conditions' => $firstConditions,
			'joins' => array(
				array(
					'table' => 'messages',
					'alias' => 'LastMessage',
					'type'  => 'left',
					'conditions' => array(
						'LastMessage.parent_id = Message.id'
					)
				),
				array(
					'table' => 'messages',
					'alias' => 'FirstMessage',
					'type'  => 'left',
					'conditions' => array('FirstMessage.id = Message.id')
				),
				array(
					'table' => 'users',
					'alias' => 'From',
					'type'  => 'left',
					'conditions' => array(
						'From.id = Message.from_id'
					)
				),
				array(
					'table' => 'users',
					'alias' => 'To',
					'type'  => 'left',
					'conditions' => array(
						'To.id = Message.to_id'
					)
				),
				array(
					'table' => 'guests',
					'alias' => 'Guest',
					'type'  => 'left',
					'conditions' => array(
						'Guest.id = Message.guest_id'
					)
				)
			),
			'order' => 'dateorder desc',
			'recursive' => -1,
			'limit' => Configure::read('Site.limitMessagePage')
		);

		$mails = $this->Paginator->paginate($this->Message);
		foreach ($mails as &$mmail) {
			if (!$mmail['LastMessage']['id']) $mmail['LastMessage'] = $mmail['FirstMessage'];
		}



		//L'id de l'user
		$id = $this->Auth->user('id');

		$this->set(compact('mails', 'id'));
	}

	public function admin_delete_mails($id)
	{
		$this->loadModel('Message');
		$this->Message->delete($id, false);

		$this->redirect(array('controller' => 'admins', 'action' => 'mails', 'admin' => true), false);
	}

	private function answerMail()
	{
		//Les datas
		$requestData = $this->request->data;
		//Les champs requis
		$requestData['Admin'] = Tools::checkFormField($requestData['Admin'], array('mail_id', 'content'), array('mail_id', 'content'));
		if ($requestData['Admin'] === false) {
			$this->Session->setFlash(__('Erreur avec le formulaire de réponse ou votre message est vide.'), 'flash_error');
			$this->redirect(array('controller' => 'admins', 'action' => 'mails', 'admin' => true), false);
		}

		$infoMessage = $this->Message->find('first', array(
			'fields' => array('Message.from_id', 'Guest.id', 'Guest.email', 'Guest.lang_id'),
			'conditions' => array('Message.id' => $requestData['Admin']['mail_id'], 'Message.deleted' => 0, 'parent_id' => null),
			'recursive' => 0
		));

		//Check sur le client-------------------------------------------------------------------------
		//Si pas de client ou pas de message
		if (empty($infoMessage)) {
			$this->Session->setFlash(__('L\'expediteur demandé n\'existe pas ou plus ou vous ne pouvez pas répondre à ce message.'), 'flash_warning');
			$this->redirect(array('controller' => 'admins', 'action' => 'mails', 'admin' => true), false);
		}

		//Si invité ??
		if ($infoMessage['Message']['from_id'] == Configure::read('Guest.id'))
			$emailUser = $infoMessage['Guest']['email'];
		else
			//L'email du client
			$emailUser = $this->User->field('email', array('id' => $infoMessage['Message']['from_id']));

		//On save (envoie) le mail
		$this->Message->create();
		if ($this->Message->save(array(
			'parent_id' => $requestData['Admin']['mail_id'],
			'from_id' => $this->Auth->user('id'),
			'to_id' => $infoMessage['Message']['from_id'],
			'guest_id' => $infoMessage['Guest']['id'],
			'content' => $requestData['Admin']['content'],
			'private' => 1,
			'admin_read_flag' => 1
		))) {


			//Si invité ??
			if ($infoMessage['Message']['from_id'] == Configure::read('Guest.id')) {
				//On génére un token pour que l'invite puisse répondre
				$token = Security::hash($this->Message->id . $infoMessage['Guest']['id'] . date('Y-m-d H:i:s'), null, true);
				//On save le token
				$this->loadModel('Guest');
				$this->Guest->id = $infoMessage['Guest']['id'];
				$this->Guest->saveField('answer_token', $token);

				//Les datas pour l'email
				//$datasEmail = array('content' => $requestData['Admin']['content'], 'urlMail' => Router::url(array('controller' => 'contacts', 'action' => 'answer', '?' => array('token' => $token), 'admin' => false),true));
				//On envoie l'email
				//$this->sendEmail($emailUser,'Message d\'un administrateur.','answer_guest',array('param' => $datasEmail));

				$requestData['Admin']['content'] = str_replace('https://fr.spiriteo.com/', '##PARAM_URLSITE##', $requestData['Admin']['content']);
				$requestData['Admin']['content'] = str_replace('https://be.spiriteo.com/', '##PARAM_URLSITE##', $requestData['Admin']['content']);
				$requestData['Admin']['content'] = str_replace('https://ca.spiriteo.com/', '##PARAM_URLSITE##', $requestData['Admin']['content']);
				$requestData['Admin']['content'] = str_replace('https://lu.spiriteo.com/', '##PARAM_URLSITE##', $requestData['Admin']['content']);
				$requestData['Admin']['content'] = str_replace('https://ch.spiriteo.com/', '##PARAM_URLSITE##', $requestData['Admin']['content']);
				$requestData['Admin']['content'] = str_replace('../../', '##PARAM_URLSITE##', $requestData['Admin']['content']);


				$this->sendCmsTemplateByMail(184, $infoMessage['Guest']['lang_id'], $emailUser, array(
					'PARAM_CONTENT' => $requestData['Admin']['content'],
					'PARAM_URL_ANSWER' => Router::url(array('controller' => 'contacts', 'action' => 'answer', '?' => array('token' => $token), 'admin' => false), true)
				));

				$this->Session->setFlash(__('Votre réponse a été envoyée par mail.'), 'flash_success');
			} else {
				/*Envoi de l'email*/
				//Role user
				$role = $this->User->field('role', array('id' => $infoMessage['Message']['from_id']));

				$this->User->id = $infoMessage['Message']['from_id'];

				//Les datas pour l'email
				$datasEmail = array(
					'admin' => true,
					'urlMail' => Router::url(array('controller' => ($role === 'client' ? 'accounts' : 'agents'), 'action' => 'mails', '?' => array('private' => true), 'admin' => false), true)
				);
				//On envoie l'email
				//$this->sendEmail($emailUser,'Message d\'un administrateur.','answer_mail',array('param' => $datasEmail));
				if ($role == 'client') {
					$this->User->id = $infoMessage['Message']['from_id'];
					$client = $this->User->read();
					$this->sendCmsTemplateByMail(185, $client['User']['lang_id'], $client['User']['email']);
				} elseif ($role == 'agent') {
					$this->User->id = $infoMessage['Message']['from_id'];
					$client = $this->User->read();
					$this->sendCmsTemplateByMail(186, $client['User']['lang_id'], $client['User']['email']);
				}

				$this->Session->setFlash(__('Votre réponse a été envoyée.'), 'flash_success');
			}
		} else
			$this->Session->setFlash(__('Erreur durant l\'envoi du mail.'), 'flash_error');

		$this->redirect(array('controller' => 'admins', 'action' => 'mails', 'admin' => true), false);
	}

	public function admin_answerForm($idMail)
	{
		if ($this->request->is('ajax')) {
			//Check l'id du mail
			if (empty($idMail) || !is_numeric($idMail))
				$this->jsonRender(array('return' => false));
			$this->loadModel('Message');
			//On récupère la conversation
			$conversation = $this->Message->find('first', array(
				'conditions' => array('Message.id' => $idMail),
				'recursive' => -1
			));

			//Si pas de conversation ou si la conversation ne lui est pas destiné
			if (empty($conversation) || ($conversation['Message']['from_id'] != $this->Auth->user('id') && $conversation['Message']['to_id'] != $this->Auth->user('id') && $conversation['Message']['to_id'] != Configure::read('Admin.id')))
				$this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => 'admins', 'action' => 'mails', 'admin' => true))));

			$this->layout = '';
			$this->set(array('idMail' => $idMail));
			$response = $this->render('/Elements/admin_answer_mail');

			$this->jsonRender(array('return' => true, 'html' => $response->body()));
		}
	}

	public  function admin_readMail()
	{
		if ($this->request->is('ajax')) {
			//Check l'id du mail
			if (!isset($this->request->data['id_mail']) || empty($this->request->data['id_mail']) || !is_numeric($this->request->data['id_mail']))
				$this->jsonRender(array('return' => false));
			$idMail = $this->request->data['id_mail'];
			$this->loadModel('Message');
			//On récupère la conversation
			$tmp_conversation = $this->Message->find('threaded', array(
				'conditions' => array(
					'OR' => array(
						array('Message.id' => $idMail),
						array('Message.parent_id' => $idMail)
					)
				)
			));

			/* On veut le repasser en NON LU */
			if (isset($this->request->query['switchreaded'])) {

				$this->Message->updateAll(array('Message.admin_read_flag' => 0), array(
					'OR' =>  array(
						array('Message.id' => $idMail),
						array('Message.parent_id' => $idMail)
					)
				));

				$this->jsonRender(array(
					'return'          => true
				));
			}

			//Si pas de conversation ou si la conversation ne lui est pas destiné
			if (empty($tmp_conversation))
				$this->jsonRender(array('return' => false, 'url' => Router::url(array('controller' => 'admins', 'action' => 'mails', 'admin' => true)))); // || ($tmp_conversation[0]['Message']['to_id'] != Configure::read('Admin.id'))$tmp_conversation[0]['Message']['from_id'] != $this->Auth->user('id') && $tmp_conversation[0]['Message']['to_id'] != $this->Auth->user('id') &&

			//Restructurer les messages
			App::import('Controller', 'Extranet');
			$extra = new ExtranetController;
			//Le nom expediteur et destinataire
			$from = $extra->displayName($tmp_conversation[0]['From']['role'], $tmp_conversation[0]['Message']['from_id'], $this->Auth->user('id'), $tmp_conversation[0]['From']['pseudo'], $tmp_conversation[0]['From']['firstname']);
			$to = $extra->displayName($tmp_conversation[0]['To']['role'], $tmp_conversation[0]['Message']['to_id'], $this->Auth->user('id'), $tmp_conversation[0]['To']['pseudo'], $tmp_conversation[0]['To']['firstname']);

			//Si invité ??
			if ($tmp_conversation[0]['Message']['from_id'] == Configure::read('Guest.id'))
				$from = __('(Invite)') . ' ' . $tmp_conversation[0]['Guest']['firstname'] . ' ' . $tmp_conversation[0]['Guest']['lastname'];
			else if ($tmp_conversation[0]['Message']['to_id'] == Configure::read('Guest.id'))
				$to = __('(Invite)') . ' ' . $tmp_conversation[0]['Guest']['firstname'] . ' ' . $tmp_conversation[0]['Guest']['lastname'];



			//Le 1er message le plus ancien
			$conversation[0] = array(
				'from_id' => $tmp_conversation[0]['Message']['from_id'],
				'to_id' => $tmp_conversation[0]['Message']['to_id'],
				'content' => $tmp_conversation[0]['Message']['content'],
				'date' => $tmp_conversation[0]['Message']['date_add'],
				'from' => $from,
				'to'  => $to
			);

			//Les autres messages
			foreach ($tmp_conversation[0]['children'] as $mail) {
				//Le nom expediteur et destinataire
				$from = $extra->displayName($mail['From']['role'], $mail['Message']['from_id'], $this->Auth->user('id'), $mail['From']['pseudo'], $mail['From']['firstname']);
				$to = $extra->displayName($mail['To']['role'], $mail['Message']['to_id'], $this->Auth->user('id'), $mail['To']['pseudo'], $mail['To']['firstname']);
				//Si invité ??
				if ($mail['Message']['from_id'] == Configure::read('Guest.id'))
					$from = __('(Invite)') . ' ' . $mail['Guest']['firstname'] . ' ' . $mail['Guest']['lastname'];
				else if ($mail['Message']['to_id'] == Configure::read('Guest.id'))
					$to = __('(Invite)') . ' ' . $mail['Guest']['firstname'] . ' ' . $mail['Guest']['lastname'];

				array_unshift($conversation, array(
					'from_id' => $mail['Message']['from_id'],
					'to_id' => $mail['Message']['to_id'],
					'content' => $mail['Message']['content'],
					'date' => $mail['Message']['date_add'],
					'from' => $from,
					'to'  => $to
				));
			}

			$this->layout = '';
			$this->set(array('idMail' => $idMail));
			$this->set(compact('conversation'));
			$response = $this->render('/Elements/admin_read_mail');
			$readMail = $response->body();

			//Le formulaire de réponse

			$response = $this->render('/Elements/admin_answer_mail');
			$answerForm = $response->body();

			//Indiquer que le message est lu s'il est le destinataire
			//if($conversation[0]['to_id'] == $this->Auth->user('id') || $conversation[0]['to_id'] == Configure::read('Admin.id')){
			$this->Message->updateAll(array('Message.admin_read_flag' => 1), array(
				'OR' =>  array(
					array('Message.id' => $idMail),
					array('Message.parent_id' => $idMail)
				)
			));
			//}



			$this->jsonRender(array(
				'return'          => true,
				'readMail'        => $readMail,
				'answerForm'      => $answerForm,
				'switchReadedUrl' => Router::url(
					array(
						'controller' => 'admins',
						'action'     => 'readMail',
						'admin'      => true,
						'?'         => array(
							'switchreaded' => true
						)
					)
				)
			));
		}
	}

	public function admin_create_logo()
	{

		$this->admin_logo();

		$this->render('admin_logo');
	}

	public function admin_edit_logo($id)
	{
		if (empty($id) || !is_numeric($id)) {
			$this->Session->setFlash(__('Impossible de modifier le logo'), 'flash_warning');
			$this->redirect(array('controller' => 'admins', 'action' => 'logo', 'admin' => true), false);
		}

		if ($this->request->is('post')) {
			$requestData = $this->request->data;

			$requestData['Logo'] = Tools::checkFormField($requestData['Logo'], array('domain', 'photo'), array('domain'));
			if ($requestData['Logo'] === false) {
				$this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
				$this->redirect(array('controller' => 'admins', 'action' => 'edit_logo', 'id' => $id, 'admin' => true), false);
			}

			if ($this->saveLogo($requestData))
				$this->redirect(array('controller' => 'admins', 'action' => 'logo', 'admin' => true), false);
			else
				$this->redirect(array('controller' => 'admins', 'action' => 'edit_logo', 'id' => $id, 'admin' => true), false);
		}

		//Le fichier existe t-il ??
		$fileLogo = new File(Configure::read('Site.pathLogo') . '/' . $id . '_logo.jpg');
		if (!$fileLogo->exists()) {
			$this->Session->setFlash(__('Le logo est introuvable.'), 'flash_warning');
			$this->redirect(array('controller' => 'admins', 'action' => 'logo', 'admin' => true), false);
		}

		$this->loadModel('Domain');
		//Le nom du domaine
		$nameDomain = $this->Domain->field('domain', array('id' => $id));
		//On retire les 'www.'
		$nameDomain = str_replace('www.', '', $nameDomain);


		$tmp = array();
		foreach ($this->logos_ssl_hosting as $id_site => $url) {
			if ($id_site == $id) {
				$tmp = array(
					'id_site' => $id_site,
					'url'     => $url
				);
			}
		}
		$this->set('ssl_logo', $tmp);
		$this->set(compact('id', 'nameDomain'));
	}

	public function admin_logo()
	{
		if ($this->request->is('post')) {
			$requestData = $this->request->data;

			//Check le formulaire
			$requestData['Logo'] = Tools::checkFormField($requestData['Logo'], array('domain', 'photo'), array('domain'));
			if ($requestData['Logo'] === false) {
				$this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
				$this->redirect(array('controller' => 'admins', 'action' => 'create_logo', 'admin' => true), false);
			}

			if ($this->saveLogo($requestData))
				$this->redirect(array('controller' => 'admins', 'action' => 'logo', 'admin' => true), false);
			else
				$this->redirect(array('controller' => 'admins', 'action' => 'create_logo', 'admin' => true), false);
		}

		//Les chemins des logos
		$paths = glob(Configure::read('Site.pathLogo') . '/*_logo.jpg');
		//On récupère les id des domains
		$idDomains = Tools::extractData($paths, '_');

		//Les informations pour chaque domaines récupérer
		$this->loadModel('Domain');
		//Les paramètres pour le paginator
		$this->Paginator->settings = array(
			'fields' => array('id', 'domain'),
			'conditions' => array('Domain.id' => $idDomains),
			'recursive' => -1,
			'paramType' => 'querystring',
			'limit' => 10
		);

		//Les domains pour le select
		$domain_select = $this->Domain->find('list', array(
			'fields' => array('domain')
		));

		//On enlève les domaines qui ont déjà une image
		foreach ($idDomains as $id) {
			unset($domain_select[$id]);
		}

		$domains = $this->Paginator->paginate($this->Domain);

		/* On rajoute les liens SSL */
		foreach ($domains as $k => $v) {
			$domains[$k]['Domain']['ssl_hosting'] = isset($this->logos_ssl_hosting[$v['Domain']['id']]) ? $this->logos_ssl_hosting[$v['Domain']['id']] : false;
		}


		$this->set(compact('domains', 'domain_select'));
	}

	private function saveLogo($data)
	{
		//Avons-nous un fichier ??
		if ($this->isUploadedFile($data['Logo']['photo'])) {
			//La infos de l'image
			$dataLogo = getimagesize($data['Logo']['photo']['tmp_name']);

			//Est-ce un fichier image autorisé ??
			if (!in_array($dataLogo['mime'], array('image/jpeg', 'image/pjpeg'))) {
				$this->Session->setFlash(__('Le fichier n\'est pas un fichier image accepté.'), 'flash_warning');
				return false;
			}

			//Test pour la taille de l'image
			if ($dataLogo[0] !== Configure::read('Logo.width') || $dataLogo[1] !== Configure::read('Logo.height')) {
				$this->Session->setFlash(__('La taille du logo n\'est pas bonne. La taille attendue est la suivante : ') . ' ' . Configure::read('Logo.width') . 'x' . Configure::read('Logo.height'), 'flash_warning');
				return false;
			}
			//En déplace l'image
			if (!move_uploaded_file($data['Logo']['photo']['tmp_name'], Configure::read('Site.pathLogo') . DS . $data['Logo']['domain'] . '_logo.jpg')) {
				$this->Session->setFlash(__('Votre logo n\'a pu être sauvegardé'), 'flash_warning');
				return false;
			}

			$this->Session->setFlash(__('Votre logo a été sauvegardé.'), 'flash_success');
			return true;
		} else {
			$this->Session->setFlash(__('Erreur avec le chargement du fichier'), 'flash_warning');
			return false;
		}
	}

	public function admin_date_range()
	{
		if ($this->request->is('post')) {
			$requestData = $this->request->data;

			//Pas de filtre avec la date
			if (empty($requestData['date'])) {
				$this->Session->delete('Date');
			} else {
				$explodeDate = explode(' au ', $requestData['date']);

				$this->Session->write('Date.start', $explodeDate[0]);
				$this->Session->write('Date.end', $explodeDate[1]);
			}
			if (empty($requestData['dateclient'])) {
				$this->Session->delete('DateClient');
			} else {
				$explodeDate = explode(' au ', $requestData['dateclient']);

				$this->Session->write('DateClient.start', $explodeDate[0]);
				$this->Session->write('DateClient.end', $explodeDate[1]);
			}

			//Filtre media
			if (isset($requestData['media'])) {
				if (empty($requestData['media']) || $requestData['media'] === 'all')
					$this->Session->delete('Media');
				else
					$this->Session->write('Media.value', $requestData['media']);
			}

			//Filtre type_export
			if (isset($requestData['type_export'])) {
				if (empty($requestData['type_export']))
					$this->Session->delete('type_export');
				else
					$this->Session->write('type_export.value', $requestData['type_export']);
			}

			//Filtre media
			if (isset($requestData['consult_total'])) {
				if (empty($requestData['consult_total']) || $requestData['consult_total'] === 'all')
					$this->Session->delete('ConsultTotal');
				else
					$this->Session->write('ConsultTotal.value', $requestData['consult_total']);
			}

			$source = $this->referer();
			if (empty($source))
				$this->redirect(array('controller' => 'agents', 'action' => 'com', 'admin' => true), false);
			else
				$this->redirect($source);
		}
	}

	public function admin_date_range_stats()
	{
		if ($this->request->is('post')) {
			$requestData = $this->request->data;

			//Pas de filtre avec la date
			if (empty($requestData['date'])) {
				$this->Session->delete('DateStats');
			} else {
				$explodeDate = explode(' au ', $requestData['date']);

				$this->Session->write('DateStats.start', $explodeDate[0]);
				$this->Session->write('DateStats.end', $explodeDate[1]);
			}

			$source = $this->referer();
			if (empty($source))
				$this->redirect(array('controller' => 'agents', 'action' => 'view', 'admin' => true), false);
			else
				$this->redirect($source);
		}
	}


	public function admin_blocnote()
	{
		$this->loadModel('AdminNote');

		if ($this->request->is('post')) {
			$requestData = $this->request->data;
			$saveData = $requestData['AdminNote'];
			$saveData['note'] = "'" . addslashes($saveData['note']) . "'";

			$this->AdminNote->updateAll($saveData, array('user_id' => 1));
		}

		$note = $this->AdminNote->find('first', array(
			'fields' => array('AdminNote.note'),
			'conditions' => array('AdminNote.user_id' => 1),
			'recursive' => -1,
		));
		$this->set(compact('note'));
	}

	public function admin_watchrelance()
	{
		$this->loadModel('Message');
		$this->loadModel('AdminAction');
		$conditions = array('Message.private' => 2, 'Message.etat' => 2);

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte'])
				$this->Session->write('MessageTexte', $this->params->data['Admin']['texte']);
			else
				$this->Session->write('MessageTexte', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
		}



		if ($this->Session->read('MessageTexte')) {
			$conditions = array('Message.private' => 2, 'Message.etat' => 2, 'Message.content LIKE ' => '%' . $this->Session->read('MessageTexte') . '%');
			$this->set('filtre_texte', $this->Session->read('MessageTexte'));
		}
		if ($this->Session->read('MessageClient')) {
			$conditions = array('Message.private' => 2, 'Message.etat' => 2);
			$conditions['OR'] = array('From.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%');
			$this->set('filtre_client', $this->Session->read('MessageClient'));
		}

		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		$this->Paginator->settings = array(
			'fields' => array(
				'Message.id', 'Message.private', 'Message.content', 'Message.attachment', 'Message.etat', 'Message.to_id', 'From.firstname', 'From.id', 'From.pseudo', 'From.lastname', 'From.email', 'To.id', 'To.firstname', 'To.lastname', 'To.email', 'To.pseudo', 'To.role', 'From.role',
				'Message.date_add', 'Message.private'
			),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Message.date_add ASC',
			'paramType' => 'querystring',
			'limit' => 2000
		);


		$messages = $this->Paginator->paginate($this->Message);

		//check si message unique
		foreach ($messages as &$message) {
			$cut_message = explode('<!---->', $message['Message']['content']);
			$contenu_test = $cut_message[2];
			$nb_content = 0;
			$nb_to_id = 0;
			foreach ($messages as $msg) {
				$cut_comp_message = explode('<!---->', $msg['Message']['content']);
				$contenu_comp = $cut_comp_message[2];

				if ($contenu_test == $contenu_comp) {
					$nb_content = $nb_content + 1;
				}
				if ($message['Message']['to_id'] == $msg['Message']['to_id']) $nb_to_id = $nb_to_id + 1;
			}
			if ($nb_content > 1) {
				$message['Message']['statut'] = 'identique';
			} else {
				$message['Message']['statut'] = 'unique';
			}
			if ($nb_to_id > 1) {
				$message['Message']['statut_send'] = 'multiple envoi';
			} else {
				$message['Message']['statut_send'] = '';
			}

			/*	$messa = $this->Message->find("first", array(
					'conditions' => array('Message.private' => 1,'Message.to_id' => $message['Message']['to_id']),
					'order' => 'Message.date_add DESC',
				));
			$message['Message']['date_last_send'] = $messa['Message']['date_add'];*/
		}



		$this->set(compact('messages', 'filtres'));
	}

	public function admin_watchrelancerefus()
	{
		$this->loadModel('Message');
		$conditions = array('Message.private' => 2, 'Message.etat' => 3);

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['texte'])
				$this->Session->write('MessageTexte', $this->params->data['Admin']['texte']);
			else
				$this->Session->write('MessageTexte', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
		}



		if ($this->Session->read('MessageTexte')) {
			$conditions = array('Message.private' => 2, 'Message.etat' => 3, 'Message.content LIKE ' => '%' . $this->Session->read('MessageTexte') . '%');
			$this->set('filtre_texte', $this->Session->read('MessageTexte'));
		}
		if ($this->Session->read('MessageClient')) {
			$conditions = array('Message.private' => 2, 'Message.etat' => 3);
			$conditions['OR'] = array('From.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'From.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'To.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%');
			$this->set('filtre_client', $this->Session->read('MessageClient'));
		}

		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		$this->Paginator->settings = array(
			'fields' => array(
				'Message.id', 'Message.private', 'Message.content', 'Message.attachment', 'Message.etat', 'From.firstname', 'From.id', 'From.pseudo', 'From.lastname', 'From.email', 'To.id', 'To.firstname', 'To.lastname', 'To.email', 'To.pseudo', 'To.role', 'From.role',
				'Message.date_add', 'Message.private'
			),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'Message.date_add ASC',
			'paramType' => 'querystring',
			'limit' => 2000
		);


		$messages = $this->Paginator->paginate($this->Message);


		$this->set(compact('messages', 'filtres'));
	}

	public function modereRelanceAgent()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');
			if (isset($this->request->data['message']) && isset($this->request->data['id_message'])) {
				$this->Message->id = $this->request->data['id_message'];
				$this->Message->saveField('content', $this->request->data['message']);
			}
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function sendRelanceAgent()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');
			$this->loadModel('User');
			App::uses('FrontblockHelper', 'View/Helper');
			$fbH = new FrontblockHelper(new View());
			$withFooter = Configure::read('Email.template.with_footer');
			if ($withFooter)
				$commonFooter = $this->getCmsPage(197, $id_lang);

			$listing_message = array();


			if (isset($this->request->data['messages'])) {
				$listing_message = $this->request->data['messages'];
			} else {
				if (isset($this->request->data['id_message'])) {
					$listing_message = array($this->request->data['id_message']);
					//$titre = $this->request->data['title'];
					//$contenu = $this->request->data['content'];
				}
			}

			foreach ($listing_message as $id_message) {
				$titre = '';
				$contenu = '';
				if ($id_message) {
					$message = $this->Message->find('first', array(
						'conditions' => array('Message.id' => $id_message),
						'recursive' => -1
					));
					if ($message['Message']['to_id'] && $message['Message']['from_id']) {
						$conditions = array(
							'User.id' => $message['Message']['to_id'],
						);

						$user_mail = $this->User->find('first', array('conditions' => $conditions));

						$conditions = array(
							'User.id' => $message['Message']['from_id'],
						);

						$agent_mail = $this->User->find('first', array('conditions' => $conditions));

						$tabcontent = explode('<!---->', $message['Message']['content']);
						if ($tabcontent[0]) $titre = $tabcontent[0];
						$tabcontent[1] = str_replace('CLIENT', $user_mail['User']['firstname'], $tabcontent[1]);

						if ($tabcontent[1]) $contenu = nl2br($tabcontent[1]) . '<br /><br />' . nl2br($tabcontent[2]) . '<br /><br />' . nl2br($tabcontent[3]);
						$agent_number = $agent_mail['User']['agent_number'];
						$path = array('Image' => 'Site.pathPhoto', 'Audio' => 'Site.pathPresentation');
						$pathValidation = array('Image' => 'Site.pathPhotoValidation', 'Audio' => 'Site.pathPresentationValidation');
						$validation = false;
						$format = 'Image';
						$photo = '<img src="' . Router::url('/', true) . Configure::read(($validation ? $pathValidation[$format] : $path[$format])) . '/' . $agent_number[0] . '/' . $agent_number[1] . '/' . $agent_number . '_listing.jpg" style="border-radius: 50%;max-height: 90px;max-width: 90px;" /><br /><br />';

						$bottom_link = '<p style="text-align:center"><a href="' . Router::url('/', true) . 'accounts/mails?private=1" style="text-transform:uppercase">accéder a mon compte Talkappdev</a></p>'; //Accéder a mon compte spiriteo';

						if ($contenu && $titre) {
							//mise en forme
							$contenu = '<p style="line-height:12px;font-size:14px;color:#000">' . $contenu . '</p>';

							$parms = array();
							$parms['content'] = $photo . $contenu . $bottom_link;
							$parms['SITE_NAME'] = Configure::read('Site.name');
							$parms['PARAM_URLSITE'] = Router::url('/', true);
							$parms['FOOTER_HTML'] = str_replace('##SITE_NAME##', Configure::read('Site.name'), $commonFooter['PageLang']['content']);

							$send = $this->sendEmail($user_mail['User']['email'], $titre, 'default', $parms);
							if ($send) {
								$this->Message->id = $id_message;
								$this->Message->saveField('private', 1);
								$this->Message->saveField('etat', 0);
							}
						}
					}
				}
			}

			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function refusRelanceAgent()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');
			$this->loadModel('User');

			if (isset($this->request->data['messages'])) {
				$listing_message = $this->request->data['messages'];
			} else {
				if (isset($this->request->data['id_message'])) {
					$listing_message = array($this->request->data['id_message']);
				}
			}

			$id_agent = '';
			$list_agent = array();

			foreach ($listing_message as $id_message) {

				$message = $this->Message->find('first', array(
					'conditions' => array('Message.id' => $id_message),
					'recursive' => -1
				));

				$id_agent = $message['Message']['from_id'];




				array_push($list_agent, $id_agent);

				$this->Message->id = $id_message;
				$content = $this->request->data['refus'] . '###' . $message['Message']['content'];
				$this->Message->saveField('content', $content);
				$this->Message->saveField('etat', 3);
			}
			$list_agent = array_unique($list_agent);
			foreach ($list_agent as $idagent) {
				$conditions = array(
					'User.id' => $idagent,
				);
				$user_mail = $this->User->find('first', array('conditions' => $conditions));
				$test = $this->sendCmsTemplateByMail(311, $this->Session->read('Config.id_lang'), $user_mail['User']['email'], array(
					'PARAM_PSEUDO' => $user_mail['User']['pseudo'],

				));
			}
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function retablirRelanceAgent()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');

			if (isset($this->request->data['id_message'])) {
				$listing_message = array($this->request->data['id_message']);

				$message = $this->Message->find('first', array(
					'conditions' => array('Message.id' => $this->request->data['id_message']),
					'recursive' => -1
				));


				$this->Message->id = $this->request->data['id_message'];
				$ct = explode('###', $message['Message']['content']);

				$this->Message->saveField('content', $ct[1]);
				$this->Message->saveField('etat', 2);
				$this->Message->saveField('deleted', 0);
			}

			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function deleteMessagePrivate()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');
			$this->loadModel('User');

			if (!isset($this->request->data['id_message'])) {
				return;
			} else {
				$id_message = $this->request->data['id_message'];
			}


			$this->Message->id = $id_message;
			$this->Message->saveField('deleted', 1);
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function archiveMessagePrivate()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('Message');
			$this->loadModel('User');

			if (!isset($this->request->data['id_message'])) {
				return;
			} else {
				$id_message = $this->request->data['id_message'];
			}


			$this->Message->id = $id_message;
			$this->Message->saveField('archive', 1);
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function admin_livecom()
	{
	}
	public function livecom()
	{

		if ($this->request->is('ajax')) {

			$html = '';
			$this->loadModel('Callinfo');
			$this->Callinfo->useTable = 'call_infos';

			//Les conditions de base
			$conditions = array(
				/*'Callinfo.accepted' => 'yes',*/
				'Callinfo.time_start !=' => NULL,
				'Callinfo.time_end' => NULL,
				'Callinfo.time_stop' => NULL,
			);

			$live_phone = $this->Callinfo->find('all', array(
				'conditions'    => $conditions,
				'order'         => 'Callinfo.time_start asc',
				'recursive'     => -1
			));


			$this->loadModel('Chat');

			//Les conditions de base
			$conditions = array('Chat.consult_date_start !=' => NULL, 'Chat.etat' => 1, 'Chat.consult_date_end' => NULL, 'Chat.date_end' => NULL);

			$live_chat = $this->Chat->find('all', array(
				'conditions'    => $conditions,
				'order'         => 'Chat.consult_date_start asc',
				'recursive'     => -1
			));

			$list_consult = array();
			$this->loadModel('User');
			$dateNow = date('Y-m-d H:i:s');
			$tmstmpEnd = new DateTime($dateNow);
			$tmstmpEnd = $tmstmpEnd->getTimestamp();

			foreach ($live_phone as $phone) {
				$obj = new stdClass();
				$agent = $this->User->find('first', array(
					'conditions' => array('User.agent_number' => $phone['Callinfo']['agent']),
					'recursive' => -1
				));
				if ($phone['Callinfo']['customer']) {
					$client = $this->User->find('first', array(
						'conditions' => array('User.personal_code' => $phone['Callinfo']['customer']),
						'recursive' => -1
					));
				} else {
					$client = $this->User->find('first', array(
						'conditions' => array('User.id' => 286),
						'recursive' => -1
					));
				}

				if ($client['User']['firstname'] == 'AUDIOTEL') {
					switch ($phone['Callinfo']['line']) {
						case 'CH-0901801885':
						case 'CH-+41225183456':
							$client['User']['firstname'] = 'Suisse audiotel';
							break;
						case 'BE-090755456':
						case 'BE-+3235553456':
							$client['User']['firstname'] = 'Belgique audiotel';
							break;
						case 'BE-090755456 mob.':
							$client['User']['firstname'] = 'Belgique mob. audiotel';
							break;
						case 'LU-+35227864456':
						case 'LU-90128222':
							$client['User']['firstname'] = 'Luxembourg audiotel';
							break;
						case 'CA-+18442514456':
						case 'CA-19007884466':
						case 'CA-19005289010':
							$client['User']['firstname'] = 'Canada audiotel';
							break;
						case 'CA-#4466 Bell':
						case 'CA-#9010 Bell':
							$client['User']['firstname'] = 'Canada mob. audiotel';
							break;
						case 'CA-#4466 Rogers/Fido':
						case 'CA-#9010 Rogers/Fido':
							$client['User']['firstname'] = 'Canada mob. audiotel';
							break;
						case 'CA-#4466 Telus':
						case 'CA-#9010 Telus':
							$client['User']['firstname'] = 'Canada mob. audiotel';
							break;
						case 'CA-#4466 Videotron':
						case 'CA-#9010 Videotron':
						case 'AT-431230460013':
							$client['User']['firstname'] = 'Canada mob. audiotel';
							break;
					}

					//audiotel pseudo talkappdev
					$client['User']['firstname'] .= ' (AUDIO' . (substr($phone['Callinfo']['callerid'], -4) * 15) . ')';
				}

				$obj->agent = $agent['User']['pseudo'];
				$obj->agent_id = $agent['User']['id'];
				$obj->agent_number = $agent['User']['agent_number'];
				$obj->client = $client['User']['firstname'];
				$obj->client_id = $client['User']['id'];
				$obj->mode = 'phone';
				$obj->start = CakeTime::format(date('Y-m-d H:i:s', $phone['Callinfo']['time_start']), '%Y-%m-%d %H:%M:%S');
				$tmstmpStart = new DateTime(date('Y-m-d H:i:s', $phone['Callinfo']['time_start']));
				$tmstmpStart = $tmstmpStart->getTimestamp();
				$obj->spendtime = ($tmstmpEnd - $tmstmpStart);
				$obj->credit = $client['User']['credit'];
				$key = CakeTime::format(date('Y-m-d H:i:s', $phone['Callinfo']['time_start']), '%Y%m%d%H%M%S');
				$list_consult[$key] = $obj;
			}

			foreach ($live_chat as $chat) {
				$obj = new stdClass();
				$agent = $this->User->find('first', array(
					'conditions' => array('User.id' => $chat['Chat']['to_id']),
					'recursive' => -1
				));
				$client = $this->User->find('first', array(
					'conditions' => array('User.id' => $chat['Chat']['from_id']),
					'recursive' => -1
				));
				$obj->agent = $agent['User']['pseudo'];
				$obj->agent_id = $agent['User']['id'];
				$obj->agent_number = $agent['User']['agent_number'];
				$obj->client = $client['User']['firstname'];
				$obj->client_id = $client['User']['id'];
				$obj->mode = 'chat';
				$obj->start = CakeTime::format($chat['Chat']['consult_date_start'], '%Y-%m-%d %H:%M:%S');
				$tmstmpStart = new DateTime($chat['Chat']['consult_date_start']);
				$tmstmpStart = $tmstmpStart->getTimestamp();
				$obj->spendtime = ($tmstmpEnd - $tmstmpStart);
				$obj->credit = $client['User']['credit'];
				$key = CakeTime::format($chat['Chat']['consult_date_start'], '%Y%m%d%H%M%S');
				$list_consult[$key] = $obj;
			}



			foreach ($list_consult as $consult) {

				$html .= '<div class="span4" style="border:1px solid #eee;padding:10px;min-height:225px;margin-left:0px;">';

				$html .= '<span class="live_agent_toff span3"><img src="/media/photo/' . substr($consult->agent_number, 0, 1) . '/' . substr($consult->agent_number, 1, 1) . '/' . $consult->agent_number . '_listing.jpg" alt=""></span>';
				$html .= '<span class="live_agent span7"><a href="/admin/agents/view-' . $consult->agent_id . '">' . $consult->agent . '</a></span>';
				$html .= '<span class="live_mode span1 ' . $consult->mode . '"></span>';

				$html .= '<span style="clear:both;" class="live_client span7">Client : <a href="/admin/accounts/view-' . $consult->client_id . '">' . $consult->client . '</a></span>';
				$html .= '<span class="live_credit span4">Crédit : <strong>' . $consult->credit . '</strong></span>';
				$html .= '<span class="live_start span12">Début : ' . CakeTime::format(Tools::dateUser('Europe/Paris', $consult->start), '%d/%m/%y %Hh%Mmin%Ss') . '</span>';
				$html .= '<span class="live_time span12">Durée : <strong>' . $consult->spendtime . ' sec.</strong></span>';
				$html .= '</div>';
			}

			$heure = date('G');
			$ratio = 3;
			if ($heure >= 23 && $heure < 24) {
				$ratio = 2;
			}
			if ($heure >= 0 && $heure <= 8) {
				$ratio = 2;
			}
			//recup nb expert dispo en tchat ou tel
			$agents_connected = $this->User->find(
				"all",
				array(
					'fields'     => array('User.id', 'User.consult_chat', 'User.consult_phone'),
					'conditions' => array(
						'role' => 'agent', 'active' => 1, 'valid' => 1, 'deleted' => 0, 'agent_status' => 'available',										   'OR' => array('consult_phone' => 1, 'consult_chat = 1 and date_last_activity >= \'' . $date_min . '\'')
					),
					'recursive' => -1,
					'group' => array('User.id')
				)
			);

			$fakeagent = array(318, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 380, 384, 390, 403, 423, 442, 443, 464, 469, 484);
			$agents_busy = $this->User->find(
				"all",
				array(
					'fields'     => array('User.id', 'User.consult_chat', 'User.consult_phone'),
					'conditions' => array('role' => 'agent', 'active' => 1, 'valid' => 1, 'deleted' => 0, 'agent_status' => 'busy'),
					'recursive' => -1
				)
			);
			foreach ($agents_busy as $k => $i) {
				if (in_array($i['User']['id'], $fakeagent)) unset($agents_busy[$k]);
			}
			$nb_expert_be_connected = count($agents_busy) * $ratio;
			if ($nb_expert_be_connected > 21) $nb_expert_be_connected = 21;

			$this->jsonRender(array('return' => true, 'html' => $html, 'ratio' => $ratio, 'dispo' => count($agents_connected), 'busy' => count($agents_busy), 'need' => $nb_expert_be_connected));
		}
	}

	public function doneRequestFailed($request)
	{

		$this->loadModel('CallInfoRequestFailed');

		$request = trim($request);


		$request_db = $this->CallInfoRequestFailed->find('first', array(
			'conditions' => array('CallInfoRequestFailed.request' => $request),
			'recursive'     => -1
		));

		if (!$request_db) {

			$request_db = array(
				'request'          => $request,
				'user_id'         => $this->Auth->user('id'),
				'date_add'          => date('Y-m-d H:i:s')
			);
			$this->CallInfoRequestFailed->create();
			$this->CallInfoRequestFailed->save($request_db);

			$tab = explode(',', $request);

			$url = 'https://fr.talkappdev.com' . $tab[0];
			$postdata = $tab[1];

			$curl_connection = curl_init($url);
			curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
			curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $postdata);
			$result = curl_exec($curl_connection);
			//print_r(curl_getinfo($curl_connection));
			//echo curl_errno($curl_connection) . '-' .
			//         curl_error($curl_connection);
			curl_close($curl_connection);


			if ($result)
				$this->Session->setFlash(__('Votre request a été traité.'), 'flash_success');

			else
				$this->Session->setFlash(__('Erreur cette request pas valable.'), 'flash_error');
		} else {
			$this->Session->setFlash(__('Erreur cette request déja traité.'), 'flash_error');
		}
		$this->redirect(array('controller' => 'admins', 'action' => 'callinfosview', 'admin' => true), false);
	}

	public function dashboard_stats_crm()
	{
		return array(
			'1' => 'JUST_REGISTERED_RELANCE_NO_BUY_1H',
			'2' => 'JUST_REGISTERED_RELANCE_NO_BUY_3H',
			'3' => 'JUST_REGISTERED_RELANCE_NO_BUY_1J',
			'4' => 'JUST_REGISTERED_RELANCE_NO_BUY_2J',
			'5' => 'JUST_REGISTERED_RELANCE_NO_BUY_7J',
			'6' => 'JUST_REGISTERED_RELANCE_NO_BUY_14J',
			'7' => 'JUST_REGISTERED_RELANCE_NO_BUY_30J',
			'8' => 'JUST_REGISTERED_RELANCE_NO_BUY_60J',
			'9' => 'JUST_REGISTERED_RELANCE_NO_BUY_90J',
			'10' => 'JUST_REGISTERED_RELANCE_NO_BUY_120J',
			'11' => 'JUST_REGISTERED_RELANCE_NO_BUY_150J',
			'12' => 'JUST_REGISTERED_RELANCE_NO_BUY_180J',
			'13' => 'JUST_REGISTERED_RELANCE_NO_BUY_210J',
			'14' => 'JUST_REGISTERED_RELANCE_NO_BUY_240J',
			'15' => 'JUST_REGISTERED_RELANCE_NO_BUY_270J',
			'16' => 'JUST_REGISTERED_RELANCE_NO_BUY_300J',
			'17' => 'JUST_REGISTERED_RELANCE_NO_BUY_330J',
			'18' => 'JUST_REGISTERED_RELANCE_NO_BUY_360J',
			'19' => 'RELANCE_NO_BUY_SINCE_14J',
			'20' => 'RELANCE_NO_BUY_SINCE_45J',
			'21' => 'RELANCE_NO_BUY_SINCE_60J',
			'22' => 'RELANCE_NO_BUY_SINCE_90J',
			'23' => 'RELANCE_NO_BUY_SINCE_120J',
			'24' => 'RELANCE_NO_BUY_SINCE_150J',
			'25' => 'RELANCE_NO_BUY_SINCE_180J',
			'26' => 'RELANCE_NO_BUY_SINCE_210J',
			'27' => 'RELANCE_NO_BUY_SINCE_240J',
			'28' => 'RELANCE_NO_BUY_SINCE_270J',
			'29' => 'RELANCE_NO_BUY_SINCE_300J',
			'30' => 'RELANCE_NO_BUY_SINCE_330J',
			'31' => 'RELANCE_NO_BUY_SINCE_360J',
			'32' => 'RELANCE_NO_BUY_SINCE_540J',
			'33' => 'RELANCE_NO_BUY_SINCE_720J',
			'34' => 'RELANCE_PANIER_NO_BUY_SINCE_LAST_VISIT',
			'35' => 'RELANCE2_PANIER_NO_BUY_SINCE_LAST_VISIT',
			'36' => 'RELANCE_VISIT_PROFIL_EXPERT_LAST_VISIT',
			'38' => 'RELANCE_LOYALTY_NO_USE_SINCE_30J',
			'39' => 'RELANCE3_PANIER_NO_BUY_SINCE_LAST_VISIT'
		);
	}

	public function dashboard_stats_sms()
	{
		return array(
			'ADMIN CONTACT' => 'ADMIN CONTACT',
			'ALERTE EXPERT' => 'ALERTE EXPERT',
			'CONSULT EMAIL' => 'CONSULT EMAIL',
			'CONSULT EMAIL' => 'CONSULT EMAIl',
			'CONSULT TCHAT' => 'CONSULT TCHAT',
			'DISPO EXPERT' => 'DISPO EXPERT',
		);
	}
	public function dashboard_stats_crmvoucher()
	{
		return array(
			'OLD_CODE_PROMO_5_MIN' => 'OLD_5_MIN',
			'NEW_CODE_PROMO_5_MIN' => 'NEW_5_MIN',
			'OLD_CODE_PROMO_10_MIN' => 'OLD_10_MIN',
			'NEW_CODE_PROMO_10_MIN' => 'NEW_10_MIN',
			'ISA_1H_5MIN' => 'ISA_1H_5MIN',
			'ISA_3H_5MIN' => 'ISA_3H_5MIN',
			'ISA_1J_10MIN' => 'ISA_1J_10MIN',
			'ISA_2J_10MIN' => 'ISA_2J_10MIN',
			'ISA_7J_10MIN' => 'ISA_7J_10MIN',
			'ISA_14J_10MIN' => 'ISA_14J_10MIN',
			'RAC_14J_5MIN' => 'RAC_14J_5MIN',
			'RAC_14J_5MIN' => 'RAC_14J_5MIN',
			'RAC_45J_5MIN' => 'RAC_45J_5MIN',
			'RAC_60J_5MIN' => 'RAC_60J_5MIN',
			'RAC_90J_5MIN' => 'RAC_90J_5MIN',
			'PA_RAC_1J_10MIN' => 'PA_RAC_1J_10MIN',
			'PA_ISA_1J_10MIN' => 'PA_ISA_1J_10MIN',
			'PA_RAC_2J_10MIN' => 'PA_RAC_2J_10MIN',
			'PA_ISA_2J_10MIN' => 'PA_ISA_2J_10MIN',
		);
	}



	public function dashboard_stats_country()
	{
		return array(
			'1' => 'France',
			'3' => 'Suisse',
			'4' => 'Belgique',
			'5' => 'Luxembourg',
			'13' => 'Canada'
		);
	}
	public function dashboard_stats_domain()
	{
		return array(
			'19' => 'France',
			'13' => 'Suisse',
			'11' => 'Belgique',
			'22' => 'Luxembourg',
			'29' => 'Canada'
		);
	}

	public function dashboard_stats_type()
	{
		return array(
			'pre' => 'prepaye',
			'aud' => 'audiotel',
		);
	}

	public function dashboard_stats_source()
	{
		return array(
			'AUDIOTEL' => 'AUDIOTEL',
			'AUDIOTEL Suisse' => 'AUDIOTEL Suisse',
			'AUDIOTEL Belgique fixe' => 'AUDIOTEL Belgique',
			'AUDIOTEL Belgique Mob' => 'AUDIOTEL Belgique',
			'AUDIOTEL Luxembourg' => 'AUDIOTEL Luxembourg',
			'AUDIOTEL Canada fixe' => 'AUDIOTEL Canada',
			'AUDIOTEL Canada mobile Bell' => 'AUDIOTEL Canada',
			'AUDIOTEL Canada mobile Telus' => 'AUDIOTEL Canada',
			'AUDIOTEL Canada mobile Videotron' => 'AUDIOTEL Canada',
			'AUDIOTEL Canada mobile Rogers' => 'AUDIOTEL Canada',
			'Google DSA' => 'Google DSA',
			'Google Ads' => 'Google Ads',
			'Google Ads landing' => 'Google Ads',
			'Talkappdev Google Ads (organic)' => 'Google Ads',
			'Google' => 'Google',
			'Facebook' => 'Facebook',
			'www.facebook.com' => 'Facebook',
			'FACEBOOK' => 'Facebook',
			'm.facebook.com' => 'Facebook',
			'l.facebook.com' => 'Facebook',
			'lm.facebook.com' => 'Facebook',
			'Bing' => 'Bing',
			'Blog' => 'Blog',
			'blog.talkappdev.com' => 'Blog',
			'Direct' => 'Direct',
			'r.email.talkappdev.com' => 'Direct',
			'www.talkappdev.com' => 'Direct',
			'www.talkappdev.com' => 'Direct',
			'www.talkappdev.be' => 'Direct',
			'www.talkappdev.lu' => 'Direct',
			'www.talkappdev.ch' => 'Direct',
			'www.talkappdev.ca' => 'Direct',
			'fr.talkappdev.com' => 'Direct',
			'be.talkappdev.com' => 'Direct',
			'lu.talkappdev.com' => 'Direct',
			'ch.talkappdev.com' => 'Direct',
			'ca.talkappdev.com' => 'Direct',
			'SOS Voyants' => 'SOS Voyants',
			'www.sosvoyants.com' => 'SOS Voyants',
			'Indefini' => 'Indefini',
			'parrainage agent hotmail.fr' => 'Parrainage',
			'parrainage agent gmail.com' => 'Parrainage',
			'parrainage agent m.facebook.com' => 'Parrainage',
			'parrainage agent www.talkappdev.be' => 'Parrainage',
			'parrainage agent www.talkappdev.ca' => 'Parrainage',
			'parrainage agent www.talkappdev.lu' => 'Parrainage',
			'parrainage agent www.talkappdev.ch' => 'Parrainage',
			'parrainage agent www.talkappdev.com' => 'Parrainage',
			'parrainage agent be.talkappdev.com' => 'Parrainage',
			'parrainage agent ca.talkappdev.com' => 'Parrainage',
			'parrainage agent lu.talkappdev.com' => 'Parrainage',
			'parrainage agent ch.talkappdev.com' => 'Parrainage',
			'parrainage agent fr.talkappdev.com' => 'Parrainage',
			'parrainage agent www.linkedin.com' => 'Parrainage',
			'parrainage agent com.linkedin.android' => 'Parrainage',
			'parrainage agent www.facebook.com' => 'Parrainage',
			'parrainage agent SOS Voyants' => 'Parrainage',
			'parrainage agent Google' => 'Parrainage',
			'parrainage agent Google Ads' => 'Parrainage',
			'parrainage agent Direct' => 'Parrainage',
			'parrainage agent l.facebook.com' => 'Parrainage',
			'parrainage agent outlook.live.com' => 'Parrainage',
			'parrainage agent webmail1p.orange.fr' => 'Parrainage',
			'parrainage client hotmail.fr' => 'Parrainage',
			'parrainage client gmail.com' => 'Parrainage',
			'parrainage client m.facebook.com' => 'Parrainage',
			'parrainage client www.talkappdev.be' => 'Parrainage',
			'parrainage client www.talkappdev.ca' => 'Parrainage',
			'parrainage client www.talkappdev.lu' => 'Parrainage',
			'parrainage client www.talkappdev.ch' => 'Parrainage',
			'parrainage client www.talkappdev.com' => 'Parrainage',
			'parrainage client be.talkappdev.com' => 'Parrainage',
			'parrainage client ca.talkappdev.com' => 'Parrainage',
			'parrainage client lu.talkappdev.com' => 'Parrainage',
			'parrainage client ch.talkappdev.com' => 'Parrainage',
			'parrainage client fr.talkappdev.com' => 'Parrainage',
			'parrainage agent lm.facebook.com' => 'Parrainage',
			'parrainage client Google Ads' => 'Parrainage',
			'parrainage client SOS Voyants' => 'Parrainage',
			'parrainage client Direct' => 'Parrainage',
			'parrainage client Google' => 'Parrainage',
			'parrainage client outlook.live.com' => 'Parrainage',
			'Autre' => 'Autre',
			'go-fr.com' => 'go-fr.com',
			'duckduckgo.com' => 'duckduckgo.com',
			'forum.leparisien.fr' => 'forum.leparisien.fr',
			'fr.search.yahoo.com' => 'fr.search.yahoo.com',
			'r.search.yahoo.com' => 'r.search.yahoo.com',
			'search.yahoo.com' => 'search.yahoo.com',
			'fr.zapmeta.ws' => 'fr.zapmeta.ws',
			'int.search.myway.com' => 'int.search.myway.com',
			'int.search.tb.ask.com' => 'int.search.tb.ask.com',
			'meilleurs-voyants-de-france.over-blog.com' => 'meilleurs-voyants-de-france.over-blog.com',
			'outlook.live.com' => 'outlook.live.com',
			'search.1and1.com' => 'search.1and1.com',
			'search.avira.com' => 'search.avira.com',
			'www.qwant.com' => 'www.qwant.com',
			'search.lilo.org' => 'search.lilo.org',
			'www.vinden.be' => 'www.vinden.be',
			'search.handycafe.com' => 'search.handycafe.com',
			'forum.leparisien.fr' => 'forum.leparisien.fr',
			'www.zapmeta.fr' => 'www.zapmeta.fr',
			'chiens-en-pension.be' => 'chiens-en-pension.be',
			'www.ecosia.org' => 'www.ecosia.org',
			'emma.blog-a-idees.over-blog.com' => 'emma.blog-a-idees.over-blog.com',
			'www.youtube.com' => 'Youtube',
			'l.instagram.com' => 'Instagram',
			'wmail.orange.fr' => 'wmail.orange.fr',
			'losx.xyz' => 'losx.xyz',
			'forum.magicmaman.com' => 'forum.magicmaman.com',
			'www.searchingdog.com' => 'www.searchingdog.com',
			'quebec-search.com' => 'quebec-search.com',
			'carte cadeau' => 'carte cadeau',
			'us.search.yahoo.com' => 'us.search.yahoo.com',
			'search.visymo.com' => 'search.visymo.com',
			'annonces.esopole.com' => 'annonces.esopole.com',
			'esopole.com' => 'esopole.com',
			'webmail1n.orange.fr' => 'webmail1n.orange.fr',
			'm.laposte.net' => 'm.laposte.net',
			'www.seekkees.com' => 'www.seekkees.com',
			'www.talkappdev.de' => 'Direct',
			'messagerieweb.globetrotter.net' => 'messagerieweb.globetrotter.net',
			'www.bestof-romandie.ch' => 'www.bestof-romandie.ch',
			'mcpl.xyz' => 'mcpl.xyz',
			'landing' => 'Landing',
			'www.search-story.com' => 'www.search-story.com',
			'search.monstercrawler.com' => 'search.monstercrawler.com',
			'www.pronto.com' => 'www.pronto.com',

			'' => 'Indefini',
			'source' => 'Indefini',
		);
	}

	public function dashboard_stats_cart()
	{
		return array(
			'-1' => 'panier abandon',
			'0' => 'achat abandon',
			'1' => 'achat finalisé',
		);
	}

	public function dashboard_stats_forfait()
	{
		return array(
			'41' => 'autre',
			'39' => 'autre',
			'38' => '120 minutes',
			'37' => 'autre',
			'36' => 'autre',
			'34' => '90 minutes',
			'33' => '60 minutes',
			'32' => '30 minutes',
			'31' => '15 minutes',
			'29' => '120 minutes',
			'28' => '90 minutes',
			'27' => '60 minutes',
			'26' => '30 minutes',
			'25' => '15 minutes',
			'24' => '10 minutes',
			'23' => '10 minutes',
			'22' => 'autre',
			'21' => '120 minutes',
			'20' => '90 minutes',
			'19' => '60 minutes',
			'18' => '30 minutes',
			'17' => '15 minutes',
			'16' => '10 minutes',
			'15' => 'autre',
			'14' => 'autre',
			'13' => 'autre',
			'12' => '120 minutes',
			'11' => '90 minutes',
			'10' => '60 minutes',
			'9' => '30 minutes',
			'8' => '15 minutes',
			'7' => '10 minutes',
			'6' => '120 minutes',
			'5' => '90 minutes',
			'4' => '60 minutes',
			'3' => '30 minutes',
			'2' => '15 minutes',
			'1' => '10 minutes',

		);
	}

	public function dashboard_stats_new()
	{
		return array(
			'0' => 'non',
			'1' => 'oui',
		);
	}

	public function dashboard_end_day()
	{
		return array(
			"01" => 31,
			"02" => 28,
			"03" => 31,
			"04" => 30,
			"05" => 31,
			"06" => 30,
			"07" => 31,
			"08" => 31,
			"09" => 30,
			"10" => 31,
			"11" => 30,
			"12" => 31
		);
	}

	public function dashboard_timing_static()
	{
		$end_day      = $this->dashboard_end_day();
		$dashboard_timing = array();

		$utc_dec = Configure::read('Site.utc_dec');
		$listing_utcdec = Configure::read('Site.utcDec');

		$dx = new DateTime(date('Y-m-d 00:00:00'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['today']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime(date('Y-m-d 23:59:59'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['today']['max'] = $dx->format('Y-m-d H:i:s');

		$dx = new DateTime(date('Y-m-d 00:00:00'));
		$dx->modify('- 1 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['yesterday']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime(date('Y-m-d 23:59:59'));
		$dx->modify('- 1 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['yesterday']['max'] = $dx->format('Y-m-d H:i:s');

		$dx = new DateTime(date('Y-m-d 00:00:00'));
		$dx->modify('- 2 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_day']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime(date('Y-m-d 23:59:59'));
		$dx->modify('- 2 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_day']['max'] = $dx->format('Y-m-d H:i:s');

		$dx = new DateTime(date('Y-m-d 00:00:00'));
		$dx->modify('- 7 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_week']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime(date('Y-m-d 23:59:59'));
		$dx->modify('- 7 day');
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_week']['max'] = $dx->format('Y-m-d H:i:s');

		$last = $end_day[date('m')];
		$dx = new DateTime(date('Y-m-01 00:00:00'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['month']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime(date('Y-m-' . $last . ' 23:59:59'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['month']['max'] = $dx->format('Y-m-d H:i:s');

		$dx = new DateTime(date('Y-m-d H:i:s'));
		$firstDate = new \DateTime(date('Y-m-d H:i:s'));
		$endDate = clone $firstDate;
		$firstDate->modify('first day of -1 month');
		$endDate->modify('last day of -1 month');
		$dx = new DateTime($firstDate->format('Y-m-d 00:00:00'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_month']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime($endDate->format('Y-m-d 23:59:59'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev_month']['max'] = $dx->format('Y-m-d H:i:s');
		//$last = $end_day[$dx->format('m')];
		//$dashboard_timing['prev_month']['min'] = $dx->format('Y-m-01 00:00:00');
		//$dashboard_timing['prev_month']['max'] = $dx->format('Y-m-'.$last.' 23:59:59');

		$dx = new DateTime(date('Y-m-d H:i:s'));
		$firstDate = new \DateTime(date('Y-m-d H:i:s'));
		$endDate = clone $firstDate;
		$firstDate->modify('first day of -2 month');
		$endDate->modify('last day of -2 month');
		$dx = new DateTime($firstDate->format('Y-m-d 00:00:00'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev2_month']['min'] = $dx->format('Y-m-d H:i:s');
		$dx = new DateTime($endDate->format('Y-m-d 23:59:59'));
		$dx->modify('- ' . $listing_utcdec[$dx->format('md')] . ' hour');
		$dashboard_timing['prev2_month']['max'] = $dx->format('Y-m-d H:i:s');
		/*$dx->modify('- 2 month');
		$last = $end_day[$dx->format('m')];
		$dashboard_timing['prev2_month']['min'] = $dx->format('Y-m-01 00:00:00');
		$dashboard_timing['prev2_month']['max'] = $dx->format('Y-m-'.$last.' 23:59:59');*/

		$dx = new DateTime(date('Y-m-d H:i:s'));
		$dx->modify('- 1 year');
		$dx2 = new DateTime($dx->format('Y-m-d 00:00:00'));
		$dx2->modify('- ' . $listing_utcdec[$dx2->format('md')] . ' hour');
		$dashboard_timing['prev_year']['min'] = $dx2->format('Y-m-d H:i:s');
		$dx2 = new DateTime($dx->format('Y-m-d 23:59:59'));
		$dx2->modify('- ' . $listing_utcdec[$dx2->format('md')] . ' hour');
		$dashboard_timing['prev_year']['max'] = $dx2->format('Y-m-d H:i:s');

		$dx = new DateTime(date('Y-m-d H:i:s'));
		$firstDate = new \DateTime(date('Y-m-d H:i:s'));
		$endDate = clone $firstDate;
		$firstDate->modify('first day of -1 year');
		$endDate->modify('last day of -1 year');
		$dx2 = new DateTime($firstDate->format('Y-m-d 00:00:00'));
		$dx2->modify('- ' . $listing_utcdec[$dx2->format('md')] . ' hour');
		$dashboard_timing['prev_month_year']['min'] = $dx2->format('Y-m-d H:i:s');
		$dx2 = new DateTime($endDate->format('Y-m-d 23:59:59'));
		$dx2->modify('- ' . $listing_utcdec[$dx2->format('md')] . ' hour');
		$dashboard_timing['prev_month_year']['max'] = $dx2->format('Y-m-d H:i:s');
		/*$dx->modify('- 1 year');
		$last = $end_day[$dx->format('m')];
		$dashboard_timing['prev_month_year']['min'] = $dx->format('Y-m-01 00:00:00');
		$dashboard_timing['prev_month_year']['max'] = $dx->format('Y-m-'.$last.' 23:59:59');*/

		return $dashboard_timing;
	}

	public function admin_dashboard_calc($dashboard_construct, $dashboard_timing)
	{
		$this->loadModel('Order');
		$this->loadModel('UserCreditHistory');
		$this->loadModel('CartLoose');
		$this->loadModel('User');
		$this->loadModel('CrmStat');
		$this->loadModel('SmsHistory');
		$dashboards = array();


		$timezone = 'Europe/Paris';

		foreach ($dashboard_construct as $obj_dashboard) {

			$dashboards[$obj_dashboard->statistique] = array();
			foreach ($dashboard_timing as $timing => $timing_tranche) {
				$request_key_stat = 'hour';
				if ($timing == 'month' || $timing == 'prev_month' || $timing == 'prev2_month' || $timing == 'prev_month_year' || $timing == 'custom') $request_key_stat = 'day';

				if ($timing == 'custom') $request_key_stat = 'monthday';

				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && $obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("hour( CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "') ) as hour", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.is_new' => 1, 'Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as hour", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && $obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as day", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.is_new' => 1, 'Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && $obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.is_new' => 1, 'Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%M%D'), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as day", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "Order.country_id as country", "count(Order.id) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%M%D'), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as hour", "Order.country_id as country", "sum(Order.product_credits) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as day", "Order.country_id as country", "sum(Order.product_credits) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.country_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'country' && !$obj_dashboard->request_new)
					$rows = $this->Order->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "Order.country_id as country", "sum(Order.product_credits) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "'),'%Y%M%D'), Order.country_id ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.media as media", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.media as media", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media  ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.media as media", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'media' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.media as media", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.media ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.domain_id as domain", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.domain_id as domain", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id  ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.domain_id as domain", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.domain_id as domain", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.type_pay as type_pay", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.type_pay ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.type_pay as type_pay", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.type_pay ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.type_pay as type_pay", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.type_pay ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.type_pay as type_pay", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.type_pay  ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.type_pay as type_pay", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.type_pay ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistory' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'type_pay' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "UserCreditHistory.type_pay as type_pay", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), UserCreditHistory.type_pay ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'UserCreditHistorybyUser' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "User.source as source", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "DATE_FORMAT(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "'),'%Y%M%D'), User.source ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'CartLoose' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'status' && !$obj_dashboard->request_new)
					$rows = $this->CartLoose->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "')) as hour", "CartLoose.status as status", "count(CartLoose.id) as nb"),
						'conditions'    => array('CartLoose.date_cart >=' => $timing_tranche['min'], 'CartLoose.date_cart <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "')), CartLoose.status ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'CartLoose' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'status' && !$obj_dashboard->request_new)
					$rows = $this->CartLoose->find('all', array(
						'fields'        => array("day(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "')) as day", "CartLoose.status as status", "count(CartLoose.id) as nb"),
						'conditions'    => array('CartLoose.date_cart >=' => $timing_tranche['min'], 'CartLoose.date_cart <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "')), CartLoose.status ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'CartLoose' && $request_key_stat == 'monthday' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'status' && !$obj_dashboard->request_new)
					$rows = $this->CartLoose->find('all', array(
						'fields'        => array("DATE_FORMAT(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "'),'%Y%m%d') as monthday", "CartLoose.status as status", "count(CartLoose.id) as nb"),
						'conditions'    => array('CartLoose.date_cart >=' => $timing_tranche['min'], 'CartLoose.date_cart <=' => $timing_tranche['max']),
						'group'         => "DATE_FORMAT(CONVERT_TZ(CartLoose.date_cart, 'UTC', '" . $timezone . "'),'%Y%M%D'), CartLoose.status ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'User' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client'),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("day(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1),
						'group'         => "day(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_FR' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client', 'User.domain_id' => 19),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_FR' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.domain_id' => 19),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_FR' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 19),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_FR' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 19),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_BE' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client', 'User.domain_id' => 11),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_BE' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.domain_id' => 11),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_BE' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 11),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_BE' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 11),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_CH' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client', 'User.domain_id' => 13),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_CH' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.domain_id' => 13),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_CH' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 13),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_CH' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 13),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_LU' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client', 'User.domain_id' => 22),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_LU' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.domain_id' => 22),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_LU' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 22),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_LU' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 22),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_CA' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client', 'User.domain_id' => 29),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User_CA' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.domain_id' => 29),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_CA' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new) {
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 29),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source",
						'recursive'     => -1
					));
				}
				if ($obj_dashboard->request == 'UserCreditHistorybyUser_CA' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'source' && $obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "User.source as source", "count(UserCreditHistory.user_credit_history) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.is_new' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.domain_id' => 29),
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type'  => 'left',
								'conditions' => array(
									'User.id = UserCreditHistory.user_id',
								)
							)
						),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), User.source ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistoryAudiotel' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.domain_id as domain", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.type_pay' => 'aud'),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id  ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistoryAudiotel' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.domain_id as domain", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max'], 'UserCreditHistory.type_pay' => 'aud'),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistoryByNew' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'is_new' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as hour", "UserCreditHistory.is_new as is_new", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.is_new  ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'UserCreditHistoryByNew' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Sum' && $obj_dashboard->request_filtre == 'is_new' && !$obj_dashboard->request_new)
					$rows = $this->UserCreditHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')) as day", "UserCreditHistory.is_new as is_new", "sum(UserCreditHistory.credits) as nb"),
						'conditions'    => array('UserCreditHistory.is_factured' => 1, 'UserCreditHistory.date_start >=' => $timing_tranche['min'], 'UserCreditHistory.date_start <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(UserCreditHistory.date_start, 'UTC', '" . $timezone . "')), UserCreditHistory.is_new ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as hour", "User.domain_id as domain", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1, 'User.role' => 'client'),
						'group'         => "hour(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.domain_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'User' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'domain' && !$obj_dashboard->request_new)
					$rows = $this->User->find('all', array(
						'fields'        => array("day(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')) as day", "User.domain_id as domain", "count(User.id) as nb"),
						'conditions'    => array('User.date_add >=' => $timing_tranche['min'], 'User.date_add <=' => $timing_tranche['max'], 'User.active' => 1),
						'group'         => "day(CONVERT_TZ(User.date_add, 'UTC', '" . $timezone . "')), User.domain_id ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'product_id')
					$rows = $this->Order->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as hour", "Order.product_id as product_id", "count(Order.id) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.product_id ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'Order' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'product_id')
					$rows = $this->Order->find('all', array(
						'fields'        => array("day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as day", "Order.product_id as product_id", "count(Order.id) as nb"),
						'conditions'    => array('Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.product_id ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'OrdersVoucher' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'voucher_name')
					$rows = $this->Order->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as hour", "Order.voucher_name as voucher_name", "count(Order.id) as nb"),
						'conditions'    => array(
							'Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max'], 'OR' => array(
								array('Order.voucher_name like' => '%_CODE_PROMO_%'),
								array('Order.voucher_name like' => '%ISA_%'),
								array('Order.voucher_name like' => '%RAC_%'),
								array('Order.voucher_name like' => '%PA_%')
							)
						),
						'group'         => "hour(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.voucher_name ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'OrdersVoucher' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'voucher_name')
					$rows = $this->Order->find('all', array(
						'fields'        => array("day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')) as day", "Order.voucher_name as voucher_name", "count(Order.id) as nb"),
						'conditions'    => array(
							'Order.valid' => 1, 'Order.date_add >=' => $timing_tranche['min'], 'Order.date_add <=' => $timing_tranche['max'], 'OR' => array(
								array('Order.voucher_name like' => '%_CODE_PROMO_%'),
								array('Order.voucher_name like' => '%ISA_%'),
								array('Order.voucher_name like' => '%RAC_%'),
								array('Order.voucher_name like' => '%PA_%')
							)
						),
						'group'         => "day(CONVERT_TZ(Order.date_add, 'UTC', '" . $timezone . "')), Order.voucher_name ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'crm')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as hour", "CrmStat.id_crm as crm", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max']),
						'group'         => "hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'crm')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as day", "CrmStat.id_crm as crm", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max']),
						'group'         => "day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'open')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as hour", "CrmStat.id_crm as open", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max'], 'CrmStat.view' => 1),
						'group'         => "hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'open')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as day", "CrmStat.id_crm as open", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max'], 'CrmStat.view' => 1),
						'group'         => "day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'clic')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as hour", "CrmStat.id_crm as clic", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max'], 'CrmStat.click' => 1),
						'group'         => "hour(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'CrmStat' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'clic')
					$rows = $this->CrmStat->find('all', array(
						'fields'        => array("day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')) as day", "CrmStat.id_crm as clic", "count(CrmStat.id) as nb"),
						'conditions'    => array('CrmStat.date >=' => $timing_tranche['min'], 'CrmStat.date <=' => $timing_tranche['max'], 'CrmStat.click' => 1),
						'group'         => "day(CONVERT_TZ(CrmStat.date, 'UTC', '" . $timezone . "')), CrmStat.id_crm ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as hour", "SmsHistory.type as type", "count(SmsHistory.id) as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number like' => '33%'),
						'group'         => "hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Count' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as day", "SmsHistory.type as type", "count(SmsHistory.id) as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number like' => '33%'),
						'group'         => "day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'CountWorld' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as hour", "SmsHistory.type as type", "count(SmsHistory.id) as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number not like' => '33%'),
						'group'         => "hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'CountWorld' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as day", "SmsHistory.type as type", "count(SmsHistory.id) as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number not like' => '33%'),
						'group'         => "day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'Cost' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as hour", "SmsHistory.type as type", "count(SmsHistory.id) * 0.055 as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number like' => '33%'),
						'group'         => "hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'Cost' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as day", "SmsHistory.type as type", "count(SmsHistory.id) * 0.055 as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number like' => '33%'),
						'group'         => "day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));

				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'hour' && $obj_dashboard->request_type == 'CostWorld' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as hour", "SmsHistory.type as type", "count(SmsHistory.id) * 0.07 as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number not like' => '33%'),
						'group'         => "hour(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));
				if ($obj_dashboard->request == 'SmsHistory' && $request_key_stat == 'day' && $obj_dashboard->request_type == 'CostWorld' && $obj_dashboard->request_filtre == 'type')
					$rows = $this->SmsHistory->find('all', array(
						'fields'        => array("day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')) as day", "SmsHistory.type as type", "count(SmsHistory.id) * 0.07 as nb"),
						'conditions'    => array('SmsHistory.date_add >=' => $timing_tranche['min'], 'SmsHistory.date_add <=' => $timing_tranche['max'], 'SmsHistory.phone_number not like' => '33%'),
						'group'         => "day(CONVERT_TZ(SmsHistory.date_add, 'UTC', '" . $timezone . "')), SmsHistory.type ",
						'recursive'     => -1
					));


				$dashboards[$obj_dashboard->statistique][$timing] = array();
				foreach ($rows as $row) {


					if ($obj_dashboard->request == 'OrdersVoucher') {
						$key = $obj_dashboard->request_key_table[$row['Order'][$obj_dashboard->request_filtre]];
					} else {
						if ($obj_dashboard->request == 'UserCreditHistoryAudiotel' || $obj_dashboard->request == 'UserCreditHistoryByNew') {
							$key = $obj_dashboard->request_key_table[$row['UserCreditHistory'][$obj_dashboard->request_filtre]];
						} else {

							if ($obj_dashboard->request == 'UserCreditHistorybyUser' || $obj_dashboard->request == 'User_FR' || $obj_dashboard->request == 'User_BE' || $obj_dashboard->request == 'User_CH' || $obj_dashboard->request == 'User_LU' || $obj_dashboard->request == 'User_CA' || $obj_dashboard->request == 'UserCreditHistorybyUser_FR' || $obj_dashboard->request == 'UserCreditHistorybyUser_BE' || $obj_dashboard->request == 'UserCreditHistorybyUser_CA' || $obj_dashboard->request == 'UserCreditHistorybyUser_CH' || $obj_dashboard->request == 'UserCreditHistorybyUser_LU') {
								$key = $obj_dashboard->request_key_table[$row['User'][$obj_dashboard->request_filtre]];
							} else {
								if (is_array($obj_dashboard->request_key_table))
									$key = $obj_dashboard->request_key_table[$row[$obj_dashboard->request][$obj_dashboard->request_filtre]];
								else
									$key = $row[$obj_dashboard->request][$obj_dashboard->request_filtre];
							}
						}
					}
					if (!$key) $key = 'Autre';
					if (!is_array($dashboards[$obj_dashboard->statistique][$timing][$key])) $dashboards[$obj_dashboard->statistique][$timing][$key] = array();

					if (!isset($dashboards[$obj_dashboard->statistique][$timing][$key][$row[0][$request_key_stat]]))
						$dashboards[$obj_dashboard->statistique][$timing][$key][$row[0][$$request_key_stat]] = 0;

					/*$date_check = new DateTime($timing_tranche['min']);
					$date_now = new DateTime('2019-02-05 00:00:00');
					$date_end = new DateTime('2019-02-06 00:00:00');
					if($request_key_stat == 'hour' && $date_check->getTimestamp() >= $date_end->getTimestamp()){
						$nn = $row[0][$request_key_stat] + 1; //UTC Europe/Paris
						if($nn == 24)$nn = 0;
						$row[0][$request_key_stat] = $nn;
					}
					if($request_key_stat == 'hour' && $date_check->getTimestamp() >= $date_now->getTimestamp() && $date_check->getTimestamp() <= $date_now->getTimestamp() && $row[0][$request_key_stat] >= 10){
						$nn = $row[0][$request_key_stat] + 1; //UTC Europe/Paris
						if($nn == 24)$nn = 0;
						$row[0][$request_key_stat] = $nn;
					}
*/
					/*if($request_key_stat == 'hour'){
						$nn = $row[0][$request_key_stat] + 1; //UTC Europe/Paris
						if($nn == 24)$nn = 0;
						$row[0][$request_key_stat] = $nn;
					}*/
					$dashboards[$obj_dashboard->statistique][$timing][$key][$row[0][$request_key_stat]] += $row[0]['nb'];
				}
			}
		}

		return $dashboards;
	}

	public function admin_dashboard()
	{
		set_time_limit(0);
		ini_set("memory_limit", -1);

		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Nouveaux clients par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);


		//Nouveaux clients source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);


		//Credits consommés par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_customer()
	{
		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Nouveaux clients (achats)
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_buy';
		$dashboard_construct_obj->request = 'Order';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'country';
		$dashboard_construct_obj->request_key_table = $stats_country;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients (consults)
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_consult';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_source';
		$dashboard_construct_obj->request = 'User';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_pays';
		$dashboard_construct_obj->request = 'User';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients fr par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_fr_source';
		$dashboard_construct_obj->request = 'User_FR';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients fr par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_fr_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser_FR';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients be par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_be_source';
		$dashboard_construct_obj->request = 'User_BE';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients be par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_be_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser_BE';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients ch par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_ch_source';
		$dashboard_construct_obj->request = 'User_CH';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients ch par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_ch_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser_CH';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients lu par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_lu_source';
		$dashboard_construct_obj->request = 'User_LU';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients lu par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_lu_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser_LU';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//inscription clients ca par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_subscribe_customer_ca_source';
		$dashboard_construct_obj->request = 'User_CA';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients ca par source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_ca_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser_CA';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);


		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_tunnel()
	{
		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Paniers
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_carts';
		$dashboard_construct_obj->request = 'CartLoose';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'status';
		$dashboard_construct_obj->request_key_table = $stats_cart;
		array_push($dashboard_construct, $dashboard_construct_obj);


		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_credit()
	{
		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();
		$stats_forfait   = $this->dashboard_stats_forfait();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Credit acheté
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_buy';
		$dashboard_construct_obj->request = 'Order';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'country';
		$dashboard_construct_obj->request_key_table = $stats_country;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Forfait acheté
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_forfait';
		$dashboard_construct_obj->request = 'Order';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'product_id';
		$dashboard_construct_obj->request_key_table = $stats_forfait;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés par type
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_buy_type';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'type_pay';
		$dashboard_construct_obj->request_key_table = $stats_type;
		array_push($dashboard_construct, $dashboard_construct_obj);



		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_consult()
	{
		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();
		$stats_new   = $this->dashboard_stats_new();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Credits consommés
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);



		//Credits consommés source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);



		//Credits consommés par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_country_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations par type
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_type_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'type_pay';
		$dashboard_construct_obj->request_key_table = $stats_type;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits audiotel consommés par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_audiotel_domain';
		$dashboard_construct_obj->request = 'UserCreditHistoryAudiotel';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés par new
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_new';
		$dashboard_construct_obj->request = 'UserCreditHistoryByNew';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'is_new';
		$dashboard_construct_obj->request_key_table = $stats_new;
		array_push($dashboard_construct, $dashboard_construct_obj);

		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_crm()
	{
		$stats_crm = $this->dashboard_stats_crm();
		$stats_crmvoucher = $this->dashboard_stats_crmvoucher();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Crm envoi
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_crmsend';
		$dashboard_construct_obj->request = 'CrmStat';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'crm';
		$dashboard_construct_obj->request_key_table = $stats_crm;
		array_push($dashboard_construct, $dashboard_construct_obj);



		//Nouveau client
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_crmcustomer';
		$dashboard_construct_obj->request = 'OrdersVoucher';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'voucher_name';
		$dashboard_construct_obj->request_key_table = $stats_crmvoucher;
		array_push($dashboard_construct, $dashboard_construct_obj);



		//Crm ouverture
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_crmopen';
		$dashboard_construct_obj->request = 'CrmStat';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'open';
		$dashboard_construct_obj->request_key_table = $stats_crm;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Crm clic
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_crmclic';
		$dashboard_construct_obj->request = 'CrmStat';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'clic';
		$dashboard_construct_obj->request_key_table = $stats_crm;
		array_push($dashboard_construct, $dashboard_construct_obj);



		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_dashboard_sms()
	{
		$stats_sms = $this->dashboard_stats_sms();
		$dashboard_timing = $this->dashboard_timing_static();
		$dashboard_construct = array();

		//Sms envoi FR
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_smssend';
		$dashboard_construct_obj->request = 'SmsHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'type';
		$dashboard_construct_obj->request_key_table = $stats_sms;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Sms envoi Worl
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_smssendworld';
		$dashboard_construct_obj->request = 'SmsHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'CountWorld';
		$dashboard_construct_obj->request_filtre = 'type';
		$dashboard_construct_obj->request_key_table = $stats_sms;
		array_push($dashboard_construct, $dashboard_construct_obj);


		//Sms Cost
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_smscost';
		$dashboard_construct_obj->request = 'SmsHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Cost';
		$dashboard_construct_obj->request_filtre = 'type';
		$dashboard_construct_obj->request_key_table = $stats_sms;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Sms Cost world
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_smscostworld';
		$dashboard_construct_obj->request = 'SmsHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'CostWorld';
		$dashboard_construct_obj->request_filtre = 'type';
		$dashboard_construct_obj->request_key_table = $stats_sms;
		array_push($dashboard_construct, $dashboard_construct_obj);


		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);
		$this->set(compact('dashboards'));
	}

	public function admin_stats()
	{
		$stats_country = $this->dashboard_stats_country();
		$stats_domain = $this->dashboard_stats_domain();
		$stats_type   = $this->dashboard_stats_type();
		$stats_source = $this->dashboard_stats_source();
		$stats_cart   = $this->dashboard_stats_cart();

		$dashboard_timing = $this->dashboard_timing_static();
		$listing_utcdec = Configure::read('Site.utcDec');

		$dx_max = new DateTime(date('Y-m-d 23:59:59'));
		$dx_min = new DateTime(date('Y-m-d 00:00:00'));
		$dx_min->modify('- 30 day');
		$dx_max->modify('-' . $listing_utcdec[$dx_max->format('md')] . ' hour');
		$dx_min->modify('-' . $listing_utcdec[$dx_min->format('md')] . ' hour');
		$dashboard_timing['custom']['min'] = $dx_min->format('Y-m-d H:i:s');
		$dashboard_timing['custom']['max'] = $dx_max->format('Y-m-d H:i:s');
		$days  = $dx_max->diff($dx_min)->format('%a');

		$label_custom = array();
		$label_max = '';
		if ($this->Session->check('Date')) {

			$is_date_min = CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00');
			$is_date_max = CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59');

			$dx_max = new DateTime($is_date_max);
			$dx_min = new DateTime($is_date_min);
			$dx_max->modify('-' . $listing_utcdec[$dx_max->format('md')] . ' hour');
			$dx_min->modify('-' . $listing_utcdec[$dx_min->format('md')] . ' hour');
			$dashboard_timing['custom']['min'] = $dx_min->format('Y-m-d H:i:s');
			$dashboard_timing['custom']['max'] = $dx_max->format('Y-m-d H:i:s');
			$days  = $dx_max->diff($dx_min)->format('%a');
			$index_min = $dx_min->format('Y-m-d');
			$index_max = $dx_max->format('Y-m-d');

			$label_custom = $this->date_range($index_min, $index_max, '+1 day', 'Ymd');
			$label_max = $dx_max->format('Ymd');
		} else {
			$days  = $dx_max->diff($dx_min)->format('%a');

			$index_min = $dx_min->format('Y-m-d');
			$index_max = $dx_max->format('Y-m-d');

			$label_custom = $this->date_range($index_min, $index_max, '+1 day', 'Ymd');
			$label_max = $dx_max->format('Ymd');
		}

		$dashboard_construct = array();

		//Nouveaux clients (achats)
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_buy';
		$dashboard_construct_obj->request = 'Order';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'country';
		$dashboard_construct_obj->request_key_table = $stats_country;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credit acheté
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_buy';
		$dashboard_construct_obj->request = 'Order';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'country';
		$dashboard_construct_obj->request_key_table = $stats_country;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients (consults)
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_consult';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés source
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_source';
		$dashboard_construct_obj->request = 'UserCreditHistorybyUser';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'source';
		$dashboard_construct_obj->request_key_table = $stats_source;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Nouveaux clients par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_new_customer_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 1;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_country';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'media';
		$dashboard_construct_obj->request_key_table = '';
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations par pays
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_country_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'domain';
		$dashboard_construct_obj->request_key_table = $stats_domain;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Consultations par type
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_consult_type_nb';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'type_pay';
		$dashboard_construct_obj->request_key_table = $stats_type;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Credits consommés par type
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_buy_type';
		$dashboard_construct_obj->request = 'UserCreditHistory';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Sum';
		$dashboard_construct_obj->request_filtre = 'type_pay';
		$dashboard_construct_obj->request_key_table = $stats_type;
		array_push($dashboard_construct, $dashboard_construct_obj);

		//Paniers
		$dashboard_construct_obj = new stdClass();
		$dashboard_construct_obj->statistique = 'canvas_carts';
		$dashboard_construct_obj->request = 'CartLoose';
		$dashboard_construct_obj->request_new = 0;
		$dashboard_construct_obj->request_type = 'Count';
		$dashboard_construct_obj->request_filtre = 'status';
		$dashboard_construct_obj->request_key_table = $stats_cart;
		array_push($dashboard_construct, $dashboard_construct_obj);

		$dashboards = $this->admin_dashboard_calc($dashboard_construct, $dashboard_timing);

		$this->set(compact('dashboards', 'label_max', 'label_custom'));
	}
	public function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y')
	{

		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while ($current <= $last) {

			$dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}

	public function admin_watchtrdv()
	{

		$this->loadModel('CustomerAppointment');

		if (isset($this->request->data['Admin'])) {

			if ($this->request->data['Admin']['content']) {
				if ($this->request->data['AdminRDVid']) {
					$appoint_id = $this->request->data['AdminRDVid'];
					$rdv = $this->CustomerAppointment->find('first', array(
						'conditions' => array('CustomerAppointment.id' => $this->request->data['AdminRDVid']),
						'recursive' => -1
					));

					//Le pseudo du client
					$user_firstname = $this->User->field('firstname', array('id' => $rdv['CustomerAppointment']['user_id']));
					$user_email = $this->User->field('email', array('id' => $rdv['CustomerAppointment']['user_id']));
					$user_domain_id = $this->User->field('domain_id', array('User.id' => $rdv['CustomerAppointment']['user_id']));

					//Le pseudo de l'agent
					$agent_pseudo = $this->User->field('pseudo', array('id' => $rdv['CustomerAppointment']['agent_id']));
					$agent_number = $this->User->field('agent_number', array('id' => $rdv['CustomerAppointment']['agent_id']));


					//envoi email
					$this->loadModel('Lang');
					$this->User->id = $requestData['CustomerAppointment']['user_id'];
					$user_lang_id = $this->User->field('lang_id');
					$this->Lang->id = $user_lang_id;
					$locale = $this->Lang->field('lc_time');
					if (empty($locale)) $locale = 'fr_FR.utf8';
					setlocale(LC_ALL, $locale);

					$this->loadModel('Domain');
					$conditions = array(
						'Domain.id' => $user_domain_id
					);
					$domain = $this->Domain->find('first', array('conditions' => $conditions));
					if (!isset($domain['Domain']['domain'])) $domain['Domain']['domain'] = 'https://fr.spiriteo.com';
					$conditions = array(
						'Lang.id_lang' => $user_lang_id
					);
					$lang = $this->Lang->find('first', array('conditions' => $conditions));

					$url_expert = 'https://' . $domain['Domain']['domain'] . '/' . $lang['Lang']['language_code'] . '/agents/' . strtolower($agent_pseudo) . '-' . $agent_number;

					$dateAppoint = $this->CustomerAppointment->field('A', array('CustomerAppointment.id' => $appoint_id)) . '-' . $this->CustomerAppointment->field('M', array('CustomerAppointment.id' => $appoint_id)) . '-' .
						$this->CustomerAppointment->field('J', array('CustomerAppointment.id' => $appoint_id)) . ' ' .
						str_pad($this->CustomerAppointment->field('H', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT) . ':' .
						str_pad($this->CustomerAppointment->field('Min', array('CustomerAppointment.id' => $appoint_id)), 2, '0', STR_PAD_LEFT) . ':00';

					$rdv = CakeTime::format($date, '%d %B à %Hh%M');

					$this->sendCmsTemplateByMail(355, $user_lang_id, $user_email, array(
						'PARAM_CLIENT' => $user_firstname,
						'PARAM_PSEUDO' => $agent_pseudo,
						'PARAM_RENDEZVOUS' => $rdv,
						'PAGE_EXPERT' => $url_expert,
						'PARAM_REPONSE' => nl2br($this->request->data['Admin']['content'])
					));
					$this->CustomerAppointment->delete($appoint_id, false);
				}
			}
		}


		/*if(isset($this->request->data['Admin'])){
			if($this->request->data['Admin']['texte'])
				$this->Session->write('MessageTexte', $this->params->data['Admin']['texte']);
			else
				$this->Session->write('MessageTexte', '');
			if($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
			if($this->request->data['Admin']['content'])
				$this->Session->write('MessageContent', $this->params->data['Admin']['content']);
			else
				$this->Session->write('MessageContent', '');

		}*/


		//on filtre le contenu
		$this->loadModel('FiltreMessage');

		$filtres = $this->FiltreMessage->find("all", array(
			'conditions' => array()
		));

		$this->Paginator->settings = array(
			'fields' => array('CustomerAppointment.*,Agent.id,Agent.agent_number,Agent.pseudo,User.firstname,User.id'),
			'conditions' => array(),
			'recursive' => 1,
			'order' => 'CustomerAppointment.id DESC',

			'paramType' => 'querystring',
			'limit' => Configure::read('Site.limitMessagePage')
		);


		$appointments = $this->Paginator->paginate($this->CustomerAppointment);

		$this->set(compact('appointments', 'filtres'));
	}


	public function admin_contact_expert()
	{

		if (isset($this->request->data['ContactExperts'])) {
			$this->loadModel('User');
			$listing_expert = '';
			if ($this->request->data['ContactExperts'])
				$listing_expert = explode(',', $this->request->data['ContactExperts']);
			if (is_array($listing_expert) && count($listing_expert)) {
				if (isset($this->request->data['ContactExpertSms'])) { //SMS
					//On charge l'API
					App::import('Vendor', 'Noox/Api');
					//On charge le model
					$this->loadModel('SmsHistory');


					foreach ($listing_expert as $expert) {
						$agent = $this->User->find('first', array(
							'fields' => array('User.phone_number', 'User.phone_mobile', 'User.pseudo'),
							'conditions' => array('User.id' => $expert, 'User.role' => 'agent', 'User.deleted' => 0, 'User.active' => 1),
							'recursive' => -1
						));

						$txt = $this->request->data['ContactExpertSms'];

						if (count($agent)) {
							$numero = $agent['User']['phone_mobile'];

							if ($numero) {
								$txtLength = strlen($txt);
								$api = new Api();
								$result = $api->sendSms($numero, base64_encode($txt));

								$history = array(
									'id_agent'          => $expert,
									'id_client'         => '',
									'id_tchat'         => '',
									'id_message'         => '',
									'email'             => 'SMS',
									'phone_number'      => $numero,
									'content_length'    => $txtLength,
									'content'    		=> $txt,
									'send'              => ($result > 0) ? 1 : 0,
									'date_add'          => date('Y-m-d H:i:s'),
									'type'				=> 'ADMIN CONTACT',
									'cost'				=> $result
								);

								//On save dans l'historique
								$this->SmsHistory->create();
								$this->SmsHistory->save($history);
								sleep(1);
							}
						}
					}
					$this->Session->setFlash(__('SMS envoyés.'), 'flash_success');
				}
				if (isset($this->request->data['ContactExpertMail'])) { //MEssage privé

					$this->loadModel('Message');

					foreach ($listing_expert as $expert) {

						$this->Message->create();
						if ($this->Message->save(array(
							'from_id' => 1,
							'to_id' => $expert,
							'content' => nl2br($this->request->data['ContactExpertMail']),
							'private' => 1,
							'admin_read_flag' => 1
						))) {
							$this->User->id = $expert;
							$client = $this->User->read();
							$this->sendCmsTemplateByMail(359, $client['User']['lang_id'], $client['User']['email'], array(
								'PSEUDO' => $client['User']['pseudo'],
								'TEXTE' => nl2br($this->request->data['ContactExpertMail']),
							));
						}
					}
					$this->Session->setFlash(__('Emails envoyés.'), 'flash_success');
				}
			} else {
				$this->Session->setFlash(__('Merci de choisir au moins un expert.'), 'flash_error');
			}
		}

		$agents = $this->User->find('all', array(
			'conditions'    => array('User.role' => 'agent', 'User.active' => 1, 'User.valid' => 1,  'User.deleted' => 0),
			'recursive'     => -1,
			'order'			=> array('User.pseudo ASC')
		));

		$this->set(compact('agents'));
	}

	public function getListingAgentsComm()
	{
		if ($this->request->is('ajax')) {
			$this->loadModel('User');

			$conditions = array('User.role' => 'agent', 'User.active' => 1, 'User.valid' => 1,  'User.deleted' => 0);

			$agent_status = '';
			if ($this->request->data['dispo'] && $this->request->data['indispo'] && $this->request->data['consult']) {
				$agent_status = '';
			}
			if (!$this->request->data['dispo'] && $this->request->data['indispo'] && $this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status !=' => 'available'
				));
			}
			if ($this->request->data['dispo'] && !$this->request->data['indispo'] && $this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status !=' => 'unavailable'
				));
			}
			if ($this->request->data['dispo'] && $this->request->data['indispo'] && !$this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status !=' => 'busy'
				));
			}
			if (!$this->request->data['dispo'] && !$this->request->data['indispo'] && $this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status' => 'busy'
				));
			}
			if (!$this->request->data['dispo'] && $this->request->data['indispo'] && !$this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status' => 'unavailable'
				));
			}
			if ($this->request->data['dispo'] && !$this->request->data['indispo'] && !$this->request->data['consult']) {
				$conditions = array_merge($conditions, array(
					'User.agent_status' => 'available'
				));
			}
			if ($this->request->data['tel'] && !$this->request->data['chat'] && !$this->request->data['mail']) {
				$conditions = array_merge($conditions, array(
					'User.consult_phone' => '1',
					array('OR' => array('User.consult_chat' => '0', '(UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) >' => '60')),
					'User.consult_email' => '0'
				));
			}
			if ($this->request->data['chat'] && !$this->request->data['tel'] && !$this->request->data['mail']) {
				if ($this->request->data['indispo']) {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '0',
						'User.consult_email' => '0',
					));
				} else {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '0',
						'User.consult_email' => '0',
						'(UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <=' => '60'
					));
				}
			}
			if ($this->request->data['mail'] && !$this->request->data['chat'] && !$this->request->data['tel']) {
				$conditions = array_merge($conditions, array(
					'User.consult_email' => '1',
					'User.consult_chat' => '0',
					'User.consult_phone' => '0',
				));
			}

			if ($this->request->data['tel'] && $this->request->data['mail'] && !$this->request->data['chat']) {
				$conditions = array_merge($conditions, array(
					'User.consult_phone' => '1',
					'User.consult_email' => '1',
					'User.consult_chat' => '0'
				));
			}

			if ($this->request->data['tel'] && $this->request->data['chat'] && !$this->request->data['mail']) {
				if ($this->request->data['indispo']) {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '1',
						'User.consult_email' => '0',
					));
				} else {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '1',
						'User.consult_email' => '0',
						'(UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <=' => '60'
					));
				}
			}

			if ($this->request->data['mail'] && $this->request->data['chat'] && !$this->request->data['tel']) {
				if ($this->request->data['indispo']) {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '0',
						'User.consult_email' => '1',
					));
				} else {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '0',
						'User.consult_email' => '1',
						'(UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <=' => '60'
					));
				}
			}

			if ($this->request->data['mail'] && $this->request->data['chat'] && $this->request->data['tel']) {
				if ($this->request->data['indispo']) {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '1',
						'User.consult_email' => '1',
					));
				} else {
					$conditions = array_merge($conditions, array(
						'User.consult_chat' => '1',
						'User.consult_phone' => '1',
						'User.consult_email' => '1',
						'(UNIX_TIMESTAMP(now()) - (unix_timestamp(date_last_activity))) <=' => '60'
					));
				}
			}
			//var_dump($conditions);
			$agents = $this->User->find('all', array(
				'conditions'    => $conditions,
				'recursive'     => -1,
				'order'			=> array('User.pseudo ASC')
			));
			$html = '';
			foreach ($agents as $agent) {
				$html .= '<tr>';
				$html .= '<td><input type="checkbox" id="checkexpert" class="checkboxexpertcontact" rel="' . $agent['User']['id'] . '"></td>';
				$html .= '<td>' . $agent['User']['id'] . '</td>';
				$html .= '<td>' . $agent['User']['pseudo'] . '</td>';
				$html .= '<td>' . $agent['User']['agent_number'] . '</td>';
				$html .= '<td>' . $agent['User']['email'] . '</td>';
				$html .= '<td>' . $agent['User']['phone_mobile'] . '</td>';
				$html .= '</tr>';
			}

			$this->jsonRender(array('return' => true, 'html' => $html));
		}
	}

	public function getCommunicationData()
	{
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			$params = $this->request->params;
			$id = $params['pass'][0];
			$this->loadModel('UserCreditHistory');
			$comm = $this->UserCreditHistory->find('first', array(
				'conditions'    => array('user_credit_history' => $id),
				'recursive'     => -1
			));

			$titre = $comm['UserCreditHistory']['media'] . ' ' . $comm['UserCreditHistory']['sessionid'];
			$data = '';
			switch ($comm['UserCreditHistory']['media']) {
				case 'phone':
					/* $paths = glob(Configure::read('Site.pathRecord').'/*.wav');
					foreach($paths as $path){
						//Le nom du fichier
						$filename = basename($path);
						if(substr_count($filename,$comm['UserCreditHistory']['sessionid'])){
							$data = '<audio controls="true" preload="none"><source src="/media/records/'.$filename.'" type="audio/wav"></audio>';
						}
					}*/
					$this->loadModel('Record');
					$record = $this->Record->find('first', array(
						'conditions' => array('sessionid' => $comm['UserCreditHistory']['sessionid'])
					));
					if ($record && !$record['Record']['deleted']) {
						if ($record['Record']['archive'])
							$data = '<audio controls="true" preload="none"><source src="/media/records_archive/' . $record['Record']['filename'] . '" type="audio/wav"></audio>';
						else
							$data = '<audio controls="true" preload="none"><source src="/media/records/' . $record['Record']['filename'] . '" type="audio/wav"></audio>';
					}
					break;
				case 'chat':
					$this->loadModel('Chat');
					$this->loadModel('ChatMessage');
					$dd = $this->Chat->find('first', array(
						'fields' => array('Chat.*', 'User.*', 'Agent.*'),
						'conditions'    => array('Chat.id' => $comm['UserCreditHistory']['sessionid']),
						'recursive'     => -1,
						'joins' => array(
							array(
								'table' => 'users',
								'alias' => 'User',
								'type' => 'left',
								'conditions' => array('User.id = Chat.from_id')
							),
							array(
								'table' => 'users',
								'alias' => 'Agent',
								'type' => 'left',
								'conditions' => array('Agent.id = Chat.to_id')
							),
						),
					));
					$ddm = $this->ChatMessage->find('all', array(
						'conditions'    => array('ChatMessage.chat_id' => $dd['Chat']['id']),
						'recursive'     => -1,

					));
					$message = '';
					$user_id = $dd['User']['id'];
					$agent_id = $dd['Agent']['id'];
					foreach ($ddm as $mes) {
						$mes = $mes["ChatMessage"];
						$user_name = '';
						if ($user_id == $mes["user_id"]) {
							$user_name = $dd['User']['firstname'];
						}
						if ($agent_id == $mes["user_id"]) {
							$user_name = $dd['Agent']['pseudo'];
						}

						$message .= CakeTime::format($mes['date_add'], '%d/%m/%y %Hh%Mmin%Ss') . ' <b>' . $user_name . '</b> -> ' . $mes["content"] . '<br />';
					}
					$data = $message;
					break;
				case 'email':
					$this->loadModel('Message');
					$data = '';
					$dd = $this->Message->find('first', array(
						'conditions'    => array(
							array('id' => $comm['UserCreditHistory']['sessionid']),
						),
						'recursive'     => -1,
						'order' => 'id asc'
					));

					if ($dd && !$dd['Message']['parent_id']) $data .=  ' <b>' . CakeTime::format($dd['Message']['date_add'], '%d/%m/%y %Hh%Mmin%Ss') . '</b><br />' . nl2br($dd['Message']['content']) . '<br /><br />';


					if ($dd &&  $dd['Message']['parent_id']) {
						$dd = $this->Message->find('all', array(
							'conditions'    => array(
								array('parent_id' => $dd['Message']['parent_id'])
							),
							'recursive'     => -1,
							'order' => 'id asc'
						));

						foreach ($dd as $mes) {
							$data .=  ' <b>' . CakeTime::format($mes['Message']['date_add'], '%d/%m/%y %Hh%Mmin%Ss') . '</b><br />' . nl2br($mes['Message']['content']) . '<br /><br />';
						}
					}
					break;
			}


			$this->set(array('title' => $titre, 'data' => $data));
			$this->render('/Admins/admin_get_communication');

			/*$html = '<div class="modal fade modal-footer-hide" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							  <h4 class="m-title" id="myModalLabel">
								 '.$titre.'
							  </h4>
						  </div>
						  <div class="modal-body">'.$data.'</div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">FERMER</button>
						  </div>
						</div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->';
			echo $html;*/
		}
	}

	public function save_declarer_incident()
	{
		if ($this->request->is('ajax')) {
			if (isset($this->request->data['message']) && isset($this->request->data['id_order'])) {

				$dbb_patch = new DATABASE_CONFIG();
				$dbb_connect = $dbb_patch->default;
				$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
				$mysqli_connect->query("UPDATE order_paypaltransactions set comments = '" . addslashes(utf8_decode($this->request->data['message'])) . "' WHERE order_id = '{$this->request->data['id_order']}'");
			}
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function save_declarer_incident_email()
	{
		if ($this->request->is('ajax')) {
			if (isset($this->request->data['email']) && isset($this->request->data['id_order'])) {

				$dbb_patch = new DATABASE_CONFIG();
				$dbb_connect = $dbb_patch->default;
				$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
				$mysqli_connect->query("UPDATE order_paypaltransactions set account_email = '" . addslashes(utf8_decode($this->request->data['email'])) . "' WHERE order_id = '{$this->request->data['id_order']}'");
			}
			$this->jsonRender(array(
				'return'          => true,
			));
		}
	}

	public function admin_watchlostmails()
	{
		$this->loadModel('Message');
		$this->loadModel('UserPenality');
		$conditions = array('UserPenality.message_id >' => 0, 'UserPenality.is_factured' => 1);


		$this->Paginator->settings = array(
			'fields' => array(
				'Message.id', 'Message.private', 'Message.content', 'Message.attachment', 'Message.attachment2', 'Message.etat', 'Message.archive', 'From.firstname', 'From.id', 'From.pseudo', 'From.lastname', 'From.email', 'To.id', 'To.firstname', 'To.lastname', 'To.email', 'To.pseudo', 'To.role', 'From.role',
				'Message.date_add', 'Message.private', 'UserPenality.date_add'
			),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'messages',
					'alias' => 'Message',
					'type' => 'left',
					'conditions' => array(
						'Message.id = UserPenality.message_id',
					)
				),
				array(
					'table' => 'users',
					'alias' => 'To',
					'type' => 'left',
					'conditions' => array(
						'To.id = Message.to_id',
					)
				),
				array(
					'table' => 'users',
					'alias' => 'From',
					'type' => 'left',
					'conditions' => array(
						'From.id = Message.from_id',
					)
				)
			),
			'recursive' => 1,
			'order' => 'UserPenality.date_add DESC',
			'paramType' => 'querystring',
			'limit' => 50
		);


		$messages = $this->Paginator->paginate($this->UserPenality);

		$this->set(compact('messages'));
	}

	public function admin_com()
	{
		$this->loadModel('UserCreditHistory');
		$this->loadModel('UserPay');

		//On complète la sous-requete avec le champ de la requete principale
		$subQuery = '1=1 '; //'UserCreditHistory.user_credit_history IN (' . $subQuery . ')';
		//Avons-nous une recharche par date ??
		if ($this->Session->check('Date')) {
			$subQuery .= ' AND UserCreditHistory.date_start >= "' . CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00') . '"';
			$subQuery .= ' AND UserCreditHistory.date_start <= "' . CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59') . '"';
		} else {
			$subQuery .= ' AND UserCreditHistory.date_start >= "' . date('Y-m-d 00:00:00') . '"';
			$subQuery .= ' AND UserCreditHistory.date_start <= "' . date('Y-m-d 23:59:59') . '"';
		}
		//Avons-nous une recharche par media ??
		if ($this->Session->check('Media')) {
			$subQuery .= ' AND UserCreditHistory.media = "' . $this->Session->read('Media.value') . '"';
		}

		//$subQueryExpression = $db->expression($subQuery);
		//Retourne un object avec l'expression complète en sql de la sous-requete
		$conditions[] = $subQuery; //$subQueryExpression;

		$this->Paginator->settings = array(
			'fields' => array('UserCreditHistory.*', 'User.firstname', 'User.lastname'),
			'conditions' => $conditions,
			'order' => 'UserCreditHistory.date_start desc',
			'paramType' => 'querystring',
			'limit' => 15
		);

		$lastCom = $this->Paginator->paginate($this->UserCreditHistory);
		$this->loadModel('UserCreditPrice');
		//recup les achats
		foreach ($lastCom as &$last) {
			$order = '';


			$user_credit_list =  unserialize($last['UserCreditHistory']['ca_ids']); //  explode('_',$last['UserCreditHistory']['ca_ids']);
			if (is_array($user_credit_list)) {
				foreach ($user_credit_list as $user_credit) {
					$lastCredits = $this->UserCreditPrice->find('first', array(
						'fields'        => array('UserCreditPrice.*', 'Order.*'),
						'conditions'    => array('UserCreditPrice.id' => $user_credit['id']),
						'joins' => array(
							array(
								'table' => 'user_credits',
								'alias' => 'UserCredit',
								'type' => 'left',
								'conditions' => array(
									'UserCredit.id = UserCreditPrice.id_user_credit'
								)
							),
							array(
								'table' => 'orders',
								'alias' => 'Order',
								'type' => 'left',
								'conditions' => array(
									'Order.id = UserCredit.order_id'
								)
							)
						),
						'recursive'     => 1
					));
					if ($lastCredits['Order']['total'] > 0)
						$order .= number_format($lastCredits['Order']['total'], 2) . ' ' . $lastCredits['Order']['currency'];
				}
			} else {
				$user_credit_list =  explode('_', $last['UserCreditHistory']['ca_ids']);
				foreach ($user_credit_list as $user_credit) {
					$lastCredits = $this->UserCreditPrice->find('first', array(
						'fields'        => array('UserCreditPrice.*', 'Order.*'),
						'conditions'    => array('UserCreditPrice.id' => $user_credit),
						'joins' => array(
							array(
								'table' => 'user_credits',
								'alias' => 'UserCredit',
								'type' => 'left',
								'conditions' => array(
									'UserCredit.id = UserCreditPrice.id_user_credit'
								)
							),
							array(
								'table' => 'orders',
								'alias' => 'Order',
								'type' => 'left',
								'conditions' => array(
									'Order.id = UserCredit.order_id'
								)
							)
						),
						'recursive'     => 1
					));
					if ($lastCredits['Order']['total'] > 0)
						$order .= number_format($lastCredits['Order']['total'], 2) . ' ' . $lastCredits['Order']['currency'];
				}
			}
			$user_pay = $this->UserPay->find('first', array(
				'fields'        => array('UserPay.price'),
				'conditions'    => array('UserPay.id_user_credit_history' => $last['UserCreditHistory']['user_credit_history']),
				'recursive'     => 1
			));
			$last['UserCreditHistory']['pay'] = $user_pay['UserPay']['price'];
			$last['UserCreditHistory']['order'] = $order;
			$last['UserCreditHistory']['ca_devise'] = $last['UserCreditHistory']['ca_currency'];
		}


		$this->set(compact('lastCom'));
	}

	public function admin_logs()
	{

		$this->loadModel('AdminLog');

		$parms = $this->params;
		$page = 1;
		if ($parms["page"]) $page = $parms["page"];

		$nbpage = 25;
		$limit = 1;
		if ($page > 1) $limit = $page * $nbpage;


		$conditions = array();

		if ($this->Session->check('Date')) {
			$conditions = array_merge($conditions, array(
				'AdminLog.date_add >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
				'AdminLog.date_add <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
			));
		}

		if (isset($this->request->data['Log'])) {
			if ($this->request->data['Log']['fullname'])
				$this->Session->write('LogName', $this->params->data['Log']['fullname']);
			else
				$this->Session->write('LogName', '');
		}

		if ($this->Session->read('LogName')) {
			$conditions = array_merge($conditions, array(
				'User.firstname LIKE' => '%' . $this->Session->read('LogName') . '%'
			));
			$this->set('filtre_name', $this->Session->read('LogName'));
		}


		$this->Paginator->settings = array(
			'fields' => array('AdminLog.*', 'User.firstname'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type'  => 'left',
					'conditions' => array('User.id = AdminLog.user_id')
				),
			),
			'order' => 'AdminLog.date_add desc',
			'recursive' => -1,
			'limit' => $nbpage,
			'page' => $page
		);

		$allLogs = $this->Paginator->paginate($this->AdminLog);

		$this->set(compact('allLogs'));
	}

	public function admin_newpassword()
	{
		$this->loadModel('User');
		$conditions = array('User.last_passwd_gen !=' => '0000-00-00 00:00:00', 'User.active' => 1, 'User.deleted' => 0, 'User.forgotten_password !=' => '');

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['mail'])
				$this->Session->write('MessageMail', $this->params->data['Admin']['mail']);
			else
				$this->Session->write('MessageMail', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('MessageClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('MessageClient', '');
		}

		if ($this->Session->read('MessageMail')) {
			$conditions = array('User.last_passwd_gen !=' => '0000-00-00 00:00:00', 'User.active' => 1, 'User.deleted' => 0, 'User.forgotten_password !=' => '', 'User.email' => $this->Session->read('MessageMail'));
			$this->set('filtre_mail', $this->Session->read('MessageMail'));
		}
		if ($this->Session->read('MessageClient')) {
			$conditions = array('User.last_passwd_gen !=' => '0000-00-00 00:00:00', 'User.active' => 1, 'User.deleted' => 0, 'User.forgotten_password !=' => '');
			$conditions['OR'] = array('User.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'User.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'User.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'User.firstname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'User.lastname LIKE' => '%' . $this->Session->read('MessageClient') . '%', 'User.pseudo LIKE' => '%' . $this->Session->read('MessageClient') . '%');
			$this->set('filtre_client', $this->Session->read('MessageClient'));
		}



		$this->Paginator->settings = array(
			'fields' => array('User.id', 'User.pseudo', 'User.firstname', 'User.lastname', 'User.email', 'User.last_passwd_gen', 'User.forgotten_password', 'User.role'),
			'conditions' => $conditions,
			'recursive' => 1,
			'order' => 'User.last_passwd_gen DESC',
			'paramType' => 'querystring',
			'limit' => 50
		);


		$users = $this->Paginator->paginate($this->User);

		$this->set(compact('users'));
	}
	public function admin_watchopposed()
	{

		$parms = $this->params;
		$page = 1;
		if ($parms["page"]) $page = $parms["page"];

		$nbpage = 25;
		$limit = 1;
		if ($page > 1) $limit = $page * $nbpage;

		$conditions = array('OR' => array('User.parent_account_opposed >' => 0, 'User.payment_opposed' => 1));

		if ($this->Session->check('Date')) {
			$conditions = array_merge($conditions, array(
				'User.date_blocked >=' => CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00'),
				'User.date_blocked <=' => CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:59')
			));
		}

		if (isset($this->request->data['Admin'])) {
			if ($this->request->data['Admin']['email'])
				$this->Session->write('OpposedEmail', $this->params->data['Admin']['email']);
			else
				$this->Session->write('OpposedEmail', '');
			if ($this->request->data['Admin']['client'])
				$this->Session->write('OpposedClient', $this->params->data['Admin']['client']);
			else
				$this->Session->write('OpposedClient', '');
			if ($this->request->data['Admin']['phone_number'])
				$this->Session->write('OpposedPhone', $this->params->data['Admin']['phone_number']);
			else
				$this->Session->write('OpposedPhone', '');
		}

		if ($this->Session->read('OpposedEmail')) {
			$conditions = array('User.email LIKE ' => '%' . $this->Session->read('OpposedEmail') . '%');
			$this->set('filtre_email', $this->Session->read('OpposedEmail'));
		}
		if ($this->Session->read('OpposedClient')) {
			$conditions['OR'] = array('User.firstname LIKE' => '%' . $this->Session->read('OpposedClient') . '%', 'User.lastname LIKE' => '%' . $this->Session->read('OpposedClient') . '%');
			$this->set('filtre_client', $this->Session->read('OpposedClient'));
		}
		if ($this->Session->read('OpposedPhone')) {
			$conditions = array('User.phone_number LIKE ' => '%' . $this->Session->read('OpposedPhone') . '%');
			$this->set('filtre_phone', $this->Session->read('OpposedPhone'));
		}

		$this->Paginator->settings = array(
			'fields' => array('User.id', 'User.firstname', 'User.email', 'User.phone_number', 'User.parent_account_opposed', 'User.active', 'User.payment_blocked', 'Parent.id', 'Parent.firstname', 'Parent.email', 'Parent.active', 'Parent.payment_blocked', 'User.date_blocked'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'Parent',
					'type'  => 'left',
					'conditions' => array('Parent.id = User.parent_account_opposed')
				)
			),
			'order' => 'User.date_blocked desc',
			'recursive' => -1,
			'limit' => $nbpage,
			'page' => $page
		);

		$allUsers = $this->Paginator->paginate($this->User);

		$this->set(compact('allUsers'));
	}

	public function admin_consults_agent()
	{

		$this->loadModel('UserCreditHistory');
		$this->loadModel('User');

		$conditions = array('User.active' => 1, 'User.role' => 'agent', 'User.deleted' => 0, 'User.valid' => 1);

		$cond_subquery = '';

		if ($this->Session->check('Date')) {

			$cond_subquery .= ' and date_start >= \'' . CakeTime::format($this->Session->read('Date.start'), '%Y-%m-%d 00:00:00') . '\'';
			$cond_subquery .= ' and date_start <= \'' . CakeTime::format($this->Session->read('Date.end'), '%Y-%m-%d 23:59:590') . '\'';
		}
		if ($this->Session->check('Media')) {

			$cond_subquery .= ' and media = \'' . $this->Session->read('Media.value') . '\'';
		}

		if ($this->Session->check('ConsultTotal') && !$this->Session->check('Media')) {
			$conditions = array_merge($conditions, array(
				'(select count(*) from user_credit_history where agent_id = User.id ' . $cond_subquery . ') <' => $this->Session->read('ConsultTotal.value')
			));
		}

		if ($this->Session->check('ConsultTotal') && $this->Session->check('Media')) {
			if ($this->Session->read('ConsultTotal.value') == 'email')
				$conditions = array_merge($conditions, array(
					'(select count(*) from user_credit_history where agent_id = User.id and media = \'email\' ' . $cond_subquery . ') <' => $this->Session->read('ConsultTotal.value')
				));
			if ($this->Session->read('ConsultTotal.value') == 'chat')
				$conditions = array_merge($conditions, array(
					'(select count(*) from user_credit_history where agent_id = User.id and media = \'chat\' ' . $cond_subquery . ') <' => $this->Session->read('ConsultTotal.value')
				));
			if ($this->Session->read('ConsultTotal.value') == 'phone')
				$conditions = array_merge($conditions, array(
					'(select count(*) from user_credit_history where agent_id = User.id and media = \'phone\' ' . $cond_subquery . ') <' => $this->Session->read('ConsultTotal.value')
				));
		}

		$orderby = 'total_consult ASC';

		$this->Paginator->settings = array(
			'fields' => array('User.id', 'User.pseudo', 'User.vat_num_status', '(select count(*) from user_credit_history where agent_id = User.id ' . $cond_subquery . ') as total_consult', '(select count(*) from user_credit_history where agent_id = User.id and media = \'email\' ' . $cond_subquery . ') as total_consult_email', '(select count(*) from user_credit_history where agent_id = User.id and media = \'chat\' ' . $cond_subquery . ') as total_consult_chat', '(select count(*) from user_credit_history where agent_id = User.id and media = \'phone\' ' . $cond_subquery . ') as total_consult_phone'),
			'conditions' => $conditions,
			'order' => $orderby,
			'paramType' => 'querystring',
			'limit' => 25
		);

		$lastCom = $this->Paginator->paginate($this->User);

		$this->set(compact('lastCom'));
	}

	public function admin_client_alerts()
	{
		$this->loadModel('Alert');

		$conditions = array();

		if (isset($this->request->data['Agent'])) {
			if ($this->request->data['Agent']['name'])
				$this->Session->write('AgentName', $this->params->data['Agent']['name']);
			else
				$this->Session->write('AgentName', '');
		}

		if (isset($this->request->data['Client'])) {
			if ($this->request->data['Client']['name'])
				$this->Session->write('ClientName', $this->params->data['Client']['name']);
			else
				$this->Session->write('ClientName', '');
		}

		if (isset($this->request->data['Alert'])) {
			if ($this->request->data['Alert']['email'])
				$this->Session->write('AlertEmail', $this->params->data['Alert']['email']);
			else
				$this->Session->write('AlertEmail', '');
		}

		$agentName = $this->Session->read('AgentName');
		if ($agentName) {
			$conditions[] = array('Agent.pseudo LIKE ' => "%$agentName%");
		}
		$this->set('agent_name', $this->Session->read('AgentName') ?: '');

		$clientName = $this->Session->read('ClientName');
		if ($clientName) {
			$conditions[] = array('User.firstname LIKE ' => "%$clientName%");
		}
		$this->set('client_name', $this->Session->read('ClientName') ?: '');

		$alertEmail = $this->Session->read('AlertEmail');
		if ($alertEmail) {
			$conditions[] = array('Alert.email LIKE ' => "%$alertEmail%");
		}
		$this->set('alert_email', $this->Session->read('AlertEmail') ?: '');

		$this->Paginator->settings = array(
			'fields' => array('Alert.*', 'User.firstname', 'User.lastname', 'User.id', 'Agent.pseudo', 'Agent.id'),
			'conditions' => array(
				'OR' => $conditions
			),
			'recursive' => 1,
			'paramType' => 'querystring',
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('User.id = Alert.users_id')
				),
				array(
					'table' => 'users',
					'alias' => 'Agent',
					'type' => 'left',
					'conditions' => array('Agent.id = Alert.agent_id')
				)
			),
			'limit' => 50
		);

		$alerts = $this->Paginator->paginate($this->Alert);

		$this->set(compact('alerts'));
	}
}

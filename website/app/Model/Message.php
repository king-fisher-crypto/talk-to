<?php
    App::uses('AppModel', 'Model');
    /**
     * Message Model
     *
     * @property User $User
     * @property Agent $Agent
     */
    class Message extends AppModel {

        /**
         * Validation rules
         *
         * @var array
         */
        public $validate = array(
            'from_id' => array(
                'numeric' => array(
                    'rule' => array('numeric'),
                    //'message' => 'Your custom message here',
                    //'allowEmpty' => false,
                    //'required' => false,
                    //'last' => false, // Stop validation after this rule
                    //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
            ),
            'to_id' => array(
                'numeric' => array(
                    'rule' => array('numeric'),
                    //'message' => 'Your custom message here',
                    //'allowEmpty' => false,
                    //'required' => false,
                    //'last' => false, // Stop validation after this rule
                    //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
            ),
        );

        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'From' => array(
                'className' => 'User',
                'foreignKey' => 'from_id',
                'conditions' => '',
                'fields' => array('From.*'),
                'order' => ''
            ),
            'To' => array(
                'className' => 'User',
                'foreignKey' => 'to_id',
                'conditions' => '',
                'fields' => array('To.*'),
                'order' => ''
            ),
            'Guest' => array(
                'className' => 'Guest',
                'foreignKey' => 'guest_id',
                'conditions' => '',
                'fields' => array('Guest.lastname', 'Guest.firstname', 'Guest.email'),
                'order' => ''
            )
        );

        public function getConditions($idUser, $admin = false, $archive = false, $mailPrivate = false){
            $firstConditions = array(
                'Message.deleted' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $idUser),
                    array('Message.to_id' => $idUser, 'Message.etat !=' => 2)
                )
            );

            //Conditions pour admin
            if($admin)
                $firstConditions['OR'] = array(
                    array('Message.from_id' => $idUser),
                    array('Message.to_id' => Configure::read('Admin.id')),
                    array('Message.to_id' => $idUser)
                );

            //Que les messages non archivés et non privées
            if(!$archive && !$mailPrivate)
                $firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 0));
            else{
                //Que les discussions archivées
                if($archive)
                    $firstConditions = array_merge($firstConditions, array('Message.archive' => 1));
                //Les discussions privés
                if($mailPrivate)
                    $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
            }

            return $firstConditions;
        }

        public function getDiscussion($idUser, $admin = false, $archive = false, $mailPrivate = false, $ip = false, $email = false, $nom = false){
			
			$request = Router::getRequest();
			
			$page = 0;
			if(isset($request->params->query['page']))
            	$page = $request->params->query['page']-1;
			
			$offset = Configure::read('Site.limitMessagePage');
			
            $firstConditions = array(
                'Message.deleted' => 0,
                'Message.parent_id' => null,
                'OR' => array(
                    array('Message.from_id' => $idUser),
                    array('Message.to_id' => $idUser, 'Message.etat !=' => 2)
                )
            );

            //Conditions pour admin
            if($admin){
                $firstConditions['OR'] = array(
                    array('Message.from_id' => $idUser),
                    array('Message.to_id' => Configure::read('Admin.id')),
                    array('Message.to_id' => $idUser)
                );
				if($ip){
					$firstConditions['Message.IP'] = $ip;	
					$firstConditions['Message.admin_read_flag'] = 1;	
				}
				if($email){
					$firstConditions['OR'] = array(
						array('Guest.email' => $email),
						array('From.email' => $email),
						array('To.email' => $email)
	                );
					$firstConditions['Message.admin_read_flag'] = 1;
				}
				if($nom){
					$firstConditions['OR'] = array(
						array('Guest.firstname' => $nom),
						array('From.firstname' => $nom),
						array('To.firstname' => $nom)
	                );	
					$firstConditions['Message.admin_read_flag'] = 1;
				}
			}

            //Que les messages non archivés et non privées
            if(!$archive && !$mailPrivate)
                $firstConditions = array_merge($firstConditions, array('Message.private' => 0, 'Message.archive' => 0));
            else{
                //Que les discussions archivées
                if($archive)
                    $firstConditions = array_merge($firstConditions, array('Message.archive !=' => 0));
                //Les discussions privés
                if($mailPrivate)
                    $firstConditions = array_merge($firstConditions, array('Message.private' => 1, 'Message.archive' => 0));
            }
			
            $data = $this->find('all',array(
                'conditions' => $firstConditions
				
            ));

            //Si aucune discussion
            if(empty($data))
                return array();
            //Les id des discussions
            $idMails = Hash::extract($data, '{n}.Message.id');

            //Création de la sous-requête
            $db = $this->getDataSource();
            //Initialisation des paramètres
            $subQuery = $db->buildStatement(
                array(
                    'fields'     => array('MAX(Message2.id)'),
                    'table'      => $db->fullTableName($this),
                    'alias'      => 'Message2',
                    'limit'      => null,
                    'offset'     => null,
                    'joins'      => array(),
                    'conditions' => array('Message2.parent_id' => $idMails),
                    'order'      => null,
                    'group'      => 'Message2.parent_id'
                ),
                $this
            );

            //On complète la sous-requete avec le champ de la requete principale
            $subQuery = "Message.id IN (" . $subQuery . ") AND Message.deleted = 0 AND Message.etat != 2";
            $subQueryExpression = $db->expression($subQuery);
            //Retourne un object avec l'expression complète en sql de la sous-requete
            $conditions[] = $subQueryExpression;
			
            $lastData = $this->find('all',array(
                'fields' => array('Message.date_add', 'Message.to_id', 'Message.etat', 'Message.parent_id', 'Message.content', 'Message.attachment','Message.admin_read_flag','Message.IP'),
                'conditions' => $conditions
            ));

            foreach($data as $key => $mail){
                //Permet de savoir s'il y a plus d'un message
                $flag = false;
                foreach($lastData as $key2 => $lastMail){
                    if($lastMail['Message']['parent_id'] == $mail['Message']['id']){
                        $data[$key]['LastMessage'] = array(
                            'date_add'      => $lastMail['Message']['date_add'],
                            'to_id'         => $lastMail['Message']['to_id'],
                            'attachment'    => $lastMail['Message']['attachment'],
                            'etat'          => $lastMail['Message']['etat'],
                            'content'       => $lastMail['Message']['content'],
                            'admin_read_flag'=> $lastMail['Message']['admin_read_flag'],
							'IP'=> $lastMail['Message']['IP']
                        );
                        $flag = true;
                        //On supprime l'element, pour accélerer la prochaine boucle
                        unset($lastData[$key2]);
                    }
                }
                //S'il y a un seul message dans la conversation
                if(!$flag){
                    $data[$key]['LastMessage'] = array(
                        'date_add'      => $mail['Message']['date_add'],
                        'to_id'         => $mail['Message']['to_id'],
                        'attachment'    => $mail['Message']['attachment'],
                        'etat'          => $mail['Message']['etat'],
                        'content'       => $mail['Message']['content'],
                        'admin_read_flag'=> $mail['Message']['admin_read_flag'],
						'IP'			 => $mail['Message']['IP']
                    );
                }
                //Tableau sur lequel un trie sera effectué
                $sortData[$key] = $data[$key]['LastMessage']['date_add'];
            }

            //On va trier le tableau (date_add desc)
            arsort($sortData);
            //Maintenant on trie dans le tableau data
            $finalData = array();
            foreach($sortData as $key => $date){
                $finalData[] = $data[$key];
            }

            return $finalData;
        }
        public function myDiscussion($idMail, $idUser){
            if(empty($idMail) || !is_numeric($idMail) || empty($idUser) || !is_numeric($idUser))
                return false;

            $data = $this->find('first', array(
                'conditions' => array('id' => $idMail, 'from_id' => $idUser, 'deleted' => 0),
                'recursive' => -1
            ));

            //Si pas de message
            if(empty($data))
                return false;

            return true;
        }

        /**
         * Permet de savoir le nombre de message non lu
         *
         * @param int   $id_user    L'id de l'utilisateur
         * @param bool  $private    Messages non lu, sur les messages privés ou pas
         * @return bool
         */
        public function hasNoReadMail($id_user, $private = false){
            if(empty($id_user) || !is_numeric($id_user))
                return false;

            //Les messages privés
            if($private){
                $count = $this->find('count', array(
                    'conditions'    => array('Message.to_id' => $id_user, 'Message.etat' => 0, 'Message.private' => 1),
                    'recursive'     => -1
                ));
            }else{
                //Les messages de consultations
                $count = $this->find('count', array(
                    'conditions'    => array('Message.to_id' => $id_user, 'Message.etat' => 0, 'Message.private' => 0, 'Message.archive' => 0),
                    'recursive'     => -1
                ));
            }

            return $count;
        }
    }
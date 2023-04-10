<?php
    App::uses('AppModel', 'Model');
    /**
     * ChatEvent Model
     *
     * @property ChatEvent $ChatEvent
     */
    class ChatEvent extends AppModel {

        /**
         * Validation rules
         *
         * @var array
         */
        public $validate = array(
            'chat_id' => array(
                'numeric' => array(
                    'rule' => array('numeric'),
                    //'message' => 'Your custom message here',
                    'allowEmpty' => false,
                    //'required' => false,
                    //'last' => false, // Stop validation after this rule
                    //'on' => 'create', // Limit validation to 'create' or 'update' operations
                ),
            )
        );

        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'Chat' => array(
                'className' => 'Chat',
                'foreignKey' => 'chat_id',
                'conditions' => '',
                'fields' => array(),
                'order' => ''
            )
        );

        public function beforeSave($options = array()){
            return true;
        }

        public function getLastDate($idChat){
            $lastData = $this->find('first', array(
                'fields' => array('id', 'user_id', 'status', 'writting', 'send', 'date_add', 'Chat.session_id', 'Chat.to_id', 'Chat.from_id'),
                'conditions' => array('ChatEvent.chat_id' => $idChat),
                'order' => 'ChatEvent.id desc',
                'recursive' => 0
            ));

            return $lastData;
        }

        public function firstMessage($idChat, $idCustomer){
            if(empty($idChat) || empty($idCustomer))
                return false;

            //La session
            $data = $this->find('count', array(
                'conditions' => array('user_id' => $idCustomer, 'send' => 1, 'chat_id' => $idChat),
                'recursive' => -1
            ));

            if($data > 0)
                return true;
            else
                return false;
        }
    }

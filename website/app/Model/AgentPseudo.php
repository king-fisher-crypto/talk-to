<?php
    App::uses('AppModel', 'Model');
    /**
     * AgentPseudo Model
     *
     * @property Agent $agent
     */
    class AgentPseudo extends AppModel {



        public $belongsTo = array(
            'User' => array(
                'className' => 'User',
                'foreignKey' => 'user_id',
                'conditions' => '',
                'fields' => array('User.id','User.pseudo'),
                'order' => ''
            )
        );

        //The Associations below have been created with all possible keys, those that are not needed can be removed

    }

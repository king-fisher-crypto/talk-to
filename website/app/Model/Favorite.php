<?php
App::uses('AppModel', 'Model');
/**
 * Favorite Model
 *
 * @property Favorite $favorite
 */
class Favorite extends AppModel {


    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Agent' => array(
            'className' => 'User',
            'foreignKey' => 'agent_id',
            'conditions' => '',
            'fields' => array('Agent.id','Agent.pseudo','Agent.agent_number', 'Agent.consult_email', 'Agent.consult_chat', 'Agent.date_last_activity', 'Agent.agent_status'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );

    public function beforeSave($options = array()){

        parent::beforeSave();
    }
}

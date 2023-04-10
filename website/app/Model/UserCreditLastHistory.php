<?php
App::uses('AppModel', 'Model');
/**
 * UserCreditLastHistory Model
 *
 * @property User $User
 * @property Agent $Agent
 */
class UserCreditLastHistory extends AppModel {

    /**
     * Primary key field
     *
     * @var string
     */
    public $primaryKey = 'user_credit_last_history';

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'users_id',
            'conditions' => '',
            'fields' => array('User.id', 'User.firstname'),
            'order' => ''
        ),
        'Agent' => array(
            'className' => 'Agent',
            'foreignKey' => 'agent_id',
            'conditions' => '',
            'fields' => array('Agent.agent_number','Agent.pseudo'),
            'order' => ''
        )
    );

    public function associatedAgent($idAgent){
        //Les 10 derniers clients de l'agent
        $customer = $this->find('list', array(
            'fields' => 'users_id',
            'conditions' => array('agent_id' => $idAgent),
            'order' => 'user_credit_last_history desc',
            'limit' => 4,
			'group' => 'users_id'
        ));

        //Les 3 derniers agents consultés par les clients de l'agent
        $agents = $this->find('all',array(
            'fields' => array('DISTINCT (agent_id)', 'Agent.agent_number', 'Agent.pseudo', 'Agent.agent_status','Agent.langs','Agent.consult_phone','Agent.id', 'Agent.consult_email','Agent.consult_chat','Agent.date_last_activity','Agent.reviews_avg'),
            'conditions' => array('users_id' => $customer, 'agent_id !=' => $idAgent, ),
            'order' => 'Agent.agent_status ASC,user_credit_last_history desc ',
			'limit' => 3
        ));

        return $agents;
    }
	
	public function duplicateLine($chat){
        
		$count = $this->find('count', array(
						'conditions' => array('agent_id' => $chat['Chat']['to_id'], 'media' => 'chat', 'users_id' => $chat['Chat']['from_id'], 'date_start' => $chat['Chat']['consult_date_start']),
						'recursive' => -1
					));

        return $count;
    }
}

<?php
App::uses('AppModel', 'Model');
/**
 * UserCreditHistory Model
 *
 * @property User $User
 * @property Agent $Agent
 */
class UserCreditHistory extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'user_credit_history';

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_credit_history';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'agent_id' => array(
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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Agent' => array(
			'className' => 'Agent',
			'foreignKey' => 'agent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    /**
     * Compte le nombre d'appels effectués pour un agent
     *
     * @param int   $idAgent    L'id de l'expert
     * @return bool
     */
    public function countPhoneCall($idAgent){
        if(empty($idAgent) && !is_numeric($idAgent))
            return false;

        $count = $this->find('count', array(
            'conditions' => array('agent_id' => $idAgent, 'media' => 'phone'),
            'recursive' => -1
        ));

        return $count;
    }
}

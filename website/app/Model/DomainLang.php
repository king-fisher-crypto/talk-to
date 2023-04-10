<?php
App::uses('AppModel', 'Model');
/**
 * DomainLang Model
 *
 * @property Domain $Domain
 * @property Lang $Lang
 */
class DomainLang extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'domain_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'lang_id' => array(
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
		'Domain' => array(
			'className' => 'Domain',
			'foreignKey' => 'domain_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Lang' => array(
			'className' => 'Lang',
			'foreignKey' => 'lang_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    
}

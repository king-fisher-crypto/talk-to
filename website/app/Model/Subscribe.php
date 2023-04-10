<?php
App::uses('AppModel', 'Model');
/**
 * Page Model
 *
 * @property PageLang $PageLang
 */
class Subscribe extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SubscribeLang' => array(
			'className' => 'SubscribeLang',
			'foreignKey' => 'subscribe_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}

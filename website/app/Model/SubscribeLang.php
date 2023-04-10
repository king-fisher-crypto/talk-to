<?php
App::uses('AppModel', 'Model');
/**
 * PageLang Model
 *
 * @property Page $Page
 * @property Lang $Lang
 */
class SubscribeLang extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	//public $primaryKey = 'page_id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Subscribe' => array(
			'className' => 'Subscribe',
			'foreignKey' => 'subscribe_id',
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

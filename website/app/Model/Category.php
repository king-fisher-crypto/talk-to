<?php
App::uses('AppModel', 'Model');
/**
 * Category Model
 *
 * @property CategoryLang $CategoryLang
 * @property CategoryUser $CategoryUser
 */
class Category extends AppModel {

    public $primaryKey = 'id';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CategoryLang' => array(
			'className' => 'CategoryLang',
			'foreignKey' => 'category_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CategoryUser' => array(
			'className' => 'CategoryUser',
			'foreignKey' => 'category_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),

	);

}

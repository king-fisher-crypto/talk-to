<?php
App::uses('AppModel', 'Model');
/**
 * UserCountryLang Model
 *
 * @property UserCountries $UserCountries
 * @property Lang $Lang
 */
class UserCountryLang extends AppModel {




	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'UserCountries' => array(
			'className' => 'UserCountries',
			'foreignKey' => 'user_countries_id',
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

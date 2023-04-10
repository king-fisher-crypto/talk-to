<?php
App::uses('AppModel', 'Model');
/**
 * UserCredit Model
 *
 * @property User $User
 */
class UserLevel extends AppModel {

public $useTable = 'userlevels';
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
	);
}

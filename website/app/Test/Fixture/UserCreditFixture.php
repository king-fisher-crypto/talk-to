<?php
/**
 * UserCreditFixture
 *
 */
class UserCreditFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'credits' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'date_upd' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'fk_user_credits_users1' => array('column' => 'users_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'credits' => 1,
			'date_upd' => '2013-12-10 15:56:33',
			'users_id' => 1
		),
	);

}

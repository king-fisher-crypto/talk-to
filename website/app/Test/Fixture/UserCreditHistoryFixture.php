<?php
/**
 * UserCreditHistoryFixture
 *
 */
class UserCreditHistoryFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'user_credit_history';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'user_credit_history' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'agent_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'credits' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'seconds' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'user_credits_before' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'user_credits_after' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10),
		'date_add' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'user_credit_history', 'unique' => 1),
			'fk_user_credit_history_users1' => array('column' => 'user_id', 'unique' => 0),
			'fk_user_credit_history_users2' => array('column' => 'agent_id', 'unique' => 0)
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
			'user_credit_history' => 1,
			'user_id' => 1,
			'agent_id' => 1,
			'credits' => 1,
			'seconds' => 1,
			'user_credits_before' => 1,
			'user_credits_after' => 1,
			'date_add' => '2013-12-10 15:56:33'
		),
	);

}

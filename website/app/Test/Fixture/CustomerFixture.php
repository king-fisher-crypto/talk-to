<?php
/**
 * CustomerFixture
 *
 */
class CustomerFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'firstname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lastname' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'pseudo' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'birthdate' => array('type' => 'date', 'null' => true, 'default' => null),
		'address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'postalcode' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'city' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 60, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'country_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'optin' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 1),
		'personal_code' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 9),
		'passwd' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'last_passwd_gen' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'date à laquelle le client a régénéré (recevoir un mot de passe par mail) son mot de passe pour la dernière fois (sécurité).'),
		'active' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'date_add' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'date_upd' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'date_lastconnexion' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'fk_customer_country1' => array('column' => 'country_id', 'unique' => 0)
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
			'id' => 1,
			'firstname' => 'Lorem ipsum dolor sit amet',
			'lastname' => 'Lorem ipsum dolor sit amet',
			'pseudo' => 'Lorem ipsum dolor sit amet',
			'email' => 'Lorem ipsum dolor sit amet',
			'birthdate' => '2013-11-28',
			'address' => 'Lorem ipsum dolor sit amet',
			'postalcode' => 'Lorem ipsum dolor ',
			'city' => 'Lorem ipsum dolor sit amet',
			'country_id' => 1,
			'optin' => 1,
			'personal_code' => 1,
			'passwd' => 'Lorem ipsum dolor sit amet',
			'last_passwd_gen' => '2013-11-28 13:53:23',
			'active' => 1,
			'deleted' => 1,
			'date_add' => '2013-11-28 13:53:23',
			'date_upd' => '2013-11-28 13:53:23',
			'date_lastconnexion' => '2013-11-28 13:53:23'
		),
	);

}

<?php
/**
 * UserCountryFixture
 *
 */
class UserCountryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'country_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'indexes' => array(
			'index3' => array('column' => array('user_id', 'country_id'), 'unique' => 1),
			'fk_user_countries_users1' => array('column' => 'user_id', 'unique' => 0),
			'fk_user_countries_countries1' => array('column' => 'country_id', 'unique' => 0)
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
			'user_id' => 1,
			'country_id' => 1
		),
	);

}

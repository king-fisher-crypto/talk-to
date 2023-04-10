<?php
/**
 * DomainFixture
 *
 */
class DomainFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'domain' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'date_add' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'active' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'domain' => 'Lorem ipsum dolor sit amet',
			'date_add' => '2013-12-10 15:56:33',
			'active' => 1
		),
	);

}

<?php
/**
 * CustomerCreditFixture
 *
 */
class CustomerCreditFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'customer_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'credits' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 20),
		'date_upd' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'fk_customer_credits_customer1' => array('column' => 'customer_id', 'unique' => 0)
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
			'customer_id' => 1,
			'credits' => 1,
			'date_upd' => '2013-11-28 13:53:23'
		),
	);

}

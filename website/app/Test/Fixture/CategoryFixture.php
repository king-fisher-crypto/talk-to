<?php
/**
 * CategoryFixture
 *
 */
class CategoryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'id_parent' => array('type' => 'integer', 'null' => true, 'default' => null),
		'level_depth' => array('type' => 'integer', 'null' => true, 'default' => null),
		'nleft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'nright' => array('type' => 'integer', 'null' => true, 'default' => null),
		'active' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'date_add' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'univers'),
		'date_upd' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'id_parent' => 1,
			'level_depth' => 1,
			'nleft' => 1,
			'nright' => 1,
			'active' => 1,
			'date_add' => '2013-12-10 15:56:32',
			'date_upd' => '2013-12-10 15:56:32'
		),
	);

}

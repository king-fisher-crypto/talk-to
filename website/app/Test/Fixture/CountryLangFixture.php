<?php
/**
 * CountryLangFixture
 *
 */
class CountryLangFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id_lang' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'country_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'name', 'unique' => 1),
			'unique_country_lang' => array('column' => array('id_lang', 'country_id'), 'unique' => 1),
			'fk_country_lang_lang1' => array('column' => 'id_lang', 'unique' => 0),
			'fk_country_lang_country1' => array('column' => 'country_id', 'unique' => 0)
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
			'id_lang' => 1,
			'country_id' => 1,
			'name' => 'Lorem ipsum dolor sit amet'
		),
	);

}

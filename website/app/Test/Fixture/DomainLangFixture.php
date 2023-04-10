<?php
/**
 * DomainLangFixture
 *
 */
class DomainLangFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'domain_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'lang_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'index'),
		'indexes' => array(
			'fk_domain_langs_domains1' => array('column' => 'domain_id', 'unique' => 0),
			'fk_domain_langs_langs1' => array('column' => 'lang_id', 'unique' => 0)
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
			'domain_id' => 1,
			'lang_id' => 1
		),
	);

}

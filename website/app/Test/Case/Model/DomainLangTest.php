<?php
App::uses('DomainLang', 'Model');

/**
 * DomainLang Test Case
 *
 */
class DomainLangTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.domain_lang',
		'app.domain',
		'app.lang',
		'app.cm',
		'app.cms_lang'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->DomainLang = ClassRegistry::init('DomainLang');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->DomainLang);

		parent::tearDown();
	}

}

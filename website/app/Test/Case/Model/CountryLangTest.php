<?php
App::uses('CountryLang', 'Model');

/**
 * CountryLang Test Case
 *
 */
class CountryLangTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.country_lang',
		'app.country'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CountryLang = ClassRegistry::init('CountryLang');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CountryLang);

		parent::tearDown();
	}

}

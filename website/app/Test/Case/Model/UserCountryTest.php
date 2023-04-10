<?php
App::uses('UserCountry', 'Model');

/**
 * UserCountry Test Case
 *
 */
class UserCountryTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_country',
		'app.country',
		'app.country_lang'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserCountry = ClassRegistry::init('UserCountry');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCountry);

		parent::tearDown();
	}

}

<?php
App::uses('UserCredit', 'Model');

/**
 * UserCredit Test Case
 *
 */
class UserCreditTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_credit',
		'app.users'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserCredit = ClassRegistry::init('UserCredit');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCredit);

		parent::tearDown();
	}

}

<?php
App::uses('UserCreditHistory', 'Model');

/**
 * UserCreditHistory Test Case
 *
 */
class UserCreditHistoryTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_credit_history',
		'app.user',
		'app.agent'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserCreditHistory = ClassRegistry::init('UserCreditHistory');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCreditHistory);

		parent::tearDown();
	}

}

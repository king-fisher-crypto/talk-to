<?php
App::uses('CustomerCredit', 'Model');

/**
 * CustomerCredit Test Case
 *
 */
class CustomerCreditTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.customer_credit',
		'app.customer'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CustomerCredit = ClassRegistry::init('CustomerCredit');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CustomerCredit);

		parent::tearDown();
	}

}

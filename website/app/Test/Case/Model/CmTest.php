<?php
App::uses('Cm', 'Model');

/**
 * Cm Test Case
 *
 */
class CmTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.cm',
		'app.lang',
		'app.cms_lang'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Cm = ClassRegistry::init('Cm');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Cm);

		parent::tearDown();
	}

}

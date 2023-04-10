<?php
App::uses('CategoryUser', 'Model');

/**
 * CategoryUser Test Case
 *
 */
class CategoryUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.category_user',
		'app.category',
		'app.category_lang',
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
		$this->CategoryUser = ClassRegistry::init('CategoryUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CategoryUser);

		parent::tearDown();
	}

}

<?php
App::uses('UserLang', 'Model');

/**
 * UserLang Test Case
 *
 */
class UserLangTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_lang',
		'app.user',
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
		$this->UserLang = ClassRegistry::init('UserLang');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserLang);

		parent::tearDown();
	}

}

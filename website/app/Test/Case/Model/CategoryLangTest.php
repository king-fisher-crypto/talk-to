<?php
App::uses('CategoryLang', 'Model');

/**
 * CategoryLang Test Case
 *
 */
class CategoryLangTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.category_lang',
		'app.category',
		'app.category_user',
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
		$this->CategoryLang = ClassRegistry::init('CategoryLang');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CategoryLang);

		parent::tearDown();
	}

}

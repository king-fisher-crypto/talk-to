<?php
App::uses('CmsLang', 'Model');

/**
 * CmsLang Test Case
 *
 */
class CmsLangTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.cms_lang',
		'app.cms',
		'app.lang',
		'app.cm'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CmsLang = ClassRegistry::init('CmsLang');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CmsLang);

		parent::tearDown();
	}

}

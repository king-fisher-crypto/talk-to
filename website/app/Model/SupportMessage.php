<?php
App::uses('AppModel', 'Model');
/**
 * BonusAgent Model
 *
 */
class SupportMessage extends AppModel {
	public $useTable = 'support_messages';

	public $hasMany = array(
		'SupportMessageAttachment' => array(
			'className' => 'SupportMessageAttachment',
		)
	);
}

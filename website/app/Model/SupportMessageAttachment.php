<?php
App::uses('AppModel', 'Model');
/**
 * BonusAgent Model
 *
 */
class SupportMessageAttachment extends AppModel {
	public $useTable = 'support_messages_attachments';

	public $belongsTo = array(
		'SupportMessage' => array(
			'className' => 'SupportMessage',
			'foreignKey' => 'support_message_id'
		)
	);
}

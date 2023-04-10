<?php
App::uses('AppModel', 'Model');
/**
 * CardLang Model
 *
 * @property CardLang $cardLang
 */
class CardLang extends AppModel {
	//
	public $belongsTo = array(
		'Card' => array(
			'className' => 'Card',
			'foreignKey' => 'card_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

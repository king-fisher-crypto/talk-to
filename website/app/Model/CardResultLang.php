<?php
App::uses('AppModel', 'Model');
/**
 * CardLang Model
 *
 * @property CardResultLang $cardResultLang
 */
class CardResultLang extends AppModel {
	//
	public $belongsTo = array(
		'CardResult' => array(
			'className' => 'CardResult',
			'foreignKey' => 'card_result_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

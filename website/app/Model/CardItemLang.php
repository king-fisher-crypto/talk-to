<?php
App::uses('AppModel', 'Model');
/**
 * CardLang Model
 *
 * @property CardItemLang $cardItemLang
 */
class CardItemLang extends AppModel {
	//
	public $belongsTo = array(
		'CardItem' => array(
			'className' => 'CardItem',
			'foreignKey' => 'card_item_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

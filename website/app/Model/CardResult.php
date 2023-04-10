<?php
App::uses('AppModel', 'Model');
/**
 * CardItem Model
 *
 * @property CardResult $cardResult
 */
class CardResult extends AppModel {
    // indicates the result type
    const RESULT_TYPE_DEFAULT = 0;
    const RESULT_TYPE_YES = 1;
    const RESULT_TYPE_NO = 2;

    const RESULT_TYPES = [
        'Defaut' => self::RESULT_TYPE_DEFAULT,
        'Oui' => self::RESULT_TYPE_YES,
        'Non' => self::RESULT_TYPE_NO,
    ];

    //
    public $primaryKey = 'card_result_id';

    //
    public $hasMany = array(
            'CardResultLang' => array(
                    'className' => 'CardResultLang',
                    'foreignKey' => 'card_result_id',
                    'dependent' => false,
                    'conditions' => '',
                    'fields' => '',
                    'order' => '',
                    'limit' => '',
                    'offset' => '',
                    'exclusive' => '',
                    'finderQuery' => '',
                    'counterQuery' => ''
            )
    );

    //
    public function getResultTypes() {
        return self::RESULT_TYPES;
    }
}

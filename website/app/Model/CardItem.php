<?php
App::uses('AppModel', 'Model');
/**
 * CardItem Model
 *
 * @property CardItem $cardItem
 */
class CardItem extends AppModel {
    //
    public $primaryKey = 'card_item_id';

    //
    public $hasMany = array(
            'CardItemLang' => array(
                    'className' => 'CardItemLang',
                    'foreignKey' => 'card_item_id',
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
    public function beforeDelete($cascade = true) {
        $r = [];
        $image_field_suff = 'image';
        $data = $this->find('first', ['conditions' => ['card_item_id' => $this->id]]);
        foreach ($data['CardItem'] as $k => $v) {
            if (strpos($k, $image_field_suff) !== strlen($k) - strlen($image_field_suff)) {
                continue;
            }
            $r[] = ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $v;
        }
        $this->files_to_delete = $r;
    }
}

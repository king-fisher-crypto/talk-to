<?php
App::uses('AppModel', 'Model');

/**
 * Card Model
 *
 * @property Card $Card
 */
class Card extends AppModel {
    // indicates the game type and the processing animation to show after choosing cards as well
    const GAME_TYPE_YES_NO = 1;
    const GAME_TYPE_SINGLE = 2;
    const GAME_TYPE_FORTUNE = 3;
    const GAME_TYPE_LOVE = 4;

    const GAME_TYPES = [
        'Oui / Non' => self::GAME_TYPE_YES_NO,
        'Célibataire' => self::GAME_TYPE_SINGLE,
        'agents' => self::GAME_TYPE_FORTUNE,
        'Amour' => self::GAME_TYPE_LOVE,
    ];

    // indicates the card items display mode
    const DISPLAY_MODE_LINE = 1;
    const DISPLAY_MODE_TWO_LINES = 2;
    const DISPLAY_MODE_SKEWED_LINE = 3;
    const DISPLAY_MODE_ARC_LINE = 4;

    const DISPLAY_MODES = [
        'Ligne' => self::DISPLAY_MODE_LINE,
        'Deux lignes' => self::DISPLAY_MODE_TWO_LINES,
        'Ligne avec cartes inclinées' => self::DISPLAY_MODE_SKEWED_LINE,
        'En arc' => self::DISPLAY_MODE_ARC_LINE,
    ];

    //
    public $primaryKey = 'card_id';

    //
    public $hasMany = array(
        'CardLang' => array(
            'className' => 'CardLang',
            'foreignKey' => 'card_id',
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
    public function getGameTypes() {
        return self::GAME_TYPES;
    }

    //
    public function getDisplayModes() {
        return self::DISPLAY_MODES;
    }

	//
    public function beforeDelete($cascade = true) {
		$r = [];
        $image_field_suff = 'image';
        $data = $this->find('first', ['conditions' => ['card_id' => $this->id]]);
        foreach ($data['Card'] as $k => $v) {
            if (strpos($k, $image_field_suff) !== strlen($k) - strlen($image_field_suff)) {
                continue;
            }
            $r[] = ROOT . '/' . Configure::read('Site.cardImages') . DS . $v;
        }
		$this->files_to_delete = $r;
	}
}

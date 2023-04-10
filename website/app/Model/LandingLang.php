<?php
App::uses('AppModel', 'Model');
/**
 * PageLang Model
 *
 * @property Page $Page
 * @property Lang $Lang
 */
class LandingLang extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	//public $primaryKey = 'page_id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Landing' => array(
			'className' => 'Landing',
			'foreignKey' => 'landing_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Lang' => array(
			'className' => 'Lang',
			'foreignKey' => 'lang_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function exist($idPage, $idLang){
        if(empty($idPage) || empty($idLang))
            return false;

        $count = $this->find('count', array(
            'conditions' => array('landing_id' => $idPage, 'lang_id' => $idLang),
            'recursive' => -1
        ));

        return ($count == 0 ?false:true);
    }

    public function select_page($id_lang){
        if(empty($id_lang))
            return array();

        $select = $this->find('list', array(
            'fields' => array('landing_id', 'meta_title'),
            'conditions' => array('LandingLang.lang_id' => $id_lang),
            'recursive' => -1
        ));

        return $select;
    }

    public function beforeSave($options = array()){

        parent::beforeSave();

        if(isset($this->data[$this->alias]['name']) && !empty($this->data[$this->alias]['name'])
        && (!isset($this->data[$this->alias]['link_rewrite']) || empty($this->data[$this->alias]['link_rewrite']))
        )
            $this->data[$this->alias]['link_rewrite'] = Tools::str2url($this->data[$this->alias]['name']);

       /* if (isset($this->data[$this->alias]['link_rewrite']))
            $this->data[$this->alias]['link_rewrite'] = Tools::str2url($this->data[$this->alias]['name']);*/
        return true;
    }
}

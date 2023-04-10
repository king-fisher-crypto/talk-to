<?php
App::uses('AppModel', 'Model');
/**
 * CategoryLang Model
 *
 * @property Lang $Lang
 */
class CategoryLang extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	//public $primaryKey = 'category_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'lang_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	    'Category' => array(
            'className' => 'Category',
            'foreignKey' => 'category_id',
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

    public function getCategories($lang){
        if(empty($lang)) return array();
        //Récupère les catégories active et dans la langue choisie
        $data = $this->find('list',array(
            'fields' => array('category_id','name'),
            'conditions' => array('Category.active' => 1, 'lang_id' => $lang),
            'recursive' => 0
        ));

        return $data;

    }

    public function beforeSave($options = array()){

        parent::beforeSave();

        /*if(isset($this->data[$this->alias]['name']) && !empty($this->data[$this->alias]['name']))
            $this->data[$this->alias]['link_rewrite'] = Tools::str2url($this->data[$this->alias]['name']);*/

        return true;
    }
}

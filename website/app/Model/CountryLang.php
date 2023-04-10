<?php
App::uses('AppModel', 'Model');
/**
 * CountryLang Model
 *
 * @property Country $Country
 */
class CountryLang extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id_lang' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'country_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

    public function getCountriesSelect($id_lang=0){
        $countries = $this->find('all',array(
            'fields' => array('country_id', 'name'),
            'conditions' => array('id_lang' => $id_lang),
            'recursive' => -1
        ));

        if(empty($countries)) return false;

        $out = array();
        foreach($countries as $country){
            $out[$country['CountryLang']['country_id']] = $country['CountryLang']['name'];
        }

        return $out;
    }
}

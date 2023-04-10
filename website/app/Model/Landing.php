<?php
App::uses('AppModel', 'Model');
/**
 * Page Model
 *
 * @property PageLang $PageLang
 */
class Landing extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'LandingLang' => array(
			'className' => 'LandingLang',
			'foreignKey' => 'landing_id',
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
    public $belongsTo = array(
        'PageCategory' => array(
            'className' => 'PageCategory',
            'foreignKey' => 'page_category_id',
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


    public function getVarsOfPage($page_id=0)
    {
        if (!$page_id)return false;

        $this->bindModel(array(
            'hasMany' => array(
                'PageParameter' => array(
                    'className' => 'PageParameter',
                    'foreignKey' => 'page_id',
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
            )
        ));

        $parms = $this->PageParameter->find("list", array(
            'fields'     => array('name','description'),
            'conditions' => array('page_id' => (int)$page_id)
        ));


        $out = array(
            'page' => $parms,
            'global' => Configure::read('Email.template.vars')
        );
        
        return $out;

    }
    public function countPageInCategory($idCat){
        if(empty($idCat))
            return 0;

        $count = $this->find('count', array(
            'conditions' => array('Page.page_category_id' => $idCat),
            'recursive' => -1
        ));

        return $count;
    }

}

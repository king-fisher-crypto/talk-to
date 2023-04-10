<?php
App::uses('AppModel', 'Model');
/**
 * Lang Model
 *
 * @property Cm $Cm
 */
class Lang extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'id_lang';

    public $hasMany = array(
        'LangLang' => array(
            'className' => 'LangLang',
            'foreignKey' => 'lang_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getAllLangs($id_cur_lang="") {
        return $this->find('all', [
            'conditions' => ['Lang.active' => 1],
            'recursive' => -1
        ]);
    }


    public function getLang($bool = false){
        //Retourne pour chaque id_lang son code et son nom
        if($bool){
            return $this->find('list',array(
                'conditions' => array('Lang.active' => 1),
                'fields' => array('Lang.language_code','Lang.name','Lang.id_lang'),
                'recursive' => -1
            ));
        }
        return $this->find('list',array(
            'conditions' => array('Lang.active' => 1),
            'fields' => array('Lang.id_lang','Lang.name'),
            'recursive' => -1
        ));
    }

	//The Associations below have been created with all possible keys, those that are not needed can be removed

  public function getLangsForSelect($id_cur_lang)
      {
	$langs = $this->getAllLangs();
	if (!$langs)return false;
        
        $out = array(''=>_('Choisir pays'));
        foreach ($langs AS $lang)
            $out[(int)$lang['UserCountries']['id']] = $country['UserCountryLang']['name'];
        
        return $out;
      }

}

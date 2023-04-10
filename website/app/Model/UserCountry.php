<?php
App::uses('AppModel', 'Model');
/**
 * UserCountry Model
 *
 */
class UserCountry extends AppModel {
    public $hasMany = array(
        'UserCountryLang' => array(
            'className' => 'UserCountryLang',
            'foreignKey' => 'user_countries_id',
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
    
    public function getCountries($id_lang=0, $orderBy='')
    {
        if (!$id_lang)return false;
        
        $conditions = array(
                                'fields'     => 'UserCountries.id,UserCountryLang.name',
                                'conditions' => array(
                                    'UserCountries.active'      =>  1,
                                    'UserCountryLang.lang_id'   =>  $id_lang
                                )
                           );
        if (!empty($orderBy))
            array_merge($conditions, array('order' => $orderBy));
        
        return $this->UserCountryLang->find('all', $conditions);
    }
    public function getCountriesForSelect($id_lang=0)
    {
        $countries = $this->getCountries($id_lang);
        if (!$countries)return false;
        
        $out = array(''=>_('Choisir pays'));
        foreach ($countries AS $country)
            $out[(int)$country['UserCountries']['id']] = $country['UserCountryLang']['name'];
        
        return $out;
    }
    public function getCountriesList($id_lang=0)
    {
        $countries = $this->getCountries($id_lang);
        if (!$countries)return false;
        
        $out = array();
        foreach ($countries AS $country)
            $out[(int)$country['UserCountries']['id']] = $country['UserCountryLang']['name'];
        
        return $out;
    }
}

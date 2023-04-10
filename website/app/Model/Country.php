<?php
App::uses('AppModel', 'Model');
/**
 * Country Model
 *
 * @property Country $Country
 * @property Product $Product
 */
class Country extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CountryLang' => array(
			'className' => 'CountryLang',
			'foreignKey' => 'country_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'country_id'
        )
	);

    public function getCountriesForSelect($id_lang = 0){
        if($id_lang == 0)
            return false;

        $tmp_countries = $this->find('all',array(
            'fields' => array('Country.id', 'CountryLang.name'),
            'conditions' => array('active' => 1),
            'joins' => array(
                array(
                    'table' => 'country_langs',
                    'alias' => 'CountryLang',
                    'type' => 'left',
                    'conditions' => array(
                        'CountryLang.id_lang = '.$id_lang,
                        'CountryLang.country_id = Country.id'
                    )
                )
            ),
            'order' => 'position ASC',
            'recursive' => -1
        ));

        $countries = array(''=>_('Choisir'));
        foreach($tmp_countries as $country)
            $countries[$country['Country']['id']] = $country['CountryLang']['name'];

        return $countries;
    }

    //Les indicatifs pour les input select
    public function getIndicatifForSelect(){
        $rows = $this->find('list', array(
            'fields'        => array('indicatif_tel', 'indicatif_tel'),
            'conditions'    => array('Country.indicatif_tel !=' => null),
            'recursive'     => -1
        ));

        /* complements */
            $rows_compl = $this->query('SELECT indicatif,country FROM indicatifs GROUP BY indicatif ORDER BY indicatif ASC');
            $compl = array();
            foreach ($rows_compl AS $row)
                if (!empty($row['indicatifs']['indicatif']))
                    $rows[$row['indicatifs']['indicatif']] = $row['indicatifs']['indicatif'];//. ' ('.$row['indicatifs']['country'].')';



        return array_unique($rows);
    }
	
	    //Les indicatifs pour les input select
    public function getIndicatifForSelectIns(){
        $rows = $this->find('list', array(
            'fields'        => array('indicatif_tel', 'indicatif_tel'),
            'conditions'    => array('Country.indicatif_tel !=' => null, 'active' => 1),
            'recursive'     => -1,
        ));
		
		$rows_compl = $this->query('SELECT indicatif,country FROM indicatifs GROUP BY indicatif ORDER BY country ASC');
            $compl = array();
            foreach ($rows_compl AS $row)
                if ( in_array($row['indicatifs']['indicatif'],$rows))
                    $rows[$row['indicatifs']['indicatif']] = $row['indicatifs']['country'].' +'.$row['indicatifs']['indicatif'];
		
		//sort($rows);

        /* complements */
            $rows_compl = $this->query('SELECT indicatif,country FROM indicatifs GROUP BY indicatif ORDER BY country ASC');
            $compl = array();
            foreach ($rows_compl AS $row)
                if (!empty($row['indicatifs']['indicatif']))
                    $rows[$row['indicatifs']['indicatif']] = ' +'.$row['indicatifs']['indicatif'].$row['indicatifs']['country'];



        return $rows;
    }


    //Retour l'indicatif du téléphone et le numéro sans l'indicatif
    public function getIndicatifOfPhone($phone_number){
        $data = array('indicatif' => false, 'phone_number' => $phone_number);

        if(empty($phone_number))
            return $data;

        $indicatifs = self::getIndicatifForSelect();

        //On récupère l'indicatif du numéro
        foreach($indicatifs as $val){
            //On a trouvé l'indicatif
            if(strpos($phone_number, $val) === 0){
                $data['indicatif'] = $val;
                $data['phone_number'] = substr($phone_number, strlen($val));
                return $data;
            }
        }

        return $data;
    }

    //Indicatif autorisé
    public function allowedIndicatif($indicatif){
        if(empty($indicatif) || !is_numeric($indicatif))
            return -1;

        $count = $this->find('count', array(
            'conditions'    => array('Country.indicatif_tel' => $indicatif),
            'recursive'     => -1
        ));

        if($count > 0)
            return true;

        /* On cherche dans les compléements */
        $count = $this->query("SELECT count(*) FROM indicatifs WHERE indicatif = ".(int)$indicatif);
        if ($count > 0)
            return true;

        return false;
    }

}

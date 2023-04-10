<?php
App::uses('AppModel', 'Model');
/**
 * CountryLangPhone Model
 *
 */
class CountryLangPhone extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'country_lang_phone';

/**
 * Primary key field
 *
 * @var string
 */
	//public $primaryKey = 'prepayed_second_credit';

    public $belongsTo = array(
        'Lang' => array(
            'className' => 'Lang',
            'foreignKey' => 'lang_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Country' => array(
            'className' => 'Country',
            'foreignKey' => 'country_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function getPhones($id_country=0, $id_lang=0)
    {
       
        if (empty($id_country) || empty($id_lang))return false;
        $phones = $this->find("all", array('conditions' => array(
                                        'lang_id'       =>   (int)$id_lang,
                                        'country_id'    =>   (int)$id_country
                                ))
                    );
        return $phones;
    }

    //Permet de récupérer le type de l'apppel surtaxé ou call
    public function getTypeCall($phone_number){
        //Impossible de déterminer le type d'appel
        if(empty($phone_number))
            return false;

        //On récupère les numéros de téléphone
        $data = $this->find('all', array(
            'fields'        => array('Country.indicatif_tel', 'CountryLangPhone.surtaxed_phone_number', 'CountryLangPhone.prepayed_phone_number'),
            'recursive'     => 2
        ));

        //Appel surtaxé ou local ??
        foreach($data as $row){
            //Numéro surtaxé
            if(!empty($row['CountryLangPhone']['surtaxed_phone_number'])){
                $tmp_phone_surtaxed = str_replace(' ','',$row['CountryLangPhone']['surtaxed_phone_number']);
                //On supprime le premier chiffre
                $tmp_phone_surtaxed = substr($tmp_phone_surtaxed, 1);
                //On rajoute l'indicatif du pays
                $tmp_phone_surtaxed = $row['Country']['indicatif_tel'].$tmp_phone_surtaxed;
                //Si le numéro est identique
                if(strcmp($phone_number, $tmp_phone_surtaxed) == 0)
                    //Alors c'est un numéro surtaxé
                    return 'surtaxed';
            }
            //Numéro local
            elseif(!empty($row['CountryLangPhone']['prepayed_phone_number'])){
                //Numéro local
                $tmp_phone_prepayed = str_replace(' ','',$row['CountryLangPhone']['prepayed_phone_number']);
                //On supprime le premier chiffre
                $tmp_phone_prepayed = substr($tmp_phone_prepayed, 1);
                //On rajoute l'indicatif du pays
                $tmp_phone_prepayed = $row['Country']['indicatif_tel'].$tmp_phone_prepayed;
                //Si le numéro est identique
                if(strcmp($phone_number, $tmp_phone_prepayed) == 0)
                    //Alors c'est un numéro local
                    return 'prepayed';
            }
        }

        //Pas réussi à déterminer le type de l'appel
        return false;
    }
}

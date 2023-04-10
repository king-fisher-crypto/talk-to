<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Countries $Countries
 * @property CategoryUser $CategoryUser
 * @property UserCountry $UserCountry
 * @property UserCreditHistory $UserCreditHistory
 * @property UserLang $UserLang
 * @property Planning $Planning
 * @property Favorite $Favorite
 * @property Message $Message
 */
class User extends AppModel {
    public $primaryKey = 'id';

    public $validate = array();
    public $customer_validate = array(
        'country_id' => array(
            'alphaNumeric' => array(
                'rule'     => 'numeric',
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez choisir votre pays'
            )
        ),
        'email' => array(
            'email' => array(
                'rule' => array('email', true),
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez saisir une adresse email valide'
            )
        )
    );

    public $agent_validate = array(
        'birthdate' => array(
            'date' => array(
                'rule'     => array('date', 'ymd'),
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez saisir une date de naissance valide'
            )
        ),
        /*'pseudo' => array(
            'alphaNumeric' => array(
                'rule'     => 'alphanumeric',
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez saisir votre pseudo uniquement avec des caractères alphanumériques et sans espace'
            )
        ),*/
        'sexe' => array(
            'alphaNumeric' => array(
                'rule'     => 'alphanumeric',
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez saisir votre sexe'
            )
        ),
        'email' => array(
            'email' => array(
                'rule' => array('email', true),
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez saisir une adresse email valide'
            )
        ),
        'country_id' => array(
            'alphaNumeric' => array(
                'rule'     => 'numeric',
                'required' => true,
                'allowEmpty' => false,
                'message'  => 'Veuillez choisir votre pays'
            )
        )
    /*,
        'siret' => array(
            'alphaNumeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Le numéro de SIRET doit être uniquement composé de chiffre'
            )
        )*/
    );

    public function getAgent($agent_id=0)
    {
        if (!$agent_id) return false;
        $datas = $this->find('first', array(
            'conditions' => array('User.id' => (int)$agent_id, 'User.role' => 'agent', 'User.active' => 1, 'User.deleted' => 0),
            'recursive' => -1
        ));
        if (empty($datas))return false;
        return $datas;
    }

    public function getByPhoneNumberApi($phoneNumberApi = 0)
    {
        if (!$phoneNumberApi) return false;
        $datas = $this->find('first', array(
            'conditions' => array('User.phone_api_use' => $phoneNumberApi, 'User.active' => 1, 'User.deleted' => 0),
            'recursive' => -1
        ));
        if (empty($datas))return false;
        return $datas;
    }

    public function beforeSave($options = array()){

        parent::beforeSave();
        $fieldsTag = array('pseudo','firstname','lastname','address','postalcode','city','siret');

        if (!$this->id){
            $passwordHasher = new SimplePasswordHasher();
            $this->data['User']['passwd'] = $passwordHasher->hash($this->data['User']['passwd']);
        }

        //Supprime les tags HTML
        foreach ($fieldsTag as $field){
            if(isset($this->data['User'][$field])) $this->data['User'][$field] = strip_tags($this->data['User'][$field]);
        }
        return true;
    }
/**
 * Validation rules
 *
 * @var array
 */


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
        'Planning' => array(
            'className' => 'Planning',
            'foreignKey' => 'user_id',
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
	    'UserPresentLang' => array(
            'className' => 'UserPresentLang',
            'foreignKey' => 'user_id',
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
		'CategoryUser' => array(
			'className' => 'CategoryUser',
			'foreignKey' => 'user_id',
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
		'UserCreditHistory' => array(
			'className' => 'UserCreditHistory',
			'foreignKey' => 'user_id',
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

    //Test si un email est unique pour un role spécifique
    public function singleEmail($email, $role){
        $data = $this->find('count', array(
            'conditions' => array('email' => $email, 'role' => $role),
            'recursive' => -1
        ));

        return (($data == 0)?true:false);
    }

    //Test si l'email existe et est unique et appartient à un admin
    public function isUniqueEmail($email){
        $data = $this->find('count', array(
            'conditions' => array('email' => $email, 'role' => 'admin', 'deleted' => 0),
            'recursive' => -1
        ));

        return ($data == 1 ?true:false);
    }

    /**
     * @param int       $id             L'id de l'user pour lequel on doit comparer le téléphone
     * @param string    $phone_number   Le numéro de tel de comparaison
     * @return bool|int
     */
    public function phoneNumberCmp($id, $phone_number){
        if(empty($id) || empty($phone_number))
            return false;

        $current_phone = $this->field('phone_number', array('id' => $id));

        //aucune valeur trouvée
        if($current_phone === false)
            return false;

        return strcmp($phone_number, $current_phone);
    }

    //Test si un compte est valid pour un client
    public function accountValid($id){
        $data = $this->find('count', array(
            'fields' => 'valid',
            'conditions' => array('id' => $id, 'valid' => 1),
            'recursive' => -1
        ));

        return (($data == 0)?false:true);
    }
	
	 //Test si un compte est come back user
    public function accountComeBack($id){
        $data = $this->find('count', array(
            'fields' => 'is_come_back',
            'conditions' => array('id' => $id, 'is_come_back' => 1),
            'recursive' => -1
        ));

        return (($data == 0)?false:true);
    }

    public function voisins($id){
        $data['prev'] = $this->find('first',array(
            'fields' => array('pseudo', 'agent_number'),
            'conditions' => array('role' => 'agent', 'deleted' => 0, 'active' => 1, 'id <' => $id),
            'order' => 'User.id DESC',
            'recursive' => -1
        ));

        $data['next'] = $this->find('first',array(
            'fields' => array('pseudo', 'agent_number'),
            'conditions' => array('role' => 'agent', 'deleted' => 0, 'active' => 1, 'id >' => $id),
            'order' => 'User.id ASC',
            'recursive' => -1
        ));

        return $data;
    }


    public function changeEtatUser($idUser, $activate = true){
        if(empty($idUser))
            return false;

        //Une activation
        if($activate){
            if($this->updateAll(array('User.active' => 1, 'User.valid' => 1), array('User.id' => $idUser)))
                return true;
        }else{  //Une désactivation
            if($this->updateAll(array('User.active' => 0, 'User.valid' => 0), array('User.id' => $idUser)))
                return true;
        }

        //Pas de modif
        return false;
    }

    public function delete_user($idUser){
        if(empty($idUser))
            return false;

        //On vérifie l'user existe
        $user = $this->find('first', array(
            'fields'        => array('User.id'),
            'conditions'    => array('User.id' => $idUser),
            'recursive'     => -1
        ));

        //Si pas d'user, alors false
        if(empty($user))
            return false;

        //On modifie le champ pour delete l'user
        $this->id = $idUser;
        if($this->saveField('deleted', 1))
            return true;
        else
            return false;
    }
	
	public function restore_user($idUser){
        if(empty($idUser))
            return false;

        //On vérifie l'user existe
        $user = $this->find('first', array(
            'fields'        => array('User.id'),
            'conditions'    => array('User.id' => $idUser),
            'recursive'     => -1
        ));

        //Si pas d'user, alors false
        if(empty($user))
            return false;

        //On modifie le champ pour delete l'user
        $this->id = $idUser;
        if($this->saveField('deleted', 0))
            return true;
        else
            return false;
    }

    public function isUniquePhoneNumber($phone, $role){
        if(empty($phone))
            return -1;

        //Pas de restriction sur les clients
        if($role === 'client')
            return true;

        $data = $this->find('first', array(
            'conditions'    => array('User.phone_number' => $phone, 'User.deleted' => 0, 'User.role' => 'agent'),
            'recursive'     => -1
        ));

        //Si pas de numéro
        if(empty($data))
            return true;
        else
            return false;
    }

    /**
     * @param int   $id     L'id du client
     * @return integer
     */
    public function getCredit($id, $withSentence=false){
        //Credit actuel du customer
        $this->id = $id;
        $current_credit = (int)$this->field('credit');
        //Aucune valeur retournée pour cet id
        if($current_credit === false)
            return false;
        return $current_credit;
    }




    /*
     * Attention, fonction développée pour les tests.
     * ELle permet de
     */
    public function fullDeleteUser($user_id=0)
    {
        $this->id = $user_id;
        $this->delete($user_id, true);
    }

}

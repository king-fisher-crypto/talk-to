<?php
/**
 * Created by PhpStorm.
 * User: Noox3
 * Date: 11/02/14
 * Time: 16:14
 */

class SocietyType extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'society_types';
	
	 public function getTypes($id_lang=0, $orderBy='')
    {
       // if (!$id_lang)return false;
        
        $conditions = array(
                                'fields'     => 'SocietyType.id,SocietyType.name',
                                'conditions' => array(
                                    'SocietyType.active'      =>  1,
                                )
                           );
        if (!empty($orderBy))
            array_merge($conditions, array('order' => $orderBy));
        
        return $this->find('all', $conditions);
    }
	
	 public function getTypeForSelect($id_lang=0)
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
        
        $out = array();
        foreach ($types AS $type)
            $out[(int)$type['SocietyType']['id']] = $type['SocietyType']['name'];
		 asort($out);
        return $out;
    }
	
	 public function getTypeForSelectExpert($id_lang=0,$list_id = array())
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;

		 $out = array(''=>_('Choisir'));
		 $out_type = array();
        foreach ($types AS $type){
			if(!count($list_id) || in_array($type['SocietyType']['id'],$list_id))
            	$out_type[(int)$type['SocietyType']['id']] = $type['SocietyType']['name'];
		}
		 asort($out_type);
		 $out = array_replace($out,$out_type);
        return $out;
    }
}
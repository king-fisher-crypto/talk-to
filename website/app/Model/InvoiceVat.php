<?php
/**
 * Created by PhpStorm.
 * User: Noox3
 * Date: 11/02/14
 * Time: 16:14
 */

class InvoiceVat extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = 'invoice_vats';
	
	 public function getTypes($id_lang=0, $orderBy='')
    {
       // if (!$id_lang)return false;
        
        $conditions = array(
                                'fields'     => array('InvoiceVat.*','Country.name','Society.name'),
                               	 'recursive' => -1,
								 'joins' => array(
									array('table' => 'user_country_langs',
										'alias' => 'Country',
										'type' => 'left',
										'conditions' => array(
											'Country.user_countries_id = InvoiceVat.country_id',
											'Country.lang_id = 1',
										)
									),
									 array('table' => 'society_types',
										'alias' => 'Society',
										'type' => 'left',
										'conditions' => array(
											'Society.id = InvoiceVat.society_type_id',
										)
									)
								),
								'order'=> array('Country.name','Society.name','InvoiceVat.rate')
                           );
        if (!empty($orderBy))
            array_merge($conditions, array('order' => $orderBy));
        
        return $this->find('all', $conditions);
    }
	
	 public function getVatForSelect($id_lang=0, $country_id = null)
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
        
        $out = array(''=>_('Choisir'));
        foreach ($types AS $type){
			if(!$country_id || $country_id == $type['InvoiceVat']['country_id'])
			$out[(int)$type['InvoiceVat']['id']] = $type['Country']['name'].' '.$type['Society']['name'].' '.$type['InvoiceVat']['rate'].'%';
		}
		if(!count($out)){
			foreach ($types AS $type){
				$out[(int)$type['InvoiceVat']['id']] = $type['Country']['name'].' '.$type['Society']['name'].' '.$type['InvoiceVat']['rate'].'%';
			}
		}
            
        
        return $out;
    }
	
	 public function getVatRateForSelect($id_lang=0, $invoice_vat_id = null, $country_id = null, $society_id = null)
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
        
        $out = array(''=>_('Choisir'));
		$list_rate = array();
        foreach ($types AS $type){
			if((!$country_id || $country_id == $type['InvoiceVat']['country_id']) && (!$society_id || $society_id == $type['InvoiceVat']['society_type_id']) && !in_array($type['InvoiceVat']['rate'],$list_rate) ){
				$out[(int)$type['InvoiceVat']['id']] = $type['InvoiceVat']['rate'].'%';
				array_push($list_rate,$type['InvoiceVat']['rate']);
			}
				
		}
		 
		 $countryid = "";
		 if(!count($out) && $invoice_vat_id){//force vat id
			$countryid = 1;
			foreach ($types AS $type){
				if(($invoice_vat_id == $type['InvoiceVat']['id']) && !in_array($type['InvoiceVat']['rate'],$list_rate) ){
					$out[(int)$type['InvoiceVat']['id']] = $type['InvoiceVat']['rate'].'%';
					array_push($list_rate,$type['InvoiceVat']['rate']);
					$countryid = $type['InvoiceVat']['country_id'];
				}
			}
		}
		 
		 if(!count($out) && $society_id){//force society
			$countryid = 1;
			foreach ($types AS $type){
				if($country_id == $type['InvoiceVat']['country_id'] &&  $society_id == $type['InvoiceVat']['society_type_id'] && !in_array($type['InvoiceVat']['rate'],$list_rate) ){
					$out[(int)$type['InvoiceVat']['id']] = $type['InvoiceVat']['rate'].'%';
					array_push($list_rate,$type['InvoiceVat']['rate']);
					$countryid = $type['InvoiceVat']['country_id'];
				}
			}
		}
		
		if(!count($out) && $countryid){//force 
			foreach ($types AS $type){
				if($countryid == $type['InvoiceVat']['country_id'] && !in_array($type['InvoiceVat']['rate'],$list_rate)){
					$out[(int)$type['InvoiceVat']['id']] = $type['InvoiceVat']['rate'].'%';
					array_push($list_rate,$type['InvoiceVat']['rate']);
				}
			}
		}
		
		 
        if(count($out) < 2)$out = array(''=>'Taux TVA non renseigné')  ;
        
        return $out;
    }
	
	 public function getVatForSave($id_lang=1,$society_id = null, $vat_id = null, $country_id = null )
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
        
		//get rate for vat_id
		$rate = null;
		foreach ($types AS $type){
			if($vat_id == $type['InvoiceVat']['id'])
			$rate = $type['InvoiceVat']['rate'];
		}
		
		 
        $out = null;
        foreach ($types AS $type){
			if($country_id == $type['InvoiceVat']['country_id'] && $society_id == $type['InvoiceVat']['society_type_id'] && $rate == $type['InvoiceVat']['rate'])
			$out = $type['InvoiceVat']['id'];
		}
            
        
        return $out;
    }
	
	 public function getVatForCompare($id_lang=1, $vat_id = null)
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
        $out = null;
        foreach ($types AS $type){
			if($vat_id == $type['InvoiceVat']['id'])
			$out = $type['Country']['name'].' '.$type['Society']['name'].' '.$type['InvoiceVat']['rate'].'%';
		}
            
        
        return $out;
    }
	
	public function getVatSocietybyCountry($id_lang=1, $country_id = null )
    {
        $types = $this->getTypes($id_lang);
        if (!$types)return false;
		
        $out = array();
        foreach ($types AS $type){
			if(!in_array($type['InvoiceVat']['society_type_id'],$out) &&  $country_id == $type['InvoiceVat']['country_id'])
				array_push($out,$type['InvoiceVat']['society_type_id']);
		}

		if(!count($out)){
			foreach ($types AS $type){
				if(!in_array($type['InvoiceVat']['society_type_id'],$out))
					array_push($out,$type['InvoiceVat']['society_type_id']);
			}
		}
        return $out;
    }
	
}
<?php

App::uses('AppHelper', 'View/Helper');

class LanguageHelper extends AppHelper
    {

    const LANG_ICON_MAP = [
	'en_gb' => 'gb',
	'en' => 'us',
    ];

    /*
      public function getFlagIconUrl($lang) {
      if (!empty($lang['lc_time'])) {
      $lang = $lang['lc_time'];
      }
      $lang = strtolower($lang);
      $lang = explode('.', $lang, 2);
      $lang = $lang[0];

      if (isset(self::LANG_ICON_MAP[$lang])) {
      $lang = self::LANG_ICON_MAP[$lang];
      } else {
      $lang = explode('_', $lang, 2);
      $lang = $lang[0];
      $lang = substr($lang, 0, 2);
      }

      return Helper::url('/assets/img/flags/' . $lang . '.png');
      }
     */

    public function getFlagIconUrl($id_lang)
	{
	if (!empty($lang['lc_time']))
	    {
	    $lang = $lang['lc_time'];
	    }
	$lang = strtolower($lang);
	$lang = explode('.', $lang, 2);
	$lang = $lang[0];

	if (isset(self::LANG_ICON_MAP[$lang]))
	    {
	    $lang = self::LANG_ICON_MAP[$lang];
	    }
	else
	    {
	    $lang = explode('_', $lang, 2);
	    $lang = $lang[0];
	    $lang = substr($lang, 0, 2);
	    }

	return Helper::url('/assets/img/flags/' . $lang . '.png');
	}

    public function getLang($id_lang)
	{

	//return 'coucou';exit;
	/*
	  App::import("Model", "Lang");
	  $model = new Lang();

	  $lang = $model->find('first', array(
	  'conditions' => array('id_lang' => $id_lang, 'active' => 1 )
	  ));
	 */

	/* On recupere les traductions des langues */
	App::import("Model", "LangLang");
	$ll = new LangLang();

	$trad = $ll->find("first",
		array(
		    'fields' => array('name'),
		    'conditions' => array(
			"lang_id" => $id_lang,
			'in_lang_id' => $id_lang
//			    'in_lang_id' => $this->Session->read('Config.id_lang')
		    )
		)
	);

	//var_dump($this->Session->read('Config.id_lang'));
	// echo"<br>".$trad["name"];
	return $trad["LangLang"]["name"];

	/*
	  $langs = $model->find("all",
	  array(
	  'conditions' => array(
	  'Domain.country_id' => $this->Session->read('Config.id_country'),
	  'DomainLang.domain_id'  => $this->Session->read('Config.id_domain'),
	  'Lang.active' => 1
	  ),
	  'recursive' => 0
	  )
	  );
	 */


	/* On remplace le nom de la langue par sa traduction */
	/*
	  foreach ($langs AS $k => $v)
	  if (isset($trad[$v['Lang']['id_lang']]))
	  $langs[$k]['Lang']['name'] = $trad[$v['Lang']['id_lang']];

	 */



	/*
	  $langs = $model->find("all",
	  array(
	  'conditions' => array(
	  'Domain.country_id' => $this->Session->read('Config.id_country'),
	  'DomainLang.domain_id'  => $this->Session->read('Config.id_domain'),
	  'Lang.active' => 1
	  ),
	  'recursive' => 0
	  )
	  );
	 */

	// Cache::write($cacheAlias, $langs, Configure::read('nomCacheNavigation'));
	// return $trad;
	}

    /*
     *  RETOURNE LE PICTO + NOM DE LA LANGUE TRADUITE
      A PARTIR DE L'id_lang DE L'USER
     * 	 */

    public function getIconAndLang($id_lang)
	{

	/* On recupere la traduction de la langues */
	$trad = $this->getLang($id_lang);

	/* picto */
	App::import("Model", "Lang");
	$lang = new Lang();

	$res = $lang->find("first",
		array(
		    'fields' => array('lc_time'),
		    'conditions' => array(
			"id_lang" => $id_lang,
			"active" => 1,
		    )
		)
	);

	$lc_time = $res["Lang"]["lc_time"];

	$lc_time = strtolower($lc_time);
	$lc_time_ar = explode('_', $lc_time, 2);
	$codelang = $lc_time_ar[0];

	$url_picto = Helper::url('/assets/img/flags/' . $codelang . '.png');

	//var_dump($this->Session->read('Config.id_lang'));
	// echo"<br>".$trad["name"];
	return "<img src='$url_picto' /> " . $trad;
	}

	 public function getFlagUrlFromLc_time($lc_time)
	{
	    $lc_time = strtolower($lc_time);
	    $lc_time_ar = explode('_', $lc_time, 2);
	    $codelang = $lc_time_ar[0];
	   // echo"<br>$lc_time =>".$codelang;
	    $url_flag = Helper::url('/assets/img/flags/' . $codelang . '.png');
	    return $url_flag;

	}
	
	 /*
     *  RETOURNE LES PICTOS + NOMS DE LA LANGUE TRADUITE
	DS la langue indiquée par $id_cur_lang
	POUR SELECTBOX
     * 	 */

    public function getIconsAndLangsForSelect($id_cur_lang)
	{
	    $options = [];
	 
	   //$this->loadModel('LangLang');
	    App::import("Model", "LangLang");
	    $ll = new LangLang();
	    
	    $sql="SELECT lang_langs.*, langs.*  FROM lang_langs RIGHT JOIN langs ON langs.id_lang = lang_langs.lang_id  WHERE in_lang_id =  $id_cur_lang";
	    //echo"<br>".$sql;
	     
	    $langs = $ll->query($sql);
	    //var_dump($langs);
	    
	    foreach ($langs as $lang) 
	    { 
		
		$id = $lang["lang_langs"]["lang_id"];
		$label = $lang["lang_langs"]["name"];
		$lc_time = $lang["langs"]["lc_time"];
		$url_flag = $this->getFlagUrlFromLc_time($lc_time); ;
		
	
	//	$options[$id] = ['value' => $label, "style" => "background:url('".$url_flag."') no-repeat;",  'class' => 'extra'];
		$options[$id] = array('name' => $label, 'value' => $id,  'style' => 'background:url(\''.$url_flag.'\') no-repeat; width:40px; height:30px;;');
	    }
	   
	    return $options;
	}
	
	
    /*
     *  RETOURNE LEs PICTOs des langues
      à la ligne
      A PARTIR DE L'id_lang DE L'USER
     * 	 */

    public function getIconslangs($ids_langs)
	{
	//return '<img src="/assets/img/flags/de.png"> <img src="/assets/img/flags/en.png"> <img src="/assets/img/flags/fr.png">';
	
	$html = "";
	//if(empty($ids_langs)) return $html;
	if(empty($ids_langs)) $ids_langs="1";
	/* picto */
	//App::import("Model", "Lang");
	$Lang = ClassRegistry::init('Lang');
	$sql = "SELECT lc_time FROM langs WHERE id_lang IN($ids_langs)";
	//echo"<br>sql=".$sql;
	$res = $Lang->query($sql);
	
	/*
	echo"<br>========================";
	var_dump($res);
	echo"<br>========================";
	*/
	
	foreach ($res as $lc_time)
	    {
	   // var_dump($lc_time["langs"]["lc_time"]);echo"<br>-----------";
	    $lc_time = strtolower($lc_time["langs"]["lc_time"]);
	    $lc_time_ar = explode('_', $lc_time, 2);
	    $codelang = $lc_time_ar[0];

	    $url_flag = Helper::url('/assets/img/flags/' . $codelang . '.png');
	    $html .= "<img src='$url_flag' /> ";
	    }

	//var_dump($this->Session->read('Config.id_lang'));
	// echo"<br>".$trad["name"];
	return $html;
	}

    }

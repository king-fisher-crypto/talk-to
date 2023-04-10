<?php
App::uses('AppController', 'Controller');

class SitemapController extends AppController {
    public $components = array('RequestHandler');
    public $autoRender = false;

    private $current_domain;
    private $current_domain_langs;
    public function beforeFilter()
    {
        $this->Auth->allow('sitemap','index');

        /* On récupère le domaine */
        $pieces = parse_url(Router::url('/', true));
        $host = $pieces['host'];


        /* Et sa configuration en db */
        $this->loadModel('Domain');
        $this->loadModel('Country');
        $this->current_domain = $this->Domain->findByDomain($host);

        foreach ($this->current_domain['Lang'] AS $lang)
            $this->current_domain_langs[$lang['id_lang']] = $lang['language_code'];


    }
    public function getXmlHeaderAndPrint($xml=false)
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo $xml;
        die();
    }
    public function index()
    {
      set_time_limit ( 0 );
		ini_set("memory_limit",-1);
		$current_lang = $this->current_domain['Lang'][0];
      /* $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($this->current_domain['Lang'] AS $lang){
            $url = Router::url(array(
                'controller' => 'sitemap',
                'action'     => 'sitemap',
                'langid'     => $lang['id_lang']
            ), true);
            $xml.= '<sitemap>
              <loc>'.$url.'</loc>
              
           </sitemap>';
        }
        $xml.= '</sitemapindex>';
        $this->getXmlHeaderAndPrint($xml);*/
		$id_domain = 0;
		if(!empty($this->current_domain['Domain']['id']))
		$id_domain = $this->current_domain['Domain']['id'];
		
		if($id_domain != 15)
			$this->sitemap($current_lang['id_lang']);
		else
			$this->redirect(array('controller' => 'home', 'action' => 'index'));	
    }
    public function sitemap($langid = null)
    {
		$dbb_r = new DATABASE_CONFIG();
		$dbb_head = $dbb_r->default;
		$mysqli_head = new mysqli($dbb_head['host'], $dbb_head['login'], $dbb_head['password'], $dbb_head['database']);
		$lang_id = (int)$langid;//$this->request->params['langid'];
		App::import("Model", "Domain");
            $model = new Domain();
            $countries = $model->find('all', array(
                'fields' => array('Domain.domain', 'Domain.country_id', 'CountryLang.name', 'Domain.id', 'Lang.language_code'),
                'conditions' => array('Domain.active' => 1), //,'Domain.domain LIKE' => '%'.Configure::read('Site.nameDomain').'%'
                'joins' => array(
                    array(
                        'table' => 'countries',
                        'alias' => 'Country',
                        'type'  => 'inner',
                        'conditions' => array(
                            'Country.active = 1',
                            'Country.id = Domain.country_id'
                        )
                    ),
                    array(
                        'table' => 'country_langs',
                        'alias' => 'CountryLang',
                        'type'  => 'left',
                        'conditions' => array(
                            'CountryLang.country_id = Domain.country_id',
                            'CountryLang.id_lang = '.$lang_id
                        )
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = Domain.default_lang_id')
                    )
                ),
                'order' => 'order_on_generiq_page ASC',
                'recursive' => -1
            ));


            $generiq_domains = explode(',',Configure::read('Site.id_domain_com'));

            //On supprime les domaines (.net, .org .info ...)
            $needle = array('.net', '.org', '.info', '.biz', '.eu');
            //Pour chaque domaine
            foreach($countries as $key => $domain){
                //Pour chaque needle
                foreach($needle as $search){
                    //Si on trouve un des needle
                    if(stripos($domain['Domain']['domain'], $search) !== false){
                        unset($countries[$key]);
                        break;
                    }
                }
            }
            foreach ($countries AS $key => $domain){
                if (in_array($domain['Domain']['id'], $generiq_domains))
                    unset($countries[$key]);
            }

		
		$countries_nav = $countries;//Cache::read('countries_nav', Configure::read('nomCacheNavigation'));
		$list_domain_actif = array();
		if($countries_nav && is_array($countries_nav)){
			foreach($countries_nav as $cc){
				array_push(	$list_domain_actif, $cc['Domain']['id']);
			}
		}

		//recup tous les langues actives des domaines
		$list_domaine_alternate = array();
		foreach($list_domain_actif as $domain_id){
			$list_domaine_alternate[$domain_id] = array();	
			$result_head = $mysqli_head->query("SELECT lang_id from domain_langs where domain_id = '{$domain_id}'");
			while($row_head = $result_head->fetch_array(MYSQLI_ASSOC)){
				array_push($list_domaine_alternate[$domain_id],$row_head['lang_id']);
			}
		}

        
        if (!in_array($lang_id, array_keys($this->current_domain_langs))){
            $this->return404();
        }
        $lang_code = $this->current_domain_langs[$lang_id];
        $forbidden_cat_ids = array(1,25);
		
		$list_domaine_reorder = array();
		$list_domaine_reorder[$this->current_domain['Domain']['id']] = $list_domaine_alternate[$this->current_domain['Domain']['id']];
		foreach($list_domaine_alternate as $k => $d){
			if($k != $this->current_domain['Domain']['id'])
				$list_domaine_reorder[$k] = $d;	
		}

		$list_domaine_alternate = $list_domaine_reorder;

       /* $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';*/
		$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        /* homepage */
			$lk = Router::url(array('controller' => 'home', 'action' => 'index'),true);
			$hrefTab = array();
			$result_head = $mysqli_head->query("SELECT category_id from category_langs where category_id = 1 and lang_id = '{$lang_id}'");
				$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
				$page_id = $row_head['category_id'];

				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT link_rewrite, cat_rewrite from category_langs where category_id = '{$page_id}' and lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){								
								if(isset($params['seo_word'])){
									$link .= $row_head['cat_rewrite'].'/'.$row_head['link_rewrite'];	
								}else{
									$link = '';	
								}
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'];//.'/'.$row_lang['language_code'];
							}
						//}
					}
				}
            $xml.= $this->getSitemapNode($lk,$hrefTab);//.$lang_code

        /* catégories */
            $this->loadModel('CategoryLang');
            $rows = $this->CategoryLang->find("all", array(
                'conditions' => array(
                    'Category.active' => 1,
                    'CategoryLang.lang_id' => $lang_id,
                    'NOT' => array(
                            'Category.id' => $forbidden_cat_ids
                        )

                    )
                ));
            ///:language/:link_rewrite-:id-:page
			
            foreach ($rows AS $row){
                /* On récupère la langue courante */
                $url = Router::url(
                   array(
                       'controller'      => 'category',
                       'action'          => 'display',
                       'language'        => $lang_code,
                       'link_rewrite'    => $row['CategoryLang']['link_rewrite'],
                       'id'              => $row['Category']['id']
                   ), true
                );
				$hrefTab = array();
				$result_head = $mysqli_head->query("SELECT category_id from category_langs where link_rewrite = '{$row['CategoryLang']['link_rewrite']}' and lang_id = '{$lang_id}'");
				$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
				$page_id = $row_head['category_id'];
				
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT link_rewrite, cat_rewrite from category_langs where category_id = '{$page_id}' and lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){
								$link = '';
								if(isset($params['seo_word'])){
									$link .= $row_head['cat_rewrite'].'/';	
								}
								$link .= $row_head['link_rewrite'].'-'.$page_id;
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
							}
						//}
					}
				}

                $xml.= $this->getSitemapNode($url,$hrefTab);


                //$url = $this->getCategoryLink($row['CategoryLang']['link_rewrite'], $lang_code, true);
                //$xml.= $this->getSitemapNode($url);
            }
		
		 /* Pages without category */
            $this->loadModel('PageLang');
            
            $rows = $this->PageLang->find("all", array(
                'fields'     => array('link_rewrite','page_id'),
                'conditions' => array(
                    'Page.active' => 1,
					'Page.page_category_id' => null,
                    'Lang.id_lang' => $lang_id,
                ),
				'limit' => 99999,
				'maxLimit' => 99999
            ));
		
            foreach ($rows AS $row){
				
				$url = $this->getCmsPageLink($row['PageLang']['link_rewrite'], $lang_code, true);
				$hrefTab = array();
				$xml.= $this->getSitemapNode($url,$hrefTab);
            }

        /* Pages with category*/
            $this->PageLang->bindModel(array(
                'belongsTo' => array(
                    'PageCategory' => array(
                        'className' => 'PageCategory',
                        'foreignKey' => '',
                        'conditions' => 'Page.page_category_id = PageCategory.id',
                        'fields' => '',
                        'order' => ''
                    )
                )
            ));
            $rows = $this->PageLang->find("all", array(
                'fields'     => array('link_rewrite','page_id', 'PageCategory.display'),
                'conditions' => array(
                    'Page.active' => 1,
                    'Lang.id_lang' => $lang_id,
                    'PageCategory.display' => 1
                ),
				'limit' => 99999,
				'maxLimit' => 99999
            ));
 			$noindexpage = array(174,175,176,244);
            foreach ($rows AS $row){
				
				$url = $this->getCmsPageLink($row['PageLang']['link_rewrite'], $lang_code, true);
				
				$hrefTab = array();
				$result_head = $mysqli_head->query("SELECT page_id from page_langs where link_rewrite = '{$row['PageLang']['link_rewrite']}' and lang_id = '{$lang_id}'");
				$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
				$page_id = $row_head['page_id'];
				$do_it = true;
				if($page_id == 36 && $lang_code == 'fre')$do_it = false;
				if( !in_array($page_id,$noindexpage) && $do_it){
					
					foreach($list_domaine_alternate as $iddomaine => $l_lang){
						$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
						$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
						if(is_array($l_lang )){
							foreach($l_lang as $langid){
								//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
									$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
									$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
									$code_lang = explode('_',$row_lang['lc_time']);
									$result_head = $mysqli_head->query("SELECT C.name, L.link_rewrite from page_langs L, pages P, page_category_langs C where L.page_id = '{$page_id}' and L.lang_id = '{$langid}' and L.page_id = P.id and C.page_category_id = P.page_category_id and C.lang_id = '{$langid}'");	
									$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
									if($row_head['link_rewrite']){
										$link = $this->slugify($row_head['name']).'/'.$row_head['link_rewrite'];
										$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
										//echo '<link rel="alternate" href="https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link.'" hreflang="'.$code_lang[0].'-'.strtolower($row_domain['iso']).'" />';	
									}
								//}
							}
						}
					}
					if(!substr_count($url, 'link_rewrite') && !substr_count($url, 'seo_word'))
					$xml.= $this->getSitemapNode($url,$hrefTab);
				}
            }
		
		/* Reviews */
            
                $url = Router::url(
                    array(
                        'controller'      => 'reviews',
                        'action'          => 'display',
                        'language'        => $lang_code,
                    ),true
                );
				$page_id = 127;
				$hrefTab = array();
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT C.name, L.link_rewrite from page_langs L, pages P, page_category_langs C where L.page_id = '{$page_id}' and L.lang_id = '{$langid}' and L.page_id = P.id and C.page_category_id = P.page_category_id and C.lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){
								$link = $row_head['link_rewrite'];
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
								//echo '<link rel="alternate" href="https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link.'" hreflang="'.$code_lang[0].'-'.strtolower($row_domain['iso']).'" />';	
							}
						//}
					}
				}
                $xml.= $this->getSitemapNode($url,$hrefTab);

		/* Products */
            
                $url = Router::url(
                    array(
                        'controller'      => 'products',
                        'action'          => 'tarif',
                        'language'        => $lang_code,
                    ),true
                );
				$page_id = 90;
				$hrefTab = array();
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT C.name, L.link_rewrite from page_langs L, pages P, page_category_langs C where L.page_id = '{$page_id}' and L.lang_id = '{$langid}' and L.page_id = P.id and C.page_category_id = P.page_category_id and C.lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){
								$link = $row_head['link_rewrite'];
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
								//echo '<link rel="alternate" href="https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link.'" hreflang="'.$code_lang[0].'-'.strtolower($row_domain['iso']).'" />';	
							}
						//}
					}
				}
                $xml.= $this->getSitemapNode($url,$hrefTab);
				
		 /* Contacts */
            
                $url = Router::url(
                    array(
                        'controller'      => 'contacts',
                        'action'          => 'index',
                        'language'        => $lang_code,
                    ),true
                );
				$page_id = 32;
				$hrefTab = array();
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT C.name, L.link_rewrite from page_langs L, pages P, page_category_langs C where L.page_id = '{$page_id}' and L.lang_id = '{$langid}' and L.page_id = P.id and C.page_category_id = P.page_category_id and C.lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){
								$link = $row_head['link_rewrite'];
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
								//echo '<link rel="alternate" href="https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link.'" hreflang="'.$code_lang[0].'-'.strtolower($row_domain['iso']).'" />';	
							}
						//}
					}
				}
                $xml.= $this->getSitemapNode($url,$hrefTab);			

        /* Agents */
            //'/:language/expert/:link_rewrite-:agent_number',
            $this->loadModel('User');
            $conditions = array(
                'User.role'   => 'agent',
                'User.deleted'=> 0,
                'User.active' => 1,
                'FIND_IN_SET('.(int)$this->current_domain['Domain']['country_id'].',User.countries)',
               // 'FIND_IN_SET('.$lang_id.',User.langs)'
            );

            $rows = $this->User->find("all", array(
                'conditions' => $conditions,
				'limit' => 99999,
				'maxLimit' => 99999
            ));
		$url_cut = '';
            foreach ($rows AS $row){
                $url = Router::url(
                    array(
                        'controller'      => 'agents',
                        'action'          => 'display',
                        'language'        => $lang_code,
                        'link_rewrite'    => strtolower(str_replace(' ','-',$row['User']['pseudo'])),
                        'agent_number'    => $row['User']['agent_number']
                    ),true
                );
				// $url = $this->getCmsPageLink($row['PageLang']['link_rewrite'], $lang_code, true);
				$hrefTab = array();
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							if($row['User']['pseudo']){
								$link = 'agents/'.strtolower(str_replace(' ','-',$row['User']['pseudo'])).'-'.$row['User']['agent_number'];
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
							}
						//}
					}
				}
                $xml.= $this->getSitemapNode($url,$hrefTab);
				$url_cut = $url;
            }
		
		/* Horoscopes */
		
		$urltab = explode('/',$url_cut);
		$domain_horo = 'https://'.$urltab[2];
		$hrefTab = array();
		$seo_horoscope = 'horoscope-du-jour';//Configure::read('Routing.horoscopes');
		$url = $domain_horo.'/'.$lang_code.'/'.$seo_horoscope;	
		foreach($list_domaine_alternate as $iddomaine => $l_lang){
			$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
			$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
			foreach($l_lang as $langid){
				$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$seo_horoscope;
			}
		}
		$xml.= $this->getSitemapNode($url,$hrefTab);
		
		$dbb_r = new DATABASE_CONFIG();
		$dbb_head = $dbb_r->default;
		$mysqli_sitemap = new mysqli($dbb_head['host'], $dbb_head['login'], $dbb_head['password'], $dbb_head['database']);
		$result_sitemap = $mysqli_sitemap->query("SELECT sign_id,link_rewrite from horoscope_signs where lang_id = '{$lang_id}'");
		while($row_sitemap = $result_sitemap->fetch_array(MYSQLI_ASSOC)){
			
			$url = $domain_horo.'/'.$lang_code.'/'.$seo_horoscope.'/'.$row_sitemap['link_rewrite'];	
			$hrefTab = array();
			$result_head = $mysqli_head->query("SELECT sign_id from horoscope_signs where link_rewrite = '{$row_sitemap['link_rewrite']}' and lang_id = '{$lang_id}'");
					$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
					$page_id = $row_head['sign_id'];
				foreach($list_domaine_alternate as $iddomaine => $l_lang){
					$result_domain = $mysqli_head->query("SELECT * from domains where id = '{$iddomaine}'");	
					$row_domain = $result_domain->fetch_array(MYSQLI_ASSOC);
					
					foreach($l_lang as $langid){
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$langid}'");	
							$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
							$code_lang = explode('_',$row_lang['lc_time']);
							$result_head = $mysqli_head->query("SELECT link_rewrite from horoscope_signs where sign_id = '{$page_id}' and lang_id = '{$langid}'");	
							$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
							if($row_head['link_rewrite']){								
								$link = $seo_horoscope.'/'.$row_head['link_rewrite'];	
								$hrefTab[$code_lang[0].'-'.strtolower($row_domain['iso'])] = 'https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link;
								//echo '<link rel="alternate" href="https://'.$row_domain['domain'].'/'.$row_lang['language_code'].'/'.$link.'" hreflang="'.$code_lang[0].'-'.strtolower($row_domain['iso']).'" />';	
							}
						//}
					}
				}
			
			$xml.= $this->getSitemapNode($url,$hrefTab);
		}
      
   /* Cards */
            
				$hrefTab = array();
				$domain_card = 'https://'.$urltab[2];
					
           // $result_lang = $mysqli_head->query("SELECT * from langs where id_lang = '{$lang_id}'");	
					//	$row_lang = $result_lang->fetch_array(MYSQLI_ASSOC);
            
						//if($iddomaine != $current_id_domain || $langid != $current_id_lang){
							$result_card = $mysqli_head->query("SELECT * from  card_langs where lang_id = '{$lang_id}'");	
							while($row_card = $result_card->fetch_array(MYSQLI_ASSOC)){
							if($row_card['url_path']){
								$link = $row_card['url_path'];
                $url = $domain_card.'/'.$lang_code.'/'.'tarots-en-ligne'.'/'.$link;
								$xml.= $this->getSitemapNode($url,$hrefTab);	
							}
						}
      	
      
		$mysqli_sitemap->close();	
		


        $xml.= '</urlset>';

        $this->getXmlHeaderAndPrint($xml);
    }
    private function getSitemapNode($url=false,$href=array(), $changefreq='daily')
    {
		$xml = '<url>';
		$xml .= '<loc>'.$url.'</loc>';
		/*if($href && is_array($href)){
			foreach($href as $lg => $url){
				$xml .='<xhtml:link rel="alternate" hreflang="'.$lg.'" href="'.$url.'" />';
			}
		}*/
		#$xml .= (!empty($changefreq)?'<changefreq>'.$changefreq.'</changefreq>':'');
		$xml .= '</url>';
        return $xml;
    }
	public function slugify($str)
	{
		
		$str = strip_tags($str); 
		$str = $this->remove_accents($str);
		$str = preg_replace('/[\r\n\t ]+/', ' ', $str);
		$str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
		$str = strtolower($str);
		$str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
		$str = htmlentities($str, ENT_QUOTES, "utf-8");
		$str = str_replace("&amp;", 'et', $str);
		$str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
		$str = str_replace(' ', '-', $str);
		$str = rawurlencode($str);
		$str = str_replace('%', '-', $str);
		return $str;
	}
}
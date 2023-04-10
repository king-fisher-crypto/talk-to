<?php

App::uses('SubdomainRoute', 'Routing/Route');

include('database.php');
include('function.php');
$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


$lang_tab = array(
	1 => "fre",
	2 => "eng",
	3 => "deu",
	4 => "spa",
	5 => "ita",
	6 => "ltz",
	7 => "por",
	8 => "frc",
	10 => "frs",
	11 => "frb",
	12 => "frl"
);

$routing['pages'] = array(
    'fre' => 'pagefre',
    'eng' => 'pageeng',
    'deu' => 'pagedeu',
    'spa' => 'pagespa',
    'ita' => 'pageita',
    'ltz' => 'pageltz',
    'por' => 'pagepor',
    'frc' => 'pagefrc',
	'frs' => 'pagefre',
	'frb' => 'pagefre',
	'frl' => 'pagefre'
);
$routing['landings'] = array(
    'fre' => 'landingfre',
    'eng' => 'landingeng',
    'deu' => 'landingdeu',
    'spa' => 'landingspa',
    'ita' => 'landingita',
    'ltz' => 'landingltz',
    'por' => 'landingpor',
    'frc' => 'landingfrc',
	'frs' => 'landingfre',
	'frb' => 'landingfre',
	'frl' => 'landingfre'
);

$result_routing_page = $mysqli_conf_route->query("SELECT name,page_category_id from page_category_langs");

while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	if($row_routing_page['name']){
		if($row_routing_page['page_category_id'] != 15)
		array_push($routing['pages'],slugify($row_routing_page['name'])); 
		else
		array_push($routing['landings'],slugify($row_routing_page['name'])); 
	}
}
$routing['categories'] = array(
    'fre' => 'catfre',
    'eng' => 'cateng',
    'deu' => 'catdeu',
    'spa' => 'catspa',
    'ita' => 'catita',
    'ltz' => 'catltz',
    'por' => 'catpor',
    'frc' => 'catfrc',
	'frs' => 'catfre',
	'frb' => 'catfre',
	'frl' => 'catfre'
);

$result_routing_page = $mysqli_conf_route->query("SELECT cat_rewrite from category_langs");
while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	if($row_routing_page['cat_rewrite']){
		array_push($routing['categories'],slugify($row_routing_page['cat_rewrite'])); 
	}
}

$routing['reviews'] = array();
$result_routing_page = $mysqli_conf_route->query("SELECT * from page_langs where page_id = 127");
while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	$routing['reviews'][$lang_tab[ $row_routing_page['lang_id'] ]  ] = $row_routing_page['link_rewrite'];
}

$routing['products'] = array();
$result_routing_page = $mysqli_conf_route->query("SELECT * from page_langs where page_id = 90");
while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	$routing['products'][$lang_tab[ $row_routing_page['lang_id'] ]  ] = $row_routing_page['link_rewrite'];
}


$routing['contacts'] = array();
$result_routing_page = $mysqli_conf_route->query("SELECT * from page_langs where page_id = 32");
while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	$routing['contacts'][$lang_tab[ $row_routing_page['lang_id'] ]  ] = $row_routing_page['link_rewrite'];
}

$routing['gifts'] = array();
$result_routing_page = $mysqli_conf_route->query("SELECT * from page_langs where page_id = 409");
while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
	$routing['gifts'][$lang_tab[ $row_routing_page['lang_id'] ]  ] = $row_routing_page['link_rewrite'];
}

$routing['old_expert'] = array(
    'fre' => 'agents',
    'eng' => 'agents',
    'deu' => 'agents',
    'spa' => 'agents',
    'ita' => 'agents',
    'ltz' => 'agents',
    'por' => 'agents',
    'frc' => 'agents',
	'frs' => 'agents',
	'frb' => 'agents',
	'frl' => 'agents'
);

$routing['expert'] = array(
    'fre' => 'agents-en-ligne',
    'eng' => 'agents-en-ligne',
    'deu' => 'agents-en-ligne',
    'spa' => 'agents-en-ligne',
    'ita' => 'agents-en-ligne',
    'ltz' => 'agents-en-ligne',
    'por' => 'agents-en-ligne',
    'frc' => 'agents-en-ligne',
	'frs' => 'agents-en-ligne',
	'frb' => 'agents-en-ligne',
	'frl' => 'agents-en-ligne'
);

$routing['horoscopes'] = array(
    'fre' => 'horoscope-du-jour',
    'eng' => 'horoscope-du-jour',
    'deu' => 'horoscope-du-jour',
    'spa' => 'horoscope-du-jour',
    'ita' => 'horoscope-du-jour',
    'ltz' => 'horoscope-du-jour',
    'por' => 'horoscope-du-jour',
    'frc' => 'horoscope-du-jour',
	'frs' => 'horoscope-du-jour',
	'frb' => 'horoscope-du-jour',
	'frl' => 'horoscope-du-jour'
);

$routing['cards'] = array(
	'fre' => 'tarots-en-ligne',
	'eng' => 'tarots-en-ligne',
	'deu' => 'tarots-en-ligne',
	'spa' => 'tarots-en-ligne',
	'ita' => 'tarots-en-ligne',
	'ltz' => 'tarots-en-ligne',
	'por' => 'tarots-en-ligne',
	'frc' => 'tarots-en-ligne',
	'frs' => 'tarots-en-ligne',
	'frb' => 'tarots-en-ligne',
	'frl' => 'tarots-en-ligne'
);

$routing['horo'] = array();
$result_routing_horo = $mysqli_conf_route->query("SELECT sign_id, link_rewrite from horoscope_signs");
while($result_routing_h = $result_routing_horo->fetch_array(MYSQLI_ASSOC)){
	if($result_routing_h['sign_id']){
		if(!array_key_exists($result_routing_h['sign_id'], $routing['horo'])){
		//if(!is_array($routing['horo'][$result_routing_h['sign_id']])){
			$routing['horo'][$result_routing_h['sign_id']] = array();	
		}
	//$routing['horo'][$result_routing_h['sign_id']] = $result_routing_h['link_rewrite'];
		array_push($routing['horo'][$result_routing_h['sign_id']], $result_routing_h['link_rewrite']);
	}
}

// cards link
// FIXME: this is not optimal, we should not query this unless the path fragment corresponds to cards (see $routing['cards'])
$routing['card'] = array();
$result_routing_card = $mysqli_conf_route->query("SELECT url_path from card_langs");
while ($result_routing_c = $result_routing_card->fetch_array(MYSQLI_ASSOC)) {
    $routing['card'][] = $result_routing_c['url_path'];
}

Configure::write('Routing', $routing);


    /* Sitemap / robots */
    Router::connect('/robots.txt',
        array('controller' => 'robots','action' => 'index')
    );
    Router::connect('/sitemap.xml',
        array('controller' => 'sitemap','action' => 'index')
    );
    Router::connect('/sitemap-:langid.xml',
        array('controller' => 'sitemap','action' => 'sitemap'),
        array('langid' => '[0-9]+')
    );




    /* Admin */
    Router::connect('/admin/:controller',
        array('controller' => 'agents|accounts','action','admin' => true),
        array('language' => false)
    );
	Router::connect('/admin/:controller/:action/action/page::page',
        array('controller' => 'agents','action' => 'notes_client','admin' => true),
        array('page' => '[0-9]+')
    );
	
	
    Router::connect('/admin/:controller/:action-:number-:timestamp',
        array('controller' => 'agents','action' => '[a-zA-Z_-]+','admin' => true),
        array(
            'pass' => array('number', 'timestamp'),
            'language' => false
        )
    );
    Router::connect('/admin/:controller/:action-:id',
        array('controller' => 'agents|accounts|category|products','action' => '[a-zA-Z_-]+','admin' => true),
        array(
            'pass' => array('id'),
            'language' => false
        )
    );
    Router::connect('/admin/:controller/:action',
        array('controller' => 'agents|accounts|products','action','admin' => true),
        array('language' => false)
    );
    Router::connect('/admin/:controller/:action-:country-:lang',
        array('controller' => 'phones', 'action' => 'edit', 'admin' => true),
        array(
            'pass' => array('country', 'lang'),
            'language' => false
        )
    );
    Router::connect('/admin/:controller/:action-:id-:sign',
        array('controller' => 'horoscopes', 'action' => 'edit', 'admin' => true),
        array(
            'pass' => array('id', 'sign'),
            'language' => false
        )
    );
    Router::connect('/admin/:controller/:action-:code',
        array('controller' => 'vouchers', 'action' => '[a-zA-Z_-]+', 'admin' => true),
        array(
            'pass' => array('code'),
            'language' => false
        )
    );

	Router::connect('/admins/:action-:id',
        array('controller'=> 'admins','action' => '[a-zA-Z_-]+','admin' => false),
        array('pass' => array('id'), 'language' => false)
    );
    Router::connect('/admins', array('controller' => 'admins', 'action' => 'index', 'admin' => true));


    /* Rest API */
    Router::connect(
         '/api/tmp/',
          array('controller' => 'api', 'action' => 'tmp')
       );
    Router::connect(
        '/api/tmp2/',
        array('controller' => 'api', 'action' => 'tmp2')
    );
    
    Router::connect(
         '/api/:call_controller/:call_action/*',
          array('controller' => 'api', 'action' => 'callController'),
          array(
            'pass' => array('call_controller','call_action'),
              'call_controller' => '[a-zA-Z0-9-]+',
              'call_action' => '[a-zA-Z0-9-]+'
            )
       );

	/* Ajax Categories */
    Router::connect(
        '/c/:id',
        array('controller' => 'category','action' => 'display'),
        array(
          'pass' => array('id'),
          'id' => '[0-9]+'
        )
    );
    
    /* Homepage */
    Router::connect(
          '/',
          array('controller' => 'home', 'action' => 'index'),
          array('language' => '[a-z]{3}')
    );
	
    Router::connect(
        '/:language',
        array('controller' => 'home', 'action' => 'index'),
        array('language' => '[a-z]{3}')
    );
	
	
	/* Contact */
	foreach($routing['contacts'] as $lien_contact){
		Router::connect(
			'/:language/'.$lien_contact,
			array('controller' => 'contacts', 'action' => 'index'),
			array('pass' => array('link_rewrite'),'language' => '[a-z]{3}','link_rewrite' => '[a-zA-Z0-9-]+')
		);
	}

	Router::connect(
			'/contacts/send/',
			array('controller' => 'contacts', 'action' => 'send')
		);

    // Vonage webhook


    Router::connect(
        '/webhooks/events',
        array('controller' => 'voicecall', 'action' => 'events')
    );
    Router::connect(
        '/webhooks/answer',
        array('controller' => 'voicecall', 'action' => 'answer')
    );

    Router::connect(
        '/webhooks/call',
        array('controller' => 'voicecall', 'action' => 'call')
    );

    Router::connect(
        '/webhooks/dtmf',
        array('controller' => 'voicecall', 'action' => 'dtmf')
    );

    Router::connect(
        '/webhooks/jwt',
        array('controller' => 'voicecall', 'action' => 'jwt')
    );




/* Gift */
	foreach($routing['gifts'] as $lien_gift){
		Router::connect(
			'/:language/'.$lien_gift,
			array('controller' => 'gifts', 'action' => 'index'),
			array('pass' => array('link_rewrite'),'language' => '[a-z]{3}','link_rewrite' => '[a-zA-Z0-9-]+')
		);
	}

	 Router::connect(
        '/gifts/show-:hash',
        array('controller' => 'gifts','action' => 'show'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'gifts',
            'action'    => 'show'
        )
    );
	 Router::connect(
        '/gifts/pdf-:hash',
        array('controller' => 'gifts','action' => 'pdf'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'gifts',
            'action'    => 'pdf'
        )
    );
	
	/* Product */
	
	foreach($routing['products'] as $lien_product){
		Router::connect(
			'/:language/'.$lien_product,
			array('controller' => 'products','action' => 'tarif'),
        	array('language' => '[a-z]{3}')
		);
	}

	/* voucher */
    Router::connect(
        '/accounts/usevoucher-:code',
        array('controller' => 'accounts','action' => 'usevoucher'),
        array(
            'pass' => array('code'),
            'code' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'accounts',
            'action'    => 'usevoucher'
        )
    );

    /* Agents */
    
    Router::connect( '/:controller/:action', 
			array(),//,'voyants.devspi.com'
			array(
			)			
		);
    
	
		Router::connect( '/:link_rewrite-:agent_number-:tab', 
			array('controller' => 'agents','action' => 'display'),//,'voyants.devspi.com'
			array(
				'pass' => array('agent_number','link_rewrite', 'tab'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'agent_number' => '[0-9]{4}',
				//'routeClass' => 'voyants.devspi.com'
			)			
		);
		
		Router::connect( '/:link_rewrite-:agent_number', 
			array('controller' => 'agents','action' => 'display'),//,'voyants.devspi.com'
			array(
				'pass' => array('agent_number','link_rewrite'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'agent_number' => '[0-9]{4}',
				//'routeClass' => 'voyants.devspi.com'
			)
		);
	
	
	foreach($routing['expert'] as $lien_expert){
		Router::connect(
			'https/:language/'.$lien_expert.'/:link_rewrite-:agent_number-:tab',
			array('controller' => 'agents','action' => 'display'),
			array(
				'pass' => array('agent_number','link_rewrite', 'tab'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'agent_number' => '[0-9]{4}'
			)
		);
		
		Router::connect(
			'/:language/'.$lien_expert.'/:link_rewrite-:agent_number',
			array('controller' => 'agents','action' => 'display'),
			array(
				'pass' => array('agent_number','link_rewrite'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'agent_number' => '[0-9]{4}'
			)
		);
	}

	foreach($routing['old_expert'] as $lien_expert){
		
		Router::connect(
			'/:language/'.$lien_expert.'/:link_rewrite-:agent_number',
			array('controller' => 'home','action' => 'index')
		);
	}


    /* Alerts */
    Router::connect(
        '/:language/alerts/stop_alert-:id',
        array('controller' => 'alerts','action' => 'stop_alert'),
        array(
            'pass' => array('id'),
            'language' => '[a-z]{3}',
            'id' => '[0-9]+',
            'controller' => 'alerts',
            'action'    => 'stop_alert'
        )
    );

	

	/* Sponsorship */
    Router::connect(
        '/sponsorship/parrainage-:hash',
        array('controller' => 'sponsorship','action' => 'parrainage'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'sponsorship',
            'action'    => 'parrainage'
        )
    );

	/* Survey */
    Router::connect(
        '/agents/survey-:hash',
        array('controller' => 'agents','action' => 'survey'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'agents',
            'action'    => 'survey'
        )
    );


	/* Remove */
    Router::connect(
        '/accounts/profilremove-:hash',
        array('controller' => 'accounts','action' => 'profilremove'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'accounts',
            'action'    => 'profilremove'
        )
    );
 	Router::connect(
        '/agents/profilremove-:hash',
        array('controller' => 'agents','action' => 'profilremove'),
        array(
            'pass' => array('hash'),
            'language' => '[a-z]{3}',
            'hash' => '[a-zA-Z0-9-=/+\|]+',
            'controller' => 'accounts',
            'action'    => 'profilremove'
        )
    );


    /* Horoscope */
	foreach($routing['horoscopes'] as $lien_horo){
		foreach($routing['horo'] as $id_h => $lien_horos){
			foreach($lien_horos as $lien_h){
				/*Router::connect('/:language/'.$lien_horo.'/'.$lien_h.'-:horoscope',
					array('controller' => 'horoscopes', 'action' => 'display'),
					array(
						'pass'          => array('seo_word', 'horoscope'),
						'language'      => '[a-z]{3}',
						'link_rewrite'  => '[a-zA-Z0-9-]+',
						'horoscope'     => '[0-9]+',
						'seo_word' 	=> $lien_h
					)
				);*/
				Router::connect('/:language/'.$lien_horo.'/'.$lien_h,
					array('controller' => 'horoscopes', 'action' => 'display'),
					array(
						'pass'          => array('seo_word'),
						'language'      => '[a-z]{3}',
						'link_rewrite'  => '[a-zA-Z0-9-]+',
						 'seo_word' 	=> $lien_h
					)
				);
				Router::connect('/'.$lien_h,
					array('controller' => 'horoscopes', 'action' => 'display'),//,'horoscope.devspi.com'
					array(
						'pass'          => array('seo_word', 'horoscope'),
						'language'      => '[a-z]{3}',
						'link_rewrite'  => '[a-zA-Z0-9-]+',
						'horoscope'     => '[0-9]+',
						'seo_word' 	=> $lien_h
					)
				);
				
			}
		}
	}
	Router::connect('/:language/horoscope-du-jour',
					array('controller' => 'horoscopes', 'action' => 'index'),
					array(
					)
				);

// card rewrite url
foreach($routing['cards'] as $lien_card){
    foreach($routing['card'] as $lien_cards){
            Router::connect(
                '/:language/'.$lien_card.'/:link_rewrite',
                array('controller' => 'cards', 'action' => 'display'),
                array(
                    'pass' => array('link_rewrite'),
                    'language'      => '[a-z]{3}',
                    'link_rewrite'  => '[a-zA-Z0-9-]+',
                    'seo_word'        => $lien_cards
                )
            );
    }
}
	
	/* Category */
		Router::connect(
			'/:language/category-display-:page/*',
			array('controller' => 'home','action' => 'index'),
			array(
				'pass' => array('page'),
				'language' => '[a-z]{3}',
				'page' => '[0-9]+'
			)
		);


		Router::connect(
			'/:language/category-display-:page',
			array('controller' => 'home','action' => 'index'),
			array(
				'pass' => array('page'),
				'language' => '[a-z]{3}',
				'page' => '[0-9]+'
			)
		);


		Router::connect(
			'/:language/:link_rewrite-:id-:page',
			array('controller' => 'category','action' => 'display'),
			array(
				'pass' => array('id','link_rewrite','page'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'id' => '[0-9]+',
				'page' => '[0-9]+'
			)
		);

		Router::connect(
			'/:language/:link_rewrite-:id',
			array('controller' => 'category','action' => 'display'),
			array(
				'pass' => array('id','link_rewrite'),
				'language' => '[a-z]{3}',
				'link_rewrite' => '[a-zA-Z0-9-]+',
				'id' => '[0-9]+'
			)
		);
	/* Reviews */
		foreach($routing['reviews'] as $lien_route){
			Router::connect(
				'/:language/'.$lien_route,
				array('controller' => 'reviews','action' => 'display'),
				array(
					'pass' => array('page'),
					'language' => '[a-z]{3}',
					'page' => '1'
				)
			);

			Router::connect(
				'/:language/'.$lien_route.'-:page',
				array('controller' => 'reviews','action' => 'display'),
				array(
					'pass' => array('page'),
					'language' => '[a-z]{3}',
					'page' => '[0-9]+'
				)
			);
		}


	/*  Landings */
	Router::connect(
            '/:language/agent/:link_rewrite-:id',
            array('controller' => 'landings','action' => 'display'),
            array(
                'pass' => array('link_rewrite','seo_word'),
                'language' => '[a-z]{3}',
                'link_rewrite' => '[a-zA-Z0-9-]+',
                'seo_word' => implode("|",array_values($routing['landings']))

            )
        );
	Router::connect(
            '/:language/:seo_word/:link_rewrite',
            array('controller' => 'landings','action' => 'display'),
            array(
                'pass' => array('link_rewrite'),
                'language' => '[a-z]{3}',
                'link_rewrite' => '[a-zA-Z0-9-]+',
                'seo_word' => implode("|",array_values($routing['landings']))
            )
        );

    	/* Pages */
	
		Router::connect(
            '/:language/:link_rewrite',
            array('controller' => 'pages','action' => 'display'),
            array(
                'pass' => array('link_rewrite'),
                'language' => '[a-z]{3}',
                'link_rewrite' => '[a-zA-Z0-9-]+',
            )
        );
		

        Router::connect(
            '/:language/:seo_word/:link_rewrite',
            array('controller' => 'pages','action' => 'display'),
            array(
                'pass' => array('link_rewrite'),
                'language' => '[a-z]{3}',
                'link_rewrite' => '[a-zA-Z0-9-]+',
                'seo_word' => implode("|",array_values($routing['pages']))
            )
        );
		Router::connect(
            '/:language/:seo_word/:seo_word2/:link_rewrite',
            array('controller' => 'pages','action' => 'display'),
            array(
                'pass' => array('link_rewrite'),
                'language' => '[a-z]{3}',
                'link_rewrite' => '[a-zA-Z0-9-]+',
                'seo_word' => implode("|",array_values($routing['pages'])),
				'seo_word2' => implode("|",array_values($routing['pages']))
            )
        );

		Router::connect(
            '/:language/pages/display/page:id',
            array('controller' => 'home','action' => 'inex'),
            array(
                'language' => '[a-z]{3}',
            )
        );
		Router::connect(
            '/:language/pages/display/seo_word:word',
            array('controller' => 'home','action' => 'inex'),
            array(
                'language' => '[a-z]{3}',
            )
        );
		Router::connect(
            '/:language/pages/display/link_rewrite:seo/seo_word:word',
            array('controller' => 'home','action' => 'inex'),
            array(
                'language' => '[a-z]{3}',
            )
        );

		
		
	/* Support */
	 Router::connect('/supports/submit_message',
        array('controller'=> 'support', 'action' => 'submit_message'),
        array(
            'controller' => 'support',
            'action'    => 'submit_message'
        )
    );


    /* Chat */
    Router::connect('/:controller/:action-:id',
        array('controller'=> 'chats','action' => 'create_session'),
        array('language' => '[a-z]{3}','id' => '[0-9]+', 'action' => 'create_session')
    );
	Router::connect('/:controller/:action-:id',
        array('controller'=> 'chats','action' => 'do_session'),
        array('language' => '[a-z]{3}','id' => '[0-9]+', 'action' => 'do_session')
    );


    /* Friendly url for user login */
    Router::connect('/login',
                array('controller'=>'users','action' => 'login'),
                array('language' => '[a-z]{3}')
                );
    Router::connect('/logout',
                array('controller'=>'users','action' => 'logout'),
                array('language' => '[a-z]{3}')
                );
    Router::connect('/:controller',
                array('controller'=> 'agents|accounts','action' => 'index'),
                array('language' => '[a-z]{3}')
                );
    Router::connect('/:controller/:action-:name',
        array('controller'=> 'agents|accounts', 'action' => 'downloadAttachment'),
        array(
            'pass' => array('name'),
            'language' => '[a-z]{3}',
            'controller' => 'agents|accounts',
            'action'    => 'downloadAttachment'
        )
    );

    Router::connect('/:controller/:action-:expert',
        array('controller'=> 'accounts', 'action' => 'review'),
        array(
            'pass' => array('expert'),
            'language' => '[a-z]{3}',
            'controller' => 'accounts',
            'action'    => 'review'
        )
    );
    Router::connect('/:controller/:action-:idMail',
        array('controller'=> 'agents|accounts', 'action' => 'mails'),
        array(
            'pass' => array('idMail'),
            'language' => '[a-z]{3}',
            'controller' => 'agents|accounts',
            'action'    => 'mails'
        )
    );
    Router::connect('/:controller/:action/:pack',
        array('controller'=> 'accounts','action' => 'buycredits'),
        array('language' => '[a-z]{3}','pack' => '[0-9]+', 'action' => 'buycredits')
    );
    Router::connect('/:controller/:action/:id',
        array('controller'=> 'accounts','action' => 'new_mail'),
        array('language' => '[a-z]{3}','id' => '[0-9]+', 'action' => 'new_mail')
    );
    Router::connect('/:controller/:action/:agent_number',
        array('controller'=> 'accounts','action' => 'favorites'),
        array('language' => '[a-z]{3}','agent_number' => '[0-9]+', 'action' => 'favorites')
    );
    Router::connect('/:controller/:action-:page',
        array('controller'=> 'accounts','action' => 'favorites|history|payments|buycredits'),
        array('language' => '[a-z]{3}', 'page' => '[0-9]+')
    );

	Router::connect('/:controller/:action/page/:page',
        array('controller'=> 'agents|accounts','action' => 'mails'),
        array('language' => '[a-z]{3}', 'page' => '[0-9]+')
    );
	Router::connect('/:controller/:action',
        array('controller'=> 'agents|accounts','action' => '[a-z]'),
        array('language' => '[a-z]{3}')
    );

    Router::connect('/:language/:controller/:action/*', array(), array('language' => '[a-z]{3}'));
    
	$mysqli_conf_route->close();
	CakePlugin::routes();

/**
* Load the CakePHP default routes. Remove this if you do not want to use
* the built-in default routes.
*/
require CAKE . 'Config' . DS . 'routes.php';
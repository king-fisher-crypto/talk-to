<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

class ReviewsController extends AppController {
    public $components = array('Paginator');
    public $helpers = array('Paginator' => array('url' => array('controller' => 'reviews')));

    public function beforeFilter() {

        $this->Auth->allow('index','ajaxLoad', 'loadreviews');

        parent::beforeFilter();
    }

    public function index(){


    }

    public function display($page = 1){

		//check url
		if($_SERVER['REQUEST_URI'] == '/reviews/display' && $this->Session->read('Config.id_lang')){
			$dbb_r = new DATABASE_CONFIG();
			$dbb_route = $dbb_r->default;
			$mysqli_url = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);
			$result_routing_page = $mysqli_url->query("SELECT * from page_langs where page_id = 127 and lang_id=".$this->Session->read('Config.id_lang'));
			$lien = '';
			while($row_routing_page = $result_routing_page->fetch_array(MYSQLI_ASSOC)){
				$lien = $row_routing_page['link_rewrite'];
			}
			$mysqli_url->close();
			$this->redirect('https://'.$_SERVER['SERVER_NAME'].'/'.$this->Session->read('Config.language').'/'.$lien);
		}

        $countReviews = $this->Review->find('count', array(
            'conditions' => array(
                'Review.status' => 1,
               'Agent.active' => 1, 'Review.parent_id' => NULL
            )
        ));
		// 'Review.lang_id = '.$this->Session->read('Config.id_lang')

        $reviewPpage = Configure::read('Site.limitReviewPage');
        if($page > (ceil($countReviews/$reviewPpage)) || $page == 0) $page = 1;

        //A partir d'"offset" reviews on répère les reviews
        $offset = ($page-1) * Configure::read('Site.limitReviewPage');


        $reviews = $this->Review->find('all',array(
            'fields' => array('Review.review_id', 'Review.user_id', 'Review.agent_id', 'Review.lang_id', 'Review.content', 'Review.rate', 'Review.date_add', 'Review.utile',
                'User.firstname',
                'Agent.id', 'Agent.pseudo', 'Agent.agent_number', 'Agent.has_photo'
            ),
            'conditions' => array(
                'Review.status' => 1, 'Agent.active' => 1, 'Review.parent_id' => NULL

            ),// 'Review.lang_id = '.$this->Session->read('Config.id_lang')
            'order' => array('Review.date_add' => 'desc'),
            'limit' => $reviewPpage,
            'offset' => $offset
        ));

		$review = array();

		foreach($reviews as $r){
			$avg = $this->Review->find('all',array(
				'fields' => array('avg(Review.pourcent) as av'
				),
				'conditions' => array(
					'Review.agent_id' => $r['Review']['agent_id'],
					'Review.status' => 1,
					'Review.parent_id' => NULL

				),
				'recursive' => -1
			));
			$response = $this->Review->find('first',array(
				'conditions' => array(
					'Review.parent_id' => $r['Review']['review_id'],
				     'Review.status' => 1
				),
				'recursive' => -1
			));
			$r['Review']['rate_avg'] = $avg[0][0]['av'];
			if($response){
				$r['Review']['reponse'] = $response['Review'];
			}

			$review[] = $r;
		}
		$reviews = $review;
        $this->set(compact('reviews', 'page', 'countReviews'));

		/* Metas */
        $this->site_vars['meta_title']       = __('Avis sur Spiriteo, le N°1 de la agents en ligne');
        $this->site_vars['meta_keywords']    = '';
        $this->site_vars['meta_description'] = __('Tous les avis clients de Spiriteo, le site de référence pour la agents en ligne de qualité - 98% de clients satisfaits ! Par tél, mail ou chat 24/24 7/7. Go !');


    }

	public function ajaxLoad()
    {


		 if (isset($this->request->data['ajax_for_reviews'])){

			 App::uses('FrontblockHelper', 'View/Helper');
        	$fbH = new FrontblockHelper(new View());

			 $reviewPpage = Configure::read('Site.limitReviewPage');
			 $page = $this->request->data['page'];
        	$offset = ($page-1) * Configure::read('Site.limitReviewPage');
		 $countReviews = $this->Review->find('count', array(
            'conditions' => array(
                'Review.status' => 1,
               'Agent.active' => 1, 'Review.parent_id' => NULL
            )
        ));

        $reviews = $this->Review->find('all',array(
            'fields' => array('Review.review_id', 'Review.user_id', 'Review.agent_id', 'Review.lang_id', 'Review.content', 'Review.rate', 'Review.date_add', 'Review.utile',
                'User.firstname',
                'Agent.id', 'Agent.pseudo', 'Agent.agent_number', 'Agent.has_photo'
            ),
            'conditions' => array(
                'Review.status' => 1, 'Agent.active' => 1, 'Review.parent_id' => NULL

            ),// 'Review.lang_id = '.$this->Session->read('Config.id_lang')
            'order' => array('Review.date_add' => 'desc'),
            'limit' => $reviewPpage,
            'offset' => $offset
        ));

		$review = array();

		foreach($reviews as $r){
			$avg = $this->Review->find('all',array(
				'fields' => array('avg(Review.pourcent) as av'
				),
				'conditions' => array(
					'Review.agent_id' => $r['Review']['agent_id'],
					'Review.status' => 1,
					'Review.parent_id' => NULL

				),
				'recursive' => -1
			));
			$response = $this->Review->find('first',array(
				'conditions' => array(
					'Review.parent_id' => $r['Review']['review_id'],
				     'Review.status' => 1
				),
				'recursive' => -1
			));
			$r['Review']['rate_avg'] = $avg[0][0]['av'];
			if($response){
				$r['Review']['reponse'] = $response['Review'];
			}

			$review[] = $r;
		}
		$reviews = $review;
		$this->layout = '';


			 $datasForView['reviews'] = $reviews;

			 $pagination = $fbH->getPagination($page, $countReviews,'Site.limitReviewPage', $this->params);

            /* On genere la vue */
                $view = new View($this, false);
                $view->set($datasForView);
                $json = array(
                    'html'          => $view->render('list'),
					'paginate'          => $fbH->getPaginateLoad($pagination,$params, 'review')
                );
                $this->jsonRender($json);
			 exit;
		 }
		$this->jsonRender(array('error' => ''));
	}

    public function admin_index(){
		// FIXME: what the heck is the following!
		//	just use some maths: k / 20
		$MatchPourcent = array(
			100	=> 5,
99	=> 4.95,
98	=> 4.90,
97	=> 4.85,
96	=> 4.80,
95	=> 4.75,
94	=> 4.70,
93	=> 4.65,
92	=> 4.60,
91	=> 4.55,
90	=> 4.50,
89	=> 4.45,
88	=> 4.40,
87	=> 4.35,
86	=> 4.30,
85	=> 4.25,
84	=> 4.20,
83	=> 4.15,
82	=> 4.10,
81	=> 4.05,
80	=> 4,
79	=> 3.95,
78	=> 3.90,
77	=> 3.85,
76	=> 3.80,
75	=>3.75,
74	=> 3.70,
73	=> 3.65,
72	=> 3.60,
71	=> 3.55,
70	=> 3.50,
69	=> 3.45,
68	=> 3.40,
67	=> 3.35,
66	=> 3.30,
65	=> 3.25,
64	=> 3.20,
63	=> 3.15,
62	=> 3.10,
61	=> 3.05,
60	=> 3.00,
59	=> 2.95,
58	=> 2.90,
57	=> 2.85,
56	=> 2.80,
55	=> 2.75,
54	=> 2.70,
53	=> 2.65,
52	=> 2.60,
51	=> 2.55,
50	=> 2.5,
49	=> 2.45,
48	=> 2.40,
47	=> 2.35,
46	=> 2.3,
45	=> 2.25,
44	=> 2.2,
43	=> 2.15,
42	=> 2.1,
41	=> 2.05,
40	=> 2,
39	=> 1.95,
38	=> 1.90,
37	=> 1.85,
36	=> 1.80,
35	=> 1.75,
34	=> 1.70,
33	=> 1.65,
32	=> 1.60,
31	=> 1.55,
30	=> 1.50,
29	=> 1.45,
28	=> 1.40,
27	=> 1.35,
26	=> 1.30,
25	=> 1.25,
24	=> 1.20,
23	=> 1.15,
22	=> 1.10,
21	=> 1.05,
20	=> 1,
19	=> 0.95,
18	=> 0.90,
17	=> 0.85,
16	=> 0.80,
15	=> 0.75,
14	=> 0.70,
13	=> 0.65,
12	=> 0.60,
11	=> 0.55,
10	=> 0.50,
9	=> 0.45,
8	=> 0.40,
7	=> 0.35,
6	=> 0.30,
5	=> 0.25,
4	=> 0.20,
3	=> 0.15,
2	=> 0.10,
1	=> 0.05
		);

        if(isset($this->params->query['refuse']))
            $conditions = array('Review.status' => 0);
        elseif(isset($this->params->query['online']))
            $conditions = array('Review.status' => 1);
        else
            $conditions = array('Review.status' => -1,'Review.rate' => 5,'Review.parent_id' => NULL);

		if(isset($this->params->data['Agent'])){
			if($this->params->data['Agent']['name'])
			$this->Session->write('ReviewAgent', $this->params->data['Agent']['name']);
			else
			$this->Session->write('ReviewAgent', '');
		}
        if($this->Session->read('ReviewAgent')){
			$condition = array('OR' => array('User.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'User.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','User.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'Agent.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%'));
			$conditions = array_merge($conditions, $condition);
			$this->set('agent_name', $this->Session->read('ReviewAgent'));
		}



		//Les avis qui sont en attente de modération
        $this->Paginator->settings = array(
            'fields' => array('Review.review_id','Review.content','Review.rate','Review.pourcent','Review.status','Review.date_add','Agent.id','Agent.pseudo','Agent.reviews_avg','User.id','User.firstname', 'ReviewsRep.*'),
            'conditions' => $conditions,
            'order' => array('Review.date_add' => 'desc'),
            'paramType' => 'querystring',
            'joins' => array(
                array(
                    'table' => 'reviews',
                    'alias' => 'ReviewsRep',
                    'type'  => 'left',
                    'conditions' => array(
                        'ReviewsRep.review_id = Review.parent_id',
                    )
                )
            ),
            'limit' => 10
        );

        $reviews = $this->Paginator->paginate($this->Review);


		foreach($reviews as &$review){
			$notation = '';
			 if(isset($this->params->query['refuse'])){
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }

			 }elseif(isset($this->params->query['online'])){
				$notation = $review['Agent']['reviews_avg'];
			 }else{
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }
			 }


			$review['Review']['notation'] = $notation;
		}


        $this->set(compact('reviews'));
    }

	 public function admin_index_bad(){
		// FIXME: what the heck is the following!
		//	just use some maths: k / 20
		$MatchPourcent = array(
			100	=> 5,
99	=> 4.95,
98	=> 4.90,
97	=> 4.85,
96	=> 4.80,
95	=> 4.75,
94	=> 4.70,
93	=> 4.65,
92	=> 4.60,
91	=> 4.55,
90	=> 4.50,
89	=> 4.45,
88	=> 4.40,
87	=> 4.35,
86	=> 4.30,
85	=> 4.25,
84	=> 4.20,
83	=> 4.15,
82	=> 4.10,
81	=> 4.05,
80	=> 4,
79	=> 3.95,
78	=> 3.90,
77	=> 3.85,
76	=> 3.80,
75	=>3.75,
74	=> 3.70,
73	=> 3.65,
72	=> 3.60,
71	=> 3.55,
70	=> 3.50,
69	=> 3.45,
68	=> 3.40,
67	=> 3.35,
66	=> 3.30,
65	=> 3.25,
64	=> 3.20,
63	=> 3.15,
62	=> 3.10,
61	=> 3.05,
60	=> 3.00,
59	=> 2.95,
58	=> 2.90,
57	=> 2.85,
56	=> 2.80,
55	=> 2.75,
54	=> 2.70,
53	=> 2.65,
52	=> 2.60,
51	=> 2.55,
50	=> 2.5,
49	=> 2.45,
48	=> 2.40,
47	=> 2.35,
46	=> 2.3,
45	=> 2.25,
44	=> 2.2,
43	=> 2.15,
42	=> 2.1,
41	=> 2.05,
40	=> 2,
39	=> 1.95,
38	=> 1.90,
37	=> 1.85,
36	=> 1.80,
35	=> 1.75,
34	=> 1.70,
33	=> 1.65,
32	=> 1.60,
31	=> 1.55,
30	=> 1.50,
29	=> 1.45,
28	=> 1.40,
27	=> 1.35,
26	=> 1.30,
25	=> 1.25,
24	=> 1.20,
23	=> 1.15,
22	=> 1.10,
21	=> 1.05,
20	=> 1,
19	=> 0.95,
18	=> 0.90,
17	=> 0.85,
16	=> 0.80,
15	=> 0.75,
14	=> 0.70,
13	=> 0.65,
12	=> 0.60,
11	=> 0.55,
10	=> 0.50,
9	=> 0.45,
8	=> 0.40,
7	=> 0.35,
6	=> 0.30,
5	=> 0.25,
4	=> 0.20,
3	=> 0.15,
2	=> 0.10,
1	=> 0.05
		);

        $conditions = array(
		'OR' => array(
			'AND' => array(
				'Review.status' => -1,
				'Review.rate <' => 5,
			),
			'Review.status' => -2,
		),
		'Review.parent_id' => NULL
	);

		if(isset($this->params->data['Agent'])){
			if($this->params->data['Agent']['name'])
			$this->Session->write('ReviewAgent', $this->params->data['Agent']['name']);
			else
			$this->Session->write('ReviewAgent', '');
		}
        if($this->Session->read('ReviewAgent')){
			$condition = array('Review.parent_id' => NULL,'Review.status' => -2,
							   'OR' => array('User.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'User.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','User.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'Agent.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%')
			);
			//$conditions = array_merge($conditions, $condition);
			$conditions = $condition;
			$this->set('agent_name', $this->Session->read('ReviewAgent'));
		}



		//Les avis qui sont en attente de modération
        $this->Paginator->settings = array(
            'fields' => array('Review.review_id','Review.content','Review.rate','Review.pourcent','Review.status','Review.date_add','Agent.id','Agent.pseudo','Agent.reviews_avg','User.id','User.firstname', 'ReviewsRep.*'),
            'conditions' => $conditions,
            'order' => array('Review.date_add' => 'desc'),
            'paramType' => 'querystring',
            'joins' => array(
                array(
                    'table' => 'reviews',
                    'alias' => 'ReviewsRep',
                    'type'  => 'left',
                    'conditions' => array(
                        'ReviewsRep.review_id = Review.parent_id',
                    )
                )
            ),
            'limit' => 10
        );

        $reviews = $this->Paginator->paginate($this->Review);


		foreach($reviews as &$review){
			$notation = '';
			 if(isset($this->params->query['refuse'])){
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }

			 }elseif(isset($this->params->query['online'])){
				$notation = $review['Agent']['reviews_avg'];
			 }else{
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }
			 }


			$review['Review']['notation'] = $notation;
		}


        $this->set(compact('reviews'));
    }

	 public function admin_index_resp(){
		// FIXME: what the heck is the following!
		//	just use some maths: k / 20
		$MatchPourcent = array(
			100	=> 5,
99	=> 4.95,
98	=> 4.90,
97	=> 4.85,
96	=> 4.80,
95	=> 4.75,
94	=> 4.70,
93	=> 4.65,
92	=> 4.60,
91	=> 4.55,
90	=> 4.50,
89	=> 4.45,
88	=> 4.40,
87	=> 4.35,
86	=> 4.30,
85	=> 4.25,
84	=> 4.20,
83	=> 4.15,
82	=> 4.10,
81	=> 4.05,
80	=> 4,
79	=> 3.95,
78	=> 3.90,
77	=> 3.85,
76	=> 3.80,
75	=>3.75,
74	=> 3.70,
73	=> 3.65,
72	=> 3.60,
71	=> 3.55,
70	=> 3.50,
69	=> 3.45,
68	=> 3.40,
67	=> 3.35,
66	=> 3.30,
65	=> 3.25,
64	=> 3.20,
63	=> 3.15,
62	=> 3.10,
61	=> 3.05,
60	=> 3.00,
59	=> 2.95,
58	=> 2.90,
57	=> 2.85,
56	=> 2.80,
55	=> 2.75,
54	=> 2.70,
53	=> 2.65,
52	=> 2.60,
51	=> 2.55,
50	=> 2.5,
49	=> 2.45,
48	=> 2.40,
47	=> 2.35,
46	=> 2.3,
45	=> 2.25,
44	=> 2.2,
43	=> 2.15,
42	=> 2.1,
41	=> 2.05,
40	=> 2,
39	=> 1.95,
38	=> 1.90,
37	=> 1.85,
36	=> 1.80,
35	=> 1.75,
34	=> 1.70,
33	=> 1.65,
32	=> 1.60,
31	=> 1.55,
30	=> 1.50,
29	=> 1.45,
28	=> 1.40,
27	=> 1.35,
26	=> 1.30,
25	=> 1.25,
24	=> 1.20,
23	=> 1.15,
22	=> 1.10,
21	=> 1.05,
20	=> 1,
19	=> 0.95,
18	=> 0.90,
17	=> 0.85,
16	=> 0.80,
15	=> 0.75,
14	=> 0.70,
13	=> 0.65,
12	=> 0.60,
11	=> 0.55,
10	=> 0.50,
9	=> 0.45,
8	=> 0.40,
7	=> 0.35,
6	=> 0.30,
5	=> 0.25,
4	=> 0.20,
3	=> 0.15,
2	=> 0.10,
1	=> 0.05
		);

            $conditions = array('Review.status' => -1,'Review.parent_id >' => 0);

		if(isset($this->params->data['Agent'])){
			if($this->params->data['Agent']['name'])
			$this->Session->write('ReviewAgent', $this->params->data['Agent']['name']);
			else
			$this->Session->write('ReviewAgent', '');
		}
        if($this->Session->read('ReviewAgent')){
			$condition = array('OR' => array('User.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'User.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','User.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.pseudo LIKE' => '%'.$this->Session->read('ReviewAgent').'%', 'Agent.firstname LIKE' => '%'.$this->Session->read('ReviewAgent').'%','Agent.lastname LIKE' => '%'.$this->Session->read('ReviewAgent').'%'));
			$conditions = array_merge($conditions, $condition);
			$this->set('agent_name', $this->Session->read('ReviewAgent'));
		}



		//Les avis qui sont en attente de modération
        $this->Paginator->settings = array(
            'fields' => array('Review.review_id','Review.content','Review.rate','Review.pourcent','Review.status','Review.date_add','Agent.id','Agent.pseudo','Agent.reviews_avg','User.id','User.firstname', 'ReviewsRep.*'),
            'conditions' => $conditions,
            'order' => array('Review.date_add' => 'desc'),
            'paramType' => 'querystring',
            'joins' => array(
                array(
                    'table' => 'reviews',
                    'alias' => 'ReviewsRep',
                    'type'  => 'left',
                    'conditions' => array(
                        'ReviewsRep.review_id = Review.parent_id',
                    )
                )
            ),
            'limit' => 10
        );

        $reviews = $this->Paginator->paginate($this->Review);


		foreach($reviews as &$review){
			$notation = '';
			 if(isset($this->params->query['refuse'])){
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }

			 }elseif(isset($this->params->query['online'])){
				$notation = $review['Agent']['reviews_avg'];
			 }else{
				$infoReviewCalc = $this->Review->find('all',array(
					'fields' => array('Review.pourcent'),
					'conditions' => array('Review.agent_id' => $review['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
					'limit' => 99999,
					'maxLimit' => 99999
				));
				$avg = 0;
				$total = 0;
				$nb = 0;
				foreach($infoReviewCalc as $note){
					$nb ++;
					$total += $MatchPourcent[$note['Review']['pourcent']];
				}
				 $nb ++;
				 $total += $MatchPourcent[$review['Review']['pourcent']];
				 if($nb){
					$avg = $total / $nb;
					$avg = number_format($avg,3);
					$notation = $avg;
				 }
			 }


			$review['Review']['notation'] = $notation;
		}


        $this->set(compact('reviews'));
    }

    public function admin_accept_review($id){
		// FIXME: what the heck is the following!
		//	just use some maths: k / 20
		$MatchPourcent = array(
			100	=> 5,
99	=> 4.95,
98	=> 4.90,
97	=> 4.85,
96	=> 4.80,
95	=> 4.75,
94	=> 4.70,
93	=> 4.65,
92	=> 4.60,
91	=> 4.55,
90	=> 4.50,
89	=> 4.45,
88	=> 4.40,
87	=> 4.35,
86	=> 4.30,
85	=> 4.25,
84	=> 4.20,
83	=> 4.15,
82	=> 4.10,
81	=> 4.05,
80	=> 4,
79	=> 3.95,
78	=> 3.90,
77	=> 3.85,
76	=> 3.80,
75	=>3.75,
74	=> 3.70,
73	=> 3.65,
72	=> 3.60,
71	=> 3.55,
70	=> 3.50,
69	=> 3.45,
68	=> 3.40,
67	=> 3.35,
66	=> 3.30,
65	=> 3.25,
64	=> 3.20,
63	=> 3.15,
62	=> 3.10,
61	=> 3.05,
60	=> 3.00,
59	=> 2.95,
58	=> 2.90,
57	=> 2.85,
56	=> 2.80,
55	=> 2.75,
54	=> 2.70,
53	=> 2.65,
52	=> 2.60,
51	=> 2.55,
50	=> 2.5,
49	=> 2.45,
48	=> 2.40,
47	=> 2.35,
46	=> 2.3,
45	=> 2.25,
44	=> 2.2,
43	=> 2.15,
42	=> 2.1,
41	=> 2.05,
40	=> 2,
39	=> 1.95,
38	=> 1.90,
37	=> 1.85,
36	=> 1.80,
35	=> 1.75,
34	=> 1.70,
33	=> 1.65,
32	=> 1.60,
31	=> 1.55,
30	=> 1.50,
29	=> 1.45,
28	=> 1.40,
27	=> 1.35,
26	=> 1.30,
25	=> 1.25,
24	=> 1.20,
23	=> 1.15,
22	=> 1.10,
21	=> 1.05,
20	=> 1,
19	=> 0.95,
18	=> 0.90,
17	=> 0.85,
16	=> 0.80,
15	=> 0.75,
14	=> 0.70,
13	=> 0.65,
12	=> 0.60,
11	=> 0.55,
10	=> 0.50,
9	=> 0.45,
8	=> 0.40,
7	=> 0.35,
6	=> 0.30,
5	=> 0.25,
4	=> 0.20,
3	=> 0.15,
2	=> 0.10,
1	=> 0.05
		);

		$old_review = $this->Review->find('first',array(
                'conditions' => array('review_id' => $id),
			));

		$url = Router::url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true, '?' => (isset($this->params->query['refuse'])?'refuse':false)),true);

		if($old_review['Review']['rate']< 5 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true, '?' => (isset($this->params->query['refuse'])?'refuse':false)),true);

		if($old_review['Review']['rate']< 1 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true, '?' => (isset($this->params->query['refuse'])?'refuse':false)),true);


        //On check la présentation
       // $this->checkEntite('Review', $id, 'status', (isset($this->params->query['refuse'])?0: $old_review['Review']['status'] == -2 ? -2 : -1), __('Cet avis n\'existe pas ou il n\'est pas en attente de validation'), $url, array('review_id' => $id));

		$this->loadModel('Domain');
		$this->loadModel('Lang');
		$this->loadModel('User');


        if($this->Review->updateAll(array('status' => 1),array('review_id' => $id))){
            //On récupère l'email du client et le pseudo de l'agent
           $infoReview = $this->Review->find('first',array(
                'fields' => array('Agent.pseudo','Agent.agent_number','Agent.id','Agent.domain_id','Agent.lang_id', 'User.email','User.firstname', 'User.lang_id', 'Review.parent_id'),
                'conditions' => array('review_id' => $id)
            ));

			$infoReviewCalc = $this->Review->find('all',array(
                'fields' => array('Review.pourcent'),
                'conditions' => array('Review.agent_id' => $infoReview['Agent']['id'],'Review.status' => 1, 'Review.parent_id' => NULL),
				'limit' => 99999,
					'maxLimit' => 99999
			));
			$avg = 0;
			$total = 0;
			$nb = 0;
			foreach($infoReviewCalc as $note){
				$nb ++;
				$total += $MatchPourcent[$note['Review']['pourcent']];
			}

			if($nb){
				$avg = $total / $nb;
				$avg = number_format($avg,3);

				$this->User->id = $infoReview['Agent']['id'];
				$this->User->saveField('reviews_avg', $avg);
				$this->User->saveField('reviews_nb', $nb);
			}

			$conditions = array(
						'Domain.id' => $infoReview['Agent']['domain_id'],
					);
			$domain = $this->Domain->find('first',array('conditions' => $conditions));
					if(!isset($domain['Domain']['domain']))$domain['Domain']['domain'] = 'https://fr.spiriteo.com';
					$conditions = array(
						'Lang.id_lang' => $infoReview['Agent']['lang_id'],
					);

			$lang = $this->Lang->find('first',array('conditions' => $conditions));


            //$this->sendEmail($infoReview['User']['email'],'Avis validé','admin_accept',array('data' => array('content' => 'Votre avis sur'.' '.$infoReview['Agent']['pseudo'].' a été validé')));

			$url_find ='https://'.$domain['Domain']['domain'].'/'.$lang['Lang']['language_code'].'/agents/'.strtolower($infoReview['Agent']['pseudo']).'-'.$infoReview['Agent']['agent_number'];

			if($infoReview['Review']['parent_id']){
				$send = $this->sendCmsTemplateByMail(306, (int)$infoReview['User']['lang_id'], $infoReview['User']['email'], array(
					  'AGENT_PSEUDO' =>   $infoReview['Agent']['pseudo'],
					  'AGENT_NOM' =>   $infoReview['Agent']['pseudo'],
					  'AGENT_URL' =>   $url_find,
					  'CART_USER_FIRSTNAME' => $infoReview['User']['firstname'],
				));
			}else{
				$send = $this->sendCmsTemplateByMail(198, (int)$infoReview['User']['lang_id'], $infoReview['User']['email'], array(
					  'AGENT_PSEUDO' =>   $infoReview['Agent']['pseudo'],
						'AGENT_NOM' =>   $infoReview['Agent']['pseudo'],
					  'AGENT_URL' =>   $url_find
				));
			}

			if($send)
            $this->Session->setFlash(__('Avis validé. Email envoyé au client.'),'flash_success');
			else
			$this->Session->setFlash(__('Avis validé.'),'flash_success');
        }else
            $this->Session->setFlash(__('Erreur dans la validation de l\'avis.'),'flash_warning');

        $this->redirect($url);
    }

    public function admin_refuse_review($id){
        $old_review = $this->Review->find('first',array(
                'conditions' => array('review_id' => $id),
			));

		$url = Router::url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), true);

		if($old_review['Review']['rate']< 5 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true), true);

		if($old_review['Review']['rate']< 1 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true), true);
        //On check la présentation
        $this->checkEntite('Review', $id, 'status', $old_review['Review']['status'] == -2 ? -2 : -1, __('Cet avis n\'existe pas ou il n\'est pas en attente de validation'), $url, array('review_id' => $id));

        if($this->Review->updateAll(array('status' => 0),array('review_id' => $id)))
            $this->Session->setFlash(__('Avis refusé.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors du rejet de l\'avis.'),'flash_warning');

        $this->redirect($url);
    }

    public function admin_moveneg_review($id){
        $old_review = $this->Review->find('first',array(
		'conditions' => array('review_id' => $id),
	));

	$url = Router::url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), true);

	if($old_review['Review']['rate']< 5 && in_array($old_review['Review']['status'], [-1, -2]))
		$url = Router::url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true), true);

	if($old_review['Review']['rate']< 1 && in_array($old_review['Review']['status'], [-1, -2]))
		$url = Router::url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true), true);

        //On check la présentation
        $this->checkEntite('Review', $id, 'status', -1, __('Cet avis n\'existe pas ou il n\'est pas en attente de validation'), $url, array('review_id' => $id));

        if($this->Review->updateAll(array('status' => -2),array('review_id' => $id)))
            $this->Session->setFlash(__('Avis déplacé.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors du deplacement de l\'avis.'),'flash_warning');

        $this->redirect($url);
    }

    public function admin_edit_review($id){
        $old_review = $this->Review->find('first',array(
                'conditions' => array('review_id' => $id),
			));

		$url = Router::url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), true);

		if($old_review['Review']['rate']< 5 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true), true);

		if($old_review['Review']['rate']< 1 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true), true);

		if(isset($this->params->query['refuse']))
            $fieldValue = 0;
        elseif(isset($this->params->query['online']))
            $fieldValue = 1;
        else
            $fieldValue = $old_review['Review']['status'] == -2 ? -2 : -1;

        $this->checkEntite('Review', $id, 'status', $fieldValue, __('Cet avis n\'existe pas ou il n\'est pas en attente de validation'), $url, array('review_id' => $id), ($this->request->is('ajax')));



        //Initialisation des paramètres
        $field = array(
			'rate'      => 'rate',
			'pourcent'  => 'pourcent',
            'name'      => 'content',
			'date_add'      => 'date_add',
            'primary'   => 'review_id',
			'send_mail' => 'send_mail'
        );
        $form = array(
            'model' => 'Review',
            'title' => __('Avis'),
            'note'  => __('Après modification de l\'avis, il est impossible de récupérer l\'avis original.<br /> PS : pourcent permet de forcer une mauvaise note')
        );
        $message = array(
            'error'     => __('Erreur lors de la modification de l\'avis.'),
            'success'   => __('Avis modifié.')
        );
        //Edition de l'entite
        $this->editEntiteReview('Review',$id,$url,$field,$form,__('Modification de l\'avis'),nl2br($message));

    }

	public function admin_send_response_review($id){

		$old_review = $this->Review->find('first',array(
                'conditions' => array('review_id' => $id),
			));

		$url = Router::url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), true);

		if($old_review['Review']['rate']< 5 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_bad', 'admin' => true), true);

		if($old_review['Review']['rate']< 1 && in_array($old_review['Review']['status'], [-1, -2]))
			$url = Router::url(array('controller' => 'reviews', 'action' => 'index_resp', 'admin' => true), true);



		$is_send_mail = false;

		if($this->params['data']["isAjax"]){

			$review = $this->Review->find("first", array(
					'recursive' => -1,
					'conditions' => array(
						'review_id' => $id
					)
				));

			$this->loadModel('User');


			$user = $this->User->find("first", array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $review["Review"]["user_id"]
					)
				));

			//$this->sendEmail($user['User']['email'],'Votre avis déposé','send_reponse_avis',array('param' => array('content' => $this->params['data']["Review"]["content"], 'name' => $user['User']['firstname'])));
			if($this->params['data']['Review']['send_mail']){
				$is_send_mail = false;
				$this->sendCmsTemplateByMail(243, $user['User']['lang_id'], $user['User']['email'], array(
						'REFUS_REASON' => nl2br($this->params['data']["Review"]["content"]),
				'TEXT_AVIS' => nl2br($review["Review"]["content"])
					));
			}

			//$this->redirect(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), false);
			//$this->params['data']["Review"]["content"] = $review["Review"]["content"];
			//$this->params['data']["Review"]["rate"] = $review["Review"]["rate"];

		}



			if(isset($this->params->query['refuse']))
				$fieldValue = 0;
			elseif(isset($this->params->query['online']))
				$fieldValue = 1;
			else
				$fieldValue = $old_review['Review']['status'] == -2 ? -2 : -1;

		   $this->checkEntite('Review', $id, 'status', $fieldValue, __('Cet avis n\'existe pas ou il n\'est pas en attente de validation'), $url, array('review_id' => $id), ($this->request->is('ajax')));

			//Initialisation des paramètres
			$field = array(
				'rate'      => 'rate',
				'pourcent'      => 'pourcent',
				'name'      => 'content',
				'date_add'      => 'date_add',
				'primary'   => 'review_id',
				'send_mail' => 'send_mail'
			);
			$form = array(
				'model' => 'Review',
				'title' => __('Réponse'),
				'note'  => __('Remplacer le texte du client par le texte que vous souhaitez lui envoyer')
			);
			if($is_send_mail){
				$message = array(
					'error'     => __('Erreur lors de l\'enregistrement.'),
					'success'   => __('Modification enregistrée et mail envoyé.')
				);
			}else{
				$message = array(
					'error'     => __('Erreur lors de l\'enregistrement.'),
					'success'   => __('Modification enregistrée.')
				);
			}


			//Edition de l'entite
			$this->editEntiteReview('Review',$id,$url,$field,$form,__('Informer le client'),$message);
    }


    public function admin_delete_review($id){
        if(empty($id) || !is_numeric($id)){
            $this->Session->setFlash(__('Cet avis n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'reviews', 'action' => 'index', 'admin' => true), false);
        }

        //On supprime l'avis
        if($this->Review->deleteAll(array('Review.review_id' => $id), false))
            $this->Session->setFlash(__('L\'avis a été supprimé.'),'flash_success');
        else
            $this->Session->setFlash(__('Erreur lors de la suppression.'), 'flash_warning');

        $this->redirect(array('action' => 'index', 'admin' => true), false);
    }

	public function reviews_post()
    {
		$parms = $this->params->query;
		$error = '';
		$user_id = (isset($parms['u']) ?(int)$parms['u']:false);
        $agent_id = (isset($parms['a']) ?(int)$parms['a']:false);
		$consult_id = (isset($parms['c']) ?(int)$parms['c']:false);

        //Customer introuvable
        if(!$user_id || !$agent_id || !$consult_id ){
			$this->Session->setFlash(__('Erreur lors du chargement de la page.'),'flash_warning');
			return '';
		}

		//check si consult present et valide avec user et agent
		$this->loadModel('UserCreditLastHistory');
        $consult_check = $this->UserCreditLastHistory->find('first',array(
            'conditions'    => array(
                'user_credit_last_history' => $consult_id,
				'users_id' => $user_id,
				'agent_id' => $agent_id,
            ),
            'recursive' => -1
        ));

		if(!$consult_check ){
			$this->Session->setFlash(__('Erreur lors du chargement de la page.'),'flash_warning');
			return '';
		}


		//check si avis deja depose ?

        /* On charge le client */
		$this->loadModel('User');
        $user = $this->User->find("first", array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $user_id
                )
            ));

        $userData = $user["User"];

		/* On charge le agent */
		$this->loadModel('User');
        $agent = $this->User->find("first", array(
                'recursive' => -1,
                'conditions' => array(
                    'id' => $agent_id
                )
            ));

        $agentData = $agent["User"];
		$expert = $agentData["agent_number"];

        $consult = $this->UserCreditLastHistory->find('first',array(
            'conditions'    => array(
                'user_credit_last_history' => $consult_id,
            ),
            'recursive' => -1
        ));

		$consultData = $consult["UserCreditLastHistory"];


        //Lorsqu'un client post un avis-------------------------------------------------------------------
        if($this->request->is('post')){

            $requestData = $this->request->data;

            //Pour le retour
            $url = array('controller' => 'reviews', 'action' => 'reviews_post?u='.$user_id.'&a='.$agent_id.'&c='.$consult_id);
			$url_home = array('controller' => 'home', 'action' => 'index');


            //Vérification des champs du formulaire
            $requestData['reviews'] = Tools::checkFormField($requestData['reviews'], array('agent_number', 'content', 'rate'), array('agent_number','content'));
            if($requestData['reviews'] === false){
                $this->Session->setFlash(__('Veuillez remplir les champs obligatoires.'),'flash_warning');

                $this->redirect($url);
            }

            //S'il y a bien une note et qu'elle est négative
            if(isset($requestData['reviews']['rate']) && $requestData['reviews']['rate'] <= 0){
                $this->Session->setFlash(__('Veuillez choisir une note pour l\'expert.'),'flash_warning');
                $this->redirect($url);
            }
            //Si un petit malin, met une note supérieur au max
            if($requestData['reviews']['rate'] > 5)
                $requestData['reviews']['rate'] = 5;

			//calcul pourcentage
			$requestData['reviews']['pourcent'] = number_format($requestData['reviews']['rate'] * 100 / 5,2);

            $requestData['reviews']['lang_id'] = $this->Session->read('Config.id_lang');
            $requestData['reviews']['user_id'] = $user_id;
            $requestData['reviews']['date_add'] = date('Y-m-d H:i:s');
            $requestData['reviews']['status'] = -1;
            //On récupère l'id de l'agent
            $idAgent = $this->User->field('id',array(
                'agent_number' => $requestData['reviews']['agent_number'],
                'deleted' => 0,
                'active' => 1
            ));

            //Si l'agent n'a pas été trouvé
            if(!$idAgent){
                $this->Session->setFlash(__('Erreur lors de la sauvegarde de votre avis.'),'flash_warning');
                $this->redirect($url);
            }

            $requestData['reviews']['agent_id'] = $idAgent;

            $requestData['Review'] = $requestData['reviews'];
            unset($requestData['reviews']);

            //On charge le model review
            $this->loadModel('Review');

            $this->Review->create();
            if($this->Review->save($requestData)){
                $this->Session->setFlash(__('Merci. Votre avis est enregistré et en attente de validation.'),'flash_success');
                $this->redirect($url_home);
            }
            else {
                $this->Session->setFlash(__('Erreur rencontrée lors de la sauvegarde de votre avis.'),'flash_error');
                $this->redirect($url);
            }
        }

        //Quand il arrive sur la page de rédaction d'un avis--------------------------------------------------------------------------------------------------------

        //S'il y a des voyants
        if(!empty($agentData)){
			$voyants = array();
            //On assoicie le pseudo avec le numero d'agent
            $voyants[$agentData['agent_number']] = $agentData['pseudo'].' - '.$agentData['agent_number'];
        }

        $this->set('voyants', $voyants);
        $this->set(compact('expert'));
		$this->set(compact('user_id'));
		$this->set(compact('agent_id'));
		$this->set(compact('consult_id'));

    }

	public function reviewutile(){
		if($this->request->is('ajax')){
			$requestData = $this->request->data;
			$this->loadModel('Review');
			$review = $this->Review->find('first',array(
					'conditions' => array(
						'Review.review_id' => $requestData['id'],
					),
					'recursive' => -1
				));
			$num = intval($review['Review']['utile']) + 1;
			$this->Review->updateAll(array('utile' => $num),array('review_id' => $requestData['id']));
			$this->jsonRender(array('error' => '', 'number' => $num));
		}

	}

	public function loadreviews() {
    $this->loadModel('User');
		if ($this->request->is('ajax')) {
			$requestData = $this->request->data;
            $agent_number = (int) (isset($requestData['agent_number']) ? $requestData['agent_number'] : 0);
            $offset = max(0, (int) (isset($requestData['offset']) ? $requestData['offset'] : 0));

            //
            $rows = $this->User->find('first', array(
                'fields' => array('User.*'),
                'conditions' => array(
                    'User.agent_number'      => $agent_number,
                    'User.role'              => 'agent',
                    'User.active'            => 1,
                    'User.deleted'           => 0
                ),
                'recursive' => -1
            ));

            // Si l'agent n'a pas été trouvé
            if (empty($rows)){
                $this->jsonRender(array('error' => 'Bad agent number', 'html' => ''));
                return;
            }

            //
            $reviews = $this->Review->find('all',array(
                'fields' => array('User.firstname', 'content', 'rate', 'utile', 'date_add', 'review_id'),
                'conditions' => array('agent_id' => $rows['User']['id'], 'Review.status' => 1),
                'order' => 'Review.date_add desc',
                'limit' => Configure::read('Site.limitReviewAgent'),
                'offset' => $offset
            ));

            //
            $this->set(compact('reviews'));
            ob_start();
            require '../View/Agents/display/reviews.ctp';
            $html = ob_get_clean();
			$this->jsonRender(array('error' => '', 'count' => count($reviews), 'html' => $html));
		}

	}

}

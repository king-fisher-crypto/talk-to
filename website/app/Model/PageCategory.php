<?php
    App::uses('AppModel', 'Model');
    /**
     * PageCategory Model
     *
     * @property PageCategoryLang $PageCategoryLang
     */
    class PageCategory extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * hasMany associations
         *
         * @var array
         */
        public $hasMany = array(
            'PageCategoryLang' => array(
                'className' => 'PageCategoryLang',
                'foreignKey' => 'page_category_id',
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

        public function getCategorySelect($id_lang=0, $withoutHidden=false){
            $categories = $this->find('all',array(
                'fields' => array('PageCategory.id', 'PageCategoryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'page_category_langs',
                        'alias' => 'PageCategoryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'PageCategoryLang.lang_id = '.$id_lang,
                            'PageCategoryLang.page_category_id = PageCategory.id'
                        )
                    )
                ),
                'recursive' => -1
            ));

            if(empty($categories)) return false;

            $hiddenCats = Configure::read('Categories.hidden_for_system');

            $out = array();
            foreach($categories as $category){
                $out[$category['PageCategory']['id']] = (empty($category['PageCategoryLang']['name']) ?__('Sans nom').' - '.$category['PageCategory']['id']:$category['PageCategoryLang']['name']);
            }

            return $out;
        }
		
		public function getCategorySelectById($id_lang=0, $id = 0){
            $categories = $this->find('all',array(
                'fields' => array('PageCategory.id', 'PageCategoryLang.name'),
				'conditions' => array(
                            'PageCategory.id = '.$id,

                        ),
                'joins' => array(
                    array(
                        'table' => 'page_category_langs',
                        'alias' => 'PageCategoryLang',
                        'type' => 'left',
                        'conditions' => array(
                            'PageCategoryLang.lang_id = '.$id_lang,
                            'PageCategoryLang.page_category_id = PageCategory.id'
                        )
                    )
                ),
                'recursive' => -1
            ));

            if(empty($categories)) return false;

            $hiddenCats = Configure::read('Categories.hidden_for_system');

            $out = array();
            foreach($categories as $category){
                $out[$category['PageCategory']['id']] = (empty($category['PageCategoryLang']['name']) ?__('Sans nom').' - '.$category['PageCategory']['id']:$category['PageCategoryLang']['name']);
            }

            return $out;
        }

    }

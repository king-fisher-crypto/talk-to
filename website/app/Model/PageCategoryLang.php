<?php
    App::uses('AppModel', 'Model');
    /**
     * PageCategoryLang Model
     *
     * @property PageCategory $PageCategory
     * @property Lang $Lang
     */
    class PageCategoryLang extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'PageCategory' => array(
                'className' => 'PageCategory',
                'foreignKey' => 'page_category_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            ),
            'Lang' => array(
                'className' => 'Lang',
                'foreignKey' => 'lang_id',
                'conditions' => '',
                'fields' => '',
                'order' => ''
            )
        );

        public function hasCategoryLang($id_page, $id_lang){
            $out = $this->find('first', array(
                'conditions'    => array('page_category_id' => $id_page, 'lang_id' => $id_lang),
                'recursive' => -1
            ));

            if(empty($out))
                return false;
            else
                return true;
        }
    }

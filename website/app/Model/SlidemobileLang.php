<?php
    App::uses('AppModel', 'Model');
    /**
     * SlideLang Model
     *
     * @property SlideLang $slideLang
     */
    class SlidemobileLang extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed


        public function lang_exist($id_slide, $id_lang){
            if(empty($id_slide) || empty($id_lang))
                return false;

            $count = $this->find('count', array(
                'conditions' => array('SlidemobileLang.slide_id' => $id_slide, 'SlidemobileLang.lang_id' => $id_lang),
                'recursive' => -1
            ));

            return ($count > 0 ?true:false);
        }



    }

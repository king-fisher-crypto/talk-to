<?php
    App::uses('AppModel', 'Model');
    /**
     * LeftColumnLang Model
     *
     * @property LeftColumnLang $leftColumnLang
     */
    class LeftColumnLang extends AppModel {


        //The Associations below have been created with all possible keys, those that are not needed can be removed


        public function lang_exist($id_block, $id_lang){
            if(empty($id_block) || empty($id_lang))
                return false;

            $count = $this->find('count', array(
                'conditions' => array('LeftColumnLang.left_column_id' => $id_block, 'LeftColumnLang.lang_id' => $id_lang),
                'recursive' => -1
            ));

            return ($count > 0 ?true:false);
        }

    }

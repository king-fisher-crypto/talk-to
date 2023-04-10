<?php
    App::uses('AppModel', 'Model');
    /**
     * ProductLang Model
     *
     * @property Product $Product
     */
    class ProductLang extends AppModel {

        //The Associations below have been created with all possible keys, those that are not needed can be removed

        /**
         * belongsTo associations
         *
         * @var array
         */
        public $belongsTo = array(
            'Product' => array(
                'className' => 'Product',
                'foreignKey' => 'product_id'
            ),
            'Lang' => array(
                'className' => 'Lang',
                'foreignKey' => 'lang_id'
            )
        );

        public function beforeSave($options = array()) {
            parent::beforeSave();

            if($this->hasLang($this->data['ProductLang']['product_id'],$this->data['ProductLang']['lang_id'])){
                $this->updateAll(
                    array(
                        'name'          => '"'.$this->data['ProductLang']['name'].'"',
                        'description'   => '"'.$this->data['ProductLang']['description'].'"'
                    )
                    ,array(
                        'product_id'    => $this->data['ProductLang']['product_id'],
                        'lang_id'       => $this->data['ProductLang']['lang_id']
                    )
                );
                return false;
            }
            return true;
        }

        private function hasLang($id_product, $id_lang){
            $productLang = $this->find('first',array(
                'conditions' => array('product_id' => $id_product, 'lang_id' => $id_lang)
            ));

            if(empty($productLang)) return false;
            return true;
        }
    }

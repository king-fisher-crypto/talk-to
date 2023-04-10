<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    protected $files_to_delete = [];

	//public $recursive = -1;

    public function __getDboExpression($expression='')
    {
        /* Fonction bidon de la classe dbosource
           qui ne sert qu'à créer un objet à passer en paramètre
        */
        if (empty($expression))return false;

        $obj = new stdClass();
        $obj->type = 'expression';
        $obj->value = $expression;
        return $obj;
    }

    //
    public function afterDelete($cascade = true)
    {
        foreach ($this->files_to_delete as $filename) {
            if (file_exists($filename)) {
                @unlink($filename);
            }
        }
		$this->files_to_delete = [];
	}

    //
    public function beforeDelete($cascade = true)
    {
		$this->files_to_delete = [];
	}

    //
    public function beforeSave($options = array())
    {
        if(empty($this->data[$this->alias]['id']) && isset($this->_schema['date_add'])){
            $this->data[$this->alias]['date_add'] = date('Y-m-d H:i:s');
        }elseif (!empty($this->data[$this->alias]['id']) && isset($this->_schema['date_upd'])){
            $this->data[$this->alias]['date_upd'] = date('Y-m-d H:i:s');
        }

        return true;
    }

    /**
     * @param mixed $data
     * @param null $column
     * @return array
     */
    public function value($data, $column = null){
        $db = $this->getDataSource();
        if(is_array($data))
            foreach($data as $key => $row){
                $data[$key] = $db->value($data[$key]);
            }
        else
            return $db->value($data, $column);
        return $data;
    }

	 function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
        $doQuery = true;
        // check if we want the cache
        if (!empty($fields['cache'])) {
            $cacheConfig = null;
            // check if we have specified a custom config
            if (!empty($fields['cacheConfig'])) {
                $cacheConfig = $fields['cacheConfig'];
            }
            $cacheName = $this->name . '-' . $fields['cache'];
            // if so, check if the cache exists
            $data = Cache::read($cacheName, $cacheConfig);
            if ($data == false) {
                $data = parent::find($conditions, $fields,
                    $order, $recursive);
                Cache::write($cacheName, $data, $cacheConfig);
            }
            $doQuery = false;
        }
        if ($doQuery) {
            $data = parent::find($conditions, $fields, $order,
                $recursive);
        }
        return $data;
    }

}
